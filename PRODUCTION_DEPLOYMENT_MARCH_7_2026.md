ga# 🚀 PRODUCTION DEPLOYMENT - DATABASE MIGRATION
**Date:** March 7, 2026  
**Type:** Database Only (Code via Git)

---

## 📦 WHAT'S IN THIS DEPLOYMENT

### **Database Changes:**
- ✅ 348 comprehensive permissions (Dashboard, Marketing, Service, Operational, Accounting, Purchasing, Warehouse, Perizinan, Admin, Reports)
- ✅ 21 new menu permissions (Audit Approval, Unit Audit, Surat Jalan)
- ✅ Role permission assignments (marketing_role, service_role, warehouse_role, admin, super_admin)

### **Code Changes (via Git):**
- ✅ Route configuration fix (nested route group)
- ✅ CSRF token handling improvements
- ✅ Sidebar permission fixes (25+ menu items)
- ✅ User management bug fixes
- ✅ Role management UI enhancements

---

## ⚠️ PRE-DEPLOYMENT CHECKLIST

### **1. Backup Production Database**
```bash
# Via SSH
mysqldump -u [username] -p optima_production > backup_march7_2026_$(date +%Y%m%d_%H%M%S).sql

# Via cPanel
# MySQL Databases → phpMyAdmin → Export → SQL → Go
```

**⚠️ CRITICAL:** Do NOT proceed without backup!

### **2. Check Database Connection**
```bash
# Test connection
mysql -u [username] -p optima_production -e "SELECT COUNT(*) FROM permissions;"
```

### **3. Verify Current Permission Count**
```sql
-- Should be around 200-250 before migration
SELECT COUNT(*) as current_permissions FROM permissions;
```

---

## 🗄️ DATABASE MIGRATION EXECUTION

### **ONE-COMMAND DEPLOYMENT**

```bash
# Upload the migration file and run:
mysql -u [username] -p optima_production < databases/migrations/PRODUCTION_MIGRATION_MARCH_7_2026.sql
```

**⏱️ Execution Time:** ~60 seconds  
**📊 Risk Level:** LOW (INSERT only, uses ON DUPLICATE KEY UPDATE)

---

## ✅ POST-MIGRATION VERIFICATION

### **Automatic Verification**
The migration script automatically runs verification queries. Check the output for:

```
Total Permissions: ~369
Permissions by Module: Should show 13 modules
New Menu Permissions: Should be 21
Role Assignments: All 5 roles should have assignments
```

### **Manual Verification**

**1. Check Total Permissions**
```sql
SELECT COUNT(*) as total FROM permissions;
-- Expected: ~369
```

**2. Check New Permissions**
```sql
SELECT module, page, COUNT(*) as count 
FROM permissions 
WHERE key_name LIKE 'marketing.audit_approval.%'
   OR key_name LIKE 'service.unit_audit.%'
   OR key_name LIKE 'warehouse.movements.%'
GROUP BY module, page;
-- Expected: 21 permissions
```

**3. Check Role Assignments**
```sql
SELECT r.name, COUNT(*) as assigned 
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
WHERE (p.key_name LIKE 'marketing.audit_approval.%'
    OR p.key_name LIKE 'service.unit_audit.%'
    OR p.key_name LIKE 'warehouse.movements.%')
  AND rp.granted = 1
GROUP BY r.name;
-- Expected: marketing_role, service_role, warehouse_role, admin, super_admin
```

**4. Check No Errors**
```sql
SELECT 'Migration Success!' AS Status;
```

---

## 📁 CODE DEPLOYMENT (VIA GIT)

### **Git Push to Production**

**1. Commit All Changes (Development)**
```bash
git add .
git commit -m "Deploy: Permission system, CSRF fixes, sidebar improvements - March 7 2026"
git push origin main
```

**2. Pull in Production (SSH)**
```bash
cd /path/to/optima
git pull origin main
```

**3. Clear Application Cache**
```bash
php spark cache:clear
php spark routes:clear
```

**4. Restart Apache**
```bash
# Via cPanel: Apache → Restart
# Via SSH (if root):
systemctl restart apache2
```

---

## 🧪 TESTING CHECKLIST

### **1. Test Login**
- [ ] Open browser incognito
- [ ] Login with admin user
- [ ] ✅ Should login successfully (no 403 errors)

### **2. Test Role Management**
- [ ] Navigate to: Admin → Role Management
- [ ] Click "Add New Role"
- [ ] Search for "marketing"
- [ ] ✅ Should see filtered permissions
- [ ] Click "Select All" per module
- [ ] Save role
- [ ] ✅ Should save without errors

### **3. Test Sidebar Navigation**
- [ ] Login as marketing user
- [ ] Check menu: Marketing → Audit Approval
- [ ] ✅ Should be visible in both collapsed and expanded sidebar
- [ ] Login as service user
- [ ] Check menu: Service → Unit Audit
- [ ] ✅ Should be visible
- [ ] Login as warehouse user
- [ ] Check menu: Warehouse → Surat Jalan
- [ ] ✅ Should be visible

### **4. Test CSRF Tokens**
- [ ] Open browser DevTools (F12) → Console
- [ ] Navigate to: Marketing → Customer Management
- [ ] ✅ No CSRF errors in console
- [ ] ✅ Statistics load correctly
- [ ] Test DataTables (filter/sort)
- [ ] ✅ No 403 Forbidden errors

### **5. Check Error Logs**
```bash
# Check for errors
tail -f writable/logs/log-*.php
# Should see no errors
```

---

## 🔄 ROLLBACK PROCEDURE (IF NEEDED)

### **Database Rollback**
```bash
# Restore from backup
mysql -u [username] -p optima_production < backup_march7_2026_[timestamp].sql
```

### **Code Rollback (Git)**
```bash
cd /path/to/optima
git reset --hard HEAD~1  # Go back 1 commit
git push -f origin main   # Force push (use with caution!)
```

### **Partial Rollback (New Permissions Only)**
```sql
START TRANSACTION;

-- Delete role assignments
DELETE rp FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE p.key_name LIKE 'marketing.audit_approval.%'
   OR p.key_name LIKE 'service.unit_audit.%'
   OR p.key_name LIKE 'warehouse.movements.%';

-- Delete new permissions
DELETE FROM permissions 
WHERE key_name LIKE 'marketing.audit_approval.%'
   OR key_name LIKE 'service.unit_audit.%'
   OR key_name LIKE 'warehouse.movements.%';

COMMIT;
```

---

## 📊 SUCCESS CRITERIA

✅ **Migration is successful when:**

1. ✅ Database verification shows ~369 total permissions
2. ✅ All 21 new permissions created
3. ✅ All 5 roles have correct assignments
4. ✅ No errors in verification output
5. ✅ Login works (no 403 errors)
6. ✅ Role management UI loads correctly
7. ✅ New menus visible for correct roles
8. ✅ Sidebar shows menus in both modes
9. ✅ No errors in application logs
10. ✅ All AJAX requests work

---

## 🎯 QUICK DEPLOYMENT SUMMARY

**For experienced admins:**

```bash
# 1. Backup
mysqldump -u user -p optima_production > backup.sql

# 2. Run migration
mysql -u user -p optima_production < PRODUCTION_MIGRATION_MARCH_7_2026.sql

# 3. Deploy code (Git)
cd /path/to/optima && git pull origin main

# 4. Clear cache
php spark cache:clear

# 5. Restart Apache
systemctl restart apache2

# 6. Test
# - Login
# - Role management
# - New menus
# - CSRF tokens
```

---

## 📞 SUPPORT

**Files:**
- Migration: `databases/migrations/PRODUCTION_MIGRATION_MARCH_7_2026.sql`
- Changes Log: `RECENT_CHANGES_MARCH_5-7_2026.md`
- CSRF Fix Doc: `docs/CSRF_PRODUCTION_FIX.md`
- Sidebar Audit: `docs/SIDEBAR_PERMISSION_AUDIT_REPORT.md`

**Common Issues:**
1. **403 on AJAX** → Restart Apache, clear cache
2. **Routes 404** → `php spark routes:clear`, `php spark cache:clear`
3. **Permissions not working** → Check role assignments with verification queries
4. **Sidebar menus missing** → Hard refresh browser (Ctrl+Shift+R)

---

**Deployment Guide By:** GitHub Copilot  
**Last Updated:** March 7, 2026  
**Execution Time:** < 5 minutes total
