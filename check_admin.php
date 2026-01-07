<?php
require_once 'db.php';

try {
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, user_type, email_verified FROM users WHERE user_type = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        echo "Admin user found:<br>";
        echo "ID: " . $admin['id'] . "<br>";
        echo "Name: " . $admin['first_name'] . " " . $admin['last_name'] . "<br>";
        echo "Email: " . $admin['email'] . "<br>";
        echo "Type: " . $admin['user_type'] . "<br>";
        echo "Verified: " . ($admin['email_verified'] ? 'Yes' : 'No') . "<br>";

        // Test password
        $password = 'password';
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$admin['id']]);
        $hash = $stmt->fetch()['password_hash'];

        if (password_verify($password, $hash)) {
            echo "Password 'password' is correct.<br>";
        } else {
            echo "Password 'password' is incorrect.<br>";
        }
    } else {
        echo "No admin user found.<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>