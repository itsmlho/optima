# 🔔 AUDIT: CROSS-DIVISION NOTIFICATIONS

**Audit berdasarkan alur bisnis yang dijelaskan user**  
**Tujuan:** Memastikan notifikasi terkirim ke divisi yang tepat sesuai workflow

---

## 📋 LEGEND

- ✅ **SUDAH ADA & BERFUNGSI** - Notification sudah diimplementasi dan target divisi sudah benar
- ⚠️ **ADA TAPI PERLU CEK TARGET** - Notification ada tapi perlu verifikasi target_users/roles
- ❌ **BELUM ADA** - Notification belum diimplementasi
- 🔍 **PERLU INVESTIGASI** - Perlu pengecekan lebih detail

---

## 1️⃣ MARKETING DIVISION

### A. CRUD Quotation, Customer, Kontrak → Intern Marketing

| Action | Trigger Event | Target | Status | Controller | Line |
|--------|---------------|--------|--------|------------|------|
| Create Quotation | `quotation_created` | Marketing (intern) | ✅ **ADA** | Marketing.php | ~725 |
| Create Customer | `customer_created` | Marketing (intern) | ✅ **ADA** | CustomerManagementController.php | ~180 |
| Update Customer | `customer_updated` | Marketing (intern) | ✅ **ADA** | CustomerManagementController.php | ~250 |
| Delete Customer | `customer_deleted` | Marketing (intern) | ✅ **ADA** | CustomerManagementController.php | ~320 |
| Create Contract | `customer_contract_created` | Marketing (intern) | ✅ **ADA** | Marketing.php | ~7415 |

**✅ KESIMPULAN:** Notifikasi intern Marketing SUDAH LENGKAP!

---

### B. Create SPK → Divisi Service

| Action | Trigger Event | Target | Status | Issue |
|--------|---------------|--------|--------|-------|
| Create SPK (manual) | `spk_created` | **Service** | ⚠️ **CEK TARGET** | Target divisi perlu diverifikasi |
| Create SPK (from quotation) | `spk_created` | **Service** | ❌ **BELUM ADA** | Tidak ada notification call |

**🔍 TEMUAN:**

1. **spkCreate()** (line 3657): ✅ Ada call `sendSpkNotification()`
2. **createSPKFromQuotation()** (line 4106): ❌ **TIDAK ADA** notification

**❓ PERTANYAAN KRITIS:**
- Apakah `target_users` di notification_rules untuk `spk_created` sudah set ke **divisi Service**?
- Format target: `{"divisions": ["SERVICE"]}` atau `{"roles": ["service_manager"]}`?

**🛠️ ACTION NEEDED:**
1. Tambahkan notification di `createSPKFromQuotation()`
2. Verifikasi target_users di database untuk `spk_created` → harus ke Service

---

### C. Create DI → Divisi Operational

| Action | Trigger Event | Target | Status | Issue |
|--------|---------------|--------|--------|-------|
| Create DI | `di_created` | **Operational** | ❌ **TIDAK DITEMUKAN** | Fungsi create DI tidak ditemukan |

**🔍 TEMUAN:**
- Tidak ditemukan fungsi untuk create DI di Marketing controller
- DI mungkin dibuat langsung di Operational?
- Atau DI dibuat otomatis saat SPK Ready?

**❓ PERTANYAAN:**
- Dimana fungsi create DI? Apakah di Marketing atau Operational?
- Atau DI dibuat otomatis dari workflow SPK?

**🛠️ ACTION NEEDED:**
1. Identifikasi fungsi create DI
2. Tambahkan `notify_di_created()` dengan target Operational

---

## 2️⃣ SERVICE DIVISION

### A. CRUD Workorder (Complain) & Area Management → Intern Service

| Action | Trigger Event | Target | Status | Location |
|--------|---------------|--------|--------|----------|
| Create Workorder | `work_order_assigned` | Service (intern) | ❌ **BELUM ADA** | WorkOrderController.php:767 |
| Assign Employee | `work_order_assigned` | Service (intern) | ❌ **BELUM ADA** | WorkOrderController.php:1839 |
| Update Status | `work_order_in_progress` / `completed` / `cancelled` | Service (intern) | ❌ **BELUM ADA** | WorkOrderController.php:628 |
| Assign Employee to Area | `employee_assigned` | Service (intern) | ✅ **ADA** | ServiceAreaManagementController.php |

**🛠️ ACTION NEEDED:**
- Tambahkan 3 notification untuk work_order (create, assign, update status)

---

### B. SPK STAGES → CROSS-DIVISION NOTIFICATIONS ⭐ PENTING!

#### 🔧 STAGE 1: Unit Preparation

**Workflow:** Service memilih unit, charger, baterai

| Target Divisi | Notification | Purpose | Status |
|---------------|-------------|---------|--------|
| **Marketing** | SPK berhasil (unit preparation complete) | Update progress SPK | ❌ **BELUM ADA** |
| **Warehouse** | Report unit, charger, baterai yang digunakan | Inventory update | ❌ **BELUM ADA** |

**📍 Location:** Service.php → `spkApproveStage()` line 1077  
**Current Implementation:** ❌ Tidak ada notification call di fungsi ini!

**💡 EXPECTED BEHAVIOR:**
```php
// Setelah approve persiapan_unit stage
notify_spk_stage_completed([
    'stage' => 'persiapan_unit',
    'spk_id' => $spkId,
    'target_divisions' => ['MARKETING', 'WAREHOUSE']
]);

// atau lebih spesifik:
notify_unit_prep_completed([
    'spk_id' => $spkId,
    'unit_id' => $unitId,
    'battery_id' => $batteryId,
    'charger_id' => $chargerId,
    'target_divisions' => ['MARKETING', 'WAREHOUSE']
]);
```

---

#### 🏭 STAGE 2: Unit Fabrication

**Workflow:** Service memilih attachment

| Target Divisi | Notification | Purpose | Status |
|---------------|-------------|---------|--------|
| **Marketing** | SPK berhasil (fabrication complete) | Update progress SPK | ❌ **BELUM ADA** |
| **Warehouse** | Report attachment yang dipilih | Inventory update | ❌ **BELUM ADA** |

**📍 Location:** Service.php → `spkApproveStage()` line 1077  
**Current Implementation:** ❌ Tidak ada notification call!

**💡 EXPECTED:**
```php
notify_spk_fabrication_completed([
    'spk_id' => $spkId,
    'attachment_id' => $attachmentId,
    'target_divisions' => ['MARKETING', 'WAREHOUSE']
]);
```

---

#### 🔍 STAGE 3: PDI Inspection

**Workflow:** Service melakukan PDI inspection

| Target Divisi | Notification | Purpose | Status |
|---------------|-------------|---------|--------|
| **Marketing** | SPK berhasil (PDI complete) | Update progress SPK | ❌ **BELUM ADA** |
| **Operational** | Sudah melakukan PDI pada SPK (A) | Siap untuk delivery planning | ❌ **BELUM ADA** |

**📍 Location:** Service.php → `spkApproveStage()` line 1077  
**Current Implementation:** ❌ Tidak ada notification call!

**💡 EXPECTED:**
```php
notify_spk_pdi_completed([
    'spk_id' => $spkId,
    'spk_number' => $nomorSpk,
    'target_divisions' => ['MARKETING', 'OPERATIONAL']
]);
```

---

#### ✅ STAGE 4: SPK Ready

**Workflow:** SPK siap untuk dijadwalkan DI

| Target Divisi | Notification | Purpose | Status |
|---------------|-------------|---------|--------|
| **Marketing** | SPK Ready - segera dijadwalkan untuk DI | Create delivery instruction | ❌ **BELUM ADA** |

**📍 Location:** Service.php → fungsi yang mengubah SPK status ke READY?  
**Current Implementation:** 🔍 **PERLU DICARI** - fungsi mana yang set SPK status = READY?

**💡 EXPECTED:**
```php
notify_spk_ready([
    'spk_id' => $spkId,
    'spk_number' => $nomorSpk,
    'customer' => $customerName,
    'target_divisions' => ['MARKETING']
]);
```

---

### C. Workorder Complain Stages → Warehouse

#### 🔧 Verifikasi Unit & Validasi Sparepart

| Action | Target | Status | User Feedback |
|--------|--------|--------|---------------|
| Verifikasi Unit | **Warehouse** | ✅ **SUDAH ADA** | User bilang sudah test, ada notifnya |
| Validasi Sparepart | **Warehouse** | 🔍 **PERLU KONFIRMASI** | User test verifikasi unit, tapi sparepart? |

**📍 Location:** 
- WorkOrderController.php → `saveUnitVerification()` line 2471
- WorkOrderController.php → `saveSparepartValidation()` line 3032

**✅ USER CONFIRMATION:** "tadi saya test untuk verifikasi unit sudah ada notifnya"

**🔍 NEED TO VERIFY:** Apakah `saveSparepartValidation()` juga sudah ada notification?

---

## 3️⃣ OPERATIONAL DIVISION

### A. Stages Plan Shipping → Marketing

| Action | Trigger Event | Target | Status | Location |
|--------|---------------|--------|--------|----------|
| Plan Shipping (DI Status) | `di_submitted`? | **Marketing** | ❌ **PERLU IMPLEMENTASI** | Operational.php |

**🔍 TEMUAN:**
- `diUpdateStatus()` line 380 sudah ada notifications, tapi hanya untuk delivery stages
- Tidak ada notification khusus untuk "Plan Shipping" stage
- Mungkin perlu trigger baru: `di_plan_shipping` atau gunakan `di_submitted`?

**❓ PERTANYAAN:**
- "Plan Shipping" itu stage DI yang mana?
- Apakah sama dengan `di_submitted` atau status khusus?

---

### B. DI Completed → Marketing

| Action | Trigger Event | Target | Status | Location |
|--------|---------------|--------|--------|----------|
| DI Completed | `delivery_completed` | **Marketing** | ⚠️ **CEK TARGET** | Operational.php:458 |

**🔍 TEMUAN:**
- ✅ `notify_delivery_completed()` SUDAH ADA di line 458
- ❓ Target divisi perlu diverifikasi - apakah sudah include Marketing?

**🛠️ ACTION NEEDED:**
- Verifikasi `target_users` untuk `delivery_completed` di database
- Pastikan include: `{"divisions": ["MARKETING", "OPERATIONAL"]}`

---

## 4️⃣ PURCHASING DIVISION

### A. Create PO → Marketing & Intern Purchasing

| Action | Type | Target | Status | Location |
|--------|------|--------|--------|----------|
| Create PO Unit | `po_unit_created` | Marketing + Purchasing | ⚠️ **CEK TARGET** | Purchasing.php:2882 |
| Create PO Attachment | `po_attachment_created` | Marketing + Purchasing | ⚠️ **CEK TARGET** | Purchasing.php:3173 |
| Create PO Sparepart | `po_sparepart_created` | Marketing + Purchasing | ⚠️ **CEK TARGET** | Purchasing.php:3450 |

**🔍 TEMUAN:**
- ✅ Notification calls SUDAH ADA
- ❓ Target divisi perlu diverifikasi

**🛠️ ACTION NEEDED:**
- Cek database `notification_rules` untuk ketiga trigger di atas
- Pastikan target: `{"divisions": ["MARKETING", "PURCHASING"]}`

---

### B. Create Delivery & Sampai → Warehouse & Intern

| Action | Target | Status | Issue |
|--------|--------|--------|-------|
| Create Delivery (dari PO) | Warehouse + Purchasing | ❌ **TIDAK DITEMUKAN** | Fungsi tidak ditemukan |
| PO Sampai/Received | Warehouse + Purchasing | ❌ **TIDAK DITEMUKAN** | Fungsi tidak ditemukan |

**🔍 TEMUAN:**
- Tidak ditemukan fungsi "create delivery" dari PO
- Mungkin ada di controller lain?
- Atau workflow langsung ke warehouse verification?

**❓ PERTANYAAN:**
- Dimana fungsi create delivery dari PO?
- Atau PO langsung masuk ke warehouse verification?

---

### C. Re-verify → Warehouse

| Action | Trigger | Target | Status |
|--------|---------|--------|--------|
| Re-verify PO items | `po_verification_requested`? | **Warehouse** | ❌ **TIDAK ADA TRIGGER** |

**🔍 TEMUAN:**
- Tidak ada trigger event untuk "re-verify"
- Perlu tambah trigger baru atau gunakan existing?

---

## 5️⃣ WAREHOUSE DIVISION

### A. Edit Unit → Service

| Action | Trigger Event | Target | Status | Location |
|--------|---------------|--------|--------|----------|
| Update Unit Status/Lokasi | `inventory_unit_status_changed` | **Service** | ⚠️ **CEK TARGET** | Warehouse.php:822 |

**🔍 TEMUAN:**
- ✅ `notify_inventory_unit_status_changed()` SUDAH ADA di line 851
- ❓ Target divisi perlu diverifikasi

**🛠️ ACTION NEEDED:**
- Cek `target_users` untuk `inventory_unit_status_changed`
- Pastikan include Service: `{"divisions": ["SERVICE", "WAREHOUSE"]}`

---

### B. Attachment Management → Service & Intern

| Action | Trigger Event | Target | Status | Controller |
|--------|---------------|--------|--------|------------|
| Add Attachment | `attachment_added` | Service + Warehouse | ❌ **BELUM ADA** | Service.php:2653 |
| Install/Attach | `attachment_attached` | Service + Warehouse | ❌ **BELUM ADA** | Service.php:137 |
| Detach | `attachment_detached` | Service + Warehouse | ❌ **BELUM ADA** | Service.php:137 |
| Swap | `attachment_swapped` | Service + Warehouse | ❌ **TIDAK DITEMUKAN** | - |

**🛠️ ACTION NEEDED:**
- Tambahkan notification calls di 3 fungsi yang ada
- Cari fungsi swap attachment

---

### C. Confirm Return Sparepart → Service

| Action | Trigger Event | Target | Status | Location |
|--------|---------------|--------|--------|----------|
| Confirm Return Sparepart | `sparepart_returned`? | **Service** | ❌ **TIDAK ADA TRIGGER** | Warehouse (sparepart-usage) |

**🔍 TEMUAN:**
- Tidak ada trigger event untuk "sparepart returned"
- Perlu tambah trigger baru

**💡 SUGGESTION:**
- Create trigger: `sparepart_returned`
- Target: Service + Warehouse

---

### D. PO Verification → Purchasing

| Action | Trigger Event | Target | Status | Location |
|--------|---------------|--------|--------|----------|
| Success Verification | `po_verified` | **Purchasing** | ⚠️ **CEK TARGET** | WarehousePO.php:1172 |
| Reject Verification | `po_rejected` | **Purchasing** | ⚠️ **CEK TARGET** | Purchasing.php:2673 |

**🔍 TEMUAN:**
- ✅ `notify_po_verified()` SUDAH ADA (3x: unit, attachment, sparepart)
- ✅ `notify_po_rejected()` SUDAH ADA
- ❓ Target divisi perlu diverifikasi

**🛠️ ACTION NEEDED:**
- Cek `target_users` untuk kedua trigger
- Pastikan include: `{"divisions": ["PURCHASING", "WAREHOUSE"]}`

---

## 6️⃣ SILO (PERIZINAN)

### Berhasil Terbitkan SILO → Marketing & Warehouse

| Action | Trigger Event | Target | Status | Location |
|--------|---------------|--------|--------|----------|
| SILO Terbit | `silo_issued`? | Marketing + Warehouse | ❌ **TIDAK ADA** | Perizinan.php:267 |

**🔍 TEMUAN:**
- ✅ Fungsi `createSilo()` DITEMUKAN di line 267
- ❌ **TIDAK ADA** notification call
- ❌ **TIDAK ADA** trigger event `silo_issued` di database

**🛠️ ACTION NEEDED:**
1. Buat trigger event: `silo_issued`
2. Buat fungsi: `notify_silo_issued()`
3. Tambahkan call di `createSilo()` dan `updateSiloStatus()`
4. Target: Marketing + Warehouse

---

## 📊 SUMMARY AUDIT

| Divisi | Total Flows | ✅ Sudah Ada | ⚠️ Perlu Cek Target | ❌ Belum Ada | 🔍 Need Investigation |
|--------|-------------|-------------|---------------------|-------------|----------------------|
| **Marketing** | 8 | 5 | 1 | 1 | 1 |
| **Service** | 11 | 2 | 0 | 9 | 0 |
| **Operational** | 2 | 1 | 1 | 0 | 0 |
| **Purchasing** | 6 | 3 | 3 | 0 | 0 |
| **Warehouse** | 7 | 3 | 3 | 1 | 0 |
| **Silo** | 1 | 0 | 0 | 1 | 0 |
| **TOTAL** | **35** | **14 (40%)** | **8 (23%)** | **12 (34%)** | **1 (3%)** |

---

## 🎯 PRIORITAS IMPLEMENTASI

### 🔥 CRITICAL (Workflow Utama)

#### 1. SPK STAGES (Service → Marketing/Warehouse/Operational)
**Impact:** HIGH - Core workflow SPK  
**Affected:** 4 stages × 2-3 divisions = 8-12 notifications

**Tasks:**
- [ ] Add notification di `spkApproveStage()` untuk setiap stage
- [ ] Create targeted notifications per stage:
  - `notify_spk_unit_prep_completed()` → Marketing + Warehouse
  - `notify_spk_fabrication_completed()` → Marketing + Warehouse
  - `notify_spk_pdi_completed()` → Marketing + Operational
  - `notify_spk_ready()` → Marketing

---

#### 2. createSPKFromQuotation Notification
**Impact:** HIGH - Batch SPK creation dari quotation  
**Affected:** Service division

**Tasks:**
- [ ] Add `sendSpkNotification()` call di loop create SPK (line ~4320)

---

#### 3. SILO Issued (Perizinan → Marketing + Warehouse)
**Impact:** MEDIUM - Compliance tracking  
**Affected:** Marketing + Warehouse

**Tasks:**
- [ ] Create trigger: `silo_issued`
- [ ] Create function: `notify_silo_issued()`
- [ ] Add call di `createSilo()` dan `updateSiloStatus()`

---

### ⚠️ HIGH (Cross-Division Notifications)

#### 4. Warehouse Attachment Management → Service
**Impact:** HIGH - Inventory tracking  
**Affected:** Service + Warehouse

**Tasks:**
- [ ] Add `notify_attachment_added()` di Service.php:2653
- [ ] Add `notify_attachment_attached()` di Service.php:137
- [ ] Add `notify_attachment_detached()` di Service.php:137
- [ ] Find/create swap attachment function

---

#### 5. Work Order Complete Cycle
**Impact:** HIGH - Customer complaints  
**Affected:** Service (intern)

**Tasks:**
- [ ] Add notifications di WorkOrderController:
  - `notify_work_order_assigned()` (store, assignEmployees)
  - `notify_work_order_in_progress()` (updateStatus)
  - `notify_work_order_completed()` (updateStatus)
  - `notify_work_order_cancelled()` (updateStatus)

---

### 📋 MEDIUM (Target Verification)

#### 6. Verify Target Divisions for Existing Notifications

**Tasks:**
- [ ] Check `po_unit_created` → Marketing + Purchasing
- [ ] Check `po_attachment_created` → Marketing + Purchasing
- [ ] Check `po_sparepart_created` → Marketing + Purchasing
- [ ] Check `po_verified` → Purchasing + Warehouse
- [ ] Check `po_rejected` → Purchasing + Warehouse
- [ ] Check `delivery_completed` → Marketing + Operational
- [ ] Check `inventory_unit_status_changed` → Service + Warehouse
- [ ] Check `spk_created` → Service (not Marketing!)

**SQL Query untuk Cek:**
```sql
SELECT 
    id, 
    name, 
    trigger_event, 
    target_users,
    target_divisions
FROM notification_rules 
WHERE trigger_event IN (
    'po_unit_created', 'po_attachment_created', 'po_sparepart_created',
    'po_verified', 'po_rejected', 'delivery_completed',
    'inventory_unit_status_changed', 'spk_created'
)
AND is_active = 1;
```

---

### 🔍 INVESTIGATION NEEDED

#### 7. Missing Functions & Workflows

**Questions for User:**
1. **DI Create:** Dimana fungsi create DI? Marketing atau Operational?
2. **Plan Shipping:** Stage DI mana yang dimaksud "Plan Shipping"?
3. **PO Delivery:** Dimana fungsi create delivery dari PO? Atau langsung verification?
4. **Sparepart Return:** URL `/warehouse/sparepart-usage` - fungsi confirm return ada dimana?
5. **Attachment Swap:** Fungsi swap attachment ada dimana?

---

## 🔗 TARGET DIVISIONS FORMAT

Untuk cross-division notifications, gunakan format ini di `notification_rules.target_users`:

```json
{
    "divisions": ["MARKETING", "SERVICE"],
    "roles": ["admin", "manager"]
}
```

Atau jika hanya division:
```json
{
    "divisions": ["WAREHOUSE"]
}
```

**PENTING:** Pastikan field `target_divisions` di table `notification_rules` sudah ada!

---

## ✅ NEXT STEPS

### Immediate Actions:
1. **Verify User Feedback:** Konfirmasi dengan user untuk 7 pertanyaan di "Investigation Needed"
2. **Check Database:** Run SQL query untuk cek target_users yang sudah ada
3. **Prioritize:** Fokus ke CRITICAL items dulu (SPK Stages + createSPKFromQuotation)

### Implementation Order:
1. ✅ Fix SPK Stages notifications (CRITICAL)
2. ✅ Add createSPKFromQuotation notification
3. ✅ Implement SILO issued notification
4. ⚠️ Verify existing notifications targets
5. ✅ Add Warehouse attachment notifications
6. ✅ Complete Work Order cycle
7. 🔍 Investigation & clarification

---

*Last Updated: 19 Desember 2025*  
*Audit by: GitHub Copilot AI*
