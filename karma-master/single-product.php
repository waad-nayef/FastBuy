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
$avg_rating = $rating_info['avg_rating'] ? number_format($rating_info['avg_rating'], 1) : "0.0";
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        /* Button Consistency Fix */
        .primary-btn {
            background: linear-gradient(90deg, #732E8E 0%, #9F50B8 100%) !important;
            border: none !important;
            border-radius: 50px !important;
            padding: 0 30px !important;
            line-height: 50px !important;
            height: 50px !important;
            color: #fff !important;
            text-transform: uppercase !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            display: inline-block !important;
            transition: all 0.3s ease !important;
            vertical-align: middle !important;
        }
        .primary-btn:hover {
            opacity: 0.9 !important;
            color: #fff !important;
        }
        .icon_btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            background: linear-gradient(90deg, #732E8E 0%, #9F50B8 100%) !important;
            color: #fff !important;
            margin-right: 10px !important;
            transition: all 0.3s ease !important;
        }
        .icon_btn:hover {
            opacity: 0.9 !important;
            color: #fff !important;
        }
        /* Fix overlapping issues */
        .product_count {
            margin-bottom: 20px !important;
            display: block !important;
            clear: both !important;
        }
        .product_count label {
            display: block !important;
            margin-bottom: 10px !important;
        }
        .product_count form {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }
        .product_count .input-text.qty {
            width: 80px !important;
            height: 50px !important;
            padding: 0 15px !important;
            border: 1px solid #ddd !important;
            border-radius: 5px !important;
            font-size: 16px !important;
        }
        .card_area {
            margin-top: 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }
        /* Separating lines between reviews */
        .review_item {
            border-bottom: 1px solid #eee !important;
            padding-bottom: 20px !important;
            margin-bottom: 20px !important;
        }
        .review_item:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
        }
        .review_item .media-body .fa-star {
            font-size: 13px !important;
            margin-right: 2px !important;
            color: #cccccc !important;
        }
        .review_item .media-body .fa-star.checked {
            color: #ffba00 !important;
        }
        .total_rate .box_total {
            background: #732E8E !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 35px 0 !important;
            text-align: center !important;
        }
        .total_rate .box_total h4 {
            color: #fff !important;
            font-size: 48px !important;
            margin-bottom: 5px !important;
        }
        .total_rate .box_total h5, .total_rate .box_total h6 {
            color: rgba(255,255,255,0.8) !important;
            margin-bottom: 0 !important;
        }
    </style>
</head>
<body>

<?php include "includes/navbar.php"; ?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'review_added'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 20px; text-align: center;">
        <strong>Success!</strong> Your review has been submitted successfully.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px; text-align: center;">
        <strong>Error!</strong> 
        <?php 
            if ($_GET['error'] === 'already_reviewed') echo 'You have already reviewed this product.';
            elseif ($_GET['error'] === 'invalid_input') echo 'Please provide valid rating and comment.';
            else echo 'Failed to submit review. Please try again.';
        ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Start Banner Area -->
    <div class="container">
       
    </div>
<!-- End Banner Area -->

<!--================Single Product Area =================-->
<div class="product_image_area">
    <div class="container">
        <div class="row s_product_inner">
            <div class="col-lg-6">
                <div class="s_Product_carousel">
                    <div class="single-prd-item">
                        <img class="img-fluid" src="/FastBuy/assets/img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="single-prd-item">
                        <img class="img-fluid" src="/FastBuy/assets/img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
                        <form id="addToCartForm" action="../actions/add_to_cart.php" method="GET">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="qty" id="qty" min="1" max="<?php echo $product['stock']; ?>" value="1" title="Quantity" class="input-text qty">
                            <button type="button" id="addToCartBtn" class="primary-btn">Add to Cart</button>
                        </form>
                    </div>
                    <?php else: ?>
                        <p class="text-danger">This product is currently out of stock.</p>
                    <?php endif; ?>
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
                <?php 
                $already_reviewed = false;
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $already_reviewed = $db->query(
                        "SELECT 1 FROM reviews WHERE user_id = ? AND product_id = ?",
                        [$user_id, $product_id]
                    )->fetch();
                }
                ?>

                <?php if (!$already_reviewed): ?>
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-12">
                        <div class="review_box">
                            <h4>Add a Review</h4>
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <p><a href="login.php">Login</a> to add your review.</p>
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
                        </div>
                    </div>
                </div>
                <hr class="mb-5">
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="row total_rate justify-content-center mb-5">
                            <div class="col-lg-4 col-md-6 col-8">
                                <div class="box_total">
                                    <h5>Overall</h5>
                                    <h4><?php echo $avg_rating; ?> / 5</h4>
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
                                            <?php 
                                            $user_photo = !empty($review['photo']) ? $review['photo'] : 'default.jpg';
                                            ?>
                                            <img src="/FastBuy/assets/img/<?php echo htmlspecialchars($user_photo); ?>" alt="User" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;">
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
                </div>
            </div>
        </div>
    </div>
</section>
<!--================End Product Description Area =================-->

<!-- start footer Area -->
    <?php include "includes/footer.php"; ?>
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
<script>
$(document).ready(function() {
    $('#addToCartBtn').click(function(e) {
        e.preventDefault();
        
        var productId = $('input[name="product_id"]').val();
        var qty = $('#qty').val();
        
        if (qty < 1 || qty > <?php echo $product['stock']; ?>) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Quantity',
                text: 'Please enter a valid quantity between 1 and <?php echo $product['stock']; ?>',
                confirmButtonColor: '#732E8E'
            });
            return;
        }
        
        $.ajax({
            url: '../actions/add_to_cart.php',
            type: 'GET',
            data: {
                product_id: productId,
                qty: qty
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: 'Product has been added to your cart successfully.',
                    confirmButtonColor: '#732E8E',
                    confirmButtonText: 'Continue Shopping'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to add product to cart. Please try again.',
                    confirmButtonColor: '#732E8E'
                });
            }
        });
    });
});
</script>
</body>
</html>
