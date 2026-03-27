<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $stmt = $db->query("SELECT name FROM roles");
    $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "ROLES: " . implode(", ", $roles) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
