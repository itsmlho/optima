# 🗂️ FASE 1: DATABASE CLEANUP - OPTIMA CI

## 📋 Target Pembersihan

### 1. Hapus Tabel Unused (17 tabel)
**Prioritas TINGGI - Aman untuk dihapus:**
```sql
-- Hapus tabel backup yang sudah tidak diperlukan
DROP TABLE IF EXISTS `customer_locations_backup`;
DROP TABLE IF EXISTS `notification_rules_backup_20250116`;
DROP TABLE IF EXISTS `po_items_backup_restructure`;
DROP TABLE IF EXISTS `po_sparepart_items_backup_restructure`;
DROP TABLE IF EXISTS `po_units_backup_restructure`;
DROP TABLE IF EXISTS `spk_backup_20250903`;
DROP TABLE IF EXISTS `suppliers_backup_old`;
DROP TABLE IF EXISTS `system_activity_log_backup`;
DROP TABLE IF EXISTS `system_activity_log_old`;
DROP TABLE IF EXISTS `work_order_staff_backup_final`;

-- Hapus tabel migration log lama (jika migration sudah selesai)
DROP TABLE IF EXISTS `migration_log`;
DROP TABLE IF EXISTS `migration_log_di_workflow`;
DROP TABLE IF EXISTS `optimization_additional_log`;
DROP TABLE IF EXISTS `optimization_log`;
```

**Prioritas SEDANG - Perlu verifikasi data:**
```sql
-- Cek dulu apakah ada data penting
SELECT COUNT(*) FROM delivery_workflow_log;
SELECT COUNT(*) FROM di_workflow_stages;
SELECT COUNT(*) FROM kontrak_status_changes;
SELECT COUNT(*) FROM rbac_audit_log;

-- Jika tidak ada data penting, hapus:
DROP TABLE IF EXISTS `delivery_workflow_log`;
DROP TABLE IF EXISTS `di_workflow_stages`;
DROP TABLE IF EXISTS `kontrak_status_changes`;
DROP TABLE IF EXISTS `rbac_audit_log`;
DROP TABLE IF EXISTS `unit_replacement_log`;
DROP TABLE IF EXISTS `unit_status_log`;
DROP TABLE IF EXISTS `spk_component_transactions`;
DROP TABLE IF EXISTS `spk_edit_permissions`;
DROP TABLE IF EXISTS `spk_units`;
```

### 2. Analisis Tabel Berukuran Besar
```sql
-- Cek ukuran tabel untuk prioritas optimasi
SELECT 
    TABLE_NAME,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size_MB',
    TABLE_ROWS,
    ROUND((INDEX_LENGTH / 1024 / 1024), 2) AS 'Index_Size_MB'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'optima_ci' 
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
LIMIT 20;
```

## ⚠️ CHECKLIST SEBELUM EKSEKUSI

- [ ] **BACKUP DATABASE LENGKAP**
- [ ] **Test di environment development dulu**
- [ ] **Verifikasi tidak ada aplikasi yang menggunakan tabel ini**
- [ ] **Cek foreign key dependencies**
- [ ] **Dokumentasi tabel yang dihapus**

## 🎯 Expected Results
- Pengurangan ukuran database 20-30%
- Menghilangkan overhead maintenance tabel unused
- Mempercepat backup/restore process
- Cleanup struktur database untuk optimasi selanjutnya