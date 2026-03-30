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
                                            <?php if($ih['completion_file_path']): ?>
                                                <a href="<?php echo $ih['completion_file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-success mr-1" title="View Submitted Work"><i class="fe fe-download"></i> Output</a>
                                            <?php endif; ?>
                                            <?php if($ih['attachment_path']): ?>
                                                <a href="<?php echo $ih['attachment_path']; ?>" target="_blank" class="btn btn-sm btn-light" title="View Brief"><i class="fe fe-paperclip"></i></a>
                                            <?php endif; ?>
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

<?php include 'layout/footer.php'; ?>
