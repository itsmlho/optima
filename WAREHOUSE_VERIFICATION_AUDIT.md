# Audit Sistem Verifikasi Warehouse - OPTIMA

## 📋 Executive Summary

Dokumen ini berisi audit lengkap sistem verifikasi warehouse yang sedang berjalan, termasuk alur bisnis untuk status "Sesuai" dan "Tidak Sesuai", struktur database, dan rekomendasi perbaikan.

**Tanggal Audit:** 2025-01-17  
**Status:** Sistem Aktif  
**Lokasi:** `/opt/lampp/htdocs/optima1`

---

## 1. 🔍 Overview Sistem Verifikasi

### 1.1 Tipe Item yang Diverifikasi
1. **Unit** - Forklift dan unit utama
2. **Attachment** - Attachment, Battery, Charger
3. **Sparepart** - Sparepart items

### 1.2 Status Verifikasi
- `Belum Dicek` - Default status saat item dibuat
- `Sesuai` - Item sesuai dengan database/PO
- `Tidak Sesuai` - Item tidak sesuai dengan database/PO

---

## 2. 📊 Struktur Database

### 2.1 Tabel `po_units`
**Lokasi:** `app/Controllers/WarehousePO.php:324-494`

**Kolom Verifikasi:**
- `status_verifikasi` - ENUM('Belum Dicek', 'Sesuai', 'Tidak Sesuai')
- `catatan_verifikasi` - TEXT - Catatan jika tidak sesuai
- `serial_number_po` - VARCHAR - SN Unit
- `sn_mast_po` - VARCHAR - SN Mast
- `sn_mesin_po` - VARCHAR - SN Mesin
- `sn_baterai_po` - VARCHAR - SN Baterai
- `sn_attachment_po` - VARCHAR - SN Attachment
- `sn_charger_po` - VARCHAR - SN Charger

**Kolom Lainnya:**
- `id_po_unit` - Primary Key
- `po_id` - Foreign Key ke `purchase_orders`
- `lokasi_unit` - VARCHAR - Lokasi penyimpanan (POS 1-5)
- Dan banyak kolom spesifikasi lainnya

### 2.2 Tabel `po_attachment`
**Lokasi:** `app/Controllers/WarehousePO.php:533-723`

**Kolom Verifikasi:**
- `status_verifikasi` - ENUM('Belum Dicek', 'Sesuai', 'Tidak Sesuai')
- `catatan_verifikasi` - TEXT
- `serial_number` - VARCHAR - SN untuk Attachment/Battery/Charger
- `item_type` - ENUM('Attachment', 'Battery', 'Charger')

**Kolom Lainnya:**
- `id_po_attachment` - Primary Key
- `po_id` - Foreign Key
- `attachment_id`, `baterai_id`, `charger_id` - Foreign Keys

### 2.3 Tabel `po_sparepart_items`
**Lokasi:** `app/Controllers/WarehousePO.php:1063-1122`

**Kolom Verifikasi:**
- `status_verifikasi` - ENUM('Belum Dicek', 'Sesuai', 'Tidak Sesuai')
- `catatan_verifikasi` - TEXT

**Kolom Lainnya:**
- `id` - Primary Key
- `po_id` - Foreign Key
- `sparepart_id` - Foreign Key
- `qty` - INT - Jumlah

### 2.4 Tabel Audit Log
**Lokasi:** `app/Models/VerificationAuditLogModel.php`

**Tabel:** `verification_audit_log`

**Kolom:**
- `id` - Primary Key
- `po_type` - ENUM('unit', 'attachment', 'sparepart')
- `source_id` - INT - ID dari po_units/po_attachment/po_sparepart_items
- `po_id` - INT - ID Purchase Order
- `action` - VARCHAR - Action yang dilakukan (verify, revert, dll)
- `status_before` - VARCHAR - Status sebelum
- `status_after` - VARCHAR - Status setelah
- `user_id` - INT - User yang melakukan action
- `notes` - TEXT - Catatan
- `payload` - TEXT - JSON data tambahan
- `created_at` - DATETIME

### 2.5 Tabel Inventory (Target setelah verifikasi "Sesuai")

**A. `inventory_unit`**
- Data dari `po_units` yang status_verifikasi = 'Sesuai'
- Unit dihapus dari `po_units` setelah masuk inventory
- Status default: `status_unit_id = 8` (STOCK NON-ASET)

**B. `inventory_attachment`**
- Data dari `po_attachment` yang status_verifikasi = 'Sesuai'
- Item tetap ada di `po_attachment` (tidak dihapus)
- Status default: `attachment_status = 'AVAILABLE'`, `status_attachment_id = 1`

**C. `inventory_spareparts`**
- Data dari `po_sparepart_items` yang status_verifikasi = 'Sesuai'
- Menggunakan `INSERT ... ON DUPLICATE KEY UPDATE` untuk update stok
- Item tetap ada di `po_sparepart_items` (tidak dihapus)

---

## 3. 🔄 Alur Bisnis Verifikasi

### 3.1 Unit Verification

#### A. Frontend Flow
**File:** `app/Views/warehouse/purchase_orders/tabs/unit_verification_script.php`

**Proses:**
1. User klik unit dari list → `createUnitDetailCard()` dipanggil
2. Tampilkan tabel verifikasi dengan kolom:
   - Item (specification name)
   - Database (nilai dari database, readonly)
   - Real Lapangan (editable field)
   - Sesuai (checkbox)
   - Tidak Sesuai (checkbox)
3. User mengisi data:
   - Jika "Sesuai" dicentang → "Real Lapangan" = "Database" (readonly, green)
   - Jika "Real Lapangan" diubah → otomatis centang "Tidak Sesuai"
   - Jika "Tidak Sesuai" dicentang → "Real Lapangan" editable (required, red)
4. User pilih "Lokasi Unit" (POS 1-5)
5. Validasi: Semua baris harus punya checkbox, lokasi harus dipilih
6. User klik "Submit Verifikasi" → `submitUnitVerificationInline()`
7. AJAX call ke `warehouse/purchase-orders/verify-po-unit`

#### B. Backend Flow
**File:** `app/Controllers/WarehousePO.php:324-494`

**Proses:**
1. Validasi input:
   - Jika status = 'Sesuai' → SN Unit dan SN Mesin wajib
   - Jika status = 'Sesuai' → Lokasi unit wajib
2. Update `po_units`:
   - `status_verifikasi` = status
   - `catatan_verifikasi` = catatan
   - Update SN fields (sn_unit, sn_mesin, sn_mast, sn_baterai)
3. **Jika status = 'Sesuai':**
   - Ambil data lengkap dari `po_units` dengan JOIN
   - Validasi `departemen_id`
   - Insert ke `inventory_unit` dengan mapping lengkap
   - **HAPUS unit dari `po_units`** (setelah masuk inventory)
4. **Jika status = 'Tidak Sesuai':**
   - Unit tetap di `po_units` dengan status 'Tidak Sesuai'
   - **TIDAK masuk inventory**
   - Catatan disimpan di `catatan_verifikasi`
5. Update status PO utama → `updateOverallPOStatusForUnit()`
6. Log ke `verification_audit_log`
7. Commit transaction

#### C. Status PO Update Logic
**File:** `app/Controllers/WarehousePO.php:496-530`

**Kondisi:**
- Jika semua unit sudah diverifikasi (Sesuai atau Tidak Sesuai):
  - Jika ada minimal 1 unit "Tidak Sesuai" → PO status = 'Selesai dengan Catatan'
  - Jika semua "Sesuai" → PO status = 'completed'

### 3.2 Attachment Verification

#### A. Frontend Flow
**File:** `app/Views/warehouse/purchase_orders/tabs/attachment_verification_script.php`

**Proses:**
1. User klik attachment item → `prepareAttachmentVerificationModal()` (MODAL)
2. Tampilkan komponen verification dengan button "Sesuai" / "Tidak Sesuai"
3. Jika "Sesuai" → tampilkan input SN
4. Jika "Tidak Sesuai" → tampilkan textarea catatan
5. User pilih lokasi penyimpanan
6. Validasi: Semua komponen harus diverifikasi, lokasi harus dipilih
7. User klik "Submit Verifikasi" → `submitAttachmentVerification()`
8. AJAX call ke `warehouse/purchase-orders/verify-po-attachment`

#### B. Backend Flow
**File:** `app/Controllers/WarehousePO.php:533-723`

**Proses:**
1. Validasi input:
   - Jika status = 'Sesuai' → SN wajib (berdasarkan item_type)
   - Jika status = 'Sesuai' → Lokasi wajib
2. Update `po_attachment`:
   - `status_verifikasi` = status
   - `catatan_verifikasi` = catatan
   - `serial_number` = SN
3. **Jika status = 'Sesuai':**
   - Insert ke `inventory_attachment` berdasarkan `item_type`:
     - Attachment → `tipe_item = 'attachment'`
     - Battery → `tipe_item = 'battery'`
     - Charger → `tipe_item = 'charger'`
   - Item **TETAP ada di `po_attachment`** (tidak dihapus)
4. **Jika status = 'Tidak Sesuai':**
   - Item tetap di `po_attachment` dengan status 'Tidak Sesuai'
   - **TIDAK masuk inventory**
5. Commit transaction

**CATATAN PENTING:** Attachment verification **TIDAK** update status PO utama (tidak ada pemanggilan `updateOverallPOStatus()`)

### 3.3 Sparepart Verification

#### A. Frontend Flow
**File:** `app/Views/warehouse/purchase_orders/tabs/sparepart_verification_script.php`

**Proses:**
1. User klik sparepart item → `createSparepartDetailCard()`
2. Tampilkan detail sparepart
3. User klik button "Sesuai" atau "Tidak Sesuai"
4. Jika "Tidak Sesuai" → Swal prompt untuk catatan (required)
5. Jika "Sesuai" → Swal konfirmasi
6. AJAX call ke `warehouse/purchase-orders/verify-po-sparepart`

#### B. Backend Flow
**File:** `app/Controllers/WarehousePO.php:1063-1122`

**Proses:**
1. Update `po_sparepart_items`:
   - `status_verifikasi` = status
   - `catatan_verifikasi` = catatan
2. **Jika status = 'Sesuai':**
   - Ambil `sparepart_id` dan `qty`
   - Insert/Update `inventory_spareparts`:
     ```sql
     INSERT INTO inventory_spareparts (sparepart_id, stok, lokasi_rak) 
     VALUES (?, ?, ?) 
     ON DUPLICATE KEY UPDATE stok = stok + ?
     ```
   - Item **TETAP ada di `po_sparepart_items`** (tidak dihapus)
3. **Jika status = 'Tidak Sesuai':**
   - Item tetap di `po_sparepart_items` dengan status 'Tidak Sesuai'
   - **TIDAK masuk inventory** (stok tidak bertambah)
4. Update status PO utama → `updateOverallPOStatus()`
5. Log ke `verification_audit_log`
6. Commit transaction

#### C. Status PO Update Logic
**File:** `app/Controllers/WarehousePO.php:1124-1150`

**Kondisi:**
- Jika semua sparepart sudah diverifikasi:
  - Jika ada minimal 1 "Tidak Sesuai" → PO status = 'Selesai dengan Catatan'
  - Jika semua "Sesuai" → PO status = 'completed'

---

## 4. ⚠️ Analisis Alur "Tidak Sesuai"

### 4.1 Current Behavior

#### Unit - Status "Tidak Sesuai"
- ✅ Status diupdate ke 'Tidak Sesuai' di `po_units`
- ✅ Catatan disimpan di `catatan_verifikasi`
- ✅ **Unit TIDAK masuk inventory** (tetap di `po_units`)
- ✅ Audit log tercatat
- ❌ **TIDAK ada notifikasi ke Purchasing**
- ❌ **TIDAK ada approval workflow**
- ❌ **TIDAK ada tracking discrepancy detail**
- ❌ **Unit tetap "terjebak" di po_units** tanpa action selanjutnya

#### Attachment - Status "Tidak Sesuai"
- ✅ Status diupdate ke 'Tidak Sesuai' di `po_attachment`
- ✅ Catatan disimpan di `catatan_verifikasi`
- ✅ **Item TIDAK masuk inventory**
- ❌ **TIDAK ada audit log** (tidak dipanggil di `verifyPoAttachment`)
- ❌ **TIDAK ada notifikasi ke Purchasing**
- ❌ **TIDAK ada approval workflow**
- ❌ **Item tetap "terjebak" di po_attachment**

#### Sparepart - Status "Tidak Sesuai"
- ✅ Status diupdate ke 'Tidak Sesuai' di `po_sparepart_items`
- ✅ Catatan disimpan di `catatan_verifikasi`
- ✅ **Stok TIDAK bertambah** (tidak masuk inventory)
- ✅ Audit log tercatat
- ❌ **TIDAK ada notifikasi ke Purchasing**
- ❌ **TIDAK ada approval workflow**

### 4.2 Masalah yang Ditemukan

1. **Tidak Ada Notifikasi**
   - Ketika item "Tidak Sesuai", Purchasing team tidak tahu
   - Tidak ada mekanisme untuk Purchasing review discrepancy

2. **Tidak Ada Approval Workflow**
   - Warehouse bisa langsung set "Tidak Sesuai" tanpa approval
   - Tidak ada mekanisme untuk Purchasing approve/reject discrepancy

3. **Tidak Ada Tracking Detail Discrepancy**
   - Hanya ada `catatan_verifikasi` (text umum)
   - Tidak ada tracking field-by-field discrepancy (Database vs Real)
   - Tidak bisa analisis pola ketidaksesuaian

4. **Item "Terjebak" di PO Tables**
   - Unit/Attachment/Sparepart dengan status "Tidak Sesuai" tetap di tabel PO
   - Tidak ada mekanisme untuk:
     - Re-verification setelah diperbaiki
     - Return to supplier
     - Adjustment/Correction

5. **Tidak Ada Dashboard untuk Purchasing**
   - Purchasing tidak bisa lihat semua item "Tidak Sesuai"
   - Tidak ada reporting untuk analisis discrepancy

6. **Inkonsistensi Audit Log**
   - Unit & Sparepart → Ada audit log ✅
   - Attachment → **TIDAK ada audit log** ❌

---

## 5. 📋 Rekomendasi Perbaikan

### 5.1 Priority 1: Fix Critical Issues

#### A. Tambahkan Audit Log untuk Attachment
**File:** `app/Controllers/WarehousePO.php:533-723`

**Action:**
```php
// Setelah update po_attachment berhasil, tambahkan:
$this->verificationAuditLogModel->log([
    'po_type' => 'attachment',
    'source_id' => $id_item,
    'po_id' => $po_id,
    'action' => 'verify',
    'status_before' => $original['status_verifikasi'] ?? null,
    'status_after' => $status,
    'user_id' => session()->get('user_id'),
    'notes' => $catatan,
    'payload' => json_encode(['serial_number' => $snAttachment])
]);
```

#### B. Tambahkan Notifikasi untuk "Tidak Sesuai"
**Action:**
- Gunakan sistem notifikasi yang sudah ada (lihat memory)
- Ketika status = 'Tidak Sesuai', create notification untuk Purchasing team
- Notification type: 'verification_discrepancy'
- Include: PO number, Item details, Catatan

### 5.2 Priority 2: Enhancement Workflow

#### A. Tambahkan Tracking Detail Discrepancy
**Database Schema:**
```sql
CREATE TABLE IF NOT EXISTS verification_discrepancies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_type ENUM('unit', 'attachment', 'sparepart') NOT NULL,
    source_id INT NOT NULL,
    po_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    database_value TEXT,
    real_value TEXT,
    discrepancy_type ENUM('Minor', 'Major', 'Missing') DEFAULT 'Minor',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_po_type_source (po_type, source_id),
    INDEX idx_po_id (po_id)
);
```

**Action:**
- Saat submit verifikasi dengan "Tidak Sesuai", simpan field-by-field discrepancy
- Gunakan data dari "Database" vs "Real Lapangan" yang berbeda

#### B. Tambahkan Approval Workflow (Optional)
**Database Schema:**
```sql
ALTER TABLE po_units ADD COLUMN requires_approval BOOLEAN DEFAULT FALSE;
ALTER TABLE po_units ADD COLUMN approval_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT NULL;
ALTER TABLE po_units ADD COLUMN approved_by INT NULL;
ALTER TABLE po_units ADD COLUMN approved_date DATETIME NULL;

-- Same for po_attachment and po_sparepart_items
```

**Workflow:**
1. Warehouse set "Tidak Sesuai" → `requires_approval = TRUE`, `approval_status = 'Pending'`
2. Create notification untuk Purchasing
3. Purchasing review → Approve/Reject
4. Jika Approved → Update data database dengan "Real Lapangan", set status = 'Sesuai', masuk inventory
5. Jika Rejected → Kembalikan ke Warehouse dengan catatan

### 5.3 Priority 3: Dashboard & Reporting

#### A. Dashboard Purchasing untuk Discrepancy
- List semua item dengan status "Tidak Sesuai"
- Filter by PO, Date, Item Type
- Action: Approve, Reject, View Detail

#### B. Reporting
- Analisis pola ketidaksesuaian
- Field yang paling sering tidak sesuai
- Supplier performance (berapa banyak "Tidak Sesuai" per supplier)

---

## 6. 🔄 Perbandingan Alur "Sesuai" vs "Tidak Sesuai"

### 6.1 Unit Verification

| Aspek | Sesuai | Tidak Sesuai |
|-------|--------|--------------|
| Update `po_units.status_verifikasi` | ✅ | ✅ |
| Simpan catatan | ✅ (optional) | ✅ (required) |
| Masuk inventory | ✅ (insert ke `inventory_unit`) | ❌ |
| Hapus dari `po_units` | ✅ (setelah masuk inventory) | ❌ (tetap di `po_units`) |
| Update status PO | ✅ | ✅ |
| Audit log | ✅ | ✅ |
| Notifikasi | ❌ | ❌ (seharusnya ada) |

### 6.2 Attachment Verification

| Aspek | Sesuai | Tidak Sesuai |
|-------|--------|--------------|
| Update `po_attachment.status_verifikasi` | ✅ | ✅ |
| Simpan catatan | ✅ (optional) | ✅ (required) |
| Masuk inventory | ✅ (insert ke `inventory_attachment`) | ❌ |
| Hapus dari `po_attachment` | ❌ (tetap di tabel) | ❌ (tetap di tabel) |
| Update status PO | ❌ (tidak ada) | ❌ (tidak ada) |
| Audit log | ❌ (seharusnya ada) | ❌ (seharusnya ada) |
| Notifikasi | ❌ | ❌ (seharusnya ada) |

### 6.3 Sparepart Verification

| Aspek | Sesuai | Tidak Sesuai |
|-------|--------|--------------|
| Update `po_sparepart_items.status_verifikasi` | ✅ | ✅ |
| Simpan catatan | ✅ (optional) | ✅ (required) |
| Masuk inventory | ✅ (update stok di `inventory_spareparts`) | ❌ |
| Hapus dari `po_sparepart_items` | ❌ (tetap di tabel) | ❌ (tetap di tabel) |
| Update status PO | ✅ | ✅ |
| Audit log | ✅ | ✅ |
| Notifikasi | ❌ | ❌ (seharusnya ada) |

---

## 7. 📝 Kesimpulan

### 7.1 Yang Sudah Berjalan dengan Baik
- ✅ Verifikasi "Sesuai" bekerja dengan baik untuk semua tipe item
- ✅ Data masuk inventory dengan benar untuk "Sesuai"
- ✅ Audit log untuk Unit dan Sparepart
- ✅ Update status PO untuk Unit dan Sparepart
- ✅ Validasi SN dan lokasi untuk "Sesuai"

### 7.2 Yang Perlu Diperbaiki
- ❌ **Attachment verification tidak ada audit log**
- ❌ **Tidak ada notifikasi untuk "Tidak Sesuai"**
- ❌ **Tidak ada approval workflow**
- ❌ **Tidak ada tracking detail discrepancy**
- ❌ **Item "Tidak Sesuai" terjebak tanpa action selanjutnya**
- ❌ **Attachment verification tidak update status PO**

### 7.3 Rekomendasi Implementasi

**Immediate (Priority 1):**
1. Tambahkan audit log untuk Attachment verification
2. Tambahkan notifikasi untuk "Tidak Sesuai" (gunakan sistem notifikasi yang ada)

**Short Term (Priority 2):**
3. Buat tabel `verification_discrepancies` untuk tracking detail
4. Tambahkan dashboard untuk Purchasing melihat discrepancy

**Long Term (Priority 3):**
5. Implement approval workflow (jika diperlukan)
6. Buat reporting untuk analisis discrepancy

---

## 8. 📚 Reference Files

### Controllers
- `app/Controllers/WarehousePO.php`
  - `verifyPoUnit()` - Line 324-494
  - `verifyPoAttachment()` - Line 533-723
  - `verifyPoSparepart()` - Line 1063-1122
  - `updateOverallPOStatusForUnit()` - Line 496-530
  - `updateOverallPOStatus()` - Line 1124-1150

### Views
- `app/Views/warehouse/purchase_orders/tabs/unit_verification_script.php`
- `app/Views/warehouse/purchase_orders/tabs/attachment_verification_script.php`
- `app/Views/warehouse/purchase_orders/tabs/sparepart_verification_script.php`

### Models
- `app/Models/VerificationAuditLogModel.php`

### Database Tables
- `po_units` - Unit items
- `po_attachment` - Attachment/Battery/Charger items
- `po_sparepart_items` - Sparepart items
- `inventory_unit` - Inventory untuk units
- `inventory_attachment` - Inventory untuk attachments
- `inventory_spareparts` - Inventory untuk spareparts
- `verification_audit_log` - Audit log untuk semua verifikasi

---

**Document Created:** 2025-01-17  
**Last Updated:** 2025-01-17  
**Status:** Complete Audit

