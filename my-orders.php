<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Daraz Bangladesh</title>
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
            cursor: pointer;
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
        }

        .page-title {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .page-title h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .page-title p {
            color: #666;
        }

        .orders-list {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .order-card {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 30px;
            align-items: start;
        }

        .order-card:last-child {
            border-bottom: none;
        }

        .order-info h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .detail-item {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 3px;
        }

        .detail-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .detail-value {
            color: #333;
            font-size: 16px;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fbbf24;
            color: #000;
        }

        .status-processing {
            background: #3b82f6;
            color: white;
        }

        .status-shipped {
            background: #8b5cf6;
            color: white;
        }

        .status-delivered {
            background: #10b981;
            color: white;
        }

        .status-cancelled {
            background: #ef4444;
            color: white;
        }

        .order-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        button, a.button {
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-view {
            background: #667eea;
            color: white;
        }

        .btn-view:hover {
            background: #764ba2;
        }

        .btn-cancel {
            background: #ef4444;
            color: white;
        }

        .btn-cancel:hover {
            background: #dc2626;
        }

        .empty-orders {
            padding: 40px;
            text-align: center;
            color: #999;
        }

        .empty-orders i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.3;
            display: block;
        }

        .empty-orders button {
            margin-top: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
        }

        @media (max-width: 768px) {
            .order-card {
                grid-template-columns: 1fr;
            }

            .order-details {
                grid-template-columns: 1fr;
            }

            .order-actions {
                flex-direction: row;
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
            <div class="header-actions">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <button onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1><i class="fas fa-box"></i> My Orders</h1>
            <p>View and manage all your orders</p>
        </div>

        <div class="orders-list">
            <?php if ($orders->num_rows > 0): ?>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-info">
                            <h3><?php echo htmlspecialchars($order['order_number']); ?></h3>
                            <div class="order-details">
                                <div class="detail-item">
                                    <div class="detail-label">Date</div>
                                    <div class="detail-value"><?php echo date('d M Y', strtotime($order['created_at'])); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Total</div>
                                    <div class="detail-value">৳<?php echo number_format($order['total_amount'], 2); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Status</div>
                                    <div class="detail-value">
                                        <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                            <?php echo str_replace('_', ' ', $order['order_status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Payment</div>
                                    <div class="detail-value"><?php echo str_replace('_', ' ', $order['payment_status']); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="order-actions">
                            <a href="order-confirmation.php?order=<?php echo $order['id']; ?>" class="button btn-view">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <?php if (in_array($order['order_status'], ['pending', 'processing'])): ?>
                                <button class="btn-cancel" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-times"></i> Cancel Order
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-orders">
                    <i class="fas fa-shopping-cart"></i>
                    <p>You haven't placed any orders yet</p>
                    <button onclick="window.location.href='index.php'" class="btn-view">Start Shopping</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }

        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                // TODO: Implement order cancellation
                alert('Order cancellation functionality coming soon!');
            }
        }
    </script>
</body>
</html>
