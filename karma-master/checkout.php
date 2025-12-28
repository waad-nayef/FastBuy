<?php
session_start();
require_once '../config/db.php';
$db = Database::getInstance();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=login_required");
    exit();
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';

$user_id = $_SESSION['user_id'];
$user = $db->getUserById($user_id);
if (!$user) {
    header("Location: login.php");
    exit();
}
$cart = $db->getCartByUserId($user_id);
if (!$cart) {
    header("Location: cart.php?error=empty_cart");
    exit();
}
$cart_items = $db->getCartItemsWithDetails($cart['id']);
if (empty($cart_items)) {
    header("Location: cart.php?error=empty_cart");
    exit();
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="zxx" class="no-js">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="img/fav.png">
    <meta charset="UTF-8">
    <title>FastBuy - Checkout</title>
    <link rel="stylesheet" href="css/linearicons.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .checkout-minimal {
            padding: 50px 0;
            background: #f9fafc;
        }

        .order-card {
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .item-list {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <?php include "includes/navbar.php"; ?>

    <section class="checkout-minimal">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="order-card">
                        <h3 class="text-center mb-4">Checkout</h3>

                        <div class="item-list">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="item-row">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($item['image'] ?? 'img/product/p1.jpg'); ?>" style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px;">
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                        </div>
                                    </div>
                                    <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total</h5>
                            <h5>$<?php echo number_format($subtotal, 2); ?></h5>
                        </div>

                        <form id="checkoutForm" action="../actions/process_checkout.php" method="POST">
                            <input type="hidden" name="cart_id" value="<?php echo $cart['id']; ?>">

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Phone Number</label>
                                    <input type="text" class="form-control" name="phone" required placeholder="Phone Number" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city" required placeholder="City" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Country</label>
                                    <input type="text" class="form-control" name="country" required placeholder="Country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Payment Provider</label>
                                    <select class="form-control" name="provider" required>
                                        <option value="Stripe">Stripe</option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="Cash">Cash on Delivery</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Payment Method</label>
                                    <select class="form-control" name="payment_method" required>
                                        <option value="Visa">Visa</option>
                                        <option value="Mastercard">Mastercard</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>
                            </div>


                            <button type="submit" class="btn primary-btn w-100" style="border-radius: 0;">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script src="js/vendor/jquery-2.2.4.min.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $('#checkoutForm').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: '../actions/process_checkout.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Order Placed!',
                            text: 'Your order has been placed successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Something went wrong.',
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to process request.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
</body>

</html>