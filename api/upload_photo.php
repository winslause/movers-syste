<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 0);
error_reporting(0);

session_start();
require_once __DIR__ . '/../db.php';

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
        $file = $_FILES['photo'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'Upload error: ' . $file['error']]);
            exit;
        }

        // Check file size (2MB max)
        if ($file['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'error' => 'File too large. Max 2MB']);
            exit;
        }

        // Check file type
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPEG, PNG, GIF, WebP allowed']);
            exit;
        }

        // Get file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid file extension']);
            exit;
        }

        // Generate unique filename
        $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
        $upload_dir = __DIR__ . '/../uploads/profiles/';

        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database
            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $result = $stmt->execute([$filename, $user_id]);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'photo_url' => 'uploads/profiles/' . $filename
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update database']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to save file']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>