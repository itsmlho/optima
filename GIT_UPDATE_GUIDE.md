# Panduan Update Repository Otomatis

## Masalah
Setelah commit di Linux, perubahan tidak muncul otomatis di Windows karena perlu melakukan `git fetch` dan `git pull` secara manual.

## Solusi yang Tersedia

### 1. Script PowerShell Manual (Disarankan)

#### `check-updates.ps1` - Dengan Konfirmasi
Script ini akan:
- Mengecek commit baru dari remote
- Menampilkan daftar commit yang akan di-pull
- Meminta konfirmasi sebelum pull

**Cara pakai:**
```powershell
.\check-updates.ps1
```

#### `auto-pull.ps1` - Otomatis tanpa Konfirmasi
Script ini akan:
- Otomatis pull commit baru tanpa menunggu konfirmasi
- Berguna untuk automation atau shortcut

**Cara pakai:**
```powershell
.\auto-pull.ps1
```

### 2. Menggunakan Git Hook (Otomatis)
Setelah pull/merge, hook akan otomatis memberitahu commit terbaru.

### 3. Mengaktifkan Auto-Fetch di VS Code/Cursor

#### Cara 1: Melalui Settings UI
1. Buka Settings (Ctrl+,)
2. Cari "git.autofetch"
3. Aktifkan "Git: Auto Fetch"
4. Set interval (default: 180 detik)

#### Cara 2: Melalui settings.json
Tambahkan ini ke `.vscode/settings.json`:
```json
{
  "git.autofetch": true,
  "git.autofetchPeriod": 180
}
```

### 4. Membuat Shortcut/Task Scheduler (Advanced)

Anda bisa membuat Task Scheduler di Windows untuk menjalankan `auto-pull.ps1` secara berkala:

1. Buka Task Scheduler
2. Create Basic Task
3. Set trigger (misalnya setiap 30 menit)
4. Action: Start a program
5. Program: `powershell.exe`
6. Arguments: `-ExecutionPolicy Bypass -File "C:\xampp\htdocs\optima\auto-pull.ps1"`

### 5. Alias Git (Quick Command)

Tambahkan alias untuk cepat cek update:
```powershell
git config --global alias.check-updates '!git fetch origin && git log HEAD..origin/main --oneline'
```

Lalu jalankan:
```powershell
git check-updates
```

## Tips
- Jalankan `.\check-updates.ps1` setiap kali membuka proyek
- Aktifkan auto-fetch di VS Code/Cursor untuk notifikasi otomatis
- Commit di Linux: pastikan sudah `git push` ke remote
- Di Windows: jalankan script untuk pull perubahan

## Troubleshooting
- Jika script tidak bisa dijalankan: `Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser`
- Jika ada conflict: selesaikan conflict manual lalu commit
- Jika remote tidak ditemukan: pastikan remote URL benar dengan `git remote -v`

