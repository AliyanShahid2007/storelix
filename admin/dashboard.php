<?php
include '../includes/header.php';
requireAdmin();
?>

<div class="container-fluid my-4 px-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1"><i class="fas fa-tachometer-alt text-primary me-2"></i>Admin Dashboard</h1>
                    <p class="text-muted mb-0">Welcome back! Here's what's happening with your store today.</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Last updated: <?php echo date('M d, Y H:i'); ?></small>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <?php $stats = getStats(); ?>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon bg-primary bg-gradient text-white rounded-3 me-3">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-0 fw-bold"><?php echo $stats['users']; ?></h2>
                                <small class="text-dark">Total Users</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: 75%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon bg-success bg-gradient text-white rounded-3 me-3">
                                <i class="fas fa-box fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-0 fw-bold"><?php echo $stats['products']; ?></h2>
                                <small class="text-dark">Total Products</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon bg-warning bg-gradient text-white rounded-3 me-3">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-0 fw-bold"><?php echo $stats['orders']; ?></h2>
                                <small class="text-dark">Total Orders</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: 60%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon bg-info bg-gradient text-white rounded-3 me-3">
                                <i class="fas fa-dollar-sign fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-0 fw-bold"><?php echo formatPrice($stats['revenue']); ?></h2>
                                <small class="text-dark">Total Revenue</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions and Chat Summary -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bolt text-warning me-2"></i>
                                <h5 class="mb-0 fw-bold">Quick Actions</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="products.php" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-box fa-lg me-2"></i>
                                        <span>Manage Products</span>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="categories.php" class="btn btn-secondary btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-tags fa-lg me-2"></i>
                                        <span>Manage Categories</span>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="orders.php" class="btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-shopping-cart fa-lg me-2"></i>
                                        <span>Manage Orders</span>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="users.php" class="btn btn-info btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-users fa-lg me-2"></i>
                                        <span>Manage Users</span>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="special-offers.php" class="btn btn-warning btn-lg w-100 d-flex align-items-center justify-content-center py-3 action-btn">
                                        <i class="fas fa-clock fa-lg me-2"></i>
                                        <span>Manage Timer</span>
                                    </a>
                                </div>
                                <!-- Live Chat removed -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat sessions removed from dashboard -->

                <!-- Recent Activity -->
                <div class="col-lg-3 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-info me-2"></i>
                                <h5 class="mb-0 fw-bold text-dark">Recent Activity</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php $recent_activities = getRecentActivities(5); ?>
                            <?php if (empty($recent_activities)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No recent activities</p>
                                    <small class="text-muted">Activities will appear here as they happen</small>
                                </div>
                            <?php else: ?>
                                <div class="activity-list">
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <div class="activity-item d-flex align-items-start mb-3">
                                            <div class="activity-icon me-3">
                                                <?php if ($activity['type'] == 'order'): ?>
                                                    <i class="fas fa-shopping-cart text-success bg-light rounded-circle p-2"></i>
                                                <?php elseif ($activity['type'] == 'product'): ?>
                                                    <i class="fas fa-box text-primary bg-light rounded-circle p-2"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-info-circle text-info bg-light rounded-circle p-2"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 small text-dark"><?php echo htmlspecialchars($activity['description']); ?></p>
                                                <small class="text-muted"><?php echo date('M d, H:i', strtotime($activity['created_at'])); ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.stats-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-btn {
    transition: all 0.3s ease;
    border-radius: 10px;
    font-weight: 600;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}
</style>

<?php include '../includes/footer.php'; ?>
