<?php
session_start();


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="img/fav.png">
    <meta charset="UTF-8">
    <title>Karma Shop - Login</title>
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
                            <li class="nav-item submenu dropdown">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown">Shop</a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item"><a class="nav-link" href="category.php">Shop Category</a></li>
                                    <li class="nav-item"><a class="nav-link" href="single-product.php">Product Details</a></li>
                                    <li class="nav-item"><a class="nav-link" href="checkout.php">Product Checkout</a></li>
                                    <li class="nav-item"><a class="nav-link" href="cart.php">Shopping Cart</a></li>
                                    <li class="nav-item"><a class="nav-link" href="confirmation.php">Confirmation</a></li>
                                </ul>
                            </li>
                            <li class="nav-item submenu dropdown active">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item active"><a class="nav-link" href="login.php">Login</a></li>
                                    <li class="nav-item"><a class="nav-link" href="signup.php">Signup</a></li>
                                    <li class="nav-item"><a class="nav-link" href="../actions/logout.php">Logout</a></li>
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
                    <h1>Login/Register</h1>
                    <nav class="d-flex align-items-center">
                        <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                        <a>Login</a>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!--================Login Box Area =================-->
    <section class="login_box_area section_gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login_box_img">
                        <img class="img-fluid" src="img/login.jpg" alt="">
                        <div class="hover">
                            <h4>New to our website?</h4>
                            <p>Create an account to enjoy faster checkout and order tracking.</p>
                            <a class="primary-btn" href="signup.php">Create an Account</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login_form_inner">
                        <h3>Log in to enter</h3>
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                $errors = [
                                    'login_required_for_checkout' => 'Please log in to proceed with checkout.',
                                    'invalid_credentials' => 'Invalid email or password.'
                                ];
                                echo htmlspecialchars($errors[$_GET['error']] ?? 'Login failed.');
                                ?>
                            </div>
                        <?php endif; ?>
                        <form class="row login_form" action="../actions/login_action.php" method="post" novalidate>
                            <div class="col-md-12 form-group">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <div class="creat_account">
                                    <input type="checkbox" id="f-option2" name="remember">
                                    <label for="f-option2">Keep me logged in</label>
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <button type="submit" class="primary-btn">Log In</button>
                                <a href="#">Forgot Password?</a>
                            </div>
                        </form>
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
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-footer-widget">
                        <h6>Newsletter</h6>
                        <p>Stay updated</p>
                        <form class="form-inline">
                            <input class="form-control" name="EMAIL" placeholder="Enter Email" type="email" required>
                            <button class="click-btn btn btn-default"><i class="fa fa-long-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="single-footer-widget mail-chimp">
                        <h6 class="mb-20">Instagram Feed</h6>
                        <ul class="instafeed d-flex flex-wrap">
                            <li><img src="img/i1.jpg" alt=""></li>
                            <li><img src="img/i2.jpg" alt=""></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="single-footer-widget">
                        <h6>Follow Us</h6>
                        <div class="footer-social d-flex">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom d-flex justify-content-center">
                <p class="footer-text m-0">Copyright &copy; <script>document.write(new Date().getFullYear());</script> All rights reserved | Made with <i class="fa fa-heart-o"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a></p>
            </div>
        </div>
    </footer>

    <script src="js/vendor/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>