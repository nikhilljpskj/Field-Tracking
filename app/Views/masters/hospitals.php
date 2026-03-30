<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Hospitals & Offices</h2>
                        <p class="text-muted">Manage the master list of client facilities, corporate offices, and hospitals.</p>
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
                                        <th>Registered Address</th>
                                        <th>Added On</th>
                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager'])): ?>
                                        <th class="text-right pr-4">Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($hospitals)): ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted">No hospitals or offices found. Add your first facility to populate the database.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($hospitals as $h): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-bold text-dark">
                                            <i class="fe fe-heart text-danger mr-2"></i> <?php echo htmlspecialchars($h['name']); ?>
                                        </td>
                                        <td>
                                            <?php if($h['address']): ?>
                                                <span class="text-muted"><i class="fe fe-map-pin mr-1"></i> <?php echo htmlspecialchars(substr($h['address'], 0, 50)) . (strlen($h['address']) > 50 ? '...' : ''); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted italic small">Not Provided</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted"><?php echo date('M d, Y', strtotime($h['created_at'])); ?></td>
                                        
                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager'])): ?>
                                        <td class="text-right pr-4">
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
        <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Register Hospital/Office</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="hospitals?action=add" method="POST">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold small text-muted text-uppercase">Facility Name</label>
                        <input type="text" name="name" class="form-control border-0 bg-light" required placeholder="e.g. Apollo Hospital / Tech Park" style="height: 45px; border-radius: 10px;">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-muted text-uppercase">City / Area / Exact Address (Optional)</label>
                        <textarea name="address" class="form-control border-0 bg-light px-3 py-2" rows="3" placeholder="Enter full address details..." style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pr-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">Save Facility</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
