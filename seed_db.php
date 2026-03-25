<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);
require_once 'config/database.php';

echo "<h2>System Setup & Database Seeding</h2>";

try {
    // 1. Connect without Database Name
    echo "Connecting to MySQL server... ";
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<span style='color:green;'>Connected.</span><br>";

    // 2. Create Database
    echo "Ensuring database '" . DB_NAME . "' exists... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "<span style='color:green;'>Done.</span><br>";

    // 3. Connect to the specific database
    echo "Connecting to database '" . DB_NAME . "'... ";
    $pdo->exec("USE " . DB_NAME);
    echo "<span style='color:green;'>Connected.</span><br>";

    // 4. Read and Execute Schema
    $schemaFile = __DIR__ . '/database_schema.sql';
    if (file_exists($schemaFile)) {
        echo "Reading schema file... ";
        $sql = file_get_contents($schemaFile);
        echo "<span style='color:green;'>Done.</span><br>";

        // Remove comments and split by ;
        echo "Executing queries...<br>";
        $queries = preg_replace('/--.*$/m', '', $sql); // Remove single line comments
        $queries = explode(';', $queries);
        
        $successCount = 0;
        $errorCount = 0;

        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            try {
                $pdo->exec($query);
                $successCount++;
            } catch (PDOException $e) {
                // Ignore "already exists" errors
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    $successCount++;
                } else {
                    echo "<li style='color:red;'>Error in query [".substr($query, 0, 50)."...]: " . $e->getMessage() . "</li>";
                    $errorCount++;
                }
            }
        }
        echo "<b>Execution Summary:</b> $successCount successful, $errorCount failed.<br>";
    } else {
        echo "<span style='color:red;'>Error: database_schema.sql not found!</span><br>";
    }

    // 5. Verify Tables
    echo "Verifying tables...<br>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($tables)) {
        echo "Created tables: " . implode(", ", $tables) . "<br>";
    } else {
        echo "<span style='color:red;'>No tables found in database!</span><br>";
    }

    // 6. Seed Admin User (using User model)
    echo "Initializing base data... ";
    require_once 'app/Core/Model.php';
    require_once 'app/Models/User.php';
    
    $userModel = new \App\Models\User();
    $adminEmail = 'admin@example.com';
    $existingAdmin = $userModel->findByEmail($adminEmail);
    
    if (!$existingAdmin) {
        $userModel->create([
            'role_id' => 1,
            'name' => 'System Admin',
            'email' => $adminEmail,
            'password' => 'admin123',
            'phone' => '1234567890',
            'manager_id' => null
        ]);
        echo "Admin created. ";
    } else {
        echo "Admin exists. ";
    }

    echo "<span style='color:green;'>Done.</span><br>";
    echo "<h3>Setup Complete!</h3>";
    echo "<p>Login with: <b>admin@example.com</b> / <b>admin123</b></p>";
    echo "<a href='login' style='padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Go to Login</a>";

} catch (PDOException $e) {
    echo "<div style='padding:15px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:5px;'>";
    echo "<strong>Fatal PDO Error:</strong> " . $e->getMessage();
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='padding:15px; background:#fef3cd; color:#856404; border:1px solid #ffeeba; border-radius:5px;'>";
    echo "<strong>General Error:</strong> " . $e->getMessage();
    echo "</div>";
}
