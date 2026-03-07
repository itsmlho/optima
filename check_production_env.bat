@echo off
REM ========================================================================
REM OPTIMA - Deploy .env_production Configuration to Production
REM ========================================================================
REM 
REM This script helps prepare .env_production for deployment.
REM 
REM IMPORTANT: After uploading to production, you MUST:
REM   1. Rename .env_production to .env on the server
REM   2. Restart Apache (via cPanel or SSH)
REM   3. Clear cache: php spark cache:clear
REM   4. Test with test_csrf.php
REM 
REM ========================================================================

echo.
echo ========================================================================
echo   OPTIMA - .env Production Configuration Checker
echo ========================================================================
echo.

REM Check if .env_production exists
if not exist ".env_production" (
    echo [ERROR] .env_production file not found!
    echo Please make sure you are running this from the project root folder.
    pause
    exit /b 1
)

echo [OK] .env_production file found
echo.

REM Display CSRF Configuration
echo Checking CSRF Configuration in .env_production:
echo ----------------------------------------------------------------
findstr /C:"security.tokenName" .env_production
findstr /C:"security.tokenRandomize" .env_production
findstr /C:"security.regenerate" .env_production
echo ----------------------------------------------------------------
echo.

REM Validate configuration
findstr /C:"security.tokenName = 'csrf_test_name'" .env_production >nul
if %errorlevel% neq 0 (
    echo [WARNING] CSRF tokenName is NOT set to 'csrf_test_name'!
    echo This will cause 403 Forbidden errors in production.
    echo.
    echo Please fix .env_production before deploying!
    pause
    exit /b 1
)

findstr /C:"security.tokenRandomize = false" .env_production >nul
if %errorlevel% neq 0 (
    echo [WARNING] tokenRandomize is NOT set to false!
    echo This will break AJAX requests in production.
    echo.
    echo Please fix .env_production before deploying!
    pause
    exit /b 1
)

findstr /C:"security.regenerate = false" .env_production >nul
if %errorlevel% neq 0 (
    echo [WARNING] regenerate is NOT set to false!
    echo This may break AJAX requests in production.
    echo.
    echo Please fix .env_production before deploying!
    pause
    exit /b 1
)

echo [OK] All CSRF settings are correct!
echo.
echo ========================================================================
echo   Configuration Validation: PASSED
echo ========================================================================
echo.
echo Next Steps for Production Deployment:
echo.
echo 1. Upload .env_production to production server via FTP/SSH
echo.
echo 2. On production server, rename .env_production to .env:
echo    SSH^> mv .env_production .env
echo.
echo 3. Restart Apache (REQUIRED!):
echo    SSH^> sudo systemctl restart apache2
echo    OR via cPanel: "Restart Apache" button
echo.
echo 4. Clear CodeIgniter cache:
echo    SSH^> php spark cache:clear
echo    SSH^> rm -rf writable/cache/*
echo    SSH^> rm -rf writable/session/*
echo.
echo 5. Upload public/test_csrf.php to production
echo.
echo 6. Verify configuration:
echo    Visit: https://optima.sml.co.id/test_csrf.php
echo    Check: All items should show GREEN checkmarks
echo.
echo 7. Delete test_csrf.php from production after verification
echo.
echo 8. Test Customer Management page:
echo    - Check Console (F12) for debug logs
echo    - Verify no 403 errors
echo    - Confirm DataTables loads properly
echo.
echo ========================================================================
echo.
echo Ready to deploy!
echo.
pause
