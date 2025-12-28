<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: karma-master/index.php");
    }
} else {
    header("Location: karma-master/index.php");
}
exit();
?>
