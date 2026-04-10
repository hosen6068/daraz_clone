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
    <title>FAQ - Daraz Bangladesh</title>
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
            max-width: 900px;
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
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            text-align: center;
        }

        .faq-item {
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }

        .faq-question {
            padding: 18px;
            background: #f9f9f9;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            color: #333;
            transition: all 0.3s;
        }

        .faq-question:hover {
            background: #f0f0f0;
        }

        .faq-question.active {
            background: #667eea;
            color: white;
        }

        .faq-icon {
            font-size: 20px;
            transition: transform 0.3s;
        }

        .faq-question.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 20px;
            background: white;
            color: #666;
            line-height: 1.8;
            display: none;
            border-top: 1px solid #e0e0e0;
        }

        .faq-answer.active {
            display: block;
        }

        .category-title {
            color: #667eea;
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .category-title:first-of-type {
            margin-top: 0;
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
            <h1><i class="fas fa-question-circle"></i> Frequently Asked Questions</h1>

            <!-- Account & Registration -->
            <h2 class="category-title">Account & Registration</h2>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How do I create an account?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Click on the "Create Account" button on the login page. Fill in your first name, email, and password. You can also add your phone number for easier login. Accept the terms and conditions, then click "Create Account". Your account will be ready immediately!</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What is the "Remember Me" feature?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>The "Remember Me" feature allows you to stay logged in for up to 30 days after login. When enabled, your email or phone number will be saved and displayed on the next login, making it easier to log in again without entering your credentials each time.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How do I reset my password?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Go to your profile page and click on "Change Password". Enter your current password and your new password. Make sure your new password is at least 6 characters long. Click "Change Password" to save your new password.</p>
                </div>
            </div>

            <!-- Shopping & Orders -->
            <h2 class="category-title">Shopping & Orders</h2>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How do I add items to my cart?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Browse our product catalog and find the item you want to purchase. Click the "Add to Cart" button on the product card or product detail page. Select the quantity if needed, then click "Add to Cart". The item will be added to your shopping cart.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How do I proceed to checkout?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Go to your shopping cart, review the items and quantities, then click "Proceed to Checkout". Fill in your shipping address, select a payment method, and review your order summary. Click "Place Order" to complete your purchase.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What payment methods do you accept?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>We accept multiple payment methods: Cash on Delivery (COD), Credit Card, Debit Card, bKash, Nagad, and Rocket. Choose your preferred method during checkout.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How long does delivery take?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Delivery typically takes 2-5 business days depending on your location. You can track your order status in real-time from the "My Orders" section of your account.</p>
                </div>
            </div>

            <!-- Shipping & Delivery -->
            <h2 class="category-title">Shipping & Delivery</h2>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How much does shipping cost?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Shipping is free for orders above ৳5,000. For orders below ৳5,000, the shipping cost is ৳100. This will be calculated automatically in your cart.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Can I track my order?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes! Once your order is placed, you can track it from the "My Orders" section. You'll see the current status and estimated delivery date. You can also view your order history and past receipts.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What if my order is delayed?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Delays are rare, but if your order is delayed beyond the estimated delivery date, please contact our support team immediately. We'll be happy to help resolve the issue.</p>
                </div>
            </div>

            <!-- Returns & Refunds -->
            <h2 class="category-title">Returns & Refunds</h2>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What is your return policy?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>We offer hassle-free returns within 30 days of delivery. Items must be in original condition with all packaging and accessories. Contact our support team to initiate a return.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How long does a refund take?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>After we receive and inspect your returned item, refunds are typically processed within 5-7 business days. The refund will be credited back to your original payment method.</p>
                </div>
            </div>

            <!-- Products -->
            <h2 class="category-title">Products</h2>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Are all products authentic?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes! All products on Daraz are carefully selected and verified for authenticity. We source directly from authorized distributors and manufacturers to ensure quality.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How do I search for a specific product?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Use the search box at the top of the page to search for products by name, brand, or category. You can also browse by category or use filters to narrow down your results.</p>
                </div>
            </div>

            <!-- Support -->
            <h2 class="category-title">Support</h2>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How can I contact customer support?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>You can reach our customer support team via email at support@daraz.com.bd or call us at +88 01700-000000. We're available Monday-Sunday, 9:00 AM - 10:00 PM. You can also visit our <a href="contact.php">Contact Us</a> page.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Is my personal information safe?</span>
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes! We use industry-standard security measures to protect your personal and payment information. Your data is encrypted and stored securely. We never share your information with third parties without your consent.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFaq(element) {
            const question = element;
            const answer = element.nextElementSibling;
            
            // Close other FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== question.parentElement) {
                    item.querySelector('.faq-question').classList.remove('active');
                    item.querySelector('.faq-answer').classList.remove('active');
                }
            });
            
            // Toggle current FAQ
            question.classList.toggle('active');
            answer.classList.toggle('active');
        }
    </script>
</body>
</html>
