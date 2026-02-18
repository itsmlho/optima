# PRODUCTION MIGRATION - REMOTE SERVER COMPLETE GUIDE
**Date:** February 18, 2026  
**Target:** 147.93.80.45 (Remote Production)  
**Database:** u138256737_optima_db

---

## 🎯 QUICK START

**You Have 2 Options:**

### ⚡ **OPTION A: PHPMyAdmin (Easiest - No SSH Required)**
1. Open: https://auth-db1866.hstgr.io/index.php?db=u138256737_optima_db
2. Login with your credentials
3. Follow **PHPMyAdmin Method** below

### 💻 **OPTION B: SSH Terminal (Faster for Large Files)**
```bash
ssh -p 65002 u138256737@147.93.80.45
```
Then follow **SSH Method** below

---

## 📤 FILES TO PREPARE

Pastikan 3 files ini ready di folder `c:\laragon\www\optima\`:

1. ✅ **fix_customer_names.sql** (37 KB)
   - Fixes 6 customer names
   - Updates: Indah Kiat, Purinusa, Kaldu Sari, Indofood, Galvanis, HISYS

2. ✅ **MERGED_MARKETING_DATA.sql** (2.1 MB) 
   - Imports 2,178 units with assignments
   - Creates 207 locations, 532 contracts
   
3. ✅ **CLEANUP_DUPLICATES.sql** (8 KB)
   - Removes duplicate locations & contracts
   - Ensures 100% clean database

---

## 🔒 STEP 1: BACKUP PRODUCTION

### Via PHPMyAdmin:
1. Go to https://auth-db1866.hstgr.io/index.php?db=u138256737_optima_db
2. Click **"Export"** tab at top
3. Method: **Custom**
4. Select tables:
   - ☑ `customers`
   - ☑ `customer_locations`  
   - ☑ `customer_contracts`
   - ☑ `kontrak`
   - ☑ `inventory_unit`
5. Format: **SQL**
6. Click **"Go"**
7. Save file: `backup_production_20260218.sql`
8. **KEEP THIS FILE SAFE!**

### Via SSH:
```bash
ssh -p 65002 u138256737@147.93.80.45

# After login:
mysqldump -u u138256737 -p u138256737_optima_db \
  customers customer_locations customer_contracts kontrak inventory_unit \
  > backup_production_20260218.sql

# Verify backup:
ls -lh backup_production_20260218.sql
# Should show ~2-3 MB
```

---

## 🚀 STEP 2: EXECUTE MIGRATION

### 📱 **METHOD 1: PHPMyAdmin (Easiest)**

**Step 2A: Fix Customer Names**
1. Open local file: `c:\laragon\www\optima\fix_customer_names.sql`
2. Copy ALL content (Ctrl+A, Ctrl+C)
3. In PHPMyAdmin → Click **"SQL"** tab
4. Paste content
5. Click **"Go"**
6. ✅ Should see: "6 rows affected" or similar success message

**Verify:**
```sql
SELECT id, customer_name 
FROM customers 
WHERE id IN (45, 49, 55, 81, 240, 241) 
ORDER BY id;
```
Expected:
- 45: PT Indah Kiat Pulp & Paper ✅ (no "Tbk")
- 49: PT Indofood CBP Sukses ✅ (no "Makmur")
- 55: PT KALDU SARI NABATI ✅ (no "Indonesia")
- 81: PURINUSA EKA PERSADA ✅
- 240: PT Galvanis Tri Lestari ✅ (new)
- 241: HISYS ENGINEERING INDONESIA ✅ (new)

---

**Step 2B: Import Merged Data**

⚠️ **IMPORTANT:** File is 2.1 MB - PHPMyAdmin may timeout!

**If upload limit error, use SSH method instead!**

Otherwise:
1. PHPMyAdmin → **"SQL"** tab
2. Click **"Choose File"** or paste content
3. **Increase timeout:** Look for "Max execution time" - set to **600 seconds**
4. Open `MERGED_MARKETING_DATA.sql` in Notepad++
5. Copy content (may take 10-20 seconds to copy - file is large)
6. Paste in SQL tab
7. Click **"Go"**
8. ⏱️ Wait 2-5 minutes...
9. ✅ Success message should appear

**Monitor Progress:**
Open new tab, run this query every 30 seconds:
```sql
SELECT COUNT(*) as assigned 
FROM inventory_unit 
WHERE customer_id IS NOT NULL;
```
Watch number increase: 1195 → 1500 → 2000 → **2265**

---

**Step 2C: Cleanup Duplicates**
1. PHPMyAdmin → **"SQL"** tab  
2. Open `CLEANUP_DUPLICATES.sql`
3. Copy content
4. Paste & Click **"Go"**
5. Review output - should show:
   - Deleted 40 duplicate locations ✅
   - Deleted 6 duplicate contracts ✅
   - Final verification: 0 duplicates ✅

---

### 💻 **METHOD 2: SSH (Faster, Recommended for Large Files)**

```bash
# Connect to server
ssh -p 65002 u138256737@147.93.80.45
# Enter password

# Check current directory
pwd

# Upload files from local (run this in LOCAL PowerShell, not SSH):
# Open NEW PowerShell window on your Windows:
```

**In LOCAL PowerShell:**
```powershell
cd c:\laragon\www\optima

# Upload 3 files
scp -P 65002 fix_customer_names.sql u138256737@147.93.80.45:~/
scp -P 65002 MERGED_MARKETING_DATA.sql u138256737@147.93.80.45:~/
scp -P 65002 CLEANUP_DUPLICATES.sql u138256737@147.93.80.45:~/
```

**Back in SSH Terminal:**
```bash
# Verify files uploaded
ls -lh *.sql

# Execute Step 2A: Fix Customer Names
mysql -u u138256737 -p u138256737_optima_db < fix_customer_names.sql
# Enter MySQL password

# Verify customer names
mysql -u u138256737 -p u138256737_optima_db -e "SELECT id, customer_name FROM customers WHERE id IN (45,49,55,81,240,241);"

# Execute Step 2B: Import Merged Data (takes 2-3 minutes)
mysql -u u138256737 -p u138256737_optima_db < MERGED_MARKETING_DATA.sql

# Check progress
mysql -u u138256737 -p u138256737_optima_db -e "SELECT COUNT(*) as assigned FROM inventory_unit WHERE customer_id IS NOT NULL;"
# Should show ~2265

# Execute Step 2C: Cleanup Duplicates
mysql -u u138256737 -p u138256737_optima_db < CLEANUP_DUPLICATES.sql
```

---

## ✅ STEP 3: VALIDATION

Run these validation queries in PHPMyAdmin or SSH:

**Validation 1: Count Check**
```sql
SELECT 
    'Assigned Units' as metric,
    COUNT(*) as count
FROM inventory_unit 
WHERE customer_id IS NOT NULL

UNION ALL

SELECT 
    'Total Locations',
    COUNT(*)
FROM customer_locations

UNION ALL

SELECT 
    'Total Contracts',
    COUNT(*)
FROM kontrak;
```

**Expected Results:**
- Assigned Units: **~2,265** ✅
- Total Locations: **~630** ✅  
- Total Contracts: **~889** ✅

---

**Validation 2: Duplicates Check (MUST BE 0)**
```sql
SELECT 
    'Duplicate Units' as check_type,
    COUNT(*) as issues
FROM (
    SELECT no_unit
    FROM inventory_unit
    WHERE customer_id IS NOT NULL
    GROUP BY no_unit
    HAVING COUNT(*) > 1
) d

UNION ALL

SELECT 
    'Duplicate Locations',
    COUNT(*)
FROM (
    SELECT customer_id, location_name
    FROM customer_locations
    GROUP BY customer_id, location_name
    HAVING COUNT(*) > 1
) d

UNION ALL

SELECT 
    'Duplicate Contracts',
    COUNT(*)
FROM (
    SELECT customer_po_number
    FROM kontrak
    WHERE customer_po_number IS NOT NULL
    GROUP BY customer_po_number
    HAVING COUNT(*) > 1
) d;
```

**Expected:** ALL must be **0** ✅

---

**Validation 3: Sample Data Check**
```sql
SELECT 
    iu.no_unit,
    c.customer_name,
    cl.location_name,
    k.customer_po_number,
    iu.harga_sewa_bulanan
FROM inventory_unit iu
INNER JOIN customers c ON iu.customer_id = c.id
INNER JOIN customer_locations cl ON iu.customer_location_id = cl.id
LEFT JOIN kontrak k ON iu.kontrak_id = k.id
WHERE iu.customer_id IS NOT NULL
ORDER BY RAND()
LIMIT 10;
```

**Expected:** Should show 10 random units with complete data ✅

---

## 🎊 SUCCESS CRITERIA

Migration is successful when:

✅ **Assigned Units:** ~2,265 (was ~1,195 before)  
✅ **Percentage Assigned:** ~45% (was ~24% before)  
✅ **New Locations:** ~207 created  
✅ **New Contracts:** ~532 created  
✅ **Duplicate Units:** 0  
✅ **Duplicate Locations:** 0  
✅ **Duplicate Contracts:** 0  
✅ **All Validation Queries:** Pass  

---

## 🆘 TROUBLESHOOTING

### ❌ PHPMyAdmin: "Maximum execution time exceeded"
**Solution:** Use SSH method for `MERGED_MARKETING_DATA.sql`

### ❌ SSH: "Permission denied"
**Solution:** 
- Check password is correct
- Verify SSH port: 65002
- Contact hosting support if blocked

### ❌ MySQL: "Access denied for user"
**Solution:**
- Verify username: `u138256737`
- Check database name: `u138256737_optima_db`
- May need to reset MySQL password in hosting panel

### ❌ SQL Error: "Column 'tanggal_berakhir' cannot be null"
**Solution:** Our SQL already fixed this! Re-download latest `MERGED_MARKETING_DATA.sql`

### ❌ Foreign Key Constraint Error
**Solution:** Add to top of SQL file:
```sql
SET FOREIGN_KEY_CHECKS=0;
-- Your SQL here
SET FOREIGN_KEY_CHECKS=1;
```

---

## 🔄 ROLLBACK (Emergency Only)

If migration fails completely:

### Via PHPMyAdmin:
1. Click **"Import"** tab
2. Choose your backup file: `backup_production_20260218.sql`
3. Click **"Go"**
4. Database restored to pre-migration state

### Via SSH:
```bash
mysql -u u138256737 -p u138256737_optima_db < backup_production_20260218.sql
```

---

## 📊 EXPECTED TIMELINE

| Step | Duration | Notes |
|------|----------|-------|
| Backup | 2-3 min | Download ~2-3 MB |
| Upload Files | 1-2 min | 2.1 MB total |
| Fix Customer Names | 30 sec | 6 updates |
| Import Merged Data | 3-5 min | 2,178 units |
| Cleanup Duplicates | 1-2 min | Remove 46 dupes |
| Validation | 2-3 min | Run queries |
| **TOTAL** | **10-15 min** | Complete migration |

---

## ✅ POST-MIGRATION CHECKLIST

- [ ] Backup downloaded and saved
- [ ] All 3 SQL files executed successfully
- [ ] Validation queries all pass (0 duplicates)
- [ ] Sample data check shows correct assignments
- [ ] Application UI tested (customer details page)
- [ ] Users notified of new data
- [ ] Documentation updated

---

## 📞 NEED HELP?

**Common Questions:**

**Q: Do I need to stop the application?**  
A: Not required, but recommended during migration (10-15 min maintenance window)

**Q: Will this delete existing data?**  
A: No! It only:
- Updates 6 customer names
- Adds new assignments to 2,178 units
- Creates new locations & contracts
- Cleans duplicates

**Q: What if I see errors?**  
A: STOP immediately, save error message, restore from backup

**Q: Can I test in production?**  
A: NO! Always backup first, then execute carefully

---

## 🚀 READY TO START?

**Recommendation:** Use **PHPMyAdmin method** for:
- Quick execution
- Visual feedback
- No SSH knowledge needed

Use **SSH method** if:
- PHPMyAdmin times out
- Faster execution preferred
- Comfortable with command line

**Both methods work perfectly - choose what you're comfortable with!**

---

**Migration Prepared:** February 18, 2026  
**Created by:** GitHub Copilot  
**Tested:** ✅ Development (localhost) - 100% success  
**Files:** 3 SQL scripts (2.2 MB total)  
**Target:** u138256737_optima_db on 147.93.80.45
