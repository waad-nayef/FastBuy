<?php
session_start();
session_destroy();
header("Location: ../karma-master/index.php");
exit();
?>
