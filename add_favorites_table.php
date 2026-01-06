<?php
require_once 'db.php';

try {
    // Add favorites table
    $sql = "
    CREATE TABLE IF NOT EXISTS favorites (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        house_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (house_id) REFERENCES houses(id),
        UNIQUE KEY unique_favorite (user_id, house_id)
    );
    ";

    $pdo->exec($sql);
    echo "Favorites table created successfully!<br>";

    // Check if table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'favorites'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "Table 'favorites' exists and is ready to use.<br>";
    } else {
        echo "Error: Table 'favorites' was not created.<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>