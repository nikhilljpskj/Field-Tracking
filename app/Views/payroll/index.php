<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">My Payroll</h2>
                        <p class="text-muted">View your salary structure and payment history.</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Active Salary Structure</h5>
                            </div>
                            <div class="card-body">
                                <?php if($structure): ?>
                                    <div class="row text-center">
                                        <div class="col-md-3 border-right">
                                            <p class="small text-muted mb-0">Basic</p>
                                            <h4 class="mb-0">₹<?php echo number_format($structure['basic_salary']); ?></h4>
                                        </div>
                                        <div class="col-md-3 border-right">
                                            <p class="small text-muted mb-0">HRA</p>
                                            <h4 class="mb-0">₹<?php echo number_format($structure['hra']); ?></h4>
                                        </div>
                                        <div class="col-md-3 border-right">
                                            <p class="small text-muted mb-0">Allowances</p>
                                            <h4 class="mb-0">₹<?php echo number_format($structure['other_allowance']); ?></h4>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="small text-muted mb-0">Total Deductions</p>
                                            <h4 class="mb-0 text-danger">₹<?php echo number_format($structure['pf_deduction'] + $structure['tax_deduction']); ?></h4>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="text-center text-muted mb-0 italic">Salary structure not yet defined by HR.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Payment History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Month/Year</th>
                                        <th>Basic + HRA</th>
                                        <th>TA/DA</th>
                                        <th class="text-danger">Deductions/LOP</th>
                                        <th class="pr-4 text-right">Net Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($history)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted small italic">No payroll history found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($history as $p): ?>
                                            <tr>
                                                <td class="pl-4">
                                                    <div class="font-weight-600"><?php echo date("F", mktime(0, 0, 0, $p['month'], 10)) . ' ' . $p['year']; ?></div>
                                                    <small class="text-muted">Processed by <?php echo htmlspecialchars($p['processed_by_name']); ?></small>
                                                </td>
                                                <td>₹<?php echo number_format($p['basic_paid'] + $p['hra_paid']); ?></td>
                                                <td><span class="text-success font-weight-bold">+ ₹<?php echo number_format($p['tada_paid']); ?></span></td>
                                                <td class="text-danger">
                                                    - ₹<?php echo number_format($p['lop_deduction'] + $p['pf_deduction'] + $p['tax_deduction']); ?>
                                                    <?php if($p['lop_deduction'] > 0): ?>
                                                        <div class="extra-small">(Incl. LOP)</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="pr-4 text-right"><h5 class="mb-0 font-weight-bold">₹<?php echo number_format($p['net_salary']); ?></h5></td>
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
.font-weight-600 { font-weight: 600; }
.extra-small { font-size: 10px; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
