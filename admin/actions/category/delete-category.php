<?php
require_once "../../../config/db.php";
$db = Database::getInstance();

if (!isset($_GET['id'])) {
    header("Location: ../../show-categories.php");
    exit();
}

$category_id = (int) $_GET['id'];

$count = $db->query(
    "SELECT COUNT(*) AS total 
     FROM products 
     WHERE category_id = ?",
    [$category_id]
)->fetch()['total'];

if ($count > 0) {
    header("Location: ../../show-categories.php?error=category_used");
    exit();
}

$db->deleteCategory($category_id);

header("Location: ../../show-categories.php?deleted=1");
exit();