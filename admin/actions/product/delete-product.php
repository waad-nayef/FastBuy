<?php
require_once "../../../config/db.php";
$db = Database::getInstance();

if (!isset($_GET['id'])) {
    header("Location: ../../show-products.php");
    exit();
}
$place = isset($_GET['category_items']);

$product_id = (int) $_GET['id'];

$count = $db->query(
    "SELECT COUNT(DISTINCT order_id) AS total 
     FROM order_items 
     WHERE product_id = ?",
    [$product_id]
)->fetch()['total'];

if ($count > 0) {
    if ($place)
        header("Location: ../category/show-category-items.php?error=product_used");
    else
        header("Location: ../../show-products.php?error=product_used");
    exit();
}

$db->deleteProduct($product_id);
if ($place)
    header("Location: ../category/show-category-items.php?deleted=1");
else
    header("Location: ../../show-products.php?deleted=1");
exit();
