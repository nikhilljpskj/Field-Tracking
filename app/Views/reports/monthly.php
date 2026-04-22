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
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm border-0 text-center py-4">
                            <div class="card-body">
                                <p class="small text-muted text-uppercase font-weight-bold mb-2">Total KM</p>
                                <h2 class="mb-0 font-weight-bold text-primary"><?php echo number_format($travel['total_distance'] ?? 0, 1); ?></h2>
                                <small class="text-muted">Distance Covered</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm border-0 text-center py-4">
                            <div class="card-body">
                                <p class="small text-muted text-uppercase font-weight-bold mb-2">Earnings</p>
                                <h2 class="mb-0 font-weight-bold text-success">₹<?php echo number_format($travel['total_allowance'] ?? 0, 0); ?></h2>
                                <small class="text-muted">Travel Allowance</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm border-0 text-center py-4">
                            <div class="card-body">
                                <p class="small text-muted text-uppercase font-weight-bold mb-2">Meetings</p>
                                <h2 class="mb-0 font-weight-bold text-warning"><?php echo $meetings['count'] ?? 0; ?></h2>
                                <small class="text-muted">Interactions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm border-0 text-center py-4">
                            <div class="card-body">
                                <p class="small text-muted text-uppercase font-weight-bold mb-2">Active Days</p>
                                <h2 class="mb-0 font-weight-bold text-info"><?php echo $meetings['active_days'] ?? 0; ?></h2>
                                <small class="text-muted">Field Activity</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 overflow-hidden mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">Daily Breakdown</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Date</th>
                                        <th>Distance (KM)</th>
                                        <th>Meetings</th>
                                        <th>Status</th>
                                        <th class="pr-4 text-right">Allowance</th>
                                    </tr>
                                </thead>
                                <tbody id="monthlyBody">
                                    <?php if(empty($breakdown)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No records found for this month.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($breakdown as $row): ?>
                                            <tr>
                                                <td class="pl-4 font-weight-600"><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                                <td class="font-weight-bold"><?php echo number_format($row['total_distance'], 1); ?></td>
                                                <td>
                                                    <span class="badge badge-soft-info px-2 py-1">
                                                        <?php echo $row['meeting_count']; ?> Meetings
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo ($row['status'] == 'Approved') ? 'badge-success' : (($row['status'] == 'Rejected') ? 'badge-danger' : 'badge-warning'); ?> px-2 py-1">
                                                        <?php echo $row['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="pr-4 text-right font-weight-700 text-dark">
                                                    ₹<?php echo number_format($row['allowance_earned'], 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                            <span class="text-muted small" id="monthlyPageInfo"></span>
                            <nav><ul class="pagination pagination-sm mb-0" id="monthlyPagination"></ul></nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.badge-soft-info { background-color: rgba(23, 162, 184, 0.1); color: #17a2b8; }
.font-weight-600 { font-weight: 600; }
.font-weight-700 { font-weight: 700; }
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
