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
    // Delete property
    $stmt = $pdo->prepare("DELETE FROM houses WHERE id = ?");
    $stmt->execute([$property_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Property deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Property not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>