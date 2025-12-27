<?php
$page_title = 'Maintenance Mode';
include '../includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: ../index.php');
    exit();
}

// Check if maintenance mode settings exist
$maintenance_query = $db->query("SELECT * FROM maintenance_mode LIMIT 1");
$maintenance_data = $maintenance_query->fetch_assoc();

$maintenance_enabled = $maintenance_data ? (bool)$maintenance_data['is_enabled'] : false;
$message = $maintenance_data ? $maintenance_data['message'] : '';
$title = $maintenance_data ? $maintenance_data['title'] : 'Site Under Maintenance';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enabled = isset($_POST['maintenance_enabled']) ? 1 : 0;
    $custom_message = trim($_POST['maintenance_message']);

    if ($maintenance_data) {
        // Update existing setting
        $stmt = $db->prepare("UPDATE maintenance_mode SET is_enabled = ?, message = ? WHERE id = ?");
        $stmt->bind_param("isi", $enabled, $custom_message, $maintenance_data['id']);
        $stmt->execute();
    } else {
        // Insert new setting
        $stmt = $db->prepare("INSERT INTO maintenance_mode (is_enabled, message) VALUES (?, ?)");
        $stmt->bind_param("is", $enabled, $custom_message);
        $stmt->execute();
    }

    $maintenance_enabled = (bool)$enabled;
    $message = $custom_message;

    // Show success message
    $success_message = "Maintenance mode settings updated successfully!";
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 bg-light sidebar collapse fixed-top" style="height: 100vh; padding-top: 60px;">
            <div class="position-sticky pt-3" style="height: calc(100vh - 60px); overflow-y: auto; overflow-x: hidden;">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
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
                        <a class="nav-link active" href="maintenance.php">
                            <i class="fas fa-tools"></i> Maintenance Mode
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-md-auto col-lg-10 px-md-4" style="margin-left: 0; padding-top: 1rem; padding-bottom: 2rem; min-height: calc(100vh - 60px);">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2"><i class="fas fa-tools text-warning me-2"></i>Maintenance Mode</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Maintenance Mode</li>
                    </ol>
                </nav>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Maintenance Mode Settings</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="maintenanceToggle" name="maintenance_enabled" <?php echo $maintenance_enabled ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="maintenanceToggle">
                                            <strong>Enable Maintenance Mode</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">When enabled, the website will show a maintenance page to all non-admin users.</small>
                                </div>

                                <div class="mb-4">
                                    <label for="maintenanceMessage" class="form-label">
                                        <strong>Maintenance Message</strong>
                                    </label>
                                    <textarea class="form-control" id="maintenanceMessage" name="maintenance_message" rows="4" placeholder="Enter a custom maintenance message..."><?php echo htmlspecialchars($message); ?></textarea>
                                    <small class="text-muted">This message will be displayed to users when maintenance mode is active. Leave blank for default message.</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Settings
                                    </button>
                                    <a href="../index.php" target="_blank" class="btn btn-outline-secondary">
                                        <i class="fas fa-external-link-alt me-2"></i>Preview Website
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-circle fa-2x <?php echo $maintenance_enabled ? 'text-danger' : 'text-success'; ?>"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Maintenance Mode</h6>
                                    <span class="badge bg-<?php echo $maintenance_enabled ? 'danger' : 'success'; ?>">
                                        <?php echo $maintenance_enabled ? 'ENABLED' : 'DISABLED'; ?>
                                    </span>
                                </div>
                            </div>

                            <?php if ($maintenance_enabled): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Warning:</strong> Maintenance mode is currently active. Only administrators can access the website.
                                </div>
                            <?php endif; ?>

                            <hr>
                            <h6>What happens in maintenance mode?</h6>
                            <ul class="list-unstyled small text-muted">
                                <li><i class="fas fa-check text-success me-2"></i>Website shows maintenance page</li>
                                <li><i class="fas fa-check text-success me-2"></i>Admin users can still access everything</li>
                                <li><i class="fas fa-check text-success me-2"></i>Orders and user registration blocked</li>
                                <li><i class="fas fa-check text-success me-2"></i>Custom message can be displayed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
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

main {
    margin-left: 0;
    padding-top: 1rem;
    padding-bottom: 2rem;
    min-height: calc(100vh - 60px);
    background-color: #f8f9fa;
    transition: margin-left 0.3s ease;
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

.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
    margin-left: -2.5em;
}

.form-switch .form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}

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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const main = document.querySelector('main');

    // Function to update main content margin based on screen size
    function updateMainMargin() {
        if (!main) return;
        sidebar.classList.remove('show');
        main.style.marginLeft = '0';
    }

    // Set initial state
    updateMainMargin();

    // Handle window resize
    window.addEventListener('resize', function() {
        updateMainMargin();
    });
});
</script>

<?php include '../includes/footer.php'; ?>
