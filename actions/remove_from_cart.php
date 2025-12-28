<?php
session_start();
require_once '../config/db.php';

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : (isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0);

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Product']);
    exit();
}

$success = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $db = Database::getInstance();
    $cart = $db->getCartByUserId($user_id);
    if ($cart) {
        $db->removeProductFromCart($cart['id'], $product_id);
        $success = true;
    }
} else {
    if (isset($_SESSION['guest_cart']) && isset($_SESSION['guest_cart'][$product_id])) {
        unset($_SESSION['guest_cart'][$product_id]);
        $success = true;
    }
}

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Item removed']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Item not found']);
}
?>