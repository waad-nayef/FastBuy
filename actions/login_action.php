<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: ../karma-master/login.php?error=invalid_credentials");
        exit();
    }

    $db = Database::getInstance();
    $user = $db->getUserByEmail($email);

    $password_valid = false;

    if ($user) {
        // 1. Check strict hash match
        if (password_verify($password, $user['password'])) {
            $password_valid = true;
        } 
        // 2. Fallback: Check plain text
        elseif ($password === $user['password']) {
            $password_valid = true;
        }
    }

        if ($password_valid) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            
            // Merge Guest Cart
            if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart'])) {
                $cart = $db->getCartByUserId($user['id']);
                if (!$cart) {
                    $cart_id = $db->createCart($user['id']);
                } else {
                    $cart_id = $cart['id'];
                }
                
                foreach ($_SESSION['guest_cart'] as $p_id => $qty) {
                    $db->addToCart($cart_id, $p_id, $qty);
                }
                unset($_SESSION['guest_cart']);
            }
            
            // Remember Me Logic
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $db->updateRememberToken($user['id'], $token);
                // Set cookie for 30 days
                setcookie('remember_me', $token, time() + (86400 * 30), "/");
            }

        
        // Redirect to main index to handle dispatching
        header("Location: ../index.php");
        exit();
    } else {
        header("Location: ../karma-master/login.php?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: ../karma-master/login.php");
    exit();
}
