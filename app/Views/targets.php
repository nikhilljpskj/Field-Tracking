<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="h3 mb-4 page-title">Sales Performance & Targets</h2>
                
                <div class="row">
                    <?php if(empty($targets)): ?>
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No active targets assigned.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($targets as $t): ?>
                            <?php 
                                $percent = ($t['target_value'] > 0) ? ($t['achieved_value'] / $t['target_value']) * 100 : 0;
                                $percent = min($percent, 100);
                                $color = ($percent < 30) ? 'danger' : (($percent < 70) ? 'warning' : 'success');
                            ?>
                            <div class="col-md-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="row align-items-center mb-3">
                                            <div class="col">
                                                <span class="h6 text-muted text-uppercase"><?php echo $t['period']; ?> <?php echo $t['type']; ?></span>
                                                <h3 class="mb-0"><?php echo number_format($t['achieved_value']); ?> / <?php echo number_format($t['target_value']); ?></h3>
                                            </div>
                                            <div class="col-auto">
                                                <span class="h4 text-<?php echo $color; ?>"><?php echo round($percent); ?>%</span>
                                            </div>
                                        </div>
                                        <div class="progress mb-3" style="height: 10px;">
                                            <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $percent; ?>%" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="row small">
                                            <div class="col">
                                                <span class="text-muted">Starts: <?php echo date('d M', strtotime($t['start_date'])); ?></span>
                                            </div>
                                            <div class="col text-right">
                                                <span class="text-muted">Ends: <?php echo date('d M', strtotime($t['end_date'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <?php if(empty($targets)): ?>
                    <!-- Optional: Show some dummy inspiration or tips -->
                    <div class="alert alert-info border-0 shadow-sm">
                        <i class="fe fe-info mr-2"></i> Reach out to your manager to set your monthly performance targets.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'layout/footer.php'; ?>
