<?php
require_once 'index.php'; // To get autoloader and config

use App\Core\Config;

$db = \Database::getInstance()->getConnection();

echo "<h2>Starting Production Migration...</h2>";

try {
    // 1. Add columns to attendance
    echo "<li>Checking 'attendance' table columns...</li>";
    $columnsToAdd = [
        'check_in_photo' => "ALTER TABLE attendance ADD COLUMN check_in_photo VARCHAR(255) AFTER check_out_address",
        'check_out_photo' => "ALTER TABLE attendance ADD COLUMN check_out_photo VARCHAR(255) AFTER check_in_photo",
        'odometer_photo' => "ALTER TABLE attendance ADD COLUMN odometer_photo VARCHAR(255) AFTER check_out_photo"
    ];

    foreach ($columnsToAdd as $col => $sql) {
        $check = $db->query("SHOW COLUMNS FROM attendance LIKE '$col'");
        if ($check->rowCount() == 0) {
            $db->exec($sql);
            echo "<span style='color:green'>+ Added column $col</span><br>";
        } else {
            echo "<span style='color:gray'>- Column $col already exists</span><br>";
        }
    }

    // 2. Create geocoding_cache
    echo "<li>Checking 'geocoding_cache' table...</li>";
    $db->exec("CREATE TABLE IF NOT EXISTS geocoding_cache (
        id INT AUTO_INCREMENT PRIMARY KEY,
        address_hash VARCHAR(64) UNIQUE,
        address TEXT,
        latitude DECIMAL(10, 8),
        longitude DECIMAL(11, 8),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<span style='color:green'>+ Table 'geocoding_cache' verified/created</span><br>";

    // 3. Verify uploads directory
    echo "<li>Checking 'uploads/attendance' directory...</li>";
    $uploadDir = BASE_PATH . '/uploads/attendance';
    if (!is_dir($uploadDir)) {
        if (mkdir($uploadDir, 0777, true)) {
            echo "<span style='color:green'>+ Created directory $uploadDir</span><br>";
        } else {
            echo "<span style='color:red'>! Failed to create directory $uploadDir</span><br>";
        }
    } else {
        echo "<span style='color:gray'>- Directory exists</span><br>";
    }
    
    if (is_writable($uploadDir)) {
        echo "<span style='color:green'>+ Directory is WRITABLE</span><br>";
    } else {
        echo "<span style='color:red'>! Directory is NOT WRITABLE</span><br>";
    }

    echo "<h3>Migration Complete! Please delete this file for security.</h3>";
    echo "<a href='attendance'>Go to Attendance Page</a>";

} catch (Exception $e) {
    echo "<h3 style='color:red'>Migration Failed: " . $e->getMessage() . "</h3>";
}
