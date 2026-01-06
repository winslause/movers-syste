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
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['id'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }

    $house_id = (int)$input['id'];

    // Check if the house belongs to the user
    $stmt = $pdo->prepare("SELECT landlord_id FROM houses WHERE id = ?");
    $stmt->execute([$house_id]);
    $house = $stmt->fetch();

    if (!$house || $house['landlord_id'] != $user_id) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    // Delete the house
    $stmt = $pdo->prepare("DELETE FROM houses WHERE id = ?");
    $result = $stmt->execute([$house_id]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Property deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete property']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>