<?php 
$page_title = 'Manage Orders';
include '../includes/header.php';

requireAdmin();

$error = '';
$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $order_id = intval($_POST['order_id']);
    $status = sanitizeInput($_POST['status']);
    
    global $db;
    $stmt = $db->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    
if ($stmt->execute()) {
        $success = 'Order status updated successfully!';

        // Notify user if status changed to processing (approval)
        if ($status == 'processing') {
            $order = getOrderById($order_id);
            if ($order) {
                createNotification($order['user_id'], 'Order Confirmed', 'Your order #' . $order_id . ' has been confirmed and is now being processed.');
            }
        }
    } else {
        $error = 'Failed to update order status.';
    }
}

$orders = getOrders();
?>

<div class="container-fluid my-4 px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-shopping-cart text-primary me-2"></i>Manage Orders</h1>
            <p class="text-muted mb-0">Track and manage customer orders</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-list text-primary me-2"></i>
                <h5 class="mb-0 fw-bold">Orders List</h5>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($orders)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-inbox fa-4x mb-3"></i>
                    <h4>No orders yet</h4>
                    <p>Orders will appear here when customers make purchases</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                                    <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                    <td>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if ($order['status'] == 'pending'): ?>
                                            <form method="POST" action="" class="d-inline me-1">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this order?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="" class="d-inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this order?')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
