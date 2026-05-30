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
                    <button class="btn btn-primary font-weight-bold shadow-sm rounded-pill px-4" data-toggle="modal" data-target="#addHospitalModal">
                        <i class="fe fe-plus mr-1"></i> Add Facility
                    </button>
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
                                        <th>Allotted Time</th>
                                        <th>Location URL</th>
                                        <th>Registered Address</th>
                                        <th>Added On</th>
                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager','HR'])): ?>
                                            <th class="text-right pr-4">Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($hospitals)): ?>
                                        <tr><td colspan="7" class="text-center py-5 text-muted">No hospitals or offices found. Add your first facility to populate the database.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($hospitals as $h): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-bold text-dark">
                                            <i class="fe fe-heart text-danger mr-2"></i> <?php echo htmlspecialchars($h['name']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($h['allotted_day'] ?? '-'); ?></td>
                                        <td><?php echo !empty($h['allotted_time']) ? htmlspecialchars(date('h:i A', strtotime($h['allotted_time']))) : '-'; ?></td>
                                        <td>
                                            <?php if(!empty($h['location_url'])): ?>
                                                <a href="<?php echo htmlspecialchars($h['location_url']); ?>" target="_blank" rel="noopener">Open Map</a>
                                            <?php else: ?>
                                                <span class="text-muted small">Not Provided</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($h['address'])): ?>
                                                <span class="text-muted"><i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars(substr($h['address'], 0, 50)) . (strlen($h['address']) > 50 ? '...' : ''); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted italic small">Not Provided</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted"><?php echo !empty($h['created_at']) ? date('M d, Y', strtotime($h['created_at'])) : '-'; ?></td>

                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager','HR'])): ?>
                                        <td class="text-right pr-4">
                                            <button type="button" class="btn btn-sm btn-outline-primary mr-1"
                                                    data-toggle="modal" data-target="#editHospitalModal"
                                                    data-id="<?php echo (int)$h['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($h['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-address="<?php echo htmlspecialchars($h['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-location_url="<?php echo htmlspecialchars($h['location_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_day="<?php echo htmlspecialchars($h['allotted_day'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_time="<?php echo htmlspecialchars($h['allotted_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="fe fe-edit-2"></i>
                                            </button>
                                            <a href="hospitals?action=delete&id=<?php echo $h['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deleting this facility is a permanent action. Continue?');">
                                                <i class="fe fe-trash-2"></i>
                                            </a>
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
</script>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>

