<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // My local setup usually has no password.
$dbname = 'sales_tracking'; // I will check this.
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach($tables as $table) {
        echo "Table: $table\n";
        $cols = $db->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        print_r($cols);
    }
} catch(Exception $e) { echo $e->getMessage(); }
