<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Target;
use App\Models\User;

class TargetController extends Controller {
    public function index() {
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager'])) {
            return $this->manage();
        }

        $targetModel = new Target();
        $targets = $targetModel->getUserTargets($_SESSION['user_id']);
        
        $data = [
            'title' => 'Sales Targets - Sales Tracking',
            'targets' => $targets
        ];
        $this->view('targets', $data);
    }

    public function manage() {
        $this->checkRole(['Admin', 'Manager']);
        $targetModel = new Target();
        $userModel = new User();
        
        if ($_SESSION['role'] == 'Manager') {
            $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
            $teamIds = array_column($team, 'id');
            $targets = [];
            if (!empty($teamIds)) {
                $db = \Database::getInstance()->getConnection();
                $in = implode(',', array_fill(0, count($teamIds), '?'));
                $stmt = $db->prepare("SELECT t.*, u.name as executive_name FROM targets t JOIN users u ON t.user_id = u.id WHERE t.user_id IN ($in) ORDER BY t.end_date DESC");
                $stmt->execute($teamIds);
                $targets = $stmt->fetchAll();
            }
        } else {
            $db = \Database::getInstance()->getConnection();
            $targets = $db->query("SELECT t.*, u.name as executive_name FROM targets t JOIN users u ON t.user_id = u.id ORDER BY t.end_date DESC LIMIT 100")->fetchAll();
            $team = $userModel->getAll();
        }
        
        $data = [
            'title' => 'Team Performance Targets',
            'targets' => $targets,
            'team' => $team
        ];
        $this->view('targets_manage', $data);
    }

    public function create() {
        $this->checkRole(['Admin', 'Manager']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $targetModel = new Target();
            $result = $targetModel->createTarget($_POST);
            if ($result) {
                $_SESSION['flash_success'] = "Target assigned successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to assign target.";
            }
        }
        $this->redirect('targets');
    }

    public function delete() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id'])) {
            $db = \Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM targets WHERE id = ?");
            $result = $stmt->execute([$_GET['id']]);
            if ($result) {
                $_SESSION['flash_success'] = "Target removed!";
            } else {
                $_SESSION['flash_error'] = "Failed to remove target.";
            }
        }
        $this->redirect('targets');
    }
}
