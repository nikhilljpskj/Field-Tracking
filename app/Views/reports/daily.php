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
                                                    <div class="d-inline-flex align-items-center">
                                                        <span class="dot <?php echo $dotClass; ?> mr-2 shadow-sm"></span>
                                                        <span class="font-weight-800 <?php echo $statusClass; ?>" style="font-size:0.8rem; letter-spacing:0.02em;">
                                                            <?php echo strtoupper($m['status']); ?>
                                                        </span>
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
