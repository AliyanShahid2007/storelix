<?php
include '../includes/db.php';
include '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_stat':
        $key = trim($_GET['key'] ?? '');
        if (empty($key)) {
            echo json_encode(['success' => false, 'message' => 'Statistic key is required']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM homepage_stats WHERE stat_key=?");
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
        break;

    case 'delete_stat':
        $key = trim($_GET['key'] ?? '');
        if (empty($key)) {
            echo json_encode(['success' => false, 'message' => 'Statistic key is required']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM homepage_stats WHERE stat_key=?");
        $stmt->bind_param("s", $key);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Statistic deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete statistic']);
        }
        break;

    case 'toggle_stat':
        $key = trim($_GET['key'] ?? '');
        if (empty($key)) {
            echo json_encode(['success' => false, 'message' => 'Statistic key is required']);
            exit;
        }

        $stmt = $db->prepare("UPDATE homepage_stats SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE stat_key=?");
        $stmt->bind_param("s", $key);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Statistic status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update statistic status']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
