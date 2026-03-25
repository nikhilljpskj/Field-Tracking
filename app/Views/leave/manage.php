<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Manage Leave Requests</h2>
                        <p class="text-muted">Review and approve employee leave applications.</p>
                    </div>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Pending Applications</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Employee</th>
                                        <th>Leave Type</th>
                                        <th>Dates</th>
                                        <th>Reason</th>
                                        <th class="pr-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($pending)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted small italic">No pending leave requests.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($pending as $p): ?>
                                            <?php 
                                                $start = strtotime($p['start_date']);
                                                $end = strtotime($p['end_date']);
                                                $diff = ($end - $start) / (60 * 60 * 24) + 1;
                                                $days = $p['is_half_day'] ? 0.5 : $diff;
                                            ?>
                                            <tr>
                                                <td class="pl-4">
                                                    <div class="font-weight-600"><?php echo htmlspecialchars($p['user_name']); ?></div>
                                                    <small class="text-muted"><?php echo $days; ?> Days Requested</small>
                                                </td>
                                                <td><span class="badge badge-soft-primary"><?php echo htmlspecialchars($p['type_name']); ?></span></td>
                                                <td>
                                                    <div class="small"><?php echo date('d M Y', $start); ?></div>
                                                    <div class="small text-muted">to <?php echo date('d M Y', $end); ?></div>
                                                </td>
                                                <td class="small text-muted" title="<?php echo htmlspecialchars($p['reason']); ?>">
                                                    <?php echo (strlen($p['reason']) > 30) ? substr(htmlspecialchars($p['reason']), 0, 30) . '...' : htmlspecialchars($p['reason']); ?>
                                                </td>
                                                <td class="pr-4 text-right">
                                                    <div class="d-flex justify-content-end">
                                                        <form action="leave-manage?action=updateStatus" method="POST" class="mr-1">
                                                            <input type="hidden" name="application_id" value="<?php echo $p['id']; ?>">
                                                            <input type="hidden" name="user_id" value="<?php echo $p['user_id']; ?>">
                                                            <input type="hidden" name="leave_type_id" value="<?php echo $p['leave_type_id']; ?>">
                                                            <input type="hidden" name="days" value="<?php echo $days; ?>">
                                                            <input type="hidden" name="status" value="Approved">
                                                            <button type="submit" class="btn btn-sm btn-success shadow-sm">
                                                                <i class="fe fe-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="leave-manage?action=updateStatus" method="POST">
                                                            <input type="hidden" name="application_id" value="<?php echo $p['id']; ?>">
                                                            <input type="hidden" name="status" value="Rejected">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fe fe-x"></i>
                                                            </button>
                                                        </form>
                                                    </div>
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
.badge-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; }
.font-weight-600 { font-weight: 600; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
