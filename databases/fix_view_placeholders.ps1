# PowerShell Script: Fix VIEW Placeholder Syntax Errors
# Purpose: Remove empty CREATE TABLE placeholders for VIEWs from SQL dump
# Created: 2026-03-06

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  FIX VIEW PLACEHOLDER SYNTAX ERRORS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

$inputFile = "PRODUCTION_IMPORT_READY.sql"
$outputFile = "PRODUCTION_IMPORT_READY_v2.sql"

# Check if input file exists
if (-not (Test-Path $inputFile)) {
    Write-Host "[ERROR] File not found: $inputFile" -ForegroundColor Red
    exit 1
}

Write-Host "[1/4] Reading SQL file..." -ForegroundColor Yellow
$content = Get-Content $inputFile -Raw
$originalSize = (Get-Item $inputFile).Length / 1MB

Write-Host "[2/4] Finding VIEW placeholders..." -ForegroundColor Yellow
$placeholderPattern = '(?m)^-- Stand-in structure for view.*?\r?\n-- \(See below for the actual view\).*?\r?\n--\r?\nCREATE TABLE `[^`]+` \(\r?\n\);'
$matches = [regex]::Matches($content, $placeholderPattern)
$placeholderCount = $matches.Count

Write-Host "      Found $placeholderCount VIEW placeholders" -ForegroundColor Green

Write-Host "[3/4] Removing VIEW placeholders..." -ForegroundColor Yellow
$content = [regex]::Replace($content, $placeholderPattern, '')

Write-Host "[4/4] Writing fixed SQL file..." -ForegroundColor Yellow
$content | Out-File -FilePath $outputFile -Encoding UTF8 -NoNewline

$finalSize = (Get-Item $outputFile).Length / 1MB

Write-Host ""
Write-Host "==========================================" -ForegroundColor Green
Write-Host "  COMPLETED SUCCESSFULLY" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""
Write-Host "File created: $outputFile" -ForegroundColor Cyan
Write-Host "Original size: $($originalSize.ToString('F2')) MB" -ForegroundColor White
Write-Host "Final size: $($finalSize.ToString('F2')) MB" -ForegroundColor White
Write-Host "Placeholders removed: $placeholderCount" -ForegroundColor White
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Upload '$outputFile' to phpMyAdmin" -ForegroundColor White
Write-Host "2. Go to Import tab" -ForegroundColor White
Write-Host "3. Select the file and click 'Import'" -ForegroundColor White
Write-Host "4. Verify import success" -ForegroundColor White
Write-Host ""
