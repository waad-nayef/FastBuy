<!doctype html>
<html lang="en">
<?php
require_once "../config/db.php";
$db = Database::getInstance();
?>

<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>FastBuy | Dashboard</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
    <!--end::Primary Meta Tags-->

    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="/FastBuy/admin/css/adminlte.css" as="style" />

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="/FastBuy/admin/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->

    <!-- apexcharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" />

    <!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous" />

    <script src="https://kit.fontawesome.com/8bb0a97d35.js" crossorigin="anonymous"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <?php include "../admin-components/navbar.php";
        include "../admin-components/sidebar.php";
        ?>

        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6" style="width:100%">
                            <div style="display: flex; justify-content:space-between; align-items: center;">
                                <h3 class="mb-0">Orders Management</h3>
                            </div>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <div class="card mb-4">
                        <!-- /.card-header -->
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Customer</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $orders = $db->getAllOrders()->fetchAll();
                                    foreach ($orders as $order) {
                                        $id = $order['id'];
                                        if ($db->getOrderItems($id) == [])
                                            continue;

                                        $firstName = $db->getUserById($order['user_id'])['first_name'];
                                        $lastName = $db->getUserById($order['user_id'])['last_name'];
                                        $totalPrice = $order['total_price'];
                                        $status = $order['status'];
                                        $userId = $order['user_id'];
                                        $date = $order['created_at'];

                                        echo '<tr class="align-middle">';
                                        echo "<td><a href='/FastBuy/admin/actions/order/show-order-items.php?id=$id'  
                                        style=\"
                                        text-decoration: none;
                                        color:black
                                        \">$id</a></td>";
                                        echo "<td>$firstName $lastName</td>";
                                        echo "<td>$ $totalPrice</td>";
                                        echo "<td>$status</td>";
                                        echo "<td>$date</td>";
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
                                                         &nbsp;&nbsp;";
                                        echo "
                                                        <span class='badge text-bg-info'
                                                            style='cursor:pointer'
                                                            onclick=\"window.location.href='/FastBuy/admin/actions/order/show-order-items.php?id=$id'\">
                                                            Info
                                                        </span>
                                                    </td>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
    <?php include "../admin-components/footer.php"; ?>
    </div>
    <!--end::App Wrapper-->
    <?php include "../admin-components/scripts.php"; ?>
    <script>
        function confirmCancel(id, userId) {
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
                        '/FastBuy/admin/actions/order/cancel-order.php?id=' + id +
                        '&userid=' + userId;
                }
            });
        }


        function updateStatus(id) {
            Swal.fire({
                title: 'Update Order Status',
                input: 'select',
                inputOptions: {
                    pending: 'Pending',
                    approved: 'Approved',
                    delivered: 'Delivered'
                },
                inputPlaceholder: 'Select a status',
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You must choose a status';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href =
                        '/FastBuy/admin/actions/order/update-status.php' +
                        '?id=' + id +
                        '&status=' + result.value;

                }
            });
        }
    </script>
</body>
<!--end::Body-->

</html>