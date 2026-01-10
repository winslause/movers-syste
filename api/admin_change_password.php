<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['currentPassword']) || !isset($data['newPassword']) || !isset($data['confirmPassword'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

$currentPassword = $data['currentPassword'];
$newPassword = $data['newPassword'];
$confirmPassword = $data['confirmPassword'];

try {
    // Get current user password hash
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('User not found');
    }

    // Verify current password
    if (!password_verify($currentPassword, $user['password_hash'])) {
        throw new Exception('Current password is incorrect');
    }

    // Check if new password matches confirmation
    if ($newPassword !== $confirmPassword) {
        throw new Exception('New password and confirmation do not match');
    }

    // Validate new password strength
    if (strlen($newPassword) < 8) {
        throw new Exception('New password must be at least 8 characters long');
    }

    // Hash new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $result = $stmt->execute([$newPasswordHash, $user_id]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    } else {
        throw new Exception('Failed to update password');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?></content>
</xai:function_call">Created API endpoint for admin password change.