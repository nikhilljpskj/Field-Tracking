<?php
namespace App\Models;

use App\Core\Model;

class InhouseTask extends Model {
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO inhouse_tasks (assigned_by, assigned_to, task_name, requirements, deadline, attachment_path) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['assigned_by'],
            $data['assigned_to'],
            $data['task_name'],
            $data['requirements'],
            $data['deadline'],
            $data['attachment_path'] ?? null
        ]);
    }

    public function getTasksForUser($user_id) {
        $stmt = $this->db->prepare("SELECT t.*, u.name as assigner_name 
                                    FROM inhouse_tasks t
                                    JOIN users u ON t.assigned_by = u.id
                                    WHERE t.assigned_to = ? ORDER BY t.deadline ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getTaskById($id) {
        $stmt = $this->db->prepare("SELECT t.*, u.name as assigner_name, u2.name as assignee_name 
                                    FROM inhouse_tasks t
                                    JOIN users u ON t.assigned_by = u.id
                                    JOIN users u2 ON t.assigned_to = u2.id
                                    WHERE t.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function acceptTask($id, $comment) {
        $stmt = $this->db->prepare("UPDATE inhouse_tasks SET status = 'Accepted', accepted_at = NOW(), acceptance_comment = ? WHERE id = ?");
        return $stmt->execute([$comment, $id]);
    }

    public function completeTask($id, $details, $filePath, $comment, $subType = 'Final') {
        $status = ($subType === 'Partial') ? 'Partial Submitted' : 'Pending Approval';
        $stmt = $this->db->prepare("UPDATE inhouse_tasks SET status = ?, completed_at = NOW(), completion_details = ?, completion_file_path = ?, completion_comment = ? WHERE id = ?");
        return $stmt->execute([$status, $details, $filePath, $comment, $id]);
    }

    public function approveTask($id) {
        $stmt = $this->db->prepare("UPDATE inhouse_tasks SET status = 'Completed' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function requestRevisionTask($id, $feedback) {
        $stmt = $this->db->prepare("UPDATE inhouse_tasks SET status = 'Revision Requested', manager_feedback = ? WHERE id = ?");
        return $stmt->execute([$feedback, $id]);
    }
    
    public function getOverdueTasks($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM inhouse_tasks WHERE assigned_to = ? AND deadline < NOW() AND status NOT IN ('Completed', 'Pending Approval')");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getTeamTasks($manager_id = null) {
        if ($manager_id) {
            $userModel = new User();
            $team = $userModel->getExecutivesByManagerId($manager_id);
            if (empty($team)) return [];
            $teamIds = array_column($team, 'id');
            $in = implode(',', array_fill(0, count($teamIds), '?'));
            $stmt = $this->db->prepare("SELECT t.*, u.name as assignee_name, u2.name as assigner_name 
                                        FROM inhouse_tasks t
                                        JOIN users u ON t.assigned_to = u.id
                                        JOIN users u2 ON t.assigned_by = u2.id
                                        WHERE t.assigned_to IN ($in) ORDER BY t.deadline ASC");
            $stmt->execute($teamIds);
            return $stmt->fetchAll();
        } else {
            // Admin
            $stmt = $this->db->query("SELECT t.*, u.name as assignee_name, u2.name as assigner_name 
                                        FROM inhouse_tasks t
                                        JOIN users u ON t.assigned_to = u.id
                                        JOIN users u2 ON t.assigned_by = u2.id
                                        ORDER BY t.created_at DESC LIMIT 100");
            return $stmt->fetchAll();
        }
    }
}
