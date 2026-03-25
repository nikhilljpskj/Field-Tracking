<?php include dirname(__DIR__).'/layout/header.php'; ?>
<?php include dirname(__DIR__).'/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Daily Activity Summary</h2>
                        <p class="text-muted">A comprehensive view of your field actions for today, <?php echo date('d M Y'); ?>.</p>
                    </div>
                    <div class="btn-group">
                        <a href="reports?action=export&type=daily&format=csv" class="btn btn-outline-success font-weight-600">
                            <i class="fe fe-file-text mr-1"></i> Excel
                        </a>
                        <a href="reports?action=export&type=daily&format=pdf" class="btn btn-outline-danger font-weight-600">
                            <i class="fe fe-file mr-1"></i> PDF
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Attendance Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="card-title mb-0 text-muted text-uppercase small font-weight-bold">Shift Attendance</h6>
                            </div>
                            <div class="card-body">
                                <?php if($attendance): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="circle circle-sm bg-soft-success mr-3 text-success">
                                            <i class="fe fe-log-in"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Checked In</small>
                                            <span class="font-weight-bold"><?php echo date('h:i A', strtotime($attendance['check_in_time'])); ?></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="circle circle-sm <?php echo $attendance['check_out_time'] ? 'bg-soft-danger text-danger' : 'bg-soft-warning text-warning'; ?> mr-3">
                                            <i class="fe fe-log-out"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Checked Out</small>
                                            <span class="font-weight-bold"><?php echo $attendance['check_out_time'] ? date('h:i A', strtotime($attendance['check_out_time'])) : 'Current Active'; ?></span>
                                        </div>
                                    </div>
                                    <div class="mt-3 p-2 bg-light rounded shadow-none small text-muted">
                                        <i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars($attendance['check_in_address']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fe fe-alert-circle fe-24 text-muted mb-2 d-block"></i>
                                        <p class="text-muted small mb-0">No attendance logged yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                            <div class="card-header bg-transparent border-0 py-3">
                                <h6 class="card-title mb-0 text-white-50 text-uppercase small font-weight-bold">Travel Allowance</h6>
                            </div>
                            <div class="card-body text-center py-4">
                                <div class="h2 mb-1 font-weight-bold"><?php echo number_format($travel['total_distance'] ?? 0, 1); ?> <small class="h6">KM</small></div>
                                <p class="text-white-50 small mb-4">Estimated Earnings Today</p>
                                <div class="p-3 bg-white-10 rounded-lg">
                                    <span class="h4 mb-0 font-weight-bold">₹<?php echo number_format($travel['allowance_earned'] ?? 0, 0); ?></span>
                                </div>
                                <div class="mt-3">
                                    <span class="badge badge-pill bg-white-10 px-3 py-1 small">
                                        Status: <?php echo $travel['status'] ?? 'Pending'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meetings Summary -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="card-title mb-0 text-muted text-uppercase small font-weight-bold">Meeting Productivity</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-3">
                                    <div class="h2 mb-1 font-weight-bold"><?php echo count($meetings); ?></div>
                                    <p class="text-muted small">Successful Interactions</p>
                                </div>
                                <div class="progress progress-sm mb-3">
                                    <div class="progress-bar bg-success" style="width: <?php echo min((count($meetings) / 5) * 100, 100); ?>%"></div>
                                </div>
                                <p class="small text-muted mb-0">Daily Target: 5 Meetings</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Table -->
                <div class="card shadow-sm border-0 overflow-hidden mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">Meeting Logs</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Time</th>
                                        <th>Target / Outcome</th>
                                        <th>Type</th>
                                        <th class="pr-4 text-right">Approval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($meetings)): ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted">No meetings recorded today.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($meetings as $m): ?>
                                            <tr>
                                                <td class="pl-4">
                                                    <div class="font-weight-600"><?php echo date('h:i A', strtotime($m['meeting_time'])); ?></div>
                                                    <small class="text-muted">Logged at interaction</small>
                                                </td>
                                                <td>
                                                    <div class="font-weight-600"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($m['hospital_office_name']); ?></small>
                                                    <p class="mb-0 mt-1 small text-dark"><?php echo htmlspecialchars($m['outcome']); ?></p>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light px-2 py-1"><?php echo $m['meeting_type']; ?></span>
                                                </td>
                                                <td class="pr-4 text-right">
                                                    <span class="dot <?php echo ($m['status'] == 'Approved') ? 'bg-success' : (($m['status'] == 'Rejected') ? 'bg-danger' : 'bg-warning'); ?> mr-1"></span>
                                                    <span class="small font-weight-bold <?php echo ($m['status'] == 'Approved') ? 'text-success' : (($m['status'] == 'Rejected') ? 'text-danger' : 'text-warning'); ?>">
                                                        <?php echo $m['status']; ?>
                                                    </span>
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
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-white-10 { background-color: rgba(255, 255, 255, 0.15); }
.circle-sm { width: 32px; height: 32px; line-height: 32px; border-radius: 50%; display: inline-block; text-align: center; }
.dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; }
.font-weight-600 { font-weight: 600; }
.bg-primary { background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%) !important; }
</style>

<?php include dirname(__DIR__).'/layout/footer.php'; ?>
