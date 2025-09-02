# Konsolidasi Komponen ke Inventory Attachment

## Overview
Migration ini mengkonsolidasikan penyimpanan komponen (battery, charger, attachment) dari multiple tables ke `inventory_attachment` sebagai **single source of truth**.

## Masalah yang Diselesaikan
1. **Inconsistency**: Data komponen tersimpan di 3 tempat berbeda
2. **Maintenance**: Sulit mengelola dan sync data antar tabel
3. **Performance**: Query kompleks untuk mendapatkan data lengkap

## Perubahan Database

### Sebelum Migration:
```
inventory_unit:
├── model_baterai_id → baterai.id
├── sn_baterai
├── model_charger_id → charger.id_charger
├── sn_charger
├── model_attachment_id → attachment.id_attachment
└── sn_attachment

inventory_attachment: (partial data)
├── baterai_id
├── sn_baterai
├── charger_id
├── sn_charger
└── attachment_id

spk.spesifikasi: (JSON)
├── persiapan_battery_id
├── persiapan_charger_id
└── fabrikasi_attachment_id
```

### Sesudah Migration:
```
inventory_unit: (simplified)
├── id_inventory_unit
├── no_unit
├── serial_number
└── departemen_id
└── status_unit_id

inventory_attachment: (single source of truth)
├── tipe_item (battery/charger/attachment)
├── baterai_id / charger_id / attachment_id
├── sn_baterai / sn_charger / sn_attachment
├── status_unit (7=available, 8=in use)
└── id_inventory_unit (link to unit)

spk.spesifikasi: (updated JSON)
├── persiapan_battery_inventory_id
├── persiapan_charger_inventory_id
└── fabrikasi_attachment_inventory_id
```

## Files yang Dibuat

### Migration Scripts:
- `databases/consolidate_components_migration.sql` - Main migration
- `databases/rollback_consolidate_components.sql` - Rollback script
- `databases/create_migration_log.sql` - Migration tracking

### Helper Scripts:
- `run_migration.sh` - Executable migration script

## Cara Menjalankan Migration

### 1. Backup Database (WAJIB!)
```bash
mysqldump -u root -p optima1 > backup_pre_migration.sql
```

### 2. Jalankan Migration
```bash
cd /opt/lampp/htdocs/optima1
chmod +x run_migration.sh
./run_migration.sh
```

### 3. Verifikasi Migration
```sql
-- Check migration log
SELECT * FROM migration_log WHERE migration_name = 'consolidate_components_to_inventory_attachment';

-- Check data integrity
SELECT COUNT(*) FROM inventory_attachment WHERE tipe_item IN ('battery', 'charger', 'attachment');

-- Check view functionality
SELECT * FROM inventory_unit_components LIMIT 5;
```

## Testing Checklist

### 1. SPK Service - Persiapan Unit
- [ ] Pilih unit berhasil
- [ ] Battery/charger assignment berhasil
- [ ] Attachment selection berhasil
- [ ] Data tersimpan dengan benar

### 2. DI Detail Display
- [ ] Unit info ditampilkan dengan benar
- [ ] Battery/charger info ditampilkan untuk Electric units
- [ ] General attachments ditampilkan dengan benar

### 3. Inventory Management
- [ ] Status unit update dengan benar (7=available, 8=in use)
- [ ] Component assignment/release berhasil

## Rollback (Jika Diperlukan)

Jika ada masalah setelah migration:

```bash
mysql -u root -p optima1 < databases/rollback_consolidate_components.sql
```

## Helper Functions (Baru)

### get_unit_battery_info(unit_id)
Mengambil info battery lengkap untuk unit tertentu.

### get_unit_charger_info(unit_id)
Mengambil info charger lengkap untuk unit tertentu.

### get_unit_attachment_info(unit_id)
Mengambil info attachment lengkap untuk unit tertentu.

## View Compatibility

### inventory_unit_components
View untuk backward compatibility dengan kode existing:

```sql
SELECT * FROM inventory_unit_components WHERE id_inventory_unit = 123;
```

## Monitoring & Maintenance

### 1. Log Migration
```sql
SELECT * FROM migration_log ORDER BY executed_at DESC;
```

### 2. Component Transactions
```sql
-- Jika tabel spk_component_transactions dibuat
SELECT * FROM spk_component_transactions ORDER BY timestamp DESC;
```

### 3. Data Integrity Check
```sql
-- Check for orphaned records
SELECT * FROM inventory_attachment
WHERE id_inventory_unit IS NOT NULL
AND id_inventory_unit NOT IN (SELECT id_inventory_unit FROM inventory_unit);

-- Check status consistency
SELECT * FROM inventory_attachment
WHERE status_unit = 8 AND id_inventory_unit IS NULL;
```

## Performance Improvements

1. **Simplified Queries**: Tidak perlu JOIN multiple tables
2. **Single Source**: Semua data komponen di satu tempat
3. **Better Indexing**: Index pada tipe_item dan status_unit
4. **Helper Functions**: Query yang sering digunakan di-cache

## Future Enhancements

1. **Component History**: Track perubahan komponen dari waktu ke waktu
2. **Bulk Operations**: Assign/release multiple components sekaligus
3. **Validation Rules**: Business rules untuk component compatibility
4. **API Endpoints**: REST API untuk component management

## Support

Jika ada masalah:
1. Check migration log: `SELECT * FROM migration_log;`
2. Check error logs di aplikasi
3. Gunakan rollback script jika diperlukan
4. Contact development team

---

**Migration Date**: 2025-08-30
**Migration Version**: 1.0
**Status**: Ready for Production
