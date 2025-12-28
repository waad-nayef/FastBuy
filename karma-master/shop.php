<?php
session_start();
require_once '../config/db.php';
$db = Database::getInstance();

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$priceRange = $db->query("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products")->fetch();
$minPriceRange = $priceRange['min_price'] ?? 0;
$maxPriceRange = $priceRange['max_price'] ?? 1000;

$categories = $db->getAllCategories()->fetchAll();

$filters = [
    'category_id' => $category_id,
    'min_price' => $min_price,
    'max_price' => $max_price,
    'sort' => $sort,
    'in_stock' => true,
    'search' => $search
];
$stmt = $db->getFilteredProducts($filters);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_carts = [];
if (isset($_SESSION['user_id'])) {
    $user_carts = $db->getUserCarts($_SESSION['user_id']);
}
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
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/nouislider.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .pixel-radio {
            display: block;
            margin-bottom: 10px;
        }
        .single-product {
            margin-bottom: 50px;
            min-height: 380px !important;
            display: flex !important;
            flex-direction: column !important;
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .single-product:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .single-product img {
            margin-bottom: 20px;
            width: 100%;
            height: 200px !important;
            object-fit: contain !important;
            background: #fbfbfb;
        }
        .single-product .product-details {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            text-align: center;
        }
        .single-product .product-details h6 {
            min-height: 40px;
            margin-bottom: 10px;
        }
        .single-product .product-details .prd-bottom {
            margin-top: auto !important;
            padding-top: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
    </style>
</head>

<body id="category">

    <?php include "includes/navbar.php"; ?>

    <!-- Start Banner Area -->
        <div class="container">
            <div class="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
              
            </div>
        </div>
 
    <!-- End Banner Area -->

    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-5">
                <div class="sidebar-categories">
                    <div class="head">Browse Categories</div>
                    <ul class="main-categories">
                        <li class="main-nav-list"><a href="shop.php" style="<?php echo $category_id == 0 ? 'color: #ffba00;' : ''; ?>">All Categories</a></li>
                        <?php foreach ($categories as $cat): ?>
                            <li class="main-nav-list"><a href="shop.php?category=<?php echo $cat['id']; ?>" style="<?php echo $category_id == $cat['id'] ? 'color: #ffba00;' : ''; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="col-xl-9 col-lg-8 col-md-7">
                <!-- Start Best Seller -->
                <section class="lattest-product-area pb-40 category-list">
                    <!-- Filter Bar -->
                    <div class="filter-bar d-flex flex-wrap align-items-center mb-4">
                        <div class="sorting">
                            <form method="GET" action="shop.php" style="display: inline;">
                                <?php if ($category_id > 0): ?>
                                    <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                                <?php endif; ?>
                                <?php if ($min_price > 0): ?>
                                    <input type="hidden" name="min_price" value="<?php echo $min_price; ?>">
                                <?php endif; ?>
                                <?php if ($max_price > 0): ?>
                                    <input type="hidden" name="max_price" value="<?php echo $max_price; ?>">
                                <?php endif; ?>
                                <select name="sort" onchange="this.form.submit()">
                                    <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
                                    <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                                    <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                                </select>
                            </form>
                        </div>
                        <div class="sorting mr-auto">
                            <span>Showing <?php echo count($products); ?> product(s)</span>
                        </div>
                    </div>

                    <div class="row">
                        <?php if (empty($products)): ?>
                            <div class="col-12 text-center">
                                <h3>No products found.</h3>
                                <p>Try adjusting your category or filters.</p>
                                <a href="shop.php" class="btn btn-primary mt-3">Reset Filters</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <!-- single product -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="single-product">
                                        <img class="img-fluid" src="/FastBuy/assets/img/<?php echo htmlspecialchars($product['image'] ?? 'default.jpg'); ?>" alt="">
                                        <div class="product-details">
                                            <h6><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <div class="price">
                                                <h6>$<?php echo number_format($product['price'], 2); ?></h6>
                                                <?php if ($product['discount'] > 0): ?>
                                                    <h6 class="l-through">$<?php echo number_format($product['price'] / (1 - $product['discount'] / 100), 2); ?></h6>
                                                <?php endif; ?>
                                            </div>
                                            <div class="prd-bottom">

                                                <a href="../actions/add_to_cart.php?product_id=<?php echo $product['id']; ?>" class="social-info">
                                                    <span class="ti-bag"></span>
                                                    <p class="hover-text">add to bag</p>
                                                </a>
                                                <a href="single-product.php?id=<?php echo $product['id']; ?>" class="social-info">
                                                    <span class="lnr lnr-move"></span>
                                                    <p class="hover-text">view more</p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
                <!-- End Best Seller -->
            </div>
        </div>
    </div>

    <!-- start footer Area -->
    <?php include "includes/footer.php"; ?>
    <!-- End footer Area -->

    <!-- Bag Modal removed -->

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
    <script src="js/gmaps.min.js"></script>
    <script src="js/main.js"></script>


    <script>
    $(document).ready(function() {
        var minPriceRange = <?php echo $minPriceRange; ?>;
        var maxPriceRange = <?php echo $maxPriceRange; ?>;
        var currentMin = <?php echo $min_price > 0 ? $min_price : $minPriceRange; ?>;
        var currentMax = <?php echo $max_price > 0 ? $max_price : $maxPriceRange; ?>;

        if(document.getElementById("price-range")) {
            var nonLinearSlider = document.getElementById('price-range');
            noUiSlider.create(nonLinearSlider, {
                connect: true,
                behaviour: 'tap',
                start: [ currentMin, currentMax ],
                range: {
                    'min': [ minPriceRange ],
                    'max': [ maxPriceRange ]
                }
            });
            var nodes = [
                document.getElementById('lower-value'),
                document.getElementById('upper-value')
            ];
            var inputs = [
                document.getElementById('min_price_input'),
                document.getElementById('max_price_input')
            ];

            nonLinearSlider.noUiSlider.on('update', function ( values, handle, unencoded, isTap, positions ) {
                nodes[handle].innerHTML = parseInt(values[handle]);
                inputs[handle].value = parseInt(values[handle]);
            });
        }
    });
    </script>
</body>

</html>