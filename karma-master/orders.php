<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'orders.php';
    header("Location: login.php?error=login_required");
    exit();
}

require_once '../config/db.php';
$db = Database::getInstance();
$user_id = $_SESSION['user_id'];

$orders = $db->getUserOrders($user_id)->fetchAll();
?>

<!DOCTYPE html>
<html lang="zxx" class="no-js">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="img/fav.png">
    <meta charset="UTF-8">
    <title>FastBuy - Order Status</title>
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

    <?php include "includes/navbar.php"; ?>

    <!-- Start Banner Area -->
    <section class="banner-area organic-breadcrumb" style="background: url(img/banner/banner-bg.png) center no-repeat; background-size: cover;">
        <div class="container">
            <div class="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                <div class="col-first">
                    <h1>Order Status</h1>
                    <nav class="d-flex align-items-center">
                        <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                        <a>Order Status</a>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <!-- End Banner Area -->

    <!--================Order Details Area =================-->
    <section class="checkout_area section_gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="mb-4">Your Orders</h3>
                    <?php if (empty($orders)): ?>
                        <div class="alert alert-info">
                            <h4>No orders found</h4>
                            <p>You haven't placed any orders yet.</p>
                            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order): 
                            $orderItems = $db->getOrderWithItems($order['id']);
                            $payment = $db->query("SELECT * FROM payments WHERE order_id = ?", [$order['id']])->fetch();
                        ?>
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
                                        <small class="text-muted">Date: <?php echo date('M d, Y', strtotime($order['created_at'])); ?></small>
                                    </div>
                                    <div>
                                        <span class="badge badge-<?php 
                                            echo $order['status'] == 'delivered' ? 'success' : 
                                                ($order['status'] == 'approved' ? 'primary' : 
                                                ($order['status'] == 'cancelled' ? 'danger' : 'warning')); 
                                        ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6>Products:</h6>
                                            <ul class="list-unstyled">
                                                <?php foreach ($orderItems as $item): ?>
                                                    <li class="mb-2 d-flex justify-content-between">
                                                        <div>
                                                            <img src="/FastBuy/assets/img/<?php echo htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" 
                                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                                 style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; display: inline-block;">
                                                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                            <span class="text-muted">x <?php echo $item['quantity']; ?></span>
                                                        </div>
                                                        <span>$<?php echo number_format($item['total_price'], 2); ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="order_summary">
                                                <h6>Order Summary</h6>
                                                <ul class="list-unstyled">
                                                    <li class="d-flex justify-content-between mb-2">
                                                        <span>Subtotal:</span>
                                                        <span>$<?php echo number_format($order['total_price'], 2); ?></span>
                                                    </li>
                                                    <li class="d-flex justify-content-between mb-2">
                                                        <span>Shipping:</span>
                                                        <span>Free</span>
                                                    </li>
                                                    <li class="d-flex justify-content-between mb-2">
                                                        <strong>Total:</strong>
                                                        <strong>$<?php echo number_format($order['total_price'], 2); ?></strong>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- End Order Details Area -->

    <!-- start footer Area -->
    <?php include "includes/footer.php"; ?>
    <!-- End footer Area -->


    <script src="js/vendor/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
