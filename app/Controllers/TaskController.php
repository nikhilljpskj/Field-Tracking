<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller {
    public function index() {
        // If Manager/Admin, show the team task management, else show personal tasks
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager'])) {
            return $this->manage();
        }

        $taskModel = new Task();
        $tasks = $taskModel->getTasksForUser($_SESSION['user_id']);
        
        $data = [
            'title' => 'Daily Tasks - Sales Tracking',
            'tasks' => $tasks
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
        
        $data = [
            'title' => 'Team Task Assignment',
            'tasks' => $tasks,
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
