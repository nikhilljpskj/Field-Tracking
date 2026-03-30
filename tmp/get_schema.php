<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $stmt = $db->query("SHOW CREATE TABLE inhouse_tasks");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['Create Table'] . ";\n\n";

    $stmt2 = $db->query("SHOW CREATE TABLE hospitals");
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    echo $row2['Create Table'] . ";\n\n";

    $stmt3 = $db->query("SHOW CREATE TABLE doctors");
    $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
    echo $row3['Create Table'] . ";\n\n";
    
} catch(Exception $e) {
    echo $e->getMessage();
}
