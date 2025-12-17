# DI (Delivery Instruction) WORKFLOW COMPREHENSIVE AUDIT

**Generated:** <?php echo date('Y-m-d H:i:s'); ?>  
**Database:** optima_ci  
**Scope:** Complete DI workflow from creation to completion for all jenis perintah kerja

---

## EXECUTIVE SUMMARY

**Purpose:** Audit complete Delivery Instruction (DI) workflow covering all 4 jenis perintah kerja types (ANTAR, TARIK, TUKAR, RELOKASI) with validation of business logic, database relationships, and workflow stages.

**Key Components Audited:**
- DI Creation (Marketing.php)
- DI Workflow Execution (Operational.php)
- Business Logic Layer (DeliveryInstructionService.php)
- Workflow Status Management (UnitWorkflowStatus.php)
- Database Tables (delivery_instructions, delivery_items, kontrak, inventory_unit)

---

## 1. DATABASE ARCHITECTURE

### 1.1 Core Tables

#### delivery_instructions
```sql
CREATE TABLE delivery_instructions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_di VARCHAR(100) NOT NULL,
    spk_id INT UNSIGNED,
    jenis_spk ENUM('UNIT','ATTACHMENT') DEFAULT 'UNIT',
    po_kontrak_nomor VARCHAR(100),
    pelanggan VARCHAR(255) NOT NULL,
    lokasi VARCHAR(255),
    tanggal_kirim DATE,
    catatan TEXT,
    
    -- Workflow fields
    jenis_perintah_kerja_id INT,
    tujuan_perintah_kerja_id INT,
    status_eksekusi_workflow_id INT DEFAULT 1,
    
    -- Status progression
    status_di ENUM('DIAJUKAN','DISETUJUI','PERSIAPAN_UNIT','SIAP_KIRIM',
                   'DALAM_PERJALANAN','SAMPAI_LOKASI','SELESAI','DIBATALKAN') 
              DEFAULT 'DIAJUKAN',
    
    -- Stage approval tracking
    perencanaan_tanggal_approve DATE,
    estimasi_sampai DATE,
    nama_supir VARCHAR(100),
    no_hp_supir VARCHAR(20),
    no_sim_supir VARCHAR(50),
    kendaraan VARCHAR(100),
    no_polisi_kendaraan VARCHAR(20),
    berangkat_tanggal_approve DATE,
    catatan_berangkat TEXT,
    sampai_tanggal_approve DATE,
    catatan_sampai TEXT,
    
    dibuat_oleh INT UNSIGNED,
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_jenis (jenis_perintah_kerja_id),
    INDEX idx_tujuan (tujuan_perintah_kerja_id),
    INDEX idx_status (status_di),
    INDEX idx_spk (spk_id),
    INDEX idx_created (dibuat_pada)
);
```

#### delivery_items
```sql
CREATE TABLE delivery_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    di_id INT UNSIGNED NOT NULL,
    item_type ENUM('UNIT','ATTACHMENT') NOT NULL DEFAULT 'UNIT',
    unit_id INT UNSIGNED,
    parent_unit_id INT,  -- For attachments linked to main unit
    attachment_id INT UNSIGNED,
    keterangan VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (di_id) REFERENCES delivery_instructions(id) ON DELETE CASCADE,
    INDEX idx_di (di_id),
    INDEX idx_type (item_type),
    INDEX idx_unit (unit_id)
);
```

### 1.2 Reference Tables

#### jenis_perintah_kerja (4 Types)
| ID | Kode | Nama | Deskripsi |
|----|------|------|-----------|
| 1 | ANTAR | Antar Unit | Pengantaran unit ke lokasi pelanggan |
| 2 | TARIK | Tarik Unit | Penarikan unit dari lokasi pelanggan |
| 3 | TUKAR | Tukar Unit | Penukaran unit lama dengan unit baru |
| 4 | RELOKASI | Relokasi Unit | Pemindahan unit antar lokasi |

#### tujuan_perintah_kerja (12 Purposes)

**ANTAR (3 tujuan):**
- ANTAR_BARU: Pengiriman untuk kontrak baru
- ANTAR_TAMBAHAN: Pengiriman tambahan unit ke kontrak aktif
- ANTAR_PENGGANTI: Pengiriman unit pengganti sementara

**TARIK (4 tujuan):**
- TARIK_HABIS_KONTRAK: Penarikan karena kontrak berakhir
- TARIK_PINDAH_LOKASI: Penarikan karena pelanggan pindah
- TARIK_MAINTENANCE: Penarikan untuk maintenance terjadwal
- TARIK_RUSAK: Penarikan karena unit rusak/tidak berfungsi

**TUKAR (4 tujuan):**
- TUKAR_UPGRADE: Penukaran ke unit kapasitas lebih besar
- TUKAR_DOWNGRADE: Penukaran ke unit kapasitas lebih kecil
- TUKAR_RUSAK: Penukaran karena unit lama rusak
- TUKAR_MAINTENANCE: Penukaran sementara saat maintenance

**RELOKASI (3 tujuan):**
- RELOKASI_INTERNAL: Pemindahan antar branch/warehouse internal
- RELOKASI_OPTIMASI: Pemindahan untuk optimasi aset
- RELOKASI_EMERGENCY: Pemindahan darurat untuk kebutuhan mendesak

---

## 2. WORKFLOW ARCHITECTURE

### 2.1 DI Status Progression

```
DIAJUKAN → DISETUJUI → PERSIAPAN_UNIT → SIAP_KIRIM → 
DALAM_PERJALANAN → SAMPAI_LOKASI → SELESAI
                                      ↓
                                 DIBATALKAN (any stage)
```

### 2.2 Stage-Based Actions

#### Stage: PERENCANAAN (Diajukan)
**Actions:**
- Validate jenis_perintah_kerja_id and tujuan_perintah_kerja_id
- Validate SPK status (must be READY)
- Select units based on workflow type:
  - **Contract-based (TARIK/TUKAR):** Select from active contract units
  - **Unit-selection (ANTAR/RELOKASI):** Select from available inventory
- Set `perencanaan_tanggal_approve`, `estimasi_sampai`

**Code Location:** `Marketing.php::diCreate()` lines 4759-5100

#### Stage: BERANGKAT (Dalam Perjalanan)
**Actions:**
- Capture driver details: `nama_supir`, `no_hp_supir`, `no_sim_supir`
- Capture vehicle details: `kendaraan`, `no_polisi_kendaraan`
- Set `berangkat_tanggal_approve`, `catatan_berangkat`
- Update status_di to `DALAM_PERJALANAN`

**Code Location:** `Operational.php::diApprove()` lines 775-840

#### Stage: SAMPAI (Delivered)
**Actions:**
- Set `sampai_tanggal_approve`, `catatan_sampai`
- **CRITICAL SECTION:** Update inventory relationships
  1. Activate contract: `kontrak.status = 'Aktif'`
  2. Link units to kontrak: `inventory_unit.kontrak_id = kontrak.id`
  3. Link units to SPK: `inventory_unit.spk_id = spk.id`
  4. Link units to DI: `inventory_unit.delivery_instruction_id = di.id`
  5. Set delivery date: `inventory_unit.tanggal_kirim = di.tanggal_kirim`
  6. Copy pricing from quotation_specifications:
     - `inventory_unit.harga_sewa_bulanan = quotation_specifications.harga_per_unit_bulanan`
     - `inventory_unit.harga_sewa_harian = quotation_specifications.harga_per_unit_harian`
  7. Copy accessories from SPK prepared_units:
     - `inventory_unit.aksesoris = prepared_units[].aksesoris_tersedia`
- Check SPK completion: Mark `spk.status = 'COMPLETED'` if all units delivered

**Code Location:** `Operational.php::diApprove()` lines 900-1030

---

## 3. WORKFLOW TYPE CLASSIFICATION

### 3.1 Contract-Based Workflows (TARIK, TUKAR)

**Characteristics:**
- Require active contract with existing units
- Get units from `getContractUnits()` method
- Validate contract status before selection
- Update contract relationships on completion

**Unit Selection Logic:**
```php
// From DeliveryInstructionService.php::getContractUnits()
$query = $this->db->table('kontrak_unit ku')
    ->select('iu.*, ku.kontrak_id, ku.status as kontrak_status')
    ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id')
    ->where('ku.kontrak_id', $kontrakId)
    ->where('ku.status', 'AKTIF')
    ->whereIn('iu.status', ['DISEWA', 'BEROPERASI']);
```

**⚠️ CRITICAL ISSUE FOUND:**
**kontrak_unit table DOES NOT EXIST in database!**

Service code references `kontrak_unit` table extensively, but table is missing:
- `DeliveryInstructionService.php` lines 32, 105, 247, 255, 275, 283, 293, 409
- System uses direct `kontrak_id` in `inventory_unit` table instead
- This is a **major architectural discrepancy**

**Current Implementation:**
- Units linked directly via `inventory_unit.kontrak_id → kontrak.id`
- No junction table for many-to-many relationship
- No unit-level contract status tracking (DITARIK, DITUKAR)
- `workflow_status` column in `inventory_unit` is always NULL (12 records checked)

### 3.2 Unit-Selection Workflows (ANTAR, RELOKASI)

**Characteristics:**
- Select from available inventory (TERSEDIA, STOCK_ASET status)
- Get units from `getAvailableUnits()` method
- No contract validation required
- Create new contract relationships on delivery

**Unit Selection Logic:**
```php
// From DeliveryInstructionService.php::getAvailableUnits()
$query = $this->db->table('inventory_unit iu')
    ->select('iu.*, mu.merk_unit, mu.model_unit')
    ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
    ->where('iu.status', 'TERSEDIA')
    ->whereNotIn('iu.id_inventory_unit', function($subquery) {
        // Exclude units already in active DI
    });
```

---

## 4. JENIS PERINTAH DETAILED WORKFLOWS

### 4.1 ANTAR (Delivery) Workflow

**Purpose:** Deliver units to customer location (new contracts, additions, replacements)

**Workflow Stages:**
1. **DIAJUKAN:** Marketing creates DI from READY SPK
2. **DISETUJUI:** Management approves delivery plan
3. **PERSIAPAN_UNIT:** Service prepares unit (battery, charger, attachment)
4. **SIAP_KIRIM:** Unit ready for shipping
5. **DALAM_PERJALANAN:** Driver en route to customer
6. **SAMPAI_LOKASI:** Unit delivered to customer
7. **SELESAI:** Contract activated, unit linked to contract

**Unit Status Changes:**
```
TERSEDIA → PREPARED → RENTAL_READY → DISEWA
```

**Business Logic:**
- Requires unit preparation approval in SPK
- Validates accessories (battery, charger, optional attachment)
- Creates contract relationship on delivery
- Activates contract when first unit arrives

**Database Updates on Delivery (sampai stage):**
```sql
-- Update unit
UPDATE inventory_unit SET
    kontrak_id = [kontrak.id],
    spk_id = [spk.id],
    delivery_instruction_id = [di.id],
    tanggal_kirim = [di.tanggal_kirim],
    harga_sewa_bulanan = [quotation_spec.harga_per_unit_bulanan],
    harga_sewa_harian = [quotation_spec.harga_per_unit_harian],
    aksesoris = [prepared_units.aksesoris_tersedia]
WHERE id_inventory_unit = [unit_id];

-- Activate contract
UPDATE kontrak SET
    status = 'Aktif'
WHERE no_kontrak = [di.po_kontrak_nomor] OR no_po_marketing = [di.po_kontrak_nomor];
```

**Code References:**
- Creation: `Marketing.php::diCreate()` lines 4759-5100
- Stage approval: `Operational.php::diApprove()` lines 775-1030
- Unit selection: `DeliveryInstructionService.php::getAvailableUnits()` lines 384-450

### 4.2 TARIK (Pickup) Workflow

**Purpose:** Retrieve units from customer location (contract end, maintenance, damaged)

**Workflow Stages:**
1. **DIAJUKAN:** Request to pickup unit from customer
2. **DISETUJUI:** Pickup approved, schedule coordination
3. **PERSIAPAN_UNIT:** Prepare transport and crew
4. **DALAM_PERJALANAN:** Crew en route to customer
5. **UNIT_DITARIK:** Unit picked up from location
6. **UNIT_PULANG:** Unit returning to warehouse
7. **SAMPAI_KANTOR:** Unit arrived at warehouse
8. **SELESAI:** Unit inspected, contract disconnected

**Unit Status Changes (from UnitWorkflowStatus.php):**
```
DISEWA → UNIT_AKAN_DITARIK → UNIT_SEDANG_DITARIK → 
UNIT_PULANG → STOCK_ASET
```

**Business Logic (from UnitWorkflowStatus.php::getStageActions()):**

```php
'TARIK' => [
    'DISETUJUI' => [
        'update_unit_status' => 'UNIT_AKAN_DITARIK',
        'notify_customer' => true,
        'prepare_transport' => true
    ],
    'UNIT_DITARIK' => [
        'update_unit_status' => 'UNIT_SEDANG_DITARIK',
        'disconnect_partial_contract' => true
    ],
    'SAMPAI_KANTOR' => [
        'update_unit_status' => 'STOCK_ASET',
        'disconnect_contract_fully' => true,
        'quality_check' => true
    ]
]
```

**Database Updates (from DeliveryInstructionService.php::processUnitTarik()):**

```php
public function processUnitTarik($unitIds, $diId, $stage) {
    $stageActions = UnitWorkflowStatus::getStageActions($stage, 'TARIK');
    
    foreach ($unitIds as $unitId) {
        // Update unit status
        if (isset($stageActions['update_unit_status'])) {
            $this->updateUnitStatus($unitId, $stageActions['update_unit_status'], $diId);
        }
        
        // Disconnect from contract
        if (isset($stageActions['disconnect_contract'])) {
            $this->disconnectUnitFromContract($unitId, $stage);
        }
    }
}

protected function disconnectUnitFromContract($unitId, $stage) {
    // ⚠️ CRITICAL: References non-existent kontrak_unit table
    $contractUnit = $this->db->table('kontrak_unit')
        ->where('unit_id', $unitId)
        ->where('status', 'AKTIF')
        ->get()->getRowArray();
    
    if ($contractUnit) {
        $this->db->table('kontrak_unit')
            ->where('id', $contractUnit['id'])
            ->update([
                'status' => 'DITARIK',
                'tanggal_tarik' => date('Y-m-d H:i:s'),
                'stage_tarik' => $stage
            ]);
    }
}
```

**⚠️ IMPLEMENTATION ISSUE:**
The `disconnectUnitFromContract()` method will **FAIL** because `kontrak_unit` table does not exist. Current system uses:
```sql
-- Actual implementation should be:
UPDATE inventory_unit SET
    kontrak_id = NULL,
    workflow_status = 'STOCK_ASET',
    contract_disconnect_date = NOW(),
    contract_disconnect_stage = 'SAMPAI_KANTOR'
WHERE id_inventory_unit = [unit_id];
```

**Code References:**
- Workflow logic: `DeliveryInstructionService.php::processUnitTarik()` lines 155-186
- Status config: `UnitWorkflowStatus.php::getStageActions()` lines 145-175
- Controller: `Operational.php::processWorkflowApproval()` lines 1689-1695

### 4.3 TUKAR (Exchange) Workflow

**Purpose:** Exchange old unit with new unit (upgrade, downgrade, replacement)

**Workflow Stages:**
1. **DIAJUKAN:** Request to exchange unit
2. **DISETUJUI:** Exchange approved, prepare replacement unit
3. **PERSIAPAN_UNIT:** Prepare new unit (battery, charger, attachment)
4. **DALAM_PERJALANAN:** Crew with new unit en route
5. **UNIT_DITUKAR:** Old unit swapped with new unit
6. **UNIT_LAMA_PULANG:** Old unit returning to warehouse
7. **SAMPAI_KANTOR:** Old unit arrived, new unit operational
8. **SELESAI:** Contract transferred, old unit to stock

**Unit Status Changes:**

**Old Unit:**
```
DISEWA → UNIT_AKAN_DITUKAR → UNIT_SEDANG_DITUKAR → 
UNIT_TUKAR_SELESAI → STOCK_ASET
```

**New Unit:**
```
TERSEDIA → PREPARED → RENTAL_READY → DISEWA
```

**Business Logic (from UnitWorkflowStatus.php):**

```php
'TUKAR' => [
    'DISETUJUI' => [
        'update_unit_status' => 'UNIT_AKAN_DITUKAR',
        'prepare_replacement_unit' => true,
        'notify_customer' => true
    ],
    'UNIT_DITUKAR' => [
        'update_old_unit_status' => 'UNIT_TUKAR_SELESAI',
        'update_new_unit_status' => 'DISEWA',
        'transfer_contract_to_new_unit' => true
    ],
    'SAMPAI_KANTOR' => [
        'update_old_unit_status' => 'STOCK_ASET',
        'disconnect_old_unit_contract' => true,
        'quality_check_old_unit' => true
    ]
]
```

**Contract Transfer Logic (DeliveryInstructionService.php):**

```php
public function processUnitTukar($oldUnitIds, $newUnitIds, $diId, $stage) {
    $stageActions = UnitWorkflowStatus::getStageActions($stage, 'TUKAR');
    
    // Process old units
    foreach ($oldUnitIds as $unitId) {
        if (isset($stageActions['update_old_unit_status'])) {
            $this->updateUnitStatus($unitId, $stageActions['update_old_unit_status'], $diId);
        }
        if (isset($stageActions['disconnect_old_unit_contract'])) {
            $this->disconnectUnitFromContract($unitId, $stage);
        }
    }
    
    // Process new units
    foreach ($newUnitIds as $unitId) {
        if (isset($stageActions['update_new_unit_status'])) {
            $this->updateUnitStatus($unitId, $stageActions['update_new_unit_status'], $diId);
        }
        if (isset($stageActions['transfer_contract_to_new_unit'])) {
            $this->transferContractToNewUnit($oldUnitIds[0], $unitId);
        }
    }
}

protected function transferContractToNewUnit($oldUnitId, $newUnitId) {
    // ⚠️ CRITICAL: References non-existent kontrak_unit table
    $oldContractUnit = $this->db->table('kontrak_unit')
        ->where('unit_id', $oldUnitId)
        ->where('status', 'AKTIF')
        ->get()->getRowArray();
    
    if ($oldContractUnit) {
        // Mark old unit as DITUKAR
        $this->db->table('kontrak_unit')
            ->where('id', $oldContractUnit['id'])
            ->update([
                'status' => 'DITUKAR',
                'tanggal_tukar' => date('Y-m-d H:i:s'),
                'unit_pengganti_id' => $newUnitId
            ]);
        
        // Create new contract_unit for new unit
        $this->db->table('kontrak_unit')->insert([
            'kontrak_id' => $oldContractUnit['kontrak_id'],
            'unit_id' => $newUnitId,
            'tanggal_mulai' => date('Y-m-d'),
            'status' => 'AKTIF',
            'unit_sebelumnya_id' => $oldUnitId
        ]);
    }
}
```

**⚠️ IMPLEMENTATION ISSUE:**
The `transferContractToNewUnit()` method will **FAIL** because `kontrak_unit` table does not exist. Current system should use:

```sql
-- Update old unit
UPDATE inventory_unit SET
    kontrak_id = NULL,
    workflow_status = 'STOCK_ASET',
    contract_disconnect_date = NOW(),
    contract_disconnect_stage = 'UNIT_DITUKAR'
WHERE id_inventory_unit = [old_unit_id];

-- Update new unit
UPDATE inventory_unit SET
    kontrak_id = [kontrak.id],
    workflow_status = 'DISEWA',
    spk_id = [spk.id],
    delivery_instruction_id = [di.id],
    tanggal_kirim = NOW()
WHERE id_inventory_unit = [new_unit_id];
```

**Attachment Transfer Consideration:**
Currently, there is **NO automatic attachment transfer** during TUKAR operations. The system should:
1. Check if old unit has battery/charger/attachment
2. Optionally transfer attachments to new unit
3. Use KANIBAL mode logic (2-step detach→attach) if transferring

**Code References:**
- Workflow logic: `DeliveryInstructionService.php::processUnitTukar()` lines 188-227
- Contract transfer: `DeliveryInstructionService.php::transferContractToNewUnit()` lines 272-310
- Status config: `UnitWorkflowStatus.php::getTukarWorkflow()` lines 45-55
- Controller: `Operational.php::processWorkflowApproval()` lines 1695-1702

### 4.4 RELOKASI (Relocation) Workflow

**Purpose:** Move units between locations (internal transfers, optimization, emergency)

**Workflow Stages:**
1. **DIAJUKAN:** Request to relocate unit
2. **DISETUJUI:** Relocation approved
3. **PERSIAPAN_UNIT:** Prepare transport logistics
4. **DALAM_PERJALANAN:** Unit in transit
5. **SAMPAI_LOKASI:** Unit arrived at new location
6. **SELESAI:** Location updated, unit operational

**Unit Status Changes:**
```
DISEWA/TERSEDIA → [same status] (location changed only)
```

**Business Logic:**
- Does not change unit operational status
- Updates `inventory_unit.lokasi_unit`
- May update `inventory_unit.area_id` for inter-branch transfers
- Can relocate units under active contracts (RELOKASI_OPTIMASI)

**Database Updates:**
```sql
UPDATE inventory_unit SET
    lokasi_unit = [new_location],
    area_id = [new_area_id],  -- If inter-branch
    delivery_instruction_id = [di.id]
WHERE id_inventory_unit = [unit_id];
```

**Code References:**
- Unit selection: `DeliveryInstructionService.php::getAvailableUnits()` (same as ANTAR)
- Stage approval: `Operational.php::diApprove()` lines 900-1030

---

## 5. CRITICAL FINDINGS & ISSUES

### 5.1 kontrak_unit Table Missing ❌ CRITICAL

**Severity:** HIGH  
**Impact:** TARIK and TUKAR workflows will fail  
**Status:** Non-functional code in production

**Problem:**
- Service layer references `kontrak_unit` table extensively (8 locations)
- Table does not exist in database
- No junction table for contract-unit relationship
- No status tracking for DITARIK, DITUKAR operations

**Code Locations:**
```php
// DeliveryInstructionService.php
Line 32:  ->table('kontrak_unit ku')
Line 105: SELECT COUNT(*) FROM kontrak_unit
Line 247: ->table('kontrak_unit')
Line 255: ->table('kontrak_unit')->where('id',...)->update([...])
Line 275: ->table('kontrak_unit')->where('unit_id',...)->get()
Line 283: ->table('kontrak_unit')->where('id',...)->update(['status'=>'DITUKAR'])
Line 293: ->table('kontrak_unit')->insert([...])
Line 409: ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit')
```

**Current Architecture:**
- Direct relationship: `inventory_unit.kontrak_id → kontrak.id`
- No junction table for many-to-many
- No unit-level status tracking
- `workflow_status` column exists but always NULL

**Recommended Fix:**

**Option A: Create kontrak_unit table (Preferred)**
```sql
CREATE TABLE kontrak_unit (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kontrak_id INT UNSIGNED NOT NULL,
    unit_id INT UNSIGNED NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE,
    status ENUM('AKTIF','DITARIK','DITUKAR','NON_AKTIF') DEFAULT 'AKTIF',
    
    -- TARIK tracking
    tanggal_tarik DATETIME,
    stage_tarik VARCHAR(50),
    
    -- TUKAR tracking
    tanggal_tukar DATETIME,
    unit_pengganti_id INT UNSIGNED,
    unit_sebelumnya_id INT UNSIGNED,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES inventory_unit(id_inventory_unit) ON DELETE CASCADE,
    UNIQUE KEY unique_active_unit (kontrak_id, unit_id, status),
    INDEX idx_status (status),
    INDEX idx_kontrak (kontrak_id),
    INDEX idx_unit (unit_id)
);
```

**Option B: Update service code to use inventory_unit directly**
```php
// Replace all kontrak_unit references with inventory_unit queries
protected function disconnectUnitFromContract($unitId, $stage) {
    $unit = $this->db->table('inventory_unit')
        ->where('id_inventory_unit', $unitId)
        ->where('kontrak_id IS NOT NULL')
        ->get()->getRowArray();
    
    if ($unit) {
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $unitId)
            ->update([
                'kontrak_id' => NULL,
                'workflow_status' => 'STOCK_ASET',
                'contract_disconnect_date' => date('Y-m-d H:i:s'),
                'contract_disconnect_stage' => $stage
            ]);
    }
}
```

### 5.2 workflow_status Column Unused ⚠️ MEDIUM

**Severity:** MEDIUM  
**Impact:** No runtime status tracking for DI workflows  
**Status:** Dead column, not populated

**Problem:**
- `inventory_unit.workflow_status` column exists
- Always NULL for all 12 contracted units checked
- UnitWorkflowStatus constants not applied to database
- No status progression tracking in real-time

**Expected Status Values (from UnitWorkflowStatus.php):**
```php
const UNIT_AKAN_DITARIK = 'UNIT_AKAN_DITARIK';
const UNIT_SEDANG_DITARIK = 'UNIT_SEDANG_DITARIK';
const UNIT_PULANG = 'UNIT_PULANG';
const STOCK_ASET = 'STOCK_ASET';
const UNIT_AKAN_DITUKAR = 'UNIT_AKAN_DITUKAR';
const UNIT_SEDANG_DITUKAR = 'UNIT_SEDANG_DITUKAR';
const UNIT_TUKAR_SELESAI = 'UNIT_TUKAR_SELESAI';
```

**Recommended Fix:**
Update `updateUnitStatus()` method to populate `workflow_status`:

```php
protected function updateUnitStatus($unitId, $newStatus, $diId) {
    $this->db->table('inventory_unit')
        ->where('id_inventory_unit', $unitId)
        ->update([
            'workflow_status' => $newStatus,  // Add this
            'updated_at' => date('Y-m-d H:i:s'),
            'di_workflow_id' => $diId
        ]);
}
```

### 5.3 Attachment Transfer Not Implemented ⚠️ MEDIUM

**Severity:** MEDIUM  
**Impact:** Manual work required for TUKAR operations  
**Status:** Missing feature

**Problem:**
- TUKAR operations do not transfer battery/charger/attachment automatically
- Old unit may have valuable attachments that should move to new unit
- No integration with KANIBAL system for attachment transfers
- Service team must manually detach and re-attach accessories

**Recommended Implementation:**
```php
public function processUnitTukar($oldUnitIds, $newUnitIds, $diId, $stage) {
    // ... existing code ...
    
    // NEW: Transfer attachments during UNIT_DITUKAR stage
    if ($stage === 'UNIT_DITUKAR' && isset($stageActions['transfer_contract_to_new_unit'])) {
        foreach ($oldUnitIds as $idx => $oldUnitId) {
            $newUnitId = $newUnitIds[$idx] ?? null;
            if ($newUnitId) {
                $this->transferAttachmentsToNewUnit($oldUnitId, $newUnitId, $diId);
            }
        }
    }
}

protected function transferAttachmentsToNewUnit($oldUnitId, $newUnitId, $diId) {
    // Get attachments from old unit
    $attachments = $this->db->table('inventory_attachment')
        ->where('id_inventory_unit', $oldUnitId)
        ->get()->getResultArray();
    
    foreach ($attachments as $attachment) {
        // Use Service.php KANIBAL 2-step update logic
        // Step 1: Detach from old unit
        $this->db->table('inventory_attachment')
            ->where('id_inventory_attachment', $attachment['id_inventory_attachment'])
            ->update([
                'id_inventory_unit' => NULL,
                'status' => 'AVAILABLE'
            ]);
        
        // Step 2: Attach to new unit
        $this->db->table('inventory_attachment')
            ->where('id_inventory_attachment', $attachment['id_inventory_attachment'])
            ->update([
                'id_inventory_unit' => $newUnitId,
                'status' => 'IN_USE'
            ]);
        
        // Log transfer
        $this->db->table('attachment_transfer_log')->insert([
            'attachment_id' => $attachment['id_inventory_attachment'],
            'from_unit_id' => $oldUnitId,
            'to_unit_id' => $newUnitId,
            'transfer_type' => 'TUKAR',
            'di_id' => $diId,
            'triggered_by' => 'DI_WORKFLOW'
        ]);
    }
}
```

### 5.4 DI Workflow Stages Table Missing ℹ️ LOW

**Severity:** LOW  
**Impact:** No granular stage tracking  
**Status:** Feature not implemented

**Problem:**
- Database schema shows `di_workflow_stages` table in SQL file (line 372)
- Table does not exist in current database
- No detailed stage progression logging
- Only high-level status_di field used

**Table Structure (from SQL):**
```sql
CREATE TABLE di_workflow_stages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    di_id INT UNSIGNED NOT NULL,
    stage_code VARCHAR(50) NOT NULL,
    stage_name VARCHAR(100) NOT NULL,
    status ENUM('PENDING','IN_PROGRESS','COMPLETED','CANCELLED') DEFAULT 'PENDING',
    started_at DATETIME,
    completed_at DATETIME,
    notes TEXT,
    approved_by INT UNSIGNED,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (di_id) REFERENCES delivery_instructions(id) ON DELETE CASCADE,
    INDEX idx_di (di_id),
    INDEX idx_stage (stage_code),
    INDEX idx_status (status)
);
```

**Benefit of Implementation:**
- Granular audit trail for each DI stage
- Timestamp each approval (perencanaan, berangkat, sampai)
- Track who approved each stage
- Historical reporting on stage durations
- Better SLA monitoring

---

## 6. WORKFLOW VALIDATION RESULTS

### 6.1 ANTAR Workflow ✅ FUNCTIONAL

**Status:** Working correctly  
**Validation Points:**
- ✅ Unit selection from available inventory
- ✅ SPK validation (status must be READY)
- ✅ Accessories preparation (battery, charger, attachment)
- ✅ Contract activation on delivery (sampai stage)
- ✅ Unit linking to kontrak, SPK, DI
- ✅ Pricing copy from quotation_specifications
- ✅ Accessories copy from prepared_units

**Code Flow:**
1. `Marketing.php::diCreate()` → Validate SPK, select units
2. `Operational.php::diApprove('perencanaan')` → Approve delivery plan
3. `Operational.php::diApprove('berangkat')` → Capture driver/vehicle
4. `Operational.php::diApprove('sampai')` → Activate contract, link units

**No Issues Found**

### 6.2 TARIK Workflow ❌ BROKEN

**Status:** Will fail at runtime  
**Validation Points:**
- ✅ Unit selection from contract units
- ❌ **processUnitTarik() references kontrak_unit table** (does not exist)
- ❌ **disconnectUnitFromContract() will fail** (table missing)
- ❌ No status update to workflow_status column
- ❌ No contract disconnection logging

**Critical Code That Will Fail:**
```php
// Line 247 in DeliveryInstructionService.php
$contractUnit = $this->db->table('kontrak_unit')  // ❌ Table does not exist
    ->where('unit_id', $unitId)
    ->where('status', 'AKTIF')
    ->get()->getRowArray();
```

**Required Fixes:**
1. Create `kontrak_unit` table OR rewrite service to use `inventory_unit`
2. Update `workflow_status` column during stage transitions
3. Implement contract disconnection logging

### 6.3 TUKAR Workflow ❌ BROKEN

**Status:** Will fail at runtime  
**Validation Points:**
- ✅ Old and new unit selection logic
- ❌ **processUnitTukar() references kontrak_unit table** (does not exist)
- ❌ **transferContractToNewUnit() will fail** (table missing)
- ❌ No attachment transfer implementation
- ❌ No KANIBAL integration for accessories

**Critical Code That Will Fail:**
```php
// Line 275 in DeliveryInstructionService.php
$oldContractUnit = $this->db->table('kontrak_unit')  // ❌ Table does not exist
    ->where('unit_id', $oldUnitId)
    ->where('status', 'AKTIF')
    ->get()->getRowArray();

// Line 283 - Will never execute
$this->db->table('kontrak_unit')  // ❌ Table does not exist
    ->where('id', $oldContractUnit['id'])
    ->update(['status' => 'DITUKAR', ...]);

// Line 293 - Will never execute
$this->db->table('kontrak_unit')->insert([...]);  // ❌ Table does not exist
```

**Required Fixes:**
1. Create `kontrak_unit` table OR rewrite service completely
2. Implement attachment transfer logic
3. Integrate with KANIBAL 2-step update for battery/charger
4. Update both old and new unit workflow_status
5. Log contract transfer in activity log

### 6.4 RELOKASI Workflow ✅ FUNCTIONAL

**Status:** Working correctly  
**Validation Points:**
- ✅ Unit selection (no contract requirement)
- ✅ Location update on delivery
- ✅ Does not change operational status
- ✅ Can relocate contracted units

**Code Flow:**
Same as ANTAR workflow, only updates `lokasi_unit` and `area_id` fields.

**No Issues Found**

---

## 7. BUSINESS LOGIC VALIDATION

### 7.1 Jenis-Tujuan Compatibility ✅

**Validation:** All tujuan_perintah_kerja records properly linked to parent jenis

**Database Query:**
```sql
SELECT tpk.kode, tpk.nama, jpk.kode as jenis_kode
FROM tujuan_perintah_kerja tpk
JOIN jenis_perintah_kerja jpk ON jpk.id = tpk.jenis_perintah_id
ORDER BY jpk.kode, tpk.kode;
```

**Result:** All 12 tujuan properly mapped to 4 jenis types ✅

### 7.2 SPK Selection Rules ✅

**Validation:** Contract-based vs unit-selection logic correctly implemented

**Code Location:** `Operational.php::getDiFormData()` lines 1560-1595

```php
if (in_array($jenis['kode'], ['TARIK', 'TUKAR'])) {
    $workflowType = 'contract_based';
    // Must select SPK with active contract
} else {
    $workflowType = 'unit_selection';
    // Can select any READY SPK
}
```

**Validation:** Logic correctly differentiates workflow types ✅

### 7.3 Unit Availability Check ⚠️

**Validation:** Excludes units already in active DI

**Code Location:** `DeliveryInstructionService.php::getAvailableUnits()` lines 440-447

```php
$query->whereNotIn('iu.id_inventory_unit', function($subquery) {
    $subquery->select('unit_id')
        ->from('delivery_items di')
        ->join('delivery_instructions dins', 'dins.id = di.delivery_instruction_id')
        ->where('unit_id IS NOT NULL')
        ->whereIn('dins.status', ['DIAJUKAN', 'DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN']);
});
```

**Issue:** Subquery uses incorrect status values:
- Database uses: `DIAJUKAN`, `DISETUJUI`, `PERSIAPAN_UNIT`, `SIAP_KIRIM`, `DALAM_PERJALANAN`, `SAMPAI_LOKASI`, `SELESAI`
- Query excludes `SAMPAI_LOKASI` (should be included as "active")

**Recommended Fix:**
```php
->whereIn('dins.status_di', ['DIAJUKAN', 'DISETUJUI', 'PERSIAPAN_UNIT', 
                              'SIAP_KIRIM', 'DALAM_PERJALANAN', 'SAMPAI_LOKASI'])
```

---

## 8. DATABASE STATUS CHECK

### 8.1 Active DI Records

**Query:**
```sql
SELECT status_di, COUNT(*) as total 
FROM delivery_instructions 
GROUP BY status_di;
```

**Results:**
| Status | Count |
|--------|-------|
| DIAJUKAN | 1 |
| SIAP_KIRIM | 1 |
| SAMPAI_LOKASI | 2 |
| SELESAI | 1 |

**Analysis:**
- 5 total DI records
- No records in broken stages (no TARIK/TUKAR attempted yet)
- SAMPAI_LOKASI stage working (2 deliveries completed)

### 8.2 Contracted Units

**Query:**
```sql
SELECT COUNT(*) as total, workflow_status 
FROM inventory_unit 
WHERE kontrak_id IS NOT NULL 
GROUP BY workflow_status;
```

**Results:**
| Total | workflow_status |
|-------|-----------------|
| 12 | NULL |

**Analysis:**
- All 12 contracted units have NULL workflow_status
- Confirms workflow_status column not being populated
- Units linked via direct kontrak_id (no junction table)

---

## 9. RECOMMENDATIONS

### 9.1 Priority 1 - CRITICAL (Must Fix Before Production Use)

1. **Fix kontrak_unit Table Issue**
   - **Option A (Recommended):** Create `kontrak_unit` table as per schema
   - **Option B:** Rewrite DeliveryInstructionService to use `inventory_unit` directly
   - **Impact:** Without this, TARIK and TUKAR will crash

2. **Populate workflow_status Column**
   - Update `updateUnitStatus()` method to write to `workflow_status`
   - Backfill existing units with correct status
   - **Impact:** Without this, no runtime visibility into unit workflow state

### 9.2 Priority 2 - HIGH (Should Implement Soon)

3. **Implement Attachment Transfer for TUKAR**
   - Integrate with KANIBAL system for battery/charger transfer
   - Add 2-step detach→attach logic
   - Log transfers in `attachment_transfer_log`
   - **Impact:** Reduces manual work, prevents attachment loss

4. **Fix Unit Availability Subquery**
   - Include `SAMPAI_LOKASI` in active DI status check
   - Prevent double-booking of units
   - **Impact:** Prevents unit assignment conflicts

### 9.3 Priority 3 - MEDIUM (Nice to Have)

5. **Implement di_workflow_stages Table**
   - Create table as per schema
   - Log each stage approval with timestamp
   - Track who approved each stage
   - **Impact:** Better audit trail and SLA tracking

6. **Add Workflow Status Validation**
   - Prevent illegal status transitions
   - Validate stage prerequisites before approval
   - Add rollback capability for cancelled DI
   - **Impact:** More robust workflow enforcement

### 9.4 Priority 4 - LOW (Future Enhancements)

7. **Add Automated Notifications**
   - Notify customer on each stage (berangkat, sampai)
   - Alert service team for TARIK pickups
   - Reminder for overdue DI
   - **Impact:** Better communication and accountability

8. **Dashboard for DI Status**
   - Real-time view of in-progress DI
   - Unit location tracking
   - Stage duration analytics
   - **Impact:** Better operational visibility

---

## 10. CONCLUSION

### 10.1 Overall Assessment

**DI Workflow Status:** ⚠️ **PARTIALLY FUNCTIONAL**

**Working Components:**
- ✅ ANTAR workflow (delivery) fully functional
- ✅ RELOKASI workflow functional
- ✅ DI creation and stage approval mechanism
- ✅ Contract activation on delivery
- ✅ Unit linking to contracts, SPKs, and DIs
- ✅ Pricing and accessories sync from SPK

**Broken Components:**
- ❌ TARIK workflow (will crash due to missing kontrak_unit table)
- ❌ TUKAR workflow (will crash due to missing kontrak_unit table)
- ❌ Contract disconnection logic (references non-existent table)
- ❌ workflow_status column not populated (dead code)
- ❌ Attachment transfer not implemented

### 10.2 Risk Assessment

**Production Risk:** HIGH for TARIK/TUKAR, LOW for ANTAR/RELOKASI

**User Impact:**
- ANTAR and RELOKASI can be used safely
- TARIK and TUKAR MUST NOT be used until fixed
- Data corruption risk if TUKAR attempted (contract relationships will break)

### 10.3 Implementation Correctness

**Question:** Apakah implementasi DITUKAR sudah benar?

**Answer:** ❌ **TIDAK BENAR**

**Alasan:**
1. Code references `kontrak_unit` table yang tidak exist
2. Method `transferContractToNewUnit()` akan throw database error
3. Status DITUKAR tidak pernah tercatat karena table missing
4. Tidak ada attachment transfer mechanism
5. workflow_status tidak ter-update

**Untuk membuat TUKAR berfungsi, harus:**
1. Create `kontrak_unit` table, ATAU
2. Rewrite complete service layer untuk pakai `inventory_unit` langsung
3. Implement attachment transfer logic
4. Update workflow_status column
5. Test end-to-end dengan real data

---

## 11. CODE REFERENCES SUMMARY

### 11.1 Controllers

**Marketing.php:**
- `diCreate()` (lines 4759-5100): DI creation, unit selection, validation
- `generateDiNumber()` (lines 4747-4757): DI number generation

**Operational.php:**
- `diApprove()` (lines 775-1030): Stage approval, contract activation, unit updates
- `getDiFormData()` (lines 1555-1595): Jenis/tujuan selection, workflow type determination
- `getContractUnits()` (lines 1603-1670): Contract-based unit selection for TARIK/TUKAR
- `processWorkflowApproval()` (lines 1674-1737): TARIK/TUKAR workflow processing

### 11.2 Services

**DeliveryInstructionService.php:**
- `getContractUnits()` (lines 26-65): Get units from active contract
- `getAvailableSpkWithContractInfo()` (lines 67-150): SPK selection for contract-based workflows
- `processUnitTarik()` (lines 155-186): TARIK workflow logic ❌ BROKEN
- `processUnitTukar()` (lines 188-227): TUKAR workflow logic ❌ BROKEN
- `updateUnitStatus()` (lines 229-242): Unit status update
- `disconnectUnitFromContract()` (lines 244-270): Contract disconnection ❌ BROKEN
- `transferContractToNewUnit()` (lines 272-310): Contract transfer ❌ BROKEN
- `getAvailableUnits()` (lines 384-450): Unit selection for ANTAR/RELOKASI

### 11.3 Config

**UnitWorkflowStatus.php:**
- Constants (lines 10-25): Status definitions for TARIK/TUKAR
- `getTarikWorkflow()` (lines 30-40): TARIK status progression
- `getTukarWorkflow()` (lines 45-55): TUKAR status progression
- `getStageActions()` (lines 145-190): Stage-based actions for TARIK/TUKAR

---

**Document End**

*This audit provides complete visibility into DI workflow implementation, critical issues found, and actionable recommendations for fixing broken functionality.*
