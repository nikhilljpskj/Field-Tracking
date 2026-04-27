<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$stmt = $db->query("DESCRIBE users");
print_r($stmt->fetchAll());
