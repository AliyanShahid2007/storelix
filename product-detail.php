<?php
include 'includes/header.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = getProductById($product_id);

if (!$product) {
    header('Location: products.php');
    exit();
}

$page_title = $product['name'];


?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php">Products</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <img src="<?php echo $product['image'] ? 'uploads/' . $product['image'] : 'https://via.placeholder.com/600x500?text=' . urlencode($product['name']); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>"
                     style="height: 500px; object-fit: cover;">
            </div>
        </div>
        
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-secondary fs-6">
                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
            </div>
            
            <div class="mb-4">
                <h2 class="product-price"><?php echo formatPrice($product['price']); ?></h2>
            </div>
            
            <div class="mb-4">
                <?php if ($product['stock'] > 0): ?>
                    <span class="badge bg-success fs-6">
                        <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock']; ?> available)
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger fs-6">
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <?php if (isLoggedIn() && $product['stock'] > 0): ?>
                <form action="cart-action.php" method="POST" class="mb-4">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                        </div>
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
            <?php elseif (!isLoggedIn()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Please <a href="login.php">login</a> to purchase this product.
                </div>
            <?php endif; ?>
            
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-truck"></i> Shipping Information</h6>
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-check text-success"></i> Fast delivery available</li>
                        <li><i class="fas fa-check text-success"></i> Free shipping on orders over $50</li>
                        <li><i class="fas fa-check text-success"></i> Easy returns within 30 days</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



    <!-- Related Products -->
    <div class="mt-5">
        <h3 class="mb-4">Related Products</h3>
        <div class="row g-4">
            <?php 
            $related_products = getProducts($product['category_id'], false, 4);
            foreach ($related_products as $related): 
                if ($related['id'] == $product['id']) continue;
            ?>
                <div class="col-md-3">
                    <div class="card h-100">
                        <img src="<?php echo $related['image'] ? 'uploads/' . $related['image'] : 'https://via.placeholder.com/300x250?text=' . urlencode($related['name']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($related['name']); ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h6>
                            <p class="product-price"><?php echo formatPrice($related['price']); ?></p>
                            <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
