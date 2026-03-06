@echo off
REM ============================================
REM Fix SQL Dump for Production Import
REM ============================================

echo Preparing SQL file for production...

powershell.exe -ExecutionPolicy Bypass -Command "& {
    Write-Host 'Reading SQL file...' -ForegroundColor Yellow
    $content = Get-Content 'databases\optima_ci (4).sql' -Raw -Encoding UTF8
    
    Write-Host 'Removing DEFINER statements...' -ForegroundColor Yellow
    # Remove DEFINER=`root`@`localhost` from procedures/functions  
    $content = $content -replace 'DEFINER=``root``@``localhost``\s+', ''
    
    # Remove SQL SECURITY DEFINER from views
    $content = $content -replace '\s+SQL SECURITY DEFINER\s+', ' '
    
    Write-Host 'Changing database name...' -ForegroundColor Yellow
    $content = $content -replace '``optima_ci``', '``u138256737_optima_db``'
    $content = $content -replace 'Database: ``optima_ci``', 'Database: ``u138256737_optima_db``'
    
    Write-Host 'Adding production safety headers...' -ForegroundColor Yellow
    $header = @'
-- ================================================================
-- PRODUCTION-READY SQL DUMP
-- Database: u138256737_optima_db
-- Modified for production compatibility
-- Date: 
'@ + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss') + @'

-- ================================================================

-- Disable foreign key checks for import
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Drop and recreate database
DROP DATABASE IF EXISTS ``u138256737_optima_db``;
CREATE DATABASE ``u138256737_optima_db`` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ``u138256737_optima_db``;

'@
    
    $footer = @'

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
'@
    
    # Remove duplicate headers from original dump
    $content = $content -replace '(?s)^-- phpMyAdmin.*?(?=DELIMITER )', ''
    
    # Assemble final content
    $fullContent = $header + $content + $footer
    
    Write-Host 'Writing production-ready SQL file...' -ForegroundColor Yellow
    Set-Content 'databases\PRODUCTION_IMPORT_READY.sql' -Value $fullContent -Encoding UTF8
    
    Write-Host ''
    Write-Host '✅ SUCCESS! File created: databases\PRODUCTION_IMPORT_READY.sql' -ForegroundColor Green
    Write-Host ''
    
    # Show file size
    $fileInfo = Get-Item 'databases\PRODUCTION_IMPORT_READY.sql'
    $sizeMB = [math]::Round($fileInfo.Length / 1MB, 2)
    Write-Host \"File size: $sizeMB MB\" -ForegroundColor Cyan
    Write-Host ''
}"

echo.
echo ============================================
echo   FILE READY FOR PRODUCTION IMPORT!
echo ============================================
echo.
echo File: databases\PRODUCTION_IMPORT_READY.sql
echo.
echo NEXT STEPS:
echo 1. Open phpMyAdmin: https://auth-db1866.hstgr.io
echo 2. Login: u138256737 / @ITSupport25
echo 3. Click "Import" tab
echo 4. Choose file: PRODUCTION_IMPORT_READY.sql
echo 5. Click "Go"
echo 6. Wait for import to complete (~2-5 minutes)
echo.
echo ============================================
pause
