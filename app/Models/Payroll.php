<?php
namespace App\Models;

use App\Core\Model;

class Payroll extends Model {
    public function getSalaryStructure($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM salary_structures WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public function saveSalaryStructure($data) {
        $stmt = $this->db->prepare("INSERT INTO salary_structures (user_id, basic, hra, ta_da, other_allowances, pf_deduction, tax_deduction, total_ctc) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE basic = ?, hra = ?, ta_da = ?, other_allowances = ?, pf_deduction = ?, tax_deduction = ?, total_ctc = ?");
        return $stmt->execute([
            $data['user_id'], $data['basic'], $data['hra'], $data['ta_da'], $data['other'], $data['pf'], $data['tax'], $data['ctc'],
            $data['basic'], $data['hra'], $data['ta_da'], $data['other'], $data['pf'], $data['tax'], $data['ctc']
        ]);
    }

    public function generatePayroll($user_id, $month, $year, $processed_by) {
        // 1. Get total working days (attendance)
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT DATE(check_in_time)) as work_days 
                                    FROM attendance 
                                    WHERE user_id = ? AND MONTH(check_in_time) = ? AND YEAR(check_in_time) = ? AND status = 'Present'");
        $stmt->execute([$user_id, $month, $year]);
        $workInfo = $stmt->fetch();
        $workDays = $workInfo['work_days'] ?? 0;

        // 2. Get LOP days from leaves (already approved for this month)
        $stmt = $this->db->prepare("SELECT SUM(lop_days) as total_lop 
                                    FROM leave_applications 
                                    WHERE user_id = ? AND status = 'Approved' 
                                    AND ((MONTH(start_date) = ? AND YEAR(start_date) = ?) OR (MONTH(end_date) = ? AND YEAR(end_date) = ?))");
        $stmt->execute([$user_id, $month, $year, $month, $year]);
        $leaveInfo = $stmt->fetch();
        $lopDays = (float)($leaveInfo['total_lop'] ?? 0);

        // 3. Get total TA/DA
        $stmt = $this->db->prepare("SELECT SUM(allowance_earned) as total_tada 
                                    FROM travel_summary 
                                    WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ? AND status = 'Approved'");
        $stmt->execute([$user_id, $month, $year]);
        $travelInfo = $stmt->fetch();
        $totalTada = $travelInfo['total_tada'] ?? 0;

        // 4. Get Salary Structure
        $structure = $this->getSalaryStructure($user_id);
        if (!$structure) {
            // If no structure, skip this user but return true (so bulk processing doesn't stop)
            return false;
        }

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $perDaySalary = $structure['basic'] / $daysInMonth;
        
        $grossSalary = $structure['basic'] + $structure['hra'] + $structure['other_allowances'] + $structure['ta_da'] + $totalTada;
        $lopAmount = $lopDays * $perDaySalary;
        
        $netSalary = $grossSalary - $lopAmount - $structure['pf_deduction'] - $structure['tax_deduction'];

        $stmt = $this->db->prepare("INSERT INTO payroll_history (user_id, month, year, gross_salary, lop_deductions, net_salary, processed_by) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE gross_salary = VALUES(gross_salary), lop_deductions = VALUES(lop_deductions), net_salary = VALUES(net_salary), processed_by = VALUES(processed_by)");
        return $stmt->execute([
            $user_id, $month, $year, $grossSalary, $lopAmount, $netSalary, $processed_by
        ]);
    }

    public function getPayrollHistory($user_id) {
        $stmt = $this->db->prepare("SELECT p.*, u.name as processed_by_name 
                                    FROM payroll_history p 
                                    LEFT JOIN users u ON p.processed_by = u.id 
                                    WHERE p.user_id = ? 
                                    ORDER BY p.year DESC, p.month DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}
