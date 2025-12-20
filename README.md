# Online Shopping Cart - PHP & MySQL

A fully functional e-commerce shopping cart system built with PHP and MySQL. Features a responsive design using Bootstrap 5, complete user authentication, shopping cart functionality, order management, and a comprehensive admin panel.

## 🌟 Features

### Customer Features
- **User Authentication**
  - User registration with validation
  - Secure login/logout system
  - Password hashing with bcrypt
  
- **Product Browsing**
  - Browse products by category
  - Search functionality
  - Product detail pages with images
  - Featured products showcase
  
- **Shopping Cart**
  - Add/remove items from cart
  - Update quantities
  - Real-time cart total calculation
  - Free shipping on orders over $50
  
- **Checkout & Orders**
  - Secure checkout process
  - Multiple payment methods (Credit Card, PayPal, Bank Transfer, COD)
  - Order confirmation page
  - Order history tracking
  - Order detail view

### Admin Features
- **Dashboard**
  - Statistics overview (users, products, orders, revenue)
  - Recent orders display
  - Quick action buttons
  
- **Product Management**
  - Add/Edit/Delete products
  - Product image upload
  - Stock management
  - Featured products selection
  
- **Category Management**
  - Add/Edit/Delete categories
  - Category descriptions
  
- **Order Management**
  - View all orders
  - Update order status (Pending, Processing, Shipped, Delivered, Cancelled)
  - Order details with customer information
  
- **User Management**
  - View all registered users
  - User information display

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PHP Extensions:
  - mysqli
  - gd (for image handling)
  - fileinfo

## 🚀 Installation

### 1. Database Setup

```bash
# Create the database
mysql -u root -p

# Import the database schema
mysql -u root -p < database.sql
```

Or manually:
1. Create a database named `shopping_cart`
2. Import the `database.sql` file using phpMyAdmin or MySQL command line

### 2. Configuration

Edit `includes/config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password
define('DB_NAME', 'shopping_cart'); // Database name
```

### 3. File Permissions

```bash
# Set write permissions for uploads directory
chmod 755 uploads/
```

### 4. Web Server Setup

#### Apache (with .htaccess)
```apache
<VirtualHost *:80>
    DocumentRoot "/path/to/webapp"
    ServerName shopping-cart.local
    
    <Directory "/path/to/webapp">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name shopping-cart.local;
    root /path/to/webapp;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 📁 Project Structure

```
webapp/
├── admin/                      # Admin panel pages
│   ├── dashboard.php          # Admin dashboard
│   ├── products.php           # Product management
│   ├── categories.php         # Category management
│   ├── orders.php             # Order management
│   ├── order-detail.php       # Order details
│   └── users.php              # User management
│
├── includes/                   # Configuration and utility files
│   ├── config.php             # Database and site configuration
│   ├── db.php                 # Database connection class
│   ├── functions.php          # Helper functions
│   ├── header.php             # Common header with navigation
│   └── footer.php             # Common footer
│
├── assets/                     # Static assets (if needed)
│   ├── css/                   # Custom stylesheets
│   ├── js/                    # JavaScript files
│   └── images/                # Site images
│
├── uploads/                    # Product image uploads
│
├── index.php                   # Homepage
├── products.php                # Products listing
├── product-detail.php          # Single product view
├── categories.php              # Categories page
├── cart.php                    # Shopping cart
├── cart-action.php             # Cart operations handler
├── checkout.php                # Checkout page
├── order-confirmation.php      # Order success page
├── orders.php                  # User order history
├── order-detail.php            # User order details
├── login.php                   # Login page
├── register.php                # Registration page
├── logout.php                  # Logout handler
├── database.sql                # Database schema
└── README.md                   # This file
```

## 👤 Default Admin Account

After database setup, use these credentials to access the admin panel:

- **Username:** `admin`
- **Password:** `admin123`
- **Admin Panel URL:** `http://yoursite.com/admin/dashboard.php`

**⚠️ Important:** Change the default admin password after first login!

## 🎨 Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **CSS Framework:** Bootstrap 5.3.0
- **Icons:** Font Awesome 6.4.0
- **Database Design:** Relational (Normalized)

## 💾 Database Schema

### Main Tables:
- `users` - User accounts and authentication
- `categories` - Product categories
- `products` - Product catalog
- `cart` - Shopping cart items
- `orders` - Customer orders
- `order_items` - Order line items

## 🔒 Security Features

- Password hashing using bcrypt
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- Session-based authentication
- Admin access control
- CSRF protection ready

## 📱 Responsive Design

The application is fully responsive and works seamlessly on:
- Desktop computers
- Tablets
- Mobile phones

## 🛠️ Customization

### Changing Site Name
Edit `includes/config.php`:
```php
define('SITE_NAME', 'Your Shop Name');
```

### Modifying Colors
The primary colors can be changed in `includes/header.php`:
```css
:root {
    --primary-color: #e91e63;
    --secondary-color: #9c27b0;
}
```

### Adding New Categories
Use the admin panel at `/admin/categories.php` or insert directly into database.

## 🐛 Troubleshooting

### Database Connection Error
- Verify database credentials in `includes/config.php`
- Ensure MySQL service is running
- Check if database exists

### Upload Errors
- Check folder permissions: `chmod 755 uploads/`
- Verify PHP file_uploads is enabled
- Check upload_max_filesize in php.ini

### Session Issues
- Ensure session.save_path is writable
- Check if sessions are enabled in php.ini

## 📄 License

This project is open source and available for educational purposes.

## 👨‍💻 Developer Notes

### Adding New Features
1. Create new database tables if needed
2. Add functions to `includes/functions.php`
3. Create page files in root or admin directory
4. Update navigation in `includes/header.php`

### Code Standards
- Follow PSR coding standards
- Use prepared statements for all database queries
- Sanitize all user inputs
- Use meaningful variable and function names

## 🤝 Support

For issues or questions:
- Check the troubleshooting section
- Review the code comments
- Contact: admin@shopping.com

## 📝 Future Enhancements

Potential features to add:
- [ ] Product reviews and ratings
- [ ] Wishlist functionality
- [ ] Email notifications
- [ ] Payment gateway integration
- [ ] Invoice generation (PDF)
- [ ] Advanced reporting
- [ ] Product variations (size, color)
- [ ] Coupon/discount system
- [ ] Multi-language support

## 🎉 Getting Started

1. Set up database using `database.sql`
2. Configure `includes/config.php`
3. Access the site at `http://localhost/webapp/`
4. Login to admin panel with default credentials
5. Add categories and products
6. Create a customer account and test shopping

Enjoy your online shopping cart system! 🛒
