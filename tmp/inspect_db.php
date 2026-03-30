<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE inhouse_tasks");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $db->query("DESCRIBE client_meetings");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $db->query("SHOW TABLES");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
