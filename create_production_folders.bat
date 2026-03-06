@echo off
REM ============================================
REM Create Production Folder Structure
REM ============================================

echo Creating production folder structure...
echo.

ssh -o StrictHostKeyChecking=no -p 65002 u138256737@147.93.80.45 "mkdir -p /home/u138256737/public_html/databases/migrations && mkdir -p /home/u138256737/public_html/app/Controllers/Warehouse && mkdir -p /home/u138256737/public_html/app/Models && mkdir -p /home/u138256737/public_html/app/Views/service && mkdir -p /home/u138256737/public_html/app/Views/warehouse && mkdir -p /home/u138256737/public_html/app/Views/marketing && mkdir -p /home/u138256737/public_html/app/Views/components && mkdir -p /home/u138256737/public_html/app/Views/layouts && mkdir -p /home/u138256737/public_html/app/Config && echo 'Folders created successfully!'"

echo.
echo Done! Now run: upload_to_production.bat
pause
