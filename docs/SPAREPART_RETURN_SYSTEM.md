# Sistem Pengembalian Sparepart - Warehouse Module

## 📋 Alur Bisnis

### 1. **Saat Membuat Work Order**
- User Service memasukkan **Sparepart yang Dibawa** → Tersimpan di `work_order_spareparts`
  - Field: `quantity_brought` (jumlah yang dibawa)

### 2. **Saat Close Work Order**
- User Service melakukan **Validasi Sparepart** → Mencatat `quantity_used` (jumlah yang digunakan)
- Sistem otomatis menghitung selisih: `quantity_return = quantity_brought - quantity_used`
- **Jika ada selisih** (quantity_return > 0):
  - Sistem otomatis membuat record di `work_order_sparepart_returns` dengan status `PENDING`
  - Work Order tetap bisa ditutup, tapi ada notifikasi untuk pengembalian

### 3. **Halaman Warehouse - Sparepart Returns**
- Warehouse melihat daftar sparepart yang perlu dikembalikan (status `PENDING`)
- Warehouse bisa:
  - Melihat detail sparepart yang dikembalikan
  - Konfirmasi pengembalian (update status ke `CONFIRMED`)
  - Update inventory setelah konfirmasi
  - Tambahkan catatan jika ada kondisi khusus

### 4. **Setelah Konfirmasi**
- Status return berubah menjadi `CONFIRMED`
- Inventory sparepart otomatis diupdate (jika ada integrasi dengan inventory)
- Work Order tetap bisa dilihat history pengembaliannya

## 🗄️ Struktur Database

### Tabel: `work_order_sparepart_returns`
```sql
CREATE TABLE `work_order_sparepart_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_id` int(11) NOT NULL,
  `work_order_sparepart_id` int(11) NOT NULL,
  `sparepart_code` varchar(50) NOT NULL,
  `sparepart_name` varchar(255) NOT NULL,
  `quantity_brought` int(11) NOT NULL,
  `quantity_used` int(11) NOT NULL DEFAULT 0,
  `quantity_return` int(11) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `status` enum('PENDING','CONFIRMED','CANCELLED') DEFAULT 'PENDING',
  `return_notes` text,
  `confirmed_by` int(11) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_id` (`work_order_id`),
  KEY `work_order_sparepart_id` (`work_order_sparepart_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 📁 File Structure

```
app/
├── Models/
│   └── WorkOrderSparepartReturnModel.php (NEW)
├── Controllers/
│   └── Warehouse/
│       └── SparepartReturnController.php (NEW)
└── Views/
    └── warehouse/
        └── sparepart_returns/
            ├── index.php (NEW)
            └── detail.php (NEW)
```

## 🔄 Flow Diagram

```
[Service: Create WO] 
    ↓
[Service: Input Sparepart Brought]
    ↓
[Service: Close WO + Validasi Sparepart]
    ↓
[System: Calculate Return = Brought - Used]
    ↓
[System: Auto-create Return Record (PENDING)]
    ↓
[Warehouse: View Pending Returns]
    ↓
[Warehouse: Confirm Return]
    ↓
[System: Update Inventory]
    ↓
[Status: CONFIRMED]
```

## ✅ Features

1. **Auto-detect Returns**: Sistem otomatis membuat return record saat validasi
2. **Warehouse Dashboard**: Daftar semua pending returns dengan filter
3. **Return Confirmation**: Warehouse bisa konfirmasi dengan catatan
4. **Inventory Integration**: Update inventory setelah konfirmasi (optional)
5. **History Tracking**: Semua return tercatat dengan timestamp dan user

## 🔐 Permission

- **Warehouse Staff/Head**: Bisa melihat dan konfirmasi returns
- **Service Staff**: Bisa melihat returns untuk WO mereka (read-only)

