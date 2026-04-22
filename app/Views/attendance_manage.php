<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<style>
    :root {
        --p-primary: #4361ee;
        --p-secondary: #3f37c9;
        --p-success: #4cc9f0;
        --p-warning: #f72585;
        --p-dark: #212529;
        --p-gray: #f8f9fa;
        --p-radius: 16px;
    }

    .main-content {
        transition: all 0.3s ease;
    }

    /* ---- High-End Dashboard Styling ---- */
    .dashboard-header {
        background: linear-gradient(135deg, #fff 0%, #fcfdfe 100%);
        padding: 2rem;
        border-radius: var(--p-radius);
        margin-bottom: 2rem;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--p-radius);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05);
    }

    /* ---- Analytics Cards ---- */
    .stat-card-premium {
        padding: 1.5rem;
        border-radius: var(--p-radius);
        background: #fff;
        border: 1px solid #eef0f2;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .stat-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        border-color: var(--p-primary);
    }
    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    /* ---- Filter Bar ---- */
    .filter-drawer {
        background: #fff;
        border-radius: var(--p-radius);
        padding: 1.25rem;
        margin-bottom: 2rem;
        border: 1px solid #eef0f2;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    .filter-group label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #adb5bd;
        margin-bottom: 0.5rem;
        display: block;
        letter-spacing: 0.5px;
    }
    .filter-control {
        height: 45px;
        border-radius: 10px;
        border: 1.5px solid #eef0f2;
        padding: 0 15px;
        font-weight: 500;
        width: 100%;
        transition: all 0.2s;
    }
    .filter-control:focus {
        border-color: var(--p-primary);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
        outline: none;
    }

    /* ---- Modern Table ---- */
    .premium-table-container {
        background: #fff;
        border-radius: var(--p-radius);
        border: 1px solid #eef0f2;
        overflow: hidden;
    }
    .premium-table thead th {
        background: #f8faff;
        padding: 1.25rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #495057;
        border-bottom: 2px solid #eef0f2;
    }
    .premium-table tbody tr {
        transition: all 0.2s;
    }
    .premium-table tbody tr:hover {
        background: rgba(67,97,238,0.02);
    }
    .premium-table td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }

    /* ---- Avatars & Badges ---- */
    .avatar-stack {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, var(--p-primary), var(--p-secondary));
        box-shadow: 0 4px 10px rgba(67,97,238,0.2);
    }
    .status-pill {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .pulse-active {
        background: #10b981;
        box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0px rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0px rgba(16, 185, 129, 0); }
    }

    /* ---- Verification Stack ---- */
    .v-stack {
        display: flex;
        gap: 4px;
    }
    .v-item {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: #adb5bd;
        border: 1px solid #eef0f2;
        cursor: pointer;
        transition: all 0.2s;
    }
    .v-item.active {
        background: rgba(76, 201, 240, 0.1);
        color: var(--p-success);
        border-color: var(--p-success);
    }
    .v-item:hover {
        transform: scale(1.1);
        z-index: 2;
    }

    /* ---- Control Group ---- */
    .action-btn-group {
        display: flex;
        gap: 8px;
    }
    .btn-icon-square {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
    }
</style>

<main role="main" class="main-content">
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h3 font-weight-bold mb-1 text-dark">Attendance Logistics</h2>
            <p class="text-muted mb-0">Management Command Center / Reviewing Check-In Intelligence</p>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-primary shadow-sm font-weight-bold px-4 py-2" style="border-radius:10px;" id="btn-global-map">
                <i class="fe fe-map mr-2"></i> Operational Map View
            </button>
            <button class="btn btn-dark shadow-sm font-weight-bold px-4 py-2" style="border-radius:10px;" onclick="exportRecords()">
                <i class="fe fe-download mr-2"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Analytics Snapshot -->
    <div class="row mb-4">
        <?php 
            $onDuty = 0; $coverage = 0; $verificationHits = 0;
            foreach($records as $r) {
                if(!$r['check_out_time'] && date('Y-m-d', strtotime($r['check_in_time'])) == date('Y-m-d')) $onDuty++;
                if($r['check_in_photo']) $verificationHits++;
            }
            $execCount = count(array_unique(array_column($records, 'user_id')));
        ?>
        <div class="col-md-3">
            <div class="stat-card-premium shadow-sm">
                <div class="stat-icon-box bg-soft-success text-success" style="background: rgba(16, 185, 129, 0.1);">
                    <i class="fe fe-user-check"></i>
                </div>
                <div class="h3 font-weight-bold mb-0 text-dark"><?php echo $onDuty; ?></div>
                <div class="small text-muted font-weight-bold text-uppercase mt-1">Executives On-Duty</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-premium shadow-sm">
                <div class="stat-icon-box bg-soft-primary text-primary" style="background: rgba(67, 97, 238, 0.1);">
                    <i class="fe fe-users"></i>
                </div>
                <div class="h3 font-weight-bold mb-0 text-dark"><?php echo $execCount; ?></div>
                <div class="small text-muted font-weight-bold text-uppercase mt-1">Personnel Tracked</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-premium shadow-sm">
                <div class="stat-icon-box bg-soft-info text-info" style="background: rgba(76, 201, 240, 0.1);">
                    <i class="fe fe-shield"></i>
                </div>
                <div class="h3 font-weight-bold mb-0 text-dark"><?php echo $verificationHits; ?></div>
                <div class="small text-muted font-weight-bold text-uppercase mt-1">Verification Logs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-premium shadow-sm border-0 bg-dark text-white">
                <div class="stat-icon-box bg-secondary text-white">
                    <i class="fe fe-activity"></i>
                </div>
                <div class="h3 font-weight-bold mb-0 text-white"><?php echo count($records); ?></div>
                <div class="small text-muted-white font-weight-bold text-uppercase mt-1" style="opacity:0.8;">Total Interactions</div>
            </div>
        </div>
    </div>

    <!-- Filter Drawer -->
    <form class="filter-drawer shadow-sm" method="GET" action="attendance">
        <div class="filter-group">
            <label>Executive Name</label>
            <select name="user_id" class="filter-control">
                <option value="">All Personnel</option>
                <?php foreach($users as $u): ?>
                    <option value="<?php echo $u['id']; ?>" <?php echo ($filters['user_id'] == $u['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($u['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>From Date</label>
            <input type="date" name="date_from" class="filter-control" value="<?php echo $filters['date_from']; ?>">
        </div>
        <div class="filter-group">
            <label>To Date</label>
            <input type="date" name="date_to" class="filter-control" value="<?php echo $filters['date_to']; ?>">
        </div>
        <div class="filter-group" style="flex: 1.5;">
            <label>Search Intelligence</label>
            <input type="text" name="search" class="filter-control" placeholder="Search by address, name..." value="<?php echo htmlspecialchars($filters['search']); ?>">
        </div>
        <div class="d-flex" style="gap: 8px;">
            <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="height:45px; border-radius:10px;">Apply</button>
            <a href="attendance" class="btn btn-light px-4 d-flex align-items-center" style="height:45px; border-radius:10px;"><i class="fe fe-refresh-cw"></i></a>
        </div>
    </form>

    <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <!-- Main Data Table -->
    <div class="premium-table-container shadow-sm mb-5">
        <table class="table premium-table mb-0" id="attendanceTable">
            <thead>
                <tr>
                    <th>Associate</th>
                    <th>Intelligence Log</th>
                    <th>In-Transit</th>
                    <th>Out-Transit</th>
                    <th>Verification</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($records)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted italic">No attendance intelligence recorded for the selected criteria.</td></tr>
                <?php else: ?>
                    <?php foreach($records as $r): ?>
                        <tr data-record='<?php echo json_encode($r); ?>'>
                            <td>
                                <div class="avatar-stack">
                                    <div class="avatar-circle">
                                        <?php echo strtoupper(substr($r['user_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($r['user_name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($r['user_phone'] ?? 'ID: #'.$r['user_id']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold small"><?php echo date('d M Y', strtotime($r['check_in_time'])); ?></div>
                                <div class="text-muted small italic"><?php echo date('l', strtotime($r['check_in_time'])); ?></div>
                            </td>
                            <td>
                                <div class="small font-weight-bold text-primary"><?php echo date('h:i A', strtotime($r['check_in_time'])); ?></div>
                                <div class="text-muted small text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($r['check_in_address']); ?>">
                                    <i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars($r['check_in_address']); ?>
                                </div>
                            </td>
                            <td>
                                <?php if($r['check_out_time']): ?>
                                    <div class="small font-weight-bold text-dark"><?php echo date('h:i A', strtotime($r['check_out_time'])); ?></div>
                                    <div class="text-muted small text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($r['check_out_address']); ?>">
                                        <i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars($r['check_out_address']); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small italic">Still In-Transit</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="v-stack">
                                    <div class="v-item <?php echo $r['check_in_photo'] ? 'active' : ''; ?>" title="Check-In Photo" onclick="viewAudit(<?php echo $r['id']; ?>)">
                                        <i class="fe fe-camera"></i>
                                    </div>
                                    <div class="v-item <?php echo $r['odometer_photo'] ? 'active' : ''; ?>" title="Odometer Log" onclick="viewAudit(<?php echo $r['id']; ?>)">
                                        <i class="fe fe-activity"></i>
                                    </div>
                                    <div class="v-item <?php echo $r['check_out_photo'] ? 'active' : ''; ?>" title="Check-Out Photo" onclick="viewAudit(<?php echo $r['id']; ?>)">
                                        <i class="fe fe-shield"></i>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if(!$r['check_out_time']): ?>
                                    <div class="status-pill border border-success text-success" style="background: rgba(16,185,129,0.05);">
                                        <span class="status-pulse pulse-active"></span>
                                        Log Active
                                    </div>
                                <?php else: ?>
                                    <div class="status-pill border border-light text-muted">
                                        <i class="fe fe-check-circle"></i> Complete
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="action-btn-group justify-content-end">
                                    <button class="btn btn-icon-square btn-light text-primary" onclick="viewAudit(<?php echo $r['id']; ?>)" title="Audit Details">
                                        <i class="fe fe-eye"></i>
                                    </button>
                                    <a href="attendance-edit?id=<?php echo $r['id']; ?>" class="btn btn-icon-square btn-light text-dark" title="Correction">
                                        <i class="fe fe-edit-3"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Audit Intelligence Modal -->
<div class="modal fade" id="auditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius:24px; overflow:hidden;">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title font-weight-bold"><i class="fe fe-shield mr-2 text-primary"></i>Security Audit Intelligence</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="row no-gutters">
                    <div class="col-md-7 p-0 bg-light" style="height: 600px;">
                        <div id="audit-map" style="width:100%; height:100%;"></div>
                    </div>
                    <div class="col-md-5 p-4 bg-white" style="height: 600px; overflow-y: auto;">
                        <div class="mb-4">
                            <h4 id="audit-user" class="font-weight-bold text-dark mb-1"></h4>
                            <p id="audit-date" class="text-muted small font-weight-bold text-uppercase"></p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="small text-muted font-weight-bold text-uppercase mb-2 d-block">Check-In Verification</label>
                                <div id="check-in-asset" class="rounded overflow-hidden border shadow-sm" style="height: 200px; background: #eee;">
                                    <img src="" class="w-100 h-100 object-fit-cover audit-img">
                                </div>
                                <div id="check-in-odometer" class="mt-2 text-primary font-weight-bold small"></div>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted font-weight-bold text-uppercase mb-2 d-block">Check-Out Verification</label>
                                <div id="check-out-asset" class="rounded overflow-hidden border shadow-sm" style="height: 200px; background: #eee;">
                                    <img src="" class="w-100 h-100 object-fit-cover audit-img">
                                </div>
                                <div id="check-out-odometer" class="mt-2 text-primary font-weight-bold small"></div>
                            </div>
                        </div>

                        <div class="p-3 rounded-lg bg-light border mb-4">
                            <label class="small text-muted font-weight-bold text-uppercase d-block mb-1">Telemetry Data</label>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">Check-In Accuracy</span>
                                <span class="small font-weight-bold text-dark">High Precision (GPS)</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted">Distance Traveled</span>
                                <span id="audit-distance" class="small font-weight-bold text-primary">Calculating...</span>
                            </div>
                        </div>

                        <div class="bg-soft-primary p-3 rounded-lg mb-4" style="background: rgba(67,97,238,0.05);">
                            <label class="small text-primary font-weight-bold text-uppercase d-block mb-1">Registered Address</label>
                            <div id="audit-address" class="small text-dark font-weight-500"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-outline-secondary font-weight-bold px-4" data-dismiss="modal">Close Audit</button>
                <a href="#" id="audit-edit-btn" class="btn btn-primary font-weight-bold px-4">Correct Record</a>
            </div>
        </div>
    </div>
</div>

<!-- Global Operational Map Modal -->
<div class="modal fade" id="globalMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius:24px; overflow:hidden;">
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title font-weight-bold"><i class="fe fe-map mr-2"></i>Global Operational Intelligence</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0" style="height: 700px;">
                <div id="global-tracking-map" style="width:100%; height:100%;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let platform = new H.service.Platform({'apikey': window.HERE_API_KEY });
    let auditMap, globalTrackingMap;
    let auditMarkerIn, auditMarkerOut;
    const records = <?php echo json_encode($records); ?>;

    window.viewAudit = (id) => {
        const record = records.find(r => r.id == id);
        if(!record) return;

        document.getElementById('audit-user').textContent = record.user_name;
        document.getElementById('audit-date').textContent = new Date(record.check_in_time).toLocaleDateString('en-IN', {day:'2-digit', month:'long', year:'numeric'}) + ' (' + record.user_phone + ')';
        document.getElementById('audit-address').textContent = record.check_in_address;
        document.getElementById('audit-edit-btn').href = 'attendance-edit?id=' + record.id;
        
        // Photos
        const cinImg = document.querySelector('#check-in-asset img');
        cinImg.src = record.check_in_photo || 'assets/images/placeholder.jpg';
        const coutImg = document.querySelector('#check-out-asset img');
        coutImg.src = record.check_out_photo || 'assets/images/placeholder.jpg';

        // Odometer
        document.getElementById('check-in-odometer').textContent = record.odometer_reading ? 'Reading: ' + record.odometer_reading + ' km' : 'No Reading';
        document.getElementById('check-out-odometer').textContent = record.check_out_odometer_reading ? 'Reading: ' + record.check_out_odometer_reading + ' km' : 'No Reading';

        // Distance
        if(record.odometer_reading && record.check_out_odometer_reading) {
            document.getElementById('audit-distance').textContent = (record.check_out_odometer_reading - record.odometer_reading).toFixed(2) + ' km';
        } else {
            document.getElementById('audit-distance').textContent = 'N/A';
        }

        $('#auditModal').modal('show');

        // Map Initialization
        setTimeout(() => {
            if(!auditMap) {
                const defaultLayers = platform.createDefaultLayers();
                auditMap = new H.Map(document.getElementById('audit-map'), defaultLayers.vector.normal.map, {
                    zoom: 14,
                    center: { lat: parseFloat(record.check_in_lat), lng: parseFloat(record.check_in_lng) }
                });
                new H.mapevents.Behavior(new H.mapevents.MapEvents(auditMap));
                H.ui.UI.createDefault(auditMap, defaultLayers);
            }

            if(auditMarkerIn) auditMap.removeObject(auditMarkerIn);
            if(auditMarkerOut) auditMap.removeObject(auditMarkerOut);

            auditMarkerIn = new H.map.Marker({lat: parseFloat(record.check_in_lat), lng: parseFloat(record.check_in_lng)});
            auditMap.addObject(auditMarkerIn);

            if(record.check_out_lat) {
                auditMarkerOut = new H.map.Marker({lat: parseFloat(record.check_out_lat), lng: parseFloat(record.check_out_lng)});
                auditMap.addObject(auditMarkerOut);
                auditMap.getViewModel().setLookAtData({bounds: auditMap.getObjects().reduce((acc, obj) => acc.extend(obj.getGeometry().getLatLngBound()), new H.geo.Rect(record.check_in_lat, record.check_in_lng, record.check_in_lat, record.check_in_lng))});
            } else {
                auditMap.setCenter({lat: parseFloat(record.check_in_lat), lng: parseFloat(record.check_in_lng)});
                auditMap.setZoom(16);
            }
        }, 300);
    };

    document.getElementById('btn-global-map').onclick = () => {
        $('#globalMapModal').modal('show');
        setTimeout(() => {
            if(!globalTrackingMap) {
                const defaultLayers = platform.createDefaultLayers();
                globalTrackingMap = new H.Map(document.getElementById('global-tracking-map'), defaultLayers.vector.normal.map, {
                    zoom: 12,
                    center: { lat: 20.5937, lng: 78.9629 } // India Center
                });
                new H.mapevents.Behavior(new H.mapevents.MapEvents(globalTrackingMap));
                H.ui.UI.createDefault(globalTrackingMap, defaultLayers);
            }
            
            globalTrackingMap.removeObjects(globalTrackingMap.getObjects());
            const group = new H.map.Group();

            records.forEach(r => {
                if(r.check_in_lat && r.check_in_lng) {
                    const marker = new H.map.Marker({lat: parseFloat(r.check_in_lat), lng: parseFloat(r.check_in_lng)});
                    marker.setData(`<b>${r.user_name}</b><br>${r.check_in_time}<br><button class="btn btn-xs btn-primary mt-1" onclick="viewAudit(${r.id})">Details</button>`);
                    group.addObject(marker);
                }
            });

            globalTrackingMap.addObject(group);
            globalTrackingMap.getViewModel().setLookAtData({bounds: group.getBoundingBox()});
        }, 300);
    };

    function exportRecords() {
        let csv = 'Associate,Date,Check-In Time,Check-In Address,Check-Out Time,Check-Out Address,Status\n';
        records.forEach(r => {
            csv += `"${r.user_name}","${r.check_in_time}","${r.check_in_time}","${r.check_in_address}","${r.check_out_time || 'Active'}","${r.check_out_address || '-'}","${r.check_out_time ? 'Complete' : 'Active'}"\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', 'attendance_log.csv');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>

<?php include 'layout/footer.php'; ?>
