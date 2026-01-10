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

$category_id = $_POST['category_id'] ?? '';

if (empty($category_id) || !is_numeric($category_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid category ID']);
    exit;
}

try {
    // Check if category has associated areas or houses
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM areas WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $areas_count = $stmt->fetch()['count'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM houses WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $houses_count = $stmt->fetch()['count'];

    if ($areas_count > 0 || $houses_count > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Cannot delete category because it has associated areas or houses. Please reassign them first.']);
        exit;
    }

    // Delete category
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Category not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>