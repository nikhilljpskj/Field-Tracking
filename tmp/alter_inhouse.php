<?php
require_once __DIR__ . '/../config/database.php';
$db = Database::getInstance()->getConnection();
try {
    $db->exec("ALTER TABLE inhouse_tasks MODIFY status VARCHAR(50) DEFAULT 'Pending'");
    $db->exec("ALTER TABLE inhouse_tasks ADD manager_feedback TEXT DEFAULT NULL");
    echo "DB Updated Successfully\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
