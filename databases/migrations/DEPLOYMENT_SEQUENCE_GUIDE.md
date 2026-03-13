# 🚀 DEPLOYMENT SEQUENCE GUIDE
**Date:** March 13, 2026  
**Project:** Optima Permission System Fix

---

## 📁 FILE SQL YANG TERSEDIA

| # | File | Type | Size | Risk | Status |
|---|------|------|------|------|--------|
| **1** | `PROD_20260313_kontrak_permissions.sql` | **ADDITIVE** | 11 permissions | ✅ LOW | **WAJIB** |
| **2** | `PROD_20260313_fix_role_permissions.sql` | **⚠️ DESTRUCTIVE** | 600+ lines | 🔥 MEDIUM | **WAJIB** |
| **3** | `PROD_20260313_additional_recommendations.sql` | **ADDITIVE** | 46 assignments | ✅ LOW | **OPSIONAL** |

---

## ⚡ FILE #1: KONTRAK PERMISSIONS (WAJIB)

**File:** `PROD_20260313_kontrak_permissions.sql`

### Apa yang Dilakukan?
- ✅ Tambah **8 permissions** untuk Kontrak CRUD (navigation, view, create, edit, delete, approve, print, export)
- ✅ Tambah **3 permissions** untuk Temporary Units Report (navigation, view, export)

### Perubahan pada Database:
```
permissions table: 348 → 359 rows (+11)
role_permissions table: TIDAK BERUBAH
```

### Operasi SQL:
```sql
INSERT IGNORE INTO permissions ... (11 rows)
```

### Risk Level: ✅ **AMAN**
- Purely additive (hanya INSERT)
- Uses `INSERT IGNORE` (tidak error jika sudah ada)
- Tidak ada DELETE atau UPDATE
- Tidak menyentuh role_permissions

### Kapan Dijalankan?
**PERTAMA KALI** - Sebelum file lainnya

### Command:
```bash
mysql -u root -p optima_db < databases/migrations/PROD_20260313_kontrak_permissions.sql
```

### Verification:
```sql
SELECT COUNT(*) FROM permissions; 
-- Expected: 359 (dari 348)

SELECT COUNT(*) FROM permissions WHERE page = 'kontrak';
-- Expected: 9 (1 old + 8 new)

SELECT COUNT(*) FROM permissions WHERE page = 'temporary_units_report';
-- Expected: 3 (all new)
```

---

## 🔥 FILE #2: ROLE PERMISSIONS REORGANIZATION (WAJIB - HATI-HATI!)

**File:** `PROD_20260313_fix_role_permissions.sql`

### ⚠️ WARNING - DESTRUCTIVE OPERATION!
```sql
DELETE FROM role_permissions WHERE role_id != 1;
```

**INI AKAN MENGHAPUS SEMUA PERMISSION ASSIGNMENTS** (kecuali Super Admin)!

### Apa yang Dilakukan?
1. ❌ **DELETE** semua assignments (kecuali Super Admin role_id 1)
2. ✅ **INSERT** ulang assignments untuk:
   - Administrator (role_id 30) - ALL 359 permissions
   - 20 departmental roles dengan cross-division access
   - Temporary Units Report untuk 10 roles

### Perubahan pada Database:
```
permissions table: TIDAK BERUBAH (359 rows)
role_permissions table: ~860 rows → ~2000+ rows (REGENERATED)
```

### Operasi SQL:
```sql
-- Step 1: Clean up
DELETE FROM role_permissions WHERE role_id != 1;  -- ⚠️ DESTRUCTIVE!

-- Step 2-22: INSERT for each role
INSERT INTO role_permissions ... (2000+ rows total)
```

### Risk Level: 🔥 **MEDIUM**
- **DELETE** existing data (kecuali Super Admin)
- If failed midway → **USERS KEHILANGAN AKSES**
- Requires backup MANDATORY

### ✅ BEFORE YOU RUN:
```sql
-- WAJIB BACKUP!
CREATE TABLE role_permissions_backup_20260313 AS 
SELECT * FROM role_permissions;

-- Verify backup
SELECT COUNT(*) FROM role_permissions_backup_20260313;
-- Should match current role_permissions count
```

### Kapan Dijalankan?
**KEDUA** - Setelah file #1 berhasil

### Command:
```bash
mysql -u root -p optima_db < databases/migrations/PROD_20260313_fix_role_permissions.sql
```

### Verification:
```sql
-- 1. Administrator should have ALL permissions
SELECT COUNT(*) FROM role_permissions WHERE role_id = 30;
-- Expected: 359

-- 2. Total assignments should increase
SELECT COUNT(*) FROM role_permissions;
-- Expected: 2000+ (from ~860)

-- 3. Check Kontrak permissions assigned
SELECT COUNT(*) FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE p.page = 'kontrak';
-- Expected: 200+ (multiple roles have kontrak access)

-- 4. Check Temporary Units Report assigned
SELECT COUNT(*) FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE p.page = 'temporary_units_report';
-- Expected: 30+ (10 roles × 3 permissions average)
```

### ⚠️ ROLLBACK (Jika Gagal):
```sql
-- Delete current broken state
DELETE FROM role_permissions;

-- Restore from backup
INSERT INTO role_permissions 
SELECT * FROM role_permissions_backup_20260313;

-- Verify
SELECT COUNT(*) FROM role_permissions;
```

---

## ⭐ FILE #3: ADDITIONAL RECOMMENDATIONS (OPSIONAL)

**File:** `PROD_20260313_additional_recommendations.sql`

### Apa yang Dilakukan?
- ✅ Head Purchasing → Inventory View (4 permissions)
- ✅ All HEAD Roles → Activity Logs (21 assignments: 7 roles × 3 permissions)
- ✅ All HEAD Roles → Notification Settings (21 assignments: 7 roles × 3 permissions)

### Perubahan pada Database:
```
permissions table: TIDAK BERUBAH (359 rows)
role_permissions table: +46 rows (additive)
```

### Operasi SQL:
```sql
INSERT IGNORE INTO role_permissions ... (46 rows total)
```

### Risk Level: ✅ **AMAN**
- Purely additive (hanya INSERT)
- Uses `INSERT IGNORE` (tidak error jika sudah ada)
- Tidak ada DELETE atau UPDATE
- Tidak mengubah existing permissions

### Kapan Dijalankan?
**KETIGA (OPSIONAL)** - Setelah file #2 berhasil

**Atau bisa SKIP** jika:
- Belum yakin dengan recommendations
- Mau test file #1 dan #2 dulu
- Deploy bertahap (file ini bisa dijalankan besok/lusa)

### Command:
```bash
mysql -u root -p optima_db < databases/migrations/PROD_20260313_additional_recommendations.sql
```

### Verification:
```sql
-- 1. Head Purchasing should have warehouse inventory access
SELECT COUNT(*) FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE rp.role_id = 10 
  AND p.module = 'warehouse'
  AND p.page IN ('unit_inventory', 'sparepart_inventory');
-- Expected: 4

-- 2. All HEAD roles should have activity logs
SELECT r.name, COUNT(*) AS activity_perms
FROM roles r
JOIN role_permissions rp ON r.id = rp.role_id
JOIN permissions p ON rp.permission_id = p.id
WHERE r.id IN (2, 4, 10, 12, 14, 16, 35)
  AND p.module = 'activity'
GROUP BY r.id, r.name;
-- Expected: Each role has 3 permissions

-- 3. All HEAD roles should have notification settings
SELECT r.name, COUNT(*) AS notif_perms
FROM roles r
JOIN role_permissions rp ON r.id = rp.role_id
JOIN permissions p ON rp.permission_id = p.id
WHERE r.id IN (2, 4, 10, 12, 14, 16, 35)
  AND p.module = 'settings' AND p.page = 'notification'
GROUP BY r.id, r.name;
-- Expected: Each role has 3 permissions
```

---

## 🎯 RECOMMENDED DEPLOYMENT STRATEGY

### **OPTION A: ALL-IN-ONE (Recommended for Experienced DBA)**
Deploy semua sekaligus jika yakin dengan test environment.

```bash
# Backup
CREATE TABLE permissions_backup_20260313 AS SELECT * FROM permissions;
CREATE TABLE role_permissions_backup_20260313 AS SELECT * FROM role_permissions;

# Deploy all
mysql -u root -p optima_db < databases/migrations/PROD_20260313_kontrak_permissions.sql
mysql -u root -p optima_db < databases/migrations/PROD_20260313_fix_role_permissions.sql
mysql -u root -p optima_db < databases/migrations/PROD_20260313_additional_recommendations.sql

# Verify
# (Run verification queries from each file)
```

**Total Time:** 30-60 seconds  
**Risk:** Medium (karena file #2 destructive)  
**Rollback:** Full restore from backup

---

### **OPTION B: PHASED DEPLOYMENT (Recommended for Production)**
Deploy bertahap dengan testing di setiap fase.

#### **PHASE 1: Add Permissions Only**
```bash
# Backup
CREATE TABLE permissions_backup_20260313 AS SELECT * FROM permissions;

# Deploy file #1
mysql -u root -p optima_db < databases/migrations/PROD_20260313_kontrak_permissions.sql

# Test: Verify 359 permissions
SELECT COUNT(*) FROM permissions;

# ✅ If SUCCESS → Wait 1 hour, monitor for errors
# ❌ If FAILED → Rollback and investigate
```

**Wait:** 1-2 hours or 1 day (monitor logs)

---

#### **PHASE 2: Role Reorganization (CRITICAL)**
```bash
# Backup role_permissions
CREATE TABLE role_permissions_backup_20260313 AS SELECT * FROM role_permissions;

# MAINTENANCE MODE ON (optional but recommended)
# Inform users: "System maintenance 10-15 minutes"

# Deploy file #2
mysql -u root -p optima_db < databases/migrations/PROD_20260313_fix_role_permissions.sql

# IMMEDIATE VERIFICATION (don't wait!)
SELECT COUNT(*) FROM role_permissions WHERE role_id = 30;  -- Must be 359
SELECT COUNT(*) FROM role_permissions;  -- Must be 2000+

# Test logins for critical roles:
# - Administrator (role_id 30)
# - Head Marketing (role_id 2) 
# - Head Service (role_id 35)

# Check Kontrak menu appears
# Check Temporary Units Report appears for correct roles

# ✅ If SUCCESS → MAINTENANCE MODE OFF
# ❌ If FAILED → IMMEDIATE ROLLBACK!
```

**Downtime:** 10-15 minutes (jika maintenance mode)

---

#### **PHASE 3: Additional Recommendations (Low Risk)**
```bash
# Can be done anytime after Phase 2 succeeds
# Even weeks later is fine

# Deploy file #3
mysql -u root -p optima_db < databases/migrations/PROD_20260313_additional_recommendations.sql

# Verify
# (Run verification queries from file #3)

# Test: HEAD roles should see new menu items
# - Activity Logs
# - Notification Settings (for HEAD Purchasing: Inventory view)
```

**Downtime:** 0 (can deploy without maintenance mode)

---

### **OPTION C: MINIMAL DEPLOYMENT (Safe Start)**
Hanya deploy yang critical, skip recommendations.

```bash
# Deploy only file #1 and #2
mysql -u root -p optima_db < databases/migrations/PROD_20260313_kontrak_permissions.sql
mysql -u root -p optima_db < databases/migrations/PROD_20260313_fix_role_permissions.sql

# Skip file #3 for now
# Deploy file #3 next week after monitoring
```

**Benefit:** Smaller change set, easier to troubleshoot  
**Trade-off:** HEAD roles won't have activity logs/notification settings yet

---

## 📋 SUMMARY TABLE

| File | Deploy? | When? | Backup Needed? | Downtime? | Risk |
|------|---------|-------|----------------|-----------|------|
| **#1 Kontrak Permissions** | ✅ **MUST** | First | ✅ YES | 0 min | LOW |
| **#2 Role Reorganization** | ✅ **MUST** | Second | ✅✅ **CRITICAL** | 10-15 min | MEDIUM |
| **#3 Additional Recommendations** | ⭐ **OPTIONAL** | Third or Later | ✅ YES | 0 min | LOW |

---

## ❓ QUICK DECISION GUIDE

**If you're comfortable with database ops:**
→ Option A (All-in-one)

**If deploying to production first time:**
→ Option B (Phased)

**If you want minimal risk:**
→ Option C (Minimal - skip file #3)

**If you only need Kontrak working ASAP:**
→ Deploy #1 and #2 only, skip #3

**If users complain Kontrak menu not working:**
→ Must deploy #1 then #2 (both required)

---

## 🆘 ROLLBACK PROCEDURES

### If File #1 Failed:
```sql
DELETE FROM permissions WHERE created_at >= CURDATE();
-- Or restore from backup:
DROP TABLE permissions;
CREATE TABLE permissions AS SELECT * FROM permissions_backup_20260313;
```

### If File #2 Failed:
```sql
DELETE FROM role_permissions;
INSERT INTO role_permissions SELECT * FROM role_permissions_backup_20260313;
```

### If File #3 Failed:
```sql
-- Option 1: Targeted delete (recommended)
DELETE FROM role_permissions
WHERE assigned_at >= CURDATE()
  AND role_id IN (2, 4, 10, 12, 14, 16, 35);

-- Option 2: Full restore (if Option 1 doesn't work)
DELETE FROM role_permissions;
INSERT INTO role_permissions SELECT * FROM role_permissions_backup_20260313;
```

---

## ✅ POST-DEPLOYMENT CHECKLIST

After deploying, verify:

- [ ] Permissions count is 359
- [ ] Administrator has 359 permissions
- [ ] Kontrak menu appears for Marketing roles
- [ ] Temporary Units Report appears for 10 roles
- [ ] Cross-division access working (test 2-3 scenarios)
- [ ] No permission denied errors in logs
- [ ] All HEAD roles can view Activity Logs (if file #3 deployed)
- [ ] HEAD Purchasing can view Inventory (if file #3 deployed)

---

## 📞 SUPPORT

**Questions?**
- Check `ROLE_PERMISSION_FIX_GUIDE.md` for detailed explanation
- Check `CROSS_DIVISION_ACCESS_RECOMMENDATIONS.md` for rationale
- Verification queries are in each SQL file (at the end)

**Emergency Rollback?**
- Use backup tables created before deployment
- Follow rollback procedures above
- Check error logs: `writable/logs/`

---

**Last Updated:** March 13, 2026  
**Version:** 1.0
