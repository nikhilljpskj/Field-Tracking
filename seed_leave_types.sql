-- Seed Default Leave Types
INSERT INTO leave_types (name, quarterly_allocation) VALUES 
('Sick Leave', 1.5),
('Casual Leave', 1.5),
('Earned Leave', 3.0)
ON DUPLICATE KEY UPDATE quarterly_allocation = VALUES(quarterly_allocation);

-- Ensure leave_allocations table exists with proper unique constraint
CREATE TABLE IF NOT EXISTS leave_allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    quarter INT NOT NULL,
    year INT NOT NULL,
    allocated DECIMAL(10,2) DEFAULT 0,
    used DECIMAL(10,2) DEFAULT 0,
    UNIQUE KEY (user_id, leave_type_id, quarter, year)
);
