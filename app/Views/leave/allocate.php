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

                <div class="card shadow-sm border-0">
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
                                            <td><span class="badge badge-soft-primary"><?php echo $u['role_name']; ?></span></td>
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

                                        <!-- Allocate Modal -->
                                        <div class="modal fade" id="allocateModal_<?php echo $u['id']; ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <form action="leave-manage?action=bulkAllocate" method="POST" class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-primary text-white border-0">
                                                        <h5 class="modal-title font-weight-bold">Assign Quota: <?php echo htmlspecialchars($u['name']); ?></h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">Select Quarter</label>
                                                                <select name="quarter" class="form-control" required>
                                                                    <option value="1" <?php echo ceil(date('n')/3)==1?'selected':''; ?>>Q1 (Jan-Mar)</option>
                                                                    <option value="2" <?php echo ceil(date('n')/3)==2?'selected':''; ?>>Q2 (Apr-Jun)</option>
                                                                    <option value="3" <?php echo ceil(date('n')/3)==3?'selected':''; ?>>Q3 (Jul-Sep)</option>
                                                                    <option value="4" <?php echo ceil(date('n')/3)==4?'selected':''; ?>>Q4 (Oct-Dec)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold">Select Year</label>
                                                                <select name="year" class="form-control" required>
                                                                    <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                                                                    <option value="<?php echo date('Y')+1; ?>"><?php echo date('Y')+1; ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <hr class="my-3">
                                                        <?php 
                                                        $uAlloc = $allAllocations[$u['id']] ?? [];
                                                        foreach($leaveTypes as $lt): 
                                                        ?>
                                                            <div class="form-group">
                                                                <label class="small text-muted text-uppercase font-weight-bold"><?php echo htmlspecialchars($lt['name']); ?> Balance</label>
                                                                <input type="number" name="alloc[<?php echo $lt['id']; ?>]" class="form-control" value="<?php echo $uAlloc[$lt['id']] ?? $lt['quarterly_allocation']; ?>" required>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 pt-0">
                                                        <button type="submit" class="btn btn-primary btn-block shadow font-weight-bold">Save All Allocations</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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
.badge-soft-primary { background: rgba(67, 97, 238, 0.1); color: #4361ee; }
.font-weight-600 { font-weight: 600; }
</style>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
