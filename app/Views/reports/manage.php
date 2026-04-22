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
                            <div class="mr-md-3 mb-2 mb-md-0 d-flex flex-wrap">
                                <div class="input-group shadow-sm mr-2 mb-2 mb-md-0" style="border-radius: 8px; overflow: hidden; height: 36px; border: 1px solid #eef0f2;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0 pl-2 pr-1"><i class="fe fe-users text-primary" style="font-size: 0.8rem;"></i></span>
                                    </div>
                                    <select id="user-selector" class="form-control border-0 px-2" style="min-width: 180px; font-size: 0.85rem; font-weight: 600; height: 34px;">
                                        <option value="all">All Personnel</option>
                                        <?php foreach($users as $u): ?>
                                            <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $selectedUser) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($u['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; height: 36px; border: 1px solid #eef0f2;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0 pl-2 pr-1"><i class="fe fe-calendar text-info" style="font-size: 0.8rem;"></i></span>
                                    </div>
                                    <input type="date" id="date-selector" class="form-control border-0 px-2" value="<?php echo $selectedDate; ?>" style="width: 140px; font-size: 0.85rem; font-weight: 600; height: 34px;">
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="dropdown shadow-sm mb-2 mb-md-0">
                            <button class="btn btn-dark dropdown-toggle font-weight-bold px-3 h-100 d-flex align-items-center" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 8px; height: 36px; font-size: 0.85rem;">
                                <i class="fe fe-download mr-2"></i> Export
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow border-0 py-3" aria-labelledby="exportDropdown" style="min-width: 300px; border-radius:12px;">
                                <h6 class="dropdown-header text-uppercase text-primary font-weight-800" style="font-size:0.7rem; letter-spacing:0.05em;">Daily Logs (<?php echo $selectedUser === 'all' ? 'Team' : (isset($user['name']) ? $user['name'] : 'User'); ?>)</h6>
                                <a class="dropdown-item py-2" href="reports?action=export&type=daily&user_id=<?php echo $selectedUser; ?>&date=<?php echo $selectedDate; ?>&category=Meeting&format=csv">
                                    <i class="fe fe-file-text mr-3 text-success"></i> Meetings & Activity (Excel)
                                </a>
                                <a class="dropdown-item py-2" target="_blank" href="reports?action=export&type=daily&user_id=<?php echo $selectedUser; ?>&date=<?php echo $selectedDate; ?>&category=Meeting&format=pdf">
                                    <i class="fe fe-printer mr-3 text-danger"></i> Meetings & Activity (PDF)
                                </a>
                                <div class="dropdown-divider mx-3"></div>
                                <h6 class="dropdown-header text-uppercase text-primary font-weight-800" style="font-size:0.7rem; letter-spacing:0.05em;">Monthly Records (<?php echo $selectedUser === 'all' ? 'Team' : 'User'; ?>)</h6>
                                <a class="dropdown-item py-2" href="reports?action=export&type=monthly&user_id=<?php echo $selectedUser; ?>&format=csv">
                                    <i class="fe fe-list mr-3 text-success"></i> Comprehensive (Excel)
                                </a>
                                <a class="dropdown-item py-2" target="_blank" href="reports?action=export&type=monthly&user_id=<?php echo $selectedUser; ?>&format=pdf">
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
                                                <button type="button" class="btn btn-sm btn-light px-3" onclick="viewIntelligence(<?php echo $m['id']; ?>)" title="View Intelligence">
                                                    <i class="fe fe-eye text-primary"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light px-3" onclick="triggerLocationPopup(<?php echo htmlspecialchars(json_encode($m)); ?>)" title="Verify Location">
                                                    <i class="fe fe-map-pin text-info"></i>
                                                </button>
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

<!-- Intelligence Detail Modal -->
<div class="modal fade" id="intelligenceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-4 border-0">
                <div class="d-flex align-items-center">
                    <div class="bg-white-20 rounded-circle p-2 mr-3">
                        <i class="fe fe-activity fe-24"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-white mb-0">Meeting Intelligence Detail</h5>
                        <p class="modal-subtitle text-white-50 mb-0 small" id="intel-session-info">Verifying field session...</p>
                    </div>
                </div>
                <button type="button" class="close text-white opacity-75" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="intel-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>
                </div>
                
                <div id="intel-content" style="display:none;">
                    <div class="row no-gutters">
                        <div class="col-md-5 bg-light p-4 d-flex flex-column align-items-center justify-content-center text-center">
                            <div id="intel-selfie-container" class="mb-3">
                                <img src="" id="intel-selfie" class="img-fluid rounded-lg shadow" style="max-height: 300px; border: 4px solid #fff;">
                            </div>
                            <div class="badge badge-soft-primary px-3 py-1 font-weight-bold mb-2" id="intel-type"></div>
                            <div class="text-dark font-weight-800" id="intel-time" style="font-size: 1.1rem;"></div>
                            <div class="small text-muted mb-4">India Standard Time (IST)</div>
                            
                            <div class="w-100 p-3 bg-white rounded-lg shadow-sm text-left">
                                <label class="text-muted small font-weight-bold text-uppercase mb-1">Assigned Personnel</label>
                                <div class="font-weight-700 text-dark" id="intel-user"></div>
                                <hr class="my-2 opacity-50">
                                <label class="text-muted small font-weight-bold text-uppercase mb-1">Approval Authority</label>
                                <div class="font-weight-700 text-primary" id="intel-approver">No authority assigned</div>
                            </div>
                        </div>
                        
                        <div class="col-md-7 p-4">
                            <section class="mb-4">
                                <label class="text-muted small font-weight-bold text-uppercase mb-1 d-block"><i class="fe fe-home mr-2"></i>Location Intelligence</label>
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="font-weight-800 text-dark mb-1" id="intel-hospital"></h6>
                                        <p class="small text-muted mb-0" id="intel-address"></p>
                                    </div>
                                    <button type="button" id="intel-map-btn" class="btn btn-sm btn-soft-primary rounded-circle" title="View Location Detail">
                                        <i class="fe fe-map-pin"></i>
                                    </button>
                                </div>
                            </section>
                            
                            <section class="mb-4">
                                <label class="text-muted small font-weight-bold text-uppercase mb-1 d-block"><i class="fe fe-user mr-2"></i>In-Person Contact</label>
                                <div class="font-weight-700 text-dark" id="intel-client"></div>
                            </section>
                            
                            <section class="mb-4 p-3 bg-soft-info rounded-lg">
                                <label class="text-info small font-weight-bold text-uppercase mb-1 d-block"><i class="fe fe-message-square mr-2"></i>Executive Summary/Notes</label>
                                <p class="text-dark font-weight-500 mb-0" id="intel-notes" style="font-style: italic; line-height: 1.5;"></p>
                            </section>
                            
                            <section class="mb-4">
                                <label class="text-muted small font-weight-bold text-uppercase mb-1 d-block"><i class="fe fe-trending-up mr-2"></i>Actionable Outcome</label>
                                <p class="text-dark font-weight-700" id="intel-outcome"></p>
                            </section>
                            
                            <div id="intel-action-footer" class="mt-5">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div id="intel-status-pill"></div>
                                    <div class="intel-controls" style="display:none;">
                                        <button type="button" id="intel-approve-btn" class="btn btn-success btn-lg rounded-pill px-5 font-weight-bold shadow">
                                            <i class="fe fe-check-circle mr-2"></i> Approve
                                        </button>
                                        <button type="button" id="intel-reject-btn" class="btn btn-outline-danger btn-lg rounded-pill px-4 font-weight-bold ml-2">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Identity Gateway (Stage 1) -->
<div class="modal fade" id="locationVerificationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="bg-soft-primary d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 12px; color: #4361ee;">
                        <i class="fe fe-map-pin h4 mb-0"></i>
                    </div>
                </div>
                <h6 class="font-weight-bold text-dark mb-2">Location Intelligence</h6>
                <div class="p-3 bg-light rounded-lg mb-3 text-left">
                    <label class="small text-muted font-weight-bold text-uppercase mb-1 d-block">Registered Address</label>
                    <p id="verify-location-name" class="text-dark small mb-2 font-weight-600" style="line-height: 1.4;"></p>
                    <label class="small text-muted font-weight-bold text-uppercase mb-1 d-block">GPS Coordinates</label>
                    <p id="verify-location-coords" class="text-monospace text-primary small mb-0 font-weight-bold"></p>
                </div>
                
                <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">
                    <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3 py-2 shadow-sm" id="btn-show-interactive-map" style="border-radius: 8px;">
                        <i class="fe fe-eye mr-1"></i> View Map
                    </button>
                    <a href="#" target="_blank" id="btn-external-gmap" class="btn btn-sm btn-outline-dark font-weight-bold px-3 py-2" style="border-radius: 8px;">
                        Google Maps
                    </a>
                </div>
                <button type="button" class="btn btn-link btn-sm text-muted mt-3 font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Map Modal (Stage 2) -->
<div class="modal fade" id="interactiveMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold text-dark"><i class="fe fe-maximize-2 mr-2 text-primary"></i>Spatial Intelligence View</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0" style="height: 500px; position: relative;">
                <div id="modal-interactive-map" style="width: 100%; height: 100%; background: #f8f9fa;"></div>
                <div id="map-control-overlay" style="position: absolute; bottom: 20px; right: 20px; z-index: 100;">
                   <a href="#" target="_blank" id="modal-external-gmap-btn" class="btn btn-white btn-sm shadow-sm font-weight-bold border rounded-pill">
                       <i class="fe fe-external-link mr-1"></i> Open in GMap
                   </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelector = document.getElementById('user-selector');
    const dateSelector = document.getElementById('date-selector');

    function applyFilters() {
        const userId = userSelector ? userSelector.value : 'all';
        const date = dateSelector ? dateSelector.value : '';
        window.location.href = `reports?user_id=${userId}&date=${date}`;
    }

    if (userSelector) userSelector.addEventListener('change', applyFilters);
    if (dateSelector) dateSelector.addEventListener('change', applyFilters);
});

function openActionModal(actionStr, recordId) {
    const isApproval = actionStr.includes('approve');
    const isMeeting = actionStr.includes('Meeting');
    
    // Auto-hide intelligence modal if it's open
    $('#intelligenceModal').modal('hide');
    
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

    function viewIntelligence(id) {
        const modal = $('#intelligenceModal');
        const loading = $('#intel-loading');
        const content = $('#intel-content');
        
        content.hide();
        loading.show();
        modal.modal('show');
        
        fetch('reports?action=getMeetingDetails&id=' + id)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const m = res.data;
                    loading.hide();
                    content.fadeIn();
                    
                    document.getElementById('intel-session-info').textContent = `Verified Session ID: #FT-${m.id}`;
                    document.getElementById('intel-hospital').textContent = m.hospital_office_name;
                    document.getElementById('intel-address').textContent = m.address;
                    document.getElementById('intel-client').textContent = m.client_name;
                    document.getElementById('intel-type').textContent = m.meeting_type;
                    document.getElementById('intel-time').textContent = new Date(m.meeting_time).toLocaleString('en-IN', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: true
                    });
                    document.getElementById('intel-notes').textContent = m.notes || 'No detailed notes provided.';
                    document.getElementById('intel-outcome').textContent = m.outcome;
                    document.getElementById('intel-user').textContent = m.user_name;
                    document.getElementById('intel-approver').textContent = m.approver_name || 'Verification Pending';
                    
                    const selfie = document.getElementById('intel-selfie');
                    if (m.selfie_path) {
                        selfie.src = m.selfie_path;
                        document.getElementById('intel-selfie-container').style.display = 'block';
                    } else {
                        document.getElementById('intel-selfie-container').style.display = 'none';
                    }
                    
                    // Controls logic
                    const controls = document.querySelector('.intel-controls');
                    const statusPill = document.getElementById('intel-status-pill');
                    
                    if (m.status === 'Pending') {
                        controls.style.display = 'block';
                        statusPill.innerHTML = '';
                        
                        document.getElementById('intel-approve-btn').onclick = () => openActionModal('approveMeeting', m.id);
                        document.getElementById('intel-reject-btn').onclick = () => openActionModal('rejectMeeting', m.id);
                    } else {
                        controls.style.display = 'none';
                        const sClass = m.status === 'Approved' ? 'badge-success' : 'badge-danger';
                        statusPill.innerHTML = `<span class="badge ${sClass} px-4 py-2 font-weight-bold" style="font-size: 1rem;">${m.status.toUpperCase()}</span>`;
                    }

                    // Map Trigger in Intelligence Modal
                    document.getElementById('intel-map-btn').onclick = () => {
                        modal.modal('hide');
                        setTimeout(() => triggerLocationPopup(m), 400);
                    };
                } else {
                    alert('Error loading meeting intelligence: ' + res.message);
                    modal.modal('hide');
                }
            })
            .catch(err => {
                console.error(err);
                alert('A network error occurred while fetching intelligence.');
                modal.modal('hide');
            });
    }

    // --- Dynamic Location Verification Logic ---
    let platform = new H.service.Platform({'apikey': window.HERE_API_KEY});
    let modalMap, modalMarker;

    window.triggerLocationPopup = (data) => {
        const modal = $('#locationVerificationModal');
        document.getElementById('verify-location-name').textContent = data.address || "Field Position Registered";
        document.getElementById('verify-location-coords').textContent = `${data.latitude}, ${data.longitude}`;
        
        document.getElementById('btn-external-gmap').href = `https://www.google.com/maps?q=${data.latitude},${data.longitude}`;
        document.getElementById('btn-show-interactive-map').onclick = () => {
            modal.modal('hide');
            setTimeout(() => showInteractiveMap(parseFloat(data.latitude), parseFloat(data.longitude)), 300);
        };
        modal.modal('show');
    };

    window.showInteractiveMap = (lat, lng) => {
        const modal = $('#interactiveMapModal');
        document.getElementById('modal-external-gmap-btn').href = `https://www.google.com/maps?q=${lat},${lng}`;
        modal.modal('show');
        
        setTimeout(() => {
            const mapContainer = document.getElementById('modal-interactive-map');
            if (!modalMap) {
                const layers = platform.createDefaultLayers();
                modalMap = new H.Map(mapContainer, layers.vector.normal.map, {
                    zoom: 16, center: { lat, lng }
                });
                new H.mapevents.Behavior(new H.mapevents.MapEvents(modalMap));
                H.ui.UI.createDefault(modalMap, layers);
                modalMarker = new H.map.Marker({ lat, lng });
                modalMap.addObject(modalMarker);
            } else {
                modalMap.setCenter({ lat, lng });
                modalMarker.setGeometry({ lat, lng });
            }
        }, 350);
    };
</script>
