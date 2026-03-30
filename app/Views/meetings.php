<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<style>
    /* Mobile First - Stacking Layout */
    .main-content {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin-left: 0 !important;
        transition: all 0.3s ease;
    }
    
    .hub-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem;
        margin-bottom: 0;
    }
    
    .hub-content {
        display: block; /* Stack on mobile */
        height: auto;
        overflow: visible;
    }
    
    .command-center {
        width: 100%;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
    }
    
    .intelligence-feed {
        padding: 1rem;
        background: #fff;
    }

    .interaction-strip {
        display: block; /* Stack strip elements on mobile */
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
        cursor: pointer;
    }

    .strip-photo-wrap { margin-bottom: 1rem; text-align: center; }
    .strip-photo { width: 100%; max-width: 120px; height: 120px; border-radius: 10px; object-fit: cover; }
    .strip-status { margin-bottom: 0.5rem; text-align: left; }
    .strip-meta { padding: 0.5rem 0; border: none; margin: 0; }
    .strip-content { padding-top: 0.5rem; border-top: 1px solid #eee; margin-top: 0.5rem; }

    /* Desktop/Laptop - Lateral Intelligence Hub */
    @media (min-width: 992px) {
        .main-content {
            width: calc(100% - 280px) !important;
            margin-left: 280px !important;
        }
        .vertical.collapsed .main-content {
            width: calc(100% - 70px) !important;
            margin-left: 70px !important;
        }
        .hub-header { padding: 1.5rem 2rem; }
        .hub-content { display: flex; height: calc(100vh - 160px); overflow: hidden; }
        .command-center { width: 400px; min-width: 400px; border-right: 1px solid #e9ecef; border-bottom: none; overflow-y: auto; }
        .intelligence-feed { flex-grow: 1; padding: 2rem; overflow-y: auto; }
        .interaction-strip { display: flex; align-items: center; padding: 1.25rem; }
        .strip-photo-wrap { margin-bottom: 0; }
        .strip-photo { width: 80px; height: 80px; }
        .strip-status { min-width: 100px; text-align: center; }
        .strip-meta { flex-grow: 1; padding: 0 1.5rem; border-right: 1px solid #eee; border-left: 1px solid #eee; margin: 0 1.5rem; }
        .strip-content { border-top: none; margin-top: 0; padding-top: 0; }
    }
    
    .stat-card-hub {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.2s;
        margin-bottom: 0.5rem;
    }
    .stat-card-hub:hover {
        border-color: #4361ee;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.08);
    }
</style>

<main role="main" class="main-content">
    <!-- Hub Header & Stats -->
    <div class="hub-header">
        <div class="row align-items-center mb-4">
            <div class="col-md-4">
                <h2 class="h3 mb-0 font-weight-bold">Intelligence Hub</h2>
                <p class="text-muted small mb-0">Full-Horizontal Intelligence Dashboard</p>
            </div>
            <div class="col-md-8">
                <div class="row no-gutters">
                    <div class="col-md-3 px-2">
                        <div class="stat-card-hub border-left shadow-sm" style="border-left: 4px solid #4361ee !important;">
                            <div class="small text-muted text-uppercase font-weight-bold">Today's Visits</div>
                            <div class="h4 mb-0 font-weight-bold"><?php echo $stats['total_today']; ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 px-2">
                        <div class="stat-card-hub border-left shadow-sm" style="border-left: 4px solid #28a745 !important;">
                            <div class="small text-muted text-uppercase font-weight-bold">Approved</div>
                            <div class="h4 mb-0 font-weight-bold"><?php echo $stats['approved_today']; ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 px-2">
                        <div class="stat-card-hub border-left shadow-sm" style="border-left: 4px solid #f72585 !important;">
                            <div class="small text-muted text-uppercase font-weight-bold">Unique Clients</div>
                            <div class="h4 mb-0 font-weight-bold"><?php echo $stats['unique_clients']; ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 px-2">
                        <div class="stat-card-hub border-left shadow-sm" style="border-left: 4px solid #ffca28 !important;">
                            <div class="small text-muted text-uppercase font-weight-bold">Precision Lock</div>
                            <div class="h4 mb-0 font-weight-bold" id="gps-accuracy-stat">-- m</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if(isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success border-0 shadow-sm m-0"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>
    </div>

    <!-- Main Workspace -->
    <div class="hub-content">
        <!-- THE COMMAND CENTER (Sticky Left) -->
        <aside class="command-center shadow-sm">
            <h5 class="text-uppercase small font-weight-bold text-muted mb-4">Command Center</h5>
            
            <div id="location-verification" class="mb-4 bg-white rounded p-3 border shadow-sm">
                <div class="d-flex align-items-center mb-2">
                    <div id="gps-spinner" class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                    <span class="text-dark small font-weight-bold" id="gps-label">Establishing GPS Lock...</span>
                </div>
                <div class="progress progress-sm" style="height: 6px;">
                    <div id="gps-progress" class="progress-bar bg-warning" style="width: 10%"></div>
                </div>
            </div>

            <form id="meeting-form" method="POST" action="meetings?action=log">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="address" id="address">
                <input type="hidden" name="selfie_data" id="selfie_data">

                <div class="form-group mb-4 text-center">
                    <div id="photo-status-box" class="alert alert-light border p-2 small italic text-muted mb-2">
                        Precision Photo Required
                    </div>
                    <img id="selfie-preview" style="display:none; width: 100%; border-radius: 12px; margin-bottom: 1rem;" class="shadow-sm border">
                    <button type="button" id="open-photo-modal" class="btn btn-primary btn-block shadow-sm font-weight-bold" data-toggle="modal" data-target="#photoModal">
                        <i class="fe fe-camera mr-2"></i> Capture Selfie Verification
                    </button>
                </div>

                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted text-uppercase">Contact Person</label>
                    <input type="text" name="client_name" class="form-control bg-white" placeholder="Client Name" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted text-uppercase">Hospital / Office</label>
                    <input type="text" name="hospital_name" id="hospital_name" class="form-control bg-white" placeholder="Facility Title" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted text-uppercase">Visit Category</label>
                    <select name="visit_category" id="visit_category" class="form-control custom-select bg-white">
                        <option value="Meeting">Standard Meeting</option>
                        <option value="Home Enrollment">Home Enrollment</option>
                    </select>
                </div>
                
                <div class="form-group mb-3" id="doctor_field_container" style="display: none;">
                    <label class="small font-weight-bold text-primary text-uppercase">Referenced Doctor</label>
                    <select name="referenced_doctor_id" class="form-control custom-select border-primary bg-light">
                        <option value="">-- Select Register Doctor --</option>
                        <?php if(!empty($doctors)): foreach($doctors as $d): ?>
                            <option value="<?php echo $d['id']; ?>">Dr. <?php echo htmlspecialchars(str_ireplace('dr. ', '', str_ireplace('dr ', '', $d['name']))); ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted text-uppercase">Meeting Nature</label>
                    <select name="meeting_type" class="form-control custom-select bg-white">
                        <option>Introductory</option>
                        <option>Follow-up</option>
                        <option>Product demo</option>
                        <option>Commercial closing</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted text-uppercase">Internal Notes</label>
                    <textarea name="notes" class="form-control bg-white" rows="3" placeholder="Visit details..."></textarea>
                </div>
                <div class="form-group mb-4">
                    <label class="small font-weight-bold text-muted text-uppercase">Planned Outcome</label>
                    <input type="text" name="outcome" class="form-control bg-white" placeholder="Next Steps">
                </div>

                <button type="submit" id="log-meeting-btn" class="btn btn-dark btn-block py-3 font-weight-bold shadow-lg" disabled>
                    <i class="fe fe-lock mr-2"></i> GPS Lock Pending
                </button>
            </form>

            <div class="mt-4 border-top pt-3">
                <div id="mini-map" style="height: 180px; border-radius: 12px; background: #eee;"></div>
            </div>
        </aside>

        <!-- THE INTELLIGENCE FEED (Scrollable Right) -->
        <section class="intelligence-feed">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-uppercase small font-weight-bold text-muted mb-0">Global Interaction Feed</h5>
                <div class="btn-group btn-group-sm shadow-sm">
                    <button class="btn btn-white active border">All Records</button>
                    <button class="btn btn-white border">Pending Audit</button>
                </div>
            </div>

            <?php if(empty($meetings)): ?>
                <div class="text-center py-5 text-muted italic">The Intelligence Feed is currently empty.</div>
            <?php else: ?>
                <?php foreach($meetings as $m): ?>
                    <div class="interaction-strip shadow-sm" onclick="viewMeetingDetails(<?php echo htmlspecialchars(json_encode($m)); ?>)">
                        <div class="strip-photo-wrap mr-3">
                            <?php if(isset($m['selfie_path']) && $m['selfie_path']): ?>
                                <img src="<?php echo $m['selfie_path']; ?>" class="strip-photo shadow-sm border">
                            <?php else: ?>
                                <div class="strip-photo bg-light d-flex align-items-center justify-content-center border">
                                    <i class="fe fe-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="strip-status">
                            <span class="badge <?php echo (($m['status'] ?? '') == 'Approved') ? 'badge-success' : ((($m['status'] ?? '') == 'Rejected') ? 'badge-danger' : 'badge-warning'); ?> px-3 py-1">
                                <?php echo htmlspecialchars($m['status'] ?? 'Pending'); ?>
                            </span>
                            <div class="small text-muted mt-2 font-weight-bold"><?php echo date('h:i A', strtotime($m['meeting_time'])); ?></div>
                        </div>

                        <div class="strip-meta">
                            <div class="small text-primary font-weight-bold mb-1 text-uppercase letter-spacing-1"><?php echo htmlspecialchars($m['user_name'] ?? 'Executive'); ?></div>
                            <h5 class="mb-1 font-weight-bold text-dark"><?php echo htmlspecialchars($m['client_name'] ?: 'No Client'); ?></h5>
                            <div class="text-muted small"><i class="fe fe-briefcase mr-1"></i> <?php echo htmlspecialchars($m['hospital_office_name']); ?></div>
                            <div class="text-muted small mt-1"><i class="fe fe-calendar mr-1"></i> <?php echo date('d M Y', strtotime($m['meeting_time'])); ?></div>
                        </div>

                        <div class="strip-content flex-fill text-truncate pr-4" style="max-width: 400px;">
                            <div class="font-weight-600 text-dark small mb-1">Outcome:</div>
                            <div class="text-muted small text-truncate">
                                <?php if(($m['visit_category'] ?? '') == 'Home Enrollment' && !empty($m['referenced_doctor_name'])): ?>
                                    <span class="badge badge-light border text-primary mb-1"><i class="fe fe-user mr-1"></i>Ref: Dr. <?php echo htmlspecialchars($m['referenced_doctor_name']); ?></span><br>
                                <?php endif; ?>
                                <?php echo $m['outcome'] ?: 'No outcome specified.'; ?>
                            </div>
                            <div class="mt-2 text-primary small font-weight-bold">
                                <i class="fe fe-maximize-2 mr-1"></i> Open Full Report
                            </div>
                        </div>

                        <div class="strip-actions border-left pl-4 ml-2">
                             <?php if($m['latitude']): ?>
                                <button class="btn btn-sm btn-white shadow-sm rounded-circle border p-2" onclick="event.stopPropagation(); focusMeeting(<?php echo $m['latitude']; ?>, <?php echo $m['longitude']; ?>)" title="Pinpoint Map View">
                                    <i class="fe fe-map-pin text-primary"></i>
                                </button>
                             <?php endif; ?>
                             <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                <button class="btn btn-sm btn-danger shadow-sm rounded-circle border-0 p-2 ml-1" onclick="event.stopPropagation(); if(confirm('Permanently delete this meeting log and its associated selfie?')) window.location.href='meetings?action=delete&id=<?php echo $m['id']; ?>'" title="Delete Record">
                                    <i class="fe fe-trash-2 text-white"></i>
                                </button>
                             <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</main>

<!-- Details Modal -->
<div class="modal fade" id="meetingDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title font-weight-bold">Intelligence Audit Report</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="row no-gutters">
                    <div class="col-md-5">
                        <img id="modal-meeting-photo" src="" style="width: 100%; height: 100%; object-fit: cover; min-height: 400px;">
                        <div id="modal-no-photo" class="text-center p-5 bg-light h-100 italic" style="display:none;">No Selfie Verification</div>
                    </div>
                    <div class="col-md-7 p-4 bg-white">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h3 id="modal-client-name" class="font-weight-bold text-primary mb-1"></h3>
                                <p id="modal-hospital-name" class="text-muted mb-0"></p>
                            </div>
                            <span id="modal-status-badge" class="badge px-4 py-2" style="height: fit-content;"></span>
                        </div>
                        
                        <div class="row mb-4">
                             <div class="col-6">
                                <label class="small text-muted font-weight-bold text-uppercase">Executive</label>
                                <div id="modal-staff-name" class="font-weight-bold"></div>
                             </div>
                             <div class="col-6 text-right">
                                <label class="small text-muted font-weight-bold text-uppercase">Time (IST)</label>
                                <div id="modal-date-time" class="font-weight-bold"></div>
                             </div>
                        </div>

                        <div id="modal-ref-doctor-container" class="bg-light rounded p-3 mb-4 border border-dashed border-primary" style="display:none;">
                            <label class="small text-primary font-weight-bold text-uppercase"><i class="fe fe-user mr-1"></i> Referenced Doctor</label>
                            <div id="modal-ref-doctor" class="font-weight-bold text-dark"></div>
                        </div>

                        <div class="bg-light rounded p-3 mb-4 border border-dashed">
                            <label class="small text-muted font-weight-bold text-uppercase">Discussion notes</label>
                            <p id="modal-notes" class="mb-0 text-dark" style="white-space: pre-wrap;"></p>
                        </div>

                        <div class="p-3 mb-4 rounded border border-success" style="background: rgba(40,167,69,0.05);">
                            <label class="small text-success font-weight-bold text-uppercase">Outcome Identified</label>
                            <p id="modal-outcome" class="mb-0 text-dark font-weight-bold"></p>
                        </div>

                        <div class="small text-muted">
                            <i class="fe fe-map-pin mr-1"></i> <span id="modal-address"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager'])): ?>
                    <div class="mr-auto">
                        <button type="button" class="btn btn-success px-4 font-weight-bold rounded-pill audit-btn" id="modal-approve-btn">Approve</button>
                        <button type="button" class="btn btn-danger px-4 font-weight-bold rounded-pill audit-btn" id="modal-reject-btn">Reject</button>
                    </div>
                <?php endif; ?>
                <button type="button" class="btn btn-primary px-4 font-weight-bold rounded-pill" id="modal-map-link">View GPS Trace</button>
                <button type="button" class="btn btn-outline-secondary px-4 font-weight-bold rounded-pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Photo Capture Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg bg-dark" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-white font-weight-bold">Precision Verification Capture</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-3">
                <div class="rounded border border-secondary shadow-sm overflow-hidden" style="position: relative; width: 100%; aspect-ratio: 4/3; background: #000;">
                    <video id="video-selfie" width="100%" height="100%" autoplay playsinline style="object-fit: cover;"></video>
                    <canvas id="canvas-selfie" style="display:none;"></canvas>
                    <img id="photo-modal-preview" style="display:none; width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-outline-light btn-sm mr-2" onclick="switchMeetingCamera()">
                        <i class="fe fe-refresh-cw"></i> Flip Camera
                    </button>
                    <button type="button" id="capture-selfie-btn" class="btn btn-primary btn-block font-weight-bold py-2 shadow-sm">
                        <i class="fe fe-camera mr-2"></i> Take Verification Photo
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-success btn-block font-weight-bold py-3 shadow-lg" data-dismiss="modal" id="done-photo-btn" style="display:none;">
                    <i class="fe fe-check-circle mr-2"></i> Confirm & Attach To Visit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selfieDataInput = document.getElementById('selfie_data');
    const video = document.getElementById('video-selfie');
    const canvas = document.getElementById('canvas-selfie');
    const captureBtn = document.getElementById('capture-selfie-btn');
    const selfiePreview = document.getElementById('selfie-preview');
    const photoModalPreview = document.getElementById('photo-modal-preview');
    const donePhotoBtn = document.getElementById('done-photo-btn');
    
    const gpsProgress = document.getElementById('gps-progress');
    const gpsLabel = document.getElementById('gps-label');
    const gpsAccuracyStat = document.getElementById('gps-accuracy-stat');
    const gpsSpinner = document.getElementById('gps-spinner');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const addrInput = document.getElementById('address');
    const btn = document.getElementById('log-meeting-btn');
    
    // Dynamic Form Trigger for Reference Doctor
    const categorySelect = document.getElementById('visit_category');
    const doctorFieldContainer = document.getElementById('doctor_field_container');
    categorySelect.addEventListener('change', function() {
        if (this.value === 'Home Enrollment') {
            doctorFieldContainer.style.display = 'block';
        } else {
            doctorFieldContainer.style.display = 'none';
            doctorFieldContainer.querySelector('select').value = '';
        }
    });
    
    let map, marker, platform;
    let isGpsLocked = false;
    let isPhotoCaptured = false;
    let currentFacingMode = "environment";
    let stream = null;
    const REQUIRED_ACCURACY = 100;

    // Platform Initialize
    platform = new H.service.Platform({'apikey': window.HERE_API_KEY});
    const defaultLayers = platform.createDefaultLayers();
    map = new H.Map(
        document.getElementById('mini-map'),
        defaultLayers.vector.normal.map,
        { zoom: 15, center: { lat: 20, lng: 77 } }
    );
    const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
    
    function startCamera(mode) {
        currentFacingMode = mode;
        if (stream) stream.getTracks().forEach(t => t.stop());
        navigator.mediaDevices.getUserMedia({ video: { facingMode: mode === 'user' ? 'user' : { ideal: 'environment' } } })
            .then(s => { stream = s; video.srcObject = s; })
            .catch(e => console.error("Camera fail:", e));
    }

    window.switchMeetingCamera = () => {
        currentFacingMode = (currentFacingMode === "user") ? "environment" : "user";
        startCamera(currentFacingMode);
    };

    $('#photoModal').on('shown.bs.modal', () => {
        startCamera(currentFacingMode);
        video.style.display = 'block';
        photoModalPreview.style.display = 'none';
        if(donePhotoBtn) donePhotoBtn.style.display = 'none';
        captureBtn.innerHTML = '<i class="fe fe-camera mr-2"></i> Take Verification Photo';
    }).on('hidden.bs.modal', () => {
        if (stream) stream.getTracks().forEach(t => t.stop());
    });

    captureBtn.onclick = () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.6);
        photoModalPreview.src = dataUrl;
        photoModalPreview.style.display = 'block';
        video.style.display = 'none';
        if(donePhotoBtn) donePhotoBtn.style.display = 'block';
        captureBtn.innerHTML = '<i class="fe fe-refresh-cw mr-2"></i> Retake Photo';
    };

    donePhotoBtn.onclick = () => {
        selfieDataInput.value = photoModalPreview.src;
        selfiePreview.src = photoModalPreview.src;
        selfiePreview.style.display = 'block';
        isPhotoCaptured = true;
        document.getElementById('photo-status-box').innerHTML = '<i class="fe fe-check-circle text-success mr-2"></i>Photo Verified';
        checkReady();
    };

    function checkReady() {
        if(isGpsLocked && isPhotoCaptured) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fe fe-check-circle mr-2"></i> Finalize Intelligence Log';
            btn.className = 'btn btn-success btn-block py-3 font-weight-bold shadow-lg';
        }
    }

    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(pos => {
            const { latitude: lat, longitude: lng, accuracy: acc } = pos.coords;
            gpsAccuracyStat.textContent = Math.round(acc) + " m";
            gpsProgress.style.width = acc < REQUIRED_ACCURACY ? '100%' : (acc < 500 ? '60%' : '20%');
            
            if(acc <= REQUIRED_ACCURACY) {
                latInput.value = lat;
                lngInput.value = lng;
                isGpsLocked = true;
                gpsLabel.innerHTML = '<i class="fe fe-check-circle text-success mr-2"></i>Precision Connection Established';
                gpsProgress.className = 'progress-bar bg-success';
                gpsSpinner.className = 'fe fe-activity text-success mr-2';
                map.setCenter({lat, lng});
                if(!marker) {
                    marker = new H.map.Marker({lat, lng});
                    map.addObject(marker);
                } else {
                    marker.setGeometry({lat, lng});
                }
                if(!addrInput.value) {
                    fetch(`map?action=geocode&q=${lat},${lng}`)
                        .then(r => r.json())
                        .then(d => addrInput.value = d.address || "Field Position Registered");
                }
                checkReady();
            }
        }, err => console.error(err), {enableHighAccuracy: true});
    }

    window.focusMeeting = (lat, lng) => {
        map.setCenter({lat, lng});
        map.setZoom(17);
        if(!marker) {
            marker = new H.map.Marker({lat, lng});
            map.addObject(marker);
        } else {
            marker.setGeometry({lat, lng});
        }
        window.scrollTo({top: 0, behavior: 'smooth'});
    };

    window.viewMeetingDetails = (data) => {
        document.getElementById('modal-client-name').textContent = data.client_name;
        document.getElementById('modal-hospital-name').textContent = data.hospital_office_name;
        document.getElementById('modal-staff-name').textContent = data.user_name || 'Executive';
        document.getElementById('modal-date-time').textContent = new Date(data.meeting_time).toLocaleString('en-IN') + ' IST';
        document.getElementById('modal-notes').textContent = data.notes;
        document.getElementById('modal-outcome').textContent = data.outcome;
        document.getElementById('modal-address').textContent = data.address;
        
        const refDocContainer = document.getElementById('modal-ref-doctor-container');
        if (data.visit_category === 'Home Enrollment' && data.referenced_doctor_name) {
            document.getElementById('modal-ref-doctor').textContent = 'Dr. ' + data.referenced_doctor_name.replace(/dr\.?\s*/i, '');
            refDocContainer.style.display = 'block';
        } else {
            refDocContainer.style.display = 'none';
        }
        
        const b = document.getElementById('modal-status-badge');
        b.textContent = data.status || 'Pending';
        b.className = 'badge px-4 py-2 ' + (data.status === 'Approved' ? 'badge-success' : (data.status === 'Rejected' ? 'badge-danger' : 'badge-warning'));

        const img = document.getElementById('modal-meeting-photo');
        const no = document.getElementById('modal-no-photo');
        if (data.selfie_path) {
            img.src = data.selfie_path; img.style.display = 'block'; no.style.display = 'none';
        } else {
            img.style.display = 'none'; no.style.display = 'block';
        }

        document.getElementById('modal-map-link').onclick = () => {
             $('#meetingDetailModal').modal('hide');
             window.focusMeeting(parseFloat(data.latitude), parseFloat(data.longitude));
        };

        if(document.getElementById('modal-approve-btn')) {
            document.getElementById('modal-approve-btn').onclick = () => {
                window.location.href = `meetings?action=update_status&id=${data.id}&status=Approved`;
            };
            document.getElementById('modal-reject-btn').onclick = () => {
                window.location.href = `meetings?action=update_status&id=${data.id}&status=Rejected`;
            };
        }
        $('#meetingDetailModal').modal('show');
    };
});
</script>

<?php include 'layout/footer.php'; ?>


