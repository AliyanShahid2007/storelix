<?php
include '../includes/db.php';
include '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

$action = $_GET['action'] ?? '';

if ($action == 'get' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM testimonials WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $testimonial = $result->fetch_assoc();
        ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Customer Name *</label>
                    <input type="text" class="form-control" name="customer_name" value="<?php echo htmlspecialchars($testimonial['customer_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Review Text *</label>
                    <textarea class="form-control" name="review_text" rows="4" required><?php echo htmlspecialchars($testimonial['review_text']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rating *</label>
                    <select class="form-control" name="rating" required>
                        <option value="5" <?php echo $testimonial['rating'] == 5 ? 'selected' : ''; ?>>5 Stars - Excellent</option>
                        <option value="4" <?php echo $testimonial['rating'] == 4 ? 'selected' : ''; ?>>4 Stars - Very Good</option>
                        <option value="3" <?php echo $testimonial['rating'] == 3 ? 'selected' : ''; ?>>3 Stars - Good</option>
                        <option value="2" <?php echo $testimonial['rating'] == 2 ? 'selected' : ''; ?>>2 Stars - Fair</option>
                        <option value="1" <?php echo $testimonial['rating'] == 1 ? 'selected' : ''; ?>>1 Star - Poor</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Customer Image</label>
                    <input type="file" class="form-control" name="customer_image" accept="image/*">
                    <?php if ($testimonial['customer_image']): ?>
                        <small class="text-muted">Current: <a href="../uploads/<?php echo $testimonial['customer_image']; ?>" target="_blank">View Image</a></small>
                    <?php else: ?>
                        <small class="text-muted">No image uploaded</small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_featured" <?php echo $testimonial['is_featured'] ? 'checked' : ''; ?>>
                        <label class="form-check-label">Featured Testimonial</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" <?php echo $testimonial['is_active'] ? 'checked' : ''; ?>>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo 'Testimonial not found';
    }
    $stmt->close();
} else {
    echo 'Invalid action';
}
?>
