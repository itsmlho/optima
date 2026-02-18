# Phase 1 Migration Execution Guide

## Overview
This guide walks through executing the Phase 1 database migrations for the Operator Management, PO Rotation, and Spot Rental features.

**Date**: 2026-02-15  
**Database**: MySQL 5.7+  
**Estimated Time**: 5-10 minutes  
**Risk Level**: Low (includes rollback capability)

---

## Pre-Migration Checklist

### 1. Database Backup
**CRITICAL: Always backup before running migrations**

```bash
# Windows (via cmd in laragon/bin/mysql folder)
mysqldump -u root -p optima > C:\laragon\www\optima\backups\optima_pre_phase1_backup_$(date +%Y%m%d_%H%M%S).sql

# Alternative: Use phpMyAdmin
# 1. Open http://localhost/phpmyadmin
# 2. Select 'optima' database
# 3. Click 'Export' tab
# 4. Choose 'Quick' export method
# 5. Click 'Go' to download backup
```

### 2. Verify Current Database State
```sql
-- Check existing tables
SHOW TABLES LIKE 'kontrak';
SHOW TABLES LIKE 'invoices';

-- Verify sample contracts exist
SELECT COUNT(*) FROM kontrak;

-- Check for existing PO number field
DESCRIBE kontrak;
```

### 3. Environment Check
- [ ] PHP 7.4+ installed
- [ ] MySQL 5.7+ running
- [ ] CodeIgniter 4 environment configured
- [ ] Database credentials in `.env` correct
- [ ] Write permissions on `writable/` folder

---

## Execution Steps

### Method 1: MySQL Command Line (Recommended)

```bash
# 1. Navigate to MySQL bin directory
cd C:\laragon\bin\mysql\mysql-5.7.33-winx64\bin

# 2. Login to MySQL
mysql -u root -p

# 3. Select database
USE optima;

# 4. Execute master migration script
SOURCE C:/laragon/www/optima/databases/migrations/MASTER_MIGRATION_PHASE1.sql;

# 5. Verify execution
SHOW TABLES LIKE 'operators';
SHOW TABLES LIKE 'contract_po_history';
SELECT COUNT(*) FROM operators;
```

### Method 2: phpMyAdmin GUI

1. Open http://localhost/phpmyadmin
2. Select `optima` database from left sidebar
3. Click `SQL` tab at top
4. Open file: `databases/migrations/MASTER_MIGRATION_PHASE1.sql`
5. Copy entire contents into SQL query box
6. Click `Go` button at bottom
7. Verify success message appears

### Method 3: CodeIgniter Migrate Command

```bash
# From workspace root
cd C:\laragon\www\optima

# Run migrations
php spark migrate

# Check migration status
php spark migrate:status
```

---

## Verification Steps

### 1. Verify New Tables Created
```sql
-- Check operators table
SELECT * FROM operators;
-- Should return 4 sample operators (OP-001 to OP-004)

-- Check contract_operator_assignments table
DESCRIBE contract_operator_assignments;
-- Should show columns: id, contract_id, operator_id, billing_type, etc.

-- Check contract_po_history table
DESCRIBE contract_po_history;
-- Should show columns: id, contract_id, po_number, effective_from, etc.
```

### 2. Verify Table Alterations
```sql
-- Check kontrak table new columns
DESCRIBE kontrak;
-- Should include: rental_mode, fast_track, billing_basis, etc.

-- Check kontrak_spesifikasi new columns
DESCRIBE kontrak_spesifikasi;
-- Should include: include_operator, operator_monthly_rate, etc.

-- Check invoice_items new columns
DESCRIBE invoice_items;
-- Should include: item_type, operator_assignment_id, billing_period_start, etc.

-- Check invoices new columns
DESCRIBE invoices;
-- Should include: po_history_id, po_number_snapshot
```

### 3. Verify Foreign Keys
```sql
-- Show all foreign keys
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_SCHEMA = 'optima'
    AND TABLE_NAME IN ('contract_operator_assignments', 'contract_po_history', 'invoice_items');
```

### 4. Verify Sample Data
```sql
-- Check operator sample data
SELECT 
    operator_code,
    operator_name,
    certification_level,
    monthly_rate,
    status
FROM operators
ORDER BY operator_code;

-- Should return:
-- OP-001 | Budi Santoso | EXPERT | 8000000 | AVAILABLE
-- OP-002 | Ahmad Fauzi | ADVANCED | 7000000 | AVAILABLE
-- OP-003 | Dwi Kurniawan | INTERMEDIATE | 6000000 | AVAILABLE
-- OP-004 | Eko Prasetyo | BASIC | 5000000 | AVAILABLE
```

---

## Post-Migration Tasks

### 1. Clear CodeIgniter Cache
```bash
cd C:\laragon\www\optima
rm -rf writable/cache/*
```

### 2. Update Models (Already Done)
- ✅ OperatorModel.php created
- ✅ OperatorAssignmentModel.php created
- ✅ ContractPOHistoryModel.php created

### 3. Test Model Access
```php
// Test in controller or spark console
$operators = model('OperatorModel')->findAll();
var_dump(count($operators)); // Should be 4

$poHistory = model('ContractPOHistoryModel')->getStatistics();
var_dump($poHistory); // Should show 0 records initially
```

---

## Rollback Procedure

If migration fails or issues detected:

### 1. Restore Database Backup
```bash
# Stop any running queries first
mysql -u root -p optima < C:\laragon\www\optima\backups\optima_pre_phase1_backup_YYYYMMDD_HHMMSS.sql
```

### 2. Manual Rollback (if needed)
```sql
-- Drop new tables
DROP TABLE IF EXISTS contract_operator_assignments;
DROP TABLE IF EXISTS operators;
DROP TABLE IF EXISTS contract_po_history;

-- Revert kontrak table changes
ALTER TABLE kontrak
    DROP COLUMN IF EXISTS rental_mode,
    DROP COLUMN IF EXISTS fast_track,
    DROP COLUMN IF EXISTS billing_basis,
    DROP COLUMN IF EXISTS requires_po_approval,
    DROP COLUMN IF EXISTS po_approval_required_value,
    DROP COLUMN IF EXISTS auto_generate_spk,
    DROP COLUMN IF EXISTS allow_partial_billing;

-- Revert kontrak_spesifikasi changes
ALTER TABLE kontrak_spesifikasi
    DROP COLUMN IF EXISTS include_operator,
    DROP COLUMN IF EXISTS operator_monthly_rate,
    DROP COLUMN IF EXISTS operator_daily_rate,
    DROP COLUMN IF EXISTS operator_hourly_rate,
    DROP COLUMN IF EXISTS operator_description;

-- Revert invoice_items changes
ALTER TABLE invoice_items
    DROP COLUMN IF EXISTS item_type,
    DROP COLUMN IF EXISTS operator_assignment_id,
    DROP COLUMN IF EXISTS billing_period_start,
    DROP COLUMN IF EXISTS billing_period_end,
    DROP COLUMN IF EXISTS prorated_days,
    DROP COLUMN IF EXISTS base_rate,
    DROP COLUMN IF EXISTS proration_factor,
    DROP COLUMN IF EXISTS breakdown_json,
    DROP COLUMN IF EXISTS reference_type;

-- Revert invoices changes
ALTER TABLE invoices
    DROP COLUMN IF EXISTS po_history_id,
    DROP COLUMN IF EXISTS po_number_snapshot,
    DROP COLUMN IF EXISTS po_effective_date;
```

---

## Troubleshooting

### Error: "Table already exists"
**Cause**: Migration was partially executed before  
**Fix**: Check which tables exist, drop them manually, re-run migration
```sql
SHOW TABLES LIKE 'operators';
DROP TABLE IF EXISTS operators;
```

### Error: "Cannot add foreign key constraint"
**Cause**: Referenced table/column doesn't exist  
**Fix**: Verify parent tables exist first
```sql
SHOW TABLES LIKE 'kontrak';
DESCRIBE kontrak;
```

### Error: "Column already exists"
**Cause**: ALTER TABLE ran twice  
**Fix**: Check existing columns before re-running
```sql
DESCRIBE kontrak;
-- If rental_mode exists, skip that ALTER statement
```

### Error: "Duplicate entry for key 'PRIMARY'"
**Cause**: Sample data inserted twice  
**Fix**: Clear operators table and re-insert
```sql
TRUNCATE TABLE operators;
-- Then re-run INSERT statements
```

---

## Migration Files Reference

| File | Purpose | Tables Affected |
|------|---------|----------------|
| `2026-02-15_create_operators_table.sql` | Create operators master data | operators (NEW) |
| `2026-02-15_create_contract_operator_assignments_table.sql` | Track operator assignments | contract_operator_assignments (NEW) |
| `2026-02-15_create_contract_po_history_table.sql` | Track PO rotation | contract_po_history (NEW) |
| `2026-02-15_alter_kontrak_table_rental_modes.sql` | Add rental modes | kontrak (ALTER) |
| `2026-02-15_alter_kontrak_spesifikasi_operator_fields.sql` | Add operator pricing | kontrak_spesifikasi (ALTER) |
| `2026-02-15_alter_invoice_items_operator_tracking.sql` | Track operator billing | invoice_items (ALTER) |
| `2026-02-15_alter_invoices_po_reference.sql` | Link invoices to PO | invoices (ALTER) |
| `MASTER_MIGRATION_PHASE1.sql` | Orchestrate all migrations | ALL |

---

## Expected Changes Summary

### New Tables (3)
1. **operators**: 4 sample records
2. **contract_operator_assignments**: 0 records (empty)
3. **contract_po_history**: 0 records (empty)

### Altered Tables (4)
1. **kontrak**: +7 columns (rental_mode, fast_track, etc.)
2. **kontrak_spesifikasi**: +5 columns (operator pricing)
3. **invoice_items**: +9 columns (item_type, operator_assignment_id, etc.)
4. **invoices**: +3 columns (po_history_id, po_number_snapshot, etc.)

### Total Database Impact
- **New Columns**: 24
- **New Tables**: 3
- **Foreign Keys**: 8
- **Indexes**: 12
- **Sample Data**: 4 operators

---

## Next Steps After Migration

### Phase 2: UI Development
1. Create Operators Management page (`app/Views/marketing/operators.php`)
2. Add "Operators" tab to Contract detail view
3. Create Operator Assignment modal
4. Add PO History card to Contract view
5. Create "Add New PO" modal
6. Update Quotation form for operator inclusion

### Phase 3: Business Logic Updates
1. Update `KontrakModel.php` for rental modes
2. Update `InvoiceModel.php` for operator billing
3. Create `OperatorController.php`
4. Update `MarketingController.php` for spot rentals

### Phase 4: Testing
1. Test operator assignment workflow
2. Test PO rotation (add multiple POs)
3. Test invoice generation with operators
4. Test spot rental fast-track

---

## Support

If you encounter issues:
1. Check `writable/logs/` for CodeIgniter errors
2. Check MySQL error log (usually in `C:\laragon\data\mysql\error.log`)
3. Review this guide's Troubleshooting section
4. Restore from backup if needed

**Contact**: Development Team  
**Documentation**: See `docs/` folder for detailed specifications
