<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$db = Database::getInstance();

$user = $db->getUserById($user_id);
if (!$user) {
    die("User not found!");
}

$orders = $db->getUserOrders($user_id)->fetchAll();
?>
<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="img/fav.png">
<meta charset="UTF-8">
<title>My Profile - FastBuy</title>
<link rel="stylesheet" href="css/linearicons.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/themify-icons.css">
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/main.css">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'profile_updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 20px; text-align: center;">
        <strong>Success!</strong> Your profile has been updated successfully.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Banner -->


<!-- Profile Section -->
<section class="cart_area">
    <div class="container">
        <div class="cart_inner">
            <div class="mb-4" style="border-left: 4px solid #5C2D92; padding-left: 20px;">
                <h4 class="mb-3">Personal Information</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="p-3" style="background: #f8f9fa; border-radius: 4px;">
                            <small class="text-muted d-block mb-1">First Name</small>
                            <strong><?php echo htmlspecialchars($user['first_name']); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3" style="background: #f8f9fa; border-radius: 4px;">
                            <small class="text-muted d-block mb-1">Last Name</small>
                            <strong><?php echo htmlspecialchars($user['last_name']); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="p-3" style="background: #f8f9fa; border-radius: 4px;">
                            <small class="text-muted d-block mb-1">Email</small>
                            <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3" style="background: #f8f9fa; border-radius: 4px;">
                            <small class="text-muted d-block mb-1">Phone</small>
                            <strong><?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3" style="background: #f8f9fa; border-radius: 4px;">
                            <small class="text-muted d-block mb-1">Country</small>
                            <strong><?php echo htmlspecialchars($user['country'] ?? 'Not set'); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3" style="background: #f8f9fa; border-radius: 4px;">
                            <small class="text-muted d-block mb-1">City</small>
                            <strong><?php echo htmlspecialchars($user['city'] ?? 'Not set'); ?></strong>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end align-items-center" style="gap: 15px; margin-top: 20px;">
                    <a class="primary-btn" href="edit_profile.php" style="border-radius: 0; margin: 0; padding: 8px 20px; display: inline-flex; align-items: center; justify-content: center; height: 38px; font-size: 14px;">Edit Profile</a>
                    <a class="gray_btn" href="delete_account.php" style="border-radius: 0; margin: 0; padding: 8px 20px; display: inline-flex; align-items: center; justify-content: center; height: 38px; font-size: 14px;"
                       onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                        Delete Account
                    </a>
                </div>
            </div>

            <div style="border-left: 4px solid #5C2D92; padding-left: 20px;">
                <h4 class="mb-3">Order History</h4>
                <div class="table-responsive">
                    <?php if (empty($orders)): ?>
                        <div class="alert alert-info">You have no orders yet.</div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                        <td><?php echo ucfirst($order['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>

<script src="js/vendor/jquery-2.2.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="js/vendor/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>