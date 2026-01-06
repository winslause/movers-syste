<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM filters ORDER BY id");
    $stmt->execute();
    $filters = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $filters
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>