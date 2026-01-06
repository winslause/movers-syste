<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['token']) || !isset($data['password'])) {
        throw new Exception('Token and new password are required');
    }

    // Validate password strength
    if (strlen($data['password']) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }

    // Get user by reset token
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$data['token']]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Invalid or expired reset token');
    }

    // Hash new password
    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

    // Update password and clear reset token
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->execute([$passwordHash, $user['id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully. You can now log in with your new password.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>