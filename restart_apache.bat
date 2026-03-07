@echo off
echo ========================================
echo OPTIMA - Restart Apache for .env Reload
echo ========================================
echo.

echo Stopping Apache...
net stop Apache2.4
timeout /t 2

echo Starting Apache...
net start Apache2.4
timeout /t 2

echo.
echo ========================================
echo Apache restarted successfully!
echo .env configuration should now be loaded.
echo ========================================
echo.
echo Please refresh your browser (Ctrl+Shift+R) and test again.
pause
