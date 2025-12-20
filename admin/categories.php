<?php 
$page_title = 'Manage Categories';
include '../includes/header.php';

requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $post_action = $_POST['action'];
        
        if ($post_action == 'add' || $post_action == 'edit') {
            $name = sanitizeInput($_POST['name']);
            $description = sanitizeInput($_POST['description']);
            
            global $db;
            
            if ($post_action == 'add') {
                $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                $stmt->bind_param("ss", $name, $description);
                
                if ($stmt->execute()) {
                    $success = 'Category added successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to add category.';
                }
            } else {
                $edit_id = intval($_POST['id']);
                $stmt = $db->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
                $stmt->bind_param("ssi", $name, $description, $edit_id);
                
                if ($stmt->execute()) {
                    $success = 'Category updated successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to update category.';
                }
            }
        } elseif ($post_action == 'delete') {
            $delete_id = intval($_POST['id']);
            global $db;
            $stmt = $db->prepare("DELETE FROM categories WHERE id=?");
            $stmt->bind_param("i", $delete_id);
            
            if ($stmt->execute()) {
                $success = 'Category deleted successfully!';
            } else {
                $error = 'Failed to delete category. It may have associated products.';
            }
        }
    }
}

$categories = getCategories();

if ($action == 'edit' && $category_id) {
    $category = getCategoryById($category_id);
}
?>

<div class="container-fluid my-4 px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-th-large text-primary me-2"></i>Manage Categories</h1>
            <p class="text-muted mb-0">Organize your products into categories for better navigation</p>
        </div>
        <?php if ($action == 'list'): ?>
            <a href="?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Category
            </a>
        <?php else: ?>
            <a href="categories.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        <?php endif; ?>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($action == 'list'): ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($category['description']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                    <td>
                                        <a href="?action=edit&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="" method="POST" class="d-inline" onsubmit="return confirmDelete()">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?php echo $action == 'add' ? 'Add New Category' : 'Edit Category'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo $action == 'edit' ? htmlspecialchars($category['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo $action == 'edit' ? htmlspecialchars($category['description']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $action == 'add' ? 'Add Category' : 'Update Category'; ?>
                    </button>
                    <a href="categories.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
