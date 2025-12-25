<?php
include '../includes/db.php';
include '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action == 'get_stat' && isset($_GET['key'])) {
    $key = trim($_GET['key']);
    $stmt = $conn->prepare("SELECT * FROM homepage_stats WHERE stat_key=?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stat = $result->fetch_assoc();
        echo json_encode(['success' => true, 'stat' => $stat]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Statistic not found']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
