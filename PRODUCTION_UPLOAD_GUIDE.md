# 🚀 PRODUCTION UPLOAD GUIDE
**Date:** March 6, 2026  
**Target:** 147.93.80.4:65002 (u138256737_optima_db)

---

## 📦 FILES TO UPLOAD

### **Priority 1: Database Migration Files (5 files)**

Upload ke folder: `/home/u138256737/public_html/databases/migrations/`

```
databases/migrations/2026-03-05_add_customer_location_id_to_kontrak_unit.sql
databases/migrations/2026-03-05_contract_model_restructure.sql
databases/migrations/2026-03-05_kontrak_unit_harga_spare.sql
databases/migrations/2026-03-05_create_unit_audit_requests_table.sql
databases/migrations/2026-03-05_create_unit_movements_table.sql
```

### **Priority 2: Controllers (NEW + UPDATED)**

Upload ke folder: `/home/u138256737/public_html/app/Controllers/`

**NEW Files:**
```
app/Controllers/UnitAudit.php
```

Upload ke folder: `/home/u138256737/public_html/app/Controllers/Warehouse/`
```
app/Controllers/Warehouse/UnitMovementController.php
```

**UPDATED Files:**
```
app/Controllers/Kontrak.php
app/Controllers/Marketing.php
app/Controllers/Service.php
```

### **Priority 3: Models (NEW + UPDATED)**

Upload ke folder: `/home/u138256737/public_html/app/Models/`

**NEW Files:**
```
app/Models/UnitAuditRequestModel.php
app/Models/UnitMovementModel.php
```

**UPDATED Files:**
```
app/Models/KontrakModel.php
app/Models/InventoryUnitModel.php
app/Models/CustomerModel.php
```

### **Priority 4: Views (NEW + UPDATED)**

Upload ke folder: `/home/u138256737/public_html/app/Views/`

**NEW Files:**
```
app/Views/service/unit_audit.php
app/Views/warehouse/unit_movement.php
app/Views/marketing/audit_approval.php
```

**UPDATED Files:**
```
app/Views/marketing/kontrak_edit.php
app/Views/marketing/kontrak_detail.php
app/Views/components/add_unit_modal.php
```

### **Priority 5: Config & Layout**

```
app/Config/Routes.php
app/Views/layouts/sidebar_new.php
```

---

## 🔧 Method 1: Upload via SFTP (WinSCP/FileZilla)

### **A. Using WinSCP (Windows)**

1. **Download WinSCP:** https://winscp.net/

2. **Connect to Server:**
   ```
   Protocol: SFTP
   Host: 147.93.80.4
   Port: 65002
   Username: u138256737
   Password: @ITSupport25
   ```

3. **Navigate to folders:**
   - Left panel: Your local `C:\laragon\www\optima\`
   - Right panel: `/home/u138256737/public_html/`

4. **Upload files:**
   - Select files from left panel
   - Drag & drop to right panel
   - Confirm overwrite when asked

### **B. Using FileZilla**

1. **Download FileZilla:** https://filezilla-project.org/

2. **Connect:**
   ```
   Host: sftp://147.93.80.4
   Username: u138256737
   Password: @ITSupport25
   Port: 65002
   ```

3. **Upload files** (same as WinSCP)

---

## 🔧 Method 2: Upload via SSH + SCP (Command Line)

### **A. Using SCP from Windows**

Open Command Prompt / PowerShell:

```bash
# Upload migration files
scp -P 65002 databases/migrations/2026-03-05*.sql u138256737@147.93.80.4:/home/u138256737/public_html/databases/migrations/

# Upload Controllers (NEW)
scp -P 65002 app/Controllers/UnitAudit.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Controllers/

scp -P 65002 app/Controllers/Warehouse/UnitMovementController.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Controllers/Warehouse/

# Upload Controllers (UPDATED)
scp -P 65002 app/Controllers/Kontrak.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Controllers/
scp -P 65002 app/Controllers/Marketing.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Controllers/

# Upload Models (NEW)
scp -P 65002 app/Models/UnitAuditRequestModel.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Models/
scp -P 65002 app/Models/UnitMovementModel.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Models/

# Upload Models (UPDATED)
scp -P 65002 app/Models/KontrakModel.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Models/

# Upload Views (NEW)
scp -P 65002 app/Views/service/unit_audit.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Views/service/
scp -P 65002 app/Views/warehouse/unit_movement.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Views/warehouse/

# Upload Views (UPDATED)
scp -P 65002 app/Views/marketing/kontrak_edit.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Views/marketing/
scp -P 65002 app/Views/marketing/kontrak_detail.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Views/marketing/
scp -P 65002 app/Views/components/add_unit_modal.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Views/components/

# Upload Config
scp -P 65002 app/Config/Routes.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Config/
scp -P 65002 app/Views/layouts/sidebar_new.php u138256737@147.93.80.4:/home/u138256737/public_html/app/Views/layouts/
```

---

## 🔧 Method 3: ZIP + Upload via cPanel/phpMyAdmin

### **Create ZIP package:**

```bash
# Create zip of all changed files
# (Manual: select files and zip them)
```

Then upload via:
- cPanel File Manager
- FTP
- Extract on server

---

## 🗄️ STEP 2: Run Database Migrations

### **Option A: Via SSH (Recommended)**

```bash
# 1. SSH to server
ssh -p 65002 u138256737@147.93.80.4

# 2. Navigate to app folder
cd /home/u138256737/public_html

# 3. Backup database first!
mysqldump -u u138256737 -p u138256737_optima_db > backups/backup_$(date +%Y%m%d_%H%M%S).sql

# 4. Run migrations in order
mysql -u u138256737 -p u138256737_optima_db < databases/migrations/2026-03-05_add_customer_location_id_to_kontrak_unit.sql

mysql -u u138256737 -p u138256737_optima_db < databases/migrations/2026-03-05_contract_model_restructure.sql

mysql -u u138256737 -p u138256737_optima_db < databases/migrations/2026-03-05_kontrak_unit_harga_spare.sql

mysql -u u138256737 -p u138256737_optima_db < databases/migrations/2026-03-05_create_unit_audit_requests_table.sql

mysql -u u138256737 -p u138256737_optima_db < databases/migrations/2026-03-05_create_unit_movements_table.sql

# 5. Populate customer_location_id data
mysql -u u138256737 -p u138256737_optima_db << 'EOF'
UPDATE kontrak_unit ku
INNER JOIN kontrak k ON ku.kontrak_id = k.id
SET ku.customer_location_id = k.customer_location_id
WHERE ku.customer_location_id IS NULL
  AND k.customer_location_id IS NOT NULL;
EOF

# 6. Verify
mysql -u u138256737 -p u138256737_optima_db -e "DESCRIBE kontrak_unit" | grep customer_location_id
mysql -u u138256737 -p u138256737_optima_db -e "SHOW TABLES LIKE 'unit_%'"
```

### **Option B: Via phpMyAdmin**

1. Open: https://auth-db1866.hstgr.io/index.php?db=u138256737_optima_db

2. **For each migration file:**
   - Click "SQL" tab
   - Open migration file in local editor
   - Copy ALL contents
   - Paste into SQL box
   - Click "Go"
   - Check for errors

3. **Run in this order:**
   - 2026-03-05_add_customer_location_id_to_kontrak_unit.sql
   - 2026-03-05_contract_model_restructure.sql
   - 2026-03-05_kontrak_unit_harga_spare.sql
   - 2026-03-05_create_unit_audit_requests_table.sql
   - 2026-03-05_create_unit_movements_table.sql

4. **Populate data:**
   ```sql
   UPDATE kontrak_unit ku
   INNER JOIN kontrak k ON ku.kontrak_id = k.id
   SET ku.customer_location_id = k.customer_location_id
   WHERE ku.customer_location_id IS NULL
     AND k.customer_location_id IS NOT NULL;
   ```

5. **Verify:**
   ```sql
   DESCRIBE kontrak_unit;
   SHOW TABLES LIKE 'unit_%';
   SELECT COUNT(*) FROM kontrak_unit WHERE customer_location_id IS NOT NULL;
   ```

---

## ✅ STEP 3: Verify Deployment

### **1. Check Schema Changes**

```sql
-- Should return EMPTY (column removed)
SELECT COLUMN_NAME FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak' 
  AND COLUMN_NAME = 'customer_location_id';

-- Should return 1 row (column added)
SELECT COLUMN_NAME FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME = 'kontrak_unit' 
  AND COLUMN_NAME = 'customer_location_id';
```

### **2. Check Data Populated**

```sql
-- Should match total kontrak_unit count
SELECT COUNT(*) FROM kontrak_unit WHERE customer_location_id IS NOT NULL;
```

### **3. Test Pages**

- Visit: https://[your-production-domain]/service/unit-audit
- Visit: https://[your-production-domain]/warehouse/movements
- Visit: https://[your-production-domain]/marketing/kontrak/edit/1

**Expected:**
- ✅ No PHP errors
- ✅ Pages load correctly
- ✅ Customer field disabled on edit page
- ✅ New features accessible

---

## 🔴 CRITICAL: Backup First!

**ALWAYS backup before migration:**

```bash
# Via SSH:
mysqldump -u u138256737 -p u138256737_optima_db > backup_pre_migration_$(date +%Y%m%d).sql

# Via phpMyAdmin:
Export → SQL → Go → Save file
```

---

## 🆘 Rollback Plan (If Issues)

```bash
# Restore from backup
mysql -u u138256737 -p u138256737_optima_db < backup_pre_migration_YYYYMMDD.sql
```

---

## 📋 Quick Checklist

### **Before Deployment:**
- [ ] Backup production database
- [ ] Upload all migration files
- [ ] Upload all code files

### **During Deployment:**
- [ ] Run migrations in correct order
- [ ] Populate customer_location_id data
- [ ] Verify schema changes
- [ ] Check for errors

### **After Deployment:**
- [ ] Test critical pages
- [ ] Check error logs
- [ ] Verify new features work
- [ ] Monitor for issues

---

## 🎯 Estimated Timeline

- Upload files: 10 minutes
- Run migrations: 5 minutes
- Verification: 5 minutes
- **Total: ~20 minutes**

---

## 📞 If You Get Stuck

**Common Issues:**

1. **"Permission denied" on upload**
   - Check SSH credentials
   - Verify folder paths exist
   - Check file permissions (chmod 755)

2. **"Table already exists" during migration**
   - Migration may have been partially run
   - Check which tables exist: `SHOW TABLES;`
   - Skip failed migration, continue next

3. **"Column not found" errors in code**
   - Migrations not completed
   - Run verification queries
   - Check error logs: `/writable/logs/`

4. **Pages show errors after deployment**
   - Check PHP error logs
   - Verify all files uploaded
   - Clear cache: `writable/cache/`

---

**Ready to Deploy?** 
Start with **Method 1 (SFTP)** if you're not comfortable with command line, or **Method 2 (SSH)** for automated deployment.
