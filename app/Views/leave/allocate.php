<?php include dirname(__DIR__) . '/layout/header.php'; ?>
<?php include dirname(__DIR__) . '/layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 px-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Leave Quota Management</h2>
                        <p class="text-muted">Assign quarterly and annual leave balances to employees.</p>
                    </div>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 text-muted small text-uppercase font-weight-bold">Employee Quota Directory</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="pl-4">Employee</th>
                                        <th>Role</th>
                                        <?php foreach($leaveTypes as $lt): ?>
                                            <th class="text-center"><?php echo htmlspecialchars($lt['name']); ?> <small>(Annual)</small></th>
                                        <?php endforeach; ?>
                                        <th class="pr-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $u): ?>
                                        <tr>
                                            <td class="pl-4">
                                                <div class="font-weight-600"><?php echo htmlspecialchars($u['name']); ?></div>
                                                <small class="text-muted">ID: #EMP-<?php echo $u['id']; ?></small>
                                            </td>
                                            <td><span class="badge badge-soft-primary"><?php echo $u['role_name'] ?? 'User'; ?></span></td>
                                            <?php 
                                            foreach($leaveTypes as $lt): 
                                                $annual = $annualAllocations[$u['id']][$lt['id']] ?? 0;
                                            ?>
                                                <td class="text-center">
                                                    <div class="font-weight-bold"><?php echo $annual; ?> Days</div>
                                                    <small class="text-muted">Allocated for <?php echo date('Y'); ?></small>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="pr-4 text-right">
                                                <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#allocateModal_<?php echo $u['id']; ?>">
                                                    <i class="fe fe-plus mr-1"></i> Allocate
                                                </button>
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

<!-- Allocate Modals (Moved outside main content to prevent hover/nesting issues) -->
<?php foreach($users as $u): ?>
    <div class="modal fade" id="allocateModal_<?php echo $u['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalTitle_<?php echo $u['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="leave-manage?action=bulkAllocate" method="POST" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold" id="modalTitle_<?php echo $u['id']; ?>">Assign Quota: <?php echo htmlspecialchars($u['name']); ?></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                    <div class="alert alert-soft-info border-0 mb-4">
                        <i class="fe fe-info mr-2"></i> 
                        Assigning <strong><?php echo htmlspecialchars($u['name']); ?>'s</strong> leave balance for a specific quarter.
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small text-muted text-uppercase font-weight-bold">Target Quarter</label>
                            <select name="quarter" class="form-control bg-light border-0" required>
                                <option value="1" <?php echo ceil(date('n')/3)==1?'selected':''; ?>>Q1 (Jan - Mar)</option>
                                <option value="2" <?php echo ceil(date('n')/3)==2?'selected':''; ?>>Q2 (Apr - Jun)</option>
                                <option value="3" <?php echo ceil(date('n')/3)==3?'selected':''; ?>>Q3 (Jul - Sep)</option>
                                <option value="4" <?php echo ceil(date('n')/3)==4?'selected':''; ?>>Q4 (Oct - Dec)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small text-muted text-uppercase font-weight-bold">Academic Year</label>
                            <select name="year" class="form-control bg-light border-0" required>
                                <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                                <option value="<?php echo date('Y')+1; ?>"><?php echo date('Y')+1; ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <h6 class="text-uppercase text-muted small font-weight-bold border-bottom pb-2 mt-3 mb-3">Leave Categories (Days per Quarter)</h6>
                    
                    <div class="row">
                    <?php 
                    $uAlloc = $allAllocations[$u['id']] ?? [];
                    if (empty($leaveTypes)):
                    ?>
                        <div class="col-12 text-center py-5">
                            <div class="empty-state">
                                <i class="fe fe-alert-circle fe-24 text-muted mb-3 d-block"></i>
                                <h6 class="text-muted font-weight-bold">No Leave Types Found</h6>
                                <p class="text-muted small mb-4">Initialize the default "Sick, Casual, and Earned" leave categories to start assigning quotas.</p>
                                <a href="leave-allocate?action=seedDefaults" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                                    <i class="fe fe-database mr-1"></i> Seed Default Leave Types
                                </a>
                            </div>
                        </div>
                    <?php 
                    else:
                        foreach($leaveTypes as $lt): 
                            $icon = 'fe-calendar';
                            if(stripos($lt['name'], 'sick') !== false) $icon = 'fe-heart';
                            if(stripos($lt['name'], 'cl') !== false || stripos($lt['name'], 'casual') !== false) $icon = 'fe-user';
                            if(stripos($lt['name'], 'el') !== false || stripos($lt['name'], 'earned') !== false || stripos($lt['name'], 'er') !== false) $icon = 'fe-award';
                    ?>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded bg-light-soft">
                                <label class="small text-dark font-weight-bold mb-2 d-block">
                                    <i class="fe <?php echo $icon; ?> mr-1 text-primary"></i> <?php echo htmlspecialchars($lt['name']); ?>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.5" name="alloc[<?php echo $lt['id']; ?>]" class="form-control font-weight-bold" 
                                           value="<?php echo $uAlloc[$lt['id']] ?? $lt['quarterly_allocation']; ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white small">Days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-ghost-secondary px-4" data-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-lg font-weight-bold">Apply Quota</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<style>
.badge-soft-primary { background: rgba(67, 97, 238, 0.1); color: #4361ee; }
.font-weight-600 { font-weight: 600; }
.modal-backdrop { z-index: 1040 !important; }
.modal { z-index: 1050 !important; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
