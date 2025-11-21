# Planning: Warehouse Verification Enhancement

## 📋 Overview
Dokumen ini berisi planning untuk enhancement pada sistem Warehouse Verification, termasuk konfirmasi dialog, alur bisnis untuk status "Tidak Sesuai", dan implementasi format verifikasi baru untuk Attachment dan Sparepart.

---

## 1. ✅ Konfirmasi Dialog Setelah Submit Verifikasi

### 1.1 Tujuan
- Memberikan konfirmasi sebelum submit untuk mengurangi kesalahan
- Menampilkan summary data yang akan disubmit
- Memastikan user yakin dengan data yang akan dikirim

### 1.2 Implementasi

#### A. Dialog Konfirmasi (Swal/SweetAlert2)
```javascript
function showVerificationConfirmationDialog(finalStatus, snData, fullNotes, lokasiUnit) {
    // Build summary message
    let summaryHTML = `
        <div style="text-align: left;">
            <h6><strong>Summary Verifikasi:</strong></h6>
            <ul style="margin-bottom: 15px;">
                <li><strong>Status:</strong> <span class="badge ${finalStatus === 'Sesuai' ? 'bg-success' : 'bg-danger'}">${finalStatus}</span></li>
                <li><strong>Lokasi Unit:</strong> ${lokasiUnit}</li>
                ${snData.sn_unit ? `<li><strong>SN Unit:</strong> ${snData.sn_unit}</li>` : ''}
                ${snData.sn_mesin ? `<li><strong>SN Mesin:</strong> ${snData.sn_mesin}</li>` : ''}
                ${snData.sn_mast ? `<li><strong>SN Mast:</strong> ${snData.sn_mast}</li>` : ''}
            </ul>
            ${fullNotes.length > 0 ? `
                <h6><strong>Catatan Ketidaksesuaian:</strong></h6>
                <ul style="max-height: 200px; overflow-y: auto;">
                    ${fullNotes.map(note => `<li style="color: #dc3545;">${note}</li>`).join('')}
                </ul>
            ` : '<p style="color: #28a745;"><i class="fas fa-check-circle"></i> Semua data sesuai dengan database</p>'}
        </div>
    `;
    
    return Swal.fire({
        title: 'Konfirmasi Submit Verifikasi',
        html: summaryHTML,
        icon: finalStatus === 'Sesuai' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Submit Verifikasi',
        cancelButtonText: 'Batal',
        confirmButtonColor: finalStatus === 'Sesuai' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusConfirm: false,
        allowOutsideClick: false
    });
}
```

#### B. Integrasi ke submitUnitVerificationInline()
- Panggil `showVerificationConfirmationDialog()` sebelum `updateUnitStatusVerifikasi()`
- Jika user konfirmasi, lanjutkan submit
- Jika user cancel, batalkan proses

#### C. File yang Perlu Diubah
- `app/Views/warehouse/purchase_orders/tabs/unit_verification_script.php`
  - Modifikasi fungsi `submitUnitVerificationInline()`
  - Tambahkan fungsi `showVerificationConfirmationDialog()`

---

## 2. 🔄 Alur Logic Bisnis untuk Status "TIDAK SESUAI"

### 2.1 Analisis Alur Bisnis

#### Scenario 1: Data Tidak Sesuai (Minor)
- **Kondisi:** Ada perbedaan kecil antara database dan real lapangan
- **Contoh:** SN berbeda, spesifikasi sedikit berbeda
- **Aksi:** Update data langsung di database, catat perbedaan

#### Scenario 2: Data Tidak Sesuai (Major)
- **Kondisi:** Perbedaan signifikan atau data penting tidak sesuai
- **Contoh:** Model berbeda, unit rusak, komponen hilang
- **Aksi:** Perlu approval dari supervisor/purchasing

#### Scenario 3: Data Kosong di Database
- **Kondisi:** Data tidak ada di database tapi ada di real lapangan
- **Aksi:** Update data baru ke database

### 2.2 Rekomendasi Alur Bisnis

```
┌─────────────────────────────────────────────────────────┐
│  Warehouse Verifikasi Unit                              │
└─────────────────────────────────────────────────────────┘
                        │
                        ▼
        ┌───────────────────────────┐
        │  Status Verifikasi?      │
        └───────────────────────────┘
                │
        ┌───────┴────────┐
        │                │
        ▼                ▼
   [SESUAI]      [TIDAK SESUAI]
        │                │
        │                ├─────────────────┐
        │                │                 │
        ▼                ▼                 ▼
   Update DB    [Minor]           [Major]      [Data Kosong]
   + Inventory  │                 │            │
   + Log        │                 │            │
                ▼                 ▼            ▼
           Update DB      Notify Purchasing  Update DB
           + Log          + Create Ticket    + Log
                          + Hold Unit        + Inventory
```

### 2.3 Detail Implementasi

#### A. Database Schema Enhancement
```sql
-- Tambah kolom ke tabel po_units atau buat tabel baru
ALTER TABLE po_units ADD COLUMN verification_status ENUM('Sesuai', 'Tidak Sesuai', 'Pending Approval') DEFAULT NULL;
ALTER TABLE po_units ADD COLUMN verification_notes TEXT NULL;
ALTER TABLE po_units ADD COLUMN verification_date DATETIME NULL;
ALTER TABLE po_units ADD COLUMN verified_by INT NULL;
ALTER TABLE po_units ADD COLUMN requires_approval BOOLEAN DEFAULT FALSE;
ALTER TABLE po_units ADD COLUMN approval_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT NULL;
ALTER TABLE po_units ADD COLUMN approved_by INT NULL;
ALTER TABLE po_units ADD COLUMN approved_date DATETIME NULL;

-- Tabel untuk tracking ketidaksesuaian
CREATE TABLE IF NOT EXISTS verification_discrepancies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_unit_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    database_value TEXT,
    real_value TEXT,
    discrepancy_type ENUM('Minor', 'Major', 'Missing') DEFAULT 'Minor',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (po_unit_id) REFERENCES po_units(id_po_unit)
);
```

#### B. Logic Flow untuk "Tidak Sesuai"

**Option 1: Auto-Update dengan Notifikasi (Recommended)**
```javascript
if (finalStatus === 'Tidak Sesuai') {
    // Cek tingkat ketidaksesuaian
    const hasMajorDiscrepancy = checkMajorDiscrepancy(fullNotes);
    
    if (hasMajorDiscrepancy) {
        // Major discrepancy: Notify Purchasing + Hold Unit
        // 1. Update status unit ke "Pending Approval"
        // 2. Create notification untuk Purchasing team
        // 3. Hold unit (tidak masuk inventory dulu)
        // 4. Log discrepancy ke tabel verification_discrepancies
    } else {
        // Minor discrepancy: Update langsung + Notify
        // 1. Update data ke database
        // 2. Create notification untuk Purchasing (informasi saja)
        // 3. Unit masuk inventory dengan catatan
        // 4. Log discrepancy
    }
}
```

**Option 2: Manual Approval Required**
```javascript
if (finalStatus === 'Tidak Sesuai') {
    // 1. Update status ke "Pending Approval"
    // 2. Create approval request untuk Supervisor/Purchasing
    // 3. Hold unit
    // 4. User warehouse tidak bisa lanjut sampai approved
}
```

#### C. Notification System
- **Channel:** Email, In-app notification, atau WhatsApp (opsional)
- **Recipient:** 
  - Purchasing team (untuk major discrepancy)
  - Supervisor Warehouse (untuk approval)
- **Content:**
  - PO Number
  - Unit Details
  - List of Discrepancies
  - Link untuk review/approve

#### D. Approval Workflow (Jika diperlukan)
```
Warehouse Submit (Tidak Sesuai)
    │
    ▼
Create Approval Request
    │
    ├─── Notify Purchasing
    │
    ├─── Notify Supervisor
    │
    ▼
Purchasing/Supervisor Review
    │
    ├─── Approve → Update DB + Release Unit
    │
    └─── Reject → Return to Warehouse + Add Notes
```

### 2.4 Rekomendasi Final

**Untuk Implementasi Awal (MVP):**
1. ✅ Auto-update untuk "Sesuai" → langsung masuk inventory
2. ⚠️ "Tidak Sesuai" → Update data + Create notification ke Purchasing
3. 📝 Log semua discrepancy ke database
4. 🔔 Notifikasi in-app untuk Purchasing team

**Untuk Implementasi Lanjutan:**
1. Tambahkan approval workflow untuk major discrepancy
2. Tambahkan dashboard untuk Purchasing melihat semua discrepancy
3. Tambahkan reporting untuk analisis ketidaksesuaian
4. Integrasi dengan email notification

---

## 3. 📦 Implementasi Verifikasi Attachment & Sparepart

### 3.1 Format Verifikasi Baru (Sama dengan Unit)

#### A. Struktur Tabel Verifikasi
- **Kolom:** Item | Database | Real Lapangan | Sesuai | Tidak Sesuai
- **Logic:** Sama persis dengan Unit Verification
- **Validasi:** Semua baris harus memiliki checkbox yang dicentang

#### B. File yang Perlu Diubah/Dibuat

**1. Attachment Verification**
- File: `app/Views/warehouse/purchase_orders/tabs/attachment_verification_tab.php`
- Script: `app/Views/warehouse/purchase_orders/tabs/attachment_verification_script.php`
- **Fungsi yang perlu dibuat:**
  - `createAttachmentDetailCard(data)` - Format sama dengan Unit
  - `createVerificationTableHTML(specDetails)` - Reusable function
  - `checkAllAttachmentVerifiedInline()`
  - `submitAttachmentVerificationInline()`
  - `showVerificationConfirmationDialog()` - Reusable

**2. Sparepart Verification**
- File: `app/Views/warehouse/purchase_orders/tabs/sparepart_verification_tab.php`
- Script: `app/Views/warehouse/purchase_orders/tabs/sparepart_verification_script.php`
- **Fungsi yang perlu dibuat:**
  - `createSparepartDetailCard(data)`
  - `checkAllSparepartVerifiedInline()`
  - `submitSparepartVerificationInline()`

### 3.2 Data Specification untuk Attachment

```javascript
// Attachment specifications
const attachmentSpecs = [
    {label: 'Tipe Attachment', value: data.tipe_attachment, fieldName: 'tipe_attachment'},
    {label: 'Merk', value: data.merk_attachment, fieldName: 'merk'},
    {label: 'Model', value: data.model_attachment, fieldName: 'model'},
    {label: 'Serial Number', value: data.serial_number || 'Belum ada SN', fieldName: 'sn_attachment', required: true},
    {label: 'Keterangan', value: data.keterangan, fieldName: 'keterangan', isTextarea: true}
];
```

### 3.3 Data Specification untuk Sparepart

```javascript
// Sparepart specifications
const sparepartSpecs = [
    {label: 'Nama Sparepart', value: data.nama_sparepart, fieldName: 'nama_sparepart'},
    {label: 'Merk', value: data.merk_sparepart, fieldName: 'merk'},
    {label: 'Model', value: data.model_sparepart, fieldName: 'model'},
    {label: 'Part Number', value: data.part_number, fieldName: 'part_number'},
    {label: 'Serial Number', value: data.serial_number || 'Belum ada SN', fieldName: 'sn_sparepart', required: true},
    {label: 'Keterangan', value: data.keterangan, fieldName: 'keterangan', isTextarea: true}
];
```

### 3.4 Backend Controller Update

**File:** `app/Controllers/Warehouse.php` atau `Purchasing.php`

**Method yang perlu dibuat/update:**
1. `verifyPoAttachment()` - Handle submit verifikasi attachment
2. `verifyPoSparepart()` - Handle submit verifikasi sparepart
3. `getAttachmentVerificationData()` - Get data untuk verifikasi attachment
4. `getSparepartVerificationData()` - Get data untuk verifikasi sparepart

### 3.5 Reusable Components

**Buat file shared untuk reusable functions:**
- `app/Views/warehouse/purchase_orders/tabs/shared/verification_common.js`
  - `createVerificationTableHTML(specDetails, itemType)`
  - `showVerificationConfirmationDialog(data)`
  - `checkAllVerifiedInline(formId, buttonId)`
  - Common event handlers

### 3.6 Implementation Steps

1. **Phase 1: Refactor Unit Verification**
   - Extract reusable functions ke `verification_common.js`
   - Test semua fungsi reusable

2. **Phase 2: Attachment Verification**
   - Copy format dari Unit Verification
   - Adapt untuk Attachment specifications
   - Test dengan berbagai skenario

3. **Phase 3: Sparepart Verification**
   - Copy format dari Unit Verification
   - Adapt untuk Sparepart specifications
   - Test dengan berbagai skenario

4. **Phase 4: Integration & Testing**
   - Test semua tiga jenis verifikasi
   - Test notification system
   - Test approval workflow (jika ada)

---

## 4. 📝 Checklist Implementasi

### 4.1 Konfirmasi Dialog
- [ ] Buat fungsi `showVerificationConfirmationDialog()`
- [ ] Integrasi ke `submitUnitVerificationInline()`
- [ ] Test dengan berbagai skenario (Sesuai/Tidak Sesuai)
- [ ] Test cancel action
- [ ] Test dengan data lengkap dan kosong

### 4.2 Alur Bisnis "Tidak Sesuai"
- [ ] Design database schema untuk discrepancy tracking
- [ ] Buat migration untuk tabel baru
- [ ] Implementasi logic untuk minor vs major discrepancy
- [ ] Buat notification system
- [ ] Buat approval workflow (jika diperlukan)
- [ ] Test end-to-end flow

### 4.3 Attachment Verification
- [ ] Update `attachment_verification_tab.php`
- [ ] Update `attachment_verification_script.php`
- [ ] Buat `createAttachmentDetailCard()`
- [ ] Implementasi format verifikasi baru
- [ ] Test dengan berbagai attachment types
- [ ] Integrasi dengan backend

### 4.4 Sparepart Verification
- [ ] Update `sparepart_verification_tab.php`
- [ ] Update `sparepart_verification_script.php`
- [ ] Buat `createSparepartDetailCard()`
- [ ] Implementasi format verifikasi baru
- [ ] Test dengan berbagai sparepart types
- [ ] Integrasi dengan backend

### 4.5 Reusable Components
- [ ] Buat `verification_common.js`
- [ ] Extract common functions
- [ ] Test reusable functions
- [ ] Update semua verification scripts untuk menggunakan common functions

---

## 5. 🎯 Priority & Timeline

### Priority 1 (High)
1. ✅ Konfirmasi Dialog - **1-2 jam**
2. ✅ Attachment Verification - **4-6 jam**
3. ✅ Sparepart Verification - **4-6 jam**

### Priority 2 (Medium)
1. ⚠️ Alur Bisnis "Tidak Sesuai" (Basic) - **6-8 jam**
2. ⚠️ Notification System (Basic) - **4-6 jam**

### Priority 3 (Low)
1. 📊 Approval Workflow - **8-10 jam**
2. 📊 Dashboard untuk Purchasing - **6-8 jam**
3. 📊 Reporting & Analytics - **4-6 jam**

### Estimated Total Time
- **MVP (Priority 1 + Basic Priority 2):** ~20-28 jam
- **Full Implementation:** ~40-50 jam

---

## 6. 🔍 Questions untuk Diskusi

1. **Untuk "Tidak Sesuai":**
   - Apakah perlu approval workflow atau langsung update?
   - Siapa yang harus approve? (Supervisor Warehouse / Purchasing)
   - Apakah unit yang "Tidak Sesuai" tetap masuk inventory atau di-hold?

2. **Notification:**
   - Channel apa yang digunakan? (Email / In-app / WhatsApp)
   - Siapa yang menerima notifikasi?
   - Apakah perlu real-time atau batch notification?

3. **Data Tracking:**
   - Apakah perlu tracking history perubahan?
   - Apakah perlu audit trail untuk approval?
   - Apakah perlu reporting untuk analisis discrepancy?

4. **Attachment & Sparepart:**
   - Apakah format specification sudah final?
   - Apakah ada field khusus yang perlu ditambahkan?
   - Apakah logic verifikasi sama persis dengan Unit?

---

## 7. 📚 Reference Files

### Files yang Sudah Ada
- `app/Views/warehouse/purchase_orders/tabs/unit_verification_tab.php`
- `app/Views/warehouse/purchase_orders/tabs/unit_verification_script.php`
- `app/Views/warehouse/purchase_orders/tabs/attachment_verification_tab.php`
- `app/Views/warehouse/purchase_orders/tabs/attachment_verification_script.php`
- `app/Views/warehouse/purchase_orders/tabs/sparepart_verification_tab.php`
- `app/Views/warehouse/purchase_orders/tabs/sparepart_verification_script.php`

### Files yang Perlu Dibuat
- `app/Views/warehouse/purchase_orders/tabs/shared/verification_common.js`
- Database migration untuk discrepancy tracking
- Controller methods untuk handle verification

---

## 8. ✅ Next Steps

1. **Review planning ini dengan team**
2. **Clarify questions di section 6**
3. **Finalize database schema**
4. **Start implementation dengan Priority 1**
5. **Test incrementally setelah setiap feature**

---

**Document Created:** 2025-01-17  
**Last Updated:** 2025-01-17  
**Status:** Planning Phase

