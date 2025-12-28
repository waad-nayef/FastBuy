<?php
require_once "../../../config/db.php";
$db = Database::getInstance();

if (isset($_GET['id'])) {

    $db->deleteUser($_GET['id']);
    header("Location: ../../show-users.php?deleted=1");
    exit();
}
