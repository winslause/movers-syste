<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        throw new Exception('All fields are required');
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    // Insert contact message
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, subject, message, status, created_at)
        VALUES (?, ?, ?, ?, 'unread', NOW())
    ");

    $stmt->execute([$name, $email, $subject, $message]);

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