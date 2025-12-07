CREATE TABLE IF NOT EXISTS teas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    tea_id INT,
    booking_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tea_id) REFERENCES teas(id)
);

INSERT INTO teas (name, description, price) VALUES
('Earl Grey', 'Black tea flavored with bergamot oil', 5.50),
('Matcha Green Tea', 'Finely ground powder of specially grown and processed green tea leaves', 6.00),
('Oolong', 'Traditional semi-oxidized Chinese tea', 5.75);
