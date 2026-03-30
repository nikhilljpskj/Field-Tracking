<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $tables = ['salary_structures', 'salary_templates', 'payroll_history'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW CREATE TABLE $table");
        if ($stmt) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo $row['Create Table'] . ";\n\n";
        } else {
            echo "-- Table $table does not exist locally.\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
