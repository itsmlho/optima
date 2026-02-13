# Production Database Migration Guide
## optima.sml.co.id - Database Update

**Date:** February 14, 2026  
**Environment:** Production  
**Database:** u138256737_optima_db  
**Changes:** inventory_unit + inventory_attachment modifications

---

## ⚠️ CRITICAL - READ BEFORE PROCEEDING

**This guide is for PRODUCTION database migration.**  
**DO NOT proceed without completing ALL preparation steps.**

---

## 📋 Pre-Migration Checklist

### 1. Backup Strategy ✅

**MANDATORY before any changes:**

```bash
# Option A: Full database backup via phpMyAdmin
1. Login to cPanel/phpMyAdmin
2. Select database: u138256737_optima_db
3. Click "Export" tab
4. Select "Custom" export method
5. Check ALL tables
6. Click "Go" to download backup
7. Save as: optima_db_backup_2026-02-14_BEFORE_MIGRATION.sql

# Option B: Via MySQL command (if SSH access)
mysqldump -u u138256737_root_optima -p \
  --single-transaction \
  --routines \
  --triggers \
  --events \
  u138256737_optima_db > optima_backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Backup Specific Tables (Additional Safety)

```sql
-- Create backup tables with timestamp
CREATE TABLE inventory_unit_backup_20260214 AS SELECT * FROM inventory_unit;
CREATE TABLE inventory_attachment_backup_20260214 AS SELECT * FROM inventory_attachment;

-- Verify backup row counts
SELECT 'inventory_unit' as table_name, COUNT(*) as row_count FROM inventory_unit
UNION ALL
SELECT 'inventory_unit_backup_20260214', COUNT(*) FROM inventory_unit_backup_20260214
UNION ALL
SELECT 'inventory_attachment', COUNT(*) FROM inventory_attachment
UNION ALL
SELECT 'inventory_attachment_backup_20260214', COUNT(*) FROM inventory_attachment_backup_20260214;
```

### 3. Verify Current Table Structure

```sql
-- Check current columns in inventory_unit
SHOW COLUMNS FROM inventory_unit 
WHERE Field IN ('on_hire_date', 'off_hire_date', 'rate_changed_at');

-- Check current columns in inventory_attachment
SHOW COLUMNS FROM inventory_attachment 
WHERE Field IN ('status_unit', 'status_attachment_id', 'attachment_status');

-- Check existing indexes in inventory_attachment
SHOW INDEXES FROM inventory_attachment 
WHERE Key_name IN ('idx_inventory_attachment_status', 'uk_unit_attachment', 'uk_unit_charger', 'uk_unit_battery');
```

### 4. Check for Application Downtime Window

**Recommended:** Schedule during low-traffic hours (e.g., 10 PM - 2 AM)

```
Estimated migration time: 5-10 minutes
Recommended downtime: 15 minutes (buffer for safety)
```

### 5. Maintenance Mode (Optional but Recommended)

Add to `.env` file before migration:
```
# Enable maintenance mode
CI_ENVIRONMENT = maintenance
```

Or create htaccess redirect:
```apache
# .htaccess - Temporary maintenance
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/maintenance.html$
RewriteRule ^(.*)$ /maintenance.html [R=503,L]
```

---

## 🔄 Migration Steps

### STEP 1: Pre-Migration Verification

Execute in this order:

```sql
USE u138256737_optima_db;

-- 1. Check if columns already exist (should return 0 rows if not yet migrated)
SELECT COUNT(*) as existing_columns
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at');

-- 2. Check if inventory_attachment still has old columns (should return 2 if not yet migrated)
SELECT COUNT(*) as old_columns_exist
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment'
  AND COLUMN_NAME IN ('status_unit', 'status_attachment_id');

-- 3. Check for duplicate attachments (MUST be 0 before adding UNIQUE constraints)
SELECT 
    id_inventory_unit,
    tipe_item,
    attachment_id,
    charger_id,
    baterai_id,
    COUNT(*) as duplicate_count
FROM inventory_attachment
WHERE id_inventory_unit IS NOT NULL
GROUP BY id_inventory_unit, tipe_item, attachment_id, charger_id, baterai_id
HAVING COUNT(*) > 1;
```

**STOP if duplicates found! Clean them first using duplicate cleanup script.**

---

### STEP 2: Execute Main Migration

**File:** `INVENTORY_TABLES_CHANGES_2026-02-14.sql`

**Method A: Via phpMyAdmin**
1. Open phpMyAdmin
2. Select database: u138256737_optima_db
3. Click "SQL" tab
4. Open file: `INVENTORY_TABLES_CHANGES_2026-02-14.sql`
5. Copy entire contents
6. Paste into SQL editor
7. Click "Go"

**Method B: Via MySQL CLI (if SSH access)**
```bash
mysql -u u138256737_root_optima -p u138256737_optima_db < INVENTORY_TABLES_CHANGES_2026-02-14.sql
```

**Expected Output:**
```
Query OK, 0 rows affected (0.XX sec) - inventory_unit columns added
Query OK, 0 rows affected (0.XX sec) - inventory_attachment constraints added
Query OK, X rows affected (0.XX sec) - verification queries
```

---

### STEP 3: Post-Migration Verification

```sql
USE u138256737_optima_db;

-- 1. Verify inventory_unit columns added
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at');
-- Expected: 3 rows

-- 2. Verify inventory_attachment old columns dropped
SELECT COUNT(*) as should_be_zero
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment'
  AND COLUMN_NAME IN ('status_unit', 'status_attachment_id');
-- Expected: 0 rows

-- 3. Verify inventory_attachment new indexes exist
SELECT INDEX_NAME, COLUMN_NAME, NON_UNIQUE
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment'
  AND INDEX_NAME IN ('idx_inventory_attachment_status', 'uk_unit_attachment', 'uk_unit_charger', 'uk_unit_battery')
ORDER BY INDEX_NAME;
-- Expected: 4 indexes

-- 4. Quick smoke test - ensure table is still readable
SELECT COUNT(*) as total_units FROM inventory_unit;
SELECT COUNT(*) as total_attachments FROM inventory_attachment;
```

---

### STEP 4: Application Smoke Test

**Test critical pages:**

1. **Inventory Unit List**
   - URL: https://optima.sml.co.id/warehouse/inventory/invent_unit
   - Expected: Page loads, unit list displays correctly
   - Check: Modal detail opens without errors

2. **Inventory Attachment List**
   - URL: https://optima.sml.co.id/warehouse/inventory/invent_attachment
   - Expected: Page loads, attachment list displays correctly
   - Check: Modal detail opens without errors

3. **Contract Management**
   - URL: https://optima.sml.co.id/marketing/customer_management
   - Expected: Contract list loads correctly

4. **SPK Management**
   - URL: https://optima.sml.co.id/marketing/spk
   - Expected: SPK list and detail modals work

**Check browser console for errors:**
- Press F12
- Check Console tab for any database-related errors
- Check Network tab for failed API requests

---

## 🚨 Rollback Plan (If Issues Occur)

### Immediate Rollback

```sql
USE u138256737_optima_db;

-- Rollback inventory_unit changes
ALTER TABLE inventory_unit DROP COLUMN IF EXISTS on_hire_date;
ALTER TABLE inventory_unit DROP COLUMN IF EXISTS off_hire_date;
ALTER TABLE inventory_unit DROP COLUMN IF EXISTS rate_changed_at;

-- Rollback inventory_attachment changes (restore from backup)
DROP TABLE IF EXISTS inventory_attachment;
CREATE TABLE inventory_attachment AS SELECT * FROM inventory_attachment_backup_20260214;

-- Verify restoration
SELECT COUNT(*) FROM inventory_unit;
SELECT COUNT(*) FROM inventory_attachment;
```

### Full Database Restore (Last Resort)

```bash
# Via MySQL CLI
mysql -u u138256737_root_optima -p u138256737_optima_db < optima_db_backup_2026-02-14_BEFORE_MIGRATION.sql

# Or via phpMyAdmin:
# 1. Select database
# 2. Click "Import" tab
# 3. Choose backup file
# 4. Click "Go"
```

---

## 📊 Migration Timeline Example

```
9:00 PM  - Announcement: Maintenance window starting
9:05 PM  - Enable maintenance mode
9:10 PM  - Execute full database backup
9:15 PM  - Create table backups (inventory_unit, inventory_attachment)
9:20 PM  - Execute migration SQL script
9:25 PM  - Post-migration verification queries
9:30 PM  - Smoke test critical pages
9:35 PM  - Monitor error logs
9:40 PM  - Disable maintenance mode
9:45 PM  - Monitor application for 30 minutes
10:15 PM - Migration complete (if no issues)
```

---

## 📝 Post-Migration Tasks

### 1. Monitor Application Logs

```bash
# Check CodeIgniter logs
tail -f writable/logs/log-$(date +%Y-%m-%d).log

# Watch for database errors
grep -i "database\|sql\|error" writable/logs/log-$(date +%Y-%m-%d).log
```

### 2. Monitor Database Performance

```sql
-- Check slow queries after migration
SHOW FULL PROCESSLIST;

-- Check table sizes
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE table_schema = 'u138256737_optima_db'
  AND table_name IN ('inventory_unit', 'inventory_attachment')
ORDER BY size_mb DESC;
```

### 3. Keep Backups

```
✅ Keep backup files for at least 30 days:
- optima_db_backup_2026-02-14_BEFORE_MIGRATION.sql
- inventory_unit_backup_20260214 (table)
- inventory_attachment_backup_20260214 (table)

After 30 days of stable operation:
DROP TABLE IF EXISTS inventory_unit_backup_20260214;
DROP TABLE IF EXISTS inventory_attachment_backup_20260214;
```

---

## ⚡ Quick Reference Commands

**Check migration status:**
```sql
USE u138256737_optima_db;

-- Quick check if migrated
SELECT 
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME='inventory_unit' 
     AND COLUMN_NAME IN ('on_hire_date','off_hire_date','rate_changed_at')) as unit_cols,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME='inventory_attachment' 
     AND COLUMN_NAME IN ('status_unit','status_attachment_id')) as old_attachment_cols,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME='inventory_attachment' 
     AND INDEX_NAME LIKE 'uk_unit_%') as unique_keys;

-- Expected after migration: unit_cols=3, old_attachment_cols=0, unique_keys=3
```

---

## 📞 Emergency Contacts

**If issues occur during migration:**

1. **Stop immediately** - Do not proceed further
2. **Execute rollback plan** - Restore from backups
3. **Document the error** - Screenshot/copy error messages
4. **Contact support** - Share error details

**Database Host Info:**
- Host: localhost
- Database: u138256737_optima_db
- User: u138256737_root_optima
- Application: https://optima.sml.co.id/

---

## ✅ Success Criteria

Migration is successful when:

- ✅ All verification queries return expected results
- ✅ No errors in application logs
- ✅ All critical pages load correctly
- ✅ Modals display without scroll issues
- ✅ No user-reported errors for 24 hours
- ✅ Database performance is normal

---

## 🎯 Final Notes

1. **This migration is tested in development** but proceed with caution in production
2. **Backup is mandatory** - do not skip this step
3. **Test thoroughly** before declaring success
4. **Keep backup files** for at least 30 days
5. **Monitor closely** for the first 24-48 hours after migration

**Migration Complexity:** Medium  
**Risk Level:** Low-Medium (with proper backups)  
**Estimated Downtime:** 5-15 minutes  
**Reversible:** Yes (with backups)  

---

**Document Version:** 1.0  
**Last Updated:** February 14, 2026  
**Reviewed By:** GitHub Copilot  
