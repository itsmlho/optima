# SPPU (Surat Perintah Penarikan Unit) - Comprehensive Enhancements Complete

## Overview
Enhanced the SPPU (Unit Withdrawal Letter) document with detailed unit information, equipment specifications, dynamic content based on workflow context, and professional formal language.

## Date Completed
<?= date('Y-m-d H:i:s') ?>

---

## 1. Enhanced Unit Details Display

### A. Tipe & Jenis Display
**Previous:** Only showed `tipe_unit.tipe` (e.g., "Forklift CAT - EP20CA")

**Enhanced:** Now shows both `tipe` and `jenis` (e.g., "Forklift COUNTER BALANCE CAT - EP20CA")

**Implementation:**
- **Query Update:** Added `tu.jenis as unit_jenis` to SELECT statement
- **Data Processing:** Combined tipe and jenis in controller:
  ```php
  if (!empty($item['unit_jenis'])) {
      $item['unit_tipe'] = trim($item['unit_tipe'] . ' ' . $item['unit_jenis']);
  }
  ```
- **Display:** Shows complete unit type classification

### B. Column Structure Changes
**Removed:** Status column (not relevant for withdrawal letters)

**Added:**
1. **Departemen Column** - Shows department classification from `departemen.nama_departemen`
2. **Kapasitas Column** - Shows unit capacity from `kapasitas.kapasitas_unit`

**Benefits:**
- More relevant information for withdrawal tracking
- Better operational context for warehouse management
- Clearer unit classification

---

## 2. Equipment Details (Battery & Charger)

### A. Battery Information
**Previous:** No battery details displayed

**Enhanced:** Complete battery specifications with serial number:
```php
Battery:
Merk Tipe (Jenis)
SN: XXXXX
```

**Data Structure:**
```php
$item['battery'] = [
    'merk_baterai' => string,  // Battery brand
    'tipe_baterai' => string,  // Battery type
    'jenis_baterai' => string, // Battery kind (e.g., Lithium, Lead Acid)
    'sn_baterai' => string     // Serial number from inventory_attachment
];
```

### B. Charger Information
**Previous:** No charger details displayed

**Enhanced:** Complete charger specifications with serial number:
```php
Charger:
Merk Tipe
SN: XXXXX
```

**Data Structure:**
```php
$item['charger'] = [
    'merk_charger' => string, // Charger brand
    'tipe_charger' => string, // Charger type
    'sn_charger' => string    // Serial number from inventory_attachment
];
```

### C. Database Schema Implementation
**Key Discovery:** Serial numbers are stored in `inventory_attachment` table, NOT in battery/charger master tables.

**Query Joins:**
```php
// Battery data
->join('inventory_attachment ia_bat', 
       'ia_bat.id_inventory_unit = iu.id_inventory_unit AND ia_bat.tipe_item = "battery"', 
       'left')
->join('baterai bat', 'bat.id = ia_bat.baterai_id', 'left')

// Charger data
->join('inventory_attachment ia_chr', 
       'ia_chr.id_inventory_unit = iu.id_inventory_unit AND ia_chr.tipe_item = "charger"', 
       'left')
->join('charger chr', 'chr.id_charger = ia_chr.charger_id', 'left')
```

**Selected Fields:**
- `bat.merk_baterai, bat.tipe_baterai, bat.jenis_baterai` - Battery specifications
- `ia_bat.sn_baterai` - Battery serial number from inventory_attachment
- `chr.merk_charger, chr.tipe_charger` - Charger specifications
- `ia_chr.sn_charger` - Charger serial number from inventory_attachment

---

## 3. Dynamic Content Implementation

### A. TUJUAN PENARIKAN (Withdrawal Purpose)
**Previous:** Static/hardcoded text

**Enhanced:** Dynamic text from `tujuan_perintah_kerja` table

**Implementation:**
```php
// Controller - Marketing.php
$tujuanData = $this->db->table('tujuan_perintah_kerja')
    ->select('kode, nama')
    ->where('id', $di['tujuan_perintah_kerja_id'])
    ->get()->getRowArray();

$tujuanDisplay = !empty($tujuanNama) 
    ? "Penarikan unit untuk " . strtolower($tujuanNama) . " sesuai instruksi operasional."
    : $withdrawalReason;
```

**Example Outputs:**
- "Penarikan unit untuk maintenance sesuai instruksi operasional."
- "Penarikan unit untuk upgrade spesifikasi sesuai instruksi operasional."
- "Penarikan unit untuk habis kontrak sesuai instruksi operasional."

### B. CATATAN PENTING (Context-Aware Important Notes)
**Previous:** Static text regardless of workflow

**Enhanced:** Context-aware notes based on `jenis_perintah` and `tujuan_perintah`

**Implementation Method:** `getCatatanPenting($jenis, $tujuan)`

**Context-Aware Notes:**

| Workflow | Note Content |
|----------|-------------|
| **TARIK_HABIS_KONTRAK** | Unit akan dikembalikan ke gudang PT. Sarana Mitra Luas setelah masa kontrak berakhir. Verifikasi kelengkapan dan kondisi unit wajib dilakukan sebelum penarikan. |
| **TARIK_RUSAK** | Unit mengalami kerusakan dan akan ditarik untuk perbaikan. Dokumentasi kondisi kerusakan wajib dilakukan sebelum penarikan. |
| **TARIK_MAINTENANCE** | Unit akan ditarik untuk maintenance terjadwal. Jadwal pengembalian akan diinformasikan setelah pekerjaan selesai. |
| **TARIK_PINDAH_LOKASI** | Unit akan dipindahkan ke lokasi baru sesuai instruksi pelanggan. Koordinasi dengan penerima di lokasi tujuan diperlukan. |
| **TUKAR_MAINTENANCE** | Unit akan ditarik untuk maintenance dan digantikan dengan unit temporary. Pengembalian unit original akan dilakukan setelah maintenance selesai. |
| **TUKAR_RUSAK** | Unit yang rusak akan digantikan dengan unit pengganti. Verifikasi kondisi unit pengganti wajib dilakukan saat pengiriman. |
| **TUKAR_UPGRADE** | Unit akan digantikan dengan unit baru dengan spesifikasi yang lebih tinggi sesuai permintaan. Addendum kontrak akan mengikuti. |
| **TUKAR_DOWNGRADE** | Unit akan digantikan dengan unit yang sesuai kebutuhan operasional pelanggan. Penyesuaian kontrak akan diproses. |
| **Default** | Penarikan unit dilakukan sesuai instruksi operasional. Koordinasi dengan tim operasional diperlukan untuk kelancaran proses. |

---

## 4. Professional & Formal Language

### A. INSTRUKSI DAN KETENTUAN PENARIKAN
**Updated to formal legal language:**

1. **Legitimasi Dokumen**
   - "Dokumen ini merupakan surat perintah resmi untuk penarikan unit sesuai ketentuan kontrak yang berlaku."

2. **Kewajiban Verifikasi**
   - "Pihak penerima diwajibkan melakukan verifikasi terhadap kondisi fisik unit, kelengkapan attachment, dan kesesuaian spesifikasi sebelum menandatangani dokumen ini."

3. **Dokumentasi Kerusakan**
   - "Setiap kerusakan, kehilangan komponen, atau ketidaksesuaian unit wajib dilaporkan dan didokumentasikan dalam Berita Acara Penarikan (BAP) yang ditandatangani bersama."

4. **Penggantian Unit (TUKAR only)**
   - "Penarikan unit pengganti akan dilaksanakan sesuai dengan Delivery Instruction (DI) terpisah yang telah diterbitkan bersamaan dengan dokumen ini."

5. **Pernyataan Persetujuan**
   - "Penandatanganan dokumen ini menyatakan persetujuan dan konfirmasi bahwa pihak customer telah menyerahkan unit beserta kelengkapannya kepada PT Sarana Mitra Luas dalam kondisi sebagaimana tertera."

6. **Kontak Klarifikasi**
   - "Untuk klarifikasi, pertanyaan, atau komplain terkait penarikan unit, dapat menghubungi Divisi Operasional PT Sarana Mitra Luas melalui kontak resmi yang tertera pada kontrak."

### B. Language Characteristics
- **Formal tone:** Official business language
- **Legal clarity:** Clear obligations and responsibilities
- **Professional structure:** Numbered instructions for easy reference
- **Customer-focused:** Clear escalation path for concerns
- **Conditional content:** Instruction #4 only appears for TUKAR workflows

---

## 5. Technical Implementation Details

### A. Database Query Enhancements

**File:** `app/Controllers/Marketing.php`

**Method:** `printWithdrawalLetter($id)` (Lines 3435-3465)

**Query Changes:**
```php
// Added fields to SELECT
'tu.tipe as unit_tipe, tu.jenis as unit_jenis',        // Type & kind
'd.nama_departemen as departemen_nama',                 // Department
'k.kapasitas_unit as kapasitas_unit_nama',             // Capacity
'bat.merk_baterai, bat.tipe_baterai, bat.jenis_baterai', // Battery specs
'ia_bat.sn_baterai',                                    // Battery SN
'chr.merk_charger, chr.tipe_charger',                   // Charger specs
'ia_chr.sn_charger'                                     // Charger SN

// Added JOIN clauses
->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
->join('inventory_attachment ia_bat', 'ia_bat.id_inventory_unit = iu.id_inventory_unit AND ia_bat.tipe_item = "battery"', 'left')
->join('baterai bat', 'bat.id = ia_bat.baterai_id', 'left')
->join('inventory_attachment ia_chr', 'ia_chr.id_inventory_unit = iu.id_inventory_unit AND ia_chr.tipe_item = "charger"', 'left')
->join('charger chr', 'chr.id_charger = ia_chr.charger_id', 'left')
```

### B. Data Processing
**Data Organization for Battery:**
```php
if (!empty($item['merk_baterai'])) {
    $item['battery'] = [
        'merk_baterai' => $item['merk_baterai'],
        'tipe_baterai' => $item['tipe_baterai'] ?? '',
        'jenis_baterai' => $item['jenis_baterai'] ?? '',
        'sn_baterai' => $item['sn_baterai'] ?? ''
    ];
}
```

**Data Organization for Charger:**
```php
if (!empty($item['merk_charger'])) {
    $item['charger'] = [
        'merk_charger' => $item['merk_charger'],
        'tipe_charger' => $item['tipe_charger'] ?? '',
        'sn_charger' => $item['sn_charger'] ?? ''
    ];
}
```

### C. View Template Updates

**File:** `app/Views/marketing/print_withdrawal_letter.php`

**Unit Table Structure (Lines 443-470):**
```php
<thead>
    <tr>
        <th>No</th>
        <th>Nomor Unit</th>
        <th>Tipe & Jenis / Merk / Model</th>
        <th>Departemen</th>         <!-- NEW -->
        <th>Kapasitas</th>          <!-- NEW -->
        <th>Serial Number</th>
        <th>Tahun</th>
        <th>Kelengkapan</th>        <!-- ENHANCED with SN -->
    </tr>
</thead>
```

**Equipment Display (Lines 472-491):**
```php
<td style="font-size: 9px;">
    <?php if (!empty($unit['battery'])): ?>
        <strong>Battery:</strong><br>
        <small>
            <?= esc($unit['battery']['merk_baterai']) ?> 
            <?= esc($unit['battery']['tipe_baterai']) ?>
            <?php if (!empty($unit['battery']['jenis_baterai'])): ?>
                (<?= esc($unit['battery']['jenis_baterai']) ?>)
            <?php endif; ?>
            <?php if (!empty($unit['battery']['sn_baterai'])): ?>
                <br>SN: <?= esc($unit['battery']['sn_baterai']) ?>
            <?php endif; ?>
        </small><br>
    <?php endif; ?>
    
    <?php if (!empty($unit['charger'])): ?>
        <strong>Charger:</strong><br>
        <small>
            <?= esc($unit['charger']['merk_charger']) ?> 
            <?= esc($unit['charger']['tipe_charger']) ?>
            <?php if (!empty($unit['charger']['sn_charger'])): ?>
                <br>SN: <?= esc($unit['charger']['sn_charger']) ?>
            <?php endif; ?>
        </small><br>
    <?php endif; ?>
</td>
```

---

## 6. Benefits & Impact

### A. Operational Benefits
1. **Complete Unit Tracking**
   - Full unit specifications (tipe + jenis)
   - Equipment serial numbers for asset tracking
   - Department and capacity classification

2. **Improved Documentation**
   - Detailed battery and charger information
   - Serial numbers for warranty and maintenance tracking
   - Context-aware notes reduce ambiguity

3. **Professional Presentation**
   - Formal language enhances company credibility
   - Clear legal obligations protect company interests
   - Customer-focused communication improves relationships

### B. Workflow Context Awareness
1. **TARIK_HABIS_KONTRAK**
   - Clear end-of-contract process
   - Verification requirements stated
   - Return-to-warehouse instructions

2. **TUKAR Workflows**
   - Automatic mention of replacement unit
   - Reference to separate DI for replacement
   - Clear exchange process

3. **MAINTENANCE/RUSAK**
   - Documentation requirements
   - Timeline expectations
   - Proper condition assessment

### C. Audit & Compliance
1. **Serial Number Tracking**
   - Battery and charger accountability
   - Asset movement traceability
   - Warranty claim support

2. **Legal Documentation**
   - Clear obligations and responsibilities
   - Formal acceptance/confirmation
   - Dispute resolution pathway

3. **Process Standardization**
   - Consistent terminology
   - Professional appearance
   - Brand consistency with other documents (DI format)

---

## 7. Database Schema Reference

### Tables Used
```
1. delivery_instructions
   - Main DI data with jenis_perintah_kerja_id, tujuan_perintah_kerja_id

2. jenis_perintah_kerja
   - kode: TARIK, TUKAR, RELOKASI
   - nama: Display names

3. tujuan_perintah_kerja
   - kode: HABIS_KONTRAK, MAINTENANCE, RUSAK, UPGRADE, etc.
   - nama: Purpose descriptions

4. inventory_unit
   - id_inventory_unit, no_unit, serial_number
   - Links to: tipe_unit, departemen, kapasitas

5. tipe_unit
   - tipe: Unit type (e.g., "Forklift CAT - EP20CA")
   - jenis: Unit kind (e.g., "COUNTER BALANCE")

6. departemen
   - nama_departemen: Department classification

7. kapasitas
   - kapasitas_unit: Capacity rating

8. inventory_attachment
   - tipe_item: 'battery', 'charger', 'attachment'
   - sn_baterai: Battery serial number ✓
   - sn_charger: Charger serial number ✓
   - baterai_id, charger_id: Links to master tables

9. baterai
   - merk_baterai, tipe_baterai, jenis_baterai
   - NO serial number (stored in inventory_attachment)

10. charger
    - merk_charger, tipe_charger
    - NO serial number (stored in inventory_attachment)
```

### Join Strategy
```
inventory_unit (base)
    ├── tipe_unit → tipe, jenis
    ├── departemen → nama_departemen
    ├── kapasitas → kapasitas_unit
    └── inventory_attachment (tipe_item = 'battery')
        ├── baterai → merk, tipe, jenis
        └── sn_baterai (from inventory_attachment)
    └── inventory_attachment (tipe_item = 'charger')
        ├── charger → merk, tipe
        └── sn_charger (from inventory_attachment)
```

---

## 8. Testing Scenarios

### Test Case 1: TARIK_HABIS_KONTRAK
**Expected Output:**
- Tujuan: "Penarikan unit untuk habis kontrak sesuai instruksi operasional."
- Catatan: End-of-contract return process with verification requirements
- Unit display: Tipe + Jenis, Departemen, Kapasitas
- Equipment: Battery and charger with serial numbers

### Test Case 2: TUKAR_UPGRADE
**Expected Output:**
- Tujuan: "Penarikan unit untuk upgrade sesuai instruksi operasional."
- Catatan: Upgrade process with addendum mention
- Instruksi: Includes item #4 about replacement unit DI
- Letter Title: "SURAT PERINTAH PENARIKAN & PENGGANTIAN UNIT"

### Test Case 3: TARIK_MAINTENANCE
**Expected Output:**
- Tujuan: "Penarikan unit untuk maintenance sesuai instruksi operasional."
- Catatan: Scheduled maintenance with timeline info
- Letter Title: "SURAT PERINTAH PENARIKAN UNIT"
- No replacement unit mention

---

## 9. File Modifications Summary

### Modified Files:
1. **app/Controllers/Marketing.php**
   - Method: `printWithdrawalLetter($id)` - Lines 3407-3560
   - Added: `getCatatanPenting($jenis, $tujuan)` - New helper method
   - Enhanced: Query with departemen, kapasitas, battery, charger joins
   - Enhanced: Data processing with tipe+jenis combination
   - Enhanced: Dynamic tujuanDisplay generation
   - Enhanced: Context-aware catatan penting

2. **app/Views/marketing/print_withdrawal_letter.php**
   - Header: Added $tujuanDisplay and $catatanPenting variables
   - Removed: Duplicate static catatan penting logic
   - Enhanced: Unit table columns (Departemen, Kapasitas)
   - Enhanced: Equipment display with jenis and serial numbers
   - Maintained: Professional INSTRUKSI DAN KETENTUAN section
   - Maintained: Conditional TUKAR instructions

### No Changes Required:
- **app/Views/operational/delivery.php** - Print SPPU button already working
- **app/Config/Routes.php** - SPPU route already configured
- Database schema - All required columns already exist

---

## 10. Maintenance Notes

### Future Enhancements (Optional):
1. **Attachment Details**
   - Could add serial numbers for attachments if needed
   - Currently shows tipe only

2. **Unit History**
   - Could add maintenance history section
   - Previous withdrawal records

3. **Customer Signature**
   - Could add digital signature capture
   - Photo verification upload

### Known Limitations:
1. **Battery/Charger Display**
   - Only shows first battery/charger per unit
   - Multiple batteries would require array handling

2. **Equipment Absence**
   - Shows "-" if no equipment data
   - Could add "Tidak ada" for clarity

### Code Maintenance:
1. **getCatatanPenting() Method**
   - Add new tujuan codes here when created
   - Maintain consistency with business processes

2. **Database Schema**
   - Serial numbers MUST stay in inventory_attachment
   - Don't add SN columns to battery/charger master tables

---

## Status: ✅ COMPLETE

All requested SPPU enhancements have been successfully implemented:
- ✅ Tipe & Jenis display (not just tipe)
- ✅ Departemen and Kapasitas columns (replaced Status)
- ✅ Battery details with merk, tipe, jenis, and SN
- ✅ Charger details with merk, tipe, and SN
- ✅ Dynamic TUJUAN PENARIKAN from tujuan_perintah
- ✅ Context-aware CATATAN PENTING based on workflow
- ✅ Professional formal INSTRUKSI PENTING language

The SPPU document is now production-ready with comprehensive unit information, equipment tracking, and professional presentation consistent with company standards.
