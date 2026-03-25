<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Global Settings</h2>
                        <p class="text-muted">Configure system-wide parameters and travel compensation rates.</p>
                    </div>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white p-4">
                                <h5 class="card-title mb-0"><i class="fe fe-truck mr-2 text-primary"></i> Travel Allowance</h5>
                            </div>
                            <form action="settings?action=updateRate" method="POST">
                                <div class="card-body p-4">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-600">Current Rate (per KM)</label>
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-0">₹</span>
                                            </div>
                                            <input type="number" step="0.01" name="rate" class="form-control bg-light border-0" value="<?php echo $rate; ?>" required>
                                        </div>
                                        <small class="form-text text-muted mt-2">This rate will be used to automatically calculate allowance earned for all executives.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block py-3 shadow-sm">Update System Rate</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white p-4">
                                <h5 class="card-title mb-0"><i class="fe fe-history mr-2 text-primary"></i> Rate History</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 small">
                                        <thead>
                                            <tr>
                                                <th class="pl-4">Rate</th>
                                                <th>Updated At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($history as $h): ?>
                                            <tr>
                                                <td class="pl-4 font-weight-bold">₹<?php echo number_format($h['rate_per_km'], 2); ?></td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($h['created_at'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white p-4">
                        <h5 class="card-title mb-0"><i class="fe fe-settings mr-2 text-primary"></i> System Parameters</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Site Title</label>
                                <input type="text" class="form-control bg-light border-0" value="FieldTrack Pro" readonly>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Tracking Frequency</label>
                                <select class="form-control bg-light border-0" disabled>
                                    <option>Every 5 Minutes</option>
                                    <option selected>Every 15 Minutes</option>
                                    <option>Every 30 Minutes</option>
                                </select>
                            </div>
                        </div>
                        <div class="alert alert-info border-0 small mt-3">
                            <i class="fe fe-info mr-2"></i> Some parameters are currently locked and can be modified in the source configuration.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.font-weight-600 { font-weight: 600; }
</style>

<?php include 'layout/footer.php'; ?>
