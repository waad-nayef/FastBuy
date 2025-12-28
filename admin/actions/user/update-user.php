<?php
require_once "../../../config/db.php";
$db = Database::getInstance();

if (isset($_GET['id'])) {
    if ($_GET['role'] == "admin")
        $db->updateUser($_GET['id'], ['role' => 'admin']);
    else
        $db->updateUser($_GET['id'], ['role' => 'user']);
    $id = $_GET['id'];
    header("Location: user-details.php?setAdmin=1&id=$id");
    exit();
}
