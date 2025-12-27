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
                $_SESSION['user_role'] = $user['role']; // ← حفظ الدور في الجلسة

                // 2. نقل سلة الجلسة (إن وُجدت)
                if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart'])) {
                    $cart = $db->getCartByUserId($user['id']);
                    if (!$cart) {
                        $cart_id = $db->createCart($user['id']);
                        $cart = ['id' => $cart_id];
                    }
                    foreach ($_SESSION['guest_cart'] as $item) {
                        $db->addToCart($cart['id'], $item['product_id'], $item['quantity']);
                    }
                    unset($_SESSION['guest_cart']);
                }

                // 3. توجيه المستخدم حسب الدور
                $redirect_url = '';
                if ($user['role'] === 'admin') {
                    $redirect_url = '../admin/index.php';
                } else {
                    // مستخدم عادي: تحقق إذا كان هناك تحويل مطلوب (مثل checkout)
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect_url = '../karma-master/' . $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                    } else {
                        $redirect_url = '../karma-master/index.php';
                    }
                }

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
                            window.location.href = '" . $redirect_url . "';
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