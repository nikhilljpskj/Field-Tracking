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

    public function getDailyDistance($user_id, $date) {
        $logs = $this->getRoute($user_id, $date);
        if (count($logs) < 2) return 0;

        $totalDistance = 0;
        for ($i = 0; $i < count($logs) - 1; $i++) {
            $totalDistance += $this->calculateHaversine(
                $logs[$i]['latitude'], $logs[$i]['longitude'],
                $logs[$i+1]['latitude'], $logs[$i+1]['longitude']
            );
        }
        return $totalDistance / 1000; // Return in KM
    }

    private function calculateHaversine($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earth_radius * $c;
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
