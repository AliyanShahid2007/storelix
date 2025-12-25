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
    $stmt = $conn->prepare("SELECT * FROM social_links WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $social = $result->fetch_assoc();
        ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $social['id']; ?>">
        <div class="mb-3">
            <label class="form-label">Platform Name *</label>
            <input type="text" class="form-control" name="platform" value="<?php echo htmlspecialchars($social['platform']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">URL *</label>
            <input type="url" class="form-control" name="url" value="<?php echo htmlspecialchars($social['url']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Icon Class *</label>
            <input type="text" class="form-control" name="icon_class" value="<?php echo htmlspecialchars($social['icon_class']); ?>" required>
            <small class="text-muted">FontAwesome icon class (without 'fab fa-')</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Sort Order</label>
            <input type="number" class="form-control" name="sort_order" value="<?php echo $social['sort_order']; ?>">
            <small class="text-muted">Lower numbers appear first</small>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" <?php echo $social['is_active'] ? 'checked' : ''; ?>>
                <label class="form-check-label">Active</label>
            </div>
        </div>
        <?php
    } else {
        echo 'Social link not found';
    }
    $stmt->close();
} else {
    echo 'Invalid action';
}
?>
