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

$name = trim($_POST['name'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$description = trim($_POST['description'] ?? '');
$color = trim($_POST['color'] ?? '#2FA4E7');

if (empty($name) || empty($slug)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Name and slug are required']);
    exit;
}

// Validate slug format
if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Slug must contain only lowercase letters, numbers, and hyphens']);
    exit;
}

try {
    // Check if slug already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetch()['count'] > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Slug already exists']);
        exit;
    }

    // Insert category
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, color) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $slug, $description, $color]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Category added successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to add category']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>