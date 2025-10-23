-- Database schema for Nail Studio Andreea
-- Create database
CREATE DATABASE IF NOT EXISTS nail_studio_andreea CHARACTER SET utf8 COLLATE utf8_general_ci;
USE nail_studio_andreea;

-- Table for services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in minutes',
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for appointments
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(100) NOT NULL,
    client_phone VARCHAR(20) NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Table for gallery
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for coaching sessions
CREATE TABLE coaching_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_name VARCHAR(100) NOT NULL,
    description TEXT,
    long_description TEXT,
    benefits TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in hours',
    max_participants INT DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for coaching bookings
CREATE TABLE coaching_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(100) NOT NULL,
    client_phone VARCHAR(20) NOT NULL,
    session_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES coaching_sessions(id)
);

-- Table for contact messages
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for admin users
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, email, password, full_name) VALUES 
('admin', 'admin@nailstudio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert sample services
INSERT INTO services (name, description, price, duration, image) VALUES 
('Manichiura Clasica', 'Manichiura clasica cu lac normal', 50.00, 60, 'manicure_classic.jpg'),
('Manichiura cu Gel', 'Manichiura cu gel rezistent, durabilitate 2-3 saptamani', 80.00, 90, 'manicure_gel.jpg'),
('Pedichiura', 'Pedichiura completa cu ingrijire si lac', 70.00, 75, 'pedicure.jpg'),
('Extensii Unghii', 'Extensii cu gel sau acril', 120.00, 120, 'extensions.jpg'),
('Nail Art', 'Decoratiuni artistice pentru unghii', 100.00, 90, 'nail_art.jpg');

-- Insert sample coaching sessions
INSERT INTO coaching_sessions (session_name, description, price, duration, max_participants) VALUES 
('Curs Initiere Manichiura', 'Curs de baza pentru incepatori', 300.00, 6, 5),
('Curs Avansат Nail Art', 'Tehnici avansate de decorare unghii', 500.00, 8, 3),
('Workshop Extensii Gel', 'Invatarea tehnicilor de extensii cu gel', 400.00, 4, 4);

-- Insert sample gallery items
INSERT INTO gallery (title, description, image, is_featured) VALUES 
('Nail Art Floral', 'Design floral delicat', 'gallery_1.jpg', TRUE),
('Manichiura Eleganta', 'Stil elegant pentru evenimente', 'gallery_2.jpg', TRUE),
('Extensii Naturale', 'Look natural cu extensii', 'gallery_3.jpg', FALSE),
('Pedichiura Vara', 'Culori vii pentru vara', 'gallery_4.jpg', FALSE);
