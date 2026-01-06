<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$user_type = $_POST['user_type'] ?? 'seeker';
$verification = $_POST['verification'] ?? 'pending';
$account_status = $_POST['account_status'] ?? 'active';
$password = $_POST['password'] ?? 'Temp@1234';

// Validation
if (empty($first_name) || empty($last_name) || empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Required fields missing']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit();
}

if (!in_array($user_type, ['seeker', 'landlord', 'mover', 'admin'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid user type']);
    exit();
}

if (!in_array($verification, ['verified', 'pending', 'unverified'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid verification status']);
    exit();
}

if (!in_array($account_status, ['active', 'inactive', 'suspended'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid account status']);
    exit();
}

// Check if email exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already exists']);
    exit();
}

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$email_verified = ($verification === 'verified') ? 1 : 0;

// Insert user
$stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, user_type, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt->execute([$first_name, $last_name, $email, $phone, $password_hash, $user_type, $email_verified])) {
    $user_id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'user_id' => $user_id, 'message' => 'User created successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create user']);
}
?>