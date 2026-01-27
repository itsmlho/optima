# Panduan Implementasi Sistem SILO

## ✅ Status Implementasi

Semua komponen utama sistem SILO telah selesai diimplementasikan:

- ✅ Database Migration (tabel `silo` dan `silo_history`)
- ✅ Model `SiloModel` dengan method-method lengkap
- ✅ Controller `Perizinan` dengan semua endpoint
- ✅ View halaman utama dengan tabs dan statistik
- ✅ Form modal untuk create dan update
- ✅ Upload file handler untuk dokumen PJK3 dan SILO
- ✅ Detail view dengan timeline
- ✅ Filter, search, dan alert expired
- ✅ Routes configuration

---

## 🚀 Cara Menjalankan

### 1. Jalankan Database Migration

```bash
# Via CodeIgniter CLI
php spark migrate

# Atau via phpMyAdmin/MySQL CLI
# Import file: app/Database/Migrations/2025_01_20_100000_CreateSiloTables.php
# Atau jalankan SQL secara manual
```

### 2. Pastikan Folder Uploads Ada

Folder sudah dibuat otomatis di:
- `public/uploads/silo/pjk3/` (untuk file dari PJK3)
- `public/uploads/silo/silo/` (untuk file SILO)

Pastikan permission folder:
```bash
chmod -R 777 public/uploads/silo/
```

### 3. Akses Halaman

URL: `http://localhost/optima1/public/perizinan/silo`

---

## 📋 Fitur yang Tersedia

### 1. Dashboard Statistik
- Card statistik: Sudah Ada SILO, Progres, Belum Ada SILO
- Alert untuk SILO yang akan expired (30 hari)

### 2. Daftar SILO
- Tabs: Semua, Sudah Ada SILO, Progres, Belum Ada SILO
- Search: Unit, Serial Number, Nomor SILO
- Filter: Status
- DataTable dengan pagination

### 3. Buat Pengajuan SILO
- Pilih unit (hanya yang belum ada SILO aktif)
- Input tanggal pengajuan dan catatan
- Auto-set status ke "PENGAJUAN_PJK3"

### 4. Update Status
- Workflow-based (hanya bisa maju ke status berikutnya)
- Upload file sesuai tahap:
  - Surat Keterangan PJK3 → Upload file PJK3
  - SILO Terbit → Upload file SILO
- Preview file sebelum submit

### 5. Detail & Timeline
- Informasi lengkap unit dan SILO
- Timeline proses dari awal hingga selesai
- Download file dokumen

### 6. Download File
- Download file PJK3
- Download file SILO

---

## 🔧 Konfigurasi

### Permission yang Diperlukan

User harus memiliki permission:
- `perizinan.access` - untuk melihat halaman dan data
- `perizinan.manage` - untuk create, update, dan upload file

### Validasi File Upload

- Format: PDF, JPG, JPEG, PNG
- Max size: 5MB per file
- File disimpan di: `public/uploads/silo/[pjk3|silo]/`

---

## 📊 Struktur Database

### Tabel `silo`
- Menyimpan semua data proses SILO per unit
- Status menggunakan ENUM dengan workflow
- Kolom untuk setiap tahap proses
- File path untuk dokumen

### Tabel `silo_history`
- Tracking perubahan status
- History log untuk audit trail

---

## 🐛 Troubleshooting

### Error: "Access denied"
- Pastikan user memiliki permission `perizinan.access` atau `perizinan.manage`

### Error: "Unit tidak ditemukan"
- Pastikan unit ada di tabel `inventory_unit`
- Pastikan foreign key `unit_id` valid

### Error: "File upload gagal"
- Pastikan folder `public/uploads/silo/` ada dan writable
- Check permission folder: `chmod -R 777 public/uploads/silo/`
- Check max upload size di PHP config

### Error: "Status tidak valid"
- Pastikan status mengikuti workflow yang benar
- Status hanya bisa maju ke tahap berikutnya

---

## 📝 Catatan Penting

1. **Satu Unit = Satu SILO Aktif**
   - Satu unit hanya boleh memiliki 1 SILO aktif
   - Jika ada SILO expired, bisa buat pengajuan baru

2. **Workflow Status**
   - Status hanya bisa maju (tidak bisa mundur)
   - Urutan: BELUM_ADA → PENGAJUAN_PJK3 → TESTING_PJK3 → SURAT_KETERANGAN_PJK3 → PENGAJUAN_UPTD → PROSES_UPTD → SILO_TERBIT

3. **File Upload**
   - File PJK3 diupload saat status SURAT_KETERANGAN_PJK3
   - File SILO diupload saat status SILO_TERBIT
   - File disimpan dengan format: `[type]_[silo_id]_[timestamp].[ext]`

4. **Alert Expired**
   - Alert muncul jika ada SILO yang akan expired dalam 30 hari
   - Badge "Expiring Soon" muncul di table jika < 30 hari
   - Badge "Expired" muncul jika sudah expired

---

## 🎯 Next Steps (Optional)

Fitur yang bisa ditambahkan di masa depan:
- Export to Excel
- Email notification untuk expired
- Reminder untuk follow-up proses
- Chart/grafik trend pengajuan
- Report bulanan/tahunan

---

**Sistem SILO siap digunakan!** 🎉

