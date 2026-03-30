<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=sales_tracking', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $queries = [
        "CREATE TABLE IF NOT EXISTS hospitals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS doctors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "ALTER TABLE client_meetings ADD COLUMN IF NOT EXISTS referenced_doctor_id INT DEFAULT NULL",
        // Add foreign key constraint if it doesn't exist? No, keep it weak coupling for now to prevent breaking local constraints.
    ];

    foreach ($queries as $sql) {
        $db->exec($sql);
        echo "Executed query successfully.\n";
    }
    echo "Master tables created.\n";
} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
