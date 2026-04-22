<?php
namespace App\Models;

use App\Core\Model;

class Meeting extends Model {
    public function logMeeting($data) {
        $stmt = $this->db->prepare("INSERT INTO client_meetings (user_id, client_name, hospital_office_name, visit_category, meeting_type, notes, outcome, meeting_time, latitude, longitude, address, selfie_path, referenced_doctor_id) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['user_id'],
            $data['client_name'],
            $data['hospital_name'],
            $data['visit_category'] ?? 'Meeting',
            $data['meeting_type'],
            $data['notes'],
            $data['outcome'],
            $data['meeting_time'],
            $data['latitude'],
            $data['longitude'],
            $data['address'],
            $data['selfie_path'] ?? null,
            $data['referenced_doctor_id'] ?? null
        ]);
    }

    public function getWeeklyUserStats($user_id, $start, $end) {
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name, au.name as approver_name, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    JOIN users u ON m.user_id = u.id
                                    LEFT JOIN users au ON m.approved_by = au.id
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    WHERE m.user_id = ? AND m.meeting_time BETWEEN ? AND ? 
                                    ORDER BY m.meeting_time DESC");
        $stmt->execute([$user_id, $start . ' 00:00:00', $end . ' 23:59:59']);
        return $stmt->fetchAll();
    }

    public function getMonthlyUserStats($user_id, $month, $year) {
        $sql = "SELECT m.*, u.name as user_name, au.name as approver_name, d.name as referenced_doctor_name 
                FROM client_meetings m 
                JOIN users u ON m.user_id = u.id
                LEFT JOIN users au ON m.approved_by = au.id
                LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                WHERE MONTH(m.meeting_time) = ? AND YEAR(m.meeting_time) = ?";
        
        $params = [$month, $year];
        if ($user_id !== 'all') {
            $sql .= " AND m.user_id = ?";
            $params[] = $user_id;
        }
        $sql .= " ORDER BY m.meeting_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUserMeetings($user_id) {
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name, au.name as approver_name, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    JOIN users u ON m.user_id = u.id
                                    LEFT JOIN users au ON m.approved_by = au.id
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    WHERE m.user_id = ? ORDER BY m.meeting_time DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status, $auditor_id = null, $comments = null) {
        $stmt = $this->db->prepare("UPDATE client_meetings SET status = ?, approved_by = ?, admin_comments = ? WHERE id = ?");
        return $stmt->execute([$status, $auditor_id, $comments, $id]);
    }

    public function getTeamMeetings($team_ids) {
        if (empty($team_ids)) return [];
        $in = implode(',', array_fill(0, count($team_ids), '?'));
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name, au.name as approver_name, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    JOIN users u ON m.user_id = u.id 
                                    LEFT JOIN users au ON m.approved_by = au.id
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    WHERE m.user_id IN ($in) 
                                    ORDER BY m.meeting_time DESC");
        $stmt->execute($team_ids);
        return $stmt->fetchAll();
    }

    public function getAllMeetings($limit = 100) {
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name, au.name as approver_name, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    JOIN users u ON m.user_id = u.id 
                                    LEFT JOIN users au ON m.approved_by = au.id
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    ORDER BY m.meeting_time DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE client_meetings SET client_name = ?, hospital_office_name = ?, visit_category = ?, meeting_type = ?, notes = ?, outcome = ?, meeting_time = ? WHERE id = ?");
        return $stmt->execute([
            $data['client_name'],
            $data['hospital_name'],
            $data['visit_category'] ?? 'Meeting',
            $data['meeting_type'],
            $data['notes'],
            $data['outcome'],
            $data['meeting_time'],
            $id
        ]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name, au.name as approver_name, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    JOIN users u ON m.user_id = u.id 
                                    LEFT JOIN users au ON m.approved_by = au.id
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    WHERE m.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getMeetingsByDate($date, $user_id = 'all') {
        $sql = "SELECT m.*, u.name as user_name, au.name as approver_name, d.name as referenced_doctor_name 
                FROM client_meetings m 
                JOIN users u ON m.user_id = u.id 
                LEFT JOIN users au ON m.approved_by = au.id
                LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                WHERE DATE(m.meeting_time) = ?";
        
        $params = [$date];
        if ($user_id !== 'all') {
            $sql .= " AND m.user_id = ?";
            $params[] = $user_id;
        }
        $sql .= " ORDER BY m.meeting_time DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getMonthlySummary($user_id, $month, $year) {
        $sql = "SELECT COUNT(*) as count, 
                       COUNT(DISTINCT DATE(meeting_time)) as active_days
                FROM client_meetings 
                WHERE MONTH(meeting_time) = ? AND YEAR(meeting_time) = ?";
        
        $params = [$month, $year];
        if ($user_id !== 'all') {
            $sql .= " AND user_id = ?";
            $params[] = $user_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }


    public function getTeamMonthlyAggregates($month, $year, $team_ids = null) {
        $sql = "SELECT u.id as user_id, u.name as user_name, 
                       COUNT(m.id) as meeting_count,
                       COUNT(DISTINCT DATE(m.meeting_time)) as active_days
                FROM users u
                LEFT JOIN client_meetings m ON u.id = m.user_id AND MONTH(m.meeting_time) = ? AND YEAR(m.meeting_time) = ?
                WHERE 1=1";
        
        $params = [$month, $year];
        if ($team_ids) {
            $in = implode(',', array_fill(0, count($team_ids), '?'));
            $sql .= " AND u.id IN ($in)";
            $params = array_merge($params, $team_ids);
        }

        $sql .= " GROUP BY u.id, u.name ORDER BY meeting_count DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM client_meetings WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
