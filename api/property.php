<?php
require_once '../db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $property_id = $_GET['id'] ?? null;

    if (!$property_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Property ID required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM houses WHERE id = ?");
        $stmt->execute([$property_id]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($property) {
            echo json_encode(['success' => true, 'data' => $property]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Property not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>