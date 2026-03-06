@echo off
echo ================================================================
echo   PRODUCTION MIGRATION - AUTO EXECUTION
echo   Step 3-6: RESET, Import, Validate
echo ================================================================
echo.

echo Step 3: RESET Database (truncate tables)...
echo ----------------------------------------------------------------
php reset_database.php --confirm
if errorlevel 1 (
    echo.
    echo ERROR: Step 3 failed!
    pause
    exit /b 1
)
echo.
echo Step 3 completed successfully!
echo ================================================================
echo.

echo Step 4: Import Contracts (687 unique contracts)...
echo ----------------------------------------------------------------
php import_kontrak_from_accounting.php --force
if errorlevel 1 (
    echo.
    echo ERROR: Step 4 failed!
    pause
    exit /b 1
)
echo.
echo Step 4 completed successfully!
echo ================================================================
echo.

echo Step 5: Import Unit Relationships (~2,005 units)...
echo ----------------------------------------------------------------
php import_kontrak_unit_from_accounting.php
if errorlevel 1 (
    echo.
    echo ERROR: Step 5 failed!
    pause
    exit /b 1
)
echo.
echo Step 5 completed successfully!
echo ================================================================
echo.

echo Step 6: Validate Data Integrity...
echo ----------------------------------------------------------------
php validate_post_import.php
if errorlevel 1 (
    echo.
    echo WARNING: Validation completed with warnings
)
echo.
echo Step 6 completed!
echo ================================================================
echo.

echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║          ALL STEPS COMPLETED SUCCESSFULLY!                 ║
echo ╚════════════════════════════════════════════════════════════╝
echo.
echo Next steps:
echo   1. Review validation report (post_import_validation_report_*.txt)
echo   2. Test in browser: http://localhost/optima/public/marketing/kontrak
echo   3. Complete DRAFT contracts (missing dates/PO)
echo.

pause
