<?php
require_once "../../../config/db.php";
$db = Database::getInstance();

if (isset($_GET['id']) && isset($_GET['status'])) {
    $db->updatePayment($_GET['id'], $_GET['status']);
    header("Location: ../../show-payments.php?updated=1");
    exit();
}

header("Location: ../../show-payments.php");
exit();
