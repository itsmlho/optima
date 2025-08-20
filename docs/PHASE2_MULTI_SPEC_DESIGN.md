# Phase 2: Enhanced Kontrak-SPK Workflow

## Skenario Multi-Spesifikasi
Kontrak dapat memiliki multiple spesifikasi dengan jumlah unit berbeda per spek.
Setiap SPK dibuat untuk 1 spesifikasi dengan multiple units.

## Database Schema Enhancement

### 1. Tabel `kontrak_spesifikasi` (NEW)
```sql
CREATE TABLE `kontrak_spesifikasi` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kontrak_id` INT UNSIGNED NOT NULL,
    `spek_kode` VARCHAR(50) NOT NULL COMMENT 'Kode unik spesifikasi dalam kontrak (A, B, C)',
    `jumlah_dibutuhkan` INT NOT NULL DEFAULT 1,
    `harga_per_unit_bulanan` DECIMAL(15,2) DEFAULT NULL,
    `harga_per_unit_harian` DECIMAL(15,2) DEFAULT NULL,
    `catatan_spek` TEXT DEFAULT NULL,
    
    -- Spesifikasi Detail
    `departemen_id` INT DEFAULT NULL,
    `tipe_unit_id` INT DEFAULT NULL,
    `kapasitas_id` INT DEFAULT NULL,
    `merk_unit` VARCHAR(100) DEFAULT NULL,
    `model_unit` VARCHAR(100) DEFAULT NULL,
    `attachment_tipe` VARCHAR(100) DEFAULT NULL,
    `jenis_baterai` VARCHAR(100) DEFAULT NULL,
    `aksesoris` JSON DEFAULT NULL,
    
    `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_kontrak_spek` (`kontrak_id`, `spek_kode`),
    FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL,
    FOREIGN KEY (`tipe_unit_id`) REFERENCES `tipe_unit` (`id_tipe_unit`) ON DELETE SET NULL,
    FOREIGN KEY (`kapasitas_id`) REFERENCES `kapasitas` (`id_kapasitas`) ON DELETE SET NULL
);
```

### 2. Update Tabel `spk`
```sql
-- Tambah reference ke kontrak_spesifikasi
ALTER TABLE `spk` 
ADD COLUMN `kontrak_spesifikasi_id` INT UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi' AFTER `kontrak_id`,
ADD COLUMN `jumlah_unit` INT DEFAULT 1 COMMENT 'Jumlah unit dalam SPK ini' AFTER `kontrak_spesifikasi_id`,
ADD FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL;
```

### 3. Update Tabel `inventory_unit`
```sql
-- Tambah reference ke kontrak_spesifikasi untuk tracking
ALTER TABLE `inventory_unit`
ADD COLUMN `kontrak_spesifikasi_id` INT UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi' AFTER `kontrak_id`,
ADD FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL;
```

## UI Workflow Enhancement

### Phase 2A: Kontrak Creation Flow
```
[Buat Kontrak] 
├── Tab 1: Basic Info (nama, tanggal, jenis_sewa)
├── Tab 2: Spesifikasi Units
│   ├── [+ Tambah Spesifikasi]
│   ├── Spek A: 2 unit @ 8jt/bulan (Diesel 3T)
│   ├── Spek B: 3 unit @ 10jt/bulan (Electric 5T) 
│   └── Spek C: 5 unit @ 12jt/bulan (Diesel 7T + Attachment)
├── Tab 3: Assign Units (per spesifikasi)
│   ├── Spek A: [Select 2 units from available Diesel 3T]
│   ├── Spek B: [Select 3 units from available Electric 5T]
│   └── Spek C: [Select 5 units from available Diesel 7T]
└── Tab 4: Review & Save
    ├── Total: 10 units
    ├── Nilai Total: 98jt/bulan 
    └── [Simpan Kontrak]
```

### Phase 2B: SPK Creation Flow
```
[Buat SPK]
├── Pilih Kontrak: "Kontrak A"
├── Pilih Spesifikasi: 
│   ├── ○ Spek A (2 unit Diesel 3T) - Available
│   ├── ○ Spek B (3 unit Electric 5T) - Available  
│   └── ○ Spek C (5 unit Diesel 7T) - Available
├── Auto-fill:
│   ├── Jumlah Unit: 2
│   ├── Units: [Unit-001, Unit-002] (pre-selected)
│   └── Spesifikasi: Auto-populated dari kontrak_spesifikasi
└── [Buat SPK] → SPK-001 untuk Spek A
```

## Implementation Plan

### Backend Updates Needed:
1. **KontrakSpesifikasiModel** (new)
2. **Kontrak Controller** - enhanced with spesifikasi management
3. **SPK Controller** - simplified with pre-selected units
4. **InventoryUnit Model** - add new fields

### Frontend Updates Needed:
1. **Kontrak Modal** - multi-tab with spesifikasi management
2. **SPK Modal** - simplified with spek selection
3. **JavaScript** - dynamic spesifikasi CRUD

## Benefits:
- ✅ Flexible multi-spec contracts
- ✅ Accurate pricing per specification  
- ✅ Clear SPK-to-specification mapping
- ✅ Better inventory tracking
- ✅ Simplified SPK creation
