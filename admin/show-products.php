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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />

    <link rel="stylesheet" href="/FastBuy/admin/css/adminlte.css" />

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
        <?php
        include "../admin-components/navbar.php";
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
                                <h3 class="mb-0">Products Management</h3>
                                <button type="button" class="btn btn-success" onclick="window.location.href='/FastBuy/admin/actions/product/add-product.php'"><i class="bi bi-plus-lg"></i> Add Product</button>
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
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                                        $products = $db->searchProducts($_GET['search'])->fetchAll();
                                    } else {
                                        $products = $db->getAllProducts()->fetchAll();
                                    }

                                    foreach ($products as $product) {
                                        $id = $product['id'];
                                        $name = $product['name'];
                                        $category = $db->getCategoryById($product['category_id'])['name'];
                                        $price = $product['price'];
                                        $stock = $product['stock'];

                                        echo '<tr class="align-middle">';
                                        echo "<td>$id</td>";
                                        echo "<td>$name</td>";
                                        echo "<td>$category</td>";
                                        echo "<td>$ $price</td>";
                                        echo "<td>$stock</td>";
                                        echo "
                                                <td>
                                                   <span class='badge text-bg-danger' 
                                                    onclick='confirmDelete($id, \"$name\")'
                                                    style='cursor:pointer'>
                                                    Delete
                                                </span>
                                                    &nbsp;&nbsp;

                                                    <span class='badge text-bg-info' 
                                                        onclick=\"window.location.href='/FastBuy/admin/actions/product/product-details.php?id=$id'\"
                                                        style='cursor:pointer'>
                                                        Info
                                                    </span>
                                                    &nbsp;&nbsp;

                                                    <span class='badge text-bg-warning' 
                                                        onclick=\"window.location.href='/FastBuy/admin/actions/product/add-product.php?id=$id'\"
                                                        style='cursor:pointer'>
                                                        Edit
                                                    </span>
                                                </td>
                                                ";
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
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('success') === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Product added successfully',
                    confirmButtonColor: '#28a745',
                    timer: 3000
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }

            if (urlParams.get('updated') === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Product updated successfully',
                    confirmButtonColor: '#28a745',
                    timer: 3000
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }

            if (urlParams.get('error') === 'product_used') {
                Swal.fire({
                    icon: 'error',
                    title: 'Deletion not allowed',
                    text: 'This product is used in multiple orders',
                    confirmButtonColor: '#dc3545',
                    showConfirmButton: true
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }
        });

        function confirmDelete(id, productName) {
            Swal.fire({
                title: 'Are you sure?',
                text: productName + " will be permanently deleted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/FastBuy/admin/actions/product/delete-product.php?id=' + id;
                }
            });
        }
    </script>

</body>
<!--end::Body-->

</html>