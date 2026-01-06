-- Database setup for Movers System

CREATE DATABASE IF NOT EXISTS movers_system;
USE movers_system;

-- Categories table
DROP TABLE IF EXISTS houses;
DROP TABLE IF EXISTS areas;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS filters;

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(20) DEFAULT '#2FA4E7',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Property Types table
CREATE TABLE property_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(20) DEFAULT '#2FA4E7',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    user_type ENUM('seeker', 'landlord', 'mover') NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_expires DATETIME,
    preferred_locations TEXT,
    max_budget DECIMAL(10,2),
    property_count VARCHAR(20),
    primary_location VARCHAR(255),
    company_name VARCHAR(255),
    service_areas TEXT,
    profile_image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Areas table (used for houses)
CREATE TABLE areas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    image_url_1 VARCHAR(500),
    image_url_2 VARCHAR(500),
    image_url_3 VARCHAR(500),
    description TEXT,
    avg_rent_min INT,
    avg_rent_max INT,
    location VARCHAR(100),
    listings_count INT DEFAULT 0,
    verified BOOLEAN DEFAULT TRUE,
    color VARCHAR(20) DEFAULT '#2FA4E7',
    landlord_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (landlord_id) REFERENCES users(id)
);

-- Houses table
CREATE TABLE houses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    security_deposit DECIMAL(10,2),
    available_from DATE DEFAULT (CURRENT_DATE),
    location VARCHAR(100) NOT NULL,
    area_id INT,
    category_id INT,
    landlord_id INT,
    property_type VARCHAR(50) DEFAULT 'apartment', -- apartment, house, etc.
    bedrooms INT,
    bathrooms INT,
    size_sqft INT, -- converted from sqm
    parking_spaces INT DEFAULT 0,
    -- Amenities
    wifi BOOLEAN DEFAULT FALSE,
    swimming_pool BOOLEAN DEFAULT FALSE,
    gym BOOLEAN DEFAULT FALSE,
    security_24_7 BOOLEAN DEFAULT FALSE,
    pet_friendly BOOLEAN DEFAULT FALSE,
    dedicated_parking BOOLEAN DEFAULT FALSE,
    -- Images (3 images per house)
    image_url_1 VARCHAR(500),
    image_url_2 VARCHAR(500),
    image_url_3 VARCHAR(500),
    verified BOOLEAN DEFAULT TRUE,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (area_id) REFERENCES areas(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (landlord_id) REFERENCES users(id)
);

-- Requests table (house hunters requesting from landlords)
CREATE TABLE requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    house_hunter_id INT NOT NULL,
    landlord_id INT NOT NULL,
    house_id INT,
    area_id INT,
    request_type ENUM('inquiry', 'application', 'visit') DEFAULT 'inquiry',
    message TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (house_hunter_id) REFERENCES users(id),
    FOREIGN KEY (landlord_id) REFERENCES users(id),
    FOREIGN KEY (house_id) REFERENCES houses(id),
    FOREIGN KEY (area_id) REFERENCES areas(id)
);

-- Messages table (messaging between house hunters and landlords)
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    house_id INT,
    area_id INT,
    message TEXT NOT NULL,
    reply_to INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id),
    FOREIGN KEY (house_id) REFERENCES houses(id),
    FOREIGN KEY (area_id) REFERENCES areas(id),
    FOREIGN KEY (reply_to) REFERENCES messages(id)
);

-- Favorites table (house hunters saving properties)
CREATE TABLE favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    house_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (house_id) REFERENCES houses(id),
    UNIQUE KEY unique_favorite (user_id, house_id)
);

-- Filters table (for filter categories) - keeping for backward compatibility
CREATE TABLE filters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL -- all, premium, affordable, family, student
);

-- Insert sample data for categories
INSERT INTO categories (name, slug, description, color) VALUES
('All Areas', 'all', 'All rental areas in Nairobi', '#2FA4E7'),
('Premium', 'premium', 'High-end luxury residential areas', '#2FA4E7'),
('Affordable', 'affordable', 'Budget-friendly housing options', '#3CB371'),
('Family-Friendly', 'family', 'Perfect for families with children', '#3CB371'),
('Student Areas', 'student', 'Convenient for students and young professionals', '#2FA4E7');

-- Insert sample data for property_types
INSERT INTO property_types (name, slug, description, color) VALUES
('Apartment', 'apartment', 'Modern apartment buildings', '#2FA4E7'),
('House', 'house', 'Standalone houses', '#3CB371'),
('Studio', 'studio', 'Single room apartments', '#2FA4E7'),
('Bedsitter', 'bedsitter', 'Small single room units', '#3CB371');

-- Insert sample data for areas
INSERT INTO areas (name, category_id, image_url, description, avg_rent_min, avg_rent_max, location, listings_count, color) VALUES
('Westlands', 2, 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'Nairobi\'s premier business and entertainment district, known for its modern apartments, shopping malls, and vibrant nightlife.', 85000, 200000, 'Nairobi • Business District', 3, '#2FA4E7'),
('Kilimani', 2, 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'An upscale residential area with modern apartments, excellent security, and convenient access to the city center.', 65000, 150000, 'Nairobi • Upscale Residential', 2, '#2FA4E7'),
('Kileleshwa', 4, 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'A quiet, leafy suburb perfect for families, with spacious homes, good schools, and a peaceful environment.', 120000, 300000, 'Nairobi • Quiet Suburb', 2, '#3CB371'),
('Roysambu', 3, 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'A rapidly growing area with affordable housing options, good transport links, and developing infrastructure.', 35000, 80000, 'Nairobi • Growing Area', 2, '#3CB371'),
('South B', 5, 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'Popular with students and young adults due to proximity to universities, affordable rents, and youthful vibe.', 25000, 60000, 'Nairobi • Near Universities', 2, '#2FA4E7'),
('Lavington', 2, 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'One of Nairobi\'s most prestigious neighborhoods, featuring luxurious homes, diplomatic residences, and exclusive amenities.', 150000, 500000, 'Nairobi • High-End Residential', 2, '#2FA4E7'),
('Karen', 2, 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'An exclusive residential area known for its luxury homes, golf courses, and proximity to major shopping centers.', 200000, 800000, 'Nairobi • Luxury Residential', 2, '#2FA4E7'),
('Parklands', 4, 'https://images.unsplash.com/photo-1449844908441-8829872d2607?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'A family-friendly neighborhood with tree-lined streets, good schools, and a mix of modern and traditional homes.', 80000, 250000, 'Nairobi • Family Area', 2, '#3CB371'),
('Eastlands', 3, 'https://images.unsplash.com/photo-1484154218962-a197022b5858?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'An up-and-coming area with affordable housing, growing commercial developments, and excellent transport links.', 20000, 50000, 'Nairobi • Emerging Area', 2, '#3CB371'),
('Langata', 2, 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'A prestigious area featuring high-end residences, international schools, and proximity to Nairobi National Park.', 180000, 600000, 'Nairobi • Exclusive District', 2, '#2FA4E7'),
('Umoja', 3, 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'A well-planned residential estate offering affordable housing with modern amenities and community facilities.', 15000, 35000, 'Nairobi • Planned Estate', 2, '#3CB371'),
('Riverside', 2, 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'A modern development along the river with contemporary apartments, shopping centers, and recreational facilities.', 70000, 180000, 'Nairobi • Modern Development', 2, '#2FA4E7');

-- Insert sample data for houses (featured ones for hero section and regular listings)
INSERT INTO houses (
    title, description, price, security_deposit, available_from, location, area_id, category_id,
    property_type, bedrooms, bathrooms, size_sqft, parking_spaces,
    wifi, swimming_pool, gym, security_24_7, pet_friendly, dedicated_parking,
    image_url_1, image_url_2, image_url_3, verified, featured
) VALUES
-- Featured houses for hero section
('Spacious 3-Bedroom Apartment', 'Modern apartment with great amenities including gym, pool, and 24/7 security', 85000, 85000, '2024-01-01', 'Westlands, Nairobi', 1, 2, 'apartment', 3, 2, 1292, 1, TRUE, TRUE, TRUE, TRUE, FALSE, TRUE,
 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1073&q=80',
 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, TRUE),

('Contemporary Family Home', 'Beautiful family home in quiet area with garden and parking', 120000, 120000, '2024-01-01', 'Kileleshwa, Nairobi', 3, 4, 'house', 4, 3, 2691, 2, TRUE, FALSE, FALSE, TRUE, TRUE, TRUE,
 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80',
 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1449844908441-8829872d2607?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, TRUE),

('Luxury 2-Bedroom Suite', 'Luxurious suite with city views and modern finishes', 65000, 65000, '2024-01-01', 'Kilimani, Nairobi', 2, 2, 'apartment', 2, 2, 1023, 1, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE,
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80',
 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, TRUE),

-- Additional houses for Westlands (Premium)
('Executive 1-Bedroom Apartment', 'Perfect for professionals, fully furnished with city center access', 55000, 55000, '2024-01-15', 'Westlands, Nairobi', 1, 2, 'apartment', 1, 1, 700, 1, TRUE, TRUE, TRUE, TRUE, FALSE, TRUE,
 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

('Penthouse with Terrace', 'Stunning penthouse with private terrace and panoramic views', 180000, 180000, '2024-02-01', 'Westlands, Nairobi', 1, 2, 'apartment', 3, 3, 2153, 2, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE,
 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1449844908441-8829872d2607?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

-- Houses for Kilimani (Premium)
('Modern 2BR with Balcony', 'Contemporary apartment with balcony and modern amenities', 70000, 70000, '2024-01-10', 'Kilimani, Nairobi', 2, 2, 'apartment', 2, 2, 969, 1, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE,
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

('Garden Apartment Complex', 'Secure complex with garden views and community facilities', 75000, 75000, '2024-01-20', 'Kilimani, Nairobi', 2, 2, 'apartment', 2, 2, 1076, 1, TRUE, TRUE, TRUE, TRUE, FALSE, TRUE,
 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

-- Houses for Kileleshwa (Family-Friendly)
('Spacious 5BR Family Home', 'Large family home with garden, perfect for growing families', 200000, 200000, '2024-02-15', 'Kileleshwa, Nairobi', 3, 4, 'house', 5, 4, 3767, 3, TRUE, FALSE, FALSE, TRUE, TRUE, TRUE,
 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1449844908441-8829872d2607?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

('Townhouse with Garage', 'Modern townhouse with double garage and private garden', 150000, 150000, '2024-01-25', 'Kileleshwa, Nairobi', 3, 4, 'house', 4, 3, 3014, 2, TRUE, FALSE, TRUE, TRUE, TRUE, TRUE,
 'https://images.unsplash.com/photo-1449844908441-8829872d2607?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

-- Houses for Roysambu (Affordable)
('Affordable 1BR Starter', 'Perfect first home with basic amenities and good location', 25000, 25000, '2024-01-05', 'Roysambu, Nairobi', 4, 3, 'apartment', 1, 1, 538, 0, TRUE, FALSE, FALSE, TRUE, FALSE, FALSE,
 'https://images.unsplash.com/photo-1484154218962-a197022b5858?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

('2BR Family Unit', 'Comfortable family apartment with balcony and parking', 40000, 40000, '2024-01-12', 'Roysambu, Nairobi', 4, 3, 'apartment', 2, 2, 915, 1, TRUE, FALSE, FALSE, TRUE, TRUE, TRUE,
 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1484154218962-a197022b5858?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

-- Houses for South B (Student Areas)
('Student Shared Apartment', 'Affordable shared accommodation near universities', 15000, 15000, '2024-01-08', 'South B, Nairobi', 5, 5, 'apartment', 1, 1, 430, 0, TRUE, FALSE, FALSE, FALSE, TRUE, FALSE,
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE),

('Modern Student Housing', 'Contemporary student accommodation with study areas', 20000, 20000, '2024-01-18', 'South B, Nairobi', 5, 5, 'apartment', 1, 1, 484, 0, TRUE, FALSE, TRUE, TRUE, TRUE, FALSE,
 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', TRUE, FALSE);

-- Insert sample data for filters
INSERT INTO filters (name, slug, category) VALUES
('All Areas', 'all', 'all'),
('Premium', 'premium', 'premium'),
('Affordable', 'affordable', 'affordable'),
('Family-Friendly', 'family', 'family'),
('Student Areas', 'student', 'student');