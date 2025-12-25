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
    $stmt = $conn->prepare("SELECT * FROM homepage_banners WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $banner = $result->fetch_assoc();
        ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($banner['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Subtitle</label>
                    <input type="text" class="form-control" name="subtitle" value="<?php echo htmlspecialchars($banner['subtitle']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($banner['description']); ?></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Button Text</label>
                    <input type="text" class="form-control" name="button_text" value="<?php echo htmlspecialchars($banner['button_text']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Button Link</label>
                    <input type="url" class="form-control" name="button_link" value="<?php echo htmlspecialchars($banner['button_link']); ?>" placeholder="https://...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" name="sort_order" value="<?php echo $banner['sort_order']; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Banner Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <?php if ($banner['image']): ?>
                        <small class="text-muted">Current: <a href="../uploads/<?php echo $banner['image']; ?>" target="_blank">View Image</a></small>
                    <?php else: ?>
                        <small class="text-muted">No image uploaded</small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" <?php echo $banner['is_active'] ? 'checked' : ''; ?>>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo 'Banner not found';
    }
    $stmt->close();
} else {
    echo 'Invalid action';
}
?>
