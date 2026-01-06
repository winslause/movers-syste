<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        // Get messages for the user (sent and received)
        $stmt = $pdo->prepare("
            SELECT m.*,
                   sender.first_name as sender_first_name, sender.last_name as sender_last_name,
                   receiver.first_name as receiver_first_name, receiver.last_name as receiver_last_name,
                   h.title as house_title, h.location as house_location
            FROM messages m
            LEFT JOIN users sender ON m.sender_id = sender.id
            LEFT JOIN users receiver ON m.receiver_id = receiver.id
            LEFT JOIN houses h ON m.house_id = h.id
            WHERE m.sender_id = ? OR m.receiver_id = ?
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$user_id, $user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $messages]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} elseif ($method === 'DELETE') {
    // Delete a message
    $input = json_decode(file_get_contents('php://input'), true);
    $message_id = $input['message_id'] ?? null;

    if (!$message_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Message ID required']);
        exit;
    }

    try {
        // Check if the message belongs to the user
        $stmt = $pdo->prepare("SELECT id FROM messages WHERE id = ? AND (sender_id = ? OR receiver_id = ?)");
        $stmt->execute([$message_id, $user_id, $user_id]);
        $message = $stmt->fetch();

        if (!$message) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Message not found or not authorized']);
            exit;
        }

        // Delete the message
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$message_id]);

        echo json_encode(['success' => true, 'message' => 'Message deleted successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>