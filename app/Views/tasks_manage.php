<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div class="mb-2 mb-sm-0">
                        <h2 class="h3 mb-0 page-title">Work &amp; Visit Assignments</h2>
                        <p class="text-muted small mb-0">Direct executives to hospitals, or assign internal jobs.</p>
                    </div>
                    <div class="tm-header-actions">
                        <button type="button" class="tm-btn tm-btn-secondary" data-toggle="modal" data-target="#assignInhouseModal">
                            <i class="fe fe-briefcase"></i><span>In-House Task</span>
                        </button>
                        <button type="button" class="tm-btn tm-btn-primary" data-toggle="modal" data-target="#assignTaskModal">
                            <i class="fe fe-plus-circle"></i><span>Assign Visit</span>
                        </button>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0 mb-5">
                            <div class="card-header bg-primary py-3" style="border-radius: 12px 12px 0 0;">
                                <h5 class="card-title mb-0 font-weight-bold text-white"><i class="fe fe-map-pin mr-2"></i>Site Visit Assignments</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 datatables ms-3" id="visitTable">
                                        <thead class="tm-thead">
                                            <tr>
                                                <th>Executive</th>
                                                <th>Hospital / Office</th>
                                                <th>Visit Date</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th class="text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($tasks as $t): ?>
                                            <tr class="tm-row">
                                                <td><span class="tm-name"><?php echo htmlspecialchars($t['executive_name']); ?></span></td>
                                                <td>
                                                    <div class="tm-hospital"><?php echo htmlspecialchars($t['hospital_office_name']); ?></div>
                                                    <div class="tm-location"><i class="fe fe-map-pin fe-10 mr-1"></i><?php echo htmlspecialchars($t['location_desc']); ?></div>
                                                </td>
                                                <td><span class="tm-date"><?php echo date('d M Y', strtotime($t['visit_date'])); ?></span></td>
                                                <td>
                                                    <?php
                                                    $pc = ['Low'=>'tm-chip-info','Medium'=>'tm-chip-warning','High'=>'tm-chip-danger'];
                                                    $pClass = $pc[$t['priority']] ?? 'tm-chip-info';
                                                    ?>
                                                    <span class="tm-chip <?php echo $pClass; ?>"><?php echo $t['priority']; ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $sc = ['Pending'=>'tm-stat-muted','Completed'=>'tm-stat-success','In Progress'=>'tm-stat-primary','Cancelled'=>'tm-stat-danger'];
                                                    $sClass = $sc[$t['status']] ?? 'tm-stat-muted';
                                                    ?>
                                                    <span class="tm-status <?php echo $sClass; ?>">
                                                        <span class="tm-status-dot"></span><?php echo $t['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <a href="tasks?action=delete&id=<?php echo $t['id']; ?>" class="tm-icon-btn tm-icon-danger" onclick="return confirm('Remove this visit assignment?')" title="Delete Visit">
                                                        <i class="fe fe-trash-2"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-5">
                            <div class="card-header bg-dark py-3" style="border-radius: 12px 12px 0 0;">
                                <h5 class="card-title mb-0 font-weight-bold text-white"><i class="fe fe-briefcase mr-2"></i>In-House Task Delegation</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 datatables ms-3" id="inhouseTable">
                                        <thead class="tm-thead">
                                            <tr>
                                                <th>Assignee</th>
                                                <th>Task Definition</th>
                                                <th>Deadline</th>
                                                <th>Status</th>
                                                <th class="text-right">Oversight</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($inhouseTasks as $ih): ?>
                                            <tr class="tm-row">
                                                <td><span class="tm-name"><?php echo htmlspecialchars($ih['assignee_name']); ?></span></td>
                                                <td>
                                                    <div class="tm-hospital"><?php echo htmlspecialchars($ih['task_name']); ?></div>
                                                    <div class="tm-location"><?php echo htmlspecialchars(mb_strimwidth($ih['requirements'], 0, 80, '...')); ?></div>
                                                </td>
                                                <td>
                                                    <?php $isOvd = strtotime($ih['deadline']) < time() && !in_array($ih['status'], ['Completed', 'Pending Approval']); ?>
                                                    <span class="tm-date <?php echo $isOvd ? 'tm-date-overdue' : ''; ?>">
                                                        <?php echo date('d M Y', strtotime($ih['deadline'])); ?>
                                                        <?php if($isOvd): ?><span class="tm-overdue-chip">OVERDUE</span><?php endif; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $bmap = [
                                                        'Pending'           => 'tm-chip-muted',
                                                        'Accepted'          => 'tm-chip-primary',
                                                        'Partial Submitted' => 'tm-chip-info',
                                                        'Pending Approval'  => 'tm-chip-warning',
                                                        'Revision Requested'=> 'tm-chip-danger',
                                                        'Completed'         => 'tm-chip-success',
                                                        'Overdue'           => 'tm-chip-danger',
                                                    ];
                                                    $bClass = $bmap[$ih['status']] ?? 'tm-chip-muted';
                                                    ?>
                                                    <span class="tm-chip <?php echo $bClass; ?>"><?php echo $ih['status']; ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <div class="d-flex justify-content-end" style="gap:6px;">
                                                        <button class="tm-icon-btn tm-icon-primary" data-task="<?php echo htmlspecialchars(json_encode($ih), ENT_QUOTES, 'UTF-8'); ?>" onclick="openViewEditInhouseModal(this)" title="View/Edit Details">
                                                            <i class="fe fe-eye"></i>
                                                        </button>
                                                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                                            <a href="tasks?action=deleteInhouse&id=<?php echo $ih['id']; ?>" class="tm-icon-btn tm-icon-danger" onclick="return confirm('Permanently delete this task and its associated files?');" title="Delete Task">
                                                                <i class="fe fe-trash-2"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Modal -->
    <div class="modal fade" id="assignTaskModal" tabindex="-1" role="dialog" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assignTaskModalLabel"><i class="fe fe-map-pin mr-2"></i>Assign New Visit</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="tasks?action=create" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Select Executive</label>
                                <select name="assigned_to" class="form-control" required>
                                    <option value="">-- Choose Member --</option>
                                    <?php foreach($team as $member): ?>
                                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?> (<?php echo $member['email']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Hospital / Office Name</label>
                                <input type="text" name="hospital_name" class="form-control" placeholder="e.g. City General Hospital" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Exact Location / Address</label>
                                <input type="text" name="location" class="form-control" placeholder="Street, Area, etc." required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Visit Date</label>
                                <input type="date" name="visit_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Priority Level</label>
                                <select name="priority" class="form-control">
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Target Description / Specific Goal</label>
                            <textarea name="target" class="form-control" rows="2" placeholder="What should be achieved during this visit?"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Additional Internal Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">Assign Visit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Inhouse Assignment Modal -->
    <div class="modal fade" id="assignInhouseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="tasks?action=createInhouse" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fe fe-briefcase mr-2"></i>Delegate Internal Task</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <div class="form-group">
                        <label>Assign To (Employee)</label>
                        <select name="assigned_to" class="form-control" required>
                            <?php foreach($team as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> <?php echo ($u['id'] == $_SESSION['user_id']) ? '(Self)' : ''; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Task Definition</label>
                        <input type="text" name="task_name" class="form-control" required placeholder="e.g. Design Dashboard Prototypes">
                    </div>
                    <div class="form-group">
                        <label>Comprehensive Requirements</label>
                        <textarea name="requirements" class="form-control" rows="3" required placeholder="List out all steps, expected outputs, etc."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Strict Deadline</label>
                        <input type="datetime-local" name="deadline" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>Attach Brief/Spec PDF</label>
                        <input type="file" name="attachment" class="form-control-file" accept=".pdf,.doc,.docx,.png,.jpg">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary shadow-sm w-100"><i class="fe fe-send mr-1"></i> Formally Assign Work</button>
                </div>
            </form>
        </div>
    </div>

</main>

<style>
/* ── Token variables ── */
:root {
    --tm-primary: #4361ee;
    --tm-success: #10b981;
    --tm-danger:  #dc3545;
    --tm-warning: #f59e0b;
    --tm-info:    #17a2b8;
    --tm-muted:   #6c757d;
    --tm-border:  #e8edf5;
    --tm-bg:      #f8fafd;
    --tm-radius:  10px;
}

/* ── Header Actions ── */
.tm-header-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}
.tm-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 10px;
    padding: 8px 16px;
    font-size: 0.82rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: opacity 0.15s, transform 0.1s;
    white-space: nowrap;
}
.tm-btn:hover { opacity: 0.88; transform: scale(1.02); }
.tm-btn-primary  { background: var(--tm-primary); color: #fff; }
.tm-btn-secondary{ background: #f1f3ff; color: var(--tm-primary); border: 1.5px solid rgba(67,97,238,0.25); }
@media (max-width: 576px) {
    .tm-header-actions { width: 100%; margin-top: 10px; }
    .tm-btn { flex: 1; justify-content: center; font-size: 0.78rem; padding: 8px 10px; }
}

/* ── Table Overrides ── */
.tm-thead th {
    padding: 24px 32px !important;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #334155;
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0 !important;
    white-space: nowrap;
}
.tm-row td {
    padding: 28px 32px !important;
    vertical-align: middle;
    border-color: #f1f5f9 !important;
    background: #fff;
}
.tm-row:hover td { background: #f8faff !important; }
.tm-row:hover { transform: scale(1.005); box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
.tm-row { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }

.tm-name {
    font-weight: 700;
    font-size: 0.88rem;
    color: #1e293b;
}
.tm-hospital {
    font-weight: 600;
    font-size: 0.85rem;
    color: #1e293b;
    margin-bottom: 2px;
}
.tm-location {
    font-size: 0.72rem;
    color: #64748b;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tm-date {
    font-size: 0.8rem;
    font-weight: 600;
    color: #334155;
}
.tm-date-overdue { color: var(--tm-danger) !important; }
.tm-overdue-chip {
    display: inline-block;
    background: rgba(220,53,69,0.1);
    color: var(--tm-danger);
    font-size: 0.58rem;
    font-weight: 800;
    border-radius: 4px;
    padding: 1px 5px;
    margin-left: 5px;
    letter-spacing: 0.06em;
}

/* ── Status & Priority Chips ── */
.tm-chip {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    white-space: nowrap;
}
.tm-chip-primary { background: rgba(67,97,238,0.1);   color: var(--tm-primary); }
.tm-chip-success { background: rgba(16,185,129,0.1);  color: var(--tm-success); }
.tm-chip-danger  { background: rgba(220,53,69,0.1);   color: var(--tm-danger);  }
.tm-chip-warning { background: rgba(245,158,11,0.1);  color: var(--tm-warning); }
.tm-chip-info    { background: rgba(23,162,184,0.1);  color: var(--tm-info);    }
.tm-chip-muted   { background: rgba(108,117,125,0.1); color: var(--tm-muted);   }

.tm-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}
.tm-status-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
    background: currentColor;
}
.tm-stat-success { color: var(--tm-success); }
.tm-stat-primary { color: var(--tm-primary); }
.tm-stat-danger  { color: var(--tm-danger);  }
.tm-stat-muted   { color: var(--tm-muted);   }

/* ── Icon Action Buttons ── */
.tm-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px; height: 32px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 0.85rem;
    transition: background 0.15s, transform 0.1s;
    text-decoration: none;
}
.tm-icon-btn:hover { transform: scale(1.1); text-decoration: none; }
.tm-icon-primary { background: rgba(67,97,238,0.1);  color: var(--tm-primary); }
.tm-icon-primary:hover { background: rgba(67,97,238,0.2); }
.tm-icon-danger  { background: rgba(220,53,69,0.1);  color: var(--tm-danger); }
.tm-icon-danger:hover  { background: rgba(220,53,69,0.2); }

/* ── Legacy compat ── */
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }
</style>

<!-- View / Edit Detail Modal -->
<div class="modal fade" id="viewEditInhouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fe fe-cpu mr-2"></i>Task Intelligence & Operations</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-uppercase text-muted font-weight-bold">Assigned By</small>
                        <div id="ve_assigned_by" class="font-weight-600 text-dark"></div>
                    </div>
                    <div class="col-md-6 text-md-right mt-2 mt-md-0">
                        <span id="ve_status_badge" class="badge px-3 py-1 font-weight-bold text-lg"></span>
                    </div>
                </div>

                <!-- Full History Timeline -->
                <div class="bg-white rounded shadow-sm mb-4 border border-light overflow-hidden">
                    <div class="px-3 pt-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                        <small class="text-uppercase text-muted font-weight-bold"><i class="fe fe-clock mr-1"></i>Full Task History &amp; Audit Trail</small>
                        <span id="ve_history_count" class="badge badge-secondary">0 events</span>
                    </div>
                    <div id="ve_history_timeline" style="max-height: 320px; overflow-y: auto;" class="px-3 pt-3 pb-2">
                        <div class="text-center text-muted py-4 small"><i class="fe fe-loader"></i> Loading history...</div>
                    </div>
                    <div class="px-3 pb-3" id="ve_attachments"></div>
                </div>

                <hr>
                
                <div id="ve_admin_controls" style="display:none;" class="bg-white p-3 border border-warning rounded rounded-lg mb-4 shadow-sm">
                    <h6 class="font-weight-bold text-warning mb-2"><i class="fe fe-shield mr-1"></i> Leadership Review Required</h6>
                    <p class="small text-muted mb-3">This task has been submitted by the assignee. Please review the output attached above and either formally Approve it to complete the cycle, or Request Revisions to send it back.</p>
                    
                    <form action="tasks?action=updateInhouse" method="POST" id="ve_review_form">
                        <input type="hidden" name="task_id" id="ve_review_task_id">
                        <div class="form-group">
                            <label class="small font-weight-bold text-dark text-uppercase">Manager Feedback / Revision Notes</label>
                            <textarea name="manager_feedback" class="form-control" rows="3" placeholder="If requesting revisions, detail exactly what needs fixing..."></textarea>
                        </div>
                        <div class="d-flex w-100">
                            <button type="submit" name="action" value="revision" class="btn btn-warning shadow-sm flex-fill mr-2 font-weight-bold" onclick="return confirm('Send task back to user for further revisions?');"><i class="fe fe-corner-up-left mr-1"></i> Request Revision</button>
                            <button type="submit" name="action" value="approve" class="btn btn-success text-white shadow-sm flex-fill ml-2 font-weight-bold" onclick="return confirm('You are formally approving this task. Continue?');"><i class="fe fe-check-circle mr-1"></i> Approve & Close</button>
                        </div>
                    </form>
                </div>

                <h6 class="font-weight-bold text-dark mb-3"><i class="fe fe-edit-3 mr-2"></i>Administrative Task Control</h6>
                <form action="tasks?action=editInhouse" method="POST">
                    <input type="hidden" name="task_id" id="ve_task_id">
                    <div class="form-group">
                        <label class="small text-uppercase font-weight-bold text-muted">Task Name</label>
                        <input type="text" name="task_name" id="ve_task_name" class="form-control font-weight-bold" required>
                    </div>
                    <div class="form-group">
                        <label class="small text-uppercase font-weight-bold text-muted">Core Requirements</label>
                        <textarea name="requirements" id="ve_requirements" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="small text-uppercase font-weight-bold text-muted">Deadline</label>
                            <input type="datetime-local" name="deadline" id="ve_deadline" class="form-control" required>
                        </div>
                    </div>
                    <div class="text-right mt-4">
                        <button type="button" class="btn btn-white shadow-sm mr-2" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary shadow-sm"><i class="fe fe-save mr-1"></i> Update Properties</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function htmlspecialchars_decode(str) {
    var map = { '&amp;': '&', '&#039;': "'", '&quot;': '"', '&lt;': '<', '&gt;': '>' };
    return str.replace(/&amp;|&#039;|&quot;|&lt;|&gt;/g, function(m) { return map[m]; });
}

function openViewEditInhouseModal(btn) {
    try {
        let jsonStr = btn.getAttribute('data-task');
        let task = JSON.parse(jsonStr);
        document.getElementById('ve_task_id').value = task.id;
        document.getElementById('ve_task_name').value = task.task_name;
        document.getElementById('ve_requirements').value = task.requirements;
        
        let d = new Date(task.deadline);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        document.getElementById('ve_deadline').value = d.toISOString().slice(0, 16);
        
        document.getElementById('ve_assigned_by').innerText = task.assigner_name || 'System';
        
        let statusBadge = document.getElementById('ve_status_badge');
        statusBadge.innerText = task.status;
        statusBadge.className = 'badge px-3 py-1 font-weight-bold ';
        if(task.status == 'Pending') statusBadge.classList.add('badge-secondary');
        if(task.status == 'Accepted') statusBadge.classList.add('badge-primary');
        if(task.status == 'Completed') statusBadge.classList.add('badge-success');
        if(task.status == 'Overdue') statusBadge.classList.add('badge-danger', 'text-white');
        if(task.status == 'Partial Submitted') statusBadge.classList.add('badge-info', 'text-white');
        if(task.status == 'Pending Approval') statusBadge.classList.add('badge-warning', 'text-dark');
        if(task.status == 'Revision Requested') statusBadge.classList.add('badge-danger', 'text-white');
        
        // ---- Full History Timeline ----
        const historyEl = document.getElementById('ve_history_timeline');
        const historyCount = document.getElementById('ve_history_count');
        const history = task.history || [];

        const actionConfig = {
            'Task Created':        { icon: 'fe-plus-circle',    color: '#6f42c1', label: 'Task Created',         bg: '#f3eeff' },
            'Accepted':            { icon: 'fe-check',           color: '#007bff', label: 'Accepted',             bg: '#e8f4ff' },
            'Submitted':           { icon: 'fe-upload-cloud',    color: '#17a2b8', label: 'Submitted',            bg: '#e8f9fc' },
            'Approved':            { icon: 'fe-check-circle',    color: '#28a745', label: 'Approved & Closed',    bg: '#eaffef' },
            'Revision Requested':  { icon: 'fe-corner-up-left', color: '#dc3545', label: 'Revision Requested',   bg: '#fff1f1' },
        };

        if (history.length === 0) {
            historyEl.innerHTML = `<div class="text-center text-muted small py-4"><i class="fe fe-inbox" style="font-size:1.5rem"></i><br>No recorded history yet. Actions on this task will appear here.</div>`;
            historyCount.textContent = '0 events';
        } else {
            historyCount.textContent = history.length + ' event' + (history.length > 1 ? 's' : '');
            let html = '<div style="position:relative; padding-left: 20px; border-left: 2px solid #e0e0e0;">';
            history.forEach(function(ev, idx) {
                const cfg = actionConfig[ev.action] || { icon: 'fe-activity', color: '#6c757d', label: ev.action, bg: '#f8f9fa' };
                const isLast = idx === history.length - 1;
                const dateStr = ev.created_at ? new Date(ev.created_at).toLocaleString('en-IN', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '';
                html += `
                <div style="margin-bottom: ${isLast ? '4px' : '18px'}; position: relative;">
                    <div style="position:absolute; left:-28px; top:2px; width:18px; height:18px; border-radius:50%; background:${cfg.bg}; border:2px solid ${cfg.color}; display:flex; align-items:center; justify-content:center;">
                        <i class="fe ${cfg.icon}" style="font-size:9px; color:${cfg.color};"></i>
                    </div>
                    <div style="background:${cfg.bg}; border-left: 3px solid ${cfg.color}; border-radius: 4px; padding: 8px 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong style="color:${cfg.color}; font-size:0.78rem; text-transform:uppercase; letter-spacing:0.5px;">${cfg.label}</strong>
                            <span class="text-muted" style="font-size:0.72rem;">${dateStr}</span>
                        </div>
                        <div class="font-weight-bold text-dark" style="font-size:0.82rem;">${ev.user_name || 'System'}</div>
                        ${ev.message ? `<div class="text-muted mt-1" style="font-size:0.8rem; white-space:pre-wrap;">${ev.message}</div>` : ''}
                    </div>
                </div>`;
            });
            html += '</div>';
            historyEl.innerHTML = html;
            historyEl.scrollTop = historyEl.scrollHeight;
        }
        
        let attachments = '';
        if(task.attachment_path) {
            attachments += `<a href="${task.attachment_path}" target="_blank" class="btn btn-sm btn-outline-secondary mr-2"><i class="fe fe-paperclip mr-1"></i> Original Brief</a>`;
        }
        if(task.completion_file_path) {
            attachments += `<a href="${task.completion_file_path}" target="_blank" class="btn btn-sm btn-success text-white"><i class="fe fe-download mr-1"></i> Download Deliverable</a>`;
        }
        document.getElementById('ve_attachments').innerHTML = attachments;

        let adminControls = document.getElementById('ve_admin_controls');
        if (adminControls) {
            if (task.status === 'Pending Approval' || task.status === 'Partial Submitted') {
                adminControls.style.display = 'block';
                document.getElementById('ve_review_task_id').value = task.id;
            } else {
                adminControls.style.display = 'none';
            }
        }

        $('#viewEditInhouseModal').modal('show');
    } catch(e) {
        console.error("Parse error: ", e);
    }
}
</script>

<link rel="stylesheet" href="css/dataTables.bootstrap4.css">
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#visitTable')) {
            $('#visitTable').DataTable({
                "pageLength": 25,
                "order": [[ 2, "desc" ]],
                "language": { "search": "", "searchPlaceholder": "Filter visits..." }
            });
        }
        if (!$.fn.DataTable.isDataTable('#inhouseTable')) {
            $('#inhouseTable').DataTable({
                "pageLength": 25,
                "order": [[ 2, "desc" ]],
                "language": { "search": "", "searchPlaceholder": "Filter in-house tasks..." }
            });
        }
        // Style enhancement for search inputs
        $('.dataTables_filter input').addClass('form-control form-control-sm border shadow-sm').css('width', '250px');
    });
</script>

<?php include 'layout/footer.php'; ?>
