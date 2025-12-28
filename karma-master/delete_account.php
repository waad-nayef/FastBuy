<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../karma-master/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {

    $cart = $db->getCartByUserId($user_id);
    if ($cart) {
        $db->clearCart($cart['id']);
    }

    $wishlist = $db->getWishlistByUserId($user_id);
    if ($wishlist) {
        $db->deleteWishlist($wishlist['id']);
    }

    $db->deleteUser($user_id);

    session_destroy();

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zxx" class="no-js">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="img/fav.png">
    <meta charset="UTF-8">
    <title>Delete Account - Karma Shop</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body>

    <!-- Start Header Area -->
    <?php include "includes/navbar.php"; ?>
    <!-- End Header Area -->

    <!-- Start Banner Area -->
    <section class="banner-area organic-breadcrumb">
        <div class="container">
            <div class="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                <div class="col-first">
                    <h1>Delete Account</h1>
                    <nav class="d-flex align-items-center">
                        <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                        <a href="user_profile.php">Profile</a>
                        <span class="lnr lnr-arrow-right"></span> Delete Account
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <!-- End Banner Area -->

    <!-- Confirmation -->
    <section class="cart_area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h3>Are you sure you want to delete your account?</h3>
                    <p>This action is irreversible and will permanently delete all your data including orders, cart, wishlist, etc.</p>

                    <form method="POST">
                        <input type="hidden" name="confirm_delete" value="1">
                        <button type="submit" class="btn btn-danger">Yes, Delete My Account</button>
                        <a href="user_profile.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="../js/vendor/jquery-2.2.4.min.js"></script>
    <script src="../js/vendor/bootstrap.min.js"></script>
</body>

</html>