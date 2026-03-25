<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'sales_tracking';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update defaults
    $pdo->exec("UPDATE salary_structures SET total_working_days = 26 WHERE total_working_days IS NULL OR total_working_days = 0");
    
    // Check columns
    $stmt = $pdo->query("DESCRIBE salary_structures");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in salary_structures:\n";
    foreach($cols as $c) echo "- " . $c['Field'] . "\n";

    echo "\nFinal sync completed successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
