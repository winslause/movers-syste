-- Database updates for househunter and landlord messaging system

USE movers_system;

-- Add admin to user_type enum
ALTER TABLE users MODIFY COLUMN user_type ENUM('seeker', 'landlord', 'mover', 'admin') NOT NULL DEFAULT 'seeker';

-- Insert admin user
INSERT INTO users (first_name, last_name, email, phone, password_hash, user_type, email_verified) VALUES
('Admin', 'User', 'admin@rheaspark.com', '+254700000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', TRUE);

-- Add missing columns to users table
ALTER TABLE users ADD COLUMN bio TEXT;
ALTER TABLE users ADD COLUMN address VARCHAR(255);
ALTER TABLE users ADD COLUMN city VARCHAR(100);
ALTER TABLE users ADD COLUMN postal_code VARCHAR(20);

-- Add landlord_id to houses table if not exists
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'movers_system' AND TABLE_NAME = 'houses' AND COLUMN_NAME = 'landlord_id') = 0,
    'ALTER TABLE houses ADD COLUMN landlord_id INT, ADD FOREIGN KEY (landlord_id) REFERENCES users(id)',
    'SELECT "landlord_id column already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create messages table for communication between house hunters and landlords
CREATE TABLE messages (
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
);

-- Create requests table for viewing requests
CREATE TABLE requests (
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
);

-- Insert sample users for testing
INSERT INTO users (first_name, last_name, email, phone, password_hash, user_type, email_verified) VALUES
('John', 'Mwangi', 'john@example.com', '+254712345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'landlord', TRUE),
('Jane', 'Muthoni', 'jane@example.com', '+254723456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seeker', TRUE),
('Peter', 'Omondi', 'peter@example.com', '+254734567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seeker', TRUE);

-- Update existing houses to have landlord_id
UPDATE houses SET landlord_id = 2 WHERE id > 0;

-- Insert sample messages
INSERT INTO messages (sender_id, receiver_id, house_id, message, message_type, is_read) VALUES
(3, 2, 1, 'Hi, I am interested in your Westlands apartment. Can we schedule a viewing?', 'inquiry', FALSE),
(2, 3, 1, 'Hello! Yes, I would be happy to show you the apartment. What day works best for you?', 'reply', FALSE),
(4, 2, 2, 'Is the Kilimani suite still available? I would like to view it this weekend.', 'inquiry', TRUE);

-- Create contact_messages table for contact form submissions
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample requests
INSERT INTO requests (house_hunter_id, landlord_id, house_id, request_type, status, requested_date, requested_time, message) VALUES
(3, 2, 1, 'viewing', 'pending', '2024-01-15', '14:00:00', 'Interested in viewing the Westlands apartment'),
(4, 2, 2, 'viewing', 'approved', '2024-01-20', '10:00:00', 'Would like to see the Kilimani suite'),
(3, 2, 3, 'inquiry', 'pending', NULL, NULL, 'General inquiry about availability');