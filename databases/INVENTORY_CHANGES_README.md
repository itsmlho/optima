# Database Changes - inventory_unit & inventory_attachment

**Date:** February 14, 2026  
**Migration Context:** Sprint 1-3 Advanced Billing + Status Cleanup

## Summary

### ✅ inventory_unit Table Changes

**Added 3 new columns** to support advanced billing features:

| Column Name | Data Type | Default | Purpose |
|-------------|-----------|---------|---------|
| `on_hire_date` | DATE | NULL | Date when unit was hired out to customer |
| `off_hire_date` | DATE | NULL | Date when unit was returned from customer |
| `rate_changed_at` | DATETIME | NULL | Timestamp of last rate change |

### ✅ inventory_attachment Table Changes

**Removed unused columns** and **added constraints**:

| Change Type | Details |
|-------------|---------|
| **DROPPED Columns** | `status_unit`, `status_attachment_id` |
| **DROPPED Foreign Keys** | `fk_inventory_attachment_status_unit`, `fk_inventory_attachment_status_attachment` |
| **ADDED Index** | `idx_inventory_attachment_status` |
| **ADDED UNIQUE Keys** | `uk_unit_attachment`, `uk_unit_charger`, `uk_unit_battery` |

---

## Detailed Changes

## PART 1: inventory_unit

### 1. on_hire_date
- **Position:** After `kontrak_id`
- **Usage:** 
  - Calculate rental periods per unit
  - Support staggered delivery (different units can have different start dates)
  - Unit billing schedule tracking
  - Used in `unit_billing_schedules` table joins

### 2. off_hire_date
- **Position:** After `on_hire_date`
- **Usage:**
  - Mark when unit rental ended
  - Calculate total rental duration
  - Determine unit availability status
  - NULL value = unit still on rent

### 3. rate_changed_at
- **Position:** After `harga_sewa_bulanan`
- **Usage:**
  - Track when rental rate was last modified
  - Used in contract amendments feature
  - Rate change history tracking
  - Connected to `contract_amendments` and `amendment_unit_rates` tables

---

## PART 2: inventory_attachment

### Changes Overview

The `inventory_attachment` table underwent cleanup to remove unused columns that were replaced by the `attachment_status` field.

### 1. DROPPED Columns

#### status_unit (REMOVED)
- **Reason:** Replaced by `attachment_status` ENUM field
- **Previous Type:** INT or similar reference to status table
- **Migration:** Data migrated to `attachment_status` before column removal

#### status_attachment_id (REMOVED)
- **Reason:** Replaced by `attachment_status` ENUM field
- **Previous Type:** Foreign key reference
- **Migration:** Data migrated to `attachment_status` before column removal

### 2. DROPPED Foreign Keys

#### fk_inventory_attachment_status_unit
- **Reason:** Referenced old status table no longer used
- **Safely removed** after data migration complete

#### fk_inventory_attachment_status_attachment
- **Reason:** Referenced old status table no longer used
- **Safely removed** after data migration complete

### 3. ADDED Index

#### idx_inventory_attachment_status
- **Purpose:** Improve query performance on `attachment_status` field
- **Type:** Standard INDEX
- **Columns:** `attachment_status`

### 4. ADDED UNIQUE Constraints

These constraints prevent duplicate assignments:

#### uk_unit_attachment
- **Constraint:** `(id_inventory_unit, attachment_id)`
- **Purpose:** Prevent same attachment being assigned to same unit twice
- **Example:** Unit #123 cannot have Attachment #456 assigned twice

#### uk_unit_charger
- **Constraint:** `(id_inventory_unit, charger_id)`
- **Purpose:** Prevent same charger being assigned to same unit twice
- **Example:** Unit #123 cannot have Charger #789 assigned twice

#### uk_unit_battery
- **Constraint:** `(id_inventory_unit, baterai_id)`
- **Purpose:** Prevent same battery being assigned to same unit twice
- **Example:** Unit #123 cannot have Battery #101 assigned twice

---

## Related New Tables (Sprint 1-3)

These columns support integration with new tables:

1. **unit_billing_schedules**
   - Tracks per-unit billing cycles
   - Uses `on_hire_date` and `off_hire_date` for billing calculation
   
2. **contract_amendments**
   - Tracks contract rate changes
   - References `rate_changed_at` for audit trail

3. **amendment_unit_rates**
   - Stores per-unit rate changes
   - Updates `rate_changed_at` when rates change

4. **contract_renewal_workflow**
   - Manages contract renewals
   - Uses hire dates to validate renewal timing

5. **contract_renewal_unit_map**
   - Maps units from old to new contracts
   - Considers off_hire_date for availability

---

## Migration Safety

- All columns use `IF NOT EXISTS` clause
- All columns are NULLABLE (no data loss risk)
- No existing data modified
- Can be rolled back by dropping columns

---

## SQL File Location

**Main Migration File:** `c:\laragon\www\optima\databases\INVENTORY_TABLES_CHANGES_2026-02-14.sql`

**Comprehensive Migrations:** 
- `databases/migrations/SAFE_MIGRATION_SPRINT_1_2_3.sql`
- `databases/migrations/EXECUTE_ALL_SPRINTS_MIGRATIONS.sql`
- `databases/migrations/2026-02-10-create-unit-billing-schedules.sql`
- `databases/migrations/2026-02-10-create-contract-amendments.sql`

---

## Verification

After running the SQL, verify with:

**For inventory_unit:**
```sql
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at')
ORDER BY ORDINAL_POSITION;
```

Expected result: 3 rows showing the new columns.

**For inventory_attachment (dropped columns should be empty):**
```sql
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'inventory_attachment'
  AND COLUMN_NAME IN ('status_unit', 'status_attachment_id');
```

Expected result: 0 rows (columns should be gone).

**For inventory_attachment (verify new indexes):**
```sql
SELECT INDEX_NAME, COLUMN_NAME, NON_UNIQUE
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'inventory_attachment'
  AND INDEX_NAME IN ('idx_inventory_attachment_status', 'uk_unit_attachment', 'uk_unit_charger', 'uk_unit_battery')
ORDER BY INDEX_NAME;
```

Expected result: 4 indexes (1 standard index + 3 unique keys).

---

## Impact Analysis

### inventory_unit - Features Enabled:
✅ Unit-level billing schedules  
✅ Staggered delivery support  
✅ Contract amendments with rate tracking  
✅ Individual unit hire date tracking  
✅ Rental period calculations  

### inventory_attachment - Features Enabled:
✅ Data integrity (no duplicate assignments)  
✅ Performance improvement (indexed attachment_status)  
✅ Cleaner schema (removed unused columns)  
✅ Prevent duplicate attachment/charger/battery per unit  

### Backward Compatibility:

**inventory_unit:**
✅ All new columns are optional (NULL allowed)  
✅ Existing queries not affected  
✅ No performance impact (columns indexed in related tables)  

**inventory_attachment:**
⚠️ **BREAKING:** Columns `status_unit` and `status_attachment_id` removed  
✅ Application must already use `attachment_status` field  
✅ UNIQUE constraints prevent duplicate data entry  
✅ Query performance improved with new index  

### Data Migration Required:

**Before running this SQL:**
1. ✅ Ensure all code uses `attachment_status` instead of old status columns
2. ✅ Run duplicate cleanup migration (separate script)
3. ✅ Backup `inventory_attachment` table
4. ✅ Test in staging environment first

### Related Code Changes:
- Controllers may reference these fields for billing logic
- Views may display hire/off-hire dates
- Models will include these in `$allowedFields` if needed
- **IMPORTANT:** Remove all references to `status_unit` and `status_attachment_id` in code

---

## Migration Order (IMPORTANT)

Execute migrations in this order:

1. **Phase 1:** Data migration (001_cleanup_inventory_attachment_status.sql)
   - Migrate data from old columns to `attachment_status`
   - Update application code to use new field

2. **Phase 2:** Column cleanup (002_drop_unused_status_columns.sql)
   - Drop old status columns and foreign keys
   - This is included in INVENTORY_TABLES_CHANGES_2026-02-14.sql

3. **Phase 3:** Add constraints (003_add_unique_constraint_inventory_attachment.sql)
   - Clean up duplicates
   - Add UNIQUE keys
   - This is included in INVENTORY_TABLES_CHANGES_2026-02-14.sql

4. **Phase 4:** Add inventory_unit columns
   - Add hire date and rate tracking columns
   - This is included in INVENTORY_TABLES_CHANGES_2026-02-14.sql

---

## Notes

1. **inventory_unit changes** are **additive** (safe to run anytime)
2. **inventory_attachment changes** are **destructive** (requires preparation)
3. Both changes are backward compatible **IF:**
   - Application code already updated for `attachment_status`
   - Old status columns no longer referenced in code
4. Future features (unit swaps, maintenance tracking) may utilize hire dates
5. The `rate_changed_at` field is automatically updated by amendment workflows
6. UNIQUE constraints will prevent duplicate attachment assignments going forward

---

## Rollback Plan

**For inventory_unit (if needed):**
```sql
ALTER TABLE inventory_unit DROP COLUMN IF EXISTS on_hire_date;
ALTER TABLE inventory_unit DROP COLUMN IF EXISTS off_hire_date;
ALTER TABLE inventory_unit DROP COLUMN IF EXISTS rate_changed_at;
```

**For inventory_attachment (NOT RECOMMENDED - data loss):**
```sql
-- Restore from backup table instead:
-- Use inventory_attachment_backup_20260202 if available
ALTER TABLE inventory_attachment DROP INDEX IF EXISTS idx_inventory_attachment_status;
ALTER TABLE inventory_attachment DROP INDEX IF EXISTS uk_unit_attachment;
ALTER TABLE inventory_attachment DROP INDEX IF EXISTS uk_unit_charger;
ALTER TABLE inventory_attachment DROP INDEX IF EXISTS uk_unit_battery;
```

---

## Session Context Summary

This documentation covers **ALL database changes** made during the development sessions including:

1. **Sprint 1-3 Advanced Billing Features**
   - Unit-level billing schedules
   - Contract amendments with prorate
   - Renewal workflow tracking

2. **Status Field Cleanup (February 2026)**
   - Removed obsolete status columns
   - Migrated to standardized `attachment_status` field
   - Added data integrity constraints

3. **Modal UI Fixes (February 14, 2026)**
   - Fixed 40+ modals across 28 files
   - Added scrollable and centered classes
   - System-wide modal overlap issues resolved
   - No database changes for modal fixes

**Total Database Tables Modified:** 2 (inventory_unit, inventory_attachment)  
**Total UI Files Modified:** 28 files (modal fixes)  
**Migration Files Created:** 2 (SQL + Documentation)
