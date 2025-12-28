<?php
session_start();
require_once '../config/db.php';

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;

if ($product_id <= 0 || $quantity < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Input']);
    exit();
}

$success = false;
$message = 'Cart updated';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $db = Database::getInstance();
    $cart = $db->getCartByUserId($user_id);
    if ($cart) {
        // Need Cart Item ID to use updateCartItem, or finding item first
        // I will use a direct query or helper in DB if available, 
        // but db.php has getCartItems which returns product_id too.
        // I'll create a helper getCartItem($cart_id, $product_id) or find logic here.
        
        $stmt = $db->query("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?", [$cart['id'], $product_id]);
        $item = $stmt->fetch();
        
        if ($item) {
            if ($quantity == 0) {
                 $db->removeFromCart($item['id']);
                 $message = 'Item removed';
            } else {
                 $db->updateCartItem($item['id'], $quantity);
                 $message = 'Quantity updated';
            }
            $success = true;
        }
    }
} else {
    // Guest Cart
    if (isset($_SESSION['guest_cart'])) {
        if ($quantity == 0) {
            unset($_SESSION['guest_cart'][$product_id]);
             $message = 'Item removed';
        } else {
            $_SESSION['guest_cart'][$product_id] = $quantity;
             $message = 'Quantity updated';
        }
        $success = true;
    }
}

if ($success) {
    echo json_encode(['status' => 'success', 'message' => $message]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update cart']);
}
?>
