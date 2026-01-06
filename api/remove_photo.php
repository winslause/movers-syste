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

// Get current photo to delete file
$stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user && $user['profile_image']) {
    $filepath = __DIR__ . '/../uploads/profiles/' . $user['profile_image'];
    if (file_exists($filepath)) {
        unlink($filepath);
    }
}

// Update database
$stmt = $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
$stmt->execute([$user_id]);

echo json_encode(['success' => true]);
?>