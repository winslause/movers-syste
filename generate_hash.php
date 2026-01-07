<?php
$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password\n";
echo "Hash: $hash\n";

// Test verification
if (password_verify($password, $hash)) {
    echo "Verification: SUCCESS\n";
} else {
    echo "Verification: FAILED\n";
}
?>