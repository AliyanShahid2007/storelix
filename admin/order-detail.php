<?php 
$page_title = 'Order Details';
include '../includes/header.php';

requireAdmin();

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header('Location: orders.php');
    exit();
}

$order = getOrderById($order_id);

if (!$order) {
    header('Location: orders.php');
    exit();
}

$order_items = getOrderItems($order_id);

$status_class = [
    'pending' => 'warning',
    'processing' => 'info',
    'shipped' => 'primary',
    'delivered' => 'success',
    'cancelled' => 'danger'
];
$status_color = $status_class[$order['status']] ?? 'secondary';
?>

<div class="container-fluid my-4 px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-receipt text-primary me-2"></i>Order #<?php echo $order['id']; ?></h1>
            <p class="text-muted mb-0">View detailed order information and customer details</p>
        </div>
        <a href="orders.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
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
                                                <img src="<?php echo $item['image'] ? '../uploads/' . $item['image'] : 'https://via.placeholder.com/60'; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;">
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
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Order Date:</strong></p>
                    <p class="text-muted"><?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?></p>
                    
                    <p class="mb-2"><strong>Last Updated:</strong></p>
                    <p class="text-muted"><?php echo date('F d, Y h:i A', strtotime($order['updated_at'])); ?></p>
                    
                    <p class="mb-2"><strong>Payment Method:</strong></p>
                    <p class="text-muted"><?php echo htmlspecialchars($order['payment_method']); ?></p>
                    
                    <p class="mb-2"><strong>Order Status:</strong></p>
                    <p><span class="badge bg-<?php echo $status_color; ?> fs-6"><?php echo ucfirst($order['status']); ?></span></p>
                    
                    <form method="POST" action="orders.php">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <label class="form-label"><strong>Update Status:</strong></label>
                        <select name="status" class="form-select mb-2">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sync"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Name:</strong></p>
                    <p class="text-muted"><?php echo htmlspecialchars($order['username']); ?></p>
                    
                    <p class="mb-2"><strong>Email:</strong></p>
                    <p class="text-muted"><?php echo htmlspecialchars($order['email']); ?></p>
                    
                    <?php if ($order['phone']): ?>
                        <p class="mb-2"><strong>Phone:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($order['phone']); ?></p>
                    <?php endif; ?>
                    
                    <p class="mb-2"><strong>Shipping Address:</strong></p>
                    <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
