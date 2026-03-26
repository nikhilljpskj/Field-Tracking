<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Attendance Log</h2>
                        <p class="text-muted">Review and manage field executive check-in/out records.</p>
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
                                        <th class="pl-4">Employee</th>
                                        <th>Date</th>
                                        <th>Check-In (IST)</th>
                                        <th>Check-Out (IST)</th>
                                        <th>Verification</th>
                                        <th>Duration</th>
                                        <th class="text-right pr-4">Action</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <?php foreach($records as $r): ?>
                                    <tr>
                                        <td class="pl-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm mr-2">
                                                    <span class="avatar-title rounded-circle bg-light text-primary font-weight-bold">
                                                        <?php echo strtoupper(substr($r['user_name'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                                <span class="font-weight-600 text-dark"><?php echo htmlspecialchars($r['user_name']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($r['check_in_time'])); ?></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark font-weight-500"><?php echo date('h:i A', strtotime($r['check_in_time'])); ?></span>
                                                <small class="text-truncate" style="max-width: 150px;" title="<?php echo $r['check_in_address']; ?>">
                                                    <i class="fe fe-map-pin fe-10"></i> <?php echo $r['check_in_address'] ?: 'No address'; ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($r['check_out_time']): ?>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark font-weight-500"><?php echo date('h:i A', strtotime($r['check_out_time'])); ?></span>
                                                    <small class="text-truncate" style="max-width: 150px;" title="<?php echo $r['check_out_address']; ?>">
                                                        <i class="fe fe-map-pin fe-10"></i> <?php echo $r['check_out_address'] ?: 'No address'; ?>
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge badge-soft-warning text-warning px-2">Still On-Duty</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if($r['check_in_photo']): ?>
                                                    <a href="<?php echo $r['check_in_photo']; ?>" target="_blank" class="mr-2" title="Check-In Selfie">
                                                        <img src="<?php echo $r['check_in_photo']; ?>" class="rounded shadow-sm" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #fff;">
                                                    </a>
                                                <?php endif; ?>
                                                <?php if($r['odometer_photo']): ?>
                                                    <a href="<?php echo $r['odometer_photo']; ?>" target="_blank" class="mr-2" title="Odometer Image">
                                                        <i class="fe fe-truck text-info" style="font-size: 1.2rem;"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if($r['check_out_photo']): ?>
                                                    <a href="<?php echo $r['check_out_photo']; ?>" target="_blank" title="Check-Out Photo">
                                                        <img src="<?php echo $r['check_out_photo']; ?>" class="rounded shadow-sm" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #fff;">
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(!$r['check_in_photo'] && !$r['odometer_photo'] && !$r['check_out_photo']): ?>
                                                    <span class="text-muted small italic">No media</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            if($r['check_out_time']) {
                                                $start = strtotime($r['check_in_time']);
                                                $end = strtotime($r['check_out_time']);
                                                $diff = $end - $start;
                                                $hours = floor($diff / 3600);
                                                $mins = floor(($diff / 60) % 60);
                                                echo ($hours > 0 ? $hours . 'h ' : '') . $mins . 'm';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-right pr-4">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fe fe-more-horizontal"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                    <a class="dropdown-item" href="attendance?action=edit&id=<?php echo $r['id']; ?>"><i class="fe fe-edit-3 fe-12 mr-2"></i> Edit Record</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="attendance?action=delete&id=<?php echo $r['id']; ?>" onclick="return confirm('Delete this attendance record?')"><i class="fe fe-trash-2 fe-12 mr-2"></i> Delete</a>
                                                </div>
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
</main>

<style>
.badge-soft-warning { background-color: rgba(255, 190, 11, 0.1); }
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }
.op-1 { opacity: 0.1; }
</style>

<?php include 'layout/footer.php'; ?>
