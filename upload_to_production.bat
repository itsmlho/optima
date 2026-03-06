@echo off
REM ============================================
REM PRODUCTION UPLOAD SCRIPT (Windows)
REM ============================================
REM Target: 147.93.80.45:65002 (u138256737)
REM Date: March 6, 2026
REM ============================================

echo.
echo ========================================
echo   OPTIMA - Production Upload Script
echo ========================================
echo.
echo Target: 147.93.80.45:65002
echo Database: u138256737_optima_db
echo.

REM Check if SCP is available
where scp >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: SCP not found!
    echo Please install OpenSSH Client:
    echo   Settings ^> Apps ^> Optional Features ^> Add OpenSSH Client
    echo.
    pause
    exit /b 1
)

echo [INFO] SCP found! Starting upload...
echo.

REM ============================================
REM STEP 1: Upload Migration Files
REM ============================================

echo ========================================
echo   STEP 1: Upload Migration Files
echo ========================================
echo.

echo [1/5] Uploading: 2026-03-05_add_customer_location_id_to_kontrak_unit.sql
scp -o StrictHostKeyChecking=no -P 65002 databases\migrations\2026-03-05_add_customer_location_id_to_kontrak_unit.sql u138256737@147.93.80.45:/home/u138256737/public_html/databases/migrations/
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to upload migration 1
    pause
    exit /b 1
)

echo [2/5] Uploading: 2026-03-05_contract_model_restructure.sql
scp -o StrictHostKeyChecking=no -P 65002 databases\migrations\2026-03-05_contract_model_restructure.sql u138256737@147.93.80.45:/home/u138256737/public_html/databases/migrations/
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to upload migration 2
    pause
    exit /b 1
)

echo [3/5] Uploading: 2026-03-05_kontrak_unit_harga_spare.sql
scp -o StrictHostKeyChecking=no -P 65002 databases\migrations\2026-03-05_kontrak_unit_harga_spare.sql u138256737@147.93.80.45:/home/u138256737/public_html/databases/migrations/
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to upload migration 3
    pause
    exit /b 1
)

echo [4/5] Uploading: 2026-03-05_create_unit_audit_requests_table.sql
scp -o StrictHostKeyChecking=no -P 65002 databases\migrations\2026-03-05_create_unit_audit_requests_table.sql u138256737@147.93.80.45:/home/u138256737/public_html/databases/migrations/
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to upload migration 4
    pause
    exit /b 1
)

echo [5/5] Uploading: 2026-03-05_create_unit_movements_table.sql
scp -o StrictHostKeyChecking=no -P 65002 databases\migrations\2026-03-05_create_unit_movements_table.sql u138256737@147.93.80.45:/home/u138256737/public_html/databases/migrations/
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to upload migration 5
    pause
    exit /b 1
)

echo.
echo [OK] All migration files uploaded!
echo.

REM ============================================
REM STEP 2: Upload Controllers
REM ============================================

echo ========================================
echo   STEP 2: Upload Controllers
echo ========================================
echo.

echo [NEW] Uploading: UnitAudit.php
scp -o StrictHostKeyChecking=no -P 65002 app\Controllers\UnitAudit.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Controllers/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload UnitAudit.php
)

echo [NEW] Uploading: UnitMovementController.php
scp -o StrictHostKeyChecking=no -P 65002 app\Controllers\Warehouse\UnitMovementController.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Controllers/Warehouse/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload UnitMovementController.php
)

echo [UPDATED] Uploading: Kontrak.php
scp -o StrictHostKeyChecking=no -P 65002 app\Controllers\Kontrak.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Controllers/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload Kontrak.php
)

echo [UPDATED] Uploading: Marketing.php
scp -o StrictHostKeyChecking=no -P 65002 app\Controllers\Marketing.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Controllers/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload Marketing.php
)

echo.
echo [OK] Controllers uploaded!
echo.

REM ============================================
REM STEP 3: Upload Models
REM ============================================

echo ========================================
echo   STEP 3: Upload Models
echo ========================================
echo.

echo [NEW] Uploading: UnitAuditRequestModel.php
scp -o StrictHostKeyChecking=no -P 65002 app\Models\UnitAuditRequestModel.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Models/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload UnitAuditRequestModel.php
)

echo [NEW] Uploading: UnitMovementModel.php
scp -o StrictHostKeyChecking=no -P 65002 app\Models\UnitMovementModel.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Models/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload UnitMovementModel.php
)

echo [UPDATED] Uploading: KontrakModel.php
scp -o StrictHostKeyChecking=no -P 65002 app\Models\KontrakModel.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Models/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload KontrakModel.php
)

echo.
echo [OK] Models uploaded!
echo.

REM ============================================
REM STEP 4: Upload Views
REM ============================================

echo ========================================
echo   STEP 4: Upload Views
echo ========================================
echo.

echo [NEW] Uploading: unit_audit.php
scp -o StrictHostKeyChecking=no -P 65002 app\Views\service\unit_audit.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Views/service/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload unit_audit.php
)

echo [NEW] Uploading: unit_movement.php
scp -o StrictHostKeyChecking=no -P 65002 app\Views\warehouse\unit_movement.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Views/warehouse/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload unit_movement.php
)

echo [UPDATED] Uploading: kontrak_edit.php
scp -o StrictHostKeyChecking=no -P 65002 app\Views\marketing\kontrak_edit.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Views/marketing/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload kontrak_edit.php
)

echo [UPDATED] Uploading: kontrak_detail.php
scp -o StrictHostKeyChecking=no -P 65002 app\Views\marketing\kontrak_detail.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Views/marketing/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload kontrak_detail.php
)

echo [UPDATED] Uploading: add_unit_modal.php
scp -o StrictHostKeyChecking=no -P 65002 app\Views\components\add_unit_modal.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Views/components/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload add_unit_modal.php
)

echo.
echo [OK] Views uploaded!
echo.

REM ============================================
REM STEP 5: Upload Config
REM ============================================

echo ========================================
echo   STEP 5: Upload Config Files
echo ========================================
echo.

echo [UPDATED] Uploading: Routes.php
scp -o StrictHostKeyChecking=no -P 65002 app\Config\Routes.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Config/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload Routes.php
)

echo [UPDATED] Uploading: sidebar_new.php
scp -o StrictHostKeyChecking=no -P 65002 app\Views\layouts\sidebar_new.php u138256737@147.93.80.45:/home/u138256737/public_html/app/Views/layouts/
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Failed to upload sidebar_new.php
)

echo.
echo [OK] Config files uploaded!
echo.

REM ============================================
REM DONE!
REM ============================================

echo.
echo ========================================
echo   FILE UPLOAD COMPLETE!
echo ========================================
echo.
echo NEXT STEPS:
echo.
echo 1. Run database migrations:
echo    - Via SSH: ssh -p 65002 u138256737@147.93.80.45
echo    - Or via phpMyAdmin: https://auth-db1866.hstgr.io
echo.
echo 2. Run these migrations IN ORDER:
echo    a) 2026-03-05_add_customer_location_id_to_kontrak_unit.sql
echo    b) 2026-03-05_contract_model_restructure.sql
echo    c) 2026-03-05_kontrak_unit_harga_spare.sql
echo    d) 2026-03-05_create_unit_audit_requests_table.sql
echo    e) 2026-03-05_create_unit_movements_table.sql
echo.
echo 3. Populate data:
echo    UPDATE kontrak_unit ku
echo    INNER JOIN kontrak k ON ku.kontrak_id = k.id
echo    SET ku.customer_location_id = k.customer_location_id
echo    WHERE ku.customer_location_id IS NULL;
echo.
echo 4. Verify deployment:
echo    - Check schema changes
echo    - Test pages
echo    - Monitor logs
echo.
echo ========================================
echo.
pause
