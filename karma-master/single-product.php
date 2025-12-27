<?php
session_start();
require_once '../config/db.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: index.php");
    exit();
}

$db = Database::getInstance();
$product = $db->getProductById($product_id);

if (!$product) {
    header("Location: index.php?error=product_not_found");
    exit();
}

$category = $db->getCategoryById($product['category_id']);
$reviews = $db->getProductReviewsWithUsers($product_id);
$rating_info = $db->getProductAverageRating($product_id);
$avg_rating = $rating_info['avg_rating'] ? round($rating_info['avg_rating'], 1) : 0;
$review_count = $rating_info['review_count'];
?>

<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="img/fav.png">
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - Karma Shop</title>
    <link rel="stylesheet" href="css/linearicons.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/nouislider.min.css">
    <link rel="stylesheet" href="css/ion.rangeSlider.css" />
    <link rel="stylesheet" href="css/ion.rangeSlider.skinFlat.css" />
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .star-rating {
            direction: rtl;
            unicode-bidi: bidi-override;
            font-size: 24px;
            color: #ddd;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            cursor: pointer;
        }
        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffba00;
        }
    </style>
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
                        <li class="nav-item active"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item submenu dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown">Shop</a>
                            <ul class="dropdown-menu">
                                <li class="nav-item"><a class="nav-link" href="category.php">Shop Category</a></li>
                                <li class="nav-item active"><a class="nav-link" href="single-product.php?id=<?php echo $product['id']; ?>">Product Details</a></li>
                                <li class="nav-item"><a class="nav-link" href="checkout.php">Product Checkout</a></li>
                                <li class="nav-item"><a class="nav-link" href="cart.php">Shopping Cart</a></li>
                                <li class="nav-item"><a class="nav-link" href="confirmation.php">Confirmation</a></li>
                            </ul>
                        </li>
                        <li class="nav-item submenu dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown">Blog</a>
                            <ul class="dropdown-menu">
                                <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                                <li class="nav-item"><a class="nav-link" href="single-blog.php">Blog Details</a></li>
                            </ul>
                        </li>
                        <li class="nav-item submenu dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                            <ul class="dropdown-menu">
                                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                                <li class="nav-item"><a class="nav-link" href="signup.php">Signup</a></li>
                                <li class="nav-item"><a class="nav-link" href="../actions/logout.php">Logout</a></li>
                                <li class="nav-item"><a class="nav-link" href="tracking.php">Tracking</a></li>
                                <li class="nav-item"><a class="nav-link" href="elements.php">Elements</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="nav-item"><a href="cart.php" class="cart"><span class="ti-bag"></span></a></li>
                        <li class="nav-item">
                            <button class="search"><span class="lnr lnr-magnifier" id="search"></span></button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="search_input" id="search_input_box">
        <div class="container">
            <form class="d-flex justify-content-between">
                <input type="text" class="form-control" id="search_input" placeholder="Search Here">
                <button type="submit" class="btn"></button>
                <span class="lnr lnr-cross" id="close_search" title="Close Search"></span>
            </form>
        </div>
    </div>
</header>
<!-- End Header Area -->

<!-- Start Banner Area -->
<section class="banner-area organic-breadcrumb">
    <div class="container">
        <div class="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
            <div class="col-first">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <nav class="d-flex align-items-center">
                    <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                    <a href="single-product.php?id=<?php echo $product['id']; ?>">Product Details</a>
                </nav>
            </div>
        </div>
    </div>
</section>
<!-- End Banner Area -->

<!--================Single Product Area =================-->
<div class="product_image_area">
    <div class="container">
        <div class="row s_product_inner">
            <div class="col-lg-6">
                <div class="s_Product_carousel">
                    <div class="single-prd-item">
                        <img class="img-fluid" src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="single-prd-item">
                        <img class="img-fluid" src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                </div>
            </div>
            <div class="col-lg-5 offset-lg-1">
                <div class="s_product_text">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <?php
                    $price = (float)$product['price'];
                    $discount = (float)$product['discount'];
                    $final_price = $discount > 0 ? $price - ($price * $discount / 100) : $price;
                    ?>
                    <h2>$<?php echo number_format($final_price, 2); ?></h2>
                    <ul class="list">
                        <li><a class="active" href="#"><span>Category</span> : <?php echo $category ? htmlspecialchars($category['name']) : 'Uncategorized'; ?></a></li>
                        <li><a href="#"><span>Availibility</span> : 
                            <?php echo $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ')' : 'Out of Stock'; ?>
                        </a></li>
                    </ul>
                    <p><?php echo htmlspecialchars($product['short_description']); ?></p>
                    <?php if ($product['stock'] > 0): ?>
                    <div class="product_count">
                        <label for="qty">Quantity:</label>
                        <form action="../actions/add_to_cart.php" method="GET" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="qty" min="1" max="<?php echo $product['stock']; ?>" value="1" title="Quantity" class="input-text qty" style="width: 60px;">
                            <button type="submit" class="primary-btn" style="margin-left: 10px;">Add to Cart</button>
                        </form>
                    </div>
                    <?php else: ?>
                        <p class="text-danger">This product is currently out of stock.</p>
                    <?php endif; ?>
                    <div class="card_area d-flex align-items-center">
                        <a class="icon_btn" href="#"><i class="lnr lnr lnr-diamond"></i></a>
                        <a class="icon_btn" href="#"><i class="lnr lnr lnr-heart"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--================End Single Product Area =================-->

<!--================Product Description Area =================-->
<section class="product_description_area">
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab">Description</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="review-tab" data-toggle="tab" href="#review" role="tab">Reviews (<?php echo $review_count; ?>)</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            <div class="tab-pane fade" id="review" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row total_rate">
                            <div class="col-6">
                                <div class="box_total">
                                    <h5>Overall</h5>
                                    <h4><?php echo $avg_rating; ?></h4>
                                    <h6>(<?php echo $review_count; ?> Reviews)</h6>
                                </div>
                            </div>
                        </div>
                        <div class="review_list">
                            <?php if ($review_count > 0): ?>
                                <?php foreach ($reviews as $review): ?>
                                <div class="review_item">
                                    <div class="media">
                                        <div class="d-flex">
                                            <img src="img/product/review-1.png" alt="User">
                                        </div>
                                        <div class="media-body">
                                            <h4><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></h4>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa fa-star <?php echo ($i <= $review['rating']) ? 'checked' : ''; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No reviews yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="review_box">
                            <h4>Add a Review</h4>
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <p><a href="login.php">Login</a> to add your review.</p>
                            <?php else: 
                                $user_id = $_SESSION['user_id'];
                                $already_reviewed = $db->query(
                                    "SELECT 1 FROM reviews WHERE user_id = ? AND product_id = ?",
                                    [$user_id, $product_id]
                                )->fetch();
                            ?>

                                <?php if ($already_reviewed): ?>
                                    <p class="text-success">You have already reviewed this product.</p>
                                <?php else: ?>
                                    <form action="../actions/add_review.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <p>Your Rating:</p>
                                        <div class="star-rating">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required />
                                                <label for="star<?php echo $i; ?>">&#9733;</label>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="form-group mt-2">
                                            <textarea class="form-control" name="comment" rows="4" placeholder="Write your review..." required></textarea>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="primary-btn">Submit Review</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================End Product Description Area =================-->

<!-- Start related-product Area -->
<section class="related-product-area section_gap_bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="section-title">
                    <h1>Related Products</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-9">
                <div class="row">
                    <?php
                    $related_products = $db->getProductsByCategory($product['category_id']);
                    $count = 0;
                    foreach ($related_products as $rel_product):
                        if ($rel_product['id'] == $product['id']) continue; 
                        if ($count >= 6) break;
                        $rel_final_price = $rel_product['discount'] > 0 ? 
                            $rel_product['price'] - ($rel_product['price'] * $rel_product['discount'] / 100) : 
                            $rel_product['price'];
                    ?>
                    <div class="col-lg-4 col-md-4 col-sm-6 mb-20">
                        <div class="single-related-product d-flex">
                            <a href="single-product.php?id=<?php echo $rel_product['id']; ?>">
                                <img src="<?php echo htmlspecialchars($rel_product['image']); ?>" alt="<?php echo htmlspecialchars($rel_product['name']); ?>">
                            </a>
                            <div class="desc">
                                <a href="single-product.php?id=<?php echo $rel_product['id']; ?>" class="title"><?php echo htmlspecialchars($rel_product['name']); ?></a>
                                <div class="price">
                                    <h6>$<?php echo number_format($rel_final_price, 2); ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $count++; endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End related-product Area -->

<!-- start footer Area -->
<footer class="footer-area section_gap">
    <div class="container">
        <div class="row">
            <div class="col-lg-3  col-md-6 col-sm-6">
                <div class="single-footer-widget">
                    <h6>About Us</h6>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                </div>
            </div>
            <div class="col-lg-4  col-md-6 col-sm-6">
                <div class="single-footer-widget">
                    <h6>Newsletter</h6>
                    <p>Stay update with our latest</p>
                    <form class="form-inline">
                        <input class="form-control" name="EMAIL" placeholder="Enter Email" type="email" required>
                        <button class="click-btn btn btn-default"><i class="fa fa-long-arrow-right"></i></button>
                    </form>
                </div>
            </div>
            <div class="col-lg-3  col-md-6 col-sm-6">
                <div class="single-footer-widget mail-chimp">
                    <h6 class="mb-20">Instagram Feed</h6>
                    <ul class="instafeed d-flex flex-wrap">
                        <li><img src="img/i1.jpg" alt=""></li>
                        <li><img src="img/i2.jpg" alt=""></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6">
                <div class="single-footer-widget">
                    <h6>Follow Us</h6>
                    <div class="footer-social d-flex align-items-center">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom d-flex justify-content-center">
            <p class="footer-text m-0">Copyright &copy; <script>document.write(new Date().getFullYear());</script> All rights reserved</p>
        </div>
    </div>
</footer>
<!-- End footer Area -->

<script src="js/vendor/jquery-2.2.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="js/vendor/bootstrap.min.js"></script>
<script src="js/jquery.ajaxchimp.min.js"></script>
<script src="js/jquery.nice-select.min.js"></script>
<script src="js/jquery.sticky.js"></script>
<script src="js/nouislider.min.js"></script>
<script src="js/countdown.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjCGmQ0Uq4exrzdcL6rvxywDDOvfAu6eE"></script>
<script src="js/gmaps.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>