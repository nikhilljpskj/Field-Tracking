<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Doctors & Points of Contact</h2>
                        <p class="text-muted">Manage reference doctors for home enrollments and client interactions.</p>
                    </div>
                    <button class="btn btn-primary font-weight-bold shadow-sm rounded-pill px-4" data-toggle="modal" data-target="#addDoctorModal">
                        <i class="fe fe-plus mr-1"></i> Add Doctor / POC
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
                                        <th class="pl-4">Doctor Name</th>
                                        <th>Phone Number</th>
                                        <th>Added On</th>
                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager'])): ?>
                                        <th class="text-right pr-4">Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($doctors)): ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted">No doctors found in the database. Add one to get started.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($doctors as $d): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-bold text-dark">
                                            Dr. <?php echo htmlspecialchars(str_ireplace('dr. ', '', str_ireplace('dr ', '', $d['name']))); ?>
                                        </td>
                                        <td>
                                            <?php if($d['phone']): ?>
                                                <a href="tel:<?php echo htmlspecialchars($d['phone']); ?>" class="text-decoration-none">
                                                    <i class="fe fe-phone text-muted mr-1"></i> <?php echo htmlspecialchars($d['phone']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted italic small">Not Provided</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted"><?php echo date('M d, Y', strtotime($d['created_at'])); ?></td>
                                        
                                        <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin','Manager'])): ?>
                                        <td class="text-right pr-4">
                                            <a href="doctors?action=delete&id=<?php echo $d['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this doctor?');">
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

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Register Doctor/POC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="doctors?action=add" method="POST">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold small text-muted text-uppercase">Full Name</label>
                        <input type="text" name="name" class="form-control border-0 bg-light" required placeholder="e.g. John Smith" style="height: 45px; border-radius: 10px;">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-muted text-uppercase">Phone Number (Optional)</label>
                        <input type="tel" name="phone" class="form-control border-0 bg-light" placeholder="e.g. +91 9876543210" style="height: 45px; border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pr-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">Save Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
