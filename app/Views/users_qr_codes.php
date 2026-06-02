<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
          <div>
            <h2 class="h3 mb-0 page-title">Employee QR / Barcode</h2>
            <p class="text-muted mb-0">Generate, preview, and download employee ID QR and barcode.</p>
          </div>
          <a href="employee-codes?action=bulkGenerate" class="btn btn-primary" onclick="return confirm('Generate employee codes for users missing code?')">
            <i class="fe fe-refresh-cw mr-1"></i> Bulk Generate Missing Codes
          </a>
        </div>

        <?php if(isset($_SESSION['flash_success'])): ?>
          <div class="alert alert-success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['flash_error'])): ?>
          <div class="alert alert-danger"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-3">
          <div class="card-body">
            <form method="GET" action="employee-codes" class="form-row">
              <input type="hidden" name="url" value="employee-codes">
              <div class="col-md-10 mb-2 mb-md-0">
                <input type="text" name="q" class="form-control" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Search by name, employee code, department, designation">
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-block">Search</button>
              </div>
            </form>
          </div>
        </div>

        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="table-responsive d-none d-md-block">
              <table class="table table-bordered align-middle">
                <thead class="thead-light">
                  <tr>
                    <th>Employee</th>
                    <th>Employee Code</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>QR Preview</th>
                    <th>Barcode Preview</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($users as $u): ?>
                    <?php
                      $dept = $u['department'] ?: '-';
                      $designation = $u['designation'] ?: ($u['role_name'] ?? '-');
                    ?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars($u['name']); ?></strong>
                        <div class="small text-muted"><?php echo ($u['is_active'] ?? 1) ? 'Active' : 'Inactive'; ?></div>
                      </td>
                      <td><?php echo htmlspecialchars($u['employee_code'] ?: 'Not generated'); ?></td>
                      <td><?php echo htmlspecialchars($dept); ?></td>
                      <td><?php echo htmlspecialchars($designation); ?></td>
                      <td>
                        <img src="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=qr&format=png&preview=1" alt="QR" class="preview-img">
                      </td>
                      <td>
                        <img src="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=barcode&format=png&preview=1" alt="Barcode" class="preview-img preview-bar">
                      </td>
                      <td>
                        <div class="action-grid">
                          <a class="btn btn-xs btn-outline-primary action-btn" href="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=qr&format=png">QR PNG</a>
                          <a class="btn btn-xs btn-outline-primary action-btn" href="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=qr&format=svg">QR SVG</a>
                          <a class="btn btn-xs btn-outline-dark action-btn" href="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=barcode&format=png">BAR PNG</a>
                          <a class="btn btn-xs btn-outline-dark action-btn" href="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=barcode&format=svg">BAR SVG</a>
                          <a class="btn btn-xs btn-outline-warning action-btn action-btn-wide" href="employee-codes?action=regenerate&id=<?php echo $u['id']; ?>" onclick="return confirm('Regenerate token for this employee?')">Regenerate</a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div class="d-md-none">
              <?php foreach($users as $u): ?>
                <div class="card border mb-3">
                  <div class="card-body">
                    <h6 class="mb-1"><?php echo htmlspecialchars($u['name']); ?></h6>
                    <p class="small text-muted mb-2"><?php echo htmlspecialchars($u['employee_code'] ?: 'Not generated'); ?></p>
                    <p class="small mb-1"><strong>Department:</strong> <?php echo htmlspecialchars($u['department'] ?: '-'); ?></p>
                    <p class="small mb-2"><strong>Designation:</strong> <?php echo htmlspecialchars($u['designation'] ?: ($u['role_name'] ?? '-')); ?></p>
                    <div class="d-flex flex-wrap">
                      <img src="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=qr&format=png&preview=1" alt="QR" class="preview-img mr-2 mb-2">
                      <img src="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=barcode&format=png&preview=1" alt="Barcode" class="preview-img preview-bar mb-2">
                    </div>
                    <div class="action-grid">
                      <a class="btn btn-xs btn-outline-primary action-btn" href="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=qr&format=png">QR PNG</a>
                      <a class="btn btn-xs btn-outline-primary action-btn" href="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=qr&format=svg">QR SVG</a>
                      <a class="btn btn-xs btn-outline-dark action-btn" href="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=barcode&format=png">BAR PNG</a>
                      <a class="btn btn-xs btn-outline-dark action-btn" href="employee-codes?action=download&id=<?php echo $u['id']; ?>&type=barcode&format=svg">BAR SVG</a>
                      <a class="btn btn-xs btn-outline-warning action-btn action-btn-wide" href="employee-codes?action=regenerate&id=<?php echo $u['id']; ?>" onclick="return confirm('Regenerate token for this employee?')">Regenerate</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<style>
.preview-img { width: 88px; height: 88px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px; background: transparent; }
.preview-bar { width: 180px; height: 64px; }
.btn-xs { padding: .2rem .45rem; font-size: .7rem; line-height: 1.2; border-radius: .25rem; }
.action-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(78px, 1fr));
  gap: 6px;
  align-items: center;
}
.action-btn {
  min-width: 78px;
  text-align: center;
  white-space: nowrap;
}
.action-btn-wide { grid-column: span 2; }
</style>

<?php include 'layout/footer.php'; ?>
