<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospitals & Offices Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; }
        .wrap { background: #fff; max-width: 1000px; margin: 20px auto; padding: 24px; }
        @media print { .no-print { display:none !important; } .wrap { margin:0; max-width:100%; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="text-right no-print mb-2">
        <button class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.close()">Close</button>
    </div>
    <h4 class="mb-1">Hospitals & Offices</h4>
    <p class="text-muted mb-3">Generated: <?php echo date('d M Y, h:i A'); ?></p>
    <table class="table table-bordered table-sm">
        <thead>
        <tr>
            <th>Name</th><th>Allotted Day</th><th>Allotted Time</th><th>Location URL</th><th>Address</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach(($rows ?? []) as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['name'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($r['allotted_day'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($r['allotted_time'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($r['location_url'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($r['address'] ?? '-'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

