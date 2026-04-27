<?php
define('BASE_PATH', dirname(__DIR__));
require_once 'app/Core/Config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Consolidate Sick
    $db->exec("UPDATE leave_applications SET leave_type_id = 7 WHERE leave_type_id = 1");
    $db->exec("DELETE FROM leave_allocations WHERE leave_type_id = 1");
    $db->exec("DELETE FROM leave_types WHERE id = 1");
    
    // 2. Consolidate Casual
    $db->exec("UPDATE leave_applications SET leave_type_id = 8 WHERE leave_type_id = 2");
    $db->exec("DELETE FROM leave_allocations WHERE leave_type_id = 2");
    $db->exec("DELETE FROM leave_types WHERE id = 2");
    
    // 3. Consolidate Earned
    $db->exec("UPDATE leave_applications SET leave_type_id = 9 WHERE leave_type_id = 3");
    $db->exec("DELETE FROM leave_allocations WHERE leave_type_id = 3");
    $db->exec("DELETE FROM leave_types WHERE id = 3");
    
    // 4. Ensure names are exactly as requested
    $db->exec("UPDATE leave_types SET name = 'Sick Leave' WHERE id = 7");
    $db->exec("UPDATE leave_types SET name = 'Casual Leave' WHERE id = 8");
    $db->exec("UPDATE leave_types SET name = 'Earned Leave' WHERE id = 9");
    
    // 5. Add LOP
    $stmt = $db->query("SELECT id FROM leave_types WHERE name = 'LOP'");
    if (!$stmt->fetch()) {
        $db->exec("INSERT INTO leave_types (name, quarterly_allocation) VALUES ('LOP', 0)");
    }
    
    echo "Leave types consolidated successfully.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
