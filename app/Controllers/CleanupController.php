<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\CleanupService;
use App\Core\Database;

class CleanupController extends Controller {

    private function getService(): CleanupService {
        $db = \Database::getInstance();
        return new CleanupService($db->getConnection(), BASE_PATH);
    }

    /**
     * AJAX: Dismiss today's cleanup warning notification for the current admin.
     * POST cleanup-notify?action=dismiss
     */
    public function dismiss() {
        $this->checkRole(['Admin', 'HR']);
        header('Content-Type: application/json');
        try {
            $service = $this->getService();
            $service->dismissForToday((int) $_SESSION['user_id']);
            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Admin-triggered manual cleanup run (also called by cleanup_job.php).
     * GET  cleanup?action=run  (Admin only — confirms and runs immediately)
     */
    public function run() {
        $this->checkRole(['Admin']);
        header('Content-Type: application/json');
        try {
            $service = $this->getService();
            $log     = $service->runCleanup();

            // Write log
            $logDir = BASE_PATH . '/logs';
            if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
            $line = sprintf(
                "[%s] MANUAL cleanup | Att: %d | Meetings: %d | Files: %d",
                $log['ran_at'], $log['attendance_deleted'],
                $log['meetings_deleted'], $log['files_deleted']
            );
            file_put_contents($logDir . '/cleanup.log', $line . PHP_EOL, FILE_APPEND | LOCK_EX);

            echo json_encode(['success' => true, 'log' => $log]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Default index — summary of pending deletions (JSON).
     */
    public function index() {
        $this->checkRole(['Admin', 'HR']);
        header('Content-Type: application/json');
        $service = $this->getService();
        $summary = $service->getPendingDeletionSummary();
        echo json_encode($summary);
        exit;
    }
}
