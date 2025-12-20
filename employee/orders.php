<?php
$page_title = 'Manage Orders';
include '../includes/header.php';

requireEmployee();

global $db;

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_status') {
        $order_id = intval($_POST['order_id']);
        $status = $db->escape($_POST['status']);

        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();

        // Create notification for customer
        $order_result = $db->query("SELECT user_id FROM orders WHERE id = $order_id");
        $order = $order_result->fetch_assoc();

        $status_messages = [
            'processing' => 'Your order #' . $order_id . ' is now being processed.',
            'shipped' => 'Your order #' . $order_id . ' has been shipped.',
            'delivered' => 'Your order #' . $order_id . ' has been delivered successfully.'
        ];

        if (isset($status_messages[$status])) {
            createNotification($order['user_id'], 'Order Status Update', $status_messages[$status]);
        }

        header('Location: orders.php');
        exit();
    }
}

// Get orders with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$status_filter = isset($_GET['status']) ? $db->escape($_GET['status']) : '';

$sql = "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id";
if ($status_filter) {
    $sql .= " WHERE o.status = '$status_filter'";
}
$sql .= " ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";

$result = $db->query($sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as count FROM orders o";
if ($status_filter) {
    $count_sql .= " WHERE o.status = '$status_filter'";
}
$count_result = $db->query($count_sql);
$total_orders = $count_result->fetch_assoc()['count'];
$total_pages = ceil($total_orders / $limit);
?>

<div class="container-fluid my-4 px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-shopping-cart text-primary me-2"></i>Manage Orders</h1>
            <p class="text-muted mb-0">View and update order statuses</p>
        </div>
        <div>
            <span class="badge bg-warning fs-6 px-3 py-2">
                <i class="fas fa-user-tie"></i> Employee Access
            </span>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <select name="status" class="form-select">
                            <option value="">All Orders</option>
                            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $status_filter == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $status_filter == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <div class="btn-group" role="group">
                            <a href="?status=pending" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-clock"></i> Pending (<?php echo $db->query("SELECT COUNT(*) as count FROM orders WHERE status='pending'")->fetch_assoc()['count']; ?>)
                            </a>
                            <a href="?status=processing" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-cog"></i> Processing (<?php echo $db->query("SELECT COUNT(*) as count FROM orders WHERE status='processing'")->fetch_assoc()['count']; ?>)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <strong>#<?php echo $order['id']; ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($order['username']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                    </div>
                                </td>
                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch ($order['status']) {
                                        case 'pending': $status_class = 'warning'; break;
                                        case 'processing': $status_class = 'info'; break;
                                        case 'shipped': $status_class = 'primary'; break;
                                        case 'delivered': $status_class = 'success'; break;
                                        case 'cancelled': $status_class = 'danger'; break;
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $status_class; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php if ($order['status'] != 'delivered' && $order['status'] != 'cancelled'): ?>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($order['status'] == 'pending'): ?>
                                                        <li>
                                                            <form method="POST" action="">
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <input type="hidden" name="status" value="processing">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-cog text-info"></i> Mark as Processing
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($order['status'] == 'processing'): ?>
                                                        <li>
                                                            <form method="POST" action="">
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <input type="hidden" name="status" value="shipped">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-truck text-primary"></i> Mark as Shipped
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($order['status'] == 'shipped'): ?>
                                                        <li>
                                                            <form method="POST" action="">
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <input type="hidden" name="status" value="delivered">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-check text-success"></i> Mark as Delivered
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Orders pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
