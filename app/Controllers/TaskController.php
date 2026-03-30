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
                $subType = $_POST['submission_type'] ?? 'Final'; // Partial or Final
                
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
                
                $inhouseModel->completeTask($taskId, $details, $filePath, $comment, $subType);
                
                // Fetch info for notification
                $task = $inhouseModel->getTaskById($taskId);
                if ($task) {
                    $db = \Database::getInstance()->getConnection();
                    $stmt = $db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, 'TaskSubmitted', ?)");
                    $title = ($subType === 'Partial') ? "Partial Submission ($task[task_name]) by $task[assignee_name]" : "Final Submission ($task[task_name]) by $task[assignee_name]";
                    $stmt->execute([$task['assigned_by'], $title]);
                }
                
                $_SESSION['flash_success'] = ($subType === 'Partial') ? "Task saved as Partial Submission!" : "Task locked and sent for Manager Approval!";
            }
            elseif ($action === 'approve') {
                $this->checkRole(['Admin', 'Manager']);
                $inhouseModel->approveTask($taskId);
                
                $task = $inhouseModel->getTaskById($taskId);
                $db = \Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, 'TaskApproved', ?)");
                $stmt->execute([$task['assigned_to'], "Good job! Task Approved: " . $task['task_name']]);
                
                $_SESSION['flash_success'] = "Task officially complete and approved.";
            }
            elseif ($action === 'revision') {
                $this->checkRole(['Admin', 'Manager']);
                $feedback = $_POST['manager_feedback'] ?? '';
                $inhouseModel->requestRevisionTask($taskId, $feedback);
                
                $task = $inhouseModel->getTaskById($taskId);
                $db = \Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, 'TaskRevision', ?)");
                $stmt->execute([$task['assigned_to'], "Revision Requested for: " . $task['task_name'] . " - Please check the task details."]);
                
                $_SESSION['flash_success'] = "Revision notification sent to the assignee.";
            }
        }
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'tasks';
        header("Location: $redirect");
        exit;
    }

    public function editInhouse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'])) {
            $db = \Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE inhouse_tasks SET task_name = ?, requirements = ?, deadline = ? WHERE id = ?");
            $result = $stmt->execute([
                $_POST['task_name'], 
                $_POST['requirements'], 
                $_POST['deadline'], 
                $_POST['task_id']
            ]);
            if ($result) {
                $_SESSION['flash_success'] = "In-House Task successfully updated.";
            } else {
                $_SESSION['flash_error'] = "Failed to update task.";
            }
        }
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'tasks';
        header("Location: $redirect");
        exit;
    }

    public function deleteInhouse() {
        $this->checkRole(['Admin']); // Only Admin can delete
        if (isset($_GET['id'])) {
            $db = \Database::getInstance()->getConnection();
            // Fetch file to delete if necessary
            $stmt = $db->prepare("SELECT attachment_path, completion_file_path FROM inhouse_tasks WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $task = $stmt->fetch();
            if ($task) {
                if ($task['attachment_path'] && file_exists(BASE_PATH . '/' . $task['attachment_path'])) {
                    @unlink(BASE_PATH . '/' . $task['attachment_path']);
                }
                if ($task['completion_file_path'] && file_exists(BASE_PATH . '/' . $task['completion_file_path'])) {
                    @unlink(BASE_PATH . '/' . $task['completion_file_path']);
                }
            }
            $stmt = $db->prepare("DELETE FROM inhouse_tasks WHERE id = ?");
            $result = $stmt->execute([$_GET['id']]);
            if ($result) {
                $_SESSION['flash_success'] = "In-House Task deleted permanently.";
            } else {
                $_SESSION['flash_error'] = "Failed to delete task.";
            }
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
