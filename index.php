        <?php 
$page_title = 'Home';
include 'includes/header.php';

$featured_products = getProducts(null, true, 8);
$categories = getCategories();
$special_offer = getActiveSpecialOffer();
$homepage_banners = getActiveHomepageBanners();
$testimonials = getActiveTestimonials(6); // Get up to 6 testimonials

// Check for maintenance mode
$maintenance_mode = $db->query("SELECT * FROM maintenance_mode WHERE is_enabled = 1 LIMIT 1")->fetch_assoc();

// Get homepage statistics
$homepage_stats = $db->query("SELECT * FROM homepage_stats WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 4")->fetch_all(MYSQLI_ASSOC);
?>

<!-- Maintenance Mode Banner -->
<?php if ($maintenance_mode): ?>
<div class="alert alert-warning alert-dismissible fade show maintenance-banner" role="alert">
    <div class="container-fluid">
        <div class="row align-items-center g-2">
            <div class="col-md-8 col-12">
                <i class="fas fa-tools me-2"></i>
                <strong><?php echo htmlspecialchars($maintenance_mode['title']); ?></strong>
                <span class="d-none d-md-inline"> - <?php echo htmlspecialchars($maintenance_mode['message']); ?></span>
            </div>
            <div class="col-md-4 col-12 text-md-end text-start">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<section class="hero-section">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            <?php if (!empty($homepage_banners)): ?>
                <?php foreach ($homepage_banners as $index => $banner): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo $banner['image'] ? 'uploads/' . $banner['image'] : 'https://via.placeholder.com/1920x600?text=' . urlencode($banner['title']); ?>');">
                        <div class="container">
                            <h1 class="display-4 mb-4 fw-bold text-shadow"><?php echo htmlspecialchars($banner['title']); ?></h1>
                            <?php if ($banner['subtitle']): ?>
                                <h2 class="h4 mb-3 text-shadow"><?php echo htmlspecialchars($banner['subtitle']); ?></h2>
                            <?php endif; ?>
                            <?php if ($banner['description']): ?>
                                <p class="lead mb-4 fs-5 text-shadow"><?php echo htmlspecialchars($banner['description']); ?></p>
                            <?php endif; ?>
                            <?php if ($banner['button_text'] && $banner['button_link']): ?>
                                <a href="<?php echo htmlspecialchars($banner['button_link']); ?>" class="btn btn-light btn-lg shadow-lg px-4 py-3">
                                    <i class="fas fa-arrow-right me-2"></i><?php echo htmlspecialchars($banner['button_text']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback static banners if no dynamic banners exist -->
                <div class="carousel-item active" style="background-image: url('uploads/b1.jpg');">
                    <div class="container">
                        <h1 class="display-4 mb-4 fw-bold text-shadow">Elevate Your Shopping Experience</h1>
                        <p class="lead mb-4 fs-5 text-shadow">Discover premium gifts, exquisite cards, and high-quality products for every special moment</p>
                        <a href="products.php" class="btn btn-light btn-lg shadow-lg px-4 py-3">
                            <i class="fas fa-shopping-bag me-2"></i> Explore Collection
                        </a>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('uploads/b3.jfif');">
                    <div class="container">
                        <h1 class="display-4 mb-4 fw-bold text-shadow">Curated for Perfection</h1>
                        <p class="lead mb-4 fs-5 text-shadow">Experience the finest selection of handcrafted gifts and premium stationery</p>
                        <a href="products.php" class="btn btn-light btn-lg shadow-lg px-4 py-3">
                            <i class="fas fa-shopping-bag me-2"></i> Explore Collection
                        </a>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('uploads/b4.jfif');">
                    <div class="container">
                        <h1 class="display-4 mb-4 fw-bold text-shadow">Quality That Speaks</h1>
                        <p class="lead mb-4 fs-5 text-shadow">Indulge in our carefully selected range of luxury items and thoughtful presents</p>
                        <a href="products.php" class="btn btn-light btn-lg shadow-lg px-4 py-3">
                            <i class="fas fa-shopping-bag me-2"></i> Explore Collection
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (count($homepage_banners) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        <?php endif; ?>
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

<!-- News & Announcements Section -->
<?php
$news_announcements = $db->query("SELECT * FROM news_announcements WHERE is_active = 1 ORDER BY is_featured DESC, created_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);

// Add sample news if no real data exists
if (empty($news_announcements)) {
    $news_announcements = array(
        array(
            'id' => 1,
            'title' => '🎉 New Year Mega Sale - Up to 50% Off!',
            'content' => 'Get ready to celebrate with our exclusive New Year sale! Shop your favorite gifts, cards, and products at unbeatable prices. Limited time offer - don\'t miss out on amazing deals across all categories!',
            'image' => '',
            'is_featured' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ),
        array(
            'id' => 2,
            'title' => '✨ Exclusive Premium Collection Launched',
            'content' => 'Introducing our brand new premium collection of handcrafted gifts and luxury items. Each piece is carefully selected for its quality and elegance. Explore our curated selection and find the perfect gift for your loved ones.',
            'image' => '',
            'is_featured' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ),
        array(
            'id' => 3,
            'title' => '🚚 Free Shipping on Orders Over $50',
            'content' => 'We\'re excited to announce free shipping on all orders over $50! Plus, get 10% off your first purchase with code WELCOME10. Shop now and enjoy fast, free delivery to your doorstep.',
            'image' => '',
            'is_featured' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ),
        array(
            'id' => 4,
            'title' => '🌟 Holiday Gift Guide 2024 - Perfect Presents for Everyone',
            'content' => 'Discover our comprehensive Holiday Gift Guide 2024! From elegant stationery to unique gifts, find the perfect present for every occasion. Explore curated collections and special holiday bundles.',
            'image' => '',
            'is_featured' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
        ),
        array(
            'id' => 5,
            'title' => '💳 New Payment Methods Added - PayPal & Apple Pay Now Available',
            'content' => 'We\'ve expanded our payment options! Now accept PayPal and Apple Pay for faster, more secure checkout. Enjoy seamless transactions and enhanced shopping experience with multiple payment methods.',
            'image' => '',
            'is_featured' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ),
        array(
            'id' => 6,
            'title' => '🎁 Customer Loyalty Program Launched - Earn Points on Every Purchase',
            'content' => 'Join our new Customer Loyalty Program! Earn points on every purchase and redeem them for exclusive discounts, free shipping, and special member-only offers. Start earning rewards today!',
            'image' => '',
            'is_featured' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ),
        array(
            'id' => 7,
            'title' => '📦 Eco-Friendly Packaging Initiative - Going Green Together',
            'content' => 'We\'re committed to sustainability! Introducing our new eco-friendly packaging made from recycled materials. Help us reduce environmental impact while enjoying your favorite products.',
            'image' => '',
            'is_featured' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-6 days'))
        ),
        array(
            'id' => 8,
            'title' => '🎨 Custom Gift Wrapping Service Now Available',
            'content' => 'Make your gifts extra special with our new custom gift wrapping service! Choose from various themes, colors, and add personal messages. Perfect for birthdays, anniversaries, and holidays.',
            'image' => '',
            'is_featured' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
        ),
        array(
            'id' => 9,
            'title' => '🏆 Store Anniversary Sale - 25% Off Everything',
            'content' => 'Celebrating our 5th anniversary! Enjoy 25% off on all products for a limited time. Thank you for being part of our journey - here\'s to many more years of great shopping!',
            'image' => '',
            'is_featured' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-8 days'))
        )
    );
}
?>
<section class="py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
            <div>
                <h2 class="mb-1"><i class="fas fa-newspaper text-info me-2"></i>Latest News & Updates</h2>
                <p class="text-muted mb-0">Stay informed about our latest offers and announcements</p>
            </div>
            <a href="#" class="btn btn-info" onclick="showAllNews(); return false;">
                <i class="fas fa-list me-2"></i>View All News
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($news_announcements as $news): ?>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card h-100 border-0 shadow-lg news-card" style="transition: all 0.3s ease; cursor: pointer;">
                        <?php if ($news['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($news['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($news['title']); ?>" style="height: 220px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-gradient" style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-newspaper text-white" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($news['is_featured']): ?>
                                    <span class="badge bg-warning text-dark me-2">
                                        <i class="fas fa-star me-1"></i>Featured
                                    </span>
                                <?php endif; ?>
                                <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i><?php echo date('M d, Y', strtotime($news['created_at'])); ?></small>
                            </div>
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($news['title']); ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(substr($news['content'], 0, 120)) . '...'; ?></p>
                            <div class="mt-auto pt-3">
                                <button class="btn btn-info btn-sm me-2" onclick="readMore('<?php echo htmlspecialchars($news['title']); ?>', '<?php echo htmlspecialchars($news['content']); ?>')">
                                    <i class="fas fa-book-open me-1"></i>Read More
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="shareNews('<?php echo htmlspecialchars($news['title']); ?>')">
                                    <i class="fas fa-share-alt me-1"></i>Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5"><i class="fas fa-star text-warning me-2"></i>Featured Products</h2>
        <div class="row g-4" id="featured-products-container">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card h-100 product-card">
                        <div class="position-relative">
                            <img src="<?php echo $product['image'] ? 'uploads/' . $product['image'] : 'https://via.placeholder.com/300x250?text=' . urlencode($product['name']); ?>"
                                 class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <!-- Secondary image for hover effect (using same image for now, can be enhanced later) -->
                            <img src="<?php echo $product['image'] ? 'uploads/' . $product['image'] : 'https://via.placeholder.com/300x250?text=' . urlencode($product['name']); ?>"
                                 class="card-img-top secondary-image" alt="<?php echo htmlspecialchars($product['name']); ?> hover">
                        </div>
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





<!-- Social Media Section -->
<?php
$social_links = $db->query("SELECT * FROM social_links WHERE is_active = 1 ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC);
if (!empty($social_links)):
?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Follow Us</h2>
            <p class="text-muted">Stay connected with us on social media</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-center gap-4">
                    <?php foreach ($social_links as $social): ?>
                        <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" class="btn btn-outline-primary btn-lg rounded-circle" title="<?php echo htmlspecialchars($social['platform']); ?>">
                            <i class="fab <?php echo htmlspecialchars($social['icon_class']); ?> fa-lg"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter Subscription Section -->
<section class="py-5 bg-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-2"><i class="fas fa-bell me-2"></i>Stay Updated</h3>
                <p class="mb-0">Subscribe to our newsletter for the latest updates, exclusive offers, and special promotions delivered directly to your inbox!</p>
            </div>
            <div class="col-lg-4">
                <form class="d-flex gap-2" id="newsletter-form" onsubmit="handleNewsletterSubmit(event)">
                    <input type="email" class="form-control" id="newsletter-email" placeholder="Enter your email" required>
                    <button type="submit" class="btn btn-light fw-bold newsletter-submit-btn" id="newsletter-btn">
                        <i class="fas fa-paper-plane me-1"></i>Subscribe
                    </button>
                </form>
                <div id="newsletter-message" class="mt-2"></div>
            </div>
        </div>
    </div>
</section>

<script>
// Newsletter Subscription Handler
function handleNewsletterSubmit(event) {
    event.preventDefault();
    const email = document.getElementById('newsletter-email').value;
    const btn = document.getElementById('newsletter-btn');
    const messageDiv = document.getElementById('newsletter-message');
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Subscribing...';
    
    // Simulate API call
    fetch('includes/subscribe-newsletter.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success alert-sm mb-0">✓ Thank you for subscribing!</div>';
            document.getElementById('newsletter-email').value = '';
            setTimeout(() => {
                messageDiv.innerHTML = '';
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 3000);
        } else {
            messageDiv.innerHTML = '<div class="alert alert-danger alert-sm mb-0">✗ ' + (data.message || 'Error subscribing') + '</div>';
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        messageDiv.innerHTML = '<div class="alert alert-danger alert-sm mb-0">✗ Subscription failed</div>';
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Read More News Function
function readMore(title, content) {
    const modal = new bootstrap.Modal(document.getElementById('newsModal') || createNewsModal());
    document.getElementById('newsModalTitle').innerHTML = title;
    document.getElementById('newsModalContent').innerHTML = '<p>' + content + '</p><p class="text-muted mt-3"><small>Thank you for reading our latest update!</small></p>';
    modal.show();
}

// Create News Modal dynamically if it doesn't exist
function createNewsModal() {
    if (document.getElementById('newsModal')) return document.getElementById('newsModal');
    
    const modalHTML = `
        <div class="modal fade" id="newsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newsModalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="newsModalContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="shareNews()">
                            <i class="fas fa-share-alt me-1"></i>Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    return document.getElementById('newsModal');
}

// Share News Function
function shareNews(title) {
    const text = 'Check out this news: ' + (title || 'Latest update from Storelix');
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: 'Storelix',
            text: text,
            url: url
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback for browsers that don't support Web Share API
        const shareText = `${text}\n\n${url}`;
        alert('Share this:\n\n' + shareText);
    }
}

// Show All News Function
function showAllNews() {
    window.location.href = '#news-section';
    alert('All news items are displayed above. Check our admin panel to add more news!');
}

// Add hover effect to news cards
document.addEventListener('DOMContentLoaded', function() {
    var myCarousel = document.querySelector('#heroCarousel');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 3000,
        ride: 'carousel'
    });

    // Add hover effects to news cards
    const newsCards = document.querySelectorAll('.news-card');
    newsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 15px 40px rgba(0,0,0,0.2)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
        });
    });

    // Initialize Swiper
    var swiper = new Swiper(".mySwiper", {
        spaceBetween: 30,
        centeredSlides: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    // Skeleton Loader Functionality
    const container = document.getElementById('featured-products-container');
    const productCards = container.querySelectorAll('.product-card');

    // Hide actual products initially and show skeleton loaders
    productCards.forEach(card => {
        card.style.opacity = '0';
        card.style.pointerEvents = 'none';
    });

    // Create skeleton loaders
    for (let i = 0; i < 8; i++) {
        const skeletonCard = document.createElement('div');
        skeletonCard.className = 'col-md-3 skeleton-card';
        skeletonCard.innerHTML = `
            <div class="card h-100">
                <div class="skeleton skeleton-img"></div>
                <div class="card-body d-flex flex-column">
                    <div class="skeleton skeleton-title"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-btn"></div>
                </div>
            </div>
        `;
        container.appendChild(skeletonCard);
    }

    // Show actual products after delay (simulating loading time)
    setTimeout(() => {
        // Remove skeleton loaders
        const skeletonCards = container.querySelectorAll('.skeleton-card');
        skeletonCards.forEach(card => card.remove());

        // Show actual products
        productCards.forEach(card => {
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
            card.style.transition = 'opacity 0.5s ease';
        });
    }, 1500); // 1.5 second delay

    // Achievements Loading with Special Animation and Effects
    setTimeout(() => {
        const achievementsSection = document.getElementById('achievementsSection');
        if (achievementsSection) {
            // Show the section with fade-in animation
            achievementsSection.style.display = 'block';
            achievementsSection.style.opacity = '0';
            achievementsSection.style.transform = 'translateY(30px)';

            // Trigger fade-in animation
            setTimeout(() => {
                achievementsSection.style.opacity = '1';
                achievementsSection.style.transform = 'translateY(0)';
            }, 100);

            // Add special effects to achievement cards
            const achievementCards = achievementsSection.querySelectorAll('.achievement-card');
            achievementCards.forEach((card, index) => {
                // Reset initial state
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8) rotateY(90deg)';
                card.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

                // Animate each card with staggered delay
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1) rotateY(0deg)';

                    // Add glow effect for completed achievements
                    card.style.boxShadow = '0 0 20px rgba(255, 215, 0, 0.4), 0 0 40px rgba(255, 215, 0, 0.2)';
                    card.style.animation = 'achievementGlow 2s ease-in-out infinite alternate';

                    // Add sparkle effect
                    addSparkleEffect(card);

                    // Animate numbers counting up
                    const numberElement = card.querySelector('.achievement-number');
                    if (numberElement) {
                        const target = parseInt(numberElement.getAttribute('data-target'));
                        animateNumber(numberElement, 0, target, 2000);
                    }

                    // Animate progress bars
                    const progressBar = card.querySelector('.progress-bar');
                    if (progressBar) {
                        const targetWidth = '100%'; // All achievements are completed
                        progressBar.style.width = '0%';
                        setTimeout(() => {
                            progressBar.style.transition = 'width 2s ease-out';
                            progressBar.style.width = targetWidth;
                        }, 500);
                    }
                }, index * 200); // Staggered animation
            });

            // Show achievement unlocked notification
            showAchievementNotification();
        }
    }, 5000); // 5 seconds delay

    // Function to animate numbers counting up
    function animateNumber(element, start, end, duration) {
        const startTime = performance.now();
        const difference = end - start;

        function updateNumber(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function for smooth animation
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(start + (difference * easeOutCubic));

            element.textContent = current.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            }
        }

        requestAnimationFrame(updateNumber);
    }

    // Function to add sparkle effects
    function addSparkleEffect(element) {
        for (let i = 0; i < 8; i++) {
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            sparkle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: #ffd700;
                border-radius: 50%;
                pointer-events: none;
                animation: sparkleAnimation 1.5s ease-out forwards;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                z-index: 10;
            `;
            element.style.position = 'relative';
            element.appendChild(sparkle);

            // Remove sparkle after animation
            setTimeout(() => {
                sparkle.remove();
            }, 1500);
        }
    }

    // Achievement unlocked notification
    function showAchievementNotification() {
        const notification = document.createElement('div');
        notification.className = 'achievement-notification';
        notification.innerHTML = `
            <div class="achievement-popup">
                <i class="fas fa-trophy text-warning me-2"></i>
                <span>Milestones Achieved!</span>
                <div class="achievement-progress-text">Celebrating our success story</div>
            </div>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            animation: slideInRight 0.5s ease-out;
        `;
        document.body.appendChild(notification);

        // Auto remove after 4 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.5s ease-in forwards';
            setTimeout(() => notification.remove(), 500);
        }, 4000);
    }

    // Edit Achievement Function
    function editAchievement(type) {
        // Redirect to homepage-settings page with achievement type parameter
        window.location.href = 'admin/homepage-settings.php?edit_achievement=' + type;
    }

});
</script>
<?php include 'includes/footer.php'; ?>
