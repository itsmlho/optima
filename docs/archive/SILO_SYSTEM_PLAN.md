# Rencana Sistem SILO (Surat Izin Layak Operasi)

## 📋 Overview

Sistem SILO adalah modul untuk mengelola surat izin kelayakan operasi unit alat berat/forklift. Surat ini diperlukan oleh customer saat melakukan rental unit.

**URL:** `http://localhost/optima1/public/perizinan/silo`

---

## 🔄 Alur Bisnis (Business Logic)

### **Tahapan Proses SILO:**

```
1. BELUM ADA SILO
   └─> Pilih Unit
       └─> Buat Pengajuan SILO
           └─> Status: "PENGAJUAN_KE_PJK3"

2. PROGRES
   ├─> PENGAJUAN_KE_PJK3
   │   └─> Input: Tanggal Pengajuan, Catatan
   │
   ├─> TESTING_PJK3
   │   └─> Input: Tanggal Testing, Hasil Testing
   │
   ├─> SURAT_KETERANGAN_PJK3
   │   └─> Input: Nomor Surat Keterangan, Tanggal Terbit, Upload PDF PJK3
   │
   ├─> PENGAJUAN_KE_UPTD
   │   └─> Input: Tanggal Pengajuan ke UPTD, Catatan
   │
   └─> PROSES_UPTD
       └─> Input: Tanggal Proses, Catatan

3. SUDAH ADA SILO
   └─> Status: "SILO_TERBIT"
       └─> Input: Nomor SILO, Tanggal Terbit, Tanggal Expired, Upload PDF SILO
```

### **Status SILO:**

| Status | Kode | Deskripsi | Warna Badge |
|--------|------|-----------|-------------|
| **Belum Ada SILO** | `BELUM_ADA` | Unit belum pernah dibuatkan SILO | Red |
| **Pengajuan ke PJK3** | `PENGAJUAN_PJK3` | Sedang mengajukan ke PJK3 untuk testing | Yellow |
| **Testing PJK3** | `TESTING_PJK3` | Unit sedang dalam proses testing oleh PJK3 | Yellow |
| **Surat Keterangan PJK3** | `SURAT_KETERANGAN_PJK3` | Sudah dapat Surat Keterangan dari PJK3 | Blue |
| **Pengajuan ke UPTD** | `PENGAJUAN_UPTD` | Sedang mengajukan ke UPTD Pengawas Ketenagakerjaan | Yellow |
| **Proses UPTD** | `PROSES_UPTD` | Sedang diproses oleh UPTD | Yellow |
| **SILO Terbit** | `SILO_TERBIT` | SILO sudah diterbitkan dan aktif | Green |
| **SILO Expired** | `SILO_EXPIRED` | SILO sudah kadaluarsa | Red |

---

## 🗄️ Struktur Database

### **Tabel: `silo`**

```sql
CREATE TABLE `silo` (
  `id_silo` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK ke inventory_unit.id_inventory_unit',
  `status` ENUM(
    'BELUM_ADA',
    'PENGAJUAN_PJK3',
    'TESTING_PJK3',
    'SURAT_KETERANGAN_PJK3',
    'PENGAJUAN_UPTD',
    'PROSES_UPTD',
    'SILO_TERBIT',
    'SILO_EXPIRED'
  ) NOT NULL DEFAULT 'BELUM_ADA',
  
  -- Data Pengajuan ke PJK3
  `tanggal_pengajuan_pjk3` DATETIME NULL,
  `catatan_pengajuan_pjk3` TEXT NULL,
  
  -- Data Testing PJK3
  `tanggal_testing_pjk3` DATETIME NULL,
  `hasil_testing_pjk3` TEXT NULL,
  
  -- Data Surat Keterangan PJK3
  `nomor_surat_keterangan_pjk3` VARCHAR(100) NULL,
  `tanggal_surat_keterangan_pjk3` DATE NULL,
  `file_surat_keterangan_pjk3` VARCHAR(255) NULL COMMENT 'Path ke file PDF/image',
  
  -- Data Pengajuan ke UPTD
  `tanggal_pengajuan_uptd` DATETIME NULL,
  `catatan_pengajuan_uptd` TEXT NULL,
  
  -- Data Proses UPTD
  `tanggal_proses_uptd` DATETIME NULL,
  `catatan_proses_uptd` TEXT NULL,
  
  -- Data SILO Terbit
  `nomor_silo` VARCHAR(100) NULL,
  `tanggal_terbit_silo` DATE NULL,
  `tanggal_expired_silo` DATE NULL,
  `file_silo` VARCHAR(255) NULL COMMENT 'Path ke file PDF/image',
  
  -- Metadata
  `created_by` INT(11) NULL COMMENT 'FK ke users.id',
  `updated_by` INT(11) NULL COMMENT 'FK ke users.id',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id_silo`),
  INDEX `idx_unit_id` (`unit_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_nomor_silo` (`nomor_silo`),
  INDEX `idx_tanggal_expired` (`tanggal_expired_silo`),
  FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit`(`id_inventory_unit`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Tabel: `silo_history` (Optional - untuk tracking perubahan)**

```sql
CREATE TABLE `silo_history` (
  `id_history` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `silo_id` INT(11) UNSIGNED NOT NULL,
  `status_lama` VARCHAR(50) NULL,
  `status_baru` VARCHAR(50) NOT NULL,
  `keterangan` TEXT NULL,
  `changed_by` INT(11) NULL,
  `changed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id_history`),
  INDEX `idx_silo_id` (`silo_id`),
  FOREIGN KEY (`silo_id`) REFERENCES `silo`(`id_silo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🎨 Tampilan Halaman

### **Layout Utama:**

```
┌─────────────────────────────────────────────────────────────┐
│  SILO Management                                            │
│  [Dashboard] > [Perizinan] > [SILO]                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                 │
│  │ Sudah Ada│  │ Progres  │  │ Belum Ada│                 │
│  │  SILO    │  │          │  │  SILO    │                 │
│  │   15     │  │    8     │  │    12    │                 │
│  └──────────┘  └──────────┘  └──────────┘                 │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  [Tab: Semua] [Tab: Sudah Ada SILO] [Tab: Progres] │   │
│  │  [Tab: Belum Ada SILO]                              │   │
│  ├─────────────────────────────────────────────────────┤   │
│  │                                                     │   │
│  │  [Search] [Filter: Unit] [Filter: Status] [+] New  │   │
│  │                                                     │   │
│  │  ┌───────────────────────────────────────────────┐ │   │
│  │  │ No │ Unit │ Status │ Nomor SILO │ Expired │ │   │
│  │  ├───────────────────────────────────────────────┤ │   │
│  │  │ 1  │ FL-01 │ Terbit │ SILO-2024-001 │ 2025  │ │   │
│  │  │ 2  │ FL-02 │ Progres│ -            │ -      │ │   │
│  │  │ 3  │ FL-03 │ Belum  │ -            │ -      │ │   │
│  │  └───────────────────────────────────────────────┘ │   │
│  │                                                     │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### **Modal Form: Buat Pengajuan SILO**

```
┌────────────────────────────────────────────┐
│  Buat Pengajuan SILO              [X]     │
├────────────────────────────────────────────┤
│                                            │
│  Pilih Unit: [Dropdown Unit ▼]            │
│  * Unit yang belum ada SILO                │
│                                            │
│  Tanggal Pengajuan: [Date Picker]         │
│  Catatan: [Textarea]                       │
│                                            │
│  [Cancel]  [Submit]                        │
│                                            │
└────────────────────────────────────────────┘
```

### **Modal Form: Update Status & Upload Dokumen**

```
┌────────────────────────────────────────────┐
│  Update Status SILO              [X]       │
├────────────────────────────────────────────┤
│                                            │
│  Unit: FL-01                               │
│  Status Saat Ini: Testing PJK3             │
│                                            │
│  ┌──────────────────────────────────────┐ │
│  │ Update ke: [Surat Keterangan PJK3 ▼]│ │
│  └──────────────────────────────────────┘ │
│                                            │
│  Nomor Surat Keterangan: [Input]          │
│  Tanggal Terbit: [Date Picker]            │
│  Upload File PJK3: [Choose File] 📎      │
│  Preview: [PDF/Image Preview]            │
│                                            │
│  Catatan: [Textarea]                       │
│                                            │
│  [Cancel]  [Update]                        │
│                                            │
└────────────────────────────────────────────┘
```

### **Detail View: Timeline Proses**

```
┌────────────────────────────────────────────┐
│  Detail SILO - FL-01              [X]      │
├────────────────────────────────────────────┤
│                                            │
│  Unit: FL-01 (Serial: SN-12345)           │
│  Status: SILO Terbit                       │
│  Nomor SILO: SILO-2024-001                │
│  Terbit: 01 Jan 2024 | Expired: 01 Jan 2025│
│                                            │
│  ┌─ Timeline Proses ───────────────────┐ │
│  │ ✅ Pengajuan ke PJK3                 │ │
│  │   01 Des 2023 - Catatan...           │
│  │                                       │ │
│  │ ✅ Testing PJK3                      │ │
│  │   05 Des 2023 - Hasil...             │ │
│  │                                       │ │
│  │ ✅ Surat Keterangan PJK3             │ │
│  │   10 Des 2023 - No: SK-001           │ │
│  │   📎 [Download PDF]                  │ │
│  │                                       │ │
│  │ ✅ Pengajuan ke UPTD                 │ │
│  │   15 Des 2023 - Catatan...           │ │
│  │                                       │ │
│  │ ✅ Proses UPTD                       │ │
│  │   20 Des 2023 - Catatan...           │ │
│  │                                       │ │
│  │ ✅ SILO Terbit                        │ │
│  │   01 Jan 2024 - No: SILO-2024-001   │ │
│  │   📎 [Download PDF]                  │ │
│  └───────────────────────────────────────┘ │
│                                            │
│  [Edit] [Close]                            │
│                                            │
└────────────────────────────────────────────┘
```

---

## 🔧 Fitur-Fitur

### **1. Dashboard Statistik**
- Card statistik: Sudah Ada SILO, Progres, Belum Ada SILO
- Alert untuk SILO yang akan expired (30 hari, 7 hari, expired)
- Chart trend pengajuan SILO (optional)

### **2. Daftar SILO (Table)**
- Filter berdasarkan:
  - Status
  - Unit (search by serial number, no unit)
  - Tanggal (range)
  - Nomor SILO
- Sort by: Unit, Status, Tanggal Terbit, Tanggal Expired
- Export to Excel (optional)
- Pagination

### **3. Buat Pengajuan SILO**
- Dropdown pilih unit (hanya unit yang belum ada SILO)
- Validasi: 1 unit = 1 SILO aktif
- Auto-set status ke "PENGAJUAN_PJK3"

### **4. Update Status & Upload Dokumen**
- Workflow-based update (hanya bisa update ke status berikutnya)
- Upload file PDF/image untuk:
  - Surat Keterangan PJK3
  - SILO
- Validasi file:
  - Format: PDF, JPG, JPEG, PNG
  - Max size: 5MB
- Preview file sebelum submit

### **5. Detail & Timeline**
- Tampilkan semua tahapan proses
- Download file dokumen
- History perubahan status

### **6. Notifikasi & Alert**
- Alert untuk SILO yang akan expired
- Notifikasi saat status berubah
- Reminder untuk follow-up proses

### **7. Search & Filter**
- Search by: Nomor Unit, Serial Number, Nomor SILO
- Filter by: Status, Tanggal Terbit, Tanggal Expired
- Quick filter: Expiring Soon (30 hari), Expired

---

## 📁 Struktur File

```
app/
├── Controllers/
│   └── Perizinan.php (update dengan method-method baru)
│
├── Models/
│   ├── SiloModel.php (baru)
│   └── SiloHistoryModel.php (baru, optional)
│
├── Views/
│   └── perizinan/
│       ├── silo.php (update - halaman utama)
│       ├── silo_form.php (baru - modal form)
│       └── silo_detail.php (baru - detail view)
│
public/
└── uploads/
    └── silo/
        ├── pjk3/ (folder untuk file PJK3)
        └── silo/ (folder untuk file SILO)
        └── .htaccess (security)

databases/
└── migrations/
    └── create_silo_tables.sql (baru)
```

---

## 🔐 Validasi & Business Rules

### **1. Validasi Unit**
- Satu unit hanya boleh memiliki 1 SILO aktif
- Jika ada SILO expired, bisa buat pengajuan baru
- Unit harus ada di `inventory_unit`

### **2. Validasi Status Workflow**
- Status hanya bisa maju ke tahap berikutnya
- Tidak bisa mundur ke status sebelumnya
- Status "SILO_TERBIT" adalah final (kecuali expired)

### **3. Validasi File Upload**
- Format: PDF, JPG, JPEG, PNG
- Max size: 5MB per file
- Nama file: `[type]_[unit_id]_[timestamp].[ext]`
  - Contoh: `pjk3_123_1704067200.pdf`
  - Contoh: `silo_123_1704067200.pdf`

### **4. Validasi Tanggal**
- Tanggal expired harus > tanggal terbit
- Tanggal pengajuan tidak boleh di masa depan (optional)
- Alert jika tanggal expired < 30 hari

---

## 🎯 Prioritas Implementasi

### **Phase 1: Core Functionality**
1. ✅ Database schema (tabel `silo`)
2. ✅ Model `SiloModel`
3. ✅ Controller methods (CRUD dasar)
4. ✅ View halaman utama dengan tabs
5. ✅ Form buat pengajuan SILO
6. ✅ Table list dengan filter

### **Phase 2: Workflow & Upload**
1. ✅ Update status workflow
2. ✅ Upload file PJK3
3. ✅ Upload file SILO
4. ✅ Preview file
5. ✅ Download file

### **Phase 3: Advanced Features**
1. ✅ Detail view dengan timeline
2. ✅ History tracking
3. ✅ Alert expired
4. ✅ Export Excel
5. ✅ Notifikasi

---

## 📝 Catatan Teknis

### **File Upload Storage:**
```
public/uploads/silo/
├── pjk3/
│   ├── pjk3_123_1704067200.pdf
│   └── pjk3_124_1704067300.jpg
└── silo/
    ├── silo_123_1704067500.pdf
    └── silo_124_1704067600.png
```

### **Security:**
- `.htaccess` di folder uploads untuk prevent PHP execution
- Validasi file type di backend
- Sanitize filename
- Check file size

### **Performance:**
- Index pada `unit_id`, `status`, `nomor_silo`
- Lazy load untuk file preview
- Pagination untuk table

---

## ✅ Checklist Implementasi

- [ ] Database migration
- [ ] Model `SiloModel`
- [ ] Controller methods
- [ ] View halaman utama
- [ ] Form buat pengajuan
- [ ] Form update status
- [ ] Upload file handler
- [ ] File preview
- [ ] Download file
- [ ] Detail view dengan timeline
- [ ] Filter & search
- [ ] Alert expired
- [ ] Testing

---

**Dokumen ini akan menjadi panduan untuk implementasi sistem SILO yang lengkap dan sesuai dengan kebutuhan bisnis.**

