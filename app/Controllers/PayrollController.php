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
            'title' => 'My Payroll - Redeemer HRMS',
            'history' => $history,
            'structure' => $structure
        ];
        $this->view('payroll/index', $data);
    }

    public function payslip() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('payroll');

        $payrollModel = new Payroll();
        $userModel = new User();
        
        $stmt = \Database::getInstance()->getConnection()->prepare("SELECT * FROM payroll_history WHERE id = ?");
        $stmt->execute([$id]);
        $payroll = $stmt->fetch();

        if (!$payroll) $this->redirect('payroll');

        // Security check: only owner or Admin/HR can view
        if ($payroll['user_id'] != $_SESSION['user_id'] && !in_array($_SESSION['role'], ['Admin', 'HR'])) {
            $this->redirect('payroll');
        }

        $employee = $userModel->findById($payroll['user_id']);
        $breakdown = json_decode($payroll['breakdown_json'], true);

        $data = [
            'payroll' => $payroll,
            'employee' => $employee,
            'breakdown' => $breakdown
        ];
        // Render without layout for printing
        extract($data);
        include BASE_PATH . '/app/Views/payroll/payslip.php';
    }

    public function manage() {
        $role = strtoupper($_SESSION['role'] ?? '');
        if (!in_array($role, ['ADMIN', 'HR'])) {
            $this->redirect('dashboard');
        }

        $userModel = new User();
        $payrollModel = new Payroll();
        
        $stmt = \Database::getInstance()->getConnection()->query("SELECT s.*, r.name as role_name, u.* 
                                                                  FROM users u 
                                                                  JOIN roles r ON u.role_id = r.id 
                                                                  LEFT JOIN salary_structures s ON u.id = s.user_id 
                                                                  ORDER BY u.name ASC");
        $users = $stmt->fetchAll();

        $stmt = \Database::getInstance()->getConnection()->query("SELECT * FROM salary_templates ORDER BY name ASC");
        $templates = $stmt->fetchAll();
        
        $data = [
            'title' => 'Enterprise Payroll - Redeemer HRMS',
            'users' => $users,
            'templates' => $templates
        ];
        $this->view('payroll/manage', $data);
    }

    public function getHistory() {
        ob_clean();
        header('Content-Type: application/json');
        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User ID required']);
            exit;
        }

        $payrollModel = new Payroll();
        $history = $payrollModel->getPayrollHistory($user_id);
        echo json_encode(['status' => 'success', 'history' => $history]);
        exit;
    }

    public function saveStructure() {
        $role = strtoupper($_SESSION['role'] ?? '');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($role, ['ADMIN', 'HR'])) {
            $payrollModel = new Payroll();
            if ($payrollModel->saveSalaryStructure($_POST)) {
                $_SESSION['flash_success'] = "Salary structure updated!";
            }
        }
        $this->redirect('payroll-manage');
    }

    public function saveDraft() {
        ob_clean();
        header('Content-Type: application/json');
        $role = strtoupper($_SESSION['role'] ?? '');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($role, ['ADMIN', 'HR'])) {
            $payrollModel = new Payroll();
            try {
                $user_id = $_POST['user_id'] ?? null;
                $historyId = $_POST['history_id'] ?? null;
                
                if (!$user_id) {
                    throw new \Exception("Employee ID is required");
                }

                if ($historyId) {
                    // Update existing record
                    $db = \Database::getInstance()->getConnection();
                    $calc = $payrollModel->calculate($_POST);
                    
                    // HANDLE LOP IN EDIT MODE
                    $lopDays = (float)($_POST['lop_days'] ?? 0);
                    $daysInMonth = (float)($_POST['total_working_days'] ?? 26);
                    $perDaySalary = $calc['gross'] / ($daysInMonth ?: 26);
                    $lopAmount = $lopDays * $perDaySalary;
                    $netSalary = $calc['net'] - $lopAmount;
                    
                    $calc['lop_days'] = $lopDays;
                    $calc['lop_amount'] = $lopAmount;
                    $calc['net_salary'] = $netSalary;

                    $monthYear = explode('-', $_POST['calc_month']);
                    $month = $monthYear[1];
                    $year = $monthYear[0];
                    
                    $stmt = $db->prepare("UPDATE payroll_history SET 
                                            month = ?, year = ?, 
                                            gross_salary = ?, lop_deductions = ?, net_salary = ?, 
                                            breakdown_json = ?, 
                                            monthly_ctc = ?, annual_ctc = ?,
                                            processed_by = ?
                                          WHERE id = ?");
                    $stmt->execute([
                        $month, $year, 
                        $calc['gross'], $lopAmount, $netSalary, 
                        json_encode($calc), 
                        $calc['monthly_ctc'], $calc['annual_ctc'],
                        $_SESSION['user_id'],
                        $historyId
                    ]);
                    echo json_encode(['status' => 'success', 'message' => 'Payslip updated successfully!']);
                } else {
                    // 1. Save as structure draft (the "template")
                    $payrollModel->saveSalaryStructure($_POST);
                    
                    // 2. Generate/Update History Record for this specific month
                    $monthYear = explode('-', $_POST['calc_month']);
                    $month = (int)$monthYear[1];
                    $year = (int)$monthYear[0];
                    $lopDays = (float)($_POST['lop_days'] ?? 0);
                    
                    if ($payrollModel->generatePayroll($user_id, $month, $year, $_SESSION['user_id'], $lopDays)) {
                        echo json_encode(['status' => 'success', 'message' => 'Draft saved and History updated!']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Structure saved but history sync failed.']);
                    }
                }
            } catch (\Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized or invalid request method.']);
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
