<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Team Live Monitoring</h2>
                        <p class="text-muted">Real-time GPS status using HERE Location Services.</p>
                    </div>
                    <div class="btn-group shadow-sm">
                        <button type="button" class="btn btn-white" onclick="location.reload()">
                            <i class="fe fe-refresh-cw mr-1"></i> Refresh Data
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-0 border">
                                <div id="teamMap" style="height: 650px; border-radius: 8px; background: #f0f0f0;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 mb-4" style="max-height: 650px; overflow-y: auto; border: 1px solid #eee;">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                                <span class="dot bg-success mr-2"></span>
                                <h6 class="card-title mb-0 font-weight-bold text-muted small text-uppercase">Field Active <span class="badge badge-pill badge-success ml-2"><?php echo count($activePersonnel); ?></span></h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <?php if(empty($activePersonnel)): ?>
                                        <li class="list-group-item text-center py-4 text-muted small italic">
                                            No field activity in last 1 hour.
                                        </li>
                                    <?php endif; ?>
                                    <?php foreach($activePersonnel as $user): ?>
                                    <li class="list-group-item border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm mr-3">
                                                <span class="avatar-title rounded-circle bg-soft-success text-success font-weight-bold small shadow-sm">
                                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <div class="flex-fill overflow-hidden">
                                                <div class="font-weight-600 mb-0 text-truncate"><?php echo htmlspecialchars($user['name']); ?></div>
                                                <small class="text-success font-weight-bold d-block" style="font-size: 9px;">
                                                    <i class="fe fe-log-in mr-1"></i>Logged in: <?php echo date('h:i A', strtotime($user['attendance']['check_in_time'])); ?>
                                                </small>
                                                <small class="text-muted d-block text-truncate" id="addr-<?php echo $user['id']; ?>" style="font-size: 10px;">
                                                    Resolving location...
                                                </small>
                                                <small class="text-info" style="font-size: 9px;">
                                                    <i class="fe fe-clock mr-1"></i>Last Seen: <?php echo date('h:i A', strtotime($user['location']['logged_at'])); ?>
                                                </small>
                                            </div>
                                            <button class="btn btn-sm btn-white shadow-sm rounded-circle ml-2" onclick="focusUser(<?php echo $user['location']['latitude']; ?>, <?php echo $user['location']['longitude']; ?>)">
                                                <i class="fe fe-map-pin text-primary"></i>
                                            </button>
                                        </div>
                                    </li>
                                    <script>setTimeout(() => resolveAddress(<?php echo $user['location']['latitude']; ?>, <?php echo $user['location']['longitude']; ?>, 'addr-<?php echo $user['id']; ?>'), 500);</script>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div class="card-header bg-light py-2 border-top border-bottom d-flex align-items-center">
                                <span class="dot bg-warning mr-2"></span>
                                <h6 class="card-title mb-0 font-weight-bold text-muted small text-uppercase">Logged In (Inactive) <span class="badge badge-pill badge-warning ml-2"><?php echo count($inactivePersonnel); ?></span></h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush opacity-7">
                                    <?php if(empty($inactivePersonnel)): ?>
                                        <li class="list-group-item text-center py-3 text-muted small">No inactive users found.</li>
                                    <?php endif; ?>
                                    <?php foreach($inactivePersonnel as $user): ?>
                                    <li class="list-group-item border-0 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs mr-2">
                                                <span class="avatar-title rounded-circle bg-soft-warning text-warning font-weight-bold" style="font-size: 10px;">
                                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="small font-weight-600 text-muted"><?php echo htmlspecialchars($user['name']); ?></div>
                                                <small class="text-success d-block" style="font-size: 9px;">Logged in: <?php echo date('h:i A', strtotime($user['attendance']['check_in_time'])); ?></small>
                                                <?php if($user['location']): ?>
                                                    <small class="text-danger italic d-block" style="font-size: 9px;">Last signal: <?php echo date('h:i A', strtotime($user['location']['logged_at'])); ?> (> 1hr ago)</small>
                                                <?php else: ?>
                                                    <small class="text-danger italic d-block" style="font-size: 9px;">No GPS signals received today</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- New Sections: On Leave and Absent -->
                            <div class="card-header bg-light py-2 border-top border-bottom d-flex align-items-center">
                                <span class="dot bg-info mr-2"></span>
                                <h6 class="card-title mb-0 font-weight-bold text-muted small text-uppercase">On Leave <span class="badge badge-pill badge-info ml-2"><?php echo count($onLeave); ?></span></h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush opacity-7">
                                    <?php foreach($onLeave as $user): ?>
                                    <li class="list-group-item border-0 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs mr-2"><span class="avatar-title rounded-circle bg-soft-info text-info font-weight-bold" style="font-size: 10px;"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span></div>
                                            <div class="small font-weight-600 text-muted"><?php echo htmlspecialchars($user['name']); ?> <small class="text-info italic ml-1">(Approved)</small></div>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div class="card-header bg-light py-2 border-top border-bottom d-flex align-items-center">
                                <span class="dot bg-danger mr-2"></span>
                                <h6 class="card-title mb-0 font-weight-bold text-muted small text-uppercase">Absent <span class="badge badge-pill badge-danger ml-2"><?php echo count($absent); ?></span></h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush opacity-7">
                                    <?php foreach($absent as $user): ?>
                                    <li class="list-group-item border-0 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs mr-2"><span class="avatar-title rounded-circle bg-soft-danger text-danger font-weight-bold" style="font-size: 10px;"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span></div>
                                            <div class="small font-weight-600 text-muted"><?php echo htmlspecialchars($user['name']); ?> <small class="text-danger italic ml-1">(No Check-in)</small></div>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.HERE_API_KEY) {
            console.error("HERE API Key missing!");
            return;
        }

        const platform = new H.service.Platform({ 'apikey': window.HERE_API_KEY });
        const defaultLayers = platform.createDefaultLayers();
        const map = new H.Map(
            document.getElementById('teamMap'),
            defaultLayers.vector.normal.map,
            { zoom: 5, center: { lat: 20.5937, lng: 78.9629 } }
        );

        const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
        const ui = H.ui.UI.createDefault(map, defaultLayers);

        const locations = <?php echo json_encode($locations); ?>;
        const group = new H.map.Group();

        locations.forEach(loc => {
            const marker = new H.map.Marker({ lat: loc.latitude, lng: loc.longitude });
            
            // Custom HTML for the info bubble
            const html = `
                <div style="padding: 10px; min-width: 150px;">
                    <h6 style="margin: 0 0 5px 0;">${loc.user_name}</h6>
                    <small style="color: #666;">Last Seen: ${new Date(loc.logged_at).toLocaleTimeString()}</small><br>
                    <small style="color: #666;">Accuracy: ±${Math.round(loc.accuracy)}m</small>
                </div>
            `;
            
            marker.setData(html);
            marker.addEventListener('tap', (evt) => {
                const bubble = new H.ui.InfoBubble(evt.target.getGeometry(), {
                    content: evt.target.getData()
                });
                ui.addBubble(bubble);
            });
            
            group.addObject(marker);
        });

        map.addObject(group);

        if (locations.length > 0) {
            const bbox = group.getBoundingBox();
            if (bbox) {
                map.getViewModel().setLookAtData({ bounds: bbox, padding: {top: 100, left: 100, bottom: 100, right: 100} });
            }
        }

        window.focusUser = function(lat, lng) {
            map.setCenter({ lat, lng });
            map.setZoom(16);
        };

        window.addEventListener('resize', () => map.getViewPort().resize());

        window.resolveAddress = async function(lat, lng, elementId) {
            try {
                const url = `https://revgeocode.search.hereapi.com/v1/revgeocode?at=${lat},${lng}&lang=en-US&apikey=${window.HERE_API_KEY}`;
                const response = await fetch(url);
                const data = await response.json();
                if (data.items && data.items.length > 0) {
                    document.getElementById(elementId).innerText = data.items[0].address.label;
                } else {
                    document.getElementById(elementId).innerText = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                }
            } catch (e) {
                document.getElementById(elementId).innerText = "Location name unavailable";
            }
        };
    });
</script>

<style>
.dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; }
.bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); }
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.opacity-7 { opacity: 0.7; }
.font-weight-600 { font-weight: 600; }
.list-group-item:hover { background-color: #f8f9fa; }
</style>

<?php include 'layout/footer.php'; ?>
