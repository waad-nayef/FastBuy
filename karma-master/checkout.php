<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php?error=login_required_for_checkout");
    exit();
}


require_once '../config/db.php';
$db = Database::getInstance();
$user_id = $_SESSION['user_id'];


if (isset($_SESSION['guest_cart'])) {
   
    $cart = $db->getCartByUserId($user_id);
    if (!$cart) {
        $cart_id = $db->createCart($user_id);
        $cart = ['id' => $cart_id];
    }
    
    foreach ($_SESSION['guest_cart'] as $item) {
        $db->addToCart($cart['id'], $item['product_id'], $item['quantity']);
    }
    unset($_SESSION['guest_cart']);
}

$cart = $db->getCartByUserId($user_id);
$cartItems = [];
$cartTotal = 0;
if ($cart) {
    $cartItems = $db->getCartItemsWithDetails($cart['id']);
    $cartTotal = $db->getCartTotal($cart['id']);
}
?>

<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="img/fav.png">
    <meta charset="UTF-8">
    <title>Karma Shop - Checkout</title>
    <link rel="stylesheet" href="css/linearicons.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
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
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown">Shop</a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item"><a class="nav-link" href="category.php">Shop Category</a></li>
                                    <li class="nav-item"><a class="nav-link" href="single-product.php">Product Details</a></li>
                                    <li class="nav-item active"><a class="nav-link" href="checkout.php">Product Checkout</a></li>
                                    <li class="nav-item"><a class="nav-link" href="cart.php">Shopping Cart</a></li>
                                    <li class="nav-item"><a class="nav-link" href="confirmation.php">Confirmation</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="nav-item"><a href="cart.php" class="cart"><span class="ti-bag"></span></a></li>
                            <li class="nav-item"><button class="search"><span class="lnr lnr-magnifier" id="search"></span></button></li>
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
                    <h1>Checkout</h1>
                    <nav class="d-flex align-items-center">
                        <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                        <a>Checkout</a>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!--================Checkout Area =================-->
    <section class="checkout_area section_gap">
        <div class="container">
            <div class="billing_details">
                <div class="row">
                    <div class="col-lg-8">
                        <h3>Billing Details</h3>
                        <!-- يمكنك لاحقًا ملء الحقول من بيانات المستخدم -->
                        <form class="row contact_form" action="../actions/process_checkout.php" method="post" novalidate>
                            <div class="col-md-6 form-group p_star">
                                <input type="text" class="form-control" name="first_name" placeholder="First name" required>
                            </div>
                            <div class="col-md-6 form-group p_star">
                                <input type="text" class="form-control" name="last_name" placeholder="Last name" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="text" class="form-control" name="company" placeholder="Company name (optional)">
                            </div>
                            <div class="col-md-12 form-group p_star">
                                <input type="text" class="form-control" name="address" placeholder="Address" required>
                            </div>
                            <div class="col-md-6 form-group p_star">
                                <input type="text" class="form-control" name="city" placeholder="City" required>
                            </div>
                            <div class="col-md-6 form-group p_star">
                                <input type="text" class="form-control" name="postal_code" placeholder="Postal Code" required>
                            </div>
                            <div class="col-md-6 form-group p_star">
                                <input type="text" class="form-control" name="phone" placeholder="Phone number" required>
                            </div>
                            <div class="col-md-6 form-group p_star">
                                <input type="email" class="form-control" name="email" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <textarea class="form-control" name="notes" rows="2" placeholder="Order Notes (optional)"></textarea>
                            </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="order_box">
                            <h2>Your Order</h2>
                            <ul class="list">
                                <li><a href="#">Product <span>Total</span></a></li>
                                <?php if (empty($cartItems)): ?>
                                    <li>No items in cart.</li>
                                <?php else: ?>
                                    <?php foreach ($cartItems as $item): 
                                        $final_price = $item['price'] - ($item['price'] * $item['discount'] / 100);
                                        $total = $final_price * $item['quantity'];
                                    ?>
                                    <li>
                                        <a href="#"><?php echo htmlspecialchars($item['name']); ?> 
                                            <span class="middle">x <?php echo $item['quantity']; ?></span> 
                                            <span class="last">$<?php echo number_format($total, 2); ?></span>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                            <ul class="list list_2">
                                <li><a href="#">Subtotal <span>$<?php echo number_format($cartTotal, 2); ?></span></a></li>
                                <li><a href="#">Shipping <span>Free</span></a></li>
                                <li><a href="#">Total <span>$<?php echo number_format($cartTotal, 2); ?></span></a></li>
                            </ul>
                            <div class="payment_item active">
                                <div class="radion_btn">
                                    <input type="radio" id="paypal" name="payment_method" value="paypal" checked>
                                    <label for="paypal">PayPal</label>
                                    <div class="check"></div>
                                </div>
                                <p>Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.</p>
                            </div>
                            <div class="creat_account">
                                <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">I’ve read and accept the <a href="#">terms & conditions</a></label>
                            </div>
                            <button type="submit" class="primary-btn">Proceed to PayPal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-area section_gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="single-footer-widget">
                        <h6>About Us</h6>
                        <p>Lorem ipsum dolor sit amet.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-footer-widget">
                        <h6>Newsletter</h6>
                        <form class="form-inline">
                            <input class="form-control" placeholder="Enter Email" type="email" required>
                            <button class="click-btn"><i class="fa fa-long-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="single-footer-widget mail-chimp">
                        <h6>Instagram Feed</h6>
                        <ul class="instafeed d-flex flex-wrap">
                            <li><img src="img/i1.jpg" alt=""></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="single-footer-widget">
                        <h6>Follow Us</h6>
                        <div class="footer-social d-flex">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom d-flex justify-content-center">
                <p class="footer-text m-0">Copyright &copy; <script>document.write(new Date().getFullYear());</script> All rights reserved</p>
            </div>
        </div>
    </footer>

    <script src="js/vendor/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>