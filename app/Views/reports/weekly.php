<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 px-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Weekly Performance Report</h2>
                        <p class="text-muted">Summary from <?php echo date('d M', strtotime($range['start'])); ?> to <?php echo date('d M', strtotime($range['end'])); ?></p>
                    </div>
                    <div class="btn-group">
                        <a href="reports?action=export&type=weekly&format=csv" class="btn btn-outline-primary shadow-sm">
                            <i class="fe fe-download mr-1"></i> Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-primary shadow">
                            <i class="fe fe-printer mr-1"></i> Print PDF
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted mb-1 text-uppercase font-weight-bold">Total Meetings</small>
                                        <h3 class="card-title mb-0"><?php echo count($meetings); ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <span class="fe fe-users fe-24 text-primary"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted mb-1 text-uppercase font-weight-bold">Distance Traveled</small>
                                        <h3 class="card-title mb-0"><?php echo number_format($travel['total_distance'] ?? 0, 1); ?> km</h3>
                                    </div>
                                    <div class="col-auto">
                                        <span class="fe fe-map-pin fe-24 text-success"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Visit Breakdown</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Date</th>
                                        <th>Client</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th class="pr-4 text-right">Selfie</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($meetings)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted small italic">No meetings found in this period.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($meetings as $m): ?>
                                            <tr>
                                                <td class="pl-4"><?php echo date('d M, H:i', strtotime($m['meeting_time'])); ?></td>
                                                <td>
                                                    <div class="font-weight-600"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($m['hospital_office_name']); ?></small>
                                                </td>
                                                <td><small class="text-muted"><?php echo htmlspecialchars($m['address']); ?></small></td>
                                                <td><span class="badge badge-soft-<?php echo $m['status']=='Approved'?'success':'warning'; ?>"><?php echo $m['status']; ?></span></td>
                                                <td class="pr-4 text-right">
                                                    <?php if($m['selfie_path']): ?>
                                                        <a href="<?php echo $m['selfie_path']; ?>" target="_blank" class="btn btn-sm btn-link p-0">View</a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
@media print {
    .sidebar-left, .navbar, .btn, .btn-group { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
    .card { border: 1px solid #eee !important; box-shadow: none !important; }
}
.badge-soft-success { background: rgba(43, 203, 186, 0.1); color: #2bcbba; }
.badge-soft-warning { background: rgba(255, 165, 0, 0.1); color: #ffa500; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
