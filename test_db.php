<?php
require_once 'db.php';

try {
    // Test database connection
    echo "Database connection successful!<br>";

    // Test if tables exist
    $tables = ['users', 'areas', 'houses', 'filters', 'categories', 'property_types'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() > 0) {
            echo "Table '$table' exists.<br>";
        } else {
            echo "Table '$table' does not exist.<br>";
        }
    }

    // Test API endpoints
    echo "<br>Testing API endpoints:<br>";

    // Test filters API
    $response = file_get_contents('http://localhost/movers%20syste/api/filters.php');
    if ($response) {
        $data = json_decode($response, true);
        if ($data['success']) {
            echo "Filters API: " . count($data['data']) . " filters loaded.<br>";
        } else {
            echo "Filters API error: " . $data['error'] . "<br>";
        }
    } else {
        echo "Filters API not accessible.<br>";
    }

    // Test areas API
    $response = file_get_contents('http://localhost/movers%20syste/api/areas.php');
    if ($response) {
        $data = json_decode($response, true);
        if ($data['success']) {
            echo "Areas API: " . count($data['data']) . " areas loaded.<br>";
        } else {
            echo "Areas API error: " . $data['error'] . "<br>";
        }
    } else {
        echo "Areas API not accessible.<br>";
    }

    // Test featured houses API
    $response = file_get_contents('http://localhost/movers%20syste/api/featured_houses.php');
    if ($response) {
        $data = json_decode($response, true);
        if ($data['success']) {
            echo "Featured houses API: " . count($data['data']) . " houses loaded.<br>";
        } else {
            echo "Featured houses API error: " . $data['error'] . "<br>";
        }
    } else {
        echo "Featured houses API not accessible.<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>