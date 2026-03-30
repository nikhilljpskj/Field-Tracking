<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $queries = [
        "ALTER TABLE client_meetings ADD COLUMN IF NOT EXISTS admin_comments TEXT DEFAULT NULL",
        "ALTER TABLE travel_summary ADD COLUMN IF NOT EXISTS admin_comments TEXT DEFAULT NULL",
    ];

    foreach ($queries as $sql) {
        $db->exec($sql);
        echo "Executed: $sql\n";
    }
    echo "Database updated successfully.\n";
} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
