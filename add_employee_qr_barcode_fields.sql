-- Employee QR / Barcode module migration (safe, idempotent)
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS employee_code VARCHAR(20) NULL,
  ADD COLUMN IF NOT EXISTS qr_token VARCHAR(64) NULL,
  ADD COLUMN IF NOT EXISTS barcode_generated_at DATETIME NULL,
  ADD COLUMN IF NOT EXISTS department VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS designation VARCHAR(100) NULL;

SET @has_emp_code_idx := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'users' AND index_name = 'uq_users_employee_code'
);
SET @sql_emp_code_idx := IF(@has_emp_code_idx = 0,
  'ALTER TABLE users ADD UNIQUE KEY uq_users_employee_code (employee_code)',
  'SELECT 1');
PREPARE stmt_emp_code_idx FROM @sql_emp_code_idx;
EXECUTE stmt_emp_code_idx;
DEALLOCATE PREPARE stmt_emp_code_idx;

SET @has_qr_token_idx := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'users' AND index_name = 'uq_users_qr_token'
);
SET @sql_qr_token_idx := IF(@has_qr_token_idx = 0,
  'ALTER TABLE users ADD UNIQUE KEY uq_users_qr_token (qr_token)',
  'SELECT 1');
PREPARE stmt_qr_token_idx FROM @sql_qr_token_idx;
EXECUTE stmt_qr_token_idx;
DEALLOCATE PREPARE stmt_qr_token_idx;
