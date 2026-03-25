<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Client Interactions</h2>
                        <p class="text-muted">High-precision GPS mandatory for visit reporting (HERE Maps).</p>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Log Interaction</h5>
                            </div>
                            <div class="card-body pt-0">
                                <div id="location-verification" class="mb-4 bg-light rounded p-3 border">
                                    <div id="gps-spinner" class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                    <span class="text-dark small d-block font-weight-bold" id="gps-label">Establishing Pinpoint Lock...</span>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div id="gps-progress" class="progress-bar bg-warning" style="width: 10%"></div>
                                    </div>
                                    <small id="accuracy-info" class="text-muted mt-1 d-block" style="font-size: 10px;">Searching for satellite signal...</small>
                                </div>
                                
                                    <form id="meeting-form" method="POST" action="meetings?action=log">
                                        <input type="hidden" name="latitude" id="latitude">
                                        <input type="hidden" name="longitude" id="longitude">
                                        <input type="hidden" name="address" id="address">
                                        <input type="hidden" name="selfie_data" id="selfie_data">
                                        
                                        <div class="form-group mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="small font-weight-bold text-muted text-uppercase mb-0">Visit Selfie / Photo</label>
                                                <button type="button" class="btn btn-xs btn-link p-0" onclick="switchMeetingCamera()"><i class="fe fe-refresh-cw"></i> Switch</button>
                                            </div>
                                            <div id="selfie-container" style="position: relative; width: 100%; aspect-ratio: 4/3; background: #000; overflow: hidden;" class="rounded border">
                                                <video id="video-selfie" width="100%" height="100%" autoplay playsinline style="object-fit: cover;"></video>
                                                <canvas id="canvas-selfie" style="display:none;"></canvas>
                                                <img id="selfie-preview" style="display:none; width: 100%; height: 100%; object-fit: cover;" class="rounded">
                                                <div id="camera-overlay" class="d-flex align-items-center justify-content-center" style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); color: #fff;">
                                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                                </div>
                                            </div>
                                            <button type="button" id="capture-selfie-btn" class="btn btn-sm btn-outline-primary btn-block mt-2 font-weight-bold" disabled>
                                                <i class="fe fe-camera mr-1"></i> Capture Selfie
                                            </button>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label class="small font-weight-bold text-muted text-uppercase">Contact Person</label>
                                            <input type="text" name="client_name" class="form-control" placeholder="Name" required>
                                        </div>
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted text-uppercase">Hospital / Office</label>
                                        <input type="text" name="hospital_name" id="hospital_name" class="form-control" placeholder="Full Title" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted text-uppercase">Meeting Type</label>
                                        <select name="meeting_type" class="form-control custom-select">
                                            <option>Introductory</option>
                                            <option>Follow-up</option>
                                            <option>Product Demo</option>
                                            <option>Closing</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <textarea name="notes" class="form-control" rows="2" placeholder="Discussion summary..."></textarea>
                                    </div>
                                    <div class="form-group mb-4">
                                        <input type="text" name="outcome" class="form-control" placeholder="Next Steps / Outcome">
                                    </div>
                                    <button type="submit" id="log-meeting-btn" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm" disabled>
                                        <i class="fe fe-lock mr-1"></i> Locked: GPS Pending
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 mb-4 overflow-hidden border">
                            <div id="meeting-map" style="height: 350px; background: #eee;"></div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Recent History</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                            <tr>
                                                <th class="pl-4">Client</th>
                                                <th>Result</th>
                                                <th class="pr-4 text-right">Location</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($meetings)): ?>
                                                <tr><td colspan="3" class="text-center py-5 text-muted small italic">No interactions recorded.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($meetings as $m): ?>
                                                    <tr>
                                                        <td class="pl-4">
                                                            <div class="font-weight-600"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                                            <small class="text-muted d-block"><?php echo htmlspecialchars($m['hospital_office_name']); ?></small>
                                                            <small class="text-muted" style="font-size: 10px;"><?php echo date('d M, h:i A', strtotime($m['meeting_time'])); ?></small>
                                                        </td>
                                                        <td>
                                                            <div class="small text-dark"><?php echo htmlspecialchars($m['outcome']); ?></div>
                                                            <span class="badge <?php echo ($m['status'] == 'Approved') ? 'text-success' : (($m['status'] == 'Rejected') ? 'text-danger' : 'text-warning'); ?> p-0 mt-1 small">
                                                                <i class="fe fe-circle fe-10 mr-1"></i><?php echo $m['status']; ?>
                                                            </span>
                                                        </td>
                                                        <td class="pr-4 text-right">
                                                            <?php if($m['latitude']): ?>
                                                            <button class="btn btn-sm btn-white shadow-sm rounded-circle" onclick="focusMeeting(<?php echo $m['latitude']; ?>, <?php echo $m['longitude']; ?>)">
                                                                <i class="fe fe-map-pin text-primary"></i>
                                                            </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
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
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const selfieDataInput = document.getElementById('selfie_data');
        const video = document.getElementById('video-selfie');
        const canvas = document.getElementById('canvas-selfie');
        const captureBtn = document.getElementById('capture-selfie-btn');
        const selfiePreview = document.getElementById('selfie-preview');
        const cameraOverlay = document.getElementById('camera-overlay');
        const gpsProgress = document.getElementById('gps-progress');
        const gpsLabel = document.getElementById('gps-label');
        const accuracyInfo = document.getElementById('accuracy-info');
        const gpsSpinner = document.getElementById('gps-spinner');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const addrInput = document.getElementById('address');
        const btn = document.getElementById('log-meeting-btn');
        
        // Variables
        let map, marker, platform;
        let isGpsLocked = false;
        let isPhotoCaptured = false;
        let currentFacingMode = "user";
        let stream = null;
        const REQUIRED_ACCURACY = 100;

        function startCamera(mode) {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            navigator.mediaDevices.getUserMedia({ video: { facingMode: mode } })
                .then(s => {
                    stream = s;
                    video.srcObject = stream;
                    cameraOverlay.classList.add('d-none');
                    captureBtn.disabled = false;
                })
                .catch(err => {
                    console.error("Camera error:", err);
                    cameraOverlay.innerHTML = '<span class="small text-danger">Camera Access Denied/Unavailable</span>';
                });
        }

        window.switchMeetingCamera = function() {
            currentFacingMode = (currentFacingMode === "user") ? "environment" : "user";
            startCamera(currentFacingMode);
        };

        startCamera(currentFacingMode);

        captureBtn.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.5);
            if (dataUrl && dataUrl.length > 1000) {
                selfieDataInput.value = dataUrl;
                selfiePreview.src = dataUrl;
                selfiePreview.style.display = 'block';
                video.style.display = 'none';
                isPhotoCaptured = true;
                validateForm();
                captureBtn.innerHTML = '<i class="fe fe-refresh-cw mr-1"></i> Retake Photo';
                captureBtn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
            }
        });

        function validateForm() {
            if (isGpsLocked && isPhotoCaptured) {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fe fe-upload-cloud mr-1"></i> Submit Visit Report';
                    btn.classList.replace('btn-primary', 'btn-success');
                }
            }
        }

        // Initialize HERE
        platform = new H.service.Platform({'apikey': window.HERE_API_KEY});
        const defaultLayers = platform.createDefaultLayers();
        map = new H.Map(
            document.getElementById('meeting-map'),
            defaultLayers.vector.normal.map,
            { zoom: 14, center: { lat: 20, lng: 77 } }
        );
        const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
        const ui = H.ui.UI.createDefault(map, defaultLayers);

        function monitorPrecision() {
            navigator.geolocation.watchPosition(function(pos) {
                const accuracy = pos.coords.accuracy;
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                
                let progress = accuracy < 100 ? 100 : (accuracy < 300 ? 80 : 20);
                if (gpsProgress) gpsProgress.style.width = progress + "%";
                if (accuracyInfo) accuracyInfo.textContent = `Accuracy: ±${Math.round(accuracy)}m (Target: <100m)`;

                if (accuracy <= REQUIRED_ACCURACY) {
                    if (latInput) latInput.value = lat;
                    if (lngInput) lngInput.value = lng;
                    isGpsLocked = true;
                    
                    if (gpsLabel) gpsLabel.innerHTML = '<i class="fe fe-check-circle text-success mr-2"></i>Location Verified';
                    if (gpsProgress) gpsProgress.classList.replace('bg-warning', 'bg-success');
                    if (gpsSpinner) gpsSpinner.classList.add('d-none');
                    
                    validateForm();

                    // Update Map
                    map.setCenter({ lat, lng });
                    map.setZoom(16);
                    if (!marker) {
                        marker = new H.map.Marker({ lat, lng });
                        map.addObject(marker);
                    } else {
                        marker.setGeometry({ lat, lng });
                    }

                    // Reverse Geocode
                    if (addrInput && !addrInput.value) {
                        fetch(`map?action=geocode&q=${lat},${lng}`)
                            .then(r => r.json())
                            .then(data => {
                                addrInput.value = data.address || "Geo-tagged Position";
                            });
                    }
                } else {
                    if (gpsLabel) gpsLabel.textContent = "Refining Satellite Lock...";
                    if (gpsProgress) gpsProgress.classList.replace('bg-success', 'bg-warning');
                }

            }, (err) => {
                console.error("GPS Error:", err);
                if (gpsLabel) gpsLabel.textContent = "GPS Access Denied/Error";
            }, {enableHighAccuracy: true, maximumAge: 0});
        }

        window.focusMeeting = function(lat, lng) {
            map.setCenter({ lat, lng });
            map.setZoom(17);
            if(marker) marker.setGeometry({ lat, lng });
        };

        if (navigator.geolocation) monitorPrecision();
        window.addEventListener('resize', () => map.getViewPort().resize());
    });
</script>

<style>
.badge-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; }
.font-weight-600 { font-weight: 600; }
.progress-bar { transition: width 0.5s ease; }
</style>

<?php include 'layout/footer.php'; ?>
