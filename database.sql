-- create database 



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
CREATE TABLE IF NOT EXISTS order_items (
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

-- Homepage Banners Table
CREATE TABLE IF NOT EXISTS homepage_banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    description TEXT,
    image VARCHAR(255),
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Testimonials Table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_image VARCHAR(255),
    rating INT DEFAULT 5,
    review_text TEXT NOT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- News & Announcements Table
CREATE TABLE IF NOT EXISTS news_announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Homepage Statistics Table
CREATE TABLE IF NOT EXISTS homepage_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_key VARCHAR(50) UNIQUE NOT NULL,
    stat_value VARCHAR(100) NOT NULL,
    stat_label VARCHAR(100) NOT NULL,
    icon_class VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Social Media Links Table
CREATE TABLE IF NOT EXISTS social_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon_class VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL
);

-- Maintenance Mode Table
CREATE TABLE IF NOT EXISTS maintenance_mode (
    id INT AUTO_INCREMENT PRIMARY KEY,
    is_enabled TINYINT(1) DEFAULT 0,
    title VARCHAR(255) DEFAULT 'Site Under Maintenance',
    message TEXT DEFAULT 'We are currently performing maintenance. Please check back soon.',
    estimated_time VARCHAR(100),
    show_timer TINYINT(1) DEFAULT 0,
    end_time DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);





-- Insert Default Admin User (password: admin123)
INSERT INTO users (username, email, password, full_name, is_admin)
VALUES ('admin', 'admin@shopping.com', '$2y$10$ECyDxX0LZDEPxPaYSdERp.UJuaexZN0ZkbXeJ2Qu/dQGd4O.5..Pa', 'Administrator', 1);


-- Insert Sample Categories
INSERT INTO categories (`name`, `description`, `image`) VALUES
('Mobile Accessories', 'Every Brand Mobile Accessories', 'greeting-cards.jpg'),
('Shoes', 'Shoes For All Genders', 'gift-articles.jpg'),
('Handbags', 'Stylish handbags and wallets', 'handbags.jpg'),
('Watches', 'Every Brand Watches Available', 'beauty-products.jpg'),
('Cloths', 'Wear In Style', 'stationery.jpg'),
('Dolls & Toys', 'Fun toys and collectible dolls', 'dolls-toys.jpg');

-- Insert Sample Products
INSERT INTO products (category_id, name, description, price, stock, featured) VALUES
(1, 'Wireless Charger for Iphone', 'Battery Pack Wireless Charger for Iphone Magsafe Wireless Power Bank 5000 MAH 20Watt Fast Charging', 32.12, 100, '69482f1721b4f_1766338327.png', 1),
(1, 'Leather Case for Iphone', 'Luxury Leather Invisible Stand for iPhone Case, Magnetic Foldable Stand Case Cover for iPhone 17/16 Pro Max', 10.70, 80, '69482fab2b7db_1766338475.png', 1),
(2, 'Black Camel Shoes', 'Black Camel Shoes for Mens | Walking Casual Sketchers Sneakers | Super Comfort', 6.27, 50, '69482d8786013_1766337927.png', 1),
(2, 'Sneakers For Men', 'Sneakers Shoes for Men - Perfect for Daily Casual Wear', 5.03, 60, '69482e1652a6d_1766338070.png', 1),
(3, 'Leather Handbag', 'Leather Handbag for Girls, Casual Crossbody and Shoulder Bag', 7.13, 30, '694830020b7dd_1766338562.png', 1),
(3, 'MSK Dunbollu 2025', 'New Genuine Leather Men Wallet Small Mini Card Holder', 9.28, 45, '6948305a06401_1766338650.png', 0),
(4, 'Arabic Numerical Watch', 'Premium Arabic Numerals Wrist Watch – Silicon Strap | Unisex Fashion Watch', 12.49, 70, '694830cbea31c_1766338763.png', 1),
(4, 'Richard Mille RM', 'Richard Mille RM 011 Red TPT Quartz Automatic Flyback Watch', 21.41, 40, '6948312e40ec8_1766338862.png', 1),
(5, 'Brown Hoodie', 'Brown pullover warm fleece kangaroo hoodie for men and boys', 9.56, 120, '694831a79b72f_1766338983.png', 1),
(5, 'MEN\'S QUARTER ZIPPER', 'MEN\'S QUARTER ZIPPER TURTLE NECK SWEATERS', 12.84, 150, '6948321553da3_1766339093.png', 0);

-- Insert Sample News & Announcements
INSERT INTO news_announcements (title, content, is_featured, is_active) VALUES
('🎉 New Year Mega Sale - Up to 50% Off!', 'Get ready to celebrate with our exclusive New Year sale! Shop your favorite gifts, cards, and products at unbeatable prices. Limited time offer - don\'t miss out on amazing deals across all categories!', 1, 1),
('✨ Exclusive Premium Collection Launched', 'Introducing our brand new premium collection of handcrafted gifts and luxury items. Each piece is carefully selected for its quality and elegance. Explore our curated selection and find the perfect gift for your loved ones.', 1, 1),
('🚚 Free Shipping on Orders Over $50', 'We\'re excited to announce free shipping on all orders over $50! Plus, get 10% off your first purchase with code WELCOME10. Shop now and enjoy fast, free delivery to your doorstep.', 0, 1),
('🌟 Holiday Gift Guide 2024 - Perfect Presents for Everyone', 'Discover our comprehensive Holiday Gift Guide 2024! From elegant stationery to unique gifts, find the perfect present for every occasion. Explore curated collections and special holiday bundles.', 1, 1),
('💳 New Payment Methods Added - PayPal & Apple Pay Now Available', 'We\'ve expanded our payment options! Now accept PayPal and Apple Pay for faster, more secure checkout. Enjoy seamless transactions and enhanced shopping experience with multiple payment methods.', 0, 1),
('🎁 Customer Loyalty Program Launched - Earn Points on Every Purchase', 'Join our new Customer Loyalty Program! Earn points on every purchase and redeem them for exclusive discounts, free shipping, and special member-only offers. Start earning rewards today!', 1, 1),
('📦 Eco-Friendly Packaging Initiative - Going Green Together', 'We\'re committed to sustainability! Introducing our new eco-friendly packaging made from recycled materials. Help us reduce environmental impact while enjoying your favorite products.', 0, 1),
('🎨 Custom Gift Wrapping Service Now Available', 'Make your gifts extra special with our new custom gift wrapping service! Choose from various themes, colors, and add personal messages. Perfect for birthdays, anniversaries, and holidays.', 1, 1),
('🏆 Store Anniversary Sale - 25% Off Everything', 'Celebrating our 5th anniversary! Enjoy 25% off on all products for a limited time. Thank you for being part of our journey - here\'s to many more years of great shopping!', 1, 1);
