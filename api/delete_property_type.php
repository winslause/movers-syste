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

$property_type_id = $_POST['property_type_id'] ?? '';

if (empty($property_type_id) || !is_numeric($property_type_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid property type ID']);
    exit;
}

try {
    // Check if property type has associated houses
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM houses WHERE property_type = (SELECT name FROM property_types WHERE id = ?)");
    $stmt->execute([$property_type_id]);
    $houses_count = $stmt->fetch()['count'];

    if ($houses_count > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Cannot delete property type because it has associated houses. Please reassign them first.']);
        exit;
    }

    // Delete property type
    $stmt = $pdo->prepare("DELETE FROM property_types WHERE id = ?");
    $stmt->execute([$property_type_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Property type deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Property type not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>