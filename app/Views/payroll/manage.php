<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title"><i class="fe fe-calculator mr-2 text-primary"></i> Salary & CTC Calculator</h2>
                        <p class="text-muted">Real-time salary breakdown calculator with CTC and statutory computations</p>
                    </div>
                    <button type="button" class="btn btn-white shadow-sm" onclick="resetCalculator()"><i class="fe fe-refresh-cw mr-1"></i> Reset</button>
                </div>

                <form id="payroll-calculator-form">
                    <!-- Selection Header -->
                    <div class="card shadow-sm border-0 mb-4 bg-white">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-5 form-group">
                                    <label class="small text-muted font-weight-bold mb-1"><i class="fe fe-user mr-1"></i> Employee Selection</label>
                                    <select id="employee_select" name="user_id" class="form-control form-control-lg bg-light border-0 custom-select" onchange="loadEmployeeData(this.value)">
                                        <option value="">-- Choose Employee --</option>
                                        <?php foreach($users as $u): ?>
                                            <option value="<?php echo $u['id']; ?>" data-structure='<?php echo json_encode($u); ?>'>
                                                <?php echo htmlspecialchars($u['name']); ?> (#EMP<?php echo str_pad($u['id'], 4, '0', STR_PAD_LEFT); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="small text-muted font-weight-bold mb-1"><i class="fe fe-calendar mr-1"></i> Calculation Month</label>
                                    <input type="month" name="calc_month" class="form-control form-control-lg bg-light border-0" value="<?php echo date('Y-m'); ?>">
                                </div>
                                <div class="col-md-3 form-group d-flex align-items-end">
                                    <button type="button" class="btn btn-lg btn-outline-primary btn-block shadow-sm" style="border-radius: 10px;" onclick="viewHistory()">
                                        <i class="fe fe-clock mr-1"></i> View History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Earnings Section -->
                    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 small text-uppercase font-weight-bold text-dark cursor-pointer" data-toggle="collapse" data-target="#earningsCollapse">
                                <i class="fe fe-dollar-sign mr-2 text-success"></i> Earnings
                                <span class="badge badge-soft-primary ml-2 py-1 px-2" id="gross_badge">Gross: ₹ 0.00</span>
                            </h5>
                            <i class="fe fe-chevron-up text-muted cursor-pointer" data-toggle="collapse" data-target="#earningsCollapse"></i>
                        </div>
                        <div id="earningsCollapse" class="collapse show">
                            <div class="card-body bg-light border-top">
                                <p class="text-muted small font-weight-bold mb-3 text-uppercase">Fixed Components</p>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Basic Salary *</label>
                                        <input type="number" name="basic" id="basic" class="form-control calc-trigger" placeholder="0" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">HRA</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <select class="custom-select border-right-0 rounded-left py-0" style="height: auto; font-size: 11px; width: 50px;" id="hra_type" onchange="calculateAll()">
                                                    <option value="percent">%</option>
                                                    <option value="fixed" selected>₹</option>
                                                </select>
                                            </div>
                                            <input type="number" name="hra" id="hra" class="form-control calc-trigger" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">HRA Auto</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="hra_auto_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">DA</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <select class="custom-select border-right-0 rounded-left py-0" style="height: auto; font-size: 11px; width: 50px;" id="da_type" onchange="calculateAll()">
                                                    <option value="percent">%</option>
                                                    <option value="fixed" selected>₹</option>
                                                </select>
                                            </div>
                                            <input type="number" name="da" id="da" class="form-control calc-trigger" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">DA Auto</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="da_auto_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Special Allowance</label>
                                        <input type="number" name="special_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Conveyance Allowance</label>
                                        <input type="number" name="conveyance_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Medical Allowance</label>
                                        <input type="number" name="medical_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Education Allowance</label>
                                        <input type="number" name="education_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Uniform Allowance</label>
                                        <input type="number" name="uniform_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Food Allowance</label>
                                        <input type="number" name="food_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Telephone Allowance</label>
                                        <input type="number" name="telephone_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Books Allowance</label>
                                        <input type="number" name="books_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Research Allowance</label>
                                        <input type="number" name="research_allowance" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">CCA</label>
                                        <input type="number" name="cca" class="form-control calc-trigger" placeholder="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Transport Allowance</label>
                                        <input type="number" name="transport_allowance" class="form-control calc-trigger" placeholder="0">
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
                    </div>

                    <!-- Leave & Attendance Section -->
                    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 small text-uppercase font-weight-bold text-dark cursor-pointer" data-toggle="collapse" data-target="#leaveCollapse">
                                <i class="fe fe-calendar mr-2 text-info"></i> Leave & Attendance
                            </h5>
                            <i class="fe fe-chevron-down text-muted cursor-pointer" data-toggle="collapse" data-target="#leaveCollapse"></i>
                        </div>
                        <div id="leaveCollapse" class="collapse show">
                            <div class="card-body bg-light border-top">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Total Working Days *</label>
                                        <input type="number" name="total_working_days" class="form-control calc-trigger" value="26">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Paid Days</label>
                                        <input type="number" id="paid_days" class="form-control bg-white" readonly placeholder="Calculated">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Present Days</label>
                                        <input type="number" name="present_days" class="form-control calc-trigger" value="26">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Leave Without Pay</label>
                                        <input type="number" name="lop_days" id="lop_days" class="form-control calc-trigger" value="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Sick Leave</label>
                                        <input type="number" name="sick_leave" class="form-control calc-trigger" value="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Casual Leave</label>
                                        <input type="number" name="casual_leave" class="form-control calc-trigger" value="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Earned Leave</label>
                                        <input type="number" name="earned_leave" class="form-control calc-trigger" value="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Overtime Hours</label>
                                        <input type="number" name="ot_hours" class="form-control calc-trigger" value="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Late Marks</label>
                                        <input type="number" name="late_marks" class="form-control calc-trigger" value="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Half Days</label>
                                        <input type="number" name="half_days" class="form-control calc-trigger" value="0">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">LOP Deduction (auto)</label>
                                        <div class="form-control bg-soft-danger border-0 text-danger font-weight-600" id="lop_deduction_display">₹ 0.00</div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="small font-weight-bold">Per Day Salary (auto)</label>
                                        <div class="form-control bg-soft-primary border-0 text-primary font-weight-600" id="per_day_display">₹ 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Grid -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 mb-3 bg-white">
                                <div class="card-body p-3">
                                    <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Total Earnings (Gross)</small>
                                    <h4 class="mb-0 text-success" id="result_gross">₹ 0.00</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 mb-3 bg-white">
                                <div class="card-body p-3">
                                    <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Total Deductions</small>
                                    <h4 class="mb-0 text-danger" id="result_deductions">₹ 0.00</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 mb-3 bg-primary text-white">
                                <div class="card-body p-3">
                                    <small class="text-white text-uppercase font-weight-bold d-block mb-1">Net Salary / In-Hand</small>
                                    <h4 class="mb-0" id="result_net">₹ 0.00</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 mb-3 bg-white">
                                <div class="card-body p-3">
                                    <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Employer Contributions</small>
                                    <h4 class="mb-0 text-warning" id="result_empr">₹ 0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 mb-3 bg-white">
                                <div class="card-body p-3 d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-soft-primary rounded-circle mr-3"><i class="fe fe-trending-up text-primary"></i></div>
                                    <div>
                                        <small class="text-muted text-uppercase font-weight-600 d-block">Monthly CTC</small>
                                        <h5 class="mb-0 text-dark" id="result_monthly_ctc">₹ 0.00</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 mb-3 bg-dark text-white">
                                <div class="card-body p-3 d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-soft-success rounded-circle mr-3"><i class="fe fe-shield text-success"></i></div>
                                    <div>
                                        <small class="text-white-50 text-uppercase font-weight-600 d-block">Annual CTC</small>
                                        <h5 class="mb-0 text-white" id="result_annual_ctc">₹ 0.00</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 mb-3 bg-white">
                                <div class="card-body p-3 d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-soft-danger rounded-circle mr-3"><i class="fe fe-activity text-danger"></i></div>
                                    <div>
                                        <small class="text-muted text-uppercase font-weight-600 d-block">Taxable Income (Annual)</small>
                                        <h5 class="mb-0 text-dark" id="result_annual_taxable">₹ 0.00</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="total_ctc" id="total_ctc_hidden">
                </form>

                <!-- Action Footer -->
                <div class="fixed-bottom bg-white border-top shadow-lg p-3 d-flex justify-content-center align-items-center" style="z-index: 1030;">
                    <div class="container-fluid d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-primary px-4 font-weight-bold" onclick="saveDraft()"><i class="fe fe-save mr-2"></i> Save Draft</button>
                            <button type="button" class="btn btn-danger px-4 font-weight-bold ml-2" onclick="exportPDF()"><i class="fe fe-file-text mr-2"></i> Export PDF</button>
                            <button type="button" class="btn btn-success px-4 font-weight-bold ml-2" onclick="exportCSV()"><i class="fe fe-database mr-2"></i> Export CSV</button>
                        </div>
                        <div class="text-right">
                            <span class="text-muted small font-weight-bold mr-3">NET SALARY: <span class="text-primary h5 mb-0 ml-1" id="sticky_net">₹ 0.00</span></span>
                            <span class="text-muted small font-weight-bold">ANNUAL CTC: <span class="text-dark h5 mb-0 ml-1" id="sticky_ctc">₹ 0.00</span></span>
                        </div>
                    </div>
                </div>

                <div style="height: 100px;"></div> <!-- Spacer for fixed footer -->
            </div>
        </div>
    </div>

    <!-- Payroll History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title" id="historyModalLabel"><i class="fe fe-clock mr-2"></i> Payroll History - <span id="history_employee_name">Employee</span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                <tr>
                                    <th class="pl-4">Month/Year</th>
                                    <th>Gross</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Processed By</th>
                                    <th class="pr-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="history_table_body">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        body.innerHTML = '<tr><td colspan="6" class="text-center py-4"><i class="fe fe-loader fe-spin mr-2"></i> Loading history...</td></tr>';
        
        $('#historyModal').modal('show');

        fetch(`payroll-manage?action=getHistory&user_id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.history.length === 0) {
                        body.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted italic">No history found for this employee.</td></tr>';
                        return;
                    }
                    
                    let html = '';
                    const months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    data.history.forEach(p => {
                        html += `
                            <tr>
                                <td class="pl-4">
                                    <span class="font-weight-bold">${months[p.month]} ${p.year}</span><br>
                                    <small class="text-muted">ID: #PAY-${p.id}</small>
                                </td>
                                <td>₹${parseFloat(p.gross_salary).toLocaleString()}</td>
                                <td>₹${(parseFloat(p.gross_salary) - parseFloat(p.net_salary)).toLocaleString()}</td>
                                <td class="font-weight-bold text-primary">₹${parseFloat(p.net_salary).toLocaleString()}</td>
                                <td><span class="badge badge-light border">${p.processed_by_name || 'System'}</span></td>
                                <td class="pr-4 text-right">
                                    <div class="btn-group">
                                        <a href="payroll?action=payslip&id=${p.id}" target="_blank" class="btn btn-sm btn-outline-primary" title="Download/Print PDF">
                                            <i class="fe fe-download mr-1"></i> Payslip
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick='loadRecordForEdit(${JSON.stringify(p).replace(/'/g, "&apos;")})' title="Edit Record">
                                            <i class="fe fe-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    body.innerHTML = html;
                } else {
                    body.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${data.message}</td></tr>`;
                }
            })
            .catch(err => {
                body.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Error loading history: ${err.message}</td></tr>`;
            });
    }

    window.EDITING_HISTORY_ID = null;

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
.bg-soft-primary { background: rgba(67, 97, 238, 0.1); }
.bg-soft-danger { background: rgba(247, 37, 133, 0.1); }
.bg-soft-success { background: rgba(0, 184, 148, 0.1); }
.badge-soft-primary { background: rgba(67, 97, 238, 0.15); color: #4361ee; font-weight: 700; }
.badge-soft-danger { background: rgba(247, 37, 133, 0.15); color: #f72585; font-weight: 700; }
.font-weight-600 { font-weight: 600; }
.font-weight-700 { font-weight: 700; }
.card { border-radius: 12px; }
.form-control-lg { border-radius: 10px; }
.cursor-pointer { cursor: pointer; }
.fixed-bottom { transition: transform 0.3s ease; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
