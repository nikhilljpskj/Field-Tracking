<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Visit Assignments</h2>
                        <p class="text-muted">Direct executives to specific hospitals, offices, or clients.</p>
                    </div>
                    <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#assignTaskModal">
                        <i class="fe fe-plus-circle mr-1"></i> Assign New Visit
                    </button>
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
</main>

<style>
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }
</style>

<?php include 'layout/footer.php'; ?>
