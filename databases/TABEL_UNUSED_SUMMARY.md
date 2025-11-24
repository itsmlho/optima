# 📊 AUDIT TABEL DATABASE - TABEL YANG TIDAK DIGUNAKAN

**Tanggal Audit:** $(date)  
**Total Tabel:** 106  
**Tabel Digunakan:** 79  
**Tabel TIDAK Digunakan:** 17  
**Tabel Backup:** 10

---

## ❌ TABEL YANG TIDAK DIGUNAKAN (17 tabel)

### Kategori: Workflow & Logging
1. **delivery_workflow_log** - Log workflow delivery instructions
2. **di_workflow_stages** - Stages untuk delivery instruction workflow
3. **kontrak_status_changes** - Log perubahan status kontrak
4. **migration_log** - Log migrasi database
5. **migration_log_di_workflow** - Log migrasi DI workflow
6. **optimization_additional_log** - Log optimisasi tambahan
7. **optimization_log** - Log optimisasi
8. **rbac_audit_log** - Log audit RBAC
9. **unit_replacement_log** - Log penggantian unit
10. **unit_status_log** - Log perubahan status unit

### Kategori: SPK (Service/Work Order)
11. **spk_component_transactions** - Transaksi komponen SPK
12. **spk_edit_permissions** - Permission untuk edit SPK
13. **spk_units** - Unit dalam SPK (mungkin sudah diganti dengan spk_unit_stages)

### Kategori: Supplier Management
14. **supplier_contacts** - Kontak supplier
15. **supplier_documents** - Dokumen supplier
16. **supplier_performance_log** - Log performa supplier

### Kategori: Work Order
17. **work_order_attachments** - Attachment work order

---

## ⏭️ TABEL BACKUP (10 tabel - Bisa dihapus jika tidak diperlukan)

1. **customer_locations_backup**
2. **notification_rules_backup_20250116**
3. **po_items_backup_restructure**
4. **po_sparepart_items_backup_restructure**
5. **po_units_backup_restructure**
6. **spk_backup_20250903**
7. **suppliers_backup_old**
8. **system_activity_log_backup**
9. **system_activity_log_old**
10. **work_order_staff_backup_final**

---

## ⚠️ CATATAN PENTING

### Sebelum Menghapus Tabel:

1. **Backup Database** - Pastikan sudah ada backup lengkap
2. **Cek Foreign Keys** - Pastikan tidak ada tabel lain yang reference ke tabel ini
3. **Cek Stored Procedures/Views** - Pastikan tidak ada procedure/view yang menggunakan tabel ini
4. **Cek Data Penting** - Pastikan tidak ada data penting yang perlu di-export dulu
5. **Test di Environment Development** - Test penghapusan di dev environment dulu

### Tabel yang Perlu Diperhatikan:

- **spk_units** - Mungkin sudah diganti dengan `spk_unit_stages`, tapi pastikan dulu
- **work_order_attachments** - Mungkin masih diperlukan untuk fitur attachment
- **supplier_contacts, supplier_documents, supplier_performance_log** - Mungkin akan digunakan di fitur supplier management yang akan datang

---

## 📋 REKOMENDASI

### Prioritas TINGGI (Aman untuk dihapus):
- Semua tabel backup (10 tabel)
- migration_log, migration_log_di_workflow (jika migration sudah selesai)
- optimization_log, optimization_additional_log (jika tidak digunakan)

### Prioritas SEDANG (Perlu verifikasi):
- delivery_workflow_log, di_workflow_stages
- kontrak_status_changes
- rbac_audit_log
- unit_replacement_log, unit_status_log
- spk_component_transactions, spk_edit_permissions, spk_units

### Prioritas RENDAH (Mungkin masih diperlukan):
- supplier_contacts, supplier_documents, supplier_performance_log
- work_order_attachments

---

## 🔍 CARA VERIFIKASI MANUAL

Untuk memastikan tabel benar-benar tidak digunakan:

1. Cek di database apakah ada data penting:
   ```sql
   SELECT COUNT(*) FROM nama_tabel;
   ```

2. Cek foreign key constraints:
   ```sql
   SELECT * FROM information_schema.KEY_COLUMN_USAGE 
   WHERE REFERENCED_TABLE_NAME = 'nama_tabel';
   ```

3. Cek views dan procedures:
   ```sql
   SELECT * FROM information_schema.VIEWS 
   WHERE VIEW_DEFINITION LIKE '%nama_tabel%';
   ```

4. Cek di codebase dengan grep:
   ```bash
   grep -r "nama_tabel" app/
   ```


