<?php
require_once '../config/db.php';


try {
    $db = Database::getInstance();
    
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $db->query("ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) NULL DEFAULT NULL");
        echo "Column 'remember_token' added successfully.";
    } else {
        echo "Column 'remember_token' already exists.";
    }
} catch (PDPException $e) {
    echo "Error: " . $e->getMessage();
}

