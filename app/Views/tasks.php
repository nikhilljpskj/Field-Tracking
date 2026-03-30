<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">My Visit Schedule</h2>
                        <p class="text-muted">Managed visit assignments from your manager.</p>
                    </div>
                    <div>
                        <button class="btn btn-primary shadow-sm font-weight-bold" data-toggle="modal" data-target="#assignInhouseModal">
                            <i class="fe fe-plus mr-1"></i> Assign In-House Task
                        </button>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>

                <?php if(!empty($overdueTasks)): ?>
                    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4">
                        <i class="fe fe-alert-triangle fe-24 mr-3"></i>
                        <div>
                            <h5 class="mb-0 text-danger font-weight-bold">CRITICAL: Overdue Tasks</h5>
                            <small>You have <?php echo count($overdueTasks); ?> in-house task(s) that have passed their deadline. Action required immediately.</small>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php if(empty($tasks)): ?>
                        <div class="col-12">
                            <div class="card shadow-sm border-0 text-center py-5">
                                <div class="card-body">
                                    <div class="avatar avatar-xl mx-auto mb-3">
                                        <span class="avatar-title rounded-circle bg-light text-muted">
                                            <i class="fe fe-clipboard fe-32"></i>
                                        </span>
                                    </div>
                                    <h5 class="font-weight-bold">No Visits Scheduled</h5>
                                    <p class="text-muted">You have no hospital or office visits assigned for today.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach($tasks as $t): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm border-0 h-100 task-card">
                                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-start">
                                        <div>
                                            <?php 
                                            $priorityClass = 'bg-soft-info text-info';
                                            if($t['priority'] == 'High') $priorityClass = 'bg-soft-danger text-danger';
                                            if($t['priority'] == 'Medium') $priorityClass = 'bg-soft-warning text-warning';
                                            ?>
                                            <span class="badge <?php echo $priorityClass; ?> px-2 py-1 small font-weight-bold">
                                                <?php echo $t['priority']; ?> Priority
                                            </span>
                                        </div>
                                        <small class="text-muted font-weight-500"><?php echo date('D, d M', strtotime($t['visit_date'])); ?></small>
                                    </div>
                                    <div class="card-body pt-0">
                                        <h4 class="mb-2 font-weight-bold"><?php echo htmlspecialchars($t['hospital_office_name']); ?></h4>
                                        <div class="d-flex align-items-center text-muted mb-3">
                                            <i class="fe fe-map-pin fe-12 mr-2"></i>
                                            <span class="small"><?php echo htmlspecialchars($t['location_desc']); ?></span>
                                        </div>
                                        
                                        <div class="bg-light rounded p-3 mb-3">
                                            <strong class="d-block small text-muted text-uppercase mb-1 tracking-wider">Target Objective</strong>
                                            <p class="mb-0 text-dark"><?php echo htmlspecialchars($t['target_desc']); ?></p>
                                        </div>
                                        
                                        <?php if($t['notes']): ?>
                                            <div class="mb-3">
                                                <small class="text-muted font-italic">"<?php echo htmlspecialchars($t['notes']); ?>"</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4 d-flex justify-content-between align-items-center">
                                        <?php 
                                        $statusBadge = 'badge-soft-warning text-warning';
                                        if($t['status'] == 'Completed') $statusBadge = 'badge-soft-success text-success';
                                        if($t['status'] == 'In Progress') $statusBadge = 'badge-soft-primary text-primary';
                                        if($t['status'] == 'Cancelled') $statusBadge = 'badge-soft-danger text-danger';
                                        ?>
                                        <div class="d-flex align-items-center">
                                            <span class="dot <?php 
                                                echo ($t['status'] == 'Completed') ? 'bg-success' : 
                                                     (($t['status'] == 'In Progress') ? 'bg-primary' : 'bg-warning'); 
                                            ?> mr-2"></span>
                                            <span class="font-weight-600 small"><?php echo $t['status']; ?></span>
                                        </div>
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle font-weight-600" type="button" data-toggle="dropdown">
                                                Update Status
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right shadow border-0">
                                                <form action="tasks?action=updateStatus" method="POST">
                                                    <input type="hidden" name="task_id" value="<?php echo $t['id']; ?>">
                                                    <button type="submit" name="status" value="In Progress" class="dropdown-item py-2">Set In Progress</button>
                                                    <button type="submit" name="status" value="Completed" class="dropdown-item py-2 text-success font-weight-bold">Mark Completed</button>
                                                    <div class="dropdown-divider"></div>
                                                    <button type="submit" name="status" value="Cancelled" class="dropdown-item py-2 text-danger">Cancel Visit</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- INHOUSE TASKS SECTION -->
                <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
                    <div>
                        <h2 class="h4 mb-0 text-dark">In-House Tasks & Assignments</h2>
                        <p class="text-muted small">Collaborative work, system assignments, and specific operational duties.</p>
                    </div>
                </div>

                <div class="row">
                    <?php if(empty($inhouseTasks)): ?>
                        <div class="col-12">
                            <div class="card shadow-sm border-0 text-center py-5 mb-5">
                                <div class="card-body">
                                    <h6 class="text-muted mb-0">No active in-house assignments.</h6>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach($inhouseTasks as $ih): ?>
                            <div class="col-md-12 mb-3">
                                <div class="card shadow-sm border-0 <?php echo (strtotime($ih['deadline']) < time() && $ih['status'] != 'Completed') ? 'border-left border-danger border-4' : ''; ?>">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <h5 class="mb-1 font-weight-bold"><?php echo htmlspecialchars($ih['task_name']); ?></h5>
                                                <div class="small text-muted">Assigned By: <span class="text-dark font-weight-bold"><?php echo htmlspecialchars($ih['assigner_name']); ?></span></div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="bg-light p-2 rounded small text-dark mb-0" style="max-height: 80px; overflow-y: auto;">
                                                    <?php echo nl2br(htmlspecialchars($ih['requirements'])); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <div class="small text-uppercase text-muted font-weight-bold mb-1">Deadline</div>
                                                <div class="<?php echo (strtotime($ih['deadline']) < time() && $ih['status'] != 'Completed') ? 'text-danger font-weight-bold' : 'text-dark'; ?>">
                                                    <?php echo date('M d, Y', strtotime($ih['deadline'])); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mt-3 mt-md-0 text-md-right text-center">
                                                <div class="btn-group w-100 mb-2 shadow-sm">
                                                    <?php if($ih['status'] == 'Pending'): ?>
                                                        <button class="btn btn-sm btn-primary" onclick="openAcceptModal(<?php echo $ih['id']; ?>)">Accept</button>
                                                    <?php elseif($ih['status'] == 'Accepted' || $ih['status'] == 'Overdue'): ?>
                                                        <button class="btn btn-sm btn-success text-white" onclick="openCompleteModal(<?php echo $ih['id']; ?>)">Complete</button>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-light text-success" disabled><i class="fe fe-check-circle"></i></button>
                                                    <?php endif; ?>
                                                    
                                                    <button class="btn btn-sm btn-light border" data-task="<?php echo htmlspecialchars(json_encode($ih), ENT_QUOTES, 'UTF-8'); ?>" onclick="openViewEditInhouseModal(this)">Details</button>
                                                    
                                                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                                        <a href="tasks?action=deleteInhouse&id=<?php echo $ih['id']; ?>" class="btn btn-sm btn-danger text-white" onclick="return confirm('Permanently delete this task?');"><i class="fe fe-trash-2"></i></a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</main>

<style>
.bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); }
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }

.badge-soft-primary { background-color: rgba(67, 97, 238, 0.12); }
.badge-soft-success { background-color: rgba(40, 167, 69, 0.12); }
.badge-soft-warning { background-color: rgba(255, 193, 7, 0.12); }
.badge-soft-danger { background-color: rgba(220, 53, 69, 0.12); }

.dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; }
.tracking-wider { letter-spacing: 0.05em; }
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }

.task-card { transition: transform 0.2s; }
.task-card:hover { transform: translateY(-3px); }
</style>

<!-- MODALS -->
<div class="modal fade" id="assignInhouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="tasks?action=createInhouse" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fe fe-briefcase mr-2"></i>Assign In-House Task</h5>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold text-muted small text-uppercase">Assign To</label>
                    <select name="assigned_to" class="form-control" required>
                        <?php foreach($team as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> <?php echo ($u['id'] == $_SESSION['user_id']) ? '(Self)' : ''; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-muted small text-uppercase">Task Name / Title</label>
                    <input type="text" name="task_name" class="form-control" required placeholder="e.g. Develop UI Dashboard">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-muted small text-uppercase">Requirements & Details</label>
                    <textarea name="requirements" class="form-control" rows="3" required placeholder="Specify exactly what needs to be done..."></textarea>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-muted small text-uppercase">Deadline</label>
                    <input type="datetime-local" name="deadline" class="form-control" required>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold text-muted small text-uppercase">Optional Brief/Spec (PDF/DOC)</label>
                    <input type="file" name="attachment" class="form-control-file" accept=".pdf,.doc,.docx,.png,.jpg">
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-white shadow-sm" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fe fe-send mr-1"></i> Dispatch Task</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="acceptModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="tasks?action=updateInhouse" method="POST" class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <h5 class="font-weight-bold mb-3">Accept Task Assignment</h5>
                <input type="hidden" name="action" value="accept">
                <input type="hidden" name="task_id" id="accept_task_id">
                <div class="form-group mb-0">
                    <label class="small text-muted font-weight-bold text-uppercase">Acceptance Comment (Optional)</label>
                    <textarea name="acceptance_comment" class="form-control" rows="2" placeholder="I have reviewed and will begin working on this..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="submit" class="btn btn-primary shadow-sm w-100">Confirm Acceptance</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="completeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="tasks?action=updateInhouse" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title font-weight-bold">Mark Task Completed</h5>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="action" value="complete">
                <input type="hidden" name="task_id" id="complete_task_id">
                <div class="form-group">
                    <label class="small text-muted font-weight-bold text-uppercase">Completion Details</label>
                    <textarea name="completion_details" class="form-control" rows="3" required placeholder="What was done? Code links, resolutions, etc."></textarea>
                </div>
                <div class="form-group">
                    <label class="small text-muted font-weight-bold text-uppercase">Deliverable File (Optional)</label>
                    <input type="file" name="completion_file" class="form-control-file">
                </div>
                <div class="form-group mb-0">
                    <label class="small text-muted font-weight-bold text-uppercase">Closing Comment</label>
                    <input type="text" name="completion_comment" class="form-control" placeholder="Any final thoughts for the assigner?">
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="submit" class="btn btn-success shadow-sm w-100 text-white font-weight-bold">Submit Completion</button>
            </div>
        </form>
    </div>
</div>

<!-- View / Edit Detail Modal -->
<div class="modal fade" id="viewEditInhouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fe fe-info mr-2"></i>Task Details & Edit</h5>
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

                <div class="bg-white p-3 rounded shadow-sm mb-4 border border-light">
                    <small class="text-uppercase text-muted font-weight-bold">Acceptance / Completion Audit</small>
                    <div class="mt-2 small" id="ve_audit_log"></div>
                    <div class="mt-3" id="ve_attachments"></div>
                </div>

                <hr>
                
                <h6 class="font-weight-bold text-dark mb-3"><i class="fe fe-edit-3 mr-2"></i>Edit Task Information</h6>
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
                        <button type="submit" class="btn btn-primary shadow-sm"><i class="fe fe-save mr-1"></i> Save Changes</button>
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

function openAcceptModal(id) {
    document.getElementById('accept_task_id').value = id;
    $('#acceptModal').modal('show');
}
function openCompleteModal(id) {
    document.getElementById('complete_task_id').value = id;
    $('#completeModal').modal('show');
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
        
        let audit = '';
        if(task.accepted_at) {
            audit += `<div class="mb-2"><strong class="text-primary">Accepted At:</strong> ${task.accepted_at} <br><span class="text-muted italic">"${task.acceptance_comment || ''}"</span></div>`;
        } else {
            audit += `<div class="text-muted italic">Not accepted yet.</div>`;
        }
        
        if(task.completed_at) {
            audit += `<hr class="my-2"><div class="mb-1"><strong class="text-success">Completed At:</strong> ${task.completed_at}</div>`;
            audit += `<div class="mb-1"><strong class="text-dark">Completion Log:</strong> ${task.completion_details || ''}</div>`;
            audit += `<div class="mb-1 text-muted italic">"${task.completion_comment || ''}"</div>`;
        }
        document.getElementById('ve_audit_log').innerHTML = audit;
        
        let attachments = '';
        if(task.attachment_path) {
            attachments += `<a href="${task.attachment_path}" target="_blank" class="btn btn-sm btn-outline-secondary mr-2"><i class="fe fe-paperclip mr-1"></i> Original Brief</a>`;
        }
        if(task.completion_file_path) {
            attachments += `<a href="${task.completion_file_path}" target="_blank" class="btn btn-sm btn-success text-white"><i class="fe fe-download mr-1"></i> Download Deliverable</a>`;
        }
        document.getElementById('ve_attachments').innerHTML = attachments;

        $('#viewEditInhouseModal').modal('show');
    } catch(e) {
        console.error("Parse error: ", e);
    }
}
</script>

<?php include 'layout/footer.php'; ?>
