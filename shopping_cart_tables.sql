-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 06:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopping_cart`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(2, 2, 22, 1, '2025-12-23 12:33:26');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `created_at`) VALUES
(1, 'Mobile Accessories', 'Every Brand Mobile Accessories', 'greeting-cards.jpg', '2025-12-23 11:35:43'),
(2, 'Shoes', 'Shoes For All Genders', 'gift-articles.jpg', '2025-12-23 11:35:43'),
(3, 'Handbags', 'Stylish handbags and wallets', 'handbags.jpg', '2025-12-23 11:35:43'),
(4, 'Watches', 'Every Brand Watches Available', 'beauty-products.jpg', '2025-12-23 11:35:43'),
(5, 'Cloths', 'Wear In Style', 'stationery.jpg', '2025-12-23 11:35:43'),
(6, 'Dolls & Toys', 'Fun toys and collectible dolls', 'dolls-toys.jpg', '2025-12-23 11:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_banners`
--

CREATE TABLE `homepage_banners` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_stats`
--

CREATE TABLE `homepage_stats` (
  `id` int(11) NOT NULL,
  `stat_key` varchar(50) NOT NULL,
  `stat_value` varchar(100) NOT NULL,
  `stat_label` varchar(100) NOT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_stats`
--

INSERT INTO `homepage_stats` (`id`, `stat_key`, `stat_value`, `stat_label`, `icon_class`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 'happy_customers', '7000', 'Happy Customers', '0', 1, 1, '2025-12-27 11:03:28', '2025-12-27 11:20:40'),
(5, 'products_delivered', '15000+', 'Products Delivered', 'fa-box', 2, 1, '2025-12-27 11:03:28', '2025-12-27 11:03:28'),
(6, 'revenue_generated', '$500,000+', 'Revenue Generated', 'fa-dollar-sign', 3, 1, '2025-12-27 11:03:28', '2025-12-27 11:03:28'),
(7, 'years_service', '5+', 'Years of Service', 'fa-calendar-alt', 4, 1, '2025-12-27 11:03:28', '2025-12-27 11:03:28');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_mode`
--

CREATE TABLE `maintenance_mode` (
  `id` int(11) NOT NULL,
  `is_enabled` tinyint(1) DEFAULT 0,
  `title` varchar(255) DEFAULT 'Site Under Maintenance',
  `message` text DEFAULT 'We are currently performing maintenance. Please check back soon.',
  `estimated_time` varchar(100) DEFAULT NULL,
  `show_timer` tinyint(1) DEFAULT 0,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unsubscribed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `name`, `is_active`, `subscribed_at`, `unsubscribed_at`) VALUES
(1, 'anas@gmail.com', 'anas@gmail.com', 1, '2025-12-23 13:09:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `news_announcements`
--

CREATE TABLE `news_announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_announcements`
--

INSERT INTO `news_announcements` (`id`, `title`, `content`, `image`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'hello', 'how are you', '', 0, 1, '2025-12-23 12:57:07', '2025-12-23 12:57:07'),
(2, '🎉 New Year Mega Sale - Up to 50% Off!', 'Get ready to celebrate with our exclusive New Year sale! Shop your favorite gifts, cards, and products at unbeatable prices. Limited time offer - don\'t miss out on amazing deals across all categories!', NULL, 1, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50'),
(3, '✨ Exclusive Premium Collection Launched', 'Introducing our brand new premium collection of handcrafted gifts and luxury items. Each piece is carefully selected for its quality and elegance. Explore our curated selection and find the perfect gift for your loved ones.', NULL, 1, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50'),
(4, '🚚 Free Shipping on Orders Over $50', 'We\'re excited to announce free shipping on all orders over $50! Plus, get 10% off your first purchase with code WELCOME10. Shop now and enjoy fast, free delivery to your doorstep.', NULL, 0, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50'),
(5, '🌟 Holiday Gift Guide 2024 - Perfect Presents for Everyone', 'Discover our comprehensive Holiday Gift Guide 2024! From elegant stationery to unique gifts, find the perfect present for every occasion. Explore curated collections and special holiday bundles.', NULL, 1, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50'),
(6, '💳 New Payment Methods Added - PayPal & Apple Pay Now Available', 'We\'ve expanded our payment options! Now accept PayPal and Apple Pay for faster, more secure checkout. Enjoy seamless transactions and enhanced shopping experience with multiple payment methods.', NULL, 0, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50'),
(7, '🎁 Customer Loyalty Program Launched - Earn Points on Every Purchase', 'Join our new Customer Loyalty Program! Earn points on every purchase and redeem them for exclusive discounts, free shipping, and special member-only offers. Start earning rewards today!', NULL, 1, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50'),
(8, '📦 Eco-Friendly Packaging Initiative - Going Green Together', 'We\'re committed to sustainability! Introducing our new eco-friendly packaging made from recycled materials. Help us reduce environmental impact while enjoying your favorite products.', NULL, 0, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50'),
(9, '🎨 Custom Gift Wrapping Service Now Available', 'Make your gifts extra special with our new custom gift wrapping service! Choose from various themes, colors, and add personal messages. Perfect for birthdays, anniversaries, and holidays.', NULL, 1, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50'),
(10, '🏆 Store Anniversary Sale - 25% Off Everything', 'Celebrating our 5th anniversary! Enjoy 25% off on all products for a limited time. Thank you for being part of our journey - here\'s to many more years of great shopping!', NULL, 1, 1, '2025-12-23 13:31:50', '2025-12-23 13:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(5, 1, 'New Order Placed', 'A new order #1 has been placed and requires approval.', 0, '2025-12-23 12:32:59'),
(6, 2, 'Order Confirmed', 'Your order #1 has been confirmed and is now being processed.', 0, '2025-12-25 16:22:08');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_address`, `created_at`, `updated_at`) VALUES
(1, 2, 9.56, 'delivered', 'Cash on Delivery', 'aAaWZq', '2025-12-23 12:32:59', '2025-12-25 16:27:04');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 29, 1, 9.56);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `image`, `featured`, `created_at`, `updated_at`) VALUES
(21, 1, 'Wireless Charger for Iphone', 'Battery Pack Wireless Charger for Iphone Magsafe Wireless Power Bank 5000 MAH 20Watt Fast Charging', 32.12, 100, '69482f1721b4f_1766338327.png', 1, '2025-12-23 11:36:25', '2025-12-23 11:36:25'),
(22, 1, 'Leather Case for Iphone', 'Luxury Leather Invisible Stand for iPhone Case, Magnetic Foldable Stand Case Cover for iPhone 17/16 Pro Max', 10.70, 80, '69482fab2b7db_1766338475.png', 1, '2025-12-23 11:36:25', '2025-12-23 11:36:25'),
(23, 2, 'Black Camel Shoes', 'Black Camel Shoes for Mens | Walking Casual Sketchers Sneakers | Super Comfort', 6.27, 50, '69482d8786013_1766337927.png', 1, '2025-12-23 11:36:25', '2025-12-23 11:36:25'),
(24, 2, 'Sneakers For Men', 'Sneakers Shoes for Men - Perfect for Daily Casual Wear', 5.03, 60, '69482e1652a6d_1766338070.png', 1, '2025-12-23 11:36:25', '2025-12-23 11:36:25'),
(25, 3, 'Leather Handbag', 'Leather Handbag for Girls, Casual Crossbody and Shoulder Bag', 7.13, 30, '694830020b7dd_1766338562.png', 1, '2025-12-23 11:36:25', '2025-12-23 11:36:25'),
(26, 3, 'MSK Dunbollu 2025', 'New Genuine Leather Men Wallet Small Mini Card Holder', 9.28, 45, '6948305a06401_1766338650.png', 0, '2025-12-23 11:36:25', '2025-12-23 11:36:25'),
(27, 4, 'Arabic Numerical Watch', 'Premium Arabic Numerals Wrist Watch – Silicon Strap | Unisex Fashion Watch', 12.49, 70, '694830cbea31c_1766338763.png', 1, '2025-12-23 11:36:25', '2025-12-23 11:36:25'),
(28, 4, 'Richard Mille RM', 'Richard Mille RM 011 Red TPT Quartz Automatic Flyback Watch', 21.41, 40, '6948312e40ec8_1766338862.png', 1, '2025-12-23 11:36:25', '2025-12-23 11:36:25'),
(29, 5, 'Brown Hoodie', 'Brown pullover warm fleece kangaroo hoodie for men and boys', 9.56, 119, '694831a79b72f_1766338983.png', 1, '2025-12-23 11:36:25', '2025-12-23 12:32:59'),
(30, 5, 'MEN\'S QUARTER ZIPPER', 'MEN\'S QUARTER ZIPPER TURTLE NECK SWEATERS', 12.84, 150, '6948321553da3_1766339093.png', 0, '2025-12-23 11:36:25', '2025-12-23 11:36:25');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` int(11) NOT NULL,
  `platform` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `special_offers`
--

CREATE TABLE `special_offers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `end_time` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `special_offers`
--

INSERT INTO `special_offers` (`id`, `title`, `description`, `discount_percentage`, `end_time`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Winter Sale', 'Up to 20% off on winter collection', 20.00, '2026-01-31 23:59:59', 1, '2025-12-23 11:37:37', '2025-12-23 11:37:37'),
(2, 'New Year Sale', '10% off storewide', 10.00, '2026-01-01 23:59:59', 1, '2025-12-23 11:37:37', '2025-12-23 11:37:37');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_image` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `review_text` text NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `customer_name`, `customer_image`, `rating`, `review_text`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Aliyan', '', 5, 'Good product', 0, 1, '2025-12-23 12:58:53', '2025-12-23 12:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','employee','customer') DEFAULT 'customer',
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@shopping.com', '$2y$10$ECyDxX0LZDEPxPaYSdERp.UJuaexZN0ZkbXeJ2Qu/dQGd4O.5..Pa', 'Administrator', NULL, NULL, 'customer', 1, '2025-12-23 11:37:13', '2025-12-23 11:37:13'),
(2, 'Abdul Aliyan', 'aliyanshahid439@gmail.com', '$2y$10$khN0Gm1mjCxZ3zZlAUrgRuhnrUpQWT62eP2xe5t3gV7neyPjPUoii', 'Abdul Aliyan', '3202043624', NULL, 'employee', 0, '2025-12-23 11:37:13', '2025-12-23 11:37:13'),
(3, 'ayan', 'ayan@gmail.com', '$2y$10$GZpMDwKH5VNyLldkEiLWg.UKEEKSvGVYq6ySTlK5MpSd2.FrRADpG', 'ayan', NULL, NULL, 'customer', 0, '2025-12-23 11:37:13', '2025-12-23 11:37:13'),
(4, 'ali', 'ali@gmail.com', '$2y$10$h11GocgzaVcbr94XdseM2ucA61G6daags.fuAzSfUkqGRb/UVaHtm', 'ali', '03204060322', 'asas', 'employee', 0, '2025-12-23 12:43:20', '2025-12-23 12:43:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_banners`
--
ALTER TABLE `homepage_banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_stats`
--
ALTER TABLE `homepage_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stat_key` (`stat_key`);

--
-- Indexes for table `maintenance_mode`
--
ALTER TABLE `maintenance_mode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `news_announcements`
--
ALTER TABLE `news_announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `special_offers`
--
ALTER TABLE `special_offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `homepage_banners`
--
ALTER TABLE `homepage_banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `homepage_stats`
--
ALTER TABLE `homepage_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `maintenance_mode`
--
ALTER TABLE `maintenance_mode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `news_announcements`
--
ALTER TABLE `news_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `special_offers`
--
ALTER TABLE `special_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
