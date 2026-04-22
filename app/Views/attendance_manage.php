<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid px-4">
        <!-- ══════════════════════════════════════════════ -->
        <!-- PREMIUM ADMIN HERO                              -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="row align-items-center mb-4 pt-3">
            <div class="col-md-7">
                <div class="d-flex align-items-center">
                    <div class="hero-icon-bg shadow-sm mr-3">
                        <i class="fe fe-users fe-20 text-primary"></i>
                    </div>
                    <div>
                        <h2 class="font-weight-800 text-dark mb-0">Attendance Logistics</h2>
                        <p class="text-muted small mb-0"><i class="fe fe-activity mr-1 text-primary"></i> Real-time operational audit of field personnel.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-right mt-3 mt-md-0">
                <div class="d-inline-flex align-items-center bg-white shadow-sm rounded-pill px-3 py-2 border">
                    <span class="dot-pulse mr-2"></span>
                    <span class="small font-weight-700 text-dark"><?php echo date('h:i A'); ?> IST</span>
                </div>
            </div>
        </div>

        <?php if(isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4 anime-fade-in"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>

        <!-- Operational Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="admin-stats-card bg-primary text-white">
                    <div class="stats-icon-overlay"><i class="fe fe-users"></i></div>
                    <div class="stats-label text-white-50">On-Duty Now</div>
                    <div class="stats-value h2 mb-0 font-weight-800"><?php 
                        $onDuty = array_filter($records, function($r) { return empty($r['check_out_time']); });
                        echo count($onDuty);
                    ?></div>
                    <div class="stats-trend small font-weight-600 mt-1"><i class="fe fe-trending-up mr-1"></i> Active sessions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="admin-stats-card bg-white shadow-sm border">
                    <div class="stats-label text-muted">Daily Coverage</div>
                    <div class="stats-value h2 mb-0 font-weight-800 text-dark"><?php 
                        $uniqueUsers = count(array_unique(array_column($records, 'user_id')));
                        echo $uniqueUsers;
                    ?></div>
                    <div class="stats-trend text-success small font-weight-600 mt-1"><i class="fe fe-check mr-1"></i> Unique associates</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="admin-stats-card bg-white shadow-sm border">
                    <div class="stats-label text-muted">Avg. Field Time</div>
                    <div class="stats-value h2 mb-0 font-weight-800 text-dark">
                        <?php 
                            $total_diff = 0;
                            $count = 0;
                            foreach($records as $r) {
                                if($r['check_out_time']) {
                                    $total_diff += strtotime($r['check_out_time']) - strtotime($r['check_in_time']);
                                    $count++;
                                }
                            }
                            echo ($count > 0) ? floor(($total_diff/$count)/3600) . "h " . floor((($total_diff/$count)/60)%60) . "m" : "0h 0m";
                        ?>
                    </div>
                    <div class="stats-trend text-primary small font-weight-600 mt-1"><i class="fe fe-clock mr-1"></i> Per session</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="admin-stats-card bg-white shadow-sm border">
                    <div class="stats-label text-muted">Verification Hits</div>
                    <div class="stats-value h2 mb-0 font-weight-800 text-dark"><?php 
                        $photosCount = array_reduce($records, function($carry, $item) {
                            if(!empty($item['check_in_photo'])) $carry++;
                            if(!empty($item['check_out_photo'])) $carry++;
                            return $carry;
                        }, 0);
                        echo $photosCount;
                    ?></div>
                    <div class="stats-trend text-info small font-weight-600 mt-1"><i class="fe fe-image mr-1"></i> Media logs</div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════ -->
        <!-- CENTRAL LOGS TABLE                              -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="card shadow-premium border-0 mb-5">
            <div class="card-header bg-white py-4 px-4 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 font-weight-800 text-dark">Central Intelligence Feed</h5>
                        <p class="text-muted small mb-0 mt-1">Granular check-in/out audit trail</p>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-0">
                <div class="table-responsive">
                    <table class="table table-premium mb-0">
                        <thead>
                            <tr>
                                <th class="pl-4">Field Associate</th>
                                <th>Timestamp</th>
                                <th>In-Transit Context</th>
                                <th>Out-Transit Context</th>
                                <th>Verification</th>
                                <th class="text-center">Activity</th>
                                <th class="text-right pr-4">Audit</th>
                            </tr>
                        </thead>
                        <tbody id="attLogBody">
                            <?php foreach($records as $r): ?>
                            <tr class="log-row">
                                <td class="pl-4 align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="premium-avatar-box mr-3">
                                            <span class="premium-avatar-text"><?php echo strtoupper(substr($r['user_name'], 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <div class="font-weight-800 text-dark mb-0"><?php echo htmlspecialchars($r['user_name']); ?></div>
                                            <div class="small text-muted font-weight-600">Executive</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="log-date"><?php echo date('M d, Y', strtotime($r['check_in_time'])); ?></div>
                                    <div class="log-day"><?php echo date('l', strtotime($r['check_in_time'])); ?></div>
                                </td>
                                <td class="align-middle">
                                    <div class="badge-premium badge-soft-success mb-1"><?php echo date('h:i A', strtotime($r['check_in_time'])); ?></div>
                                    <div class="log-addr" title="<?php echo htmlspecialchars($r['check_in_address']); ?>">
                                        <i class="fe fe-map-pin mr-1 text-primary"></i> <?php echo $r['check_in_address'] ?: 'Internal Logic'; ?>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <?php if($r['check_out_time']): ?>
                                        <div class="badge-premium badge-soft-danger mb-1"><?php echo date('h:i A', strtotime($r['check_out_time'])); ?></div>
                                        <div class="log-addr" title="<?php echo htmlspecialchars($r['check_out_address']); ?>">
                                            <i class="fe fe-map-pin mr-1 text-primary"></i> <?php echo $r['check_out_address'] ?: 'Internal Logic'; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="status-live-pulse"><span class="pulse-dot"></span> Log Active</div>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <div class="verification-gallery">
                                        <?php if(!empty($r['check_in_photo'])): ?>
                                            <img src="<?php echo $r['check_in_photo']; ?>" class="gallery-thumb" onclick="viewPhoto('<?php echo $r['check_in_photo']; ?>')" title="Entry Verification">
                                        <?php endif; ?>
                                        <?php if(!empty($r['check_out_photo'])): ?>
                                            <img src="<?php echo $r['check_out_photo']; ?>" class="gallery-thumb" onclick="viewPhoto('<?php echo $r['check_out_photo']; ?>')" title="Exit Verification">
                                        <?php endif; ?>
                                        <?php if(empty($r['check_in_photo']) && empty($r['check_out_photo'])): ?>
                                            <span class="small text-muted">No Media</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <?php if($r['check_out_time']):
                                        $diff = strtotime($r['check_out_time']) - strtotime($r['check_in_time']);
                                        $h = floor($diff/3600); $m = floor(($diff/60)%60);
                                    ?>
                                        <span class="duration-badge"><?php echo ($h > 0 ? "{$h}h " : "") . "{$m}m"; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right pr-4 align-middle">
                                    <div class="btn-group">
                                        <button class="control-btn control-primary" onclick="viewAttendanceDetails(<?php echo htmlspecialchars(json_encode($r)); ?>)" title="Detailed Audit">
                                            <i class="fe fe-eye"></i>
                                        </button>
                                        <a href="attendance?action=edit&id=<?php echo $r['id']; ?>" class="control-btn control-secondary" title="Edit Logic">
                                            <i class="fe fe-edit-2"></i>
                                        </a>
                                        <?php if($_SESSION['role'] === 'Admin'): ?>
                                            <button class="control-btn control-danger" onclick="deleteRecord(<?php echo $r['id']; ?>)" title="Void Record">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container py-4 px-4 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted font-weight-700" id="attLogPageInfo"></div>
                        <ul class="pagination pagination-modern mb-0" id="attLogPagination"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --p-primary: #4361ee;
    --p-primary-soft: #eef2ff;
    --p-success: #10b981;
    --p-danger: #f43f5e;
    --p-text-dark: #0f172a;
    --p-text-muted: #64748b;
    --p-border: #f1f5f9;
    --p-bg: #f8fafc;
    --p-radius: 16px;
    --p-shadow: 0 10px 15px -3px rgba(0,0,0,0.04), 0 4px 6px -4px rgba(0,0,0,0.04);
}

body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--p-bg); }
.font-weight-800 { font-weight: 800; }
.hero-icon-bg { width: 50px; height: 50px; background: #fff; border-radius: 14px; display: flex; align-items: center; justify-content: center; }

/* ── Metrics Cards ── */
.admin-stats-card {
    padding: 24px; border-radius: var(--p-radius); position: relative; overflow: hidden;
    margin-bottom: 20px; transition: transform 0.3s;
}
.admin-stats-card:hover { transform: translateY(-5px); }
.stats-icon-overlay { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1; transform: rotate(-15deg); }
.stats-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
.stats-value { font-size: 2rem; font-weight: 800; line-height: 1.2; margin: 5px 0; }
.stats-trend { font-size: 0.72rem; font-weight: 700; display: flex; align-items: center; }

/* ── Table Styling ── */
.shadow-premium { box-shadow: var(--p-shadow); }
.table-premium { border-collapse: separate; border-spacing: 0; width: 100%; }
.table-premium thead th {
    background: #f8fafc; color: var(--p-text-muted); padding: 18px 20px;
    border: none; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
}
.log-row:hover { background: #fdfdff; }
.log-row td { border-top: 1px solid var(--p-border); padding: 22px 20px; }

.premium-avatar-box {
    width: 42px; height: 42px; background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff;
    font-weight: 800; flex-shrink: 0; box-shadow: 0 4px 10px rgba(67,97,238,0.2);
}
.premium-avatar-text { font-size: 1.1rem; }
.log-date { font-weight: 800; color: var(--p-text-dark); font-size: 0.9rem; }
.log-day { font-size: 0.75rem; color: var(--p-text-muted); font-weight: 600; }

.badge-premium { display: inline-block; padding: 4px 12px; border-radius: 30px; font-weight: 800; font-size: 0.7rem; }
.badge-soft-success { background: #ecfdf5; color: #059669; }
.badge-soft-danger { background: #fff1f2; color: #e11d48; }

.log-addr { font-size: 0.72rem; color: var(--p-text-muted); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 500; }

.status-live-pulse { display: flex; align-items: center; gap: 8px; color: var(--p-primary); font-weight: 800; font-size: 0.75rem; background: #eef2ff; padding: 4px 12px; border-radius: 30px; border: 1px solid rgba(67,97,238,0.1); }
.pulse-dot { width: 8px; height: 8px; background: var(--p-primary); border-radius: 50%; animation: pulse-anim 2s infinite; }
@keyframes pulse-anim { 0% { transform: scale(0.9); opacity: 1; } 50% { transform: scale(1.2); opacity: 0.5; } 100% { transform: scale(0.9); opacity: 1; } }

.verification-gallery { display: flex; align-items: center; gap: 6px; }
.gallery-thumb { width: 34px; height: 34px; border-radius: 8px; border: 2px solid #fff; object-fit: cover; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s; }
.gallery-thumb:hover { transform: scale(1.2); z-index: 10; }

.duration-badge { background: #f1f5f9; color: var(--p-text-dark); padding: 4px 10px; border-radius: 8px; font-weight: 800; font-size: 0.75rem; }

/* ── Control Buttons ── */
.control-btn {
    width: 38px; height: 38px; border: none; border-radius: 10px; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem;
    transition: all 0.2s; margin-left: 5px;
}
.control-primary { background: #eef2ff; color: var(--p-primary); }
.control-primary:hover { background: var(--p-primary); color: #fff; }
.control-secondary { background: #f8fafc; color: var(--p-text-muted); border: 1px solid #e2e8f0; }
.control-secondary:hover { background: #e2e8f0; color: var(--p-text-dark); }
.control-danger { background: #fff1f2; color: var(--p-danger); }
.control-danger:hover { background: var(--p-danger); color: #fff; }

.pagination-modern .page-link { border: none; background: transparent; color: var(--p-text-muted); font-weight: 700; padding: 8px 16px; margin: 0 4px; border-radius: 10px; }
.pagination-modern .page-item.active .page-link { background: var(--p-primary); color: #fff; box-shadow: 0 4px 10px rgba(67,97,238,0.3); }

/* Animations */
.anime-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
(function(){
    const PER_PAGE = 10;
    let page = 1;
    const tbody = document.getElementById('attLogBody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('.log-row'));

    function render() {
        const total = rows.length;
        const pages = Math.max(1, Math.ceil(total / PER_PAGE));
        const start = (page - 1) * PER_PAGE;
        const end = Math.min(start + PER_PAGE, total);

        rows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
        
        document.getElementById('attLogPageInfo').textContent = total ? `Displaying ${start+1}–${end} of ${total} trace records` : 'No traces available';

        const ul = document.getElementById('attLogPagination');
        ul.innerHTML = '';

        // Prev
        const prev = document.createElement('li');
        prev.className = 'page-item' + (page === 1 ? ' disabled' : '');
        prev.innerHTML = '<a class="page-link" href="#"><i class="fe fe-chevron-left"></i></a>';
        prev.onclick = e => { e.preventDefault(); if(page > 1) { page--; render(); } };
        ul.appendChild(prev);

        // Pages
        for(let i=1; i<=pages; i++) {
            const li = document.createElement('li');
            li.className = 'page-item' + (i === page ? ' active' : '');
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.onclick = e => { e.preventDefault(); page = i; render(); };
            ul.appendChild(li);
        }

        // Next
        const next = document.createElement('li');
        next.className = 'page-item' + (page === pages ? ' disabled' : '');
        next.innerHTML = '<a class="page-link" href="#"><i class="fe fe-chevron-right"></i></a>';
        next.onclick = e => { e.preventDefault(); if(page < pages) { page++; render(); } };
        ul.appendChild(next);
    }
    render();
})();

function viewAttendanceDetails(data) {
    const modalHtml = `
        <div class="modal fade premium-modal" id="dynamicAuditModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg border-0" style="border-radius:24px; overflow:hidden;">
                    <div class="modal-header d-block p-4 border-0" style="background: linear-gradient(135deg, #1e1e2d 0%, #32325d 100%); color:#fff;">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <h5 class="mb-0 font-weight-800"><i class="fe fe-shield mr-2"></i>Security Audit Trace</h5>
                            <button type="button" class="close text-white opacity-75" data-dismiss="modal">&times;</button>
                        </div>
                    </div>
                    <div class="modal-body p-4 bg-white">
                        <div class="row">
                            <div class="col-md-5 border-right">
                                <div class="premium-avatar-box mb-3" style="width:60px; height:60px; font-size:1.5rem;">
                                    ${data.user_name.charAt(0)}
                                </div>
                                <h4 class="font-weight-800 text-dark mb-1">${data.user_name}</h4>
                                <p class="text-muted small mb-4">Associate Intelligence Record</p>
                                
                                <div class="bg-light p-3 rounded-lg mb-4">
                                    <div class="mb-2 d-flex justify-content-between small">
                                        <span class="font-weight-700">Check-In:</span>
                                        <span class="text-dark font-weight-800">${new Date(data.check_in_time).toLocaleTimeString()}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="font-weight-700">Check-Out:</span>
                                        <span class="text-dark font-weight-800">${data.check_out_time ? new Date(data.check_out_time).toLocaleTimeString() : 'In-Field'}</span>
                                    </div>
                                </div>

                                <div class="verification-grid">
                                    <label class="stats-label text-muted d-block mb-2">Visual Proof</label>
                                    <div class="d-flex gap-2">
                                        ${data.check_in_photo ? `<img src="${data.check_in_photo}" class="rounded shadow-sm" style="width:100%; height:120px; object-fit:cover;">` : '<div class="rounded bg-light text-center py-4 small w-100">No Entry Media</div>'}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 pl-md-4 mt-3 mt-md-0">
                                <label class="stats-label text-muted d-block mb-2">Spatial Context</label>
                                <div class="mb-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="circle-sm bg-soft-success text-success mr-3"><i class="fe fe-arrow-down-left"></i></div>
                                        <div>
                                            <div class="small font-weight-800 text-dark">Arrival Location</div>
                                            <div class="small text-muted">${data.check_in_address || 'Unidentified Entry'}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="circle-sm bg-soft-danger text-danger mr-3"><i class="fe fe-arrow-up-right"></i></div>
                                        <div>
                                            <div class="small font-weight-800 text-dark">Departure Location</div>
                                            <div class="small text-muted">${data.check_out_address || 'Session Active'}</div>
                                        </div>
                                    </div>
                                </div>

                                <label class="stats-label text-muted d-block mb-2">Odometer Intelligence</label>
                                <div class="p-3 border rounded-lg d-flex align-items-center bg-light-50">
                                    <div class="mr-3">
                                        ${data.odometer_photo ? `<img src="${data.odometer_photo}" class="rounded" style="width:60px; height:45px; object-fit:cover;">` : '<i class="fe fe-truck fe-24 text-muted"></i>'}
                                    </div>
                                    <div>
                                        <div class="h6 mb-0 font-weight-800">${data.odometer_reading || '0'} KM</div>
                                        <div class="small text-muted font-weight-600">Logged Reading</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button class="btn btn-primary d-block w-100 font-weight-800 py-3 rounded-pill" data-dismiss="modal">Close Audit Intelligence</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('modal-placeholder')?.remove();
    const div = document.createElement('div');
    div.id = 'modal-placeholder';
    div.innerHTML = modalHtml;
    document.body.appendChild(div);
    $('#dynamicAuditModal').modal('show');
}

function deleteRecord(id) {
    if(confirm('Permanently void this attendance log and erase its verification data?')) {
        window.location.href = `attendance?action=delete&id=${id}`;
    }
}

function viewPhoto(url) { window.open(url, '_blank'); }
</script>

<style>
.bg-light-50 { background: rgba(248,250,252,0.5); }
.circle-sm { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.bg-soft-success { background: #ecfdf5; color: #10b981; }
.bg-soft-danger  { background: #fff1f2; color: #f43f5e; }
.gap-2 { gap: 8px; }
</style>

<?php include 'layout/footer.php'; ?>
