<?php include 'layout/header.php'; ?>

<main class="main-content">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h3 class="mb-3">Employee Verification</h3>
            <p class="text-muted mb-4">Redeemer Technologies</p>
            <div class="mb-2"><strong>Employee Name:</strong> <?php echo htmlspecialchars($employee['name']); ?></div>
            <div class="mb-2"><strong>Employee Code:</strong> <?php echo htmlspecialchars($employee['employee_code'] ?: '-'); ?></div>
            <div class="mb-2"><strong>Department:</strong> <?php echo htmlspecialchars($employee['department'] ?: '-'); ?></div>
            <div class="mb-2"><strong>Designation:</strong> <?php echo htmlspecialchars($employee['designation'] ?: ($employee['role_name'] ?? '-')); ?></div>
            <div class="mb-0"><strong>Status:</strong> <?php echo ($employee['is_active'] ?? 1) ? 'Active' : 'Inactive'; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include 'layout/footer.php'; ?>
