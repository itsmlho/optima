# MARKETING MODULE COMPLETE REFACTORING PLAN

**Project:** OPTIMA - Marketing Module Restructuring  
**Date:** March 4, 2026  
**Version:** 1.0 (Complete Implementation Plan)  
**Status:** Ready for Implementation

---

## EXECUTIVE SUMMARY

### Overview
Comprehensive audit of the marketing module (Quotation → Customer → Location → Contract → Inventory Unit) revealed **critical structural issues** requiring systematic refactoring:

- **50+ Indonesian column names** causing code inconsistency
- **Critical data redundancy** in inventory_unit table (kontrak_id, customer_id, customer_location_id stored redundantly)
- **Missing FK constraints** risking data integrity (kontrak.customer_location_id, customers.marketing_name)
- **God controllers** (Marketing.php: 9,213 lines, CustomerManagementController.php: 2,123 lines)
- **No service layer** - business logic scattered across controllers and models

### Critical Problems (Immediate Attention Required)

| Issue | Severity | Impact | Timeline |
|-------|----------|--------|----------|
| **inventory_unit redundant FKs** | CRITICAL | Data integrity - units can be out-of-sync with contracts | Week 1-2 |
| **customers.marketing_name** VARCHAR instead of FK | CRITICAL | No referential integrity for marketing assignments | Week 1-2 |
| **Missing FK: kontrak.customer_location_id** | HIGH | Orphaned contract records possible | Week 1-2 |
| **Indonesian column naming** | HIGH | Team productivity, code maintainability | Week 3-5 |
| **God controllers** | MEDIUM | Code maintainability, testing difficulty | Week 6-10 |

### Expected Business Impact

**Positive Outcomes:**
- ✅ **Data integrity guaranteed** - FK constraints prevent orphaned records
- ✅ **50% faster development** - consistent English naming, clear code structure
- ✅ **Reduced bugs** - service layer encapsulates business logic
- ✅ **Better performance** - optimized queries, proper indexing
- ✅ **Easier onboarding** - new developers understand code faster

**Risks During Transition:**
- ⚠️ 2-4 week testing period per phase (staging deployment)
- ⚠️ Training needed for team on new structure
- ⚠️ Temporary dual-column support during migration

### Resources Required

- **Lead Developer:** 1 FTE (13 weeks)
- **QA Engineer:** 0.5 FTE (testing across 3 phases)
- **DevOps:** 0.25 FTE (staging/production deployment)
- **Staging Environment:** Required for parallel testing
- **Database Backup:** Daily during migration phases

### Total Timeline

**13 weeks (3.25 months) for complete refactoring**

- Phase 1A: Critical Data Integrity (Week 1-2)
- Phase 1B: Database Column Renaming (Week 3-5)
- Phase 2: Backend Refactoring (Week 6-10)
- Phase 3: Frontend Consolidation (Week 11-13)

---

## DETAILED FINDINGS

### 1. Database Schema Issues

#### 1.1 Primary Key Naming Inconsistency

| Table | Current PK | Should Be | Impact |
|-------|------------|-----------|--------|
| quotations | `id_quotation` | `id` | HIGH - Framework conventions |
| quotation_specifications | `id_specification` | `id` | HIGH - FK references inconsistent |
| inventory_unit | `id_inventory_unit` | `id` | HIGH - 50+ queries affected |

**Files Affected:** 
- [app/Models/QuotationModel.php](app/Models/QuotationModel.php)
- [app/Models/InventoryUnitModel.php](app/Models/InventoryUnitModel.php)
- [app/Controllers/Marketing.php](app/Controllers/Marketing.php) (100+ references)

#### 1.2 Indonesian Column Names (Complete List)

##### kontrak Table (7 columns)

| Current | New | Data Type | Priority |
|---------|-----|-----------|----------|
| `jenis_sewa` | `billing_period_type` | ENUM('BULANAN','MINGGUAN','HARIAN') | CRITICAL |
| `tanggal_mulai` | `start_date` | DATE | CRITICAL |
| `tanggal_berakhir` | `end_date` | DATE | CRITICAL |
| `dibuat_oleh` | `created_by_user_id` | INT (FK to users.id) | CRITICAL |
| `dibuat_pada` | `created_at` | TIMESTAMP | CRITICAL |
| `diperbarui_pada` | `updated_at` | TIMESTAMP | CRITICAL |
| `nilai_total` | `total_contract_value` | DECIMAL(15,2) | HIGH |

**Impact:** 30-40 references in [Marketing.php](app/Controllers/Marketing.php)

##### kontrak_unit Table (5 columns)

| Current | New | Data Type | Priority |
|---------|-----|-----------|----------|
| `tanggal_mulai` | `assignment_start_date` | DATE | HIGH |
| `tanggal_selesai` | `assignment_end_date` | DATE | HIGH |
| `tanggal_tarik` | `withdrawal_date` | DATE | HIGH |
| `stage_tarik` | `withdrawal_stage_code` | VARCHAR(50) | MEDIUM |
| `tanggal_tukar` | `swap_date` | DATE | MEDIUM |

**Impact:** Unit swap/withdrawal workflows in Marketing controller

##### spk Table (10 columns)

| Current | New | Data Type | Priority |
|---------|-----|-----------|----------|
| `nomor_spk` | `spk_number` | VARCHAR(50) | HIGH |
| `jenis_spk` | `spk_type` | ENUM('UNIT','ATTACHMENT') | HIGH |
| `jumlah_unit` | `unit_quantity` | INT | HIGH |
| `po_kontrak_nomor` | `contract_po_number` | VARCHAR(100) | HIGH |
| `pelanggan` | `customer_name_snapshot` | VARCHAR(255) | HIGH |
| `lokasi` | `delivery_location` | TEXT | HIGH |
| `catatan` | `notes` | TEXT | MEDIUM |
| `dibuat_oleh` | `created_by_user_id` | INT | HIGH |
| `dibuat_pada` | `created_at` | TIMESTAMP | HIGH |
| `diperbarui_pada` | `updated_at` | TIMESTAMP | HIGH |

**Impact:** 50+ references in Marketing.php SPK methods  
**Note:** `pelanggan` renamed to `customer_name_snapshot` (intentional denormalization for audit trail)

##### delivery_instructions Table (21 columns)

| Current | New | Data Type | Priority |
|---------|-----|-----------|----------|
| `nomor_di` | `di_number` | VARCHAR(50) | HIGH |
| `jenis_spk` | `source_spk_type` | VARCHAR(50) | HIGH |
| `po_kontrak_nomor` | `contract_po_number` | VARCHAR(100) | HIGH |
| `pelanggan` | `customer_name_snapshot` | VARCHAR(255) | HIGH |
| `lokasi` | `delivery_address` | TEXT | HIGH |
| `tanggal_kirim` | `delivery_date` | DATE | HIGH |
| `catatan` | `notes` | TEXT | MEDIUM |
| `dibuat_oleh` | `created_by_user_id` | INT | HIGH |
| `dibuat_pada` | `created_at` | TIMESTAMP | HIGH |
| `diperbarui_pada` | `updated_at` | TIMESTAMP | HIGH |
| `perencanaan_tanggal_approve` | `planning_approved_date` | DATETIME | HIGH |
| `estimasi_sampai` | `estimated_arrival_date` | DATE | MEDIUM |
| `nama_supir` | `driver_name` | VARCHAR(255) | MEDIUM |
| `no_hp_supir` | `driver_phone` | VARCHAR(20) | MEDIUM |
| `no_sim_supir` | `driver_license_number` | VARCHAR(50) | MEDIUM |
| `kendaraan` | `vehicle_type` | VARCHAR(100) | MEDIUM |
| `no_polisi_kendaraan` | `vehicle_plate_number` | VARCHAR(20) | MEDIUM |
| `berangkat_tanggal_approve` | `departure_approved_date` | DATETIME | HIGH |
| `catatan_berangkat` | `departure_notes` | TEXT | MEDIUM |
| `sampai_tanggal_approve` | `arrival_approved_date` | DATETIME | HIGH |
| `catatan_sampai` | `arrival_notes` | TEXT | MEDIUM |

**Impact:** DI workflow methods, triggers that update inventory_unit.delivery_date  
**Triggers to update:** Multiple triggers reference these columns

##### inventory_unit Table (6 columns)

| Current | New | Data Type | Priority |
|---------|-----|-----------|----------|
| `tahun_unit` | `manufacturing_year` | YEAR | MEDIUM |
| `lokasi_unit` | `current_location_address` | VARCHAR(255) | MEDIUM |
| `tanggal_kirim` | `delivery_date` | DATE | MEDIUM |
| `keterangan` | `notes` | TEXT | LOW |
| `sn_mast` | `mast_serial_number` | VARCHAR(100) | LOW |
| `sn_mesin` | `engine_serial_number` | VARCHAR(100) | LOW |

**Note:** `lokasi_unit` is denormalized but auto-updated by triggers (acceptable pattern)

##### Other Tables (Summary)

- **customers:** `marketing_name` → should be FK to users.id (not just rename)
- **15+ supporting tables** follow similar pattern (`dibuat_pada/diperbarui_pada` → `created_at/updated_at`)

**Total Indonesian Columns:** 50+

#### 1.3 Data Redundancy (CRITICAL)

##### inventory_unit Redundant Foreign Keys

**Problem:** [inventory_unit table](databases/optima_ci (3).sql#L14289) stores:
```sql
kontrak_id INT,              -- REDUNDANT (use kontrak_unit junction)
customer_id INT,             -- REDUNDANT (derive via kontrak → customer_locations)
customer_location_id INT,    -- REDUNDANT (derive via kontrak)
```

**Correct Data Flow:**
```
inventory_unit (id_inventory_unit)
    ↓
kontrak_unit (unit_id, kontrak_id) -- JUNCTION TABLE (source of truth)
    ↓
kontrak (id, customer_location_id)
    ↓
customer_locations (id, customer_id)
    ↓
customers (id)
```

**Risk:** HIGH - No triggers synchronize these fields, can become out-of-sync

**Evidence of Usage:**
- [InventoryUnitModel.php](app/Models/InventoryUnitModel.php) line 30-32: `'kontrak_id', 'customer_id', 'customer_location_id'` in allowedFields
- Direct queries in controllers bypass kontrak_unit junction

**Solution:** Remove redundant fields, always query via kontrak_unit junction, create VIEW for backward compatibility

##### kontrak Calculated Fields

**Fields:**
```sql
nilai_total DECIMAL(15,2),    -- Sum of unit rates × period
total_units INT,              -- Count of units in contract
```

**Analysis:**
- `total_units` - REDUNDANT (query: `SELECT COUNT(*) FROM kontrak_unit WHERE kontrak_id = ?`)
- `nilai_total` - **BUSINESS DECISION NEEDED:** Is this pre-negotiated contract value OR calculated sum?

**Current Behavior:** [Marketing.php line 6480-6483](app/Controllers/Marketing.php#L6480) calculates on-the-fly for DataTables

**Solution:** Remove `total_units`, clarify business rule for `nilai_total`

#### 1.4 Missing Foreign Key Constraints

| Table.Column | References | Current Status | Cascade Rule Needed |
|--------------|------------|----------------|---------------------|
| **kontrak.customer_location_id** | customer_locations.id | ❌ NOT ENFORCED | ON DELETE RESTRICT |
| **customers.marketing_name** | users.id | ❌ VARCHAR (should be FK) | ON DELETE SET NULL |
| **spk.kontrak_id** | kontrak.id | ❌ NOT FOUND | ON DELETE SET NULL |
| quotations.created_customer_id | customers.id | ✅ EXISTS | - |
| customer_locations.customer_id | customers.id | ✅ EXISTS (CASCADE) | - |

**Critical Impact:**
- Can delete customer_location while contracts still reference it (orphaned records)
- Marketing assignments use text name instead of user ID (no referential integrity)
- SPK can reference deleted contracts

#### 1.5 Existing FK Constraints (Review Needed)

**CASCADE DELETE Risks:**

| Constraint | Rule | Risk Level | Assessment |
|------------|------|------------|------------|
| customer_locations → customers | ON DELETE CASCADE | MEDIUM | May want RESTRICT if contracts exist |
| contract_amendments → kontrak | ON DELETE CASCADE | HIGH | Removes audit trail - should be RESTRICT |
| contract_po_history → kontrak | ON DELETE CASCADE | HIGH | Loses history - should be RESTRICT |
| delivery_items → delivery_instructions | ON DELETE CASCADE | MEDIUM | Acceptable for line items |

**Recommendation:** Review audit trail table cascades, change to RESTRICT for historical data preservation

---

### 2. Backend Code Issues

#### 2.1 God Controllers

**Marketing.php**
- **Lines:** 9,213
- **Methods:** 100+ (estimated)
- **Responsibilities:** Quotations, Contracts, SPK, DI, Exports, Dashboard
- **Issues:**
  - violates Single Responsibility Principle
  - Difficult to test (no unit test isolation)
  - Merge conflicts in team development
  - Hard to onboard new developers

**Active Routes Confirmed:** 80+ routes map to Marketing controller methods

**CustomerManagementController.php**
- **Lines:** 2,123
- **Methods:** 42
- **Responsibilities:** Customers, Customer Locations, Customer Reports
- **Issues:** Mixed customer + location CRUD, direct DB queries at lines 288-340

#### 2.2 Missing Service Layer

**Current Architecture:**
```
View → Controller → Model → Database
        ↓
    (Business logic here - BAD)
```

**Needed Architecture:**
```
View → Controller → Service → Model → Database
                      ↓
               (Business logic - GOOD)
```

**Missing Services:**
- CustomerService (customer creation, prospect conversion, marketing assignment)
- QuotationService (workflow stage management, conversion to deal, PDF generation)
- ContractService (billing calculation, renewal workflow, PO generation)
- SpkService (SPK creation from quotation/contract, status updates)
- DeliveryInstructionService (multi-stage approval workflow)
- InventoryService (unit assignment via junction table, swap logic, release)

#### 2.3 Direct Database Queries in Controllers

**Examples Found:**
- [CustomerManagementController.php lines 288-340](app/Controllers/CustomerManagementController.php#L288-L340) - Raw SQL queries
- Marketing.php - Multiple raw SQL queries for DataTables

**Issues:**
- No parameter binding (SQL injection risk)
- Hard to test
- Duplicated query patterns
- Bypasses model validation

#### 2.4 Code Duplication Patterns

**DataTables Initialization:**
- Same pattern repeated in 5+ view files
- Should extract to reusable config

**Query Builder Joins:**
```php
// Found in 5+ places:
$builder->join('customer_locations cl', ...)
       ->join('customers c', 'cl.customer_id = c.id')
       ->join('areas a', 'cl.area_id = a.id')
```
Should extract to model relationships or repository methods

**Permission Checks:**
```php
if (!$this->hasPermission('...')) { ... }
```
Repeated in every controller method - should use middleware/filter

#### 2.5 Dead Code Analysis

**Marketing.php (9,213 lines):**
- ✅ No TODO/FIXME comments found
- ✅ Minimal large commented blocks
- ✅ No obvious deprecated methods with annotations
- ⚠️ May have unused private methods (need deeper analysis)

**Conclusion:** Controllers are actively maintained, minimal dead code, but need architectural refactoring (splitting) rather than just cleanup

---

### 3. Frontend Issues

#### 3.1 Form Duplication

**Pattern:** Customer/Quotation/Contract forms duplicated 3x each (create/edit/view)

**Example:** Customer form fields repeated in:
- customer_create.php (~200 lines)
- customer_edit.php (~200 lines)
- customer_view.php (~200 lines)

**Total Duplication:** ~800 lines across all forms

**Solution:** Create partial views with `$mode` parameter

#### 3.2 JavaScript Duplication

**Issues:**
- Inline scripts in every view file
- No module system
- Repeated AJAX patterns
- DataTables initialization code duplicated 5+ times
- Global variable pollution

**Solution:** Extract to modules (quotation.js, customer.js, contract.js), centralized AJAX helper

#### 3.3 UI Inconsistencies

- Mix of inline styles and CSS classes
- Inconsistent button colors for same actions
- Different form layouts for same entity types
- No standard spacing/padding conventions

**Solution:** Create custom.css with standardized classes, cleanup inline styles

---

## COMPLETE IMPLEMENTATION PLAN

### PHASE 1A: Critical Data Integrity Fixes (Week 1-2)

Priority: **CRITICAL**  
Estimated Effort: **80 hours (10 working days)**

#### Step 1A.1: Remove inventory_unit Redundant FK Fields

**Objective:** Eliminate data redundancy in inventory_unit table (kontrak_id, customer_id, customer_location_id)

**Tasks:**

- [ ] **1.1.1 Data Consistency Audit** (4 hours)
  
  Create and run audit SQL:
  ```sql
  -- File: databases/migrations/audit_inventory_unit_redundancy.sql
  
  -- Check for mismatches between inventory_unit and kontrak_unit
  SELECT 
      iu.id_inventory_unit,
      iu.no_unit,
      iu.kontrak_id as unit_table_kontrak,
      ku.kontrak_id as junction_table_kontrak,
      iu.customer_id as unit_table_customer,
      cl.customer_id as derived_customer,
      CASE 
          WHEN iu.kontrak_id IS NULL AND ku.kontrak_id IS NULL THEN 'OK: Both NULL'
          WHEN iu.kontrak_id = ku.kontrak_id THEN 'OK: Match'
          ELSE 'MISMATCH'
      END as status
  FROM inventory_unit iu
  LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id 
      AND ku.status IN ('AKTIF', 'DIPERPANJANG', 'MENUNGGU_PERSETUJUAN')
  LEFT JOIN kontrak k ON ku.kontrak_id = k.id
  LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
  WHERE iu.kontrak_id IS NOT NULL OR ku.kontrak_id IS NOT NULL
  ORDER BY status DESC, iu.id_inventory_unit;
  
  -- Summary statistics
  SELECT 
      COUNT(*) as total_units,
      SUM(CASE WHEN iu.kontrak_id IS NOT NULL THEN 1 ELSE 0 END) as units_with_kontrak_id,
      SUM(CASE WHEN ku.kontrak_id IS NOT NULL THEN 1 ELSE 0 END) as units_in_junction,
      SUM(CASE WHEN iu.kontrak_id != ku.kontrak_id THEN 1 ELSE 0 END) as mismatched
  FROM inventory_unit iu
  LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id;
  ```
  
  **Success Criteria:** Document all mismatches, create reconciliation plan

- [ ] **1.1.2 Create Database VIEW for Backward Compatibility** (4 hours)
  
  ```sql
  -- File: databases/migrations/create_vw_unit_with_contracts.sql
  
  CREATE OR REPLACE VIEW vw_unit_with_contracts AS
  SELECT 
      iu.*,
      -- Derived from kontrak_unit junction (source of truth)
      ku.kontrak_id,
      ku.assignment_start_date,
      ku.assignment_end_date,
      ku.status as assignment_status,
      -- Derived from kontrak
      k.customer_location_id,
      k.start_date as contract_start_date,
      k.end_date as contract_end_date,
      k.rental_type,
      -- Derived from customer_locations
      cl.customer_id,
      cl.location_name,
      cl.address as location_address,
      -- Derived from customers
      c.customer_name,
      c.customer_code
  FROM inventory_unit iu
  LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id 
      AND ku.status IN ('AKTIF', 'DIPERPANJANG', 'MENUNGGU_PERSETUJUAN')
      AND ku.is_temporary = 0  -- Exclude temporary replacements
  LEFT JOIN kontrak k ON ku.kontrak_id = k.id
  LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
  LEFT JOIN customers c ON cl.customer_id = c.id;
  
  -- Create index on underlying table for performance
  CREATE INDEX IF NOT EXISTS idx_kontrak_unit_active 
  ON kontrak_unit(unit_id, status, is_temporary);
  ```
  
  **Success Criteria:** VIEW returns correct data, performance test with 1000+ units

- [ ] **1.1.3 Update InventoryUnitModel** (6 hours)
  
  **File:** [app/Models/InventoryUnitModel.php](app/Models/InventoryUnitModel.php)
  
  Changes:
  ```php
  // Remove from $allowedFields:
  // 'kontrak_id',
  // 'customer_id', 
  // 'customer_location_id',
  
  // Add deprecation notice
  protected $deprecatedFields = [
      'kontrak_id' => 'Use getWithContractInfo() method or vw_unit_with_contracts view',
      'customer_id' => 'Derive via kontrak → customer_locations',
      'customer_location_id' => 'Derive via kontrak',
  ];
  
  // Add new method
  public function getWithContractInfo(array $filters = []) {
      $builder = $this->db->table('vw_unit_with_contracts');
      
      // Apply filters
      if (!empty($filters['unit_id'])) {
          $builder->where('id_inventory_unit', $filters['unit_id']);
      }
      if (!empty($filters['customer_id'])) {
          $builder->where('customer_id', $filters['customer_id']);
      }
      if (!empty($filters['kontrak_id'])) {
          $builder->where('kontrak_id', $filters['kontrak_id']);
      }
      
      return $builder->get()->getResult();
  }
  
  // Add method to get unit's current contract
  public function getCurrentContract(int $unitId) {
      return $this->db->table('kontrak_unit')
          ->select('kontrak_unit.*, kontrak.*')
          ->join('kontrak', 'kontrak_unit.kontrak_id = kontrak.id')
          ->where('kontrak_unit.unit_id', $unitId)
          ->whereIn('kontrak_unit.status', ['AKTIF', 'DIPERPANJANG'])
          ->where('kontrak_unit.is_temporary', 0)
          ->get()
          ->getRow();
  }
  ```
  
  **Success Criteria:** Model compiles, new methods tested

- [ ] **1.1.4 Update Marketing.php Queries** (24 hours)
  
  **File:** [app/Controllers/Marketing.php](app/Controllers/Marketing.php)
  
  **Pattern to Find & Replace:**
  
  Find:
  ```php
  inventory_unit.kontrak_id
  iu.kontrak_id
  ->where('inventory_unit.customer_id', ...)
  ```
  
  Replace with:
  ```php
  // Option 1: Use VIEW
  vw_unit_with_contracts.kontrak_id
  vuc.kontrak_id
  
  // Option 2: JOIN to kontrak_unit
  ->join('kontrak_unit ku', 'iu.id_inventory_unit = ku.unit_id AND ku.status IN ("AKTIF","DIPERPANJANG")')
  ->where('ku.kontrak_id', ...)
  ```
  
  **Estimated References:** 20-30 queries
  
  **Testing:** After each update, test related functionality (SPK creation, DI, unit reports)

- [ ] **1.1.5 Update CustomerManagementController.php** (8 hours)
  
  **File:** [app/Controllers/CustomerManagementController.php](app/Controllers/CustomerManagementController.php)
  
  Similar updates for customer-related unit queries

- [ ] **1.1.6 Staging Deployment & Testing** (16 hours / 2 days)
  
  **Test Cases:**
  - [ ] Create new contract with units (verify kontrak_unit populated correctly)
  - [ ] View unit details (verify derived kontrak_id displays)
  - [ ] Filter units by customer (verify query performance)
  - [ ] SPK creation workflow (end-to-end test)
  - [ ] DI creation and approval (end-to-end test)
  - [ ] Unit swap functionality
  - [ ] Contract renewal
  - [ ] Reports showing unit assignments
  
  **Performance Benchmark:**
  ```sql
  EXPLAIN SELECT * FROM vw_unit_with_contracts WHERE customer_id = 123;
  EXPLAIN SELECT * FROM inventory_unit iu 
      JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id 
      WHERE ku.kontrak_id = 456;
  ```
  
  **Success Criteria:** All tests pass, performance acceptable (< 100ms per query)

- [ ] **1.1.7 Production Deployment (DROP Redundant Columns)** (4 hours)
  
  **Prerequisites:**
  - ✅ All code updated and tested in staging
  - ✅ Performance validated
  - ✅ Backup created
  
  ```sql
  -- File: databases/migrations/drop_inventory_unit_redundant_fks.sql
  
  -- BACKUP FIRST!
  -- mysqldump -u root -p optima_ci > backup_before_drop_redundant_fks_$(date +%Y%m%d_%H%M%S).sql
  
  ALTER TABLE inventory_unit
  DROP COLUMN kontrak_id,
  DROP COLUMN customer_id,
  DROP COLUMN customer_location_id;
  
  -- Verify
  SHOW COLUMNS FROM inventory_unit;
  ```
  
  **Rollback Plan:** Restore from backup if issues detected within 24 hours
  
  **Monitoring:** Check error logs for 48 hours post-deployment

**Risk Assessment:**
- **Probability:** Medium (extensive code changes)
- **Impact:** High (affects core inventory functionality)
- **Mitigation:** 
  - VIEW provides transition period
  - Comprehensive testing in staging
  - Rollback plan ready

**Deliverables:**
- ✅ Audit report (SQL results)
- ✅ Migration scripts (3 files)
- ✅ Updated models (InventoryUnitModel.php)
- ✅ Updated controllers (Marketing.php, CustomerManagementController.php)
- ✅ Test results documentation

---

#### Step 1A.2: Add Missing FK Constraints

**Objective:** Add critical foreign key constraints to prevent data integrity issues

**Tasks:**

- [ ] **1.2.1 Add FK: kontrak.customer_location_id → customer_locations.id** (4 hours)
  
  ```sql
  -- File: databases/migrations/add_fk_kontrak_customer_location.sql
  
  -- 1. Audit orphaned records
  SELECT k.id, k.no_kontrak, k.customer_location_id
  FROM kontrak k
  LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
  WHERE k.customer_location_id IS NOT NULL 
    AND cl.id IS NULL;
  
  -- 2. Clean orphaned records (if any)
  -- DECISION NEEDED: SET NULL or assign to default location?
  -- UPDATE kontrak SET customer_location_id = NULL WHERE id IN (...);
  
  -- 3. Add foreign key constraint
  ALTER TABLE kontrak
  ADD CONSTRAINT fk_kontrak_customer_location
  FOREIGN KEY (customer_location_id)
  REFERENCES customer_locations(id)
  ON DELETE RESTRICT
  ON UPDATE CASCADE;
  
  -- 4. Verify
  SHOW CREATE TABLE kontrak;
  ```
  
  **Business Rule:** ON DELETE RESTRICT - Cannot delete location if contracts exist (must reassign first)

- [ ] **1.2.2 Add FK: spk.kontrak_id → kontrak.id** (3 hours)
  
  ```sql
  -- File: databases/migrations/add_fk_spk_kontrak.sql
  
  -- 1. Audit
  SELECT s.id, s.nomor_spk, s.kontrak_id
  FROM spk s
  LEFT JOIN kontrak k ON s.kontrak_id = k.id
  WHERE s.kontrak_id IS NOT NULL 
    AND k.id IS NULL;
  
  -- 2. Add FK (allow NULL - some SPKs may be non-contract)
  ALTER TABLE spk
  ADD CONSTRAINT fk_spk_kontrak
  FOREIGN KEY (kontrak_id)
  REFERENCES kontrak(id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;
  ```
  
  **Business Rule:** ON DELETE SET NULL - If contract deleted, SPK remains but kontrak_id set to NULL

- [ ] **1.2.3 Convert customers.marketing_name to FK** (12 hours - COMPLEX)
  
  **Current:** `marketing_name VARCHAR(255)` stores user name as text  
  **Target:** `marketing_user_id INT` FK to users.id
  
  **Migration Steps:**
  
  ```sql
  -- File: databases/migrations/convert_marketing_name_to_fk.sql
  
  -- 1. Add new column
  ALTER TABLE customers
  ADD COLUMN marketing_user_id INT AFTER marketing_name,
  ADD INDEX idx_marketing_user (marketing_user_id);
  
  -- 2. Map existing names to user IDs
  UPDATE customers c
  LEFT JOIN users u ON c.marketing_name = u.name
  SET c.marketing_user_id = u.id
  WHERE c.marketing_name IS NOT NULL;
  
  -- 3. Report unmatched names
  SELECT DISTINCT c.marketing_name
  FROM customers c
  LEFT JOIN users u ON c.marketing_name = u.name
  WHERE c.marketing_name IS NOT NULL 
    AND u.id IS NULL;
  
  -- 4. Handle unmatched (BUSINESS DECISION REQUIRED)
  -- Option A: Create users for unmatched names
  -- Option B: Set to default/null
  -- Option C: Map to closest match
  
  -- 5. Add FK constraint
  ALTER TABLE customers
  ADD CONSTRAINT fk_customers_marketing_user
  FOREIGN KEY (marketing_user_id)
  REFERENCES users(id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;
  
  -- 6. Deprecate old column (keep for 1 month transition)
  ALTER TABLE customers 
  MODIFY marketing_name VARCHAR(255) 
  COMMENT 'DEPRECATED: Use marketing_user_id instead. Will be dropped after 2026-04-04';
  ```
  
  **Model Update:** [app/Models/CustomerModel.php](app/Models/CustomerModel.php)
  
  ```php
  protected $allowedFields = [
      // ... other fields
      'marketing_user_id',  // NEW
      // 'marketing_name',  // DEPRECATED
  ];
  
  // Add relationship method
  public function getWithMarketingPerson(int $customerId) {
      return $this->db->table('customers')
          ->select('customers.*, users.name as marketing_person_name, users.email as marketing_email')
          ->join('users', 'customers.marketing_user_id = users.id', 'left')
          ->where('customers.id', $customerId)
          ->get()
          ->getRow();
  }
  ```
  
  **Controller Update:** [app/Controllers/CustomerManagementController.php](app/Controllers/CustomerManagementController.php)
  
  - Update `storeCustomer()` - change field name
  - Update `updateCustomer()` - change field name
  - Update `getCustomers()` DataTables - JOIN to users table for display
  - Update customer forms - change to user dropdown

- [ ] **1.2.4 Review CASCADE Rules for Audit Tables** (4 hours)
  
  **Tables to Review:**
  - contract_amendments → kontrak (currently CASCADE)
  - contract_po_history → kontrak (currently CASCADE)
  - quotation_history → quotations (check current rule)
  
  **Recommended Changes:**
  ```sql
  -- File: databases/migrations/update_audit_table_cascade_rules.sql
  
  -- Drop existing FK and recreate with RESTRICT
  ALTER TABLE contract_amendments
  DROP FOREIGN KEY fk_amendments_contract;
  
  ALTER TABLE contract_amendments
  ADD CONSTRAINT fk_amendments_contract
  FOREIGN KEY (kontrak_id)
  REFERENCES kontrak(id)
  ON DELETE RESTRICT  -- Prevent contract deletion if amendments exist
  ON UPDATE CASCADE;
  
  -- Same for contract_po_history
  ALTER TABLE contract_po_history
  DROP FOREIGN KEY contract_po_history_ibfk_1;
  
  ALTER TABLE contract_po_history
  ADD CONSTRAINT fk_contract_po_history_kontrak
  FOREIGN KEY (kontrak_id)
  REFERENCES kontrak(id)
  ON DELETE RESTRICT  -- Preserve history
  ON UPDATE CASCADE;
  ```
  
  **Business Rule:** Audit trail data should prevent deletion (force soft delete instead)

- [ ] **1.2.5 Testing & Validation** (8 hours)
  
  **Test Cases:**
  - [ ] Try deleting customer_location with active contracts (should fail with FK error)
  - [ ] Try deleting contract with amendments (should fail)
  - [ ] Delete SPK's contract (SPK kontrak_id should become NULL)
  - [ ] Change customer's marketing person (verify FK update)
  - [ ] Create new customer with marketing assignment (verify FK insert)
  - [ ] Delete user who is marketing person (customer.marketing_user_id should become NULL)
  
  **Validation Queries:**
  ```sql
  -- Verify all FKs added
  SELECT 
      TABLE_NAME,
      COLUMN_NAME,
      CONSTRAINT_NAME,
      REFERENCED_TABLE_NAME,
      REFERENCED_COLUMN_NAME
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
  WHERE TABLE_SCHEMA = 'optima_ci'
    AND REFERENCED_TABLE_NAME IS NOT NULL
    AND TABLE_NAME IN ('kontrak', 'spk', 'customers', 'contract_amendments', 'contract_po_history')
  ORDER BY TABLE_NAME;
  ```

**Risk Assessment:**
- **Probability:** Low-Medium (straightforward FK additions)
- **Impact:** Medium (may prevent some delete operations)
- **Mitigation:**
  - Clear error messages to users
  - Documentation on how to handle FK constraint errors
  - Soft delete option for entities with dependencies

**Deliverables:**
- ✅ 5 migration SQL files
- ✅ Orphaned records audit report
- ✅ Updated CustomerModel.php
- ✅ Updated CustomerManagementController.php
- ✅ FK constraint verification report

**Total Phase 1A Effort:** 80 hours (10 days)

---

### PHASE 1B: Database Column Renaming (Week 3-5)

Priority: **HIGH**  
Estimated Effort: **120 hours (15 working days)**

**Strategy:** Zero-downtime migration using dual-column approach:
1. Add new English columns
2. Copy data old → new
3. Update code to use new columns
4. Test for 1-2 weeks (parallel run)
5. Drop old Indonesian columns

#### Step 1B.1: Rename kontrak Table Columns

**Objective:** Rename 7 Indonesian columns in core kontrak table

**Tasks:**

- [ ] **1.1.1 Create Migration SQL** (4 hours)
  
  ```sql
  -- File: databases/migrations/rename_kontrak_columns_phase1_add.sql
  
  -- Add new English columns
  ALTER TABLE kontrak
  ADD COLUMN billing_period_type ENUM('BULANAN','MINGGUAN','HARIAN') 
      AFTER rental_type
      COMMENT 'Replaces jenis_sewa',
  ADD COLUMN start_date DATE 
      AFTER tanggal_berakhir
      COMMENT 'Replaces tanggal_mulai',
  ADD COLUMN end_date DATE 
      AFTER start_date
      COMMENT 'Replaces tanggal_berakhir',
  ADD COLUMN created_by_user_id INT 
      AFTER dibuat_oleh
      COMMENT 'Replaces dibuat_oleh - FK to users.id',
  ADD COLUMN created_at TIMESTAMP 
      DEFAULT CURRENT_TIMESTAMP
      AFTER created_by_user_id
      COMMENT 'Replaces dibuat_pada',
  ADD COLUMN updated_at TIMESTAMP 
      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      AFTER created_at
      COMMENT 'Replaces diperbarui_pada';
  
  -- Note: nilai_total → total_contract_value rename handled separately
  
  -- Copy data from old to new columns
  UPDATE kontrak SET
      billing_period_type = jenis_sewa,
      start_date = tanggal_mulai,
      end_date = tanggal_berakhir,
      created_by_user_id = dibuat_oleh,
      created_at = dibuat_pada,
      updated_at = diperbarui_pada;
  
  -- Verify data copied correctly
  SELECT 
      COUNT(*) as total,
      SUM(CASE WHEN billing_period_type = jenis_sewa THEN 1 ELSE 0 END) as billing_match,
      SUM(CASE WHEN start_date = tanggal_mulai THEN 1 ELSE 0 END) as start_match,
      SUM(CASE WHEN end_date = tanggal_berakhir THEN 1 ELSE 0 END) as end_match
  FROM kontrak;
  
  -- Add indexes on new columns
  CREATE INDEX idx_kontrak_dates ON kontrak(start_date, end_date);
  CREATE INDEX idx_kontrak_created ON kontrak(created_by_user_id, created_at);
  ```

- [ ] **1.1.2 Update KontrakModel.php** (6 hours)
  
  **File:** [app/Models/KontrakModel.php](app/Models/KontrakModel.php)
  
  ```php
  protected $table = 'kontrak';
  protected $primaryKey = 'id';
  
  protected $useTimestamps = true;
  protected $createdField  = 'created_at';  // Changed from dibuat_pada
  protected $updatedField  = 'updated_at';  // Changed from diperbarui_pada
  
  protected $allowedFields = [
      'no_kontrak',
      'rental_type',
      'billing_period_type',  // NEW (replaces jenis_sewa)
      'customer_id',
      'customer_location_id',
      'customer_po_number',
      'start_date',  // NEW (replaces tanggal_mulai)
      'end_date',    // NEW (replaces tanggal_berakhir)
      'status',
      'total_contract_value',  // Renamed from nilai_total
      // ... other fields
      'created_by_user_id',  // NEW (replaces dibuat_oleh)
      
      // DEPRECATED - Remove after migration complete (2026-05-04)
      // 'jenis_sewa',
      // 'tanggal_mulai',
      // 'tanggal_berakhir',
      // 'dibuat_oleh',
  ];
  
  // Update callbacks to use new field names
  protected function beforeInsert(array $data) {
      // Update field references to new names
      if (isset($data['data']['created_by_user_id'])) {
          // Logic using new field name
      }
      return $data;
  }
  ```

- [ ] **1.1.3 Update Marketing.php References** (24 hours - EXTENSIVE)
  
  **File:** [app/Controllers/Marketing.php](app/Controllers/Marketing.php)
  
  **Search & Replace Patterns:**
  
  | Find | Replace | Estimated Count |
  |------|---------|-----------------|
  | `jenis_sewa` | `billing_period_type` | 10-15 |
  | `tanggal_mulai` | `start_date` | 20-30 |
  | `tanggal_berakhir` | `end_date` | 20-30 |
  | `dibuat_oleh` | `created_by_user_id` | 5-10 |
  | `dibuat_pada` | `created_at` | 5-10 |
  | `diperbarui_pada` | `updated_at` | 5-10 |
  
  **Critical Areas:**
  - kontrak() method - DataTables query
  - getDataTable() method
  - Contract creation/update methods
  - Contract renewal workflow
  - Filtering by date ranges
  - Reports and exports
  
  **Testing After Each Update:** Unit test for related functionality

- [ ] **1.1.4 Update Views** (8 hours)
  
  **Files:** [app/Views/marketing/](app/Views/marketing/)
  - kontrak.php
  - kontrak_detail.php
  - kontrak_edit.php
  - kontrak_create.php
  
  **Updates:**
  - Form field names: `tanggal_mulai` → `start_date`
  - JavaScript variable names
  - Display labels (can keep Indonesian labels, just update field names)

- [ ] **1.1.5 Staging Testing** (16 hours / 2 days)
  
  **Test Cases:**
  - [ ] Create new contract (verify new fields populated)
  - [ ] Edit existing contract (verify data integrity)
  - [ ] View contract details (verify display)
  - [ ] Filter contracts by date range
  - [ ] Contract renewal workflow
  - [ ] Contract reports/exports (Excel, PDF)
  - [ ] Billing calculations using billing_period_type
  - [ ] DataTables sorting by dates
  
  **Parallel Validation:**
  ```sql
  -- Verify old and new columns match during transition
  SELECT 
      id,
      jenis_sewa,
      billing_period_type,
      CASE WHEN jenis_sewa = billing_period_type THEN 'OK' ELSE 'MISMATCH' END as check1,
      tanggal_mulai,
      start_date,
      CASE WHEN tanggal_mulai = start_date THEN 'OK' ELSE 'MISMATCH' END as check2
  FROM kontrak
  WHERE jenis_sewa != billing_period_type 
     OR tanggal_mulai != start_date;
  ```

- [ ] **1.1.6 Production Deployment & Monitoring** (4 hours)
  
  - Deploy code changes
  - Monitor error logs for 48 hours
  - Check for any references to old field names in error messages

- [ ] **1.1.7 Drop Old Columns** (2 hours - AFTER 2 weeks parallel run)
  
  ```sql
  -- File: databases/migrations/rename_kontrak_columns_phase2_drop.sql
  
  -- ONLY run after 2 weeks of successful parallel operation
  
  -- Final verification
  SELECT COUNT(*) FROM kontrak WHERE 
      billing_period_type IS NULL AND jenis_sewa IS NOT NULL;
  
  -- Drop old columns
  ALTER TABLE kontrak
  DROP COLUMN jenis_sewa,
  DROP COLUMN tanggal_mulai,
  DROP COLUMN tanggal_berakhir,
  DROP COLUMN dibuat_oleh,
  DROP COLUMN dibuat_pada,
  DROP COLUMN diperbarui_pada;
  
  -- Verify
  SHOW COLUMNS FROM kontrak;
  ```

**Effort:** 64 hours (8 days)

---

#### Step 1B.2: Rename kontrak_unit Table Columns

**Objective:** Rename 5 Indonesian columns

**Tasks:**

- [ ] **1.2.1 Add New Columns & Copy Data** (4 hours)
  
  ```sql
  -- File: databases/migrations/rename_kontrak_unit_columns.sql
  
  ALTER TABLE kontrak_unit
  ADD COLUMN assignment_start_date DATE AFTER tanggal_selesai,
  ADD COLUMN assignment_end_date DATE AFTER assignment_start_date,
  ADD COLUMN withdrawal_date DATE AFTER tanggal_tukar,
  ADD COLUMN withdrawal_stage_code VARCHAR(50) AFTER withdrawal_date,
  ADD COLUMN swap_date DATE AFTER withdrawal_stage_code;
  
  UPDATE kontrak_unit SET
      assignment_start_date = tanggal_mulai,
      assignment_end_date = tanggal_selesai,
      withdrawal_date = tanggal_tarik,
      withdrawal_stage_code = stage_tarik,
      swap_date = tanggal_tukar;
  ```

- [ ] **1.2.2 Update Model (Create if not exists)** (4 hours)
  
  Create [app/Models/KontrakUnitModel.php](app/Models/KontrakUnitModel.php) if it doesn't exist

- [ ] **1.2.3 Update Controller References** (8 hours)
  
  Search Marketing.php for unit swap/withdrawal logic

- [ ] **1.2.4 Testing & Cleanup** (8 hours)

**Effort:** 24 hours (3 days)

---

#### Step 1B.3: Rename spk Table Columns

**Objective:** Rename 10 Indonesian columns (HIGH PRIORITY - daily usage)

**Tasks:**

- [ ] **1.3.1 Add New Columns** (4 hours)
  
  ```sql
  -- File: databases/migrations/rename_spk_columns.sql
  
  ALTER TABLE spk
  ADD COLUMN spk_number VARCHAR(50) AFTER nomor_spk,
  ADD COLUMN spk_type ENUM('UNIT','ATTACHMENT') AFTER jenis_spk,
  ADD COLUMN unit_quantity INT AFTER jumlah_unit,
  ADD COLUMN contract_po_number VARCHAR(100) AFTER po_kontrak_nomor,
  ADD COLUMN customer_name_snapshot VARCHAR(255) AFTER pelanggan 
      COMMENT 'Intentional denormalization for audit trail',
  ADD COLUMN delivery_location TEXT AFTER lokasi,
  ADD COLUMN notes TEXT AFTER catatan,
  ADD COLUMN created_by_user_id INT AFTER dibuat_oleh,
  ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER created_by_user_id,
  ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
  
  UPDATE spk SET
      spk_number = nomor_spk,
      spk_type = jenis_spk,
      unit_quantity = jumlah_unit,
      contract_po_number = po_kontrak_nomor,
      customer_name_snapshot = pelanggan,
      delivery_location = lokasi,
      notes = catatan,
      created_by_user_id = dibuat_oleh,
      created_at = dibuat_pada,
      updated_at = diperbarui_pada;
  ```

- [ ] **1.3.2 Create SpkModel** (6 hours)
  
  Create [app/Models/SpkModel.php](app/Models/SpkModel.php) - extract from Marketing.php

- [ ] **1.3.3 Update Marketing.php SPK Methods** (16 hours - 50+ references)
  
  Critical methods:
  - spk()
  - spkCreate()
  - createSPKFromQuotation()
  - spkDelete()
  - All SPK-related AJAX endpoints

- [ ] **1.3.4 Update Views** (8 hours)
  
  Files: spk.php, spk_create.php, spk_detail.php

- [ ] **1.3.5 Testing** (12 hours)
  
  SPK workflow critical for operations - comprehensive testing needed

**Effort:** 46 hours (6 days)

---

#### Step 1B.4: Rename delivery_instructions Table Columns

**Objective:** Rename 21 Indonesian columns (COMPLEX - has triggers)

**Tasks:**

- [ ] **1.4.1 Document Existing Triggers** (4 hours)
  
  ```sql
  -- Find all triggers on delivery_instructions table
  SHOW TRIGGERS WHERE `Table` = 'delivery_instructions';
  
  -- Export trigger definitions
  SHOW CREATE TRIGGER trigger_name;
  ```

- [ ] **1.4.2 Add New Columns** (6 hours)
  
  ```sql
  -- File: databases/migrations/rename_delivery_instructions_columns.sql
  
  ALTER TABLE delivery_instructions
  ADD COLUMN di_number VARCHAR(50) AFTER nomor_di,
  ADD COLUMN source_spk_type VARCHAR(50) AFTER jenis_spk,
  ADD COLUMN contract_po_number VARCHAR(100) AFTER po_kontrak_nomor,
  ADD COLUMN customer_name_snapshot VARCHAR(255) AFTER pelanggan,
  ADD COLUMN delivery_address TEXT AFTER lokasi,
  ADD COLUMN delivery_date DATE AFTER tanggal_kirim,
  ADD COLUMN notes TEXT AFTER catatan,
  ADD COLUMN created_by_user_id INT AFTER dibuat_oleh,
  ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN planning_approved_date DATETIME AFTER perencanaan_tanggal_approve,
  ADD COLUMN estimated_arrival_date DATE AFTER estimasi_sampai,
  ADD COLUMN driver_name VARCHAR(255) AFTER nama_supir,
  ADD COLUMN driver_phone VARCHAR(20) AFTER no_hp_supir,
  ADD COLUMN driver_license_number VARCHAR(50) AFTER no_sim_supir,
  ADD COLUMN vehicle_type VARCHAR(100) AFTER kendaraan,
  ADD COLUMN vehicle_plate_number VARCHAR(20) AFTER no_polisi_kendaraan,
  ADD COLUMN departure_approved_date DATETIME AFTER berangkat_tanggal_approve,
  ADD COLUMN departure_notes TEXT AFTER catatan_berangkat,
  ADD COLUMN arrival_approved_date DATETIME AFTER sampai_tanggal_approve,
  ADD COLUMN arrival_notes TEXT AFTER catatan_sampai;
  
  -- Copy data
  UPDATE delivery_instructions SET
      di_number = nomor_di,
      source_spk_type = jenis_spk,
      contract_po_number = po_kontrak_nomor,
      customer_name_snapshot = pelanggan,
      delivery_address = lokasi,
      delivery_date = tanggal_kirim,
      notes = catatan,
      created_by_user_id = dibuat_oleh,
      created_at = dibuat_pada,
      updated_at = diperbarui_pada,
      planning_approved_date = perencanaan_tanggal_approve,
      estimated_arrival_date = estimasi_sampai,
      driver_name = nama_supir,
      driver_phone = no_hp_supir,
      driver_license_number = no_sim_supir,
      vehicle_type = kendaraan,
      vehicle_plate_number = no_polisi_kendaraan,
      departure_approved_date = berangkat_tanggal_approve,
      departure_notes = catatan_berangkat,
      arrival_approved_date = sampai_tanggal_approve,
      arrival_notes = catatan_sampai;
  ```

- [ ] **1.4.3 Update Triggers** (8 hours - CRITICAL)
  
  Find all triggers referencing old column names and update to new names
  
  ```sql
  -- Backup triggers first
  -- Then DROP and recreate with new column names
  
  DROP TRIGGER IF EXISTS trigger_update_inventory_unit_delivery_date;
  
  CREATE TRIGGER trigger_update_inventory_unit_delivery_date
  AFTER UPDATE ON delivery_instructions
  FOR EACH ROW
  BEGIN
      -- Update inventory_unit.delivery_date when DI approved
      IF NEW.arrival_approved_date IS NOT NULL AND OLD.arrival_approved_date IS NULL THEN
          UPDATE inventory_unit
          SET delivery_date = NEW.delivery_date  -- NEW column name
          WHERE id_inventory_unit IN (
              SELECT unit_id FROM delivery_items WHERE di_id = NEW.id
          );
      END IF;
  END;
  ```

- [ ] **1.4.4 Create DeliveryInstructionModel** (6 hours)

- [ ] **1.4.5 Update Marketing.php DI Methods** (16 hours)
  
  Methods: diCreate(), approvePlanning(), approveDeparture(), approveArrival()

- [ ] **1.4.6 Update Views** (8 hours)

- [ ] **1.4.7 Testing DI Workflow** (16 hours)
  
  Complete workflow: Create DI → Approve Planning → Approve Departure → Approve Arrival
  Verify inventory_unit.delivery_date updated correctly

**Effort:** 64 hours (8 days)

---

#### Step 1B.5: Rename inventory_unit Columns

**Objective:** Rename 6 remaining Indonesian columns

**Tasks:**

- [ ] **1.5.1 Migration SQL** (4 hours)
- [ ] **1.5.2 Update InventoryUnitModel** (4 hours)
- [ ] **1.5.3 Update Controller References** (8 hours)
- [ ] **1.5.4 Testing** (8 hours)

**Effort:** 24 hours (3 days)

---

**Total Phase 1B Effort:** 222 hours (28 days actual, but can parallelize some tasks - estimate 15 days with focus)

---

### PHASE 2: Backend Refactoring (Week 6-10)

Priority: **MEDIUM**  
Estimated Effort: **200 hours (25 working days)**

#### Step 2.1: Create Service Layer

**Objective:** Extract business logic from controllers into dedicated service classes

**Architecture Pattern:**

```
Controller (thin - request/response handling)
    ↓
Service (business logic, validation, transactions)
    ↓
Model / Repository (data access)
    ↓
Database
```

**Tasks:**

- [ ] **2.1.1 Create Service Base Class** (4 hours)
  
  ```php
  // File: app/Services/BaseService.php
  
  namespace App\Services;
  
  use CodeIgniter\Database\ConnectionInterface;
  use App\Traits\ActivityLoggingTrait;
  
  abstract class BaseService
  {
      use ActivityLoggingTrait;
      
      protected $db;
      
      public function __construct(ConnectionInterface &$db = null)
      {
          $this->db = $db ?? \Config\Database::connect();
      }
      
      /**
       * Start database transaction
       */
      protected function beginTransaction()
      {
          $this->db->transStart();
      }
      
      /**
       * Commit transaction
       */
      protected function commit()
      {
          $this->db->transComplete();
          
          if ($this->db->transStatus() === false) {
              throw new \RuntimeException('Transaction failed');
          }
      }
      
      /**
       * Rollback transaction
       */
      protected function rollback()
      {
          $this->db->transRollback();
      }
      
      /**
       * Standard success response
       */
      protected function successResponse($data = null, string $message = 'Success')
      {
          return [
              'success' => true,
              'message' => $message,
              'data' => $data
          ];
      }
      
      /**
       * Standard error response
       */
      protected function errorResponse(string $message, $errors = null)
      {
          return [
              'success' => false,
              'message' => $message,
              'errors' => $errors
          ];
      }
  }
  ```

- [ ] **2.1.2 Create CustomerService** (16 hours)
  
  ```php
  // File: app/Services/CustomerService.php
  
  namespace App\Services;
  
  use App\Models\CustomerModel;
  use App\Models\CustomerLocationModel;
  use App\Models\ProspectModel;
  
  class CustomerService extends BaseService
  {
      protected $customerModel;
      protected $locationModel;
      protected $prospectModel;
      
      public function __construct()
      {
          parent::__construct();
          $this->customerModel = new CustomerModel();
          $this->locationModel = new CustomerLocationModel();
          $this->prospectModel = new ProspectModel();
      }
      
      /**
       * Create new customer with initial location
       */
      public function createCustomer(array $customerData, array $locationData = null)
      {
          $this->beginTransaction();
          
          try {
              // Validate customer data
              if (!$this->customerModel->validate($customerData)) {
                  return $this->errorResponse(
                      'Validation failed',
                      $this->customerModel->errors()
                  );
              }
              
              // Insert customer
              $customerId = $this->customerModel->insert($customerData);
              
              if (!$customerId) {
                  throw new \RuntimeException('Failed to create customer');
              }
              
              // Create primary location if provided
              if ($locationData) {
                  $locationData['customer_id'] = $customerId;
                  $locationData['is_primary'] = 1;
                  
                  $locationId = $this->locationModel->insert($locationData);
                  
                  if (!$locationId) {
                      throw new \RuntimeException('Failed to create location');
                  }
              }
              
              $this->commit();
              
              // Log activity
              $this->logActivity(
                  'customer',
                  $customerId,
                  'created',
                  "Customer created: {$customerData['customer_name']}"
              );
              
              return $this->successResponse(
                  ['customer_id' => $customerId],
                  'Customer created successfully'
              );
              
          } catch (\Exception $e) {
              $this->rollback();
              return $this->errorResponse($e->getMessage());
          }
      }
      
      /**
       * Convert prospect to customer
       */
      public function convertFromProspect(int $prospectId, array $additionalData = [])
      {
          $this->beginTransaction();
          
          try {
              // Get prospect data
              $prospect = $this->prospectModel->find($prospectId);
              
              if (!$prospect) {
                  return $this->errorResponse('Prospect not found');
              }
              
              // Map prospect fields to customer fields
              $customerData = [
                  'customer_name' => $prospect['prospect_name'],
                  'phone' => $prospect['prospect_phone'],
                  'email' => $prospect['prospect_email'],
                  'address' => $prospect['prospect_address'],
                  'is_active' => 1,
              ];
              
              // Merge with additional data
              $customerData = array_merge($customerData, $additionalData);
              
              // Create customer
              $result = $this->createCustomer($customerData);
              
              if (!$result['success']) {
                  throw new \RuntimeException($result['message']);
              }
              
              // Update prospect with customer_id
              $this->prospectModel->update($prospectId, [
                  'converted_to_customer_id' => $result['data']['customer_id'],
                  'converted_at' => date('Y-m-d H:i:s')
              ]);
              
              $this->commit();
              
              return $this->successResponse(
                  $result['data'],
                  'Prospect converted to customer successfully'
              );
              
          } catch (\Exception $e) {
              $this->rollback();
              return $this->errorResponse($e->getMessage());
          }
      }
      
      /**
       * Get customers with their locations and contracts
       */
      public function getCustomersWithDetails(array $filters = [])
      {
          $builder = $this->db->table('customers c');
          $builder->select('c.*, 
                           COUNT(DISTINCT cl.id) as location_count,
                           COUNT(DISTINCT k.id) as contract_count,
                           u.name as marketing_person_name');
          $builder->join('customer_locations cl', 'c.id = cl.customer_id', 'left');
          $builder->join('kontrak k', 'cl.id = k.customer_location_id', 'left');
          $builder->join('users u', 'c.marketing_user_id = u.id', 'left');
          
          // Apply filters
          if (!empty($filters['customer_name'])) {
              $builder->like('c.customer_name', $filters['customer_name']);
          }
          
          if (isset($filters['is_active'])) {
              $builder->where('c.is_active', $filters['is_active']);
          }
          
          if (!empty($filters['marketing_user_id'])) {
              $builder->where('c.marketing_user_id', $filters['marketing_user_id']);
          }
          
          $builder->groupBy('c.id');
          
          return $builder->get()->getResult();
      }
      
      /**
       * Assign marketing person to customer
       */
      public function assignMarketingPerson(int $customerId, int $userId)
      {
          // Verify user exists and has marketing role
          $user = $this->db->table('users')->where('id', $userId)->get()->getRow();
          
          if (!$user) {
              return $this->errorResponse('User not found');
          }
          
          // Update customer
          $updated = $this->customerModel->update($customerId, [
              'marketing_user_id' => $userId
          ]);
          
          if (!$updated) {
              return $this->errorResponse('Failed to assign marketing person');
          }
          
          $this->logActivity(
              'customer',
              $customerId,
              'marketing_assigned',
              "Marketing person assigned: {$user->name}"
          );
          
          return $this->successResponse(null, 'Marketing person assigned successfully');
      }
      
      /**
       * Get customer statistics
       */
      public function getCustomerStatistics(int $customerId)
      {
          $stats = $this->db->query("
              SELECT 
                  (SELECT COUNT(*) FROM customer_locations WHERE customer_id = ?) as total_locations,
                  (SELECT COUNT(*) FROM kontrak k 
                   JOIN customer_locations cl ON k.customer_location_id = cl.id 
                   WHERE cl.customer_id = ?) as total_contracts,
                  (SELECT COUNT(*) FROM kontrak k 
                   JOIN customer_locations cl ON k.customer_location_id = cl.id 
                   WHERE cl.customer_id = ? AND k.status = 'ACTIVE') as active_contracts,
                  (SELECT COUNT(DISTINCT ku.unit_id) 
                   FROM kontrak_unit ku
                   JOIN kontrak k ON ku.kontrak_id = k.id
                   JOIN customer_locations cl ON k.customer_location_id = cl.id
                   WHERE cl.customer_id = ? AND ku.status = 'AKTIF') as active_units
          ", [$customerId, $customerId, $customerId, $customerId])->getRow();
          
          return $this->successResponse($stats);
      }
  }
  ```

- [ ] **2.1.3 Create QuotationService** (20 hours)
  
  Methods:
  - createQuotation()
  - updateQuotationStage()
  - convertToDeal()
  - createRevision()
  - calculateTotals()
  - generatePDF()

- [ ] **2.1.4 Create ContractService** (20 hours)
  
  Methods:
  - createContractFromQuotation()
  - calculateContractValue()
  - renewContract()
  - generatePO()
  - updateBillingMethod()
  - assignUnitsToContract()

- [ ] **2.1.5 Create SpkService** (16 hours)
  
  Methods:
  - createSpkFromQuotation()
  - createSpkFromContract()
  - updateSpkStatus()
  - deleteSpk()

- [ ] **2.1.6 Create DeliveryInstructionService** (16 hours)
  
  Methods:
  - createDiFromSpk()
  - approvePlanningStage()
  - approveDepartureStage()
  - approveArrivalStage()

- [ ] **2.1.7 Create InventoryService** (16 hours)
  
  Methods:
  - assignUnitToContract() (uses kontrak_unit junction)
  - swapUnit()
  - releaseUnitFromContract()
  - updateUnitLocation()

- [ ] **2.1.8 Unit Tests for Services** (24 hours)
  
  Create PHPUnit tests for each service
  Target: 70%+ code coverage

**Effort:** 132 hours

---

#### Step 2.2: Split Marketing.php God Controller

**Objective:** Split 9,213-line controller into 5 focused controllers

**Tasks:**

- [ ] **2.2.1 Create QuotationController** (16 hours)
  
  ```php
  // File: app/Controllers/QuotationController.php
  
  namespace App\Controllers;
  
  use App\Services\QuotationService;
  use App\Models\QuotationModel;
  
  class QuotationController extends BaseController
  {
      protected $quotationService;
      protected $quotationModel;
      
      public function __construct()
      {
          $this->quotationService = new QuotationService();
          $this->quotationModel = new QuotationModel();
      }
      
      public function index()
      {
          // List quotations view
          return view('marketing/quotations');
      }
      
      public function getDataTable()
      {
          // DataTables AJAX endpoint
          // Move logic from Marketing.php
      }
      
      public function create()
      {
          return view('marketing/quotation_create');
      }
      
      public function store()
      {
          $data = $this->request->getPost();
          $result = $this->quotationService->createQuotation($data);
          
          return $this->response->setJSON($result);
      }
      
      // ... other methods extracted from Marketing.php
  }
  ```
  
  **Methods to extract from Marketing.php:**
  - quotations()
  - createQuotation()
  - storeQuotation()
  - getQuotation()
  - updateQuotation()
  - deleteQuotation()
  - convertToContract()
  - createRevision()
  - exportQuotations()

- [ ] **2.2.2 Create ContractController** (16 hours)
  
  Extract contract-related methods

- [ ] **2.2.3 Create SpkController** (16 hours)
  
  Extract SPK methods

- [ ] **2.2.4 Create DeliveryInstructionController** (12 hours)
  
  Extract DI methods

- [ ] **2.2.5 Create MarketingDashboardController** (8 hours)
  
  Keep only dashboard, statistics, charts

- [ ] **2.2.6 Update Routes** (8 hours)
  
  **File:** [app/Config/Routes.php](app/Config/Routes.php)
  
  ```php
  // Old routes (keep for backward compatibility - redirect)
  $routes->get('marketing/quotations', 'Marketing::quotations'); // Redirect to QuotationController
  
  // New routes
  $routes->group('quotations', ['namespace' => 'App\Controllers'], function($routes) {
      $routes->get('/', 'QuotationController::index');
      $routes->get('datatable', 'QuotationController::getDataTable');
      $routes->get('create', 'QuotationController::create');
      $routes->post('store', 'QuotationController::store');
      $routes->get('edit/(:num)', 'QuotationController::edit/$1');
      $routes->post('update/(:num)', 'QuotationController::update/$1');
      $routes->post('convert-to-deal/(:num)', 'QuotationController::convertToDeal/$1');
  });
  
  // Similar for contracts, spk, di
  ```
  
  **Add redirects in Marketing.php:**
  ```php
  public function quotations()
  {
      return redirect()->to('quotations');
  }
  ```

- [ ] **2.2.7 Update View Form Actions** (8 hours)
  
  Update all view files to point to new controller routes

- [ ] **2.2.8 Testing** (20 hours)
  
  Comprehensive regression testing for all moved functionality

**Effort:** 104 hours

---

#### Step 2.3: Refactor CustomerManagementController

**Objective:** Extract location management, reduce to < 800 lines

**Tasks:**

- [ ] **2.3.1 Create CustomerLocationController** (12 hours)
- [ ] **2.3.2 Update CustomerManagementController** (12 hours)
  - Inject CustomerService
  - Remove direct DB queries
  - Use service methods
- [ ] **2.3.3 Testing** (8 hours)

**Effort:** 32 hours

---

#### Step 2.4: Centralize Validation Rules

**Tasks:**

- [ ] **2.4.1 Create QuotationRules** (4 hours)
- [ ] **2.4.2 Create CustomerRules** (4 hours)
- [ ] **2.4.3 Create ContractRules** (4 hours)
- [ ] **2.4.4 Create SpkRules** (4 hours)
- [ ] **2.4.5 Update Controllers** (8 hours)

**Effort:** 24 hours

---

#### Step 2.5: Replace Direct SQL with Query Builder

**Tasks:**

- [ ] **2.5.1 Identify All Raw SQL** (4 hours)
- [ ] **2.5.2 Refactor to Query Builder** (16 hours)
- [ ] **2.5.3 Security Audit** (4 hours)

**Effort:** 24 hours

---

**Total Phase 2 Effort:** 316 hours (40 days actual, estimate 25 days with focus)

---

### PHASE 3: Frontend Consolidation (Week 11-13)

Priority: **LOW**  
Estimated Effort: **96 hours (12 working days)**

#### Step 3.1: Create Reusable Partial Views

**Tasks:**

- [ ] **3.1.1 Create _customer_form_fields.php** (8 hours)
- [ ] **3.1.2 Create _quotation_form_fields.php** (8 hours)
- [ ] **3.1.3 Create _contract_form_fields.php** (8 hours)
- [ ] **3.1.4 Create _location_form_fields.php** (6 hours)
- [ ] **3.1.5 Update Existing Views** (12 hours)

**Effort:** 42 hours

---

#### Step 3.2: Modularize JavaScript

**Tasks:**

- [ ] **3.2.1 Create quotation.js module** (8 hours)
- [ ] **3.2.2 Create customer.js module** (6 hours)
- [ ] **3.2.3 Create contract.js module** (8 hours)
- [ ] **3.2.4 Create spk.js module** (6 hours)
- [ ] **3.2.5 Create ajax-helper.js** (4 hours)
- [ ] **3.2.6 Remove Inline Scripts** (8 hours)

**Effort:** 40 hours

---

#### Step 3.3: Standardize UI

**Tasks:**

- [ ] **3.3.1 Create custom-marketing.css** (4 hours)
- [ ] **3.3.2 Audit & Cleanup Inline Styles** (6 hours)
- [ ] **3.3.3 Standardize DataTables Config** (4 hours)

**Effort:** 14 hours

---

**Total Phase 3 Effort:** 96 hours (12 days)

---

## TESTING STRATEGY

### Unit Testing

**Framework:** PHPUnit

**Coverage Target:** 70%+

**Test Suites:**

1. **Service Layer Tests** (app/Tests/Services/)
   - CustomerServiceTest.php
   - QuotationServiceTest.php
   - ContractServiceTest.php
   - SpkServiceTest.php
   - DeliveryInstructionServiceTest.php
   - InventoryServiceTest.php

2. **Model Tests** (app/Tests/Models/)
   - Test relationships
   - Test validation rules
   - Test callbacks

3. **Validation Tests** (app/Tests/Validation/)
   - Test all validation rule classes

**Sample Test:**
```php
// File: app/Tests/Services/CustomerServiceTest.php

namespace Tests\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\CustomerService;

class CustomerServiceTest extends CIUnitTestCase
{
    protected $customerService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->customerService = new CustomerService();
    }
    
    public function testCreateCustomerSuccess()
    {
        $customerData = [
            'customer_name' => 'Test Customer',
            'phone' => '081234567890',
            'email' => 'test@example.com',
            'is_active' => 1
        ];
        
        $result = $this->customerService->createCustomer($customerData);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('customer_id', $result['data']);
    }
    
    public function testCreateCustomerValidationFail()
    {
        $customerData = [
            'customer_name' => '', // Empty - should fail
        ];
        
        $result = $this->customerService->createCustomer($customerData);
        
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }
}
```

### Integration Testing

**Test Scenarios:**

1. **Quotation to Contract Flow**
   - [ ] Create quotation
   - [ ] Update stage to "Deal"
   - [ ] Convert to contract
   - [ ] Verify contract created with correct data
   - [ ] Verify quotation status updated

2. **Contract to SPK to DI Flow**
   - [ ] Create contract
   - [ ] Create SPK from contract
   - [ ] Create DI from SPK
   - [ ] Approve DI stages (planning → departure → arrival)
   - [ ] Verify inventory_unit.delivery_date updated

3. **Unit Assignment Flow**
   - [ ] Assign unit to contract (via kontrak_unit junction)
   - [ ] Verify inventory_unit NOT storing kontrak_id directly
   - [ ] Query unit's current contract via junction
   - [ ] Release unit from contract
   - [ ] Swap unit with replacement

4. **Customer Conversion Flow**
   - [ ] Create prospect
   - [ ] Convert to customer
   - [ ] Verify customer created
   - [ ] Verify prospect.converted_to_customer_id updated

### Regression Testing Checklist

**Critical Workflows:**

- [ ] User login and permissions
- [ ] Customer CRUD operations
- [ ] Customer location management
- [ ] Quotation creation and editing
- [ ] Quotation stage workflow
- [ ] Convert quotation to deal
- [ ] Create contract from quotation
- [ ] Contract renewal workflow
- [ ] SPK creation (from quotation and contract)
- [ ] DI creation and multi-stage approval
- [ ] Unit assignment/swap/release
- [ ] Invoicing calculations
- [ ] Reports (customer list, contract list, unit list)
- [ ] Excel exports
- [ ] PDF generation (quotations, contracts, SPK, DI)
- [ ] DataTables filtering and sorting
- [ ] Dashboard statistics

### Performance Testing

**Benchmarks:**

1. **Database Queries**
   ```sql
   -- Test view performance
   EXPLAIN SELECT * FROM vw_unit_with_contracts WHERE customer_id = 123;
   
   -- Should use index, avoid full table scan
   -- Acceptable: < 100ms for 10,000+ records
   ```

2. **DataTables Loading**
   - Target: < 2 seconds for 1000 records
   - Test with pagination (25, 50, 100 per page)

3. **PDF Generation**
   - Quotation PDF: < 5 seconds
   - Contract PDF: < 5 seconds
   - SPK PDF: < 3 seconds

4. **Excel Export**
   - 1000 records: < 10 seconds
   - 5000 records: < 30 seconds

### User Acceptance Testing

**Participants:** Marketing team, workshop team, IT admin

**UAT Scenarios:**

1. **Marketing Team**
   - [ ] Create quotation for new prospect
   - [ ] Update quotation through workflow stages
   - [ ] Convert quotation to deal and create contract
   - [ ] Generate quotation PDF
   - [ ] Filter and search quotations
   - [ ] Export quotation list to Excel

2. **Workshop Team**
   - [ ] Create SPK from contract
   - [ ] Create DI from SPK
   - [ ] Approve DI stages
   - [ ] Assign units to SPK
   - [ ] View unit assignment history

3. **Admin**
   - [ ] Manage customer master data
   - [ ] Assign marketing person to customer
   - [ ] View customer statistics
   - [ ] Generate reports
   - [ ] Manage user permissions

**UAT Exit Criteria:**
- 90%+ of test cases pass
- No critical bugs
- Performance acceptable
- User feedback positive

---

## DEPLOYMENT STRATEGY

### Staging Deployment

**Environment:** staging.optima.local

**Process:**

1. **Database Migration**
   ```bash
   # Backup staging database
   mysqldump -u root -p optima_staging > staging_backup_$(date +%Y%m%d).sql
   
   # Run migrations
   php spark migrate
   
   # Verify migrations
   php spark migrate:status
   ```

2. **Code Deployment**
   ```bash
   # Pull latest from git
   git checkout staging
   git pull origin main
   
   # Install dependencies
   composer install --no-dev
   
   # Clear cache
   php spark cache:clear
   ```

3. **Testing** (2 weeks minimum per phase)

4. **Sign-off** from QA and business stakeholders

### Production Rollout

**Schedule:** Deploy during off-peak hours (Saturday night, 22:00 - 02:00)

**Phases:**

#### Phase 1A Production Deployment (Week 2)

**Pre-Deployment:**
- [ ] Create full database backup
- [ ] Notify users of maintenance window
- [ ] Prepare rollback scripts
- [ ] Deploy to staging and final test

**Deployment Steps:**
1. Enable maintenance mode (22:00)
2. Backup database (22:10)
3. Run Phase 1A migrations (22:20)
4. Deploy code changes (22:30)
5. Run smoke tests (22:40)
6. Disable maintenance mode (23:00)
7. Monitor for 1 hour

**Post-Deployment:**
- [ ] Monitor error logs for 48 hours
- [ ] Check performance metrics
- [ ] Collect user feedback
- [ ] Hotfix if critical issues found

#### Phase 1B Production Deployment (Week 5)

Similar process, longer maintenance window (4 hours) due to column renames

#### Phase 2 Production Deployment (Week 10)

Deployment in 2 sub-phases:
- Week 10: Service layer + new controllers (old routes still work)
- Week 12: Switch routes to new controllers

#### Phase 3 Production Deployment (Week 13)

Frontend changes - low risk, can deploy incrementally

### Rollback Procedures

**Scenario 1: Database Migration Failed**

```bash
# Restore from backup
mysql -u root -p optima_ci < backup_before_migration.sql

# Revert code
git checkout previous_tag
```

**Scenario 2: Critical Bug Found Post-Deployment**

1. Enable maintenance mode
2. Revert database changes (restore backup)
3. Deploy previous code version
4. Disable maintenance mode
5. Investigate issue in staging

**Scenario 3: Performance Degradation**

1. Identify slow queries (check slow query log)
2. Add missing indexes if needed
3. Or rollback to previous version
4. Optimize and redeploy

### Monitoring Checklist

**First 24 Hours:**
- [ ] Check error_log every 2 hours
- [ ] Monitor database query performance
- [ ] Check user-reported issues
- [ ] Verify DataTables loading correctly
- [ ] Test critical workflows manually

**First Week:**
- [ ] Daily error log review
- [ ] Performance metrics (response times)
- [ ] User feedback collection
- [ ] Fix minor bugs if any

**Ongoing:**
- [ ] Weekly error log review
- [ ] Monthly performance review
- [ ] Quarterly code quality audit

---

## RISK ASSESSMENT

### Risk Matrix

| Risk | Probability | Impact | Severity | Mitigation | Owner |
|------|-------------|--------|----------|------------|-------|
| **Data loss during migration** | Low | Critical | HIGH | Daily backups, test in staging first, rollback plan | DevOps |
| **Breaking existing functionality** | Medium | High | HIGH | Comprehensive regression testing, parallel run period | Lead Dev |
| **Performance degradation** | Medium | Medium | MEDIUM | Performance benchmarks, query optimization, indexes | Lead Dev |
| **User resistance to change** | Medium | Low | LOW | Training sessions, documentation, gradual rollout | Project Manager |
| **Timeline overrun** | Medium | Medium | MEDIUM | Buffer time in estimate, prioritize critical items | Project Manager |
| **FK constraints block operations** | Medium | Medium | MEDIUM | Clear error messages, soft delete option, documentation | Lead Dev |
| **Trigger update failures** | Low | High | MEDIUM | Backup triggers, test in staging, manual verification | Lead Dev |
| **Team knowledge gap** | Low | Medium | LOW | Code documentation, pair programming, training | Lead Dev |

### Mitigation Strategies

**Data Integrity:**
- Daily automated backups (retained 30 days)
- Point-in-time recovery capability
- Staging environment mirrors production
- All migrations tested in staging 2+ weeks before production

**Code Quality:**
- Code reviews for all changes (2 reviewers minimum)
- Automated testing (PHPUnit)
- Static analysis (PHPStan level 5+)
- Linting (PHP CodeSniffer)

**Communication:**
- Weekly progress updates to stakeholders
- Daily standup for development team
- User training sessions before major releases
- Change log documentation

**Knowledge Transfer:**
- Inline code documentation (PHPDoc)
- Architecture decision records (ADR)
- Video tutorials for complex workflows
- Onboarding guide for new developers

---

## APPENDIX

### A. Complete Column Rename Mapping

See detailed tables in Phase 1B sections above (50+ columns documented)

### B. Foreign Key Cascade Rules Reference

| Rule | Description | When to Use |
|------|-------------|-------------|
| **RESTRICT** | Prevent deletion if references exist | Master data, audit tables |
| **CASCADE** | Delete dependent records automatically | Line items, purely dependent data |
| **SET NULL** | Set FK to NULL when parent deleted | Optional relationships |
| **NO ACTION** | Similar to RESTRICT (check at transaction end) | Default, rarely used explicitly |

### C. SQL Script Templates

**Migration Template:**
```sql
-- File: databases/migrations/YYYY_MM_DD_HHMMSS_description.sql

-- ===================================================
-- Migration: [Description]
-- Date: [Date]
-- Author: [Name]
-- ===================================================

-- BACKUP FIRST!
-- mysqldump -u root -p optima_ci > backup_before_[description]_$(date +%Y%m%d_%H%M%S).sql

-- ===================================================
-- 1. PRE-MIGRATION VALIDATION
-- ===================================================

-- Check for data issues
SELECT COUNT(*) FROM table_name WHERE condition;

-- ===================================================
-- 2. MIGRATION STEPS
-- ===================================================

ALTER TABLE table_name
ADD COLUMN new_column VARCHAR(255);

-- ===================================================
-- 3. DATA MIGRATION
-- ===================================================

UPDATE table_name SET new_column = old_column;

-- ===================================================
-- 4. VERIFICATION
-- ===================================================

SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN new_column = old_column THEN 1 ELSE 0 END) as match_count
FROM table_name;

-- ===================================================
-- 5. CLEANUP (RUN AFTER TESTING PERIOD)
-- ===================================================

-- ALTER TABLE table_name DROP COLUMN old_column;

-- ===================================================
-- ROLLBACK SCRIPT
-- ===================================================

-- ALTER TABLE table_name DROP COLUMN new_column;
```

### D. Code Pattern Examples

**Service Pattern:**
```php
public function methodName(array $params)
{
    $this->beginTransaction();
    
    try {
        // 1. Validation
        if (!$this->validate($params)) {
            return $this->errorResponse('Validation failed', $this->errors);
        }
        
        // 2. Business logic
        $result = $this->processData($params);
        
        // 3. Database operations
        $id = $this->model->insert($result);
        
        // 4. Related operations
        $this->updateRelatedRecords($id);
        
        // 5. Commit
        $this->commit();
        
        // 6. Log activity
        $this->logActivity('entity', $id, 'action', 'description');
        
        // 7. Return success
        return $this->successResponse(['id' => $id], 'Success message');
        
    } catch (\Exception $e) {
        $this->rollback();
        log_message('error', $e->getMessage());
        return $this->errorResponse($e->getMessage());
    }
}
```

**Controller Pattern:**
```php
public function methodName()
{
    // 1. Get input
    $data = $this->request->getPost();
    
    // 2. Call service
    $result = $this->service->methodName($data);
    
    // 3. Return response
    if ($result['success']) {
        return $this->response->setJSON($result);
    } else {
        return $this->response->setStatusCode(400)->setJSON($result);
    }
}
```

---

## PROJECT TIMELINE GANTT

```
Week 1-2:  [████████] Phase 1A - Critical Data Integrity
Week 3-5:  [████████████████] Phase 1B - Database Column Renaming
Week 6-10: [████████████████████████████] Phase 2 - Backend Refactoring
Week 11-13:[████████████] Phase 3 - Frontend Consolidation

Legend:
█ - Active development
▓ - Testing
░ - Deployment & monitoring
```

---

## SUCCESS CRITERIA

**Phase 1A:**
- ✅ All redundant FK fields removed from inventory_unit
- ✅ All missing FK constraints added
- ✅ No orphaned records in database
- ✅ All tests pass
- ✅ Performance benchmarks met

**Phase 1B:**
- ✅ All 50+ Indonesian columns renamed
- ✅ All code references updated
- ✅ Triggers updated successfully
- ✅ No Indonesian column names remain in active use
- ✅ Old columns dropped after transition period

**Phase 2:**
- ✅ 6 service classes created with 70%+ test coverage
- ✅ Marketing.php split into 5 controllers (each < 2000 lines)
- ✅ All direct SQL replaced with Query Builder
- ✅ No regression bugs

**Phase 3:**
- ✅ 800+ lines of duplicated HTML removed
- ✅ JavaScript modularized (no inline scripts)
- ✅ UI consistent across all pages
- ✅ 20%+ faster page load times

**Overall:**
- ✅ Data integrity guaranteed by FK constraints
- ✅ Code maintainability improved (smaller files, clear structure)
- ✅ Developer productivity increased (faster feature development)
- ✅ Team satisfaction with new structure
- ✅ Zero critical bugs in production post-deployment

---

## SIGN-OFF

| Role | Name | Signature | Date |
|------|------|-----------|------|
| **Project Sponsor** | | | |
| **Lead Developer** | | | |
| **QA Lead** | | | |
| **DevOps Engineer** | | | |
| **Business Stakeholder** | | | |

---

**Document Version:** 1.0  
**Last Updated:** March 4, 2026  
**Next Review:** After Phase 1A completion

---

_This plan is a living document and will be updated as the project progresses._
