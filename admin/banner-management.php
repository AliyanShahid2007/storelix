<?php
include '../includes/header.php';
requireAdmin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add' || $action == 'edit') {
            $title = trim($_POST['title']);
            $subtitle = trim($_POST['subtitle']);
            $description = trim($_POST['description']);
            $button_text = trim($_POST['button_text']);
            $button_link = trim($_POST['button_link']);
            $sort_order = (int)$_POST['sort_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // Handle image upload
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (in_array($_FILES['image']['type'], $allowed_types)) {
                    $image_name = time() . '_' . basename($_FILES['image']['name']);
                    $image_path = '../uploads/' . $image_name;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                        $image = $image_name;
                    }
                }
            }

            if ($action == 'add') {
                $stmt = $db->prepare("INSERT INTO homepage_banners (title, subtitle, description, image, button_text, button_link, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssii", $title, $subtitle, $description, $image, $button_text, $button_link, $sort_order, $is_active);
            } else {
                $id = (int)$_POST['id'];
                if ($image) {
                    $stmt = $db->prepare("UPDATE homepage_banners SET title=?, subtitle=?, description=?, image=?, button_text=?, button_link=?, sort_order=?, is_active=? WHERE id=?");
                    $stmt->bind_param("ssssssiii", $title, $subtitle, $description, $image, $button_text, $button_link, $sort_order, $is_active, $id);
                } else {
                    $stmt = $db->prepare("UPDATE homepage_banners SET title=?, subtitle=?, description=?, button_text=?, button_link=?, sort_order=?, is_active=? WHERE id=?");
                    $stmt->bind_param("sssssiii", $title, $subtitle, $description, $button_text, $button_link, $sort_order, $is_active, $id);
                }
            }

            if ($stmt->execute()) {
                $message = $action == 'add' ? 'Banner added successfully!' : 'Banner updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error: ' . $stmt->error;
                $message_type = 'error';
            }
            $stmt->close();
        } elseif ($action == 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("DELETE FROM homepage_banners WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'Banner deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error deleting banner!';
                $message_type = 'error';
            }
            $stmt->close();
        } elseif ($action == 'toggle') {
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("UPDATE homepage_banners SET is_active = NOT is_active WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Get all banners
$banners = $db->query("SELECT * FROM homepage_banners ORDER BY sort_order ASC, created_at DESC");
?>

<div class="container-fluid my-4 px-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1"><i class="fas fa-images text-primary me-2"></i>Homepage Banner Management</h1>
                    <p class="text-muted mb-0">Manage dynamic banners displayed on the homepage carousel</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                    <i class="fas fa-plus me-2"></i>Add New Banner
                </button>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $message_type == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Banners Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">All Banners</h5>
                </div>
                <div class="card-body">
                    <?php if ($banners->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Preview</th>
                                        <th>Title</th>
                                        <th>Button Text</th>
                                        <th>Sort Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($banner = $banners->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?php if ($banner['image']): ?>
                                                    <img src="../uploads/<?php echo $banner['image']; ?>" alt="Banner" class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light text-center" style="width: 80px; height: 50px; border-radius: 5px;">No Image</div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($banner['title']); ?></td>
                                            <td><?php echo htmlspecialchars($banner['button_text'] ?: 'No Button'); ?></td>
                                            <td><?php echo $banner['sort_order']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $banner['is_active'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $banner['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editBanner(<?php echo $banner['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-<?php echo $banner['is_active'] ? 'warning' : 'success'; ?>"
                                                            onclick="toggleBanner(<?php echo $banner['id']; ?>)">
                                                        <i class="fas fa-<?php echo $banner['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBanner(<?php echo $banner['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No banners found</h5>
                            <p class="text-muted">Create your first banner to get started</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Title *</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" name="subtitle">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Button Text</label>
                                <input type="text" class="form-control" name="button_text">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Button Link</label>
                                <input type="url" class="form-control" name="button_link" placeholder="https://...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sort_order" value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Banner Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <small class="text-muted">Recommended: 1920x600px, JPG/PNG/WebP</small>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Banner Modal -->
<div class="modal fade" id="editBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editBannerForm">
                <div class="modal-body" id="editBannerContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBanner(id) {
    fetch(`banner-actions.php?action=get&id=${id}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('editBannerContent').innerHTML = data;
            document.getElementById('editBannerForm').querySelector('input[name="action"]').value = 'edit';
            new bootstrap.Modal(document.getElementById('editBannerModal')).show();
        });
}

function toggleBanner(id) {
    if (confirm('Are you sure you want to toggle this banner\'s status?')) {
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

function deleteBanner(id) {
    if (confirm('Are you sure you want to delete this banner? This action cannot be undone.')) {
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
