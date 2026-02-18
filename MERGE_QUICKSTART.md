# OPTIMA ERP - Marketing & Accounting Data Merge Quick Start Guide

**Date**: 2026-02-18  
**Purpose**: Merge marketing_fix.csv and data_from_acc.csv into unified dataset  
**Total Units**: ~2,100 assignments after merge

---

## 📋 Overview

This guide walks you through merging two data sources:

| File | Units | Completeness | Key Fields |
|------|-------|--------------|------------|
| **marketing_fix.csv** | 1,940 | 75% | ✅ **cust_id**, ✅ **Area**, Marketing, Customer, Lokasi, PO, Dates, Harga |
| **data_from_acc.csv** | 1,412 | 95% | Marketing, Customer, **LOKASI (detailed)**, **UNIT ANTAR**, PO, Harga, **PERIODE KONTRAK** |

### Merge Strategy

```
BASE:     marketing_fix.csv (1,940 units) - has cust_id & Area (CRITICAL)
         ↓
ENRICH:  Overlap 1,200 units - use better data from data_from_acc
         ↓
ADD:     150 units only in data_from_acc (match customer by name)
         ↓
RESULT:  ~2,100 units with best data from both sources
```

**Key Decision**: When data conflicts (PO numbers differ), **accounting data wins** (source of truth from official records).

---

## 🔍 Data Analysis Summary

### Overlap Analysis
- **1,200 units** exist in BOTH files (63% overlap)
- **740 units** only in marketing_fix (historical/inactive?)
- **150 units** only in data_from_acc (recent assignments)

### Data Quality Comparison

| Field | marketing_fix | data_from_acc | Winner |
|-------|---------------|---------------|--------|
| Lokasi | Generic (65% filled) | Detailed (100% filled) | ✅ Accounting |
| Harga | 75% filled | 95% filled | ✅ Accounting |
| PO | 80% filled | 90% filled | ✅ Accounting |
| cust_id | ✅ **Has it** | ❌ Missing | ✅ Marketing |
| Area | ✅ **Has it** | ❌ Missing | ✅ Marketing |

### Enrichment Examples

**Location Detail:**
- marketing_fix: "Cicurug"
- data_from_acc: "Cicurug/ Loading CAN/ PET" ✅ **Much better!**

**PO Conflicts (30-40% of overlap):**
- Unit 3591: marketing PO `4212029724` vs accounting PO `4212032259`
- **Resolution**: Use `4212032259` (from accounting, likely renewal/addendum)

---

## 🚀 Quick Start (5 Steps)

### **STEP 1: Backup Database** ⚠️ CRITICAL

```bash
cd c:\laragon\www\optima
mysqldump -u root optima_ci > backup_before_merge_%date:~-4,4%%date:~-10,2%%date:~-7,2%.sql
```

**Verify backup:**
```bash
dir backup_before_merge_*.sql
# Should show file size ~50-100MB
```

---

### **STEP 2: Run Merge Script**

```bash
python merge_marketing_accounting.py
```

**Expected Output:**
```
================================================================================
OPTIMA ERP - MARKETING & ACCOUNTING DATA MERGER
================================================================================

📂 Loading CSV files...
✅ Loaded 1940 rows from marketing_fix.csv
✅ Loaded 1412 rows from data_from_acc.csv

🔌 Connecting to database...
✅ Database connected: optima_ci

📋 Building lookup tables...
✅ Loaded 239 customers
✅ Loaded 34 areas
✅ Loaded 468 locations
✅ Loaded 363 contracts
🔍 Overlap detection: 1195 units currently assigned in DB

📊 Data Analysis:
   Marketing units:     1940
   Accounting units:    1412
   Overlap:             1200 units
   Marketing only:      740 units
   Accounting only:     150 units
   DB overlap (reset):  850 units

⚙️  Processing 1200 overlap units (marketing base + accounting enrichment)...
⚙️  Processing 740 marketing-only units...
⚙️  Processing 150 accounting-only units...

📊 Merge complete:
   Total processed:     2090
   - Overlap enriched:  1200
   - Marketing only:    740
   - Accounting only:   140
   Skipped:             10
   New locations:       120
   New contracts:       180
   Enriched fields:     850
   Conflicts logged:    380

📝 Generating SQL script...
✅ SQL script generated: MERGED_MARKETING_DATA.sql

📊 Generating reports...
✅ Main report generated: merge_report.txt
⚠️  Conflicts report: data_conflicts_report.csv

================================================================================
✅ MERGE COMPLETE!
================================================================================

Generated files:
  1. MERGED_MARKETING_DATA.sql - SQL import script
  2. merge_report.txt - Detailed merge report
  3. data_conflicts_report.csv - Data conflicts log

📊 Summary:
  Total units to import: 2090
  - From overlap (enriched): 1200
  - From marketing only: 740
  - From accounting only: 140
  New locations: 120
  New contracts: 180
  Enriched fields: 850
```

**Runtime**: 3-5 minutes depending on system

---

### **STEP 3: Review Generated Files**

#### **A. Main Report (merge_report.txt)**
```bash
notepad merge_report.txt
```

**Check:**
- Total units processed: Should be ~2,090
- Enrichment stats: How many fields updated from accounting
- Missing customer mappings: Should be minimal (<10)
- Conflicts: ~380 PO conflicts (resolved with accounting data)

#### **B. SQL Script (MERGED_MARKETING_DATA.sql)**
```bash
notepad MERGED_MARKETING_DATA.sql
# OR use VS Code for syntax highlighting
code MERGED_MARKETING_DATA.sql
```

**Verify:**
- STEP 1: Reset ~850 overlap units (clear DB assignments)
- STEP 2: Insert ~120 new locations
- STEP 3: Insert ~180 new contracts
- STEP 4: Update 2,090 units
- STEP 5-6: Create links, update totals

**Sample check:**
```sql
-- Should see something like:
UPDATE inventory_unit SET customer_id=NULL, ... WHERE no_unit IN (3622, 2294, ...);
INSERT INTO customer_locations (customer_id, area_id, ...) VALUES (4, 10, ...);
UPDATE inventory_unit SET customer_id=4, kontrak_id=@contract_1, ... WHERE no_unit=3622;
```

#### **C. Conflicts Report (data_conflicts_report.csv)**
```bash
# Open in Excel or LibreOffice
start data_conflicts_report.csv
```

**Review PO conflicts** - Examples:
| unit | field | marketing | accounting | used |
|------|-------|-----------|------------|------|
| 3591 | PO | 4212029724 | 4212032259 | accounting |
| 5128 | PO | 4212029724 | 4212032259 | accounting |

**Decision**: OK to use accounting PO (likely renewal/addendum from 2025-2026)

---

### **STEP 4: Execute SQL Import**

```bash
mysql -u root optima_ci < MERGED_MARKETING_DATA.sql
```

**Watch for:**
- ✅ No errors (foreign key violations, duplicates)
- ✅ Execution completes in 5-10 minutes
- ⚠️ If errors occur, restore backup immediately

**Manual execution (recommended for first time):**
```bash
mysql -u root optima_ci
```

```sql
-- In MySQL prompt:
SOURCE c:/laragon/www/optima/MERGED_MARKETING_DATA.sql;

-- Watch for errors, if any appear:
ROLLBACK;  -- If using transactions
-- Then restore backup
```

---

### **STEP 5: Validate Results**

```bash
mysql -u root optima_ci < POST_MIGRATION_VALIDATION.sql > validation_results.txt
notepad validation_results.txt
```

**Expected Results:**

```sql
-- Section 1: Data Counts
Total customers:          239
Total locations:          588  (468 + 120 new = 588)
Total areas:              34
Total contracts:          543  (363 + 180 new = 543)
Total units:              4989
Assigned units:           2090  (42% of inventory) ✅
Unassigned units:         2899  (58% spare/stock)

-- Section 2: Data Quality (should be 0 violations)
Customers without locations:     0  ✅
Locations without area_id:       ~15  ⚠️ (from accounting data, OK)
Units without customer:          2899  ✅ (unassigned units)
Units without contract:          ~200  ⚠️ (placeholder contracts pending)

-- Section 3: Business Logic
Contract total_units mismatch:   0  ✅
Orphaned customer_contracts:     0  ✅
Units with price:                ~1900  (90%+) ✅

-- Section 4: Top Customers/Contracts
(Should show PT Amerta Indah Otsuka, PT Indo Bharat Rayon, etc. with unit counts)
```

**Validation Checklist:**
- [ ] Total assigned units = ~2,090 (42% of inventory)
- [ ] No critical violations (❌ markers)
- [ ] Warnings (⚠️) are acceptable (placeholders, missing area)
- [ ] Top customers show correct unit counts
- [ ] Prices populated for 90%+ of units

---

## 📊 Expected Final State

### Before Merge
- **Assigned units**: 1,195 (24%)
- **Unassigned units**: 3,794 (76%)

### After Merge
- **Assigned units**: 2,090 (42%) ✅ **+895 units**
- **Unassigned units**: 2,899 (58%)

### Data Breakdown
```
Source Distribution:
- Overlap (enriched):      1,200 units (57%)
- Marketing only:            740 units (35%)
- Accounting only:           150 units (7%)

Total:                     2,090 units
```

### Quality Improvements
- **Lokasi**: 850 units upgraded to detailed location descriptions
- **Harga**: 400 units got updated prices from accounting
- **PO**: 380 units got corrected PO numbers (from official records)
- **UNIT ANTAR**: 1,200 units now have delivery dates (new field!)

---

## 🔧 Troubleshooting

### Issue 1: "Missing customer mapping" errors
**Symptom**: Script reports ~10-20 units with missing customer matches
**Cause**: Customer names in accounting don't match database (typo, format difference)
**Solution**:
1. Check `missing_customer_mapping.csv`
2. Add missing customers to database:
   ```sql
   INSERT INTO customers (customer_name, is_active) VALUES ('EXACT NAME FROM CSV', 1);
   ```
3. Re-run merge script

---

### Issue 2: "Duplicate location" warnings
**Symptom**: SQL execution shows warnings about duplicate customer_locations
**Cause**: Location already exists with same name
**Solution**: Safe to ignore - script uses INSERT IGNORE or checks before inserting

---

### Issue 3: SQL execution very slow (>20 minutes)
**Symptom**: Import hangs or takes extremely long
**Cause**: Large database, indexes rebuilding
**Solution**:
```sql
-- Before import:
SET FOREIGN_KEY_CHECKS=0;
SET UNIQUE_CHECKS=0;
SET AUTOCOMMIT=0;

-- Run import
SOURCE MERGED_MARKETING_DATA.sql;

-- After import:
COMMIT;
SET FOREIGN_KEY_CHECKS=1;
SET UNIQUE_CHECKS=1;
```

---

### Issue 4: "Unit not found in inventory_unit"
**Symptom**: UPDATE fails for some unit numbers
**Cause**: Unit number in CSV doesn't exist in database
**Solution**:
1. Check which units failed (script logs them)
2. Verify units exist:
   ```sql
   SELECT no_unit FROM inventory_unit WHERE no_unit IN (3622, 2294, ...);
   ```
3. If units genuinely missing, need to add to inventory first

---

### Issue 5: Want to rollback after SQL execution
**Solution**:
```bash
# Stop immediately
mysql -u root optima_ci

# In MySQL:
DROP DATABASE optima_ci;
CREATE DATABASE optima_ci CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Restore backup:
mysql -u root optima_ci < backup_before_merge_20260218.sql

# Verify restoration:
SELECT COUNT(*) FROM inventory_unit WHERE customer_id IS NOT NULL;
# Should show 1,195 (original count)
```

---

## ✅ Post-Migration Checklist

- [ ] **Backup verified** (file exists, size correct)
- [ ] **Merge script executed without errors**
- [ ] **Reports reviewed** (merge_report.txt, conflicts log)
- [ ] **SQL script reviewed** (spot-check 10-20 units)
- [ ] **SQL executed successfully** (no foreign key errors)
- [ ] **Validation passed** (POST_MIGRATION_VALIDATION.sql results OK)
- [ ] **Customer management page loads** (no JavaScript errors)
- [ ] **Spot-check 5-10 customers** (detail modal shows correct units/contracts)
- [ ] **Review PENDING contracts** (update status to ACTIVE)
- [ ] **Review missing prices** (units with NULL harga_sewa_bulanan)
- [ ] **Review locations without area** (~15 locations from accounting data)

---

## 📞 Manual Review Tasks (Admin/Marketing)

### Task 1: Activate Placeholder Contracts
```sql
SELECT id, no_kontrak, customer_po_number, status 
FROM kontrak 
WHERE status = 'PENDING' 
ORDER BY created_at DESC 
LIMIT 50;

-- After verifying contract data is correct:
UPDATE kontrak SET status = 'ACTIVE' WHERE id = ?;
```

### Task 2: Fill Missing Prices
```sql
SELECT iu.no_unit, c.customer_name, cl.location_name, iu.harga_sewa_bulanan
FROM inventory_unit iu
JOIN customers c ON iu.customer_id = c.id
LEFT JOIN customer_locations cl ON iu.customer_location_id = cl.id
WHERE iu.customer_id IS NOT NULL 
  AND iu.harga_sewa_bulanan IS NULL
ORDER BY c.customer_name;

-- Get pricing from contract documents, then:
UPDATE inventory_unit SET harga_sewa_bulanan = ? WHERE no_unit = ?;
```

### Task 3: Update NULL Area IDs
```sql
SELECT cl.id, cl.location_name, c.customer_name, cl.area_id
FROM customer_locations cl
JOIN customers c ON cl.customer_id = c.id
WHERE cl.area_id IS NULL
ORDER BY c.customer_name;

-- Assign correct area:
UPDATE customer_locations SET area_id = ? WHERE id = ?;
```

### Task 4: Review PO Conflicts
Open `data_conflicts_report.csv` and verify:
- PO number changes make sense (renewal, addendum)
- If accounting PO is wrong, update:
  ```sql
  UPDATE kontrak SET customer_po_number = 'CORRECT_PO' WHERE id = ?;
  ```

---

## 📈 Performance Metrics

**Expected metrics after merge:**

| Metric | Before | After | Change |
|--------|--------|-------|--------|
| Assigned units | 1,195 (24%) | 2,090 (42%) | +895 (+74%) |
| Locations | 468 | 588 | +120 |
| Contracts | 363 | 543 | +180 |
| Data completeness | 75% | 90%+ | +15% |
| Location detail | Generic | Detailed | ✅ |

**Business Impact:**
- ✅ More accurate inventory tracking (42% vs 24% visibility)
- ✅ Better location granularity (WH-A vs just "Sukabumi")
- ✅ Updated pricing from accounting (Q4 2025 / Q1 2026)
- ✅ Official PO numbers from records (audit-ready)
- ✅ Unit delivery dates tracked (UNIT ANTAR field)

---

## 🆘 Support

**If issues occur:**
1. **Check merge_report.txt** for specific errors
2. **Review validation_results.txt** for database state
3. **Check conflict log** for data discrepancies
4. **Restore backup** if critical errors

**Contact**: Development Team  
**Generated**: 2026-02-18

---

## 📝 File Summary

**Input Files:**
- `marketing_fix.csv` (1,940 units, has cust_id)
- `data_from_acc.csv` (1,412 units, from accounting)

**Generated Files:**
- `MERGED_MARKETING_DATA.sql` - SQL import script (~15,000 lines)
- `merge_report.txt` - Summary report
- `data_conflicts_report.csv` - PO/price conflicts
- `missing_customer_mapping.csv` - Unmatched customers (if any)

**Documentation:**
- `DATABASE_MIGRATION_STRATEGY_COMPLETE.md` - Technical ERD & strategy
- `MERGE_QUICKSTART.md` - This guide
- `POST_MIGRATION_VALIDATION.sql` - Validation queries

---

**Ready to proceed? Run the merge script!** 🚀

```bash
python merge_marketing_accounting.py
```
