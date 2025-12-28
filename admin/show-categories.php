<!doctype html>
<html lang="en">
<?php
require_once "../config/db.php";
$db = Database::getInstance();
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>FastBuy | Categories</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />

    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />

    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="/FastBuy/admin/css/adminlte.css" as="style" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />

    <link rel="stylesheet" href="/FastBuy/admin/css/adminlte.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous" />

    <script src="https://kit.fontawesome.com/8bb0a97d35.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <?php include "../admin-components/sidebar.php"; ?>

    <?php include "../admin-components/navbar.php"; ?>
    <?php include "../admin-components/scripts.php"; ?>

    <div class="app-wrapper">
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
                                <h3 class="mb-0">Categories Management</h3>
                                <button type="button" class="btn btn-success" onclick="window.location.href='/FastBuy/admin/actions/category/add-category.php'"><i class="bi bi-plus-lg"></i> Add Category</button>
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
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Name</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $categories = $db->getAllCategories()->fetchAll();
                                    foreach ($categories as $category) {
                                        $id = $category['id'];
                                        $name = $category['name'];
                                        $created_at = $category['created_at'];

                                        echo '<tr class="align-middle">';
                                        echo "<td>$id</td>";
                                        echo "<td>$name</td>";
                                        echo "<td>$created_at</td>";
                                        echo "
                                                <td>
                                                   <span class='badge text-bg-danger' 
                                                    onclick='confirmDelete($id, \"$name\")'
                                                    style='cursor:pointer'>
                                                    Delete
                                                </span>
                                                    &nbsp;&nbsp;

                                                    <span class='badge text-bg-info' 
                                                        onclick=\"window.location.href='/FastBuy/admin/actions/category/show-category-items.php?id=$id'\"
                                                        style='cursor:pointer'>
                                                        Show Products
                                                    </span>
                                                    &nbsp;&nbsp;

                                                    <span class='badge text-bg-warning' 
                                                        onclick=\"window.location.href='/FastBuy/admin/actions/category/add-category.php?id=$id'\"
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('success') === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Category added successfully',
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
                    text: 'Category updated successfully',
                    confirmButtonColor: '#28a745',
                    timer: 3000
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }

            if (urlParams.get('deleted') === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Category deleted successfully',
                    confirmButtonColor: '#28a745',
                    timer: 3000
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }

            if (urlParams.get('error') === 'category_used') {
                Swal.fire({
                    icon: 'error',
                    title: 'Deletion not allowed',
                    text: 'This category is used in multiple products',
                    confirmButtonColor: '#dc3545',
                    showConfirmButton: true
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }
        });

        function confirmDelete(id, categoryName) {
            Swal.fire({
                title: 'Are you sure?',
                text: categoryName + " will be permanently deleted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/FastBuy/admin/actions/category/delete-category.php?id=' + id;
                }
            });
        }
    </script>
    <?php include "../admin-components/footer.php"; ?>

</body>

</html>