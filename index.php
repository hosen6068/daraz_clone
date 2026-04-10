<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();

// Set JavaScript variable for login status
$isLoggedIn = $auth->isLoggedIn();

// Get categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

// Get filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get products
$sql = "SELECT * FROM products WHERE status = 'active'";
$params = [];
$types = '';

if ($category_filter) {
    $sql .= " AND category_id = ?";
    $params[] = intval($category_filter);
    $types .= 'i';
}

if ($search) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $search_term = "%{$search}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

$sql .= " ORDER BY created_at DESC LIMIT 20";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();
$stmt->close();

// Get cart count
$cart_count = 0;
if ($auth->isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $cart_count = $result['total'] ?? 0;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daraz Bangladesh - Online Shopping</title>
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
            color: #333;
        }

        header {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-top {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 0;
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
            font-size: 28px;
            font-weight: bold;
        }

        .search-box {
            flex: 1;
            margin: 0 40px;
            display: flex;
        }

        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 3px 0 0 3px;
        }

        .search-box button {
            padding: 10px 20px;
            background: #f85606;
            color: white;
            border: none;
            border-radius: 0 3px 3px 0;
            cursor: pointer;
            font-weight: bold;
        }

        .header-actions {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .header-actions a,
        .header-actions button {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .cart-badge {
            background: #f85606;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            font-weight: bold;
        }

        .nav-menu {
            background: white;
            padding: 15px 0;
            border-top: 1px solid #e0e0e0;
        }

        .nav-menu .header-content {
            display: flex;
            gap: 30px;
        }

        .nav-menu a {
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .nav-menu a:hover {
            color: #667eea;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .sidebar {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .sidebar h3 {
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .category-btn {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .category-btn:hover,
        .category-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .main-content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 5px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 180px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            min-height: 40px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            margin-bottom: 10px;
        }

        .original-price {
            color: #999;
            text-decoration: line-through;
            font-size: 14px;
            margin-right: 10px;
        }

        .current-price {
            color: #f85606;
            font-size: 18px;
            font-weight: bold;
        }

        .discount-badge {
            background: #f85606;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            display: inline-block;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-add-cart {
            flex: 1;
            padding: 8px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.3s;
        }

        .btn-add-cart:hover {
            background: #764ba2;
        }

        .btn-view {
            flex: 1;
            padding: 8px;
            background: #f0f0f0;
            color: #333;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }

        .btn-view:hover {
            background: #e0e0e0;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .rating {
            font-size: 12px;
            color: #f59e0b;
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-wrap: wrap;
            }

            .search-box {
                margin: 10px 0;
                flex-basis: 100%;
                order: 3;
            }

            .main-content {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .category-list {
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-top">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shopping-bag"></i> Daraz
                </div>
                <form class="search-box" action="" method="GET">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <div class="header-actions">
                    <?php if ($auth->isLoggedIn()): ?>
                        <a href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            Cart <?php if ($cart_count > 0): ?><span class="cart-badge"><?php echo $cart_count; ?></span><?php endif; ?>
                        </a>
                        <a href="my-orders.php"><i class="fas fa-box"></i> Orders</a>
                        <a href="profile.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></a>
                        <button onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    <?php else: ?>
                        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="nav-menu">
            <div class="header-content">
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
                <a href="faq.php">FAQ</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="sidebar">
            <h3><i class="fas fa-filter"></i> Filter by Category</h3>
            <div class="category-list">
                <button class="category-btn <?php echo empty($category_filter) ? 'active' : ''; ?>" onclick="filterCategory('')">
                    All Products
                </button>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <button class="category-btn <?php echo $category_filter == $category['id'] ? 'active' : ''; ?>" 
                            onclick="filterCategory(<?php echo $category['id']; ?>)">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </button>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="main-content">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <div class="rating">
                                <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star-half-alt"></i> (45)
                            </div>
                            <div class="product-name"><?php echo htmlspecialchars(substr($product['name'], 0, 50)); ?></div>
                            <div class="product-price">
                                <?php if ($product['discount_price']): ?>
                                    <span class="original-price">৳<?php echo number_format($product['price'], 2); ?></span>
                                    <span class="current-price">৳<?php echo number_format($product['discount_price'], 2); ?></span>
                                    <span class="discount-badge">-<?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>%</span>
                                <?php else: ?>
                                    <span class="current-price">৳<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-cart-plus"></i> Add
                                </button>
                                <button class="btn-view" onclick="viewProduct(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results" style="grid-column: 1 / -1;">
                    <i class="fas fa-search" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
                    <p>No products found. Try adjusting your filters.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Pass PHP variable to JavaScript
        const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        
        function filterCategory(categoryId) {
            let url = 'index.php';
            if (categoryId) {
                url += '?category=' + categoryId;
            }
            window.location.href = url;
        }

        function addToCart(productId) {
            if (!isLoggedIn) {
                alert('Please login to add items to cart');
                window.location.href = 'login.php';
                return;
            }

            fetch('add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }

        function viewProduct(productId) {
            window.location.href = 'product.php?id=' + productId;
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
