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

    if (!$data || !isset($data['email'])) {
        throw new Exception('Email is required');
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, first_name FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();

    if (!$user) {
        // Don't reveal if email exists or not for security
        echo json_encode([
            'success' => true,
            'message' => 'If an account with that email exists, we have sent a password reset link.'
        ]);
        exit;
    }

    // Generate reset token
    $resetToken = bin2hex(random_bytes(32));
    $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Update user with reset token
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
    $stmt->execute([$resetToken, $resetExpires, $user['id']]);

    // Send reset email
    sendResetEmail($data['email'], $resetToken, $user['first_name']);

    echo json_encode([
        'success' => true,
        'message' => 'Password reset link sent to your email.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function sendResetEmail($email, $token, $firstName) {
    $subject = 'Reset Your Rheaspark Password';
    $resetLink = "http://localhost:8000/index.php?page=reset_password&token=$token";
    $message = "
    <html>
    <head>
        <title>Reset Your Password</title>
    </head>
    <body>
        <h2>Hi $firstName,</h2>
        <p>You requested a password reset for your Rheaspark account.</p>
        <p>Please click the link below to reset your password:</p>
        <a href=\"$resetLink\">Reset Password</a>
        <p>If the link doesn't work, copy and paste this URL: $resetLink</p>
        <p>This link will expire in 1 hour.</p>
        <p>If you didn't request this reset, please ignore this email.</p>
    </body>
    </html>
    ";

    sendSMTPEmail($email, $subject, $message);
}
?>