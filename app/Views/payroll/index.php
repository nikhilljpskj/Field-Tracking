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
                        <div class="card shadow-sm border-0 mb-4 bg-white">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Active Salary Structure</h5>
                            </div>
                            <div class="card-body">
                                <?php if($structure): ?>
                                    <div class="row text-center">
                                        <div class="col-md-3 border-right">
                                            <p class="small text-muted mb-0">Basic</p>
                                            <h4 class="mb-0">₹<?php echo number_format($structure['basic'] ?? 0); ?></h4>
                                        </div>
                                        <div class="col-md-3 border-right">
                                            <p class="small text-muted mb-0">HRA</p>
                                            <h4 class="mb-0">₹<?php echo number_format($structure['hra'] ?? 0); ?></h4>
                                        </div>
                                        <div class="col-md-3 border-right">
                                            <p class="small text-muted mb-0">Monthly CTC</p>
                                            <h4 class="mb-0 text-primary">₹<?php echo number_format(($structure['total_ctc'] ?? 0) / 12); ?></h4>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="small text-muted mb-0">Total Statutory</p>
                                            <h4 class="mb-0 text-danger">₹<?php echo number_format(($structure['pf_deduction'] ?? 0) + ($structure['tax_deduction'] ?? 0)); ?></h4>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="text-center text-muted mb-0 italic">Salary structure not yet defined by HR.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 bg-white">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Payment History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Month/Year</th>
                                        <th>Gross Earnings</th>
                                        <th>Deductions/LOP</th>
                                        <th class="pr-4 text-right">Net Salary (In-Hand)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($history)): ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted small italic">No payroll history found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($history as $p): ?>
                                            <tr>
                                                <td class="pl-4">
                                                    <div class="font-weight-600"><?php echo date("F", mktime(0, 0, 0, $p['month'], 10)) . ' ' . $p['year']; ?></div>
                                                    <small class="text-muted">Processed on <?php echo date('d M, Y', strtotime($p['created_at'])); ?></small>
                                                </td>
                                                <td><span class="text-success font-weight-bold">₹<?php echo number_format($p['gross_salary']); ?></span></td>
                                                <td class="text-danger">
                                                    - ₹<?php echo number_format($p['lop_deductions'] + ($p['total_statutory_deductions'] ?? 0)); ?>
                                                    <?php if($p['lop_deductions'] > 0): ?>
                                                        <div class="extra-small">(Incl. LOP)</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="pr-4 text-right"><h5 class="mb-0 font-weight-bold text-primary">₹<?php echo number_format($p['net_salary']); ?></h5></td>
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
.card { border-radius: 12px; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
