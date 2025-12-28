<!doctype html>
<html lang="en">
<?php
require_once "../../../config/db.php";
$db = Database::getInstance();

if (!isset($_GET['id'])) {
    header("Location: ../../show-products.php");
    exit();
}

$product = $db->getProductById($_GET['id']);
if (!$product) {
    echo "Product not found.";
    exit();
}
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>FastBuy | Product Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="/FastBuy/admin/css/adminlte.css" />
    
    <!-- Icons -->
    <script src="https://kit.fontawesome.com/8bb0a97d35.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php
        include "../../../admin-components/navbar.php";
        include "../../../admin-components/sidebar.php";
        ?>

        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Product Details</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/FastBuy/admin/index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="/FastBuy/admin/show-products.php">Products</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Details</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::App Content Header-->

            <div class="app-content">
                <div class="container-fluid">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title"><?= htmlspecialchars($product['name']) ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="/FastBuy/assets/img/<?= htmlspecialchars($product['image']) ?>" 
                                             alt="<?= htmlspecialchars($product['name']) ?>" 
                                             class="img-fluid rounded shadow" 
                                             style="max-height: 400px; object-fit: contain;">
                                    <?php else: ?>
                                        <div class="alert alert-secondary d-flex align-items-center justify-content-center" style="height: 300px;">
                                            <span>No Image Available</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 200px;">ID</th>
                                                <td><?= $product['id'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Name</th>
                                                <td><?= htmlspecialchars($product['name']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Category</th>
                                                <td><?= htmlspecialchars($db->getCategoryById($product['category_id'])['name']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Price</th>
                                                <td>$<?= number_format($product['price'], 2) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Stock</th>
                                                <td><?= $product['stock'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Discount</th>
                                                <td><?= $product['discount'] ?>%</td>
                                            </tr>
                                            <tr>
                                                <th>Short Description</th>
                                                <td class="text-break"><?= htmlspecialchars($product['short_description']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Full Description</th>
                                                <td class="text-break"><?= nl2br(htmlspecialchars($product['description'])) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="../../show-products.php" class="btn btn-secondary">Back to List</a>
                            <a href="add-product.php?id=<?= $product['id'] ?>" class="btn btn-warning">Edit Product</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
    <script src="/FastBuy/admin/js/adminlte.js"></script>
    <script>
        const sidebarWrapper = document.querySelector('.sidebar-wrapper');
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal !== 'undefined') {
            OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                scrollbars: {
                    theme: 'os-theme-light',
                    autoHide: 'leave',
                    clickScroll: true
                }
            });
        }
    </script>
</body>
</html>
