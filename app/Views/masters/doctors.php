<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Doctors & Points of Contact</h2>
                        <p class="text-muted">Manage doctors/POCs with allotted visit day and time.</p>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fe fe-download mr-1"></i> Export
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                <a class="dropdown-item" href="doctors?action=export&format=csv"><i class="fe fe-file-text mr-2 text-success"></i>Excel (CSV)</a>
                                <a class="dropdown-item" target="_blank" href="doctors?action=export&format=pdf"><i class="fe fe-printer mr-2 text-danger"></i>PDF</a>
                            </div>
                        </div>
                        <button class="btn btn-primary font-weight-bold shadow-sm rounded-pill px-4" data-toggle="modal" data-target="#addDoctorModal">
                            <i class="fe fe-plus mr-1"></i> Add Doctor / POC
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
                                        <th class="pl-4">Doctor / POC Name</th>
                                        <th>Phone Number</th>
                                        <th>Allotted Day</th>
                                        <th class="d-none d-md-table-cell">Allotted Time</th>
                                        <th class="d-none d-lg-table-cell">Added On</th>
                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager','HR'])): ?>
                                            <th class="text-right pr-4">Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($doctors)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No doctors found in the database. Add one to get started.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($doctors as $d): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-bold text-dark">
                                            <?php echo htmlspecialchars($d['name']); ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($d['phone'])): ?>
                                                <a href="tel:<?php echo htmlspecialchars($d['phone']); ?>" class="text-decoration-none">
                                                    <i class="fe fe-phone text-muted mr-1"></i> <?php echo htmlspecialchars($d['phone']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted italic small">Not Provided</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($d['allotted_day'] ?? '-'); ?></td>
                                        <td class="d-none d-md-table-cell"><?php echo !empty($d['allotted_time']) ? htmlspecialchars(date('h:i A', strtotime($d['allotted_time']))) : '-'; ?></td>
                                        <td class="d-none d-lg-table-cell text-muted"><?php echo !empty($d['created_at']) ? date('M d, Y', strtotime($d['created_at'])) : '-'; ?></td>

                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager','HR'])): ?>
                                        <td class="text-right pr-4">
                                            <div class="d-inline-flex align-items-center dc-actions">
                                            <button type="button" class="btn btn-sm btn-outline-secondary mr-1 dc-ic"
                                                    data-toggle="modal" data-target="#viewDoctorModal"
                                                    data-name="<?php echo htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-phone="<?php echo htmlspecialchars($d['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_day="<?php echo htmlspecialchars($d['allotted_day'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_time="<?php echo htmlspecialchars($d['allotted_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="fe fe-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary mr-1 dc-ic"
                                                    data-toggle="modal" data-target="#editDoctorModal"
                                                    data-id="<?php echo (int)$d['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-phone="<?php echo htmlspecialchars($d['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_day="<?php echo htmlspecialchars($d['allotted_day'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-allotted_time="<?php echo htmlspecialchars($d['allotted_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="fe fe-edit-2"></i>
                                            </button>
                                            <a href="doctors?action=delete&id=<?php echo $d['id']; ?>" class="btn btn-sm btn-outline-danger dc-ic" onclick="return confirm('Are you sure you want to delete this doctor / POC?');">
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

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fe fe-plus-circle mr-2"></i>Register Doctor/POC</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="doctors?action=add" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Dr. John Smith / POC Name">
                    </div>
                    <div class="form-group">
                        <label>Phone Number (Optional)</label>
                        <input type="tel" name="phone" class="form-control" placeholder="e.g. +91 9876543210">
                    </div>
                    <div class="form-group">
                        <label>Doctor Allotted Day</label>
                        <select name="allotted_day" class="form-control">
                            <option value="">Select Day</option>
                            <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
                                <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Doctor Allotted Time</label>
                        <input type="time" name="allotted_time" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Doctor Modal -->
<div class="modal fade" id="editDoctorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fe fe-edit-2 mr-2"></i>Edit Doctor/POC</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="doctors?action=update" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="ed_id">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" id="ed_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" id="ed_phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Doctor Allotted Day</label>
                        <select name="allotted_day" id="ed_allotted_day" class="form-control">
                            <option value="">Select Day</option>
                            <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
                                <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Doctor Allotted Time</label>
                        <input type="time" name="allotted_time" id="ed_allotted_time" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info px-4 shadow-sm">Update Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Doctor Modal -->
<div class="modal fade" id="viewDoctorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fe fe-eye mr-2"></i>Doctor / POC Full Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="vd_name"></span></p>
                <p><strong>Phone:</strong> <span id="vd_phone"></span></p>
                <p><strong>Allotted Day:</strong> <span id="vd_day"></span></p>
                <p class="mb-0"><strong>Allotted Time:</strong> <span id="vd_time"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
$('#editDoctorModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#ed_id').val(button.data('id') || '');
    $('#ed_name').val(button.data('name') || '');
    $('#ed_phone').val(button.data('phone') || '');
    $('#ed_allotted_day').val(button.data('allotted_day') || '');
    $('#ed_allotted_time').val(button.data('allotted_time') || '');
});

$('#viewDoctorModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#vd_name').text(button.data('name') || '-');
    $('#vd_phone').text(button.data('phone') || '-');
    $('#vd_day').text(button.data('allotted_day') || '-');
    $('#vd_time').text(button.data('allotted_time') || '-');
});
</script>

<style>
.dc-actions { gap: 6px; }
.dc-ic { width: 30px; height: 30px; display:inline-flex; align-items:center; justify-content:center; padding:0; }
.dc-ic i { font-size: 13px; }
@media (max-width: 767px) {
    .page-title { font-size: 1.2rem; }
    .btn.rounded-pill { padding-left: 12px !important; padding-right: 12px !important; }
    .table td, .table th { white-space: nowrap; }
}
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
