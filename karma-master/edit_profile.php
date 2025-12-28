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
?>
<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="img/fav.png">
<meta charset="UTF-8">
<title>Edit Profile - FastBuy</title>
<link rel="stylesheet" href="css/linearicons.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/themify-icons.css">
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/main.css">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px; text-align: center;">
        <strong>Error!</strong> 
        <?php 
            if ($_GET['error'] === 'missing_fields') echo 'Please fill in all required fields.';
            elseif ($_GET['error'] === 'wrong_password') echo 'Current password is incorrect.';
            elseif ($_GET['error'] === 'password_mismatch') echo 'New passwords do not match.';
            elseif ($_GET['error'] === 'update_failed') echo 'Failed to update profile. Please try again.';
            else echo 'An error occurred.';
        ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>



<!-- Edit Profile Section -->
<section class="cart_area">
    <div class="container">
        <div class="cart_inner" style="border-left: 4px solid #5C2D92; padding-left: 20px;">
            <form action="../actions/edit_profile.php" method="POST">
                <h4 class="mb-3">Personal Information</h4>
                
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" 
                               value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" 
                               value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Enter phone number">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Country</label>
                        <input type="text" class="form-control" name="country" 
                               value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>" placeholder="Enter country">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>City</label>
                    <input type="text" class="form-control" name="city" 
                           value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" placeholder="Enter city">
                </div>
                
                <h4 class="mb-3 mt-5">Change Password (Optional)</h4>
                
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" class="form-control" name="current_password" 
                           placeholder="Enter current password to change">
                </div>
                
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="new_password" 
                               placeholder="Enter new password">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" name="confirm_password" 
                               placeholder="Confirm new password">
                    </div>
                </div>
                
                <div class="d-flex justify-content-end" style="gap: 15px;">
                    <button type="submit" class="primary-btn" style="border-radius: 0; margin: 0; padding: 8px 20px; display: inline-flex; align-items: center; justify-content: center; height: 38px; font-size: 14px;">Save Changes</button>
                    <a class="gray_btn" href="user_profile.php" style="border-radius: 0; margin: 0; padding: 8px 20px; display: inline-flex; align-items: center; justify-content: center; height: 38px; font-size: 14px;">Cancel</a>
                </div>
            </form>
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