<?php 
$page_title = 'Order Confirmation';
include 'includes/header.php';

requireLogin();

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header('Location: index.php');
    exit();
}

$order = getOrderById($order_id);

if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: index.php');
    exit();
}

$order_items = getOrderItems($order_id);
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
        <h1 class="mt-3">Order Confirmed!</h1>
        <p class="lead text-muted">Thank you for your purchase</p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body p-4">
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle"></i> Your order has been successfully placed. 
                        You will receive a confirmation email shortly at <strong><?php echo htmlspecialchars($order['email']); ?></strong>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-receipt"></i> Order Details</h5>
                            <p class="mb-1"><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                            <p class="mb-1"><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?></p>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-warning"><?php echo ucfirst($order['status']); ?></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-credit-card"></i> Payment</h5>
                            <p class="mb-1"><strong>Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                            <p class="mb-1"><strong>Total Amount:</strong> 
                                <span class="product-price"><?php echo formatPrice($order['total_amount']); ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5><i class="fas fa-map-marker-alt"></i> Shipping Address</h5>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    </div>
                    
                    <h5 class="mb-3"><i class="fas fa-box"></i> Order Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $item['image'] ? 'uploads/' . $item['image'] : 'https://via.placeholder.com/50'; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo formatPrice($item['price']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong class="product-price"><?php echo formatPrice($order['total_amount']); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <a href="orders.php" class="btn btn-primary me-2">
                    <i class="fas fa-list"></i> View All Orders
                </a>
                <a href="products.php" class="btn btn-outline-primary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
    
    <!-- What's Next Section -->
    <div class="row mt-5">
        <div class="col-md-4 text-center">
            <i class="fas fa-box-open fa-3x text-primary mb-3"></i>
            <h5>Order Processing</h5>
            <p class="text-muted">We're preparing your items for shipment</p>
        </div>
        <div class="col-md-4 text-center">
            <i class="fas fa-truck fa-3x text-primary mb-3"></i>
            <h5>Shipping</h5>
            <p class="text-muted">Your order will be shipped soon</p>
        </div>
        <div class="col-md-4 text-center">
            <i class="fas fa-home fa-3x text-primary mb-3"></i>
            <h5>Delivery</h5>
            <p class="text-muted">Estimated delivery in 3-5 business days</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
