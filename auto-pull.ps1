# Script untuk otomatis pull commit baru tanpa konfirmasi
# Usage: .\auto-pull.ps1

Write-Host "Auto-updating repository..." -ForegroundColor Cyan

# Fetch dari remote
git fetch origin

# Cek apakah ada commit baru
$LOCAL = git rev-parse HEAD
$REMOTE = git rev-parse origin/main

if ($LOCAL -eq $REMOTE) {
    Write-Host "Repository sudah up-to-date!" -ForegroundColor Green
} else {
    Write-Host "Ditemukan commit baru, sedang pull..." -ForegroundColor Yellow
    
    # Tampilkan commit yang akan di-pull
    git log --oneline $LOCAL..$REMOTE
    
    # Pull otomatis
    git pull origin main
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Berhasil update!" -ForegroundColor Green
    } else {
        Write-Host "Terjadi error saat pull." -ForegroundColor Red
    }
}
