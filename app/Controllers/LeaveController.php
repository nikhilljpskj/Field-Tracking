<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Leave;

class LeaveController extends Controller {
    public function index() {
        $leaveModel = new Leave();
        $applications = $leaveModel->getApplications($_SESSION['user_id']);
        $allocations = $leaveModel->getAllocations($_SESSION['user_id']);
        
        $data = [
            'title' => 'My Leaves - Sales Tracking',
            'applications' => $applications,
            'allocations' => $allocations,
            'leaveTypes' => $leaveModel->getLeaveTypes()
        ];
        $this->view('leave/index', $data);
    }

    public function apply() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $leaveModel = new Leave();
            
            $start = $_POST['start_date'];
            $end = $_POST['end_date'];
            $isHalf = isset($_POST['is_half_day']) ? 1 : 0;
            
            // Basic date calculation
            $diff = (strtotime($end) - strtotime($start)) / (60 * 60 * 24) + 1;
            $days = $isHalf ? 0.5 : $diff;

            // Check balance for current quarter
            $quarter = ceil(date('n', strtotime($start)) / 3);
            $year = date('Y', strtotime($start));
            $balance = $leaveModel->getBalance($_SESSION['user_id'], $_POST['leave_type_id'], $quarter, $year);

            if ($balance < $days) {
                $_SESSION['flash_error'] = "Insufficient leave balance. You only have $balance days available for this quarter.";
                $this->redirect('leaves');
                exit;
            }

            $data = [
                'user_id' => $_SESSION['user_id'],
                'leave_type_id' => $_POST['leave_type_id'],
                'start_date' => $start,
                'end_date' => $end,
                'is_half_day' => $isHalf,
                'reason' => $_POST['reason']
            ];

            if ($leaveModel->apply($data)) {
                $_SESSION['flash_success'] = "Leave application submitted successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to submit leave application.";
            }
        }
        $this->redirect('leaves');
    }

    public function manage() {
        // Only for Managers/Admin/HR
        if (!in_array($_SESSION['role'], ['Admin', 'Manager', 'HR'])) {
            $this->redirect('dashboard');
        }

        $leaveModel = new Leave();
        $pending = $leaveModel->getPendingForManager($_SESSION['user_id']);
        
        $data = [
            'title' => 'Manage Leaves - Sales Tracking',
            'pending' => $pending
        ];
        $this->view('leave/manage', $data);
    }

    public function allocate() {
        if (!in_array($_SESSION['role'], ['Admin', 'HR'])) {
            $this->redirect('dashboard');
        }

        $leaveModel = new Leave();
        $userModel = new \App\Models\User();
        
        $users = $userModel->getAll();
        $leaveTypes = $leaveModel->getLeaveTypes();
        
        // Structure allocations for easy access in view
        $allAllocations = []; // Current Quarter
        $annualAllocations = []; // Sum of all Quarters
        $quarter = ceil(date('n') / 3);
        $year = date('Y');
        
        foreach ($users as $u) {
            $uAllocations = $leaveModel->getAllocations($u['id'], $year);
            foreach ($uAllocations as $ua) {
                // Annual Sum
                $annualAllocations[$u['id']][$ua['leave_type_id']] = ($annualAllocations[$u['id']][$ua['leave_type_id']] ?? 0) + $ua['allocated'];
                
                // Current Quarter
                if ($ua['quarter'] == $quarter) {
                    $allAllocations[$u['id']][$ua['leave_type_id']] = $ua['allocated'];
                }
            }
        }

        $data = [
            'title' => 'Manage Quotas - Sales Tracking',
            'users' => $users,
            'leaveTypes' => $leaveTypes,
            'allAllocations' => $allAllocations,
            'annualAllocations' => $annualAllocations
        ];
        $this->view('leave/allocate', $data);
    }

    public function bulkAllocate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($_SESSION['role'], ['Admin', 'HR'])) {
            $leaveModel = new Leave();
            $userId = $_POST['user_id'];
            $quarter = $_POST['quarter'];
            $year = $_POST['year'];
            $allocations = $_POST['alloc'];

            foreach ($allocations as $typeId => $days) {
                $leaveModel->allocate($userId, $typeId, $quarter, $year, $days);
            }

            $_SESSION['flash_success'] = "Leave quota assigned successfully!";
        }
        $this->redirect('leave-allocate');
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $leaveModel = new Leave();
            $status = $_POST['status'];
            $id = $_POST['application_id'];
            $userId = $_POST['user_id'];
            $typeId = $_POST['leave_type_id'];
            $days = $_POST['days'];
            $lop = 0;

            if ($status == 'Approved') {
                $app = $this->db->query("SELECT * FROM leave_applications WHERE id = " . $id)->fetch(); // Simplified fetch
                $quarter = ceil(date('n', strtotime($app['start_date'])) / 3);
                $year = date('Y', strtotime($app['start_date']));
                $balance = $leaveModel->getBalance($userId, $typeId, $quarter, $year);
                
                if ($balance < $days) {
                    $lop = $days - $balance;
                    $daysToDeduct = $balance;
                } else {
                    $daysToDeduct = $days;
                }
                
                if ($daysToDeduct > 0) {
                    $leaveModel->updateUsedBalance($userId, $typeId, $quarter, $year, $daysToDeduct);
                }
            }

            if ($leaveModel->updateStatus($id, $status, $_SESSION['user_id'], $lop)) {
                $_SESSION['flash_success'] = "Leave $status successfully!";
            }
        }
    }

    public function seedDefaults() {
        if (!in_array($_SESSION['role'], ['Admin', 'HR'])) {
            $this->redirect('dashboard');
        }

        $db = \Database::getInstance()->getConnection();
        $sql = "INSERT INTO leave_types (name, quarterly_allocation) VALUES 
                ('Sick Leave', 1.5),
                ('Casual Leave', 1.5),
                ('Earned Leave', 3.0)
                ON DUPLICATE KEY UPDATE quarterly_allocation = VALUES(quarterly_allocation)";
        
        if ($db->query($sql)) {
            $_SESSION['flash_success'] = "Default leave types (SL, CL, EL) seeded successfully!";
        } else {
            $_SESSION['flash_error'] = "Failed to seed default leave types.";
        }
        $this->redirect('leave-allocate');
    }

    public function getBalance() {
        $leaveModel = new Leave();
        $typeId = $_GET['type_id'] ?? 0;
        $quarter = ceil(date('n') / 3);
        $year = date('Y');
        $balance = $leaveModel->getBalance($_SESSION['user_id'], $typeId, $quarter, $year);
        header('Content-Type: application/json');
        echo json_encode(['balance' => $balance]);
        exit;
    }
}
