<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payroll;
use App\Models\User;

class PayrollController extends Controller {
    public function index() {
        $payrollModel = new Payroll();
        $history = $payrollModel->getPayrollHistory($_SESSION['user_id']);
        $structure = $payrollModel->getSalaryStructure($_SESSION['user_id']);
        
        $data = [
            'title' => 'My Payroll - Sales Tracking',
            'history' => $history,
            'structure' => $structure
        ];
        $this->view('payroll/index', $data);
    }

    public function manage() {
        if (!in_array($_SESSION['role'], ['Admin', 'HR'])) {
            $this->redirect('dashboard');
        }

        $userModel = new User();
        $payrollModel = new Payroll();
        
        $stmt = \Database::getInstance()->getConnection()->query("SELECT u.*, r.name as role_name, s.basic, s.hra, s.ta_da, s.other_allowances, s.pf_deduction, s.tax_deduction, s.total_ctc 
                                                                  FROM users u 
                                                                  JOIN roles r ON u.role_id = r.id 
                                                                  LEFT JOIN salary_structures s ON u.id = s.user_id 
                                                                  ORDER BY u.name ASC");
        $users = $stmt->fetchAll();
        
        $data = [
            'title' => 'Payroll Management - Admin/HR',
            'users' => $users
        ];
        $this->view('payroll/manage', $data);
    }

    public function saveStructure() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($_SESSION['role'], ['Admin', 'HR'])) {
            $payrollModel = new Payroll();
            if ($payrollModel->saveSalaryStructure($_POST)) {
                $_SESSION['flash_success'] = "Salary structure updated!";
            }
        }
        $this->redirect('payroll-manage');
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($_SESSION['role'], ['Admin', 'HR'])) {
            $payrollModel = new Payroll();
            $month = $_POST['month'];
            $year = $_POST['year'];
            $userId = $_POST['user_id'] ?? null;
            
            if ($userId) {
                // Single User
                if ($payrollModel->generatePayroll($userId, $month, $year, $_SESSION['user_id'])) {
                    $_SESSION['flash_success'] = "Payroll processed successfully!";
                } else {
                    $_SESSION['flash_error'] = "Failed to process payroll. Ensure salary structure is set.";
                }
            } else {
                // Bulk Process (All Executives/Managers)
                $userModel = new User();
                $users = $userModel->getAll();
                $count = 0;
                foreach ($users as $u) {
                    if ($payrollModel->generatePayroll($u['id'], $month, $year, $_SESSION['user_id'])) {
                        $count++;
                    }
                }
                $_SESSION['flash_success'] = "Processed payroll for $count employees.";
            }
        }
        $this->redirect('payroll-manage');
    }
}
