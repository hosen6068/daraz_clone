# Daraz E-Commerce Installation Guide

## Quick Start

### 1. Database Setup

#### Option A: Using phpMyAdmin (Easiest)
1. Open `http://localhost/phpmyadmin` in your browser
2. Click on **SQL** tab at the top
3. Copy all content from `database.sql` file
4. Paste it in the SQL input area
5. Click **Go** button to execute

#### Option B: Using Command Line
```bash
mysql -u root -p < database.sql
```

### 2. Verify Files Are in Correct Location
Files should be in: `C:\xampp\htdocs\Lab Report 05\`

Required files:
- ✅ config.php
- ✅ auth.php
- ✅ login.php
- ✅ index.php
- ✅ cart.php
- ✅ checkout.php
- ✅ order-confirmation.php
- ✅ my-orders.php
- ✅ profile.php
- ✅ product.php
- ✅ about.php
- ✅ contact.php
- ✅ faq.php
- ✅ logout.php
- ✅ add-to-cart.php
- ✅ database.sql

### 3. Start Services
1. Open XAMPP Control Panel
2. Click **Start** for Apache
3. Click **Start** for MySQL

### 4. Access the Website
Open your browser and go to: `http://localhost/Lab%20Report%2005/`

## Troubleshooting

### Connection Error?
```
"Connection failed: Connection refused"
```
- Make sure MySQL is running in XAMPP
- Check database credentials in `config.php`
- Database name should be: `daraz_ecommerce`

### Database Not Found?
- Go to phpMyAdmin: `http://localhost/phpmyadmin`
- Check if `daraz_ecommerce` database exists
- If not, run the SQL script again

### Files Not Loading?
- Check file paths match: `Lab Report 05`
- URL should be: `http://localhost/Lab%20Report%2005/`
- Not: `http://localhost/Lab Report 05/`

### Page Shows Errors?
1. Check PHP error log: `C:\xampp\apache\logs\error.log`
2. Check MySQL error log in phpMyAdmin
3. Verify all files are in correct folder

## Test Accounts

### Admin Account
- Email: `admin@daraz.com`
- Password: `admin123`

### Create Your Own Account
1. Click "Create Account" on login page
2. Fill in all details
3. Click Create Account
4. Login with your new account

## Features to Test

### 1. Authentication
- [ ] Register new account
- [ ] Login with email
- [ ] Login with phone
- [ ] Remember me (30 days)
- [ ] Session timeout
- [ ] Logout

### 2. Shopping
- [ ] Browse products
- [ ] Filter by category
- [ ] Search products
- [ ] View product details
- [ ] Add to cart
- [ ] Update cart quantities

### 3. Checkout
- [ ] View cart summary
- [ ] Enter shipping address
- [ ] Select payment method
- [ ] Place order
- [ ] View order confirmation
- [ ] Print receipt

### 4. User Management
- [ ] View profile
- [ ] Update profile info
- [ ] Update address
- [ ] Change password
- [ ] View order history

### 5. Security
- [ ] Passwords encrypted
- [ ] Session secure
- [ ] Cookies secure
- [ ] SQL injection prevention
- [ ] XSS prevention

## Sample Data

Database comes with sample products in these categories:
1. **Electronics** - Headphones, Smart Watch
2. **Clothing** - T-Shirt
3. **Books** - Programming Guide
4. **Home & Kitchen** - Kitchen Knives

Sample prices:
- Wireless Headphones: ৳3500 → ৳2800
- Smart Watch: ৳8500 → ৳7200
- T-Shirt: ৳500 → ৳350
- Programming Guide: ৳800 → ৳650
- Kitchen Knife Set: ৳2000 → ৳1500

## Important Configuration

### In config.php:
```php
define('DB_HOST', 'localhost');      // MySQL server
define('DB_USER', 'root');           // MySQL username
define('DB_PASS', '');               // MySQL password (empty)
define('DB_NAME', 'daraz_ecommerce'); // Database name
define('BASE_URL', 'http://localhost/Lab%20Report%2005/');
define('SESSION_TIMEOUT', 3600);     // 1 hour
define('REMEMBER_ME_DURATION', 30 * 24 * 60 * 60); // 30 days
```

Modify these if your MySQL setup is different.

## Features Implemented

### ✅ Core Features
- User Registration & Login
- Email & Phone Number Login
- Remember Me (30 days) with Cookies
- Session Management
- Secure Password Hashing (BCrypt)
- Product Catalog with Categories
- Shopping Cart
- Checkout Process
- Order Management
- User Profile Management
- Password Change
- Logout

### ✅ Security
- SQL Injection Prevention
- Password Hashing (BCrypt)
- Session Regeneration
- Secure Cookies
- Input Validation
- XSS Prevention
- Session Timeout

### ✅ E-Commerce
- Product Categories
- Product Search
- Product Filtering
- Discount Pricing
- Stock Management
- Tax Calculation (10%)
- Shipping Calculation
- Order History
- Order Tracking
- Payment Methods Support

## Email Integration (Optional)

To enable email notifications, add this to `config.php`:

```php
define('MAIL_FROM', 'noreply@daraz.com.bd');
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USER', 'your-email@gmail.com');
define('MAIL_PASS', 'your-app-password');
define('MAIL_PORT', 587);
```

## Adding New Products

Use phpMyAdmin to add products:

1. Go to `http://localhost/phpmyadmin`
2. Select `daraz_ecommerce` database
3. Go to `products` table
4. Click Insert
5. Fill in product details:
   - name: Product name
   - description: Product description
   - category_id: Category ID (1-5)
   - price: Product price
   - discount_price: Discounted price
   - stock: Quantity available
   - image_url: Image URL
   - status: 'active'

## Getting Help

For issues:
1. Check README.md
2. Review code comments
3. Check XAMPP error logs
4. Verify database connection
5. Clear browser cache

## Next Steps

1. Customize the design and colors
2. Add real product images
3. Integrate payment gateway
4. Setup email notifications
5. Add admin dashboard
6. Implement reviews and ratings
7. Add wishlist feature
8. Setup live chat
9. Optimize for mobile
10. Setup SSL/HTTPS

---

**Installation should be complete!** 🎉

Start by going to: `http://localhost/Lab%20Report%2005/`
