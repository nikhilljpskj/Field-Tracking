<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 mb-2">
                            <li class="breadcrumb-item"><a href="reports">Reports</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Meeting</li>
                        </ol>
                    </nav>
                    <h2 class="h3 mb-0 page-title">Edit Meeting Record</h2>
                    <p class="text-muted">Administrator override for meeting details logged by <strong><?php echo htmlspecialchars($meeting['user_name']); ?></strong>.</p>
                </div>
                
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white p-4">
                        <h5 class="card-title"><i class="fe fe-briefcase mr-2 text-primary"></i> Meeting Details</h5>
                    </div>
                    <form action="reports?action=updateMeeting" method="POST">
                        <input type="hidden" name="id" value="<?php echo $meeting['id']; ?>">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Client Name</label>
                                    <input type="text" name="client_name" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($meeting['client_name']); ?>" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Hospital / Office Name</label>
                                    <input type="text" name="hospital_name" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($meeting['hospital_office_name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Meeting Type</label>
                                    <select name="meeting_type" class="form-control form-control-lg bg-light border-0">
                                        <option value="In-Person" <?php echo $meeting['meeting_type'] == 'In-Person' ? 'selected' : ''; ?>>In-Person Visit</option>
                                        <option value="Phone" <?php echo $meeting['meeting_type'] == 'Phone' ? 'selected' : ''; ?>>Phone Call</option>
                                        <option value="Video" <?php echo $meeting['meeting_type'] == 'Video' ? 'selected' : ''; ?>>Video Conference</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Meeting Date & Time</label>
                                    <input type="datetime-local" name="meeting_time" class="form-control form-control-lg bg-light border-0" value="<?php echo date('Y-m-d\TH:i', strtotime($meeting['meeting_time'])); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-600">Meeting Notes</label>
                                <textarea name="notes" class="form-control form-control-lg bg-light border-0" rows="3"><?php echo htmlspecialchars($meeting['notes']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-600">Outcome / Result</label>
                                <textarea name="outcome" class="form-control form-control-lg bg-light border-0" rows="2"><?php echo htmlspecialchars($meeting['outcome']); ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 p-4 d-flex justify-content-between">
                            <a href="reports" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">Save Meeting Changes</button>
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

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
