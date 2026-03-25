<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Team Targets & Performance</h2>
                        <p class="text-muted">Set sales goals and monitor real-time achievement levels across your team.</p>
                    </div>
                    <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#assignTargetModal">
                        <i class="fe fe-target mr-1"></i> Set New Target
                    </button>
                </div>
                
                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>

                <div class="row">
                    <?php if(empty($targets)): ?>
                        <div class="col-12">
                            <div class="card shadow-sm border-0 text-center py-5">
                                <div class="card-body">
                                    <i class="fe fe-trending-up fe-32 mb-3 text-muted d-block"></i>
                                    <h5>No Targets Assigned</h5>
                                    <p class="text-muted">Start by assigning a performance goal to your team members.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php foreach($targets as $t): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($t['type']); ?></h6>
                                            <h4 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($t['executive_name']); ?></h4>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-muted p-0" type="button" data-toggle="dropdown">
                                                <i class="fe fe-more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                <a class="dropdown-item text-danger" href="targets?action=delete&id=<?php echo $t['id']; ?>" onclick="return confirm('Remove this target?')">Delete Target</a>
                                            </div>
                                        </div>
                                    </div>

                                    <?php 
                                        $percent = ($t['target_value'] > 0) ? round(($t['achieved_value'] / $t['target_value']) * 100) : 0;
                                        $progressClass = 'bg-primary';
                                        if($percent >= 100) $progressClass = 'bg-success';
                                        elseif($percent < 30) $progressClass = 'bg-danger';
                                    ?>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1 small font-weight-600">
                                            <span>Progress</span>
                                            <span><?php echo $percent; ?>%</span>
                                        </div>
                                        <div class="progress shadow-sm" style="height: 10px; border-radius: 5px;">
                                            <div class="progress-bar <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo min($percent, 100); ?>%" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>

                                    <div class="row text-center mt-3">
                                        <div class="col-6 border-right">
                                            <p class="text-muted small mb-1">Target</p>
                                            <h5 class="mb-0 font-weight-bold"><?php echo number_format($t['target_value']); ?></h5>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted small mb-1">Achieved</p>
                                            <h5 class="mb-0 font-weight-bold text-success"><?php echo number_format($t['achieved_value']); ?></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light border-0 py-2">
                                    <small class="text-muted">
                                        <i class="fe fe-calendar fe-10"></i> 
                                        <?php echo date('M d', strtotime($t['start_date'])); ?> - <?php echo date('M d, Y', strtotime($t['end_date'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Target Modal -->
    <div class="modal fade" id="assignTargetModal" tabindex="-1" role="dialog" aria-labelledby="assignTargetModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold" id="assignTargetModalLabel">Set Performance Target</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="targets?action=create" method="POST">
                    <div class="modal-body p-4">
                        <div class="form-group">
                            <label class="font-weight-600">Executive Member</label>
                            <select name="user_id" class="form-control form-control-lg bg-light border-0" required>
                                <option value="">-- Select Member --</option>
                                <?php foreach($team as $member): ?>
                                    <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Target Type</label>
                                <select name="type" class="form-control form-control-lg bg-light border-0">
                                    <option value="Sales Volume">Sales Volume</option>
                                    <option value="Client Meetings">Client Meetings</option>
                                    <option value="New Enrollments">New Enrollments</option>
                                    <option value="Recovery Amount">Recovery Amount</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Target Value</label>
                                <input type="number" name="target_value" class="form-control form-control-lg bg-light border-0" placeholder="e.g. 50000" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Start Date</label>
                                <input type="date" name="start_date" class="form-control form-control-lg bg-light border-0" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">End Date</label>
                                <input type="date" name="end_date" class="form-control form-control-lg bg-light border-0" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-600">Period</label>
                            <select name="period" class="form-control form-control-lg bg-light border-0">
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly" selected>Monthly</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-light px-4" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">Assign Target</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
.font-weight-600 { font-weight: 600; }
</style>

<?php include 'layout/footer.php'; ?>
