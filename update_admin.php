<?php
require_once 'includes/db.php';

$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "Admin password updated successfully.";
} else {
    echo "Failed to update admin password.";
}
?>
