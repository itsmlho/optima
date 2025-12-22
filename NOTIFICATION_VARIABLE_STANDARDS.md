# Notification Variable Naming Standards

## 📋 GLOBAL VARIABLE STANDARDS

Untuk menghindari inkonsistensi, gunakan nama variable yang SAMA di semua notification.

### 🔹 Core Variables (Wajib untuk semua notifications)

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `module` | string | Module name | `'inventory'`, `'service'`, `'purchasing'` |
| `url` | string | Link to detail page | `base_url('/path/to/detail')` |

### 🔹 Unit Information

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `unit_id` | int | Unit ID dari database | `16` |
| `no_unit` | string | Nomor unit | `'UNIT-001'` |
| `unit_type` | string | Jenis unit | `'Forklift'`, `'Crane'` |
| `unit_model` | string | Model unit | `'FD30T15'` |

**❌ JANGAN PAKAI:**
- `unit_code` (use `no_unit`)
- `unit_no` (use `no_unit`)
- `unit_number` (use `no_unit`)

### 🔹 Attachment Information

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `attachment_id` | int | Attachment ID dari database | `53` |
| `tipe_item` | string | Jenis attachment | `'Battery'`, `'Charger'` |
| `serial_number` | string | Serial number | `'ZJ33H-B5'` |
| `merk` | string | Brand/Merk | `'HELI'`, `'CASCADE'` |
| `model` | string | Model | `'PAPER roll CLAMP'` |
| `attachment_info` | string | Gabungan merk + model | `'HELI PAPER roll CLAMP'` |

### 🔹 User Information

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `user_id` | int | User ID dari database | `1` |
| `username` | string | Username | `'admin'` |
| `user_email` | string | Email | `'admin@optima.com'` |
| `user_fullname` | string | Full name | `'John Doe'` |

**Action-specific user fields:**
- `created_by` - Username yang create
- `updated_by` - Username yang update
- `deleted_by` - Username yang delete
- `performed_by` - Username yang perform action
- `assigned_by` - Username yang assign
- `approved_by` - Username yang approve
- `verified_by` - Username yang verify

### 🔹 Dates & Timestamps

| Variable | Type | Description | Format |
|----------|------|-------------|--------|
| `created_at` | datetime | Waktu dibuat | `'2025-12-22 10:30:00'` |
| `updated_at` | datetime | Waktu diupdate | `'2025-12-22 10:30:00'` |
| `performed_at` | datetime | Waktu action dilakukan | `'2025-12-22 10:30:00'` |
| `scheduled_date` | date | Tanggal dijadwalkan | `'2025-12-22'` |
| `due_date` | date | Tanggal deadline | `'2025-12-22'` |
| `completion_date` | date | Tanggal selesai | `'2025-12-22'` |

### 🔹 Location Information

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `from_location` | string | Lokasi asal | `'Workshop'` |
| `to_location` | string | Lokasi tujuan | `'Area 1'` |
| `current_location` | string | Lokasi saat ini | `'POS 1'` |

**❌ JANGAN PAKAI:**
- `old_location` (use `from_location`)
- `new_location` (use `to_location`)

### 🔹 Customer/Supplier Information

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `customer_id` | int | Customer ID | `5` |
| `customer_name` | string | Nama customer | `'PT ABC'` |
| `supplier_id` | int | Supplier ID | `10` |
| `supplier_name` | string | Nama supplier | `'PT XYZ'` |

**❌ JANGAN PAKAI:**
- `pelanggan` (use `customer_name`)
- `nama_supplier` (use `supplier_name`)

### 🔹 Document Numbers

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `spk_number` | string | Nomor SPK | `'SPK-2025-001'` |
| `po_number` | string | Nomor PO | `'PO-2025-001'` |
| `wo_number` | string | Nomor WO | `'WO-2025-001'` |
| `di_number` | string | Nomor DI | `'DI-2025-001'` |
| `invoice_number` | string | Nomor Invoice | `'INV-2025-001'` |

**❌ JANGAN PAKAI:**
- `nomor_spk` (use `spk_number`)
- `nomor_po` (use `po_number`)
- `nomor_wo` (use `wo_number`)

### 🔹 Status Information

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `old_status` | string | Status lama | `'Pending'` |
| `new_status` | string | Status baru | `'Completed'` |
| `current_status` | string | Status saat ini | `'In Progress'` |

### 🔹 Quantity & Measurement

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `quantity` | int/float | Jumlah | `5`, `2.5` |
| `unit` | string | Satuan | `'pcs'`, `'box'`, `'kg'` |

**❌ JANGAN PAKAI:**
- `qty` (use `quantity`)
- `jumlah` (use `quantity`)

## 🎯 CONTOH IMPLEMENTASI

### ✅ BENAR - attachment_swapped:

```php
notify_attachment_swapped([
    'module' => 'inventory',
    'attachment_id' => $attachmentId,
    'tipe_item' => $movingAttachment['tipe_item'],
    'serial_number' => $movingAttachment['serial_number'],
    'merk' => $movingAttachment['merk'],
    'model' => $movingAttachment['model'],
    'attachment_info' => ($movingAttachment['merk'] ?? '') . ' ' . ($movingAttachment['model'] ?? ''),
    'from_unit_id' => $actualFromUnitId,
    'from_unit_number' => $fromUnit['no_unit'],  // ✅ Descriptive
    'to_unit_id' => $toUnitId,
    'to_unit_number' => $toUnit['no_unit'],      // ✅ Descriptive
    'reason' => $reason,
    'performed_by' => session('username'),
    'performed_at' => date('Y-m-d H:i:s'),
    'url' => base_url('/warehouse/attachment/view/' . $attachmentId)
]);
```

### ❌ SALAH:

```php
notify_attachment_swapped([
    'id' => $attachmentId,              // ❌ Ambiguous
    'old_unit' => $fromUnit,            // ❌ Use from_unit_number
    'new_unit' => $toUnit,              // ❌ Use to_unit_number
    'swapped_by' => session('user'),    // ❌ Use performed_by
]);
```

## 📝 MIGRATION CHECKLIST

1. ✅ Audit semua notification calls
2. ✅ Update notification_helper.php - standardize variable names
3. ✅ Update controller calls - use standard names
4. ✅ Update notification templates di database
5. ✅ Test setiap notification
6. ✅ Update documentation

## 🔄 BACKWARD COMPATIBILITY

Untuk transisi, gunakan fallback:

```php
'no_unit' => $data['no_unit'] ?? $data['unit_code'] ?? $data['unit_number'] ?? '',
```

Setelah semua migration selesai, remove fallback.
