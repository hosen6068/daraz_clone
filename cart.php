<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $cart_id = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity <= 0) {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: cart.php');
        exit;
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'remove') {
        $cart_id = intval($_POST['cart_id']);
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        $stmt->close();
        header('Location: cart.php');
        exit;
    }
}

// Get cart items
$stmt = $conn->prepare("
    SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.discount_price, p.image_url, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();
$stmt->close();

// Calculate totals
$subtotal = 0;
while ($item = $cart_items->fetch_assoc()) {
    $price = $item['discount_price'] ?? $item['price'];
    $subtotal += $price * $item['quantity'];
}
$cart_items->data_seek(0);

$tax = $subtotal * 0.10; // 10% tax
$shipping = $subtotal > 5000 ? 0 : 100; // Free shipping above 5000
$total = $subtotal + $tax + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Daraz Bangladesh</title>
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
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .header-actions a,
        .header-actions button {
            color: white;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .cart-items {
            background: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-header {
            background: #f5f5f5;
            padding: 20px;
            border-bottom: 2px solid #667eea;
            font-weight: bold;
        }

        .cart-item {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: grid;
            grid-template-columns: 100px 1fr 100px 100px 50px;
            gap: 20px;
            align-items: center;
        }

        .item-image {
            width: 100px;
            height: 100px;
            background: #f0f0f0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .item-image img {
            max-width: 100%;
            max-height: 100%;
        }

        .item-details h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .item-price {
            color: #f85606;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .item-quantity input {
            width: 80px;
            padding: 5px;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            text-align: center;
        }

        .item-total {
            text-align: right;
            font-weight: bold;
            color: #333;
        }

        .item-remove {
            text-align: center;
        }

        .btn-remove {
            background: #f85606;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-remove:hover {
            background: #d64407;
        }

        .btn-update {
            background: #667eea;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
            width: 100%;
        }

        .empty-cart {
            padding: 40px;
            text-align: center;
            color: #999;
        }

        .summary {
            background: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 80px;
        }

        .summary h3 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
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

        .btn-checkout {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 3px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-continue {
            width: 100%;
            padding: 10px;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 3px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-continue:hover {
            background: #f5f5f5;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 10px;
            }

            .item-quantity,
            .item-total,
            .item-remove {
                grid-column: 2;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo" onclick="window.location.href='index.php'" style="cursor: pointer;">
                <i class="fas fa-shopping-bag"></i> Daraz
            </div>
            <div class="header-actions">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="my-orders.php"><i class="fas fa-box"></i> Orders</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <button onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="cart-items">
            <div class="cart-header">
                <i class="fas fa-shopping-cart"></i> Shopping Cart (<?php echo $cart_items->num_rows; ?> items)
            </div>

            <?php if ($cart_items->num_rows > 0): ?>
                <?php while ($item = $cart_items->fetch_assoc()): ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <div class="item-price">
                                ৳<?php echo number_format($item['discount_price'] ?? $item['price'], 2); ?>
                            </div>
                            <small style="color: #999;">Stock: <?php echo $item['stock']; ?></small>
                        </div>
                        <div class="item-quantity">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                        </div>
                        <div class="item-total">
                            ৳<?php echo number_format(($item['discount_price'] ?? $item['price']) * $item['quantity'], 2); ?>
                        </div>
                        <div class="item-remove">
                            <form method="POST" action="" style="margin: 0;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn-remove"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>Your cart is empty</p>
                    <button class="btn-continue" onclick="window.location.href='index.php'">Continue Shopping</button>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($cart_items->num_rows > 0): ?>
            <div class="summary">
                <h3>Order Summary</h3>
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
                <button class="btn-checkout" onclick="checkout()">Proceed to Checkout</button>
                <button class="btn-continue" onclick="window.location.href='index.php'">Continue Shopping</button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }

        function checkout() {
            window.location.href = 'checkout.php';
        }
    </script>
</body>
</html>
