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

                <div class="card shadow-sm border-0 overflow-hidden mb-4 rounded-lg">
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
                                        <tr><td colspan="5" class="text-center py-5 text-muted bg-white">No records found for this month.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($breakdown as $row): ?>
                                            <tr>
                                                <td class="pl-4 py-3 font-weight-700 text-dark"><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                                <td class="py-3 font-weight-600 text-primary"><?php echo number_format($row['total_distance'], 1); ?> KM</td>
                                                <td class="py-3">
                                                    <span class="badge badge-soft-primary px-2 py-1 font-weight-700" style="font-size:0.7rem;">
                                                        <?php echo $row['meeting_count']; ?> INTERACTIONS
                                                    </span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge <?php echo ($row['status'] == 'Approved') ? 'badge-success' : (($row['status'] == 'Rejected') ? 'badge-danger' : 'badge-warning'); ?> px-2 py-1 font-weight-bold" style="font-size:0.7rem; border-radius:4px;">
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
                            <span class="text-muted font-weight-600" style="font-size:0.85rem;" id="monthlyPageInfo"></span>
                            <nav><ul class="pagination pagination-sm mb-0" id="monthlyPagination"></ul></nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; }
.bg-soft-success { background-color: rgba(0, 184, 148, 0.12); color: #00b894; }
.bg-soft-warning { background-color: rgba(253, 196, 39, 0.15); color: #f9a02e; }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.12); color: #17a2b8; }
.bg-light-50 { background-color: rgba(248, 249, 252, 0.5); }
.font-weight-600 { font-weight: 600; }
.font-weight-700 { font-weight: 700; }
.font-weight-800 { font-weight: 800; }
.card-title { letter-spacing: 0.02em; }
</style>
<script>
(function(){
    const PER_PAGE = 10; let page = 1;
    const tbody = document.getElementById('monthlyBody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
    if (!rows.length) return;
    function render() {
        const total = rows.length, pages = Math.max(1, Math.ceil(total / PER_PAGE));
        const start = (page-1)*PER_PAGE, end = Math.min(start+PER_PAGE, total);
        Array.from(tbody.querySelectorAll('tr')).forEach(r => r.style.display = rows.includes(r) ? 'none' : '');
        rows.forEach((r,i) => r.style.display = (i>=start && i<end) ? '' : 'none');
        document.getElementById('monthlyPageInfo').textContent = `Showing ${start+1}–${end} of ${total}`;
        const ul = document.getElementById('monthlyPagination'); ul.innerHTML = '';
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
