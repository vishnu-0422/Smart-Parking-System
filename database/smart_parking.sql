-- Smart Parking System Database Schema
-- MySQL Database

-- Create database
CREATE DATABASE IF NOT EXISTS smart_parking;
USE smart_parking;

-- Vehicle Companies Table
CREATE TABLE IF NOT EXISTS vehicle_companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL UNIQUE,
    country VARCHAR(50) NULL,
    founded_year INT NULL,
    logo_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_company_name (company_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vehicle Models Table
CREATE TABLE IF NOT EXISTS vehicle_models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    vehicle_type ENUM('car', 'motorcycle', 'truck', 'van') NOT NULL,
    year_from INT NULL,
    year_to INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES vehicle_companies(id) ON DELETE CASCADE,
    INDEX idx_company_id (company_id),
    INDEX idx_model_name (model_name),
    INDEX idx_vehicle_type (vehicle_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users Table (Application Users)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vehicles Table
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(20) NOT NULL UNIQUE,
    company_id INT NULL,
    model_id INT NULL,
    vehicle_type ENUM('car', 'motorcycle', 'truck', 'van') NOT NULL,
    color VARCHAR(50) NULL,
    owner_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL,
    address TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES vehicle_companies(id) ON DELETE SET NULL,
    FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE SET NULL,
    INDEX idx_vehicle_number (vehicle_number),
    INDEX idx_company_id (company_id),
    INDEX idx_model_id (model_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Parking Slots Table
CREATE TABLE IF NOT EXISTS slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_number VARCHAR(20) NOT NULL UNIQUE,
    slot_type ENUM('car', 'motorcycle', 'truck', 'van') NOT NULL,
    price_per_hour DECIMAL(10, 2) NOT NULL DEFAULT 5.00,
    status ENUM('available', 'occupied', 'maintenance') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_slot_number (slot_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Parking Entry Table (Bookings)
CREATE TABLE IF NOT EXISTS parking_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    slot_id INT NOT NULL,
    entry_time DATETIME NOT NULL,
    expiry_time DATETIME NOT NULL,
    exit_time DATETIME NULL,
    amount_paid DECIMAL(10, 2) DEFAULT 0.00,
    payment_method ENUM('cash', 'card', 'mobile') NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    entry_gate VARCHAR(50) NULL,
    exit_gate VARCHAR(50) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES slots(id) ON DELETE CASCADE,
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_slot_id (slot_id),
    INDEX idx_entry_time (entry_time),
    INDEX idx_exit_time (exit_time),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings Table (Alias for backward compatibility)
CREATE TABLE IF NOT EXISTS bookings LIKE parking_entries;

-- Stolen Vehicles Table (Duplicate Police Database)
CREATE TABLE IF NOT EXISTS stolen_vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(20) NOT NULL,
    company_id INT NULL,
    model_id INT NULL,
    vehicle_type ENUM('car', 'motorcycle', 'truck', 'van') NOT NULL,
    color VARCHAR(50) NULL,
    owner_name VARCHAR(100) NOT NULL,
    owner_phone VARCHAR(20) NULL,
    owner_address TEXT NULL,
    chassis_number VARCHAR(50) NULL,
    engine_number VARCHAR(50) NULL,
    police_case_number VARCHAR(50) NULL,
    police_station VARCHAR(100) NULL,
    reported_by VARCHAR(100) NULL,
    description TEXT NULL,
    status ENUM('active', 'resolved', 'cancelled') NOT NULL DEFAULT 'active',
    reported_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_date TIMESTAMP NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES vehicle_companies(id) ON DELETE SET NULL,
    FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE SET NULL,
    INDEX idx_vehicle_number (vehicle_number),
    INDEX idx_status (status),
    INDEX idx_police_case_number (police_case_number),
    INDEX idx_reported_date (reported_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin Users Table (for future authentication)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NULL,
    full_name VARCHAR(100) NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin Alerts Table
CREATE TABLE IF NOT EXISTS admin_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alert_type ENUM('stolen_vehicle', 'overdue_parking', 'maintenance_required', 'system_error', 'security_breach', 'payment_failed', 'other') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
    related_vehicle_id INT NULL,
    related_slot_id INT NULL,
    related_entry_id INT NULL,
    status ENUM('new', 'acknowledged', 'resolved', 'dismissed') NOT NULL DEFAULT 'new',
    acknowledged_by INT NULL,
    acknowledged_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (related_vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    FOREIGN KEY (related_slot_id) REFERENCES slots(id) ON DELETE SET NULL,
    FOREIGN KEY (related_entry_id) REFERENCES parking_entries(id) ON DELETE SET NULL,
    FOREIGN KEY (acknowledged_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_alert_type (alert_type),
    INDEX idx_severity (severity),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Vehicle Companies (10 companies)
INSERT INTO vehicle_companies (company_name, country, founded_year) VALUES
('Toyota', 'Japan', 1937),
('Honda', 'Japan', 1948),
('Ford', 'USA', 1903),
('BMW', 'Germany', 1916),
('Mercedes-Benz', 'Germany', 1926),
('Volkswagen', 'Germany', 1937),
('Hyundai', 'South Korea', 1967),
('Nissan', 'Japan', 1933),
('Chevrolet', 'USA', 1911),
('Tesla', 'USA', 2003);

-- Insert Vehicle Models (5 models per company = 50 models)
INSERT INTO vehicle_models (company_id, model_name, vehicle_type, year_from, year_to) VALUES
-- Toyota Models
(1, 'Camry', 'car', 1982, NULL),
(1, 'Corolla', 'car', 1966, NULL),
(1, 'RAV4', 'car', 1994, NULL),
(1, 'Hilux', 'truck', 1968, NULL),
(1, 'Hiace', 'van', 1967, NULL),
-- Honda Models
(2, 'Civic', 'car', 1972, NULL),
(2, 'Accord', 'car', 1976, NULL),
(2, 'CR-V', 'car', 1995, NULL),
(2, 'CBR600RR', 'motorcycle', 2003, NULL),
(2, 'Activa', 'motorcycle', 2001, NULL),
-- Ford Models
(3, 'F-150', 'truck', 1948, NULL),
(3, 'Mustang', 'car', 1964, NULL),
(3, 'Explorer', 'car', 1990, NULL),
(3, 'Transit', 'van', 1965, NULL),
(3, 'Escape', 'car', 2000, NULL),
-- BMW Models
(4, '3 Series', 'car', 1975, NULL),
(4, '5 Series', 'car', 1972, NULL),
(4, 'X5', 'car', 1999, NULL),
(4, 'S1000RR', 'motorcycle', 2009, NULL),
(4, 'R1250GS', 'motorcycle', 2019, NULL),
-- Mercedes-Benz Models
(5, 'C-Class', 'car', 1993, NULL),
(5, 'E-Class', 'car', 1953, NULL),
(5, 'S-Class', 'car', 1972, NULL),
(5, 'Sprinter', 'van', 1995, NULL),
(5, 'G-Class', 'car', 1979, NULL),
-- Volkswagen Models
(6, 'Golf', 'car', 1974, NULL),
(6, 'Passat', 'car', 1973, NULL),
(6, 'Jetta', 'car', 1979, NULL),
(6, 'Transporter', 'van', 1950, NULL),
(6, 'Tiguan', 'car', 2007, NULL),
-- Hyundai Models
(7, 'Elantra', 'car', 1990, NULL),
(7, 'Sonata', 'car', 1985, NULL),
(7, 'Tucson', 'car', 2004, NULL),
(7, 'Santa Fe', 'car', 2000, NULL),
(7, 'H100', 'van', 1977, NULL),
-- Nissan Models
(8, 'Altima', 'car', 1992, NULL),
(8, 'Sentra', 'car', 1982, NULL),
(8, 'Rogue', 'car', 2007, NULL),
(8, 'Frontier', 'truck', 1997, NULL),
(8, 'NV200', 'van', 2009, NULL),
-- Chevrolet Models
(9, 'Silverado', 'truck', 1998, NULL),
(9, 'Malibu', 'car', 1964, NULL),
(9, 'Equinox', 'car', 2004, NULL),
(9, 'Express', 'van', 1996, NULL),
(9, 'Tahoe', 'car', 1992, NULL),
-- Tesla Models
(10, 'Model 3', 'car', 2017, NULL),
(10, 'Model S', 'car', 2012, NULL),
(10, 'Model X', 'car', 2015, NULL),
(10, 'Model Y', 'car', 2020, NULL),
(10, 'Cybertruck', 'truck', 2023, NULL);

-- Insert 100 Parking Slots
INSERT INTO slots (slot_number, slot_type, price_per_hour, status) VALUES
-- Car Slots Level A (10 slots - ₹5.00/hour)
('A-101', 'car', 5.00, 'available'),
('A-102', 'car', 5.00, 'available'),
('A-103', 'car', 5.00, 'available'),
('A-104', 'car', 5.00, 'available'),
('A-105', 'car', 5.00, 'available'),
('A-106', 'car', 5.00, 'available'),
('A-107', 'car', 5.00, 'available'),
('A-108', 'car', 5.00, 'available'),
('A-109', 'car', 5.00, 'available'),
('A-110', 'car', 5.00, 'available'),
-- Car Slots Level B (10 slots - ₹6.00/hour)
('B-201', 'car', 6.00, 'available'),
('B-202', 'car', 6.00, 'available'),
('B-203', 'car', 6.00, 'available'),
('B-204', 'car', 6.00, 'available'),
('B-205', 'car', 6.00, 'available'),
('B-206', 'car', 6.00, 'available'),
('B-207', 'car', 6.00, 'available'),
('B-208', 'car', 6.00, 'available'),
('B-209', 'car', 6.00, 'available'),
('B-210', 'car', 6.00, 'available'),
-- Car Slots Level C (20 slots - ₹7.00/hour)
('C-301', 'car', 7.00, 'available'),
('C-302', 'car', 7.00, 'available'),
('C-303', 'car', 7.00, 'available'),
('C-304', 'car', 7.00, 'available'),
('C-305', 'car', 7.00, 'available'),
('C-306', 'car', 7.00, 'available'),
('C-307', 'car', 7.00, 'available'),
('C-308', 'car', 7.00, 'available'),
('C-309', 'car', 7.00, 'available'),
('C-310', 'car', 7.00, 'available'),
('C-311', 'car', 7.00, 'available'),
('C-312', 'car', 7.00, 'available'),
('C-313', 'car', 7.00, 'available'),
('C-314', 'car', 7.00, 'available'),
('C-315', 'car', 7.00, 'available'),
('C-316', 'car', 7.00, 'available'),
('C-317', 'car', 7.00, 'available'),
('C-318', 'car', 7.00, 'available'),
('C-319', 'car', 7.00, 'available'),
('C-320', 'car', 7.00, 'available'),
-- Motorcycle Slots (15 slots - ₹2.00/hour)
('M-401', 'motorcycle', 2.00, 'available'),
('M-402', 'motorcycle', 2.00, 'available'),
('M-403', 'motorcycle', 2.00, 'available'),
('M-404', 'motorcycle', 2.00, 'available'),
('M-405', 'motorcycle', 2.00, 'available'),
('M-406', 'motorcycle', 2.00, 'available'),
('M-407', 'motorcycle', 2.00, 'available'),
('M-408', 'motorcycle', 2.00, 'available'),
('M-409', 'motorcycle', 2.00, 'available'),
('M-410', 'motorcycle', 2.00, 'available'),
('M-411', 'motorcycle', 2.00, 'available'),
('M-412', 'motorcycle', 2.00, 'available'),
('M-413', 'motorcycle', 2.00, 'available'),
('M-414', 'motorcycle', 2.00, 'available'),
('M-415', 'motorcycle', 2.00, 'available'),
-- Truck Slots (10 slots - ₹10.00/hour)
('T-501', 'truck', 10.00, 'available'),
('T-502', 'truck', 10.00, 'available'),
('T-503', 'truck', 10.00, 'available'),
('T-504', 'truck', 10.00, 'available'),
('T-505', 'truck', 10.00, 'available'),
('T-506', 'truck', 10.00, 'available'),
('T-507', 'truck', 10.00, 'available'),
('T-508', 'truck', 10.00, 'available'),
('T-509', 'truck', 10.00, 'available'),
('T-510', 'truck', 10.00, 'available'),
-- Van Slots (15 slots - ₹7.00/hour)
('V-601', 'van', 7.00, 'available'),
('V-602', 'van', 7.00, 'available'),
('V-603', 'van', 7.00, 'available'),
('V-604', 'van', 7.00, 'available'),
('V-605', 'van', 7.00, 'available'),
('V-606', 'van', 7.00, 'available'),
('V-607', 'van', 7.00, 'available'),
('V-608', 'van', 7.00, 'available'),
('V-609', 'van', 7.00, 'available'),
('V-610', 'van', 7.00, 'available'),
('V-611', 'van', 7.00, 'available'),
('V-612', 'van', 7.00, 'available'),
('V-613', 'van', 7.00, 'available'),
('V-614', 'van', 7.00, 'available'),
('V-615', 'van', 7.00, 'available'),
-- Electric Vehicle Charging Slots (15 slots - ₹8.00/hour)
('EV-701', 'car', 8.00, 'available'),
('EV-702', 'car', 8.00, 'available'),
('EV-703', 'car', 8.00, 'available'),
('EV-704', 'car', 8.00, 'available'),
('EV-705', 'car', 8.00, 'available'),
('EV-706', 'car', 8.00, 'available'),
('EV-707', 'car', 8.00, 'available'),
('EV-708', 'car', 8.00, 'available'),
('EV-709', 'car', 8.00, 'available'),
('EV-710', 'car', 8.00, 'available'),
('EV-711', 'car', 8.00, 'available'),
('EV-712', 'car', 8.00, 'available'),
('EV-713', 'car', 8.00, 'available'),
('EV-714', 'car', 8.00, 'available'),
('EV-715', 'car', 8.00, 'available');

-- Insert default admin user (password: admin123)
-- Note: In production, use password_hash() function to hash passwords
INSERT INTO admin_users (username, password_hash, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@smartparking.com', 'System Administrator', 'admin');

-- Insert Sample Vehicles
INSERT INTO vehicles (vehicle_number, company_id, model_id, vehicle_type, color, owner_name, phone_number, email) VALUES
('REG-1001', 1, 1, 'car', 'Black', 'John Doe', '+1-555-1001', 'john.doe@email.com'),
('REG-1002', 2, 6, 'car', 'White', 'Jane Smith', '+1-555-1002', 'jane.smith@email.com'),
('REG-1003', 2, 9, 'motorcycle', 'Red', 'Bob Johnson', '+1-555-1003', 'bob.johnson@email.com'),
('REG-1004', 3, 12, 'car', 'Blue', 'Alice Brown', '+1-555-1004', 'alice.brown@email.com'),
('REG-1005', 3, 11, 'truck', 'Silver', 'Charlie Wilson', '+1-555-1005', 'charlie.wilson@email.com'),
('REG-1006', 4, 15, 'car', 'Gray', 'Diana Martinez', '+1-555-1006', 'diana.martinez@email.com'),
('REG-1007', 5, 20, 'van', 'White', 'Edward Taylor', '+1-555-1007', 'edward.taylor@email.com'),
('REG-1008', 6, 25, 'car', 'Black', 'Fiona Anderson', '+1-555-1008', 'fiona.anderson@email.com'),
('REG-1009', 7, 30, 'car', 'Red', 'George Thomas', '+1-555-1009', 'george.thomas@email.com'),
('REG-1010', 8, 35, 'car', 'Blue', 'Helen Jackson', '+1-555-1010', 'helen.jackson@email.com');

-- Insert Sample Stolen Vehicles (Police Database Duplicate)
INSERT INTO stolen_vehicles (vehicle_number, company_id, model_id, vehicle_type, color, owner_name, owner_phone, police_case_number, police_station, reported_by, description, status) VALUES
('ABC-1234', 1, 1, 'car', 'Black', 'John Smith', '+1-555-0101', 'CASE-2024-001', 'Downtown Police Station', 'Officer Johnson', 'Stolen from parking lot on Main Street', 'active'),
('XYZ-5678', 2, 6, 'car', 'White', 'Sarah Johnson', '+1-555-0102', 'CASE-2024-002', 'North Precinct', 'Officer Williams', 'Vehicle reported missing from residence', 'active'),
('DEF-9012', 3, 11, 'truck', 'Red', 'Mike Davis', '+1-555-0103', 'CASE-2024-003', 'South Police Station', 'Officer Brown', 'Commercial vehicle theft', 'active'),
('GHI-3456', 4, 14, 'motorcycle', 'Blue', 'Emily Wilson', '+1-555-0104', 'CASE-2024-004', 'East Precinct', 'Officer Taylor', 'Motorcycle stolen from garage', 'resolved'),
('JKL-7890', 5, 20, 'van', 'Silver', 'Robert Martinez', '+1-555-0105', 'CASE-2024-005', 'West Police Station', 'Officer Anderson', 'Van reported stolen during delivery', 'active');

-- Insert Sample Parking Entries
INSERT INTO parking_entries (vehicle_id, slot_id, entry_time, expiry_time, amount_paid, payment_method, payment_status, entry_gate) VALUES
(1, 1, '2024-01-15 09:00:00', '2024-01-15 11:00:00', 10.00, 'card', 'completed', 'Gate-A'),
(2, 2, '2024-01-15 10:30:00', '2024-01-15 12:30:00', 10.00, 'cash', 'completed', 'Gate-A'),
(3, 31, '2024-01-15 11:00:00', '2024-01-15 13:00:00', 4.00, 'mobile', 'completed', 'Gate-B'),
(4, 3, '2024-01-15 12:00:00', '2024-01-15 14:00:00', 10.00, 'card', 'completed', 'Gate-A'),
(5, 41, '2024-01-15 13:00:00', '2024-01-15 15:00:00', 20.00, 'card', 'completed', 'Gate-C');

-- Insert Sample Admin Alerts
INSERT INTO admin_alerts (alert_type, title, message, severity, related_vehicle_id, related_slot_id, status) VALUES
('stolen_vehicle', 'Stolen Vehicle Detected', 'Vehicle ABC-1234 has been detected entering the parking facility. This vehicle is reported as stolen in the police database.', 'critical', NULL, NULL, 'new'),
('overdue_parking', 'Overdue Parking Detected', 'Vehicle in slot A-101 has exceeded the parking expiry time by 2 hours.', 'high', 1, 1, 'new'),
('maintenance_required', 'Slot Maintenance Required', 'Slot T-501 requires maintenance. Please schedule inspection.', 'medium', NULL, 41, 'acknowledged'),
('payment_failed', 'Payment Processing Failed', 'Payment failed for vehicle entry at slot B-202. Manual intervention required.', 'high', 2, 2, 'new'),
('system_error', 'Database Connection Issue', 'Temporary database connection issue detected. System recovered automatically.', 'low', NULL, NULL, 'resolved'),
('security_breach', 'Unauthorized Access Attempt', 'Multiple failed login attempts detected from IP address 192.168.1.100.', 'high', NULL, NULL, 'acknowledged');

-- Create indexes for better performance
CREATE INDEX idx_bookings_active ON parking_entries(vehicle_id, slot_id, exit_time);
CREATE INDEX idx_vehicles_created ON vehicles(created_at);
CREATE INDEX idx_slots_type_status ON slots(slot_type, status);
CREATE INDEX idx_stolen_vehicles_active ON stolen_vehicles(vehicle_number, status);
CREATE INDEX idx_admin_alerts_status ON admin_alerts(status, severity, created_at);

