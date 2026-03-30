<?php
namespace App\Models;

use App\Core\Model;

class Travel extends Model {
    public function getCurrentRate() {
        $stmt = $this->db->query("SELECT rate_per_km FROM travel_rates ORDER BY created_at DESC LIMIT 1");
        $result = $stmt->fetch();
        return $result ? $result['rate_per_km'] : 8.00; // Default rate if none set
    }

    public function setRate($rate) {
        $stmt = $this->db->prepare("INSERT INTO travel_rates (rate_per_km) VALUES (?)");
        return $stmt->execute([$rate]);
    }

    public function getTravelSummary($user_id, $date) {
        $stmt = $this->db->prepare("SELECT * FROM travel_summary WHERE user_id = ? AND date = ?");
        $stmt->execute([$user_id, $date]);
        return $stmt->fetch();
    }

    public function updateTravelSummary($user_id, $date, $distance, $allowance) {
        $existing = $this->getTravelSummary($user_id, $date);
        if ($existing) {
            $stmt = $this->db->prepare("UPDATE travel_summary SET total_distance = ?, allowance_earned = ? WHERE id = ?");
            return $stmt->execute([$distance, $allowance, $existing['id']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO travel_summary (user_id, date, total_distance, allowance_earned) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$user_id, $date, $distance, $allowance]);
        }
    }

    public function updateStatus($id, $status, $auditor_id = null, $comments = null) {
        $stmt = $this->db->prepare("UPDATE travel_summary SET status = ?, approved_by = ?, admin_comments = ? WHERE id = ?");
        return $stmt->execute([$status, $auditor_id, $comments, $id]);
    }

    public function getTeamSummaries($team_ids) {
        if (empty($team_ids)) return [];
        $in = implode(',', array_fill(0, count($team_ids), '?'));
        $stmt = $this->db->prepare("SELECT s.*, u.name as user_name 
                                    FROM travel_summary s 
                                    JOIN users u ON s.user_id = u.id 
                                    WHERE s.user_id IN ($in) 
                                    ORDER BY s.date DESC");
        $stmt->execute($team_ids);
        return $stmt->fetchAll();
    }

    public function getAllSummaries($limit = 100) {
        $stmt = $this->db->prepare("SELECT s.*, u.name as user_name 
                                    FROM travel_summary s 
                                    JOIN users u ON s.user_id = u.id 
                                    ORDER BY s.date DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getAllRates() {
        return $this->db->query("SELECT * FROM travel_rates ORDER BY created_at DESC")->fetchAll();
    }

    public function getMonthlyUserTravel($user_id, $month, $year) {
        $stmt = $this->db->prepare("SELECT SUM(total_distance) as total_distance, 
                                    SUM(allowance_earned) as total_allowance
                                    FROM travel_summary 
                                    WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ?");
        $stmt->execute([$user_id, $month, $year]);
        return $stmt->fetch();
    }

    public function getMonthlyBreakdown($user_id, $month, $year) {
        $stmt = $this->db->prepare("SELECT s.*, 
                                    (SELECT COUNT(*) FROM client_meetings m WHERE m.user_id = s.user_id AND DATE(m.meeting_time) = s.date) as meeting_count
                                    FROM travel_summary s
                                    WHERE s.user_id = ? AND MONTH(s.date) = ? AND YEAR(s.date) = ?
                                    ORDER BY s.date DESC");
        $stmt->execute([$user_id, $month, $year]);
        return $stmt->fetchAll();
    }

    public function getWeeklyUserTravel($user_id, $start, $end) {
        $stmt = $this->db->prepare("SELECT SUM(total_distance) as total_distance, 
                                    SUM(allowance_earned) as total_allowance
                                    FROM travel_summary 
                                    WHERE user_id = ? AND date BETWEEN ? AND ?");
        $stmt->execute([$user_id, $start, $end]);
        return $stmt->fetch();
    }
}
