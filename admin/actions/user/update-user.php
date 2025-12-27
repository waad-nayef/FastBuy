<?php
require_once "../../../config/db.php";
$db = Database::getInstance();

if (isset($_GET['id'])) {

    $db->updateUser($_GET['id'], ['role' => 'admin']);
    header("Location: ../../show-users.php?setAdmin=1");
    exit();
}
