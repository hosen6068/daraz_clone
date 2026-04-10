<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Daraz Bangladesh</title>
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

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .content-box {
            background: white;
            border-radius: 5px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }

        h2 {
            color: #667eea;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        ul {
            margin-left: 20px;
            color: #666;
            line-height: 1.8;
        }

        li {
            margin-bottom: 10px;
        }

        a {
            color: #667eea;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo" onclick="window.location.href='index.php'" style="cursor: pointer;">
                <i class="fas fa-shopping-bag"></i> Daraz
            </div>
            <div>
                <a href="index.php" style="color: white; margin-left: 20px;"><i class="fas fa-home"></i> Home</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="content-box">
            <h1><i class="fas fa-info-circle"></i> About Daraz Bangladesh</h1>

            <p>Welcome to Daraz Bangladesh, your premier online shopping destination! We are committed to providing the best e-commerce experience with a wide range of products, reliable service, and customer satisfaction.</p>

            <h2>Our Mission</h2>
            <p>Our mission is to make online shopping easy, convenient, and accessible to everyone in Bangladesh. We strive to offer quality products at competitive prices with excellent customer service.</p>

            <h2>Our Vision</h2>
            <p>To become the leading e-commerce platform in South Asia by providing innovative solutions, superior quality products, and exceptional customer service.</p>

            <h2>Why Choose Daraz?</h2>
            <ul>
                <li><strong>Wide Selection:</strong> Browse thousands of products across multiple categories</li>
                <li><strong>Best Prices:</strong> Competitive pricing with regular discounts and offers</li>
                <li><strong>Secure Shopping:</strong> Safe and secure payment options</li>
                <li><strong>Fast Delivery:</strong> Quick delivery to your doorstep</li>
                <li><strong>Customer Support:</strong> 24/7 customer service team</li>
                <li><strong>Easy Returns:</strong> Hassle-free return and exchange policy</li>
                <li><strong>Trusted Platform:</strong> Millions of satisfied customers</li>
            </ul>

            <h2>Our Categories</h2>
            <ul>
                <li>Electronics & Gadgets</li>
                <li>Fashion & Clothing</li>
                <li>Books & Educational Materials</li>
                <li>Home & Kitchen</li>
                <li>Sports & Outdoors</li>
            </ul>

            <h2>Payment Methods</h2>
            <p>We accept multiple payment methods for your convenience:</p>
            <ul>
                <li>Cash on Delivery (COD)</li>
                <li>Credit Card</li>
                <li>Debit Card</li>
                <li>bKash</li>
                <li>Nagad</li>
                <li>Rocket</li>
            </ul>

            <h2>Shipping & Delivery</h2>
            <p>We offer fast and reliable delivery services:</p>
            <ul>
                <li><strong>Free Shipping:</strong> On orders above ৳5000</li>
                <li><strong>Standard Shipping:</strong> ৳100 for orders below ৳5000</li>
                <li><strong>Delivery Time:</strong> 2-5 business days depending on location</li>
                <li><strong>Track Your Order:</strong> Real-time order tracking available</li>
            </ul>

            <h2>Our Commitment</h2>
            <p>We are committed to maintaining the highest standards of quality, reliability, and customer satisfaction. Every product on our platform is carefully selected to ensure authenticity and quality. Our team works tirelessly to ensure your shopping experience is smooth, safe, and enjoyable.</p>

            <h2>Contact Us</h2>
            <p>Have questions or need assistance? <a href="contact.php">Contact our support team</a> - we're here to help!</p>
        </div>
    </div>
</body>
</html>
