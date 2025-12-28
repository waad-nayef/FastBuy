<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
$db = Database::getInstance();
$userPhoto = '/FastBuy/assets/img/default.jpg';
$userName = 'Waad Nayef';

if (isset($_SESSION['user_id'])) {
    $user = $db->getUserById($_SESSION['user_id']);
    if ($user) {
        if (!empty($user['photo'])) {
            $userPhoto = '/FastBuy/assets/img/' . $user['photo'];
        }
        $userName = $user['first_name'] . ' ' . $user['last_name'];
    }
}
?>
<!--begin::Sidebar-->
<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="/FastBuy/admin/index.php" class="brand-link">
            <!--begin::Brand Image-->
            <img src="<?= htmlspecialchars($userPhoto) ?>" alt="Admin Photo" class="brand-image opacity-75 shadow" style="border-radius: 9999px;" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light"><?= htmlspecialchars($userName) ?></span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">

                <li class="nav-item">
                    <a href="/FastBuy/admin/index.php" class="nav-link">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/FastBuy/admin/show-products.php" class="nav-link">
                        <i class="nav-icon fa-solid fa-box-open"></i>
                        <p>Products</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/FastBuy/admin/show-orders.php" class="nav-link">
                        <i class="nav-icon fa-solid fa-cart-shopping"></i>
                        <p>Orders</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/FastBuy/admin/show-users.php" class="nav-link">
                        <i class="nav-icon fa-solid fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/FastBuy/admin/show-categories.php" class="nav-link">
                        <i class="nav-icon fa-solid fa-layer-group"></i>
                        <p>Categories</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/FastBuy/karma-master/edit_profile.php" class="nav-link">
                        <i class="nav-icon fa-solid fa-pen-to-square"></i>
                        <p>Edit Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/FastBuy/actions/logout.php" class="nav-link">
                        <i class="nav-icon fa-solid fa-right-from-bracket"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->