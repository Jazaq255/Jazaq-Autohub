-- Create database
CREATE DATABASE IF NOT EXISTS car-dealership;
USE car-dealership;

-- Users table for registration/login
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cars table for inventory
CREATE TABLE IF NOT EXISTS cars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    mileage INT,
    fuel_type VARCHAR(20),
    transmission VARCHAR(20),
    color VARCHAR(30),
    description TEXT,
    image_url VARCHAR(255) DEFAULT 'default_car.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Insert sample data
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@dealership.com', '$2y$10$YourHashedPasswordHere', 'admin'),
('john', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'user');

-- Note: In real project, use password_hash() in PHP to hash passwords

INSERT INTO cars (make, model, year, price, mileage, fuel_type, transmission, color, description) VALUES
('Toyota', 'Camry', 2022, 25000.00, 15000, 'Petrol', 'Automatic', 'White', 'Excellent condition, one owner'),
('Honda', 'Civic', 2021, 22000.00, 20000, 'Petrol', 'Manual', 'Blue', 'Well maintained, low mileage'),
('Ford', 'Mustang', 2020, 35000.00, 12000, 'Petrol', 'Automatic', 'Red', 'Powerful V8 engine');