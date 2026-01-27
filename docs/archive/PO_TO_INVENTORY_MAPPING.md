# Field Mapping: po_units → inventory_unit

## 📋 Overview
Dokumen ini menjelaskan mapping field antara tabel `po_units` (Purchase Order) dan `inventory_unit` (Inventory) setelah proses verifikasi.

## 🗂️ Field Mapping Table

| **po_units** | **inventory_unit** | **Type** | **Keterangan** |
|---|---|---|---|
| `id_po_unit` | - | INT | Primary Key PO Unit |
| - | `id_inventory_unit` | INT | Primary Key Inventory (Auto Increment) |
| `po_id` | `id_po` | INT | FK ke purchase_orders |
| `jenis_unit` | `departemen_id` | INT | FK ke departemen |
| `merk_unit` | - | INT | Merk di PO (tidak langsung ke inventory) |
| `model_unit_id` | `model_unit_id` | INT | FK ke model_unit ✅ |
| `tipe_unit_id` | `tipe_unit_id` | INT | FK ke tipe_unit ✅ |
| `serial_number_po` | `serial_number` | VARCHAR(100/255) | Serial number unit ✅ |
| `tahun_po` | `tahun_unit` | INT/YEAR | Tahun unit ✅ |
| `kapasitas_id` | `kapasitas_unit_id` | INT | FK ke kapasitas ⚠️ (beda nama) |
| - | `status_unit_id` | INT | Status unit (STOK/RENTAL/JUAL) - diisi saat insert |
| - | `lokasi_unit` | VARCHAR(255) | Lokasi unit - diisi saat insert |
| - | `tanggal_kirim` | DATETIME | Tanggal kirim - diisi saat insert |
| `keterangan` | `keterangan` | TEXT | Keterangan unit ✅ |

### 🔧 **Komponen Mast**
| **po_units** | **inventory_unit** | **Type** | **Keterangan** |
|---|---|---|---|
| `mast_id` | `model_mast_id` | INT | FK ke tipe_mast ⚠️ (beda nama) |
| `tinggi_mast_po` | `tinggi_mast` | VARCHAR(50) | Tinggi mast (4500mm atau 4.5m) ✅ **BARU** |
| `sn_mast_po` | `sn_mast` | VARCHAR(100/255) | Serial number mast ✅ |

### 🔧 **Komponen Engine/Mesin**
| **po_units** | **inventory_unit** | **Type** | **Keterangan** |
|---|---|---|---|
| `mesin_id` | `model_mesin_id` | INT | FK ke mesin ⚠️ (beda nama) |
| `sn_mesin_po` | `sn_mesin` | VARCHAR(100/255) | Serial number mesin ✅ |

### 🔧 **Komponen Lainnya**
| **po_units** | **inventory_unit** | **Type** | **Keterangan** |
|---|---|---|---|
| `ban_id` | `ban_id` | INT | FK ke tipe_ban ✅ |
| `roda_id` | `roda_id` | INT | FK ke jenis_roda ✅ |
| `valve_id` | `valve_id` | INT | FK ke valve ✅ |

### 🔋 **Komponen Battery & Charger**
> **Note:** Komponen battery dan charger di PO akan disimpan di tabel terpisah:
> - `po_batteries` → `inventory_attachments` (type: battery)
> - `po_chargers` → `inventory_attachments` (type: charger)

| **po_units** | **Tabel Tujuan** | **Keterangan** |
|---|---|---|
| `baterai_id` | `inventory_attachments` | Disimpan sebagai attachment type battery |
| `sn_baterai_po` | `inventory_attachments.serial_number` | SN battery |
| `charger_id` | `inventory_attachments` | Disimpan sebagai attachment type charger |
| `sn_charger_po` | `inventory_attachments.serial_number` | SN charger |
| `attachment_id` | `inventory_attachments` | Attachment lainnya (fork, mast, dll) |
| `sn_attachment_po` | `inventory_attachments.serial_number` | SN attachment |

### 📊 **Status & Timestamps**
| **po_units** | **inventory_unit** | **Type** | **Keterangan** |
|---|---|---|---|
| `status_verifikasi` | - | ENUM | Status di PO (Belum Dicek/Sesuai/Tidak Sesuai) |
| `status_penjualan` | - | ENUM | Kondisi (Baru/Bekas/Rekondisi) - untuk referensi |
| `created_at` | `created_at` | DATETIME | Timestamp dibuat ✅ |
| `updated_at` | `updated_at` | DATETIME | Timestamp diupdate ✅ |

## ⚠️ **Perbedaan Nama Field (PENTING!)**

Saat transfer data dari PO ke Inventory, perhatikan perbedaan nama:

```php
// Field mapping dengan nama berbeda:
'mast_id'        => 'model_mast_id'
'mesin_id'       => 'model_mesin_id'
'kapasitas_id'   => 'kapasitas_unit_id'
'serial_number_po' => 'serial_number'
'tahun_po'       => 'tahun_unit'
'sn_mast_po'     => 'sn_mast'
'sn_mesin_po'    => 'sn_mesin'
'tinggi_mast_po' => 'tinggi_mast'
```

## 🔄 **Proses Transfer Data (Workflow)**

```
1. User buat PO → data masuk ke po_units dengan status_verifikasi = 'Belum Dicek'

2. Warehouse melakukan verifikasi:
   - Jika SESUAI → status_verifikasi = 'Sesuai'
   - Jika TIDAK SESUAI → status_verifikasi = 'Tidak Sesuai' + catatan_verifikasi

3. Setelah semua item PO diverifikasi dan SESUAI:
   - Data dari po_units → inventory_unit
   - Field mapping sesuai tabel di atas
   - Serial number dari PO digunakan untuk inventory
   - Status unit diset (default: STOK)
   - Lokasi unit diisi
   
4. Komponen tambahan (battery, charger, attachment):
   - Disimpan ke inventory_attachments
   - Linked ke inventory_unit via FK
```

## 📝 **SQL Migration Example**

```sql
-- Add tinggi_mast_po to po_units
ALTER TABLE `po_units` 
ADD COLUMN `tinggi_mast_po` VARCHAR(50) NULL COMMENT 'Tinggi mast (contoh: 4500mm atau 4.5m)' 
AFTER `mast_id`;

-- Transfer data example (after verification)
INSERT INTO inventory_unit (
    serial_number, id_po, tahun_unit, status_unit_id, lokasi_unit,
    departemen_id, keterangan, tipe_unit_id, model_unit_id, kapasitas_unit_id,
    model_mast_id, tinggi_mast, sn_mast,
    model_mesin_id, sn_mesin,
    roda_id, ban_id, valve_id
)
SELECT 
    serial_number_po, po_id, tahun_po, 1 AS status_unit_id, 'GUDANG' AS lokasi_unit,
    jenis_unit, keterangan, tipe_unit_id, model_unit_id, kapasitas_id,
    mast_id, tinggi_mast_po, sn_mast_po,
    mesin_id, sn_mesin_po,
    roda_id, ban_id, valve_id
FROM po_units
WHERE status_verifikasi = 'Sesuai'
  AND id_po_unit = ?;
```

## ✅ **Checklist Validasi**

Sebelum transfer dari PO ke Inventory, pastikan:

- [ ] status_verifikasi = 'Sesuai'
- [ ] serial_number_po terisi (WAJIB)
- [ ] model_unit_id terisi (WAJIB)
- [ ] tipe_unit_id terisi
- [ ] kapasitas_id terisi
- [ ] Tidak ada duplikasi serial_number di inventory_unit
- [ ] Data master (departemen, model, tipe, kapasitas) valid

## 🔗 **Related Files**

- Model: `/app/Models/POUnitsModel.php`
- Model: `/app/Models/InventoryUnitModel.php`
- Form: `/app/Views/purchasing/forms/unit_form_fragment.php`
- Controller: `/app/Controllers/Purchasing.php`
- Migration: `/databases/fix_po_units_add_tinggi_mast.sql`

---
**Last Updated:** 2025-10-10
**Created By:** System Documentation

