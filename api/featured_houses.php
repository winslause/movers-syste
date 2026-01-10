<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$filter = isset($_GET['property_type']) ? $_GET['property_type'] : 'all';

try {
    if ($id) {
        $stmt = $pdo->prepare("SELECT h.*, a.name as area_name FROM houses h LEFT JOIN areas a ON h.area_id = a.id WHERE h.id = ?");
        $stmt->execute([$id]);
        $houses = $stmt->fetchAll();
    } elseif ($filter === 'all') {
        $stmt = $pdo->prepare("SELECT h.*, a.name as area_name FROM houses h LEFT JOIN areas a ON h.area_id = a.id ORDER BY h.created_at DESC LIMIT 6");
        $stmt->execute();
        $houses = $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare("SELECT h.*, a.name as area_name FROM houses h LEFT JOIN areas a ON h.area_id = a.id WHERE h.property_type = ? ORDER BY h.created_at DESC LIMIT 6");
        $stmt->execute([$filter]);
        $houses = $stmt->fetchAll();
    }

    echo json_encode([
        'success' => true,
        'data' => $houses
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>