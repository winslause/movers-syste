<?php
require_once 'db.php';

$email = 'admin@rheaspark.com';
$password = 'password';

try {
    // Get user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "User not found\n";
        exit;
    }

    echo "User found: " . $user['first_name'] . " " . $user['last_name'] . "\n";
    echo "User type: " . $user['user_type'] . "\n";
    echo "Email verified: " . ($user['email_verified'] ? 'Yes' : 'No') . "\n";

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        echo "Password incorrect\n";
        exit;
    }

    echo "Password correct\n";
    echo "Login should succeed\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>