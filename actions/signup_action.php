<?php
require_once '../config/db.php'; // التأكد من المسار الصحيح

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = Database::getInstance();
    
    // استقبال البيانات...
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
   $password = $_POST['password'];

    try {
        $result = $db->createUser($first_name, $last_name, $email, $password);
        
        if ($result) {
            // هنا نضع المكتبة والكود معاً لأن هذه هي الصفحة التي يراها المتصفح الآن
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                window.onload = function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Account created successfully',
                        icon: 'success',
                        confirmButtonColor: '#ffba00'
                    }).then(() => {
                        window.location.href = '../karma-master/login.php';
                    });
                };
            </script>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}