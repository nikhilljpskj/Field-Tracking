<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Directory Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; -webkit-print-color-adjust: exact; }
        .print-container { background: #fff; max-width: 900px; margin: 30px auto; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .report-header { border-bottom: 2px solid #007bff; padding-bottom: 15px; margin-bottom: 25px; }
        .table th { background: #f1f4f8 !important; font-size: 12px; text-transform: uppercase; }
        @media print {
            body { background: #fff; }
            .print-container { margin: 0; padding: 0; box-shadow: none; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="print-container">
    <div class="text-right no-print mb-3">
        <button onclick="window.print()" class="btn btn-primary btn-sm">Print to PDF</button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-sm ml-2">Close</button>
    </div>

    <div class="report-header">
        <h4 class="mb-1">REDEEMER TECHNOLOGIES</h4>
        <p class="mb-0 text-muted">Contact Directory Report - Generated on <?php echo date('d M Y, h:i A'); ?></p>
    </div>

    <table class="table table-bordered table-sm">
        <thead>
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Email</th>
            <th>Mobile Number</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($contacts)): ?>
            <?php foreach ($contacts as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($c['role_name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($c['email'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($c['phone'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center text-muted">No contacts available.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
window.onload = function() { setTimeout(function() { window.print(); }, 300); };
</script>
</body>
</html>

