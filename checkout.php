<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $conn->prepare("
    SELECT c.quantity, p.id as product_id, p.name, p.price, p.discount_price, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();
$stmt->close();

if ($cart_items->num_rows === 0) {
    header('Location: cart.php');
    exit;
}

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $postal_code = trim($_POST['postal_code']);
    $payment_method = $_POST['payment_method'];

    if (empty($address) || empty($city) || empty($district)) {
        $error = 'Please fill all required fields';
    } else {
        // Calculate totals
        $cart_items->data_seek(0);
        $subtotal = 0;
        $order_items_data = [];

        while ($item = $cart_items->fetch_assoc()) {
            if ($item['stock'] < $item['quantity']) {
                $error = "Insufficient stock for " . $item['name'];
                break;
            }
            $price = $item['discount_price'] ?? $item['price'];
            $subtotal += $price * $item['quantity'];
            $order_items_data[] = $item;
        }

        if (!isset($error)) {
            $tax = $subtotal * 0.10;
            $shipping = $subtotal > 5000 ? 0 : 100;
            $total = $subtotal + $tax + $shipping;

            // Generate order number
            $order_number = 'ORD-' . time() . '-' . rand(1000, 9999);

            // Insert order
            $shipping_address = "$address, $city, $district, $postal_code";
            $stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, tax, shipping_cost, payment_method, order_status, shipping_address) 
                                    VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)");
            $stmt->bind_param("isddss", $user_id, $order_number, $total, $tax, $shipping, $payment_method, $shipping_address);
            
            if ($stmt->execute()) {
                $order_id = $stmt->insert_id;
                $stmt->close();

                // Insert order items
                $success = true;
                foreach ($order_items_data as $item) {
                    $price = $item['discount_price'] ?? $item['price'];
                    $item_total = $price * $item['quantity'];

                    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, total_price) 
                                            VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("iisid d", $order_id, $item['product_id'], $item['name'], $item['quantity'], $price, $item_total);
                    
                    if (!$stmt->execute()) {
                        $success = false;
                        break;
                    }
                    $stmt->close();

                    // Update product stock
                    $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                    $stmt->execute();
                    $stmt->close();
                }

                if ($success) {
                    // Clear cart
                    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->close();

                    $_SESSION['order_success'] = $order_number;
                    header('Location: order-confirmation.php?order=' . $order_id);
                    exit;
                } else {
                    $error = 'Failed to create order items';
                }
            } else {
                $error = 'Failed to create order';
            }
        }
    }
}

// Get cart items again for display
$stmt = $conn->prepare("
    SELECT c.quantity, p.id as product_id, p.name, p.price, p.discount_price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_display = $stmt->get_result();
$stmt->close();

// Calculate totals
$subtotal = 0;
while ($item = $cart_display->fetch_assoc()) {
    $price = $item['discount_price'] ?? $item['price'];
    $subtotal += $price * $item['quantity'];
}
$cart_display->data_seek(0);

$tax = $subtotal * 0.10;
$shipping = $subtotal > 5000 ? 0 : 100;
$total = $subtotal + $tax + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Daraz Bangladesh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .checkout-form {
            background: white;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .form-group-half {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }

        .summary {
            background: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .summary h3 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .summary-row.total {
            font-size: 18px;
            font-weight: bold;
            color: #f85606;
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
            margin-top: 15px;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .form-group-half {
                grid-template-columns: 1fr;
            }

            .summary {
                position: relative;
                top: auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo"><i class="fas fa-shopping-bag"></i> Daraz - Checkout</div>
        </div>
    </header>

    <div class="container">
        <div class="checkout-form">
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Billing Information -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Billing Information</h3>
                    
                    <div class="form-group-half">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" disabled>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Shipping Address</h3>
                    
                    <div class="form-group">
                        <label for="address">Street Address *</label>
                        <textarea id="address" name="address" rows="3" placeholder="Enter your street address" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group-half">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city" placeholder="e.g., Dhaka" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="district">District *</label>
                            <input type="text" id="district" name="district" placeholder="e.g., Dhaka" value="<?php echo htmlspecialchars($user['district'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" placeholder="e.g., 1000" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-section">
                    <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
                    
                    <div class="form-group">
                        <label for="payment_method">Select Payment Method *</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">-- Select Method --</option>
                            <option value="cash_on_delivery">Cash on Delivery (COD)</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="place_order" class="btn-submit">
                    <i class="fas fa-check"></i> Place Order
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="summary">
            <h3><i class="fas fa-receipt"></i> Order Summary</h3>
            
            <?php while ($item = $cart_display->fetch_assoc()): ?>
                <div class="order-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                    <span>৳<?php echo number_format(($item['discount_price'] ?? $item['price']) * $item['quantity'], 2); ?></span>
                </div>
            <?php endwhile; ?>

            <div class="summary-row">
                <span>Subtotal:</span>
                <span>৳<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Tax (10%):</span>
                <span>৳<?php echo number_format($tax, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span><?php echo $shipping > 0 ? '৳' . number_format($shipping, 2) : 'FREE'; ?></span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>৳<?php echo number_format($total, 2); ?></span>
            </div>
        </div>
    </div>
</body>
</html>
