<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$session_id = $_POST['session_id'] ?? '';
$typing = $_POST['typing'] ?? '';

if (empty($session_id)) {
    echo json_encode(['success' => false, 'message' => 'Session ID is required']);
    exit;
}

try {
    // Update typing status in database
    $stmt = $pdo->prepare("UPDATE chat_sessions SET is_typing = ?, typing_updated_at = NOW() WHERE session_id = ?");
    $stmt->execute([$typing === '1' ? 1 : 0, $session_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Error updating typing status: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update typing status']);
}
?>
?>
