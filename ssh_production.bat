@echo off
REM Auto-login SSH to Production Server
REM Server: optima.sml.co.id (147.93.80.45:65002)
REM User: u138256737

echo ================================================
echo  CONNECTING TO PRODUCTION SERVER
echo  optima.sml.co.id (147.93.80.45:65002)
echo ================================================
echo.
echo Password: @ITSupport25
echo.

REM Using plink (PuTTY) for auto-login
REM Install PuTTY if not available: winget install PuTTY.PuTTY

plink -ssh u138256737@147.93.80.45 -P 65002 -pw @ITSupport25 -t "cd /home/u138256737/domains/sml.co.id/public_html/optima && bash"
