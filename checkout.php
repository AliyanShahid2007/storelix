<?php
$page_title = 'Checkout';
require_once 'includes/functions.php';
requireLogin();

$cart_items = getCartItems($_SESSION['user_id']);
$cart_total = getCartTotal($_SESSION['user_id']);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$shipping_cost = $cart_total >= 50 ? 0 : 5.99;
$grand_total = $cart_total + $shipping_cost;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = sanitizeInput($_POST['payment_method']);
    $shipping_address = sanitizeInput($_POST['shipping_address']);

    if (empty($payment_method) || empty($shipping_address)) {
        $error = 'Please fill in all required fields.';
    } elseif ($payment_method !== 'Cash on Delivery') {
        // Validate card details for non-COD payments
        $card_number = sanitizeInput($_POST['card_number'] ?? '');
        $expiry_date = sanitizeInput($_POST['expiry_date'] ?? '');
        $cvv = sanitizeInput($_POST['cvv'] ?? '');
        $card_holder_name = sanitizeInput($_POST['card_holder_name'] ?? '');

        if (empty($card_number) || empty($expiry_date) || empty($cvv) || empty($card_holder_name)) {
            $error = 'Please fill in all card details.';
        } elseif (strlen(preg_replace('/\s+/', '', $card_number)) < 13) {
            $error = 'Please enter a valid card number.';
        } elseif (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry_date)) {
            $error = 'Please enter a valid expiry date (MM/YY).';
        } elseif (strlen($cvv) < 3) {
            $error = 'Please enter a valid CVV.';
        }
    }

    if (!$error) {
        $order_id = createOrder($_SESSION['user_id'], $payment_method, $shipping_address);

        if ($order_id) {
            header('Location: order-confirmation.php?order_id=' . $order_id);
            exit();
        } else {
            $error = 'Failed to create order. Please try again.';
        }
    }
}

// Get user info for pre-filling
global $db;
$user_id = $_SESSION['user_id'];
$result = $db->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="fas fa-credit-card"></i> Checkout</h1>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
            <li class="breadcrumb-item active">Checkout</li>
        </ol>
    </nav>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="row">
            <div class="col-lg-8">
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" 
                                   value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">
                                Shipping Address <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" 
                                      rows="4" required><?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : htmlspecialchars($user['address']); ?></textarea>
                            <small class="text-muted">Include street address, city, state, and ZIP code</small>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-credit-card"></i> Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="credit_card" 
                                   value="Credit Card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Credit Card') || !isset($_POST['payment_method']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="credit_card">
                                <i class="fas fa-credit-card"></i> Credit/Debit Card
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="paypal" 
                                   value="PayPal" <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'PayPal' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="paypal">
                                <i class="fab fa-paypal"></i> PayPal
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" 
                                   value="Bank Transfer" <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'Bank Transfer' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bank_transfer">
                                <i class="fas fa-university"></i> Bank Transfer
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" 
                                   value="Cash on Delivery" <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'Cash on Delivery' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="cod">
                                <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                            </label>
                        </div>
                        
                        <!-- Card Details Section -->
                        <div id="card-details" class="mt-4" style="display: none;">
                            <h6 class="mb-3"><i class="fas fa-credit-card"></i> Card Details</h6>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="card_number" class="form-label">Card Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="card_number" name="card_number"
                                           placeholder="1234 5678 9012 3456" maxlength="19"
                                           value="<?php echo isset($_POST['card_number']) ? htmlspecialchars($_POST['card_number']) : ''; ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date"
                                           placeholder="MM/YY" maxlength="5"
                                           value="<?php echo isset($_POST['expiry_date']) ? htmlspecialchars($_POST['expiry_date']) : ''; ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="cvv" name="cvv"
                                           placeholder="123" maxlength="4"
                                           value="<?php echo isset($_POST['cvv']) ? htmlspecialchars($_POST['cvv']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="card_holder_name" class="form-label">Card Holder Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="card_holder_name" name="card_holder_name"
                                       placeholder="John Doe"
                                       value="<?php echo isset($_POST['card_holder_name']) ? htmlspecialchars($_POST['card_holder_name']) : ''; ?>">
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle"></i> You will not be charged until your order is confirmed.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <small><?php echo htmlspecialchars($item['name']); ?></small>
                                    <small class="text-muted"> x<?php echo $item['quantity']; ?></small>
                                </div>
                                <small><strong><?php echo formatPrice($item['price'] * $item['quantity']); ?></strong></small>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong><?php echo formatPrice($cart_total); ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <strong><?php echo $shipping_cost == 0 ? 'FREE' : formatPrice($shipping_cost); ?></strong>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Total:</h5>
                            <h5 class="product-price"><?php echo formatPrice($grand_total); ?></h5>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-check"></i> Place Order
                        </button>
                        
                        <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Back to Cart
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-lock"></i> Secure Checkout</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li><i class="fas fa-check text-success"></i> SSL Encrypted</li>
                            <li><i class="fas fa-check text-success"></i> PCI Compliant</li>
                            <li><i class="fas fa-check text-success"></i> Money Back Guarantee</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Show/hide card details based on payment method
document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        const cardDetails = document.getElementById('card-details');
        if (this.value === 'Cash on Delivery') {
            cardDetails.style.display = 'none';
        } else {
            cardDetails.style.display = 'block';
        }
    });
});

// Format card number with spaces
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Format expiry date
document.getElementById('expiry_date').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});

// Show card details on page load if a payment method is selected
document.addEventListener('DOMContentLoaded', function() {
    const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    if (selectedPayment && selectedPayment.value !== 'Cash on Delivery') {
        document.getElementById('card-details').style.display = 'block';
    }
});
</script>

<?php include 'includes/footer.php'; ?>
