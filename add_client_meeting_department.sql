-- ============================================================
-- Add department field to client meetings
-- Safe for production (IF NOT EXISTS)
-- ============================================================

ALTER TABLE client_meetings ADD COLUMN IF NOT EXISTS department VARCHAR(255) NULL AFTER hospital_office_name;
