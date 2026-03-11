-- Add mechanic_name for "who checked" (optional, when mechanic is not a system user)
-- Add ADD_UNIT to result enum for "unit kurang" flow
-- 2026-03-10

-- Add mechanic_name to unit_audit_locations
ALTER TABLE unit_audit_locations
ADD COLUMN mechanic_name VARCHAR(100) NULL DEFAULT NULL
COMMENT 'Nama mekanik yang mengecek (jika bukan user sistem)' AFTER audited_by;

-- Add ADD_UNIT to unit_audit_location_items result enum
ALTER TABLE unit_audit_location_items
MODIFY COLUMN result ENUM(
    'MATCH', 'NO_UNIT_IN_KONTRAK', 'EXTRA_UNIT', 'ADD_UNIT',
    'MISMATCH_NO_UNIT', 'MISMATCH_SERIAL', 'MISMATCH_SPEC', 'MISMATCH_SPARE'
) DEFAULT 'MATCH';
