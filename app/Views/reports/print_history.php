<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report - <?php echo htmlspecialchars($user['name']); ?></title>
    <!-- Simple Bootstrap for print styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; -webkit-print-color-adjust: exact; }
        .print-container { background: #fff; max-width: 900px; margin: 30px auto; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .report-header { border-bottom: 2px solid #28a745; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-placeholder { font-size: 24px; font-weight: 800; color: #28a745; text-transform: uppercase; letter-spacing: 1px; }
        .table th { background-color: #f1f4f8 !important; color: #495057; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .table td { font-size: 14px; vertical-align: middle; }
        .text-success-custom { color: #28a745 !important; font-weight: 600; }
        .text-danger-custom { color: #dc3545 !important; font-weight: 600; }
        @media print {
            body { background: #fff; }
            .print-container { margin: 0; padding: 0; box-shadow: none; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Print Actions Route -->
        <div class="text-right no-print mb-4">
            <button onclick="window.print()" class="btn btn-primary shadow-sm"><i class="fe fe-printer mr-2"></i> Print to PDF</button>
            <button onclick="window.close()" class="btn btn-outline-secondary ml-2">Close</button>
        </div>

        <!-- Header -->
        <div class="row report-header align-items-center">
            <div class="col-8">
                <div class="logo-placeholder">REDEEMER TECHNOLOGIES</div>
                <h4 class="mt-3 mb-1 text-dark">Employee Attendance & Audit Report</h4>
                <p class="text-muted mb-0">Generated on <?php echo date('d M Y, h:i A'); ?> (IST)</p>
            </div>
            <div class="col-4 text-right">
                <div class="p-3 bg-light rounded text-left d-inline-block border">
                    <div class="small text-uppercase text-muted font-weight-bold mb-1">Employee Details</div>
                    <div class="font-weight-bold text-dark" style="font-size: 16px;"><?php echo htmlspecialchars($user['name']); ?></div>
                    <div class="text-muted small">ID: E-<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></div>
                    <div class="text-muted small">Month: <strong><?php echo date('F Y', mktime(0, 0, 0, $month, 10, $year)); ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="15%">Date</th>
                    <th width="35%">Start of Shift (Check-In)</th>
                    <th width="35%">End of Shift (Check-Out)</th>
                    <th width="15%">Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($records)): ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td>
                                <strong><?php echo date('d M Y', strtotime($r['check_in_time'])); ?></strong><br>
                                <span class="text-muted small"><?php echo date('l', strtotime($r['check_in_time'])); ?></span>
                            </td>
                            <td>
                                <span class="text-success-custom"><?php echo date('h:i A', strtotime($r['check_in_time'])); ?></span><br>
                                <span class="small text-muted"><?php echo htmlspecialchars($r['check_in_address']); ?></span>
                            </td>
                            <td>
                                <?php if ($r['check_out_time']): ?>
                                    <span class="text-danger-custom"><?php echo date('h:i A', strtotime($r['check_out_time'])); ?></span><br>
                                    <span class="small text-muted"><?php echo htmlspecialchars($r['check_out_address']); ?></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary" style="font-size: 11px;">Active / Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center font-weight-bold">
                                <?php 
                                if ($r['check_out_time']) {
                                    $diff = strtotime($r['check_out_time']) - strtotime($r['check_in_time']);
                                    echo floor($diff/3600) . 'h ' . floor(($diff/60)%60) . 'm';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <h6 class="mb-0">No attendance records found for this month.</h6>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer signatures -->
        <div class="row mt-5 pt-4">
            <div class="col-6 text-left">
                <hr style="width: 200px; margin-left: 0;">
                <p class="text-muted small mt-2">Employee Signature</p>
            </div>
            <div class="col-6 text-right">
                <hr style="width: 200px; margin-right: 0;">
                <p class="text-muted small mt-2">Manager / HR Authorized Signature</p>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <p class="text-muted small" style="font-size: 11px;">This is a system generated report. Geolocation tags and time logs are verified via GPS satellites.</p>
        </div>
    </div>
    <script>
        // Auto trigger print dialog on load
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        }
    </script>
</body>
</html>
