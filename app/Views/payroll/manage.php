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

                <!-- Selection Header -->
                <div class="card shadow-sm border-0 mb-4 bg-white">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small text-muted font-weight-bold mb-1"><i class="fe fe-user mr-1"></i> Employee (Optional)</label>
                                <select id="employee_select" class="form-control form-control-lg bg-light border-0 custom-select" onchange="loadEmployeeData(this.value)">
                                    <option value="">Select Employee...</option>
                                    <?php foreach($users as $u): ?>
                                        <option value="<?php echo $u['id']; ?>" data-structure='<?php echo json_encode($u); ?>'>
                                            <?php echo htmlspecialchars($u['name']); ?> (#EMP<?php echo str_pad($u['id'], 4, '0', STR_PAD_LEFT); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small text-muted font-weight-bold mb-1"><i class="fe fe-calendar mr-1"></i> Calculation Month</label>
                                <input type="month" id="calc_month" class="form-control form-control-lg bg-light border-0" value="<?php echo date('Y-m'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <form id="payroll-calculator-form">
                    <!-- Earnings Section -->
                    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center cursor-pointer" data-toggle="collapse" data-target="#earningsCollapse">
                            <h5 class="card-title mb-0 small text-uppercase font-weight-bold text-dark">
                                <i class="fe fe-dollar-sign mr-2 text-success"></i> Earnings
                                <span class="badge badge-soft-primary ml-2 py-1 px-2" id="gross_badge">Gross: ₹ 0.00</span>
                            </h5>
                            <i class="fe fe-chevron-up text-muted"></i>
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
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center cursor-pointer" data-toggle="collapse" data-target="#statutoryCollapse">
                            <h5 class="card-title mb-0 small text-uppercase font-weight-bold text-dark">
                                <i class="fe fe-shield mr-2 text-warning"></i> Statutory & Deductions
                                <span class="badge badge-soft-danger ml-2 py-1 px-2" id="deductions_badge">Deductions: ₹ 0.00</span>
                            </h5>
                            <i class="fe fe-chevron-down text-muted"></i>
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
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center cursor-pointer" data-toggle="collapse" data-target="#leaveCollapse">
                            <h5 class="card-title mb-0 small text-uppercase font-weight-bold text-dark">
                                <i class="fe fe-calendar mr-2 text-info"></i> Leave & Attendance
                            </h5>
                            <i class="fe fe-chevron-down text-muted"></i>
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
</main>

<script>
    const formatter = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 2 });

    function calculateAll() {
        const formData = new FormData(document.getElementById('payroll-calculator-form'));
        const data = Object.fromEntries(formData.entries());
        
        // Basic inputs
        const basic = parseFloat(data.basic || 0);
        const hraType = document.getElementById('hra_type').value;
        const hraVal = parseFloat(data.hra || 0);
        const daType = document.getElementById('da_type').value;
        const daVal = parseFloat(data.da || 0);
        
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
        earningsFields.forEach(f => totalEarnings += parseFloat(data[f] || 0));
        
        document.getElementById('gross_badge').textContent = `Gross: ${formatter.format(totalEarnings)}`;
        document.getElementById('result_gross').textContent = formatter.format(totalEarnings);
        
        // Statutory (PF/ESI/etc) - Indian Standards
        const pfWage = Math.min(basic + daActual, 15000); // EPS capping
        const pfActualWage = basic + daActual;
        
        const pfEmp = pfActualWage * 0.12;
        const eps = pfWage * 0.0833;
        const epfEmpr = pfEmp - eps;
        
        document.getElementById('pf_emp_display').textContent = formatter.format(pfEmp);
        document.getElementById('pf_empr_display').textContent = formatter.format(pfEmp);
        document.getElementById('eps_display').textContent = formatter.format(eps);
        document.getElementById('epf_balance_display').textContent = formatter.format(epfEmpr);
        
        const esiEmp = (totalEarnings <= 21000) ? Math.ceil(totalEarnings * 0.0075) : 0;
        const esiEmpr = (totalEarnings <= 21000) ? Math.ceil(totalEarnings * 0.0325) : 0;
        
        document.getElementById('esi_emp_display').textContent = formatter.format(esiEmp);
        document.getElementById('esi_empr_display').textContent = formatter.format(esiEmpr);
        
        const gratuity = basic * 0.0481;
        const bonus = basic * 0.0833;
        document.getElementById('gratuity_display').textContent = formatter.format(gratuity);
        document.getElementById('bonus_display').textContent = formatter.format(bonus);
        
        // TDS (Mock logic - simplified slab)
        let taxableAnnual = totalEarnings * 12;
        let tds = (taxableAnnual > 500000) ? (taxableAnnual - 500000) * 0.1 / 12 : 0;
        document.getElementById('tds_display').textContent = formatter.format(tds);
        
        // Attendance
        const workingDays = parseInt(data.total_working_days || 26);
        const presentDays = parseInt(data.present_days || 0);
        const lopDays = parseFloat(data.lop_days || 0);
        const perDay = totalEarnings / workingDays;
        const lopDeduction = lopDays * perDay;
        
        document.getElementById('paid_days').value = workingDays - lopDays;
        document.getElementById('per_day_display').textContent = formatter.format(perDay);
        document.getElementById('lop_deduction_display').textContent = formatter.format(lopDeduction);
        
        // Totals
        const otherDeductions = parseFloat(data.professional_tax || 0) + parseFloat(data.lwf_employee || 0) +
                                parseFloat(data.advance_recovery || 0) + parseFloat(data.loan_emi || 0) + tds + pfEmp + esiEmp;
        
        const totalDeductions = otherDeductions + lopDeduction;
        const netSalary = totalEarnings - totalDeductions;
        const emprTotal = pfEmp + esiEmpr + gratuity + bonus + parseFloat(data.lwf_employer || 0);
        const monthlyCTC = totalEarnings + emprTotal;
        
        document.getElementById('result_deductions').textContent = formatter.format(totalDeductions);
        document.getElementById('deductions_badge').textContent = `Deductions: ${formatter.format(totalDeductions)}`;
        document.getElementById('result_net').textContent = formatter.format(netSalary);
        document.getElementById('sticky_net').textContent = formatter.format(netSalary);
        document.getElementById('result_empr').textContent = formatter.format(emprTotal);
        document.getElementById('result_monthly_ctc').textContent = formatter.format(monthlyCTC);
        document.getElementById('result_annual_ctc').textContent = formatter.format(monthlyCTC * 12);
        document.getElementById('sticky_ctc').textContent = formatter.format(monthlyCTC * 12);
        document.getElementById('total_ctc_hidden').value = monthlyCTC * 12; // Added for DB persistence
        document.getElementById('result_annual_taxable').textContent = formatter.format(taxableAnnual);
    }

    document.querySelectorAll('.calc-trigger').forEach(input => {
        input.addEventListener('input', calculateAll);
    });

    function loadEmployeeData(id) {
        if (!id) return;
        const option = document.querySelector(`#employee_select option[value="${id}"]`);
        const data = JSON.parse(option.dataset.structure);
        
        // Map data to fields
        for (const [key, value] of Object.entries(data)) {
            const field = document.getElementsByName(key)[0];
            if (field) field.value = value || 0;
        }
        calculateAll();
    }

    function resetCalculator() {
        document.getElementById('payroll-calculator-form').reset();
        document.getElementById('employee_select').value = "";
        calculateAll();
    }

    function saveDraft() {
        const formData = new FormData(document.getElementById('payroll-calculator-form'));
        const employeeId = document.getElementById('employee_select').value;
        if (!employeeId) {
            alert("Please select an employee first.");
            return;
        }
        formData.append('user_id', employeeId);

        fetch('payroll-manage?action=saveDraft', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Failed to save draft. Check console.");
        });
    }

    function exportCSV() {
        window.location.href = 'payroll-manage?action=exportCSV';
    }

    function exportPDF() {
        // High-fidelity print view
        window.print();
    }
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
