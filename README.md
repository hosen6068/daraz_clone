# Daraz Bangladesh - E-Commerce Platform

A fully functional e-commerce website similar to Daraz Bangladesh, built with PHP, MySQL, HTML5, and CSS3.

## Features

### ✅ User Authentication & Security
- **User Registration**: New users can create accounts with email/phone verification
- **Login System**: Secure login with email or phone number
- **Remember Me**: Optional 30-day automatic login with secure tokens
- **Session Management**: Secure session handling with timeout
- **Cookie-based Authentication**: Persistent login support

### ✅ User Management
- **User Profile**: View and edit personal information
- **Address Management**: Save multiple shipping addresses
- **Password Management**: Secure password change functionality
- **Order History**: Track all purchases

### ✅ Shopping Features
- **Product Catalog**: Browse products by category
- **Search Functionality**: Search products by name/description
- **Shopping Cart**: Add/remove products, update quantities
- **Wishlist Support**: Ready for wishlist implementation

### ✅ Checkout & Orders
- **Cart System**: Dynamic cart with real-time calculations
- **Checkout Process**: Multi-step checkout with address validation
- **Order Confirmation**: Instant order confirmation with details
- **Payment Methods**: Support for multiple payment methods:
  - Cash on Delivery (COD)
  - Credit Card
  - Debit Card
  - bKash
  - Nagad
  - Rocket

### ✅ E-Commerce Features
- **Product Management**: Categories, pricing, stock management
- **Discounts**: Original price and discount price display
- **Tax Calculation**: Automatic tax calculation (10%)
- **Shipping**: Free shipping above ৳5000, otherwise ৳100
- **Product Images**: Multiple image support per product
- **Rating System**: Ready for review/rating implementation

## Installation Guide

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- PHP 7.2 or higher
- MySQL 5.7 or higher
- Modern web browser

### Step 1: Setup XAMPP

1. Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Create Database

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on "SQL" tab
3. Copy and paste the contents of `database.sql` file
4. Click "Go" to execute

### Step 3: Configure Environment

1. The `config.php` file is pre-configured with default settings:
   - Database: `daraz_ecommerce`
   - Username: `root`
   - Password: (empty)
   - Host: `localhost`

   If your MySQL configuration is different, update these values in `config.php`

### Step 4: Place Files in XAMPP

1. Copy all project files to: `C:\xampp\htdocs\Lab Report 05\`
2. Make sure the folder structure is correct

### Step 5: Access the Application

1. Open your browser
2. Navigate to: `http://localhost/Lab%20Report%2005/`
3. You will be redirected to the login page

## Login Credentials

### For Testing:

**Admin Account:**
- Username: `admin`
- Email: `admin@daraz.com`
- Password: `admin123`

**Or create a new user account:**
1. Click on "Create Account" on the login page
2. Fill in the registration form
3. Click "Create Account"
4. Login with your credentials

## File Structure

```
Lab Report 05/
├── config.php                 # Database configuration
├── auth.php                   # Authentication system
├── login.php                  # Login & Register page
├── index.php                  # Homepage with product listing
├── logout.php                 # Logout functionality
├── add-to-cart.php            # Add to cart API
├── cart.php                   # Shopping cart page
├── checkout.php               # Checkout page
├── order-confirmation.php     # Order confirmation page
├── my-orders.php              # Order history page
├── profile.php                # User profile page
├── database.sql               # Database schema and sample data
├── README.md                  # This file
└── styles.css                 # Global styles (optional)
```

## Database Structure

### Tables Created:

1. **users** - User account information
2. **remember_tokens** - Remember me tokens for auto-login
3. **categories** - Product categories
4. **products** - Product information
5. **product_images** - Multiple images per product
6. **cart** - Shopping cart items
7. **orders** - Customer orders
8. **order_items** - Items in each order
9. **reviews** - Product reviews and ratings
10. **admin_users** - Admin user accounts

## Key Features Explained

### 1. Authentication System

The authentication system uses:
- **BCrypt Hashing** for password security
- **Session Management** with timeout (1 hour default)
- **Secure Cookies** for remember me functionality
- **Token-based Auto-login** with expiration

### 2. Remember Me Functionality

When a user enables "Remember Me":
1. A secure token is generated
2. Token is hashed and stored in database
3. Cookie is set for 30 days
4. On next visit, the token is validated and user is auto-logged in
5. Saved email/phone appears for easy login

### 3. Shopping Cart

- **Session-based Cart**: Items stored in database (not session)
- **Real-time Calculations**: Tax and shipping calculated automatically
- **Stock Management**: System checks stock availability
- **Cart Persistence**: Cart data remains even after logout

### 4. Order Management

- **Order Number Generation**: Unique order ID with timestamp
- **Order Tracking**: Users can view all past orders
- **Order Status**: Tracks product delivery status
- **Payment Status**: Tracks payment completion

## Usage Guide

### For Customers:

1. **Register/Login**
   - Create a new account or login with existing credentials
   - Enable "Remember Me" for automatic login next time

2. **Browse Products**
   - Filter products by category
   - Search for specific products
   - View product details

3. **Shopping**
   - Add products to cart
   - Adjust quantities in cart
   - Apply any available discounts

4. **Checkout**
   - Fill in shipping address
   - Select payment method
   - Review order summary
   - Place order

5. **Order Tracking**
   - View all orders in "My Orders"
   - Check order status
   - View order details and receipts

6. **Profile Management**
   - Update personal information
   - Change password
   - Save multiple addresses

### For Developers:

#### Adding New Products:
```php
$sql = "INSERT INTO products (name, description, category_id, price, discount_price, stock, image_url) 
        VALUES ('Product Name', 'Description', 1, 1000, 800, 50, 'image.jpg')";
$conn->query($sql);
```

#### Getting Cart Items:
```php
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart = $stmt->get_result();
```

#### Creating Orders:
- Use checkout.php as reference
- Orders are automatically created from cart items
- Stock is automatically updated

## Security Features

- ✅ SQL Injection Prevention (Prepared Statements)
- ✅ Password Hashing (BCrypt)
- ✅ Session Regeneration on Login
- ✅ CSRF Protection Ready
- ✅ Input Validation
- ✅ XSS Prevention (htmlspecialchars)
- ✅ Secure Cookie Handling

## Future Enhancements

- [ ] Payment Gateway Integration (SSL Commerz, Sslwireless)
- [ ] Email Notifications
- [ ] SMS Alerts
- [ ] Wishlist Feature
- [ ] Product Reviews & Ratings
- [ ] Admin Dashboard
- [ ] Inventory Management
- [ ] Seller Dashboard
- [ ] Advanced Search with Filters
- [ ] Product Recommendations
- [ ] Live Chat Support
- [ ] Mobile App

## Troubleshooting

### Database Connection Error
- Check if MySQL service is running
- Verify database credentials in `config.php`
- Ensure database "daraz_ecommerce" exists

### Files Not Found (404 Error)
- Check file paths in URL
- Ensure all PHP files are in correct folder
- Verify folder name matches in URL

### Login Issues
- Clear browser cookies
- Empty browser cache
- Check if user account exists
- Verify password is correct

### Cart Not Working
- Ensure user is logged in
- Check browser cookies are enabled
- Clear browser cache and cookies

## Support & Contact

For issues or questions:
- Check the documentation
- Review code comments
- Test with provided sample data
- Check PHP error logs

## License

This project is provided as-is for educational purposes.

---

**Created**: 2026
**Version**: 1.0.0
**Status**: Production Ready
