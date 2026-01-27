# Area Management Enhancement - Implementation Guide

**Date:** December 10, 2025  
**Status:** ✅ COMPLETE  
**Version:** 1.0

---

## 📋 Overview

Implementasi **Hybrid Area Management** untuk mengakomodasi perbedaan akses antara:
- **Admin Pusat (Central HQ):** Fokus per department (ELECTRIC atau DIESEL+GASOLINE)
- **Admin Cabang (Branch):** Handle semua department (ALL)

---

## 🗄️ Database Changes

### New Columns

**Table: `areas`**
```sql
ALTER TABLE `areas` 
ADD COLUMN `area_type` ENUM('CENTRAL', 'BRANCH') DEFAULT 'BRANCH';
```

**Table: `area_employee_assignments`**
```sql
ALTER TABLE `area_employee_assignments` 
ADD COLUMN `department_scope` VARCHAR(100) DEFAULT 'ALL';
```

### Column Purpose

| Column | Values | Purpose |
|--------|--------|---------|
| `area_type` | CENTRAL, BRANCH | Membedakan area pusat vs cabang |
| `department_scope` | ALL, ELECTRIC, DIESEL, DIESEL,GASOLINE | Menentukan department access per employee |

---

## 🔧 Implementation Files

### 1. Database Migration
**File:** `databases/area_management_enhancement.sql`

**Features:**
- Add new columns (area_type, department_scope)
- Update existing data automatically
- Create sample branch areas (Surabaya, Perawang, Semarang)
- Create sample branch admins
- Assign admins to areas with proper scope
- Verification queries
- Rollback script

**How to Run:**
```bash
# Via phpMyAdmin or MySQL command line
mysql -u root -p optima_db < databases/area_management_enhancement.sql

# Or import via phpMyAdmin
```

### 2. Models Updated

#### AreaModel.php
**New Fields:**
- `area_type` (CENTRAL/BRANCH)
- `departemen_id` (kept for reference, marked as DEPRECATED)

**New Methods:**
```php
getAreasByType($type)    // Get areas by CENTRAL or BRANCH
getCentralAreas()        // Get only central HQ areas
getBranchAreas()         // Get only branch areas
```

#### AreaEmployeeAssignmentModel.php
**New Field:**
- `department_scope` (ALL, ELECTRIC, DIESEL, DIESEL,GASOLINE)

**New Method:**
```php
getEmployeeAssignmentsWithScope($employeeId)
// Returns: ['areas' => [...], 'departments' => [...], 'has_full_access' => bool]
```

### 3. Helper Functions

**File:** `app/Helpers/auth_helper.php`

#### New Primary Function
```php
get_user_area_department_scope()
```

**Returns:**
- `null` = Full access (superadmin or branch admin with ALL scope)
- `array` = Limited access with specific areas/departments

**Example Return:**
```php
// Central HQ Admin ELECTRIC
[
    'areas' => [1],           // Pusat area ID
    'departments' => [2],     // ELECTRIC only
    'has_full_access' => false
]

// Branch Admin Surabaya
null  // Full access - no filter

// Central HQ Admin DIESEL
[
    'areas' => [1],
    'departments' => [1, 3],  // DIESEL + GASOLINE
    'has_full_access' => false
]
```

#### New Helper Function
```php
apply_area_department_filter($builder, $table, $areaColumn, $deptColumn)
```

**Usage Example:**
```php
$builder = $db->table('inventory_units');
apply_area_department_filter($builder, 'inventory_units');
// Automatically applies WHERE IN filters based on user scope
```

### 4. Controller Updates

**File:** `app/Controllers/ServiceAreaManagementController.php`

**Changes:**
- Replace `get_user_division_departments()` with `get_user_area_department_scope()`
- Simplified filter logic
- Remove complex column existence checks
- Apply area filter directly on area IDs
- Apply department filter on employee departemen_id

---

## 🚀 Usage Examples

### Example 1: Central HQ Admin ELECTRIC

**Setup:**
```sql
-- Area: Pusat Jakarta
INSERT INTO areas (area_code, area_name, area_type) 
VALUES ('HQ', 'Pusat Jakarta', 'CENTRAL');

-- Employee: Admin Electric
INSERT INTO employees (staff_code, staff_name, staff_role, departemen_id) 
VALUES ('ADM-ELC-001', 'Admin Electric HQ', 'ADMIN', 2);

-- Assignment: Admin Electric to Pusat with ELECTRIC scope
INSERT INTO area_employee_assignments 
(area_id, employee_id, assignment_type, start_date, department_scope) 
VALUES (1, 1, 'PRIMARY', '2025-01-01', 'ELECTRIC');
```

**Result:**
- Can only see/manage **ELECTRIC** units
- Limited to **Pusat** area
- Cannot see DIESEL or GASOLINE units

### Example 2: Central HQ Admin DIESEL

**Setup:**
```sql
-- Employee: Admin Diesel
INSERT INTO employees (staff_code, staff_name, staff_role, departemen_id) 
VALUES ('ADM-DSL-001', 'Admin Diesel HQ', 'ADMIN', 1);

-- Assignment: Admin Diesel to Pusat with DIESEL,GASOLINE scope
INSERT INTO area_employee_assignments 
(area_id, employee_id, assignment_type, start_date, department_scope) 
VALUES (1, 2, 'PRIMARY', '2025-01-01', 'DIESEL,GASOLINE');
```

**Result:**
- Can see/manage **DIESEL** and **GASOLINE** units
- Limited to **Pusat** area
- Cannot see ELECTRIC units

### Example 3: Branch Admin Surabaya

**Setup:**
```sql
-- Area: Surabaya Branch
INSERT INTO areas (area_code, area_name, area_type) 
VALUES ('SBY', 'Surabaya', 'BRANCH');

-- Employee: Admin Surabaya (no specific department)
INSERT INTO employees (staff_code, staff_name, staff_role, departemen_id) 
VALUES ('ADM-SBY-001', 'Admin Surabaya', 'ADMIN', NULL);

-- Assignment: Admin Surabaya with ALL scope
INSERT INTO area_employee_assignments 
(area_id, employee_id, assignment_type, start_date, department_scope) 
VALUES (2, 3, 'PRIMARY', '2025-01-01', 'ALL');
```

**Result:**
- Can see/manage **ALL departments** (ELECTRIC, DIESEL, GASOLINE)
- Limited to **Surabaya** area only
- Full access to all units in Surabaya

---

## 🔍 Testing & Verification

### Verify Database Structure
```sql
-- Check new columns
DESCRIBE areas;
DESCRIBE area_employee_assignments;

-- View all areas with type
SELECT id, area_code, area_name, area_type, is_active 
FROM areas 
ORDER BY area_type, area_name;

-- View assignments with scope
SELECT 
    a.area_code,
    a.area_name,
    a.area_type,
    e.staff_name,
    e.staff_role,
    d.nama_departemen,
    aea.department_scope,
    aea.is_active
FROM area_employee_assignments aea
JOIN areas a ON aea.area_id = a.id
JOIN employees e ON aea.employee_id = e.id
LEFT JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE aea.is_active = 1
ORDER BY a.area_type, a.area_name;
```

### Test Helper Function
```php
// In any controller or test script
$scope = get_user_area_department_scope();
dd($scope);

// Expected output examples:
// null - Full access
// ['areas' => [1], 'departments' => [2], 'has_full_access' => false] - Limited
```

### Test Filter in Controller
```php
// Test in ServiceAreaManagementController
public function testFilter()
{
    $db = \Config\Database::connect();
    $builder = $db->table('inventory_units');
    
    // Before filter
    $countBefore = $builder->countAllResults();
    echo "Before filter: $countBefore units<br>";
    
    // Apply filter
    $builder = $db->table('inventory_units');
    apply_area_department_filter($builder, 'inventory_units');
    $countAfter = $builder->countAllResults();
    echo "After filter: $countAfter units<br>";
    
    // Get filtered data
    $builder = $db->table('inventory_units');
    apply_area_department_filter($builder, 'inventory_units');
    $units = $builder->get()->getResultArray();
    dd($units);
}
```

---

## 📊 Comparison: Before vs After

| Aspect | Before | After |
|--------|--------|-------|
| **Area-Department Relation** | 1:1 (area has departemen_id) | Many:Many (via assignments) |
| **Central HQ Admin** | Hard-coded division filter | Flexible scope per assignment |
| **Branch Admin** | Not supported | Full support with ALL scope |
| **Filter Logic** | Complex column checks + joins | Simple scope-based filter |
| **Flexibility** | Low - tied to division | High - per employee assignment |
| **Maintenance** | Hard - scattered logic | Easy - centralized helper |

---

## 🎯 Benefits

### For Central HQ
✅ Admin Electric fokus pada unit ELECTRIC saja  
✅ Admin Diesel fokus pada unit DIESEL & GASOLINE  
✅ Tidak saling tumpang tindih akses  
✅ Data lebih terorganisir per department

### For Branch
✅ Admin cabang handle semua department  
✅ Tidak perlu multiple admin per department  
✅ Efisiensi SDM di cabang  
✅ Fleksibilitas pengelolaan unit

### For System
✅ Cleaner code dengan centralized helper  
✅ Consistent filter logic across controllers  
✅ Easy to extend untuk kebutuhan future  
✅ Better performance dengan simple queries  
✅ Easier to maintain dan debug

---

## 🔄 Migration Path

### Phase 1: Database ✅
1. Run migration SQL file
2. Verify new columns exist
3. Check data population

### Phase 2: Code Update ✅
1. Update Models (AreaModel, AreaEmployeeAssignmentModel)
2. Update Helper (auth_helper.php)
3. Update Controllers (ServiceAreaManagementController)

### Phase 3: Testing 🔄
1. Test with Central HQ admin account
2. Test with Branch admin account
3. Test with Superadmin account
4. Verify data filtering works correctly

### Phase 4: Documentation ✅
1. Create implementation guide (this file)
2. Update API documentation if needed
3. Create user guide for admin

---

## 🚨 Important Notes

### Backward Compatibility
- Old function `get_user_division_departments()` kept but marked DEPRECATED
- Existing code still works, gradually migrate to new function
- `departemen_id` in areas kept for reference (nullable)

### Security Considerations
- Always use helper functions for filtering
- Never bypass filter with raw queries
- Log access attempts for audit trail

### Performance Tips
- Scope filter uses simple WHERE IN (fast)
- No complex joins needed
- Indexes already exist on key columns
- Cache scope result per request if needed

---

## 📞 Support & Troubleshooting

### Common Issues

**Issue:** Filter not working, showing all data
**Solution:** 
- Check if user has area_employee_assignments
- Verify `is_active = 1` on assignments
- Check department_scope value (must not be empty)

**Issue:** Branch admin sees limited data
**Solution:**
- Verify `department_scope = 'ALL'` in assignments
- Check area_type is 'BRANCH'

**Issue:** Central admin sees wrong departments
**Solution:**
- Check department_scope value format
- Should be: 'ELECTRIC' or 'DIESEL,GASOLINE' (comma-separated)

### Debug Queries
```php
// Enable query log
$db = \Config\Database::connect();
$queries = $db->getQueries();
dd($queries);

// Check current user scope
$scope = get_user_area_department_scope();
log_message('debug', 'User scope: ' . json_encode($scope));
```

---

## ✅ Checklist

- [x] Database migration created
- [x] New columns added (area_type, department_scope)
- [x] AreaModel updated
- [x] AreaEmployeeAssignmentModel updated
- [x] Helper functions created
- [x] ServiceAreaManagementController updated
- [x] Documentation created
- [ ] Testing completed
- [ ] User training done
- [ ] Production deployment

---

## 📚 References

- Database Schema: `databases/area_management_enhancement.sql`
- Models: `app/Models/AreaModel.php`, `app/Models/AreaEmployeeAssignmentModel.php`
- Helper: `app/Helpers/auth_helper.php`
- Controller: `app/Controllers/ServiceAreaManagementController.php`
- RBAC Guide: `docs/BALANCED_RBAC_COMPLETE.md`

---

**END OF DOCUMENTATION**
