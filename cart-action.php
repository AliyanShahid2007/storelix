<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: cart.php');
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

switch ($action) {
    case 'add':
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        if ($product_id && $quantity > 0) {
            if (addToCart($_SESSION['user_id'], $product_id, $quantity)) {
                header('Location: ' . (isset($_POST['redirect']) ? $_POST['redirect'] : 'cart.php') . '?success=Product added to cart');
            } else {
                header('Location: ' . (isset($_POST['redirect']) ? $_POST['redirect'] : 'cart.php') . '?error=Failed to add product');
            }
        }
        break;
        
    case 'update':
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        if ($product_id) {
            if (updateCartQuantity($_SESSION['user_id'], $product_id, $quantity)) {
                header('Location: cart.php?success=Cart updated');
            } else {
                header('Location: cart.php?error=Failed to update cart');
            }
        }
        break;
        
    case 'remove':
        if ($product_id) {
            if (removeFromCart($_SESSION['user_id'], $product_id)) {
                header('Location: cart.php?success=Item removed from cart');
            } else {
                header('Location: cart.php?error=Failed to remove item');
            }
        }
        break;
        
    default:
        header('Location: cart.php');
        break;
}

exit();
?>
