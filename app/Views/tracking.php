<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="h3 mb-4 page-title">Live Tracking & Route</h2>
                
                <div class="row">
                    <div class="col-md-9 mb-4">
                        <div class="card shadow">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong class="card-title">Today's Route Visualization</strong>
                                <span id="sync-status" class="badge badge-success">Live Sync Active</span>
                            </div>
                            <div class="card-body p-0">
                                <div id="live-map" style="height: 600px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <strong class="card-title">Tracking Stats</strong>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Points Captured</small>
                                    <span class="h4" id="points-count"><?php echo count($route); ?></span>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Total Distance Today</small>
                                    <span class="h4" id="total-distance">0.00 KM</span>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted d-block">Last Sync</small>
                                    <span class="small" id="last-sync">Just now</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow mt-4">
                            <div class="card-header">
                                <strong class="card-title">Movement Log</strong>
                            </div>
                            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                                <ul class="list-group list-group-flush small" id="movement-list">
                                    <?php foreach(array_reverse($route) as $log): ?>
                                        <li class="list-group-item">
                                            <i class="fe fe-map-pin mr-2 text-primary"></i>
                                            <?php echo date('h:i A', strtotime($log['logged_at'])); ?> - 
                                            Lat: <?php echo round($log['latitude'], 4); ?>
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
        const routeData = <?php echo json_encode($route); ?>;
        const map = L.map('live-map').setView([20.5937, 78.9629], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        let polyline = L.polyline([], {color: 'blue', weight: 4, opacity: 0.7}).addTo(map);
        let markers = [];

        // Draw existing route
        if (routeData.length > 0) {
            const points = routeData.map(p => [parseFloat(p.latitude), parseFloat(p.longitude)]);
            polyline.setLatLngs(points);
            
            // Add marker for last point
            const last = points[points.length - 1];
            L.marker(last).addTo(map).bindPopup('Current Location').openPopup();
            map.setView(last, 13);
            
            calculateDistance(points);
        }

        function calculateDistance(points) {
            let total = 0;
            for (let i = 0; i < points.length - 1; i++) {
                total += L.latLng(points[i]).distanceTo(L.latLng(points[i+1]));
            }
            document.getElementById('total-distance').textContent = (total / 1000).toFixed(2) + " KM";
        }

        // Background Tracking Logic
        function logLocation(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            fetch('tracking?action=log', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `latitude=${lat}&longitude=${lng}&accuracy=${accuracy}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const now = new Date();
                    document.getElementById('last-sync').textContent = now.toLocaleTimeString();
                    
                    // Update map
                    const newPoint = [lat, lng];
                    polyline.addLatLng(newPoint);
                    
                    // Update stats
                    const count = parseInt(document.getElementById('points-count').textContent) + 1;
                    document.getElementById('points-count').textContent = count;
                    
                    // Add to list
                    const list = document.getElementById('movement-list');
                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.innerHTML = `<i class="fe fe-map-pin mr-2 text-primary"></i> ${now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - Lat: ${lat.toFixed(4)}`;
                    list.insertBefore(li, list.firstChild);
                }
            });
        }

        if (navigator.geolocation) {
            // Log every 5 minutes (reduced for battery efficiency, though user said "periodic")
            // For testing purposes, we can set it shorter
            setInterval(() => {
                navigator.geolocation.getCurrentPosition(logLocation, null, {enableHighAccuracy: true});
            }, 60000 * 5); // 5 minutes
            
            // Initial log
            navigator.geolocation.getCurrentPosition(logLocation, null, {enableHighAccuracy: true});
        }
    });
</script>

<?php include 'layout/footer.php'; ?>
