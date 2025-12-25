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
    $stmt = $conn->prepare("SELECT * FROM news_announcements WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $news = $result->fetch_assoc();
        ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Content *</label>
                    <textarea class="form-control" name="content" rows="6" required><?php echo htmlspecialchars($news['content']); ?></textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Image (Optional)</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <?php if ($news['image']): ?>
                        <small class="text-muted">Current: <a href="../uploads/<?php echo $news['image']; ?>" target="_blank">View Image</a></small>
                    <?php else: ?>
                        <small class="text-muted">No image uploaded</small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_featured" <?php echo $news['is_featured'] ? 'checked' : ''; ?>>
                        <label class="form-check-label">Featured News</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" <?php echo $news['is_active'] ? 'checked' : ''; ?>>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo 'News item not found';
    }
    $stmt->close();
} else {
    echo 'Invalid action';
}
?>
