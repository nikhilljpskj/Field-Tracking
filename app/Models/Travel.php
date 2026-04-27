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
        $sql = "SELECT s.*, 
                       (SELECT COUNT(*) FROM client_meetings m WHERE m.user_id = s.user_id AND DATE(m.meeting_time) = s.date) as meeting_count
                FROM travel_summary s
                WHERE MONTH(s.date) = ? AND YEAR(s.date) = ?";
        
        $params = [$month, $year];
        if ($user_id !== 'all') {
            $sql .= " AND s.user_id = ?";
            $params[] = $user_id;
        }
        $sql .= " ORDER BY s.date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getMonthlyTeamAggregates($month, $year, $team_ids = null) {
        $sql = "SELECT u.id as user_id, u.name as user_name,
                       SUM(s.total_distance) as total_distance,
                       SUM(s.allowance_earned) as total_allowance,
                       COUNT(s.id) as active_days
                FROM users u
                LEFT JOIN travel_summary s ON u.id = s.user_id AND MONTH(s.date) = ? AND YEAR(s.date) = ?
                WHERE 1=1";
        
        $params = [$month, $year];
        if ($team_ids) {
            $in = implode(',', array_fill(0, count($team_ids), '?'));
            $sql .= " AND u.id IN ($in)";
            $params = array_merge($params, $team_ids);
        }

        $sql .= " GROUP BY u.id, u.name ORDER BY total_distance DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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

    public function calculateMilestoneDistance($user_id, $date) {
        // 1. Get Attendance (Check-in and Check-out)
        $stmt = $this->db->prepare("SELECT check_in_lat, check_in_lng, check_out_lat, check_out_lng 
                                     FROM attendance 
                                     WHERE user_id = ? AND DATE(check_in_time) = ? 
                                     ORDER BY check_in_time DESC LIMIT 1");
        $stmt->execute([$user_id, $date]);
        $att = $stmt->fetch();
        
        if (!$att || !$att['check_in_lat']) return 0;
        
        // 2. Get Meetings for the day
        $stmt = $this->db->prepare("SELECT latitude, longitude 
                                     FROM client_meetings 
                                     WHERE user_id = ? AND DATE(meeting_time) = ? 
                                     ORDER BY meeting_time ASC");
        $stmt->execute([$user_id, $date]);
        $meetings = $stmt->fetchAll();
        
        $points = [];
        $points[] = ['lat' => $att['check_in_lat'], 'lng' => $att['check_in_lng']];
        
        foreach ($meetings as $m) {
            if ($m['latitude'] && $m['longitude']) {
                $points[] = ['lat' => $m['latitude'], 'lng' => $m['longitude']];
            }
        }
        
        // 3. Add Checkout point if it exists
        if ($att['check_out_lat'] && $att['check_out_lng']) {
            $points[] = ['lat' => $att['check_out_lat'], 'lng' => $att['check_out_lng']];
        }
        
        if (count($points) < 2) return 0;
        
        $totalDistance = 0;
        for ($i = 0; $i < count($points) - 1; $i++) {
            $totalDistance += $this->haversine(
                $points[$i]['lat'], $points[$i]['lng'],
                $points[$i+1]['lat'], $points[$i+1]['lng']
            );
        }
        
        return $totalDistance / 1000; // Return in KM
    }

    private function haversine($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earth_radius * $c;
    }
}
