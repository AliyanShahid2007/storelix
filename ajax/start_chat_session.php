<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
$guest_name = sanitizeInput($_POST['guest_name'] ?? '');
$guest_email = sanitizeInput($_POST['guest_email'] ?? '');

$session_id = createChatSession($user_id, $guest_name, $guest_email);
if ($session_id) {
    echo json_encode(['success' => true, 'session_id' => $session_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to start chat session']);
}
?>
