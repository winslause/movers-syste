<?php
require_once 'db.php';

$first_name = 'Admin';
$last_name = 'User';
$email = 'admin@rheaspark.com';
$password = 'password';
$user_type = 'admin';
$email_verified = 1;

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        echo "Admin user already exists.\n";
    } else {
        // Insert admin user
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, user_type, email_verified) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $password_hash, $user_type, $email_verified]);
        echo "Admin user inserted successfully.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>