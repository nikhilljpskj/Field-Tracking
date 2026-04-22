<?php include dirname(__DIR__).'/layout/header.php'; ?>
<?php include dirname(__DIR__).'/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Daily Activity Summary</h2>
                        <p class="text-muted">A comprehensive view of your field actions for today, <?php echo date('d M Y'); ?>.</p>
                    </div>
                    <div class="btn-group">
                        <a href="reports?action=export&type=daily&format=csv" class="btn btn-outline-success font-weight-600">
                            <i class="fe fe-file-text mr-1"></i> Excel
                        </a>
                        <a href="reports?action=export&type=daily&format=pdf" class="btn btn-outline-danger font-weight-600">
                            <i class="fe fe-file mr-1"></i> PDF
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Attendance Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="card-title mb-0 text-primary text-uppercase small font-weight-bold"><i class="fe fe-clock mr-2"></i> Shift Attendance</h6>
                            </div>
                            <div class="card-body">
                                <?php if($attendance): ?>
                                    <div class="p-3 bg-light rounded-lg mb-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="circle circle-sm bg-success text-white mr-3 shadow-sm">
                                                <i class="fe fe-log-in"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block text-uppercase" style="font-size:0.65rem; letter-spacing:0.05em;">Checked In</small>
                                                <span class="font-weight-bold text-dark" style="font-size:1.1rem;"><?php echo date('h:i A', strtotime($attendance['check_in_time'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="circle circle-sm <?php echo $attendance['check_out_time'] ? 'bg-danger text-white' : 'bg-warning text-white'; ?> mr-3 shadow-sm">
                                                <i class="fe fe-log-out"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block text-uppercase" style="font-size:0.65rem; letter-spacing:0.05em;">Checked Out</small>
                                                <span class="font-weight-bold text-dark" style="font-size:1.1rem;"><?php echo $attendance['check_out_time'] ? date('h:i A', strtotime($attendance['check_out_time'])) : 'Active Now'; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="small text-muted d-flex align-items-start px-2">
                                        <i class="fe fe-map-pin mr-2 mt-1 text-primary"></i> 
                                        <span><?php echo htmlspecialchars($attendance['check_in_address']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <div class="bg-light rounded-circle shadow-none mb-3 d-inline-flex align-items-center justify-content-center" style="width:60px; height:60px;">
                                            <i class="fe fe-alert-circle fe-24 text-muted"></i>
                                        </div>
                                        <p class="text-muted font-weight-bold mb-0">No records found</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow border-0 h-100 text-white" style="background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
                            <div class="card-header bg-transparent border-0 py-3">
                                <h6 class="card-title mb-0 text-white-50 text-uppercase small font-weight-bold"><i class="fe fe-map mr-2"></i> Today's Travel</h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center text-center">
                                <div class="mb-2">
                                    <span class="display-4 font-weight-bold"><?php echo number_format($travel['total_distance'] ?? 0, 1); ?></span>
                                    <span class="h5 font-weight-normal opacity-75 ml-1">KM</span>
                                </div>
                                <p class="text-white-50 small mb-4 font-weight-600">Calculated Intelligence Audit</p>
                                
                                <div class="mx-auto px-4 py-2 bg-white-20 rounded-pill d-inline-block shadow-sm">
                                    <span class="h5 mb-0 font-weight-bold">₹<?php echo number_format($travel['allowance_earned'] ?? 0, 0); ?> EARNED</span>
                                </div>
                                <div class="mt-3">
                                    <span class="badge badge-pill bg-white-10 text-white px-3 py-1 font-weight-bold" style="font-size:0.7rem; letter-spacing:0.04em;">
                                        STATUS: <?php echo strtoupper($travel['status'] ?? 'PENDING'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meetings Summary -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0 text-success text-uppercase small font-weight-bold"><i class="fe fe-users mr-2"></i> Productivity</h6>
                                <span class="badge badge-soft-success px-2 py-1"><?php echo count($meetings); ?> / 5 Done</span>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-2 mb-4">
                                    <div class="h1 mb-1 font-weight-bold text-dark"><?php echo count($meetings); ?></div>
                                    <p class="text-muted small font-weight-bold text-uppercase" style="letter-spacing:0.05em;">Client Interactions</p>
                                </div>
                                <div class="progress progress-sm mb-2 rounded-pill shadow-none bg-light" style="height: 8px;">
                                    <div class="progress-bar bg-success rounded-pill" style="width: <?php echo min((count($meetings) / 5) * 100, 100); ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted x-small font-weight-bold mt-2">
                                    <span style="font-size:0.65rem;">0</span>
                                    <span style="font-size:0.65rem;">DAILY TARGET: 5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Table -->
                <div class="card shadow-sm border-0 overflow-hidden mb-4 rounded-lg">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark font-weight-bold"><i class="fe fe-activity mr-2 text-primary"></i> Interaction Log</h5>
                        <div class="small text-muted font-weight-bold"><?php echo isset($meetings) ? count($meetings) : 0; ?> Entries Today</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: #fbfcfe;" class="text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4 py-3 border-0">Timestamp</th>
                                        <th class="border-0">Interaction Detail</th>
                                        <th class="border-0">Type</th>
                                        <th class="pr-4 text-right border-0">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="dailyMeetBody">
                                    <?php if(empty($meetings)): ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted bg-white">No meetings recorded today.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($meetings as $m): ?>
                                            <tr>
                                                <td class="pl-4 py-3">
                                                    <div class="text-dark font-weight-700" style="font-size:0.95rem;"><?php echo date('h:i A', strtotime($m['meeting_time'])); ?></div>
                                                    <div class="text-muted" style="font-size:0.75rem;">Verified Entry</div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="font-weight-700 text-dark"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                                    <div class="small text-muted mb-1"><i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars($m['hospital_office_name']); ?></div>
                                                    <div class="p-2 rounded bg-light border-0 small text-dark mt-2" style="font-style: italic; border-left: 3px solid #dee2e6 !important;">
                                                        "<?php echo htmlspecialchars($m['outcome']); ?>"
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge badge-soft-primary px-3 py-1 font-weight-700" style="font-size:0.7rem; border-radius:6px;"><?php echo strtoupper($m['meeting_type']); ?></span>
                                                </td>
                                                <td class="pr-4 py-3 text-right">
                                                    <?php 
                                                        $statusClass = ($m['status'] == 'Approved') ? 'text-success' : (($m['status'] == 'Rejected') ? 'text-danger' : 'text-warning');
                                                        $dotClass = ($m['status'] == 'Approved') ? 'bg-success' : (($m['status'] == 'Rejected') ? 'bg-danger' : 'bg-warning');
                                                    ?>
                                                    <div class="d-flex flex-column align-items-end">
                                                        <div class="d-inline-flex align-items-center mb-2">
                                                            <span class="dot <?php echo $dotClass; ?> mr-2 shadow-sm"></span>
                                                            <span class="font-weight-800 <?php echo $statusClass; ?>" style="font-size:0.8rem; letter-spacing:0.02em;">
                                                                <?php echo strtoupper($m['status']); ?>
                                                            </span>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-light border-0 shadow-none px-3" onclick="viewIntelligence(<?php echo $m['id']; ?>)" style="border-radius:8px;">
                                                            <i class="fe fe-eye mr-1 text-primary"></i> <span class="small font-weight-bold">Details</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light-50">
                            <span class="text-muted font-weight-600" style="font-size:0.85rem;" id="dailyPageInfo"></span>
                            <nav><ul class="pagination pagination-sm mb-0" id="dailyPagination"></ul></nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.bg-soft-success { background-color: rgba(0, 184, 148, 0.12); color: #00b894; }
.bg-soft-warning { background-color: rgba(253, 196, 39, 0.15); color: #f9a02e; }
.bg-soft-danger { background-color: rgba(247, 37, 133, 0.12); color: #f72585; }
.bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; }
.bg-white-10 { background-color: rgba(255, 255, 255, 0.12); }
.bg-white-20 { background-color: rgba(255, 255, 255, 0.22); }
.bg-light-50 { background-color: rgba(248, 249, 252, 0.5); }
.circle-sm { width: 42px; height: 42px; line-height: 42px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; }
.dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; }
.font-weight-700 { font-weight: 700; }
.font-weight-800 { font-weight: 800; }
.x-small { font-size: 0.75rem; }
</style>
<script>
(function(){
    const PER_PAGE = 10; let page = 1;
    const tbody = document.getElementById('dailyMeetBody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
    if (!rows.length) return;
    function render() {
        const total = rows.length, pages = Math.max(1, Math.ceil(total / PER_PAGE));
        const start = (page-1)*PER_PAGE, end = Math.min(start+PER_PAGE, total);
        Array.from(tbody.querySelectorAll('tr')).forEach(r => r.style.display = rows.includes(r) ? 'none' : '');
        rows.forEach((r,i) => r.style.display = (i>=start && i<end) ? '' : 'none');
        document.getElementById('dailyPageInfo').textContent = `Showing ${start+1}–${end} of ${total}`;
        const ul = document.getElementById('dailyPagination'); ul.innerHTML = '';
        const prev = document.createElement('li'); prev.className='page-item'+(page===1?' disabled':'');
        prev.innerHTML='<a class="page-link" href="#">&laquo;</a>';
        prev.addEventListener('click',e=>{e.preventDefault();if(page>1){page--;render();}}); ul.appendChild(prev);
        for(let i=1;i<=pages;i++){const li=document.createElement('li');li.className='page-item'+(i===page?' active':'');
            li.innerHTML=`<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click',e=>{e.preventDefault();page=i;render();});ul.appendChild(li);}
        const next=document.createElement('li');next.className='page-item'+(page===pages?' disabled':'');
        next.innerHTML='<a class="page-link" href="#">&raquo;</a>';
        next.addEventListener('click',e=>{e.preventDefault();if(page<pages){page++;render();}});ul.appendChild(next);
    }
    render();
})();
</script>

<?php include dirname(__DIR__).'/layout/footer.php'; ?>

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
                                <h6 class="font-weight-800 text-dark mb-1" id="intel-hospital"></h6>
                                <p class="small text-muted mb-0" id="intel-address"></p>
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
                    <p id="actionModalDesc" class="text-muted small">Please provide a reason or comment for this action.</p>
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
                
                // Controls logic (only show if viewer is Admin/Manager and status is Pending)
                const role = '<?php echo $_SESSION['role']; ?>';
                const controls = document.querySelector('.intel-controls');
                const statusPill = document.getElementById('intel-status-pill');
                
                if (m.status === 'Pending' && (role === 'Admin' || role === 'Manager')) {
                    controls.style.display = 'block';
                    statusPill.innerHTML = '';
                    document.getElementById('intel-approve-btn').onclick = () => openActionModal('approveMeeting', m.id);
                    document.getElementById('intel-reject-btn').onclick = () => openActionModal('rejectMeeting', m.id);
                } else {
                    controls.style.display = 'none';
                    const sClass = m.status === 'Approved' ? 'badge-success' : (m.status === 'Rejected' ? 'badge-danger' : 'badge-warning');
                    statusPill.innerHTML = `<span class="badge ${sClass} px-4 py-2 font-weight-bold" style="font-size: 1rem;">${m.status.toUpperCase()}</span>`;
                }
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

function openActionModal(actionStr, recordId) {
    const isApproval = actionStr.includes('approve');
    $('#intelligenceModal').modal('hide');
    document.getElementById('actionModalId').value = recordId;
    document.getElementById('virtualActionForm').action = 'reports?action=' + actionStr;
    document.getElementById('actionModalTitle').innerText = isApproval ? 'Approve Record' : 'Reject Record';
    document.getElementById('actionModalTitle').style.color = isApproval ? '#28a745' : '#dc3545';
    document.getElementById('actionModalBtn').innerText = isApproval ? 'Approve' : 'Reject';
    document.getElementById('actionModalBtn').className = isApproval ? 'btn btn-success rounded-pill px-4 font-weight-bold shadow-sm' : 'btn btn-danger rounded-pill px-4 font-weight-bold shadow-sm';
    $('#actionModal').modal('show');
}
</script>
