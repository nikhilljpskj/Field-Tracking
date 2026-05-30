<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 page-title">Contact Directory</h2>
                <p class="text-muted mb-0">Employee contact list based on your access level.</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fe fe-download mr-1"></i> Export
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-sm">
                    <a class="dropdown-item" href="contacts?action=export&format=csv">
                        <i class="fe fe-file-text mr-2 text-success"></i> Excel (CSV)
                    </a>
                    <a class="dropdown-item" target="_blank" href="contacts?action=export&format=pdf">
                        <i class="fe fe-printer mr-2 text-danger"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="contactsTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($contacts)): ?>
                                <?php foreach ($contacts as $c): ?>
                                    <tr>
                                        <td class="font-weight-600"><?php echo htmlspecialchars($c['name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($c['role_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($c['email'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($c['phone'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No contact records available for your scope.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'layout/footer.php'; ?>

