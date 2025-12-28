<!doctype html>
<html lang="en">
<?php
require_once "../../../config/db.php";
$db = Database::getInstance();
$user = $db->getUserById($_GET['id']);
$userOrders = $db->getUserOrders($_GET['id'])->fetchAll();
?>

<head>
    <meta charset="utf-8" />
    <title>FastBuy | <?php echo "username" ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />

    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="/FastBuy/admin/css/adminlte.css" />

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/8bb0a97d35.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>


<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php
        include "../../../admin-components/navbar.php";
        include "../../../admin-components/sidebar.php";
        ?>
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">User Details</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/FastBuy/admin/index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="/FastBuy/admin/show-users.php">Users</a></li>
                                <li class="breadcrumb-item active" aria-current="page">User Details</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4">
                        <!-- User Card Column -->
                        <div class="col-md-4 col-lg-3">
                            <div class="card" style="height: fit-content;">
                                <?php $imagePath = $user['photo'] ?? "default.jpg"; ?>
                                <img class="card-img-top" src="/FastBuy/assets/img/<?= $imagePath ?>" alt="user image">
                                <div class="card-body">
                                    <h5><?= "#" . $user['id'] . " " . $user['first_name'] . " " . $user['last_name'] ?></h5>
                                    <h6>Email: <?= $user['email'] ?></h6>
                                    <h6>Orders: <?= count($userOrders) ?> </h6>
                                    <h6>Status: <?= $user['role'] ?> </h6>
                                    <h6>Registered at: <?= substr($user['created_at'], 0, -8) ?> </h6>
                                    <button type="button" class="btn btn-outline-danger mb-2" onclick="confirmDelete(<?= $user['id'] ?>, '<?= $user['first_name'] . ' ' . $user['last_name'] ?>')">Delete <?= $user['first_name'] ?></button>
                                    <?php
                                    if ($user['role'] === 'admin') {
                                        $btnText = "Unset admin";
                                        $btnClass = "btn-outline-primary";
                                        $newRole = "user";
                                    } else {
                                        $btnText = "Set as Admin";
                                        $btnClass = "btn-outline-secondary";
                                        $newRole = "admin";
                                    }
                                    ?>

                                    <button type="button" class="btn <?= $btnClass ?> mb-2"
                                        onclick="updateUserRole(<?= $user['id'] ?>, '<?= $newRole ?>', '<?= $user['first_name'] ?>')">
                                        <?= $btnText ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Table Column -->
                        <div class="col-md-8 col-lg-9">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?= $user['first_name'] . "'s" ?> Orders</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>Total Price</th>
                                                <th>Status</th>
                                                <th>created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($userOrders as $order) {
                                                $id = $order['id'];
                                                $totalPrice = $order['total_price'];
                                                $status = $order['status'];
                                                $created_at = $order['created_at'];
                                                $userId = $order['user_id'];
                                                echo '<tr class="align-middle">';
                                                echo "<td><a href='../order/show-order-items.php?id=$id'  
                                                        style=\"
                                                        text-decoration: none;
                                                        color:black
                                                        \">$id</a></td>";
                                                echo "<td>$totalPrice</td>";
                                                echo "<td>$status</td>";
                                                echo "<td>$created_at</td>";

                                                echo "<td>";

                                                if ($status != 'cancelled' && $status != 'delivered') {
                                                    echo "
                                                    <span class='badge text-bg-danger'
                                                        onclick='confirmCancel($id,$userId)'
                                                        style='cursor:pointer'>Cancel</span>
                                                    &nbsp;&nbsp;";
                                                } else {
                                                    echo "
                                                    <span class='badge text-bg-secondary'>Cancel</span>
                                                    &nbsp;&nbsp;";
                                                }

                                                echo "
                                                <span class='badge text-bg-warning'
                                                    onclick='updateStatus($id,$userId)'
                                                    style='cursor:pointer'>Update Status</span>
                                                    &nbsp;&nbsp;
                                                ";

                                                echo "
                                                <span class='badge text-bg-info'
                                                    style='cursor:pointer'
                                                    onclick=\"window.location.href='../order/show-order-items.php?id=$id'\">
                                                    Info
                                                </span>
                                            </td>";
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
    </div>

    <!-- JS Plugins -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
    <script src="/FastBuy/admin/js/adminlte.js"></script>


    <!-- OverlayScrollbars initialization -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('cancelled') === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Order deleted successfully',
                    confirmButtonColor: '#28a745',
                    timer: 3000
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }

            window.confirmCancel = function(id, userId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This order will be cancelled",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, cancel order',
                    cancelButtonText: 'Close'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href =
                            '../order/cancel-order.php?id=' + id +
                            '&place=userDetails' +
                            '&userid=' + userId;
                    }
                });
            };

            window.confirmDelete = function(id, userName) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: userName + " will be permanently deleted",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'delete-user.php?id=' + id;
                    }
                });
            };
        });

        window.updateUserRole = function(id, newRole, name) {
            let actionText = name + ' will lose admin privileges';
            if (newRole == 'admin') {
                actionText = "set " + name + " as an admin";
            }
            Swal.fire({
                title: 'Are you sure?',
                text: actionText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Update',
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href =
                        'update-user.php?id=' + id +
                        '&role=' + newRole;
                }
            });
        };
    </script>