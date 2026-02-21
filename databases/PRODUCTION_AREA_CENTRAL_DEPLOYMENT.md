# ============================================================================
# PRODUCTION DEPLOYMENT GUIDE - Area CENTRAL Migration
# Date: February 21, 2026
# Target: Production Server (147.93.80.45:65002)
# Purpose: Add 115 CENTRAL areas (61 DIESEL + 54 ELECTRIC) and employee assignments
# ============================================================================

## SUMMARY
- Add 115 new CENTRAL areas with D-* and E-* prefixes
- Assign 2 DIESEL staff to 64 D-* areas (128 assignments)
- Assign 3 ELECTRIC staff to 57 E-* areas (171 assignments)
- Total: 299 new assignment records
- Estimated time: 5-10 minutes
- Zero downtime (safe online migration)

## PRE-DEPLOYMENT CHECKLIST

- [ ] SSH access verified to production server
- [ ] Database credentials ready
- [ ] Backup created
- [ ] Migration files uploaded
- [ ] Employee IDs verified (DIESEL: BAGUS, Deni | ELECTRIC: Novi, Sari, AgusA)
- [ ] Team notified

---

## STEP 1: CONNECT TO PRODUCTION SERVER

```bash
# Connect via SSH
ssh -p 65002 u138256737@147.93.80.45

# Navigate to application directory (adjust path if needed)
cd ~/domains/[YOUR_DOMAIN]/public_html
# OR
cd ~/public_html/optima
# OR  
cd ~/optima

# Verify it's the correct application
pwd
ls -la | grep -E "app|spark|databases"
```

---

## STEP 2: GET DATABASE CREDENTIALS

```bash
# Check database config
cat .env | grep database

# Note down:
# - database.default.database (DB_NAME)
# - database.default.username (DB_USER)
# - database.default.hostname (DB_HOST, usually localhost or 127.0.0.1)
# - database.default.password (DB_PASS)
```

**Expected output example:**
```
database.default.database = optima_production
database.default.username = u138256737_admin
database.default.hostname = localhost
```

---

## STEP 3: CREATE BACKUP (CRITICAL!)

```bash
# Create backup directory if not exists
mkdir -p databases/backups

# Create timestamped backup
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
mysqldump -u [DB_USER] -p [DB_NAME] > databases/backups/backup_areas_${TIMESTAMP}.sql

# Verify backup was created successfully
ls -lh databases/backups/backup_areas_${TIMESTAMP}.sql
# Should show file size > 1MB

# Count lines to confirm it's complete
wc -l databases/backups/backup_areas_${TIMESTAMP}.sql
```

**⚠️ CRITICAL: Do NOT proceed without a valid backup!**

---

## STEP 4: UPLOAD MIGRATION FILES TO PRODUCTION

**Option A: Via Git (if production has git access)**
```bash
# On production server
git pull origin main
# OR specific branch
git pull origin [your-branch]

# Verify files exist
ls -la databases/migrations/2026_02_20_*.sql
```

**Option B: Via SCP (from your local machine)**

Open NEW terminal on your LOCAL Windows machine:

```powershell
# Navigate to project folder
cd C:\laragon\www\optima

# Upload migration files
scp -P 65002 databases/migrations/2026_02_20_add_central_areas_diesel_electric.sql u138256737@147.93.80.45:~/optima/databases/migrations/

scp -P 65002 databases/migrations/2026_02_20_execute_employee_assignments.sql u138256737@147.93.80.45:~/optima/databases/migrations/

scp -P 65002 databases/migrations/2026_02_20_rollback_central_areas.sql u138256737@147.93.80.45:~/optima/databases/migrations/

scp -P 65002 databases/migrations/2026_02_20_rollback_employee_assignments.sql u138256737@147.93.80.45:~/optima/databases/migrations/
```

**Option C: Via FTP/SFTP Client (FileZilla, WinSCP)**
1. Connect to: 147.93.80.45:65002
2. User: u138256737
3. Navigate to: ~/optima/databases/migrations/
4. Upload 4 SQL files from local databases/migrations/

---

## STEP 5: VERIFY CURRENT STATE IN PRODUCTION

```bash
# Back on production SSH session
# Check current area counts
mysql -u [DB_USER] -p [DB_NAME] -e "
SELECT 'Current CENTRAL areas' as info, COUNT(*) as total 
FROM areas WHERE area_type='CENTRAL' AND is_active=1;

SELECT 'D-* CENTRAL areas' as info, COUNT(*) as total 
FROM areas WHERE area_code LIKE 'D-%' AND area_type='CENTRAL' AND is_active=1;

SELECT 'E-* CENTRAL areas' as info, COUNT(*) as total 
FROM areas WHERE area_code LIKE 'E-%' AND area_type='CENTRAL' AND is_active=1;
"
```

**Expected result if NOT yet migrated:**
- Current CENTRAL areas: ~9 (only old ones)
- D-* CENTRAL areas: ~3 (only old short codes like D-TGR)
- E-* CENTRAL areas: ~3 (only old short codes like E-TGR)

**If counts already show 64 and 57, STOP! Already migrated.**

---

## STEP 6: VERIFY EMPLOYEE IDs IN PRODUCTION

**IMPORTANT:** Employee IDs might be DIFFERENT in production!

```bash
# Check DIESEL employees
mysql -u [DB_USER] -p [DB_NAME] -e "
SELECT e.id, e.staff_name, e.staff_role, e.work_location, d.nama_departemen
FROM employees e 
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE d.nama_departemen = 'DIESEL' AND e.is_active = 1
ORDER BY e.work_location, e.staff_name;
"

# Check ELECTRIC employees
mysql -u [DB_USER] -p [DB_NAME] -e "
SELECT e.id, e.staff_name, e.staff_role, e.work_location, d.nama_departemen
FROM employees e 
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE d.nama_departemen = 'ELECTRIC' AND e.is_active = 1
ORDER BY e.work_location, e.staff_name;
"
```

**⚠️ UPDATE EMPLOYEE IDs in deployment script if different!**

Take note of:
- DIESEL CENTRAL staff IDs (for assignment)
- ELECTRIC ADMIN staff IDs (for assignment)

---

## STEP 7: EXECUTE AREA MIGRATION

```bash
# Run area migration
mysql -u [DB_USER] -p [DB_NAME] < databases/migrations/2026_02_20_add_central_areas_diesel_electric.sql
```

**Expected output:**
```
Total CENTRAL areas: [should increase by 115]
DIESEL CENTRAL areas: 64
ELECTRIC CENTRAL areas: 57
Duplicate area_code check: (empty - no duplicates)
All DIESEL CENTRAL areas: [list of 64 areas]
All ELECTRIC CENTRAL areas: [list of 57 areas]
```

**If there's an error, check:**
- Duplicate area_code conflicts
- Column length constraints (area_code max 10 chars)
- Database permissions

---

## STEP 8: UPDATE EMPLOYEE ASSIGNMENT SCRIPT (IF NEEDED)

If employee IDs are different in production, edit the script:

```bash
# Create temp copy
cp databases/migrations/2026_02_20_execute_employee_assignments.sql databases/migrations/2026_02_20_execute_employee_assignments_production.sql

# Edit with nano or vi
nano databases/migrations/2026_02_20_execute_employee_assignments_production.sql

# Find these lines and UPDATE employee IDs:
# Line ~45: AND e.id IN (8, 9)  -- DIESEL staff
# Line ~68: AND e.id IN (1, 2, 18)  -- ELECTRIC staff
# 
# Replace with ACTUAL production employee IDs

# Save and exit (Ctrl+O, Ctrl+X for nano)
```

---

## STEP 9: EXECUTE EMPLOYEE ASSIGNMENTS

```bash
# Run employee assignment
mysql -u [DB_USER] -p [DB_NAME] < databases/migrations/2026_02_20_execute_employee_assignments.sql
# OR if you edited:
mysql -u [DB_USER] -p [DB_NAME] < databases/migrations/2026_02_20_execute_employee_assignments_production.sql
```

**Expected output:**
```
D-* CENTRAL areas (DIESEL): 64
E-* CENTRAL areas (ELECTRIC): 57
Expected new assignments: 284 records (122 DIESEL + 162 ELECTRIC)
DIESEL assignments created: 128
ELECTRIC assignments created: 171
[Table showing employee coverage]
```

---

## STEP 10: VERIFICATION

```bash
# Verify areas
mysql -u [DB_USER] -p [DB_NAME] -e "
SELECT area_type, COUNT(*) as total 
FROM areas 
WHERE is_active=1 
GROUP BY area_type;
"

# Should show:
# CENTRAL: 124 (or similar, +115 from before)
# BRANCH: 26 (unchanged)

# Verify assignments
mysql -u [DB_USER] -p [DB_NAME] -e "
SELECT 
    d.nama_departemen,
    COUNT(DISTINCT aea.employee_id) as employees,
    COUNT(DISTINCT aea.area_id) as areas,
    COUNT(*) as total_assignments
FROM area_employee_assignments aea
JOIN areas a ON aea.area_id = a.id
JOIN employees e ON aea.employee_id = e.id
JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE a.area_type = 'CENTRAL'
  AND (a.area_code LIKE 'D-%' OR a.area_code LIKE 'E-%')
  AND aea.start_date >= '2026-02-20'
GROUP BY d.nama_departemen;
"
```

**Expected:**
- DIESEL: 2 employees, 64 areas, 128 assignments
- ELECTRIC: 3 employees, 57 areas, 171 assignments

---

## STEP 11: TEST IN APPLICATION

```bash
# Test via CLI (if available)
php spark

# Check areas via MySQL
mysql -u [DB_USER] -p [DB_NAME] -e "
SELECT area_code, area_name, area_type 
FROM areas 
WHERE area_code IN ('D-BANDUNG', 'E-MEDAN', 'D-JAKARTA', 'E-SURABAYA')
AND is_active = 1;
"
```

**Then test in browser:**
1. Login to production application
2. Navigate to Service Area Management
3. Filter by area_type = 'CENTRAL'
4. Verify new areas appear
5. Check employee assignments

---

## ROLLBACK PROCEDURE (IF NEEDED)

**If something goes wrong:**

```bash
# Rollback employee assignments
mysql -u [DB_USER] -p [DB_NAME] < databases/migrations/2026_02_20_rollback_employee_assignments.sql

# Rollback areas
mysql -u [DB_USER] -p [DB_NAME] < databases/migrations/2026_02_20_rollback_central_areas.sql

# OR restore from backup
mysql -u [DB_USER] -p [DB_NAME] < databases/backups/backup_areas_[TIMESTAMP].sql
```

---

## POST-DEPLOYMENT

- [ ] Verify areas appear in UI
- [ ] Test SPK creation with new areas
- [ ] Verify employee access permissions
- [ ] Monitor for errors in logs
- [ ] Update team documentation
- [ ] Archive backup somewhere safe

---

## TROUBLESHOOTING

### Error: Duplicate area_code
**Solution:** Some areas might already exist. Check with:
```bash
mysql -u [DB_USER] -p [DB_NAME] -e "
SELECT area_code FROM areas WHERE area_code LIKE 'D-%' OR area_code LIKE 'E-%';
"
```

### Error: Data too long for column 'area_code'
**Solution:** Already fixed in script with abbreviated codes (max 10 chars)

### Error: Unknown column 'e.role'
**Solution:** Column is 'staff_role' not 'role' - already fixed in latest script

### Employee IDs don't match
**Solution:** Update IDs in Step 8 before executing assignments

---

## SUPPORT

If issues occur, contact:
- Database Admin
- DevOps Team
- Application Developer

**Backup location:** databases/backups/backup_areas_[TIMESTAMP].sql

---

## COMPLETION CHECKLIST

- [ ] Areas migrated successfully (115 new areas)
- [ ] Employees assigned (299 assignments)
- [ ] Verification queries passed
- [ ] UI tested and working
- [ ] No errors in application logs
- [ ] Backup archived
- [ ] Team notified of completion
- [ ] Documentation updated

**Migration completed at:** _______________  
**Completed by:** _______________  
**Notes:** _______________

