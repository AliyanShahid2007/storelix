<?php
require_once 'includes/db.php';

try {
    // Read the SQL file
    $sql = file_get_contents('notifications_table.sql');

    // Execute the SQL
    $db = new Database();
    $db->getConnection()->query($sql);

    echo "Notifications table created successfully!\n";
} catch (Exception $e) {
    echo "Error creating notifications table: " . $e->getMessage() . "\n";
}
?>
