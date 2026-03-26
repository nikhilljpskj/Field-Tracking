<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Reconstructing attendance table schema...\n";
    
    $cols = [
        'check_in_photo' => "VARCHAR(255) DEFAULT NULL AFTER check_in_address",
        'odometer_photo' => "VARCHAR(255) DEFAULT NULL AFTER check_in_photo",
        'odometer_reading' => "VARCHAR(20) DEFAULT NULL AFTER odometer_photo",
        'ticket_details' => "TEXT DEFAULT NULL AFTER odometer_reading",
        'check_out_time' => "DATETIME DEFAULT NULL AFTER ticket_details",
        'check_out_lat' => "DECIMAL(10,8) DEFAULT NULL AFTER check_out_time",
        'check_out_lng' => "DECIMAL(11,8) DEFAULT NULL AFTER check_out_lat",
        'check_out_address' => "TEXT DEFAULT NULL AFTER check_out_lng",
        'check_out_photo' => "VARCHAR(255) DEFAULT NULL AFTER check_out_address",
        'check_out_odometer_photo' => "VARCHAR(255) DEFAULT NULL AFTER check_out_photo",
        'check_out_odometer_reading' => "VARCHAR(20) DEFAULT NULL AFTER check_out_odometer_photo"
    ];

    foreach ($cols as $col => $definition) {
        $stmt = $db->query("SHOW COLUMNS FROM attendance LIKE '$col'");
        if (!$stmt->fetch()) {
            $db->exec("ALTER TABLE attendance ADD COLUMN $col $definition");
            echo " - Added $col\n";
        } else {
            echo " - $col already exists\n";
        }
    }
    
    echo "Reconstruction Completed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
