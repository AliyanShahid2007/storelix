<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is employee
if (!isLoggedIn() || !isEmployee()) {
    http_response_code(403);
    echo 'Access denied';
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo 'Invalid product ID';
    exit();
}

$product_id = intval($_GET['id']);
$product = getProductById($product_id);

if (!$product) {
    http_response_code(404);
    echo 'Product not found';
    exit();
}
?>

<div class="row">
    <div class="col-md-4">
        <img src="../<?php echo htmlspecialchars($product['image']); ?>"
             alt="<?php echo htmlspecialchars($product['name']); ?>"
             class="img-fluid rounded">
    </div>
    <div class="col-md-8">
        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
        <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>

        <div class="row mt-3">
            <div class="col-sm-6">
                <strong>Price:</strong> <?php echo formatPrice($product['price']); ?>
            </div>
            <div class="col-sm-6">
                <strong>Stock:</strong>
                <span class="badge bg-<?php echo $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                    <?php echo $product['stock']; ?> units
                </span>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-sm-6">
                <strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?>
            </div>
            <div class="col-sm-6">
                <strong>Featured:</strong>
                <span class="badge bg-<?php echo $product['featured'] ? 'primary' : 'secondary'; ?>">
                    <?php echo $product['featured'] ? 'Yes' : 'No'; ?>
                </span>
            </div>
        </div>

        <div class="mt-3">
            <strong>Added:</strong> <?php echo date('F d, Y H:i', strtotime($product['created_at'])); ?>
        </div>

        <div class="mt-3">
            <strong>Last Updated:</strong> <?php echo date('F d, Y H:i', strtotime($product['updated_at'])); ?>
        </div>
    </div>
</div>
