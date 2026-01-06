<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../db.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

try {
    if ($filter === 'all') {
        $stmt = $pdo->prepare("SELECT * FROM areas ORDER BY id");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT a.* FROM areas a JOIN categories c ON a.category_id = c.id WHERE c.slug = ? ORDER BY a.id");
        $stmt->execute([$filter]);
    }
    $areas = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $areas
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>