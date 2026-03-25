<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Reports & Approvals</h2>
                        <p class="text-muted">Review client meetings and travel allowance submissions from your team.</p>
                    </div>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>

                <!-- Meeting Reports Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-primary">Client Meetings</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Executive</th>
                                        <th>Date & Time</th>
                                        <th>Client / Hospital</th>
                                        <th>Meeting Type</th>
                                        <th>Status</th>
                                        <th class="text-right pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($meetings)): ?>
                                        <tr><td colspan="6" class="text-center py-4 text-muted">No meeting records found.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($meetings as $m): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-600"><?php echo htmlspecialchars($m['user_name']); ?></td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($m['meeting_time'])); ?></td>
                                        <td>
                                            <div class="font-weight-500"><?php echo htmlspecialchars($m['client_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($m['hospital_office_name']); ?></small>
                                        </td>
                                        <td><?php echo $m['meeting_type']; ?></td>
                                        <td>
                                            <?php 
                                            $badge = 'badge-secondary';
                                            if($m['status'] == 'Approved') $badge = 'badge-success';
                                            if($m['status'] == 'Rejected') $badge = 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $badge; ?> px-2 py-1"><?php echo $m['status']; ?></span>
                                        </td>
                                        <td class="text-right pr-4">
                                            <div class="btn-group">
                                                <?php if($m['status'] == 'Pending'): ?>
                                                    <a href="reports?action=approveMeeting&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-success mr-1">Approve</a>
                                                    <a href="reports?action=rejectMeeting&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-danger mr-1">Reject</a>
                                                <?php endif; ?>
                                                <?php if($_SESSION['role'] == 'Admin'): ?>
                                                    <a href="reports?action=editMeeting&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-light">Edit</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Travel Summaries Section -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-primary">Travel Allowance Claims</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Executive</th>
                                        <th>Date</th>
                                        <th>Total Distance</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-right pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($travelSummaries)): ?>
                                        <tr><td colspan="6" class="text-center py-4 text-muted">No travel claims found.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach($travelSummaries as $s): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-600"><?php echo htmlspecialchars($s['user_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($s['date'])); ?></td>
                                        <td><strong><?php echo number_format($s['total_distance'], 2); ?> KM</strong></td>
                                        <td class="text-success font-weight-bold">₹<?php echo number_format($s['allowance_earned'], 2); ?></td>
                                        <td>
                                            <?php 
                                            $badge = 'badge-secondary';
                                            if($s['status'] == 'Approved') $badge = 'badge-success';
                                            if($s['status'] == 'Rejected') $badge = 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $badge; ?> px-2 py-1"><?php echo $s['status']; ?></span>
                                        </td>
                                        <td class="text-right pr-4">
                                            <?php if($s['status'] == 'Pending'): ?>
                                                <a href="reports?action=approveTravel&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-success mr-1">Approve</a>
                                                <a href="reports?action=rejectTravel&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger">Reject</a>
                                            <?php else: ?>
                                                <span class="text-muted small italic">Finalized</span>
                                            <?php endif; ?>
                                        </td>
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

<style>
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
