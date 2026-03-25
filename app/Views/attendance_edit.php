<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 mb-2">
                            <li class="breadcrumb-item"><a href="attendance">Attendance</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Record</li>
                        </ol>
                    </nav>
                    <h2 class="h3 mb-0 page-title">Edit Attendance Record</h2>
                    <p class="text-muted">Manually override check-in/out times or coordinates for <strong><?php echo htmlspecialchars($record['user_name']); ?></strong>.</p>
                </div>
                
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white p-4">
                        <h5 class="card-title"><i class="fe fe-edit-2 mr-2 text-primary"></i> Attendance Data</h5>
                    </div>
                    <form action="attendance?action=update" method="POST">
                        <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase font-weight-bold small text-muted mb-3 border-bottom pb-2">Check-In Information</h6>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Check-In Time</label>
                                    <input type="datetime-local" name="check_in_time" class="form-control form-control-lg bg-light border-0" value="<?php echo date('Y-m-d\TH:i', strtotime($record['check_in_time'])); ?>" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-600">Latitude</label>
                                    <input type="text" name="check_in_lat" class="form-control form-control-lg bg-light border-0" value="<?php echo $record['check_in_lat']; ?>">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-600">Longitude</label>
                                    <input type="text" name="check_in_lng" class="form-control form-control-lg bg-light border-0" value="<?php echo $record['check_in_lng']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-600">Location Address</label>
                                <input type="text" name="check_in_address" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($record['check_in_address']); ?>">
                            </div>

                            <h6 class="text-uppercase font-weight-bold small text-muted mt-5 mb-3 border-bottom pb-2">Check-Out Information</h6>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Check-Out Time</label>
                                    <input type="datetime-local" name="check_out_time" class="form-control form-control-lg bg-light border-0" value="<?php echo $record['check_out_time'] ? date('Y-m-d\TH:i', strtotime($record['check_out_time'])) : ''; ?>">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-600">Latitude</label>
                                    <input type="text" name="check_out_lat" class="form-control form-control-lg bg-light border-0" value="<?php echo $record['check_out_lat']; ?>">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-600">Longitude</label>
                                    <input type="text" name="check_out_lng" class="form-control form-control-lg bg-light border-0" value="<?php echo $record['check_out_lng']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-600">Location Address</label>
                                <input type="text" name="check_out_address" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($record['check_out_address']); ?>">
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 p-4 d-flex justify-content-between">
                            <a href="attendance" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">Update Attendance Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.font-weight-600 { font-weight: 600; }
</style>

<?php include 'layout/footer.php'; ?>
