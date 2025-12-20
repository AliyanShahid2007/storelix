<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$session_id = intval($_POST['session_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if (!$session_id || !$message) {
    echo json_encode(['success' => false, 'message' => 'Session ID and message required']);
    exit;
}

$sender_type = isLoggedIn() ? 'user' : 'user';
$sender_id = isLoggedIn() ? $_SESSION['user_id'] : null;

$message_id = sendChatMessage($session_id, $sender_type, $sender_id, $message);

if ($message_id) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}
?>
