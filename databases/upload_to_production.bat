@echo off
REM ============================================================================
REM Production Upload Script - Upload Migration Files to Production Server
REM Date: February 21, 2026
REM Run this from: C:\laragon\www\optima
REM ============================================================================

echo ============================================
echo OPTIMA - Upload Migration Files to Production
echo ============================================
echo.

REM Check if we're in correct directory
if not exist "databases\migrations\2026_02_20_add_central_areas_diesel_electric.sql" (
    echo ERROR: Migration files not found!
    echo Please run this script from C:\laragon\www\optima directory
    pause
    exit /b 1
)

echo Files to upload:
echo 1. 2026_02_20_add_central_areas_diesel_electric.sql
echo 2. 2026_02_20_execute_employee_assignments.sql
echo 3. 2026_02_20_rollback_central_areas.sql
echo 4. 2026_02_20_rollback_employee_assignments.sql
echo 5. deploy_areas_production.sh (helper script)
echo.

set /p CONTINUE="Continue upload? (Y/N): "
if /i not "%CONTINUE%"=="Y" (
    echo Upload cancelled.
    pause
    exit /b 0
)

echo.
echo ============================================
echo Uploading files via SCP...
echo ============================================
echo.
echo You will be prompted for SSH password 5 times (once per file)
echo Password is for user: u138256737@147.93.80.45
echo.

REM Upload migration files one by one
echo [1/5] Uploading area migration...
scp -P 65002 databases\migrations\2026_02_20_add_central_areas_diesel_electric.sql u138256737@147.93.80.45:~/optima/databases/migrations/

echo [2/5] Uploading employee assignment...
scp -P 65002 databases\migrations\2026_02_20_execute_employee_assignments.sql u138256737@147.93.80.45:~/optima/databases/migrations/

echo [3/5] Uploading area rollback...
scp -P 65002 databases\migrations\2026_02_20_rollback_central_areas.sql u138256737@147.93.80.45:~/optima/databases/migrations/

echo [4/5] Uploading assignment rollback...
scp -P 65002 databases\migrations\2026_02_20_rollback_employee_assignments.sql u138256737@147.93.80.45:~/optima/databases/migrations/

echo [5/5] Uploading deployment script...
scp -P 65002 databases\deploy_areas_production.sh u138256737@147.93.80.45:~/optima/databases/

echo.
echo ============================================
echo Upload completed!
echo ============================================
echo.
echo Next steps:
echo 1. Connect to production: ssh -p 65002 u138256737@147.93.80.45
echo 2. Navigate to app: cd optima
echo 3. Review deployment guide: databases/PRODUCTION_AREA_CENTRAL_DEPLOYMENT.md
echo 4. Run deployment:
echo    Option A: chmod +x databases/deploy_areas_production.sh
echo              bash databases/deploy_areas_production.sh
echo    Option B: Follow step-by-step guide in PRODUCTION_AREA_CENTRAL_DEPLOYMENT.md
echo.
pause
