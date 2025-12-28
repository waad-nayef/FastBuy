<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=login_required");
    exit();
}

require_once '../config/db.php';
$db = Database::getInstance();
$user_id = $_SESSION['user_id'];

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 0);

if ($order_id <= 0) {
    header("Location: checkout.php");
    exit();
}

$order = $db->getOrderById($order_id);

if (!$order || $order['user_id'] != $user_id) {
    header("Location: checkout.php?error=invalid_order");
    exit();
}

$orderItems = $db->getOrderWithItems($order_id);
$payment = $db->query("SELECT * FROM payments WHERE order_id = ?", [$order_id])->fetch();
$user = $db->getUserById($user_id);
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
	<title>Order Status - FastBuy</title>

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
	<section class="banner-area organic-breadcrumb">
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
	<section class="order_details section_gap">
		<div class="container">
			<?php 
			$statusMessages = [
				'pending' => 'Your order is pending approval.',
				'approved' => 'Your order has been approved and is being processed.',
				'delivered' => 'Your order has been delivered successfully!',
				'cancelled' => 'Your order has been cancelled.'
			];
			$statusIcons = [
				'pending' => 'lnr-hourglass',
				'approved' => 'lnr-checkmark-circle',
				'delivered' => 'lnr-checkmark-circle',
				'cancelled' => 'lnr-cross-circle'
			];
			?>
			<h3 class="title_confirmation">
				<span class="lnr <?php echo $statusIcons[$order['status']] ?? 'lnr-hourglass'; ?>" style="font-size: 48px; color: <?php 
					echo $order['status'] == 'delivered' ? '#28a745' : 
						($order['status'] == 'approved' ? '#007bff' : 
						($order['status'] == 'cancelled' ? '#dc3545' : '#ffc107')); 
				?>;"></span>
				<br>
				<?php echo $order['status'] == 'delivered' ? 'Thank you! Your order has been delivered.' : 
					($order['status'] == 'approved' ? 'Thank you! Your order has been approved.' : 
					($order['status'] == 'cancelled' ? 'Order Cancelled' : 'Thank you! Your order has been received.')); ?>
			</h3>
			<p class="text-center mb-4"><?php echo $statusMessages[$order['status']] ?? 'Your order is being processed.'; ?></p>
			
			<div class="row order_d_inner">
				<div class="col-lg-4">
					<div class="details_item">
						<h4>Order Info</h4>
						<ul class="list">
							<li><a href="#"><span>Order number</span> : #<?php echo $order['id']; ?></a></li>
							<li><a href="#"><span>Date</span> : <?php echo date('M d, Y', strtotime($order['created_at'])); ?></a></li>
							<li><a href="#"><span>Total</span> : $<?php echo number_format($order['total_price'], 2); ?></a></li>
							<li><a href="#"><span>Status</span> : 
								<span class="badge badge-<?php 
									echo $order['status'] == 'delivered' ? 'success' : 
										($order['status'] == 'approved' ? 'primary' : 
										($order['status'] == 'cancelled' ? 'danger' : 'warning')); 
								?>">
									<?php echo ucfirst($order['status']); ?>
								</span>
							</a></li>
							<?php if ($payment): ?>
								<li><a href="#"><span>Payment method</span> : <?php echo htmlspecialchars($payment['payment_method']); ?></a></li>
								<li><a href="#"><span>Payment status</span> : 
									<span class="badge badge-<?php echo $payment['status'] == 'paid' ? 'success' : 'warning'; ?>">
										<?php echo ucfirst($payment['status']); ?>
									</span>
								</a></li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="details_item">
						<h4>Customer Info</h4>
						<ul class="list">
							<li><a href="#"><span>Name</span> : <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></a></li>
							<li><a href="#"><span>Email</span> : <?php echo htmlspecialchars($user['email']); ?></a></li>
							<?php if ($user['phone']): ?>
								<li><a href="#"><span>Phone</span> : <?php echo htmlspecialchars($user['phone']); ?></a></li>
							<?php endif; ?>
							<?php if ($user['city']): ?>
								<li><a href="#"><span>City</span> : <?php echo htmlspecialchars($user['city']); ?></a></li>
							<?php endif; ?>
							<?php if ($user['country']): ?>
								<li><a href="#"><span>Country</span> : <?php echo htmlspecialchars($user['country']); ?></a></li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="details_item">
						<h4>Order Summary</h4>
						<ul class="list">
							<li><a href="#"><span>Items</span> : <?php echo count($orderItems); ?></a></li>
							<li><a href="#"><span>Subtotal</span> : $<?php echo number_format($order['total_price'], 2); ?></a></li>
							<li><a href="#"><span>Shipping</span> : Free</a></li>
							<li><a href="#"><span>Total</span> : $<?php echo number_format($order['total_price'], 2); ?></a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="order_details_table">
				<h2>Order Details</h2>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th scope="col">Product</th>
								<th scope="col">Quantity</th>
								<th scope="col">Total</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($orderItems as $item): ?>
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<img src="<?php echo htmlspecialchars($item['image'] ?? 'img/product/p1.jpg'); ?>" 
												 alt="<?php echo htmlspecialchars($item['name']); ?>" 
												 style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
											<p class="mb-0"><?php echo htmlspecialchars($item['name']); ?></p>
										</div>
									</td>
									<td>
										<h5>x <?php echo $item['quantity']; ?></h5>
									</td>
									<td>
										<p>$<?php echo number_format($item['total_price'], 2); ?></p>
									</td>
								</tr>
							<?php endforeach; ?>
							<tr>
								<td>
									<h4>Subtotal</h4>
								</td>
								<td>
									<h5></h5>
								</td>
								<td>
									<p>$<?php echo number_format($order['total_price'], 2); ?></p>
								</td>
							</tr>
							<tr>
								<td>
									<h4>Shipping</h4>
								</td>
								<td>
									<h5></h5>
								</td>
								<td>
									<p>Free</p>
								</td>
							</tr>
							<tr>
								<td>
									<h4>Total</h4>
								</td>
								<td>
									<h5></h5>
								</td>
								<td>
									<p>$<?php echo number_format($order['total_price'], 2); ?></p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="text-center mt-4">
				<a href="orders.php" class="btn btn-primary">View All Orders</a>
				<a href="index.php" class="btn btn-secondary">Continue Shopping</a>
			</div>
		</div>
	</section>
	<!--================End Order Details Area =================-->

	<!-- start footer Area -->
    <?php include "includes/footer.php"; ?>
    <!-- End footer Area -->




	<script src="js/vendor/jquery-2.2.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
	 crossorigin="anonymous"></script>
	<script src="js/vendor/bootstrap.min.js"></script>
	<script src="js/jquery.ajaxchimp.min.js"></script>
	<script src="js/jquery.nice-select.min.js"></script>
	<script src="js/jquery.sticky.js"></script>
	<script src="js/nouislider.min.js"></script>
	<script src="js/jquery.magnific-popup.min.js"></script>
	<script src="js/owl.carousel.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjCGmQ0Uq4exrzdcL6rvxywDDOvfAu6eE"></script>
	<script src="js/gmaps.min.js"></script>
	<script src="js/main.js"></script>
</body>

</html>
