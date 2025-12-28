<?php
session_start();
?>
<!DOCTYPE html>
<html lang="zxx" class="no-js">

<head>
    <!-- Mobile Specific Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/fav.png">
    <!-- Author Meta -->
    <meta name="author" content="CodePixar">
    <!-- Meta Description -->
    <meta name="description" content="">
    <!-- Meta Keyword -->
    <meta name="keywords" content="">
    <!-- meta character set -->
    <meta charset="UTF-8">
    <!-- Site Title -->
    <title>FastBuy</title>

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
</head>

<body>

    <?php include "includes/navbar.php"; ?>

    <!-- start banner Area -->
    <section class="banner-area">
        <div class="container">
            <div class="row fullscreen align-items-center justify-content-start">
                <div class="col-lg-12">
                    <div class="static-banner-slider">
                        <!-- single-slide -->
                        <div class="row single-slide align-items-center d-flex">
                            <div class="col-lg-5 col-md-6">
                                <div class="banner-content">
                                    <h1>Welcome to <br>FastBuy!</h1>
                                    <p>Discover the best products at unbeatable prices. Shop now and enjoy our exclusive collection.</p>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="banner-img" style="position: relative; z-index: 10;">
                                    <img class="img-fluid" src="img/banner/banner-img.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End banner Area -->

    <!-- start features Area -->
    <section class="features-area section_gap">
        <div class="container">
            <div class="row features-inner">
                <!-- single features -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="single-features">
                        <div class="f-icon">
                            <img src="img/features/f-icon1.png" alt="">
                        </div>
                        <h6>Free Delivery</h6>
                        <p>Free Shipping on all order</p>
                    </div>
                </div>
                <!-- single features -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="single-features">
                        <div class="f-icon">
                            <img src="img/features/f-icon2.png" alt="">
                        </div>
                        <h6>Return Policy</h6>
                        <p>Free Shipping on all order</p>
                    </div>
                </div>
                <!-- single features -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="single-features">
                        <div class="f-icon">
                            <img src="img/features/f-icon3.png" alt="">
                        </div>
                        <h6>24/7 Support</h6>
                        <p>Free Shipping on all order</p>
                    </div>
                </div>
                <!-- single features -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="single-features">
                        <div class="f-icon">
                            <img src="img/features/f-icon4.png" alt="">
                        </div>
                        <h6>Secure Payment</h6>
                        <p>Free Shipping on all order</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end features Area -->

    <!-- Product Areas removed as per request -->


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
    
    <!-- Price Range Slider -->
    <script>
        $(document).ready(function() {
            var minPrice = <?php echo $min_price > 0 ? $min_price : $minPriceRange; ?>;
            var maxPrice = <?php echo $max_price > 0 ? $max_price : $maxPriceRange; ?>;
            var minRange = <?php echo $minPriceRange; ?>;
            var maxRange = <?php echo $maxPriceRange; ?>;
            
            if (document.getElementById("price-range")) {
                var nonLinearSlider = document.getElementById('price-range');
                
                if (nonLinearSlider.noUiSlider) {
                    nonLinearSlider.noUiSlider.destroy();
                }
                
                noUiSlider.create(nonLinearSlider, {
                    connect: true,
                    behaviour: 'tap',
                    start: [minPrice, maxPrice],
                    range: {
                        'min': [minRange],
                        'max': [maxRange]
                    },
                    step: 1
                });
                
                var nodes = [
                    document.getElementById('lower-value'),
                    document.getElementById('upper-value')
                ];
                
                var minPriceInput = document.getElementById('min_price');
                var maxPriceInput = document.getElementById('max_price');
                
                nonLinearSlider.noUiSlider.on('update', function (values, handle) {
                    var value = Math.round(values[handle]);
                    nodes[handle].innerHTML = '$' + value;
                    if (handle === 0) {
                        minPriceInput.value = value;
                    } else {
                        maxPriceInput.value = value;
                    }
                });
            }
        });
    </script>

    <!-- start footer Area -->
    <?php include "includes/footer.php"; ?>
    <!-- End footer Area -->

</body>

</html>