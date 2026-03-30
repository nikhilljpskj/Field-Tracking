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
        $stmt = $this->db->prepare("SELECT m.*, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    WHERE m.user_id = ? AND m.meeting_time BETWEEN ? AND ? 
                                    ORDER BY m.meeting_time DESC");
        $stmt->execute([$user_id, $start . ' 00:00:00', $end . ' 23:59:59']);
        return $stmt->fetchAll();
    }

    public function getMonthlyUserStats($user_id, $month, $year) {
        $stmt = $this->db->prepare("SELECT m.*, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    WHERE m.user_id = ? AND MONTH(m.meeting_time) = ? AND YEAR(m.meeting_time) = ? 
                                    ORDER BY m.meeting_time DESC");
        $stmt->execute([$user_id, $month, $year]);
        return $stmt->fetchAll();
    }

    public function getUserMeetings($user_id) {
        $stmt = $this->db->prepare("SELECT m.*, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
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
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    JOIN users u ON m.user_id = u.id 
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    WHERE m.user_id IN ($in) 
                                    ORDER BY m.meeting_time DESC");
        $stmt->execute($team_ids);
        return $stmt->fetchAll();
    }

    public function getAllMeetings($limit = 100) {
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    JOIN users u ON m.user_id = u.id 
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
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name, d.name as referenced_doctor_name 
                                    FROM client_meetings m 
                                    JOIN users u ON m.user_id = u.id 
                                    LEFT JOIN doctors d ON m.referenced_doctor_id = d.id
                                    WHERE m.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getMonthlySummary($user_id, $month, $year) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count, 
                                    COUNT(DISTINCT DATE(meeting_time)) as active_days
                                    FROM client_meetings 
                                    WHERE user_id = ? AND MONTH(meeting_time) = ? AND YEAR(meeting_time) = ?");
        $stmt->execute([$user_id, $month, $year]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM client_meetings WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
