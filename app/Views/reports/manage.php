<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Reports & Approvals</h2>
                        <p class="text-muted mb-0">Review client meetings and travel allowance submissions from your team.</p>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <!-- Employee Filter -->
                        <?php if (!empty($users)): ?>
                            <div class="mr-3 pt-1">
                                <select id="user-selector" class="form-control form-control-sm border-0 shadow-sm px-3" style="min-width: 200px; border-radius: 20px; height: 38px;">
                                    <option value="all">All Employees / Team</option>
                                    <?php foreach($users as $u): ?>
                                        <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $selectedUser) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($u['name']); ?> (<?php echo $u['role_name'] ?? ($u['role'] ?? 'Staff'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="dropdown d-inline-block shadow-sm">
                            <button class="btn btn-primary dropdown-toggle font-weight-bold px-4" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 8px;">
                                <i class="fe fe-download mr-1"></i> Export <?php echo $selectedUser === 'all' ? 'Team' : 'Employee'; ?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="exportDropdown" style="min-width: 280px;">
                                <h6 class="dropdown-header text-uppercase text-primary font-weight-bold">Daily Intelligence (<?php echo $selectedUser === 'all' ? 'Team' : 'User'; ?>)</h6>
                                <a class="dropdown-item" href="reports?action=export&type=daily&user_id=<?php echo $selectedUser; ?>&category=Meeting&format=csv">
                                    <i class="fe fe-file-text mr-2 text-success"></i> Standard Meeting (Excel)
                                </a>
                                <a class="dropdown-item" target="_blank" href="reports?action=export&type=daily&user_id=<?php echo $selectedUser; ?>&category=Meeting&format=pdf">
                                    <i class="fe fe-printer mr-2 text-danger"></i> Standard Meeting (PDF)
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="reports?action=export&type=daily&user_id=<?php echo $selectedUser; ?>&category=Home+Enrollment&format=csv">
                                    <i class="fe fe-file-text mr-2 text-success"></i> Home Enrollment (Excel)
                                </a>
                                <a class="dropdown-item" target="_blank" href="reports?action=export&type=daily&user_id=<?php echo $selectedUser; ?>&category=Home+Enrollment&format=pdf">
                                    <i class="fe fe-printer mr-2 text-danger"></i> Home Enrollment (PDF)
                                </a>

                                <div class="dropdown-divider"></div>
                                
                                <h6 class="dropdown-header text-uppercase text-primary font-weight-bold">Monthly Summaries (<?php echo $selectedUser === 'all' ? 'Team' : 'User'; ?>)</h6>
                                <a class="dropdown-item" href="reports?action=export&type=monthly&user_id=<?php echo $selectedUser; ?>&category=Meeting&format=csv">
                                    <i class="fe fe-list mr-2 text-success"></i> Standard Meeting (Excel)
                                </a>
                                <a class="dropdown-item" target="_blank" href="reports?action=export&type=monthly&user_id=<?php echo $selectedUser; ?>&category=Meeting&format=pdf">
                                    <i class="fe fe-layers mr-2 text-danger"></i> Standard Meeting (PDF)
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="reports?action=export&type=monthly&user_id=<?php echo $selectedUser; ?>&category=Home+Enrollment&format=csv">
                                    <i class="fe fe-list mr-2 text-success"></i> Home Enrollment (Excel)
                                </a>
                                <a class="dropdown-item" target="_blank" href="reports?action=export&type=monthly&user_id=<?php echo $selectedUser; ?>&category=Home+Enrollment&format=pdf">
                                    <i class="fe fe-layers mr-2 text-danger"></i> Home Enrollment (PDF)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>

                <!-- Meeting Reports Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-primary">Client Meetings</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Executive</th>
                                        <th>Date & Time</th>
                                        <th>Client / Hospital</th>
                                        <th>Meeting Type</th>
                                        <th>Status</th>
                                        <th class="text-right pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($meetings)): ?>
                                        <tr><td colspan="6" class="text-center py-4 text-muted">No meeting records found.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($meetings as $m): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-600"><?php echo htmlspecialchars($m['user_name']); ?></td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($m['meeting_time'])); ?></td>
                                        <td>
                                            <div class="font-weight-500"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($m['hospital_office_name']); ?></small>
                                        </td>
                                        <td><?php echo $m['meeting_type']; ?></td>
                                        <td>
                                            <?php 
                                            $badge = 'badge-secondary';
                                            if($m['status'] == 'Approved') $badge = 'badge-success';
                                            if($m['status'] == 'Rejected') $badge = 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $badge; ?> px-2 py-1"><?php echo $m['status']; ?></span>
                                            <?php if(!empty($m['admin_comments'])): ?>
                                                <div class="small text-muted mt-1 font-italic" style="max-width: 150px; line-height: 1.2;">
                                                    "<?php echo htmlspecialchars($m['admin_comments']); ?>"
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right pr-4">
                                            <div class="btn-group">
                                                <?php if($m['status'] == 'Pending'): ?>
                                                    <button type="button" onclick="openActionModal('approveMeeting', <?php echo $m['id']; ?>)" class="btn btn-sm btn-outline-success mr-1">Approve</button>
                                                    <button type="button" onclick="openActionModal('rejectMeeting', <?php echo $m['id']; ?>)" class="btn btn-sm btn-outline-danger mr-1">Reject</button>
                                                <?php endif; ?>
                                                <?php if($_SESSION['role'] == 'Admin'): ?>
                                                    <a href="reports?action=editMeeting&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-light mr-1">Edit</a>
                                                    <a href="meetings?action=delete&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-danger text-white" onclick="return confirm('Permanently delete this meeting log and its associated verification selfie?');"><i class="fe fe-trash-2"></i></a>
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

                <!-- Travel Summaries Section -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-primary">Travel Allowance Claims</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Executive</th>
                                        <th>Date</th>
                                        <th>Total Distance</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-right pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($travelSummaries)): ?>
                                        <tr><td colspan="6" class="text-center py-4 text-muted">No travel claims found.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($travelSummaries as $s): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-600"><?php echo htmlspecialchars($s['user_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($s['date'])); ?></td>
                                        <td><strong><?php echo number_format($s['total_distance'], 2); ?> KM</strong></td>
                                        <td class="text-success font-weight-bold">₹<?php echo number_format($s['allowance_earned'], 2); ?></td>
                                        <td>
                                            <?php 
                                            $badge = 'badge-secondary';
                                            if($s['status'] == 'Approved') $badge = 'badge-success';
                                            if($s['status'] == 'Rejected') $badge = 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $badge; ?> px-2 py-1"><?php echo $s['status']; ?></span>
                                            <?php if(!empty($s['admin_comments'])): ?>
                                                <div class="small text-muted mt-1 font-italic" style="max-width: 150px; line-height: 1.2;">
                                                    "<?php echo htmlspecialchars($s['admin_comments']); ?>"
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right pr-4">
                                            <a href="travel-history?user_id=<?php echo $s['user_id']; ?>&date=<?php echo $s['date']; ?>" class="btn btn-sm btn-info text-white mr-2" title="Verify Route & Coordinates"><i class="fe fe-map-pin mr-1"></i>Audit Route</a>
                                            <?php if($s['status'] == 'Pending'): ?>
                                                <button type="button" onclick="openActionModal('approveTravel', <?php echo $s['id']; ?>)" class="btn btn-sm btn-outline-success mr-1" title="Approve Claim"><i class="fe fe-check"></i></button>
                                                <button type="button" onclick="openActionModal('rejectTravel', <?php echo $s['id']; ?>)" class="btn btn-sm btn-outline-danger" title="Reject Claim"><i class="fe fe-x"></i></button>
                                            <?php else: ?>
                                                <span class="text-muted small italic">Finalized</span>
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
</main>

<style>
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>

<!-- Verification Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="actionModalTitle">Verify Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="reports" method="POST" id="virtualActionForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="actionModalId">
                    <p id="actionModalDesc" class="text-muted small">Please provide a reason or comment for this action. This will be saved in the audit logs.</p>
                    <div class="form-group mb-0">
                        <textarea name="reason" class="form-control bg-light border-0 px-3 py-2" rows="3" placeholder="Enter comments (optional)..." style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm" id="actionModalBtn">Confirm Action</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelector = document.getElementById('user-selector');
    if (userSelector) {
        userSelector.addEventListener('change', function() {
            window.location.href = 'reports?user_id=' + this.value;
        });
    }
});

function openActionModal(actionStr, recordId) {
    const isApproval = actionStr.includes('approve');
    const isMeeting = actionStr.includes('Meeting');
    
    document.getElementById('actionModalId').value = recordId;
    document.getElementById('virtualActionForm').action = 'reports?action=' + actionStr;
    
    document.getElementById('actionModalTitle').innerText = isApproval ? 'Approve Record' : 'Reject Record';
    document.getElementById('actionModalTitle').style.color = isApproval ? '#28a745' : '#dc3545';
    
    document.getElementById('actionModalDesc').innerText = `Please provide a mandatory or optional comment before ${isApproval ? 'approving' : 'rejecting'} this ${isMeeting ? 'meeting' : 'travel allocation'}.`;
    
    const submitBtn = document.getElementById('actionModalBtn');
    submitBtn.innerText = isApproval ? 'Approve' : 'Reject';
    submitBtn.className = isApproval ? 'btn btn-success rounded-pill px-4 font-weight-bold shadow-sm' : 'btn btn-danger rounded-pill px-4 font-weight-bold shadow-sm';
    
    $('#actionModal').modal('show');
}
</script>
