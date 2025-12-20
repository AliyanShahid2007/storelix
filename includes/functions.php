
<?php
require_once 'db.php';

// User Authentication Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function isEmployee() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'employee';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit();
    }
}

function requireEmployee() {
    if (!isEmployee()) {
        header('Location: ../index.php');
        exit();
    }
}

function login($username, $password) {
    global $db;
    $username = $db->escape($username);
    
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}

function register($username, $email, $password, $full_name, $phone = '', $address = '') {
    global $db;
    
    $username = $db->escape($username);
    $email = $db->escape($email);
    $full_name = $db->escape($full_name);
    $phone = $db->escape($phone);
    $address = $db->escape($address);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $full_name, $phone, $address);
    
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Product Functions
function getProducts($category_id = null, $featured = false, $limit = null) {
    global $db;
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
    
    if ($category_id) {
        $sql .= " AND p.category_id = " . intval($category_id);
    }
    
    if ($featured) {
        $sql .= " AND p.featured = 1";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getProductById($id) {
    global $db;
    $id = intval($id);
    $result = $db->query("SELECT p.*, c.name as category_name FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = $id");
    return $result->fetch_assoc();
}

function searchProducts($keyword) {
    global $db;
    $keyword = $db->escape($keyword);
    $result = $db->query("SELECT p.*, c.name as category_name FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.name LIKE '%$keyword%' OR p.description LIKE '%$keyword%'
                          ORDER BY p.created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Category Functions
function getCategories() {
    global $db;
    $result = $db->query("SELECT * FROM categories ORDER BY name ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getCategoryById($id) {
    global $db;
    $id = intval($id);
    $result = $db->query("SELECT * FROM categories WHERE id = $id");
    return $result->fetch_assoc();
}

// Cart Functions
function getCartItems($user_id) {
    global $db;
    $user_id = intval($user_id);
    $result = $db->query("SELECT c.*, p.name, p.price, p.image, p.stock 
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          WHERE c.user_id = $user_id");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getCartCount($user_id) {
    global $db;
    $user_id = intval($user_id);
    $result = $db->query("SELECT SUM(quantity) as count FROM cart WHERE user_id = $user_id");
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}

function getCartTotal($user_id) {
    global $db;
    $user_id = intval($user_id);
    $result = $db->query("SELECT SUM(c.quantity * p.price) as total 
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          WHERE c.user_id = $user_id");
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

function addToCart($user_id, $product_id, $quantity = 1) {
    global $db;
    $user_id = intval($user_id);
    $product_id = intval($product_id);
    $quantity = intval($quantity);
    
    $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) 
                          VALUES (?, ?, ?) 
                          ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
    return $stmt->execute();
}

function updateCartQuantity($user_id, $product_id, $quantity) {
    global $db;
    $user_id = intval($user_id);
    $product_id = intval($product_id);
    $quantity = intval($quantity);
    
    if ($quantity <= 0) {
        return removeFromCart($user_id, $product_id);
    }
    
    $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    return $stmt->execute();
}

function removeFromCart($user_id, $product_id) {
    global $db;
    $user_id = intval($user_id);
    $product_id = intval($product_id);
    
    $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    return $stmt->execute();
}

function clearCart($user_id) {
    global $db;
    $user_id = intval($user_id);
    $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

// Order Functions
function createOrder($user_id, $payment_method, $shipping_address) {
    global $db;
    
    $cart_items = getCartItems($user_id);
    if (empty($cart_items)) {
        return false;
    }
    
    $total = getCartTotal($user_id);
    
    $db->getConnection()->begin_transaction();
    
    try {
        $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, payment_method, shipping_address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $user_id, $total, $payment_method, $shipping_address);
        $stmt->execute();
        
        $order_id = $db->lastInsertId();
        
        foreach ($cart_items as $item) {
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
            
            $stmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmt->execute();
        }
        
        clearCart($user_id);

        // Notify admin about new order
        $admin_users = getAdminUsers();
        foreach ($admin_users as $admin) {
            createNotification($admin['id'], 'New Order Placed', 'A new order #' . $order_id . ' has been placed and requires approval.');
        }

        $db->getConnection()->commit();
        return $order_id;
    } catch (Exception $e) {
        $db->getConnection()->rollback();
        return false;
    }
}

function getOrders($user_id = null) {
    global $db;
    
    $sql = "SELECT o.*, u.username, u.email FROM orders o 
            JOIN users u ON o.user_id = u.id";
    
    if ($user_id) {
        $user_id = intval($user_id);
        $sql .= " WHERE o.user_id = $user_id";
    }
    
    $sql .= " ORDER BY o.created_at DESC";
    
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getOrderById($order_id) {
    global $db;
    $order_id = intval($order_id);
    $result = $db->query("SELECT o.*, u.username, u.email, u.phone 
                          FROM orders o 
                          JOIN users u ON o.user_id = u.id 
                          WHERE o.id = $order_id");
    return $result->fetch_assoc();
}

function getOrderItems($order_id) {
    global $db;
    $order_id = intval($order_id);
    $result = $db->query("SELECT oi.*, p.name, p.image 
                          FROM order_items oi 
                          JOIN products p ON oi.product_id = p.id 
                          WHERE oi.order_id = $order_id");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUserById($user_id) {
    global $db;
    $user_id = intval($user_id);
    $result = $db->query("SELECT * FROM users WHERE id = $user_id");
    return $result->fetch_assoc();
}

// Utility Functions
function formatPrice($price) {
    // Assuming 1 USD = 280 PKR
    $pkr_price = $price * 280;
    return 'PKR ' . number_format($pkr_price, 0);
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function uploadImage($file, $upload_dir = '') {
    if (!$upload_dir) {
        $upload_dir = UPLOAD_DIR;
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Upload failed'];
}

function getStats() {
    global $db;

    $stats = [];

    $result = $db->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0");
    $stats['users'] = $result->fetch_assoc()['count'];

    $result = $db->query("SELECT COUNT(*) as count FROM products");
    $stats['products'] = $result->fetch_assoc()['count'];

    $result = $db->query("SELECT COUNT(*) as count FROM orders");
    $stats['orders'] = $result->fetch_assoc()['count'];

    $result = $db->query("SELECT SUM(total_amount) as total FROM orders");
    $stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

    return $stats;
}

function getAdminUsers() {
    global $db;
    $result = $db->query("SELECT id, username FROM users WHERE is_admin = 1");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function cancelOrder($order_id, $user_id) {
    global $db;

    $order_id = intval($order_id);
    $user_id = intval($user_id);

    // Check if order exists and belongs to user and is pending
    $stmt = $db->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        if ($order['status'] == 'pending') {
            // Update order status to cancelled
            $stmt = $db->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            return $stmt->execute();
        }
    }
    return false;
}

function updateOrderStatus($order_id, $status) {
    global $db;
    
    $order_id = intval($order_id);
    $status = $db->escape($status);
    
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    return $stmt->execute();
}

// Notification Functions
function createNotification($user_id, $title, $message) {
    global $db;
    $user_id = intval($user_id);
    $title = $db->escape($title);
    $message = $db->escape($message);

    $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $message);
    return $stmt->execute();
}

function getNotifications($user_id, $limit = 10) {
    global $db;
    $user_id = intval($user_id);
    $limit = intval($limit);
    $result = $db->query("SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT $limit");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUnreadNotificationsCount($user_id) {
    global $db;
    $user_id = intval($user_id);
    $result = $db->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = $user_id AND is_read = 0");
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}

function markNotificationAsRead($notification_id, $user_id) {
    global $db;
    $notification_id = intval($notification_id);
    $user_id = intval($user_id);

    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notification_id, $user_id);
    return $stmt->execute();
}

function getRecentActivities($limit = 10) {
    global $db;
    $limit = intval($limit);
    $activities = [];

    // Calculate limit per type (ensure integer)
    $per_type_limit = intval(ceil($limit / 3));

    // Get recent orders
    $stmt = $db->prepare("SELECT 'order' as type, CONCAT('New order #', o.id, ' placed by ', u.username) as description, o.created_at
                          FROM orders o
                          JOIN users u ON o.user_id = u.id
                          ORDER BY o.created_at DESC LIMIT ?");
    $stmt->bind_param("i", $per_type_limit);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }

    // Get recent products
    $stmt = $db->prepare("SELECT 'product' as type, CONCAT('New product added: ', name) as description, created_at
                          FROM products
                          ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $per_type_limit);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }

    // Get recent notifications
    $stmt = $db->prepare("SELECT 'notification' as type, title as description, created_at
                          FROM notifications
                          ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $per_type_limit);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }

    // Sort by created_at descending
    usort($activities, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    return array_slice($activities, 0, $limit);
}

// Special Offer Functions
function getActiveSpecialOffer() {
    global $db;
    $result = $db->query("SELECT * FROM special_offers WHERE is_active = 1 AND end_time > NOW() ORDER BY created_at DESC LIMIT 1");
    return $result->fetch_assoc();
}

function getSpecialOfferById($id) {
    global $db;
    $id = intval($id);
    $result = $db->query("SELECT * FROM special_offers WHERE id = $id");
    return $result->fetch_assoc();
}

function getAllSpecialOffers() {
    global $db;
    $result = $db->query("SELECT * FROM special_offers ORDER BY created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createSpecialOffer($title, $description, $discount_percentage, $end_time) {
    global $db;
    $title = $db->escape($title);
    $description = $db->escape($description);
    $discount_percentage = floatval($discount_percentage);

    $stmt = $db->prepare("INSERT INTO special_offers (title, description, discount_percentage, end_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $title, $description, $discount_percentage, $end_time);
    return $stmt->execute();
}

function updateSpecialOffer($id, $title, $description, $discount_percentage, $end_time, $is_active) {
    global $db;
    $id = intval($id);
    $title = $db->escape($title);
    $description = $db->escape($description);
    $discount_percentage = floatval($discount_percentage);
    $is_active = intval($is_active);

    $stmt = $db->prepare("UPDATE special_offers SET title = ?, description = ?, discount_percentage = ?, end_time = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("ssdsii", $title, $description, $discount_percentage, $end_time, $is_active, $id);
    return $stmt->execute();
}

function deleteSpecialOffer($id) {
    global $db;
    $id = intval($id);
    $stmt = $db->prepare("DELETE FROM special_offers WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Live Chat Functions
function createChatSession($user_id = null, $guest_name = null, $guest_email = null) {
    global $db;
    $stmt = $db->prepare("INSERT INTO chat_sessions (user_id, guest_name, guest_email) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $guest_name, $guest_email);
    if ($stmt->execute()) {
        return $db->lastInsertId();
    }
    return false;
}

function getChatSession($session_id) {
    global $db;
    $session_id = intval($session_id);
    $result = $db->query("SELECT cs.*, u.username, u.email FROM chat_sessions cs
                          LEFT JOIN users u ON cs.user_id = u.id
                          WHERE cs.id = $session_id");
    return $result->fetch_assoc();
}

function getActiveChatSessions() {
    global $db;
    $result = $db->query("SELECT cs.*, u.username, u.email,
                          (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.id AND cm.sender_type = 'user' AND cm.is_read = 0) as unread_count
                          FROM chat_sessions cs
                          LEFT JOIN users u ON cs.user_id = u.id
                          WHERE cs.status = 'active'
                          ORDER BY cs.updated_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getChatMessages($session_id) {
    global $db;
    $session_id = intval($session_id);
    $result = $db->query("SELECT cm.*, u.username, u.full_name
                          FROM chat_messages cm
                          LEFT JOIN users u ON cm.sender_id = u.id AND cm.sender_type = 'user'
                          WHERE cm.session_id = $session_id
                          ORDER BY cm.created_at ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function sendChatMessage($session_id, $sender_type, $sender_id, $message) {
    global $db;
    $session_id = intval($session_id);
    $message = $db->escape($message);

    $stmt = $db->prepare("INSERT INTO chat_messages (session_id, sender_type, sender_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $session_id, $sender_type, $sender_id, $message);

    if ($stmt->execute()) {
        // Update session timestamp
        $stmt = $db->prepare("UPDATE chat_sessions SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $session_id);
        $stmt->execute();

        return $db->lastInsertId();
    }
    return false;
}

function markMessagesAsRead($session_id, $sender_type) {
    global $db;
    $session_id = intval($session_id);
    $stmt = $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE session_id = ? AND sender_type = ?");
    $stmt->bind_param("is", $session_id, $sender_type);
    return $stmt->execute();
}

function closeChatSession($session_id) {
    global $db;
    $session_id = intval($session_id);
    $stmt = $db->prepare("UPDATE chat_sessions SET status = 'closed' WHERE id = ?");
    $stmt->bind_param("i", $session_id);
    return $stmt->execute();
}

function getChatStats() {
    global $db;
    $stats = [];

    $result = $db->query("SELECT COUNT(*) as count FROM chat_sessions WHERE status = 'active'");
    $stats['active_chats'] = $result->fetch_assoc()['count'];

    $result = $db->query("SELECT COUNT(*) as count FROM chat_sessions WHERE DATE(created_at) = CURDATE()");
    $stats['today_sessions'] = $result->fetch_assoc()['count'];

    $result = $db->query("SELECT COUNT(*) as count FROM chat_messages WHERE DATE(created_at) = CURDATE()");
    $stats['today_messages'] = $result->fetch_assoc()['count'];

    return $stats;
}

function getClosedChatSessions() {
    global $db;

    try {
        $stmt = $db->prepare("
            SELECT cs.*, u.username, u.full_name,
                   COUNT(cm.id) as message_count,
                   MAX(cm.created_at) as last_message_time
            FROM chat_sessions cs
            LEFT JOIN users u ON cs.user_id = u.id
            LEFT JOIN chat_messages cm ON cs.id = cm.session_id
            WHERE cs.status = 'closed'
            GROUP BY cs.id
            ORDER BY cs.updated_at DESC
            LIMIT 50
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getAllChatSessions() {
    global $db;
    $result = $db->query("SELECT cs.*, u.username, u.email,
                          (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.id AND cm.sender_type = 'user' AND cm.is_read = 0) as unread_count,
                          COUNT(cm.id) as message_count,
                          MAX(cm.created_at) as last_message_time
                          FROM chat_sessions cs
                          LEFT JOIN users u ON cs.user_id = u.id
                          LEFT JOIN chat_messages cm ON cs.id = cm.session_id
                          GROUP BY cs.id
                          ORDER BY cs.updated_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

?>
