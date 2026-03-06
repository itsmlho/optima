# ✅ PRODUCTION DEPLOYMENT CHECKLIST
**Date:** March 6, 2026  
**Production:** u138256737_optima_db @ 147.93.80.4:65002

---

## 🔍 Pre-Deployment Verification

- [x] Production DB checked - `kontrak.customer_location_id` still exists ✅
- [x] Development DB correct - schema migrated ✅
- [x] Migration files ready (5 files) ✅
- [x] Code files ready ✅
- [ ] **BACKUP PRODUCTION DATABASE** ⚠️ **DO THIS FIRST!**

---

## 📦 STEP 1: Upload Files (~10 minutes)

### **Option A: Automated Upload (Recommended)**

```bash
# Run this script:
upload_to_production.bat
```

**What it does:**
- Uploads all 5 migration files
- Uploads all controllers (new + updated)
- Uploads all models (new + updated)
- Uploads all views (new + updated)
- Uploads config files

**Checklist:**
- [ ] Script runs without errors
- [ ] All files uploaded successfully
- [ ] No "Permission denied" errors

### **Option B: Manual Upload (SFTP)**

Use WinSCP or FileZilla:
- [ ] Upload 5 migration files to `/databases/migrations/`
- [ ] Upload 2 new controllers
- [ ] Upload 4 updated controllers
- [ ] Upload 2 new models
- [ ] Upload 3 updated models
- [ ] Upload 3 new views
- [ ] Upload 5 updated views
- [ ] Upload config files

---

## 🗄️ STEP 2: Backup Production Database (~2 minutes)

### **Via phpMyAdmin:**
1. Open: https://auth-db1866.hstgr.io
2. Login: u138256737 / @ITSupport25
3. Select database: `u138256737_optima_db`
4. Click **Export**
5. Format: **SQL**
6. Click **Go**
7. Save file: `optima_prod_backup_2026-03-06.sql`

**Checklist:**
- [ ] Backup file downloaded
- [ ] File size > 1 MB (verify not empty)
- [ ] Saved in safe location

### **Via SSH (Alternative):**
```bash
ssh -p 65002 u138256737@147.93.80.4
mysqldump -u u138256737 -p u138256737_optima_db > backup_2026-03-06.sql
```

---

## 🚀 STEP 3: Run Migrations (~5 minutes)

### **Via phpMyAdmin (Recommended)**

For each migration file, run **IN THIS ORDER:**

#### **Migration 1:** Add customer_location_id to kontrak_unit
- [ ] Open: `2026-03-05_add_customer_location_id_to_kontrak_unit.sql`
- [ ] Copy ALL contents
- [ ] Paste in phpMyAdmin SQL tab
- [ ] Click **Go**
- [ ] ✅ Success: "Query OK, 0 rows affected"

#### **Migration 2:** Contract model restructure
- [ ] Open: `2026-03-05_contract_model_restructure.sql`
- [ ] Copy ALL contents
- [ ] Paste in phpMyAdmin SQL tab
- [ ] Click **Go**
- [ ] ✅ Success: "customer_location_id column removed from kontrak"

#### **Migration 3:** Add harga_sewa and is_spare
- [ ] Open: `2026-03-05_kontrak_unit_harga_spare.sql`
- [ ] Copy ALL contents
- [ ] Paste in phpMyAdmin SQL tab
- [ ] Click **Go**
- [ ] ✅ Success: "2 columns added"

#### **Migration 4:** Create unit_audit_requests table
- [ ] Open: `2026-03-05_create_unit_audit_requests_table.sql`
- [ ] Copy ALL contents
- [ ] Paste in phpMyAdmin SQL tab
- [ ] Click **Go**
- [ ] ✅ Success: "Table created"

#### **Migration 5:** Create unit_movements table
- [ ] Open: `2026-03-05_create_unit_movements_table.sql`
- [ ] Copy ALL contents
- [ ] Paste in phpMyAdmin SQL tab
- [ ] Click **Go**
- [ ] ✅ Success: "Table created"

---

## 🔄 STEP 4: Populate Data (~1 minute)

Run this query in phpMyAdmin SQL tab:

```sql
UPDATE kontrak_unit ku
INNER JOIN kontrak k ON ku.kontrak_id = k.id
SET ku.customer_location_id = k.customer_location_id
WHERE ku.customer_location_id IS NULL
  AND k.customer_location_id IS NOT NULL;
```

**Expected result:**
```
Query OK, [XXX] rows affected
```

**Checklist:**
- [ ] Query executed successfully
- [ ] Rows affected > 0 (shows number of units updated)

---

## ✅ STEP 5: Verify Deployment (~5 minutes)

### **A. Schema Verification**

Run these queries in phpMyAdmin:

**1. Check kontrak.customer_location_id REMOVED:**
```sql
SELECT COLUMN_NAME FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak' 
  AND COLUMN_NAME = 'customer_location_id';
```
- [ ] ✅ Result: **EMPTY** (0 rows)

**2. Check kontrak_unit.customer_location_id ADDED:**
```sql
SELECT COLUMN_NAME FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak_unit' 
  AND COLUMN_NAME = 'customer_location_id';
```
- [ ] ✅ Result: **1 row** (column exists)

**3. Check new tables created:**
```sql
SHOW TABLES LIKE 'unit_%';
```
- [ ] ✅ Shows: `unit_audit_requests`, `unit_movements`

**4. Check data populated:**
```sql
SELECT COUNT(*) FROM kontrak_unit WHERE customer_location_id IS NOT NULL;
```
- [ ] ✅ Result: > 1000 (should match total kontrak_unit count)

### **B. Page Testing**

**1. Contract Edit Page:**
- [ ] Visit: `https://[your-domain]/marketing/kontrak/edit/1`
- [ ] ✅ Page loads without error
- [ ] ✅ Customer dropdown is **DISABLED**
- [ ] ✅ Location field is **REMOVED**
- [ ] ✅ Financial fields are **readonly**

**2. Contract Detail Page:**
- [ ] Visit: `https://[your-domain]/marketing/kontrak/detail/1`
- [ ] ✅ Page loads without error
- [ ] ✅ Overview tab shows by default
- [ ] ✅ Units tab shows unit locations

**3. Unit Audit Page (NEW):**
- [ ] Visit: `https://[your-domain]/service/unit-audit`
- [ ] ✅ Page loads without error
- [ ] ✅ Customer dropdown works
- [ ] ✅ Can select units

**4. Unit Movement Page (NEW):**
- [ ] Visit: `https://[your-domain]/warehouse/movements`
- [ ] ✅ Page loads without error
- [ ] ✅ Stats display
- [ ] ✅ Can create movement

### **C. Error Log Check**

- [ ] Check: `/writable/logs/log-2026-03-06.php`
- [ ] ✅ No critical errors
- [ ] ✅ No "column not found" errors

---

## 🆘 Rollback (If Issues)

**If anything goes wrong:**

```sql
-- Drop new tables
DROP TABLE IF EXISTS unit_movements;
DROP TABLE IF EXISTS unit_audit_requests;

-- Restore from backup
-- (Upload backup file via phpMyAdmin Import)
```

**Checklist:**
- [ ] Backup file ready
- [ ] Know how to restore

---

## 📊 Expected Results Summary

| What | Before | After |
|------|--------|-------|
| `kontrak.customer_location_id` | ✅ Exists | ❌ Removed |
| `kontrak_unit.customer_location_id` | ❌ Not exists | ✅ Added |
| `unit_audit_requests` table | ❌ Not exists | ✅ Created |
| `unit_movements` table | ❌ Not exists | ✅ Created |
| Data in `kontrak_unit.customer_location_id` | NULL | ✅ Populated |

---

## 🎯 Timeline

- Upload files: **10 min**
- Backup production: **2 min**
- Run migrations: **5 min**
- Populate data: **1 min**
- Verify: **5 min**
- **Total: ~23 minutes**

---

## ✅ Deployment Complete!

**After successful deployment:**

- [ ] All migrations run successfully
- [ ] All verifications passed
- [ ] All pages load without errors
- [ ] New features accessible
- [ ] Team notified of changes
- [ ] Documentation updated

**Next Steps:**

1. **Development Testing (1-2 weeks):**
   - Team uses Unit Audit to clean data
   - Team uses Unit Movement to track units
   - Report bugs found
   - Fix issues iteratively

2. **Production Monitoring:**
   - Watch error logs daily
   - Monitor user feedback
   - Track feature usage

3. **After Testing Complete:**
   - Full production rollout
   - Team training on new features
   - Go live!

---

**Status:** ⏳ Ready to Deploy  
**Risk:** 🟢 LOW (backward compatible, empty new tables)  
**Backup:** ⚠️ **MANDATORY - DO NOT SKIP!**
