# Halaman Rejected Items - Implementation Complete

## ✅ Yang Sudah Dibuat

### 1. SQL File untuk Create Table `po_verification`
**File:** `databases/create_po_verification_table.sql`

**Cara Install:**
1. Buka phpMyAdmin atau MySQL client
2. Pilih database `optima_ci`
3. Import file `databases/create_po_verification_table.sql`
4. Atau copy-paste SQL dan execute

**Struktur Tabel:**
- `id` - Primary Key
- `po_type` - ENUM('unit', 'attachment', 'sparepart')
- `source_id` - ID dari po_units/po_attachment/po_sparepart_items
- `po_id` - ID Purchase Order
- `field_name` - Nama field yang tidak sesuai
- `database_value` - Nilai dari database/PO
- `real_value` - Nilai real dari lapangan
- `discrepancy_type` - ENUM('Minor', 'Major', 'Missing')
- `status_verifikasi` - ENUM('Sesuai', 'Tidak Sesuai')
- `catatan` - Catatan tambahan
- `verified_by` - User ID yang melakukan verifikasi
- `created_at` - Timestamp

---

### 2. Controller Method `rejectedItems()`
**File:** `app/Controllers/WarehousePO.php` (Line 210-320)

**Fungsi:**
- Mengambil semua item dengan status "Tidak Sesuai" dari:
  - `po_units`
  - `po_attachment`
  - `po_sparepart_items`
- Mengambil detail discrepancy dari tabel `po_verification` (jika ada)
- Menampilkan data ke view

**Route:**
- URL: `/warehouse/purchase-orders/rejected-items`
- Method: GET
- Controller: `WarehousePO::rejectedItems()`

---

### 3. View Halaman Rejected Items
**File:** `app/Views/warehouse/purchase_orders/rejected_items.php`

**Fitur:**
- ✅ Statistics Cards untuk Unit, Attachment, Sparepart, dan Total
- ✅ Tab Navigation untuk memisahkan tipe item
- ✅ Card-based layout untuk setiap rejected item
- ✅ Menampilkan detail discrepancy (jika ada)
- ✅ Badge untuk discrepancy type (Minor/Major/Missing)
- ✅ Informasi verifier (siapa yang melakukan verifikasi)
- ✅ Catatan verifikasi
- ✅ Responsive design
- ✅ Link kembali ke halaman verifikasi

**Layout:**
- Header dengan breadcrumb
- 4 Statistics Cards (Unit, Attachment, Sparepart, Total)
- Tab Navigation (3 tabs)
- Card Grid untuk rejected items
- Detail discrepancy dengan color coding:
  - **Minor** - Yellow background
  - **Major** - Red background
  - **Missing** - Blue background

---

## 📋 Cara Menggunakan

### 1. Install Tabel `po_verification`
```sql
-- Import file databases/create_po_verification_table.sql
-- Atau run migration (jika migration bisa dijalankan)
php spark migrate
```

### 2. Akses Halaman
```
URL: http://localhost/optima1/public/warehouse/purchase-orders/rejected-items
```

### 3. Navigasi
- Dari halaman verifikasi, bisa tambahkan link ke rejected items
- Atau akses langsung via URL di atas

---

## 🎨 Tampilan Halaman

### Statistics Cards
- **Unit Card** - Purple gradient, menampilkan jumlah unit ditolak
- **Attachment Card** - Pink gradient, menampilkan jumlah attachment ditolak
- **Sparepart Card** - Blue gradient, menampilkan jumlah sparepart ditolak
- **Total Card** - Yellow gradient, menampilkan total semua item ditolak

### Rejected Item Card
Setiap card menampilkan:
- PO Number
- Item details (merk, model, dll)
- Catatan verifikasi
- Detail discrepancy (jika ada)
- Verifier information
- Badge untuk tipe item

### Discrepancy Display
- **Field Name** - Nama field yang tidak sesuai
- **Database Value** - Nilai dari database/PO
- **Real Value** - Nilai real dari lapangan
- **Type Badge** - Minor/Major/Missing dengan color coding

---

## 🔗 Integration

### Link dari Halaman Verifikasi
Tambahkan link di `wh_verification.php`:
```php
<a href="<?= base_url('warehouse/purchase-orders/rejected-items') ?>" class="btn btn-danger">
    <i class="fas fa-times-circle me-2"></i>Lihat Item Ditolak
</a>
```

### Link dari Menu Sidebar (Opsional)
Tambahkan menu item di sidebar untuk quick access.

---

## 📊 Data Flow

```
User verifikasi item → Status "Tidak Sesuai"
    ↓
Data disimpan ke:
- po_units/po_attachment/po_sparepart_items (status_verifikasi = 'Tidak Sesuai')
- po_verification (detail discrepancy)
    ↓
User akses halaman rejected-items
    ↓
Controller mengambil:
- Semua item dengan status "Tidak Sesuai"
- Detail discrepancy dari po_verification
    ↓
View menampilkan data dalam card layout
```

---

## ✅ Testing Checklist

- [ ] Install tabel `po_verification` via SQL
- [ ] Akses halaman `/warehouse/purchase-orders/rejected-items`
- [ ] Cek statistics cards menampilkan jumlah yang benar
- [ ] Cek tab navigation berfungsi
- [ ] Cek rejected items ditampilkan dengan benar
- [ ] Cek detail discrepancy ditampilkan (jika ada)
- [ ] Cek responsive design di mobile
- [ ] Cek link kembali ke verifikasi berfungsi

---

## 📝 Notes

1. **Tabel `po_verification` harus dibuat dulu** sebelum halaman bisa menampilkan detail discrepancy
2. Jika tabel belum ada, halaman tetap bisa diakses, hanya detail discrepancy yang tidak ditampilkan
3. Controller sudah handle error jika tabel belum ada (try-catch)
4. View sudah handle jika tidak ada rejected items (menampilkan alert info)

---

**Status:** ✅ Complete - Ready for Testing  
**Created:** 2025-01-17

