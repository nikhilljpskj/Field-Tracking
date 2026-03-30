<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Work & Visit Assignments</h2>
                        <p class="text-muted">Direct executives to specific hospitals, or assign internal jobs.</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary shadow-sm mr-2" data-toggle="modal" data-target="#assignInhouseModal">
                            <i class="fe fe-briefcase mr-1"></i> Assign In-House Task
                        </button>
                        <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#assignTaskModal">
                            <i class="fe fe-plus-circle mr-1"></i> Assign New Visit
                        </button>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="card-title mb-0 font-weight-bold text-primary"><i class="fe fe-map-pin mr-2"></i>Site Visit Assignments</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 datatables" id="visitTable">
                                        <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                            <tr>
                                                <th class="pl-4">Executive</th>
                                                <th>Hospital / Office</th>
                                                <th>Visit Date</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th class="text-right pr-4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($tasks as $t): ?>
                                            <tr>
                                                <td class="pl-4 font-weight-600"><?php echo htmlspecialchars($t['executive_name']); ?></td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-dark font-weight-500"><?php echo htmlspecialchars($t['hospital_office_name']); ?></span>
                                                        <small class="text-muted"><?php echo htmlspecialchars($t['location_desc']); ?></small>
                                                    </div>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($t['visit_date'])); ?></td>
                                                <td>
                                                    <?php 
                                                    $priorityClass = 'bg-secondary';
                                                    if($t['priority'] == 'High') $priorityClass = 'bg-danger';
                                                    if($t['priority'] == 'Medium') $priorityClass = 'bg-warning';
                                                    ?>
                                                    <span class="badge <?php echo $priorityClass; ?> text-white px-2 py-1"><?php echo $t['priority']; ?></span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $statusClass = 'text-muted';
                                                    if($t['status'] == 'Completed') $statusClass = 'text-success font-weight-bold';
                                                    if($t['status'] == 'In Progress') $statusClass = 'text-primary font-weight-bold';
                                                    if($t['status'] == 'Cancelled') $statusClass = 'text-danger';
                                                    ?>
                                                    <span class="<?php echo $statusClass; ?>">
                                                        <i class="fe fe-circle fe-10 mr-1"></i> <?php echo $t['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="text-right pr-4">
                                                    <a href="tasks?action=delete&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm border" onclick="return confirm('Remove this visit assignment?')" title="Delete Visit">
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
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="card-title mb-0 font-weight-bold text-dark"><i class="fe fe-briefcase mr-2"></i>In-House Task Delegation</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 datatables" id="inhouseTable">
                                        <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                            <tr>
                                                <th class="pl-4">Assignee</th>
                                                <th>Task Definition</th>
                                                <th>Deadline</th>
                                                <th>Status</th>
                                                <th class="text-right pr-4">Oversight</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($inhouseTasks as $ih): ?>
                                            <tr>
                                                <td class="pl-4 font-weight-600 font-weight-bold"><?php echo htmlspecialchars($ih['assignee_name']); ?></td>
                                                <td>
                                                    <div class="d-flex flex-column" style="max-width:300px;">
                                                        <span class="text-dark font-weight-bold"><?php echo htmlspecialchars($ih['task_name']); ?></span>
                                                        <small class="text-muted text-truncate"><?php echo htmlspecialchars($ih['requirements']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="<?php echo (strtotime($ih['deadline']) < time() && !in_array($ih['status'], ['Completed', 'Pending Approval'])) ? 'text-danger font-weight-bold' : ''; ?>">
                                                        <?php echo date('M d, Y H:i', strtotime($ih['deadline'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $bh = 'badge-secondary';
                                                    if($ih['status'] == 'Accepted') $bh = 'badge-primary';
                                                    if($ih['status'] == 'Partial Submitted') $bh = 'badge-info text-white';
                                                    if($ih['status'] == 'Pending Approval') $bh = 'badge-warning text-dark';
                                                    if($ih['status'] == 'Revision Requested') $bh = 'badge-danger text-white';
                                                    if($ih['status'] == 'Completed') $bh = 'badge-success';
                                                    if($ih['status'] == 'Overdue') $bh = 'badge-danger';
                                                    ?>
                                                    <span class="badge <?php echo $bh; ?> px-2 py-1"><?php echo $ih['status']; ?></span>
                                                </td>
                                                <td class="text-right pr-4">
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-white border shadow-sm text-primary" data-task="<?php echo htmlspecialchars(json_encode($ih), ENT_QUOTES, 'UTF-8'); ?>" onclick="openViewEditInhouseModal(this)" title="View/Edit Details">
                                                            <i class="fe fe-eye"></i>
                                                        </button>
                                                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                                            <a href="tasks?action=deleteInhouse&id=<?php echo $ih['id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm border ml-1" onclick="return confirm('Permanently delete this task and its associated files?');" title="Delete Task">
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
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold" id="assignTaskModalLabel">Assign New Visit</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="tasks?action=create" method="POST">
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Select Executive</label>
                                <select name="assigned_to" class="form-control form-control-lg bg-light border-0" required>
                                    <option value="">-- Choose Member --</option>
                                    <?php foreach($team as $member): ?>
                                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?> (<?php echo $member['email']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Hospital / Office Name</label>
                                <input type="text" name="hospital_name" class="form-control form-control-lg bg-light border-0" placeholder="e.g. City General Hospital" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label class="font-weight-600">Exact Location / Address</label>
                                <input type="text" name="location" class="form-control form-control-lg bg-light border-0" placeholder="Street, Area, etc." required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Visit Date</label>
                                <input type="date" name="visit_date" class="form-control form-control-lg bg-light border-0" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Priority Level</label>
                                <select name="priority" class="form-control form-control-lg bg-light border-0">
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-600">Target Description / Specific Goal</label>
                            <textarea name="target" class="form-control form-control-lg bg-light border-0" rows="2" placeholder="What should be achieved during this visit?"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-600">Additional Internal Notes</label>
                            <textarea name="notes" class="form-control form-control-lg bg-light border-0" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-light px-4" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">Assign Visit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Inhouse Assignment Modal -->
    <div class="modal fade" id="assignInhouseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="tasks?action=createInhouse" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title font-weight-bold"><i class="fe fe-briefcase mr-2"></i>Delegate Internal Task</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold text-muted small text-uppercase">Assign To (Employee)</label>
                        <select name="assigned_to" class="form-control" required>
                            <?php foreach($team as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> <?php echo ($u['id'] == $_SESSION['user_id']) ? '(Self)' : ''; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-muted small text-uppercase">Task Definition</label>
                        <input type="text" name="task_name" class="form-control" required placeholder="e.g. Design Dashboard Prototypes">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-muted small text-uppercase">Comprehensive Requirements</label>
                        <textarea name="requirements" class="form-control" rows="3" required placeholder="List out all steps, expected outputs, etc."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-muted small text-uppercase">Strict Deadline</label>
                        <input type="datetime-local" name="deadline" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-muted small text-uppercase">Attach Brief/Spec PDF</label>
                        <input type="file" name="attachment" class="form-control-file" accept=".pdf,.doc,.docx,.png,.jpg">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-dark shadow-sm w-100"><i class="fe fe-send mr-1"></i> Formally Assign Work</button>
                </div>
            </form>
        </div>
    </div>

</main>

<style>
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }
</style>

<!-- View / Edit Detail Modal -->
<div class="modal fade" id="viewEditInhouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white border-bottom border-primary" style="border-bottom-width: 4px !important;">
                <h5 class="modal-title font-weight-bold"><i class="fe fe-cpu mr-2"></i>Task Intelligence & Operations</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
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
