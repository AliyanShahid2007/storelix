<?php
session_start();
require_once 'includes/functions.php';

// Test basic functions
echo "Testing basic functions...\n";

// Test isLoggedIn (should be false since no session)
echo "isLoggedIn: " . (isLoggedIn() ? 'true' : 'false') . "\n";

// Test isAdmin (should be false)
echo "isAdmin: " . (isAdmin() ? 'true' : 'false') . "\n";

// Test formatPrice
echo "formatPrice(10): " . formatPrice(10) . "\n";

// Test sanitizeInput
echo "sanitizeInput('<script>alert(1)</script>'): " . sanitizeInput('<script>alert(1)</script>') . "\n";

// Test getCategories (requires DB)
$categories = getCategories();
echo "getCategories count: " . count($categories) . "\n";

// Test getProducts
$products = getProducts(null, false, 5);
echo "getProducts count: " . count($products) . "\n";

// Test searchProducts
$searchResults = searchProducts('test');
echo "searchProducts count: " . count($searchResults) . "\n";

echo "Basic tests completed.\n";
?>
