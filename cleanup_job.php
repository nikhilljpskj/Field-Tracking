<?php
/**
 * cleanup_job.php — Scheduled Data Retention Cleanup
 *
 * Deletes attendance logs and client meeting logs older than 60 days,
 * including all associated media files from storage.
 *
 * HOW TO SCHEDULE ON WINDOWS (Task Scheduler):
 * -----------------------------------------------
 * 1. Open "Task Scheduler" → Create Basic Task
 * 2. Name: "Redeemer HRMS - 60-Day Data Cleanup"
 * 3. Trigger: Daily at 02:00 AM
 * 4. Action: Start a program
 *    Program: C:\xampp\php\php.exe
 *    Arguments: "C:\xampp\htdocs\Field-Tracking-System\cleanup_job.php"
 * 5. Save → enable "Run whether user is logged on or not"
 *
 * HOW TO TEST MANUALLY:
 *   php cleanup_job.php
 *   OR via browser (admin only): http://localhost/Field-Tracking-System/cleanup
 *
 * Cleanup log is written to: logs/cleanup.log
 */

define('BASE_PATH', __DIR__);
define('CLEANUP_CLI', php_sapi_name() === 'cli');

// ---- Bootstrap ----
require_once BASE_PATH . '/app/Core/Config.php';
\App\Core\Config::load(BASE_PATH . '/.env');
date_default_timezone_set('Asia/Kolkata');

require_once BASE_PATH . '/config/database.php';

// Autoload
spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    $len     = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require $file;
});

// Require admin session if via browser
if (!CLEANUP_CLI) {
    session_start();
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin'])) {
        http_response_code(403);
        die('Forbidden: Admin access required.');
    }
}

// ---- Run cleanup ----
try {
    $db      = Database::getInstance();
    $service = new \App\Services\CleanupService($db->getConnection(), BASE_PATH);
    $log     = $service->runCleanup();

    // Build log entry
    $logLine = sprintf(
        "[%s] Cleanup run | Attendance deleted: %d | Meetings deleted: %d | Files removed: %d | Files missing: %d",
        $log['ran_at'],
        $log['attendance_deleted'],
        $log['meetings_deleted'],
        $log['files_deleted'],
        $log['files_missing']
    );

    if (!empty($log['errors'])) {
        $logLine .= " | ERRORS: " . implode(', ', $log['errors']);
    }

    // Write to log file
    $logDir = BASE_PATH . '/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    file_put_contents($logDir . '/cleanup.log', $logLine . PHP_EOL, FILE_APPEND | LOCK_EX);

    if (CLEANUP_CLI) {
        echo $logLine . PHP_EOL;
    } else {
        // Browser response (JSON)
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'log' => $log]);
    }

} catch (\Throwable $e) {
    $errMsg = "[" . date('Y-m-d H:i:s') . "] CLEANUP ERROR: " . $e->getMessage();
    $logDir = BASE_PATH . '/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    file_put_contents($logDir . '/cleanup.log', $errMsg . PHP_EOL, FILE_APPEND | LOCK_EX);

    if (CLEANUP_CLI) {
        echo $errMsg . PHP_EOL;
        exit(1);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
