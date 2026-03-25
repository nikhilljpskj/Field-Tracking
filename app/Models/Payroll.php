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
        $fields = [
            'user_id', 'basic', 'hra', 'da', 'special_allowance', 'conveyance_allowance', 
            'medical_allowance', 'education_allowance', 'uniform_allowance', 'food_allowance', 
            'telephone_allowance', 'books_allowance', 'research_allowance', 'cca', 
            'transport_allowance', 'pf_deduction', 'tax_deduction', 'total_ctc',
            'pf_employer_contribution', 'eps_contribution', 'esi_employer_contribution',
            'gratuity_provision', 'bonus_provision', 'lwf_employee', 'lwf_employer',
            'surcharge', 'advance_recovery', 'loan_emi', 'tds_monthly', 'professional_tax',
            'deduction_80c', 'deduction_80d', 'prev_employer_tds', 'total_working_days'
        ];
        
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $updates = implode(', ', array_map(fn($f) => "$f = VALUES($f)", array_slice($fields, 1)));

        $sql = "INSERT INTO salary_structures (" . implode(', ', $fields) . ") 
                VALUES ($placeholders) 
                ON DUPLICATE KEY UPDATE $updates";
        
        $stmt = $this->db->prepare($sql);
        $values = array_map(fn($f) => $data[$f] ?? 0, $fields);
        return $stmt->execute($values);
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
        if (!$structure) return false;

        $calc = $this->calculate($structure);
        $daysInMonth = $structure['total_working_days'] ?? cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $perDaySalary = $calc['gross'] / $daysInMonth;
        
        $lopAmount = $lopDays * $perDaySalary;
        $netSalary = $calc['net'] - $lopAmount; // net of statutory + lop

        $breakdown = $calc;
        $breakdown['lop_days'] = $lopDays;
        $breakdown['lop_amount'] = $lopAmount;
        $breakdown['total_tada'] = (float)$totalTada;
        
        $stmt = $this->db->prepare("INSERT INTO payroll_history (user_id, month, year, gross_salary, lop_deductions, net_salary, processed_by, breakdown_json, monthly_ctc, annual_ctc) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE gross_salary = VALUES(gross_salary), lop_deductions = VALUES(lop_deductions), net_salary = VALUES(net_salary), processed_by = VALUES(processed_by), breakdown_json = VALUES(breakdown_json)");
        return $stmt->execute([
            $user_id, $month, $year, $calc['gross'], $lopAmount, $netSalary, $processed_by, json_encode($breakdown), $calc['monthly_ctc'], $calc['annual_ctc']
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
