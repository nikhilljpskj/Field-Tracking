<?php
define('BASE_PATH', dirname(__DIR__));
require_once 'app/Core/Config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check usage
    $tables = ['leave_allocations', 'leave_applications'];
    foreach ($tables as $table) {
        echo "Usage in $table:\n";
        $stmt = $db->query("SELECT leave_type_id, COUNT(*) as count FROM $table GROUP BY leave_type_id");
        print_r($stmt->fetchAll());
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
