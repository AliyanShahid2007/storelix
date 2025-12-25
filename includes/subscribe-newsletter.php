<?php
require_once 'db.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    try {
        // Check if email already exists
        $result = $db->query("SELECT id FROM newsletter_subscribers WHERE email = '" . $db->escape($email) . "' AND is_active = 1");
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'This email is already subscribed']);
            exit;
        }
        
        // Insert new subscriber
        $stmt = $db->prepare("INSERT INTO newsletter_subscribers (email, name, is_active) VALUES (?, ?, 1)");
        $stmt->bind_param("ss", $email, $email);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Successfully subscribed to our newsletter!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error subscribing. Please try again']);
        }
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
