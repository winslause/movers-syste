<?php
require_once 'db.php';

try {
    // Add admin to user_type enum
    $pdo->exec("ALTER TABLE users MODIFY COLUMN user_type ENUM('seeker', 'landlord', 'mover', 'admin') NOT NULL DEFAULT 'seeker'");

    // Insert admin user if not exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@rheaspark.com']);
    if (!$stmt->fetch()) {
        $password_hash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, user_type, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'User', 'admin@rheaspark.com', '+254700000000', $password_hash, 'admin', 1]);
        echo "Admin user inserted.\n";
    } else {
        echo "Admin user already exists.\n";
    }

    // Add missing columns
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS bio TEXT");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS address VARCHAR(255)");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS city VARCHAR(100)");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS postal_code VARCHAR(20)");

    // Add landlord_id to houses if not exists
    $stmt = $pdo->query("SHOW COLUMNS FROM houses LIKE 'landlord_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE houses ADD COLUMN landlord_id INT, ADD FOREIGN KEY (landlord_id) REFERENCES users(id)");
    }

    // Create messages table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        house_id INT,
        message TEXT NOT NULL,
        message_type ENUM('inquiry', 'reply', 'general') DEFAULT 'general',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id),
        FOREIGN KEY (receiver_id) REFERENCES users(id),
        FOREIGN KEY (house_id) REFERENCES houses(id)
    )");

    // Create requests table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS requests (
        id INT PRIMARY KEY AUTO_INCREMENT,
        house_hunter_id INT NOT NULL,
        landlord_id INT NOT NULL,
        house_id INT NOT NULL,
        request_type ENUM('viewing', 'inquiry') DEFAULT 'viewing',
        status ENUM('pending', 'approved', 'declined', 'completed') DEFAULT 'pending',
        requested_date DATE,
        requested_time TIME,
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (house_hunter_id) REFERENCES users(id),
        FOREIGN KEY (landlord_id) REFERENCES users(id),
        FOREIGN KEY (house_id) REFERENCES houses(id)
    )");

    // Create contact_messages table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Database updates completed.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>