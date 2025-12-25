<?php
include '../includes/header.php';
requireAdmin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add' || $action == 'edit') {
            $platform = trim($_POST['platform']);
            $url = trim($_POST['url']);
            $icon_class = trim($_POST['icon_class']);
            $sort_order = (int)$_POST['sort_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if ($action == 'add') {
                $stmt = $db->prepare("INSERT INTO social_links (platform, url, icon_class, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssii", $platform, $url, $icon_class, $sort_order, $is_active);
            } else {
                $id = (int)$_POST['id'];
                $stmt = $db->prepare("UPDATE social_links SET platform=?, url=?, icon_class=?, sort_order=?, is_active=? WHERE id=?");
                $stmt->bind_param("sssiii", $platform, $url, $icon_class, $sort_order, $is_active, $id);
            }

            if ($stmt->execute()) {
                $message = $action == 'add' ? 'Social link added successfully!' : 'Social link updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error: ' . $stmt->error;
                $message_type = 'danger';
            }
            $stmt->close();
        } elseif ($action == 'toggle') {
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("UPDATE social_links SET is_active = NOT is_active WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'Social link status updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error updating status: ' . $stmt->error;
                $message_type = 'danger';
            }
            $stmt->close();
        } elseif ($action == 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("DELETE FROM social_links WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'Social link deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error deleting social link: ' . $stmt->error;
                $message_type = 'danger';
            }
            $stmt->close();
        }
    }
}

// Get all social links
$social_links = $db->query("SELECT * FROM social_links ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-share-alt text-secondary me-2"></i>Social Media Management</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSocialModal">
                    <i class="fas fa-plus me-2"></i>Add Social Link
                </button>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Social Media Links</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Platform</th>
                                    <th>URL</th>
                                    <th>Icon</th>
                                    <th>Sort Order</th>
                                    <th>Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($social_links)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fab fa-facebook fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No social media links found.</p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSocialModal">
                                                <i class="fas fa-plus me-2"></i>Add Your First Social Link
                                            </button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($social_links as $social): ?>
                                        <tr>
                                            <td><?php echo $social['id']; ?></td>
                                            <td>
                                                <i class="fab <?php echo htmlspecialchars($social['icon_class']); ?> me-2"></i>
                                                <?php echo htmlspecialchars($social['platform']); ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($social['url']); ?>
                                                    <i class="fas fa-external-link-alt ms-1 small"></i>
                                                </a>
                                            </td>
                                            <td><code><?php echo htmlspecialchars($social['icon_class']); ?></code></td>
                                            <td><?php echo $social['sort_order']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm <?php echo $social['is_active'] ? 'btn-success' : 'btn-outline-secondary'; ?>"
                                                        onclick="toggleSocial(<?php echo $social['id']; ?>)">
                                                    <i class="fas <?php echo $social['is_active'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                                        onclick="editSocial(<?php echo $social['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteSocial(<?php echo $social['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Social Link Modal -->
<div class="modal fade" id="addSocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Social Media Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Platform Name *</label>
                        <input type="text" class="form-control" name="platform" required placeholder="e.g., Facebook, Twitter, Instagram">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL *</label>
                        <input type="url" class="form-control" name="url" required placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Class *</label>
                        <input type="text" class="form-control" name="icon_class" required placeholder="e.g., fa-facebook-f, fa-twitter, fa-instagram">
                        <small class="text-muted">FontAwesome icon class (without 'fab fa-')</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Social Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Social Link Modal -->
<div class="modal fade" id="editSocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Social Media Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editSocialForm">
                <div class="modal-body" id="editSocialContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Social Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSocial(id) {
    fetch(`social-media-actions.php?action=get&id=${id}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('editSocialContent').innerHTML = data;
            document.getElementById('editSocialForm').querySelector('input[name="action"]').value = 'edit';
            new bootstrap.Modal(document.getElementById('editSocialModal')).show();
        });
}

function toggleSocial(id) {
    if (confirm('Are you sure you want to toggle this social link\'s status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSocial(id) {
    if (confirm('Are you sure you want to delete this social link? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
