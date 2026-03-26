<?php
$db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
$stmt = $db->query('SHOW TABLES');
echo implode(", ", $stmt->fetchAll(PDO::FETCH_COLUMN));
