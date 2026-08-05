<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars(ucfirst($type ?? 'daily')); ?> Report - <?php echo htmlspecialchars($targetName ?? 'Employees'); ?></title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1f5; color: #172033; font-family: Arial, Helvetica, sans-serif; }
        .sheet { max-width: 1120px; margin: 24px auto; background: #fff; padding: 28px; box-shadow: 0 12px 30px rgba(22, 34, 51, .12); }
        .actions { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 18px; }
        .btn { border: 1px solid #c9d2df; background: #fff; color: #172033; padding: 9px 14px; border-radius: 6px; font-weight: 700; cursor: pointer; }
        .btn-primary { border-color: #2454d6; background: #2454d6; color: #fff; }
        .header { display: flex; justify-content: space-between; gap: 24px; padding-bottom: 18px; border-bottom: 3px solid #2454d6; }
        .brand { font-size: 22px; font-weight: 800; color: #2454d6; letter-spacing: .4px; }
        h1 { margin: 10px 0 6px; font-size: 24px; }
        .muted { color: #647084; font-size: 12px; }
        .meta { min-width: 240px; border: 1px solid #d9e0ea; background: #f7f9fc; padding: 14px; border-radius: 8px; }
        .meta-row { display: flex; justify-content: space-between; gap: 12px; margin: 6px 0; font-size: 13px; }
        .meta-row strong { color: #172033; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 20px 0; }
        .stat { border: 1px solid #d9e0ea; border-radius: 8px; padding: 12px; background: #fbfcfe; }
        .stat-label { color: #647084; text-transform: uppercase; font-size: 10px; font-weight: 800; letter-spacing: .5px; }
        .stat-value { margin-top: 6px; font-size: 20px; font-weight: 800; color: #172033; }
        h2 { margin: 22px 0 10px; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; table-layout: fixed; }
        th, td { border: 1px solid #d9e0ea; padding: 8px; vertical-align: top; font-size: 12px; word-wrap: break-word; }
        th { background: #edf2fb; color: #33415c; text-transform: uppercase; font-size: 10px; letter-spacing: .35px; }
        .status { display: inline-block; border-radius: 999px; padding: 3px 8px; font-size: 10px; font-weight: 800; }
        .status-Approved { background: #dff3e7; color: #176b38; }
        .status-Rejected { background: #fde1e1; color: #a12a2a; }
        .status-Pending { background: #fff0c2; color: #806000; }
        .empty { text-align: center; color: #647084; padding: 22px; }
        .footer { display: flex; justify-content: space-between; gap: 40px; margin-top: 38px; color: #647084; font-size: 12px; }
        .sign { width: 240px; border-top: 1px solid #9aa6b6; padding-top: 8px; }
        @media print {
            body { background: #fff; }
            .sheet { margin: 0; padding: 0; box-shadow: none; max-width: none; }
            .actions { display: none; }
            a { color: inherit; text-decoration: none; }
            th { background: #edf2fb !important; }
            .stat { background: #fbfcfe !important; }
        }
        @media (max-width: 700px) {
            .sheet { margin: 0; padding: 16px; }
            .header, .footer { flex-direction: column; }
            .meta { min-width: 0; }
            .stats { grid-template-columns: repeat(2, 1fr); }
            th, td { font-size: 11px; padding: 6px; }
        }
    </style>
</head>
<body>
<?php
    $rows = $data ?? [];
    $attendanceRows = $attendanceRows ?? [];
    $type = $type ?? 'daily';
    $totalVisits = count($rows);
    $approvedVisits = count(array_filter($rows, function($row) { return ($row['status'] ?? '') === 'Approved'; }));
    $pendingVisits = count(array_filter($rows, function($row) { return ($row['status'] ?? 'Pending') === 'Pending'; }));
    $employeeNames = array_unique(array_filter(array_map(function($row) { return $row['user_name'] ?? null; }, $rows)));
    foreach ($attendanceRows as $row) {
        if (!empty($row['user_name'])) $employeeNames[] = $row['user_name'];
    }
    $employeeCount = count(array_unique($employeeNames));
?>
<div class="sheet">
    <div class="actions">
        <button class="btn btn-primary" onclick="window.print()">Print / Save PDF</button>
        <button class="btn" onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <div>
            <div class="brand">REDEEMER TECHNOLOGIES</div>
            <h1><?php echo ucfirst(htmlspecialchars($type)); ?> Field Report</h1>
            <div class="muted">Generated on <?php echo date('d M Y, h:i A'); ?> IST</div>
        </div>
        <div class="meta">
            <div class="meta-row"><span>Scope</span><strong><?php echo htmlspecialchars($targetName ?? 'Employees'); ?></strong></div>
            <div class="meta-row"><span>Period</span><strong><?php echo htmlspecialchars($period ?? '-'); ?></strong></div>
            <div class="meta-row"><span>Report</span><strong><?php echo ucfirst(htmlspecialchars($type)); ?></strong></div>
        </div>
    </div>

    <div class="stats">
        <div class="stat"><div class="stat-label">Employees</div><div class="stat-value"><?php echo $employeeCount ?: '-'; ?></div></div>
        <div class="stat"><div class="stat-label">Client Visits</div><div class="stat-value"><?php echo $totalVisits; ?></div></div>
        <div class="stat"><div class="stat-label">Approved</div><div class="stat-value"><?php echo $approvedVisits; ?></div></div>
        <div class="stat"><div class="stat-label">Pending</div><div class="stat-value"><?php echo $pendingVisits; ?></div></div>
    </div>

    <?php if ($type === 'daily'): ?>
        <h2>Attendance Login / Logout</h2>
        <table>
            <thead>
                <tr>
                    <th style="width:16%;">Employee</th>
                    <th style="width:13%;">Login</th>
                    <th style="width:20%;">Login Address</th>
                    <th style="width:13%;">Logout</th>
                    <th style="width:20%;">Logout Address</th>
                    <th style="width:9%;">Start KM</th>
                    <th style="width:9%;">End KM</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendanceRows)): ?>
                    <?php foreach ($attendanceRows as $row): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['user_name'] ?? '-'); ?></strong></td>
                            <td><?php echo !empty($row['check_in_time']) ? date('h:i A', strtotime($row['check_in_time'])) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($row['check_in_address'] ?? '-'); ?></td>
                            <td><?php echo !empty($row['check_out_time']) ? date('h:i A', strtotime($row['check_out_time'])) : 'Active'; ?></td>
                            <td><?php echo htmlspecialchars($row['check_out_address'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['odometer_reading'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['check_out_odometer_reading'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td class="empty" colspan="7">No attendance records found for this date.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Client Visit Details</h2>
    <table>
        <thead>
            <tr>
                <th style="width:14%;">Employee</th>
                <th style="width:12%;">Time</th>
                <th style="width:18%;">Client</th>
                <th style="width:18%;">Hospital / Office</th>
                <th style="width:10%;">Category</th>
                <th style="width:18%;">Outcome</th>
                <th style="width:10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $m): ?>
                    <?php $status = $m['status'] ?? 'Pending'; ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($m['user_name'] ?? $targetName ?? '-'); ?></strong></td>
                        <td>
                            <?php if (!empty($m['meeting_time'])): ?>
                                <?php echo date($type === 'monthly' ? 'd M, h:i A' : 'h:i A', strtotime($m['meeting_time'])); ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($m['client_name'] ?? '-'); ?></td>
                        <td>
                            <?php echo htmlspecialchars($m['hospital_office_name'] ?? '-'); ?>
                            <?php if (!empty($m['department'])): ?><br><span class="muted"><?php echo htmlspecialchars($m['department']); ?></span><?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($m['visit_category'] ?? $m['meeting_type'] ?? '-'); ?></td>
                        <td>
                            <?php echo htmlspecialchars($m['outcome'] ?? '-'); ?>
                            <?php if (!empty($m['notes'])): ?><br><span class="muted"><?php echo htmlspecialchars($m['notes']); ?></span><?php endif; ?>
                        </td>
                        <td><span class="status status-<?php echo htmlspecialchars($status); ?>"><?php echo htmlspecialchars($status); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td class="empty" colspan="7">No client visits recorded for this period.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="sign">Prepared By</div>
        <div class="sign">Manager / Authorized Signature</div>
    </div>
</div>
<script>
    window.onload = function() {
        setTimeout(function() { window.print(); }, 400);
    };
</script>
</body>
</html>
