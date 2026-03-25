<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payslip - <?php echo $employee['name']; ?> - <?php echo date("F Y", mktime(0, 0, 0, $payroll['month'], 10, $payroll['year'])); ?></title>
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
        .net-salary-box { background: #4361ee; color: #fff; padding: 25px; text-align: center; border-radius: 8px; margin-top: 30px; }
        .net-salary-box h2 { margin: 0; font-size: 32px; }
        .net-salary-box p { margin: 5px 0 0; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-size: 12px; }

        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        
        @media print {
            body { background: #fff; padding: 0; }
            .payslip-container { box-shadow: none; border: none; width: 100%; max-width: 100%; }
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

        <div class="info-grid">
            <div class="info-box">
                <h4>Employee Details</h4>
                <div class="info-row"><span class="info-label">Name:</span> <span class="info-value"><?php echo $employee['name']; ?></span></div>
                <div class="info-row"><span class="info-label">Employee ID:</span> <span class="info-value">EMP-<?php echo str_pad($employee['id'], 4, '0', STR_PAD_LEFT); ?></span></div>
                <div class="info-row"><span class="info-label">Designation:</span> <span class="info-value"><?php echo $employee['role_name']; ?></span></div>
                <div class="info-row"><span class="info-label">Email:</span> <span class="info-value"><?php echo $employee['email']; ?></span></div>
            </div>
            <div class="info-box">
                <h4>Payment Summary</h4>
                <div class="info-row"><span class="info-label">Bank Name:</span> <span class="info-value"><?php echo htmlspecialchars($employee['bank_name'] ?? 'N/A'); ?></span></div>
                <div class="info-row"><span class="info-label">Account No:</span> <span class="info-value"><?php echo htmlspecialchars($employee['account_number'] ?? 'N/A'); ?></span></div>
                <div class="info-row"><span class="info-label">IFSC Code:</span> <span class="info-value"><?php echo htmlspecialchars($employee['ifsc_code'] ?? 'N/A'); ?></span></div>
                <div class="info-row"><span class="info-label">LOP Days:</span> <span class="info-value text-danger"><?php echo $breakdown['lop_days'] ?? 0; ?></span></div>
            </div>
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
                <tr>
                    <td>Basic Salary</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['basic'] ?? 0, 2); ?></td>
                    <td>Provident Fund (PF)</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['pf_employee'] ?? 0, 2); ?></td>
                </tr>
                <tr>
                    <td>HRA</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['hra'] ?? 0, 2); ?></td>
                    <td>Professional Tax</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['professional_tax'] ?? 0, 2); ?></td>
                </tr>
                <tr>
                    <td>DA</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['da'] ?? 0, 2); ?></td>
                    <td>TDS / Income Tax</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['tds'] ?? 0, 2); ?></td>
                </tr>
                <tr>
                    <td>Special Allowance</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['special_allowance'] ?? 0, 2); ?></td>
                    <td>ESI</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['esi_employee'] ?? 0, 2); ?></td>
                </tr>
                <tr>
                    <td>Conveyance</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['conveyance_allowance'] ?? 0, 2); ?></td>
                    <td>LOP Deductions</td>
                    <td style="text-align: right"><?php echo number_format($breakdown['lop_amount'] ?? 0, 2); ?></td>
                </tr>
                <tr class="section-title">
                    <td>Total Gross Earnings</td>
                    <td style="text-align: right">₹<?php echo number_format($payroll['gross_salary'], 2); ?></td>
                    <td>Total Deductions</td>
                    <td style="text-align: right">₹<?php echo number_format($payroll['gross_salary'] - $payroll['net_salary'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="net-salary-box">
            <p>Net Salary Payable (In-Hand)</p>
            <h2>₹<?php echo number_format($payroll['net_salary'], 2); ?></h2>
            <small style="opacity: 0.7">(<?php echo number_to_words($payroll['net_salary']); ?> Only)</small>
        </div>

        <div class="footer">
            <p>This is a computer-generated payslip and does not require a signature.</p>
            <p>© 2026 Redeemer Technologies. All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>
<?php
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
?>
