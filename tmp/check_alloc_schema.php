<?php
define('BASE_PATH', dirname(__DIR__));
require_once 'app/Core/Config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("DESCRIBE leave_allocations");
    print_r($stmt->fetchAll());
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
