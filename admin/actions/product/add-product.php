<!doctype html>
<html lang="en">
<?php
require_once "../../../config/db.php";
$db = Database::getInstance();
$isEdit = false;
$product = null;

if (isset($_GET['id'])) {
    $isEdit = true;
    $product = $db->getProductById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['product-name'] ?? '');
    $short_desc = trim($_POST['product-short-description'] ?? '');
    $price = $_POST['product-price'] ?? '';
    $stock = $_POST['product-stock'] ?? '';
    $discount = $_POST['product-discount'] ?? '';
    $category_id = $_POST['product-id-category'] ?? '';
    $description = trim($_POST['product-description'] ?? '');
    $image = '';
    if (empty($name)) {
        $errors['name'] = "Product name is required";
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $name)) {
        $errors['name'] = "Product name can only contain letters, numbers, spaces, and dashes";
    }

    if (empty($short_desc)) {
        $errors['short_desc'] = "Brief description is required";
    } elseif (strlen($short_desc) < 10) {
        $errors['short_desc'] = "Brief description must be at least 10 characters";
    } elseif (strlen($short_desc) > 250) {
        $errors['short_desc'] = "Brief description must not exceed 250 characters";
    }

    if (empty($price)) {
        $errors['price'] = "Price is required";
    } elseif (!is_numeric($price) || floatval($price) <= 0) {
        $errors['price'] = "Price must be a positive number";
    }

    if (empty($stock)) {
        $errors['stock'] = "Stock quantity is required";
        ////////
    } elseif (!ctype_digit($stock) || intval($stock) < 1) {
        $errors['stock'] = "Stock must be a positive number";
    }

    if (!is_numeric($discount) || floatval($discount) < 0) {
        $errors['discount'] = "Discount must be a positive number";
    } elseif (floatval($discount) > 100) {
        $errors['discount'] = "Discount cannot exceed 100%";
    }

    if (empty($category_id)) {
        $errors['category'] = "Category is required";
    }

    if (empty($description)) {
        $errors['description'] = "Description is required";
    } elseif (strlen($description) < 20) {
        $errors['description'] = "Description must be at least 20 characters";
    } elseif (strlen($description) > 1000) {
        $errors['description'] = "Description must not exceed 1000 characters";
    }

    if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product-image']['tmp_name'];
        $fileName = $_FILES['product-image']['name'];
        $fileSize = $_FILES['product-image']['size'];
        $fileType = $_FILES['product-image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../../../assets/img/products/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $image = 'products/' . $newFileName; 
            } else {
                $errors['image'] = 'There was some error moving the file to upload directory.';
            }
        } else {
            $errors['image'] = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    } elseif (!$isEdit && empty($_FILES['product-image']['name'])) {

    }


    if (empty($errors)) {
        if ($isEdit) {
            $data = [
                'name' => $name,
                'description' => $description,
                'short_description' => $short_desc,
                'price' => $price,
                'stock' => $stock,
                'discount' => $discount,
                'category_id' => $category_id
            ];
            
            if (!empty($image)) {
                $data['image'] = $image;
            }

            $db->updateProduct($_GET['id'], $data);
            header("Location: ../../show-products.php?updated=1");
        } else {

            $db->createProduct($name, $description, $short_desc, $price, $stock, $discount, $image, $category_id);
            header("Location: ../../show-products.php?success=1");
        }
        exit();
    }
}


?>

<head>
    <meta charset="utf-8" />
    <title>FastBuy | <?php echo $isEdit ? 'Edit Product' : 'Add Product'; ?></title>
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

    <!-- ------------------------------------ -->
    <style>
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .is-valid {
            border-color: #28a745;
        }
    </style>


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
                            <h3 class="mb-0"><?php echo $isEdit ? 'Edit Product' : 'Add New Product'; ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/FastBuy/admin/index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="/FastBuy/admin/show-products.php">Products</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <?php echo $isEdit ? 'Edit' : 'Add'; ?>
                                </li>
                            </ol>
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
                    <!--begin::Quick Example-->
                    <div class="card card-primary card-outline mb-4">
                        <!--begin::Header-->
                        <div class="card-header">
                            <div class="card-title"><?php echo $isEdit ? 'Edit Product' : 'Add New Product'; ?></div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form action="<?php echo $isEdit ? '?id=' . $_GET['id'] : '#'; ?>" method="post" id="productForm" enctype="multipart/form-data">
                            <!--begin::Body-->
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="product-name" class="form-label">Product Name</label>
                                    <input
                                        id="product-name"
                                        name="product-name"
                                        type="text"
                                        class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($_POST['product-name'] ?? $product['name'] ?? ''); ?>" />
                                    <div class="invalid-feedback" id="product-name-error">
                                        <?php echo $errors['name'] ?? ''; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="product-short-description" class="form-label">Brief Description</label>
                                    <input
                                        id="product-short-description"
                                        name="product-short-description"
                                        type="text"
                                        class="form-control <?php echo isset($errors['short_desc']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($_POST['product-short-description'] ?? $product['short_description'] ?? ''); ?>" />
                                    <div class="invalid-feedback" id="product-short-description-error">
                                        <?php echo $errors['short_desc'] ?? ''; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="product-price" class="form-label">Price</label>
                                    <input
                                        id="product-price"
                                        name="product-price"
                                        type="text"
                                        class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($_POST['product-price'] ?? $product['price'] ?? ''); ?>" />
                                    <div class="invalid-feedback" id="product-price-error">
                                        <?php echo $errors['price'] ?? ''; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="product-stock" class="form-label">Stock Quantity</label>
                                    <input
                                        id="product-stock"
                                        name="product-stock"
                                        type="text"
                                        class="form-control <?php echo isset($errors['stock']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($_POST['product-stock'] ?? $product['stock'] ?? ''); ?>" />
                                    <div class="invalid-feedback" id="product-stock-error">
                                        <?php echo $errors['stock'] ?? ''; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="product-discount" class="form-label">Discount (%)</label>
                                    <input
                                        id="product-discount"
                                        name="product-discount"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['product-discount'] ?? $product['discount'] ?? '0'); ?>"
                                        class="form-control <?php echo isset($errors['discount']) ? 'is-invalid' : ''; ?>" />
                                    <div class="invalid-feedback" id="product-discount-error">
                                        <?php echo $errors['discount'] ?? ''; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="product-id-category" class="form-label">Category</label>
                                    <select class="form-select <?php echo isset($errors['category']) ? 'is-invalid' : ''; ?>"
                                        name="product-id-category"
                                        id="product-id-category">
                                        <?php
                                        $categories = $db->getAllCategories()->fetchAll();
                                        foreach ($categories as $category) {
                                            $id = $category['id'];
                                            $name = $category['name'];
                                            $selected = ($isEdit && $product['category_id'] == $id) ? 'selected' : '';
                                            echo "<option value='$id' $selected>$name</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback" id="product-id-category-error">
                                        <?php echo $errors['category'] ?? ''; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="product-description" class="form-label">Description</label>
                                    <textarea id="product-description"
                                        name="product-description"
                                        rows="5"
                                        class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>"><?php echo htmlspecialchars($_POST['product-description'] ?? $product['description'] ?? ''); ?></textarea>
                                    <div class="invalid-feedback" id="product-description-error">
                                        <?php echo $errors['description'] ?? ''; ?>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="inputGroupFile02" name="product-image" accept="image/*" />
                                    <label class="input-group-text" for="inputGroupFile02">Upload</label>
                                    <div class="invalid-feedback" id="product-image-error">
                                        <?php echo $errors['image'] ?? ''; ?>
                                    </div>
                                </div>

                            </div>
                            <!--end::Body-->
                            <!--begin::Footer-->
                            <div class="card-footer">
                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='../../show-products.php';">Cancel</button>
                                    <input type="submit" class="btn btn-success" value="<?php echo $isEdit ? 'Update' : 'Add'; ?>">
                                </div>
                            </div>
                            <!--end::Footer-->
                        </form>
                        <!--end::Form-->
                    </div>
                </div>
                <!--end::Container-->
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

    <script>
        // validation 
        function validateProductName(value) {
            const regex = /^[a-zA-Z0-9\s\-]+$/;
            if (!value.trim()) {
                return "Product name is required";
            }
            if (!regex.test(value)) {
                return "Product name can only contain letters, numbers, spaces, and dashes";
            }
            return "";
        }

        function validateShortDescription(value) {
            if (!value.trim()) {
                return "Brief description is required";
            }
            if (value.trim().length < 10) {
                return "Brief description must be at least 10 characters";
            }
            if (value.trim().length > 250) {
                return "Brief description must not exceed 250 characters";
            }
            return "";
        }

        function validatePrice(value) {
            if (!value.trim()) {
                return "Price is required";
            }
            const num = parseFloat(value);
            if (isNaN(num) || num <= 0) {
                return "Price must be a positive number";
            }
            return "";
        }

        function validateStock(value) {
            if (!value.trim()) {
                return "Stock quantity is required";
            }
            const num = parseInt(value);
            if (isNaN(num) || num < 1 || !Number.isInteger(parseFloat(value))) {
                return "Stock must be a positive number";
            }
            return "";
        }

        function validateDiscount(value) {
            if (!value.trim()) {
                return "Discount is required";
            }
            const num = parseFloat(value);
            if (isNaN(num) || num < 0) {
                return "Discount must be a positive number (e.g: 90 = 90%)";
            }
            if (num > 100) {
                return "Discount cannot exceed 100%";
            }
            return "";
        }

        function validateCategory(value) {
            if (!value) {
                return "Category is required";
            }
            return "";
        }


        function validateDescription(value) {
            if (!value.trim()) {
                return "Description is required";
            }
            if (value.trim().length < 20) {
                return "Description must be at least 20 characters";
            }
            if (value.trim().length > 1000) {
                return "Description must not exceed 1000 characters";
            }
            return "";
        }

        function showError(input, message) {
            const errorDiv = document.getElementById(input.id + '-error');
            if (message) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                errorDiv.textContent = message;
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                errorDiv.textContent = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const productName = document.getElementById('product-name');
            const shortDesc = document.getElementById('product-short-description');
            const price = document.getElementById('product-price');
            const stock = document.getElementById('product-stock');
            const discount = document.getElementById('product-discount');
            const category = document.getElementById('product-id-category');
            const description = document.getElementById('product-description');

            productName.addEventListener('input', function() {
                showError(this, validateProductName(this.value));
            });

            shortDesc.addEventListener('input', function() {
                showError(this, validateShortDescription(this.value));
            });

            price.addEventListener('input', function() {
                showError(this, validatePrice(this.value));
            });

            stock.addEventListener('input', function() {
                showError(this, validateStock(this.value));
            });

            discount.addEventListener('input', function() {
                showError(this, validateDiscount(this.value));
            });

            category.addEventListener('change', function() {
                showError(this, validateCategory(this.value));
            });

            description.addEventListener('input', function() {
                showError(this, validateDescription(this.value));
            });

            document.getElementById('productForm').addEventListener('submit', function(e) {
                let isValid = true;

                const validations = [{
                        input: productName,
                        validator: validateProductName
                    },
                    {
                        input: shortDesc,
                        validator: validateShortDescription
                    },
                    {
                        input: price,
                        validator: validatePrice
                    },
                    {
                        input: stock,
                        validator: validateStock
                    },
                    {
                        input: discount,
                        validator: validateDiscount
                    },
                    {
                        input: category,
                        validator: validateCategory
                    },
                    {
                        input: description,
                        validator: validateDescription
                    }
                ];

                validations.forEach(({
                    input,
                    validator
                }) => {
                    let error = validator(input.value);
                    showError(input, error);
                    if (error) isValid = false;
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });

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
        });
    </script>

    <!-- OverlayScrollbars initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: 'os-theme-light',
                        autoHide: 'leave',
                        clickScroll: true
                    }
                });
            }
        });
    </script>