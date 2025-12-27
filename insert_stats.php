<?php
include 'includes/db.php';

$stmt = $db->prepare("INSERT INTO homepage_stats (stat_key, stat_value, stat_label, icon_class, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE stat_value=?, stat_label=?, icon_class=?, sort_order=?, is_active=?");
$stmt->bind_param('ssssiiisiii', $key, $value, $label, $icon, $order, $active, $value, $label, $icon, $order, $active);

$stats = [
    ['happy_customers', '5000+', 'Happy Customers', 'fa-users', 1, 1],
    ['products_delivered', '15000+', 'Products Delivered', 'fa-box', 2, 1],
    ['revenue_generated', '$500,000+', 'Revenue Generated', 'fa-dollar-sign', 3, 1],
    ['years_service', '5+', 'Years of Service', 'fa-calendar-alt', 4, 1]
];

foreach ($stats as $stat) {
    list($key, $value, $label, $icon, $order, $active) = $stat;
    $stmt->execute();
}

echo 'Statistics inserted successfully';
?>
