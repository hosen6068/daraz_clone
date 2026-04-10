<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$order_id = intval($_GET['order'] ?? 0);
$user_id = $_SESSION['user_id'];

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Get order items
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Daraz Bangladesh</title>
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
            cursor: pointer;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .confirmation-box {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .success-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .success-header i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .success-header h1 {
            margin-bottom: 10px;
        }

        .success-header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .order-details {
            padding: 30px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            margin-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: bold;
            color: #667eea;
        }

        .detail-value {
            color: #333;
        }

        .items-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .items-table th {
            background: #f5f5f5;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #667eea;
            font-weight: bold;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .totals {
            margin-top: 20px;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 30px;
            width: 50%;
            margin-left: auto;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .total-row.final {
            border-bottom: 2px solid #667eea;
            padding-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }

        button, a.button {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-continue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-print {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-print:hover {
            background: #f5f5f5;
        }

        @media print {
            header, .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo" onclick="window.location.href='index.php'">
                <i class="fas fa-shopping-bag"></i> Daraz
            </div>
        </div>
    </header>

    <div class="container">
        <div class="confirmation-box">
            <div class="success-header">
                <i class="fas fa-check-circle"></i>
                <h1>Order Confirmed!</h1>
                <p>Thank you for your purchase</p>
            </div>

            <div class="order-details">
                <div class="detail-row">
                    <div class="detail-label">Order Number:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['order_number']); ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Order Date:</div>
                    <div class="detail-value"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span style="background: #fbbf24; color: #000; padding: 5px 10px; border-radius: 3px;">
                            <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                        </span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Payment Method:</div>
                    <div class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Shipping Address:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['shipping_address']); ?></div>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px;">Order Items</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $order_items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>৳<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td>৳<?php echo number_format($item['total_price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="totals">
                    <div>
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>৳<?php echo number_format($order['total_amount'] - $order['tax'] - $order['shipping_cost'], 2); ?></span>
                        </div>
                        <div class="total-row">
                            <span>Tax:</span>
                            <span>৳<?php echo number_format($order['tax'], 2); ?></span>
                        </div>
                        <div class="total-row">
                            <span>Shipping:</span>
                            <span>৳<?php echo number_format($order['shipping_cost'], 2); ?></span>
                        </div>
                        <div class="total-row final">
                            <span>Total:</span>
                            <span>৳<?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-continue" onclick="document.location='my-orders.php'">View All Orders</button>
                    <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    <button class="btn-continue" onclick="document.location='index.php'">Continue Shopping</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
