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

$user_id = $_POST['user_id'] ?? '';

if (empty($user_id) || !is_numeric($user_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit;
}

try {
    // Check if user has associated houses
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM houses WHERE landlord_id = ?");
    $stmt->execute([$user_id]);
    $houses_count = $stmt->fetch()['count'];

    if ($houses_count > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Cannot delete user because they have associated properties. Please reassign or delete the properties first.']);
        exit;
    }

    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>