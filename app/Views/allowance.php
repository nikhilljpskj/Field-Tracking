<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="h3 mb-4 page-title">Travel Allowance Summary</h2>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card shadow border-0 bg-primary text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-3 text-center">
                                        <span class="circle circle-sm bg-primary-light">
                                            <i class="fe fe-16 fe-navigation text-white mb-0"></i>
                                        </span>
                                    </div>
                                    <div class="col pr-0">
                                        <p class="small text-white-50 mb-0">Today's Distance</p>
                                        <span class="h3 mb-0"><?php echo number_format($summary['total_distance'], 2); ?> KM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow border-0 bg-success text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-3 text-center">
                                        <span class="circle circle-sm bg-success-light">
                                            <i class="fe fe-16 fe-dollar-sign text-white mb-0"></i>
                                        </span>
                                    </div>
                                    <div class="col pr-0">
                                        <p class="small text-white-50 mb-0">Allowance Earned</p>
                                        <span class="h3 mb-0">₹<?php echo number_format($summary['allowance_earned'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow border-0 bg-info text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-3 text-center">
                                        <span class="circle circle-sm bg-info-light">
                                            <i class="fe fe-16 fe-activity text-white mb-0"></i>
                                        </span>
                                    </div>
                                    <div class="col pr-0">
                                        <p class="small text-white-50 mb-0">Current Rate</p>
                                        <span class="h3 mb-0">₹<?php echo number_format($rate, 2); ?> / KM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header">
                        <strong class="card-title">Daily Travel Breakdown</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Automatic calculation based on periodic GPS tracking points captured during your work hours.</p>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Distance</th>
                                    <th>Rate</th>
                                    <th>Total Earnings</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo date('d M Y'); ?></td>
                                    <td><?php echo number_format($summary['total_distance'], 2); ?> KM</td>
                                    <td>₹<?php echo number_format($rate, 2); ?></td>
                                    <td><strong>₹<?php echo number_format($summary['allowance_earned'], 2); ?></strong></td>
                                    <td><span class="badge badge-pill badge-warning">Processing</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'layout/footer.php'; ?>
