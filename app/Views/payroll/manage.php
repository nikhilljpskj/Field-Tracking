<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 px-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Payroll Management</h2>
                        <p class="text-muted">Set salary structures and process monthly payouts.</p>
                    </div>
                    <button class="btn btn-dark shadow" data-toggle="modal" data-target="#bulkProcessModal">
                        <i class="fe fe-cpu mr-1"></i> Bulk Process (All)
                    </button>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between">
                        <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Employee Directory</h5>
                        <div class="badge badge-pill badge-primary">Admin/HR View</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Employee</th>
                                        <th>Role</th>
                                        <th>Contact</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $u): ?>
                                        <tr>
                                            <td class="pl-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm mr-3">
                                                        <img src="<?php echo $u['profile_pic'] ? $u['profile_pic'] : 'assets/avatars/default.png'; ?>" class="avatar-img rounded-circle">
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-600"><?php echo htmlspecialchars($u['name']); ?></div>
                                                        <small class="text-muted">ID: #EMP-<?php echo $u['id']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge badge-soft-primary"><?php echo $u['role_name']; ?></span></td>
                                            <td><small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#manageSalary_<?php echo $u['id']; ?>">
                                                    <i class="fe fe-edit mr-1"></i> Structure
                                                </button>
                                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#processPayroll_<?php echo $u['id']; ?>">
                                                    <i class="fe fe-cpu mr-1"></i> Process
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Structure Modal -->
                                        <div class="modal fade" id="manageSalary_<?php echo $u['id']; ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <form action="payroll-manage?action=saveStructure" method="POST" class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-dark text-white border-0">
                                                        <h5 class="modal-title font-weight-bold">Salary Breakup: <?php echo htmlspecialchars($u['name']); ?></h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body p-4 bg-light">
                                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">Basic</label>
                                                                <input type="number" name="basic" class="form-control salary-input-<?php echo $u['id']; ?>" value="<?php echo $u['basic'] ?? 0; ?>" required>
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">HRA</label>
                                                                <input type="number" name="hra" class="form-control salary-input-<?php echo $u['id']; ?>" value="<?php echo $u['hra'] ?? 0; ?>" required>
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">TA / DA</label>
                                                                <input type="number" name="ta_da" class="form-control salary-input-<?php echo $u['id']; ?>" value="<?php echo $u['ta_da'] ?? 0; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">Other Allw.</label>
                                                                <input type="number" name="other" class="form-control salary-input-<?php echo $u['id']; ?>" value="<?php echo $u['other_allowances'] ?? 0; ?>" required>
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">PF (Deduc.)</label>
                                                                <input type="number" name="pf" class="form-control" value="<?php echo $u['pf_deduction'] ?? 0; ?>" required>
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">Tax (Deduc.)</label>
                                                                <input type="number" name="tax" class="form-control" value="<?php echo $u['tax_deduction'] ?? 0; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="bg-dark p-3 rounded text-white mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="font-weight-bold text-uppercase small">Calculated Total Monthly CTC</span>
                                                                <div class="h4 mb-0 text-success">₹ <span id="ctc_display_<?php echo $u['id']; ?>"><?php echo number_format($u['total_ctc'] ?? 0, 2); ?></span></div>
                                                            </div>
                                                            <input type="hidden" name="ctc" id="ctc_input_<?php echo $u['id']; ?>" value="<?php echo $u['total_ctc'] ?? 0; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 pt-0 bg-light">
                                                        <button type="submit" class="btn btn-primary btn-block shadow font-weight-bold py-3">Update & Save Salary Breakup</button>
                                                    </div>
                                                </form>
                                                <script>
                                                    (function() {
                                                        const inputs = document.querySelectorAll('.salary-input-<?php echo $u['id']; ?>');
                                                        const display = document.getElementById('ctc_display_<?php echo $u['id']; ?>');
                                                        const hiddenInput = document.getElementById('ctc_input_<?php echo $u['id']; ?>');
                                                        
                                                        function calc() {
                                                            let total = 0;
                                                            inputs.forEach(i => total += parseFloat(i.value || 0));
                                                            display.textContent = total.toLocaleString('en-IN', {minimumFractionDigits: 2});
                                                            hiddenInput.value = total;
                                                        }
                                                        
                                                        inputs.forEach(i => i.addEventListener('input', calc));
                                                    })();
                                                </script>
                                            </div>
                                        </div>

                                        <!-- Process Modal -->
                                        <div class="modal fade" id="processPayroll_<?php echo $u['id']; ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <form action="payroll-manage?action=process" method="POST" class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-primary text-white border-0">
                                                        <h5 class="modal-title font-weight-bold">Process Payout: <?php echo htmlspecialchars($u['name']); ?></h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">Select Month</label>
                                                                <select name="month" class="form-control" required>
                                                                    <?php for($i=1;$i<=12;$i++): ?>
                                                                        <option value="<?php echo $i; ?>" <?php echo date('n')==$i?'selected':''; ?>><?php echo date("F", mktime(0,0,0,$i,10)); ?></option>
                                                                    <?php endfor; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">Select Year</label>
                                                                <select name="year" class="form-control" required>
                                                                    <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                                                                    <option value="<?php echo date('Y')-1; ?>"><?php echo date('Y')-1; ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="alert alert-info border-0 shadow-sm small">
                                                            <i class="fe fe-info mr-2"></i> This will calculate LOP based on Approved Leaves and aggregate all Approved TA/DA for the month.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 pt-0">
                                                        <button type="submit" class="btn btn-success btn-block shadow font-weight-bold">Calculate & Generate Payslip</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Bulk Process Modal -->
<div class="modal fade" id="bulkProcessModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="payroll-manage?action=process" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title font-weight-bold">Bulk Payroll Generation</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small text-muted text-uppercase font-weight-bold">Select Month</label>
                        <select name="month" class="form-control" required>
                            <?php for($i=1;$i<=12;$i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo date('n')==$i?'selected':''; ?>><?php echo date("F", mktime(0,0,0,$i,10)); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small text-muted text-uppercase font-weight-bold">Select Year</label>
                        <select name="year" class="form-control" required>
                            <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                            <option value="<?php echo date('Y')-1; ?>"><?php echo date('Y')-1; ?></option>
                        </select>
                    </div>
                </div>
                <div class="alert alert-warning border-0 shadow-sm small">
                    <i class="fe fe-alert-triangle mr-2"></i> This will calculate and save payroll for <strong>ALL</strong> employees whose salary structure is defined. Existing entries for this month will be updated.
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-dark btn-block shadow font-weight-bold">Generate All Payslips</button>
            </div>
        </form>
    </div>
</div>

<style>
.badge-soft-primary { background: rgba(67, 97, 238, 0.1); color: #4361ee; }
.font-weight-600 { font-weight: 600; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
