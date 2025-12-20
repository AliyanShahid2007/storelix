<?php 
$page_title = 'Manage Products';
include '../includes/header.php';

requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $post_action = $_POST['action'];
        
        if ($post_action == 'add' || $post_action == 'edit') {
            $category_id = intval($_POST['category_id']);
            $name = sanitizeInput($_POST['name']);
            $description = sanitizeInput($_POST['description']);
            $price = floatval($_POST['price']);
            $stock = intval($_POST['stock']);
            $featured = isset($_POST['featured']) ? 1 : 0;
            
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_result = uploadImage($_FILES['image']);
                if ($upload_result['success']) {
                    $image = $upload_result['filename'];
                } else {
                    $error = $upload_result['message'];
                }
            }
            
            if (!$error) {
                global $db;
                
                if ($post_action == 'add') {
                    $stmt = $db->prepare("INSERT INTO products (category_id, name, description, price, stock, image, featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issdisi", $category_id, $name, $description, $price, $stock, $image, $featured);
                    
                    if ($stmt->execute()) {
                        $success = 'Product added successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Failed to add product.';
                    }
                } else {
                    $edit_id = intval($_POST['id']);
                    
                    if ($image) {
                        $stmt = $db->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image=?, featured=? WHERE id=?");
                        $stmt->bind_param("issdisii", $category_id, $name, $description, $price, $stock, $image, $featured, $edit_id);
                    } else {
                        $stmt = $db->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, featured=? WHERE id=?");
                        $stmt->bind_param("issdiii", $category_id, $name, $description, $price, $stock, $featured, $edit_id);
                    }
                    
                    if ($stmt->execute()) {
                        $success = 'Product updated successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Failed to update product.';
                    }
                }
            }
        } elseif ($post_action == 'delete') {
            $delete_id = intval($_POST['id']);
            global $db;
            $stmt = $db->prepare("DELETE FROM products WHERE id=?");
            $stmt->bind_param("i", $delete_id);
            
            if ($stmt->execute()) {
                $success = 'Product deleted successfully!';
            } else {
                $error = 'Failed to delete product.';
            }
        }
    }
}

$products = getProducts();
$categories = getCategories();

if ($action == 'edit' && $product_id) {
    $product = getProductById($product_id);
}
?>

<div class="container-fluid my-4 px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-box text-primary me-2"></i>Manage Products</h1>
            <p class="text-muted mb-0"><?php echo $action == 'list' ? 'Manage your product inventory and details' : ($action == 'add' ? 'Add a new product to your store' : 'Edit product information'); ?></p>
        </div>
        <?php if ($action == 'list'): ?>
            <a href="?action=add" class="btn btn-primary btn-lg px-4">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        <?php else: ?>
            <a href="products.php" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to List
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
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <img src="<?php echo $product['image'] ? '../uploads/' . $product['image'] : 'https://via.placeholder.com/50'; ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo formatPrice($product['price']); ?></td>
                                    <td>
                                        <?php if ($product['stock'] > 10): ?>
                                            <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                                        <?php elseif ($product['stock'] > 0): ?>
                                            <span class="badge bg-warning"><?php echo $product['stock']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['featured']): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="" method="POST" class="d-inline" onsubmit="return confirmDelete()">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
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
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-20 rounded-3 p-2 me-3">
                                <i class="fas fa-<?php echo $action == 'add' ? 'plus' : 'edit'; ?> fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold"><?php echo $action == 'add' ? 'Add New Product' : 'Edit Product'; ?></h5>
                                <small><?php echo $action == 'add' ? 'Create a new product for your store' : 'Update product information'; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $action; ?>">
                            <?php if ($action == 'edit'): ?>
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <?php endif; ?>

                            <!-- Basic Information -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" id="name" name="name"
                                               value="<?php echo $action == 'edit' ? htmlspecialchars($product['name']) : ''; ?>" required
                                               placeholder="Enter product name">
                                        <div class="form-text">This will be displayed to customers</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="category_id" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-lg" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>"
                                                        <?php echo ($action == 'edit' && $product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"
                                              placeholder="Describe your product..."><?php echo $action == 'edit' ? htmlspecialchars($product['description']) : ''; ?></textarea>
                                    <div class="form-text">Provide detailed information about the product</div>
                                </div>
                            </div>

                            <!-- Pricing & Inventory -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-tags me-2"></i>Pricing & Inventory
                                </h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="price" class="form-label fw-semibold">Price ($) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control form-control-lg" id="price" name="price" step="0.01" min="0"
                                                   value="<?php echo $action == 'edit' ? $product['price'] : ''; ?>" required
                                                   placeholder="0.00">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="stock" class="form-label fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-lg" id="stock" name="stock" min="0"
                                               value="<?php echo $action == 'edit' ? $product['stock'] : ''; ?>" required
                                               placeholder="0">
                                        <div class="form-text">Number of items available</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="image" class="form-label fw-semibold">Product Image</label>
                                        <input type="file" class="form-control form-control-lg" id="image" name="image" accept="image/*">
                                        <?php if ($action == 'edit' && $product['image']): ?>
                                            <div class="form-text">
                                                Current: <strong><?php echo htmlspecialchars($product['image']); ?></strong>
                                                <br><small class="text-muted">Upload a new image to replace the current one</small>
                                            </div>
                                        <?php else: ?>
                                            <div class="form-text">Upload a high-quality product image</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured"
                                           <?php echo ($action == 'edit' && $product['featured']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-semibold" for="featured">
                                        <i class="fas fa-star text-warning me-2"></i>Featured Product
                                    </label>
                                    <div class="form-text">Featured products appear prominently on the homepage</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-save me-2"></i><?php echo $action == 'add' ? 'Create Product' : 'Update Product'; ?>
                                </button>
                                <a href="products.php" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    border-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 6px !important;
    margin: 0 1px;
}

.badge {
    font-weight: 500;
    padding: 0.375rem 0.75rem;
}
</style>

<script>
// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const productName = row.querySelector('td:nth-child(3) strong')?.textContent.toLowerCase() || '';
                const category = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';

                if (productName.includes(searchTerm) || category.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

function filterProducts(filter) {
    const rows = document.querySelectorAll('.product-row');

    rows.forEach(row => {
        const stock = parseInt(row.dataset.stock);
        const featured = parseInt(row.dataset.featured);

        switch(filter) {
            case 'all':
                row.style.display = '';
                break;
            case 'in-stock':
                row.style.display = stock > 0 ? '' : 'none';
                break;
            case 'out-of-stock':
                row.style.display = stock === 0 ? '' : 'none';
                break;
            case 'featured':
                row.style.display = featured ? '' : 'none';
                break;
        }
    });
}

function viewProduct(id) {
    // Quick view functionality - could be expanded to show modal
    window.location.href = '../product-detail.php?id=' + id;
}
</script>

<?php include '../includes/footer.php'; ?>
