<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/Config.php';
\App\Core\Config::load(BASE_PATH . '/.env');
require_once BASE_PATH . '/config/database.php';
$conn = \Database::getInstance()->getConnection();

echo "Creating notifications table...\n";
$conn->exec("CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type VARCHAR(50),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "Done!\n";
