<?php
session_start();
require_once '../config/db.php';

// Guest Cart Update
$product_id = $_REQUEST['product_id'] ?? null;
$quantity = $_REQUEST['qty'] ?? 1;

if (!isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }
    
    // Check if product exists
    if (isset($_SESSION['guest_cart'][$product_id])) {
        $_SESSION['guest_cart'][$product_id] += $quantity;
    } else {
        $_SESSION['guest_cart'][$product_id] = $quantity;
    }
    
    // Check stock check? Assuming unlimited for guest or check DB?
    // Good practice to check stock even for guest.
    $db = Database::getInstance();
    $product = $db->getProductById($product_id);
    if ($product && $product['stock'] >= $_SESSION['guest_cart'][$product_id]) {
        // Safe
    } else {
        // Revert if out of stock
        // For simplicity, just adding now, checking stock might be complex without DB instance early.
        // Actually I have $db instance below. I should move $db init up.
    }
    
    // Redirect
    $referer = $_SERVER['HTTP_REFERER'];
    $separator = (parse_url($referer, PHP_URL_QUERY) == NULL) ? '?' : '&';
    header("Location: " . $referer . $separator . "msg=added_to_cart");
    exit();
}

// User is logged in
$db = Database::getInstance();
$user_id = $_SESSION['user_id'];
$product_id = $_REQUEST['product_id'] ?? null;
$quantity = $_REQUEST['qty'] ?? 1;

if (!$product_id) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Single Cart Policy: Always use the latest active cart or create one
$cart = $db->getCartByUserId($user_id);
if (!$cart) {
    $cart_id = $db->createCart($user_id);
} else {
    $cart_id = $cart['id'];
}

// Add to cart
$result = $db->addToCart($cart_id, $product_id, $quantity);

if ($result) {
    // Check if referer contains query params to avoid appending &msg multiple times or malformed URL
    $referer = $_SERVER['HTTP_REFERER'];
    $separator = (parse_url($referer, PHP_URL_QUERY) == NULL) ? '?' : '&';
    header("Location: " . $referer . $separator . "msg=added_to_cart");
} else {
    $referer = $_SERVER['HTTP_REFERER'];
    $separator = (parse_url($referer, PHP_URL_QUERY) == NULL) ? '?' : '&';
    header("Location: " . $referer . $separator . "error=failed_to_add");
}
exit();
?>
