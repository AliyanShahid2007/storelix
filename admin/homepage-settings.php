<?php
include '../includes/header.php';
requireAdmin();

// Handle form submissions for stats
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'update_stat') {
            $stat_key = trim($_POST['stat_key']);
            $stat_value = trim($_POST['stat_value']);
            $stat_label = trim($_POST['stat_label']);
            $icon_class = trim($_POST['icon_class']);
            $sort_order = (int)$_POST['sort_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $stmt = $db->prepare("INSERT INTO homepage_stats (stat_key, stat_value, stat_label, icon_class, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE stat_value=?, stat_label=?, icon_class=?, sort_order=?, is_active=?");
            $stmt->bind_param("ssssiiisiii", $stat_key, $stat_value, $stat_label, $icon_class, $sort_order, $is_active, $stat_value, $stat_label, $icon_class, $sort_order, $is_active);

            if ($stmt->execute()) {
                $message = 'Homepage statistic updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error: ' . $stmt->error;
                $message_type = 'error';
            }
            $stmt->close();
        }
    }
}

// Get all homepage stats
$stats = $db->query("SELECT * FROM homepage_stats ORDER BY sort_order ASC");
?>

<div class="container-fluid my-4 px-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1"><i class="fas fa-cog text-warning me-2"></i>Homepage Settings</h1>
                    <p class="text-muted mb-0">Manage general homepage controls and statistics</p>
                </div>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $message_type == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Homepage Statistics Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Homepage Statistics</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Configure the statistics displayed in the homepage counter section</p>

                    <?php if ($stats->num_rows > 0): ?>
                        <div class="row">
                            <?php while ($stat = $stats->fetch_assoc()): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas <?php echo $stat['icon_class']; ?> fa-lg text-primary me-2"></i>
                                                <h6 class="card-title mb-0"><?php echo htmlspecialchars($stat['stat_label']); ?></h6>
                                            </div>
                                            <h4 class="text-primary mb-2"><?php echo htmlspecialchars($stat['stat_value']); ?></h4>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Key: <?php echo $stat['stat_key']; ?></small>
                                                <span class="badge bg-<?php echo $stat['is_active'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $stat['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </div>
                                            <div class="d-flex gap-1 mt-2">
                                                <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editStat('<?php echo $stat['stat_key']; ?>')" data-bs-toggle="tooltip" title="Edit this statistic">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStat('<?php echo $stat['stat_key']; ?>')" data-bs-toggle="tooltip" title="Toggle active status">
                                                    <i class="fas fa-toggle-<?php echo $stat['is_active'] ? 'on' : 'off'; ?>"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteStat('<?php echo $stat['stat_key']; ?>')" data-bs-toggle="tooltip" title="Delete this statistic">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No statistics configured</h5>
                            <p class="text-muted">Add your first homepage statistic below</p>
                        </div>
                    <?php endif; ?>

                    <!-- Quick Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Use the buttons below each statistic to edit, toggle status, or delete.
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStatModal">
                            <i class="fas fa-plus me-2"></i>Add New Statistic
                        </button>
                    </div>
                </div>
            </div>

            <!-- Other Settings Sections -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-palette me-2"></i>Theme Settings</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Configure homepage theme and appearance settings</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Theme settings will be implemented in future updates.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-globe me-2"></i>SEO Settings</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Manage homepage meta tags and SEO settings</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                SEO settings will be implemented in future updates.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Stat Modal -->
<div class="modal fade" id="addStatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Add/Edit Homepage Statistic</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_stat">
                    <div class="mb-3">
                        <label class="form-label">Statistic Key *</label>
                        <input type="text" class="form-control" name="stat_key" id="stat_key" required placeholder="e.g., total_products, happy_customers">
                        <small class="text-muted">Unique identifier for this statistic</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Value *</label>
                        <input type="text" class="form-control" name="stat_value" id="stat_value" required placeholder="e.g., 1000+, 500+">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Label *</label>
                        <input type="text" class="form-control" name="stat_label" id="stat_label" required placeholder="e.g., Products Sold, Happy Customers">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Class</label>
                        <input type="text" class="form-control" name="icon_class" id="icon_class" placeholder="e.g., fa-shopping-cart, fa-users">
                        <small class="text-muted">FontAwesome icon class (without 'fas fa-')</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="sort_order" value="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Statistic</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editStat(statKey) {
    // Fetch stat data and populate modal
    fetch(`homepage-settings-actions.php?action=get_stat&key=${encodeURIComponent(statKey)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('stat_key').value = data.stat.stat_key;
                document.getElementById('stat_value').value = data.stat.stat_value;
                document.getElementById('stat_label').value = data.stat.stat_label;
                document.getElementById('icon_class').value = data.stat.icon_class;
                document.getElementById('sort_order').value = data.stat.sort_order;
                document.getElementById('is_active').checked = data.stat.is_active == 1;
                document.getElementById('modal_title').textContent = 'Edit Homepage Statistic';
                new bootstrap.Modal(document.getElementById('addStatModal')).show();
            } else {
                alert('Error loading statistic data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Error loading statistic data. Please try again.');
        });
}

function addNewStat() {
    // Reset modal for adding new stat
    document.getElementById('stat_key').value = '';
    document.getElementById('stat_value').value = '';
    document.getElementById('stat_label').value = '';
    document.getElementById('icon_class').value = '';
    document.getElementById('sort_order').value = '0';
    document.getElementById('is_active').checked = true;
    document.getElementById('modal_title').textContent = 'Add New Homepage Statistic';
    new bootstrap.Modal(document.getElementById('addStatModal')).show();
}

function deleteStat(statKey) {
    if (confirm('Are you sure you want to delete this statistic? This action cannot be undone.')) {
        fetch(`homepage-settings-actions.php?action=delete_stat&key=${statKey}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting statistic: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('Error deleting statistic. Please try again.');
            });
    }
}

function toggleStat(statKey) {
    fetch(`homepage-settings-actions.php?action=toggle_stat&key=${statKey}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error toggling statistic: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Toggle error:', error);
            alert('Error toggling statistic. Please try again.');
        });
}

// Initialize tooltips and other enhancements on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add loading state to form submission
    const form = document.querySelector('#addStatModal form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
