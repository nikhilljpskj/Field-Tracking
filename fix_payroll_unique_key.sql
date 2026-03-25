-- PAYROLL HISTORY UNIQUE CONSTRAINT MIGRATION
-- Ensures (user_id, month, year) is unique to allow INSERT ... ON DUPLICATE KEY UPDATE

-- First, remove any potential existing duplicates (optional but safe)
-- DELETE t1 FROM payroll_history t1
-- INNER JOIN payroll_history t2 
-- WHERE t1.id < t2.id 
-- AND t1.user_id = t2.user_id 
-- AND t1.month = t2.month 
-- AND t1.year = t2.year;

ALTER TABLE payroll_history ADD UNIQUE KEY uk_user_month_year (user_id, month, year);
