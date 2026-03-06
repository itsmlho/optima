# PowerShell Script: Fix Stored Procedures in SQL Dump
# Purpose: Replace k.customer_location_id references with updated queries
# Created: 2026-03-06

Write-Host "=========================================="  -ForegroundColor Cyan
Write-Host "  ALL-IN-ONE PRODUCTION SQL FIXER" -ForegroundColor Cyan
Write-Host "=========================================="-ForegroundColor Cyan
Write-Host ""

$inputFile = "PRODUCTION_IMPORT_READY_v2.sql"
$outputFile = "PRODUCTION_IMPORT_READY_FINAL.sql"

if (-not (Test-Path $inputFile)) {
    Write-Host "[ERROR] File not found: $inputFile" -ForegroundColor Red
    exit 1
}

Write-Host "[1/5] Reading SQL file..." -ForegroundColor Yellow
$content = Get-Content $inputFile -Raw
$originalSize = (Get-Item $inputFile).Length / 1MB

Write-Host "[2/5] Fixing stored procedure: auto_assign_employees_to_work_order..." -ForegroundColor Yellow
# OLD: JOIN kontrak k ON iu.kontrak_id = k.id
#      JOIN customer_locations cl ON k.customer_location_id = cl.id
# NEW: JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id
#      JOIN customer_locations cl ON ku.customer_location_id = cl.id

$content = $content -replace `
    '(?ms)JOIN kontrak k ON iu\.kontrak_id = k\.id\s+JOIN customer_locations cl ON k\.customer_location_id = cl\.id', `
    "JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id`r`n    JOIN customer_locations cl ON ku.customer_location_id = cl.id"

Write-Host "[3/5] Fixing stored procedure: auto_fill_accessories..." -ForegroundColor Yellow
# OLD: LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
#      LEFT JOIN customers c ON cl.customer_id = c.id
# NEW: JOIN customers c ON k.customer_id = c.id

$content = $content -replace `
    '(?ms)LEFT JOIN customer_locations cl ON k\.customer_location_id = cl\.id\s+LEFT JOIN customers c ON cl\.customer_id = c\.id', `
    "JOIN customers c ON k.customer_id = c.id"

Write-Host "      Fixing inventory_unit trigger..." -ForegroundColor Yellow
# Fix trigger that sets lokasi_unit from kontrak
# OLD: FROM kontrak k JOIN customer_locations cl ON k.customer_location_id = cl.id
# NEW: FROM kontrak_unit ku JOIN customer_locations cl ON ku.customer_location_id = cl.id
$content = $content -replace `
    '(?ms)FROM kontrak k\s+JOIN customer_locations cl ON k\.customer_location_id = cl\.id\s+WHERE k\.id = v_kontrak_id', `
    "FROM kontrak_unit ku`r`n                JOIN customer_locations cl ON ku.customer_location_id = cl.id`r`n                WHERE ku.kontrak_id = v_kontrak_id AND ku.unit_id = NEW.id_inventory_unit"

Write-Host "      Removing obsolete VIEW: contract_unit_summary..." -ForegroundColor Yellow
# Remove the entire contract_unit_summary VIEW (uses old schema with k.customer_location_id)
$content = $content -replace `
    '(?ms)--\r?\nDROP TABLE IF EXISTS `contract_unit_summary`;.*?CREATE ALGORITHM=UNDEFINED VIEW `contract_unit_summary`.*?;', `
    "-- VIEW contract_unit_summary removed (obsolete - used kontrak.customer_location_id which no longer exists)"

Write-Host "      Fixing VIEW: vw_unit_with_contracts..." -ForegroundColor Yellow
# This VIEW is important for backward compatibility (Phase 1A migration)
# Fix ALL references to k.customer_location_id -> ku.customer_location_id in ALL VIEWs
# This affects: vw_unit_with_contracts, vw_work_orders_detail, v_amendment_summary, 
#               v_customer_activity, v_spk_persiapan_unit, etc.

Write-Host "      Replacing ALL k.customer_location_id references with ku.customer_location_id..." -ForegroundColor Yellow

# Simple string replacement (no regex) - more reliable
$content = $content.Replace('`k`.`customer_location_id`', '`ku`.`customer_location_id`')
$content = $content.Replace('(`k`.`customer_location_id` = `cl`.`id`)', '(`ku`.`customer_location_id` = `cl`.`id`)')
$content = $content.Replace('((`k`.`customer_location_id` = `cl`.`id`))', '((`ku`.`customer_location_id` = `cl`.`id`))')
$content = $content.Replace('ON k.customer_location_id = cl.id', 'ON ku.customer_location_id = cl.id')

Write-Host "      Fixing VIEWs without kontrak_unit JOIN..." -ForegroundColor Yellow
# Some VIEWs don't have kontrak_unit joined, so they can't use ku.customer_location_id
# Fix them to use k.customer_id directly instead

# v_amendment_summary: Remove customer_locations join entirely, use customers directly
$content = $content.Replace(
    '((`contract_amendments` `ca` left join `kontrak` `k` on((`k`.`id` = `ca`.`contract_id`))) left join `customer_locations` `cl` on((`cl`.`id` = `ku`.`customer_location_id`))) left join `customers` `c` on((`c`.`id` = `cl`.`customer_id`))',
    '((`contract_amendments` `ca` left join `kontrak` `k` on((`k`.`id` = `ca`.`contract_id`))) left join `customers` `c` on((`c`.`id` = `k`.`customer_id`))'
)

# v_customer_activity: Fix JOIN order - kontrak should join on customer.id, not location
$content = $content.Replace(
    '(((`customers` `c` left join `customer_locations` `cl` on((`c`.`id` = `cl`.`customer_id`))) left join `kontrak` `k` on((`cl`.`id` = `ku`.`customer_location_id`))',
    '(((`customers` `c` left join `customer_locations` `cl` on((`c`.`id` = `cl`.`customer_id`))) left join `kontrak` `k` on((`c`.`id` = `k`.`customer_id`))'
)

Write-Host "[4/5] Removing DEFINER statements & fixing database name..." -ForegroundColor Yellow
# Remove all DEFINER clauses
$content = $content -replace 'DEFINER=`[^`]+`@`[^`]+`\s*', ''

# Change database name
$content = $content -replace '`optima_ci`', '`u138256737_optima_db`'
$content = $content -replace 'optima_ci', 'u138256737_optima_db'

# Add production safety header
$safetyHeader = @"
-- PRODUCTION-READY SQL DUMP
-- Database: u138256737_optima_db
-- Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
-- Fixed: Stored procedures updated for new schema (kontrak_unit.customer_location_id)

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

DROP DATABASE IF EXISTS ``u138256737_optima_db``;
CREATE DATABASE ``u138256737_optima_db`` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ``u138256737_optima_db``;

"@

$content = $safetyHeader + $content

# Add footer
$content = $content + "`r`n`r`nSET FOREIGN_KEY_CHECKS=1;`r`nCOMMIT;`r`n"

Write-Host "      Removing deprecated sync procedure code..." -ForegroundColor Yellow
# Remove the lokasi_unit sync section in sync_unit_denormalized_fields
$content = $content -replace `
    '(?ms)UPDATE inventory_unit iu\s+JOIN kontrak k ON iu\.kontrak_id = k\.id\s+LEFT JOIN customer_locations cl ON k\.customer_location_id = cl\.id\s+LEFT JOIN customers c ON cl\.customer_id = c\.id\s+SET iu\.lokasi_unit = c\.customer_name,\s+iu\.updated_at = CURRENT_TIMESTAMP\s+WHERE iu\.kontrak_id IS NOT NULL\s+AND \(iu\.lokasi_unit IS NULL OR iu\.lokasi_unit != c\.customer_name\);', `
    "-- Removed: lokasi_unit sync (kontrak.customer_location_id no longer exists)`r`n    -- Note: Consider syncing from kontrak_unit.customer_location_id if needed"

Write-Host "      Writing fixed SQL file..." -ForegroundColor Yellow
$content | Out-File -FilePath $outputFile -Encoding UTF8 -NoNewline

$finalSize = (Get-Item $outputFile).Length / 1MB

Write-Host ""
Write-Host "==========================================" -ForegroundColor  Green
Write-Host "  COMPLETED SUCCESSFULLY" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""
Write-Host "File created: $outputFile" -ForegroundColor Cyan
Write-Host "Original size: $($originalSize.ToString('F2')) MB" -ForegroundColor White
Write-Host "Final size: $($finalSize.ToString('F2')) MB" -ForegroundColor White
Write-Host ""
Write-Host "What was fixed:" -ForegroundColor Yellow
Write-Host "1. auto_assign_employees_to_work_order - Uses kontrak_unit.customer_location_id" -ForegroundColor White
Write-Host "2. auto_fill_accessories - Uses kontrak.customer_id directly" -ForegroundColor White
Write-Host "3. sync_unit_denormalized_fields - Removed obsolete code" -ForegroundColor White
Write-Host "4. inventory_unit trigger - Uses kontrak_unit.customer_location_id" -ForegroundColor White
Write-Host "5. contract_unit_summary VIEW - REMOVED (obsolete)" -ForegroundColor White
Write-Host "6. ALL VIEWs - k.customer_location_id replaced with ku.customer_location_id" -ForegroundColor White
Write-Host "7. All DEFINER statements removed" -ForegroundColor White
Write-Host "8. Database name changed to u138256737_optima_db" -ForegroundColor White
Write-Host "9. Foreign key checks disabled/re-enabled" -ForegroundColor White
Write-Host ""
Write-Host "Ready to upload to production!" -ForegroundColor Green
Write-Host ""
