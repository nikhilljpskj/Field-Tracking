<?php
namespace App\Services;

/**
 * CleanupService
 *
 * Handles automated purging of attendance logs and client meeting logs
 * older than RETENTION_DAYS (60 days), including all associated media files.
 *
 * Also provides methods to query records that are in the warning window
 * (50–59 days old) for admin notification purposes.
 */
class CleanupService {

    const RETENTION_DAYS = 60;  // delete records older than this
    const WARN_DAYS      = 10;  // start warning this many days before deletion

    private \PDO $db;
    private string $basePath;

    public function __construct(\PDO $db, string $basePath) {
        $this->db       = $db;
        $this->basePath = rtrim($basePath, '/\\');
    }

    // ----------------------------------------------------------------
    // PUBLIC: WARNING QUERY — records aged 50–59 days
    // ----------------------------------------------------------------

    /**
     * Returns counts of records that will be deleted within WARN_DAYS.
     * Returns ['attendance' => int, 'meetings' => int, 'earliest_deletion' => 'Y-m-d']
     */
    public function getPendingDeletionSummary(): array {
        $warnStart = self::RETENTION_DAYS - self::WARN_DAYS; // day 50
        $end       = self::RETENTION_DAYS;                   // day 60

        // Attendance in warning window
        $attStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM attendance
             WHERE created_at < NOW() - INTERVAL :start DAY
               AND created_at >= NOW() - INTERVAL :end DAY"
        );
        $attStmt->execute([':start' => $warnStart, ':end' => $end]);
        $attCount = (int) $attStmt->fetchColumn();

        // Meetings in warning window
        $meetStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM client_meetings
             WHERE created_at < NOW() - INTERVAL :start DAY
               AND created_at >= NOW() - INTERVAL :end DAY"
        );
        $meetStmt->execute([':start' => $warnStart, ':end' => $end]);
        $meetCount = (int) $meetStmt->fetchColumn();

        // Earliest record that will be deleted next (oldest in window)
        $earliest = null;
        if ($attCount > 0 || $meetCount > 0) {
            $minStmt = $this->db->prepare(
                "SELECT MIN(created_at) FROM (
                    SELECT created_at FROM attendance
                    WHERE created_at < NOW() - INTERVAL :start DAY
                      AND created_at >= NOW() - INTERVAL :end DAY
                    UNION ALL
                    SELECT created_at FROM client_meetings
                    WHERE created_at < NOW() - INTERVAL :start2 DAY
                      AND created_at >= NOW() - INTERVAL :end2 DAY
                 ) t"
            );
            $minStmt->execute([
                ':start' => $warnStart, ':end' => $end,
                ':start2' => $warnStart, ':end2' => $end,
            ]);
            $raw = $minStmt->fetchColumn();
            if ($raw) {
                // The record at $raw will be deleted when it reaches 60d
                $deleteAt = date('Y-m-d', strtotime($raw . ' + ' . self::RETENTION_DAYS . ' days'));
                $earliest = $deleteAt;
            }
        }

        return [
            'attendance'        => $attCount,
            'meetings'          => $meetCount,
            'total'             => $attCount + $meetCount,
            'earliest_deletion' => $earliest,
        ];
    }

    // ----------------------------------------------------------------
    // PUBLIC: DISMISSAL TRACKING
    // ----------------------------------------------------------------

    public function isDismissedToday(int $userId): bool {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM cleanup_notification_dismissals
             WHERE user_id = ? AND dismiss_date = CURDATE() LIMIT 1"
        );
        $stmt->execute([$userId]);
        return (bool) $stmt->fetchColumn();
    }

    public function dismissForToday(int $userId): void {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO cleanup_notification_dismissals (user_id, dismiss_date)
             VALUES (?, CURDATE())"
        );
        $stmt->execute([$userId]);
    }

    // ----------------------------------------------------------------
    // PUBLIC: ACTUAL CLEANUP — run via scheduled job
    // ----------------------------------------------------------------

    /**
     * Performs the full cleanup and returns a log of what was done.
     */
    public function runCleanup(): array {
        $log = [
            'attendance_deleted' => 0,
            'meetings_deleted'   => 0,
            'files_deleted'      => 0,
            'files_missing'      => 0,
            'errors'             => [],
            'ran_at'             => date('Y-m-d H:i:s'),
        ];

        $log = $this->purgeAttendance($log);
        $log = $this->purgeMeetings($log);

        return $log;
    }

    // ----------------------------------------------------------------
    // PRIVATE: PURGE ATTENDANCE
    // ----------------------------------------------------------------

    private function purgeAttendance(array $log): array {
        // Fetch all expired attendance records with their photo paths
        $stmt = $this->db->prepare(
            "SELECT id, check_in_photo, check_out_photo, odometer_photo, check_out_odometer_photo
             FROM attendance
             WHERE created_at < NOW() - INTERVAL :days DAY"
        );
        $stmt->execute([':days' => self::RETENTION_DAYS]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($rows)) return $log;

        $photoFields = ['check_in_photo', 'check_out_photo', 'odometer_photo', 'check_out_odometer_photo'];
        $ids = [];

        foreach ($rows as $row) {
            $ids[] = (int) $row['id'];
            foreach ($photoFields as $field) {
                if (!empty($row[$field])) {
                    $result = $this->deleteFile($row[$field]);
                    if ($result === true) {
                        $log['files_deleted']++;
                    } elseif ($result === false) {
                        $log['files_missing']++;
                    }
                }
            }
        }

        // Bulk delete
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $del = $this->db->prepare("DELETE FROM attendance WHERE id IN ($placeholders)");
            $del->execute($ids);
            $log['attendance_deleted'] = $del->rowCount();
        }

        return $log;
    }

    // ----------------------------------------------------------------
    // PRIVATE: PURGE MEETINGS
    // ----------------------------------------------------------------

    private function purgeMeetings(array $log): array {
        $stmt = $this->db->prepare(
            "SELECT id, selfie_path
             FROM client_meetings
             WHERE created_at < NOW() - INTERVAL :days DAY"
        );
        $stmt->execute([':days' => self::RETENTION_DAYS]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($rows)) return $log;

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int) $row['id'];
            if (!empty($row['selfie_path'])) {
                $result = $this->deleteFile($row['selfie_path']);
                if ($result === true)  $log['files_deleted']++;
                elseif ($result === false) $log['files_missing']++;
            }
        }

        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $del = $this->db->prepare("DELETE FROM client_meetings WHERE id IN ($placeholders)");
            $del->execute($ids);
            $log['meetings_deleted'] = $del->rowCount();
        }

        return $log;
    }

    // ----------------------------------------------------------------
    // PRIVATE: FILE DELETE HELPER
    // ----------------------------------------------------------------

    /**
     * Deletes a file relative to basePath.
     * Returns: true = deleted, false = not found, null = empty path
     */
    private function deleteFile(string $path): ?bool {
        if (empty($path)) return null;

        // Handle both relative and absolute paths
        $fullPath = (str_starts_with($path, '/') || preg_match('/^[A-Za-z]:\\\\/', $path))
            ? $path
            : $this->basePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\');

        if (file_exists($fullPath)) {
            @unlink($fullPath);
            return true;
        }
        return false;
    }
}
