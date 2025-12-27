<?php
require_once "../../../config/db.php";
$db = Database::getInstance();

if (isset($_GET['id'])) {
    $db->deleteProduct($_GET['id']);
    header("Location: ../../show-products.php?deleted=1");
    exit();
}
?>