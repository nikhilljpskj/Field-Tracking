-- ============================================================
-- Add scheduling/location fields to hospitals and doctors
-- Safe for production (IF NOT EXISTS)
-- ============================================================

ALTER TABLE hospitals ADD COLUMN IF NOT EXISTS allotted_day VARCHAR(20) NULL;
ALTER TABLE hospitals ADD COLUMN IF NOT EXISTS allotted_time TIME NULL;
ALTER TABLE hospitals ADD COLUMN IF NOT EXISTS location_url VARCHAR(500) NULL;

ALTER TABLE doctors ADD COLUMN IF NOT EXISTS allotted_day VARCHAR(20) NULL;
ALTER TABLE doctors ADD COLUMN IF NOT EXISTS allotted_time TIME NULL;

