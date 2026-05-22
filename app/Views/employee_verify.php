<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Employee Verification - Redeemer Technologies</title>
  <style>
    :root {
      --bg: #f4f7fb;
      --card: #ffffff;
      --text: #1f2937;
      --muted: #6b7280;
      --ok: #0f766e;
      --border: #e5e7eb;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: "Inter", "Segoe UI", Arial, sans-serif;
      background: radial-gradient(circle at top left, #eef6ff 0%, var(--bg) 45%, #eef2f7 100%);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .card {
      width: 100%;
      max-width: 680px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
      overflow: hidden;
    }
    .head {
      padding: 20px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .head img { width: 68px; height: 68px; object-fit: contain; }
    .head h1 { margin: 0; font-size: 1.2rem; }
    .head p { margin: 4px 0 0; color: var(--muted); font-size: 0.92rem; }
    .badge {
      display: inline-block;
      margin: 14px 20px 0;
      padding: 8px 12px;
      border-radius: 999px;
      background: #ecfeff;
      color: var(--ok);
      font-weight: 700;
      font-size: 0.82rem;
      border: 1px solid #c7f9f2;
    }
    .content {
      padding: 20px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px 22px;
    }
    .item label {
      display: block;
      font-size: 0.78rem;
      color: var(--muted);
      margin-bottom: 4px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    .item div {
      font-size: 1rem;
      font-weight: 600;
      word-break: break-word;
    }
    .footer {
      border-top: 1px solid var(--border);
      padding: 16px 20px 20px;
      background: #fafbfc;
    }
    .footer h3 {
      margin: 0 0 8px;
      font-size: 0.95rem;
    }
    .footer p {
      margin: 4px 0;
      color: #374151;
      font-size: 0.95rem;
    }
    @media (max-width: 640px) {
      .content { grid-template-columns: 1fr; }
      .head { align-items: flex-start; }
    }
  </style>
</head>
<body>
  <section class="card">
    <header class="head">
      <img src="assets/images/redeemer-technologies-logo.png" alt="Redeemer Technologies Logo">
      <div>
        <h1>Redeemer Technologies</h1>
        <p>Employee Identity Verification</p>
      </div>
    </header>

    <span class="badge">Verified Company Employee</span>

    <div class="content">
      <div class="item">
        <label>Employee ID</label>
        <div><?php echo htmlspecialchars($employee['employee_code'] ?: '-'); ?></div>
      </div>
      <div class="item">
        <label>Employee Name</label>
        <div><?php echo htmlspecialchars($employee['name']); ?></div>
      </div>
      <div class="item">
        <label>Designation</label>
        <div><?php echo htmlspecialchars($employee['designation'] ?: ($employee['role_name'] ?? '-')); ?></div>
      </div>
      <div class="item">
        <label>Department</label>
        <div><?php echo htmlspecialchars($employee['department'] ?: '-'); ?></div>
      </div>
      <div class="item">
        <label>Status</label>
        <div><?php echo ($employee['is_active'] ?? 1) ? 'Active' : 'Inactive'; ?></div>
      </div>
      <div class="item">
        <label>Company</label>
        <div>Redeemer Technologies</div>
      </div>
    </div>

    <footer class="footer">
      <h3>If this ID card is found, contact company:</h3>
      <p><strong>Phone:</strong> +91 9447-355775</p>
      <p><strong>Email:</strong> info@redeemertechnologies.com</p>
    </footer>
  </section>
</body>
</html>
