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
        
        $stmt = \Database::getInstance()->getConnection()->query("SELECT u.*, r.name as role_name, s.* 
                                                                  FROM users u 
                                                                  JOIN roles r ON u.role_id = r.id 
                                                                  LEFT JOIN salary_structures s ON u.id = s.user_id 
                                                                  ORDER BY u.name ASC");
        $users = $stmt->fetchAll();

        $stmt = \Database::getInstance()->getConnection()->query("SELECT * FROM salary_templates ORDER BY name ASC");
        $templates = $stmt->fetchAll();
        
        $data = [
            'title' => 'Enterprise Payroll - Calculator',
            'users' => $users,
            'templates' => $templates
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

    public function saveDraft() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($_SESSION['role'], ['Admin', 'HR'])) {
            $payrollModel = new Payroll();
            if ($payrollModel->saveSalaryStructure($_POST)) {
                echo json_encode(['status' => 'success', 'message' => 'Draft saved successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save draft.']);
            }
        }
        exit;
    }

    public function exportCSV() {
        if (!in_array($_SESSION['role'], ['Admin', 'HR'])) exit;
        
        $filename = "payroll_export_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Employee Name', 'Basic', 'HRA', 'DA', 'Gross', 'Deductions', 'Net Salary', 'Monthly CTC']);
        
        $userModel = new User();
        $payrollModel = new Payroll();
        $stmt = \Database::getInstance()->getConnection()->query("SELECT u.name, s.* FROM users u JOIN salary_structures s ON u.id = s.user_id");
        while ($row = $stmt->fetch()) {
            // Re-calculate basic totals for CSV
            $gross = $row['basic'] + $row['hra'] + $row['da'] + $row['special_allowance']; // simplified for CSV
            fputcsv($output, [$row['name'], $row['basic'], $row['hra'], $row['da'], $gross, $row['pf_deduction'], $row['total_ctc'] / 12, $row['total_ctc'] / 12]);
        }
        fclose($output);
        exit;
    }
}
