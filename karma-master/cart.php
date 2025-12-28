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
        foreach ($_SESSION['guest_cart'] as $product_id => $quantity) {
            $product = $db->getProductById($product_id);
            if ($product) {
                $price = (float)$product['price'];
                $discount = (float)($product['discount'] ?? 0);
                $final_price = $discount > 0 ? $price - ($price * $discount / 100) : $price;
                $total_price = $final_price * $quantity;
                
                $cartItemsWithDetails[] = array_merge($product, [
                    'quantity' => $quantity, 
                    'total_price' => $total_price,
                    'product_id' => $product['id']
                ]);
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
    <title>FastBuy</title>
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

    <?php include "includes/navbar.php"; ?>

    <!-- Start Banner Area -->
    <section class="banner-area organic-breadcrumb">
        <div class="container">
          
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
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cartItemsWithDetails)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <h5>Your cart is empty.</h5>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cartItemsWithDetails as $item):
                                    $price = (float)$item['price'];
                                    $discount = (float)($item['discount'] ?? 0);
                                    $final_price = $discount > 0 ? $price - ($price * $discount / 100) : $price;
                                    $total_price = (float)$item['total_price'];
                                ?>
                                    <tr>
                                        <td>
                                            <div class="media">
                                                <div class="d-flex">

                                                    <img src="/FastBuy/assets/img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                                                </div>
                                                <div class="media-body">
                                                    <p><?php echo htmlspecialchars($item['name']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>$<?php echo number_format($final_price, 2); ?></h5>
                                        </td>
                                        <td>
                                            <div class="product_count">
                                                <input type="number" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" 
                                                       max="<?php echo $item['stock'] ?? 999; ?>"
                                                       class="input-text qty cart-qty-input" 
                                                       data-product-id="<?php echo $item['product_id']; ?>"
                                                       style="width: 80px;">
                                            </div>
                                        </td>
                                        <td>
                                            <h5>$<?php echo number_format($total_price, 2); ?></h5>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-sm remove-item" 
                                                    data-product-id="<?php echo $item['product_id']; ?>"
                                                    style="border-radius: 3px;">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <h5>Subtotal</h5>
                                    </td>
                                    <td>
                                        <h5>$<?php echo number_format($cartTotal, 2); ?></h5>
                                    </td>
                                </tr>
                                <tr class="out_button_area">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <div class="checkout_btn_inner d-flex align-items-center">
                                            <a class="gray_btn" href="shop.php">Continue Shopping</a>
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
     	<!-- start footer Area -->
    <?php include "includes/footer.php"; ?>
    <!-- End footer Area -->

    <script src="js/vendor/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $('.cart-qty-input').on('change', function() {
            var productId = $(this).data('product-id');
            var quantity = $(this).val();
            
            $.ajax({
                url: '../actions/update_cart_quantity.php',
                type: 'POST',
                data: { product_id: productId, qty: quantity },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Updated!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update quantity',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        
        $('.remove-item').on('click', function() {
            var productId = $(this).data('product-id');
            
            Swal.fire({
                title: 'Remove Item?',
                text: 'Are you sure you want to remove this item from your cart?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../actions/remove_from_cart.php',
                        type: 'POST',
                        data: { product_id: productId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Removed!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to remove item',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>