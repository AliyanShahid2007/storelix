<?php
$page_title = 'Employee Dashboard';
include '../includes/header.php';

requireEmployee();

global $db;

// Get dashboard statistics
$stats = [];

// Total orders
$result = $db->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['count'];

// Pending orders
$result = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $result->fetch_assoc()['count'];

// Processing orders
$result = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'processing'");
$stats['processing_orders'] = $result->fetch_assoc()['count'];

// Total products
$result = $db->query("SELECT COUNT(*) as count FROM products");
$stats['total_products'] = $result->fetch_assoc()['count'];

// Recent orders
$result = $db->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = $result->fetch_all(MYSQLI_ASSOC);

// Recent products
$result = $db->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
$recent_products = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid my-4 px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-tachometer-alt text-primary me-2"></i>Employee Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        </div>
        <div>
            <span class="badge bg-warning fs-6 px-3 py-2">
                <i class="fas fa-user-tie"></i> Employee Access
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $stats['total_orders']; ?></h4>
                    <p class="text-muted mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $stats['pending_orders']; ?></h4>
                    <p class="text-muted mb-0">Pending Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-cog fa-2x text-info mb-2"></i>
                    <h4 class="mb-1"><?php echo $stats['processing_orders']; ?></h4>
                    <p class="text-muted mb-0">Processing Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-box fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $stats['total_products']; ?></h4>
                    <p class="text-muted mb-0">Total Products</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart text-primary me-2"></i>Recent Orders</h5>
                    <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['username']); ?></td>
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
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box text-success me-2"></i>Recent Products</h5>
                    <a href="products.php" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_products as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                     class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;"
                                                     onerror="this.src='../uploads/placeholder.jpg'">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo formatPrice($product['price']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                                                <?php echo $product['stock']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['featured'] ? 'primary' : 'secondary'; ?>">
                                                <?php echo $product['featured'] ? 'Featured' : 'Regular'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="orders.php" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center py-4">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <span>Manage Orders</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="products.php" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center py-4">
                                <i class="fas fa-box fa-2x mb-2"></i>
                                <span>View Products</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="../notifications.php" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center py-4">
                                <i class="fas fa-bell fa-2x mb-2"></i>
                                <span>Notifications</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="../logout.php" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center py-4">
                                <i class="fas fa-sign-out-alt fa-2x mb-2"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
