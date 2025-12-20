<?php 
$page_title = 'Shopping Cart';
include 'includes/header.php';

requireLogin();

$cart_items = getCartItems($_SESSION['user_id']);
$cart_total = getCartTotal($_SESSION['user_id']);
?>

<div class="container my-5">
    <h1 class="mb-4">
        <i class="fas fa-shopping-cart"></i> Shopping Cart
    </h1>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (empty($cart_items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to get started</p>
            <a href="products.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="row mb-4 pb-4 border-bottom">
                                <div class="col-md-2">
                                    <img src="<?php echo $item['image'] ? 'uploads/' . $item['image'] : 'https://via.placeholder.com/150?text=Product'; ?>" 
                                         class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p class="text-muted mb-2"><?php echo formatPrice($item['price']); ?> each</p>
                                    <?php if ($item['stock'] < $item['quantity']): ?>
                                        <span class="badge bg-warning">Only <?php echo $item['stock']; ?> left in stock</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3">
                                    <form action="cart-action.php" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <div class="input-group">
                                            <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" 
                                                    class="btn btn-outline-secondary" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control text-center" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="<?php echo $item['stock']; ?>" readonly>
                                            <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" 
                                                    class="btn btn-outline-secondary" 
                                                    <?php echo $item['quantity'] >= $item['stock'] ? 'disabled' : ''; ?>>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-2 text-end">
                                    <h5 class="product-price"><?php echo formatPrice($item['price'] * $item['quantity']); ?></h5>
                                </div>
                                <div class="col-md-1 text-end">
                                    <form action="cart-action.php" method="POST" onsubmit="return confirmDelete('Remove this item from cart?')">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <a href="products.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <strong><?php echo formatPrice($cart_total); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <strong><?php echo $cart_total >= 50 ? 'FREE' : formatPrice(5.99); ?></strong>
                        </div>
                        <?php if ($cart_total >= 50): ?>
                            <div class="alert alert-success p-2 mb-3">
                                <small><i class="fas fa-check"></i> Free shipping applied!</small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info p-2 mb-3">
                                <small><i class="fas fa-info-circle"></i> Add <?php echo formatPrice(50 - $cart_total); ?> more for free shipping</small>
                            </div>
                        <?php endif; ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Total:</h5>
                            <h5 class="product-price">
                                <?php echo formatPrice($cart_total + ($cart_total >= 50 ? 0 : 5.99)); ?>
                            </h5>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-credit-card"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-shield-alt"></i> Secure Checkout</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li><i class="fas fa-check text-success"></i> SSL Encrypted Payment</li>
                            <li><i class="fas fa-check text-success"></i> 100% Money Back Guarantee</li>
                            <li><i class="fas fa-check text-success"></i> Multiple Payment Options</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
