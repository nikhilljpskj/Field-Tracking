-- ================================================================
-- REDEEMER HRMS — CONSOLIDATED PRODUCTION MIGRATION SCRIPT
-- Generated: 2026-04-22
-- Run this once on the PRODUCTION database via phpMyAdmin or CLI.
-- All ALTER TABLE statements use IF NOT EXISTS (safe & idempotent).
-- ================================================================


-- ================================================================
-- STEP 1: PAYROLL HISTORY — Add missing columns
-- (Fixes the SQLSTATE[42S22] Unknown column 'gross_salary' error)
-- ================================================================

ALTER TABLE payroll_history
    ADD COLUMN IF NOT EXISTS gross_salary         DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS lop_deductions       DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS net_salary           DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS breakdown_json       TEXT,
    ADD COLUMN IF NOT EXISTS monthly_ctc          DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS annual_ctc           DECIMAL(12,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS processed_by         INT           DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS is_statutory_enabled TINYINT(1)    DEFAULT 1;


-- ================================================================
-- STEP 2: USERS — Add is_active column (Employee Disable/Enable)
-- ================================================================

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1;


-- ================================================================
-- STEP 3: CLEANUP NOTIFICATION DISMISSALS TABLE
-- (Tracks per-admin per-day banner dismissals for 60-day cleanup)
-- ================================================================

CREATE TABLE IF NOT EXISTS cleanup_notification_dismissals (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT  NOT NULL,
    dismiss_date DATE NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_date (user_id, dismiss_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ================================================================
-- STEP 4: Ensure created_at columns exist on attendance &
--         client_meetings (needed for 60-day retention cleanup)
-- ================================================================

ALTER TABLE attendance
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE client_meetings
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;


-- ================================================================
-- STEP 5: REMOVE TEST / DEMO USERS
-- Emails: rahul@example.com, manager@example.com, staff@example.com
--
-- SAFETY: This first previews which users will be deleted.
--         Review the SELECT output before running the DELETEs.
-- ================================================================

-- ---- Preview (run first to confirm) ----
SELECT id, name, email, created_at
FROM users
WHERE email IN (
    'rahul@example.com',
    'manager@example.com',
    'staff@example.com'
);

-- ---- Delete related data (attendance, meetings, payroll) ----
-- This cascades cleanup for the demo users before removing them.

DELETE FROM attendance
WHERE user_id IN (
    SELECT id FROM (
        SELECT id FROM users WHERE email IN (
            'rahul@example.com', 'manager@example.com', 'staff@example.com'
        )
    ) AS t
);

DELETE FROM client_meetings
WHERE user_id IN (
    SELECT id FROM (
        SELECT id FROM users WHERE email IN (
            'rahul@example.com', 'manager@example.com', 'staff@example.com'
        )
    ) AS t
);

DELETE FROM payroll_history
WHERE user_id IN (
    SELECT id FROM (
        SELECT id FROM users WHERE email IN (
            'rahul@example.com', 'manager@example.com', 'staff@example.com'
        )
    ) AS t
);

DELETE FROM salary_structures
WHERE user_id IN (
    SELECT id FROM (
        SELECT id FROM users WHERE email IN (
            'rahul@example.com', 'manager@example.com', 'staff@example.com'
        )
    ) AS t
);

-- ---- Finally, delete the users themselves ----
DELETE FROM users
WHERE email IN (
    'rahul@example.com',
    'manager@example.com',
    'staff@example.com'
);

-- Confirm deletion
SELECT 'Remaining demo users (should be 0):' AS check_result;
SELECT COUNT(*) AS remaining_demo_users
FROM users
WHERE email IN (
    'rahul@example.com',
    'manager@example.com',
    'staff@example.com'
);


-- ================================================================
-- STEP 6: VERIFICATION — Confirm all changes applied correctly
-- ================================================================

SELECT 'payroll_history columns:' AS check_section;
DESCRIBE payroll_history;

SELECT 'users.is_active exists:' AS check_section;
SHOW COLUMNS FROM users LIKE 'is_active';

SELECT 'cleanup_notification_dismissals table:' AS check_section;
SHOW TABLES LIKE 'cleanup_notification_dismissals';

SELECT 'attendance.created_at exists:' AS check_section;
SHOW COLUMNS FROM attendance LIKE 'created_at';

SELECT 'client_meetings.created_at exists:' AS check_section;
SHOW COLUMNS FROM client_meetings LIKE 'created_at';

-- ================================================================
-- END OF MIGRATION SCRIPT
-- ================================================================
