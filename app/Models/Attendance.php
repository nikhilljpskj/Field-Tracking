<?php
namespace App\Models;

use App\Core\Model;

class Attendance extends Model {
    public function getTodayAttendance($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM attendance WHERE user_id = ? AND DATE(created_at) = CURDATE() ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public function getSessionsForToday($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM attendance WHERE user_id = ? AND DATE(created_at) = CURDATE() ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getMonthlyHistory($user_id, $month = null, $year = null) {
        $month = $month ?: date('n');
        $year = $year ?: date('Y');
        
        $stmt = $this->db->prepare("SELECT * FROM attendance 
                                    WHERE user_id = ? 
                                    AND MONTH(check_in_time) = ? 
                                    AND YEAR(check_in_time) = ? 
                                    ORDER BY check_in_time ASC");
        $stmt->execute([$user_id, $month, $year]);
        return $stmt->fetchAll();
    }

    public function checkIn($user_id, $lat, $lng, $address, $photo_path = null, $odometer_path = null, $odometer_reading = null, $ticket_details = null) {
        $stmt = $this->db->prepare("INSERT INTO attendance (user_id, check_in_time, check_in_lat, check_in_lng, check_in_address, check_in_photo, odometer_photo, odometer_reading, ticket_details) 
                                    VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, $lat, $lng, $address, $photo_path, $odometer_path, $odometer_reading, $ticket_details]);
    }

    public function checkOut($id, $lat, $lng, $address, $photo_path = null, $odometer_path = null, $odometer_reading = null) {
        $stmt = $this->db->prepare("UPDATE attendance SET check_out_time = NOW(), check_out_lat = ?, check_out_lng = ?, check_out_address = ?, check_out_photo = ?, check_out_odometer_photo = ?, check_out_odometer_reading = ? WHERE id = ?");
        return $stmt->execute([$lat, $lng, $address, $photo_path, $odometer_path, $odometer_reading, $id]);
    }

    public function getAllLatest($limit = 50) {
        $stmt = $this->db->prepare("SELECT a.*, u.name as user_name, u.phone as user_phone 
                                    FROM attendance a 
                                    JOIN users u ON a.user_id = u.id 
                                    ORDER BY a.created_at DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFilteredRecords($filters = []) {
        $sql = "SELECT a.*, u.name as user_name, u.phone as user_phone 
                FROM attendance a 
                JOIN users u ON a.user_id = u.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(a.check_in_time) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(a.check_in_time) <= ?";
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR a.check_in_address LIKE ? OR a.check_out_address LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY a.check_in_time DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT a.*, u.name as user_name 
                                    FROM attendance a 
                                    JOIN users u ON a.user_id = u.id 
                                    WHERE a.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE attendance SET check_in_time = ?, check_out_time = ?, check_in_lat = ?, check_in_lng = ?, check_out_lat = ?, check_out_lng = ?, check_in_address = ?, check_out_address = ? WHERE id = ?");
        return $stmt->execute([
            $data['check_in_time'],
            $data['check_out_time'],
            $data['check_in_lat'],
            $data['check_in_lng'],
            $data['check_out_lat'],
            $data['check_out_lng'],
            $data['check_in_address'],
            $data['check_out_address'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM attendance WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
