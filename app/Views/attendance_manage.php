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
                                
                                <tbody id="attLogBody">
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
                                                <?php if(isset($r['check_in_photo']) && $r['check_in_photo']): ?>
                                                    <a href="<?php echo $r['check_in_photo']; ?>" target="_blank" class="mr-2" title="Check-In Selfie">
                                                        <img src="<?php echo $r['check_in_photo']; ?>" class="rounded shadow-sm" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #fff;">
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(isset($r['odometer_photo']) && $r['odometer_photo']): ?>
                                                    <a href="<?php echo $r['odometer_photo']; ?>" target="_blank" class="mr-2" title="Odometer Image">
                                                        <i class="fe fe-truck text-info" style="font-size: 1.2rem;"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(isset($r['check_out_photo']) && $r['check_out_photo']): ?>
                                                    <a href="<?php echo $r['check_out_photo']; ?>" target="_blank" title="Check-Out Photo">
                                                        <img src="<?php echo $r['check_out_photo']; ?>" class="rounded shadow-sm" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #fff;">
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(empty($r['check_in_photo']) && empty($r['odometer_photo']) && empty($r['check_out_photo'])): ?>
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
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="viewAttendanceDetails(<?php echo htmlspecialchars(json_encode($r)); ?>)"><i class="fe fe-eye fe-12 mr-2 text-primary"></i> View Full Audit</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="attendance?action=edit&id=<?php echo $r['id']; ?>"><i class="fe fe-edit-3 fe-12 mr-2"></i> Edit Record</a>
                                                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="attendance?action=delete&id=<?php echo $r['id']; ?>" onclick="return confirm('Permanently delete this attendance record and its associated verification photos?')"><i class="fe fe-trash-2 fe-12 mr-2"></i> Delete</a>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                            <span class="text-muted small" id="attLogPageInfo"></span>
                            <nav><ul class="pagination pagination-sm mb-0" id="attLogPagination"></ul></nav>
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
<script>
(function(){
    const PER_PAGE = 10;
    let page = 1;
    const tbody = document.getElementById('attLogBody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr'));
    function render() {
        const total = rows.length, pages = Math.max(1, Math.ceil(total / PER_PAGE));
        const start = (page - 1) * PER_PAGE, end = Math.min(start + PER_PAGE, total);
        rows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
        document.getElementById('attLogPageInfo').textContent = total ? `Showing ${start+1}–${end} of ${total} records` : '';
        const ul = document.getElementById('attLogPagination');
        ul.innerHTML = '';
        const prev = document.createElement('li'); prev.className = 'page-item' + (page===1?' disabled':'');
        prev.innerHTML = '<a class="page-link" href="#">&laquo;</a>';
        prev.addEventListener('click', e => { e.preventDefault(); if(page>1){page--;render();} }); ul.appendChild(prev);
        for(let i=1;i<=pages;i++) {
            const li = document.createElement('li'); li.className = 'page-item'+(i===page?' active':'');
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', e => { e.preventDefault(); page=i; render(); }); ul.appendChild(li);
        }
        const next = document.createElement('li'); next.className = 'page-item'+(page===pages?' disabled':'');
        next.innerHTML = '<a class="page-link" href="#">&raquo;</a>';
        next.addEventListener('click', e => { e.preventDefault(); if(page<pages){page++;render();} }); ul.appendChild(next);
    }
    render();
})();
</script>

<?php include 'layout/footer.php'; ?>

<!-- Attendance Detail Modal -->
<div class="modal fade" id="attendanceDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fe fe-clipboard mr-2"></i>Attendance Audit Detail</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h6 class="text-uppercase small font-weight-bold text-muted mb-3">Verification Photos</h6>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="small text-muted d-block">Check-In Selfie</label>
                                <div id="modal-check-in-photo-container" class="rounded border p-1 bg-light">
                                    <img id="modal-check-in-photo" src="" class="img-fluid rounded" style="width: 100%; aspect-ratio: 1/1; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="small text-muted d-block">Odometer Reading</label>
                                <div id="modal-odo-photo-container" class="rounded border p-1 bg-light">
                                    <img id="modal-odo-photo" src="" class="img-fluid rounded" style="width: 100%; aspect-ratio: 1/1; object-fit: cover;">
                                    <div id="modal-odo-reading-badge" class="badge badge-dark mt-1 w-100 py-2"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="small text-muted d-block">Check-Out Verification</label>
                                <div id="modal-check-out-photo-container" class="rounded border p-1 bg-light">
                                    <img id="modal-check-out-photo" src="" class="img-fluid rounded" style="width: 100%; height: 180px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase small font-weight-bold text-muted mb-3">Session Metadata</h6>
                        <div class="bg-light p-3 rounded mb-3 border">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-0"><i class="fe fe-user mr-1"></i> Staff Member</label>
                                <div id="modal-staff-name" class="h6 font-weight-bold mb-0"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="small font-weight-bold text-success mb-0">Check-In</label>
                                    <div id="modal-check-in-time" class="font-weight-600"></div>
                                </div>
                                <div class="col-6">
                                    <label class="small font-weight-bold text-danger mb-0">Check-Out</label>
                                    <div id="modal-check-out-time" class="font-weight-600"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small font-weight-bold text-muted mb-0">Duration</label>
                                <div id="modal-duration" class="h5 font-weight-bold text-dark"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small font-weight-bold text-muted"><i class="fe fe-map-pin mr-1"></i> Locations</label>
                            <div id="modal-locations" class="small">
                                <div class="mb-2">
                                    <span class="badge badge-soft-success">In:</span> <span id="modal-in-addr"></span>
                                </div>
                                <div>
                                    <span class="badge badge-soft-danger">Out:</span> <span id="modal-out-addr"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="modal-ticket-box" style="display:none;">
                            <label class="small font-weight-bold text-muted">Additional Details</label>
                            <div id="modal-ticket-details" class="p-2 border rounded bg-soft-info small text-dark"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-dismiss="modal">Close Audit</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewAttendanceDetails(data) {
    document.getElementById('modal-staff-name').textContent = data.user_name || 'N/A';
    document.getElementById('modal-check-in-time').textContent = data.check_in_time ? new Date(data.check_in_time).toLocaleString('en-IN') : 'N/A';
    document.getElementById('modal-check-out-time').textContent = data.check_out_time ? new Date(data.check_out_time).toLocaleString('en-IN') : 'Still On-Duty';
    document.getElementById('modal-in-addr').textContent = data.check_in_address || 'No address logged';
    document.getElementById('modal-out-addr').textContent = data.check_out_address || 'N/A';
    
    // Duration Calculation
    if (data.check_out_time) {
        const start = new Date(data.check_in_time);
        const end = new Date(data.check_out_time);
        const diff = Math.abs(end - start);
        const hours = Math.floor(diff / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        document.getElementById('modal-duration').textContent = hours + 'h ' + minutes + 'm';
    } else {
        document.getElementById('modal-duration').textContent = 'Active Session';
    }

    // Images
    const checkInImg = document.getElementById('modal-check-in-photo');
    checkInImg.src = data.check_in_photo || 'assets/images/placeholder-face.jpg';
    
    const odoImg = document.getElementById('modal-odo-photo');
    const odoContainer = document.getElementById('modal-odo-photo-container');
    const odoBadge = document.getElementById('modal-odo-reading-badge');
    if (data.odometer_photo) {
        odoImg.src = data.odometer_photo;
        odoContainer.style.display = 'block';
        odoBadge.textContent = 'Odo Reading: ' + (data.odometer_reading || 'N/A');
        odoBadge.style.display = 'block';
    } else {
        odoContainer.style.display = 'none';
        odoBadge.style.display = 'none';
    }

    const checkOutImg = document.getElementById('modal-check-out-photo');
    if (data.check_out_photo) {
        checkOutImg.src = data.check_out_photo;
        document.getElementById('modal-check-out-photo-container').style.display = 'block';
    } else {
        document.getElementById('modal-check-out-photo-container').style.display = 'none';
    }

    const ticketBox = document.getElementById('modal-ticket-box');
    if (data.ticket_details) {
        ticketBox.style.display = 'block';
        document.getElementById('modal-ticket-details').textContent = data.ticket_details;
    } else {
        ticketBox.style.display = 'none';
    }

    $('#attendanceDetailModal').modal('show');
}
</script>

<style>
.font-weight-600 { font-weight: 600; }
.badge-soft-success { background-color: rgba(40, 167, 69, 0.12); color: #28a745; }
.badge-soft-danger { background-color: rgba(220, 53, 69, 0.12); color: #dc3545; }
.badge-soft-info { background-color: rgba(23, 162, 184, 0.12); color: #17a2b8; }
</style>

