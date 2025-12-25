<?php
include '../includes/header.php';
requireAdmin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add' || $action == 'edit') {
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
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
                $stmt = $conn->prepare("INSERT INTO news_announcements (title, content, image, is_featured, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssii", $title, $content, $image, $is_featured, $is_active);
            } else {
                $id = (int)$_POST['id'];
                if ($image) {
                    $stmt = $conn->prepare("UPDATE news_announcements SET title=?, content=?, image=?, is_featured=?, is_active=? WHERE id=?");
                    $stmt->bind_param("sssiii", $title, $content, $image, $is_featured, $is_active, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE news_announcements SET title=?, content=?, is_featured=?, is_active=? WHERE id=?");
                    $stmt->bind_param("ssiii", $title, $is_featured, $is_active, $id);
                }
            }

            if ($stmt->execute()) {
                $message = $action == 'add' ? 'News/announcement added successfully!' : 'News/announcement updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error: ' . $stmt->error;
                $message_type = 'danger';
            }
            $stmt->close();
        } elseif ($action == 'toggle') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE news_announcements SET is_active = NOT is_active WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'News/announcement status updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error updating status: ' . $stmt->error;
                $message_type = 'danger';
            }
            $stmt->close();
        } elseif ($action == 'toggle_featured') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE news_announcements SET is_featured = NOT is_featured WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'Featured status updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error updating featured status: ' . $stmt->error;
                $message_type = 'danger';
            }
            $stmt->close();
        } elseif ($action == 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM news_announcements WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'News/announcement deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error deleting news/announcement: ' . $stmt->error;
                $message_type = 'danger';
            }
            $stmt->close();
        }
    }
}

// Get all news/announcements
$news_items = $db->query("SELECT * FROM news_announcements ORDER BY is_featured DESC, created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-newspaper text-info me-2"></i>News & Announcements Management</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                    <i class="fas fa-plus me-2"></i>Add New News
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
                    <h5 class="mb-0">All News & Announcements</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Content Preview</th>
                                    <th>Image</th>
                                    <th>Featured</th>
                                    <th>Active</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($news_items)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No news or announcements found.</p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                                                <i class="fas fa-plus me-2"></i>Add Your First News Item
                                            </button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($news_items as $news): ?>
                                        <tr>
                                            <td><?php echo $news['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                                <?php if ($news['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark ms-1">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars(substr($news['content'], 0, 100)) . (strlen($news['content']) > 100 ? '...' : ''); ?></td>
                                            <td>
                                                <?php if ($news['image']): ?>
                                                    <img src="../uploads/<?php echo $news['image']; ?>" alt="News Image" class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="text-muted">No image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm <?php echo $news['is_featured'] ? 'btn-warning' : 'btn-outline-warning'; ?>"
                                                        onclick="toggleFeatured(<?php echo $news['id']; ?>)">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm <?php echo $news['is_active'] ? 'btn-success' : 'btn-outline-secondary'; ?>"
                                                        onclick="toggleNews(<?php echo $news['id']; ?>)">
                                                    <i class="fas <?php echo $news['is_active'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                                </button>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($news['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                                        onclick="editNews(<?php echo $news['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteNews(<?php echo $news['id']; ?>)">
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

<!-- Add News Modal -->
<div class="modal fade" id="addNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New News/Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Title *</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content *</label>
                                <textarea class="form-control" name="content" rows="6" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Image (Optional)</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <small class="text-muted">Recommended: 800x400px</small>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="add_featured">
                                    <label class="form-check-label" for="add_featured">
                                        Featured News
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="add_active" checked>
                                    <label class="form-check-label" for="add_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add News</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit News Modal -->
<div class="modal fade" id="editNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit News/Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editNewsForm">
                <div class="modal-body" id="editNewsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update News</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editNews(id) {
    fetch(`news-actions.php?action=get&id=${id}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('editNewsContent').innerHTML = data;
            document.getElementById('editNewsForm').querySelector('input[name="action"]').value = 'edit';
            new bootstrap.Modal(document.getElementById('editNewsModal')).show();
        });
}

function toggleNews(id) {
    if (confirm('Are you sure you want to toggle this news item\'s status?')) {
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

function toggleFeatured(id) {
    if (confirm('Are you sure you want to toggle this news item\'s featured status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="toggle_featured">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteNews(id) {
    if (confirm('Are you sure you want to delete this news item? This action cannot be undone.')) {
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
