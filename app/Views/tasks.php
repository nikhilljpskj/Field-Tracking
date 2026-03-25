<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">My Visit Schedule</h2>
                        <p class="text-muted">Managed visit assignments from your manager.</p>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
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

<?php include 'layout/footer.php'; ?>
