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

$property_id = $_POST['property_id'] ?? '';

if (empty($property_id) || !is_numeric($property_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid property ID']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Delete related requests
    $stmt = $pdo->prepare("DELETE FROM requests WHERE house_id = ?");
    $stmt->execute([$property_id]);

    // Delete related messages
    $stmt = $pdo->prepare("DELETE FROM messages WHERE house_id = ?");
    $stmt->execute([$property_id]);

    // Delete related favorites
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE house_id = ?");
    $stmt->execute([$property_id]);

    // Delete the property
    $stmt = $pdo->prepare("DELETE FROM houses WHERE id = ?");
    $stmt->execute([$property_id]);

    if ($stmt->rowCount() > 0) {
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Property deleted successfully']);
    } else {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Property not found']);
    }
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>