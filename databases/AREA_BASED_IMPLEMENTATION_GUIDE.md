# AREA-BASED STAFF MANAGEMENT SYSTEM IMPLEMENTATION GUIDE

## Overview
This guide details the implementation of a comprehensive area-based staff management system for the Optima work order application. The system creates a proper hierarchy: **Area → Customer → Unit → Staff Assignment**.

## Database Structure Changes

### New Tables Created
1. **areas** - Master areas/wilayah (Jakarta Utara, Bekasi, etc.)
2. **customers** - PT/Company clients linked to areas  
3. **customer_locations** - Multiple locations per customer
4. **staff** - Renamed from work_order_staff, improved structure
5. **area_staff_assignments** - Key table linking staff to areas

### Modified Tables
- **inventory_unit** - Added customer_id and customer_location_id links

## Implementation Steps

### Phase 1: Database Structure Creation
```bash
# Run the main database design file
mysql -u root -p optima_db < /opt/lampp/htdocs/optima1/databases/area_based_staff_system_design.sql
```

### Phase 2: Data Migration (Manual Steps Required)

#### 2.1 Migrate Customers from Kontrak Table
```sql
-- Check existing kontrak data first
SELECT id, pelanggan, lokasi, pic, status FROM kontrak WHERE status = 'Aktif' LIMIT 10;

-- Then migrate (adjust area_id based on actual location)
INSERT INTO customers (customer_code, customer_name, area_id, primary_address, city, province, pic_name, contract_type)
SELECT 
    CONCAT('CUST', LPAD(id, 3, '0')) as customer_code,
    pelanggan as customer_name,
    CASE 
        WHEN lokasi LIKE '%Jakarta Utara%' OR lokasi LIKE '%Sunter%' THEN 1
        WHEN lokasi LIKE '%Bekasi%' OR lokasi LIKE '%Cikarang%' THEN 2  
        WHEN lokasi LIKE '%Tangerang%' THEN 3
        ELSE 4 -- Default Jakarta Selatan
    END as area_id,
    lokasi as primary_address,
    CASE 
        WHEN lokasi LIKE '%Jakarta%' THEN 'Jakarta'
        WHEN lokasi LIKE '%Bekasi%' THEN 'Bekasi'
        WHEN lokasi LIKE '%Tangerang%' THEN 'Tangerang'
        ELSE 'Jakarta'
    END as city,
    'DKI Jakarta' as province,
    pic as pic_name,
    'RENTAL' as contract_type
FROM kontrak 
WHERE status = 'Aktif';
```

#### 2.2 Create Customer Locations
```sql
-- Create primary location for each customer
INSERT INTO customer_locations (customer_id, location_name, address, city, province, is_primary)
SELECT 
    c.id as customer_id,
    'Lokasi Utama' as location_name,
    c.primary_address as address,
    c.city,
    c.province,
    1 as is_primary
FROM customers c;
```

#### 2.3 Migrate Staff Data
```sql
-- Check existing work_order_staff
SELECT * FROM work_order_staff;

-- Migrate to new staff table
INSERT INTO staff (staff_code, staff_name, staff_role, phone, is_active)
SELECT 
    CONCAT('STF', LPAD(id, 3, '0')) as staff_code,
    staff_name,
    staff_role,
    NULL as phone, -- Add manually later
    is_active
FROM work_order_staff;
```

#### 2.4 Update inventory_unit Links
```sql
-- Link inventory units to customers based on kontrak relationship
UPDATE inventory_unit iu
JOIN kontrak k ON iu.kontrak_id = k.id
JOIN customers c ON CONCAT('CUST', LPAD(k.id, 3, '0')) = c.customer_code
SET iu.customer_id = c.id;

-- Link to primary customer locations
UPDATE inventory_unit iu
JOIN customer_locations cl ON iu.customer_id = cl.customer_id AND cl.is_primary = 1
SET iu.customer_location_id = cl.id;
```

#### 2.5 Create Initial Staff Area Assignments
```sql
-- Create sample staff assignments (adjust based on actual needs)
-- Example: Assign admin to all areas
INSERT INTO area_staff_assignments (area_id, staff_id, assignment_type, start_date)
SELECT 
    a.id as area_id,
    s.id as staff_id,
    'PRIMARY' as assignment_type,
    CURDATE() as start_date
FROM areas a
CROSS JOIN staff s 
WHERE s.staff_role = 'ADMIN'
LIMIT 4; -- One admin per area

-- Assign mechanics and helpers to specific areas (customize as needed)
INSERT INTO area_staff_assignments (area_id, staff_id, assignment_type, start_date)
VALUES
(1, (SELECT id FROM staff WHERE staff_role = 'FOREMAN' LIMIT 1), 'PRIMARY', CURDATE()),
(1, (SELECT id FROM staff WHERE staff_role = 'MECHANIC' LIMIT 1), 'PRIMARY', CURDATE()),
(1, (SELECT id FROM staff WHERE staff_role = 'HELPER' LIMIT 1), 'PRIMARY', CURDATE());
```

### Phase 3: Application Code Updates

#### 3.1 Create New Models

**app/Models/AreaModel.php**
```php
<?php namespace App\Models;

use CodeIgniter\Model;

class AreaModel extends Model
{
    protected $table = 'areas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['area_code', 'area_name', 'area_description', 'area_coordinates', 'is_active'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    public function getActiveAreas()
    {
        return $this->where('is_active', 1)->findAll();
    }
}
```

**app/Models/CustomerModel.php**
```php
<?php namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'customer_code', 'customer_name', 'area_id', 'primary_address', 
        'secondary_address', 'city', 'province', 'postal_code',
        'pic_name', 'pic_phone', 'pic_email', 'contract_type', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    public function getCustomersWithArea()
    {
        return $this->select('customers.*, areas.area_name, areas.area_code')
                   ->join('areas', 'areas.id = customers.area_id')
                   ->where('customers.is_active', 1)
                   ->findAll();
    }
}
```

**app/Models/StaffModel.php**
```php
<?php namespace App\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table = 'staff';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'staff_code', 'staff_name', 'staff_role', 'phone', 'email',
        'address', 'hire_date', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    public function getStaffByArea($areaId)
    {
        return $this->select('staff.*, area_staff_assignments.assignment_type')
                   ->join('area_staff_assignments', 'area_staff_assignments.staff_id = staff.id')
                   ->where('area_staff_assignments.area_id', $areaId)
                   ->where('area_staff_assignments.is_active', 1)
                   ->where('staff.is_active', 1)
                   ->findAll();
    }

    public function getStaffByUnit($unitId)
    {
        return $this->db->query("
            SELECT s.*, s.staff_role, asa.assignment_type,
                   c.customer_name, a.area_name
            FROM staff s
            JOIN area_staff_assignments asa ON s.id = asa.staff_id
            JOIN areas a ON asa.area_id = a.id
            JOIN customers c ON a.id = c.area_id
            JOIN inventory_unit iu ON c.id = iu.customer_id
            WHERE iu.id_inventory_unit = ?
              AND s.is_active = 1
              AND asa.is_active = 1
            ORDER BY s.staff_role
        ", [$unitId])->getResultArray();
    }
}
```

#### 3.2 Update WorkOrderController for Auto Staff Assignment

```php
// In WorkOrderController.php - add this method
private function autoAssignStaff($unitId)
{
    $staffModel = new \App\Models\StaffModel();
    $staffList = $staffModel->getStaffByUnit($unitId);
    
    $assignedStaff = [];
    foreach ($staffList as $staff) {
        $assignedStaff[$staff['staff_role']][] = [
            'id' => $staff['id'],
            'name' => $staff['staff_name']
        ];
    }
    
    return $assignedStaff;
}

// Update store method to include auto assignment
public function store()
{
    // ... existing validation code ...
    
    $workOrderData = [
        // ... existing fields ...
    ];
    
    if ($this->workOrderModel->save($workOrderData)) {
        $workOrderId = $this->workOrderModel->getInsertID();
        
        // Auto assign staff based on unit area
        $assignedStaff = $this->autoAssignStaff($request->getPost('unit_id'));
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Work order berhasil dibuat',
            'work_order_id' => $workOrderId,
            'assigned_staff' => $assignedStaff
        ]);
    }
    
    // ... error handling ...
}
```

## Testing the New System

### 1. Test Area-Staff Relationships
```sql
-- Check area staff summary
SELECT * FROM vw_area_staff_summary;

-- Check unit complete info
SELECT * FROM vw_unit_complete_info LIMIT 5;
```

### 2. Test Auto Staff Assignment Function
```sql
-- Test staff assignment for specific unit
SELECT GetAreaStaffByRole(1, 'ADMIN') as admin_id;
SELECT GetAreaStaffByRole(1, 'MECHANIC') as mechanic_id;
```

### 3. Frontend Testing
1. Create new work order - should auto-populate staff based on unit's area
2. Check work order list - should show assigned staff names
3. Verify staff can only see work orders in their assigned areas

## Benefits of New System

1. **Scalable Area Management** - Easy to add new areas/regions
2. **Proper Staff Organization** - Clear hierarchy and assignments
3. **Automatic Staff Assignment** - No manual staff selection needed
4. **Geographic Efficiency** - Staff work in their designated areas
5. **Multiple Customer Locations** - Support for companies with multiple sites
6. **Flexible Staff Assignments** - Primary, backup, and temporary assignments
7. **Better Reporting** - Area-based performance analytics

## Next Steps

1. Run database migration
2. Update application models
3. Modify controllers for auto-assignment
4. Update frontend to show area information
5. Add area management interface for admins
6. Implement area-based access control

This system provides a solid foundation for managing work orders across different geographical areas with proper staff assignments.