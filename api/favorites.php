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
    // Get user's favorites
    try {
        $stmt = $pdo->prepare("
            SELECT f.*, h.title, h.price, h.location, h.image_url_1, h.bedrooms, h.bathrooms
            FROM favorites f
            JOIN houses h ON f.house_id = h.id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $favorites]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} elseif ($method === 'POST') {
    // Add or remove favorite
    $input = json_decode(file_get_contents('php://input'), true);
    $house_id = $input['house_id'] ?? null;
    $action = $input['action'] ?? 'add'; // 'add' or 'remove'

    if (!$house_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'House ID required']);
        exit;
    }

    try {
        if ($action === 'add') {
            // Check if already favorited
            $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND house_id = ?");
            $stmt->execute([$user_id, $house_id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => true, 'message' => 'Already favorited']);
                exit;
            }

            // Add favorite
            $stmt = $pdo->prepare("INSERT INTO favorites (user_id, house_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $house_id]);
            echo json_encode(['success' => true, 'message' => 'Added to favorites']);
        } elseif ($action === 'remove') {
            // Remove favorite
            $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND house_id = ?");
            $stmt->execute([$user_id, $house_id]);
            echo json_encode(['success' => true, 'message' => 'Removed from favorites']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} elseif ($method === 'DELETE') {
    // Remove favorite (alternative method)
    $house_id = $_GET['house_id'] ?? null;

    if (!$house_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'House ID required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND house_id = ?");
        $stmt->execute([$user_id, $house_id]);
        echo json_encode(['success' => true, 'message' => 'Removed from favorites']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>