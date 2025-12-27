<?php
session_start();
require_once '../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $db = Database::getInstance();
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $user = $db->getUserByEmail($email); 

            if ($user && $password === $user['password']) {
                // 1. بدء جلسة المستخدم
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_full_name'] = $user['first_name'] . " " . $user['last_name'];
                $_SESSION['is_logged_in'] = true;

                // 2. نقل سلة الجلسة (إن وُجدت) إلى قاعدة البيانات
                if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart'])) {
                    // جلب أو إنشاء سلة المستخدم
                    $cart = $db->getCartByUserId($user['id']);
                    if (!$cart) {
                        $cart_id = $db->createCart($user['id']);
                        $cart = ['id' => $cart_id];
                    }

                    // إضافة كل عنصر من السيشن إلى قاعدة البيانات
                    foreach ($_SESSION['guest_cart'] as $item) {
                        $db->addToCart($cart['id'], $item['product_id'], $item['quantity']);
                    }

                    // مسح سلة الجلسة
                    unset($_SESSION['guest_cart']);
                }

                // 3. توجيه المستخدم إلى صفحة الدفع (checkout.php) بعد النجاح
                echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Welcome back, " . htmlspecialchars($user['first_name']) . "!',
                            icon: 'success',
                            confirmButtonColor: '#ffba00'
                        }).then(() => {
                            window.location.href = '../karma-master/checkout.php'; // ✅ التغيير هنا
                        });
                    };
                </script>";
            } else {
                echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: 'Invalid email or password',
                            confirmButtonColor: '#ffba00'
                        }).then(() => { window.history.back(); });
                    };
                </script>";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>