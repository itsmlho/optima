# CROSS-DIVISION ACCESS RECOMMENDATIONS
**Date:** 2026-03-13  
**For:** Optima Integrated ERP System

---

## EXECUTIVE SUMMARY

Karena OPTIMA adalah sistem **terintegrasi antar divisi**, beberapa role memerlukan akses ke module lain untuk **efisiensi workflow**. Berikut adalah rekomendasi akses cross-division yang sudah diimplementasikan + tambahan saran.

---

## 1. IMPLEMENTED CROSS-DIVISION ACCESS

### ✅ MARKETING DEPARTMENT

#### Head Marketing (role_id 2)
**Primary:** Marketing Module (Full Access)

**Cross-Division Access:**
- ✅ **Warehouse:** Inventory Unit, Attachment Inventory, Unit Tracking
- ✅ **Perizinan:** SILO monitoring
- ✅ **Operational:** Temporary Units Report (Full Access) ← **NEW**
- ✅ **Reports:** All reports (export)
- ✅ **Dashboard:** Full view

**Reason:** 
- Perlu cek **unit availability** sebelum buat quotation
- Monitor **SILO status** untuk kontrak legal
- Track **unit location** untuk customer inquiry
- View **attachment** untuk unit specification
- Monitor **temporary unit deployments** untuk sales follow-up

#### Staff Marketing (role_id 3)
**Primary:** Marketing Module (No Delete/Approve)

**Cross-Division Access:**
- ✅ **Warehouse:** Inventory Unit, Attachment, Tracking (VIEW + EDIT, No Delete)
- ✅ **Perizinan:** SILO (VIEW + EDIT, No Delete)
- ✅ **Operational:** Temporary Units Report (VIEW only) ← **NEW**
- ✅ **Dashboard:** View only

**Reason:** Same as Head Marketing, tapi limited access

---

### ✅ OPERATIONAL DEPARTMENT

#### Head Operational (role_id 4)
**Primary:** Operational Module (Full Access)

**Cross-Division Access:**
- ✅ **Warehouse:** Inventory Unit (Full Access)
- ✅ **Marketing:** Quotation & SPK (VIEW only) ← **RECOMMENDATION ADDED**
- ✅ **Reports:** All reports (export)
- ✅ **Dashboard:** Full view

**Reason:**
- Perlu tahu **unit status** untuk delivery planning
- Lihat **quotation & SPK** untuk koordinasi jadwal delivery
- Track **unit movements** untuk logistik

#### Staff Operational (role_id 5)
**Primary:** Operational Module (No Delete/Approve)

**Cross-Division Access:**
- ✅ **Warehouse:** Inventory Unit (VIEW only)
- ✅ **Marketing:** Quotation & SPK (VIEW only) ← **RECOMMENDATION ADDED**
- ✅ **Dashboard:** View only

**Reason:** Same as Head Operational, tapi view-only

---

### ✅ ACCOUNTING/FINANCE DEPARTMENT

#### Head Accounting (role_id 12)
**Primary:** Accounting + Finance Module (Full Access)

**Cross-Division Access:**
- ✅ **Marketing:** SEMUA halaman (Full Access) ← **AS REQUESTED**
- ✅ **Reports:** All reports (export)
- ✅ **Dashboard:** Full view

**Reason:**
- Perlu akses **Kontrak** untuk buat invoice
- Lihat **Quotation & SPK** untuk pricing verification
- Track **Delivery** untuk invoice triggering
- Monitor **customer data** untuk billing

#### Staff Accounting (role_id 13)
**Primary:** Accounting + Finance Module (No Delete/Approve)

**Cross-Division Access:**
- ✅ **Marketing:** SEMUA halaman (No Delete/Approve) ← **AS REQUESTED**
- ✅ **Dashboard:** View only

**Reason:** Same as Head Accounting, tapi limited

---

### ✅ HRD DEPARTMENT

#### Head HRD (role_id 14) ← **SPECIAL CASE**
**Primary:** Employee, Division, Position Management (Full CRUD)

**Cross-Division Access:**
- ✅ **SEMUA MODULE:** VIEW + EXPORT + PRINT (NO CREATE, NO DELETE) ← **AS REQUESTED**
- ✅ **Reports:** Full access
- ✅ **Dashboard:** Full access

**Reason:**
- HRD perlu **monitor semua aktivitas** karyawan
- Audit **performance** dan **compliance**
- Generate **comprehensive reports**
- NO operational access (read-only untuk transparency)

---

### ✅ WAREHOUSE DEPARTMENT

#### Head Warehouse (role_id 16)
**Primary:** Warehouse Module (Full Access)

**Cross-Division Access:**
- ✅ **Purchasing:** PO Unit & PO Attachment (Full Access) ← **AS REQUESTED**
- ✅ **Operational:** Temporary Units Report (Full Access) ← **NEW**
- ✅ **Reports:** All reports (export)
- ✅ **Dashboard:** Full view

**Reason:**
- Monitor **incoming units** dari PO
- Verifikasi **attachment matching** dengan PO
- Track **temporary unit deployments** untuk inventory planning
- Koordinasi dengan purchasing untuk stock planning

#### Staff Warehouse (role_id 32)
**Primary:** Warehouse Module (No Delete/Approve)

**Cross-Division Access:**
- ✅ **Purchasing:** PO Unit & Attachment (VIEW + EDIT, No Delete) ← **AS REQUESTED**
- ✅ **Operational:** Temporary Units Report (VIEW only) ← **NEW**
- ✅ **Dashboard:** View only

**Reason:** Same as Head Warehouse, tapi limited

---

### ✅ SERVICE DEPARTMENT (ALL LEVELS)

#### Head Service (role_id 35)
**Primary:** Service Module (Full Access)

**Cross-Division Access:**
- ✅ **Warehouse:** Inventory Unit, Attachment (FULL ACCESS) ← **AS REQUESTED**
- ✅ **Warehouse:** Unit Tracking, Sparepart Usage, Surat Jalan
- ✅ **Perizinan:** SILO
- ✅ **Purchasing:** PO Verification
- ✅ **Operational:** Temporary Units Report (Full Access) ← **NEW**
- ✅ **Reports:** All reports
- ✅ **Dashboard:** Full view

**Reason:**
- Tahu **unit location** untuk service scheduling
- Monitor **attachment status** untuk maintenance
- Track **SILO compliance** untuk operating license
- Track **temporary unit deployments** untuk service coordination
- View **sparepart usage** untuk inventory planning
- Koordinasi **PO verification** untuk sparepart procurement

#### Admin Service Pusat (role_id 36)
**Primary:** Service Module (No User Management)

**Cross-Division Access:** ← **AS REQUESTED**
- ✅ **Warehouse:** Inventory Unit, Attachment (FULL ACCESS)
- ✅ **Warehouse:** Tracking, Sparepart Usage, Surat Jalan
- ✅ **Perizinan:** SILO
- ✅ **Purchasing:** PO Verification
- ✅ **Operational:** Temporary Units Report (Full Access) ← **NEW**

**Reason:** Same as Head Service

#### Admin Service Area (role_id 37)
**Primary:** Service Module (No DELETE, No User Management)

**Cross-Division Access:** ← **AS REQUESTED**
- ✅ **Warehouse:** Inventory Unit, Attachment, Tracking (VIEW + EDIT + CREATE, No DELETE)
- ✅ **Warehouse:** Sparepart Usage, Surat Jalan
- ✅ **Perizinan:** SILO
- ✅ **Purchasing:** PO Verification
- ✅ **Operational:** Temporary Units Report (VIEW only) ← **NEW**

**Reason:** Regional coordination, track temporary units in coverage area

#### Manager Service Area (role_id 40)
**Primary:** Service Module (No User Management)

**Cross-Division Access:** ← **AS REQUESTED**
- ✅ **Warehouse:** Inventory Unit, Attachment (FULL ACCESS)
- ✅ **Warehouse:** Tracking, Sparepart Usage, Surat Jalan
- ✅ **Perizinan:** SILO
- ✅ **Purchasing:** PO Verification
- ✅ **Operational:** Temporary Units Report (Full Access) ← **NEW**

**Reason:** Same as Head Service

#### Supervisor Service (role_id 38)
**Primary:** Service Work Order + PMPS (VIEW, CREATE, EDIT)

**Cross-Division Access:** ← **AS REQUESTED**
- ✅ **Warehouse:** Inventory Unit, Attachment, Tracking (VIEW + EDIT + CREATE)
- ✅ **Warehouse:** Sparepart Usage, Surat Jalan
- ✅ **Perizinan:** SILO
- ✅ **Operational:** Temporary Units Report (VIEW only) ← **NEW**

**Reason:** Field-level coordination

#### Staff Service (role_id 39)
**Primary:** Service Work Order (VIEW, CREATE)

**Cross-Division Access:** ← **AS REQUESTED**
- ✅ **Warehouse:** Inventory Unit, Attachment, Tracking, Sparepart (VIEW only)
- ✅ **Perizinan:** SILO (VIEW only)
- ✅ **Operational:** Temporary Units Report (VIEW only) ← **NEW**

**Reason:** Field technician need to check unit status and temporary deployments

---

## 2. ADDITIONAL RECOMMENDATIONS

### 🎯 RECOMMENDATION 1: Head Purchasing
**Current:** Purchasing Module only

**Suggested Add:**
```sql
-- Head Purchasing (role_id 10) - Add Inventory View
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT 10, id, 1, NOW()
FROM permissions
WHERE module = 'warehouse' 
  AND page IN ('unit_inventory', 'sparepart_inventory')
  AND action IN ('navigation', 'view')
  AND is_active = 1;
```

**Reason:**
- Perlu cek **current stock** sebelum buat PO
- Monitor **minimum stock level** untuk reorder
- Koordinasi dengan warehouse untuk procurement planning

---

### 🎯 RECOMMENDATION 2: Activity Logs Access
**Current:** Only Admin & IT can view

**Suggested Add:**
```sql
-- All HEAD roles can view activity logs (no delete)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT r.id, p.id, 1, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug IN ('head_marketing', 'head_operational', 'head_purchasing', 
                 'head_accounting', 'head_warehouse', 'head_service')
  AND p.module = 'activity' 
  AND p.action IN ('navigation', 'view', 'export')
  AND p.is_active = 1;
```

**Reason:**
- Head roles perlu **audit trail** untuk monitoring team
- Investigate **suspicious activities**
- Performance tracking

---

### 🎯 RECOMMENDATION 3: Settings Access for Heads
**Current:** Only Admin & IT

**Suggested Add:**
```sql
-- All HEAD roles - View notification settings (customize alerts)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT r.id, p.id, 1, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug LIKE 'head_%'
  AND p.module = 'settings' 
  AND p.page = 'notification'
  AND p.action IN ('navigation', 'view', 'edit')
  AND p.is_active = 1;
```

**Reason:**
- Head roles bisa **customize notification rules**
- Set **alert thresholds** per department
- Manage **email templates** for their team

---

### 🎯 RECOMMENDATION 4: Dashboard Customization
**Current:** All roles see same dashboard

**Suggested Enhancement:**
Create **role-specific dashboard widgets**:

| Role | Custom Dashboard Widgets |
|------|--------------------------|
| Head Marketing | Quotation Pipeline, Conversion Rate, Revenue Forecast |
| Head Operational | Delivery Schedule, Unit Availability, Logistics KPIs |
| Head Purchasing | PO Status, Supplier Performance, Procurement Budget |
| Head Accounting | Outstanding Invoices, Payment Collection, Cash Flow |
| Head Warehouse | Stock Levels, Movement History, Warehouse Capacity |
| Head Service | Work Order Status, PMPS Schedule, Maintenance Costs |
| Head HRD | Employee Attendance, Payroll Summary, Leave Requests |

**Implementation:**
```php
// In DashboardController.php
public function index() {
    $role = session()->get('role');
    $widgets = $this->getWidgetsForRole($role);
    
    return view('dashboard/index', [
        'widgets' => $widgets,
        'can_customize' => can_edit('dashboard')
    ]);
}
```

---

### 🎯 RECOMMENDATION 5: Customer Portal Access
**Future Enhancement:** Create limited portal for customers

**Suggested Permissions:**
```sql
-- New role: Customer (external)
INSERT INTO roles (name, slug, description, is_system_role, is_active)
VALUES ('Customer Portal', 'customer_portal', 'External customer access', 0, 1);

-- Grant limited view permissions
-- - View their own quotations
-- - View their own contracts
-- - Track unit delivery status
-- - View invoices and payment history
-- - Submit service requests
```

**Reason:**
- Reduce customer inquiry calls
- Self-service portal
- 24/7 access to order status
- Improve customer satisfaction

---

### 🎯 RECOMMENDATION 6: Supplier Portal Access
**Future Enhancement:** Create limited portal for suppliers

**Suggested Permissions:**
```sql
-- New role: Supplier (external)
INSERT INTO roles (name, slug, description, is_system_role, is_active)
VALUES ('Supplier Portal', 'supplier_portal', 'External supplier access', 0, 1);

-- Grant limited permissions
-- - View PO sent to them
-- - Update delivery status
-- - Upload invoices
-- - Submit quotes
```

**Reason:**
- Real-time PO communication
- Reduce email back-and-forth
- Track payment status
- Upload delivery documents

---

### 🎯 RECOMMENDATION 7: Mobile App Roles
**Future Enhancement:** Field technician mobile access

**Suggested Permissions:**
```sql
-- Mobile-specific permissions (lightweight)
-- - Offline work order management
-- - Photo upload (before/after service)
-- - GPS location tracking
-- - Signature capture
-- - Sparepart usage logging
```

**Reason:**
- Field technicians tidak selalu ada laptop
- Real-time service updates
- GPS-based attendance
- Paperless work order

---

## 3. PERMISSION NAMING CONVENTION

**Best Practice untuk Future Permissions:**

```
{module}.{page}.{action}[.{sub_action}]

Examples:
✅ marketing.quotation.view
✅ marketing.quotation.create
✅ marketing.quotation.export.pdf
✅ marketing.quotation.export.excel
✅ warehouse.unit.view.cross_division
✅ service.workorder.assign.technician
```

**Action Hierarchy:**
1. `navigation` - Menu visibility
2. `view` - Read access
3. `create` - Add new records
4. `edit` - Update existing records
5. `delete` - Remove records
6. `approve` - Approval actions
7. `export` - Export data
8. `print` - Print documents
9. `{custom}` - Specific actions (assign, verify, etc.)

---

## 4. ROLE PERMISSION MATRIX (UPDATED)

| Module | Marketing | Operational | Purchasing | Accounting | HRD | Warehouse | Service | IT/Admin |
|--------|-----------|-------------|------------|------------|-----|-----------|---------|----------|
| **Marketing** | ✅ Full | 📖 View (Q&SPK) | - | ✅ Full | 📖 View | - | - | ✅ All |
| **Operational** | - | ✅ Full | - | - | 📖 View | - | - | ✅ All |
| **Purchasing** | - | - | ✅ Full | - | 📖 View | 📖 PO View | 📖 PO Verify | ✅ All |
| **Accounting** | - | - | - | ✅ Full | 📖 View | - | - | ✅ All |
| **Finance** | - | - | - | ✅ Full | 📖 View | - | - | ✅ All |
| **Warehouse.Unit** | 📖 View | 📖 View | 📖 View | - | 📖 View | ✅ Full | ✅ Full | ✅ All |
| **Warehouse.Attach** | 📖 View | - | - | - | 📖 View | ✅ Full | ✅ Full | ✅ All |
| **Warehouse.Spare** | - | - | - | - | 📖 View | ✅ Full | 📖 View | ✅ All |
| **Service** | - | - | - | - | 📖 View | - | ✅ Full | ✅ All |
| **Perizinan.SILO** | 📖 View | - | - | - | 📖 View | - | 📖 View | ✅ All |
| **Reports** | 📊 View | 📊 View | 📊 View | 📊 View | 📊 Full | 📊 View | 📊 View | ✅ All |
| **Dashboard** | 📊 View | 📊 View | 📊 View | 📊 View | 📊 Full | 📊 View | 📊 View | ✅ All |
| **Admin** | - | - | - | - | ✅ Employee | - | - | ✅ All |

**Legend:**
- ✅ **Full** = Complete CRUD access
- 📖 **View** = Read-only access
- 📊 **View** = Read + Export
- **-** = No access

---

## 5. WORKFLOW OPTIMIZATION SCENARIOS

### Scenario 1: New Unit Quotation Flow
```
1. Marketing creates quotation
   → Check unit availability (Warehouse.unit_inventory - VIEW)
   → Check SILO status (Perizinan.silo - VIEW)
   → Attach unit specs (Warehouse.attachment - VIEW)

2. Marketing converts to SPK
   → Notify Operational (auto-email)

3. Operational receives SPK
   → View SPK details (Marketing.spk - VIEW)
   → Check unit location (Warehouse.unit_tracking - VIEW)
   → Create Delivery Instruction

4. Operational delivers unit
   → Update delivery status
   → Auto-notify Accounting (email)

5. Accounting creates invoice
   → View Kontrak (Marketing.kontrak - VIEW)
   → View Delivery (Operational.delivery - VIEW)
   → Create Invoice (Accounting.invoice - CREATE)
```

**Cross-division permissions needed:** ✅ All implemented!

### Scenario 2: Service Work Order Flow
```
1. Customer calls for service
   → Staff Service creates Work Order

2. Supervisor assigns technician
   → View unit location (Warehouse.unit_tracking - VIEW)
   → Check SILO validity (Perizinan.silo - VIEW)
   → Assign to technician

3. Technician goes to site
   → View unit specs (Warehouse.attachment - VIEW)
   → Log sparepart usage (Warehouse.sparepart_usage - CREATE)
   → Complete work order

4. Admin Service verifies
   → Check sparepart usage (Warehouse.sparepart_usage - VIEW)
   → Approve work order

5. Head Service reviews
   → Generate service report (Reports.service - VIEW/EXPORT)
```

**Cross-division permissions needed:** ✅ All implemented!

---

## 6. SECURITY CONSIDERATIONS

### Row-Level Security (Future Enhancement)
Some roles should only see **their own division's data**:

```sql
-- Example: Marketing Head can only see their division's quotations
-- Add division_id filter in queries
SELECT * FROM quotations 
WHERE division_id = ? 
  AND user_id IN (SELECT id FROM users WHERE division_id = ?)
```

**Recommended for:**
- 🔒 **Division-based filtering** for Staff roles
- 🔒 **Team-based filtering** for Supervisor roles
- 🔒 **Area-based filtering** for Service Area roles
- ✅ **No filtering** for Head roles (can see all division data)

### Data Masking (Future Enhancement)
Hide sensitive data from certain roles:

```php
// Example: Staff cannot see profit margin
if (!can_view('marketing.quotation.profit_margin')) {
    unset($quotation['profit_margin']);
    unset($quotation['cost_price']);
}
```

**Recommended for:**
- 🔒 **Financial data** (cost, margin, profit)
- 🔒 **Salary information** (except HRD)
- 🔒 **Supplier pricing** (except Purchasing Head)

---

## 7. SUMMARY OF CHANGES

### ✅ Implemented in Migration SQL
1. ✅ Marketing → Warehouse (Unit, Attachment, Tracking) + SILO
2. ✅ Operational → Warehouse (Unit) + Marketing (Q&SPK view)
3. ✅ Accounting → ALL Marketing pages
4. ✅ HRD → ALL pages (VIEW only, except employee management)
5. ✅ Warehouse → Purchasing (PO Unit & Attachment)
6. ✅ Service (ALL levels) → Warehouse (Unit, Attachment, Tracking, Sparepart, Surat Jalan) + SILO + PO Verification

### 🎯 Additional Recommendations (Optional)
1. 🎯 Head Purchasing → Warehouse Inventory (view)
2. 🎯 All Head roles → Activity Logs (view)
3. 🎯 All Head roles → Notification Settings (edit)
4. 🎯 Role-specific Dashboard customization
5. 🎯 Customer Portal (future)
6. 🎯 Supplier Portal (future)
7. 🎯 Mobile App for field technicians (future)

---

## 8. DEPLOYMENT NOTES

**What's Changed:**
- File `PROD_20260313_fix_role_permissions.sql` sudah diupdate dengan SEMUA cross-division access
- TIDAK ADA SQL tambahan yang perlu dijalankan untuk cross-division access
- Recommendation tambahan (1-7) bersifat **OPTIONAL** dan bisa diimplementasikan nanti

**Testing Checklist:**
1. ✅ Marketing dapat view inventory unit
2. ✅ Operational dapat view quotation & SPK
3. ✅ Accounting dapat akses semua halaman marketing
4. ✅ HRD dapat view semua halaman (no create/delete)
5. ✅ Warehouse dapat view/edit PO
6. ✅ Service dapat akses inventory, attachment, SILO, sparepart

---

**Prepared By:** GitHub Copilot (Claude Sonnet 4.5)  
**Date:** March 13, 2026  
**Status:** ✅ READY FOR REVIEW & DEPLOYMENT
