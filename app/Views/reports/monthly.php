<?php include dirname(__DIR__).'/layout/header.php'; ?>
<?php include dirname(__DIR__).'/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-10">
                
                <!-- Compact Intelligence Header -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                    <div class="mb-3 mb-md-0">
                        <h2 class="h3 mb-0 page-title font-weight-bold"><?php echo $title; ?></h2>
                        <p class="text-muted mb-0">Strategic overview of field performance and fiscal data.</p>
                    </div>

                    <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                        <!-- Miniaturized Desktop Filters -->
                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; height: 36px; border: 1px solid #eef0f2; width: auto;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0 pl-2 pr-1"><i class="fe fe-calendar text-primary" style="font-size: 0.8rem;"></i></span>
                            </div>
                            <select id="month-selector" class="form-control border-0 px-2" style="font-size: 0.85rem; font-weight: 600; height: 34px; width: 110px;">
                                <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?php echo sprintf('%02d', $i); ?>" <?php echo $i == $selectedMonth ? 'selected' : ''; ?>>
                                        <?php echo date('F', mktime(0,0,0,$i,1)); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <select id="year-selector" class="form-control border-0 px-2" style="font-size: 0.85rem; font-weight: 600; height: 34px; width: 85px; border-left: 1px solid #eee !important;">
                                <?php for($y=date('Y'); $y>=2024; $y--): ?>
                                    <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <?php if(!empty($users)): ?>
                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; height: 36px; border: 1px solid #eef0f2; width: auto;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0 pl-2 pr-1"><i class="fe fe-users text-info" style="font-size: 0.8rem;"></i></span>
                            </div>
                            <select id="user-selector" class="form-control border-0 px-2" style="min-width: 160px; font-size: 0.85rem; font-weight: 600; height: 34px;">
                                <option value="all">All Personnel</option>
                                <?php foreach($users as $u): ?>
                                    <option value="<?php echo $u['id']; ?>" <?php echo $u['id'] == $selectedUser ? 'selected' : ''; ?>><?php echo htmlspecialchars($u['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <a id="export-link" href="#" class="btn btn-dark font-weight-bold px-3 d-flex align-items-center" style="border-radius: 8px; height: 36px; font-size: 0.85rem;">
                            <i class="fe fe-download mr-2"></i> Export
                        </a>
                    </div>
                </div>

                <?php if($is_aggregate): ?>
                    <!-- TEAM AGGREGATE VIEW (Leaderboard) -->
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                                <div class="card-header bg-white border-0 py-3">
                                    <h5 class="card-title mb-0 font-weight-bold"><i class="fe fe-trending-up mr-2 text-primary"></i> Personnel Monthly leadership</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="bg-light small text-uppercase font-weight-bold text-muted">
                                                <tr>
                                                    <th class="pl-4 py-3">Employee</th>
                                                    <th class="py-3 text-center">Active Days</th>
                                                    <th class="py-3 text-center">Meetings</th>
                                                    <th class="py-3 text-center">Distance (KM)</th>
                                                    <th class="pr-4 py-3 text-right">Allowance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $total_dist = 0; $total_amt = 0; $total_meet = 0;
                                                foreach($user_aggregates as $ua): 
                                                    // Merge with travel and meeting data
                                                    $ta = array_values(array_filter($travel_aggregates, fn($x) => $x['user_id'] == $ua['user_id']))[0] ?? ['total_distance'=>0, 'total_allowance'=>0];
                                                    $total_dist += $ta['total_distance'];
                                                    $total_amt += $ta['total_allowance'];
                                                    $total_meet += $ua['meeting_count'];
                                                ?>
                                                <tr>
                                                    <td class="pl-4 py-3 font-weight-bold text-dark">
                                                        <?php echo htmlspecialchars($ua['user_name']); ?>
                                                        <div class="text-muted small font-weight-normal">ID: #USR-<?php echo $ua['user_id']; ?></div>
                                                    </td>
                                                    <td class="py-3 text-center font-weight-bold"><?php echo $ua['active_days']; ?> Days</td>
                                                    <td class="py-3 text-center">
                                                        <span class="badge badge-soft-primary px-3 py-1 font-weight-bold" style="font-size:0.7rem; border-radius:6px;"><?php echo $ua['meeting_count']; ?> MTGS</span>
                                                    </td>
                                                    <td class="py-3 text-center font-weight-bold"><?php echo number_format($ta['total_distance'], 1); ?> KM</td>
                                                    <td class="pr-4 py-3 text-right font-weight-800 text-dark">₹<?php echo number_format($ta['total_allowance'], 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot class="bg-dark text-white font-weight-bold">
                                                <tr>
                                                    <td class="pl-4 py-3">TEAM TOTALS</td>
                                                    <td class="py-3"></td>
                                                    <td class="py-3 text-center"><?php echo $total_meet; ?> MTGS</td>
                                                    <td class="py-3 text-center"><?php echo number_format($total_dist, 1); ?> KM</td>
                                                    <td class="pr-4 py-3 text-right">₹<?php echo number_format($total_amt, 2); ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- INDIVIDUAL PERFORMANCE VIEW -->
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
                                    <thead style="background: #fbfcfe;" class="text-muted small text-uppercase font-weight-bold text-muted">
                                        <tr>
                                            <th class="pl-4 py-3">Date</th>
                                            <th class="py-3">Travel (KM)</th>
                                            <th class="py-3">Meetings</th>
                                            <th class="py-3">Audit Status</th>
                                            <th class="pr-4 py-3 text-right">Allowance</th>
                                        </tr>
                                    </thead>
                                    <tbody id="monthlyBody">
                                        <?php if(empty($breakdown)): ?>
                                            <tr><td colspan="5" class="text-center py-5 text-muted bg-white">No activity breakdown found for this period.</td></tr>
                                        <?php else: ?>
                                            <?php foreach($breakdown as $row): ?>
                                                <tr>
                                                    <td class="pl-4 py-3 font-weight-bold text-dark"><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                                    <td class="py-3 font-weight-bold text-primary"><?php echo number_format($row['total_distance'], 1); ?> KM</td>
                                                    <td class="py-3">
                                                        <span class="badge badge-soft-primary px-3 py-1 font-weight-bold" style="font-size:0.7rem; border-radius:6px;">
                                                            <?php echo $row['meeting_count']; ?> MTGS
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
                <?php endif; ?>

                <!-- Individual Interaction Logs (Always shown at bottom for selected month context) -->
                <div class="card shadow-sm border-0 overflow-hidden mb-4 rounded-lg">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark font-weight-bold"><i class="fe fe-activity mr-2 text-info"></i> Interaction intelligence Log</h5>
                        <span class="badge badge-soft-info px-3 py-1"><?php echo count($meetings_list); ?> TOTAL ENTRIES</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: #fbfcfe;" class="text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4 py-3 border-0">Timestamp</th>
                                        <?php if($is_aggregate): ?><th class="border-0">Employee</th><?php endif; ?>
                                        <th class="border-0">Client Information</th>
                                        <th class="border-0">Type</th>
                                        <th class="pr-4 text-right border-0">Intelligence</th>
                                    </tr>
                                </thead>
                                <tbody id="individualLogsBody">
                                    <?php if(empty($meetings_list)): ?>
                                        <tr><td colspan="<?php echo $is_aggregate ? 5 : 4; ?>" class="text-center py-5 text-muted bg-white">No interaction logs recorded for this period.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($meetings_list as $m): ?>
                                            <tr>
                                                <td class="pl-4 py-3">
                                                    <div class="text-dark font-weight-bold"><?php echo date('d M, h:i A', strtotime($m['meeting_time'])); ?></div>
                                                    <div class="text-muted small">Validated Record</div>
                                                </td>
                                                <?php if($is_aggregate): ?>
                                                <td class="py-3 font-weight-bold text-muted"><?php echo htmlspecialchars($m['user_name']); ?></td>
                                                <?php endif; ?>
                                                <td class="py-3">
                                                    <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                                    <div class="small text-muted"><i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars($m['hospital_office_name']); ?></div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge badge-soft-primary px-2 py-1"><?php echo strtoupper($m['meeting_type']); ?></span>
                                                </td>
                                                <td class="pr-4 py-3 text-right">
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-weight-bold" onclick="viewIntelligence(<?php echo $m['id']; ?>)">
                                                        <i class="fe fe-eye mr-1"></i> Intelligence
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

<!-- Modals (Intelligence Detail and Action) -->
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
                            <div class="small text-muted mb-4">IST Timeline Verified</div>
                            <div class="w-100 p-3 bg-white rounded-lg shadow-sm text-left">
                                <label class="text-muted small font-weight-bold text-uppercase mb-1">Personnel</label>
                                <div class="font-weight-700 text-dark" id="intel-user"></div>
                                <hr class="my-2 opacity-50">
                                <label class="text-muted small font-weight-bold text-uppercase mb-1">Authority</label>
                                <div class="font-weight-700 text-primary" id="intel-approver">Verification Pending</div>
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
                                <label class="text-info small font-weight-bold text-uppercase mb-1 d-block"><i class="fe fe-message-square mr-2"></i>Executive Summary</label>
                                <p class="text-dark font-weight-500 mb-0" id="intel-notes" style="font-style: italic;"></p>
                            </section>
                            <section class="mb-4">
                                <label class="text-muted small font-weight-bold text-uppercase mb-1 d-block"><i class="fe fe-trending-up mr-2"></i>Actionable Outcome</label>
                                <p class="text-dark font-weight-700" id="intel-outcome"></p>
                            </section>
                            <div id="intel-action-footer" class="mt-5">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div id="intel-status-pill"></div>
                                    <div class="intel-controls" style="display:none;">
                                        <button type="button" id="intel-approve-btn" class="btn btn-success btn-lg rounded-pill px-5 font-weight-bold shadow">Approve</button>
                                        <button type="button" id="intel-reject-btn" class="btn btn-outline-danger btn-lg rounded-pill px-4 font-weight-bold ml-2">Reject</button>
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

<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="actionModalTitle">Verify Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="reports" method="POST" id="virtualActionForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="actionModalId">
                    <div class="form-group mb-0">
                        <textarea name="reason" class="form-control bg-light border-0 px-3 py-2" rows="3" placeholder="Enter comments (optional)..." style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary btn-block rounded-pill font-weight-bold shadow-sm" id="actionModalBtn">Confirm Action</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function syncUI() {
    const m = document.getElementById('month-selector').value;
    const y = document.getElementById('year-selector').value;
    const u = document.getElementById('user-selector')?.value || '<?php echo $_SESSION['user_id']; ?>';
    
    // Update export link
    document.getElementById('export-link').href = `reports?action=export&type=monthly&user_id=${u}&month=${m}&year=${y}&format=csv`;

    const reload = () => {
        window.location.href = `reports?action=monthly&user_id=${u}&month=${m}&year=${y}`;
    };

    document.getElementById('month-selector').onchange = reload;
    document.getElementById('year-selector').onchange = reload;
    if(document.getElementById('user-selector')) document.getElementById('user-selector').onchange = reload;
}

function makePaginator(tbodyId, infoId, paginationId) {
    const PER_PAGE = 10;
    let page = 1;
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
    if (!rows.length) return;
    function render() {
        const total = rows.length, pages = Math.max(1, Math.ceil(total / PER_PAGE));
        if(page > pages) page = pages;
        const start = (page - 1) * PER_PAGE, end = Math.min(start + PER_PAGE, total);
        Array.from(tbody.querySelectorAll('tr')).forEach(r => r.style.display = rows.includes(r) ? 'none' : '');
        rows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
        const info = document.getElementById(infoId); if(info) info.textContent = `Showing ${start+1}–${end} of ${total}`;
        const ul = document.getElementById(paginationId); if(!ul) return; ul.innerHTML = '';
        if(pages <= 1) return;
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

function viewIntelligence(id) {
    const modal = $('#intelligenceModal');
    const loading = $('#intel-loading');
    const content = $('#intel-content');
    content.hide(); loading.show(); modal.modal('show');
    fetch('reports?action=getMeetingDetails&id=' + id)
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const m = res.data;
                loading.hide(); content.fadeIn();
                document.getElementById('intel-session-info').textContent = `Verified Session ID: #FT-${m.id}`;
                document.getElementById('intel-hospital').textContent = m.hospital_office_name;
                document.getElementById('intel-address').textContent = m.address;
                document.getElementById('intel-client').textContent = m.client_name;
                document.getElementById('intel-type').textContent = m.meeting_type;
                document.getElementById('intel-time').textContent = new Date(m.meeting_time).toLocaleString('en-IN', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit', hour12:true});
                document.getElementById('intel-notes').textContent = m.notes || 'No detailed notes.';
                document.getElementById('intel-outcome').textContent = m.outcome;
                document.getElementById('intel-user').textContent = m.user_name;
                document.getElementById('intel-approver').textContent = m.approver_name || 'Verification Pending';
                const selfie = document.getElementById('intel-selfie');
                if (m.selfie_path) { selfie.src = m.selfie_path; document.getElementById('intel-selfie-container').style.display='block'; }
                else { document.getElementById('intel-selfie-container').style.display='none'; }
                const role = '<?php echo $_SESSION['role']; ?>';
                const controls = document.querySelector('.intel-controls');
                const statusPill = document.getElementById('intel-status-pill');
                if (m.status === 'Pending' && (role === 'Admin' || role === 'Manager')) {
                    controls.style.display = 'block'; statusPill.innerHTML = '';
                    document.getElementById('intel-approve-btn').onclick = () => openActionModal('approveMeeting', m.id);
                    document.getElementById('intel-reject-btn').onclick = () => openActionModal('rejectMeeting', m.id);
                } else {
                    controls.style.display = 'none';
                    const sClass = m.status === 'Approved' ? 'badge-success' : (m.status === 'Rejected' ? 'badge-danger' : 'badge-warning');
                    statusPill.innerHTML = `<span class="badge ${sClass} px-4 py-2 font-weight-bold" style="font-size: 1rem;">${m.status.toUpperCase()}</span>`;
                }
            }
        });
}

function openActionModal(actionStr, recordId) {
    $('#intelligenceModal').modal('hide');
    document.getElementById('actionModalId').value = recordId;
    document.getElementById('virtualActionForm').action = 'reports?action=' + actionStr;
    $('#actionModal').modal('show');
}

document.addEventListener('DOMContentLoaded', () => {
    syncUI();
    makePaginator('monthlyBody', 'monthlyPageInfo', 'monthlyPagination');
    makePaginator('individualLogsBody', 'logsPageInfo', 'logsPagination');
});
</script>
