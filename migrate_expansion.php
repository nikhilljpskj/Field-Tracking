<?php
require_once 'index.php'; // get config and autoloader

$db = \Database::getInstance()->getConnection();

echo "<h2>Starting Expansion Migration...</h2>";

try {
    // 1. Update users table
    echo "<li>Updating 'users' table...</li>";
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_pic VARCHAR(255) DEFAULT 'assets/avatars/default.jpg' AFTER phone");
    
    // 2. Update attendance table
    echo "<li>Updating 'attendance' table...</li>";
    $db->exec("ALTER TABLE attendance ADD COLUMN IF NOT EXISTS odometer_reading VARCHAR(20) AFTER odometer_photo");
    $db->exec("ALTER TABLE attendance ADD COLUMN IF NOT EXISTS ticket_details TEXT AFTER odometer_reading");

    // 3. Update client_meetings
    echo "<li>Updating 'client_meetings' table...</li>";
    $db->exec("ALTER TABLE client_meetings ADD COLUMN IF NOT EXISTS selfie_path VARCHAR(255) AFTER address");

    // 4. Create new tables
    echo "<li>Creating 'leave_types' table...</li>";
    $db->exec("CREATE TABLE IF NOT EXISTS leave_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        quarterly_allocation INT DEFAULT 0,
        annual_allocation INT DEFAULT 0
    )");

    echo "<li>Creating 'leave_allocations' table...</li>";
    $db->exec("CREATE TABLE IF NOT EXISTS leave_allocations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        leave_type_id INT,
        quarter INT,
        year INT,
        allocated INT,
        used INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
    )");

    echo "<li>Creating 'leave_applications' table...</li>";
    $db->exec("CREATE TABLE IF NOT EXISTS leave_applications (
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
    )");

    echo "<li>Creating 'salary_structures' table...</li>";
    $db->exec("CREATE TABLE IF NOT EXISTS salary_structures (
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
    )");

    echo "<li>Creating 'payroll_history' table...</li>";
    $db->exec("CREATE TABLE IF NOT EXISTS payroll_history (
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
    )");

    // 5. Seed Leave Types
    echo "<li>Seeding Leave Types...</li>";
    $leaveTypes = [
        ['Sick', 3],
        ['Casual', 3],
        ['Earned', 6]
    ];
    $stmt = $db->prepare("INSERT IGNORE INTO leave_types (name, quarterly_allocation) VALUES (?, ?)");
    foreach ($leaveTypes as $type) {
        $stmt->execute($type);
    }

    // 6. Ensure HR Role exists
    echo "<li>Ensuring 'HR' role exists...</li>";
    $db->exec("INSERT IGNORE INTO roles (name, description) VALUES ('HR', 'HR & Payroll Admin')");

    echo "<h3 style='color:green'>Expansion Migration Successful!</h3>";
    echo "<p>Please delete migrate_expansion.php for security.</p>";

} catch (Exception $e) {
    echo "<h3 style='color:red'>Migration Failed: " . $e->getMessage() . "</h3>";
}
