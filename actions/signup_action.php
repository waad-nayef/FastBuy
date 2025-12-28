<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        // In a real app, we should pass back specific errors
        header("Location: ../karma-master/signup.php?error=missing_fields");
        exit();
    }

    $db = Database::getInstance();
    
    // Check if user exists
    if ($db->getUserByEmail($email)) {
        header("Location: ../karma-master/signup.php?error=email_exists");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Default role is 'user'
    $result = $db->createUser($first_name, $last_name, $email, $hashed_password, 'user');

    if ($result) {
        // Auto login or redirect to login? Let's redirect to login for now as per common flow, 
        // or we could log them in. User didn't specify, but login flow is safer.
        header("Location: ../karma-master/login.php?success=account_created");
        exit();
    } else {
        header("Location: ../karma-master/signup.php?error=registration_failed");
        exit();
    }
} else {
    header("Location: ../karma-master/signup.php");
    exit();
}
