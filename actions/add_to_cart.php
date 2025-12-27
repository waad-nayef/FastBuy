<?php
session_start();

require_once '../config/db.php';

// دالة لإضافة المنتج لسلة الضيف (السيشن)
function addToSessionCart($product_id, $quantity) {
    if (!isset($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }

    foreach ($_SESSION['guest_cart'] as &$item) {
        if ($item['product_id'] == $product_id) {
            $item['quantity'] += $quantity;
            return true;
        }
    }

    $_SESSION['guest_cart'][] = [
        'product_id' => $product_id,
        'quantity' => $quantity
    ];
    return true;
}

// دالة لنقل سلة الضيف إلى قاعدة البيانات عند تسجيل الدخول
function migrateSessionCartToDB($user_id, $db) {
    if (!isset($_SESSION['guest_cart']) || empty($_SESSION['guest_cart'])) {
        return;
    }

    $cart = $db->getCartByUserId($user_id);
    if (!$cart) {
        $db->createCart($user_id);
        $cart = $db->getCartByUserId($user_id);
    }

    foreach ($_SESSION['guest_cart'] as $item) {
        $db->addToCart($cart['id'], $item['product_id'], $item['quantity']);
    }

    unset($_SESSION['guest_cart']);
}

// جلب بيانات المنتج
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
$quantity = 1; // يمكن تحسينه لاحقًا

if (!$product_id) {
    header("Location: ../karma-master/index.php");
    exit();
}

// التحقق من تسجيل الدخول
if (isset($_SESSION['user_id'])) {
    $db = Database::getInstance();
    $user_id = $_SESSION['user_id'];

    migrateSessionCartToDB($user_id, $db);

    $cart = $db->getCartByUserId($user_id);
    if (!$cart) {
        $db->createCart($user_id);
        $cart = $db->getCartByUserId($user_id);
    }

    $result = $db->addToCart($cart['id'], $product_id, $quantity);
    $redirect_url = $result ? 
        "../karma-master/cart.php?success=item_added" : 
        "../karma-master/single-product.php?id=$product_id&error=out_of_stock";

} else {
    // المستخدم غير مسجل
    addToSessionCart($product_id, $quantity);
    $redirect_url = "../karma-master/cart.php?success=item_added_as_guest";
}

header("Location: $redirect_url");
exit();
?>