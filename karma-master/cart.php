<?php
session_start();
require_once '../config/db.php';
$db = Database::getInstance();

$cartItemsWithDetails = [];
$cartTotal = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart = $db->getCartByUserId($user_id);
    if ($cart) {
        $cartItemsWithDetails = $db->getCartItemsWithDetails($cart['id']);
        $cartTotal = $db->getCartTotal($cart['id']);
    }
} else {
    if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart'])) {
        foreach ($_SESSION['guest_cart'] as $item) {
            $product = $db->getProductById($item['product_id']);
            if ($product) {
                $total_price = ((float)$product['price']) * $item['quantity'];
                $cartItemsWithDetails[] = array_merge($product, ['quantity' => $item['quantity'], 'total_price' => $total_price]);
                $cartTotal += $total_price;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="img/fav.png">
<meta charset="UTF-8">
<title>Karma Shop</title>
<link rel="stylesheet" href="css/linearicons.css">
<link rel="stylesheet" href="css/owl.carousel.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/themify-icons.css">
<link rel="stylesheet" href="css/nice-select.css">
<link rel="stylesheet" href="css/nouislider.min.css">
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/main.css">
</head>
<body>

<!-- Start Header Area -->
<header class="header_area sticky-header">
<div class="main_menu">
<nav class="navbar navbar-expand-lg navbar-light main_box">
<div class="container">
<a class="navbar-brand logo_h" href="index.php"><img src="img/logo.png" alt=""></a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
<span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
</button>
<div class="collapse navbar-collapse offset" id="navbarSupportedContent">
<ul class="nav navbar-nav menu_nav ml-auto">
<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
<li class="nav-item submenu dropdown active">
<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Shop</a>
<ul class="dropdown-menu">
<li class="nav-item"><a class="nav-link" href="category.php">Shop Category</a></li>
<li class="nav-item"><a class="nav-link" href="single-product.php">Product Details</a></li>
<li class="nav-item"><a class="nav-link" href="checkout.php">Product Checkout</a></li>
<li class="nav-item active"><a class="nav-link" href="cart.php">Shopping Cart</a></li>
<li class="nav-item"><a class="nav-link" href="confirmation.php">Confirmation</a></li>
</ul>
</li>
<!-- ... باقي القائمة ... -->
<li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
</ul>
</div>
</div>
</nav>
</div>
</header>

<!-- Start Banner Area -->
<section class="banner-area organic-breadcrumb">
<div class="container">
<div class="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
<div class="col-first">
<h1>Shopping Cart</h1>
<nav class="d-flex align-items-center">
<a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
<a href="cart.php">Cart</a>
</nav>
</div>
</div>
</div>
</section>

<!--================Cart Area =================-->
<section class="cart_area">
<div class="container">
<div class="cart_inner">
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th scope="col">Product</th>
<th scope="col">Price</th>
<th scope="col">Quantity</th>
<th scope="col">Total</th>
</tr>
</thead>
<tbody>
<?php if (empty($cartItemsWithDetails)): ?>
<tr>
<td colspan="4" class="text-center"><h5>Your cart is empty.</h5></td>
</tr>
<?php else: ?>
<?php foreach ($cartItemsWithDetails as $item): 
    $price = (float)$item['price'];
    $discount = (float)$item['discount'];
    $final_price = $discount > 0 ? $price - ($price * $discount / 100) : $price;
    $total_price = $final_price * $item['quantity'];
?>
<tr>
<td>
<div class="media">
<div class="d-flex">

<img src="<?php echo (isset($_SESSION['user_id'])) ? 'uploads/' : ''; ?><?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
</div>
<div class="media-body">
<p><?php echo htmlspecialchars($item['name']); ?></p>
</div>
</div>
</td>
<td><h5>$<?php echo number_format($final_price, 2); ?></h5></td>
<td>
<div class="product_count">
<input type="text" value="<?php echo $item['quantity']; ?>" class="input-text qty" readonly>
</div>
</td>
<td><h5>$<?php echo number_format($total_price, 2); ?></h5></td>
</tr>
<?php endforeach; ?>
<tr>
<td></td><td></td>
<td><h5>Subtotal</h5></td>
<td><h5>$<?php echo number_format($cartTotal, 2); ?></h5></td>
</tr>
<tr class="out_button_area">
<td></td><td></td><td></td>
<td>
<div class="checkout_btn_inner d-flex align-items-center">
<a class="gray_btn" href="index.php">Continue Shopping</a>
<a class="primary-btn" href="checkout.php">Proceed to checkout</a>
</div>
</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</section>

<!-- ... Footer ... -->
<script src="js/vendor/jquery-2.2.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="js/vendor/bootstrap.min.js"></script>
<script src="js/jquery.nice-select.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>