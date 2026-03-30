<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intelligence Report - <?php echo htmlspecialchars($targetName ?? 'Employee'); ?></title>
    <!-- Simple Bootstrap for print styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; -webkit-print-color-adjust: exact; }
        .print-container { background: #fff; max-width: 1000px; margin: 30px auto; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .report-header { border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-placeholder { font-size: 24px; font-weight: 800; color: #007bff; text-transform: uppercase; letter-spacing: 1px; }
        .table th { background-color: #f1f4f8 !important; color: #495057; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .table td { font-size: 14px; vertical-align: middle; }
        .text-success-custom { color: #28a745 !important; font-weight: 600; }
        .text-danger-custom { color: #dc3545 !important; font-weight: 600; }
        .badge-status { font-size: 12px; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .status-Approved { background-color: #d4edda; color: #155724; }
        .status-Pending { background-color: #ffeeba; color: #856404; }
        .status-Rejected { background-color: #f8d7da; color: #721c24; }
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
                <h4 class="mt-3 mb-1 text-dark">Client Intelligence Report (<?php echo ucfirst($type); ?>)</h4>
                <p class="text-muted mb-0">Generated on <?php echo date('d M Y, h:i A'); ?> (IST)</p>
            </div>
            <div class="col-4 text-right">
                <div class="p-3 bg-light rounded text-left d-inline-block border">
                    <div class="small text-uppercase text-muted font-weight-bold mb-1">Target Scope</div>
                    <div class="font-weight-bold text-dark" style="font-size: 16px;"><?php echo htmlspecialchars($targetName ?? 'Unknown'); ?></div>
                    <div class="text-muted small">Period: <?php echo date('F Y'); ?></div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <table class="table table-bordered table-sm mt-4">
            <thead>
                <tr>
                    <?php 
                        $showNameColumn = (($targetName ?? '') === 'Team' || ($targetName ?? '') === 'My' || !empty($data[0]['user_name']));
                        if ($showNameColumn): 
                    ?>
                        <th width="15%">Staff Name</th>
                    <?php endif; ?>
                    <th width="15%">Interaction Date</th>
                    <th width="30%">Client & Hospital (Lead)</th>
                    <th width="15%">Category & Outcome</th>
                    <th width="20%">Geolocation Coordinates</th>
                    <th width="10%" class="text-center">Verification</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $m): ?>
                        <tr>
                            <?php if ($showNameColumn): ?>
                                <td class="font-weight-bold text-dark">
                                    <?php echo htmlspecialchars($m['user_name'] ?? $targetName); ?>
                                </td>
                            <?php endif; ?>
                            
                            <td>
                                <strong><?php echo date('d M Y', strtotime($m['meeting_time'])); ?></strong><br>
                                <span class="text-muted small"><?php echo date('h:i A', strtotime($m['meeting_time'])); ?></span>
                            </td>
                            <td>
                                <span class="d-block font-weight-bold"><?php echo htmlspecialchars($m['client_name'] ?? '-'); ?></span>
                                <span class="small text-muted d-block"><?php echo htmlspecialchars($m['hospital_office_name'] ?? '-'); ?></span>
                                <?php if (!empty($m['notes'])): ?>
                                    <span class="small font-italic text-secondary mt-1 d-block">"<?php echo htmlspecialchars(substr($m['notes'], 0, 50)) . (strlen($m['notes']) > 50 ? '...' : ''); ?>"</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="d-block text-dark"><?php echo htmlspecialchars($m['visit_category'] ?? $m['meeting_type'] ?? '-'); ?></span>
                                <span class="small text-primary font-weight-bold"><?php echo htmlspecialchars($m['outcome'] ?? '-'); ?></span>
                            </td>
                            <td>
                                <?php if (!empty($m['latitude']) && strpos($m['latitude'], '0.000') === false): ?>
                                    <span class="small d-block text-monospace"><span class="text-muted">LAT:</span> <?php echo number_format($m['latitude'], 6); ?></span>
                                    <span class="small d-block text-monospace"><span class="text-muted">LNG:</span> <?php echo number_format($m['longitude'], 6); ?></span>
                                    <span class="d-block text-muted" style="font-size: 10px; line-height: 1.2; margin-top:2px;">
                                        <?php echo htmlspecialchars(substr($m['address'] ?? '', 0, 60)) . (strlen($m['address'] ?? '') > 60 ? '...' : ''); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="small text-muted font-italic">No Location Data</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php $status = $m['status'] ?? 'Pending'; ?>
                                <span class="badge-status status-<?php echo $status; ?>"><?php echo $status; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <h6 class="mb-0">No client meetings recorded for this timeframe.</h6>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer signatures -->
        <div class="row mt-5 pt-4">
            <div class="col-6 text-left">
                <hr style="width: 200px; margin-left: 0;">
                <p class="text-muted small mt-2">Executive Signature</p>
            </div>
            <div class="col-6 text-right">
                <hr style="width: 200px; margin-right: 0;">
                <p class="text-muted small mt-2">Manager / Director Authorized Signature</p>
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
