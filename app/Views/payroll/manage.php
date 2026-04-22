<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid px-2 px-md-3">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="payroll-header-banner mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="payroll-header-title"><i class="fe fe-calculator mr-2"></i> Salary &amp; CTC Calculator</h2>
                            <p class="payroll-header-subtitle d-none d-md-block">Real-time salary breakdown with CTC &amp; statutory computations</p>
                        </div>
                        <button type="button" class="btn btn-light btn-sm shadow-sm px-3" onclick="resetCalculator()">
                            <i class="fe fe-refresh-cw mr-1"></i> <span class="d-none d-sm-inline">Reset</span>
                        </button>
                    </div>
                </div>

                <form id="payroll-calculator-form">
                    <!-- Selection Header -->
                    <div class="pc-card mb-4">
                        <div class="pc-card-body">
                            <div class="row no-gutters">
                                <div class="col-12 col-md-5 pr-md-2 mb-3 mb-md-0">
                                    <label class="pc-label"><i class="fe fe-user mr-1"></i> Employee</label>
                                    <select id="employee_select" name="user_id" class="form-control pc-select custom-select" onchange="loadEmployeeData(this.value)">
                                        <option value="">-- Choose Employee --</option>
                                        <?php foreach($users as $u): ?>
                                            <option value="<?php echo $u['id']; ?>" data-structure='<?php echo json_encode($u); ?>'>
                                                <?php echo htmlspecialchars($u['name']); ?> (#EMP<?php echo str_pad($u['id'], 4, '0', STR_PAD_LEFT); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-7 col-md-4 pr-2 pr-md-2">
                                    <label class="pc-label"><i class="fe fe-calendar mr-1"></i> Month</label>
                                    <input type="month" name="calc_month" class="form-control pc-input" value="<?php echo date('Y-m'); ?>">
                                </div>
                                <div class="col-5 col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary btn-block pc-history-btn" onclick="viewHistory()">
                                        <i class="fe fe-clock mr-1"></i> <span>History</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Earnings Section -->
                    <div class="pc-card mb-3 overflow-hidden">
                        <div class="pc-section-header pc-header-earnings cursor-pointer" data-toggle="collapse" data-target="#earningsCollapse">
                            <div class="d-flex align-items-center">
                                <div class="pc-section-icon bg-success-soft">
                                    <i class="fe fe-trending-up text-success"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="pc-section-title">Earnings</span>
                                    <span class="pc-live-badge badge-success-soft ml-2" id="gross_badge">₹ 0.00</span>
                                </div>
                            </div>
                            <i class="fe fe-chevron-up pc-chevron-icon" data-toggle="collapse" data-target="#earningsCollapse"></i>
                        </div>
                        <div id="earningsCollapse" class="collapse show">
                            <div class="pc-section-body">
                                <p class="pc-section-subtitle">Fixed Components</p>
                                <div class="row">
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Basic Salary <span class="text-danger">*</span></label>
                                        <input type="number" name="basic" id="basic" class="form-control pc-input calc-trigger" placeholder="0" required>
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">HRA</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <select class="custom-select pc-type-select" id="hra_type" onchange="calculateAll()">
                                                    <option value="percent">%</option>
                                                    <option value="fixed" selected>₹</option>
                                                </select>
                                            </div>
                                            <input type="number" name="hra" id="hra" class="form-control pc-input calc-trigger" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">HRA (Calculated)</label>
                                        <div class="pc-auto-display" id="hra_auto_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">DA</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <select class="custom-select pc-type-select" id="da_type" onchange="calculateAll()">
                                                    <option value="percent">%</option>
                                                    <option value="fixed" selected>₹</option>
                                                </select>
                                            </div>
                                            <input type="number" name="da" id="da" class="form-control pc-input calc-trigger" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">DA (Calculated)</label>
                                        <div class="pc-auto-display" id="da_auto_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Special Allowance</label>
                                        <input type="number" name="special_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Conveyance</label>
                                        <input type="number" name="conveyance_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Medical</label>
                                        <input type="number" name="medical_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Education</label>
                                        <input type="number" name="education_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Uniform</label>
                                        <input type="number" name="uniform_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Food</label>
                                        <input type="number" name="food_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Telephone</label>
                                        <input type="number" name="telephone_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Books</label>
                                        <input type="number" name="books_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Research</label>
                                        <input type="number" name="research_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">CCA</label>
                                        <input type="number" name="cca" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Transport</label>
                                        <input type="number" name="transport_allowance" class="form-control pc-input calc-trigger" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statutory Section -->
                    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 small text-uppercase font-weight-bold text-dark cursor-pointer" data-toggle="collapse" data-target="#statutoryCollapse">
                                <i class="fe fe-shield mr-2 text-warning"></i> Statutory & Deductions
                            </h5>
                            <div class="d-flex align-items-center">
                                <div class="custom-control custom-switch mr-3">
                                    <input type="checkbox" name="is_statutory_enabled" class="custom-control-input calc-trigger" id="statutory_toggle" checked>
                                    <label class="custom-control-label small font-weight-bold" for="statutory_toggle">Enable Calc</label>
                                </div>
                                <span class="badge badge-soft-danger ml-2 py-1 px-2" id="deductions_badge">Deductions: ₹ 0.00</span>
                                <i class="fe fe-chevron-down text-muted ml-2 cursor-pointer" data-toggle="collapse" data-target="#statutoryCollapse"></i>
                            </div>
                        </div>
                        <div id="statutoryCollapse" class="collapse show">
                            <div class="card-body bg-light border-top">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">PF Employee (12%)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="pf_emp_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">PF Employer (12%)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="pf_empr_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">EPS (8.33% capped)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="eps_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">EPF Balance (auto)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="epf_balance_display">₹ 0.00</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">ESI Employee (0.75%)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="esi_emp_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">ESI Employer (3.25%)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="esi_empr_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Gratuity (4.81%)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="gratuity_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Bonus Provision (8.33%)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="bonus_display">₹ 0.00</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">TDS Monthly (auto)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="tds_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Professional Tax</label>
                                        <input type="number" name="professional_tax" class="form-control calc-trigger" placeholder="200" value="200">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">80C Deduction</label>
                                        <input type="number" name="deduction_80c" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">80D Deduction</label>
                                        <input type="number" name="deduction_80d" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Prev. Employer TDS</label>
                                        <input type="number" name="prev_employer_tds" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">LWF Employee</label>
                                        <input type="number" name="lwf_employee" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">LWF Employer</label>
                                        <input type="number" name="lwf_employer" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Surcharge</label>
                                        <input type="number" name="surcharge" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Advance Recovery</label>
                                        <input type="number" name="advance_recovery" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Loan EMI</label>
                                        <input type="number" name="loan_emi" class="form-control calc-trigger" placeholder="0">
                                    </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statutory Section -->
                    <div class="pc-card mb-3 overflow-hidden">
                        <div class="pc-section-header pc-header-leave cursor-pointer" data-toggle="collapse" data-target="#leaveCollapse">
                            <div class="d-flex align-items-center">
                                <div class="pc-section-icon bg-info-soft">
                                    <i class="fe fe-calendar text-info"></i>
                                </div>
                                <span class="pc-section-title ml-3">Leave &amp; Attendance</span>
                            </div>
                            <i class="fe fe-chevron-down pc-chevron-icon"></i>
                        </div>
                        <div id="leaveCollapse" class="collapse show">
                            <div class="pc-section-body">
                                <div class="row">
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Working Days <span class="text-danger">*</span></label>
                                        <input type="number" name="total_working_days" class="form-control pc-input calc-trigger" value="26">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Paid Days</label>
                                        <input type="number" id="paid_days" class="form-control pc-input" style="background:#f8f9fc;" readonly placeholder="Auto">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Present Days</label>
                                        <input type="number" name="present_days" class="form-control pc-input calc-trigger" value="26">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">LOP Days</label>
                                        <input type="number" name="lop_days" id="lop_days" class="form-control pc-input calc-trigger" value="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Sick Leave</label>
                                        <input type="number" name="sick_leave" class="form-control pc-input calc-trigger" value="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Casual Leave</label>
                                        <input type="number" name="casual_leave" class="form-control pc-input calc-trigger" value="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Earned Leave</label>
                                        <input type="number" name="earned_leave" class="form-control pc-input calc-trigger" value="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Overtime Hours</label>
                                        <input type="number" name="ot_hours" class="form-control pc-input calc-trigger" value="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Late Marks</label>
                                        <input type="number" name="late_marks" class="form-control pc-input calc-trigger" value="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Half Days</label>
                                        <input type="number" name="half_days" class="form-control pc-input calc-trigger" value="0">
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">LOP Deduction</label>
                                        <div class="pc-auto-display pc-auto-danger" id="lop_deduction_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-6 col-md-3 form-group">
                                        <label class="pc-label">Per Day Salary</label>
                                        <div class="pc-auto-display" id="per_day_display">₹ 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div></div>
                    </div>

                </form>
            </div><!-- End Col-11 -->
        </div><!-- End Row -->

        <!-- Full Width Summary Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="pc-summary-dashboard full-width mb-5 shadow-lg">
                    <div class="pc-summary-main-card">
                        <div class="pc-main-stats">
                            <div class="pc-stat-item">
                                <span class="pc-stat-label">Gross Earnings</span>
                                <div class="pc-stat-value text-dark" id="result_gross">₹ 0.00</div>
                            </div>
                            <div class="pc-stat-divider"></div>
                            <div class="pc-stat-item">
                                <span class="pc-stat-label text-danger">Total Deductions</span>
                                <div class="pc-stat-value text-danger" id="result_deductions">₹ 0.00</div>
                            </div>
                            <div class="pc-stat-divider"></div>
                            <div class="pc-stat-item pc-stat-highlight">
                                <span class="pc-stat-label text-primary">Net Pay (Take Home)</span>
                                <div class="pc-stat-value text-primary" id="result_net">₹ 0.00</div>
                            </div>
                        </div>
                        
                        <div class="pc-secondary-stats">
                            <div class="pc-sec-item">
                                <span class="pc-sec-label">Monthly CTC</span>
                                <span class="pc-sec-value" id="result_monthly_ctc">₹ 0.00</span>
                            </div>
                            <div class="pc-sec-item">
                                <span class="pc-sec-label">Annual CTC</span>
                                <span class="pc-sec-value font-weight-bold" id="result_annual_ctc">₹ 0.00</span>
                            </div>
                            <div class="pc-sec-item">
                                <span class="pc-sec-label">Employer Cost</span>
                                <span class="pc-sec-value" id="result_empr">₹ 0.00</span>
                            </div>
                            <div class="pc-sec-item d-none d-lg-flex">
                                <span class="pc-sec-label">Taxable / yr</span>
                                <span class="pc-sec-value" id="result_annual_taxable">₹ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile-Friendly Sticky Footer -->
        <div class="pc-sticky-footer" id="payrollFooter">
            <div class="pc-footer-actions">
                <button type="button" class="pc-footer-btn pc-btn-save" onclick="saveDraft()" id="saveDraftBtn">
                    <i class="fe fe-save"></i>
                    <span class="pc-btn-label">Save Draft</span>
                </button>
                <button type="button" class="pc-footer-btn pc-btn-pdf" onclick="exportPDF()">
                    <i class="fe fe-file-text"></i>
                    <span class="pc-btn-label">PDF</span>
                </button>
                <button type="button" class="pc-footer-btn pc-btn-csv" onclick="exportCSV()">
                    <i class="fe fe-download"></i>
                    <span class="pc-btn-label">CSV</span>
                </button>
            </div>
            <div class="pc-footer-totals">
                <div class="pc-footer-stat">
                    <span class="pc-footer-stat-label">Net</span>
                    <span class="pc-footer-stat-value text-primary" id="sticky_net">₹ 0</span>
                </div>
                <div class="pc-footer-stat d-none d-sm-flex">
                    <span class="pc-footer-stat-label">CTC/yr</span>
                    <span class="pc-footer-stat-value text-dark" id="sticky_ctc">₹ 0</span>
                </div>
            </div>
        </div>

        <!-- Extra Spacer to prevent Footer Overlap -->
        <div style="height: 140px;"></div>
    </div>

    <!-- Payroll History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title mb-0" id="historyModalLabel"><i class="fe fe-clock mr-2"></i> Payroll History</h5>
                        <small class="opacity-75" id="history_employee_name">Employee</small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <!-- Desktop Table -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover mb-0">
                            <thead style="background:#f4f6fb;" class="text-muted small text-uppercase font-weight-bold">
                                <tr>
                                    <th class="pl-4">Month / Year</th>
                                    <th>Gross</th>
                                    <th>LOP</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Monthly CTC</th>
                                    <th>By</th>
                                    <th class="pr-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="history_table_body">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile Card List -->
                    <div class="d-block d-md-none" id="history_card_body"></div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal"><i class="fe fe-x mr-1"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const formatter = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 2 });
    window.SELECTED_USER_ID = ''; // GLOBAL TRACKER

    function calculateAll() {
        const form = document.querySelector('#payroll-calculator-form');
        if (!form) return;
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Helper for safe parsing
        const n = (val) => {
            const parsed = parseFloat(val);
            return isNaN(parsed) ? 0 : parsed;
        };

        // Basic inputs
        const basic = n(data.basic);
        const hraType = document.getElementById('hra_type').value;
        const hraVal = n(data.hra);
        const daType = document.getElementById('da_type').value;
        const daVal = n(data.da);
        
        // Auto calcs
        let hraActual = (hraType === 'percent') ? (basic * hraVal / 100) : hraVal;
        let daActual = (daType === 'percent') ? (basic * daVal / 100) : daVal;
        
        document.getElementById('hra_auto_display').textContent = formatter.format(hraActual);
        document.getElementById('da_auto_display').textContent = formatter.format(daActual);
        
        // Other earnings
        const earningsFields = [
            'special_allowance', 'conveyance_allowance', 'medical_allowance',
            'education_allowance', 'uniform_allowance', 'food_allowance',
            'telephone_allowance', 'books_allowance', 'research_allowance',
            'cca', 'transport_allowance'
        ];
        
        let totalEarnings = basic + hraActual + daActual;
        earningsFields.forEach(f => totalEarnings += n(data[f]));
        
        document.getElementById('gross_badge').textContent = `Gross: ${formatter.format(totalEarnings)}`;
        document.getElementById('result_gross').textContent = formatter.format(totalEarnings);
        
        // Statutory Toggle check
        const statutoryEnabled = document.getElementById('statutory_toggle').checked;
        
        let pfEmp = 0, pfEmpr = 0, eps = 0, epfEmpr = 0, esiEmp = 0, esiEmpr = 0, gratuity = 0, bonus = 0, tds = 0;
        
        if (statutoryEnabled) {
            const pfWage = Math.min(basic + daActual, 15000); 
            const pfActualWage = basic + daActual;
            
            pfEmp = pfActualWage * 0.12;
            eps = pfWage * 0.0833;
            epfEmpr = pfEmp - eps;
            
            esiEmp = (totalEarnings <= 21000) ? Math.ceil(totalEarnings * 0.0075) : 0;
            esiEmpr = (totalEarnings <= 21000) ? Math.ceil(totalEarnings * 0.0325) : 0;
            
            gratuity = basic * 0.0481;
            bonus = basic * 0.0833;
            
            let taxableAnnual = totalEarnings * 12;
            tds = (taxableAnnual > 500000) ? (taxableAnnual - 500000) * 0.1 / 12 : 0;
        }
        
        document.getElementById('pf_emp_display').textContent = formatter.format(pfEmp);
        document.getElementById('pf_empr_display').textContent = formatter.format(pfEmp);
        document.getElementById('eps_display').textContent = formatter.format(eps);
        document.getElementById('epf_balance_display').textContent = formatter.format(epfEmpr);
        document.getElementById('esi_emp_display').textContent = formatter.format(esiEmp);
        document.getElementById('esi_empr_display').textContent = formatter.format(esiEmpr);
        document.getElementById('gratuity_display').textContent = formatter.format(gratuity);
        document.getElementById('bonus_display').textContent = formatter.format(bonus);
        document.getElementById('tds_display').textContent = formatter.format(tds);
        
        const workingDays = Math.max(1, parseInt(data.total_working_days) || 26);
        const lopDays = n(data.lop_days);
        const perDay = totalEarnings / workingDays;
        const lopDeduction = lopDays * perDay;
        
        document.getElementById('paid_days').value = Math.max(0, workingDays - lopDays);
        document.getElementById('per_day_display').textContent = formatter.format(perDay);
        document.getElementById('lop_deduction_display').textContent = formatter.format(lopDeduction);
        
        const otherDeductions = n(data.professional_tax) + n(data.lwf_employee) +
                                n(data.advance_recovery) + n(data.loan_emi) + tds + pfEmp + esiEmp;
        
        const totalDeductions = otherDeductions + lopDeduction;
        const netSalary = totalEarnings - totalDeductions;
        const emprTotal = pfEmp + esiEmpr + gratuity + bonus + n(data.lwf_employer);
        const monthlyCTC = totalEarnings + emprTotal;
        
        document.getElementById('result_deductions').textContent = formatter.format(totalDeductions);
        document.getElementById('deductions_badge').textContent = `Deductions: ${formatter.format(totalDeductions)}`;
        document.getElementById('result_net').textContent = formatter.format(netSalary);
        document.getElementById('sticky_net').textContent = formatter.format(netSalary);
        document.getElementById('result_empr').textContent = formatter.format(emprTotal);
        document.getElementById('result_monthly_ctc').textContent = formatter.format(monthlyCTC);
        document.getElementById('result_annual_ctc').textContent = formatter.format(monthlyCTC * 12);
        document.getElementById('sticky_ctc').textContent = formatter.format(monthlyCTC * 12);
        document.getElementById('total_ctc_hidden').value = monthlyCTC * 12;
        document.getElementById('result_annual_taxable').textContent = formatter.format(totalEarnings * 12);
    }

    document.querySelectorAll('.calc-trigger').forEach(input => {
        input.addEventListener('input', calculateAll);
    });

    function loadEmployeeData(id) {
        if (!id) {
            window.SELECTED_USER_ID = '';
            return;
        }
        
        window.SELECTED_USER_ID = id;
        console.log("loadEmployeeData CALLED with ID:", id);

        const option = document.querySelector(`#employee_select option[value="${id}"]`);
        if (!option) {
            console.error("Option NOT found for ID:", id);
            return;
        }
        const data = JSON.parse(option.dataset.structure);
        
        for (const [key, value] of Object.entries(data)) {
            if (key === 'user_id' || key === 'id') continue; 
            const fields = document.getElementsByName(key);
            if (fields.length > 0) {
                if (fields[0].type === 'checkbox') {
                    fields[0].checked = (value == 1 || value == 'on');
                } else {
                    fields[0].value = value || 0;
                }
            }
        }
        calculateAll();
    }

    // GLOBAL LISTENER for all changes (Theme Safe)
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'employee_select') {
            console.log("GLOBAL CHANGE detected for employee_select. ID:", e.target.value);
            loadEmployeeData(e.target.value);
        }
    });

    // jQuery FALLBACK for themed selects like Select2
    if (typeof $ !== 'undefined') {
        $(document).on('change', '#employee_select', function() {
            console.log("JQUERY CHANGE detected for employee_select. ID:", $(this).val());
            loadEmployeeData($(this).val());
        });
    }

    function resetCalculator() {
        document.getElementById('payroll-calculator-form').reset();
        window.SELECTED_USER_ID = '';
        calculateAll();
    }

    function viewHistory() {
        const id = document.getElementById('employee_select').value;
        if (!id) {
            alert("Please select an employee first.");
            return;
        }

        const option = document.querySelector(`#employee_select option[value="${id}"]`);
        document.getElementById('history_employee_name').textContent = option.text;
        
        const body = document.getElementById('history_table_body');
        body.innerHTML = '<tr><td colspan="8" class="text-center py-4"><i class="fe fe-loader fe-spin mr-2"></i> Loading history...</td></tr>';
        
        $('#historyModal').modal('show');

        fetch(`payroll-manage?action=getHistory&user_id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.history.length === 0) {
                        body.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted italic">No payslip history found for this employee.</td></tr>';
                        return;
                    }
                    
                    let html = '';
                    let mobileHtml = '';
                    const months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    data.history.forEach(p => {
                        const gross = parseFloat(p.gross_salary) || 0;
                        const net = parseFloat(p.net_salary) || 0;
                        const lop = parseFloat(p.lop_deductions) || 0;
                        const deductions = gross - net;
                        const monthlyCTC = parseFloat(p.monthly_ctc) || 0;
                        const safeRecord = JSON.stringify(p).replace(/'/g, "&apos;");
                        
                        // Desktop Row
                        html += `
                            <tr>
                                <td class="pl-4">
                                    <span class="font-weight-bold">${months[p.month]} ${p.year}</span><br>
                                    <small class="text-muted">ID: #PAY-${p.id}</small>
                                </td>
                                <td class="text-success font-weight-600">₹${gross.toLocaleString('en-IN', {minimumFractionDigits:2})}</td>
                                <td>${lop > 0 ? '<span class="badge badge-soft-danger">' + (p.lop_days || '?') + ' days</span>' : '<span class="text-muted">—</span>'}</td>
                                <td class="text-danger">₹${deductions.toLocaleString('en-IN', {minimumFractionDigits:2})}</td>
                                <td class="font-weight-bold text-primary">₹${net.toLocaleString('en-IN', {minimumFractionDigits:2})}</td>
                                <td class="text-dark">₹${monthlyCTC.toLocaleString('en-IN', {minimumFractionDigits:2})}</td>
                                <td><span class="badge badge-light border">${p.processed_by_name || 'System'}</span></td>
                                <td class="pr-4 text-right">
                                    <div class="btn-group">
                                        <a href="payroll?action=payslip&id=${p.id}" target="_blank" class="btn btn-sm btn-outline-primary" title="View / Print Payslip">
                                            <i class="fe fe-file-text mr-1"></i> Payslip
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick='loadToCalculator(${safeRecord})' title="Load this record's data into the Calculator below">
                                            <i class="fe fe-upload-cloud"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick='loadRecordForEdit(${safeRecord})' title="Edit this payslip record">
                                            <i class="fe fe-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;

                        // Mobile Card
                        mobileHtml += `
                            <div class="history-mobile-card">
                                <div class="history-mobile-card-header">
                                    <div>
                                        <div class="font-weight-bold text-dark">${months[p.month]} ${p.year}</div>
                                        <small class="text-muted">ID: #PAY-${p.id}</small>
                                    </div>
                                    <div class="text-right">
                                        <div class="h6 mb-0 text-primary font-weight-bold">₹${net.toLocaleString('en-IN', {minimumFractionDigits:0})}</div>
                                        <small class="text-muted">Net Payable</small>
                                    </div>
                                </div>
                                <div class="row no-gutters mt-2 pt-2 border-top">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Gross</small>
                                        <div class="small font-weight-bold text-success">₹${gross.toLocaleString('en-IN', {minimumFractionDigits:0})}</div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <small class="text-muted d-block">Deductions</small>
                                        <div class="small font-weight-bold text-danger">₹${deductions.toLocaleString('en-IN', {minimumFractionDigits:0})}</div>
                                    </div>
                                </div>
                                <div class="history-mobile-actions">
                                    <a href="payroll?action=payslip&id=${p.id}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fe fe-file-text mr-1"></i> Payslip
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick='loadToCalculator(${safeRecord})'>
                                        <i class="fe fe-upload-cloud mr-1"></i> Load
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick='loadRecordForEdit(${safeRecord})'>
                                        <i class="fe fe-edit mr-1"></i> Edit
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    body.innerHTML = html;
                    const cardBody = document.getElementById('history_card_body');
                    if (cardBody) cardBody.innerHTML = mobileHtml;
                } else {
                    body.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">${data.message}</td></tr>`;
                }
            })
            .catch(err => {
                body.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Error loading history: ${err.message}</td></tr>`;
            });
    }

    window.EDITING_HISTORY_ID = null;

    /**
     * Loads a history record's breakdown data into the calculator form
     * WITHOUT entering edit mode. Useful for recalculating or generating a new month
     * based on a previous payslip's salary structure.
     */
    function loadToCalculator(record) {
        $('#historyModal').modal('hide');

        const breakdown = (typeof record.breakdown_json === 'string')
            ? JSON.parse(record.breakdown_json)
            : (record.breakdown_json || {});

        // Set the calc month to current month (not the historical one)
        // so the user can tweak and save as a new record if needed.
        // Uncomment the line below if you want it to prefill the historical month too:
        // const monthInput = document.querySelector('input[name="calc_month"]');
        // if (monthInput) monthInput.value = `${record.year}-${record.month.toString().padStart(2, '0')}`;

        // Populate employee dropdown
        if (record.user_id) {
            const sel = document.getElementById('employee_select');
            if (sel) sel.value = record.user_id;
            window.SELECTED_USER_ID = record.user_id;
        }

        // Map all breakdown fields to form inputs
        for (const [key, value] of Object.entries(breakdown)) {
            const fields = document.getElementsByName(key);
            if (fields.length > 0) {
                if (fields[0].type === 'checkbox') {
                    fields[0].checked = (value == 1 || value === true || value === 'on');
                } else {
                    fields[0].value = (value !== null && value !== undefined) ? value : 0;
                }
            }
        }

        // Also set lop_days from history record if not in breakdown
        const lopField = document.getElementsByName('lop_days')[0];
        if (lopField && breakdown.lop_days !== undefined) {
            lopField.value = breakdown.lop_days || 0;
        }

        // Recalculate
        calculateAll();

        // Show info banner (NOT edit mode — no EDITING_HISTORY_ID set)
        const months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const alertHtml = `
            <div id="load-mode-alert" class="alert alert-info alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
                <strong><i class="fe fe-upload-cloud mr-2"></i> Data Loaded:</strong>
                Salary data from <b>${months[record.month]} ${record.year}</b> has been loaded into the calculator.
                You can now modify values and <b>Save Draft</b> to create a new payslip for the selected month.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        const container = document.querySelector('.container-fluid');
        const existing = document.getElementById('load-mode-alert');
        const existingEdit = document.getElementById('edit-mode-alert');
        if (existing) existing.remove();
        if (existingEdit) existingEdit.remove();
        container.insertAdjacentHTML('afterbegin', alertHtml);

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function loadRecordForEdit(record) {
        $('#historyModal').modal('hide');
        window.EDITING_HISTORY_ID = record.id;
        
        // Populate Calculator
        const breakdown = JSON.parse(record.breakdown_json);
        console.log("Loading Record for Edit:", record.id, breakdown);

        // Update Month Input
        const monthInput = document.querySelector('input[name="calc_month"]');
        if (monthInput) {
            monthInput.value = `${record.year}-${record.month.toString().padStart(2, '0')}`;
        }

        // Map breakdown fields to form
        for (const [key, value] of Object.entries(breakdown)) {
            const fields = document.getElementsByName(key);
            if (fields.length > 0) {
                if (fields[0].type === 'checkbox') {
                    fields[0].checked = (value == 1 || value == true);
                } else {
                    fields[0].value = value || 0;
                }
            }
        }

        // Update UI
        const saveBtn = document.querySelector('button[onclick="saveDraft()"]');
        saveBtn.innerHTML = '<i class="fe fe-check-circle mr-2"></i> Update Payslip';
        saveBtn.classList.replace('btn-primary', 'btn-warning');
        
        calculateAll();
        
        // Show alert
        const alertHtml = `
            <div id="edit-mode-alert" class="alert alert-warning alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
                <strong><i class="fe fe-edit-3 mr-2"></i> Edit Mode Active:</strong> You are modifying the payslip for <b>${document.getElementById('history_employee_name').textContent}</b> for ${record.month}/${record.year}.
                <button type="button" class="close" onclick="cancelEditMode()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        const container = document.querySelector('.container-fluid');
        const existing = document.getElementById('edit-mode-alert');
        if (existing) existing.remove();
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function cancelEditMode() {
        window.EDITING_HISTORY_ID = null;
        const alert = document.getElementById('edit-mode-alert');
        if (alert) alert.remove();
        
        const saveBtn = document.querySelector('button[onclick="saveDraft()"]');
        saveBtn.innerHTML = '<i class="fe fe-save mr-2"></i> Save Draft';
        saveBtn.classList.replace('btn-warning', 'btn-primary');
        
        resetCalculator();
    }

    function saveDraft() {
        const form = document.getElementById('payroll-calculator-form');
        const formData = new FormData(form);
        
        const employeeId = formData.get('user_id') || window.SELECTED_USER_ID || document.getElementById('employee_select').value;
        
        if (!employeeId || employeeId === "" || employeeId === "0") {
            alert("Please select an employee first.");
            return;
        }

        if (!formData.has('user_id')) formData.append('user_id', employeeId);
        
        // Add editing flag if active
        if (window.EDITING_HISTORY_ID) {
            formData.append('history_id', window.EDITING_HISTORY_ID);
        }

        const saveBtn = document.querySelector('button[onclick="saveDraft()"]');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fe fe-loader fe-spin mr-1"></i> ' + (window.EDITING_HISTORY_ID ? 'Updating...' : 'Saving...');

        fetch('payroll-manage?action=saveDraft', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                
                // REFRESH HISTORY IF MODAL IS OPEN
                if ($('#historyModal').is(':visible')) {
                    viewHistory(); 
                }
                
                if (window.EDITING_HISTORY_ID) cancelEditMode();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            alert("Request Failed: " + error.message);
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        });
    }

    function exportCSV() {
        window.location.href = 'payroll-manage?action=exportCSV';
    }

    function exportPDF() {
        window.print();
    }

    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('.cursor-pointer');
        if (!trigger) return;
        const targetId = trigger.getAttribute('data-target');
        if (!targetId) return;
        const target = document.querySelector(targetId);
        if (target) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                new bootstrap.Collapse(target, { toggle: true });
            } else {
                target.classList.toggle('show');
            }
            const icon = trigger.querySelector('i.fe-chevron-down, i.fe-chevron-up');
            if (icon) {
                icon.classList.toggle('fe-chevron-down');
                icon.classList.toggle('fe-chevron-up');
            }
        }
    });

    calculateAll();
</script>

<style>
/* ============================================================
   PAYROLL CALCULATOR – MOBILE-FIRST UI SYSTEM
   ============================================================ */

/* Google Font */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

body, .main-content { font-family: 'Inter', sans-serif; }

/* ---- Page Header Banner ---- */
.payroll-header-banner {
    background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    border-radius: 16px;
    padding: 20px 24px;
    color: #fff;
    box-shadow: 0 8px 30px rgba(67,97,238,0.25);
}
.payroll-header-title { font-size: 1.3rem; font-weight: 700; margin: 0; color: #fff; }
.payroll-header-subtitle { font-size: 0.82rem; margin: 4px 0 0; color: rgba(255,255,255,0.75); }
@media (max-width: 576px) {
    .payroll-header-banner { padding: 14px 16px; }
    .payroll-header-title { font-size: 1.05rem; }
}

/* ---- Base Cards ---- */
.pc-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #eef0f7;
    overflow: hidden;
}
.pc-card-body { padding: 16px 18px; }

/* ---- Section Headers ---- */
.pc-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    background: #fff;
    border-bottom: 1px solid #f0f2f9;
    cursor: pointer;
    user-select: none;
}
.pc-section-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.bg-success-soft  { background: rgba(0,184,148,0.12); }
.bg-warning-soft  { background: rgba(253,196,39,0.15); }
.bg-info-soft     { background: rgba(23,162,184,0.12); }
.pc-section-title { font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #344054; }
.pc-live-badge {
    display: inline-block;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
}
.badge-success-soft { background: rgba(0,184,148,0.12); color: #00b894; }
.badge-danger-soft  { background: rgba(247,37,133,0.12); color: #f72585; }
.pc-chevron-icon { font-size: 1rem; color: #adb5bd; transition: transform 0.25s; }

/* ---- Section Body ---- */
.pc-section-body { padding: 16px 18px 4px; background: #f8f9fd; }
.pc-section-subtitle {
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.05em; color: #6c757d; margin-bottom: 12px;
}

/* ---- Form Controls ---- */
.pc-label {
    font-size: 0.74rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pc-input {
    border: 1.5px solid #e8eaf0 !important;
    border-radius: 9px !important;
    font-size: 0.88rem;
    padding: 8px 10px;
    background: #fff !important;
    height: 40px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.pc-input:focus {
    border-color: #4361ee !important;
    box-shadow: 0 0 0 3px rgba(67,97,238,0.12) !important;
    outline: none;
}
.pc-select {
    border: 1.5px solid #e8eaf0 !important;
    border-radius: 9px !important;
    font-size: 0.88rem;
    height: 44px;
    background: #f8f9fd !important;
    transition: border-color 0.2s;
}
.pc-select:focus { border-color: #4361ee !important; outline: none; }
.pc-type-select {
    height: 40px !important;
    font-size: 0.75rem !important;
    width: 46px !important;
    border: 1.5px solid #e8eaf0 !important;
    border-right: none !important;
    border-radius: 9px 0 0 9px !important;
    background: #f0f2f9 !important;
    padding: 0 4px;
}
.pc-auto-display {
    background: rgba(67,97,238,0.07);
    color: #4361ee;
    font-weight: 600;
    font-size: 0.88rem;
    border-radius: 9px;
    padding: 8px 12px;
    border: none;
    height: 40px;
    display: flex;
    align-items: center;
}
.pc-auto-danger { background: rgba(247,37,133,0.07); color: #f72585; }
.pc-history-btn {
    border-radius: 9px !important;
    font-size: 0.85rem;
    font-weight: 600;
    height: 40px;
    white-space: nowrap;
}

/* ---- Professional Summary Dashboard ---- */
.pc-summary-dashboard {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #eef0f7;
    box-shadow: 0 4px 25px rgba(0,0,0,0.06);
    overflow: hidden;
}
.pc-summary-dashboard.full-width {
    margin-left: -5px;
    margin-right: -5px;
}
.pc-summary-main-card { padding: 0; }
.pc-main-stats {
    display: flex;
    background: #f8f9ff;
    border-bottom: 1px solid #eef0f7;
    padding: 24px;
    align-items: center;
    justify-content: space-around;
}
.pc-stat-item { text-align: center; flex: 1; }
.pc-stat-label { 
    display: block; 
    font-size: 0.78rem; 
    text-transform: uppercase; 
    font-weight: 700; 
    letter-spacing: 0.05em;
    margin-bottom: 8px;
    color: #6c757d;
}
.pc-stat-value { font-size: 1.5rem; font-weight: 800; }
.pc-stat-divider { width: 1px; height: 50px; background: #e2e5f0; }

.pc-stat-highlight {
    background: rgba(67, 97, 238, 0.05);
    padding: 12px;
    border-radius: 12px;
}

.pc-secondary-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    padding: 20px 30px;
    background: #fff;
}
.pc-sec-item { display: flex; flex-direction: column; padding: 0 15px; }
.pc-sec-label { font-size: 0.68rem; color: #adb5bd; font-weight: 600; text-transform: uppercase; }
.pc-sec-value { font-size: 1rem; color: #495057; font-weight: 600; }

@media (max-width: 768px) {
    .pc-main-stats { flex-direction: column; padding: 20px; gap: 15px; }
    .pc-stat-divider { display: none; }
    .pc-secondary-stats { grid-template-columns: repeat(2, 1fr); gap: 15px; padding: 20px; }
    .pc-stat-value { font-size: 1.3rem; }
    .pc-summary-dashboard.full-width { margin-left: 0; margin-right: 0; }
}

/* ---- Sticky Footer ---- */
.pc-sticky-footer {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: #fff;
    border-top: 1px solid #e8eaf0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1030;
    gap: 10px;
}
.pc-footer-actions { display: flex; gap: 8px; flex-shrink: 0; }
.pc-footer-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 12px;
    padding: 8px 14px;
    font-size: 0.7rem;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
    min-width: 58px;
    line-height: 1.2;
    gap: 3px;
}
.pc-footer-btn i { font-size: 1.1rem; }
.pc-footer-btn:active { transform: scale(0.95); }
.pc-btn-save  { background: #4361ee; color: #fff; box-shadow: 0 3px 12px rgba(67,97,238,0.3); }
.pc-btn-pdf   { background: #f72585; color: #fff; box-shadow: 0 3px 12px rgba(247,37,133,0.25); }
.pc-btn-csv   { background: #00b894; color: #fff; box-shadow: 0 3px 12px rgba(0,184,148,0.25); }
.pc-btn-label { font-size: 0.65rem; letter-spacing: 0.02em; }
.pc-footer-totals {
    display: flex; gap: 16px; align-items: center; justify-content: flex-end; flex: 1; overflow: hidden;
}
.pc-footer-stat { display: flex; flex-direction: column; align-items: flex-end; }
.pc-footer-stat-label { font-size: 0.6rem; text-transform: uppercase; font-weight: 700; color: #adb5bd; letter-spacing: 0.05em; }
.pc-footer-stat-value { font-size: 0.98rem; font-weight: 700; white-space: nowrap; }
@media (max-width: 400px) {
    .pc-footer-btn { min-width: 48px; padding: 7px 10px; }
    .pc-footer-stat-value { font-size: 0.82rem; }
}
@media (min-width: 768px) {
    .pc-sticky-footer { padding: 12px 24px; }
    .pc-footer-btn { flex-direction: row; gap: 6px; flex-direction: row; min-width: auto; padding: 10px 18px; }
    .pc-footer-btn i { font-size: 0.95rem; }
    .pc-btn-label { font-size: 0.8rem; }
    .pc-footer-stat-value { font-size: 1.1rem; }
}

/* ---- History Modal Polish ---- */
.modal-dialog-scrollable .modal-body { overflow-y: auto; max-height: 70vh; }
#history_table_body tr:hover { background: #f8f9ff; }
.history-mobile-card {
    margin: 8px 12px;
    border-radius: 12px;
    border: 1px solid #eef0f7;
    padding: 14px;
    background: #fff;
    box-shadow: 0 1px 6px rgba(0,0,0,0.05);
}
.history-mobile-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.history-mobile-actions { display: flex; gap: 6px; margin-top: 12px; }
.history-mobile-actions .btn { flex: 1; font-size: 0.75rem; border-radius: 8px; padding: 7px 4px; }

/* ---- Misc ---- */
.bg-soft-primary { background: rgba(67, 97, 238, 0.1); }
.bg-soft-danger  { background: rgba(247, 37, 133, 0.1); }
.bg-soft-success { background: rgba(0, 184, 148, 0.1); }
.badge-soft-primary { background: rgba(67,97,238,0.15); color: #4361ee; font-weight: 700; }
.badge-soft-danger  { background: rgba(247,37,133,0.15); color: #f72585; font-weight: 700; }
.font-weight-600 { font-weight: 600; }
.font-weight-700 { font-weight: 700; }
.cursor-pointer { cursor: pointer; }
.form-group { margin-bottom: 12px; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
