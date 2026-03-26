<?php include_once 'includes/header.php'; ?>

<!-- Calendar Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<div class="main-content" style="padding: 20px; background: #f8f9fa; min-height: 100vh;">
    <div class="container-fluid">
        
        <!-- Header & Filters -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h3 class="mb-0 font-weight-bold" style="color: #2D3748;">Attendance Tracking</h3>
                <p class="text-muted small mb-0">Monthly shift & leave audit logs</p>
            </div>
            
            <div class="col-md-6 text-right d-flex justify-content-end align-items-center">
                <?php if(!empty($users)): ?>
                <div class="mr-3">
                    <select id="user-selector" class="form-control form-control-sm border-0 shadow-sm" style="min-width: 200px; border-radius: 8px;">
                        <?php foreach($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $selectedUser) ? 'selected' : ''; ?>>
                                <?php echo $u['name']; ?> (<?php echo $u['role']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="btn-group shadow-sm bg-white p-1" style="border-radius: 10px;">
                    <button class="btn btn-sm btn-light active px-3" id="btn-calendar" style="border-radius: 8px;"><i class="fe fe-calendar mr-2"></i>Calendar</button>
                    <button class="btn btn-sm btn-light px-3" id="btn-list" style="border-radius: 8px;"><i class="fe fe-list mr-2"></i>List</button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Calendar Section -->
            <div class="col-12" id="calendar-wrapper">
                <div class="card shadow-sm border-0 p-4" style="border-radius: 15px;">
                    <div id="attendance-calendar"></div>
                    
                    <!-- Legend -->
                    <div class="d-flex mt-4 pt-3 border-top justify-content-center">
                        <div class="d-flex align-items-center mr-4">
                            <span style="width: 12px; height: 12px; border-radius: 2px; background: #28a745; display: inline-block; margin-right: 8px;"></span>
                            <small class="font-weight-bold text-muted">Present</small>
                        </div>
                        <div class="d-flex align-items-center mr-4">
                            <span style="width: 12px; height: 12px; border-radius: 2px; background: #ffc107; display: inline-block; margin-right: 8px;"></span>
                            <small class="font-weight-bold text-muted">On Leave</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span style="width: 12px; height: 12px; border-radius: 2px; background: #dc3545; display: inline-block; margin-right: 8px;"></span>
                            <small class="font-weight-bold text-muted">Absent</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Section (Hidden by Default) -->
            <div class="col-12 d-none" id="list-wrapper">
                <div class="card shadow-sm border-0" style="border-radius: 15px;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light small text-uppercase font-weight-bold">
                                <tr>
                                    <th class="pl-4">Date</th>
                                    <th>Shift ID</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Duration</th>
                                    <th class="pr-4 text-right">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($records)): ?>
                                    <?php foreach($records as $r): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-bold"><?php echo date('D, d M y', strtotime($r['check_in_time'])); ?></td>
                                        <td><span class="badge badge-light">#<?php echo $r['id']; ?></span></td>
                                        <td><span class="text-success"><?php echo date('h:i A', strtotime($r['check_in_time'])); ?></span></td>
                                        <td><span class="<?php echo $r['check_out_time'] ? 'text-danger' : 'text-muted italic'; ?>"><?php echo $r['check_out_time'] ? date('h:i A', strtotime($r['check_out_time'])) : 'Open'; ?></span></td>
                                        <td>
                                            <?php if($r['check_out_time']): 
                                                $d = (strtotime($r['check_out_time']) - strtotime($r['check_in_time'])) / 3600;
                                                echo round($d, 1) . ' hrs';
                                            endif; ?>
                                        </td>
                                        <td class="text-right pr-4">
                                            <a href="attendance-edit?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="fe fe-eye"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No attendance sessions recorded for this month.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --fc-border-color: #EDF2F7;
        --fc-daygrid-dot-event-width: 8px;
    }
    .fc-theme-standard td, .fc-theme-standard th { border: 1px solid #EDF2F7 !important; }
    .fc .fc-toolbar-title { font-size: 1.1rem !important; color: #4A5568 !important; font-weight: 700 !important; }
    .fc .fc-button-primary { background-color: #fff !important; color: #718096 !important; border: 1px solid #E2E8F0 !important; font-size: 0.8rem; font-weight: 600; padding: 5px 10px; border-radius: 8px !important; }
    .fc .fc-button-primary:hover { background-color: #F7FAFC !important; color: #2D3748 !important; }
    
    .fc-daygrid-day-number { font-size: 0.85rem; color: #718096; padding: 10px !important; text-decoration: none !important; }
    .fc-daygrid-day.fc-day-today { background-color: #EBF8FF !important; }
    
    .status-badge-present { background-color: #C6F6D5 !important; border-radius: 4px; padding: 2px 8px; color: #22543D !important; font-weight: 700; font-size: 10px; border: none; }
    .status-badge-leave { background-color: #FEFCBF !important; border-radius: 4px; padding: 2px 8px; color: #744210 !important; font-weight: 700; font-size: 10px; border: none; }
    .absent-mark { display: flex; align-items: center; justify-content: center; height: 100%; width: 100%; color: #E53E3E; font-weight: 700; font-size: 10px; opacity: 0.6; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('attendance-calendar');
    const events = [];

    // Map Attendance to Events
    <?php foreach($records as $r): ?>
    events.push({
        title: 'PRESENT',
        start: '<?php echo date('Y-m-d', strtotime($r['check_in_time'])); ?>',
        classNames: ['status-badge-present'],
        display: 'block'
    });
    <?php endforeach; ?>

    // Map Leaves to Events
    <?php foreach($leaves as $l): ?>
    let eDate = new Date('<?php echo $l['end_date']; ?>');
    eDate.setDate(eDate.getDate() + 1); 
    
    events.push({
        title: 'ON LEAVE (<?php echo $l['type_name']; ?>)',
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
            const dateStr = arg.date.toISOString().split('T')[0];

            if (cellDate < today) {
                const hasSession = events.some(e => {
                    const s = new Date(e.start).setHours(0,0,0,0);
                    const end = e.end ? new Date(e.end).setHours(0,0,0,0) : s;
                    return cellDate >= s && cellDate < (e.end ? end : s + 86400000);
                });

                if (!hasSession && arg.date.getDay() !== 0) { // All skip Sundays for absent marking
                    const frame = arg.el.querySelector('.fc-daygrid-day-frame');
                    const mark = document.createElement('div');
                    mark.className = 'absent-mark';
                    mark.innerHTML = '<i class="fe fe-slash mr-1"></i>ABSENT';
                    frame.appendChild(mark);
                    arg.el.style.backgroundColor = '#FFF5F5';
                }
            }
        }
    });

    calendar.render();

    // View Switching Logic
    document.getElementById('btn-calendar').addEventListener('click', function() {
        document.getElementById('calendar-wrapper').classList.remove('d-none');
        document.getElementById('list-wrapper').classList.add('d-none');
        this.classList.add('active');
        document.getElementById('btn-list').classList.remove('active');
        calendar.render();
    });

    document.getElementById('btn-list').addEventListener('click', function() {
        document.getElementById('calendar-wrapper').classList.add('d-none');
        document.getElementById('list-wrapper').classList.remove('d-none');
        this.classList.add('active');
        document.getElementById('btn-calendar').classList.remove('active');
    });

    // User Selection Redirect
    const sel = document.getElementById('user-selector');
    if(sel) {
        sel.addEventListener('change', function() {
            window.location.href = `attendance-history?user_id=${this.value}`;
        });
    }
});
</script>

<?php include_once 'includes/footer.php'; ?>
