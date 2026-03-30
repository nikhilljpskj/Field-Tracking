<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<!-- Calendar Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                
                <!-- Page Header with Breadcrumbs & Standard Actions -->
                <div class="row align-items-center mb-4">
                    <div class="col">
                        <h2 class="h3 mb-0 page-title">Attendance Tracking</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent p-0 mb-0">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="attendance">Attendance</a></li>
                                <li class="breadcrumb-item active" aria-current="page">History</li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="col-auto">
                        <div class="d-flex align-items-center">
                            <?php if(!empty($users)): ?>
                                <div class="form-group mb-0 mr-3">
                                    <select id="user-selector" class="form-control form-control-sm border-0 shadow-sm px-3" style="min-width: 200px; border-radius: 20px; height: 38px;">
                                        <?php foreach($users as $u): ?>
                                            <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $selectedUser) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($u['name']); ?> (<?php echo $u['role_name'] ?? 'Staff'; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <div class="btn-group btn-group-toggle shadow-sm" data-toggle="buttons">
                                <label class="btn btn-white active" id="btn-calendar">
                                    <input type="radio" name="options" id="option1" checked> <i class="fe fe-calendar mr-1"></i> Calendar
                                </label>
                                <label class="btn btn-white" id="btn-list">
                                    <input type="radio" name="options" id="option2"> <i class="fe fe-list mr-1"></i> List
                                </label>
                            </div>
                            
                            <!-- Export Functional Dropdown -->
                            <div class="dropdown d-inline-block ml-3 shadow-sm">
                                <button class="btn btn-white dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 8px;">
                                    <i class="fe fe-download mr-1 text-primary"></i> Export Report
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                                    <a class="dropdown-item" href="attendance-export?user_id=<?php echo $selectedUser; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&format=csv">
                                        <i class="fe fe-file-text mr-2 text-success"></i> Excel (CSV)
                                    </a>
                                    <a class="dropdown-item" target="_blank" href="attendance-export?user_id=<?php echo $selectedUser; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&format=pdf">
                                        <i class="fe fe-printer mr-2 text-danger"></i> PDF / Print
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Row -->
                <div class="row">
                    <!-- Calendar Container -->
                    <div class="col-12" id="calendar-wrapper">
                        <div class="card shadow-sm border-0 mb-4 p-4" style="border-radius: 12px;">
                            <div id="attendance-calendar"></div>
                            
                            <!-- Custom Legend for Professionalism -->
                            <div class="d-flex mt-4 pt-3 border-top justify-content-center flex-wrap">
                                <div class="d-flex align-items-center mx-3 mb-2">
                                    <span class="dot dot-md bg-success mr-2"></span>
                                    <small class="text-muted font-weight-bold">Present (Deployment Logged)</small>
                                </div>
                                <div class="d-flex align-items-center mx-3 mb-2">
                                    <span class="dot dot-md bg-warning mr-2"></span>
                                    <small class="text-muted font-weight-bold">On Leave (Approved)</small>
                                </div>
                                <div class="d-flex align-items-center mx-3 mb-2">
                                    <span class="dot dot-md bg-danger mr-2"></span>
                                    <small class="text-muted font-weight-bold">Absent (No Activity Record)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List Container (Hidden) -->
                    <div class="col-12 d-none" id="list-wrapper">
                        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0">Detailed Session Log</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 datatables" id="historyTable">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="pl-4">Date & Day</th>
                                            <th>Location Tag</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
                                            <th>Shift Duration</th>
                                            <th class="pr-4 text-right">Audit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($records)): ?>
                                            <?php foreach($records as $r): ?>
                                            <tr>
                                                <td class="pl-4">
                                                    <span class="d-block font-weight-bold"><?php echo date('d M Y', strtotime($r['check_in_time'])); ?></span>
                                                    <small class="text-muted"><?php echo date('l', strtotime($r['check_in_time'])); ?></small>
                                                </td>
                                                <td>
                                                    <span class="small text-muted d-block" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        <i class="fe fe-map-pin mr-1"></i> <?php echo $r['check_in_address']; ?>
                                                    </span>
                                                </td>
                                                <td><span class="badge badge-soft-success px-2"><?php echo date('h:i A', strtotime($r['check_in_time'])); ?></span></td>
                                                <td><span class="badge <?php echo $r['check_out_time'] ? 'badge-soft-danger' : 'badge-soft-secondary'; ?> px-2"><?php echo $r['check_out_time'] ? date('h:i A', strtotime($r['check_out_time'])) : 'Active'; ?></span></td>
                                                <td>
                                                    <?php if($r['check_out_time']): 
                                                        $diff = strtotime($r['check_out_time']) - strtotime($r['check_in_time']);
                                                        echo floor($diff/3600) . 'h ' . floor(($diff/60)%60) . 'm';
                                                    else: ?>
                                                        <span class="text-muted italic small">Calculating...</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="pr-4 text-right">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='attendance-edit?id=<?php echo $r['id']; ?>'">
                                                        <i class="fe fe-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center py-5 text-muted small">No session records found for selected period.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<style>
    /* FullCalendar Professional Styling */
    :root {
        --fc-border-color: #f1f4f8;
        --fc-daygrid-dot-event-width: 10px;
    }
    
    .fc-theme-standard td, .fc-theme-standard th { border: 1px solid #f1f4f8 !important; }
    .fc .fc-toolbar-title { font-size: 1.1rem !important; color: #32325d !important; font-weight: 700 !important; text-transform: uppercase; letter-spacing: 0.5px; }
    .fc .fc-button-primary { background-color: #ffffff !important; color: #4a5568 !important; border: 1px solid #e2e8f0 !important; font-size: 0.75rem; font-weight: 700; border-radius: 8px !important; text-transform: uppercase; }
    .fc .fc-button-primary:hover { background-color: #f7fafc !important; color: #2d3748 !important; }
    .fc .fc-today-button { font-size: 0.75rem; text-transform: uppercase; }
    
    .fc-daygrid-day-number { font-size: 0.8rem; color: #adb5bd; padding: 12px !important; text-decoration: none !important; font-weight: 600; }
    .fc-daygrid-day.fc-day-today { background-color: #f4f6f9 !important; border: 2px solid #5d87ff !important; }
    
    /* Event Badges */
    .status-badge-present { background-color: #dcfce7 !important; border-radius: 4px; border: none; padding: 4px 10px; color: #166534 !important; font-weight: 800; font-size: 10px; text-transform: uppercase; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .status-badge-leave { background-color: #fef9c3 !important; border-radius: 4px; border: none; padding: 4px 10px; color: #854d0e !important; font-weight: 800; font-size: 10px; text-transform: uppercase; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    
    /* Absent Overrides */
    .absent-mark { display: flex; align-items: center; justify-content: center; height: 100%; width: 100%; color: #ef4444; font-weight: 800; font-size: 9px; opacity: 0.6; letter-spacing: 0.5px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('attendance-calendar');
    const events = [];

    // Map Backend Attendance to Calendar Events
    <?php foreach($records as $r): ?>
    events.push({
        title: 'Present',
        start: '<?php echo date('Y-m-d', strtotime($r['check_in_time'])); ?>',
        classNames: ['status-badge-present'],
        display: 'block'
    });
    <?php endforeach; ?>

    // Map Approved Leaves to Calendar Events
    <?php foreach($leaves as $l): ?>
    let eDate = new Date('<?php echo $l['end_date']; ?>');
    eDate.setDate(eDate.getDate() + 1); // FullCalendar is exclusive on end date
    
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
            
            // Check if date is in past
            if (cellDate < today) {
                // Check for weekends (Sundays)
                if (arg.date.getDay() === 0) {
                    arg.el.style.backgroundColor = '#f9fafb';
                    return;
                }

                const dateStr = arg.date.toISOString().split('T')[0];
                const hasSession = events.some(e => {
                    const start = new Date(e.start).setHours(0,0,0,0);
                    const end = e.end ? new Date(e.end).setHours(0,0,0,0) : start;
                    return cellDate >= start && cellDate < (e.end ? end : start + 86400000);
                });

                if (!hasSession) {
                    const frame = arg.el.querySelector('.fc-daygrid-day-frame');
                    const mark = document.createElement('div');
                    mark.className = 'absent-mark';
                    mark.innerHTML = '<i class="fe fe-user-x mr-1"></i> Absent';
                    frame.appendChild(mark);
                    arg.el.style.backgroundColor = '#fff5f5';
                }
            }
        }
    });

    calendar.render();

    // View Toggles
    document.getElementById('btn-calendar').addEventListener('click', function() {
        document.getElementById('calendar-wrapper').classList.remove('d-none');
        document.getElementById('list-wrapper').classList.add('d-none');
        calendar.render();
    });

    document.getElementById('btn-list').addEventListener('click', function() {
        document.getElementById('calendar-wrapper').classList.add('d-none');
        document.getElementById('list-wrapper').classList.remove('d-none');
    });

    // Auditor/Manager User Selector
    const userSel = document.getElementById('user-selector');
    if(userSel) {
        userSel.addEventListener('change', function() {
            window.location.href = `attendance-history?user_id=${this.value}`;
        });
    }
});
</script>

<?php include 'layout/footer.php'; ?>
