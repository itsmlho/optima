# Work Order Unit Dropdown Fix - March 9, 2026

## Problem
Work order creation failed with SQL error: `Unknown column 'k.customer_location_id' in 'on clause'`

**Root Cause:** Database schema change (March 5, 2026) removed `kontrak.customer_location_id` column. Location tracking is now at `kontrak_unit` level via `kontrak_unit.customer_location_id`.

## Schema Changes (Already Applied)
```sql
-- OLD (REMOVED):
kontrak.customer_location_id → customer_locations.id

-- NEW (CORRECT):
kontrak.customer_id → customers.id (direct)
kontrak_unit.customer_location_id → customer_locations.id (per unit)
```

## Files Fixed

### Phase 1: WorkOrder Module (Original Issue)

#### 1. **WorkOrderController.php** (4 locations)
**Lines:** ~850, ~1846, ~2081, ~2643

**Before:**
```php
LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id  // ❌ WRONG
```

**After:**
```php
LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id  // ✅ CORRECT
```

**Affected Methods:**
- `viewWorkOrder()` - Work order detail view
- `searchUnits()` - Unit search functionality
- `getUnitsDropdown()` - **Main fix for unit dropdown**
- Customer data query for print

#### 2. **StaffModel.php** (1 location)
**Line:** ~133

**Before:**
```php
->join('kontrak ktr', 'ktr.customer_location_id = cl.id')  // ❌ kontrak doesn't have this column
->join('kontrak_unit ku', 'ku.kontrak_id = ktr.id ...')
```

**After:**
```php
->join('kontrak_unit ku', 'ku.customer_location_id = cl.id ...')  // ✅ Direct join to kontrak_unit
```

**Affected Method:**
- `getStaffByUnit()` - Get staff assigned to specific unit

#### 3. **ContractPOHistoryModel.php** (1 location)
**Line:** ~403

**Before:**
```php
->join('customer_location', 'customer_location.id = kontrak.location_id', 'left')  // ❌ WRONG
```

**After:**
```php
// Use subquery to get location from kontrak_unit
->select('..., (SELECT cl.nama_lokasi FROM kontrak_unit ku 
                JOIN customer_location cl ON cl.id = ku.customer_location_id 
                WHERE ku.kontrak_id = kontrak.id LIMIT 1) as nama_lokasi')  // ✅ CORRECT
```

**Affected Method:**
- `getByStatus()` - Get POs by status with contract details

---

### Phase 2: System-Wide Audit & Fixes

#### 4. **KontrakModel.php**
**Lines:** 15-23, 48-67

**Changes:**
- ❌ **REMOVED** `customer_location_id` from `$allowedFields` array
- ❌ **REMOVED** `customer_location_id` validation rules
- ❌ **REMOVED** `customer_location_id` validation messages
- ✅ Added comment: *"customer_location_id REMOVED - moved to kontrak_unit table (March 5, 2026)"*

**Before:**
```php
protected $allowedFields = [
    'no_kontrak',
    'customer_location_id',  // ❌ Column doesn't exist anymore
    ...
];

protected $validationRules = [
    'customer_location_id' => 'permit_empty|is_natural_no_zero',  // ❌ Invalid
    ...
];
```

**After:**
```php
protected $allowedFields = [
    'no_kontrak',
    // customer_location_id REMOVED - moved to kontrak_unit table (March 5, 2026)
    ...
];

protected $validationRules = [
    // customer_location_id REMOVED (March 5, 2026)
    ...
];
```

#### 5. **Kontrak.php** (5 locations)

##### a. `store()` method (Line ~384)
**Before:**
```php
$data = [
    'customer_location_id' => (int)$this->request->getPost('customer_location_id'),  // ❌ WRONG
    ...
];
```

**After:**
```php
// Get customer_location_id from form to lookup customer_id
$customerLocationId = (int)$this->request->getPost('customer_location_id') ?: (int)$this->request->getPost('location_id');
$customerId = null;

// Query customer_id from customer_location
if ($customerLocationId > 0) {
    $location = $this->db->table('customer_locations')
        ->select('customer_id')
        ->where('id', $customerLocationId)
        ->get()
        ->getRowArray();
    $customerId = $location['customer_id'] ?? null;
}

$data = [
    'customer_id' => $customerId,  // ✅ Use customer_id instead
    ...
];
```

##### b. `update()` method (Lines ~534, ~600)
**Before:**
```php
$rules = [
    'customer_location_id' => 'required|is_natural_no_zero',  // ❌ WRONG
    ...
];

$data = [
    'customer_location_id' => (int)$this->request->getPost('customer_location_id'),  // ❌ WRONG
    ...
];
```

**After:**
```php
// Get customer_location_id from form to lookup customer_id  
$customerLocationId = (int)$this->request->getPost('customer_location_id');
// ... query customer_id ...

$rules = [
    // customer_location_id validation REMOVED (March 5, 2026 - moved to kontrak_unit)
    ...
];

$data = [
    'customer_id' => $customerId,  // ✅ Use customer_id instead
    ...
];
```

##### c. `createRenewal()` method (Line ~1926)
**Before:**
```php
$renewalData = [
    'customer_location_id' => $locationId,  // ❌ WRONG
    ...
];
```

**After:**
```php
$renewalData = [
    'customer_id' => $customerId,  // ✅ Use customer_id instead
    ...
];
```

##### d. Logging methods (Lines ~456, ~634, ~747)
**Before:**
```php
// Get customer info for logging
if (!empty($data['customer_location_id'])) {
    $customerLocation = $this->db->query("SELECT c.customer_name, cl.location_name 
                                         FROM customer_locations cl 
                                         LEFT JOIN customers c ON cl.customer_id = c.id 
                                         WHERE cl.id = ?", [$data['customer_location_id']])->getRowArray();  // ❌ WRONG
}
```

**After:**
```php
// Get customer info for logging
if (!empty($data['customer_id'])) {
    $customerLocation = $this->db->query("SELECT c.customer_name, 
                                                 (SELECT cl.location_name 
                                                  FROM kontrak_unit ku 
                                                  JOIN customer_locations cl ON cl.id = ku.customer_location_id 
                                                  WHERE ku.kontrak_id = ? 
                                                  LIMIT 1) as location_name
                                          FROM customers c 
                                          WHERE c.id = ?", [$newId, $data['customer_id']])->getRowArray();  // ✅ CORRECT
}
```

#### 6. **Marketing.php** (3 locations)

##### a. `createContract()` method (Line ~8191)
**Before:**
```php
$contractData = [
    'customer_location_id' => $customerLocation['id'],  // ❌ WRONG
    'nilai_total' => $quotation['total_amount'],
    ...
];
```

**After:**
```php
$contractData = [
    'customer_id' => $quotation['created_customer_id'],  // ✅ Use customer_id instead
    'nilai_total' => $quotation['total_amount'],
    ...
];
```

##### b. Contract renewal method (Line ~8869)
**Before:**
```php
$newContractData = [
    'customer_location_id' => $originalContract['customer_location_id'],  // ❌ WRONG
    ...
];
```

**After:**
```php
$newContractData = [
    'customer_id' => $originalContract['customer_id'],  // ✅ Use customer_id instead
    ...
];
```

##### c. `getKontrakRemoved()` deprecated method (Line ~6979)
**Before:**
```php
if (!empty($kontrak['customer_location_id'])) {
    $locationId = (int)$kontrak['customer_location_id'];
    $customer_location = $this->db->query("SELECT cl.*, c.customer_name 
                                         FROM customer_locations cl 
                                         LEFT JOIN customers c ON cl.customer_id = c.id 
                                         WHERE cl.id = ?", [$locationId])->getRowArray();  // ❌ WRONG
}
```

**After:**
```php
if (!empty($kontrak['customer_id'])) {
    $customer_location = $this->db->query("SELECT cl.*, c.customer_name 
                                         FROM kontrak_unit ku
                                         JOIN customer_locations cl ON cl.id = ku.customer_location_id
                                         JOIN customers c ON cl.customer_id = c.id
                                         WHERE ku.kontrak_id = ?
                                         LIMIT 1", [$id])->getRowArray();  // ✅ CORRECT
}
```

---

## Summary of Fixes

### ✅ **Files Modified:** 6 files
1. `app/Controllers/WorkOrderController.php` (4 fixes)
2. `app/Models/StaffModel.php` (1 fix)
3. `app/Models/ContractPOHistoryModel.php` (1 fix)
4. `app/Models/KontrakModel.php` (3 fixes - allowedFields, validationRules, validationMessages)
5. `app/Controllers/Kontrak.php` (8 fixes - store, update, renewal, 3x logging)
6. `app/Controllers/Marketing.php` (3 fixes - createContract, renewal, deprecated method)

### ✅ **Total Instances Fixed:** 20+

### ❌ **Not Affected (Correct Usage):**
- `Warehouse.php` line 757 - Debug logging only (display data)
- `UnitAudit.php` line 353 - Correct usage (audit_location table has customer_location_id)
- `Operational.php` lines 2675, 2687 - Correct usage (inventory_unit table has customer_location_id)

---

## Correct Pattern

### ❌ **WRONG - Direct JOIN to kontrak:**
```php
LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
```

### ✅ **CORRECT - JOIN via kontrak_unit:**
```php
LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
LEFT JOIN kontrak k ON k.id = ku.kontrak_id
LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
```

### ✅ **ALTERNATIVE - Subquery (for summary views):**
```php
(SELECT cl.location_name 
 FROM kontrak_unit ku 
 JOIN customer_locations cl ON cl.id = ku.customer_location_id 
 WHERE ku.kontrak_id = k.id 
 LIMIT 1) as location_name
```

## Testing Performed
### Phase 1 (WorkOrder Module)
- ✅ Work order creation modal loads units successfully
- ✅ No SQL errors in console
- ✅ Unit dropdown populated with customer and location data
- ✅ Unit dropdown shows ALL units with ALL statuses and proper format
- ✅ CSRF token working correctly on work order submission
- ✅ Work order validation functional (staff assignment requirement)

### Phase 2 (System-Wide)
- ✅ No PHP syntax errors in all modified files
- ✅ All modified controllers pass validation
- ✅ KontrakModel validation rules updated correctly
- ✅ Contract creation/update/renewal methods use correct schema

## Related Documentation
- `CONTRACT_LOCATION_SCHEMA_FIX.md` - Original schema change documentation (March 5, 2026)
- `RECENT_CHANGES_MARCH_5-7_2026.md` - Related changes log
- `databases/Input_Data/MIGRATION_CUSTOMER_LOCATION_ID_FIXES.md` - Migration guide
- `databases/Input_Data/LOCATION_TRACKING_REDESIGN_SUMMARY.md` - Design rationale
- `databases/PRODUCTION_VERIFICATION_QUERIES.sql` - Verification queries for production

## Production Deployment Notes

### ⚠️ Pre-Deployment Checklist
1. ✅ Verify `kontrak.customer_location_id` column already removed in production database
2. ✅ Verify `kontrak_unit.customer_location_id` column exists with foreign key constraint
3. ✅ Backup production database before deploying code changes
4. ✅ Test contract creation/update forms work with new schema
5. ✅ Verify all location data properly migrated to `kontrak_unit` table

### 📋 Deployment Steps
1. Deploy code changes to production server
2. Clear application cache: `php spark cache:clear`
3. Test critical workflows:
   - Work order creation
   - Contract creation from quotation
   - Contract renewal
   - Unit assignment to contracts
4. Monitor error logs for 24 hours

## Notes
- **Schema change was already applied** - This fix addresses code that wasn't updated during the March 5, 2026 schema migration
- Most of the codebase was **already correct** using subqueries (good design!)
- Fixes ensure **100% consistency** with new multi-location contract support
- The pattern is now consistent: **location is always retrieved from `kontrak_unit`, not `kontrak`**
- All INSERT/UPDATE operations to `kontrak` table now use `customer_id` instead of `customer_location_id`

---
**Fixed By:** GitHub Copilot  
**Date:** March 9, 2026  
**Issue ID:** Contract Location Schema Violation (Post-Migration Cleanup)  
**Original Issue:** Unit dropdown not loading in Work Order creation  
**Extended Scope:** System-wide audit and cleanup of all `kontrak.customer_location_id` references
