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
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="card-title mb-0 font-weight-bold text-muted small text-uppercase">Active Personnel</h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <?php if(empty($locations)): ?>
                                        <li class="list-group-item text-center py-5 text-muted small italic">
                                            <i class="fe fe-user-minus d-block mb-2 fe-24"></i>
                                            No field activity detected.
                                        </li>
                                    <?php endif; ?>
                                    <?php foreach($locations as $loc): ?>
                                    <li class="list-group-item border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm mr-3">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-weight-bold small shadow-sm">
                                                    <?php echo strtoupper(substr($loc['user_name'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="font-weight-600 mb-0"><?php echo htmlspecialchars($loc['user_name']); ?></div>
                                                <small class="text-muted d-block" style="font-size: 10px;">
                                                    <i class="fe fe-clock mr-1"></i><?php echo date('h:i A', strtotime($loc['logged_at'])); ?> • Accuracy: ±<?php echo round($loc['accuracy']); ?>m
                                                </small>
                                            </div>
                                            <button class="btn btn-sm btn-white shadow-sm rounded-circle" onclick="focusUser(<?php echo $loc['latitude']; ?>, <?php echo $loc['longitude']; ?>)">
                                                <i class="fe fe-map-pin text-primary"></i>
                                            </button>
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
    });
</script>

<style>
.bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); }
.font-weight-600 { font-weight: 600; }
.list-group-item:hover { background-color: #f8f9fa; }
</style>

<?php include 'layout/footer.php'; ?>
