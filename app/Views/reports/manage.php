<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                    <div class="mb-3 mb-md-0">
                        <h2 class="h3 mb-0 page-title font-weight-bold">Intelligence Reports & Approvals</h2>
                        <p class="text-muted mb-0">Operational overview of field interactions and travel allowance claims.</p>
                    </div>
                    
                    <div class="d-flex align-items-center flex-wrap">
                        <!-- Employee Filter -->
                        <?php if (!empty($users)): ?>
                            <div class="mr-md-3 mb-2 mb-md-0">
                                <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0 pl-3"><i class="fe fe-users text-primary"></i></span>
                                    </div>
                                    <select id="user-selector" class="form-control border-0 px-2" style="min-width: 220px; height: 42px; font-weight: 600;">
                                        <option value="all">All Personnel (Team View)</option>
                                        <?php foreach($users as $u): ?>
                                            <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $selectedUser) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($u['name']); ?> — <?php echo $u['role_name'] ?? ($u['role'] ?? 'Staff'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="dropdown shadow-sm mb-2 mb-md-0">
                            <button class="btn btn-dark dropdown-toggle font-weight-bold px-4 h-100 d-flex align-items-center" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 12px; height: 42px;">
                                <i class="fe fe-download mr-2"></i> Export Data
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow border-0 py-3" aria-labelledby="exportDropdown" style="min-width: 300px; border-radius:12px;">
                                <h6 class="dropdown-header text-uppercase text-primary font-weight-800" style="font-size:0.7rem; letter-spacing:0.05em;">Daily Logs (<?php echo $selectedUser === 'all' ? 'Team' : 'User'; ?>)</h6>
                                <a class="dropdown-item py-2" href="reports?action=export&type=daily&user_id=<?php echo $selectedUser; ?>&category=Meeting&format=csv">
                                    <i class="fe fe-file-text mr-3 text-success"></i> Meetings & Activity (Excel)
                                </a>
                                <a class="dropdown-item py-2" target="_blank" href="reports?action=export&type=daily&user_id=<?php echo $selectedUser; ?>&category=Meeting&format=pdf">
                                    <i class="fe fe-printer mr-3 text-danger"></i> Meetings & Activity (PDF)
                                </a>
                                <div class="dropdown-divider mx-3"></div>
                                <h6 class="dropdown-header text-uppercase text-primary font-weight-800" style="font-size:0.7rem; letter-spacing:0.05em;">Monthly Records (<?php echo $selectedUser === 'all' ? 'Team' : 'User'; ?>)</h6>
                                <a class="dropdown-item py-2" href="reports?action=export&type=monthly&user_id=<?php echo $selectedUser; ?>&category=Meeting&format=csv">
                                    <i class="fe fe-list mr-3 text-success"></i> Comprehensive (Excel)
                                </a>
                                <a class="dropdown-item py-2" target="_blank" href="reports?action=export&type=monthly&user_id=<?php echo $selectedUser; ?>&category=Meeting&format=pdf">
                                    <i class="fe fe-layers mr-3 text-danger"></i> Comprehensive (PDF)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-lg py-3 px-4 mb-4" style="border-left: 5px solid #2ecc71 !important;">
                        <i class="fe fe-check-circle mr-2"></i> <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                    </div>
                <?php endif; ?>

                <!-- Meeting Reports Section -->
                <div class="card shadow-sm border-0 mb-5 rounded-lg overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h5 class="card-title mb-0 text-dark font-weight-bold"><i class="fe fe-users mr-2 text-primary"></i> Field Interaction Verification</h5>
                        <span class="badge badge-soft-primary px-3 py-1"><?php echo count($meetings); ?> LOGS</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background:#fbfcfe;" class="text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4 py-3 border-0">Personnel</th>
                                        <th class="py-3 border-0">Occurred On</th>
                                        <th class="py-3 border-0">Client / Institution</th>
                                        <th class="py-3 border-0">Category</th>
                                        <th class="py-3 border-0">Status</th>
                                        <th class="text-right pr-4 py-3 border-0">Operations</th>
                                    </tr>
                                </thead>
                                <tbody id="meetingsBody">
                                    <?php if(empty($meetings)): ?>
                                        <tr><td colspan="6" class="text-center py-5 text-muted bg-white">No pending or historical interactions found.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($meetings as $m): ?>
                                    <tr>
                                        <td class="pl-4 py-3">
                                            <div class="font-weight-700 text-dark"><?php echo htmlspecialchars($m['user_name']); ?></div>
                                            <div class="small text-muted">Executive</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="font-weight-600"><?php echo date('M d, Y', strtotime($m['meeting_time'])); ?></div>
                                            <div class="small text-muted"><?php echo date('h:i A', strtotime($m['meeting_time'])); ?></div>
                                        </td>
                                        <td class="py-3">
                                            <div class="font-weight-700 text-dark"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                            <div class="small text-muted"><i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars($m['hospital_office_name']); ?></div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge badge-light border x-small font-weight-bold"><?php echo strtoupper($m['meeting_type']); ?></span>
                                        </td>
                                        <td class="py-3">
                                            <?php 
                                            $badge = 'badge-soft-secondary';
                                            if($m['status'] == 'Approved') $badge = 'badge-soft-success';
                                            if($m['status'] == 'Rejected') $badge = 'badge-soft-danger';
                                            ?>
                                            <div class="d-flex flex-column">
                                                <span class="badge <?php echo $badge; ?> px-2 py-1 font-weight-bold" style="width:fit-content;"><?php echo strtoupper($m['status']); ?></span>
                                                <?php if(!empty($m['admin_comments'])): ?>
                                                    <div class="small text-muted mt-1 font-italic font-weight-500" style="max-width: 150px; line-height: 1.2;">
                                                        "<?php echo htmlspecialchars($m['admin_comments']); ?>"
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-right pr-4 py-3">
                                            <div class="btn-group shadow-sm rounded-lg overflow-hidden">
                                                <?php if($m['status'] == 'Pending'): ?>
                                                    <button type="button" onclick="openActionModal('approveMeeting', <?php echo $m['id']; ?>)" class="btn btn-sm btn-success px-3" style="font-weight:700;"><i class="fe fe-check mr-1"></i> Approve</button>
                                                    <button type="button" onclick="openActionModal('rejectMeeting', <?php echo $m['id']; ?>)" class="btn btn-sm btn-danger px-3" style="font-weight:700;"><i class="fe fe-x mr-1"></i> Reject</button>
                                                <?php endif; ?>
                                                <?php if($_SESSION['role'] == 'Admin'): ?>
                                                    <a href="reports?action=editMeeting&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-light border-left px-3 font-weight-700">Edit</a>
                                                    <a href="meetings?action=delete&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-light text-danger border-left px-3" onclick="return confirm('Permanently delete this meeting log?');"><i class="fe fe-trash-2"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light-50">
                            <span class="text-muted font-weight-600 small" id="meetingsPageInfo"></span>
                            <nav><ul class="pagination pagination-sm mb-0 shadow-none border-0" id="meetingsPagination"></ul></nav>
                        </div>
                    </div>
                </div>

                <!-- Travel Summaries Section -->
                <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h5 class="card-title mb-0 text-dark font-weight-bold"><i class="fe fe-truck mr-2 text-primary"></i> Travel Allowance Claims</h5>
                        <span class="badge badge-soft-primary px-3 py-1"><?php echo count($travelSummaries); ?> CLAIMS</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background:#fbfcfe;" class="text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4 py-3 border-0">Personnel</th>
                                        <th class="py-3 border-0">Audit Date</th>
                                        <th class="py-3 border-0">Accurate Distance</th>
                                        <th class="py-3 border-0">Payout Amount</th>
                                        <th class="py-3 border-0">Status</th>
                                        <th class="text-right pr-4 py-3 border-0">Audit Action</th>
                                    </tr>
                                </thead>
                                <tbody id="travelBody">
                                    <?php if(empty($travelSummaries)): ?>
                                        <tr><td colspan="6" class="text-center py-5 text-muted bg-white">No travel claims found.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($travelSummaries as $s): ?>
                                    <tr>
                                        <td class="pl-4 py-3">
                                            <div class="font-weight-700 text-dark"><?php echo htmlspecialchars($s['user_name']); ?></div>
                                            <small class="text-muted">Intelligence Verified</small>
                                        </td>
                                        <td class="py-3"><?php echo date('M d, Y', strtotime($s['date'])); ?></td>
                                        <td class="py-3">
                                            <div class="font-weight-800 text-primary" style="font-size:1rem;"><?php echo number_format($s['total_distance'], 2); ?> <span class="small font-weight-600">KM</span></div>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-success font-weight-800" style="font-size:1.05rem;">₹<?php echo number_format($s['allowance_earned'], 2); ?></div>
                                        </td>
                                        <td class="py-3">
                                            <?php 
                                            $badge = 'badge-soft-secondary';
                                            if($s['status'] == 'Approved') $badge = 'badge-soft-success';
                                            if($s['status'] == 'Rejected') $badge = 'badge-soft-danger';
                                            ?>
                                            <span class="badge <?php echo $badge; ?> px-2 py-1 font-weight-bold"><?php echo strtoupper($s['status']); ?></span>
                                        </td>
                                        <td class="text-right pr-4 py-3">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <a href="travel-history?user_id=<?php echo $s['user_id']; ?>&date=<?php echo $s['date']; ?>" class="btn btn-sm btn-info text-white mr-2 px-3 font-weight-700 shadow-sm" title="Verify Route & Coordinates"><i class="fe fe-map-pin mr-1"></i>Audit Route</a>
                                                
                                                <?php if($s['status'] == 'Pending'): ?>
                                                    <div class="btn-group shadow-sm rounded-lg overflow-hidden">
                                                        <button type="button" onclick="openActionModal('approveTravel', <?php echo $s['id']; ?>)" class="btn btn-sm btn-success px-3" title="Approve Claim"><i class="fe fe-check"></i></button>
                                                        <button type="button" onclick="openActionModal('rejectTravel', <?php echo $s['id']; ?>)" class="btn btn-sm btn-danger px-3" title="Reject Claim"><i class="fe fe-x"></i></button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small font-weight-600 italic">Audit Finalized</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light-50">
                            <span class="text-muted font-weight-600 small" id="travelPageInfo"></span>
                            <nav><ul class="pagination pagination-sm mb-0 shadow-none border-0" id="travelPagination"></ul></nav>
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
<script>
function makePaginator(tbodyId, infoId, paginationId) {
    const PER_PAGE = 10;
    let page = 1;
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
    if (!rows.length) return;
    function render() {
        const total = rows.length, pages = Math.max(1, Math.ceil(total / PER_PAGE));
        const start = (page - 1) * PER_PAGE, end = Math.min(start + PER_PAGE, total);
        Array.from(tbody.querySelectorAll('tr')).forEach(r => r.style.display = rows.includes(r) ? 'none' : '');
        rows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
        document.getElementById(infoId).textContent = `Showing ${start+1}–${end} of ${total}`;
        const ul = document.getElementById(paginationId); ul.innerHTML = '';
        const prev = document.createElement('li'); prev.className = 'page-item'+(page===1?' disabled':'');
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
}
document.addEventListener('DOMContentLoaded', function() {
    makePaginator('meetingsBody', 'meetingsPageInfo', 'meetingsPagination');
    makePaginator('travelBody', 'travelPageInfo', 'travelPagination');
});
</script>

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
