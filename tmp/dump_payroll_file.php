<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $tables = ['salary_structures', 'salary_templates', 'payroll_history'];
    $out = "";
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW CREATE TABLE $table");
        if ($stmt) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $out .= $row['Create Table'] . ";\n\n";
        } else {
            $out .= "-- Table $table does not exist locally.\n\n";
        }
    }
    file_put_contents('tmp/payroll_schema.sql', $out);
    echo "Done";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
