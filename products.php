<?php 
$page_title = 'Products';
include 'includes/header.php';

$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

if ($search) {
    $products = searchProducts($search);
    $page_heading = 'Search Results: ' . htmlspecialchars($search);
} elseif ($category_id) {
    $products = getProducts($category_id);
    $category = getCategoryById($category_id);
    $page_heading = $category ? $category['name'] : 'Products';
} else {
    $products = getProducts();
    $page_heading = 'All Products';
}

$categories = getCategories();
?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Products</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Categories</h6>
                    <div class="list-group">
                        <a href="products.php" class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">
                            All Products
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="products.php?category=<?php echo $cat['id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $page_heading; ?></h2>
                <span class="badge bg-primary"><?php echo count($products); ?> Products</span>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No products found.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="<?php echo $product['image'] ? 'uploads/' . $product['image'] : 'https://via.placeholder.com/300x250?text=' . urlencode($product['name']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <span class="badge bg-secondary mb-2 align-self-start">
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </span>
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                        <?php if ($product['stock'] > 0): ?>
                                            <span class="badge bg-success">Stock: <?php echo $product['stock']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-3">
                                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <?php if (isLoggedIn() && $product['stock'] > 0): ?>
                                            <form action="cart-action.php" method="POST" class="d-inline w-100">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm w-100" onclick="addToCartAnimation(this)">
                                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                                </button>
                                            </form>
                                        <?php elseif (!isLoggedIn()): ?>
                                            <a href="login.php" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-sign-in-alt"></i> Login to Buy
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
