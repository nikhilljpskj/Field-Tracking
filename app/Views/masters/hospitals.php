<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Hospitals & Offices</h2>
                        <p class="text-muted">Manage the master list of client facilities, allotted visit day/time, and location details.</p>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fe fe-download mr-1"></i> Export
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                <a class="dropdown-item" href="hospitals?action=export&format=csv"><i class="fe fe-file-text mr-2 text-success"></i>Excel (CSV)</a>
                                <a class="dropdown-item" target="_blank" href="hospitals?action=export&format=pdf"><i class="fe fe-printer mr-2 text-danger"></i>PDF</a>
                            </div>
                        </div>
                        <button class="btn btn-primary font-weight-bold shadow-sm rounded-pill px-4" data-toggle="modal" data-target="#addHospitalModal">
                            <i class="fe fe-plus mr-1"></i> Add Facility
                        </button>
                    </div>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Facility Name</th>
                                        <th>Allotted Day</th>
                                        <th class="d-none d-md-table-cell">Allotted Time</th>
                                        <th class="d-none d-lg-table-cell">Added On</th>
                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager','HR'])): ?>
                                            <th class="text-right pr-4">Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($hospitals)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No hospitals or offices found. Add your first facility to populate the database.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($hospitals as $h): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-bold text-dark">
                                            <i class="fe fe-heart text-danger mr-2"></i> <?php echo htmlspecialchars($h['name']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($h['allotted_day'] ?? '-'); ?></td>
                                        <td class="d-none d-md-table-cell"><?php echo !empty($h['allotted_time']) ? htmlspecialchars(date('h:i A', strtotime($h['allotted_time']))) : '-'; ?></td>
                                        <td class="d-none d-lg-table-cell text-muted"><?php echo !empty($h['created_at']) ? date('M d, Y', strtotime($h['created_at'])) : '-'; ?></td>

                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager','HR'])): ?>
                                        <td class="text-right pr-4">
                                            <div class="d-inline-flex align-items-center hc-actions">
                                            <button type="button" class="btn btn-sm btn-outline-secondary mr-1 hc-ic"
                                                    data-toggle="modal" data-target="#viewHospitalModal"
                                                    data-name="<?php echo htmlspecialchars($h['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-address="<?php echo htmlspecialchars($h['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-location_url="<?php echo htmlspecialchars($h['location_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_day="<?php echo htmlspecialchars($h['allotted_day'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_time="<?php echo htmlspecialchars($h['allotted_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="fe fe-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary mr-1 hc-ic"
                                                    data-toggle="modal" data-target="#editHospitalModal"
                                                    data-id="<?php echo (int)$h['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($h['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-address="<?php echo htmlspecialchars($h['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-location_url="<?php echo htmlspecialchars($h['location_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_day="<?php echo htmlspecialchars($h['allotted_day'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_time="<?php echo htmlspecialchars($h['allotted_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="fe fe-edit-2"></i>
                                            </button>
                                            <a href="hospitals?action=delete&id=<?php echo $h['id']; ?>" class="btn btn-sm btn-outline-danger hc-ic" onclick="return confirm('Deleting this facility is a permanent action. Continue?');">
                                                <i class="fe fe-trash-2"></i>
                                            </a>
                                            </div>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Hospital Modal -->
<div class="modal fade" id="addHospitalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fe fe-plus-circle mr-2"></i>Register Hospital/Office</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="hospitals?action=add" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Facility Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Apollo Hospital / Tech Park">
                    </div>
                    <div class="form-group">
                        <label>Hospital Allotted Day</label>
                        <select name="allotted_day" class="form-control">
                            <option value="">Select Day</option>
                            <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
                                <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hospital Allotted Time</label>
                        <input type="time" name="allotted_time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Hospital Location URL</label>
                        <input type="url" name="location_url" class="form-control" placeholder="https://maps.google.com/...">
                    </div>
                    <div class="form-group mb-0">
                        <label>City / Area / Exact Address (Optional)</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter full address details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Facility</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Hospital Modal -->
<div class="modal fade" id="editHospitalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fe fe-edit-2 mr-2"></i>Edit Hospital/Office</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="hospitals?action=update" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="eh_id">
                    <div class="form-group">
                        <label>Facility Name</label>
                        <input type="text" name="name" id="eh_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Hospital Allotted Day</label>
                        <select name="allotted_day" id="eh_allotted_day" class="form-control">
                            <option value="">Select Day</option>
                            <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
                                <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hospital Allotted Time</label>
                        <input type="time" name="allotted_time" id="eh_allotted_time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Hospital Location URL</label>
                        <input type="url" name="location_url" id="eh_location_url" class="form-control">
                    </div>
                    <div class="form-group mb-0">
                        <label>Address</label>
                        <textarea name="address" id="eh_address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info px-4 shadow-sm">Update Facility</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Hospital Modal -->
<div class="modal fade" id="viewHospitalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fe fe-eye mr-2"></i>Facility Full Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="vh_name"></span></p>
                <p><strong>Allotted Day:</strong> <span id="vh_day"></span></p>
                <p><strong>Allotted Time:</strong> <span id="vh_time"></span></p>
                <p><strong>Location URL:</strong> <a id="vh_url" href="#" target="_blank" rel="noopener">Open Map</a></p>
                <p class="mb-0"><strong>Address:</strong><br><span id="vh_address"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
$('#editHospitalModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#eh_id').val(button.data('id') || '');
    $('#eh_name').val(button.data('name') || '');
    $('#eh_address').val(button.data('address') || '');
    $('#eh_location_url').val(button.data('location_url') || '');
    $('#eh_allotted_day').val(button.data('allotted_day') || '');
    $('#eh_allotted_time').val(button.data('allotted_time') || '');
});

$('#viewHospitalModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var url = button.data('location_url') || '';
    $('#vh_name').text(button.data('name') || '-');
    $('#vh_day').text(button.data('allotted_day') || '-');
    $('#vh_time').text(button.data('allotted_time') || '-');
    $('#vh_address').text(button.data('address') || '-');
    if (url) {
        $('#vh_url').attr('href', url).text('Open Map').show();
    } else {
        $('#vh_url').attr('href', '#').text('Not Provided');
    }
});
</script>

<style>
.hc-actions { gap: 6px; }
.hc-ic { width: 30px; height: 30px; display:inline-flex; align-items:center; justify-content:center; padding:0; }
.hc-ic i { font-size: 13px; }
@media (max-width: 767px) {
    .page-title { font-size: 1.2rem; }
    .btn.rounded-pill { padding-left: 12px !important; padding-right: 12px !important; }
    .table td, .table th { white-space: nowrap; }
}
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
