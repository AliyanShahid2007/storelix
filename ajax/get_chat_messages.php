<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['session_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session ID required']);
    exit;
}

$session_id = intval($_GET['session_id']);
$session = getChatSession($session_id);

if (!$session) {
    echo json_encode(['success' => false, 'message' => 'Session not found']);
    exit;
}

$messages = getChatMessages($session_id);

// Check if admin is typing (for user sessions)
$admin_typing = false;
if ($session['user_type'] === 'guest' || $session['user_id']) {
    // Check if any admin is typing in this session
    $stmt = $pdo->prepare("SELECT COUNT(*) as typing_count FROM chat_sessions WHERE session_id = ? AND is_typing = 1 AND user_type = 'admin'");
    $stmt->execute([$session_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_typing = $result['typing_count'] > 0;
}

echo json_encode([
    'success' => true,
    'session' => $session,
    'messages' => $messages,
    'admin_typing' => $admin_typing
]);
?>
