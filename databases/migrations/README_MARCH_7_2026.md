# 🗄️ PRODUCTION MIGRATION - MARCH 7, 2026

## ✅ READY TO DEPLOY

**Migration File:** `PRODUCTION_MIGRATION_MARCH_7_2026.sql`

---

## 🚀 DEPLOYMENT COMMAND

```bash
mysql -u [username] -p optima_production < databases/migrations/PRODUCTION_MIGRATION_MARCH_7_2026.sql
```

---

## 📋 WHAT'S INCLUDED

1. **348 Comprehensive Permissions** across 13 modules
2. **21 New Menu Permissions** (Audit Approval, Unit Audit, Surat Jalan)
3. **Role Assignments** to 5 roles

---

## ⚠️ BEFORE RUNNING

1. ✅ **BACKUP DATABASE FIRST!**
   ```bash
   mysqldump -u [username] -p optima_production > backup_march7_2026.sql
   ```

2. ✅ **Verify connection:**
   ```bash
   mysql -u [username] -p optima_production -e "SELECT 1"
   ```

---

## ✅ AFTER MIGRATION

1. **Deploy code via Git:**
   ```bash
   git pull origin main
   ```

2. **Clear cache:**
   ```bash
   php spark cache:clear
   php spark routes:clear
   ```

3. **Restart Apache**

4. **Test:**
   - Login
   - Role management
   - New menus (Audit Approval, Unit Audit, Surat Jalan)
   - CSRF tokens

---

## 📊 VERIFICATION

Migration automatically runs verification queries. Check output for:

- ✅ Total Permissions: ~369
- ✅ New Menu Permissions: 21
- ✅ Role Assignments: 5 roles

**Manual check:**
```sql
SELECT COUNT(*) FROM permissions;  -- Expected: ~369
```

---

## 🔄 ROLLBACK

If something goes wrong:

```bash
mysql -u [username] -p optima_production < backup_march7_2026.sql
```

---

## 📖 FULL DOCUMENTATION

See [PRODUCTION_DEPLOYMENT_MARCH_7_2026.md](../../PRODUCTION_DEPLOYMENT_MARCH_7_2026.md) for complete guide.

---

**Migration Type:** Database Only  
**Code Deployment:** Via Git  
**Risk Level:** LOW (INSERT only)  
**Execution Time:** ~60 seconds
