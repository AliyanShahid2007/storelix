<?php
$page_title = 'Admin Dashboard';
include '../includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: ../index.php');
    exit();
}

// Get dashboard statistics
$total_products = $db->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $db->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_users = $db->query("SELECT COUNT(*) as count FROM users WHERE role != 'admin'")->fetch_assoc()['count'];
$total_revenue = $db->query("SELECT SUM(total_amount) as revenue FROM orders")->fetch_assoc()['revenue'];
$total_completed_orders = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'delivered'")->fetch_assoc()['count'];

// Get recent orders
$recent_orders = $db->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Get low stock products
$low_stock_products = $db->query("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid dashboard-wrapper">
    <div class="row h-100">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 bg-light sidebar collapse fixed-top" style="height: 100vh; padding-top: 60px;">
            <div class="position-sticky pt-3" style="height: calc(100vh - 60px); overflow-y: auto; overflow-x: hidden;">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-th-large"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="special-offers.php">
                            <i class="fas fa-tags"></i> Special Offers
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Homepage Management</span>
                        </h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="banner-management.php">
                            <i class="fas fa-images"></i> Banners
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="testimonials.php">
                            <i class="fas fa-comments"></i> Testimonials
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="news.php">
                            <i class="fas fa-newspaper"></i> News & Updates
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="social-media.php">
                            <i class="fas fa-share-alt"></i> Social Media
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="maintenance.php">
                            <i class="fas fa-tools"></i> Maintenance Mode
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-md-auto col-lg-10 px-md-4" style="margin-left: 0; padding-top: 1rem; padding-bottom: 2rem; min-height: calc(100vh - 60px);">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2 mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="shareBtn"><i class="fas fa-share me-1"></i>Share</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="exportBtn"><i class="fas fa-download me-1"></i>Export</button>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" id="dateFilterBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar"></i> <span id="dateFilterText">This week</span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dateFilterBtn">
                            <li><a class="dropdown-item" href="#" data-filter="today">Today</a></li>
                            <li><a class="dropdown-item active" href="#" data-filter="week">This week</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="month">This month</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="year">This year</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-filter="all">All time</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4 g-3">
                <div class="col-xxl-3 col-xl-6 col-md-6 col-sm-12">
                    <div class="card border-left-primary shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        <i class="fas fa-box me-2"></i>Total Products</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_products; ?></div>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-box fa-3x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-6 col-md-6 col-sm-12">
                    <div class="card border-left-success shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        <i class="fas fa-shopping-cart me-2"></i>Total Orders</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_orders; ?></div>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-shopping-cart fa-3x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-6 col-md-6 col-sm-12">
                    <div class="card border-left-info shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        <i class="fas fa-users me-2"></i>Total Users</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_users; ?></div>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-users fa-3x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-6 col-md-6 col-sm-12">
                    <div class="card border-left-warning shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        <i class="fas fa-dollar-sign me-2"></i>Total Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($total_revenue ?? 0, 2); ?></div>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-dollar-sign fa-3x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Achievements Section (Hidden initially, loads after 5 seconds) -->
            <div class="row mb-4" id="achievementsSection" style="display: none;">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-trophy text-warning me-2"></i>Achievements</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Completed Orders Achievement -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="achievement-card text-center p-3 border rounded <?php echo $total_completed_orders >= 10 ? 'bg-success text-white' : 'bg-light'; ?>">
                                        <i class="fas fa-check-circle fa-2x mb-2 <?php echo $total_completed_orders >= 10 ? 'text-white' : 'text-success'; ?>"></i>
                                        <h6 class="mb-1">Order Master</h6>
                                        <p class="mb-2 small"><?php echo $total_completed_orders; ?>/10 Completed Orders</p>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar <?php echo $total_completed_orders >= 10 ? 'bg-white' : 'bg-success'; ?>" style="width: <?php echo min(($total_completed_orders / 10) * 100, 100); ?>%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Revenue Achievement -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="achievement-card text-center p-3 border rounded <?php echo ($total_revenue ?? 0) >= 1000 ? 'bg-warning text-white' : 'bg-light'; ?>">
                                        <i class="fas fa-dollar-sign fa-2x mb-2 <?php echo ($total_revenue ?? 0) >= 1000 ? 'text-white' : 'text-warning'; ?>"></i>
                                        <h6 class="mb-1">Revenue Champion</h6>
                                        <p class="mb-2 small">$<?php echo number_format($total_revenue ?? 0, 0); ?>/1,000 Revenue</p>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar <?php echo ($total_revenue ?? 0) >= 1000 ? 'bg-white' : 'bg-warning'; ?>" style="width: <?php echo min((($total_revenue ?? 0) / 1000) * 100, 100); ?>%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Base Achievement -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="achievement-card text-center p-3 border rounded <?php echo $total_users >= 50 ? 'bg-info text-white' : 'bg-light'; ?>">
                                        <i class="fas fa-users fa-2x mb-2 <?php echo $total_users >= 50 ? 'text-white' : 'text-info'; ?>"></i>
                                        <h6 class="mb-1">Community Builder</h6>
                                        <p class="mb-2 small"><?php echo $total_users; ?>/50 Registered Users</p>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar <?php echo $total_users >= 50 ? 'bg-white' : 'bg-info'; ?>" style="width: <?php echo min(($total_users / 50) * 100, 100); ?>%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Catalog Achievement -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="achievement-card text-center p-3 border rounded <?php echo $total_products >= 25 ? 'bg-primary text-white' : 'bg-light'; ?>">
                                        <i class="fas fa-box-open fa-2x mb-2 <?php echo $total_products >= 25 ? 'text-white' : 'text-primary'; ?>"></i>
                                        <h6 class="mb-1">Product Expert</h6>
                                        <p class="mb-2 small"><?php echo $total_products; ?>/25 Products Listed</p>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar <?php echo $total_products >= 25 ? 'bg-white' : 'bg-primary'; ?>" style="width: <?php echo min(($total_products / 25) * 100, 100); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Homepage Management Actions -->
                                <div class="col-md-6 col-lg-4">
                                    <a href="banner-management.php" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-images fa-lg me-2"></i>
                                        <span>Manage Banners</span>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <a href="testimonials.php" class="btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-comments fa-lg me-2"></i>
                                        <span>Manage Testimonials</span>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <a href="news.php" class="btn btn-info btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-newspaper fa-lg me-2"></i>
                                        <span>Manage News</span>
                                    </a>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <a href="social-media.php" class="btn btn-secondary btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-share-alt fa-lg me-2"></i>
                                        <span>Social Media</span>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <a href="maintenance.php" class="btn btn-danger btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-tools fa-lg me-2"></i>
                                        <span>Maintenance Mode</span>
                                    </a>
                                </div>

                                <!-- Regular Admin Actions -->
                                <div class="col-md-6 col-lg-4">
                                    <a href="products.php" class="btn btn-outline-primary btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-plus fa-lg me-2"></i>
                                        <span>Add Product</span>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <a href="orders.php" class="btn btn-outline-success btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-list fa-lg me-2"></i>
                                        <span>View Orders</span>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <a href="users.php" class="btn btn-outline-info btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-user-plus fa-lg me-2"></i>
                                        <span>Manage Users</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Orders -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php
                                                        echo $order['status'] == 'pending' ? 'warning' :
                                                             ($order['status'] == 'completed' ? 'success' :
                                                             ($order['status'] == 'cancelled' ? 'danger' : 'secondary'));
                                                    ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($low_stock_products)): ?>
                                <p class="text-success mb-0">All products are well stocked!</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($low_stock_products as $product): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                <br>
                                                <small class="text-muted">Stock: <?php echo $product['stock']; ?> units</small>
                                            </div>
                                            <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.action-btn {
    transition: all 0.3s ease;
    border-radius: 10px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-primary {
    color: #5a5c69 !important;
}

.text-success {
    color: #1cc88a !important;
}

.text-info {
    color: #36b9cc !important;
}

.text-warning {
    color: #f6c23e !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.sidebar {
    position: fixed;
    top: 56px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    transition: left 0.3s ease;
}

.sidebar:not(.show) {
    left: -250px;
}

.sidebar-sticky {
    position: relative;
    top: 0;
    height: calc(100vh - 48px);
    padding-top: .5rem;
    overflow-x: hidden;
    overflow-y: auto;
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.sidebar .nav-link:hover {
    color: #007bff;
    background-color: rgba(0, 123, 255, 0.1);
    border-left-color: #007bff;
}

.sidebar .nav-link.active {
    color: #007bff;
    background-color: rgba(0, 123, 255, 0.1);
    border-left-color: #007bff;
}

.sidebar .nav-link i {
    width: 20px;
    text-align: center;
    margin-right: 0.5rem;
}

.sidebar-heading {
    font-size: .75rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Sidebar Responsive */
.sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    width: 250px;
    height: calc(100vh - 60px);
    z-index: 1030;
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    transition: left 0.3s ease;
}

/* Hide scrollbars */
.sidebar::-webkit-scrollbar {
    display: none;
}

.sidebar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

main {
    margin-left: 0; /* Start with no margin, controlled by JavaScript */
    padding-top: 1rem;
    padding-bottom: 2rem;
    min-height: calc(100vh - 60px);
    background-color: #f8f9fa;
    transition: margin-left 0.3s ease; /* Smooth transition */
}

.action-btn {
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1.25rem;
    font-weight: 600;
}

.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.border-left-primary {
    border-left: 4px solid #007bff !important;
}

.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}

.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}

.font-weight-bold {
    font-weight: 600 !important;
}

.text-xs {
    font-size: 0.75rem !important;
}

@media (max-width: 1200px) {
    main {
        margin-left: 250px;
        padding: 1rem;
    }
}

@media (max-width: 992px) {
    main {
        margin-left: 250px;
        padding: 0.75rem;
    }

    .card {
        margin-bottom: 1rem;
    }

    .btn-lg {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
}

@media (max-width: 768px) {
    main {
        margin-left: 0 !important;
        padding: 0.5rem;
    }

    .sidebar {
        left: -250px !important;
    }

    .sidebar.show {
        left: 0 !important;
    }

    .stat-card {
        margin-bottom: 0.5rem;
    }

    .btn-group {
        flex-wrap: wrap;
    }

    .btn-toolbar {
        justify-content: flex-start;
    }

    h1.h2 {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    main {
        padding: 0.25rem;
    }

    .btn-lg {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .card-body {
        padding: 0.75rem;
    }

    .stat-card .card-body {
        padding: 0.75rem;
    }

    .fa-3x {
        font-size: 2rem !important;
    }

    .text-gray-800 {
        font-size: 1rem;
    }

    h1.h2 {
        font-size: 1.25rem;
    }

    .col-xxl-3,
    .col-xl-6,
    .col-md-6,
    .col-sm-12 {
        padding: 0.25rem;
    }
}

.text-gray-800 {
    color: #2e3338;
}

.text-gray-300 {
    color: #bfc1c8;
}

/* Achievements Animation */
#achievementsSection {
    transition: opacity 0.5s ease, transform 0.5s ease;
}

/* Achievement Special Effects */
@keyframes achievementGlow {
    0% {
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.6), 0 0 40px rgba(255, 215, 0, 0.4);
    }
    100% {
        box-shadow: 0 0 30px rgba(255, 215, 0, 0.8), 0 0 60px rgba(255, 215, 0, 0.6);
    }
}

@keyframes sparkleAnimation {
    0% {
        opacity: 1;
        transform: scale(0) rotate(0deg);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.2) rotate(180deg);
    }
    100% {
        opacity: 0;
        transform: scale(0) rotate(360deg);
    }
}

@keyframes slideInRight {
    0% {
        transform: translateX(100%);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    0% {
        transform: translateX(0);
        opacity: 1;
    }
    100% {
        transform: translateX(100%);
        opacity: 0;
    }
}

.achievement-notification {
    font-family: 'Arial', sans-serif;
}

.achievement-popup {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 250px;
    border: 2px solid #ffd700;
}

.achievement-popup i {
    font-size: 1.5rem;
    margin-bottom: 5px;
}

.achievement-popup span {
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 5px;
}

.achievement-progress-text {
    font-size: 0.9rem;
    opacity: 0.9;
    text-align: center;
}



/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 60px;
        left: -250px;
        width: 250px;
        transition: left 0.3s ease;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .sidebar.show {
        left: 0;
        z-index: 1050;
    }

    main {
        margin-left: 0;
        padding-left: 15px;
        padding-right: 15px;
    }
}

@media (max-width: 576px) {
    main {
        padding-left: 10px;
        padding-right: 10px;
    }

    .btn-lg {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    .card {
        margin-bottom: 1rem;
    }

    .col-xl-3 {
        min-width: 100%;
    }
}
</style>

<script>
    // Sidebar responsive behavior
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const main = document.querySelector('main');
        const sidebarToggle = document.getElementById('mobileSidebarToggle');
        const footer = document.querySelector('footer');

        // Function to update main content margin based on screen size
        function updateMainMargin() {
            if (!main) return;

            // Always hide sidebar by default on all screen sizes
            sidebar.classList.remove('show');
            main.style.marginLeft = '0';
            if (footer) footer.style.marginLeft = '0';
        }

        // Set initial state
        updateMainMargin();

        // Handle sidebar toggle button click - THIS IS THE MAIN FIX
        if (sidebarToggle && window.innerWidth > 768) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle sidebar visibility
                sidebar.classList.toggle('show');

                // Update margin based on new state (only on desktop)
                const isVisible = sidebar.classList.contains('show');
                main.style.marginLeft = isVisible ? '250px' : '0';
                if (footer) footer.style.marginLeft = isVisible ? '250px' : '0';

                console.log('Sidebar toggled. Show:', sidebar.classList.contains('show'));
            });
        }



        // Close sidebar when clicking outside (on mobile only)
        document.addEventListener('click', function(e) {
            if (sidebar && sidebar.classList.contains('show') && window.innerWidth <= 768) {
                // Don't close if clicking on sidebar or toggle button
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                    updateMainMargin();
                }
            }
        });

        // Close sidebar when clicking nav links (on mobile only)
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('show');
                    updateMainMargin();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            updateMainMargin();
        });

        // Share Button Functionality
        const shareBtn = document.getElementById('shareBtn');
        if (shareBtn) {
            shareBtn.addEventListener('click', function() {
                const dashboardData = {
                    total_products: document.querySelector('.card:nth-of-type(1) .h5').textContent,
                    total_orders: document.querySelector('.card:nth-of-type(2) .h5').textContent,
                    total_users: document.querySelector('.card:nth-of-type(3) .h5').textContent,
                    total_revenue: document.querySelector('.card:nth-of-type(4) .h5').textContent
                };
                
                const shareText = `Dashboard Report:\n- Total Products: ${dashboardData.total_products}\n- Total Orders: ${dashboardData.total_orders}\n- Total Users: ${dashboardData.total_users}\n- Total Revenue: ${dashboardData.total_revenue}`;
                
                if (navigator.share) {
                    navigator.share({
                        title: 'Dashboard Report',
                        text: shareText
                    }).catch(err => console.log('Share failed:', err));
                } else {
                    // Fallback: Copy to clipboard
                    navigator.clipboard.writeText(shareText).then(() => {
                        alert('Dashboard data copied to clipboard!');
                    }).catch(err => {
                        alert('Share is not supported on this device. Please use the Export option.');
                    });
                }
            });
        }

        // Export Button Functionality
        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                const totalProducts = document.querySelectorAll('.card')[0].querySelector('.h5').textContent;
                const totalOrders = document.querySelectorAll('.card')[1].querySelector('.h5').textContent;
                const totalUsers = document.querySelectorAll('.card')[2].querySelector('.h5').textContent;
                const totalRevenue = document.querySelectorAll('.card')[3].querySelector('.h5').textContent;

                const csvContent = `Dashboard Report\nGenerated: ${new Date().toLocaleString()}\n\nTotal Products,${totalProducts}\nTotal Orders,${totalOrders}\nTotal Users,${totalUsers}\nTotal Revenue,${totalRevenue}`;
                
                const blob = new Blob([csvContent], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `dashboard-report-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            });
        }

        // Date Filter Functionality
        const dateFilterItems = document.querySelectorAll('.dropdown-menu a[data-filter]');
        const dateFilterText = document.getElementById('dateFilterText');
        
        dateFilterItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                const filterText = this.textContent;
                
                // Update button text
                dateFilterText.textContent = filterText;
                
                // Remove active class from all items
                dateFilterItems.forEach(i => i.classList.remove('active'));
                
                // Add active class to selected item
                this.classList.add('active');
                
                // Show confirmation
                console.log('Filter changed to: ' + filter);
                
                // In a real application, you would make an AJAX call here to filter data
                // For now, we'll show a simple message
                showNotification(`Dashboard filtered by: ${filterText}`);
            });
        });



        // Helper function to show notifications
        function showNotification(message) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-info alert-dismissible fade show';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alert.style.position = 'fixed';
            alert.style.top = '70px';
            alert.style.right = '20px';
            alert.style.zIndex = '9999';
            alert.style.minWidth = '300px';

            document.body.appendChild(alert);

            // Auto dismiss after 3 seconds
            setTimeout(() => {
                alert.remove();
            }, 3000);
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
