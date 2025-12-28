<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../karma-master/login.php?error=login_required");
    exit();
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../karma-master/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Validate inputs
if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
    header("Location: ../karma-master/single-product.php?id=" . $product_id . "&error=invalid_input");
    exit();
}

$db = Database::getInstance();

// Check if user already reviewed this product
$existing = $db->query(
    "SELECT 1 FROM reviews WHERE user_id = ? AND product_id = ?",
    [$user_id, $product_id]
)->fetch();

if ($existing) {
    header("Location: ../karma-master/single-product.php?id=" . $product_id . "&error=already_reviewed");
    exit();
}

// Add review
$result = $db->addReview($user_id, $product_id, $rating, $comment);

if ($result) {
    header("Location: ../karma-master/single-product.php?id=" . $product_id . "&success=review_added");
} else {
    header("Location: ../karma-master/single-product.php?id=" . $product_id . "&error=review_failed");
}
exit();
?>
