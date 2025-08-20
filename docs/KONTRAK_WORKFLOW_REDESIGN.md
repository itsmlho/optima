# Redesign Kontrak Workflow

## Alur Baru: Spesifikasi Unit dari Kontrak

### 1. Database Changes Required

```sql
-- Tambah field harga per unit di inventory_unit
ALTER TABLE `inventory_unit` 
ADD COLUMN `harga_sewa_bulanan` DECIMAL(15,2) NULL DEFAULT NULL COMMENT 'Harga sewa per bulan',
ADD COLUMN `harga_sewa_harian` DECIMAL(15,2) NULL DEFAULT NULL COMMENT 'Harga sewa per hari';

-- Tambah tabel untuk spesifikasi kontrak (opsional - bisa pakai existing kontrak table)
CREATE TABLE `kontrak_spesifikasi` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kontrak_id` INT UNSIGNED NOT NULL,
    `departemen_id` INT DEFAULT NULL,
    `tipe_unit_id` INT DEFAULT NULL,
    `kapasitas_id` INT DEFAULT NULL,
    `merk_unit` VARCHAR(100) DEFAULT NULL,
    `jumlah_dibutuhkan` INT DEFAULT 1,
    `harga_per_unit` DECIMAL(15,2) DEFAULT NULL,
    `catatan_spek` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE
);
```

### 2. UI Flow Changes

#### A. Kontrak Creation
```
[Buat Kontrak] → [Basic Info] → [Tambah Spesifikasi Unit] → [Assign Units] → [Review & Save]
```

#### B. Unit Assignment Interface
- Tampilkan available units berdasarkan spesifikasi
- Filter by: Departemen, Tipe, Kapasitas, Status (STOK only)
- Bulk assign units ke kontrak
- Auto-calculate total berdasarkan harga per unit

#### C. SPK Creation (Simplified)
- Pilih kontrak → Unit sudah tersedia (pre-assigned)
- Tidak perlu pilih spek lagi

### 3. Backend Implementation

#### A. Kontrak Controller Updates
```php
// Method untuk assign unit ke kontrak
public function assignUnits($kontrakId) {
    $unitIds = $this->request->getPost('unit_ids');
    $hargaPerUnit = $this->request->getPost('harga_per_unit');
    
    // Update inventory_unit
    foreach($unitIds as $unitId) {
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $unitId)
            ->update([
                'kontrak_id' => $kontrakId,
                'harga_sewa_bulanan' => $hargaPerUnit,
                'status_unit_id' => 3 // RENTAL
            ]);
    }
    
    // Auto-calculate kontrak totals
    $this->updateKontrakTotals($kontrakId);
}

private function updateKontrakTotals($kontrakId) {
    $result = $this->db->query("
        SELECT 
            COUNT(*) as total_units,
            SUM(harga_sewa_bulanan) as nilai_total
        FROM inventory_unit 
        WHERE kontrak_id = ?
    ", [$kontrakId])->getRow();
    
    $this->kontrakModel->update($kontrakId, [
        'total_units' => $result->total_units,
        'nilai_total' => $result->nilai_total
    ]);
}
```

#### B. SPK Controller Updates
```php
// Simplify SPK creation - units pre-selected from kontrak
public function getKontrakUnits($kontrakId) {
    return $this->db->table('inventory_unit iu')
        ->select('iu.*, mu.merk_unit, mu.model_unit')
        ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
        ->where('iu.kontrak_id', $kontrakId)
        ->where('iu.status_unit_id', 3) // RENTAL
        ->get()->getResultArray();
}
```

### 4. Frontend Implementation

#### A. Kontrak Form Enhancement
- Tab 1: Basic kontrak info
- Tab 2: Spesifikasi unit yang dibutuhkan
- Tab 3: Assign units dari inventory
- Tab 4: Review total & konfirmasi

#### B. Unit Assignment Modal
```javascript
function openUnitAssignment(kontrakId) {
    // Load available units based on kontrak specs
    // Show grid with checkboxes
    // Calculate total on selection change
}
```

### 5. Migration Path

1. **Phase 1**: Add new fields to inventory_unit
2. **Phase 2**: Update Kontrak UI for unit assignment
3. **Phase 3**: Simplify SPK creation flow
4. **Phase 4**: Add auto-calculation triggers

### 6. Benefits

- **Kontrak lebih akurat**: Nilai dan unit count otomatis
- **SPK simplified**: Unit sudah pre-selected
- **Better inventory tracking**: Unit status clear
- **Pricing consistency**: Harga per unit tersimpan

### 7. Considerations

- **Existing data**: Perlu migration script untuk data lama
- **Unit availability**: Conflict resolution jika unit sudah di-assign
- **Price changes**: Versioning untuk perubahan harga
- **Cancellation**: Logic untuk release units saat kontrak batal
