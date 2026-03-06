# 🔍 PRODUCTION vs DEVELOPMENT - Quick Check Guide

## 📊 Quick Status Check

### **1. Connect to Production:**

**phpMyAdmin:**
```
URL: https://auth-db1866.hstgr.io/index.php?db=u138256737_optima_db
User: u138256737
Pass: @ITSupport25
```

**SSH:**
```bash
ssh -p 65002 u138256737@147.93.80.4
# Password: @ITSupport25
```

**MySQL via SSH:**
```bash
# After SSH login:
mysql -u u138256737 -p u138256737_optima_db
# Enter password when prompted
```

---

## ✅ Critical Checks (Run in phpMyAdmin)

### **1. Check Schema Differences**

```sql
-- A. Check if kontrak still has customer_location_id (should be NO)
SELECT COLUMN_NAME 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak' 
  AND COLUMN_NAME = 'customer_location_id';
```
**Expected:** EMPTY (0 rows) ✅  
**If shows 1 row:** ❌ Production NOT migrated yet

```sql
-- B. Check if kontrak_unit has customer_location_id (should be YES)
SELECT COLUMN_NAME 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak_unit' 
  AND COLUMN_NAME = 'customer_location_id';
```
**Expected:** 1 row (customer_location_id) ✅  
**If empty:** ❌ Production NOT migrated yet

```sql
-- C. Check if new tables exist
SELECT TABLE_NAME 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME IN ('unit_audit_requests', 'unit_movements');
```
**Expected:** 2 rows (both tables) ✅  
**If 0 rows:** ❌ New features NOT deployed yet

---

### **2. Check Data Counts**

```sql
SELECT 'customers' as tbl, COUNT(*) as count FROM customers
UNION ALL SELECT 'customer_locations', COUNT(*) FROM customer_locations
UNION ALL SELECT 'kontrak', COUNT(*) FROM kontrak
UNION ALL SELECT 'kontrak_unit', COUNT(*) FROM kontrak_unit
UNION ALL SELECT 'inventory_unit', COUNT(*) FROM inventory_unit;
```

**Expected (from Dev):**
| Table | Dev Count | Prod Count | Status |
|-------|-----------|------------|--------|
| customers | 246 | ??? | Check |
| customer_locations | 399 | ??? | Check |
| kontrak | 676 | ??? | Check |
| kontrak_unit | 1,992 | ??? | Check |
| inventory_unit | 4,989 | ??? | Check |

---

### **3. Check Data Integrity**

```sql
-- A. Orphaned kontrak_unit (should be 0)
SELECT COUNT(*) as orphaned_units
FROM kontrak_unit ku
LEFT JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit
WHERE iu.id_inventory_unit IS NULL;
```
**Expected:** 0 ✅  
**If > 0:** ❌ Data integrity issue!

```sql
-- B. Units WITHOUT customer_location_id
SELECT COUNT(*) as units_without_location
FROM kontrak_unit
WHERE customer_location_id IS NULL;
```
**Expected after migration:** 0 ✅  
**Expected before migration:** HIGH number (same as total kontrak_unit)  
**Current dev:** 0 (all populated)

```sql
-- C. Contract status distribution
SELECT status, COUNT(*) as count
FROM kontrak
GROUP BY status;
```
**Expected (from Dev):**
- ACTIVE: ~583
- PENDING: ~93

---

## 🚨 Decision Matrix

### **Scenario 1: Production = OLD (Not Migrated)**

**Symptoms:**
- ✅ kontrak HAS customer_location_id
- ❌ kontrak_unit DOES NOT HAVE customer_location_id
- ❌ unit_audit_requests table MISSING
- ❌ unit_movements table MISSING

**Action Required:**
1. ✅ **RUN MIGRATIONS** using `deploy_production.sh`
2. ✅ **UPLOAD CODE FILES**
3. ✅ **POPULATE DATA** (customer_location_id)

**Risk:** 🟢 LOW - Fresh migration

---

### **Scenario 2: Production = PARTIALLY MIGRATED**

**Symptoms:**
- ❌ kontrak still HAS customer_location_id
- ✅ kontrak_unit HAS customer_location_id (added)
- ⚠️ Some tables exist, some don't

**Action Required:**
1. ⚠️ **REVIEW which migrations already ran**
2. ✅ **RUN MISSING migrations only**
3. ✅ **FIX data inconsistencies**

**Risk:** 🟡 MEDIUM - Need careful review

---

### **Scenario 3: Production = FULLY MIGRATED (Schema OK)**

**Symptoms:**
- ❌ kontrak DOES NOT HAVE customer_location_id
- ✅ kontrak_unit HAS customer_location_id
- ✅ unit_audit_requests table EXISTS
- ✅ unit_movements table EXISTS

**BUT:**
- ⚠️ kontrak_unit.customer_location_id = NULL (many records)

**Action Required:**
1. ❌ **DO NOT run migrations again!**
2. ✅ **POPULATE DATA only:**
   ```sql
   UPDATE kontrak_unit ku
   INNER JOIN kontrak k ON ku.kontrak_id = k.id
   SET ku.customer_location_id = k.customer_location_id
   WHERE ku.customer_location_id IS NULL;
   ```
3. ✅ **UPLOAD CODE FILES if not done**

**Risk:** 🟢 LOW - Just data population needed

---

### **Scenario 4: Production = SAME AS DEV (Perfect Match)**

**Symptoms:**
- ❌ kontrak DOES NOT HAVE customer_location_id ✅
- ✅ kontrak_unit HAS customer_location_id ✅
- ✅ unit_audit_requests EXISTS ✅
- ✅ unit_movements EXISTS ✅
- ✅ Data populated (customer_location_id NOT NULL) ✅
- ✅ Data integrity OK (0 orphans) ✅

**Action Required:**
1. ✅ **VERIFY CODE FILES uploaded**
2. ✅ **TEST features work**
   - /service/unit-audit
   - /warehouse/movements
   - /marketing/kontrak/edit

**Risk:** 🟢 ZERO - Ready to use!

---

## 📋 Quick Verification Checklist

Run these queries in production and compare:

```sql
-- 1. Schema Check
DESCRIBE kontrak;
DESCRIBE kontrak_unit;

-- 2. New Tables Check
SHOW TABLES LIKE 'unit_%';

-- 3. Data Count
SELECT COUNT(*) FROM kontrak_unit;
SELECT COUNT(*) FROM kontrak_unit WHERE customer_location_id IS NOT NULL;

-- 4. Sample Data
SELECT * FROM kontrak_unit LIMIT 5;
```

**Compare results with development:**
- Run same queries in `optima_ci` database
- Tables/columns should MATCH
- Data counts should be SIMILAR (production might have more)

---

## 🎯 Recommended Steps

### **If Production is BEHIND (Scenario 1 or 2):**

1. **SSH to server:**
   ```bash
   ssh -p 65002 u138256737@147.93.80.4
   cd /home/u138256737/public_html  # or wherever your app is
   ```

2. **Upload migration files:**
   ```bash
   # From local machine:
   scp -P 65002 databases/migrations/2026-03-05*.sql u138256737@147.93.80.4:/home/u138256737/public_html/databases/migrations/
   ```

3. **Run deployment script:**
   ```bash
   # On server:
   chmod +x deploy_production.sh
   ./deploy_production.sh
   ```

4. **Upload code files:**
   - Use SFTP or Git to upload changed files
   - See `RECENT_CHANGES_MARCH_5-6_2026.md` for file list

---

### **If Production is UP-TO-DATE (Scenario 3 or 4):**

1. **Just verify code files:**
   - Check Controllers, Models, Views uploaded
   - Test pages load without errors

2. **Test new features:**
   - Login to production
   - Navigate to new pages
   - Create test audit request
   - Create test movement

3. **Monitor logs:**
   ```bash
   # On server:
   tail -f writable/logs/log-*.php
   ```

---

## 📞 If Issues Found

### **Error: Column 'customer_location_id' not found in kontrak_unit**
**Fix:** Run migration #1
```bash
mysql -u u138256737 -p u138256737_optima_db < databases/migrations/2026-03-05_add_customer_location_id_to_kontrak_unit.sql
```

### **Error: Table 'unit_audit_requests' doesn't exist**
**Fix:** Run migration #4
```bash
mysql -u u138256737 -p u138256737_optima_db < databases/migrations/2026-03-05_create_unit_audit_requests_table.sql
```

### **Warning: Many NULL values in customer_location_id**
**Fix:** Populate data
```sql
UPDATE kontrak_unit ku
INNER JOIN kontrak k ON ku.kontrak_id = k.id
SET ku.customer_location_id = k.customer_location_id
WHERE ku.customer_location_id IS NULL;
```

---

## 🔗 Related Files

- **Credentials:** `.credentials/production.txt`
- **Verification Queries:** `databases/PRODUCTION_VERIFICATION_QUERIES.sql`
- **Recent Changes:** `RECENT_CHANGES_MARCH_5-6_2026.md`
- **Deployment Guide:** `PRODUCTION_DEPLOYMENT_READY.md`
- **Deploy Script (Bash):** `deploy_production.sh`
- **Deploy Script (Windows):** `deploy_production.bat`

---

**Last Updated:** March 6, 2026  
**Next Action:** Run verification queries in production phpMyAdmin
