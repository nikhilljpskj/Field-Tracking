<?php
define('BASE_PATH', dirname(__DIR__));
require_once 'app/Core/Config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'last_activity_at'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE users ADD COLUMN last_activity_at TIMESTAMP NULL AFTER is_active");
        echo "Column last_activity_at added successfully.\n";
    } else {
        echo "Column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
