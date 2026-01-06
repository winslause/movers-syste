<?php
require_once 'db.php';

// Test password verification
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$password = 'Temp@1234';

if (password_verify($password, $hash)) {
    echo "Password matches!\n";
} else {
    echo "Password does not match!\n";
}

// Check if admin user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute(['admin@rheaspark.com']);
$user = $stmt->fetch();

if ($user) {
    echo "Admin user found:\n";
    print_r($user);
} else {
    echo "Admin user not found!\n";
}
?>