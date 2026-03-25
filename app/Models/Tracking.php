<?php
namespace App\Models;

use App\Core\Model;

class Tracking extends Model {
    public function logLocation($user_id, $lat, $lng, $accuracy) {
        $stmt = $this->db->prepare("INSERT INTO location_logs (user_id, latitude, longitude, accuracy) 
                                    VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_id, $lat, $lng, $accuracy]);
    }

    public function getRoute($user_id, $date) {
        $stmt = $this->db->prepare("SELECT * FROM location_logs 
                                    WHERE user_id = ? AND DATE(logged_at) = ? 
                                    ORDER BY logged_at ASC");
        $stmt->execute([$user_id, $date]);
        return $stmt->fetchAll();
    }

    public function getTeamLastLocations($team_ids) {
        if (empty($team_ids)) return [];
        $in = implode(',', array_fill(0, count($team_ids), '?'));
        // Subquery to get the latest log for each user in the team
        $sql = "SELECT l.*, u.name as user_name 
                FROM location_logs l
                JOIN (
                    SELECT user_id, MAX(logged_at) as max_logged_at
                    FROM location_logs
                    WHERE user_id IN ($in)
                    GROUP BY user_id
                ) latest ON l.user_id = latest.user_id AND l.logged_at = latest.max_logged_at
                JOIN users u ON l.user_id = u.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($team_ids);
        return $stmt->fetchAll();
    }
}
