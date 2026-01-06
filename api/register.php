<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $required = ['firstName', 'lastName', 'email', 'phone', 'password', 'userType'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validate user type - movers are admin-only
    if ($data['userType'] === 'mover') {
        throw new Exception('Moving services can only be registered by administrators. Please contact support.');
    }

    // Validate user type is valid
    $validUserTypes = ['seeker', 'landlord'];
    if (!in_array($data['userType'], $validUserTypes)) {
        throw new Exception('Invalid user type selected');
    }

    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        throw new Exception('Email already registered');
    }

    // Hash password
    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32));

    // Prepare user data
    $userData = [
        'first_name' => $data['firstName'],
        'last_name' => $data['lastName'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'password_hash' => $passwordHash,
        'user_type' => $data['userType'],
        'verification_token' => $verificationToken
    ];

    // Add user type specific fields
    switch ($data['userType']) {
        case 'seeker':
            $userData['preferred_locations'] = $data['preferredLocations'] ?? null;
            $userData['max_budget'] = $data['maxBudget'] ?? null;
            break;
        case 'landlord':
            $userData['property_count'] = $data['propertyCount'] ?? null;
            $userData['primary_location'] = $data['primaryLocation'] ?? null;
            break;
        case 'mover':
            $userData['company_name'] = $data['companyName'] ?? null;
            $userData['service_areas'] = $data['serviceAreas'] ?? null;
            break;
    }

    // For testing, set verified to true
    $userData['email_verified'] = true;

    // Insert user
    $columns = implode(', ', array_keys($userData));
    $placeholders = str_repeat('?, ', count($userData) - 1) . '?';
    $stmt = $pdo->prepare("INSERT INTO users ($columns) VALUES ($placeholders)");
    $stmt->execute(array_values($userData));

    $userId = $pdo->lastInsertId();

    // Send verification email
    sendVerificationEmail($data['email'], $verificationToken);

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful. Please check your email to verify your account.',
        'user_id' => $userId
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function sendVerificationEmail($email, $token) {
    $subject = 'Verify Your Rheaspark Account';
    $verificationLink = "http://localhost:8000/index.php?page=verify&token=$token";
    $message = "
    <html>
    <head>
        <title>Verify Your Account</title>
    </head>
    <body>
        <h2>Welcome to Rheaspark!</h2>
        <p>Please click the link below to verify your email address:</p>
        <a href=\"$verificationLink\">Verify Email</a>
        <p>If the link doesn't work, copy and paste this URL: $verificationLink</p>
        <p>This link will expire in 24 hours.</p>
    </body>
    </html>
    ";

    sendSMTPEmail($email, $subject, $message);
}
?>