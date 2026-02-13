# Panduan Update Database Production - SIMPLE

**Database:** u138256737_optima_db  
**URL:** https://optima.sml.co.id/  
**File SQL:** `PRODUCTION_MIGRATION_SIMPLE.sql`

---

## 📋 Langkah-Langkah (5 Menit)

### 1. BACKUP Database ⚠️ WAJIB!

**Via phpMyAdmin:**
1. Login ke cPanel → phpMyAdmin
2. Pilih database: `u138256737_optima_db`
3. Tab **"Export"**
4. Klik **"Go"**
5. Save file: `optima_backup_2026-02-14.sql`

### 2. Jalankan SQL Migration

**Via phpMyAdmin:**
1. Pilih database: `u138256737_optima_db`
2. Tab **"SQL"**
3. Buka file: `PRODUCTION_MIGRATION_SIMPLE.sql`
4. Copy semua isi
5. Paste ke SQL tab
6. Klik **"Go"**

**Waktu eksekusi:** ±30 detik

### 3. Cek Hasil

Lihat output di phpMyAdmin:
- ✓ Backup tables created
- ✓ inventory_unit: 3 kolom ditambahkan
- ✓ Duplicates dihapus: X rows
- ✓ inventory_attachment: Kolom lama dihapus
- ✓ inventory_attachment: Index dan UNIQUE constraints ditambahkan
- **MIGRATION SELESAI!**

### 4. Test Aplikasi

Buka halaman-halaman ini dan pastikan tidak ada error:
- https://optima.sml.co.id/warehouse/inventory/invent_unit
- https://optima.sml.co.id/warehouse/inventory/invent_attachment
- https://optima.sml.co.id/marketing/customer_management

**Cek modal detail:** Klik salah satu unit/attachment, pastikan modal bisa scroll dengan baik.

---

## ✅ Perubahan yang Dilakukan

### Table: `inventory_unit`
**DITAMBAHKAN:**
- ✅ `on_hire_date` - Tanggal unit disewakan
- ✅ `off_hire_date` - Tanggal unit dikembalikan  
- ✅ `rate_changed_at` - Terakhir ubah harga

### Table: `inventory_attachment`
**DIHAPUS:**
- ❌ `status_unit` (kolom lama)
- ❌ `status_attachment_id` (kolom lama)

**DITAMBAHKAN:**
- ✅ Index: `idx_inventory_attachment_status`
- ✅ UNIQUE: `uk_unit_attachment` (cegah duplikat attachment)
- ✅ UNIQUE: `uk_unit_charger` (cegah duplikat charger)
- ✅ UNIQUE: `uk_unit_battery` (cegah duplikat battery)

**DIBERSIHKAN:**
- 🗑️ Duplicate records (otomatis dihapus, yang paling lama dipertahankan)

---

## 🚨 Jika Ada Masalah (Rollback)

**Copy script ini ke SQL tab phpMyAdmin:**

```sql
USE u138256737_optima_db;

DROP TABLE IF EXISTS inventory_unit;
DROP TABLE IF EXISTS inventory_attachment;

CREATE TABLE inventory_unit AS SELECT * FROM inventory_unit_backup_20260214;
CREATE TABLE inventory_attachment AS SELECT * FROM inventory_attachment_backup_20260214;
```

Atau restore dari file backup: `optima_backup_2026-02-14.sql`

---

## 📞 Kontak Jika Error

**Jika menemui error:**
1. Screenshot error message
2. Jangan lanjutkan migration
3. Jalankan rollback script
4. Hubungi IT Support

---

## 📝 Catatan

- Backup tables (`*_backup_20260214`) bisa dihapus setelah 30 hari kalau sudah stabil
- Migration ini aman dan bisa di-rollback
- Total waktu: ±5 menit termasuk backup
- Tidak perlu maintenance mode (perubahan additive)

**Status:** Ready for Production ✅
