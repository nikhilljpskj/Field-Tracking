<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding checkout odometer columns to attendance table...\n";
    
    // Check if columns exist
    $stmt = $db->query("SHOW COLUMNS FROM attendance LIKE 'check_out_odometer_photo'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE attendance ADD COLUMN check_out_odometer_photo VARCHAR(255) DEFAULT NULL AFTER check_out_photo");
        echo " - Added check_out_odometer_photo\n";
    }
    
    $stmt = $db->query("SHOW COLUMNS FROM attendance LIKE 'check_out_odometer_reading'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE attendance ADD COLUMN check_out_odometer_reading VARCHAR(20) DEFAULT NULL AFTER check_out_odometer_photo");
        echo " - Added check_out_odometer_reading\n";
    }
    
    echo "Schema Update Completed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
