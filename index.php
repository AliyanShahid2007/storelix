    <?php 
$page_title = 'Home';
include 'includes/header.php';

$featured_products = getProducts(null, true, 8);
$categories = getCategories();
$special_offer = getActiveSpecialOffer();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="1000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('b2.jpg');">
                <div class="container">
                    <h1 class="display-4 mb-4">Welcome to <?php echo SITE_NAME; ?></h1>
                    <p class="lead mb-4">Discover unique gifts, beautiful cards, and quality products for every occasion</p>
                    <a href="products.php" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag"></i> Shop Now
                    </a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('b3.jfif');">
                <div class="container">
                    <h1 class="display-4 mb-4">Welcome to <?php echo SITE_NAME; ?></h1>
                    <p class="lead mb-4">Discover unique gifts, beautiful cards, and quality products for every occasion</p>
                    <a href="products.php" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag"></i> Shop Now
                    </a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('b4.jfif');">
                <div class="container">
                    <h1 class="display-4 mb-4">Welcome to <?php echo SITE_NAME; ?></h1>
                    <p class="lead mb-4">Discover unique gifts, beautiful cards, and quality products for every occasion</p>
                    <a href="products.php" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag"></i> Shop Now
                    </a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<!-- Special Offer Countdown Timer -->
<?php if ($special_offer): ?>
<section class="special-offer-section py-4">
    <div class="container">
        <div class="special-offer-banner">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="mb-2"><?php echo htmlspecialchars($special_offer['title']); ?></h3>
                    <p class="mb-0"><?php echo htmlspecialchars($special_offer['description']); ?></p>
                    <div class="discount-badge">
                        <span class="badge bg-warning text-dark fs-6"><?php echo $special_offer['discount_percentage']; ?>% OFF</span>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="countdown-timer" id="countdown-timer" data-endtime="<?php echo isset($special_offer) && $special_offer ? date('c', strtotime($special_offer['end_time'])) : ''; ?>">
                        <div class="timer-label">Offer ends in:</div>
                        <div class="timer-display">
                            <div class="timer-item">
                                <span class="timer-value" id="days">00</span>
                                <span class="timer-unit">Days</span>
                            </div>
                            <div class="timer-item">
                                <span class="timer-value" id="hours">00</span>
                                <span class="timer-unit">Hours</span>
                            </div>
                            <div class="timer-item">
                                <span class="timer-value" id="minutes">00</span>
                                <span class="timer-unit">Min</span>
                            </div>
                            <div class="timer-item">
                                <span class="timer-value" id="seconds">00</span>
                                <span class="timer-unit">Sec</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Explore Our Categories</h2>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="products.php?category=<?php echo $category['id']; ?>" class="text-decoration-none">
                        <div class="card category-card">
                            <div class="card-body text-center p-4">
                                <i class="<?php echo getCategoryIcon($category['name']); ?> fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">
                                    <i class="<?php echo getCategoryIcon($category['name']); ?> me-2"></i>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Featured Products</h2>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-3">
                    <div class="card h-100">
                        <img src="<?php echo $product['image'] ? 'uploads/' . $product['image'] : 'https://via.placeholder.com/300x250?text=' . urlencode($product['name']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="badge bg-success">In Stock</span>
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
        
        <div class="text-center mt-5">
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-th"></i> View All Products
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 col-sm-6 text-center">
                <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h5>Fast Delivery</h5>
                <p class="text-muted">Quick and reliable shipping</p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 text-center">
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h5>Secure Payment</h5>
                <p class="text-muted">100% secure transactions</p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 text-center">
                <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                <h5>24/7 Support</h5>
                <p class="text-muted">Always here to help</p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 text-center">
                <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                <h5>Easy Returns</h5>
                <p class="text-muted">Hassle-free return policy</p>
            </div>
        </div>
    </div>
</section>



<?php include 'includes/footer.php'; ?>
