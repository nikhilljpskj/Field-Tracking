<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $tables = ['leave_types', 'leave_allocations', 'leave_applications', 'attendance', 'travel_summary', 'client_meetings'];
    foreach($tables as $t) {
        $stmt = $db->query("SHOW CREATE TABLE $t");
        $res = $stmt->fetch();
        echo "--- $t ---\n";
        echo $res[1] . ";\n\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
