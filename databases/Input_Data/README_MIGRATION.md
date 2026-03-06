# Production Data Migration Guide

## Overview

Migrasi data kontrak dari **accounting source** ([kontrak_acc.csv](kontrak_acc.csv)) ke production database dengan **zero duplikasi** dan **modular architecture**.

**Source Data:**
- 2,008 rows = unit relationships dari accounting
- 348 unique contracts teridentifikasi
- Missing data: 172 rows tanpa tanggal, 110 rows tanpa PO/Kontrak number

**Target Architecture:**
```
Customer (1) ──→ (Many) Kontrak
                    ↓
Kontrak (1) ──→ (1) Customer Location  
   ↓
Kontrak (1) ──→ (Many) Kontrak_Unit ←── (1) Unit
```

**Benefit:**
✅ Modular - Unit bisa attach/detach dengan mudah  
✅ Flexible - Nilai kontrak dinamis dari SUM(unit.harga_sewa)  
✅ Clean - Zero duplikasi kontrak  
✅ Production-ready - Complete validation & reporting  

---

## Quick Start

### Option 1: Automated Execution (Recommended)

```bash
cd databases/Input_Data
php EXECUTE_MIGRATION.php
```

Script ini akan guide anda step-by-step melalui **6 tahapan**:
1. Extract unique contracts
2. Backup existing data  
3. RESET database (truncate)
4. Import contracts
5. Import unit relationships
6. Validate & generate report

Setiap step meminta **konfirmasi** sebelum execute.

### Option 2: Manual Step-by-Step

Jika prefer control penuh, jalankan satu-per-satu:

```bash
# Step 1: Extract unique contracts
php extract_unique_contracts.php

# Step 2: Backup (PENTING!)
php backup_before_reset.php

# Step 3: RESET database (requires typing "RESET" to confirm)
php reset_database.php

# Step 4: Import contracts
php import_kontrak_from_accounting.php

# Step 5: Import unit relationships
php import_kontrak_unit_from_accounting.php

# Step 6: Validate
php validate_post_import.php
```

---

## Scripts Reference

### 1. extract_unique_contracts.php
**Purpose:** Analyze [kontrak_acc.csv](kontrak_acc.csv) dan extract 348 unique contracts

**Logic:**
- Group by: `customer_id + contract_number + dates`
- Calculate: `nilai_total = SUM(harga_sewa)`, `total_units = COUNT(units)`
- Handle: Missing dates → status DRAFT
- Convert: Multiple date formats (DD/MM/YYYY, MM/DD/YY, etc)

**Output:**
- `kontrak_from_accounting.csv` - 348 unique contracts
- `unit_to_contract_mapping.csv` - 2,008 unit relationships
- `skipped_rows_report.txt` - Invalid rows report

**Runtime:** ~2-3 seconds

---

### 2. backup_before_reset.php
**Purpose:** Create safety backup before TRUNCATE

**Output:**
- `backup_kontrak_pre_reset_YYYYMMDD_HHMMSS.sql`
- `backup_kontrak_unit_pre_reset_YYYYMMDD_HHMMSS.sql`

**Usage:**
```sql
-- To restore backup if needed:
SOURCE backup_kontrak_pre_reset_20260305_143022.sql;
```

**Runtime:** ~5-10 seconds (depends on row count)

---

### 3. reset_database.php
**Purpose:** TRUNCATE kontrak & kontrak_unit tables

**⚠️ WARNING:** Requires typing "RESET" to confirm - **DESTRUCTIVE OPERATION**

**Actions:**
1. Disable foreign key checks
2. TRUNCATE kontrak_unit (child first)
3. TRUNCATE kontrak
4. Re-enable foreign key checks
5. Verify count = 0

**Runtime:** ~1 second

---

### 4. import_kontrak_from_accounting.php
**Purpose:** Import 348 unique contracts to `kontrak` table

**Source:** `kontrak_from_accounting.csv` (from Step 1)

**Validations:**
- ✅ customer_id exists in customers table
- ✅ customer_location_id exists in customer_locations table
- ✅ No duplicate contract keys

**Output:**
- 348 rows in `kontrak` table
- `kontrak_id_mapping.json` - Maps contract_key → kontrak.id (for Step 5)

**Runtime:** ~3-5 seconds

---

### 5. import_kontrak_unit_from_accounting.php
**Purpose:** Import ~2,008 unit relationships to `kontrak_unit` table

**Sources:**
- `unit_to_contract_mapping.csv` (from Step 1)
- `kontrak_id_mapping.json` (from Step 4)

**Validations:**
- ✅ unit_id exists in inventory_unit table
- ✅ kontrak_id exists (from mapping)
- ✅ No orphan records

**Logic:**
- Match unit → contract via contract_key
- Inherit dates from kontrak table
- Set status: ACTIVE for valid dates, TEMP_ACTIVE for DRAFT

**Runtime:** ~10-15 seconds

---

### 6. validate_post_import.php
**Purpose:** Comprehensive data integrity validation

**Checks:**
1. ✅ Basic counts (kontrak, kontrak_unit)
2. ✅ Status distribution
3. ✅ No orphan records (kontrak_unit without valid kontrak/unit)
4. ✅ Totals match: `kontrak.nilai_total` = `SUM(kontrak_unit.harga_sewa)`
5. ✅ Unit counts match: `kontrak.total_units` = `COUNT(kontrak_unit)`
6. 📋 DRAFT contracts report (missing data for manual review)
7. 📊 Top customers by unit count
8. 📅 Contract date range analysis

**Output:**
- `post_import_validation_report_YYYYMMDD_HHMMSS.txt`

**Runtime:** ~5 seconds

---

## Expected Results

### After Step 1 (Extract)
```
✓ 348 unique contracts identified
✓ 2,008 unit mappings created
✓ 0-2 rows skipped (invalid customer_location_id)
```

### After Step 4 (Import Contracts)
```
✓ 348 contracts imported
  - ~170 ACTIVE (complete data)
  - ~178 DRAFT (missing dates/PO)
✓ Zero orphan records
```

### After Step 5 (Import Units)
```
✓ ~2,008 unit relationships imported
✓ All units linked to valid contracts
✓ Zero orphan records
```

### After Step 6 (Validation)
```
✓ All kontrak.nilai_total match calculated SUM
✓ All kontrak.total_units match actual COUNT
⚠ ~178 DRAFT contracts need manual review
```

---

## DRAFT Contracts

Contracts with **status = 'DRAFT'** need manual completion:

**Missing Data:**
- ❌ Empty `tanggal_mulai` or `tanggal_berakhir` (172 rows)
- ❌ Empty `no_kontrak` and `customer_po_number` (110 rows)

**How to Fix:**
1. Review `post_import_validation_report_*.txt` section "DRAFT CONTRACTS"
2. Use web helpers to update:
   - http://localhost/optima/databases/Input_Data/helper_dashboard.php
3. Or update directly in database:
   ```sql
   UPDATE kontrak 
   SET tanggal_mulai = '2025-01-01', 
       tanggal_berakhir = '2026-01-01',
       status = 'ACTIVE'
   WHERE id = 123;
   ```

---

## Troubleshooting

### Issue: "Invalid customer_id"
**Cause:** Customer doesn't exist in database  
**Fix:** Add customer first, or update customer_id in CSV

### Issue: "Invalid customer_location_id"  
**Cause:** Location doesn't exist  
**Fix:** 
```sql
-- Check valid locations for customer
SELECT * FROM customer_locations WHERE customer_id = 123;

-- Update CSV with correct location_id
```

### Issue: "Missing unit_id"
**Cause:** Unit tidak ada di inventory_unit table  
**Fix:** Import unit first or remove dari CSV

### Issue: "Totals don't match"
**Cause:** Rounding errors or data inconsistency  
**Fix:** Recalculate dengan:
```sql
UPDATE kontrak k SET nilai_total = (
    SELECT SUM(harga_sewa) FROM kontrak_unit WHERE kontrak_id = k.id
);
```

---

## Rollback Procedure

Jika migration gagal atau ada masalah:

```bash
# 1. Stop at any step
# 2. Restore from backup

mysql -u root optima_ci < backup_kontrak_pre_reset_20260305_143022.sql
mysql -u root optima_ci < backup_kontrak_unit_pre_reset_20260305_143022.sql

# 3. Verify
mysql -u root optima_ci -e "SELECT COUNT(*) FROM kontrak;"
```

---

## Testing Checklist

Before deploying to production:

- [ ] All validation checks pass (Step 6)
- [ ] Zero orphan records
- [ ] Kontrak totals match calculated values
- [ ] Web interface loads correctly: http://localhost/optima/public/marketing/kontrak
- [ ] Can view customer contracts
- [ ] Can see unit assignments per contract
- [ ] Unit attach/detach workflow works
- [ ] DRAFT contracts documented for manual completion
- [ ] Backup files safely stored

---

## Production Deployment

After successful testing:

1. **Create production backup:**
   ```bash
   ssh user@production
   mysqldump optima_ci kontrak kontrak_unit > prod_backup_$(date +%Y%m%d).sql
   ```

2. **Upload scripts:**
   ```bash
   scp databases/Input_Data/*.php user@production:/path/to/optima/databases/Input_Data/
   scp kontrak_acc.csv user@production:/path/to/optima/databases/Input_Data/
   ```

3. **Execute migration on production:**
   ```bash
   ssh user@production
   cd /path/to/optima/databases/Input_Data
   php EXECUTE_MIGRATION.php
   ```

4. **Verify:**
   - Check validation report
   - Test application
   - Monitor for issues

5. **Complete DRAFT contracts** via web interface or SQL

---

## Files Generated

| File | Purpose | When |
|------|---------|------|
| `kontrak_from_accounting.csv` | 348 unique contracts | Step 1 |
| `unit_to_contract_mapping.csv` | 2,008 unit relationships | Step 1 |
| `skipped_rows_report.txt` | Invalid rows | Step 1 |
| `backup_kontrak_pre_reset_*.sql` | Safety backup | Step 2 |
| `backup_kontrak_unit_pre_reset_*.sql` | Safety backup | Step 2 |
| `kontrak_id_mapping.json` | contract_key → kontrak.id | Step 4 |
| `post_import_validation_report_*.txt` | Final validation | Step 6 |

---

## Support

**Issues?** Check:
1. Validation report for detailed errors
2. Skipped rows report for data quality issues
3. Database error logs
4. PHP error logs

**Need help?** Review this documentation and script comments.

---

## Architecture Notes

**Why this approach?**

1. **Modular:** Contracts sebagai "payung", units bisa attach/detach
2. **Flexible:** Nilai kontrak calculated, bukan hardcoded
3. **Clean:** Deduplication logic ensures zero duplikasi
4. **Auditable:** Complete logs, reports, backups
5. **Safe:** Multi-step with confirmations, full rollback capability

**Schema Design:**
```sql
kontrak {
  id (PK)
  customer_id (FK → customers.id)
  customer_location_id (FK → customer_locations.id)
  no_kontrak
  nilai_total -- SUM dari kontrak_unit.harga_sewa
  total_units -- COUNT dari kontrak_unit
  status -- ACTIVE or DRAFT
}

kontrak_unit {
  id (PK)
  kontrak_id (FK → kontrak.id)
  unit_id (FK → inventory_unit.id_inventory_unit)
  harga_sewa -- Individual unit rental price
  status -- ACTIVE or TEMP_ACTIVE
}
```

---

**Last Updated:** 2026-03-05  
**Version:** 1.0  
**Tested On:** MySQL 8.4.3, PHP 8.x
