<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content full-width-hub">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 px-0">
                <!-- Dashboard Header -->
                <div class="hub-header mb-4 bg-dark text-white p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h3 mb-1 font-weight-bold">Travel Intelligence Audit</h2>
                            <p class="text-white-50 mb-0">High-resolution route mapping and distance verification</p>
                        </div>
                        <div class="text-right">
                            <div class="h2 mb-0 font-weight-bold text-success"><?php echo $distance; ?> <small class="text-white-50">KM</small></div>
                            <div class="small text-white-50 text-uppercase font-weight-bold tracking-wider">Total Daily Travel</div>
                        </div>
                    </div>
                </div>

                <!-- Audit Filters -->
                <div class="card shadow-sm border-0 mb-4 mx-4" style="border-radius: 15px; margin-top: -30px;">
                    <div class="card-body p-3">
                        <form method="GET" action="travel-history" class="row align-items-end">
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-muted text-uppercase">Select Employee</label>
                                <select name="user_id" class="form-control custom-select bg-light border-0 shadow-none">
                                    <?php foreach($employees as $emp): ?>
                                        <option value="<?php echo $emp['id']; ?>" <?php echo ($emp['id'] == $selected_user) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($emp['name']); ?> (<?php echo htmlspecialchars($emp['role']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Audit Date</label>
                                <input type="date" name="date" class="form-control bg-light border-0 shadow-none" value="<?php echo $selected_date; ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                    <i class="fe fe-search mr-1"></i> Audit Path
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row mx-4">
                    <!-- Left: Route Map -->
                    <div class="col-md-8 px-2">
                        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                            <div id="route-map" style="width: 100%; height: 600px; background: #f0f0f0;"></div>
                        </div>
                    </div>

                    <!-- Right: Coordinate List -->
                    <div class="col-md-4 px-2">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Chronological Path Logs</h6>
                            </div>
                            <div class="card-body p-0" style="max-height: 540px; overflow-y: auto;" data-simplebar>
                                <ul class="list-group list-group-flush">
                                    <?php if(empty($route)): ?>
                                        <li class="list-group-item text-center py-5 text-muted italic">No movement logs registered for this period.</li>
                                    <?php else: ?>
                                        <?php foreach($route as $index => $log): ?>
                                            <li class="list-group-item px-4 border-0 mb-2 location-log-item" onclick="focusRoutePoint(<?php echo $index; ?>)">
                                                <div class="d-flex align-items-start">
                                                    <div class="mr-3 mt-1">
                                                        <span class="dot dot-md <?php echo ($index == 0) ? 'bg-success' : (($index == count($route)-1) ? 'bg-danger' : 'bg-primary'); ?>"></span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="font-weight-bold text-dark small">
                                                                <?php echo ($index == 0) ? 'Start Position' : (($index == count($route)-1) ? 'Final Position' : 'Trace Point #'.$index); ?>
                                                            </div>
                                                            <div class="small text-muted"><?php echo date('h:i:A', strtotime($log['logged_at'])); ?></div>
                                                        </div>
                                                        <div class="text-muted small mt-1 italic" style="font-size: 0.75rem;">
                                                            Lat: <?php echo $log['latitude']; ?>, Lng: <?php echo $log['longitude']; ?>
                                                        </div>
                                                        <div class="mt-2">
                                                            <span class="badge badge-light border small px-2">Accuracy: <?php echo round($log['accuracy']); ?>m</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.full-width-hub { padding-left: 0 !important; padding-right: 0 !important; max-width: 100% !important; overflow-x: hidden; }
.location-log-item { cursor: pointer; transition: all 0.2s ease; border-radius: 10px; margin: 0 1rem; }
.location-log-item:hover { background: rgba(0,0,0,0.03); transform: translateX(5px); }
.dot-md { width: 10px; height: 10px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const route = <?php echo json_encode($route); ?>;
    if(!route.length) return;

    // Initialize HERE Platform
    const platform = new H.service.Platform({'apikey': window.HERE_API_KEY});
    const defaultLayers = platform.createDefaultLayers();
    const map = new H.Map(
        document.getElementById('route-map'),
        defaultLayers.vector.normal.map,
        { zoom: 14, center: { lat: parseFloat(route[0].latitude), lng: parseFloat(route[0].longitude) } }
    );
    const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
    const ui = H.ui.UI.createDefault(map, defaultLayers);

    // Create Polyline for the trace
    const lineString = new H.geo.LineString();
    route.forEach(p => {
        lineString.pushPoint({lat: parseFloat(p.latitude), lng: parseFloat(p.longitude)});
        
        // Add markers for Start/End
        const isStart = route.indexOf(p) === 0;
        const isEnd = route.indexOf(p) === route.length - 1;
        
        if(isStart || isEnd) {
             const markerIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${isStart ? '#28a745' : '#dc3545'}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>`;
             const icon = new H.map.Icon(markerIcon);
             const marker = new H.map.Marker({lat: parseFloat(p.latitude), lng: parseFloat(p.longitude)}, {icon: icon});
             map.addObject(marker);
        }
    });

    const polyline = new H.map.Polyline(lineString, {
        style: { lineWidth: 4, strokeColor: 'rgba(0, 123, 255, 0.7)', lineDash: [5, 2] }
    });
    map.addObject(polyline);

    // Auto-zoom to fit route
    map.getViewModel().setLookAtData({ bounds: polyline.getBoundingBox() });

    window.focusRoutePoint = (index) => {
        const point = route[index];
        map.setCenter({lat: parseFloat(point.latitude), lng: parseFloat(point.longitude)});
        map.setZoom(17);
    };
});
</script>

<?php include 'layout/footer.php'; ?>
