-- ============================================================
-- USER MANAGEMENT: Add is_active column for disable/enable
-- Run on both DEV and PRODUCTION. Safe (IF NOT EXISTS).
-- ============================================================

ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1;

-- Optional: view disabled users
-- SELECT id, name, email, is_active FROM users WHERE is_active = 0;
