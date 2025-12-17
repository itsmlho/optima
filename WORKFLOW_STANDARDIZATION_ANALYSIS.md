# WORKFLOW STANDARDIZATION - TARIK & TUKAR ANALYSIS

**Date:** December 17, 2025  
**Purpose:** Analyze and standardize FK relationships for TARIK and TUKAR workflows  
**Focus:** kontrak_id, customer_id, customer_location_id handling

---

## CURRENT SITUATION ANALYSIS

### inventory_unit FK Relationships:
```sql
kontrak_id           INT UNSIGNED  (FK to kontrak.id)
customer_id          INT           (FK to customer)
customer_location_id INT           (FK to customer_location)
```

### Tujuan Perintah Kerja Types:

**TARIK (4 types):**
1. **TARIK_HABIS_KONTRAK** - "Penarikan unit karena kontrak berakhir"
2. **TARIK_MAINTENANCE** - "Penarikan unit untuk perawatan/perbaikan"
3. **TARIK_PINDAH_LOKASI** - "Penarikan unit untuk dipindah ke lokasi lain"
4. **TARIK_RUSAK** - "Penarikan unit karena mengalami kerusakan"

**TUKAR (4 types):**
1. **TUKAR_UPGRADE** - "Penukaran dengan unit yang lebih tinggi spesifikasinya"
2. **TUKAR_DOWNGRADE** - "Penukaran dengan unit yang lebih rendah spesifikasinya"
3. **TUKAR_MAINTENANCE** - "Penukaran sementara selama unit di maintenance"
4. **TUKAR_RUSAK** - "Penukaran unit yang mengalami kerusakan"

---

## BUSINESS LOGIC ANALYSIS

### A. TARIK Workflows - Should FK be Disconnected?

#### 1. ✅ TARIK_HABIS_KONTRAK (Kontrak Berakhir)
**Business Meaning:** Contract ended, unit returned permanently

**Correct Behavior:**
- ❌ **kontrak_id** → Should be **DISCONNECTED** (set to NULL)
- ❌ **customer_id** → Should be **DISCONNECTED** (set to NULL)
- ❌ **customer_location_id** → Should be **DISCONNECTED** (set to NULL)

**Reason:** 
- Contract has ended, no longer bound to customer
- Unit returns to warehouse stock (TERSEDIA/STOCK_ASET)
- Ready for new contract with different customer

**Current Implementation:** ❌ **INCORRECT**
- `kontrak_unit.status` → 'DITARIK' ✓ (correct)
- `inventory_unit.kontrak_id` → **STILL LINKED** ❌ (wrong!)
- `inventory_unit.customer_id` → **STILL LINKED** ❌ (wrong!)

---

#### 2. ⚠️ TARIK_MAINTENANCE (Maintenance Sementara)
**Business Meaning:** Unit pulled for temporary maintenance/repair

**Correct Behavior:**
- ✅ **kontrak_id** → Should **REMAIN CONNECTED**
- ✅ **customer_id** → Should **REMAIN CONNECTED**
- ✅ **customer_location_id** → Should **REMAIN CONNECTED**

**Reason:**
- Contract still active, unit will return to same customer
- Customer still paying during maintenance (or paused)
- Temporary pull, not permanent disconnection

**Current Implementation:** ❌ **INCORRECT**
- Would disconnect all FKs (same as HABIS_KONTRAK) ❌

**Recommended Status Flow:**
```
DISEWA → MAINTENANCE_IN_PROGRESS → DISEWA (back to customer)
```

---

#### 3. ⚠️ TARIK_PINDAH_LOKASI (Pindah ke Lokasi Lain)
**Business Meaning:** Unit moved to different customer location

**Correct Behavior:**
- ✅ **kontrak_id** → Should **REMAIN CONNECTED**
- ✅ **customer_id** → Should **REMAIN CONNECTED**
- ⚠️ **customer_location_id** → Should be **UPDATED** (not NULL, but changed)

**Reason:**
- Same customer, same contract, different location
- Unit still under contract, just relocated
- Update `customer_location_id` to new location

**Current Implementation:** ❌ **INCORRECT**
- Would disconnect all FKs ❌

**Recommended Status Flow:**
```
DISEWA (Location A) → RELOCATING → DISEWA (Location B)
```

---

#### 4. ⚠️ TARIK_RUSAK (Unit Rusak)
**Business Meaning:** Unit damaged, needs repair

**Two Scenarios:**

**Scenario A: Damaged during contract (customer keeps or waits)**
- ✅ **kontrak_id** → Should **REMAIN CONNECTED** (if customer waits for repair)
- ✅ **customer_id** → Should **REMAIN CONNECTED**
- ✅ **customer_location_id** → Should **REMAIN CONNECTED**
- Status: UNDER_REPAIR → Returns to customer after fix

**Scenario B: Damaged beyond repair (contract terminated)**
- ❌ **kontrak_id** → Should be **DISCONNECTED**
- ❌ **customer_id** → Should be **DISCONNECTED**
- ❌ **customer_location_id** → Should be **DISCONNECTED**
- Status: DECOMMISSIONED / SCRAP

**Current Implementation:** ❌ **UNCLEAR**
- Single TARIK_RUSAK doesn't distinguish scenarios

---

### B. TUKAR Workflows - Should FK be Transferred?

#### 1. ✅ TUKAR_UPGRADE (Upgrade Spesifikasi)
**Business Meaning:** Replace with higher capacity unit

**Correct Behavior:**

**Old Unit:**
- ❌ **kontrak_id** → Should be **DISCONNECTED** (set to NULL)
- ❌ **customer_id** → Should be **DISCONNECTED** (set to NULL)
- ❌ **customer_location_id** → Should be **DISCONNECTED** (set to NULL)
- Status: DITUKAR → STOCK_ASET (returned to warehouse)

**New Unit:**
- ✅ **kontrak_id** → **TRANSFERRED** from old unit
- ✅ **customer_id** → **TRANSFERRED** from old unit
- ✅ **customer_location_id** → **TRANSFERRED** from old unit
- Status: TERSEDIA → DISEWA (now serving customer)

**Current Implementation:** ⚠️ **PARTIALLY CORRECT**
- `kontrak_unit` old: status='DITUKAR', unit_pengganti_id set ✓
- `kontrak_unit` new: created with same kontrak_id ✓
- `inventory_unit` old: **kontrak_id NOT DISCONNECTED** ❌
- `inventory_unit` new: **kontrak_id TRANSFERRED** ✓

---

#### 2. ✅ TUKAR_DOWNGRADE (Downgrade Spesifikasi)
**Business Meaning:** Replace with lower capacity unit

**Correct Behavior:** **SAME AS TUKAR_UPGRADE**
- Old unit: Disconnect all FKs
- New unit: Transfer all FKs from old unit

**Current Implementation:** ⚠️ **PARTIALLY CORRECT** (same issues as UPGRADE)

---

#### 3. ⚠️ TUKAR_MAINTENANCE (Ganti Sementara Saat Maintenance)
**Business Meaning:** Temporary replacement while original unit in maintenance

**Correct Behavior:**

**Old Unit (Original):**
- ✅ **kontrak_id** → Should **REMAIN CONNECTED** (temporarily paused)
- ✅ **customer_id** → Should **REMAIN CONNECTED**
- ✅ **customer_location_id** → Should **REMAIN CONNECTED**
- Status: MAINTENANCE_TEMPORARY_REPLACEMENT
- Note: Will return to customer after maintenance

**New Unit (Temporary):**
- ⚠️ **kontrak_id** → Should have **TEMPORARY LINK** (not permanent)
- ⚠️ **customer_id** → Should have **TEMPORARY LINK**
- ⚠️ **customer_location_id** → Should have **TEMPORARY LINK**
- Status: TEMPORARY_RENTAL
- Note: Will be returned to stock after original unit returns

**Current Implementation:** ❌ **INCORRECT**
- Treats as permanent TUKAR
- Original unit would be marked DITUKAR permanently ❌
- Doesn't distinguish temporary vs permanent replacement

**Required Enhancement:**
Need `is_temporary` flag in `kontrak_unit` table to track temporary replacements.

---

#### 4. ✅ TUKAR_RUSAK (Ganti Unit Rusak)
**Business Meaning:** Replace damaged unit with working unit

**Correct Behavior:** **SAME AS TUKAR_UPGRADE**
- Old unit: Disconnect all FKs (damaged unit returned)
- New unit: Transfer all FKs (replacement serves customer)

**Current Implementation:** ⚠️ **PARTIALLY CORRECT** (same issues)

---

## CRITICAL ISSUES FOUND

### Issue 1: ❌ TARIK Does Not Distinguish Temporary vs Permanent
**Problem:**
- All TARIK operations use same `disconnectUnitFromContract()` method
- No logic to differentiate HABIS_KONTRAK vs MAINTENANCE vs PINDAH_LOKASI
- All would disconnect FKs, which is WRONG for temporary pulls

**Impact:**
- TARIK_MAINTENANCE: Customer loses unit in system ❌
- TARIK_PINDAH_LOKASI: Contract disconnected unnecessarily ❌
- TARIK_RUSAK: No distinction between repair vs decommission ❌

---

### Issue 2: ❌ TUKAR Does Not Disconnect Old Unit FKs
**Problem:**
- `transferContractToNewUnit()` creates new kontrak_unit for new unit ✓
- BUT does NOT clear `inventory_unit.kontrak_id` on old unit ❌
- Old unit still shows linked to contract in inventory_unit table

**Impact:**
- Old unit appears to still be with customer in reports ❌
- Double-counting: Both old and new unit show same contract ❌
- Confusing for inventory tracking

---

### Issue 3: ❌ TUKAR_MAINTENANCE Treated as Permanent
**Problem:**
- No `is_temporary` flag to distinguish temporary vs permanent TUKAR
- Original unit marked DITUKAR permanently
- No way to return original unit to customer after maintenance

**Impact:**
- Cannot handle temporary replacements correctly ❌
- Original unit lost to customer permanently ❌
- Business flow broken

---

### Issue 4: ⚠️ customer_id and customer_location_id Not Managed
**Problem:**
- Service code only manages `kontrak_id`
- Does not update `customer_id` or `customer_location_id`
- FKs remain even after contract ends

**Impact:**
- Inconsistent data: Unit shows no contract but still has customer ⚠️
- Reports may show incorrect customer assignments ⚠️

---

## RECOMMENDED WORKFLOW STANDARDIZATION

### A. TARIK Workflows - Corrected Logic

#### TARIK_HABIS_KONTRAK:
```sql
-- Step 1: Update kontrak_unit
UPDATE kontrak_unit 
SET status = 'DITARIK', 
    tanggal_tarik = NOW(),
    stage_tarik = 'SAMPAI_KANTOR'
WHERE id = [kontrak_unit_id];

-- Step 2: DISCONNECT ALL FKs from inventory_unit
UPDATE inventory_unit
SET kontrak_id = NULL,
    customer_id = NULL,
    customer_location_id = NULL,
    workflow_status = 'STOCK_ASET',
    contract_disconnect_date = NOW(),
    contract_disconnect_stage = 'HABIS_KONTRAK'
WHERE id_inventory_unit = [unit_id];
```

**Result:** Unit fully disconnected, ready for new contract ✓

---

#### TARIK_MAINTENANCE:
```sql
-- Step 1: Update kontrak_unit (mark as maintenance, NOT DITARIK)
UPDATE kontrak_unit 
SET status = 'MAINTENANCE',  -- New status!
    maintenance_start = NOW(),
    maintenance_reason = 'Scheduled maintenance'
WHERE id = [kontrak_unit_id];

-- Step 2: KEEP ALL FKs in inventory_unit
UPDATE inventory_unit
SET workflow_status = 'MAINTENANCE_IN_PROGRESS',
    -- kontrak_id remains
    -- customer_id remains
    -- customer_location_id remains
    maintenance_location = 'WORKSHOP'
WHERE id_inventory_unit = [unit_id];
```

**Result:** Unit in maintenance but still bound to customer ✓

---

#### TARIK_PINDAH_LOKASI:
```sql
-- Step 1: Update kontrak_unit (no status change)
-- (No change to kontrak_unit, still AKTIF)

-- Step 2: UPDATE customer_location_id ONLY
UPDATE inventory_unit
SET customer_location_id = [new_location_id],
    workflow_status = 'RELOCATING',
    -- kontrak_id remains
    -- customer_id remains
    updated_at = NOW()
WHERE id_inventory_unit = [unit_id];

-- Step 3: After arrival, set back to DISEWA
UPDATE inventory_unit
SET workflow_status = 'DISEWA'
WHERE id_inventory_unit = [unit_id];
```

**Result:** Unit moved to new location, contract intact ✓

---

#### TARIK_RUSAK:
**Need to add tujuan_perintah field to specify:**
- TARIK_RUSAK_REPAIR (will return to customer)
- TARIK_RUSAK_DECOMMISSION (contract terminated)

**Option A: Repair (Keep FKs):**
```sql
-- Same as TARIK_MAINTENANCE
UPDATE kontrak_unit SET status = 'UNDER_REPAIR';
UPDATE inventory_unit 
SET workflow_status = 'UNDER_REPAIR',
    -- FKs remain connected
```

**Option B: Decommission (Clear FKs):**
```sql
-- Same as TARIK_HABIS_KONTRAK
UPDATE kontrak_unit SET status = 'DITARIK';
UPDATE inventory_unit 
SET kontrak_id = NULL, 
    customer_id = NULL,
    workflow_status = 'DECOMMISSIONED'
```

---

### B. TUKAR Workflows - Corrected Logic

#### TUKAR_UPGRADE / TUKAR_DOWNGRADE / TUKAR_RUSAK (Permanent):
```sql
-- Step 1: Mark old unit as DITUKAR
UPDATE kontrak_unit 
SET status = 'DITUKAR',
    tanggal_tukar = NOW(),
    unit_pengganti_id = [new_unit_id]
WHERE id = [old_kontrak_unit_id];

-- Step 2: DISCONNECT ALL FKs from OLD unit
UPDATE inventory_unit
SET kontrak_id = NULL,
    customer_id = NULL,
    customer_location_id = NULL,
    workflow_status = 'STOCK_ASET',
    contract_disconnect_date = NOW()
WHERE id_inventory_unit = [old_unit_id];

-- Step 3: Create new kontrak_unit for NEW unit
INSERT INTO kontrak_unit 
(kontrak_id, unit_id, status, unit_sebelumnya_id, created_by)
VALUES ([kontrak_id], [new_unit_id], 'AKTIF', [old_unit_id], [user_id]);

-- Step 4: TRANSFER ALL FKs to NEW unit
UPDATE inventory_unit
SET kontrak_id = [kontrak_id],
    customer_id = [customer_id],
    customer_location_id = [customer_location_id],
    workflow_status = 'DISEWA'
WHERE id_inventory_unit = [new_unit_id];

-- Step 5: Transfer attachments (already implemented)
CALL transferAttachments([old_unit_id], [new_unit_id]);
```

**Result:** Clean transfer, old unit freed, new unit serving customer ✓

---

#### TUKAR_MAINTENANCE (Temporary):
**Requires Schema Enhancement:**

```sql
-- Step 1: Mark old unit as TEMPORARILY_REPLACED (not DITUKAR!)
UPDATE kontrak_unit 
SET status = 'TEMPORARILY_REPLACED',  -- New status!
    temporary_replacement_date = NOW(),
    temporary_replacement_unit_id = [temp_unit_id]
WHERE id = [original_kontrak_unit_id];

-- Step 2: KEEP ALL FKs on ORIGINAL unit (in maintenance)
UPDATE inventory_unit
SET workflow_status = 'MAINTENANCE_WITH_REPLACEMENT',
    -- kontrak_id remains (paused but not disconnected)
    -- customer_id remains
    -- customer_location_id remains
    maintenance_location = 'WORKSHOP'
WHERE id_inventory_unit = [original_unit_id];

-- Step 3: Create TEMPORARY kontrak_unit for replacement
INSERT INTO kontrak_unit 
(kontrak_id, unit_id, status, is_temporary, original_unit_id, created_by)
VALUES ([kontrak_id], [temp_unit_id], 'TEMPORARY_ACTIVE', TRUE, [original_unit_id], [user_id]);

-- Step 4: TEMPORARY LINK for replacement unit
UPDATE inventory_unit
SET kontrak_id = [kontrak_id],
    customer_id = [customer_id],
    customer_location_id = [customer_location_id],
    workflow_status = 'TEMPORARY_RENTAL',
    is_temporary_assignment = TRUE
WHERE id_inventory_unit = [temp_unit_id];
```

**When Original Returns from Maintenance:**
```sql
-- Step 1: Mark temporary unit for return
UPDATE kontrak_unit 
SET status = 'TEMPORARY_ENDED',
    end_date = NOW()
WHERE unit_id = [temp_unit_id] AND is_temporary = TRUE;

-- Step 2: Disconnect temporary unit
UPDATE inventory_unit
SET kontrak_id = NULL,
    customer_id = NULL,
    customer_location_id = NULL,
    workflow_status = 'STOCK_ASET',
    is_temporary_assignment = FALSE
WHERE id_inventory_unit = [temp_unit_id];

-- Step 3: Restore original unit to customer
UPDATE kontrak_unit 
SET status = 'AKTIF',
    temporary_replacement_date = NULL,
    temporary_replacement_unit_id = NULL
WHERE id = [original_kontrak_unit_id];

UPDATE inventory_unit
SET workflow_status = 'DISEWA'
WHERE id_inventory_unit = [original_unit_id];
```

**Result:** Temporary replacement handled correctly, original unit returns ✓

---

## REQUIRED SCHEMA ENHANCEMENTS

### 1. kontrak_unit Table - Add Columns:
```sql
ALTER TABLE kontrak_unit 
ADD COLUMN is_temporary BOOLEAN DEFAULT FALSE COMMENT 'True for temporary replacements (TUKAR_MAINTENANCE)',
ADD COLUMN original_unit_id INT UNSIGNED NULL COMMENT 'Original unit ID for temporary replacements',
ADD COLUMN temporary_replacement_unit_id INT UNSIGNED NULL COMMENT 'Temp unit ID when original in maintenance',
ADD COLUMN temporary_replacement_date DATETIME NULL COMMENT 'Date temporary replacement started',
ADD COLUMN maintenance_start DATETIME NULL COMMENT 'Maintenance start date',
ADD COLUMN maintenance_reason VARCHAR(255) NULL COMMENT 'Reason for maintenance pull',
MODIFY COLUMN status ENUM('AKTIF','DITARIK','DITUKAR','NON_AKTIF','MAINTENANCE','UNDER_REPAIR','TEMPORARILY_REPLACED','TEMPORARY_ACTIVE','TEMPORARY_ENDED') 
    NOT NULL DEFAULT 'AKTIF';
```

### 2. inventory_unit Table - Add Columns:
```sql
ALTER TABLE inventory_unit
ADD COLUMN is_temporary_assignment BOOLEAN DEFAULT FALSE COMMENT 'True if unit is temporary replacement',
ADD COLUMN maintenance_location VARCHAR(100) NULL COMMENT 'Workshop/location during maintenance';
```

### 3. New ENUM Values for workflow_status:
```sql
-- Add to existing workflow_status column:
- 'MAINTENANCE_IN_PROGRESS'
- 'MAINTENANCE_WITH_REPLACEMENT'
- 'UNDER_REPAIR'
- 'RELOCATING'
- 'TEMPORARY_RENTAL'
- 'DECOMMISSIONED'
```

---

## IMPLEMENTATION PLAN

### Phase 1: Schema Updates (Priority: HIGH)
1. ✅ Alter `kontrak_unit` table - add new columns
2. ✅ Alter `inventory_unit` table - add new columns
3. ✅ Update `workflow_status` ENUM values
4. ✅ Create migration script

### Phase 2: Service Layer Updates (Priority: HIGH)
1. ✅ Update `disconnectUnitFromContract()` - add tujuan logic
2. ✅ Update `transferContractToNewUnit()` - disconnect old unit FKs
3. ✅ Add `transferCustomerRelationships()` - handle customer_id, customer_location_id
4. ✅ Add `handleTemporaryReplacement()` - TUKAR_MAINTENANCE logic
5. ✅ Update `processUnitTarik()` - conditional FK disconnect
6. ✅ Update `processUnitTukar()` - permanent vs temporary logic

### Phase 3: Workflow Configuration (Priority: MEDIUM)
1. ✅ Split TARIK_RUSAK into two tujuan:
   - TARIK_RUSAK_REPAIR (temporary)
   - TARIK_RUSAK_DECOMMISSION (permanent)
2. ✅ Add workflow rules per tujuan type
3. ✅ Update UnitWorkflowStatus.php with new statuses

### Phase 4: Testing (Priority: HIGH)
1. ✅ Test TARIK_HABIS_KONTRAK (full disconnect)
2. ✅ Test TARIK_MAINTENANCE (keep FKs)
3. ✅ Test TARIK_PINDAH_LOKASI (location update only)
4. ✅ Test TUKAR permanent (transfer FKs, clear old)
5. ✅ Test TUKAR_MAINTENANCE (temporary replacement)

---

## SUMMARY OF RECOMMENDATIONS

### ✅ TARIK Workflows:
| Tujuan | Disconnect kontrak_id? | Disconnect customer_id? | Disconnect location_id? |
|--------|------------------------|-------------------------|------------------------|
| HABIS_KONTRAK | ✅ YES | ✅ YES | ✅ YES |
| MAINTENANCE | ❌ NO (keep) | ❌ NO (keep) | ❌ NO (keep) |
| PINDAH_LOKASI | ❌ NO (keep) | ❌ NO (keep) | ⚠️ UPDATE (change) |
| RUSAK_REPAIR | ❌ NO (keep) | ❌ NO (keep) | ❌ NO (keep) |
| RUSAK_DECOMMISSION | ✅ YES | ✅ YES | ✅ YES |

### ✅ TUKAR Workflows:
| Tujuan | Old Unit | New Unit | Temporary? |
|--------|----------|----------|------------|
| UPGRADE | Disconnect ALL FKs | Transfer ALL FKs | ❌ Permanent |
| DOWNGRADE | Disconnect ALL FKs | Transfer ALL FKs | ❌ Permanent |
| RUSAK | Disconnect ALL FKs | Transfer ALL FKs | ❌ Permanent |
| MAINTENANCE | Keep ALL FKs (paused) | Temporary FKs | ✅ Temporary |

---

## NEXT STEPS

**User Decision Required:**
1. ✅ Approve schema enhancements?
2. ✅ Approve workflow standardization logic?
3. ✅ Proceed with implementation?

**If approved, I will:**
1. Create migration scripts for schema updates
2. Update DeliveryInstructionService.php with corrected logic
3. Add new methods for temporary replacements
4. Create comprehensive tests
5. Update documentation

---

**Analysis Complete**  
**Status:** Awaiting approval to implement standardization
