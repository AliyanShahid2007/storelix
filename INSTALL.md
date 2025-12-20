# Installation Guide - Online Shopping Cart

## Quick Start Guide

Follow these steps to install and set up your shopping cart system.

## Prerequisites

Before you begin, ensure you have:
- A web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- phpMyAdmin (optional, for easier database management)

## Step-by-Step Installation

### Step 1: Download/Extract Files

1. Download the project files
2. Extract to your web server directory:
   - XAMPP: `C:/xampp/htdocs/shopping-cart/`
   - WAMP: `C:/wamp64/www/shopping-cart/`
   - Linux: `/var/www/html/shopping-cart/`

### Step 2: Create Database

#### Option A: Using phpMyAdmin
1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Click on "New" in the left sidebar
3. Enter database name: `shopping_cart`
4. Click "Create"
5. Select the newly created database
6. Click on "Import" tab
7. Choose file `database.sql` from the project
8. Click "Go" at the bottom

#### Option B: Using MySQL Command Line
```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE shopping_cart;

# Exit MySQL
exit;

# Import database file
mysql -u root -p shopping_cart < database.sql
```

### Step 3: Configure Database Connection

1. Open `includes/config.php`
2. Update the database settings:

```php
// Database Configuration
define('DB_HOST', 'localhost');      // Usually 'localhost'
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password (empty for XAMPP)
define('DB_NAME', 'shopping_cart');  // Database name

// Site Configuration
define('SITE_NAME', 'Arts Shopping Cart');
define('SITE_URL', 'http://localhost/shopping-cart');
define('ADMIN_EMAIL', 'admin@shopping.com');
```

### Step 4: Set File Permissions

#### On Linux/Mac:
```bash
cd /path/to/shopping-cart
chmod 755 uploads/
chmod 644 includes/config.php
```

#### On Windows:
- Right-click on `uploads` folder
- Properties → Security → Edit
- Give "Users" write permissions

### Step 5: Start Web Server

#### XAMPP:
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL

#### WAMP:
1. Start WAMP
2. Ensure both Apache and MySQL are green

#### Linux (Apache):
```bash
sudo systemctl start apache2
sudo systemctl start mysql
```

### Step 6: Access the Application

1. Open your browser
2. Navigate to: `http://localhost/shopping-cart/`
3. You should see the homepage

### Step 7: Login to Admin Panel

1. Go to: `http://localhost/shopping-cart/admin/dashboard.php`
2. Use default credentials:
   - **Username:** admin
   - **Password:** admin123
3. **Important:** Change the admin password immediately!

## Post-Installation Steps

### 1. Change Admin Password

Currently, you need to change it directly in the database:

```sql
# Generate new password hash using PHP
php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"

# Update in database
UPDATE users SET password = 'paste_generated_hash_here' WHERE username = 'admin';
```

### 2. Add Categories

1. Go to Admin Panel → Categories
2. Click "Add New Category"
3. Add your product categories

### 3. Add Products

1. Go to Admin Panel → Products
2. Click "Add New Product"
3. Fill in product details
4. Upload product image
5. Set stock quantity

### 4. Test Customer Experience

1. Logout from admin
2. Register as a new customer
3. Browse products
4. Add items to cart
5. Complete checkout process

## Troubleshooting

### Issue: Database Connection Error

**Solution:**
- Check database credentials in `includes/config.php`
- Verify MySQL is running
- Ensure database exists

### Issue: Can't Upload Images

**Solution:**
```bash
# Check folder permissions
ls -la uploads/

# Set correct permissions
chmod 755 uploads/
```

### Issue: Blank Page / White Screen

**Solution:**
1. Enable error reporting in `includes/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

2. Check Apache/PHP error logs:
   - XAMPP: `xampp/apache/logs/error.log`
   - Linux: `/var/log/apache2/error.log`

### Issue: Session Errors

**Solution:**
- Check PHP session configuration in `php.ini`
- Ensure session save path is writable
- Clear browser cookies/cache

## Configuration Options

### Change Site Name

Edit `includes/config.php`:
```php
define('SITE_NAME', 'Your Store Name');
```

### Change Upload Limits

Edit `.htaccess` or `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

### Enable/Disable Registration

Edit `register.php` to add restrictions or disable registration.

## Security Recommendations

### 1. Change Database Credentials
Don't use default MySQL root account. Create a specific user:

```sql
CREATE USER 'shopping_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON shopping_cart.* TO 'shopping_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Update Configuration
Update `includes/config.php` with new credentials.

### 3. Secure Admin Panel
Add `.htaccess` in `admin/` folder:

```apache
# Restrict access by IP (optional)
Order Deny,Allow
Deny from all
Allow from 127.0.0.1
Allow from your.ip.address
```

### 4. SSL Certificate
For production, always use HTTPS:
- Get SSL certificate (Let's Encrypt is free)
- Update SITE_URL to use https://

### 5. Regular Backups
- Backup database regularly
- Backup uploaded images in `uploads/` folder

## Production Deployment

### Before Going Live:

1. **Disable Error Display**
   ```php
   // In config.php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

2. **Update Site URL**
   ```php
   define('SITE_URL', 'https://yourdomain.com');
   ```

3. **Enable HTTPS**
   - Install SSL certificate
   - Force HTTPS in `.htaccess`

4. **Secure Database**
   - Use strong passwords
   - Restrict remote access

5. **Set Strict Permissions**
   ```bash
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   chmod 755 uploads/
   ```

## System Requirements

### Minimum:
- PHP 7.4+
- MySQL 5.7+
- Apache 2.4+
- 256MB RAM
- 100MB Disk Space

### Recommended:
- PHP 8.0+
- MySQL 8.0+
- Apache 2.4+ or Nginx
- 512MB+ RAM
- 1GB+ Disk Space
- SSL Certificate

## Testing Checklist

- [ ] Homepage loads correctly
- [ ] Products display with images
- [ ] Category filtering works
- [ ] Search functionality works
- [ ] User registration works
- [ ] User login works
- [ ] Add to cart functions
- [ ] Cart updates properly
- [ ] Checkout process completes
- [ ] Order confirmation displays
- [ ] Admin login works
- [ ] Admin can add/edit products
- [ ] Admin can manage orders
- [ ] Order status updates work

## Getting Help

If you encounter issues:

1. Check this installation guide
2. Review README.md
3. Check error logs
4. Verify all requirements are met
5. Test with default database data

## Congratulations! 🎉

Your shopping cart is now installed and ready to use!

**Next Steps:**
1. Login to admin panel
2. Add your categories
3. Upload products
4. Customize site settings
5. Test the complete workflow
6. Launch your store!

Happy Selling! 🛒
