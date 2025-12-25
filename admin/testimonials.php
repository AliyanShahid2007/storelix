<?php
include '../includes/header.php';
requireAdmin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add' || $action == 'edit') {
            $customer_name = trim($_POST['customer_name']);
            $review_text = trim($_POST['review_text']);
            $rating = (int)$_POST['rating'];
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // Handle image upload
            $customer_image = '';
            if (isset($_FILES['customer_image']) && $_FILES['customer_image']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (in_array($_FILES['customer_image']['type'], $allowed_types)) {
                    $image_name = time() . '_' . basename($_FILES['customer_image']['name']);
                    $image_path = '../uploads/' . $image_name;
                    if (move_uploaded_file($_FILES['customer_image']['tmp_name'], $image_path)) {
                        $customer_image = $image_name;
                    }
                }
            }

            if ($action == 'add') {
                $stmt = $db->prepare("INSERT INTO testimonials (customer_name, customer_image, rating, review_text, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisii", $customer_name, $customer_image, $rating, $review_text, $is_featured, $is_active);
            } else {
                $id = (int)$_POST['id'];
                if ($customer_image) {
                    $stmt = $db->prepare("UPDATE testimonials SET customer_name=?, customer_image=?, rating=?, review_text=?, is_featured=?, is_active=? WHERE id=?");
                    $stmt->bind_param("ssisiii", $customer_name, $customer_image, $rating, $review_text, $is_featured, $is_active, $id);
                } else {
                    $stmt = $db->prepare("UPDATE testimonials SET customer_name=?, rating=?, review_text=?, is_featured=?, is_active=? WHERE id=?");
                    $stmt->bind_param("sisiii", $customer_name, $rating, $review_text, $is_featured, $is_active, $id);
                }
            }

            if ($stmt->execute()) {
                $message = $action == 'add' ? 'Testimonial added successfully!' : 'Testimonial updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error: ' . $stmt->error;
                $message_type = 'error';
            }
            $stmt->close();
        } elseif ($action == 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("DELETE FROM testimonials WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'Testimonial deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error deleting testimonial!';
                $message_type = 'error';
            }
            $stmt->close();
        } elseif ($action == 'toggle') {
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($action == 'toggle_featured') {
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("UPDATE testimonials SET is_featured = NOT is_featured WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Get all testimonials
$testimonials = $db->query("SELECT * FROM testimonials ORDER BY is_featured DESC, created_at DESC");
?>

<div class="container-fluid my-4 px-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1"><i class="fas fa-comments text-success me-2"></i>Testimonials Management</h1>
                    <p class="text-muted mb-0">Manage customer testimonials displayed on the homepage</p>
                </div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                    <i class="fas fa-plus me-2"></i>Add New Testimonial
                </button>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $message_type == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Testimonials Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">All Testimonials</h5>
                </div>
                <div class="card-body">
                    <?php if ($testimonials->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Review</th>
                                        <th>Rating</th>
                                        <th>Featured</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($testimonial = $testimonials->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($testimonial['customer_image']): ?>
                                                        <img src="../uploads/<?php echo $testimonial['customer_image']; ?>" alt="Customer" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($testimonial['customer_name']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php echo htmlspecialchars(substr($testimonial['review_text'], 0, 50)) . '...'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rating-stars">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($testimonial['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-star me-1"></i>Featured
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Regular</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $testimonial['is_active'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $testimonial['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editTestimonial(<?php echo $testimonial['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-<?php echo $testimonial['is_featured'] ? 'warning' : 'secondary'; ?>"
                                                            onclick="toggleFeatured(<?php echo $testimonial['id']; ?>)">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-<?php echo $testimonial['is_active'] ? 'warning' : 'success'; ?>"
                                                            onclick="toggleTestimonial(<?php echo $testimonial['id']; ?>)">
                                                        <i class="fas fa-<?php echo $testimonial['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTestimonial(<?php echo $testimonial['id']; ?>)">
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
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No testimonials found</h5>
                            <p class="text-muted">Create your first testimonial to get started</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Testimonial Modal -->
<div class="modal fade" id="addTestimonialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Customer Name *</label>
                                <input type="text" class="form-control" name="customer_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Review Text *</label>
                                <textarea class="form-control" name="review_text" rows="4" required placeholder="Enter the customer's review..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating *</label>
                                <select class="form-control" name="rating" required>
                                    <option value="5">5 Stars - Excellent</option>
                                    <option value="4">4 Stars - Very Good</option>
                                    <option value="3">3 Stars - Good</option>
                                    <option value="2">2 Stars - Fair</option>
                                    <option value="1">1 Star - Poor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Customer Image</label>
                                <input type="file" class="form-control" name="customer_image" accept="image/*">
                                <small class="text-muted">Optional. Recommended: Square image, 200x200px</small>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured">
                                    <label class="form-check-label">Featured Testimonial</label>
                                </div>
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
                    <button type="submit" class="btn btn-success">Add Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Testimonial Modal -->
<div class="modal fade" id="editTestimonialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editTestimonialForm">
                <div class="modal-body" id="editTestimonialContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTestimonial(id) {
    fetch(`testimonial-actions.php?action=get&id=${id}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('editTestimonialContent').innerHTML = data;
            document.getElementById('editTestimonialForm').querySelector('input[name="action"]').value = 'edit';
            new bootstrap.Modal(document.getElementById('editTestimonialModal')).show();
        });
}

function toggleTestimonial(id) {
    if (confirm('Are you sure you want to toggle this testimonial\'s status?')) {
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
    if (confirm('Are you sure you want to toggle this testimonial\'s featured status?')) {
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

function deleteTestimonial(id) {
    if (confirm('Are you sure you want to delete this testimonial? This action cannot be undone.')) {
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
