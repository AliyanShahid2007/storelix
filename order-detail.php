<?php 
$page_title = 'Order Details';
include 'includes/header.php';

requireLogin();

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header('Location: orders.php');
    exit();
}

$order = getOrderById($order_id);

if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: orders.php');
    exit();
}

// Handle cancel order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel_order') {
    if (cancelOrder($order_id, $_SESSION['user_id'])) {
        header('Location: order-detail.php?id=' . $order_id . '&cancelled=1');
        exit();
    } else {
        $error = 'Failed to cancel order.';
    }
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

// Check for success message
$success = '';
if (isset($_GET['cancelled']) && $_GET['cancelled'] == '1') {
    $success = 'Order has been cancelled successfully.';
}
?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="orders.php">My Orders</a></li>
            <li class="breadcrumb-item active">Order #<?php echo $order['id']; ?></li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-receipt"></i> Order #<?php echo $order['id']; ?></h1>
        <span class="badge bg-<?php echo $status_color; ?> fs-5"><?php echo ucfirst($order['status']); ?></span>
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
                                                <img src="<?php echo $item['image'] ? 'uploads/' . $item['image'] : 'https://via.placeholder.com/60'; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <a href="product-detail.php?id=<?php echo $item['product_id']; ?>" 
                                                       class="text-muted small">View Product</a>
                                                </div>
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
            
            <a href="orders.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Order Date:</strong></p>
                    <p class="text-muted"><?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?></p>
                    
                    <p class="mb-2"><strong>Payment Method:</strong></p>
                    <p class="text-muted"><?php echo htmlspecialchars($order['payment_method']); ?></p>
                    
                    <p class="mb-2"><strong>Order Status:</strong></p>
                    <p><span class="badge bg-<?php echo $status_color; ?>"><?php echo ucfirst($order['status']); ?></span></p>

                    <?php if ($order['status'] == 'pending'): ?>
                        <form method="POST" action="" class="mt-3">
                            <input type="hidden" name="action" value="cancel_order">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this order?')">
                                <i class="fas fa-times"></i> Cancel Order
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Shipping Details</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Customer:</strong></p>
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
            
            <div class="card">
                <div class="card-body">
                    <h6><i class="fas fa-question-circle"></i> Need Help?</h6>
                    <p class="small text-muted mb-2">Contact our support team</p>
                    <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-envelope"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
