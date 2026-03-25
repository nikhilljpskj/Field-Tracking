<?php
namespace App\Models;

use App\Core\Model;

class Task extends Model {
    public function getTasksForUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE assigned_to = ? ORDER BY visit_date ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function updateTaskStatus($task_id, $status) {
        $stmt = $this->db->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $task_id]);
    }

    public function createTask($data) {
        $stmt = $this->db->prepare("INSERT INTO tasks (assigned_by, assigned_to, hospital_office_name, location_desc, target_desc, visit_date, priority, notes) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['assigned_by'],
            $data['assigned_to'],
            $data['hospital_name'],
            $data['location'],
            $data['target'],
            $data['visit_date'],
            $data['priority'],
            $data['notes']
        ]);
    }
}
