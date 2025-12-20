-- create database 

CREATE DATABASE IF NOT EXISTS shopping_cart;
USE shopping_cart;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'employee', 'customer') DEFAULT 'customer',
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLEust IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Cart Table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
);
-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Special Offers Table
CREATE TABLE IF NOT EXISTS special_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    discount_percentage DECIMAL(5,2) NOT NULL,
    end_time DATETIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Live Chat Tables
CREATE TABLE IF NOT EXISTS chat_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, -- NULL for guest users
    guest_name VARCHAR(100) NULL,
    guest_email VARCHAR(100) NULL,
    status ENUM('active', 'closed', 'waiting') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT,
    sender_type ENUM('user', 'admin') NOT NULL,
    sender_id INT NULL, -- user_id for users, admin_id for admins
    user_id INT NULL, -- For logged-in users
    guest_name VARCHAR(100) NULL, -- For guest users
    guest_email VARCHAR(100) NULL, -- For guest users
    message TEXT NOT NULL,
    file_path VARCHAR(255) NULL, -- For file attachments
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES chat_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);




-- Insert Default Admin User (password: admin123)
INSERT INTO users (username, email, password, full_name, is_admin)
VALUES ('admin', 'admin@shopping.com', '$2y$10$ECyDxX0LZDEPxPaYSdERp.UJuaexZN0ZkbXeJ2Qu/dQGd4O.5..Pa', 'Administrator', 1);


-- Insert Sample Categories
INSERT INTO categories (name, description, image) VALUES
('Greeting Cards', 'Beautiful greeting cards for all occasions', 'greeting-cards.jpg'),
('Gift Articles', 'Unique gift items for your loved ones', 'gift-articles.jpg'),
('Handbags', 'Stylish handbags and wallets', 'handbags.jpg'),
('Beauty Products', 'Quality beauty and cosmetic products', 'beauty-products.jpg'),
('Stationery', 'Office and school stationery items', 'stationery.jpg'),
('Dolls & Toys', 'Fun toys and collectible dolls', 'dolls-toys.jpg');

-- Insert Sample Products
INSERT INTO products (category_id, name, description, price, stock, featured) VALUES
(1, 'Birthday Card - Floral', 'Beautiful floral design birthday card', 3.99, 100, 1),
(1, 'Anniversary Card - Romantic', 'Elegant romantic anniversary card', 4.99, 80, 1),
(2, 'Decorative Vase', 'Hand-crafted ceramic vase', 29.99, 50, 1),
(2, 'Photo Frame Set', 'Set of 3 wooden photo frames', 24.99, 60, 0),
(3, 'Leather Handbag', 'Premium leather handbag', 89.99, 30, 1),
(3, 'Designer Wallet', 'Stylish designer wallet', 39.99, 45, 0),
(4, 'Lipstick Set', 'Set of 5 matte lipsticks', 19.99, 70, 1),
(4, 'Skincare Kit', 'Complete skincare routine kit', 49.99, 40, 0),
(5, 'Notebook Bundle', '5 premium notebooks', 14.99, 120, 0),
(5, 'Pen Collection', 'Set of 10 gel pens', 9.99, 150, 0),
(6, 'Teddy Bear - Large', 'Soft and cuddly teddy bear', 34.99, 55, 1),
(6, 'Collectible Doll', 'Limited edition collectible doll', 59.99, 25, 0);
