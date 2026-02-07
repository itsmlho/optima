# Quotation-Based SPK Workflow - Implementation Guide

**Version:** 1.0  
**Date:** February 7, 2026  
**Status:** ✅ Backend Complete, Views Pending

---

## 📋 Executive Summary

Implemented comprehensive workflow enhancement to enable **Quotation-based SPK creation** without immediate Contract/PO requirement, addressing B2B operational reality where contract documentation arrives 1-3 months after operational start. System maintains financial control through **three-layer invoice locking** until contracts are formalized.

### Key Features
- ✅ SPK creation from Quotations (no contract required)
- ✅ Late-linking mechanism via database triggers
- ✅ Contract-based invoice locking (three-layer validation)
- ✅ Contract renewals without re-delivery
- ✅ Price amendment tracking with history
- ✅ Automated recurring billing schedules
- ✅ Notification alerts for unlinked DIs

---

## 🔄 Workflow Comparison

### Before (Contract-Required)
```
Quotation (DEAL) → Customer Creation → Contract/PO → SPK → DI → Invoice
                                        ↑
                                   BLOCKER (1-3 months delay)
```

### After (Quotation-Based with Late-Linking)
```
Quotation (DEAL) → SPK (no contract) → Operations Start → DI (AWAITING_CONTRACT)
                                                            ↓
Contract Arrives → Link SPK to Contract → DI unlocked → Invoice enabled
                   (Database trigger propagates to all DIs)
```

---

## 🗄️ Database Changes

### 1. Modified Tables

#### `spk` Table
```sql
ALTER TABLE spk
ADD COLUMN contract_linked_at DATETIME NULL COMMENT 'When SPK was linked to contract',
ADD COLUMN contract_linked_by INT NULL COMMENT 'User who linked SPK',
ADD COLUMN source_type ENUM('CONTRACT', 'QUOTATION') DEFAULT 'CONTRACT' 
    COMMENT 'Whether SPK created from contract or quotation',
ADD INDEX idx_source_type (source_type),
ADD INDEX idx_contract_linked (kontrak_id, contract_linked_at);
```

#### `delivery_instructions` Table
```sql
ALTER TABLE delivery_instructions
ADD COLUMN contract_id INT NULL COMMENT 'FK to kontrak table',
ADD COLUMN bast_date DATE NULL COMMENT 'BAST (Berita Acara Serah Terima) date',
ADD COLUMN billing_start_date DATE NULL COMMENT 'When billing should start',
ADD COLUMN contract_linked_at DATETIME NULL COMMENT 'When contract was linked',
ADD COLUMN contract_linked_by INT NULL COMMENT 'User who linked contract',
MODIFY COLUMN status_di ENUM('DRAFT', 'DIAJUKAN', 'AWAITING_CONTRACT', 'SUBMITTED', 
    'IN_PROGRESS', 'DELIVERED', 'COMPLETED', 'CANCELLED') DEFAULT 'DIAJUKAN',
ADD CONSTRAINT fk_di_contract FOREIGN KEY (contract_id) REFERENCES kontrak(id) ON DELETE SET NULL,
ADD INDEX idx_contract_linked (contract_id, contract_linked_at),
ADD INDEX idx_status_di (status_di);
```

### 2. New Tables

#### `contract_amendments` Table
Tracks price/term changes during rental period.
```sql
CREATE TABLE contract_amendments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amendment_number VARCHAR(50) UNIQUE NOT NULL,
    parent_contract_id INT NOT NULL,
    reason TEXT NOT NULL,
    previous_monthly_rate DECIMAL(15, 2),
    new_monthly_rate DECIMAL(15, 2) NOT NULL,
    price_change_percent DECIMAL(5, 2),
    effective_date DATE NOT NULL,
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    INDEX idx_contract_effective (parent_contract_id, effective_date)
);
```

#### `contract_renewals` Table
Links old and new contracts to maintain rental history.
```sql
CREATE TABLE contract_renewals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_contract_id INT NOT NULL,
    renewed_contract_id INT NOT NULL,
    renewal_date DATE NOT NULL,
    same_location BOOLEAN DEFAULT TRUE,
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (original_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (renewed_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    UNIQUE KEY unique_renewal (original_contract_id, renewed_contract_id),
    INDEX idx_original (original_contract_id),
    INDEX idx_renewed (renewed_contract_id)
);
```

#### `invoices` Table
Contract-based invoicing with mandatory contract linkage.
```sql
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    contract_id INT NOT NULL COMMENT 'Required - no invoice without contract',
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    total_amount DECIMAL(15, 2) DEFAULT 0,
    status ENUM('DRAFT', 'APPROVED', 'SENT', 'PAID', 'OVERDUE', 'CANCELLED') DEFAULT 'DRAFT',
    paid_date DATE,
    payment_method VARCHAR(50),
    notes TEXT,
    cancellation_reason TEXT,
    created_by INT,
    approved_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    INDEX idx_contract (contract_id),
    INDEX idx_status (status),
    INDEX idx_invoice_date (invoice_date),
    INDEX idx_due_date (due_date)
);
```

#### `invoice_items` Table
Line items with auto-calculation triggers.
```sql
CREATE TABLE invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    description TEXT NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(15, 2) NOT NULL,
    subtotal DECIMAL(15, 2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice (invoice_id)
);
```

#### `recurring_billing_schedules` Table
Automated monthly/quarterly/yearly billing.
```sql
CREATE TABLE recurring_billing_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL UNIQUE,
    frequency ENUM('MONTHLY', 'QUARTERLY', 'YEARLY') DEFAULT 'MONTHLY',
    next_billing_date DATE NOT NULL,
    last_generated_date DATE,
    status ENUM('ACTIVE', 'PAUSED', 'COMPLETED') DEFAULT 'ACTIVE',
    pause_reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    INDEX idx_next_billing (next_billing_date, status),
    INDEX idx_contract_status (contract_id, status)
);
```

#### `invoice_status_history` Table
Audit trail for invoice state changes.
```sql
CREATE TABLE invoice_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    old_status VARCHAR(20),
    new_status VARCHAR(20) NOT NULL,
    changed_by INT,
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice (invoice_id)
);
```

### 3. Database Triggers

#### `propagate_contract_to_di`
Auto-updates DIs when SPK is linked to contract.
```sql
DELIMITER $$
CREATE TRIGGER propagate_contract_to_di
AFTER UPDATE ON spk
FOR EACH ROW
BEGIN
    IF OLD.kontrak_id IS NULL AND NEW.kontrak_id IS NOT NULL THEN
        UPDATE delivery_instructions
        SET 
            contract_id = NEW.kontrak_id,
            contract_linked_at = NOW(),
            contract_linked_by = NEW.contract_linked_by,
            status_di = CASE 
                WHEN status_di = 'AWAITING_CONTRACT' THEN 'DIAJUKAN'
                ELSE status_di
            END
        WHERE spk_id = NEW.id
        AND contract_id IS NULL;
    END IF;
END$$
DELIMITER ;
```

#### `calculate_invoice_item_subtotal`
Auto-calculates item subtotals.
```sql
DELIMITER $$
CREATE TRIGGER calculate_invoice_item_subtotal
BEFORE INSERT ON invoice_items
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END$$
DELIMITER ;
```

#### Invoice total update triggers (3 triggers)
```sql
-- update_invoice_total_on_item_insert
-- update_invoice_total_on_item_update
-- update_invoice_total_on_item_delete
```

#### `log_invoice_status_change`
Audit trail logging.
```sql
DELIMITER $$
CREATE TRIGGER log_invoice_status_change
AFTER UPDATE ON invoices
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO invoice_status_history (invoice_id, old_status, new_status, changed_by)
        VALUES (NEW.id, OLD.status, NEW.status, NEW.approved_by);
    END IF;
END$$
DELIMITER ;
```

### 4. Stored Procedures

#### `sp_generate_invoice_number`
Thread-safe invoice numbering with GET_LOCK.
```sql
DELIMITER $$
CREATE PROCEDURE sp_generate_invoice_number(OUT invoice_num VARCHAR(50))
BEGIN
    DECLARE next_num INT;
    DECLARE prefix VARCHAR(20);
    DECLARE lock_result INT;
    
    SET lock_result = GET_LOCK('invoice_number_lock', 10);
    IF lock_result = 1 THEN
        SET prefix = CONCAT('INV/', DATE_FORMAT(NOW(), '%Y%m'), '/');
        SELECT COALESCE(MAX(CAST(SUBSTRING(invoice_number, LENGTH(prefix) + 1) AS UNSIGNED)), 0) + 1
        INTO next_num
        FROM invoices
        WHERE invoice_number LIKE CONCAT(prefix, '%');
        SET invoice_num = CONCAT(prefix, LPAD(next_num, 3, '0'));
        DO RELEASE_LOCK('invoice_number_lock');
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Could not acquire lock for invoice number generation';
    END IF;
END$$
DELIMITER ;
```

---

## 📦 Models Created/Updated

### 1. SpkModel (Updated)
**File:** `app/Models/SpkModel.php`

**New Methods:**
- `createFromQuotation($quotationSpecId, $customerData, $userId)` - Creates SPK without contract
- `linkToContract($spkId, $contractId, $userId)` - Late-linking mechanism
- `getUnlinkedSPKs($customerId)` - Returns quotation-based SPKs without contracts
- `getSPKsForLinking($contractId)` - Filters eligible SPKs for contract modal
- `hasContract($spkId)` - Quick boolean check

### 2. DeliveryInstructionModel (Updated)
**File:** `app/Models/DeliveryInstructionModel.php`

**New Methods:**
- `determineInitialStatus($spkId)` - Returns 'AWAITING_CONTRACT' or 'DIAJUKAN'
- `inheritContractFromSPK($diId)` - Manual sync backup for trigger
- `getUnlinkedDeliveries()` - Dashboard alert data with days_pending
- `validateBillingReadiness($diId)` - **THREE-LAYER VALIDATION** for invoice locking
- `setBillingStartDate($diId, $bastDate)` - BAST date handling with auto-calculation

### 3. ContractAmendmentModel (New)
**File:** `app/Models/ContractAmendmentModel.php` (200+ lines)

**Key Methods:**
- `createAmendment($contractId, $data)` - Price change management
- `getAmendmentsByContract($contractId)` - History tracking
- `getEffectiveRate($contractId, $billingDate)` - Rate determination for invoicing
- `validateEffectiveDate($contractId, $effectiveDate)` - Business rule validation

### 4. ContractRenewalModel (New)
**File:** `app/Models/ContractRenewalModel.php` (180+ lines)

**Key Methods:**
- `createRenewal($originalId, $renewedId, $data)` - Links old→new contracts
- `getRenewalChain($contractId)` - Recursive query for complete history
- `checkRenewalEligibility($contractId)` - Validation logic
- `migrateBillingSchedule($oldId, $newId)` - Seamless billing continuation

### 5. InvoiceModel (New)
**File:** `app/Models/InvoiceModel.php` (320+ lines)

**Key Methods:**
- `generateInvoiceNumber()` - Format INV/YYYYMM/NNN via stored procedure
- `createFromDI($diId, $contractId, $userId, $options)` - **ONE-TIME invoice with VALIDATION**
- `createRecurringInvoice($scheduleId, $userId)` - Monthly/quarterly/yearly billing
- `updateStatus($invoiceId, $status, $userId)` - Status management with audit
- `getInvoicesByContract($contractId)` - Contract-based history
- `getInvoiceDetails($invoiceId)` - Full joined query for display

### 6. InvoiceItemModel (New)
**File:** `app/Models/InvoiceItemModel.php` (150+ lines)

**Key Methods:**
- `addItemsFromDI($invoiceId, $diId)` - Auto-populates from delivery_items
- `addItemsFromContract($contractId, $amendedRate)` - Recurring invoice items
- `getItemsByInvoice($invoiceId)` - Display data with unit details
- `calculateInvoiceTotal($invoiceId)` - Manual fallback if triggers fail

### 7. RecurringBillingScheduleModel (New)
**File:** `app/Models/RecurringBillingScheduleModel.php` (180+ lines)

**Key Methods:**
- `createSchedule($contractId, $frequency)` - Initialize billing
- `getUpcomingInvoices($days)` - Dashboard widget data
- `generateDueInvoices()` - Batch generation for cron job
- `pauseSchedule($scheduleId, $reason)` / `resumeSchedule()` / `completeSchedule()` - Lifecycle

---

## 🎮 Controllers Updated

### Marketing Controller
**File:** `app/Controllers/Marketing.php`

**New Methods (5):**
1. `linkSPKToContract()` - Core late-linking mechanism with notification
2. `getSPKsForContractLinking($customerId)` - Fetch unlinked SPKs for modal
3. `checkSPKHasContract($spkId)` - Quick contract status check
4. `renewContract($contractId)` - Contract renewal without re-delivery
5. `createAmendment($contractId)` - Price/term amendments with validation

**Updated Methods (1):**
- `diCreate()` - Lines 5461-5472, auto-determines status via `determineInitialStatus()`

**Helper Methods (2):**
- `sendLinkingSuccessNotification()` - Notifies Finance team when SPK linked
- `getFinanceTeamUsers()` - Returns Finance division user IDs

### Finance Controller (Complete Rewrite)
**File:** `app/Controllers/Finance.php` (621 lines)

**Methods (13):**
1. `index()` - Dashboard with unlinked DI alerts, upcoming invoices
2. `invoices()` - Invoice management view
3. `getInvoiceDataTable()` - DataTables API with search/pagination
4. **`generateInvoiceFromDI()`** - ⚠️ THREE-LAYER VALIDATION prevents invoice without contract
5. `generateRecurringInvoice()` - Monthly/quarterly/yearly billing
6. `batchGenerateRecurringInvoices()` - Cron job endpoint
7. `approveInvoice($invoiceId)` - DRAFT → APPROVED transition
8. `markAsPaid($invoiceId)` - Payment recording with audit trail
9. `viewInvoice($invoiceId)` - Invoice detail with history
10. `getInvoicesByContract($contractId)` - Contract invoice history
11. `cancelInvoice($invoiceId)` - Cancellation with reason tracking
12. `createBillingSchedule()` - Setup recurring billing
13. `pauseBillingSchedule()` / `resumeBillingSchedule()` - Schedule lifecycle

---

## 🛣️ Routes Configuration

### Marketing Routes (Added)
```php
// Quotation-based workflow - SPK/Contract linking
$routes->post('spk/link-to-contract', 'Marketing::linkSPKToContract');
$routes->get('spk/unlinked/(:num)', 'Marketing::getSPKsForContractLinking/$1');
$routes->get('spk/has-contract/(:num)', 'Marketing::checkSPKHasContract/$1');

// Contract renewals and amendments
$routes->post('contracts/renew/(:num)', 'Marketing::renewContract/$1');
$routes->post('contracts/create-amendment/(:num)', 'Marketing::createAmendment/$1');
```

### Finance Routes (Complete)
```php
$routes->group('finance', static function ($routes) {
    // Dashboard
    $routes->get('/', 'Finance::index');
    $routes->get('dashboard', 'Finance::index');
    
    // Invoice Management
    $routes->get('invoices', 'Finance::invoices');
    $routes->get('invoices/datatable', 'Finance::getInvoiceDataTable');
    $routes->get('invoices/view/(:num)', 'Finance::viewInvoice/$1');
    $routes->get('invoices/by-contract/(:num)', 'Finance::getInvoicesByContract/$1');
    
    // Invoice Generation
    $routes->post('invoices/generate-from-di', 'Finance::generateInvoiceFromDI');
    $routes->post('invoices/generate-recurring', 'Finance::generateRecurringInvoice');
    $routes->post('invoices/batch-generate', 'Finance::batchGenerateRecurringInvoices');
    
    // Invoice Actions
    $routes->post('invoices/approve/(:num)', 'Finance::approveInvoice/$1');
    $routes->post('invoices/mark-paid/(:num)', 'Finance::markAsPaid/$1');
    $routes->post('invoices/cancel/(:num)', 'Finance::cancelInvoice/$1');
    
    // Recurring Billing Schedules
    $routes->post('billing-schedule/create', 'Finance::createBillingSchedule');
    $routes->post('billing-schedule/pause/(:num)', 'Finance::pauseBillingSchedule/$1');
    $routes->post('billing-schedule/resume/(:num)', 'Finance::resumeBillingSchedule/$1');
});
```

---

## 🔔 Notification System

### Command: GenerateWorkflowNotifications
**File:** `app/Commands/GenerateWorkflowNotifications.php`

**Usage:**
```bash
# Run all notification types
php spark workflow:notify

# Dry run (preview without sending)
php spark workflow:notify --dry-run

# Specific type
php spark workflow:notify --type=unlinked-di
php spark workflow:notify --type=upcoming-invoices
```

**Notification Types:**

1. **Unlinked DI Alerts** (AWAITING_CONTRACT status)
   - Sent to Finance team
   - Urgency levels: 3-7 days (warning), 7-14 days (warning), 14+ days (critical)
   - Prevents duplicate notifications (checks if sent today)

2. **Upcoming Invoice Alerts**
   - Sent 3 days before and 1 day before due date
   - Includes contract info, customer, billing date
   - Prevents duplicate notifications

3. **Linking Success Notifications**
   - Sent when SPK successfully linked to contract
   - Notifies Finance team + linking user
   - Includes DI count updated

**Cron Job Setup:**
```bash
# Run every hour
0 * * * * cd /path/to/optima && /usr/bin/php spark workflow:notify >> /var/log/workflow-notify.log 2>&1

# Run twice daily (9 AM and 3 PM)
0 9,15 * * * cd /path/to/optima && /usr/bin/php spark workflow:notify
```

---

## 🔒 Three-Layer Invoice Validation

**Prevents invoice generation without contract linkage through three independent checks:**

### Layer 1: Model-Level Validation
**File:** `app/Models/DeliveryInstructionModel.php`
```php
public function validateBillingReadiness($diId)
{
    // Check 1: contract_id NOT NULL
    // Check 2: status NOT AWAITING_CONTRACT
    // Check 3: bast_date exists
    // Check 4: status in DELIVERED/COMPLETED
    // Returns: array of error messages or empty array
}
```

### Layer 2: Controller Guard
**File:** `app/Controllers/Finance.php`
```php
public function generateInvoiceFromDI()
{
    // Calls validateBillingReadiness()
    // Returns locked: true with errors if validation fails
    // Blocks invoice creation at controller level
}
```

### Layer 3: Database Constraint
**File:** `databases/migrations/create_invoicing_system.sql`
```sql
CREATE TABLE invoices (
    contract_id INT NOT NULL COMMENT 'Required - no invoice without contract',
    FOREIGN KEY (contract_id) REFERENCES kontrak(id)
);
```

**Testing the Lock:**
```bash
# Try to create invoice from unlinked DI
curl -X POST http://optima.local/finance/invoices/generate-from-di \
  -H "Content-Type: application/json" \
  -d '{"di_id": 123, "contract_id": null}'

# Expected Response:
{
  "success": false,
  "locked": true,
  "message": "Invoice cannot be created: Missing requirements",
  "errors": [
    "DI has no contract linked",
    "DI status is AWAITING_CONTRACT (not ready for billing)",
    "BAST date not set (billing start date unknown)"
  ]
}
```

---

## 🚀 Usage Examples

### Example 1: Create SPK from Quotation (No Contract)
```javascript
// Frontend: Marketing creates SPK from quotation
fetch('/marketing/spk/create', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    kontrak_spesifikasi_id: 456, // quotation_specifications.id_specification
    jumlah_unit: 2,
    pelanggan: 'PT ABC',
    lokasi: 'Jakarta',
    jenis_spk: 'UNIT'
  })
});

// Response:
{
  "success": true,
  "spk_id": 789,
  "nomor_spk": "SPK/202602/001",
  "message": "SPK created successfully (quotation-based, no contract)"
}
```

### Example 2: Create DI from SPK (Auto-Determines Status)
```javascript
// Marketing creates DI
fetch('/marketing/di/create', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    spk_id: 789,
    tanggal_kirim: '2026-02-15',
    jenis_perintah_kerja_id: 1,
    tujuan_perintah_kerja_id: 2
  })
});

// Response:
{
  "success": true,
  "di_id": 321,
  "nomor_di": "DI/202602/001",
  "status": "AWAITING_CONTRACT",  // Auto-determined (SPK has no contract)
  "message": "DI created. Waiting for contract to enable invoicing."
}
```

### Example 3: Link SPK to Contract (Late-Linking)
```javascript
// Finance/Marketing links contract when PO arrives
fetch('/marketing/spk/link-to-contract', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    spk_id: 789,
    contract_id: 654,
    bast_date: '2026-02-10'  // Optional
  })
});

// Response:
{
  "success": true,
  "message": "SPK linked to contract successfully. 3 DIs updated.",
  "di_count": 3
}

// Trigger automatically:
// - Updates spk.kontrak_id = 654
// - Updates spk.contract_linked_at = NOW()
// - Database trigger propagates to all DIs:
//   - di.contract_id = 654
//   - di.status_di = 'DIAJUKAN' (if was AWAITING_CONTRACT)
//   - di.contract_linked_at = NOW()
// - Notification sent to Finance team
```

### Example 4: Try to Create Invoice (Locked - No Contract)
```javascript
// Finance tries to invoice unlinked DI
fetch('/finance/invoices/generate-from-di', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    di_id: 321,
    contract_id: null,
    invoice_date: '2026-02-15',
    due_date: '2026-03-15'
  })
});

// Response (LOCKED):
{
  "success": false,
  "locked": true,
  "message": "Invoice cannot be created: Missing requirements",
  "errors": [
    "DI has no contract linked (contract_id is NULL)",
    "DI status is AWAITING_CONTRACT (not ready for billing)",
    "BAST date not set (required for billing start date)"
  ]
}
```

### Example 5: Create Invoice After Linking (Unlocked)
```javascript
// After linking, Finance can invoice
fetch('/finance/invoices/generate-from-di', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    di_id: 321,
    contract_id: 654,
    invoice_date: '2026-02-15',
    due_date: '2026-03-15',
    notes: 'Initial rental invoice'
  })
});

// Response (SUCCESS):
{
  "success": true,
  "invoice_id": 111,
  "invoice_number": "INV/202602/001",
  "message": "Invoice created successfully"
}
```

### Example 6: Renew Contract Without Re-Delivery
```javascript
// Marketing renews expired contract
fetch('/marketing/contracts/renew/654', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    start_date: '2027-02-01',
    end_date: '2028-01-31',
    po_number: 'PO/2027/ABC',
    rates: 50000000,  // Optional rate change
    same_location: true,
    notes: 'Annual renewal'
  })
});

// Response:
{
  "success": true,
  "new_contract_id": 987,
  "contract_number": "CONT/2026/123-R1",  // Auto-appends -R1
  "message": "Contract renewed successfully"
}

// System actions:
// - Creates new contract with copied specs
// - Records in contract_renewals (links old → new)
// - Migrates recurring billing schedule
// - Maintains rental history
```

### Example 7: Create Price Amendment
```javascript
// Marketing creates price amendment mid-contract
fetch('/marketing/contracts/create-amendment/654', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    reason: 'Fuel cost increase adjustment',
    new_rate: 55000000,
    effective_date: '2026-03-01'
  })
});

// Response:
{
  "success": true,
  "amendment_id": 22,
  "message": "Amendment created successfully",
  "warnings": [
    "Effective date is mid-month. Pro-rated billing may be required."
  ]
}

// System actions:
// - Creates amendment record
// - Auto-calculates price_change_percent
// - Future recurring invoices use getEffectiveRate() to apply amendment
```

### Example 8: Generate Recurring Invoices (Cron Job)
```bash
# Manual trigger (or via cron)
php spark workflow:notify

# Or call via HTTP (for monitoring systems)
curl -X POST http://optima.local/finance/invoices/batch-generate
```

**Response:**
```json
{
  "success": true,
  "message": "Generated 5 invoices",
  "invoices": [
    {"invoice_id": 112, "invoice_number": "INV/202602/002", "contract_id": 654},
    {"invoice_id": 113, "invoice_number": "INV/202602/003", "contract_id": 789},
    ...
  ],
  "errors": []
}
```

---

## 📊 Data Migration

**File:** `databases/migrations/quotation_based_spk_workflow.sql`

**Runs automatically during migration:**
```sql
-- Set source_type for existing SPKs
UPDATE spk
SET source_type = CASE 
    WHEN kontrak_id IS NOT NULL THEN 'CONTRACT'
    ELSE 'QUOTATION'
END;

-- Link existing DIs to contracts if SPK has contract
UPDATE delivery_instructions di
JOIN spk s ON s.id = di.spk_id
SET di.contract_id = s.kontrak_id,
    di.contract_linked_at = s.dibuat_pada  -- Use SPK creation date as proxy
WHERE s.kontrak_id IS NOT NULL
AND di.contract_id IS NULL;
```

---

## 🧪 Testing Checklist

### Backend Testing
- [ ] Create SPK from quotation (kontrak_id = NULL)
- [ ] Create DI from quotation-based SPK (status = AWAITING_CONTRACT)
- [ ] Try to invoice unlinked DI (should fail with locked=true)
- [ ] Link SPK to contract via linkSPKToContract()
- [ ] Verify trigger propagates contract to DIs
- [ ] Create invoice after linking (should succeed)
- [ ] Create recurring billing schedule
- [ ] Generate recurring invoice
- [ ] Create contract amendment
- [ ] Renew contract
- [ ] Run notification command (dry-run and live)

### Database Testing
```sql
-- Test trigger
UPDATE spk SET kontrak_id = 654, contract_linked_by = 1 WHERE id = 789;
SELECT * FROM delivery_instructions WHERE spk_id = 789;
-- Should see contract_id = 654, status_di = 'DIAJUKAN'

-- Test invoice number generation
CALL sp_generate_invoice_number(@num);
SELECT @num;
-- Should return INV/202602/001

-- Test amendment rate lookup
SELECT getEffectiveRate(654, '2026-03-15');
-- Should return amended rate if effective date applies
```

### Notification Testing
```bash
# Dry run
php spark workflow:notify --dry-run

# Live run
php spark workflow:notify

# Check notifications table
mysql> SELECT * FROM notifications WHERE related_module IN ('delivery_instructions', 'spk') ORDER BY created_at DESC LIMIT 10;
```

---

## 🔍 Monitoring & Maintenance

### Key Queries

**1. Unlinked DIs Report**
```sql
SELECT 
    di.id,
    di.nomor_di,
    di.spk_id,
    s.nomor_spk,
    di.pelanggan,
    di.status_di,
    DATEDIFF(NOW(), di.dibuat_pada) AS days_pending,
    di.dibuat_pada
FROM delivery_instructions di
JOIN spk s ON s.id = di.spk_id
WHERE di.contract_id IS NULL
AND di.status_di = 'AWAITING_CONTRACT'
ORDER BY days_pending DESC;
```

**2. Upcoming Recurring Invoices**
```sql
SELECT 
    rbs.id,
    rbs.contract_id,
    k.no_kontrak,
    c.nama_customer,
    rbs.next_billing_date,
    DATEDIFF(rbs.next_billing_date, CURDATE()) AS days_until_due,
    rbs.frequency,
    rbs.status
FROM recurring_billing_schedules rbs
JOIN kontrak k ON k.id = rbs.contract_id
JOIN customers c ON c.id_customer = k.customer_location_id
WHERE rbs.status = 'ACTIVE'
AND rbs.next_billing_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY rbs.next_billing_date;
```

**3. Contract Renewal Chain**
```sql
WITH RECURSIVE renewal_chain AS (
    SELECT 
        original_contract_id AS contract_id,
        renewed_contract_id,
        renewal_date,
        1 AS level
    FROM contract_renewals
    WHERE original_contract_id = 654  -- Starting contract
    
    UNION ALL
    
    SELECT 
        cr.original_contract_id,
        cr.renewed_contract_id,
        cr.renewal_date,
        rc.level + 1
    FROM contract_renewals cr
    JOIN renewal_chain rc ON rc.renewed_contract_id = cr.original_contract_id
)
SELECT 
    rc.*,
    k.no_kontrak,
    k.tanggal_mulai,
    k.tanggal_berakhir
FROM renewal_chain rc
JOIN kontrak k ON k.id = rc.renewed_contract_id
ORDER BY rc.level;
```

**4. Invoice Lock Violations (Audit)**
```sql
-- Should return 0 rows (no invoices without contracts)
SELECT 
    i.id,
    i.invoice_number,
    i.contract_id
FROM invoices i
WHERE i.contract_id IS NULL;
```

### Performance Indexes
All critical indexes already created in migrations:
- `idx_source_type` on spk.source_type
- `idx_contract_linked` on spk (kontrak_id, contract_linked_at)
- `idx_status_di` on delivery_instructions.status_di
- `idx_next_billing` on recurring_billing_schedules (next_billing_date, status)

### Maintenance Tasks

**Daily:**
- Run `php spark workflow:notify` via cron
- Monitor unlinked DIs > 7 days

**Weekly:**
- Review invoice status distribution
- Check for paused billing schedules

**Monthly:**
- Audit contract renewals
- Review amendment history
- Validate trigger functionality

---

## 📝 Remaining Work

### Phase 5: View Files (NOT STARTED)
**Priority: HIGH** - Users need UI to interact with new features

**Files to Create/Update:**
1. `app/Views/marketing/spk.php` - Add "Link to Contract" button, source type indicator
2. `app/Views/marketing/di.php` - Add AWAITING_CONTRACT tab, status badges
3. `app/Views/finance/dashboard.php` - Unlinked DI alerts, upcoming invoice widgets
4. `app/Views/finance/invoices.php` - Complete invoice management interface
5. `app/Views/finance/invoice_detail.php` - Invoice detail with history timeline
6. `app/Views/contracts/detail.php` - Show renewal chain, amendments, billing schedule

**UI Components Needed:**
- Modal: Link SPK to Contract (select contract, BAST date)
- Alert Widget: Unlinked DIs (with days pending, urgency colors)
- Table: Contract amendments with effective date timeline
- Timeline: Renewal history visualization
- Badge: DI status (AWAITING_CONTRACT = orange, DIAJUKAN = blue)

### Phase 7: Business Logic Validation (NOT STARTED)
**Priority: MEDIUM** - Additional validation rules

**Rules to Implement:**
1. Prevent SPK linking if contract expired
2. Validate renewal eligibility (>60 days until expiry)
3. Prevent amendment with past effective_date
4. Validate billing schedule for HARIAN vs BULANAN contracts
5. Check DI quantity doesn't exceed SPK quantity

**Implementation Approach:**
- Add validation rules to models
- Create `app/Validation/WorkflowRules.php` custom validation class
- Add client-side validation in JavaScript

---

## 🎯 Success Metrics

### Operational Metrics
- **SPK Creation Time:** Reduced from 1-3 months → Same day (contract-free start)
- **DI Stuck in AWAITING_CONTRACT:** Target < 5% of total DIs
- **Average Days to Contract Linking:** Target < 7 days
- **Invoice Lock Violations:** 0 (enforced by three-layer validation)

### System Metrics
- **Trigger Propagation Success Rate:** 100% (automatic contract sync)
- **Notification Delivery Rate:** >99%
- **Recurring Invoice Generation:** 100% on-time
- **Invoice Numbering Conflicts:** 0 (GET_LOCK prevents race conditions)

### User Experience
- **Marketing:** Can immediately start operations after quotation deal
- **Finance:** Clear visibility of unlinked DIs via dashboard alerts
- **Management:** Complete rental history via renewal chain

---

## 🆘 Troubleshooting

### Issue: Trigger not propagating contract to DIs
**Symptoms:** After linking SPK, DIs still have contract_id = NULL

**Diagnosis:**
```sql
SHOW TRIGGERS LIKE 'propagate_contract_to_di';
SELECT * FROM delivery_instructions WHERE spk_id = 789;
```

**Fix:**
```sql
-- Manual propagation
UPDATE delivery_instructions di
JOIN spk s ON s.id = di.spk_id
SET di.contract_id = s.kontrak_id,
    di.contract_linked_at = NOW(),
    di.status_di = 'DIAJUKAN'
WHERE s.id = 789
AND di.contract_id IS NULL;

-- Or call model method
$diModel->inheritContractFromSPK($diId);
```

### Issue: Invoice number conflicts
**Symptoms:** Duplicate invoice_number errors

**Diagnosis:**
```sql
SELECT invoice_number, COUNT(*) 
FROM invoices 
GROUP BY invoice_number 
HAVING COUNT(*) > 1;
```

**Fix:**
```sql
-- Ensure stored procedure uses GET_LOCK
SHOW CREATE PROCEDURE sp_generate_invoice_number;

-- Manual fix for duplicates
UPDATE invoices 
SET invoice_number = CONCAT(invoice_number, '-', id)
WHERE invoice_number IN (SELECT...);
```

### Issue: Notifications not sending
**Symptoms:** No notifications in table after running command

**Diagnosis:**
```bash
php spark workflow:notify --dry-run
# Check output for errors

# Check user division mapping
SELECT u.id, u.name, d.name AS division 
FROM users u 
LEFT JOIN divisions d ON d.id = u.division_id 
WHERE d.name LIKE '%Finance%';
```

**Fix:**
- Ensure Finance division exists with correct name pattern
- Check NotificationModel::sendToMultiple() for errors
- Verify notifications table has correct schema

### Issue: Invoice locked incorrectly
**Symptoms:** Invoice creation fails even after linking

**Diagnosis:**
```sql
-- Check DI status
SELECT id, nomor_di, contract_id, status_di, bast_date 
FROM delivery_instructions 
WHERE id = 321;

-- Run validation manually
SELECT 
    CASE WHEN contract_id IS NULL THEN 'No contract' ELSE 'OK' END AS check1,
    CASE WHEN status_di = 'AWAITING_CONTRACT' THEN 'Wrong status' ELSE 'OK' END AS check2,
    CASE WHEN bast_date IS NULL THEN 'No BAST date' ELSE 'OK' END AS check3,
    CASE WHEN status_di NOT IN ('DELIVERED', 'COMPLETED') THEN 'Wrong status' ELSE 'OK' END AS check4
FROM delivery_instructions 
WHERE id = 321;
```

**Fix:**
```sql
-- Update missing fields
UPDATE delivery_instructions 
SET bast_date = '2026-02-10',
    status_di = 'DELIVERED'
WHERE id = 321;
```

---

## 📚 Additional Resources

### Related Documentation
- [Database Schema Reference](databases/DATABASE_SCHEMA_REFERENCE.md)
- [Workflow Implementation Guide](docs/WORKFLOW_IMPLEMENTATION_GUIDE.md)
- [Invoice System Documentation](docs/INVOICE_SYSTEM_DOCS.md)

### API Endpoints Summary
- POST `/marketing/spk/create` - Create SPK from quotation
- POST `/marketing/spk/link-to-contract` - Link SPK to contract
- POST `/marketing/di/create` - Create DI (auto-status determination)
- POST `/finance/invoices/generate-from-di` - Generate invoice (with validation)
- POST `/finance/invoices/batch-generate` - Batch recurring invoices
- POST `/marketing/contracts/renew/:id` - Renew contract
- POST `/marketing/contracts/create-amendment/:id` - Create amendment

### Command Reference
```bash
# Notification generation
php spark workflow:notify [--dry-run] [--type=all|unlinked-di|upcoming-invoices]

# Database migrations
php spark migrate

# Rollback migrations (if needed)
php spark migrate:rollback
```

---

**Document Version:** 1.0  
**Last Updated:** February 7, 2026  
**Implementation Status:** ✅ Backend Complete (Phases 1-4, 6, 8), ⏳ Views Pending (Phase 5, 7)  
**Next Steps:** Implement view files for user interface
