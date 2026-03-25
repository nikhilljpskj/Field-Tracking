<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'sales_tracking';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to $name\n";

    $table = 'salary_structures';
    $columns_to_add = [
        'hra' => "DECIMAL(10,2) DEFAULT 0",
        'da' => "DECIMAL(10,2) DEFAULT 0",
        'special_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'conveyance_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'medical_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'education_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'uniform_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'food_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'telephone_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'books_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'research_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'cca' => "DECIMAL(10,2) DEFAULT 0",
        'transport_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'other_allowance' => "DECIMAL(10,2) DEFAULT 0",
        'pf_deduction' => "DECIMAL(10,2) DEFAULT 0",
        'tax_deduction' => "DECIMAL(10,2) DEFAULT 0",
        'pf_employer_contribution' => "DECIMAL(10,2) DEFAULT 0",
        'eps_contribution' => "DECIMAL(10,2) DEFAULT 0",
        'esi_employer_contribution' => "DECIMAL(10,2) DEFAULT 0",
        'gratuity_provision' => "DECIMAL(10,2) DEFAULT 0",
        'bonus_provision' => "DECIMAL(10,2) DEFAULT 0",
        'lwf_employee' => "DECIMAL(10,2) DEFAULT 0",
        'lwf_employer' => "DECIMAL(10,2) DEFAULT 0",
        'surcharge' => "DECIMAL(10,2) DEFAULT 0",
        'advance_recovery' => "DECIMAL(10,2) DEFAULT 0",
        'loan_emi' => "DECIMAL(10,2) DEFAULT 0",
        'tds_monthly' => "DECIMAL(10,2) DEFAULT 0",
        'professional_tax' => "DECIMAL(10,2) DEFAULT 200",
        'deduction_80c' => "DECIMAL(10,2) DEFAULT 0",
        'deduction_80d' => "DECIMAL(10,2) DEFAULT 0",
        'prev_employer_tds' => "DECIMAL(10,2) DEFAULT 0",
        'total_working_days' => "INT DEFAULT 26",
        'is_statutory_enabled' => "TINYINT(1) DEFAULT 1",
        'total_ctc' => "DECIMAL(15,2) DEFAULT 0"
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

    echo "Sync completed successfully.";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
