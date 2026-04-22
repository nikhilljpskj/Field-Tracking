<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<!-- Calendar Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<main role="main" class="main-content">
<div class="container-fluid px-2 px-md-3 pb-5">

    <!-- ══════════════════════════════════════════════ -->
    <!-- PAGE HERO                                       -->
    <!-- ══════════════════════════════════════════════ -->
    <div class="ah-hero mb-4">
        <div class="ah-hero-left">
            <div class="ah-hero-icon"><i class="fe fe-calendar"></i></div>
            <div>
                <h2 class="ah-hero-title">Attendance Tracking</h2>
                <nav aria-label="breadcrumb" class="d-none d-sm-block">
                    <ol class="breadcrumb bg-transparent p-0 mb-0 ah-breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="attendance">Attendance</a></li>
                        <li class="breadcrumb-item active">History</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ah-hero-controls">
            <?php if(!empty($users)): ?>
                <select id="user-selector" class="ah-select">
                    <?php foreach($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $selectedUser) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($u['name']); ?> (<?php echo $u['role_name'] ?? 'Staff'; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <!-- View toggle -->
            <div class="ah-view-toggle">
                <button class="ah-toggle-btn active" id="btn-calendar">
                    <i class="fe fe-calendar"></i><span>Calendar</span>
                </button>
                <button class="ah-toggle-btn" id="btn-list">
                    <i class="fe fe-list"></i><span>List</span>
                </button>
            </div>

            <!-- Export dropdown -->
            <div class="dropdown">
                <button class="ah-export-btn dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fe fe-download"></i><span class="d-none d-sm-inline ml-1">Export</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow border-0 rounded-lg">
                    <a class="dropdown-item py-2" href="attendance-export?user_id=<?php echo $selectedUser; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&format=csv">
                        <i class="fe fe-file-text mr-2 text-success"></i> Excel (CSV)
                    </a>
                    <a class="dropdown-item py-2" target="_blank" href="attendance-export?user_id=<?php echo $selectedUser; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&format=pdf">
                        <i class="fe fe-printer mr-2 text-danger"></i> PDF / Print
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- CALENDAR VIEW                                   -->
    <!-- ══════════════════════════════════════════════ -->
    <div id="calendar-wrapper">
        <div class="ah-card mb-4">
            <div id="attendance-calendar"></div>

            <!-- Legend -->
            <div class="ah-legend">
                <div class="ah-legend-item">
                    <span class="ah-legend-dot" style="background:#16a34a;"></span>
                    <span>Present</span>
                </div>
                <div class="ah-legend-item">
                    <span class="ah-legend-dot" style="background:#ca8a04;"></span>
                    <span>On Leave</span>
                </div>
                <div class="ah-legend-item">
                    <span class="ah-legend-dot" style="background:#ef4444;"></span>
                    <span>Absent</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- LIST VIEW                                       -->
    <!-- ══════════════════════════════════════════════ -->
    <div id="list-wrapper" class="d-none">
        <div class="ah-card mb-4" style="overflow:hidden;">
            <div class="ah-card-header">
                <h6 class="ah-card-title"><i class="fe fe-list mr-2"></i>Detailed Session Log</h6>
                <span class="ah-record-count"><?php echo count($records ?? []); ?> session<?php echo count($records ?? []) != 1 ? 's' : ''; ?></span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 datatables" id="historyTable">
                    <thead class="ah-thead">
                        <tr>
                            <th>Date &amp; Day</th>
                            <th>Location</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Duration</th>
                            <th class="text-right">Audit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($records)): ?>
                            <?php foreach($records as $r): ?>
                            <tr class="ah-row">
                                <td>
                                    <div class="ah-date"><?php echo date('d M Y', strtotime($r['check_in_time'])); ?></div>
                                    <div class="ah-day"><?php echo date('l', strtotime($r['check_in_time'])); ?></div>
                                </td>
                                <td>
                                    <div class="ah-addr">
                                        <i class="fe fe-map-pin fe-10 mr-1 text-primary"></i>
                                        <?php echo htmlspecialchars($r['check_in_address'] ?? '—'); ?>
                                    </div>
                                </td>
                                <td><span class="ah-time-badge ah-time-in"><?php echo date('h:i A', strtotime($r['check_in_time'])); ?></span></td>
                                <td>
                                    <?php if($r['check_out_time']): ?>
                                        <span class="ah-time-badge ah-time-out"><?php echo date('h:i A', strtotime($r['check_out_time'])); ?></span>
                                    <?php else: ?>
                                        <span class="ah-time-badge ah-time-active"><i class="fe fe-activity fe-10 mr-1"></i>Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($r['check_out_time']):
                                        $diff = strtotime($r['check_out_time']) - strtotime($r['check_in_time']);
                                        $h = floor($diff/3600); $m = floor(($diff/60)%60);
                                    ?>
                                        <span class="ah-duration"><?php echo "{$h}h {$m}m"; ?></span>
                                    <?php else: ?>
                                        <span class="ah-duration-calc">Calculating…</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <div class="d-flex justify-content-end" style="gap:6px;">
                                        <button class="ah-icon-btn ah-icon-primary" onclick="window.location.href='attendance-edit?id=<?php echo $r['id']; ?>'" title="View / Edit">
                                            <i class="fe fe-eye"></i>
                                        </button>
                                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                        <button class="ah-icon-btn ah-icon-danger" title="Delete"
                                            onclick="if(confirm('Permanently delete this attendance record and its verification photos?')) window.location.href='attendance?action=delete&id=<?php echo $r['id']; ?>'">
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
                                    <div style="color:#94a3b8;font-size:0.85rem;">
                                        <i class="fe fe-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                        No session records found for selected period.
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
</main>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
    --ah-primary: #4361ee;
    --ah-success: #16a34a;
    --ah-danger:  #dc3545;
    --ah-warning: #ca8a04;
    --ah-border:  #e8edf5;
    --ah-bg:      #f8fafd;
    --ah-text:    #1e293b;
    --ah-muted:   #64748b;
    --ah-radius:  14px;
    --ah-shadow:  0 2px 12px rgba(0,0,0,0.07);
}

/* ── Hero ── */
.ah-hero {
    background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    border-radius: var(--ah-radius);
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    box-shadow: 0 4px 24px rgba(67,97,238,0.25);
}
.ah-hero-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.ah-hero-icon {
    width: 44px; height: 44px;
    background: rgba(255,255,255,0.18);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; color: #fff;
    flex-shrink: 0;
}
.ah-hero-title {
    font-size: 1.1rem; font-weight: 800;
    color: #fff; margin: 0 0 2px;
    font-family: 'Inter', sans-serif;
}
.ah-breadcrumb { color: rgba(255,255,255,0.65) !important; font-size: 0.75rem; }
.ah-breadcrumb a { color: rgba(255,255,255,0.75) !important; }
.ah-breadcrumb .active { color: rgba(255,255,255,0.5) !important; }

.ah-hero-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* ── Select ── */
.ah-select {
    border: 1.5px solid rgba(255,255,255,0.35);
    background: rgba(255,255,255,0.15);
    color: #fff;
    border-radius: 10px;
    padding: 7px 12px;
    font-size: 0.78rem;
    font-weight: 600;
    height: 36px;
    appearance: none;
    cursor: pointer;
    min-width: 160px;
    max-width: 200px;
}
.ah-select option { background: #1e293b; color: #fff; }

/* ── View Toggle ── */
.ah-view-toggle {
    display: flex;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    padding: 3px;
    gap: 2px;
}
.ah-toggle-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 12px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: rgba(255,255,255,0.75);
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.ah-toggle-btn.active {
    background: rgba(255,255,255,0.9);
    color: var(--ah-primary);
}

/* ── Export ── */
.ah-export-btn {
    display: inline-flex; align-items: center;
    background: rgba(255,255,255,0.18);
    border: 1.5px solid rgba(255,255,255,0.35);
    color: #fff;
    border-radius: 10px;
    padding: 7px 14px;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s;
    height: 36px;
}
.ah-export-btn:hover { background: rgba(255,255,255,0.3); }

/* ── Card ── */
.ah-card {
    background: #fff;
    border-radius: var(--ah-radius);
    box-shadow: var(--ah-shadow);
    padding: 20px;
}
.ah-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--ah-border);
    background: var(--ah-bg);
}
.ah-card-title {
    font-size: 0.85rem; font-weight: 700;
    color: var(--ah-text); margin: 0;
}
.ah-record-count {
    font-size: 0.72rem; color: var(--ah-muted);
    font-weight: 600;
}

/* ── Legend ── */
.ah-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    justify-content: center;
    padding-top: 16px;
    margin-top: 16px;
    border-top: 1px solid var(--ah-border);
}
.ah-legend-item {
    display: flex; align-items: center; gap: 7px;
    font-size: 0.76rem; font-weight: 600; color: var(--ah-muted);
}
.ah-legend-dot {
    width: 10px; height: 10px; border-radius: 50%;
}

/* ── Table ── */
.ah-thead th {
    padding: 12px 16px !important;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ah-muted);
    background: var(--ah-bg);
    border-bottom: 2px solid var(--ah-border) !important;
    white-space: nowrap;
}
.ah-row td {
    padding: 14px 16px !important;
    vertical-align: middle;
    border-color: var(--ah-border) !important;
}
.ah-row:hover { background: #f8f9ff !important; }

.ah-date { font-weight: 700; font-size: 0.84rem; color: var(--ah-text); }
.ah-day  { font-size: 0.7rem; color: var(--ah-muted); margin-top: 1px; }
.ah-addr {
    font-size: 0.74rem; color: var(--ah-muted);
    max-width: 220px; overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap;
}

.ah-time-badge {
    display: inline-flex; align-items: center;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 0.72rem;
    font-weight: 700;
    white-space: nowrap;
}
.ah-time-in     { background: rgba(22,163,74,0.1);  color: var(--ah-success); }
.ah-time-out    { background: rgba(220,53,69,0.1);  color: var(--ah-danger);  }
.ah-time-active { background: rgba(67,97,238,0.1);  color: var(--ah-primary); }

.ah-duration      { font-weight: 700; font-size: 0.8rem; color: var(--ah-text); }
.ah-duration-calc { font-size: 0.74rem; color: var(--ah-muted); font-style: italic; }

/* ── Icon Buttons ── */
.ah-icon-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    border: none; cursor: pointer; font-size: 0.85rem;
    transition: background 0.15s, transform 0.1s;
    text-decoration: none;
}
.ah-icon-btn:hover { transform: scale(1.1); }
.ah-icon-primary { background: rgba(67,97,238,0.1); color: var(--ah-primary); }
.ah-icon-primary:hover { background: rgba(67,97,238,0.2); }
.ah-icon-danger  { background: rgba(220,53,69,0.1); color: var(--ah-danger); }
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
.fc-daygrid-day.fc-day-today { background-color: #f4f6f9 !important; border: 2px solid #5d87ff !important; }
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
    .ah-hero { padding: 14px 16px; }
    .ah-hero-title { font-size: 0.95rem; }
    .ah-hero-controls { width: 100%; }
    .ah-select { min-width: 0; flex: 1; }
    .ah-toggle-btn span { display: none; }
    .ah-toggle-btn { padding: 5px 9px; }
    .ah-card { padding: 12px; }
    .ah-addr { max-width: 130px; }
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
        calendar.render();
    });

    btnList.addEventListener('click', function() {
        calWrap.classList.add('d-none');
        listWrap.classList.remove('d-none');
        btnList.classList.add('active');
        btnCal.classList.remove('active');
    });

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
