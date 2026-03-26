<?php
namespace App\Models;

use App\Core\Model;

class Attendance extends Model {
    public function getTodayAttendance($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM attendance WHERE user_id = ? AND DATE(created_at) = CURDATE() ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
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
        $stmt = $this->db->prepare("SELECT a.*, u.name as user_name 
                                    FROM attendance a 
                                    JOIN users u ON a.user_id = u.id 
                                    ORDER BY a.created_at DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
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
