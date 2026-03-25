<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Daily Attendance</h2>
                        <p class="text-muted">High-precision GPS & Photo verification system (HERE Maps).</p>
                    </div>
                    <div id="live-clock" class="h4 mb-0 font-weight-bold text-primary">
                        <?php echo date('h:i:s A'); ?>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center p-3">
                                <!-- Selfie Section (Modal-based) -->
                                <div class="p-3 mb-4 bg-soft-primary rounded border border-primary">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 font-weight-bold text-primary"><i class="fe fe-user mr-2"></i> Identification</h6>
                                        <button type="button" id="open-selfie-modal" class="btn btn-sm btn-primary shadow-sm font-weight-bold" data-toggle="modal" data-target="#selfieModal">
                                            <i class="fe fe-camera mr-1"></i> Capture Selfie
                                        </button>
                                    </div>
                                    <div id="selfie-status-box" class="alert alert-light border m-0 p-2 small text-center italic text-muted">
                                        No selfie captured yet...
                                    </div>
                                    <img id="photo-preview" style="display:none; width: 100%; aspect-ratio: 1/1; object-fit: cover;" class="rounded-circle border border-primary mt-2 mx-auto shadow-sm">
                                </div>

                                <div id="location-status" class="mb-3 p-3 rounded bg-light border text-left">
                                    <div class="d-flex align-items-center mb-2">
                                        <div id="gps-spinner" class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                                        <span class="text-dark small font-weight-bold" id="gps-text">Waiting for Satellites...</span>
                                    </div>
                                    <div class="progress progress-sm" style="height: 6px;">
                                        <div id="accuracy-progress" class="progress-bar bg-warning" style="width: 5%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small id="accuracy-text" class="text-muted" style="font-size: 10px;">Establishing fix...</small>
                                        <small class="text-primary font-weight-bold" style="font-size: 10px;">Goal: <100m</small>
                                    </div>
                                </div>

                                <form id="attendance-form" method="POST" action="attendance?action=checkIn">
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">
                                    <input type="hidden" name="address" id="address">
                                    <input type="hidden" name="photo_data" id="photo_data">
                                    <input type="hidden" name="odometer_data" id="odometer_data">
                                    
                                    <?php if(!$attendance): ?>
                                        <?php if($_SESSION['role'] == 'Executive'): ?>
                                        <div class="p-3 mb-4 bg-soft-info rounded border border-info">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 font-weight-bold"><i class="fe fe-truck mr-2"></i> Vehicle Details</h6>
                                                <button type="button" id="open-odo-modal" class="btn btn-sm btn-info shadow-sm font-weight-bold" data-toggle="modal" data-target="#odometerModal" disabled>
                                                    <i class="fe fe-maximize mr-1"></i> Open Camera
                                                </button>
                                            </div>
                                            <div id="odo-status-box" class="alert alert-light border m-0 p-2 small text-center italic text-muted">
                                                Capture selfie first to unlock odometer...
                                            </div>
                                            <img id="odo-final-preview" style="display:none; width: 100%; aspect-ratio: 16/9; object-fit: cover;" class="rounded border mt-2">
                                            
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <div class="form-group mb-0">
                                                        <label class="small text-muted mb-1 font-weight-bold">Reading (Last 4 digits)</label>
                                                        <input type="text" name="odometer_reading" class="form-control form-control-sm border-info" placeholder="e.g. 1234" maxlength="4">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group mb-0">
                                                        <label class="small text-muted mb-1 font-weight-bold">Other Details</label>
                                                        <input type="text" name="ticket_details" class="form-control form-control-sm border-info" placeholder="e.g. Bike / Mall">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <button type="submit" id="check-in-btn" class="btn btn-lg btn-primary btn-block py-3 shadow-sm font-weight-bold" disabled>
                                            <i class="fe fe-lock mr-2"></i> Verify GPS & Photo
                                        </button>
                                    <?php elseif(!$attendance['check_out_time']): ?>
                                        <input type="hidden" name="attendance_id" value="<?php echo $attendance['id']; ?>">
                                        <button type="submit" id="check-out-btn" class="btn btn-lg btn-danger btn-block py-3 shadow-sm font-weight-bold" formaction="attendance?action=checkOut" disabled>
                                            <i class="fe fe-lock mr-2"></i> Verify GPS & Photo
                                        </button>
                                    <?php else: ?>
                                        <div class="p-3 bg-soft-success rounded-lg border border-success">
                                            <i class="fe fe-check-circle fe-20 text-success mb-1 d-block"></i>
                                            <h6 class="text-success font-weight-bold mb-0 small">Shift Completed</h6>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 mb-4">
                        <div class="card shadow-sm border-0 h-100 overflow-hidden border">
                            <div id="map-container" style="width: 100%; height: 100%; min-height: 450px; background: #f0f0f0;"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card shadow-sm border-0 overflow-hidden mt-2">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Attendance Log</h5>
                        <span class="badge badge-pill badge-light"><?php echo date('F Y'); ?></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Session Info</th>
                                        <th>Check-In</th>
                                        <th>Check-Out</th>
                                        <th class="pr-4 text-right">Verification</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($attendance): ?>
                                    <tr>
                                        <td class="pl-4">
                                            <div class="font-weight-600"><?php echo date('d M Y'); ?></div>
                                            <small class="text-muted italic"><?php echo $attendance['check_in_address']; ?></small>
                                        </td>
                                        <td>
                                            <div class="text-success font-weight-bold"><?php echo date('h:i A', strtotime($attendance['check_in_time'])); ?></div>
                                            <?php if($attendance['check_in_photo']): ?>
                                                <a href="<?php echo $attendance['check_in_photo']; ?>" target="_blank" class="small text-primary"><i class="fe fe-image mr-1"></i>View Photo</a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="<?php echo $attendance['check_out_time'] ? 'text-danger font-weight-bold' : 'text-muted italic'; ?>">
                                                <?php echo $attendance['check_out_time'] ? date('h:i A', strtotime($attendance['check_out_time'])) : 'Active Session'; ?>
                                            </div>
                                            <?php if($attendance['check_out_photo']): ?>
                                                <a href="<?php echo $attendance['check_out_photo']; ?>" target="_blank" class="small text-primary"><i class="fe fe-image mr-1"></i>View Photo</a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pr-4 text-right">
                                            <span class="badge <?php echo $attendance['check_out_time'] ? 'badge-success' : 'badge-primary'; ?> px-3 py-1">
                                                <?php echo $attendance['check_out_time'] ? 'Verified & Closed' : 'GPS Active'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted small">No attendance logs found for today. Get started by capturing your photo and GPS lock.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const addrInput = document.getElementById('address');
        const photoDataInput = document.getElementById('photo_data');
        const gpsText = document.getElementById('gps-text');
        const accuracyText = document.getElementById('accuracy-text');
        const accuracyProgress = document.getElementById('accuracy-progress');
        const checkInBtn = document.getElementById('check-in-btn');
        const checkOutBtn = document.getElementById('check-out-btn');
        const gpsSpinner = document.getElementById('gps-spinner');
        
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('capture-btn');
        const photoPreview = document.getElementById('photo-preview');
        const doneSelfieBtn = document.getElementById('done-selfie-btn');

        const videoOdo = document.getElementById('video-odo');
        const captureOdoBtn = document.getElementById('capture-odo-btn');
        const odoPreview = document.getElementById('odo-preview');
        const odometerDataInput = document.getElementById('odometer_data');

        let map, marker, platform;
        const isExecutive = <?php echo ($_SESSION['role'] == 'Executive') ? 'true' : 'false'; ?>;
        let isGpsLocked = false;
        let isPhotoCaptured = false;
        let isOdoCaptured = !isExecutive; 
        const REQUIRED_ACCURACY = 100;

        // Initialize HERE Platform
        platform = new H.service.Platform({
            'apikey': window.HERE_API_KEY
        });
        const defaultLayers = platform.createDefaultLayers();
        map = new H.Map(
            document.getElementById('map-container'),
            defaultLayers.vector.normal.map,
            { zoom: 16, center: { lat: 20, lng: 77 } }
        );
        const mapEvents = new H.mapevents.MapEvents(map);
        new H.mapevents.Behavior(mapEvents);
        window.addEventListener('resize', () => map.getViewPort().resize());

        let currentFacingMode = { selfie: "user", odo: "environment" };
        let activeStream = null;

        function stopActiveStream() {
            if (activeStream) {
                activeStream.getTracks().forEach(track => track.stop());
                activeStream = null;
            }
        }

        function startCamera(type, mode) {
            stopActiveStream();
            const constraints = { 
                video: { 
                    facingMode: (mode === 'environment') ? { ideal: "environment" } : (mode === 'user' ? "user" : mode)
                } 
            };
            
            const vidElem = (type === 'selfie') ? video : videoOdo;

            navigator.mediaDevices.getUserMedia(constraints)
                .then(stream => {
                    activeStream = stream;
                    vidElem.srcObject = stream;
                })
                .catch(err => {
                    console.error(`${type} camera error:`, err);
                    if (mode === 'environment') {
                        navigator.mediaDevices.getUserMedia({ video: true })
                            .then(stream => { activeStream = stream; vidElem.srcObject = stream; })
                            .catch(e => console.error("Total camera failure:", e));
                    }
                });
        }

        window.switchCamera = function(type) {
            currentFacingMode[type] = (currentFacingMode[type] === "user") ? "environment" : "user";
            startCamera(type, currentFacingMode[type]);
        };

        // Selfie Modal Logic
        $('#selfieModal').on('shown.bs.modal', function () {
            startCamera('selfie', currentFacingMode.selfie);
            video.style.display = 'block';
            photoPreview.style.display = 'none';
        }).on('hidden.bs.modal', function () {
            stopActiveStream();
        });

        captureBtn.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.5);
            if (dataUrl && dataUrl.length > 1000) {
                photoDataInput.value = dataUrl;
                photoPreview.src = dataUrl;
                photoPreview.style.display = 'block';
                video.style.display = 'none';
                if (doneSelfieBtn) doneSelfieBtn.style.display = 'block';
                captureBtn.innerHTML = '<i class="fe fe-refresh-cw mr-1"></i> Retake';
            }
        });

        if (doneSelfieBtn) {
            doneSelfieBtn.addEventListener('click', () => {
                isPhotoCaptured = true;
                const mainPreview = document.getElementById('photo-preview');
                const selfieStatus = document.getElementById('selfie-status-box');
                if (mainPreview) {
                    mainPreview.src = photoDataInput.value;
                    mainPreview.style.display = 'block';
                }
                if (selfieStatus) {
                    selfieStatus.innerHTML = '<i class="fe fe-check-circle mr-1"></i> Selfie Captured';
                    selfieStatus.classList.replace('alert-light', 'alert-success');
                }
                
                // Unlock Odometer for Executives
                if (isExecutive) {
                    const odoBtn = document.getElementById('open-odo-modal');
                    const odoStatus = document.getElementById('odo-status-box');
                    if (odoBtn) odoBtn.disabled = false;
                    if (odoStatus) {
                        odoStatus.textContent = "Selfie verified. Please capture Odometer.";
                        odoStatus.classList.replace('alert-light', 'alert-success');
                    }
                }
                validateCheckIn();
            });
        }

        // Odometer Modal Logic
        $('#odometerModal').on('shown.bs.modal', function () {
            startCamera('odo', currentFacingMode.odo);
            videoOdo.style.display = 'block';
            odoPreview.style.display = 'none';
        }).on('hidden.bs.modal', function () {
            stopActiveStream();
        });

        if (captureOdoBtn) {
            const canvasOdo = document.getElementById('canvas-odo');
            const doneOdoBtn = document.getElementById('done-odo-btn');
            const odoFinalPreview = document.getElementById('odo-final-preview');
            const odoStatus = document.getElementById('odo-status-box');

            captureOdoBtn.addEventListener('click', () => {
                const context = canvasOdo.getContext('2d');
                canvasOdo.width = videoOdo.videoWidth;
                canvasOdo.height = videoOdo.videoHeight;
                context.drawImage(videoOdo, 0, 0);
                const dataUrl = canvasOdo.toDataURL('image/jpeg', 0.5);
                if (dataUrl && dataUrl.length > 1000) {
                    odometerDataInput.value = dataUrl;
                    odoPreview.src = dataUrl;
                    odoPreview.style.display = 'block';
                    videoOdo.style.display = 'none';
                    if (doneOdoBtn) doneOdoBtn.style.display = 'block';
                    captureOdoBtn.innerHTML = '<i class="fe fe-refresh-cw mr-1"></i> Retake';
                }
            });

            if (doneOdoBtn) {
                doneOdoBtn.addEventListener('click', () => {
                    isOdoCaptured = true;
                    if (odoFinalPreview) {
                        odoFinalPreview.src = odometerDataInput.value;
                        odoFinalPreview.style.display = 'block';
                    }
                    if (odoStatus) {
                        odoStatus.innerHTML = '<i class="fe fe-check-circle mr-1"></i> Vehicle Photo Captured';
                        odoStatus.classList.replace('alert-success', 'alert-info');
                    }
                    validateCheckIn();
                });
            }
        }

        function validateCheckIn() {
            if (isGpsLocked && isPhotoCaptured && isOdoCaptured) {
                if (checkInBtn) {
                    checkInBtn.disabled = false;
                    checkInBtn.innerHTML = '<i class="fe fe-check mr-2"></i> Confirm Check-In';
                    checkInBtn.classList.replace('btn-primary', 'btn-success');
                }
                if (checkOutBtn) {
                    checkOutBtn.disabled = false;
                    checkOutBtn.innerHTML = '<i class="fe fe-check mr-2"></i> Confirm Check-Out';
                    checkOutBtn.classList.replace('btn-danger', 'btn-success');
                }
            }
        }

        function monitorPrecision() {
            navigator.geolocation.watchPosition(pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const acc = pos.coords.accuracy;

                let progress = acc < 100 ? 100 : (acc < 300 ? 80 : (acc < 1000 ? 50 : 20));
                accuracyProgress.style.width = progress + "%";
                accuracyText.textContent = `Accuracy: ±${Math.round(acc)}m`;

                if (acc <= REQUIRED_ACCURACY) {
                    latInput.value = lat;
                    lngInput.value = lng;
                    isGpsLocked = true;
                    gpsText.innerHTML = '<i class="fe fe-check-circle text-success mr-2"></i>Pinpoint Lock Confirmed';
                    accuracyProgress.classList.replace('bg-warning', 'bg-success');
                    gpsSpinner.classList.add('d-none');
                    validateCheckIn();

                    // Update Map
                    map.setCenter({ lat, lng });
                    if (!marker) {
                        marker = new H.map.Marker({ lat, lng });
                        map.addObject(marker);
                    } else {
                        marker.setGeometry({ lat, lng });
                    }

                    if (!addrInput.value) {
                        fetch(`https://revgeocode.search.hereapi.com/v1/revgeocode?at=${lat},${lng}&lang=en-US&apiKey=${window.HERE_API_KEY}`)
                            .then(r => r.json())
                            .then(d => {
                                const addr = d.items[0]?.address.label || "GPS Tagged Location";
                                addrInput.value = addr;
                            });
                    }
                } else {
                    gpsText.textContent = "Refining Satellite Signal...";
                    accuracyProgress.classList.replace('bg-success', 'bg-warning');
                }
            }, null, { enableHighAccuracy: true, maximumAge: 0 });
        }

        monitorPrecision();

        // Clock
        setInterval(() => {
            const clock = document.getElementById('live-clock');
            if (clock) clock.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        }, 1000);
    });
</script>

<!-- Selfie Modal -->
<div class="modal fade" id="selfieModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg bg-dark">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-white font-weight-bold">Selfie Verification</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-3">
                <div id="selfie-container" style="position: relative; width: 100%; aspect-ratio: 1/1; background: #000; overflow: hidden;" class="rounded-circle border border-secondary shadow-sm mx-auto">
                    <video id="video" width="100%" height="100%" autoplay playsinline style="object-fit: cover;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <img id="photo-preview-modal" style="display:none; width: 100%; height: 100%; object-fit: cover;" class="rounded">
                </div>
                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-outline-light btn-sm mr-2" onclick="switchCamera('selfie')">
                        <i class="fe fe-refresh-cw"></i> Flip
                    </button>
                    <button type="button" id="capture-btn" class="btn btn-primary btn-block font-weight-bold">
                        <i class="fe fe-camera mr-1"></i> Capture Selfie
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-success btn-block font-weight-bold py-2" data-dismiss="modal" id="done-selfie-btn" style="display:none;">
                    <i class="fe fe-check-circle mr-1"></i> Confirm & Use This Selfie
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Odometer Capture Modal -->
<div class="modal fade" id="odometerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg bg-dark">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-white font-weight-bold">Capture Odometer Reading</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-3">
                <div id="odometer-container" style="position: relative; width: 100%; aspect-ratio: 4/3; background: #000; overflow: hidden;" class="rounded border border-secondary shadow-sm">
                    <video id="video-odo" width="100%" height="100%" autoplay playsinline style="object-fit: cover;"></video>
                    <canvas id="canvas-odo" style="display:none;"></canvas>
                    <img id="odo-preview" style="display:none; width: 100%; height: 100%; object-fit: cover;" class="rounded">
                </div>
                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-outline-light btn-sm mr-2" onclick="switchCamera('odo')">
                        <i class="fe fe-refresh-cw"></i> Flip
                    </button>
                    <button type="button" id="capture-odo-btn" class="btn btn-info btn-block font-weight-bold">
                        <i class="fe fe-camera mr-1"></i> Snap Picture
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-success btn-block font-weight-bold py-2" data-dismiss="modal" id="done-odo-btn" style="display:none;">
                    <i class="fe fe-check-circle mr-1"></i> Confirm & Use This Photo
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); }
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
.btn-xs { padding: 0.1rem 0.4rem; font-size: 0.7rem; }
.font-weight-600 { font-weight: 600; }
.italic { font-style: italic; }
.progress-bar { transition: width 0.5s ease; }
</style>

<?php include 'layout/footer.php'; ?>
