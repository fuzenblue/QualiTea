CREATE TABLE IF NOT EXISTS teas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'General',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    queue_number INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    note TEXT,
    status ENUM('WAITING', 'IN_PROGRESS', 'DONE', 'CANCELED') DEFAULT 'WAITING',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO teas (name, description, price, category) VALUES
('Earl Grey', 'Black tea flavored with bergamot oil', 45.00, 'Classic'),
('Matcha Green Tea', 'Finely ground powder of green tea', 60.00, 'Premium'),
('Oolong', 'Semi-oxidized Chinese tea', 50.00, 'Classic'),
('Thai Tea', 'Sweet and creamy Thai style tea', 40.00, 'Sweet');
