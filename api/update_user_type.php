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

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['user_type']) && in_array($data['user_type'], ['landlord', 'seeker'])) {
    $stmt = $pdo->prepare("UPDATE users SET user_type = ? WHERE id = ?");
    $stmt->execute([$data['user_type'], $user_id]);

    // Update session
    $_SESSION['user_type'] = $data['user_type'];

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid user type']);
}
?>