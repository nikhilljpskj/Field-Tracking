<?php
namespace App\Models;

use App\Core\Model;

class Target extends Model {
    public function getUserTargets($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM targets WHERE user_id = ? ORDER BY end_date ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function updateAchievement($id, $value) {
        $stmt = $this->db->prepare("UPDATE targets SET achieved_value = achieved_value + ? WHERE id = ?");
        return $stmt->execute([$value, $id]);
    }

    public function createTarget($data) {
        $stmt = $this->db->prepare("INSERT INTO targets (user_id, type, period, target_value, start_date, end_date) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['user_id'],
            $data['type'],
            $data['period'],
            $data['target_value'],
            $data['start_date'],
            $data['end_date']
        ]);
    }
}
