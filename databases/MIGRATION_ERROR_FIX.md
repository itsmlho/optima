# MIGRATION ERROR FIX - PERMISSION ISSUES

## Problem
When running the original `PRODUCTION_MIGRATION_2026-02-19.sql`, you may encounter:
```
#1044 - Access denied for user 'u138256737_root_optima'@'127.0.0.1' to database 'information_schema'
```

This happens because:
1. Prepared statements with dynamic SQL have restricted permissions
2. MySQL user doesn't have full access to `INFORMATION_SCHEMA` queries inside prepared statements
3. Older MySQL versions may not support `IF NOT EXISTS` in `ALTER TABLE ADD COLUMN`

## Solution
Use the **SIMPLE migration version** instead, which:
- ✅ No prepared statements
- ✅ No dynamic SQL
- ✅ Compatible with MySQL 5.7+ / MariaDB 10.2+
- ✅ Works with restricted database permissions
- ✅ Errors gracefully if columns already exist

## Files to Use

### ✅ RECOMMENDED: PRODUCTION_MIGRATION_2026-02-19_SIMPLE.sql
**Use this file for production deployment**
- Simple SQL statements
- No permission issues
- Better compatibility
- Expected errors are documented (duplicate columns/indexes)

### ⚠️ ALTERNATIVE: PRODUCTION_MIGRATION_2026-02-19.sql
**Only if you have MySQL 8.0.29+ and full permissions**
- Uses `IF NOT EXISTS` syntax
- Requires newer MySQL version
- May have permission issues

## Deployment Commands

### Option 1: Via phpMyAdmin
1. Login to phpMyAdmin
2. Select your database
3. Click "Import" tab
4. Upload: `PRODUCTION_MIGRATION_2026-02-19_SIMPLE.sql`
5. Click "Go"
6. **Ignore any errors** about "Duplicate column name" or "Duplicate key name"
7. Check the bottom results for verification queries

### Option 2: Via SSH Command Line
```bash
# Connect to production
ssh -p 65002 u138256737@147.93.80.45

# Navigate to optima
cd optima

# Run migration (SIMPLE version)
mysql -u [username] -p [database] < databases/migrations/PRODUCTION_MIGRATION_2026-02-19_SIMPLE.sql

# If prompted for password, enter it
# Expected output: Some errors are OK (documented in the file)
```

### Option 3: Via Automated Script
The deployment script will automatically choose the SIMPLE version if available:
```bash
bash databases/deploy_production.sh
```

## Expected Errors (THESE ARE OK)

When re-running the migration, you may see:
```
ERROR 1060 (42S21): Duplicate column name 'invoice_generated'
ERROR 1060 (42S21): Duplicate column name 'invoice_generated_at'
ERROR 1061 (42000): Duplicate key name 'idx_invoice_automation'
ERROR 1060 (42S21): Duplicate column name 'customer_converted_at'
```

**This means:**
- ✅ Migration was already applied previously
- ✅ Columns and indexes already exist
- ✅ Your database is up to date
- ✅ No action needed

**Check the verification queries at the end of the output to confirm everything is OK.**

## Verification After Migration

Run these queries in phpMyAdmin or MySQL CLI:

```sql
-- Should return 2 rows (invoice_generated, invoice_generated_at)
SELECT COLUMN_NAME, COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'delivery_instructions' 
  AND COLUMN_NAME IN ('invoice_generated', 'invoice_generated_at');

-- Should return 4 rows (4 columns in index)
SELECT INDEX_NAME, COLUMN_NAME 
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'delivery_instructions' 
  AND INDEX_NAME = 'idx_invoice_automation';

-- Should return 1 row (customer_converted_at)
SELECT COLUMN_NAME, COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotations' 
  AND COLUMN_NAME = 'customer_converted_at';
```

## Success Indicators

✅ **Migration Successful if you see:**
- 2 columns in `delivery_instructions`: `invoice_generated`, `invoice_generated_at`
- 1 index: `idx_invoice_automation` with 4 columns
- 1 column in `quotations`: `customer_converted_at`
- Eligible DIs count (e.g., "8 DIs eligible for invoice generation")
- Sample DI data with customer names and contract numbers

## Rollback (If Needed)

If you need to rollback:
```bash
mysql -u [username] -p [database] < databases/migrations/PRODUCTION_ROLLBACK_2026-02-19.sql
```

The rollback script is also simple and has no permission issues.

## Summary

| Issue | Solution |
|-------|----------|
| Permission denied on INFORMATION_SCHEMA | Use SIMPLE version |
| Prepared statement error | Use SIMPLE version |
| Duplicate column errors | Expected, ignore them |
| MySQL version too old | Use SIMPLE version |
| Want idempotent migration | Both versions are idempotent |

**Bottom line: Use `PRODUCTION_MIGRATION_2026-02-19_SIMPLE.sql` for hassle-free deployment.**
