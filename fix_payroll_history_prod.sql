-- ============================================================
-- PAYROLL HISTORY - PRODUCTION FIX SCRIPT
-- Run this on the PRODUCTION database to fix missing columns.
-- All statements use IF NOT EXISTS to be safe and idempotent.
-- ============================================================

ALTER TABLE payroll_history
    ADD COLUMN IF NOT EXISTS gross_salary      DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS lop_deductions    DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS net_salary        DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS breakdown_json    TEXT,
    ADD COLUMN IF NOT EXISTS monthly_ctc       DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS annual_ctc        DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS processed_by      INT           DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS is_statutory_enabled TINYINT(1) DEFAULT 1;

-- Optional: Add unique key to prevent duplicate payroll per employee per month.
-- Skip if the key already exists (will throw a duplicate key error — safe to ignore).
-- ALTER TABLE payroll_history ADD UNIQUE KEY uk_user_month_year (user_id, month, year);

-- Verify final structure
DESCRIBE payroll_history;
