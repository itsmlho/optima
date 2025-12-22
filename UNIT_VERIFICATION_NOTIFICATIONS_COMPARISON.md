# Perbedaan 2 Notifikasi Unit Verification

## Overview
Ada **DUA** event notifikasi yang berbeda untuk Unit Verification:

### 1. `unit_verification_saved` ✅
**Trigger:** SETIAP kali unit verification disimpan  
**Kondisi:** Selalu trigger (tidak peduli ada perubahan atau tidak)  
**Tujuan:** Informasi bahwa verifikasi telah selesai dilakukan

**Notification Rules:**
- Service Division (success) 
- Manager Role (info)

**Message Format:**
```
Title: Unit Verification Disimpan - WO: WO-2024-001

Message:
Unit FL-001 telah diverifikasi.

Status: COMPLETED
Oleh: Admin Service
Tanggal: 2025-12-22 10:30:00
```

### 2. `work_order_unit_verified` ✅
**Trigger:** HANYA jika ada perubahan data  
**Kondisi:** Hanya trigger ketika data lapangan berbeda dengan database  
**Tujuan:** Alert untuk monitoring discrepancy data

**Notification Rules:**
- Service Division (info)
- Warehouse Division (info) - khusus monitor attachment changes
- Manager Role (warning) - dengan count perubahan

**Message Format:**
```
Title: Verifikasi Unit WO: WO-2024-001

Message:
Perubahan data yang dilakukan pada No Unit FL-001:
- Serial Number: SN123 → SN456
- Attachment: ATT-001 - Fork → ATT-002 - Side Shifter
- Charger: - → Brand X Model Y

Oleh: Admin Service

Total 3 perubahan terdeteksi. (Manager only)
```

## Comparison Table

| Aspect | unit_verification_saved | work_order_unit_verified |
|--------|------------------------|--------------------------|
| **Trigger** | Setiap verifikasi disimpan | Hanya jika ada perubahan |
| **Frequency** | Always (100% of time) | Conditional (only when changes detected) |
| **Purpose** | Completion notification | Data discrepancy alert |
| **Type** | success/info | info/warning |
| **Detail Level** | Basic (status, user, date) | Detailed (list all changes) |
| **Target** | Service, Manager | Service, Warehouse, Manager |
| **Use Case** | "Task completed" | "Data monitoring" |

## Flow Example

### Scenario A: Verifikasi TANPA Perubahan Data
**User Action:** Buka WO verification → Tidak ubah apa-apa → Save

**Notifications Sent:**
- ✅ `unit_verification_saved` → "Unit FL-001 telah diverifikasi"
- ❌ `work_order_unit_verified` → TIDAK trigger (karena tidak ada perubahan)

**Recipients:**
- Service Division: 1 notification
- Manager: 1 notification
- **Total: 2 notifications**

### Scenario B: Verifikasi DENGAN Perubahan Data
**User Action:** Buka WO verification → Ubah serial number dan ganti attachment → Save

**Notifications Sent:**
- ✅ `unit_verification_saved` → "Unit FL-001 telah diverifikasi"
- ✅ `work_order_unit_verified` → "Perubahan data: Serial Number..., Attachment..."

**Recipients:**
- Service Division: 2 notifications (1 completion + 1 changes)
- Warehouse Division: 1 notification (changes only)
- Manager: 2 notifications (1 completion + 1 changes with count)
- **Total: 5 notifications**

## Code Location

### WorkOrderController.php (lines 3503-3540)

```php
// ALWAYS send: unit_verification_saved
if (function_exists('notify_unit_verification_saved') && $workOrder) {
    notify_unit_verification_saved([
        'id' => $workOrderId,
        'wo_number' => $workOrder['work_order_number'] ?? '',
        'unit_code' => $workOrder['unit_code'] ?? '',
        'verification_status' => 'COMPLETED',
        'verified_by' => session('username') ?? session('user_id'),
        'verification_date' => date('Y-m-d H:i:s'),
        'url' => base_url('/service/work-orders/view/' . $workOrderId)
    ]);
}

// CONDITIONAL send: work_order_unit_verified (only if changes exist)
if (!empty($allChanges)) {
    $changesList = implode("\n- ", $allChanges);
    
    if (function_exists('notify_work_order_unit_verified')) {
        notify_work_order_unit_verified([
            'work_order_id' => $workOrderId,
            'wo_number' => $workOrder['work_order_number'] ?? '',
            'unit_code' => $unitNo,
            'changes_count' => count($allChanges),
            'changes_list' => $changesList,
            'created_by' => session('username') ?? session('user_id'),
            'verified_at' => date('Y-m-d H:i:s'),
            'url' => base_url('/service/work-orders/view/' . $workOrderId)
        ]);
    }
}
```

## Summary

✅ **unit_verification_saved:**
- Event ID: 223
- Rules: 2 (Service, Manager)
- Purpose: Task completion notification
- Trigger: Always

✅ **work_order_unit_verified:**
- Event ID: 111  
- Rules: 3 (Service, Warehouse, Manager)
- Purpose: Data monitoring & audit
- Trigger: Conditional (when changes detected)

---

**Kesimpulan:** Kedua notifikasi **sudah lengkap** dan siap digunakan! 🎉
- `unit_verification_saved` → untuk tracking completion
- `work_order_unit_verified` → untuk monitoring data changes
