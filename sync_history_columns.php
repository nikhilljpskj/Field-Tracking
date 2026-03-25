<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'sales_tracking';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to $name\n";

    $table = 'payroll_history';
    $columns_to_add = [
        'processed_by' => "INT NULL",
        'breakdown_json' => "TEXT",
        'monthly_ctc' => "DECIMAL(10,2) DEFAULT 0",
        'annual_ctc' => "DECIMAL(10,2) DEFAULT 0",
        'is_statutory_enabled' => "TINYINT(1) DEFAULT 1"
    ];

    // Get current columns
    $stmt = $pdo->query("DESCRIBE $table");
    $existing_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columns_to_add as $col => $definition) {
        if (!in_array($col, $existing_columns)) {
            $pdo->exec("ALTER TABLE $table ADD COLUMN $col $definition");
            echo "Added column: $col\n";
        } else {
            echo "Column exists: $col\n";
        }
    }

    echo "History sync completed successfully.";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
