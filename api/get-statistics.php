<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';

try {
    // Get active homepage statistics
    $stmt = $conn->prepare("SELECT * FROM homepage_stats WHERE is_active = 1 ORDER BY sort_order ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    $statistics = [];
    while ($row = $result->fetch_assoc()) {
        $statistics[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'value' => $row['value'],
            'icon' => $row['icon'],
            'description' => $row['description'],
            'sort_order' => $row['sort_order']
        ];
    }

    echo json_encode([
        'success' => true,
        'statistics' => $statistics
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
