# ROLE PERMISSION SYSTEM - FIX & MIGRATION GUIDE
**Date:** 2026-03-13  
**Status:** READY FOR DEPLOYMENT

---

## EXECUTIVE SUMMARY

### CURRENT STATE ✅
- ✅ **Permissions.csv:** 348 permissions (SUDAH LENGKAP - updated March 7, 2026)
- ❌ **Kontrak Module:** HANYA 1 permission (`view_cross_division`) - **KURANG 8 CRUD permissions**
- ❌ **Role Assignments:** TIDAK LENGKAP - Banyak role tidak punya permissions yang sesuai

### MASALAH YANG DITEMUKAN

**1. Kontrak Module (CRITICAL) 🔴**
- **Current:** Only 1 permission (view_cross_division)
- **Missing:** navigation, view, create, edit, delete, approve, print, export
- **Impact:** Users TIDAK BISA akses Kontrak Management properly

**2. Role Permission Assignments (HIGH) ⚠️**
- Administrator (role_id 30): ✅ Punya semua permissions (348)
- Departmental Roles: ❌ Tidak lengkap/tidak sesuai
- Service Roles: ❌ Hierarchy tidak proper
- IT Roles: ❌ Tidak punya admin access

**3. Permission Coverage by Module ✅**
| Module | Permissions | Status |
|--------|------------|--------|
| Marketing | 60 (+8 after fix) | ⚠️ Missing Kontrak CRUD |
| Service | 57 | ✅ Complete |
| Warehouse | 50 | ✅ Complete |
| Admin | 31 | ✅ Complete |
| Settings | 30 | ✅ Complete |
| Purchasing | 29 | ✅ Complete |
| Operational | 26 | ✅ Complete |
| Finance | 21 | ✅ Complete |
| Accounting | 12 | ✅ Complete |
| Reports | 12 | ✅ Complete |
| Perizinan | 11 | ✅ Complete |
| Dashboard | 5 | ✅ Complete |
| Activity | 4 | ✅ Complete |
| **TOTAL** | **348** | **356 after migration** |

---

## MIGRATION FILES YANG SUDAH DISIAPKAN

### 1. PROD_20260313_kontrak_permissions.sql
**Purpose:** Add 8 missing Kontrak CRUD permissions

**What it does:**
- Add: `marketing.kontrak.navigation`
- Add: `marketing.kontrak.view`
- Add: `marketing.kontrak.create`
- Add: `marketing.kontrak.edit`
- Add: `marketing.kontrak.delete`
- Add: `marketing.kontrak.approve`
- Add: `marketing.kontrak.print`
- Add: `marketing.kontrak.export`

**Safe to run:** Uses `INSERT IGNORE` - won't fail if permissions already exist

**Expected result:**
- Before: 348 permissions
- After: 356 permissions (+8)

---

### 2. PROD_20260313_fix_role_permissions.sql
**Purpose:** Properly assign ALL permissions to ALL roles

**What it does:**
1. **Clean up:** Remove all role_permissions (except Super Admin role_id 1)
2. **Assign permissions** to 21 roles with proper hierarchy:
   - Administrator (30): ALL 356 permissions
   - Departmental Heads: Full access to their module + reports + dashboard
   - Departmental Staffs: Limited access (no delete/approve)
   - Service Hierarchy: Head > Admin > Supervisor > Staff
   - IT Roles: Full admin/settings access

**Role Permission Matrix:**

| Role | ID | Permissions Granted |
|------|----|--------------------|
| **Administrator** | 30 | ALL (356 permissions) |
| **Head Marketing** | 2 | ALL marketing + reports + dashboard |
| **Staff Marketing** | 3 | Marketing (no delete/approve) + dashboard |
| **Head Operational** | 4 | ALL operational + reports + dashboard |
| **Staff Operational** | 5 | Operational (no delete/approve) + dashboard |
| **Head Purchasing** | 10 | ALL purchasing + reports + dashboard |
| **Staff Purchasing** | 11 | Purchasing (no delete/approve) + dashboard |
| **Head Accounting** | 12 | ALL accounting + finance + reports + dashboard |
| **Staff Accounting** | 13 | Accounting/Finance (no delete/approve) + dashboard |
| **Head HRD** | 14 | Employee/Division/Position + reports + dashboard |
| **Staff HRD** | 15 | Employee only (no delete) + dashboard |
| **Head Warehouse** | 16 | ALL warehouse + reports + dashboard |
| **Staff Warehouse** | 32 | Warehouse (no delete/approve) + dashboard |
| **Head IT** | 33 | ALL admin + settings + activity + dashboard + reports |
| **Staff IT** | 34 | Admin/Settings/Activity (no delete) + dashboard |
| **Head Service** | 35 | ALL service + reports + dashboard |
| **Admin Service Pusat** | 36 | Service (no user management) + dashboard |
| **Admin Service Area** | 37 | Service (no delete, no user mgmt) + dashboard |
| **Supervisor Service** | 38 | Work order + PMPS (view, create, edit) + dashboard |
| **Staff Service** | 39 | Work order (view, create) + dashboard |
| **Manager Service Area** | 40 | Service (no user management) + reports + dashboard |

**Safe to run:** Deletes and recreates all assignments (except Super Admin)

---

## DEPLOYMENT PLAN

### STEP 1: BACKUP DATABASE ✅ MANDATORY
```sql
-- Backup permissions table
CREATE TABLE permissions_backup_20260313 AS SELECT * FROM permissions;

-- Backup role_permissions table
CREATE TABLE role_permissions_backup_20260313 AS SELECT * FROM role_permissions;

-- Verify backups
SELECT COUNT(*) FROM permissions_backup_20260313;
SELECT COUNT(*) FROM role_permissions_backup_20260313;
```

### STEP 2: RUN KONTRAK PERMISSIONS MIGRATION
```bash
mysql -u optima_user -p optima_db < databases/migrations/PROD_20260313_kontrak_permissions.sql
```

**Expected Output:**
```
=== BEFORE MIGRATION ===
max_permission_id: 384
total_permissions: 348

=== EXISTING KONTRAK PERMISSIONS ===
150 | marketing | kontrak | view_cross_division | ...

[8 rows inserted]

=== AFTER MIGRATION ===
max_permission_id: 392
total_permissions: 356

=== ALL KONTRAK PERMISSIONS AFTER MIGRATION ===
150 | marketing | kontrak | view_cross_division | ...
385 | marketing | kontrak | navigation | ...
386 | marketing | kontrak | view | ...
387 | marketing | kontrak | create | ...
388 | marketing | kontrak | edit | ...
389 | marketing | kontrak | delete | ...
390 | marketing | kontrak | approve | ...
391 | marketing | kontrak | print | ...
392 | marketing | kontrak | export | ...
```

### STEP 3: RUN ROLE PERMISSIONS FIX
```bash
mysql -u optima_user -p optima_db < databases/migrations/PROD_20260313_fix_role_permissions.sql
```

**Expected Output:**
```
[Role permission assignments created for 21 roles]

=== VERIFICATION ===
Role ID | Role Name              | Total Permissions
--------|------------------------|------------------
1       | Super Administrator    | 356
30      | Administrator          | 356
2       | Head Marketing         | 80+
3       | Staff Marketing        | 70+
4       | Head Operational       | 40+
5       | Staff Operational      | 30+
...
```

### STEP 4: VERIFICATION QUERIES
```sql
-- 1. Check total permissions
SELECT COUNT(*) as total_permissions FROM permissions WHERE is_active = 1;
-- Expected: 356

-- 2. Check Administrator permissions
SELECT COUNT(*) FROM role_permissions WHERE role_id = 30 AND granted = 1;
-- Expected: 356

-- 3. Check Kontrak permissions
SELECT id, module, page, action, key_name 
FROM permissions 
WHERE page = 'kontrak' 
ORDER BY action;
-- Expected: 9 rows (1 old + 8 new)

-- 4. Check permissions by module
SELECT module, COUNT(*) as count
FROM permissions
WHERE is_active = 1
GROUP BY module
ORDER BY module;

-- 5. Check role permission distribution
SELECT 
    r.name as role_name,
    COUNT(rp.permission_id) as total_permissions
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id AND rp.granted = 1
GROUP BY r.name
ORDER BY total_permissions DESC;
```

### STEP 5: APPLICATION TESTING
```
1. Login as Administrator
   ✅ Check sidebar - ALL menus should appear
   ✅ Test Kontrak Management access
   ✅ Test Work Order "Tambah" button (already fixed)
   ✅ Test all CRUD operations

2. Login as Head Marketing
   ✅ Check Kontrak menu appears
   ✅ Test Kontrak CRUD (create, edit, delete, approve)
   ✅ Test Export/Print buttons
   ✅ Check Reports access

3. Login as Staff Marketing
   ✅ Check Kontrak menu appears
   ✅ Test Create/Edit (should work)
   ✅ Test Delete button (should be hidden)
   ✅ Check Approve button (should be hidden)

4. Login as Head Service
   ✅ Check Work Order full access
   ✅ Test CRUD operations
   ✅ Check User Management access

5. Login as Staff Service
   ✅ Check Work Order view + create
   ✅ Verify Edit/Delete buttons hidden
```

---

## ROLLBACK PROCEDURE (If something goes wrong)

```sql
-- Restore permissions
DROP TABLE IF EXISTS permissions;
CREATE TABLE permissions AS SELECT * FROM permissions_backup_20260313;

-- Restore role_permissions
DROP TABLE IF EXISTS role_permissions;
CREATE TABLE role_permissions AS SELECT * FROM role_permissions_backup_20260313;

-- Verify restoration
SELECT COUNT(*) FROM permissions;
SELECT COUNT(*) FROM role_permissions;
```

---

## POST-DEPLOYMENT CHECKLIST

### Immediate (Within 1 hour)
- [ ] Verify total permissions = 356
- [ ] Verify Administrator has all 356 permissions
- [ ] Test Kontrak Management access (Admin + Marketing roles)
- [ ] Test Work Order "Tambah" button (Service roles)
- [ ] Check activity logs for errors

### Within 24 hours
- [ ] Test all departmental role logins
- [ ] Verify menu visibility per role
- [ ] Test CRUD operations per role
- [ ] Check delete button visibility (should be hidden for Staff roles)
- [ ] Verify Export/Print buttons work
- [ ] Monitor error logs

### Within 1 week
- [ ] Collect user feedback
- [ ] Adjust permissions if needed
- [ ] Document any issues
- [ ] Update role_permissions.csv if manual changes made

---

## FILES SUMMARY

**Migration Files (Run These):**
1. ✅ `databases/migrations/PROD_20260313_kontrak_permissions.sql` - Add 8 Kontrak perms
2. ✅ `databases/migrations/PROD_20260313_fix_role_permissions.sql` - Fix all role assignments

**Documentation Files:**
1. ✅ `databases/PERMISSION_AUDIT_REPORT_2026-03-13.md` - Full audit report (OLD - before discovery of 348 perms)
2. ✅ `databases/ROLE_PERMISSION_FIX_GUIDE.md` - This file

**Obsolete Files (Delete These):**
1. ❌ `databases/migrations/PROD_20260313_add_missing_permissions.sql` - OBSOLETE (incomplete, many duplicate INSERTs)

---

## TROUBLESHOOTING

### Issue 1: "Duplicate entry for key 'PRIMARY'"
**Cause:** Permission ID already exists  
**Solution:** Migration uses `INSERT IGNORE` - safe to ignore this error

### Issue 2: "Kontrak menu still not visible"
**Cause:** Permission not assigned to role  
**Solution:** 
```sql
-- Check if role has kontrak permissions
SELECT p.key_name 
FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
WHERE rp.role_id = YOUR_ROLE_ID AND p.page = 'kontrak';

-- If empty, manually assign
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT YOUR_ROLE_ID, id, 1, NOW()
FROM permissions
WHERE page = 'kontrak';
```

### Issue 3: "Administrator can't access something"
**Cause:** Permission not granted OR rbac_helper.php bypass not working  
**Solution:**
```sql
-- Verify Administrator bypass in session
-- Check app/Helpers/rbac_helper.php line 76-100
-- Ensure 'administrator' AND 'admin' are in bypass array
```

### Issue 4: "Delete button still visible for Staff"
**Cause:** Permission check in view not implemented  
**Solution:** Update view to check `$can_delete` variable from controller

---

## SUCCESS CRITERIA

✅ **All 356 permissions exist in database**  
✅ **Administrator has all 356 permissions**  
✅ **Kontrak module has 9 permissions (1 old + 8 new)**  
✅ **All 21 roles have appropriate permissions**  
✅ **Head roles have full access to their modules**  
✅ **Staff roles have limited access (no delete/approve)**  
✅ **Service hierarchy works (Head > Admin > Supervisor > Staff)**  
✅ **IT roles have admin/settings access**  
✅ **All menus visible to appropriate roles**  
✅ **CRUD buttons show/hide based on permissions**  
✅ **No errors in application logs**  

---

## SUPPORT

**If you encounter issues:**
1. Check application logs: `writable/logs/log-2026-03-13.log`
2. Run verification queries (Step 4)
3. Check rbac_helper.php for proper bypass
4. Verify session data has correct role
5. Test with Administrator account first
6. Contact IT if permissions still not working

---

**Migration Prepared By:** GitHub Copilot (Claude Sonnet 4.5)  
**Date:** March 13, 2026  
**Status:** ✅ READY FOR DEPLOYMENT
