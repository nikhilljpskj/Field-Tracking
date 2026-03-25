<?php
require_once 'app/Core/Config.php';

// Mock Config if not available or just hardcode based on what I saw
$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'sales_tracking';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to $name\n";

    // 1. Add is_statutory_enabled column
    try {
        $pdo->exec("ALTER TABLE salary_structures ADD COLUMN is_statutory_enabled TINYINT(1) DEFAULT 1 AFTER other_allowance");
        echo "Column 'is_statutory_enabled' added.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "Column 'is_statutory_enabled' already exists.\n";
        } else {
            echo "Error adding column: " . $e->getMessage() . "\n";
        }
    }

    // 2. Add total_ctc column
    try {
        $pdo->exec("ALTER TABLE salary_structures ADD COLUMN total_ctc DECIMAL(15,2) DEFAULT 0 AFTER is_statutory_enabled");
        echo "Column 'total_ctc' added.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "Column 'total_ctc' already exists.\n";
        } else {
            echo "Error adding column: " . $e->getMessage() . "\n";
        }
    }

    echo "Fix applied successfully.";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
