<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Leave Management</h2>
                        <p class="text-muted">Apply for leaves and track your quarterly balance.</p>
                    </div>
                    <button type="button" class="btn btn-primary shadow" data-toggle="modal" data-target="#applyLeaveModal">
                        <i class="fe fe-plus mr-1"></i> Apply for Leave
                    </button>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>

                <!-- Leave Balances -->
                <div class="row mb-4">
                    <?php foreach($allocations as $alloc): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted mb-1 text-uppercase font-weight-bold"><?php echo htmlspecialchars($alloc['type_name']); ?></small>
                                        <h3 class="card-title mb-0"><?php echo ($alloc['allocated'] - $alloc['used']); ?> <span class="small text-muted">/ <?php echo $alloc['allocated']; ?> Remaining</span></h3>
                                    </div>
                                    <div class="col-auto">
                                        <span class="fe fe-calendar fe-24 text-primary"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($allocations)): ?>
                        <div class="col-12"><div class="alert alert-info border-0 shadow-sm">No leave allocations found for the current quarter.</div></div>
                    <?php endif; ?>
                </div>

                <!-- History Table -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Leave History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Dates</th>
                                        <th>Type</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th class="pr-4 text-right">LOP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($applications)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted small italic">No leave applications yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($applications as $app): ?>
                                            <tr>
                                                <td class="pl-4">
                                                    <div class="font-weight-600 text-dark"><?php echo date('d M', strtotime($app['start_date'])); ?> - <?php echo date('d M', strtotime($app['end_date'])); ?></div>
                                                    <small class="text-muted"><?php echo $app['is_half_day'] ? 'Half Day' : 'Full Day'; ?></small>
                                                </td>
                                                <td><span class="badge badge-soft-primary px-2"><?php echo htmlspecialchars($app['type_name']); ?></span></td>
                                                <td class="small text-muted" title="<?php echo htmlspecialchars($app['reason']); ?>">
                                                    <?php echo (strlen($app['reason']) > 30) ? substr(htmlspecialchars($app['reason']), 0, 30) . '...' : htmlspecialchars($app['reason']); ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo ($app['status'] == 'Approved') ? 'badge-success' : (($app['status'] == 'Rejected') ? 'badge-danger' : 'badge-warning'); ?> p-1 px-2">
                                                        <?php echo $app['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="pr-4 text-right text-danger font-weight-bold">
                                                    <?php echo $app['lop_days'] > 0 ? $app['lop_days'] . ' Days' : '-'; ?>
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

<!-- Apply Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" role="dialog" aria-labelledby="applyLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold" id="applyLeaveModalLabel">Submit Leave Application</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="leaves?action=apply" method="POST">
                <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small text-muted text-uppercase font-weight-bold mb-0">Leave Type</label>
                            <span id="current-balance-display" class="badge badge-soft-success p-1 px-2 d-none">
                                Balance: <span id="balance-value">0</span> Days
                            </span>
                        </div>
                        <select name="leave_type_id" id="leave_type_select" class="form-control custom-select" onchange="updateBalance(this.value)" required>
                            <option value="">-- Select Type --</option>
                            <?php foreach($leaveTypes as $lt): ?>
                                <option value="<?php echo $lt['id']; ?>"><?php echo htmlspecialchars($lt['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label class="small text-muted text-uppercase font-weight-bold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label class="small text-muted text-uppercase font-weight-bold">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="is_half_day" name="is_half_day">
                        <label class="custom-control-label small" for="is_half_day">Half Day Application</label>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small text-muted text-uppercase font-weight-bold">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Explain your reason briefly..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.badge-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; }
.font-weight-600 { font-weight: 600; }
</style>

<script>
function updateBalance(typeId) {
    const display = document.getElementById('current-balance-display');
    const valueElem = document.getElementById('balance-value');
    
    if (!typeId) {
        display.classList.add('d-none');
        return;
    }

    fetch(`leaves?action=getBalance&type_id=${typeId}`)
        .then(response => response.json())
        .then(data => {
            valueElem.innerText = data.balance;
            display.classList.remove('d-none');
            if (parseFloat(data.balance) <= 0) {
                display.classList.remove('badge-soft-success');
                display.classList.add('badge-soft-danger');
            } else {
                display.classList.add('badge-soft-success');
                display.classList.remove('badge-soft-danger');
            }
        })
        .catch(err => console.error('Error fetching balance:', err));
}
</script>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
