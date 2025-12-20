<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if (isset($_POST['notification_id'])) {
    $notification_id = intval($_POST['notification_id']);
    $user_id = $_SESSION['user_id'];

    if (markNotificationAsRead($notification_id, $user_id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark as read']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
