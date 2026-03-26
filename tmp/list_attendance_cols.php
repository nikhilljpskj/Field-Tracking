<?php
$db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
$stmt = $db->query('DESCRIBE attendance');
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
