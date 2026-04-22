<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<!-- Calendar Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<main role="main" class="main-content">
<div class="container-fluid px-2 px-md-3 pb-5">

    <!-- ══════════════════════════════════════════════ -->
    <!-- PAGE HERO & METRICS                             -->
    <!-- ══════════════════════════════════════════════ -->
    <div class="premium-hero mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="d-flex align-items-center mb-3 mb-lg-0">
                    <div class="hero-glass-icon"><i class="fe fe-calendar"></i></div>
                    <div class="ml-3">
                        <h2 class="hero-glass-title">Attendance Tracking</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent p-0 mb-0 hero-breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Attendance History</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-actions justify-content-lg-end">
                    <?php if(!empty($users)): ?>
                        <div class="select-wrapper">
                            <i class="fe fe-user select-icon"></i>
                            <select id="user-selector" class="premium-select">
                                <?php foreach($users as $u): ?>
                                    <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $selectedUser) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($u['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="dropdown">
                        <button class="premium-btn primary dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fe fe-download mr-1"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-right premium-dropdown shadow">
                            <a class="dropdown-item" href="attendance-export?user_id=<?php echo $selectedUser; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&format=csv">
                                <i class="fe fe-file-text mr-2 text-success"></i> Excel (CSV)
                            </a>
                            <a class="dropdown-item" target="_blank" href="attendance-export?user_id=<?php echo $selectedUser; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&format=pdf">
                                <i class="fe fe-printer mr-2 text-danger"></i> PDF Document
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="stats-card glass">
                <div class="stats-card-body">
                    <div class="stats-icon bg-soft-success"><i class="fe fe-check-circle"></i></div>
                    <div>
                        <div class="stats-label">Days Present</div>
                        <div class="stats-value"><?php echo count($records ?? []); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="stats-card glass">
                <div class="stats-card-body">
                    <div class="stats-icon bg-soft-warning"><i class="fe fe-sun"></i></div>
                    <div>
                        <div class="stats-label">On Leave</div>
                        <div class="stats-value"><?php echo count($leaves ?? []); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card glass">
                <div class="stats-card-body">
                    <div class="stats-icon bg-soft-primary"><i class="fe fe-activity"></i></div>
                    <div>
                        <div class="stats-label">Avg. Duration</div>
                        <div class="stats-value">
                            <?php 
                                $total_diff = 0;
                                $complete_logs = 0;
                                foreach($records as $r) {
                                    if($r['check_out_time']) {
                                        $total_diff += strtotime($r['check_out_time']) - strtotime($r['check_in_time']);
                                        $complete_logs++;
                                    }
                                }
                                if($complete_logs > 0) {
                                    $avg = $total_diff / $complete_logs;
                                    echo floor($avg/3600) . "h " . floor(($avg/60)%60) . "m";
                                } else echo "0h 0m";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- MAIN CONTENT CARD                               -->
    <!-- ══════════════════════════════════════════════ -->
    <div class="ah-main-card shadow-lg border-0 mb-5">
        <div class="card-header-premium">
            <div class="d-flex align-items-center">
                <div class="header-indicator"></div>
                <h5 class="mb-0 ml-2 font-weight-bold">Activity Intelligence</h5>
            </div>
            <div class="view-switcher">
                <button class="view-btn active" id="btn-calendar" title="Calendar View">
                    <i class="fe fe-calendar"></i>
                </button>
                <button class="view-btn" id="btn-list" title="Data List View">
                    <i class="fe fe-list"></i>
                </button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <!-- CALENDAR VIEW -->
            <div id="calendar-wrapper" class="p-4 anime-fade-in">
                <div id="attendance-calendar"></div>
                <div class="calendar-footer mt-4 pt-3 border-top d-flex justify-content-center gap-4 flex-wrap">
                    <div class="legend-pill lg-present"><span class="pill-dot"></span> Present</div>
                    <div class="legend-pill lg-leave"><span class="pill-dot"></span> On Leave</div>
                    <div class="legend-pill lg-absent"><span class="pill-dot"></span> Absent</div>
                </div>
            </div>

            <!-- LIST VIEW -->
            <div id="list-wrapper" class="d-none anime-fade-in">
                <div class="table-responsive">
                    <table class="table table-premium mb-0 datatables" id="historyTable">
                        <thead>
                            <tr>
                                <th>Date & Day</th>
                                <th>Location Context</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Net Activity</th>
                                <th class="text-right">Intelligence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($records)): ?>
                                <?php foreach($records as $r): ?>
                                <tr class="premium-row">
                                    <td class="pl-4">
                                        <div class="date-box">
                                            <span class="d-block font-weight-bold text-dark"><?php echo date('d M Y', strtotime($r['check_in_time'])); ?></span>
                                            <span class="small text-muted text-uppercase tracking-wider"><?php echo date('l', strtotime($r['check_in_time'])); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="address-box">
                                            <i class="fe fe-map-pin text-primary mt-1 mr-2"></i>
                                            <div class="text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($r['check_in_address'] ?? ''); ?>">
                                                <?php echo htmlspecialchars($r['check_in_address'] ?? 'General Location'); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge-premium badge-success"><i class="fe fe-arrow-down-left mr-1"></i><?php echo date('h:i A', strtotime($r['check_in_time'])); ?></span></td>
                                    <td>
                                        <?php if($r['check_out_time']): ?>
                                            <span class="badge-premium badge-danger"><i class="fe fe-arrow-up-right mr-1"></i><?php echo date('h:i A', strtotime($r['check_out_time'])); ?></span>
                                        <?php else: ?>
                                            <span class="badge-premium badge-primary pulse"><i class="fe fe-activity mr-1"></i>Direct</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($r['check_out_time']):
                                            $diff = strtotime($r['check_out_time']) - strtotime($r['check_in_time']);
                                            $h = floor($diff/3600); $m = floor(($diff/60)%60);
                                        ?>
                                            <div class="duration-pill"><?php echo "{$h}h {$m}m"; ?></div>
                                        <?php else: ?>
                                            <span class="text-muted small">Session in progress...</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right pr-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="action-circle-btn primary" onclick="window.location.href='attendance-edit?id=<?php echo $r['id']; ?>'" title="View Full Audit">
                                                <i class="fe fe-eye"></i>
                                            </button>
                                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                            <button class="action-circle-btn danger" onclick="confirmDelete(<?php echo $r['id']; ?>)" title="Delete Trace">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fe fe-search"></i></div>
                                            <h5>No Sessions Found</h5>
                                            <p>Tailor your filters or check back later for live activity.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
</main>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --p-primary: #6366f1;
    --p-primary-light: #818cf8;
    --p-success: #10b981;
    --p-danger:  #f43f5e;
    --p-warning: #f59e0b;
    --p-bg:      #f8fafc;
    --p-card-bg: #ffffff;
    --p-text-main: #1e293b;
    --p-text-muted: #64748b;
    --p-border:  #f1f5f9;
    --p-radius:  16px;
    --p-shadow:  0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
}

body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; background-color: var(--p-bg); }

/* ── Premium Hero ── */
.premium-hero {
    background: linear-gradient(135deg, #1e1e2d 0%, #32325d 100%);
    border-radius: var(--p-radius);
    padding: 30px;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.hero-glass-icon {
    width: 60px; height: 60px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(8px);
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: #fff;
}
.hero-glass-title { font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.5px; color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,0.2); }
.hero-breadcrumb { font-size: 0.85rem; opacity: 0.8; }
.hero-breadcrumb a { color: #fff !important; text-decoration: none; font-weight: 600; }
.hero-breadcrumb .active { color: #fff !important; opacity: 1; font-weight: 600; }

.hero-actions { display: flex; align-items: center; gap: 15px; }
.premium-select {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    padding: 10px 15px 10px 40px;
    border-radius: 12px;
    font-size: 0.9rem; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.premium-select option {
    color: #1e293b;
    background: #fff;
}
.select-wrapper { position: relative; }
.select-icon { position: absolute; left: 15px; top: 50%; translate: 0 -50%; opacity: 0.5; }
.premium-btn {
    border-radius: 12px; padding: 10px 20px; font-weight: 700; font-size: 0.9rem;
    display: inline-flex; align-items: center; border: none; transition: all 0.2s;
}
.premium-btn.primary { background: #fff; color: var(--p-primary); }
.premium-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

/* ── Stats Cards ── */
.stats-card {
    background: var(--p-card-bg);
    border-radius: var(--p-radius);
    padding: 24px;
    box-shadow: var(--p-shadow);
    transition: transform 0.3s;
}
.stats-card:hover { transform: translateY(-5px); }
.stats-card-body { display: flex; align-items: center; gap: 20px; }
.stats-icon {
    width: 50px; height: 50px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
}
.bg-soft-success { background: #ecfdf5; color: #10b981; }
.bg-soft-warning { background: #fffbeb; color: #f59e0b; }
.bg-soft-primary { background: #eef2ff; color: #6366f1; }
.stats-label { font-size: 0.8rem; font-weight: 700; color: var(--p-text-muted); text-transform: uppercase; }
.stats-value { font-size: 1.5rem; font-weight: 800; color: var(--p-text-main); }

/* ── Main Activity Card ── */
.ah-main-card { background: #fff; border-radius: var(--p-radius); overflow: hidden; }
.card-header-premium {
    padding: 25px 30px; border-bottom: 1px solid var(--p-border);
    display: flex; align-items: center; justify-content: space-between;
}
.header-indicator { width: 5px; height: 25px; background: var(--p-primary); border-radius: 10px; }

.view-switcher { background: #f1f5f9; padding: 5px; border-radius: 12px; display: flex; gap: 5px; }
.view-btn {
    width: 40px; height: 40px; border: none; background: transparent;
    color: var(--p-text-muted); border-radius: 10px; cursor: pointer; transition: all 0.2s;
}
.view-btn.active { background: #fff; color: var(--p-primary); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

/* ── Table Styling ── */
.table-premium { border-collapse: separate; border-spacing: 0 10px; padding: 0 20px 20px; }
.table-premium thead th {
    background: transparent; color: var(--p-text-muted); font-weight: 700;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
    border: none; padding: 15px 10px;
}
.premium-row { transition: all 0.2s; }
.premium-row td {
    background: #fff; border-top: 1px solid var(--p-border); border-bottom: 1px solid var(--p-border);
    padding: 20px 10px;
}
.premium-row td:first-child { border-left: 1px solid var(--p-border); border-radius: 16px 0 0 16px; }
.premium-row td:last-child { border-right: 1px solid var(--p-border); border-radius: 0 16px 16px 0; }
.premium-row:hover td { background: #f8faff; border-color: var(--p-primary-light); }

.date-box { display: flex; flex-direction: column; }
.address-box { display: flex; align-items: flex-start; font-size: 0.85rem; color: var(--p-text-muted); }

.badge-premium {
    display: inline-flex; align-items: center; padding: 6px 14px;
    border-radius: 30px; font-weight: 700; font-size: 0.75rem;
}
.badge-success { background: #ecfdf5; color: #059669; }
.badge-danger  { background: #fff1f2; color: #e11d48; }
.badge-primary { background: #eef2ff; color: #4f46e5; }

.duration-pill { 
    background: #f1f5f9; color: var(--p-text-main); font-weight: 800;
    font-size: 0.75rem; padding: 4px 12px; border-radius: 8px; display: inline-block;
}

.action-circle-btn {
    width: 36px; height: 36px; border-radius: 50%; border: none;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s;
}
.action-circle-btn.primary { background: #eef2ff; color: var(--p-primary); }
.action-circle-btn.primary:hover { background: var(--p-primary); color: #fff; }
.action-circle-btn.danger { background: #fff1f2; color: var(--p-danger); }
.action-circle-btn.danger:hover { background: var(--p-danger); color: #fff; }

/* ── FullCalendar Customization ── */
.fc .fc-toolbar-title { font-weight: 800; letter-spacing: -0.5px; color: var(--p-text-main); }
.fc .fc-button-primary {
    background: #fff !important; border: 1px solid var(--p-border) !important;
    color: var(--p-text-main) !important; font-weight: 700; border-radius: 10px !important;
}
.fc .fc-button-primary:hover { background: var(--p-border) !important; }
.fc-theme-standard td, .fc-theme-standard th { border: 1px solid var(--p-border) !important; }
.fc-daygrid-day-number { font-weight: 700; padding: 12px !important; opacity: 0.5; }

.status-badge-present { background: #dcfce7 !important; color: #059669 !important; border-radius: 6px !important; font-weight: 800 !important; font-size: 10px !important; border: none !important; }
.status-badge-leave   { background: #fef9c3 !important; color: #a16207 !important; border-radius: 6px !important; font-weight: 800 !important; font-size: 10px !important; border: none !important; }

.absent-mark {
    display: flex; align-items: center; justify-content: center; gap: 4px;
    height: 100%; width: 100%; color: #f43f5e; font-weight: 700; font-size: 10px;
    opacity: 0.8;
}

/* Animations */
.anime-fade-in { animation: fadeIn 0.4s ease forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* ── Utilities ── */
.gap-2 { gap: 8px; }
.gap-4 { gap: 24px; }
.pulse { animation: pulseAnim 2s infinite; }
@keyframes pulseAnim { 0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; } }

.legend-pill { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: var(--p-text-muted); }
.pill-dot { width: 8px; height: 8px; border-radius: 50%; }
.lg-present .pill-dot { background: var(--p-success); }
.lg-leave .pill-dot { background: var(--p-warning); }
.lg-absent .pill-dot { background: var(--p-danger); }

/* Custom Scrollbar */
.table-responsive::-webkit-scrollbar { height: 6px; }
.table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

/* ── Icon Buttons ── */
.ah-icon-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    border: none; cursor: pointer; font-size: 0.85rem;
    transition: background 0.15s, transform 0.1s;
    text-decoration: none;
}
.ah-icon-btn:hover { transform: scale(1.1); }
.ah-icon-primary { background: rgba(67,97,238,0.1); color: var(--p-primary); }
.ah-icon-primary:hover { background: rgba(67,97,238,0.2); }
.ah-icon-danger  { background: rgba(220,53,69,0.1); color: var(--p-danger); }
.ah-icon-danger:hover  { background: rgba(220,53,69,0.2); }

/* ── FullCalendar Overrides ── */
:root {
    --fc-border-color: #f1f4f8;
}
.fc-theme-standard td, .fc-theme-standard th { border: 1px solid #f1f4f8 !important; }
.fc .fc-toolbar-title {
    font-size: 1rem !important; color: #32325d !important;
    font-weight: 700 !important; text-transform: uppercase; letter-spacing: 0.5px;
}
.fc .fc-button-primary {
    background-color: #fff !important; color: #4a5568 !important;
    border: 1px solid #e2e8f0 !important; font-size: 0.72rem;
    font-weight: 700; border-radius: 8px !important; text-transform: uppercase;
}
.fc .fc-button-primary:hover { background-color: #f7fafc !important; color: #2d3748 !important; }
.fc-daygrid-day-number {
    font-size: 0.78rem; color: #adb5bd;
    padding: 10px !important; text-decoration: none !important; font-weight: 600;
}
.fc-daygrid-day.fc-day-today { background-color: #f4f6f9 !important; border: 2px solid var(--p-primary) !important; }
.status-badge-present {
    background-color: #dcfce7 !important; border-radius: 4px; border: none;
    padding: 3px 8px; color: #166534 !important; font-weight: 800;
    font-size: 9px; text-transform: uppercase;
}
.status-badge-leave {
    background-color: #fef9c3 !important; border-radius: 4px; border: none;
    padding: 3px 8px; color: #854d0e !important; font-weight: 800;
    font-size: 9px; text-transform: uppercase;
}
.absent-mark {
    display: flex; align-items: center; justify-content: center;
    height: 100%; width: 100%;
    color: #ef4444; font-weight: 800; font-size: 8px; opacity: 0.6; letter-spacing: 0.5px;
}

/* ── Mobile ── */
@media (max-width: 576px) {
    .premium-hero { padding: 15px !important; }
    .hero-glass-title { font-size: 1.25rem !important; }
    .hero-glass-icon { width: 45px; height: 45px; font-size: 1.2rem; }
    .ah-hero-controls { width: 100%; }
    .stats-card { padding: 15px; }
    .stats-value { font-size: 1.2rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('attendance-calendar');
    const events = [];

    <?php foreach($records as $r): ?>
    events.push({
        title: 'Present',
        start: '<?php echo date('Y-m-d', strtotime($r['check_in_time'])); ?>',
        classNames: ['status-badge-present'],
        display: 'block'
    });
    <?php endforeach; ?>

    <?php foreach($leaves as $l): ?>
    let eDate = new Date('<?php echo $l['end_date']; ?>');
    eDate.setDate(eDate.getDate() + 1);
    events.push({
        title: 'On Leave',
        start: '<?php echo $l['start_date']; ?>',
        end: eDate.toISOString().split('T')[0],
        classNames: ['status-badge-leave'],
        display: 'block'
    });
    <?php endforeach; ?>

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
        events: events,
        height: 'auto',
        dayCellDidMount: function(arg) {
            const today = new Date().setHours(0,0,0,0);
            const cellDate = arg.date.setHours(0,0,0,0);
            if (cellDate < today) {
                if (arg.date.getDay() === 0) {
                    arg.el.style.backgroundColor = '#f9fafb';
                    return;
                }
                const hasSession = events.some(e => {
                    const start = new Date(e.start).setHours(0,0,0,0);
                    const end = e.end ? new Date(e.end).setHours(0,0,0,0) : start;
                    return cellDate >= start && cellDate < (e.end ? end : start + 86400000);
                });
                if (!hasSession) {
                    const frame = arg.el.querySelector('.fc-daygrid-day-frame');
                    const mark = document.createElement('div');
                    mark.className = 'absent-mark';
                    mark.innerHTML = '<i class="fe fe-user-x mr-1"></i>Absent';
                    frame.appendChild(mark);
                    arg.el.style.backgroundColor = '#fff5f5';
                }
            }
        }
    });

    calendar.render();

    // View toggle logic
    const btnCal  = document.getElementById('btn-calendar');
    const btnList = document.getElementById('btn-list');
    const calWrap  = document.getElementById('calendar-wrapper');
    const listWrap = document.getElementById('list-wrapper');

    btnCal.addEventListener('click', function() {
        calWrap.classList.remove('d-none');
        listWrap.classList.add('d-none');
        btnCal.classList.add('active');
        btnList.classList.remove('active');
        setTimeout(() => calendar.render(), 100);
    });

    btnList.addEventListener('click', function() {
        calWrap.classList.add('d-none');
        listWrap.classList.remove('d-none');
        btnList.classList.add('active');
        btnCal.classList.remove('active');
    });

    window.confirmDelete = function(id) {
        if(confirm('Are you certain you want to permanently erase this attendance record and its metadata?')) {
            window.location.href=`attendance?action=delete&id=${id}`;
        }
    };

    // User selector
    const userSel = document.getElementById('user-selector');
    if (userSel) {
        userSel.addEventListener('change', function() {
            window.location.href = `attendance-history?user_id=${this.value}`;
        });
    }
});
</script>

<?php include 'layout/footer.php'; ?>
