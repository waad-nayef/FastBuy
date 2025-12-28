<?php
$user = null;
$user_photo = null;
if (isset($_SESSION['user_id'])) {
    if (file_exists('../config/db.php')) {
        require_once '../config/db.php';
    } else {
        require_once '../../config/db.php';
    }
    $db = Database::getInstance();
    $user = $db->getUserById($_SESSION['user_id']);
    if ($user && !empty($user['photo'])) {
        $user_photo = $user['photo'];
    }
}
$is_logged_in = isset($_SESSION['user_id']);
?>
<!-- Start Header Area -->
<header class="header_area sticky-header" style="position: fixed; top: 0; width: 100%; z-index: 9999; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <div class="main_menu">
        <nav class="navbar navbar-expand-lg navbar-light main_box">
            <div class="container">
                <a class="navbar-brand logo_h" href="index.php"><img src="img/logo.png" alt=""></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
                    <ul class="nav navbar-nav menu_nav ml-auto" style="display: flex; align-items: center; flex-wrap: nowrap;">
                        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'active' : ''; ?>">
                            <a class="nav-link" href="shop.php">Shop</a>
                        </li>
                        <?php if ($is_logged_in): ?>
                            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                                <a class="nav-link" href="orders.php">Order Status</a>
                            </li>
                            <li class="nav-item">
                                <button class="search nav-link" id="search" style="background: none; border: none; cursor: pointer;"><span class="lnr lnr-magnifier"></span></button>
                            </li>
                            <li class="nav-item"><a href="cart.php" class="nav-link"><span class="ti-bag"></span></a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php if ($user_photo): ?>
                                        <?php 
                                        $photo_path = '';
                                        if (file_exists('uploads/' . $user_photo)) {
                                            $photo_path = 'uploads/' . $user_photo;
                                        } elseif (file_exists('../uploads/' . $user_photo)) {
                                            $photo_path = '../uploads/' . $user_photo;
                                        } elseif (file_exists('../../uploads/' . $user_photo)) {
                                            $photo_path = '../../uploads/' . $user_photo;
                                        } else {
                                            $photo_path = 'uploads/' . $user_photo; 
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($photo_path); ?>" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; margin-right: 5px;">
                                    <?php else: ?>
                                        <span class="lnr lnr-user" style="font-size: 20px;"></span>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')); ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="user_profile.php">My Profile</a>
                                    <a class="dropdown-item" href="../actions/logout.php">Logout</a>
                                </div>
                            </li>
                        <?php else: ?>
                            <?php if (basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'signup.php'): ?>
                                <li class="nav-item">
                                    <button class="search nav-link" id="search" style="background: none; border: none; cursor: pointer;"><span class="lnr lnr-magnifier"></span></button>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item"><a href="login.php" class="nav-link">Login</a></li>
                            <li class="nav-item"><a href="signup.php" class="nav-link">Signup</a></li>
                        <?php endif; ?>
                    </ul>
            </div>
        </nav>
    </div>
    <div class="search_input" id="search_input_box" style="display: none;">
        <div class="container">
            <form class="d-flex align-items-center w-100" method="GET" action="shop.php" style="flex-wrap: nowrap;">
                <input type="text" class="form-control" name="search" id="search_input" placeholder="Search Products..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="flex: 1; background: transparent; border: 0; color: #fff; height: 40px; border-bottom: 1px solid rgba(255,255,255,0.2) !important;">
                <button type="submit" class="btn" style="cursor: pointer; background: none; border: none; padding: 0 15px; width: 50px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span class="lnr lnr-magnifier" style="color: #fff; font-size: 18px;"></span>
                </button>
                <span class="lnr lnr-cross" id="close_search" title="Close Search" style="cursor: pointer; color: #fff; font-size: 18px; padding: 0 5px; height: 40px; display: flex; align-items: center; flex-shrink: 0;"></span>
            </form>
        </div>
    </div>
</header>
<!-- End Header Area -->

