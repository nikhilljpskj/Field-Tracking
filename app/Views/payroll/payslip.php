<?php
if (!function_exists('number_to_words')) {
    function number_to_words($number) {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Forty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion',
            1000000000000       => 'Trillion',
            1000000000000000    => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );

        if (!is_numeric($number)) return false;
        $number = (int) $number;
        if ($number < 0) return $negative . number_to_words(abs($number));

        $string = $fraction = null;
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) $string .= $hyphen . $dictionary[$units];
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) $string .= $conjunction . number_to_words($remainder);
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= number_to_words($remainder);
                }
                break;
        }
        return $string;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payslip - <?php echo htmlspecialchars($employee['name']); ?> - <?php echo date("F Y", mktime(0, 0, 0, $payroll['month'], 10, $payroll['year'])); ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 40px; background: #f8f9fa; }
        .payslip-container { max-width: 850px; margin: 0 auto; background: #fff; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,0.05); border-radius: 8px; border: 1px solid #eee; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #4361ee; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { max-height: 70px; }
        .company-info h2 { margin: 0; color: #4361ee; font-size: 24px; }
        .company-info p { margin: 5px 0 0; color: #666; font-size: 13px; }
        .payslip-title { text-align: right; }
        .payslip-title h1 { margin: 0; font-size: 28px; text-transform: uppercase; color: #222; letter-spacing: 1px; }
        .payslip-title p { margin: 5px 0 0; font-weight: bold; color: #4361ee; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 30px; }
        .info-grid.single-col { grid-template-columns: 1fr; }
        .info-box h4 { margin: 0 0 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; font-size: 14px; text-transform: uppercase; color: #666; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px; }
        .info-label { color: #888; }
        .info-value { font-weight: 600; }

        .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .salary-table th { background: #f8f9fa; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 13px; text-transform: uppercase; color: #666; }
        .salary-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .section-title { font-weight: bold; color: #4361ee; background: #f0f3ff !important; }
        
        .totals-section { display: grid; grid-template-columns: 1fr 1fr; gap: 0; border: 1px solid #eee; border-radius: 4px; overflow: hidden; }
        .total-box { padding: 20px; }
        .total-box.earnings { background: #fff; }
        .total-box.deductions { background: #fff; border-left: 1px solid #eee; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .total-row.grand { margin-top: 15px; padding-top: 15px; border-top: 2px solid #eee; font-size: 18px; font-weight: bold; color: #222; }
        .net-salary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 5px solid #2563eb;
            border-radius: 6px;
            padding: 18px 24px;
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .net-salary-info { flex: 1; min-width: 0; }
        .net-salary-title {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .net-salary-words {
            font-size: 13.5px;
            color: #334155;
            line-height: 1.45;
        }
        .net-salary-words strong {
            color: #0f172a;
            font-weight: 600;
        }
        .net-salary-amount {
            font-size: 30px;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            letter-spacing: -0.5px;
            font-variant-numeric: tabular-nums;
        }
        .currency-symbol {
            font-size: 22px;
            font-weight: 600;
            margin-right: 2px;
            color: #475569;
        }

        @media (max-width: 650px) {
            .net-salary-card {
                flex-direction: column-reverse;
                text-align: center;
                gap: 12px;
                padding: 16px;
            }
            .net-salary-amount { text-align: center; }
            .net-salary-words { text-align: center; }
        }

        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        .billing-line { margin-top: 12px; font-size: 10px; font-weight: 300; color: #bbb; }
        
        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            * { box-sizing: border-box; }
            body { background: #fff; padding: 0; }
            .payslip-container { box-shadow: none; border: none; width: 100%; max-width: 100%; margin: 0; }
            .header, .info-grid, .info-row, .total-row { min-width: 0; }
            .company-info, .payslip-title, .info-box, .info-label, .info-value { min-width: 0; overflow-wrap: anywhere; }
            .net-salary-card {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border: 1px solid #cbd5e1 !important;
                border-left: 5px solid #1e293b !important;
                padding: 14px 20px !important;
                margin-top: 18px !important;
            }
            .net-salary-amount { color: #0f172a !important; font-size: 26px; }
            .footer { margin-top: 35px; }
            .no-print { display: none; }
        }
        
        .no-print-btn { position: fixed; top: 20px; right: 20px; background: #4361ee; color: #white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <button class="no-print no-print-btn" onclick="window.print()">
        <svg style="width:16px;height:16px;margin-right:8px;vertical-align:middle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Download / Print PDF
    </button>

    <div class="payslip-container">
        <div class="header">
            <div class="company-info">
                <img src="assets/images/redeemer-technologies-logo.png" alt="Logo" class="logo">
                <h2>Redeemer Technologies</h2>
                <p>CSBC III, Ashtamudi Tower, Technopark , Kollam, India</p>
                <p>Email: info@redeemertechnologies.com | Web: redeemertechnologies.com</p>
            </div>
            <div class="payslip-title">
                <h1>Payslip</h1>
                <p><?php echo date("F Y", mktime(0, 0, 0, $payroll['month'], 10, $payroll['year'])); ?></p>
            </div>
        </div>

        <?php
        // Prepare employee fields
        $empCode = !empty($employee['employee_code']) ? trim($employee['employee_code']) : ('EMP-' . str_pad($employee['id'], 4, '0', STR_PAD_LEFT));
        $designation = trim($employee['designation'] ?? '');
        $email = trim($employee['email'] ?? '');
        $phone = trim($employee['phone'] ?? '');

        // Prepare payment summary fields
        $bankName = trim($employee['bank_name'] ?? '');
        $hasBankName = ($bankName !== '' && strtoupper($bankName) !== 'N/A');

        $accountNumber = trim($employee['account_number'] ?? '');
        $hasAccountNumber = ($accountNumber !== '' && strtoupper($accountNumber) !== 'N/A');

        $ifscCode = trim($employee['ifsc_code'] ?? '');
        $hasIfscCode = ($ifscCode !== '' && strtoupper($ifscCode) !== 'N/A');

        $lopDays = (float)($breakdown['lop_days'] ?? 0);
        $hasLopDays = ($lopDays > 0);

        $hasPaymentSummary = ($hasBankName || $hasAccountNumber || $hasIfscCode || $hasLopDays);

        // Prepare earnings - ONLY filled (positive > 0) fields
        $earnings = [];
        $isPositive = function($val) {
            return is_numeric($val) && (float)$val > 0.0001;
        };

        $basicVal = $breakdown['basic'] ?? ($breakdown['basic_salary'] ?? 0);
        if ($isPositive($basicVal)) {
            $earnings[] = ['label' => 'Basic Salary', 'amount' => (float)$basicVal];
        }

        $hraVal = $breakdown['hra'] ?? 0;
        if ($isPositive($hraVal)) {
            $earnings[] = ['label' => 'HRA', 'amount' => (float)$hraVal];
        }

        $daVal = $breakdown['da'] ?? 0;
        if ($isPositive($daVal)) {
            $earnings[] = ['label' => 'DA', 'amount' => (float)$daVal];
        }

        $allowanceMap = [
            'special_allowance'    => 'Special Allowance',
            'conveyance_allowance' => 'Conveyance Allowance',
            'medical_allowance'    => 'Medical Allowance',
            'education_allowance'  => 'Education Allowance',
            'uniform_allowance'    => 'Uniform Allowance',
            'food_allowance'       => 'Food Allowance',
            'telephone_allowance'  => 'Telephone Allowance',
            'books_allowance'      => 'Books Allowance',
            'research_allowance'   => 'Research Allowance',
            'cca'                  => 'City Compensatory Allowance (CCA)',
            'transport_allowance'  => 'Transport Allowance',
            'other_allowance'      => 'Other Allowance',
        ];

        foreach ($allowanceMap as $key => $label) {
            if (isset($breakdown[$key]) && $isPositive($breakdown[$key])) {
                $earnings[] = ['label' => $label, 'amount' => (float)$breakdown[$key]];
            }
        }

        // Prepare deductions - ONLY filled (positive > 0) fields
        $deductions = [];

        $pfVal = $breakdown['pf_employee'] ?? ($breakdown['pf_deduction'] ?? 0);
        if ($isPositive($pfVal)) {
            $deductions[] = ['label' => 'Provident Fund (PF)', 'amount' => (float)$pfVal];
        }

        $ptVal = $breakdown['professional_tax'] ?? 0;
        if ($isPositive($ptVal)) {
            $deductions[] = ['label' => 'Professional Tax', 'amount' => (float)$ptVal];
        }

        $tdsVal = $breakdown['tds'] ?? ($breakdown['tds_monthly'] ?? ($breakdown['tax_deduction'] ?? 0));
        if ($isPositive($tdsVal)) {
            $deductions[] = ['label' => 'TDS / Income Tax', 'amount' => (float)$tdsVal];
        }

        $esiVal = $breakdown['esi_employee'] ?? 0;
        if ($isPositive($esiVal)) {
            $deductions[] = ['label' => 'ESI', 'amount' => (float)$esiVal];
        }

        $lopVal = $breakdown['lop_amount'] ?? ($breakdown['lop_deductions'] ?? ($payroll['lop_deductions'] ?? 0));
        if ($isPositive($lopVal)) {
            $deductions[] = ['label' => 'LOP Deductions', 'amount' => (float)$lopVal];
        }

        $otherDeductionMap = [
            'lwf_employee'     => 'Labour Welfare Fund (LWF)',
            'advance_recovery' => 'Advance Recovery',
            'loan_emi'         => 'Loan EMI',
        ];

        foreach ($otherDeductionMap as $key => $label) {
            if (isset($breakdown[$key]) && $isPositive($breakdown[$key])) {
                $deductions[] = ['label' => $label, 'amount' => (float)$breakdown[$key]];
            }
        }

        $maxRows = max(count($earnings), count($deductions));
        ?>

        <div class="info-grid <?php echo !$hasPaymentSummary ? 'single-col' : ''; ?>">
            <div class="info-box">
                <h4>Employee Details</h4>
                <?php if (!empty($employee['name'])): ?>
                    <div class="info-row"><span class="info-label">Name:</span> <span class="info-value"><?php echo htmlspecialchars($employee['name']); ?></span></div>
                <?php endif; ?>
                <?php if (!empty($empCode)): ?>
                    <div class="info-row"><span class="info-label">Employee ID:</span> <span class="info-value"><?php echo htmlspecialchars($empCode); ?></span></div>
                <?php endif; ?>
                <?php if (!empty($designation)): ?>
                    <div class="info-row"><span class="info-label">Designation:</span> <span class="info-value"><?php echo htmlspecialchars($designation); ?></span></div>
                <?php endif; ?>
                <?php if (!empty($email)): ?>
                    <div class="info-row"><span class="info-label">Email:</span> <span class="info-value"><?php echo htmlspecialchars($email); ?></span></div>
                <?php endif; ?>
                <?php if (!empty($phone)): ?>
                    <div class="info-row"><span class="info-label">Phone:</span> <span class="info-value"><?php echo htmlspecialchars($phone); ?></span></div>
                <?php endif; ?>
            </div>
            <?php if ($hasPaymentSummary): ?>
            <div class="info-box">
                <h4>Payment Summary</h4>
                <?php if ($hasBankName): ?>
                    <div class="info-row"><span class="info-label">Bank Name:</span> <span class="info-value"><?php echo htmlspecialchars($bankName); ?></span></div>
                <?php endif; ?>
                <?php if ($hasAccountNumber): ?>
                    <div class="info-row"><span class="info-label">Account No:</span> <span class="info-value"><?php echo htmlspecialchars($accountNumber); ?></span></div>
                <?php endif; ?>
                <?php if ($hasIfscCode): ?>
                    <div class="info-row"><span class="info-label">IFSC Code:</span> <span class="info-value"><?php echo htmlspecialchars($ifscCode); ?></span></div>
                <?php endif; ?>
                <?php if ($hasLopDays): ?>
                    <div class="info-row"><span class="info-label">LOP Days:</span> <span class="info-value text-danger"><?php echo htmlspecialchars($breakdown['lop_days']); ?></span></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <table class="salary-table">
            <thead>
                <tr>
                    <th>Earnings</th>
                    <th style="text-align: right">Amount (₹)</th>
                    <th>Deductions</th>
                    <th style="text-align: right">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($maxRows === 0): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #888; padding: 20px;">No earnings or deductions recorded for this payslip.</td>
                    </tr>
                <?php else: ?>
                    <?php for ($i = 0; $i < $maxRows; $i++): 
                        $earn = $earnings[$i] ?? null;
                        $ded  = $deductions[$i] ?? null;
                    ?>
                        <tr>
                            <td><?php echo $earn ? htmlspecialchars($earn['label']) : ''; ?></td>
                            <td style="text-align: right"><?php echo $earn ? number_format($earn['amount'], 2) : ''; ?></td>
                            <td><?php echo $ded ? htmlspecialchars($ded['label']) : ''; ?></td>
                            <td style="text-align: right"><?php echo $ded ? number_format($ded['amount'], 2) : ''; ?></td>
                        </tr>
                    <?php endfor; ?>
                <?php endif; ?>
                <tr class="section-title">
                    <td>Total Gross Earnings</td>
                    <td style="text-align: right">₹<?php echo number_format($payroll['gross_salary'], 2); ?></td>
                    <td>Total Deductions</td>
                    <td style="text-align: right">₹<?php echo number_format($payroll['gross_salary'] - $payroll['net_salary'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="net-salary-card">
            <div class="net-salary-info">
                <span class="net-salary-title">Net Salary Payable (In-Hand)</span>
                <div class="net-salary-words">Amount in words: <strong><?php echo number_to_words($payroll['net_salary']); ?> Only</strong></div>
            </div>
            <div class="net-salary-amount">
                <span class="currency-symbol">₹</span><?php echo number_format($payroll['net_salary'], 2); ?>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated payslip and does not require a signature.</p>
            <p>© 2026 Redeemer Technologies. All Rights Reserved.</p>
            <p class="billing-line">Billed by Daya Life Science</p>
        </div>
    </div>
</body>
</html>
