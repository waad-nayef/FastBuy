<?php
require_once "../../../config/db.php";
$db = Database::getInstance();
if (isset($_GET['id'])) {
    $db->cancelOrder($_GET['id']);
    if ($_GET['place'] === 'userDetails') {
        $userId = $_GET['userid'];
        header("Location: ../user/user-details.php?cancelled=1&id=$userId");
    } else
        header("Location: ../../show-orders.php");

    exit();
}
