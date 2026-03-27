<?php
$db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
$stmt = $db->query("SELECT * FROM roles");
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    print_r($r);
}
