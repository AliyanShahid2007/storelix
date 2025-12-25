<?php
$page_title = 'Order Details';
include '../includes/header.php';

requireEmployee();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

$order_id = intval($_GET['id']);
global $db;

$order = getOrderById($order_id);
if (!$order) {
    header('Location: orders.php');
    exit();
}

$order_items = getOrderItems($order_id);
$customer = getUserById($order['user_id']);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = sanitizeInput($_POST['new_status']);

    // Validate status transition for employees
    $allowed_transitions = [
        'pending' => ['processing'],
        'processing' => ['shipped'],
        'shipped' => ['delivered']
    ];

    if (isset($allowed_transitions[$order['status']]) && in_array($new_status, $allowed_transitions[$order['status']])) {
        if (updateOrderStatus($order_id, $new_status)) {
            // Send notification to customer
            $notification_title = 'Order Status Updated';
            $notification_message = "Your order #$order_id status has been updated to " . ucfirst($new_status) . ".";

            createNotification($order['user_id'], $notification_title, $notification_message);

            $success = 'Order status updated successfully!';
            $order['status'] = $new_status; // Update local variable
        } else {
            $error = 'Failed to update order status.';
        }
    } else {
        $error = 'Invalid status transition.';
    }
}
?>

<div class="container-fluid my-4 px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-shopping-cart text-primary me-2"></i>Order Details</h1>
            <p class="text-muted mb-0">Order #<?php echo $order_id; ?></p>
        </div>
        <div>
            <span class="badge bg-warning fs-6 px-3 py-2">
                <i class="fas fa-user-tie"></i> Employee Access
            </span>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                            <p><strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?></p>
                            <p><strong>Status:</strong>
                                <span class="badge bg-<?php
                                    echo $order['status'] == 'pending' ? 'warning' :
                                         ($order['status'] == 'processing' ? 'info' :
                                         ($order['status'] == 'shipped' ? 'primary' :
                                         ($order['status'] == 'delivered' ? 'success' : 'danger')));
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Amount:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method'] ?? 'Not specified'); ?></p>
                        </div>
                    </div>

                    <?php if (isset($order['notes']) && $order['notes']): ?>
                        <div class="mt-3">
                            <strong>Order Notes:</strong>
                            <p class="text-muted"><?php echo htmlspecialchars($order['notes']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../<?php echo htmlspecialchars($item['image']); ?>"
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                     class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo formatPrice($item['price']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td><?php echo formatPrice($order['total_amount']); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information & Actions -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['full_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone'] ?? 'Not provided'); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($customer['created_at'])); ?></p>
                </div>
            </div>

            <!-- Update Status -->
            <?php if (in_array($order['status'], ['pending', 'processing', 'shipped'])): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Update Status</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="new_status" class="form-label">New Status</label>
                                <select name="new_status" class="form-select" id="new_status" required>
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <option value="processing">Processing</option>
                                    <?php elseif ($order['status'] == 'processing'): ?>
                                        <option value="shipped">Shipped</option>
                                    <?php elseif ($order['status'] == 'shipped'): ?>
                                        <option value="delivered">Delivered</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button type="submit" name="update_status" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Back Button -->
            <div class="mt-3">
                <a href="orders.php" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
