<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\InhouseTask;

class TaskController extends Controller {
    public function index() {
        // If Manager/Admin, show the team task management, else show personal tasks
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager'])) {
            return $this->manage();
        }

        $taskModel = new Task();
        $inhouseModel = new InhouseTask();
        $userModel = new User();
        
        $tasks = $taskModel->getTasksForUser($_SESSION['user_id']);
        $inhouseTasks = $inhouseModel->getTasksForUser($_SESSION['user_id']);
        $overdueTasks = $inhouseModel->getOverdueTasks($_SESSION['user_id']);
        
        // Employees can assign inhouse tasks to themselves or others in their team (if restricted, here we allow all or team)
        $team = (in_array($_SESSION['role'], ['Admin'])) ? $userModel->getAll() : $userModel->getAll(); 
        
        $data = [
            'title' => 'Daily Tasks - Sales Tracking',
            'tasks' => $tasks,
            'inhouseTasks' => $inhouseTasks,
            'overdueTasks' => $overdueTasks,
            'team' => $team
        ];
        $this->view('tasks', $data);
    }

    public function manage() {
        $this->checkRole(['Admin', 'Manager']);
        $taskModel = new Task();
        $userModel = new User();
        
        // For manager, get only their team
        if ($_SESSION['role'] == 'Manager') {
            $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
            $teamIds = array_column($team, 'id');
            // Mocking a method to get tasks for a group or just the team members specifically
            $tasks = []; 
            if (!empty($teamIds)) {
                $db = \Database::getInstance()->getConnection();
                $in = implode(',', array_fill(0, count($teamIds), '?'));
                $stmt = $db->prepare("SELECT t.*, u.name as executive_name FROM tasks t JOIN users u ON t.assigned_to = u.id WHERE t.assigned_to IN ($in) ORDER BY t.visit_date DESC");
                $stmt->execute($teamIds);
                $tasks = $stmt->fetchAll();
            }
        } else {
            // Admin sees everything
            $db = \Database::getInstance()->getConnection();
            $tasks = $db->query("SELECT t.*, u.name as executive_name FROM tasks t JOIN users u ON t.assigned_to = u.id ORDER BY t.visit_date DESC LIMIT 100")->fetchAll();
            $team = $userModel->getAll(); // Everyone is available for admin
        }
        
        $inhouseModel = new InhouseTask();
        $inhouseTasks = $inhouseModel->getTeamTasks(($_SESSION['role'] == 'Manager') ? $_SESSION['user_id'] : null);
        
        $data = [
            'title' => 'Team Task Assignment',
            'tasks' => $tasks,
            'inhouseTasks' => $inhouseTasks,
            'team' => $team
        ];
        $this->view('tasks_manage', $data);
    }

    public function create() {
        $this->checkRole(['Admin', 'Manager']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $taskModel = new Task();
            $_POST['assigned_by'] = $_SESSION['user_id'];
            $result = $taskModel->createTask($_POST);
            if ($result) {
                $_SESSION['flash_success'] = "Task assigned successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to assign task.";
            }
        }
        $this->redirect('tasks');
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'])) {
            $taskModel = new Task();
            $result = $taskModel->updateTaskStatus($_POST['task_id'], $_POST['status']);
            if ($result) {
                $_SESSION['flash_success'] = "Task status updated!";
            } else {
                $_SESSION['flash_error'] = "Failed to update task.";
            }
        }
        $this->redirect('tasks');
    }

    public function createInhouse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $inhouseModel = new InhouseTask();
            
            $filePath = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                if (in_array(strtolower($ext), ['pdf', 'doc', 'docx', 'jpg', 'png'])) {
                    $uploadDir = 'uploads/tasks/';
                    if (!is_dir(BASE_PATH . '/' . $uploadDir)) {
                        mkdir(BASE_PATH . '/' . $uploadDir, 0777, true);
                    }
                    $filename = 'task_' . time() . '_' . rand(100,999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], BASE_PATH . '/' . $uploadDir . $filename)) {
                        $filePath = $uploadDir . $filename;
                    }
                }
            }

            $data = [
                'assigned_by' => $_SESSION['user_id'],
                'assigned_to' => $_POST['assigned_to'],
                'task_name' => $_POST['task_name'],
                'requirements' => $_POST['requirements'],
                'deadline' => $_POST['deadline'],
                'attachment_path' => $filePath
            ];

            $result = $inhouseModel->create($data);
            if ($result) {
                // Notify user
                $db = \Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, 'TaskAssigned', ?)");
                $stmt->execute([$data['assigned_to'], "New Task Assigned: " . $data['task_name'] . " (Deadline: " . date('M d', strtotime($data['deadline'])) . ")"]);
                
                $_SESSION['flash_success'] = "In-House Task assigned successfully.";
            } else {
                $_SESSION['flash_error'] = "Failed to assign in-house task.";
            }
        }
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'tasks';
        header("Location: $redirect");
        exit;
    }

    public function updateInhouse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'])) {
            $inhouseModel = new InhouseTask();
            $action = $_POST['action'];
            $taskId = $_POST['task_id'];
            
            if ($action === 'accept') {
                $comment = $_POST['acceptance_comment'] ?? '';
                $inhouseModel->acceptTask($taskId, $comment);
                $_SESSION['flash_success'] = "Task marked as Accepted.";
            } 
            elseif ($action === 'complete') {
                $details = $_POST['completion_details'] ?? '';
                $comment = $_POST['completion_comment'] ?? '';
                
                $filePath = null;
                if (isset($_FILES['completion_file']) && $_FILES['completion_file']['error'] == UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['completion_file']['name'], PATHINFO_EXTENSION);
                    $uploadDir = 'uploads/tasks_completed/';
                    if (!is_dir(BASE_PATH . '/' . $uploadDir)) {
                        mkdir(BASE_PATH . '/' . $uploadDir, 0777, true);
                    }
                    $filename = 'completed_' . $taskId . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['completion_file']['tmp_name'], BASE_PATH . '/' . $uploadDir . $filename)) {
                        $filePath = $uploadDir . $filename;
                    }
                }
                
                $inhouseModel->completeTask($taskId, $details, $filePath, $comment);
                $_SESSION['flash_success'] = "Task submitted as Completed!";
            }
            // Send back to caller view
        }
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'tasks';
        header("Location: $redirect");
        exit;
    }


    public function delete() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id'])) {
            $db = \Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM tasks WHERE id = ?");
            $result = $stmt->execute([$_GET['id']]);
            if ($result) {
                $_SESSION['flash_success'] = "Task deleted!";
            } else {
                $_SESSION['flash_error'] = "Failed to delete task.";
            }
        }
        $this->redirect('tasks');
    }
}
