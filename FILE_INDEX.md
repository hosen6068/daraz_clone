# 🛒 Daraz E-Commerce Platform - File Index

**Project Location**: `C:\xampp\htdocs\Lab Report 05\`
**Database**: `daraz_ecommerce`
**Access URL**: `http://localhost/Lab%20Report%2005/`

---

## 📋 Complete File List

### 🔧 Core Configuration
| File | Purpose |
|------|---------|
| `config.php` | Database connection and settings |
| `auth.php` | Authentication & session management |

### 🔐 Authentication Pages
| File | Purpose |
|------|---------|
| `login.php` | Login & Registration (remember me included) |
| `logout.php` | Logout handler |

### 🏠 Frontend Pages
| File | Purpose |
|------|---------|
| `index.php` | Homepage with product catalog & filtering |
| `product.php` | Product detail page with reviews |
| `about.php` | About Us page |
| `contact.php` | Contact Us page with form |
| `faq.php` | FAQ with expandable items |

### 🛍️ Shopping
| File | Purpose |
|------|---------|
| `add-to-cart.php` | AJAX cart handler |
| `cart.php` | Shopping cart page |
| `checkout.php` | Checkout & order creation |
| `order-confirmation.php` | Order confirmation page |

### 👤 User Dashboard
| File | Purpose |
|------|---------|
| `my-orders.php` | Order history & tracking |
| `profile.php` | User profile & settings |

### 📁 Data Files
| File | Purpose |
|------|---------|
| `database.sql` | Complete database schema + sample data |

### 📚 Documentation
| File | Purpose |
|------|---------|
| `README.md` | Full comprehensive documentation |
| `INSTALLATION.md` | Installation & setup steps |
| `COMPLETE_SETUP.txt` | Quick reference guide |
| `FILE_INDEX.md` | This file (complete file listing) |

---

## 🔄 User Flow

```
START
  ↓
LOGIN.PHP
  ├─ Login (with Remember Me option)
  ├─ Create Account
  └─ Auto-login if remembered
  ↓
INDEX.PHP
  ├─ Browse Products
  ├─ Search Products
  ├─ Filter by Category
  └─ View Product Details
  ↓
PRODUCT.PHP
  ├─ View Product Info
  ├─ Add to Cart
  └─ Check Reviews
  ↓
CART.PHP
  ├─ View Cart Items
  ├─ Update Quantities
  └─ Proceed to Checkout
  ↓
CHECKOUT.PHP
  ├─ Enter Address
  ├─ Select Payment
  └─ Place Order
  ↓
ORDER-CONFIRMATION.PHP
  └─ View Order Details
  ↓
MY-ORDERS.PHP
  ├─ Order History
  ├─ Order Tracking
  └─ Reorder
  ↓
PROFILE.PHP
  ├─ Update Info
  ├─ Change Password
  └─ Manage Addresses
  ↓
LOGOUT.PHP
  └─ Exit
```

---

## 🗄️ Database Tables

### 1. **users** - User Accounts
```sql
Columns: id, first_name, last_name, email, phone, password, 
         address, city, district, postal_code, country, 
         created_at, updated_at, status
```

### 2. **remember_tokens** - Auto-Login Tokens
```sql
Columns: id, user_id, token, created_at, expires_at
```

### 3. **categories** - Product Categories
```sql
Columns: id, name, description, slug, image_url, created_at, updated_at
```

### 4. **products** - Product Information
```sql
Columns: id, name, description, category_id, price, discount_price, 
         stock, image_url, created_at, updated_at, status
Sample: 5 products with 15-30% discounts
```

### 5. **product_images** - Additional Images
```sql
Columns: id, product_id, image_url, alt_text, created_at
```

### 6. **cart** - Shopping Cart
```sql
Columns: id, user_id, product_id, quantity, created_at, updated_at
```

### 7. **orders** - Customer Orders
```sql
Columns: id, user_id, order_number, total_amount, tax, shipping_cost, 
         discount, payment_method, payment_status, order_status, 
         shipping_address, notes, created_at, updated_at
```

### 8. **order_items** - Items in Orders
```sql
Columns: id, order_id, product_id, product_name, quantity, 
         unit_price, total_price
```

### 9. **reviews** - Product Reviews
```sql
Columns: id, product_id, user_id, rating, comment, created_at, updated_at
```

### 10. **admin_users** - Admin Accounts
```sql
Columns: id, username, password, email, role, created_at, status
Sample: admin@daraz.com / admin123
```

---

## 🔐 Authentication Flow

### Login with Remember Me
```
1. User enters email/phone + password
2. Check credentials in database
3. If correct:
   - Create session variables
   - If "Remember Me" checked:
     * Generate random 32-byte token
     * Hash token with SHA-256
     * Store hashed token in database
     * Set cookie for 30 days
4. Redirect to homepage
```

### Auto-Login on Return Visit
```
1. Check for remember_token cookie
2. Hash it and check against database
3. If found and not expired:
   - Get user info
   - Create session
   - Auto-login user
4. If expired:
   - Delete cookie
   - Show login form
```

---

## 🛡️ Security Features

### Password Security
- BCrypt hashing (cost factor 10)
- Random salt generation
- 6+ character requirement
- Never stored plain text

### Session Security
- 1-hour timeout
- Auto-regenerate on login
- Database validation
- Secure session cookies

### Cookie Security
- HTTPOnly flag (prevent JS access)
- Secure flag (HTTPS in production)
- 30-day max age
- Signed tokens

### Data Protection
- Prepared statements (SQL injection prevention)
- htmlspecialchars() for XSS prevention
- Input type casting
- Whitelist validation

### Remember Me Security
- Token expiration after 30 days
- Hash validation on every access
- Automatic cleanup of expired tokens
- One-way hashing (SHA-256)

---

## 📊 Feature Summary

### Pages: 14
- 1 Login/Register page
- 1 Homepage
- 1 Product detail page
- 1 Shopping cart
- 1 Checkout
- 1 Order confirmation
- 1 Order history
- 1 User profile
- 3 Info pages (About, Contact, FAQ)
- 1 Logout handler
- 1 Cart API

### Database Tables: 10
- User management
- Token storage
- Product management
- Cart system
- Order management
- Reviews system
- Admin accounts

### Features: 30+
- User registration
- Email/phone login
- Remember me (30 days)
- Session management
- Product browsing
- Category filtering
- Product search
- Shopping cart
- Checkout process
- Order creation
- Order tracking
- User profile
- Password change
- Address management
- Tax calculation
- Shipping calculation
- Multiple payment methods
- Stock management
- Discount pricing
- Product ratings
- And more...

---

## 🚀 Installation Checklist

- [ ] Extract all files to: `C:\xampp\htdocs\Lab Report 05\`
- [ ] Start Apache (XAMPP)
- [ ] Start MySQL (XAMPP)
- [ ] Open phpMyAdmin: `http://localhost/phpmyadmin`
- [ ] Create database: `daraz_ecommerce`
- [ ] Import `database.sql`
- [ ] Visit: `http://localhost/Lab%20Report%2005/`
- [ ] Login with test account or create new
- [ ] Test all features
- [ ] Customize as needed

---

## 🔑 Quick Reference

| Item | Value |
|------|-------|
| **Database Name** | daraz_ecommerce |
| **DB User** | root |
| **DB Password** | (empty) |
| **Base URL** | http://localhost/Lab%20Report%2005/ |
| **Session Timeout** | 1 hour |
| **Remember Me** | 30 days |
| **Tax Rate** | 10% |
| **Free Shipping** | Orders > ৳5000 |
| **Shipping Cost** | ৳100 |

---

## 🎯 Test Accounts

```
Admin Account:
  Email: admin@daraz.com
  Password: admin123

Test Products:
  1. Wireless Headphones - ৳3500 → ৳2800
  2. Smart Watch - ৳8500 → ৳7200
  3. T-Shirt - ৳500 → ৳350
  4. Programming Guide - ৳800 → ৳650
  5. Kitchen Knife Set - ৳2000 → ৳1500
```

---

## 📞 Support Documentation

- **Full Docs**: READ `README.md`
- **Setup Help**: READ `INSTALLATION.md`
- **Quick Ref**: READ `COMPLETE_SETUP.txt`
- **Issues**: Check XAMPP error logs

---

## ✨ Key Highlights

✅ **Production Ready** - All features tested
✅ **Secure** - Latest security practices
✅ **Scalable** - Clean, organized code
✅ **Well Documented** - Comments throughout
✅ **Easy to Customize** - Well-structured code
✅ **Database Optimized** - Proper indices and relationships

---

**Version**: 1.0.0 | **Year**: 2026 | **Status**: COMPLETE ✅
