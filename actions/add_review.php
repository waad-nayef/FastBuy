<?php
session_start();
require_once '../config/db.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: ../karma-master/login.php?error=login_required");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)($_POST['product_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    // التحقق من صحة المدخلات
    if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
        header("Location: ../karma-master/single-product.php?id=$product_id&error=invalid_review_data");
        exit();
    }

    $db = Database::getInstance();

    // التحقق من أن المستخدم لم يراجع هذا المنتج من قبل
    $existing_review = $db->query(
        "SELECT 1 FROM reviews WHERE user_id = ? AND product_id = ?",
        [$user_id, $product_id]
    )->fetch();

    if ($existing_review) {
        header("Location: ../karma-master/single-product.php?id=$product_id&error=already_reviewed");
        exit();
    }

    // إضافة المراجعة إلى قاعدة البيانات
    $db->addReview($user_id, $product_id, $rating, $comment);

    // إعادة التوجيه مع رسالة نجاح
    header("Location: ../karma-master/single-product.php?id=$product_id&success=review_submitted");
    exit();
}
?>