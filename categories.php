<?php 
$page_title = 'Categories';
include 'includes/header.php';

$categories = getCategories();
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Browse Categories</h1>
    
    <div class="row g-4">
        <?php foreach ($categories as $category): ?>
            <div class="col-md-4">
                <a href="products.php?category=<?php echo $category['id']; ?>" class="text-decoration-none">
                    <div class="card category-card h-100">
                        <div class="card-body text-center p-5">
                            <i class="fas fa-box-open fa-4x text-primary mb-4"></i>
                            <h3 class="card-title text-dark"><?php echo htmlspecialchars($category['name']); ?></h3>
                            <p class="card-text text-muted mt-3"><?php echo htmlspecialchars($category['description']); ?></p>
                            <div class="mt-4">
                                <span class="btn btn-primary">
                                    <i class="fas fa-arrow-right"></i> Browse Products
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
