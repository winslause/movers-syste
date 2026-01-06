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

    if (!$data || !isset($data['receiver_id']) || !isset($data['message'])) {
        throw new Exception('Receiver ID and message are required');
    }

    // Insert message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, house_id, message, message_type, is_read, created_at)
        VALUES (?, ?, ?, ?, ?, FALSE, NOW())
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        $data['receiver_id'],
        $data['house_id'] ?? null,
        $data['message'],
        $data['message_type'] ?? 'general'
    ]);

    $message_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully',
        'message_id' => $message_id
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>