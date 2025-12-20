<?php 
$page_title = 'My Orders';
include 'includes/header.php';

requireLogin();

$orders = getOrders($_SESSION['user_id']);
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="fas fa-box"></i> My Orders</h1>
    
    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-5x text-muted mb-4"></i>
            <h3>No orders yet</h3>
            <p class="text-muted mb-4">Start shopping to see your orders here</p>
            <a href="products.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Browse Products
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <strong>Order #<?php echo $order['id']; ?></strong><br>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></small>
                        </div>
                        <div class="col-md-3">
                            <strong>Total:</strong> <?php echo formatPrice($order['total_amount']); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?>
                        </div>
                        <div class="col-md-3 text-end">
                            <?php
                            $status_class = [
                                'pending' => 'warning',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $status_color = $status_class[$order['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $status_color; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6><i class="fas fa-map-marker-alt"></i> Shipping Address:</h6>
                            <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php
                            $order_items = getOrderItems($order['id']);
                            $item_count = array_sum(array_column($order_items, 'quantity'));
                            ?>
                            <p class="mb-2"><strong><?php echo $item_count; ?></strong> item(s)</p>
                            <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
