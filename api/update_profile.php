<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');

    // Basic validation - only validate fields that are being updated
    if (isset($_POST['first_name']) && empty($first_name)) {
        echo json_encode(['success' => false, 'error' => 'First name is required']);
        exit;
    }

    if (isset($_POST['last_name']) && empty($last_name)) {
        echo json_encode(['success' => false, 'error' => 'Last name is required']);
        exit;
    }

    if (isset($_POST['email'])) {
        if (empty($email)) {
            echo json_encode(['success' => false, 'error' => 'Email is required']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Invalid email format']);
            exit;
        }
    }

    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Email already in use']);
        exit;
    }

    // Build update query dynamically for partial updates
    $updateFields = [];
    $params = [];

    if (!empty($first_name)) {
        $updateFields[] = "first_name = ?";
        $params[] = $first_name;
    }
    if (!empty($last_name)) {
        $updateFields[] = "last_name = ?";
        $params[] = $last_name;
    }
    if (!empty($email)) {
        $updateFields[] = "email = ?";
        $params[] = $email;
    }
    if (isset($_POST['phone'])) { // Allow empty phone
        $updateFields[] = "phone = ?";
        $params[] = $phone;
    }
    if (isset($_POST['bio'])) { // Allow empty bio
        $updateFields[] = "bio = ?";
        $params[] = $bio;
    }
    if (isset($_POST['address'])) { // Allow empty address
        $updateFields[] = "address = ?";
        $params[] = $address;
    }
    if (isset($_POST['city'])) { // Allow empty city
        $updateFields[] = "city = ?";
        $params[] = $city;
    }
    if (isset($_POST['postal_code'])) { // Allow empty postal_code
        $updateFields[] = "postal_code = ?";
        $params[] = $postal_code;
    }

    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'error' => 'No fields to update']);
        exit;
    }

    $params[] = $user_id;
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);

    if ($result) {
        // Update session variables
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email;

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>