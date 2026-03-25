<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "<h3>Starting Advanced Payroll Migration...</h3>";

    // 1. Alter salary_structures - use IGNORE-like logic or check if columns exist
    echo "<li>Adding advanced fields to 'salary_structures' table...</li>";
    
    // Using individual ALTER IGNORE or checking for column existence is safer
    $columns = [
        'da' => 'DECIMAL(12, 2) DEFAULT 0',
        'special_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'conveyance_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'medical_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'education_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'uniform_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'food_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'telephone_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'books_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'research_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'cca' => 'DECIMAL(12, 2) DEFAULT 0',
        'transport_allowance' => 'DECIMAL(12, 2) DEFAULT 0',
        'overtime_hours' => 'DECIMAL(4, 1) DEFAULT 0',
        'leave_encashment_days' => 'DECIMAL(4, 1) DEFAULT 0',
        'pf_employer_contribution' => 'DECIMAL(12, 2) DEFAULT 0',
        'eps_contribution' => 'DECIMAL(12, 2) DEFAULT 0',
        'esi_employer_contribution' => 'DECIMAL(12, 2) DEFAULT 0',
        'gratuity_provision' => 'DECIMAL(12, 2) DEFAULT 0',
        'bonus_provision' => 'DECIMAL(12, 2) DEFAULT 0',
        'lwf_employee' => 'DECIMAL(12, 2) DEFAULT 0',
        'lwf_employer' => 'DECIMAL(12, 2) DEFAULT 0',
        'surcharge' => 'DECIMAL(12, 2) DEFAULT 0',
        'advance_recovery' => 'DECIMAL(12, 2) DEFAULT 0',
        'loan_emi' => 'DECIMAL(12, 2) DEFAULT 0',
        'tds_monthly' => 'DECIMAL(12, 2) DEFAULT 0',
        'deduction_80c' => 'DECIMAL(12, 2) DEFAULT 0',
        'deduction_80d' => 'DECIMAL(12, 2) DEFAULT 0',
        'prev_employer_tds' => 'DECIMAL(12, 2) DEFAULT 0',
        'professional_tax' => 'DECIMAL(12, 2) DEFAULT 0',
        'total_working_days' => 'INT DEFAULT 26'
    ];

    foreach ($columns as $name => $type) {
        try {
            $db->exec("ALTER TABLE salary_structures ADD COLUMN $name $type");
        } catch (Exception $e) {
            echo "<li>Column '$name' already exists or other error: " . $e->getMessage() . "</li>";
        }
    }

    // 2. Modify payroll_history for snapshots
    echo "<li>Adding breakdown fields to 'payroll_history' table...</li>";
    $history_cols = [
        'breakdown_json' => 'JSON',
        'net_salary_taxable' => 'DECIMAL(12, 2) DEFAULT 0',
        'gross_earnings' => 'DECIMAL(12, 2) DEFAULT 0',
        'total_statutory_deductions' => 'DECIMAL(12, 2) DEFAULT 0',
        'employer_contributions' => 'DECIMAL(12, 2) DEFAULT 0',
        'monthly_ctc' => 'DECIMAL(12, 2) DEFAULT 0',
        'annual_ctc' => 'DECIMAL(12, 2) DEFAULT 0'
    ];

    foreach ($history_cols as $name => $type) {
        try {
            $db->exec("ALTER TABLE payroll_history ADD COLUMN $name $type");
        } catch (Exception $e) {
            echo "<li>Column '$name' (history) already exists: " . $e->getMessage() . "</li>";
        }
    }

    // 3. Create formula templates table
    echo "<li>Creating 'salary_templates' table...</li>";
    $db->exec("CREATE TABLE IF NOT EXISTS salary_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        data_json JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "<h3 style='color:green'>Advanced Payroll Migration Successful!</h3>";

} catch (Exception $e) {
    echo "<h3 style='color:red'>Migration Failed: " . $e->getMessage() . "</h3>";
}
