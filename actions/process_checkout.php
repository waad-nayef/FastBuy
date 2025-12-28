<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../karma-master/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance();
    $user_id = $_SESSION['user_id'];
    $cart_id = $_POST['cart_id'];
    
    // address is posted but not stored in DB as per current schema, 
    // but Phone, City, Country are updated in User Profile
    $phone = $_POST['phone'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    $provider = $_POST['provider'] ?? 'Cash';
    $payment_method = $_POST['payment_method'] ?? 'Cash';

    // Update User Info
    $db->updateUser($user_id, [
        'phone' => $phone,
        'city' => $city,
        'country' => $country
    ]);

    try {
        // Create Order and Move Items (Transaction)
        $order_id = $db->createOrderFromCart($user_id, $cart_id);
        
        if ($order_id) {
            // Fetch Order to get Total Price
            $order = $db->getOrderById($order_id);
            $amount = $order['total_price'] ?? 0;

            // Create Payment Record
            $db->createPayment(
                $order_id,
                $user_id,
                $amount,
                $provider,
                $payment_method,
                'pending' // Status
            );

            // Return JSON Success
            echo json_encode(['status' => 'success', 'order_id' => $order_id, 'message' => 'Order placed successfully!']);
            exit();
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Order failed to create.']);
             exit();
        }
    } catch (Exception $e) {
        // Log error
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
}
// Invalid Request
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
exit();
?>
