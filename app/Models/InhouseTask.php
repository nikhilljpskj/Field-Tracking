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

    public function acceptTask($id, $comment, $user_id) {
        $stmt = $this->db->prepare("UPDATE inhouse_tasks SET status = 'Accepted', accepted_at = NOW(), acceptance_comment = ? WHERE id = ?");
        $this->logEvent($id, $user_id, 'Accepted', $comment);
        return $stmt->execute([$comment, $id]);
    }

    public function completeTask($id, $details, $filePath, $comment, $user_id, $subType = 'Final') {
        $status = ($subType === 'Partial') ? 'Partial Submitted' : 'Pending Approval';
        $stmt = $this->db->prepare("UPDATE inhouse_tasks SET status = ?, completed_at = NOW(), completion_details = ?, completion_file_path = ?, completion_comment = ? WHERE id = ?");
        $logText = "($subType Submission) Details: " . $details . ($comment ? " | Comment: " . $comment : "");
        $this->logEvent($id, $user_id, 'Submitted', $logText);
        return $stmt->execute([$status, $details, $filePath, $comment, $id]);
    }

    public function approveTask($id, $user_id) {
        $stmt = $this->db->prepare("UPDATE inhouse_tasks SET status = 'Completed' WHERE id = ?");
        $this->logEvent($id, $user_id, 'Approved', 'Task formally approved and closed.');
        return $stmt->execute([$id]);
    }

    public function requestRevisionTask($id, $feedback, $user_id) {
        $stmt = $this->db->prepare("UPDATE inhouse_tasks SET status = 'Revision Requested', manager_feedback = ? WHERE id = ?");
        $this->logEvent($id, $user_id, 'Revision Requested', $feedback);
        return $stmt->execute([$feedback, $id]);
    }
    
    public function logEvent($task_id, $user_id, $action, $message = null) {
        $stmt = $this->db->prepare("INSERT INTO inhouse_task_history (task_id, user_id, action, message) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$task_id, $user_id, $action, $message]);
    }

    public function getTaskHistory($task_id) {
        $stmt = $this->db->prepare("SELECT h.*, u.name as user_name FROM inhouse_task_history h 
                                    JOIN users u ON h.user_id = u.id 
                                    WHERE h.task_id = ? ORDER BY h.created_at ASC");
        $stmt->execute([$task_id]);
        return $stmt->fetchAll();
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
