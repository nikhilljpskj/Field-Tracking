<?php include dirname(__DIR__).'/layout/header.php'; ?>
<?php include dirname(__DIR__).'/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Monthly Performance Summary</h2>
                        <p class="text-muted">Consolidated view of your field achievements for <strong><?php echo date('F Y'); ?></strong>.</p>
                    </div>
                    <div class="btn-group">
                        <a href="reports?action=export&type=monthly&format=csv" class="btn btn-outline-success font-weight-600">
                            <i class="fe fe-file-text mr-1"></i> Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-primary shadow">
                            <i class="fe fe-printer mr-1"></i> Print PDF
                        </button>
                    </div>
                </div>

                <!-- Monthly Stats Grid -->
                <div class="row">
                    <div class="col-6 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body px-2">
                                <div class="bg-soft-primary rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px; height:48px;">
                                    <i class="fe fe-map-pin text-primary"></i>
                                </div>
                                <p class="small text-muted text-uppercase font-weight-bold mb-1" style="font-size:0.65rem; letter-spacing:0.05em;">Total Distance</p>
                                <h2 class="mb-0 font-weight-bold text-dark"><?php echo number_format($travel['total_distance'] ?? 0, 1); ?> <small class="h6 text-muted">KM</small></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body px-2">
                                <div class="bg-soft-success rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px; height:48px;">
                                    <i class="fe fe-dollar-sign text-success"></i>
                                </div>
                                <p class="small text-muted text-uppercase font-weight-bold mb-1" style="font-size:0.65rem; letter-spacing:0.05em;">Total Earnings</p>
                                <h2 class="mb-0 font-weight-bold text-success">₹<?php echo number_format($travel['total_allowance'] ?? 0, 0); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body px-2">
                                <div class="bg-soft-warning rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px; height:48px;">
                                    <i class="fe fe-users text-warning"></i>
                                </div>
                                <p class="small text-muted text-uppercase font-weight-bold mb-1" style="font-size:0.65rem; letter-spacing:0.05em;">Meetings</p>
                                <h2 class="mb-0 font-weight-bold text-dark"><?php echo $meetings['count'] ?? 0; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body px-2">
                                <div class="bg-soft-info rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px; height:48px;">
                                    <i class="fe fe-activity text-info"></i>
                                </div>
                                <p class="small text-muted text-uppercase font-weight-bold mb-1" style="font-size:0.65rem; letter-spacing:0.05em;">Active Days</p>
                                <h2 class="mb-0 font-weight-bold text-dark"><?php echo $meetings['active_days'] ?? 0; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 overflow-hidden mb-5 rounded-lg">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0 font-weight-bold"><i class="fe fe-calendar mr-2 text-primary"></i> Daily Activity Breakdown</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: #fbfcfe;" class="text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4 py-3">Date</th>
                                        <th class="py-3">Travel (KM)</th>
                                        <th class="py-3">Meetings</th>
                                        <th class="py-3">Audit</th>
                                        <th class="pr-4 py-3 text-right">Allowance</th>
                                    </tr>
                                </thead>
                                <tbody id="monthlyBody">
                                    <?php if(empty($breakdown)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted bg-white">No activity breakdown found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($breakdown as $row): ?>
                                            <tr>
                                                <td class="pl-4 py-3 font-weight-700 text-dark"><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                                <td class="py-3 font-weight-600 text-primary"><?php echo number_format($row['total_distance'], 1); ?> KM</td>
                                                <td class="py-3">
                                                    <span class="badge badge-soft-primary px-3 py-1 font-weight-700" style="font-size:0.7rem; border-radius:6px;">
                                                        <?php echo $row['meeting_count']; ?> INTERACTIONS
                                                    </span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge <?php echo ($row['status'] == 'Approved') ? 'badge-success' : (($row['status'] == 'Rejected') ? 'badge-danger' : 'badge-warning'); ?> px-3 py-1 font-weight-bold" style="font-size:0.7rem; border-radius:6px;">
                                                        <?php echo strtoupper($row['status']); ?>
                                                    </span>
                                                </td>
                                                <td class="pr-4 py-3 text-right font-weight-800 text-dark" style="font-size:0.95rem;">
                                                    ₹<?php echo number_format($row['allowance_earned'], 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light-50">
                            <span class="text-muted font-weight-600 small" id="monthlyPageInfo"></span>
                            <nav><ul class="pagination pagination-sm mb-0" id="monthlyPagination"></ul></nav>
                        </div>
                    </div>
                </div>

                <!-- Individual Logs Section -->
                <div class="card shadow-sm border-0 overflow-hidden mb-4 rounded-lg">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark font-weight-bold"><i class="fe fe-activity mr-2 text-info"></i> Individual Interaction Logs</h5>
                        <span class="badge badge-soft-info px-3 py-1"><?php echo count($meetings_list); ?> ENTRIES</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: #fbfcfe;" class="text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4 py-3 border-0">Timestamp</th>
                                        <th class="border-0">Client Information</th>
                                        <th class="border-0">Type</th>
                                        <th class="pr-4 text-right border-0">Intelligence</th>
                                    </tr>
                                </thead>
                                <tbody id="individualLogsBody">
                                    <?php if(empty($meetings_list)): ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted bg-white">No individual logs recorded for this month.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($meetings_list as $m): ?>
                                            <tr>
                                                <td class="pl-4 py-3">
                                                    <div class="text-dark font-weight-700"><?php echo date('d M, h:i A', strtotime($m['meeting_time'])); ?></div>
                                                    <div class="text-muted small">Validated Record</div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="font-weight-700 text-dark"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                                    <div class="small text-muted"><i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars($m['hospital_office_name']); ?></div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge badge-soft-primary px-2 py-1"><?php echo strtoupper($m['meeting_type']); ?></span>
                                                </td>
                                                <td class="pr-4 py-3 text-right">
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-weight-bold" onclick="viewIntelligence(<?php echo $m['id']; ?>)">
                                                        <i class="fe fe-eye mr-1"></i> View Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light-50">
                            <span class="text-muted font-weight-600 small" id="logsPageInfo"></span>
                            <nav><ul class="pagination pagination-sm mb-0" id="logsPagination"></ul></nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

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
    makePaginator('monthlyBody', 'monthlyPageInfo', 'monthlyPagination');
    makePaginator('individualLogsBody', 'logsPageInfo', 'logsPagination');
});

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
                alert('Error loading intelligence: ' + res.message);
                modal.modal('hide');
            }
        })
        .catch(err => {
            console.error(err);
            alert('A network error occurred.');
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
