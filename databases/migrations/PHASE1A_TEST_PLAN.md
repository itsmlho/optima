# Phase 1A Testing Plan
## Data Integrity & Junction Table Refactoring

**Document Version:** 1.0  
**Created:** 2026-03-04  
**Last Updated:** 2026-03-04  
**Status:** Ready for Execution  
**Target Deployment:** Week 1 Staging, Week 5 Production

---

## Table of Contents
1. [Testing Objectives](#testing-objectives)
2. [Scope](#scope)
3. [Test Environment Setup](#test-environment-setup)
4. [Pre-Deployment Testing](#pre-deployment-testing)
5. [Post-Deployment Testing](#post-deployment-testing)
6. [Performance Testing](#performance-testing)
7. [Regression Testing](#regression-testing)
8. [User Acceptance Testing (UAT)](#user-acceptance-testing-uat)
9. [Test Data Requirements](#test-data-requirements)
10. [Exit Criteria](#exit-criteria)
11. [Rollback Procedures](#rollback-procedures)

---

## Testing Objectives

### Primary Goals
1. **Data Integrity Validation**
   - Verify all queries return identical results using junction table vs old FK fields
   - Confirm no data loss during migration
   - Validate FK constraints enforce referential integrity

2. **Functional Correctness**
   - All 42 refactored queries produce expected business results
   - Marketing workflows (Quotation → Deal → Contract → SPK → DI) function correctly
   - Customer management, work orders, warehouse operations work as before

3. **Performance Validation**
   - Junction table queries perform within acceptable SLA (<200ms for list views, <50ms for API)
   - No N+1 query regressions introduced
   - Database indexes utilized correctly

4. **Backward Compatibility**
   - `vw_unit_with_contracts` VIEW provides accurate data during transition period
   - API responses maintain same structure
   - Frontend displays unchanged

### Success Criteria
- ✅ 100% of test cases pass
- ✅ Zero production bugs reported in first 48 hours
- ✅ Performance metrics within ±10% of baseline
- ✅ UAT approval from all stakeholders

---

## Scope

### In Scope
- **13 PHP Files Modified:**
  1. InventoryUnitModel.php
  2. Marketing.php (7 queries)
  3. CustomerManagementController.php (4 queries)
  4. WorkOrderController.php (7 queries)
  5. Warehouse.php (6 queries)
  6. UnitInventoryController.php (2 queries)
  7. SparepartUsageController.php (5 queries)
  8. UnitAssetController.php (2 queries)
  9. Reports.php (1 query)
  10. MarketingOptimized.php (1 query)
  11. Perizinan.php (2 queries)
  12. Kontrak.php (1 query)

- **4 Migration Scripts:**
  1. phase1a_step1_audit_inventory_unit_redundancy.sql
  2. phase1a_step2_create_vw_unit_with_contracts.sql
  3. phase1a_step3_add_missing_fk_constraints.sql
  4. phase1a_step4_drop_redundant_columns.sql

- **Database Tables Affected:**
  - `inventory_unit` (columns: kontrak_id, customer_id, customer_location_id)
  - `kontrak_unit` (junction table - now source of truth)
  - `kontrak`, `customer_locations`, `customers` (FK relationships)

### Out of Scope
- Phase 1B column renaming (bahasa → English)
- Phase 2 service layer implementation
- Phase 3 frontend consolidation
- Non-marketing module functionality
- Mobile app testing (uses same API, covered by API tests)

---

## Test Environment Setup

### Environment Requirements

#### Staging Environment
```yaml
Server: staging.optima.local
Database: optima_staging
PHP: 8.1+
MySQL: 8.0+
CodeIgniter: 4.x
Web Server: Nginx/Apache (matching production)
```

#### Required Configurations
1. **Database Setup:**
   ```sql
   -- Clone production data (anonymized)
   CREATE DATABASE optima_staging;
   USE optima_staging;
   SOURCE /path/to/production_snapshot.sql;
   
   -- Update environment config
   UPDATE config SET environment = 'staging';
   ```

2. **CodeIgniter Configuration:**
   ```php
   // app/Config/Database.php
   public array $staging = [
       'DSN'      => '',
       'hostname' => 'localhost',
       'username' => 'optima_staging_user',
       'password' => '***',
       'database' => 'optima_staging',
       'DBDriver' => 'MySQLi',
       'DBPrefix' => '',
       'pConnect' => false,
       'DBDebug'  => true, // Enable query logging
       'charset'  => 'utf8mb4',
       'DBCollat' => 'utf8mb4_unicode_ci',
   ];
   ```

3. **Query Logging:**
   ```php
   // app/Config/Logger.php
   public array $handlers = [
       'file' => [
           'class'       => 'CodeIgniter\Log\Handlers\FileHandler',
           'level'       => 'debug', // Log all queries
           'path'        => WRITEPATH . 'logs/',
       ],
   ];
   ```

4. **Performance Monitoring:**
   ```bash
   # Enable MySQL slow query log
   SET GLOBAL slow_query_log = 'ON';
   SET GLOBAL long_query_time = 0.2; # 200ms threshold
   SET GLOBAL log_queries_not_using_indexes = 'ON';
   ```

### Test Data Requirements
- **Minimum 1,000 inventory units**
- **100+ active contracts** (status AKTIF, DIPERPANJANG)
- **50+ historical contracts** (status SELESAI, DIBATALKAN)
- **20+ temporary assignments** (is_temporary = 1)
- **Edge cases:**
  - Units with no contracts
  - Units with multiple historical contracts
  - Units transferred between customers (contract history)
  - Contracts with 0 units
  - Contracts with 100+ units

---

## Pre-Deployment Testing

### 1. Code Quality Checks

#### Static Analysis
```bash
# PHP syntax validation
find app/Controllers app/Models -name "*.php" -exec php -l {} \;

# CodeIgniter coding standards
vendor/bin/phpcs --standard=CodeIgniter app/Controllers/Marketing.php
vendor/bin/phpcs --standard=CodeIgniter app/Models/InventoryUnitModel.php

# Complexity analysis
vendor/bin/phpmd app/Controllers text cleancode,codesize,design
```

**Expected Results:**
- ✅ Zero syntax errors
- ✅ Zero coding standard violations
- ✅ Cyclomatic complexity < 10 for new methods

#### Dependency Analysis
```bash
# Check for remaining FK field references
grep -r "iu\.kontrak_id" app/Controllers/
grep -r "iu\.customer_id" app/Controllers/
grep -r "iu\.customer_location_id" app/Controllers/

# Check for junction table usage
grep -r "kontrak_unit ku" app/Controllers/
```

**Expected Results:**
- ✅ Zero direct FK references
- ✅ 42+ junction table usages

### 2. Unit Testing

#### Model Tests
Create `tests/Models/InventoryUnitModelTest.php`:

```php
<?php
namespace Tests\Models;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\InventoryUnitModel;

class InventoryUnitModelTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new InventoryUnitModel();
    }

    public function testGetWithContractInfo()
    {
        // Test unit with active contract
        $unit = $this->model->getWithContractInfo(1);
        $this->assertArrayHasKey('current_kontrak_id', $unit);
        $this->assertArrayHasKey('current_customer_name', $unit);
        $this->assertArrayHasKey('current_customer_location_name', $unit);
        
        // Test unit without contract
        $unit = $this->model->getWithContractInfo(999);
        $this->assertNull($unit['current_kontrak_id']);
    }

    public function testGetCurrentContract()
    {
        // Unit with active contract
        $contract = $this->model->getCurrentContract(1);
        $this->assertNotNull($contract);
        $this->assertContains($contract['status'], ['AKTIF', 'DIPERPANJANG']);
        $this->assertEquals(0, $contract['is_temporary']);
        
        // Unit without active contract
        $contract = $this->model->getCurrentContract(999);
        $this->assertNull($contract);
    }

    public function testGetContractHistory()
    {
        $history = $this->model->getContractHistory(1);
        $this->assertIsArray($history);
        
        // Verify chronological order
        if (count($history) > 1) {
            $this->assertGreaterThanOrEqual(
                strtotime($history[1]['start_date']),
                strtotime($history[0]['start_date'])
            );
        }
    }

    public function testGetUnitsForDropdownExcludesContracted()
    {
        // Should not include units with active contracts
        $units = $this->model->getUnitsForDropdown();
        foreach ($units as $unit) {
            $contract = $this->model->getCurrentContract($unit['id_inventory_unit']);
            $this->assertNull($contract);
        }
    }

    public function testGetUnitDetailForWorkOrder()
    {
        $detail = $this->model->getUnitDetailForWorkOrder(1);
        $this->assertArrayHasKey('brand_name', $detail);
        $this->assertArrayHasKey('customer_name', $detail);
        $this->assertArrayHasKey('customer_location_name', $detail);
    }
}
```

**Run Tests:**
```bash
vendor/bin/phpunit tests/Models/InventoryUnitModelTest.php
```

**Expected Results:**
- ✅ All 5 test methods pass
- ✅ Code coverage > 80% for new methods

### 3. Integration Testing

#### Database Migration Validation

**Test Script:** `tests/Integration/Phase1AMigrationTest.php`

```php
<?php
namespace Tests\Integration;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Database\BaseBuilder;

class Phase1AMigrationTest extends CIUnitTestCase
{
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = \Config\Database::connect();
    }

    public function testStep1AuditIdentifiesMismatches()
    {
        // Execute audit query
        $sql = file_get_contents(ROOTPATH . 'databases/migrations/phase1a_step1_audit_inventory_unit_redundancy.sql');
        
        // Check for discrepancies
        $query = $this->db->query("
            SELECT COUNT(*) as mismatch_count
            FROM inventory_unit iu
            INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit 
                AND ku.status IN ('AKTIF','DIPERPANJANG') 
                AND ku.is_temporary = 0
            INNER JOIN kontrak k ON k.id = ku.kontrak_id
            WHERE iu.kontrak_id != ku.kontrak_id
               OR iu.customer_id != (SELECT customer_id FROM customer_locations WHERE id = k.customer_location_id)
               OR iu.customer_location_id != k.customer_location_id
        ");
        
        $result = $query->getRow();
        $this->assertEquals(0, $result->mismatch_count, "Found data mismatches - reconciliation required before migration");
    }

    public function testStep2ViewCreation()
    {
        // Execute VIEW creation
        $sql = file_get_contents(ROOTPATH . 'databases/migrations/phase1a_step2_create_vw_unit_with_contracts.sql');
        $this->db->query($sql);
        
        // Verify VIEW exists
        $query = $this->db->query("SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_optima_staging = 'vw_unit_with_contracts'");
        $this->assertGreaterThan(0, $query->getNumRows());
        
        // Verify VIEW columns match old structure
        $query = $this->db->query("DESCRIBE vw_unit_with_contracts");
        $columns = array_column($query->getResultArray(), 'Field');
        $this->assertContains('kontrak_id', $columns);
        $this->assertContains('customer_id', $columns);
        $this->assertContains('customer_location_id', $columns);
    }

    public function testStep3ForeignKeyConstraints()
    {
        // Execute FK addition
        $sql = file_get_contents(ROOTPATH . 'databases/migrations/phase1a_step3_add_missing_fk_constraints.sql');
        $this->db->query($sql);
        
        // Verify FK exists
        $query = $this->db->query("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'kontrak_unit'
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
              AND CONSTRAINT_NAME = 'fk_kontrak_unit_unit_id'
        ");
        
        $this->assertGreaterThan(0, $query->getNumRows());
    }

    public function testDataConsistencyAfterMigration()
    {
        // Compare old FK fields vs junction table
        $query = $this->db->query("
            SELECT 
                COUNT(*) as total_units,
                SUM(CASE WHEN iu.kontrak_id = derived.kontrak_id THEN 1 ELSE 0 END) as matching_kontrak,
                SUM(CASE WHEN iu.customer_id = derived.customer_id THEN 1 ELSE 0 END) as matching_customer,
                SUM(CASE WHEN iu.customer_location_id = derived.customer_location_id THEN 1 ELSE 0 END) as matching_location
            FROM inventory_unit iu
            LEFT JOIN vw_unit_with_contracts derived ON derived.id_inventory_unit = iu.id_inventory_unit
        ");
        
        $result = $query->getRow();
        $this->assertEquals($result->total_units, $result->matching_kontrak, "Kontrak ID mismatch");
        $this->assertEquals($result->total_units, $result->matching_customer, "Customer ID mismatch");
        $this->assertEquals($result->total_units, $result->matching_location, "Customer Location ID mismatch");
    }
}
```

**Run Tests:**
```bash
vendor/bin/phpunit tests/Integration/Phase1AMigrationTest.php
```

**Expected Results:**
- ✅ Step 1: Zero mismatches found
- ✅ Step 2: VIEW created successfully with all columns
- ✅ Step 3: FK constraints added
- ✅ Data consistency: 100% match between old and new patterns

---

## Post-Deployment Testing

### 1. Smoke Tests (30 minutes)

Execute immediately after deployment:

#### Marketing Module
```
✅ Login as marketing user
✅ View quotation list (app/Controllers/Marketing.php:165)
✅ Create new quotation
✅ Convert quotation to deal
✅ Create contract from deal
✅ Verify contract shows correct customer/location
✅ Create SPK from contract
✅ Create Delivery Instruction from SPK
✅ Verify unit assignment shows correct customer
```

#### Customer Management
```
✅ View customer list with unit counts
✅ View customer detail with units
✅ Export customer units to Excel
✅ Verify unit data shows current contracts only
```

#### Work Order
```
✅ Create new work order
✅ Assign unit to work order
✅ View work order detail
✅ Complete work order
✅ Verify unit customer info accurate
```

#### Warehouse
```
✅ Receive new units
✅ Dispatch units to customer
✅ View inventory list
✅ Export inventory to Excel
✅ View unit detail
✅ Verify contract information accurate
```

### 2. Functional Testing (2-3 days)

#### Test Case Matrix

| Module | Feature | Test Case ID | Query Location | Expected Result |
|--------|---------|--------------|----------------|-----------------|
| **Marketing** | Quotation List | TC-MKT-001 | Marketing.php:165 | Shows all quotations with customer info via junction |
| Marketing | Unit in Quotation Details | TC-MKT-002 | Marketing.php:260 | Unit details include current customer assignment |
| Marketing | Units by Contract | TC-MKT-003 | Marketing.php:344 | All units for specific contract via junction table |
| Marketing | Contract Unit Listing | TC-MKT-004 | Marketing.php:3625 | Units with AKTIF/DIPERPANJANG contracts only |
| Marketing | SPK Unit Assignments | TC-MKT-005 | Marketing.php:6452 | Units assigned to SPK show correct customer |
| Marketing | DI Unit Assignments | TC-MKT-006 | Marketing.php:6482 | Units in delivery instruction show correct location |
| Marketing | Unit Detail in Deal | TC-MKT-007 | Marketing.php (bonus) | Unit detail modal shows current contract customer |
| **Customer Mgmt** | Customer List | TC-CUS-001 | CustomerManagement.php:163 | Customer list with accurate unit counts |
| Customer Mgmt | Customer Units View | TC-CUS-002 | CustomerManagement.php:298 | Units list for customer shows only current contracts |
| Customer Mgmt | Customer Detail | TC-CUS-003 | CustomerManagement.php:1411 | Detail includes units via junction table |
| Customer Mgmt | Export Customer Units | TC-CUS-004 | CustomerManagement.php:1948 | Excel export has accurate customer-unit relationships |
| **Work Order** | Available Units | TC-WO-001 | WorkOrder.php:114 | Available units for WO dropdown (no active contract) |
| Work Order | Unit Assignment | TC-WO-002 | WorkOrder.php:845 | Assign unit to WO shows customer context |
| Work Order | WO with Unit Info | TC-WO-003 | WorkOrder.php:1706 | WO detail shows unit customer via junction |
| Work Order | Unit Detail in WO | TC-WO-004 | WorkOrder.php:1733 | Unit detail modal in WO context |
| Work Order | Customer Units for WO | TC-WO-005 | WorkOrder.php:1837 | Units at customer location for WO |
| Work Order | WO History | TC-WO-006 | WorkOrder.php:2070 | WO history shows correct customer assignments |
| Work Order | Unit Maintenance History | TC-WO-007 | WorkOrder.php:2630 | Unit WO history with customer context |
| **Warehouse** | Export Inventory | TC-WH-001 | Warehouse.php:334 | Excel export with contract info via junction |
| Warehouse | Unit Detail View | TC-WH-002 | Warehouse.php:490 | Detailed unit info with current customer |
| Warehouse | Inventory List | TC-WH-003 | Warehouse.php:677-678 | Inventory with customer/location via junction |
| Warehouse | Full Unit Detail | TC-WH-004 | Warehouse.php:736-738 | Complete unit detail with contract chain |
| Warehouse | Unit History | TC-WH-005 | Warehouse.php:928 | Historical view uses junction table |
| Warehouse | Active Contract Lookup | TC-WH-006 | Warehouse.php:1008 | Find units with active contracts |
| **Unit Inventory** | Inventory Dashboard | TC-UI-001 | UnitInventory.php:158 | Dashboard shows units with current customers |
| Unit Inventory | Inventory Export | TC-UI-002 | UnitInventory.php:640 | Export includes customer data via junction |
| **Sparepart** | Usage List | TC-SP-001 | SparepartUsage.php:113 | Sparepart usage with unit customer info |
| Sparepart | Usage by Unit | TC-SP-002 | SparepartUsage.php:215 | Unit sparepart history with customer |
| Sparepart | Usage Report | TC-SP-003 | SparepartUsage.php:320 | Report shows customer assignments correctly |
| Sparepart | Usage Detail | TC-SP-004 | SparepartUsage.php:537 | Detail view with customer context |
| Sparepart | Export Usage | TC-SP-005 | SparepartUsage.php:789 | Excel export with accurate customer data |
| **Asset** | Asset List | TC-AS-001 | UnitAsset.php:104-113 | Asset listing with contract info |
| Asset | Asset Export | TC-AS-002 | UnitAsset.php:104-113 | Export shows current customer assignments |
| **Reports** | Rental Report | TC-RP-001 | Reports.php:933 | Rental reports use junction table |
| **Marketing Opt** | Cached Unit Detail | TC-MO-001 | MarketingOptimized.php:480 | Optimized query shows correct customer |
| **Perizinan** | License by Unit | TC-PZ-001 | Perizinan.php:224 | SILO license shows unit customer |
| **Perizinan** | License Report | TC-PZ-002 | Perizinan.php:825 | License report with customer assignments |
| **Kontrak** | Contract Units | TC-KT-001 | Kontrak.php:951 | Contract detail shows assigned units correctly |

**Total Test Cases:** 42 (matching 42 refactored queries)

---

## Performance Testing

### 1. Baseline Metrics Collection

**Before Migration (using old FK fields):**
```sql
-- Enable profiling
SET profiling = 1;

-- Sample queries
SELECT * FROM inventory_unit iu
LEFT JOIN kontrak k ON k.id = iu.kontrak_id
LEFT JOIN customer_locations cl ON cl.id = iu.customer_location_id
LEFT JOIN customers c ON c.id = iu.customer_id
LIMIT 100;

-- Get execution time
SHOW PROFILES;
```

**After Migration (using junction table):**
```sql
SET profiling = 1;

SELECT * FROM inventory_unit iu
LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit 
  AND ku.status IN ('AKTIF','DIPERPANJANG') 
  AND ku.is_temporary = 0
LEFT JOIN kontrak k ON k.id = ku.kontrak_id
LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
LEFT JOIN customers c ON c.id = cl.customer_id
LIMIT 100;

SHOW PROFILES;
```

### 2. Load Testing

**Test Scenarios:**
```yaml
Scenario 1: List Views
  - Endpoint: /marketing/quotations
  - Users: 10 concurrent
  - Duration: 5 minutes
  - Expected: < 200ms avg response time

Scenario 2: Detail Views
  - Endpoint: /warehouse/unit-detail/{id}
  - Users: 5 concurrent
  - Duration: 5 minutes
  - Expected: < 150ms avg response time

Scenario 3: Export Operations
  - Endpoint: /customer-management/export-units
  - Users: 3 concurrent
  - Duration: 2 minutes
  - Expected: < 5s for 1000 records

Scenario 4: API Calls
  - Endpoint: /api/units/dropdown
  - Users: 20 concurrent
  - Duration: 5 minutes
  - Expected: < 50ms avg response time
```

**Tools:**
- Apache JMeter for load testing
- MySQL slow query log for identifying bottlenecks
- New Relic/Datadog for application monitoring

### 3. Index Optimization

**Verify Indexes:**
```sql
-- kontrak_unit indexes
SHOW INDEXES FROM kontrak_unit;
-- Expected: unit_id, kontrak_id, status, is_temporary

-- Analyze query execution plans
EXPLAIN SELECT iu.*, ku.kontrak_id, k.nomor_kontrak
FROM inventory_unit iu
LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit 
  AND ku.status IN ('AKTIF','DIPERPANJANG') 
  AND ku.is_temporary = 0
LEFT JOIN kontrak k ON k.id = ku.kontrak_id
WHERE iu.status = 'READY';

-- Expected: type=ref for kontrak_unit join
```

**Optimization Queries (if needed):**
```sql
-- Add composite index if not exists
CREATE INDEX idx_ku_active_contracts 
ON kontrak_unit (unit_id, status, is_temporary, kontrak_id)
WHERE status IN ('AKTIF','DIPERPANJANG') AND is_temporary = 0;

-- Analyze table statistics
ANALYZE TABLE kontrak_unit;
ANALYZE TABLE inventory_unit;
```

---

## Regression Testing

### 1. Non-Marketing Module Testing

Verify other modules still work correctly:

```
✅ PO Management (purchasing)
✅ Invoice Generation
✅ SPK Processing (non-marketing)
✅ Maintenance Scheduling
✅ User Management
✅ Role & Permissions
✅ Activity Logs
✅ Dashboard Analytics
```

### 2. API Endpoint Testing

**Test all API endpoints:**
```bash
# GET /api/units - unit listing
curl -X GET http://staging.optima.local/api/units \
  -H "Authorization: Bearer {token}"

# GET /api/units/{id} - unit detail
curl -X GET http://staging.optima.local/api/units/1 \
  -H "Authorization: Bearer {token}"

# GET /api/customers/{id}/units - customer units
curl -X GET http://staging.optima.local/api/customers/5/units \
  -H "Authorization: Bearer {token}"

# POST /api/work-orders - create work order
curl -X POST http://staging.optima.local/api/work-orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{"unit_id": 10, "type": "maintenance"}'
```

**Validation:**
- ✅ Response structure unchanged
- ✅ Customer/location data accurate
- ✅ Response time within SLA

### 3. Frontend Testing

**Browser Testing Matrix:**
| Browser | Version | Resolution | Status |
|---------|---------|------------|--------|
| Chrome | Latest | 1920x1080 | ⏳ |
| Firefox | Latest | 1920x1080 | ⏳ |
| Edge | Latest | 1920x1080 | ⏳ |
| Safari | Latest | 1920x1080 | ⏳ |
| Chrome Mobile | Latest | 375x667 | ⏳ |

**Test Checklist:**
```
✅ DataTables rendering correctly
✅ Dropdown selections work
✅ Modal popups display data
✅ Export buttons generate files
✅ Form submissions succeed
✅ AJAX calls return expected data
✅ No JavaScript console errors
```

---

## User Acceptance Testing (UAT)

### UAT Participants
1. **Marketing Team** (3 users) - quotation/contract workflows
2. **Customer Service** (2 users) - customer management
3. **Warehouse Team** (2 users) - inventory operations
4. **Maintenance Team** (2 users) - work order management
5. **Finance Team** (1 user) - reporting
6. **Management** (1 user) - dashboard/analytics

### UAT Schedule
**Week 1-2 (Post-Staging Deployment):**
- Day 1-3: Marketing workflows
- Day 4-5: Customer management
- Day 6-7: Warehouse operations
- Day 8-10: Work order management
- Day 11-12: Reports & exports
- Day 13-14: Edge cases & bug fixes

**Week 3-4 (Pre-Production):**
- Day 15-21: Full regression testing
- Day 22-24: Performance validation
- Day 25-28: Final approval & sign-off

### UAT Test Scenarios

#### Marketing Workflow (Critical Path)
```
Test: Complete Quotation to Contract Flow
Steps:
1. Create quotation for customer "PT ABC"
2. Add 5 units to quotation
3. Convert quotation to deal
4. Create contract from deal
5. Verify contract shows:
   - Customer: PT ABC
   - Location: correct branch
   - Units: 5 units listed
6. Create SPK from contract
7. Create DI from SPK
8. Verify DI shows:
   - Delivery location: correct
   - Units: 5 units with customer info

Expected: All steps complete without errors, data accurate throughout
```

#### Customer Transfer Scenario
```
Test: Unit Transfer Between Customers
Steps:
1. Find unit with active contract at Customer A
2. Complete contract (set status SELESAI)
3. Create new contract for Customer B
4. Assign same unit to new contract
5. Verify:
   - Unit now shows Customer B in all views
   - Contract history shows both customers
   - Old contract still accessible

Expected: Transfer successful, history maintained, no data loss
```

#### Edge Case Testing
```
Test: Unit with No Contract
Steps:
1. Find unit in warehouse (no active contract)
2. Verify unit shows in:
   - Inventory list (no customer shown)
   - Available units dropdown for WO
   - Available units for new contracts
3. Should NOT show in:
   - Customer unit listings
   - Active contract reports

Expected: Unit behaves correctly in all contexts
```

### UAT Exit Criteria
- ✅ All critical path scenarios pass
- ✅ Zero blocking bugs
- ✅ < 5 minor bugs (documented, prioritized)
- ✅ Performance within acceptable range
- ✅ Formal sign-off from all stakeholders

---

## Test Data Requirements

### Minimum Test Data Set

```sql
-- Create test data script
-- databases/migrations/create_phase1a_test_data.sql

-- 1. Test Customers
INSERT INTO customers (nama_customer, is_deleted) VALUES
('PT Test Customer Active', 0),
('PT Test Customer Inactive', 0),
('PT Test Customer Multiple Locations', 0);

-- 2. Test Customer Locations
INSERT INTO customer_locations (customer_id, nama_area, is_deleted) VALUES
(LAST_INSERT_ID()-2, 'Jakarta Office', 0),
(LAST_INSERT_ID()-2, 'Bandung Branch', 0),
(LAST_INSERT_ID()-1, 'Surabaya HQ', 0);

-- 3. Test Inventory Units (various states)
INSERT INTO inventory_unit (nomor_mesin, status, brand_id) VALUES
('TEST-UNIT-001', 'READY', 1),      -- Available unit
('TEST-UNIT-002', 'TERSEWA', 1),    -- Contracted unit
('TEST-UNIT-003', 'TERSEWA', 1),    -- Unit with history
('TEST-UNIT-004', 'READY', 1),      -- Recently returned
('TEST-UNIT-005', 'TERSEWA', 1);    -- Temporary assignment

-- 4. Test Contracts
INSERT INTO kontrak (nomor_kontrak, customer_location_id, status, jenis_sewa) VALUES
('TEST-K-001', (SELECT id FROM customer_locations WHERE nama_area='Jakarta Office'), 'AKTIF', 'BULANAN'),
('TEST-K-002', (SELECT id FROM customer_locations WHERE nama_area='Bandung Branch'), 'DIPERPANJANG', 'BULANAN'),
('TEST-K-003', (SELECT id FROM customer_locations WHERE nama_area='Surabaya HQ'), 'SELESAI', 'HARIAN');

-- 5. Test Contract-Unit Assignments
INSERT INTO kontrak_unit (kontrak_id, unit_id, status, is_temporary, start_date) VALUES
-- Active contract
((SELECT id FROM kontrak WHERE nomor_kontrak='TEST-K-001'), 
 (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin='TEST-UNIT-002'), 
 'AKTIF', 0, '2026-01-01'),

-- Extended contract
((SELECT id FROM kontrak WHERE nomor_kontrak='TEST-K-002'), 
 (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin='TEST-UNIT-003'), 
 'DIPERPANJANG', 0, '2025-12-01'),

-- Historical contract
((SELECT id FROM kontrak WHERE nomor_kontrak='TEST-K-003'), 
 (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin='TEST-UNIT-004'), 
 'SELESAI', 0, '2025-11-01'),

-- Temporary assignment
((SELECT id FROM kontrak WHERE nomor_kontrak='TEST-K-001'), 
 (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin='TEST-UNIT-005'), 
 'AKTIF', 1, '2026-03-01');

-- 6. Edge Cases
-- Unit with multiple historical contracts
INSERT INTO kontrak (nomor_kontrak, customer_location_id, status, jenis_sewa) VALUES
('TEST-K-004', (SELECT id FROM customer_locations WHERE nama_area='Jakarta Office'), 'SELESAI', 'BULANAN'),
('TEST-K-005', (SELECT id FROM customer_locations WHERE nama_area='Bandung Branch'), 'SELESAI', 'BULANAN');

INSERT INTO kontrak_unit (kontrak_id, unit_id, status, is_temporary, start_date, end_date) VALUES
((SELECT id FROM kontrak WHERE nomor_kontrak='TEST-K-004'), 
 (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin='TEST-UNIT-003'), 
 'SELESAI', 0, '2025-06-01', '2025-11-30'),
 
((SELECT id FROM kontrak WHERE nomor_kontrak='TEST-K-005'), 
 (SELECT id_inventory_unit FROM inventory_unit WHERE nomor_mesin='TEST-UNIT-003'), 
 'SELESAI', 0, '2025-01-01', '2025-05-31');
```

### Data Validation Queries

```sql
-- Verify test data created correctly
SELECT 
    'Total Test Units' as metric,
    COUNT(*) as value
FROM inventory_unit
WHERE nomor_mesin LIKE 'TEST-%'

UNION ALL

SELECT 
    'Test Units with Active Contracts',
    COUNT(DISTINCT iu.id_inventory_unit)
FROM inventory_unit iu
INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
    AND ku.status IN ('AKTIF','DIPERPANJANG')
    AND ku.is_temporary = 0
WHERE iu.nomor_mesin LIKE 'TEST-%'

UNION ALL

SELECT 
    'Test Units Available',
    COUNT(*)
FROM inventory_unit iu
LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
    AND ku.status IN ('AKTIF','DIPERPANJANG')
    AND ku.is_temporary = 0
WHERE iu.nomor_mesin LIKE 'TEST-%'
  AND ku.id IS NULL;
```

---

## Exit Criteria

### Phase 1A Testing Complete When:

1. **Code Quality:** ✅
   - All unit tests pass (100% pass rate)
   - Integration tests pass (100% pass rate)
   - Zero syntax errors
   - Code coverage > 80% for new methods

2. **Functional Testing:** ✅
   - All 42 test cases pass
   - Critical workflows validated
   - Edge cases handled correctly
   - No regression bugs

3. **Performance:** ✅
   - Query execution time within SLA
   - No slow query log entries > 200ms
   - Load testing passes all scenarios
   - Database indexes optimized

4. **Data Integrity:** ✅
   - Audit query returns zero mismatches
   - FK constraints enforce referential integrity
   - VIEW provides accurate backward compatibility
   - Historical data preserved

5. **UAT Approval:** ✅
   - All stakeholders sign off
   - Zero blocking bugs
   - Minor bugs documented and prioritized
   - Production deployment approved

### Go/No-Go Decision Criteria

**GO - Proceed to Production:**
- ✅ All exit criteria met
- ✅ Performance metrics acceptable
- ✅ UAT sign-off received
- ✅ Rollback plan tested
- ✅ Monitoring dashboards ready

**NO-GO - Delay Production:**
- ❌ Any exit criteria not met
- ❌ Performance degradation > 20%
- ❌ Blocking bugs exist
- ❌ Stakeholder concerns unresolved
- ❌ Rollback plan not ready

---

## Rollback Procedures

### Scenario 1: Rollback Before Step 4 (Column Drop)

**Situation:** Issues found during UAT, need to revert to old FK fields

**Steps:**
```sql
-- 1. Drop foreign key constraints (if Step 3 executed)
ALTER TABLE kontrak_unit DROP FOREIGN KEY IF EXISTS fk_kontrak_unit_unit_id;
ALTER TABLE kontrak_unit DROP FOREIGN KEY IF EXISTS fk_kontrak_unit_kontrak_id;

-- 2. Drop VIEW (if Step 2 executed)
DROP VIEW IF EXISTS vw_unit_with_contracts;

-- 3. Redeploy old controller code
git checkout <previous-commit-hash>
git push origin main --force  # Use with CAUTION

-- 4. Clear application cache
php spark cache:clear

-- 5. Restart web server
systemctl restart nginx
systemctl restart php-fpm

-- 6. Verify old system operational
curl http://staging.optima.local/marketing/quotations
```

**Recovery Time:** ~15 minutes  
**Data Loss:** None (redundant FK fields still populated)

### Scenario 2: Rollback After Step 4 (Column Drop Executed)

**Situation:** CRITICAL - Columns dropped, must restore

**Steps:**
```sql
-- 1. Restore from backup
mysql -u root -p optima_staging < /backups/optima_pre_phase1a_backup.sql

-- 2. Verify data integrity
SELECT COUNT(*) FROM inventory_unit WHERE kontrak_id IS NOT NULL;

-- 3. Redeploy old code (same as Scenario 1)

-- 4. Full application restart
```

**Recovery Time:** ~30-60 minutes (depends on database size)  
**Data Loss:** Changes made after backup point

### Scenario 3: Partial Rollback (Code Only)

**Situation:** Database migration OK, but code has bugs

**Steps:**
```bash
# 1. Revert specific controller files
git checkout <previous-commit> -- app/Controllers/Marketing.php
git checkout <previous-commit> -- app/Models/InventoryUnitModel.php

# 2. Deploy reverted code
git add app/
git commit -m "Rollback Marketing.php due to bug #123"
git push origin main

# 3. Clear cache
php spark cache:clear

# 4. Monitor for 30 minutes
tail -f writable/logs/log-*.log
```

**Recovery Time:** ~5 minutes  
**Data Loss:** None

### Rollback Testing

**Pre-Production Rollback Drill:**
```yaml
Date: 1 week before production deployment
Environment: Staging
Participants: DevOps, Lead Developer, QA Lead

Steps:
1. Note current database state
2. Execute Phase 1A migration completely
3. Perform Scenario 1 rollback
4. Verify data integrity
5. Re-execute migration
6. Perform Scenario 2 rollback
7. Document actual recovery times
8. Update rollback procedures based on findings
```

---

## Test Execution Tracking

### Test Execution Log

| Date | Tester | Test Type | Test Cases | Pass | Fail | Blocked | Notes |
|------|--------|-----------|------------|------|------|---------|-------|
| 2026-03-04 | - | Unit Tests | 5 | - | - | - | Not started |
| 2026-03-04 | - | Integration | 4 | - | - | - | Not started |
| 2026-03-04 | - | Functional | 42 | - | - | - | Not started |
| 2026-03-04 | - | Performance | 4 | - | - | - | Not started |
| 2026-03-04 | - | UAT | 10 | - | - | - | Not started |

### Bug Tracking

| Bug ID | Severity | Module | Description | Status | Assigned To | Fix Version |
|--------|----------|--------|-------------|--------|-------------|-------------|
| - | - | - | - | - | - | - |

**Severity Levels:**
- **BLOCKER:** Prevents deployment, must fix immediately
- **CRITICAL:** Major functionality broken, fix before production
- **MAJOR:** Important feature issue, fix in current sprint
- **MINOR:** Small issue, can defer to next sprint
- **TRIVIAL:** Cosmetic issue, low priority

---

## Contact & Escalation

### Test Team
- **Test Lead:** [Name] - [Email] - [Phone]
- **QA Engineers:** [Names]
- **DevOps:** [Name] - [Email]

### Escalation Path
1. **Level 1:** Test Lead (response: 2 hours)
2. **Level 2:** Development Manager (response: 4 hours)
3. **Level 3:** CTO (response: 8 hours)

### Communication Channels
- **Daily Updates:** Slack #phase1a-testing
- **Bug Reports:** JIRA Project OPTIMA-PHASE1A
- **Emergency:** WhatsApp Group "OPTIMA Deployment Team"

---

**Document Status:** Ready for Execution  
**Next Review:** After Staging Deployment  
**Approvals Required:**
- [ ] QA Lead
- [ ] Development Lead
- [ ] DevOps Lead
- [ ] Product Owner

---

END OF TEST PLAN
