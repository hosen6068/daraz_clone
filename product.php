<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();

$product_id = intval($_GET['id'] ?? 0);

if ($product_id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: index.php');
    exit;
}

// Get product images
$stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$images = $stmt->get_result();
$stmt->close();

// Get category
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $product['category_id']);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get reviews
$stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews = $stmt->get_result();
$stmt->close();

// Calculate average rating
$avg_rating_result = $conn->query("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE product_id = $product_id");
$rating_info = $avg_rating_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Daraz Bangladesh</title>
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

        .breadcrumb {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .product-images {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .main-image {
            width: 100%;
            height: 400px;
            background: #f0f0f0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .main-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .thumbnail-images {
            display: flex;
            gap: 10px;
        }

        .thumbnail {
            width: 60px;
            height: 60px;
            background: #f0f0f0;
            border: 2px solid #e0e0e0;
            border-radius: 3px;
            cursor: pointer;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .thumbnail img {
            max-width: 100%;
            max-height: 100%;
        }

        .thumbnail:hover,
        .thumbnail.active {
            border-color: #667eea;
        }

        .product-info h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 28px;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .stars {
            color: #f59e0b;
        }

        .price-section {
            margin: 20px 0;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .original-price {
            color: #999;
            text-decoration: line-through;
            font-size: 18px;
            margin-right: 15px;
        }

        .current-price {
            color: #f85606;
            font-size: 32px;
            font-weight: bold;
        }

        .discount-badge {
            background: #f85606;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            margin-left: 10px;
        }

        .stock-info {
            margin: 15px 0;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 3px;
        }

        .description {
            margin: 20px 0;
            color: #666;
            line-height: 1.6;
        }

        .quantity-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
        }

        .quantity-input {
            display: flex;
            align-items: center;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
        }

        .quantity-input button {
            padding: 8px 12px;
            background: #f0f0f0;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .quantity-input input {
            width: 50px;
            text-align: center;
            border: none;
            padding: 8px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        button {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-add-cart {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-wishlist {
            background: white;
            color: #f85606;
            border: 2px solid #f85606;
        }

        .btn-wishlist:hover {
            background: #fff5f0;
        }

        .reviews-section {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .reviews-section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }

        .review-item {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .review-author {
            font-weight: bold;
            color: #333;
        }

        .review-date {
            color: #999;
            font-size: 14px;
        }

        .review-rating {
            color: #f59e0b;
            margin-bottom: 10px;
        }

        .review-text {
            color: #666;
            line-height: 1.6;
        }

        .no-reviews {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .current-price {
                font-size: 24px;
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
                <?php if ($auth->isLoggedIn()): ?>
                    <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                    <a href="my-orders.php"><i class="fas fa-box"></i> Orders</a>
                    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                    <button onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Home</a> /
            <a href="index.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a> /
            <span><?php echo htmlspecialchars(substr($product['name'], 0, 50)); ?></span>
        </div>

        <div class="product-detail">
            <div class="product-images">
                <div class="main-image">
                    <img id="mainImage" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="thumbnail-images">
                    <div class="thumbnail active" onclick="changeImage('<?php echo htmlspecialchars($product['image_url']); ?>')">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="">
                    </div>
                    <?php while ($image = $images->fetch_assoc()): ?>
                        <div class="thumbnail" onclick="changeImage('<?php echo htmlspecialchars($image['image_url']); ?>')">
                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="">
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="rating">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span><?php echo number_format($rating_info['avg_rating'] ?? 0, 1); ?>/5.0</span>
                    <span>(<?php echo $rating_info['review_count'] ?? 0; ?> reviews)</span>
                </div>

                <div class="price-section">
                    <?php if ($product['discount_price']): ?>
                        <span class="original-price">৳<?php echo number_format($product['price'], 2); ?></span>
                        <span class="current-price">৳<?php echo number_format($product['discount_price'], 2); ?></span>
                        <span class="discount-badge">-<?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>%</span>
                    <?php else: ?>
                        <span class="current-price">৳<?php echo number_format($product['price'], 2); ?></span>
                    <?php endif; ?>
                </div>

                <div class="stock-info">
                    <strong>Availability:</strong> 
                    <?php if ($product['stock'] > 0): ?>
                        <span style="color: #10b981;"><i class="fas fa-check"></i> In Stock (<?php echo $product['stock']; ?> available)</span>
                    <?php else: ?>
                        <span style="color: #ef4444;"><i class="fas fa-times"></i> Out of Stock</span>
                    <?php endif; ?>
                </div>

                <div class="description">
                    <h3>Description</h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                </div>

                <?php if ($product['stock'] > 0): ?>
                    <div class="quantity-section">
                        <label>Quantity:</label>
                        <div class="quantity-input">
                            <button onclick="decreaseQty()">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button onclick="increaseQty(<?php echo $product['stock']; ?>)">+</button>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>)">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn-wishlist">
                            <i class="fas fa-heart"></i> Add to Wishlist
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="reviews-section">
            <h2><i class="fas fa-comments"></i> Customer Reviews (<?php echo $rating_info['review_count'] ?? 0; ?>)</h2>
            <?php if ($reviews->num_rows > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="review-author"><?php echo htmlspecialchars($review['id']); ?></span>
                            <span class="review-date"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="review-text"><?php echo htmlspecialchars($review['comment']); ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-reviews">
                    <i class="fas fa-comment-dots" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            event.target.closest('.thumbnail').classList.add('active');
        }

        function increaseQty(max) {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function addToCart(productId) {
            <?php if (!$auth->isLoggedIn()): ?>
                alert('Please login to add items to cart');
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            const quantity = document.getElementById('quantity').value;
            
            fetch('add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&quantity=' + quantity
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    window.location.href = 'cart.php';
                } else {
                    alert(data.message);
                }
            });
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
