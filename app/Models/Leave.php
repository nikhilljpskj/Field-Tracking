<?php
namespace App\Models;

use App\Core\Model;

class Leave extends Model {
    public function getLeaveTypes() {
        return $this->db->query("SELECT * FROM leave_types ORDER BY name ASC")->fetchAll();
    }

    public function getAllocations($user_id, $year = null) {
        $year = $year ?: date('Y');
        $stmt = $this->db->prepare("SELECT la.*, lt.name as type_name 
                                    FROM leave_allocations la 
                                    JOIN leave_types lt ON la.leave_type_id = lt.id 
                                    WHERE la.user_id = ? AND la.year = ?");
        $stmt->execute([$user_id, $year]);
        return $stmt->fetchAll();
    }

    public function getApplications($user_id) {
        $stmt = $this->db->prepare("SELECT la.*, lt.name as type_name, u.name as approved_by_name 
                                    FROM leave_applications la 
                                    JOIN leave_types lt ON la.leave_type_id = lt.id 
                                    LEFT JOIN users u ON la.approved_by = u.id 
                                    WHERE la.user_id = ? 
                                    ORDER BY la.created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getApprovedLeavesForMonth($user_id, $month = null, $year = null) {
        $month = $month ?: date('n');
        $year = $year ?: date('Y');
        
        // Find leaves that overlap with the targeted month
        $stmt = $this->db->prepare("SELECT la.*, lt.name as type_name 
                                    FROM leave_applications la 
                                    JOIN leave_types lt ON la.leave_type_id = lt.id 
                                    WHERE la.user_id = ? 
                                    AND la.status = 'Approved' 
                                    AND (
                                        (MONTH(la.start_date) = ? AND YEAR(la.start_date) = ?) OR 
                                        (MONTH(la.end_date) = ? AND YEAR(la.end_date) = ?)
                                    )
                                    ORDER BY la.start_date ASC");
        $stmt->execute([$user_id, $month, $year, $month, $year]);
        return $stmt->fetchAll();
    }

    public function apply($data) {
        $stmt = $this->db->prepare("INSERT INTO leave_applications (user_id, leave_type_id, start_date, end_date, is_half_day, reason, status) 
                                    VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        return $stmt->execute([
            $data['user_id'],
            $data['leave_type_id'],
            $data['start_date'],
            $data['end_date'],
            $data['is_half_day'],
            $data['reason']
        ]);
    }

    public function getPendingForManager($manager_id) {
        $stmt = $this->db->prepare("SELECT la.*, lt.name as type_name, u.name as user_name 
                                    FROM leave_applications la 
                                    JOIN leave_types lt ON la.leave_type_id = lt.id 
                                    JOIN users u ON la.user_id = u.id 
                                    WHERE u.manager_id = ? AND la.status = 'Pending' 
                                    ORDER BY la.created_at ASC");
        $stmt->execute([$manager_id]);
        return $stmt->fetchAll();
    }

    public function updateStatus($application_id, $status, $approved_by, $lop_days = 0) {
        $stmt = $this->db->prepare("UPDATE leave_applications SET status = ?, approved_by = ?, lop_days = ? WHERE id = ?");
        return $stmt->execute([$status, $approved_by, $lop_days, $application_id]);
    }

    public function getBalance($user_id, $leave_type_id, $quarter, $year) {
        $stmt = $this->db->prepare("SELECT (allocated - used) as balance 
                                    FROM leave_allocations 
                                    WHERE user_id = ? AND leave_type_id = ? AND quarter = ? AND year = ?");
        $stmt->execute([$user_id, $leave_type_id, $quarter, $year]);
        $res = $stmt->fetch();
        return $res ? (float)$res['balance'] : 0;
    }

    public function updateUsedBalance($user_id, $leave_type_id, $quarter, $year, $days) {
        $stmt = $this->db->prepare("UPDATE leave_allocations SET used = used + ? 
                                    WHERE user_id = ? AND leave_type_id = ? AND quarter = ? AND year = ?");
        return $stmt->execute([$days, $user_id, $leave_type_id, $quarter, $year]);
    }

    public function allocate($user_id, $leave_type_id, $quarter, $year, $days) {
        $stmt = $this->db->prepare("INSERT INTO leave_allocations (user_id, leave_type_id, quarter, year, allocated) 
                                    VALUES (?, ?, ?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE allocated = ?");
        return $stmt->execute([$user_id, $leave_type_id, $quarter, $year, $days, $days]);
    }
}
