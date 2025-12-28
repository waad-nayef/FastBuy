<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../karma-master/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../karma-master/edit_profile.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$db = Database::getInstance();

// Get form data
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$country = trim($_POST['country'] ?? '');
$city = trim($_POST['city'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email)) {
    header('Location: ../karma-master/edit_profile.php?error=missing_fields');
    exit;
}

// Prepare update data
$updateData = [
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
    'phone' => $phone,
    'country' => $country,
    'city' => $city
];

// Handle password change if requested
if (!empty($current_password)) {
    $user = $db->getUserById($user_id);
    
    // Verify current password
    $password_valid = false;
    if (password_verify($current_password, $user['password'])) {
        $password_valid = true;
    } elseif ($current_password === $user['password']) {
        // Plain text fallback
        $password_valid = true;
    }
    
    if (!$password_valid) {
        header('Location: ../karma-master/edit_profile.php?error=wrong_password');
        exit;
    }
    
    // Validate new password
    if (empty($new_password) || $new_password !== $confirm_password) {
        header('Location: ../karma-master/edit_profile.php?error=password_mismatch');
        exit;
    }
    
    // Add hashed password to update data
    $updateData['password'] = password_hash($new_password, PASSWORD_DEFAULT);
}

// Update user
$result = $db->updateUser($user_id, $updateData);

if ($result) {
    // Update session data
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    
    header('Location: ../karma-master/user_profile.php?success=profile_updated');
} else {
    header('Location: ../karma-master/edit_profile.php?error=update_failed');
}
exit;
?>
