@echo off
REM ========================================
REM Production Deployment Script
REM Date: 2026-03-06
REM Database: optima_ci -> optima_production
REM ========================================

echo.
echo ========================================
echo  OPTIMA PRODUCTION DEPLOYMENT
echo  Database Migration + Code Deploy
echo ========================================
echo.

REM Check if running from correct directory
if not exist "databases\migrations" (
    echo ERROR: Please run this script from the optima root directory
    pause
    exit /b 1
)

echo Step 1: Backup Production Database
echo ----------------------------------------
echo Database: u138256737_optima_db
echo Server: 147.93.80.4 (via SSH tunnel or local if dumping remotely)
echo.
set BACKUP_FILE=backups\optima_prod_backup_%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
set BACKUP_FILE=%BACKUP_FILE: =0%

echo Creating backup: %BACKUP_FILE%
echo NOTE: Replace 'optima_production' with 'u138256737_optima_db' if running remotely
mysqldump -u u138256737 -p u138256737_optima_db > %BACKUP_FILE%

if %errorlevel% neq 0 (
    echo ERROR: Database backup failed!
    pause
    exit /b 1
)

echo SUCCESS: Backup created
echo.

echo Step 2: Run Database Migrations
echo ----------------------------------------
echo.
echo Running 5 migration files...
echo.

REM Priority 1: Critical Schema Changes
echo [1/5] Adding customer_location_id to kontrak_unit...
mysql -u root -p optima_production < databases\migrations\2026-03-05_add_customer_location_id_to_kontrak_unit.sql
if %errorlevel% neq 0 goto :migration_error

echo [2/5] Restructuring contract model...
mysql -u root -p optima_production < databases\migrations\2026-03-05_contract_model_restructure.sql
if %errorlevel% neq 0 goto :migration_error

echo [3/5] Updating kontrak_unit schema...
mysql -u root -p optima_production < databases\migrations\2026-03-05_kontrak_unit_harga_spare.sql
if %errorlevel% neq 0 goto :migration_error

REM Priority 2: New Feature Tables
echo [4/5] Creating unit_audit_requests table...
mysql -u root -p optima_production < databases\migrations\2026-03-05_create_unit_audit_requests_table.sql
if %errorlevel% neq 0 goto :migration_error

echo [5/5] Creating unit_movements table...
mysql -u root -p optima_production < databases\migrations\2026-03-05_create_unit_movements_table.sql
if %errorlevel% neq 0 goto :migration_error

echo.
echo SUCCESS: All migrations completed!
echo.

echo Step 3: Verify Migrations
echo ----------------------------------------
echo Checking new columns and tables...
echo.

mysql -u root -p optima_production -e "DESCRIBE kontrak_unit" | findstr customer_location_id
mysql -u root -p optima_production -e "SHOW TABLES LIKE 'unit_audit%%'"
mysql -u root -p optima_production -e "SHOW TABLES LIKE 'unit_movements'"

echo.
echo Step 4: Data Integrity Check
echo ----------------------------------------

mysql -u root -p optima_production -e "SELECT COUNT(*) as kontrak_unit_records FROM kontrak_unit"
mysql -u root -p optima_production -e "SELECT COUNT(*) as units_with_location FROM kontrak_unit WHERE customer_location_id IS NOT NULL"
mysql -u root -p optima_production -e "SELECT COUNT(*) as orphaned FROM kontrak_unit ku LEFT JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit WHERE iu.id_inventory_unit IS NULL"

echo.
echo ========================================
echo  DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Next Steps:
echo 1. Upload code files to production server
echo 2. Test new features:
echo    - /service/unit-audit
echo    - /warehouse/movements
echo    - /marketing/kontrak/edit
echo 3. Monitor error logs for issues
echo.
echo Backup saved to: %BACKUP_FILE%
echo.
pause
exit /b 0

:migration_error
echo.
echo ========================================
echo  ERROR: Migration Failed!
echo ========================================
echo.
echo Rolling back...
echo Restoring from backup: %BACKUP_FILE%
mysql -u root -p optima_production < %BACKUP_FILE%
echo.
echo Database restored to pre-migration state.
echo Please check the error message above and fix the issue.
echo.
pause
exit /b 1
