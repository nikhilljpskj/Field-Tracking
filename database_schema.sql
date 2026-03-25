CREATE DATABASE IF NOT EXISTS sales_tracking;
USE sales_tracking;

-- 1. Roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    profile_pic VARCHAR(255) DEFAULT 'assets/avatars/default.jpg',
    manager_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (manager_id) REFERENCES users(id)
);

-- 3. Attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    check_in_time DATETIME,
    check_out_time DATETIME,
    check_in_lat DECIMAL(10, 8),
    check_in_lng DECIMAL(11, 8),
    check_out_lat DECIMAL(10, 8),
    check_out_lng DECIMAL(11, 8),
    check_in_address TEXT,
    check_out_address TEXT,
    check_in_photo VARCHAR(255),
    check_out_photo VARCHAR(255),
    odometer_photo VARCHAR(255),
    odometer_reading VARCHAR(20),
    ticket_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 4. Employee_tracking (Periodic location capture)
CREATE TABLE IF NOT EXISTS location_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    accuracy DECIMAL(8, 2),
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 5. Client_meetings
CREATE TABLE IF NOT EXISTS client_meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    client_name VARCHAR(255),
    hospital_office_name VARCHAR(255),
    meeting_type VARCHAR(100),
    notes TEXT,
    outcome TEXT,
    meeting_time DATETIME,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    address TEXT,
    selfie_path VARCHAR(255),
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ... (Rest of tables 6, 7, 8, 9 already exist, keep them)

-- 10. Leave Types (Sick, Casual, Earned)
CREATE TABLE IF NOT EXISTS leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    quarterly_allocation INT DEFAULT 0,
    annual_allocation INT DEFAULT 0
);

-- 11. Leave Allocations
CREATE TABLE IF NOT EXISTS leave_allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    leave_type_id INT,
    quarter INT,
    year INT,
    allocated INT,
    used INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
);

-- 12. Leave Applications
CREATE TABLE IF NOT EXISTS leave_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    leave_type_id INT,
    start_date DATE,
    end_date DATE,
    is_half_day BOOLEAN DEFAULT FALSE,
    reason TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    approved_by INT DEFAULT NULL,
    lop_days DECIMAL(4, 1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- 13. Salary Structures
CREATE TABLE IF NOT EXISTS salary_structures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    basic DECIMAL(12, 2) DEFAULT 0,
    hra DECIMAL(12, 2) DEFAULT 0,
    ta_da DECIMAL(12, 2) DEFAULT 0,
    other_allowances DECIMAL(12, 2) DEFAULT 0,
    pf_deduction DECIMAL(12, 2) DEFAULT 0,
    tax_deduction DECIMAL(12, 2) DEFAULT 0,
    total_ctc DECIMAL(12, 2) DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 14. Payroll History
CREATE TABLE IF NOT EXISTS payroll_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    month INT,
    year INT,
    gross_salary DECIMAL(12, 2),
    lop_deductions DECIMAL(12, 2),
    net_salary DECIMAL(12, 2),
    status ENUM('Generated', 'Paid') DEFAULT 'Generated',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 6. Task_assignments
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assigned_by INT,
    assigned_to INT,
    hospital_office_name VARCHAR(255),
    location_desc TEXT,
    target_desc TEXT,
    visit_date DATE,
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    notes TEXT,
    status ENUM('Pending', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_by) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

-- 7. Targets
CREATE TABLE IF NOT EXISTS targets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('Visits', 'Sales Amount', 'New Clients', 'Product Promotion'),
    period ENUM('Daily', 'Weekly', 'Monthly'),
    target_value DECIMAL(12, 2),
    achieved_value DECIMAL(12, 2) DEFAULT 0,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 8. Travel_rates
CREATE TABLE IF NOT EXISTS travel_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rate_per_km DECIMAL(8, 2) NOT NULL,
    effective_from DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 9. Travel_logs (Summary of daily travel)
CREATE TABLE IF NOT EXISTS travel_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date DATE,
    total_distance DECIMAL(10, 2),
    allowance_earned DECIMAL(10, 2),
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 9. Geocoding Cache (To optimize HERE API usage)
CREATE TABLE IF NOT EXISTS geocoding_cache (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address_hash VARCHAR(64) UNIQUE,
    address TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Data for Roles
INSERT INTO roles (name, description) VALUES 
('Admin', 'Full system access'),
('HR', 'HR & Payroll Admin'),
('Manager', 'Manages assigned executives'),
('Executive', 'Field employee');
