<?php
$db = new mysqli('localhost', 'root', '', 'sales_tracking');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Add is_statutory_enabled if missing
$check = $db->query("SHOW COLUMNS FROM salary_structures LIKE 'is_statutory_enabled'");
if ($check->num_rows == 0) {
    $db->query("ALTER TABLE salary_structures ADD COLUMN is_statutory_enabled TINYINT(1) DEFAULT 1 AFTER other_allowance");
    echo "Added is_statutory_enabled column.\n";
} else {
    echo "Column is_statutory_enabled already exists.\n";
}

// Add total_ctc if missing (needed for some logic)
$check = $db->query("SHOW COLUMNS FROM salary_structures LIKE 'total_ctc'");
if ($check->num_rows == 0) {
    $db->query("ALTER TABLE salary_structures ADD COLUMN total_ctc DECIMAL(15,2) DEFAULT 0 AFTER is_statutory_enabled");
    echo "Added total_ctc column.\n";
}

echo "Database fix completed.";
?>
