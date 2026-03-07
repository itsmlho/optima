# 📋 RECENT CHANGES LOG - March 5-7, 2026

## 🚨 CRITICAL FIX - March 7, 2026

### **CSRF Configuration Bug Fix**

#### **Issue:**
- 403 Forbidden errors on ALL AJAX POST requests
- Root cause: `.env` files overriding `app/Config/Security.php` with incorrect values
- Token name mismatch: JavaScript sending `csrf_test_name`, PHP expecting `csrf_token_name`

#### **Files Fixed:**
1. **`.env`** (Development)
   - ✅ `security.tokenName = 'csrf_test_name'` (was: `csrf_token_name`)
   - ✅ `security.tokenRandomize = false` (was: `true`)
   - ✅ `security.regenerate = false` (confirmed)

2. **`.env_production`** (Production)
   - ✅ `security.tokenName = 'csrf_test_name'` (was: `csrf_token_name`)
   - ✅ `security.tokenRandomize = false` (was: `true`)
   - ✅ `security.regenerate = false` (confirmed)

3. **`app/Views/layouts/base.php`**
   - ✅ Enhanced `getCsrfTokenData()` with 3-layer fallback (cookie → meta tag → empty)
   - ✅ Added debug logging for troubleshooting
   - ✅ Try-catch for Safari/Edge Tracking Prevention cookie blocking

4. **`public/assets/js/optima-datatable-config.js`**
   - ✅ Updated CSRF handling to use dynamic `getCsrfTokenData()`
   - ✅ Added debug logging for AJAX requests

5. **`app/Views/marketing/customer_management.php`**
   - ✅ Statistics AJAX updated to use `getCsrfTokenData()`
   - ✅ Window focus event CSRF refresh

#### **New Files Created:**
- 📄 `docs/CSRF_PRODUCTION_FIX.md` - Detailed fix documentation
- 📄 `public/test_csrf.php` - Configuration verification tool
- 📄 `check_production_env.bat` - Pre-deployment validation script
- 📄 `restart_apache.bat` - Apache restart helper

#### **Deployment Requirements:**
- ⚠️ **MUST restart Apache** after uploading `.env_production` to production
- ⚠️ **MUST clear cache**: `php spark cache:clear`
- ⚠️ **MUST clear sessions**: `rm -rf writable/session/*`
- ⚠️ **MUST test** with `test_csrf.php` on production

#### **Impact:**
- ✅ All AJAX POST requests now working (Customer Management, DataTables, etc.)
- ✅ Browser Tracking Prevention handled gracefully (Safari/Edge)
- ✅ Debug logging added for easier troubleshooting
- ⚠️ Requires Apache restart to load new `.env` config

---

## 🔄 Schema Changes (Database)

### **March 5, 2026 - Multi-Location Contract Restructure**

#### **REMOVED:**
- ❌ `kontrak.customer_location_id` - Removed from kontrak table
  - **Reason:** Contract should be umbrella (not tied to single location)
  - **Impact:** All code references updated

#### **ADDED:**
- ✅ `kontrak_unit.customer_location_id` - Added to kontrak_unit table
  - **Type:** INT, nullable
  - **Purpose:** Per-unit location tracking (multi-location support)
  - **Data:** All 1,992 records populated in development

- ✅ `kontrak_unit.is_spare` - Spare unit flag
  - **Type:** TINYINT(1)
  - **Default:** 0
  - **Purpose:** Mark spare units (not billed)

- ✅ `kontrak_unit.harga_sewa` - Per-unit custom rate
  - **Type:** DECIMAL(15,2), nullable
  - **Purpose:** Override default rate per unit

#### **MODIFIED:**
- 🔄 `invoices.contract_id` - Made nullable
  - **Purpose:** Support PO-only invoices (without contract)

- 🔄 `kontrak.nilai_total` - Made nullable  
  - **Purpose:** Computed field (from kontrak_unit)

### **March 5, 2026 - New Feature Tables**

#### **NEW TABLES:**

1. **`unit_audit_requests`** - Unit Audit Feature
   ```sql
   - Workflow: Service reports → Marketing approves
   - Purpose: Track unit location mismatches
   - Status: DRAFT, SUBMITTED, APPROVED, REJECTED
   - Fields: unit_id, customer_id, request_type, status
   ```

2. **`unit_movements`** - Movement/Surat Jalan Tracking
   ```sql
   - Purpose: Track unit movements between workshops
   - Features: Auto-generate Surat Jalan number
   - Status: DRAFT, IN_TRANSIT, ARRIVED, CANCELLED
   - Fields: unit_id, origin/destination, movement_date
   ```

---

## 💻 Code Changes

### **Controllers Updated:**

#### 1. **app/Controllers/Kontrak.php**
**Changes:**
- ❌ Removed all `customer_location_id` references
- ✅ Updated `getAvailableUnits()` - fixed JOINs, LIMIT 500
- ✅ Updated queries to use `kontrak.customer_id` directly
- ✅ Added `loadContractTotals()` endpoint for auto-calculation

**Files Modified:** 85+ instances across codebase

#### 2. **app/Controllers/Marketing.php**
**Changes:**
- ❌ Removed `customer_location_id` from getActiveContracts()
- ✅ Updated queries to JOIN via kontrak.customer_id
- ✅ Fixed SPK monitoring queries

**Fixes:** 16 instances

#### 3. **app/Controllers/UnitAudit.php** (NEW)
**Purpose:** Unit Audit feature
**Methods:**
- `index()` - Audit page
- `getCustomersWithUnits()` - Customer dropdown
- `createAuditRequest()` - Submit request
- `approveRequest()` - Marketing approval

**Lines:** 260

#### 4. **app/Controllers/Warehouse/UnitMovementController.php** (NEW)
**Purpose:** Surat Jalan / Movement tracking
**Methods:**
- `index()` - Movement list page
- `createMovement()` - Create movement
- `startMovement()` - Start transit
- `getMovements()` - DataTable data

**Lines:** 288

### **Views Updated:**

#### 1. **app/Views/marketing/kontrak_edit.php**
**Changes (March 6, 2026):**
- 🔒 Customer field: **DISABLED** (cannot change after creation)
- ❌ Location field: **REMOVED** (now tracked per-unit)
- 🔒 Contract Value: **READONLY** (auto-calculated from units)
- 🔒 Total Units: **READONLY** (auto-calculated)
- ✅ Added hidden input for customer_id submission
- ✅ Added JavaScript: `loadContractTotals()` function

#### 2. **app/Views/marketing/kontrak_detail.php**
**Changes (March 5-6, 2026):**
- ✅ Overview tab: **Default active** on page load
- ✅ Edit Unit modal: Currency formatting `formatRupiahInput()`
  - Input: 15000000 → Display: "Rp 15.000.000"
  - Live formatting on input
  - Parse back to number on submit

#### 3. **app/Views/components/add_unit_modal.php**
**Changes (March 5, 2026):**
- 🌍 **100% English translation**
  - "Tambah Unit" → "Add Unit to Contract"
  - "Lokasi Penempatan" → "Unit Location"
  - "Simpan Semua" → "Save All"
  - All labels, buttons, messages translated
- ✅ Show all 500 units (was 50)
- ✅ Fixed location dropdown (proper JOIN)
- ✅ Auto-trigger search on dropdown open

#### 4. **app/Views/service/unit_audit.php** (NEW)
**Purpose:** Unit Audit page
**Features:**
- Customer selection
- Unit list for customer
- Create audit request form
- Request history table

**Lines:** 612

#### 5. **app/Views/warehouse/unit_movement.php** (NEW)
**Purpose:** Surat Jalan / Movement page
**Features:**
- Movement stats cards
- Create movement modal
- Movement list table
- Surat Jalan generation

**Lines:** 623

### **Models Updated:**

#### 1. **app/Models/KontrakModel.php**
**Changes:**
- ❌ Removed `customer_location_id` from allowedFields
- ✅ Updated getWithDetails() queries
- ✅ Fixed JOINs to use kontrak.customer_id

#### 2. **app/Models/UnitAuditRequestModel.php** (NEW)
**Methods:**
- `generateAuditNumber()` - AUD-YYYYMMDD-NNNN
- `getWithDetails()` - List with JOINs
- `approveAndApply()` - Auto-apply changes
- `getStats()` - Dashboard stats

**Lines:** 327

#### 3. **app/Models/UnitMovementModel.php** (NEW)
**Methods:**
- `generateMovementNumber()` - MVYYYYMMDD-NNNN
- `generateSuratJalanNumber()` - SJYYYYMM-NNN
- `getWithUnitInfo()` - List with JOINs
- `getStats()` - Dashboard stats

**Lines:** 253

### **Routes Updated:**

#### **app/Config/Routes.php**
**Added:**
```php
// Unit Audit routes
$routes->get('unit_audit', 'UnitAudit::index');
$routes->post('unit_audit/createAuditRequest', 'UnitAudit::createAuditRequest');
$routes->get('unit_audit/getAuditRequests', 'UnitAudit::getAuditRequests');

// Unit Movement routes  
$routes->group('movements', function($routes) {
    $routes->get('/', 'Warehouse\UnitMovementController::index');
    $routes->post('createMovement', 'Warehouse\UnitMovementController::createMovement');
    $routes->get('getMovements', 'Warehouse\UnitMovementController::getMovements');
});
```

### **Layouts Updated:**

#### **app/Views/layouts/sidebar_new.php**
**Added:**
- ✅ Unit Audit menu (Service section)
- ✅ Surat Jalan menu (Warehouse section)

---

## 📊 Data Changes

### **Development Database (optima_ci):**

**Record Counts:**
- customers: 246
- customer_locations: 399
- kontrak: 676 (583 ACTIVE, 93 PENDING)
- kontrak_unit: 1,992 (ALL have customer_location_id)
- inventory_unit: 4,989
- unit_audit_requests: 0 (new table)
- unit_movements: 0 (new table)

**Data Integrity:**
- ✅ Orphaned kontrak_unit: 0
- ✅ Orphaned locations: 0
- ✅ Units with location: 1,992 (100%)

---

## 🎯 Impact Summary

### **Breaking Changes:**
1. ❌ `kontrak.customer_location_id` removed
   - **Fix:** Use `kontrak_unit.customer_location_id` instead
   - **Code:** 85+ instances updated

2. 🔄 Multi-location contracts now supported
   - 1 contract can have units at multiple customer locations
   - Location tracked per-unit, not per-contract

### **New Features:**
1. ✅ Unit Audit (Service → Marketing approval workflow)
2. ✅ Unit Movement (Surat Jalan tracking)
3. ✅ Contract edit improvements (disabled customer, readonly financials)
4. ✅ Currency formatting (Rp 15.000.000)
5. ✅ English translation (add unit modal)

### **Bug Fixes:**
1. ✅ Contract detail tab - Overview now default
2. ✅ Add unit modal - all units shown (500 vs 50)
3. ✅ Location dropdown - fixed JOIN query
4. ✅ Edit contract - customer immutable after creation

---

## 🚀 Deployment Requirements

### **Database Migrations (5 files):**
1. `2026-03-05_add_customer_location_id_to_kontrak_unit.sql`
2. `2026-03-05_contract_model_restructure.sql`
3. `2026-03-05_kontrak_unit_harga_spare.sql`
4. `2026-03-05_create_unit_audit_requests_table.sql`
5. `2026-03-05_create_unit_movements_table.sql`

### **Code Files (Controllers):**
- UnitAudit.php (NEW)
- UnitMovementController.php (NEW)
- Kontrak.php (UPDATED)
- Marketing.php (UPDATED)
- All other controllers with customer_location_id removed

### **Code Files (Views):**
- unit_audit.php (NEW)
- unit_movement.php (NEW)
- kontrak_edit.php (UPDATED)
- kontrak_detail.php (UPDATED)
- add_unit_modal.php (UPDATED)

### **Code Files (Models):**
- UnitAuditRequestModel.php (NEW)
- UnitMovementModel.php (NEW)
- KontrakModel.php (UPDATED)
- InventoryUnitModel.php (UPDATED)

### **Code Files (Other):**
- Routes.php (UPDATED)
- sidebar_new.php (UPDATED)

---

## ⚠️ Critical Notes

### **MUST DO in Production:**

1. **Populate customer_location_id**
   ```sql
   -- After migration, run this:
   UPDATE kontrak_unit ku
   INNER JOIN kontrak k ON ku.kontrak_id = k.id
   SET ku.customer_location_id = k.customer_location_id
   WHERE ku.customer_location_id IS NULL;
   ```

2. **Verify no orphaned records**
   ```sql
   -- Should return 0:
   SELECT COUNT(*) FROM kontrak_unit ku
   LEFT JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit
   WHERE iu.id_inventory_unit IS NULL;
   ```

3. **Test new features accessible**
   - /service/unit-audit
   - /warehouse/movements
   - /marketing/kontrak/edit

---

## 📅 Timeline

- **March 5, 2026:** Schema changes, multi-location support
- **March 6, 2026:** UI improvements, English translation, readonly fields
- **March 6, 2026:** Production deployment prepared

---

**Last Updated:** March 6, 2026  
**Status:** Ready for Production Deployment
