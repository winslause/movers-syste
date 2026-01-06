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
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Email and password are required');
    }

    // Get user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Invalid email or password');
    }

    // Verify password
    if (!password_verify($data['password'], $user['password_hash'])) {
        throw new Exception('Invalid email or password');
    }

    // Check if email is verified
    if (!$user['email_verified']) {
        throw new Exception('Please verify your email before logging in');
    }

    // Start session
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];

    // Determine redirect URL based on user type
    $redirect_url = 'index.php';
    if ($user['user_type'] === 'admin') {
        $redirect_url = 'admin.php';
    }

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect_url' => $redirect_url,
        'user' => [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'user_type' => $user['user_type']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>