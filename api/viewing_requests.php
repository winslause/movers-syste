<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
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
        if (isset($_GET['landlord'])) {
            // Get requests for landlord's properties
            $stmt = $pdo->prepare("
                SELECT r.*, h.title, h.price, h.location, h.image_url_1, h.bedrooms, h.bathrooms,
                       u.first_name, u.last_name, u.email, u.phone
                FROM requests r
                JOIN houses h ON r.house_id = h.id
                JOIN users u ON r.house_hunter_id = u.id
                WHERE h.landlord_id = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$user_id]);
        } else {
            // Get user's viewing requests
            $stmt = $pdo->prepare("
                SELECT r.*, h.title, h.price, h.location, h.image_url_1, h.bedrooms, h.bathrooms,
                       u.first_name, u.last_name, u.email, u.phone
                FROM requests r
                JOIN houses h ON r.house_id = h.id
                JOIN users u ON r.landlord_id = u.id
                WHERE r.house_hunter_id = ? AND r.request_type = 'viewing'
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$user_id]);
        }
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $requests]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} elseif ($method === 'POST') {
    // Create viewing request
    $input = json_decode(file_get_contents('php://input'), true);

    $house_id = $input['house_id'] ?? null;
    $full_name = $input['full_name'] ?? null;
    $phone = $input['phone'] ?? null;
    $preferred_date = $input['preferred_date'] ?? null;
    $message = $input['message'] ?? null;

    if (!$house_id || !$full_name || !$phone || !$preferred_date) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All required fields must be provided']);
        exit;
    }

    try {
        // Get landlord_id from house
        $stmt = $pdo->prepare("SELECT landlord_id FROM houses WHERE id = ?");
        $stmt->execute([$house_id]);
        $house = $stmt->fetch();

        if (!$house) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'House not found']);
            exit;
        }

        $landlord_id = $house['landlord_id'];

        // Create viewing request
        $stmt = $pdo->prepare("
            INSERT INTO requests (house_hunter_id, landlord_id, house_id, request_type, message, status)
            VALUES (?, ?, ?, 'viewing', ?, 'pending')
        ");

        $request_message = "Viewing request from $full_name ($phone)\nPreferred date: $preferred_date\n" . ($message ? "Message: $message" : "");

        $stmt->execute([$user_id, $landlord_id, $house_id, $request_message]);

        echo json_encode(['success' => true, 'message' => 'Viewing request submitted successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} elseif ($method === 'DELETE') {
    // Delete viewing request
    $input = json_decode(file_get_contents('php://input'), true);
    $request_id = $input['request_id'] ?? null;

    if (!$request_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Request ID required']);
        exit;
    }

    try {
        // Check if the request belongs to the user (house hunter) or landlord
        $stmt = $pdo->prepare("SELECT id FROM requests WHERE id = ? AND (house_hunter_id = ? OR landlord_id = ?)");
        $stmt->execute([$request_id, $user_id, $user_id]);
        $request = $stmt->fetch();

        if (!$request) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Request not found or not authorized']);
            exit;
        }

        // Delete the request
        $stmt = $pdo->prepare("DELETE FROM requests WHERE id = ?");
        $stmt->execute([$request_id]);

        echo json_encode(['success' => true, 'message' => 'Request deleted successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>