<?php
$page_title = 'View Products';
include '../includes/header.php';

requireEmployee();

global $db;

// Get products with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? $db->escape($_GET['search']) : '';

$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";

if ($category_filter) {
    $sql .= " AND p.category_id = $category_filter";
}

if ($search) {
    $sql .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

$sql .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";

$result = $db->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as count FROM products p WHERE 1=1";

if ($category_filter) {
    $count_sql .= " AND p.category_id = $category_filter";
}

if ($search) {
    $count_sql .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

$count_result = $db->query($count_sql);
$total_products = $count_result->fetch_assoc()['count'];
$total_pages = ceil($total_products / $limit);

// Get categories for filter
$categories = getCategories();
?>

<div class="container-fluid my-4 px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-box text-success me-2"></i>View Products</h1>
            <p class="text-muted mb-0">Browse and monitor product inventory</p>
        </div>
        <div>
            <span class="badge bg-warning fs-6 px-3 py-2">
                <i class="fas fa-user-tie"></i> Employee Access
            </span>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Products</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?php echo htmlspecialchars($search); ?>" placeholder="Product name or description">
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" class="form-select" id="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="products.php" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No products found</h5>
                        <p class="text-muted">Try adjusting your search criteria</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="../<?php echo htmlspecialchars($product['image']); ?>"
                                 class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <?php if ($product['featured']): ?>
                                <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                    <i class="fas fa-star"></i> Featured
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 text-primary mb-0"><?php echo formatPrice($product['price']); ?></span>
                                    <span class="badge bg-<?php echo $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                                        Stock: <?php echo $product['stock']; ?>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['category_name']); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($product['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="row g-1">
                                <div class="col-12">
                                    <button class="btn btn-outline-primary btn-sm w-100" onclick="viewProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Products pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <!-- Product Details Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="productModalBody">
                    <!-- Product details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewProduct(productId) {
    // Load product details via AJAX
    fetch(`../ajax/get_product.php?id=${productId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('productModalBody').innerHTML = data;
            new bootstrap.Modal(document.getElementById('productModal')).show();
        })
        .catch(error => {
            console.error('Error loading product details:', error);
            alert('Error loading product details');
        });
}
</script>

<?php include '../includes/footer.php'; ?>
