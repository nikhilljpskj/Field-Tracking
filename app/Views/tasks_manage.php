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

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
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
                                    <?php if(empty($tasks)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fe fe-info fe-24 mb-2 d-block"></i>
                                                No visits assigned to your team yet.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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
                                            <span class="badge <?php echo $priorityClass; ?> text-white"><?php echo $t['priority']; ?></span>
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
                                            <a href="tasks?action=delete&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Remove this visit assignment?')">
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
                
                <h4 class="mt-5 mb-3 text-dark">In-House Task Delegation</h4>
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Assignee</th>
                                        <th>Task Definition</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th class="text-right pr-4">Attachment / View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($inhouseTasks)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No in-house tasks delegated.</td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php foreach($inhouseTasks as $ih): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-600"><?php echo htmlspecialchars($ih['assignee_name']); ?></td>
                                        <td>
                                            <div class="d-flex flex-column" style="max-width:300px;">
                                                <span class="text-dark font-weight-bold"><?php echo htmlspecialchars($ih['task_name']); ?></span>
                                                <small class="text-muted text-truncate"><?php echo htmlspecialchars($ih['requirements']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="<?php echo (strtotime($ih['deadline']) < time() && $ih['status'] != 'Completed') ? 'text-danger font-weight-bold' : ''; ?>">
                                                <?php echo date('M d, Y H:i', strtotime($ih['deadline'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $bh = 'badge-secondary';
                                            if($ih['status'] == 'Accepted') $bh = 'badge-primary';
                                            if($ih['status'] == 'Completed') $bh = 'badge-success';
                                            if($ih['status'] == 'Overdue') $bh = 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $bh; ?> px-2 py-1"><?php echo $ih['status']; ?></span>
                                        </td>
                                        <td class="text-right pr-4">
                                            <div class="btn-group shadow-sm">
                                                <button class="btn btn-sm btn-light border font-weight-bold" onclick="openViewEditInhouseModal(htmlspecialchars_decode('<?php echo htmlspecialchars(json_encode($ih), ENT_QUOTES); ?>'))">Details</button>
                                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                                    <a href="tasks?action=deleteInhouse&id=<?php echo $ih['id']; ?>" class="btn btn-sm btn-danger text-white" onclick="return confirm('Permanently delete this task and its associated files?');" title="Delete Event">
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

function openViewEditInhouseModal(jsonStr) {
    try {
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
