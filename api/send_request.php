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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['landlord_id']) || !isset($data['house_id'])) {
        throw new Exception('Landlord ID and house ID are required');
    }

    // Insert request
    $stmt = $pdo->prepare("
        INSERT INTO requests (house_hunter_id, landlord_id, house_id, request_type, status, requested_date, requested_time, message, created_at)
        VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        $data['landlord_id'],
        $data['house_id'],
        $data['request_type'] ?? 'viewing',
        $data['requested_date'] ?? null,
        $data['requested_time'] ?? null,
        $data['message'] ?? null
    ]);

    $request_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Request sent successfully',
        'request_id' => $request_id
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>