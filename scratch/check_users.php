<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Core/Config.php';
\App\Core\Config::load(__DIR__ . '/../.env');

$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT email FROM users LIMIT 5");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($users);
