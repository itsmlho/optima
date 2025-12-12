# RBAC STRUCTURE RECOMMENDATION - OPTIMA PRO

## 🎯 **ROLES HIERARCHY DESIGN**

### **LEVEL 1: SUPER ADMIN**
```sql
-- Super Administrator (Existing)
- Role: Super Administrator 
- Scope: ALL divisions, ALL areas, ALL departments
- Access: Full system control
```

### **LEVEL 2: DIVISION HEADS**
```sql
-- Division Level Management
- Head Marketing
- Head Service (NEW) ⭐
- Head Warehouse  
- Head Purchasing
- Head Accounting
- Head HRD
- Head IT
```

### **LEVEL 3: SERVICE SPECIFIC ROLES** ⭐
```sql
-- Service Division Structure (RECOMMENDATION)

A. SERVICE PUSAT (Central Office)
   ├── Admin Service Pusat Electric    (Electric Dept only)
   ├── Admin Service Pusat Diesel      (Diesel + Gasoline Dept)
   └── Manager Service Pusat           (All Departments)

B. SERVICE AREA (Branch/Regional)
   ├── Admin Service Area              (All Departments in specific area)
   ├── Supervisor Service Area         (Operational level)
   └── Staff Service Area              (Field level)

C. FIELD OPERATIONS
   ├── Foreman                        (Team leader in area)
   ├── Mechanic/Technician            (Specialist by department)
   └── Helper/Assistant               (Support staff)
```

## 🗃️ **DATABASE STRUCTURE UPDATES**

### **1. UPDATE DIVISIONS TABLE**
```sql
-- Add Service Division (unified)
INSERT INTO divisions (id, name, code, description, is_active) VALUES 
(9, 'Service', 'SERVICE', 'Service Division - All Service Operations', 1);

-- Update existing service divisions to be departments under Service Division
UPDATE divisions SET 
    name = 'Service (Legacy - Electric)', 
    is_active = 0 
WHERE code = 'SERVICE_ELECTRIC';

UPDATE divisions SET 
    name = 'Service (Legacy - Diesel)', 
    is_active = 0 
WHERE code = 'SERVICE_DIESEL';
```

### **2. UPDATE ROLES TABLE**
```sql
-- Add new service-specific roles
INSERT INTO roles (name, slug, description, division_id, is_active) VALUES

-- Level 2: Division Head
('Head Service', 'head_service', 'Head of Service Division - All Areas & Departments', 9, 1),

-- Level 3A: Service Pusat (Central Office)
('Admin Service Pusat Electric', 'admin_service_pusat_electric', 'Central Service Admin - Electric Department Only', 9, 1),
('Admin Service Pusat Diesel', 'admin_service_pusat_diesel', 'Central Service Admin - Diesel & Gasoline Departments', 9, 1),
('Manager Service Pusat', 'manager_service_pusat', 'Central Service Manager - All Departments', 9, 1),

-- Level 3B: Service Area (Branch/Regional)  
('Admin Service Area', 'admin_service_area', 'Area Service Admin - All Departments in Assigned Areas', 9, 1),
('Supervisor Service Area', 'supervisor_service_area', 'Area Service Supervisor - Operational Level', 9, 1),
('Staff Service Area', 'staff_service_area', 'Area Service Staff - Field Operations', 9, 1),

-- Level 3C: Field Operations
('Foreman Service', 'foreman_service', 'Service Team Leader in Area', 9, 1),
('Mechanic Service', 'mechanic_service', 'Service Technician/Mechanic', 9, 1),
('Helper Service', 'helper_service', 'Service Assistant/Helper', 9, 1);
```

## 🔐 **PERMISSION MATRIX**

| Role | Areas Access | Departments | Key Permissions |
|------|-------------|-------------|----------------|
| **Super Admin** | ALL | ALL | Full system control |
| **Head Service** | ALL | ALL | Service division management |
| **Admin Service Pusat Electric** | Central areas only | ELECTRIC only | Central electric operations |
| **Admin Service Pusat Diesel** | Central areas only | DIESEL + GASOLINE | Central diesel/gasoline ops |
| **Manager Service Pusat** | ALL | ALL | Strategic oversight |
| **Admin Service Area** | Assigned areas | ALL in area | Area operations management |
| **Supervisor Service Area** | Assigned areas | ALL in area | Daily operations |
| **Staff Service Area** | Assigned areas | Assigned dept | Field work execution |

## 🏗️ **IMPLEMENTATION STRATEGY**

### **PHASE 1: Database Structure**
1. ✅ Update divisions table
2. ✅ Add new roles  
3. ✅ Create permission mappings
4. ✅ Update user assignments

### **PHASE 2: Access Control Logic**
1. ✅ Update auth_helper.php scope functions
2. ✅ Implement area-department filtering
3. ✅ Add role-based menu restrictions
4. ✅ Update controllers with proper checks

### **PHASE 3: User Interface**
1. ✅ Role-based dashboard views
2. ✅ Dynamic menu generation
3. ✅ Area/department selection forms
4. ✅ Permission-based feature access

## 📋 **ACCESS CONTROL EXAMPLES**

### **Scenario 1: Admin Service Pusat Electric**
```php
$scope = get_user_area_department_scope();
// Returns: ['areas' => [Central areas], 'departments' => [2], 'has_full_access' => false]

// User sees:
- Jakarta Pusat, Jakarta Selatan (Central areas only)
- Electric department data only
- Cannot access Diesel/Gasoline records
```

### **Scenario 2: Admin Service Area**  
```php
$scope = get_user_area_department_scope();
// Returns: ['areas' => [specific area ID], 'departments' => [1,2,3], 'has_full_access' => false]

// User sees:
- Assigned area only (e.g., Tangerang)
- All departments in that area
- Cannot access other areas
```

### **Scenario 3: Manager Service Pusat**
```php
$scope = get_user_area_department_scope();
// Returns: null (full access)

// User sees:
- All areas and departments
- Strategic reports and analytics
- System-wide service operations
```

## 🎯 **BENEFITS OF THIS STRUCTURE**

### **✅ CLEAR HIERARCHY**
- Logical progression from division → area → department
- Easy to understand and maintain
- Scalable for future growth

### **✅ FLEXIBLE PERMISSIONS**  
- Granular access control
- Department-specific restrictions where needed
- Area-based geographic limitations

### **✅ OPERATIONAL EFFICIENCY**
- Users only see relevant data
- Reduced cognitive load
- Better data security

### **✅ FUTURE-PROOF**
- Easy to add new areas/departments
- Role duplication prevented
- Consistent naming convention

## 🚀 **NEXT STEPS**

1. **Review & Approve**: Validate this structure with stakeholders
2. **Implement Database Changes**: Run SQL updates
3. **Update Code Logic**: Modify auth_helper and controllers  
4. **Test Thoroughly**: Verify access controls work correctly
5. **User Training**: Brief users on new role structure

---
**Created**: December 10, 2025  
**Purpose**: RBAC optimization for OptimaPro service management system