# OPTIMA ERP - Marketing Data Migration Quick Start Guide

Generated: 2026-02-18

## 📂 Files Generated

1. **DATABASE_MIGRATION_STRATEGY_COMPLETE.md** - Full documentation with ERD and pricing strategy
2. **migration_cleaning_script.py** - Python script to process CSV and generate SQL
3. **POST_MIGRATION_VALIDATION.sql** - Validation queries to run after import

---

## 🚀 Quick Start (Step by Step)

### Prerequisites

- Python 3.8+ with pandas and mysql-connector-python installed
- MySQL client access to optima_ci database
- CSV files: `marketing_fix.csv`, `customer2.csv`, `customer_locations2.csv`

### Step 1: Install Python Dependencies

```bash
pip install pandas mysql-connector-python
```

### Step 2: Backup Database (CRITICAL!)

```bash
mysqldump -u root optima_ci > backup_before_migration_$(date +%Y%m%d_%H%M%S).sql
```

### Step 3: Run Python Cleaning Script

```bash
python migration_cleaning_script.py
```

**What it does:**
- Reads marketing_fix.csv
- Normalizes customer names and data
- Handles missing locations (creates defaults)
- Handles missing contracts (creates placeholders with "PENDING" status)
- Generates SQL import script: `INSERT_MARKETING_DATA.sql`
- Creates reports:
  * `migration_report.txt` - Summary and flagged issues
  * `missing_data_report.csv` - Customers/units not found in database

**Expected output:**
```
✅ Loaded 1940 rows from CSV
✅ Database connected
✅ Loaded lookups:
   - Customers: 239
   - Locations: 468
   - Areas: 34
   - Contracts: 363
   - Units in inventory: 4989

⚙️ Processing marketing data...
📊 Processing complete:
   - Processed: 1850 rows
   - Skipped: 90 rows
   - New locations: 120
   - New contracts: 180
   - Units to update: 1850

✅ MIGRATION SCRIPT COMPLETED
```

### Step 4: Review Generated Files

#### 4.1 Check missing_data_report.csv
Open `missing_data_report.csv` to see any flagged issues:
- **MISSING_CUSTOMER**: Customer names in CSV not found in database
  - Action: Add customer to database first, or fix spelling
- **MISSING_UNIT**: Unit numbers in CSV not found in inventory_unit table
  - Action: Verify unit exists or skip these rows

#### 4.2 Review INSERT_MARKETING_DATA.sql
Open `INSERT_MARKETING_DATA.sql` and check:
- Location INSERTs look correct
- Contract INSERTs have valid dates
- Unit UPDATEs reference correct IDs
- Look for any "QUERY_LOCATION" or "QUERY_CONTRACT" placeholders (should be subqueries)

### Step 5: Execute SQL Import

```bash
mysql -u root optima_ci < INSERT_MARKETING_DATA.sql
```

or in MySQL shell:
```sql
source INSERT_MARKETING_DATA.sql;
```

**This will:**
1. Insert new customer_locations
2. Insert new contracts (with status "PENDING")
3. Update inventory_unit assignments (customer_id, kontrak_id, harga_sewa_bulanan)
4. Create customer_contracts links
5. Update contract total_units

### Step 6: Run Validation Queries

```bash
mysql -u root optima_ci < POST_MIGRATION_VALIDATION.sql > validation_results.txt
```

Review `validation_results.txt` for:
- ❌ **Critical violations** (must fix immediately):
  * Customers without locations
  * Orphaned foreign keys
  * Duplicate entries
  
- ⚠️ **Warnings** (review and decide):
  * Contracts with 0 units
  * Units without prices
  * Contract totals not matching actual unit counts

### Step 7: Fix Issues (If Any)

Based on validation results:

**Fix missing customer_contracts:**
```sql
INSERT INTO customer_contracts (customer_id, kontrak_id, is_active)
SELECT DISTINCT iu.customer_id, iu.kontrak_id, 1
FROM inventory_unit iu
WHERE iu.customer_id IS NOT NULL 
  AND iu.kontrak_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM customer_contracts cc
      WHERE cc.customer_id = iu.customer_id 
        AND cc.kontrak_id = iu.kontrak_id
  );
```

**Update contract totals:**
```sql
UPDATE kontrak k
SET total_units = (
    SELECT COUNT(*) 
    FROM inventory_unit iu 
    WHERE iu.kontrak_id = k.id
);
```

### Step 8: Manual Review in Admin Panel

1. Login to OPTIMA ERP
2. Go to Customer Management page
3. Verify customers show correct:
   - Number of locations
   - Number of contracts
   - Number of units
4. Check contracts with status "PENDING":
   - Review placeholder contracts (those with "BELUM ADA PO")
   - Update PO number if needed
   - Change status to "ACTIVE" when ready
5. Review units without prices:
   - Set harga_sewa_bulanan manually
   - Or leave NULL for marketing to fill later

---

## 📊 Expected Results

**Before Migration:**
- Customers: 239
- Locations: 468
- Contracts: 363
- Assigned Units: 1,195 (24%)

**After Migration (actual analysis):**
- Customers: 239
- Locations: 588 (+120 new, estimated)
- Contracts: 543 (+180 new, estimated)
- **Assigned Units: 2,187 (44%)**
  - 947 units UPDATED (overlap dengan existing)
  - 992 units NEW assignments
  - 248 units UNCHANGED (existing, not in CSV)

**Breakdown:**
- CSV contains: 1,939 units
- Overlap with existing: 947 units (will be UPDATED)
- New units from CSV: 992 units (new assignments)
- Existing keep: 248 units (not in CSV, unchanged)

**Remaining unassigned:** 2,802 units (56% - SPARE/STOCK/MAINTENANCE)

---

## 🔧 Troubleshooting

### Issue: "Missing customers" in report

**Cause:** Customer names in CSV don't match database
**Solution:** 
1. Check spelling: "PT ABC" vs "PT. ABC" vs "PT  ABC" (extra space)
2. Add missing customers to database first
3. Update CSV to match exact database names
4. Re-run script

### Issue: "Missing units" in report

**Cause:** Unit numbers in CSV don't exist in inventory_unit table
**Solution:**
1. Verify unit exists: `SELECT * FROM inventory_unit WHERE no_unit = 3622`
2. If unit doesn't exist, it needs to be added to inventory first
3. Or remove these rows from CSV if units don't exist

### Issue: Query too slow during import

**Cause:** Large dataset, many subqueries
**Solution:**
1. Break down INSERT_MARKETING_DATA.sql into smaller batches
2. Run locations INSERTs first
3. Then contracts INSERTs
4. Finally unit UPDATEs in batches of 500

### Issue: Duplicate key errors

**Cause:** Location or contract already exists
**Solution:**
1. Script checks for duplicates, but edge cases may occur
2. Add `INSERT IGNORE` or `ON DUPLICATE KEY UPDATE` to SQL
3. Or manually resolve conflicts

---

## 📖 Advanced Topics

### Pricing Strategy Explained

Read [DATABASE_MIGRATION_STRATEGY_COMPLETE.md](DATABASE_MIGRATION_STRATEGY_COMPLETE.md) for:
- ERD diagram
- 3-Tier pricing model recommendation
- Why `harga_sewa_bulanan` is in `inventory_unit` (per-unit pricing)
- Why `nilai_total` is in `kontrak` (contract total value)
- Future: Create `contract_unit_rates` table for rate templates

### Custom Data Rules

If you need different handling for:
- Missing locations → Edit `process_marketing_data()` function
- Price parsing → Edit `parse_price()` function
- Date formats → Edit `parse_date()` function
- Contract status → Change `'PENDING'` to `'ACTIVE'` in script

---

## 🆘 Need Help?

**Error in Python script:**
- Check Python version: `python --version` (need 3.8+)
- Check dependencies: `pip list | grep -E "pandas|mysql"`
- Check CSV encoding: Should be UTF-8

**SQL import errors:**
- Check database connection
- Check foreign key constraints
- Review validation script output

**Data looks wrong in UI:**
- Clear browser cache
- Run validation script
- Check customer_contracts table links

---

## ✅ Post-Migration Checklist

- [ ] Backup created and verified
- [ ] Python script ran successfully
- [ ] migration_report.txt reviewed
- [ ] missing_data_report.csv issues resolved
- [ ] INSERT_MARKETING_DATA.sql executed without errors
- [ ] POST_MIGRATION_VALIDATION.sql shows zero critical violations
- [ ] Customer Management page displays correct data
- [ ] Contracts with "PENDING" status reviewed
- [ ] Units without prices flagged for marketing team
- [ ] All files archived to `_archived/` folder after success

---

**Questions?** Check DATABASE_MIGRATION_STRATEGY_COMPLETE.md or contact development team.

**Generated:** 2026-02-18
