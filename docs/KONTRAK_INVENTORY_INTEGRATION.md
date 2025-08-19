# Implementasi Hubungan Kontrak dengan Unit Inventory

## Overview
Implementasi ini menghubungkan tabel `kontrak` dengan unit-unit yang ada di warehouse/inventory sistem. Field `total_units` pada tabel kontrak akan menyimpan total unit yang terkait dengan kontrak tersebut.

## Database Schema Changes

### Tabel Kontrak - Kolom Baru
```sql
-- Kolom yang ditambahkan ke tabel kontrak
ALTER TABLE `kontrak` 
ADD COLUMN `pic` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nama Person In Charge' AFTER `lokasi`,
ADD COLUMN `kontak` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Kontak PIC (telepon/email)' AFTER `pic`,
ADD COLUMN `nilai_total` DECIMAL(15,2) NULL DEFAULT NULL COMMENT 'Nilai total kontrak dalam rupiah' AFTER `kontak`,
ADD COLUMN `total_units` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total unit yang terkait dengan kontrak ini' AFTER `nilai_total`;
```

### Hubungan dengan Inventory Unit
Untuk menghubungkan dengan unit inventory, Anda perlu:

1. **Tabel Junction** (opsional, jika ingin relasi many-to-many):
```sql
CREATE TABLE `kontrak_unit` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kontrak_id` INT UNSIGNED NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL, -- referensi ke tabel inventory/unit
    `tanggal_assign` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('Aktif', 'Selesai', 'Dibatalkan') DEFAULT 'Aktif',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_kontrak_unit` (`kontrak_id`, `unit_id`),
    FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`unit_id`) REFERENCES `invent_unit` (`id`) ON DELETE CASCADE
);
```

2. **Atau Update Unit Table** (jika relasi one-to-many):
```sql
-- Jika setiap unit hanya bisa terkait dengan satu kontrak
ALTER TABLE `invent_unit` 
ADD COLUMN `kontrak_id` INT UNSIGNED NULL DEFAULT NULL,
ADD FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL;
```

## Backend Implementation

### Model - KontrakModel.php
- ✅ Ditambahkan `pic`, `kontak`, `nilai_total`, `total_units` ke `allowedFields`
- ✅ Validasi untuk semua field sudah ada

### Controller - Kontrak.php
- ✅ Mapping `total_units` di method `store()` dan `update()`
- ✅ Response DataTable sudah include semua field baru

### Views - kontrak.php
- ✅ Form input untuk semua field baru
- ✅ Population field saat edit
- ✅ Display di detail modal

## Frontend Implementation

### Form Fields
```html
<!-- Field Total Unit -->
<div class="col-md-6 mb-3">
    <label class="form-label">Total Unit</label>
    <input type="number" class="form-control" name="total_units" min="0" value="0">
</div>
```

### DataTable
- Kolom `total_units` sudah ditampilkan di tabel
- Field `pic` dan `kontak` hanya ditampilkan di detail modal

## Cara Menjalankan Migration

### Option 1: Manual SQL
```bash
# Jalankan file SQL yang sudah dibuat
mysql -u username -p database_name < databases/add_kontrak_fields.sql
```

### Option 2: CodeIgniter Migration
```bash
# Jalankan migration via CLI
php spark migrate
```

## Next Steps untuk Inventory Integration

1. **Identifikasi Tabel Inventory**: 
   - Pastikan nama tabel unit inventory (misal: `invent_unit`)
   - Identifikasi primary key dan field yang relevan

2. **Buat Relasi**:
   - Pilih antara junction table atau foreign key langsung
   - Implement di model dan controller

3. **Update Logic Total Units**:
   ```php
   // Contoh function untuk update total_units otomatis
   public function updateTotalUnits($kontrakId) {
       $totalUnits = $this->db->query("
           SELECT COUNT(*) as total 
           FROM invent_unit 
           WHERE kontrak_id = ?", [$kontrakId]
       )->getRow()->total;
       
       $this->kontrakModel->update($kontrakId, ['total_units' => $totalUnits]);
   }
   ```

4. **UI untuk Assign Units**:
   - Buat interface untuk assign/unassign unit ke kontrak
   - Integrate dengan existing inventory system

## File Structure Update

```
app/
├── Controllers/
│   └── Kontrak.php ✅ Updated
├── Models/
│   └── KontrakModel.php ✅ Updated
├── Views/marketing/
│   └── kontrak.php ✅ Updated
└── Database/Migrations/
    └── 2025-08-19-120000_AddKontrakFields.php ✅ Created

databases/
└── add_kontrak_fields.sql ✅ Created
```

## Testing Checklist

- [ ] Migration berhasil dijalankan
- [ ] Form kontrak bisa create dengan field baru
- [ ] Form kontrak bisa edit dan populate field baru
- [ ] DataTable menampilkan kolom yang benar
- [ ] Detail modal menampilkan semua informasi
- [ ] Validasi field bekerja dengan baik
