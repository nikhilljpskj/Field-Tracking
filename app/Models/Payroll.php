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
            'deduction_80c', 'deduction_80d', 'prev_employer_tds', 'total_working_days',
            'is_statutory_enabled'
        ];
        
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $updates = implode(', ', array_map(fn($f) => "$f = VALUES($f)", array_slice($fields, 1)));

        $sql = "INSERT INTO salary_structures (" . implode(', ', $fields) . ") 
                VALUES ($placeholders) 
                ON DUPLICATE KEY UPDATE $updates";
        
        $stmt = $this->db->prepare($sql);
        $values = array_map(fn($f) => $data[$f] ?? 0, $fields);
        // Explicitly handle checkbox conversion if needed, but typically it comes as 0/1 or "on"
        $values[array_search('is_statutory_enabled', $fields)] = (isset($data['is_statutory_enabled']) && ($data['is_statutory_enabled'] == 1 || $data['is_statutory_enabled'] == 'on')) ? 1 : 0;
        
        return $stmt->execute($values);
    }

    public function calculate($data) {
        // Basic inputs
        $basic = (float)($data['basic'] ?? 0);
        $hraVal = (float)($data['hra'] ?? 0);
        $daVal = (float)($data['da'] ?? 0);
        $hraActual = ($hraVal < 100 && $hraVal > 0) ? ($basic * $hraVal / 100) : $hraVal;
        $daActual = ($daVal < 100 && $daVal > 0) ? ($basic * $daVal / 100) : $daVal;
        
        $totalEarnings = $basic + $hraActual + $daActual;
        $earningsFields = [
            'special_allowance', 'conveyance_allowance', 'medical_allowance',
            'education_allowance', 'uniform_allowance', 'food_allowance',
            'telephone_allowance', 'books_allowance', 'research_allowance',
            'cca', 'transport_allowance'
        ];
        foreach ($earningsFields as $f) $totalEarnings += (float)($data[$f] ?? 0);

        // Statutory
        $statutoryEnabled = (isset($data['is_statutory_enabled']) && ($data['is_statutory_enabled'] == 1 || $data['is_statutory_enabled'] == 'on'));
        
        $pfEmp = 0; $pfEmpr = 0; $eps = 0; $epfEmpr = 0; $esiEmp = 0; $esiEmpr = 0; $gratuity = 0; $bonus = 0; $tds = 0;
        
        if ($statutoryEnabled) {
            $pfWage = min($basic + $daActual, 15000);
            $pfEmp = ($basic + $daActual) * 0.12;
            $eps = $pfWage * 0.0833;
            $epfEmpr = $pfEmp - $eps;
            
            if ($totalEarnings <= 21000) {
                $esiEmp = ceil($totalEarnings * 0.0075);
                $esiEmpr = ceil($totalEarnings * 0.0325);
            }
            
            $gratuity = $basic * 0.0481;
            $bonus = $basic * 0.0833;
            
            $taxableAnnual = $totalEarnings * 12;
            $tds = ($taxableAnnual > 500000) ? ($taxableAnnual - 500000) * 0.1 / 12 : 0;
        }

        $otherDeductions = (float)($data['professional_tax'] ?? 0) + (float)($data['lwf_employee'] ?? 0) +
                           (float)($data['advance_recovery'] ?? 0) + (float)($data['loan_emi'] ?? 0) + $tds + $pfEmp + $esiEmp;
        
        $emprTotal = $pfEmp + $esiEmpr + $gratuity + $bonus + (float)($data['lwf_employer'] ?? 0);
        
        $results = [
            'gross' => $totalEarnings,
            'deductions' => $otherDeductions,
            'net' => $totalEarnings - $otherDeductions,
            'monthly_ctc' => $totalEarnings + $emprTotal,
            'annual_ctc' => ($totalEarnings + $emprTotal) * 12,
            'pf_employee' => $pfEmp,
            'pf_employer' => $pfEmpr,
            'eps' => $eps,
            'epf_balance' => $epfEmpr,
            'esi_employee' => $esiEmp,
            'esi_employer' => $esiEmpr,
            'gratuity' => $gratuity,
            'bonus' => $bonus,
            'tds' => $tds,
            'hra' => $hraActual, // Ensure actual value, not percentage
            'da' => $daActual    // Ensure actual value, not percentage
        ];

        // Merge input data to preserve basic salary components in the breakdown
        return array_merge($data, $results);
    }

    public function generatePayroll($user_id, $month, $year, $processed_by, $lopDays = 0) {
        $structure = $this->getSalaryStructure($user_id);
        if (!$structure) return false;

        $calc = $this->calculate($structure);
        
        $daysInMonth = $structure['total_working_days'] ?? 26;
        $perDaySalary = $calc['gross'] / ($daysInMonth ?: 26);
        $lopAmount = $lopDays * $perDaySalary;
        $netSalary = $calc['net'] - $lopAmount;

        $breakdown = $calc;
        $breakdown['lop_days'] = $lopDays;
        $breakdown['lop_amount'] = $lopAmount;
        $breakdown['net_salary'] = $netSalary; // Override net with LOP deduction
        
        $sql = "INSERT INTO payroll_history (user_id, month, year, gross_salary, lop_deductions, net_salary, processed_by, breakdown_json, monthly_ctc, annual_ctc) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                    gross_salary = VALUES(gross_salary), 
                    lop_deductions = VALUES(lop_deductions), 
                    net_salary = VALUES(net_salary), 
                    processed_by = VALUES(processed_by), 
                    breakdown_json = VALUES(breakdown_json), 
                    monthly_ctc = VALUES(monthly_ctc), 
                    annual_ctc = VALUES(annual_ctc)";
                    
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $user_id, $month, $year, $calc['gross'], $lopAmount, $netSalary, $processed_by, json_encode($breakdown), $calc['monthly_ctc'], $calc['annual_ctc']
        ]);
    }

    public function getPayrollHistory($user_id) {
        $stmt = $this->db->prepare("SELECT p.*, u.name as processed_by_name FROM payroll_history p LEFT JOIN users u ON p.processed_by = u.id WHERE p.user_id = ? ORDER BY p.year DESC, p.month DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}
