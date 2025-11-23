# Script untuk mengecek dan mengambil commit baru dari remote repository
# Usage: .\check-updates.ps1

Write-Host "Memeriksa commit baru dari remote..." -ForegroundColor Cyan

# Fetch dari remote untuk melihat commit baru
git fetch origin

# Cek apakah ada commit baru
$LOCAL = git rev-parse HEAD
$REMOTE = git rev-parse origin/main

if ($LOCAL -eq $REMOTE) {
    Write-Host "Repository sudah up-to-date!" -ForegroundColor Green
    Write-Host "Commit terbaru: $LOCAL" -ForegroundColor Gray
} else {
    Write-Host "Ditemukan commit baru!" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Commit baru yang akan di-pull:" -ForegroundColor Cyan
    git log --oneline $LOCAL..$REMOTE
    
    Write-Host ""
    $response = Read-Host "Apakah Anda ingin pull commit baru ini? (y/n)"
    
    if ($response -eq 'y' -or $response -eq 'Y') {
        Write-Host ""
        Write-Host "Mengambil commit baru..." -ForegroundColor Cyan
        git pull origin main
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host ""
            Write-Host "Berhasil update! Repository sekarang up-to-date." -ForegroundColor Green
        } else {
            Write-Host ""
            Write-Host "Terjadi error saat pull. Silakan periksa secara manual." -ForegroundColor Red
        }
    } else {
        Write-Host "Pull dibatalkan." -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "Status repository:" -ForegroundColor Cyan
git status
