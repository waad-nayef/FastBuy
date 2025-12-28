<!doctype html>
<html lang="en">
<?php
require_once "../../../config/db.php";
$db = Database::getInstance();
$isEdit = false;
$category = null;

if (isset($_GET['id'])) {
    $isEdit = true;
    $category = $db->getCategoryById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['category-name'] ?? '');
    $description = trim($_POST['category-description'] ?? '');

    if (empty($name)) {
        $errors['name'] = "Category name is required";
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $name)) {
        $errors['name'] = "Category name can only contain letters, numbers, spaces, and dashes";
    }

    if (empty($description)) {
        $errors['description'] = "Description is required";
    } elseif (strlen($description) < 20) {
        $errors['description'] = "Description must be at least 20 characters";
    } elseif (strlen($description) > 1000) {
        $errors['description'] = "Description must not exceed 1000 characters";
    }

    if (empty($errors)) {
        if ($isEdit) {
            $db->updateCategory($_GET['id'], [
                'name' => $name,
                'description' => $description
            ]);
            header("Location: ../../show-categories.php?updated=1");
        } else {
            $db->createCategory($name, $description);
            header("Location: ../../show-categories.php?success=1");
        }
        exit();
    }
}
?>

<head>
    <meta charset="utf-8" />
    <title>FastBuy | <?php echo $isEdit ? 'Edit Category' : 'Add Category'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="/FastBuy/admin/css/adminlte.css" />

    <script src="https://kit.fontawesome.com/8bb0a97d35.js" crossorigin="anonymous"></script>

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
                            <h3 class="mb-0"><?php echo $isEdit ? 'Edit Category' : 'Add New Category'; ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/FastBuy/admin/index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="/FastBuy/admin/show-categories.php">Categories</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <?php echo $isEdit ? 'Edit' : 'Add'; ?>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <div class="card-title"><?php echo $isEdit ? 'Edit Category' : 'Add New Category'; ?></div>
                        </div>
                        <form action="<?php echo $isEdit ? '?id=' . $_GET['id'] : '#'; ?>" method="post" id="categoryForm">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="category-name" class="form-label">Category Name</label>
                                    <input
                                        id="category-name"
                                        name="category-name"
                                        type="text"
                                        class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($_POST['category-name'] ?? $category['name'] ?? ''); ?>" />
                                    <div class="invalid-feedback" id="category-name-error">
                                        <?php echo $errors['name'] ?? ''; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="category-description" class="form-label">Description</label>
                                    <textarea id="category-description"
                                        name="category-description"
                                        rows="5"
                                        class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>"><?php echo htmlspecialchars($_POST['category-description'] ?? $category['description'] ?? ''); ?></textarea>
                                    <div class="invalid-feedback" id="category-description-error">
                                        <?php echo $errors['description'] ?? ''; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='../../show-categories.php';">Cancel</button>
                                    <input type="submit" class="btn btn-success" value="<?php echo $isEdit ? 'Update' : 'Add'; ?>">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
    <script src="/FastBuy/admin/js/adminlte.js"></script>

    <script>
        function validateCategoryName(value) {
            const regex = /^[a-zA-Z0-9\s\-]+$/;
            if (!value.trim()) {
                return "Category name is required";
            }
            if (!regex.test(value)) {
                return "Category name can only contain letters, numbers, spaces, and dashes";
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
            const categoryName = document.getElementById('category-name');
            const description = document.getElementById('category-description');

            categoryName.addEventListener('input', function() {
                showError(this, validateCategoryName(this.value));
            });

            description.addEventListener('input', function() {
                showError(this, validateDescription(this.value));
            });

            document.getElementById('categoryForm').addEventListener('submit', function(e) {
                let isValid = true;

                const validations = [{
                        input: categoryName,
                        validator: validateCategoryName
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
</body>

</html>