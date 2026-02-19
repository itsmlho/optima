# Testing & Verification Guide
## Optima ERP - Marketing Module Enhancement Implementation

**Date:** February 19, 2026  
**Version:** 1.0

---

## Overview

This document provides a comprehensive testing checklist for all features implemented during the marketing module enhancement project. Follow these steps sequentially to verify that all systems are working correctly.

---

## Pre-Testing Setup

### 1. Database Migrations
Run all database migrations to ensure schema is up-to-date:

```bash
# Navigate to database migrations folder
cd c:\laragon\www\optima\databases\migrations

# Run invoice automation migration
mysql -u root -p optima < 2026-02-19_add_invoice_generated_flag.sql

# Run customer conversion migration
mysql -u root -p optima < 2026-02-19_add_customer_converted_at_column.sql

# Verify columns were added
mysql -u root -p optima -e "DESCRIBE delivery_instructions;" | grep invoice_generated
mysql -u root -p optima -e "DESCRIBE quotations;" | grep customer_converted_at
```

### 2. Environment Configuration
Update `.env` file with required email addresses:

```env
# Email Configuration for Invoice Automation
ACC_EMAIL_1=finance@sml.co.id
ACC_EMAIL_2=anselin_smlforklift@yahoo.com
MARKETING_EMAIL=marketing@sml.co.id

# SMTP Configuration (if not already set)
email.protocol=smtp
email.SMTPHost=your-smtp-server.com
email.SMTPPort=587
email.SMTPUser=your-email@company.com
email.SMTPPass=your-password
email.SMTPCrypto=tls
```

### 3. Cron Job Setup (Production)
Configure the invoice automation cron job:

**Linux/Unix:**
```bash
crontab -e
# Add this line:
0 1 * * * cd /path/to/optima && php spark jobs:invoice-automation >> /path/to/optima/writable/logs/cron-invoice-automation.log 2>&1
```

**Windows Task Scheduler:**
See `databases/CRON_CONFIGURATION.txt` for detailed instructions.

---

## Phase 1: UI/UX Testing

### Test 1.1: Modal Sizing Standardization ✅

**Objective:** Verify all modals use the new `.modal-wide` class (85vw width)

**Steps:**
1. Navigate to Marketing → Quotations
2. Open any quotation detail modal
3. Verify modal width is 85% of viewport width (85vw)
4. Repeat for:
   - Customer Management detail modal
   - Contract (Kontrak) detail modal
   - SPK detail modal
   - DI detail modal
   - Invoice detail modal

**Expected Result:** All modals should be consistently wide (85vw) without horizontal scrollbars.

---

### Test 1.2: Direct Contract Creation UI ✅

**Objective:** Verify contract can be created directly without quotation

**Steps:**
1. Navigate to Marketing → Kontrak
2. Click the **"Create Contract"** button (replaces old "Create New" button)
3. Fill in the modal form:
   - Contract number: Auto-generated or manual
   - Customer: Select from dropdown (loads from customer_management table)
   - Location: Cascading dropdown based on customer selection
   - Contract type: CONTRACT, PO_ONLY, CONTRACT_AND_PO, or DAILY_SPOT
   - Start date, end date, rate details
4. Click **"Save Contract"**

**Expected Result:**
- Contract successfully created without requiring quotation_id
- Redirects to contract detail page
- Contract appears in Kontrak datatable

**Error Scenarios to Test:**
- Missing required fields → Should show validation errors
- Duplicate contract number → Should show error message
- Invalid date range (end before start) → Should prevent submission

---

### Test 1.3: Customer Management Separation ✅

**Objective:** Verify customer management page only shows view-only contracts

**Steps:**
1. Navigate to Marketing → Customer Management
2. Open any customer detail modal
3. Go to "Contracts & PO" tab
4. Verify NO "Add Contract" button exists
5. Verify only "Manage Contracts" button that redirects to Kontrak page
6. Click on any contract row → Opens detail modal in READ-ONLY mode
7. Verify action dropdown shows ONLY:
   - **View Detail (Read-Only)**
   - **Manage in Kontrak Page** (redirect link)
8. Verify NO edit, delete, renew, or change rate options in customer management

**Expected Result:**
- Customer management = VIEW ONLY for contracts
- All CRUD operations redirected to Kontrak page
- Dynamic redirect link: `marketing/kontrak?customer={id}`

**Test Kontrak Page Full CRUD:**
1. Navigate to Marketing → Kontrak
2. Filter by customer
3. Verify ALL contract actions available:
   - Edit contract
   - Delete contract
   - Renew contract (opens renewal wizard)
   - Change rate/amendment (opens amendment modal)
   - View history
   - Link to SPK

---

## Phase 2: Invoice Automation Testing

### Test 2.1: CLI Command Dry-Run ✅

**Objective:** Verify CLI command works in preview mode without generating invoices

**Steps:**
```bash
# Navigate to project root
cd c:\laragon\www\optima

# Run dry-run to preview eligible DIs
php spark jobs:invoice-automation --dry-run

# Run with verbose output
php spark jobs:invoice-automation --dry-run --verbose
```

**Expected Result:**
- Shows table of eligible delivery instructions
- Displays DI number, customer, contract number, completed date, days elapsed
- NO invoices actually generated
- Exit code: 0 (success)

**Error Scenario:**
- If no eligible DIs, should display: "No eligible delivery instructions found for invoicing."

---

### Test 2.2: Manual Invoice Generation ✅

**Objective:** Generate invoices for old DIs manually

**Test Setup:**
1. Create a test DI with these properties:
   - Status: DELIVERED
   - contract_id: NOT NULL (linked to a contract)
   - completed_at: 35 days ago (use SQL to backdate)
   - invoice_generated: 0

**SQL to create test scenario:**
```sql
-- Find a completed DI
SELECT id, di_number, status, contract_id, completed_at, invoice_generated
FROM delivery_instructions
WHERE status = 'DELIVERED' AND contract_id IS NOT NULL
LIMIT 1;

-- Backdate the completed_at to 35 days ago
UPDATE delivery_instructions
SET completed_at = DATE_SUB(NOW(), INTERVAL 35 DAY),
    invoice_generated = 0
WHERE id = <your_test_di_id>;
```

**Steps:**
```bash
# Run invoice automation WITHOUT dry-run
php spark jobs:invoice-automation
```

**Expected Result:**
- Console output: "Generated 1 invoice(s) successfully"
- Check invoices table: New invoice record created
- Check delivery_instructions table: invoice_generated = 1, invoice_generated_at timestamp populated
- Check queue or email logs: Emails sent to ACC and Marketing

**Verification Queries:**
```sql
-- Check generated invoice
SELECT * FROM invoices WHERE di_id = <your_test_di_id>;

-- Check DI flag updated
SELECT id, di_number, invoice_generated, invoice_generated_at
FROM delivery_instructions
WHERE id = <your_test_di_id>;
```

---

### Test 2.3: Email Notification Testing ✅

**Objective:** Verify emails sent to correct recipients with proper content

**Steps:**
1. After running manual invoice generation (Test 2.2)
2. Check email inbox for:
   - **finance@sml.co.id** (To)
   - **anselin_smlforklift@yahoo.com** (CC)
   - **marketing@sml.co.id** (separate email)

**Expected Email Content (ACC Team):**
- Subject: "Invoice Ready for Processing: [INVOICE_NUMBER] - [CUSTOMER_NAME]"
- Template: `app/Views/emails/invoice_ready_acc.php`
- Contains:
  - Invoice number (bold, blue)
  - Customer name
  - Contract/PO number
  - Invoice amount (green, large font)
  - DI number
  - DI completed date
  - Invoice generated timestamp
  - Action checklist (5 steps)
  - Link to view invoice in system

**Expected Email Content (Marketing Team):**
- Subject: "Invoice Auto-Generated: [INVOICE_NUMBER] - [CUSTOMER_NAME]"
- Template: `app/Views/emails/invoice_ready_marketing.php`
- Contains:
  - Invoice number
  - Customer name
  - Contract/PO number
  - Invoice amount
  - Status badge: "Sent to Accounting"
  - Success checklist (4 items)
  - Link to view invoice

**Manual Email Test (if queue not processing):**
```php
// Add to any controller method temporarily
$emailData = [
    'invoice' => ['invoice_number' => 'TEST-001', 'total_amount' => 5000000],
    'di' => ['di_number' => 'DI-TEST-001', 'completed_at' => date('Y-m-d H:i:s')],
    'customer_name' => 'Test Customer',
    'contract_number' => 'CONTRACT-TEST-001'
];

$email = \Config\Services::email();
$email->setTo('finance@sml.co.id');
$email->setCC(['anselin_smlforklift@yahoo.com']);
$email->setSubject('TEST: Invoice Ready');
$email->setMessage(view('emails/invoice_ready_acc', $emailData));
$email->send();
```

---

### Test 2.4: Late-Linking Invoice Trigger ✅

**Objective:** Verify instant invoice generation when contract linked to old DI

**Test Setup:**
1. Create a test SPK with DIs that have been delivered >30 days ago
2. Ensure DIs do NOT have contract_id (unlinked)

**SQL Setup:**
```sql
-- Find an unlinked SPK with old DIs
SELECT s.id, s.nomor_spk, s.kontrak_id, 
       di.id AS di_id, di.di_number, di.completed_at,
       DATEDIFF(NOW(), di.completed_at) AS days_since_completion
FROM spk s
JOIN delivery_instructions di ON di.spk_id = s.id
WHERE s.kontrak_id IS NULL 
  AND di.status = 'DELIVERED'
  AND di.completed_at IS NOT NULL
  AND DATEDIFF(NOW(), di.completed_at) > 30
LIMIT 1;

-- If none found, backdate a DI
UPDATE delivery_instructions
SET completed_at = DATE_SUB(NOW(), INTERVAL 35 DAY),
    contract_id = NULL,
    invoice_generated = 0
WHERE id = <your_test_di_id>;

-- Unlink SPK from contract
UPDATE spk
SET kontrak_id = NULL,
    contract_linked_at = NULL,
    contract_linked_by = NULL
WHERE id = <test_spk_id>;
```

**Steps:**
1. Navigate to Marketing → SPK
2. Find the test SPK
3. Click "Link to Contract" action
4. Select a contract from dropdown
5. Click "Link" button

**Expected Result:**
- Success message: "SPK linked to contract successfully. {X} delivery instructions updated. Note: {Y} invoice(s) were automatically generated due to late-linking (>30 days after delivery completion)."
- Check invoices table: Instant invoice created for old DIs
- Check delivery_instructions table: invoice_generated = 1
- Emails sent immediately to ACC and Marketing teams
- In-app notification created for sales user

**Verification Queries:**
```sql
-- Check instant invoice generation
SELECT i.*, di.di_number, di.completed_at,
       DATEDIFF(NOW(), di.completed_at) AS days_late
FROM invoices i
JOIN delivery_instructions di ON di.id = i.di_id
WHERE i.created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
ORDER BY i.created_at DESC
LIMIT 5;
```

**Edge Cases to Test:**
- Link contract with DI exactly 30 days old → Should trigger instant invoice
- Link contract with DI 29 days old → Should NOT trigger instant invoice (wait for cron)
- Link contract with DI already invoiced → Should skip duplicate generation
- Link SPK with multiple DIs (some old, some new) → Should only invoice eligible DIs

---

## Phase 3: Customer Conversion Testing

### Test 3.1: Convert Prospect to Customer ✅

**Objective:** Verify quotation prospect can be converted to permanent customer

**Test Setup:**
1. Create a test quotation with these properties:
   - workflow_stage: 'DEAL'
   - stage: 'ACCEPTED'
   - created_customer_id: NULL (not yet converted)

**SQL Setup (if needed):**
```sql
-- Find a DEAL quotation without customer conversion
SELECT id_quotation, quotation_number, prospect_name, 
       workflow_stage, stage, created_customer_id
FROM quotations
WHERE workflow_stage = 'DEAL' 
  AND stage = 'ACCEPTED'
  AND created_customer_id IS NULL
LIMIT 1;

-- Or create test quotation
INSERT INTO quotations (
    quotation_number, prospect_name, prospect_contact_person, 
    prospect_phone, prospect_email, prospect_address,
    quotation_title, quotation_description, quotation_date, valid_until,
    total_amount, stage, workflow_stage, created_by
) VALUES (
    'QT-TEST-001', 'Test Prospect Company', 'John Doe',
    '081234567890', 'testprospect@company.com', 'Jl. Test No. 123, Jakarta',
    'Rental Quote', 'Test quotation for conversion', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY),
    5000000, 'ACCEPTED', 'DEAL', 1
);
```

**Steps:**
1. Navigate to Marketing → Quotations
2. Click on the DEAL quotation to open detail modal
3. Verify **"Convert to Customer"** button is visible (green button with user-check icon)
4. Click **"Convert to Customer"** button
5. Review confirmation dialog showing:
   - Prospect name
   - Contact person
   - Phone number
   - Quotation number
   - Info message about customer code generation
6. Click **"Yes, Convert to Customer"**
7. Wait for processing (shows loading indicator)

**Expected Result:**
- Success message: "Customer Created!" with customer code displayed
- Check customers table: New customer record created with:
  - Auto-generated customer_code (e.g., CUST-2026-001)
  - customer_name = prospect_name
  - status = 'ACTIVE'
  - notes = "Converted from quotation #QT-XXX-XXX"
- Check customer_locations table: Primary location created if address provided
- Check quotations table: 
  - created_customer_id populated
  - customer_converted_at timestamp set
- In-app notification created for user
- Quotation detail modal refreshes, showing **"Customer Created"** badge instead of button

**Verification Queries:**
```sql
-- Check customer creation
SELECT c.*, q.quotation_number
FROM customers c
JOIN quotations q ON q.created_customer_id = c.id
WHERE q.id_quotation = <your_test_quotation_id>;

-- Check location creation
SELECT cl.*
FROM customer_locations cl
JOIN quotations q ON q.created_customer_id = cl.customer_id
WHERE q.id_quotation = <your_test_quotation_id>;

-- Check quotation updated
SELECT id_quotation, quotation_number, prospect_name,
       created_customer_id, customer_converted_at
FROM quotations
WHERE id_quotation = <your_test_quotation_id>;
```

**Error Scenarios to Test:**
- Convert already-converted quotation → Should show error: "This quotation has already been converted to a customer"
- Convert PROSPECT stage quotation → Should show error: "Only DEAL quotations can be converted to customers"
- Missing prospect_name → Should show error

---

### Test 3.2: Customer Created Badge Display ✅

**Objective:** Verify UI updates after successful conversion

**Steps:**
1. After converting prospect (Test 3.1)
2. Close and re-open the quotation detail modal
3. Verify **"Convert to Customer"** button is replaced with:
   - Green badge with check-circle icon
   - Text: "Customer Created"
   - Tooltip showing conversion timestamp

**Expected Result:**
- Button hidden for converted quotations
- Badge always visible in quotationActions section
- No way to "undo" conversion (permanent action)

---

## Phase 4: Integration Testing

### Test 4.1: End-to-End Workflow (Full Cycle)

**Objective:** Test complete workflow from quotation to invoice

**Steps:**
1. **Create Quotation**
   - Marketing → Quotations → Create New
   - Fill prospect details
   - Add specifications
   - Set stage = ACCEPTED, workflow_stage = DEAL

2. **Convert to Customer**
   - Click "Convert to Customer"
   - Verify customer created successfully

3. **Create Contract (Direct - No Quotation)**
   - Marketing → Kontrak → Create Contract
   - Select the newly created customer
   - Choose location
   - Set contract type, dates, rates
   - Save contract

4. **Create SPK from Contract**
   - Marketing → Kontrak → View contract detail
   - Click "Create SPK"
   - Fill SPK details
   - Save SPK

5. **Create DI from SPK**
   - Marketing → SPK → View SPK detail
   - Click "Create DI"
   - Fill delivery details
   - Save DI

6. **Mark DI as Delivered**
   - Find the DI
   - Update status to DELIVERED
   - Set completed_at date (use SQL to backdate 31 days)

```sql
UPDATE delivery_instructions
SET status = 'DELIVERED',
    completed_at = DATE_SUB(NOW(), INTERVAL 31 DAY)
WHERE id = <your_di_id>;
```

7. **Run Invoice Automation**
```bash
php spark jobs:invoice-automation
```

8. **Verify Invoice Generated**
   - Check Finance → Invoices
   - Verify invoice for the DI exists
   - Check email inbox for ACC and Marketing notifications

**Expected Result:**
- Complete workflow executes without errors
- Invoice automatically generated 30+ days after DI completion
- All stakeholders notified via email and in-app notification

---

### Test 4.2: Late-Linking Integration

**Objective:** Test late-linking when old SPK linked to new contract

**Steps:**
1. Create SPK WITHOUT contract (source_type = 'DAILY_SPOT')
2. Create DIs for the SPK
3. Mark DIs as DELIVERED and backdate completed_at to 35 days ago
4. Create a new contract for the customer
5. Link SPK to contract via "Link SPK to Contract" action
6. Verify instant invoice generation

**Expected Result:**
- Invoices generated instantly upon linking (no need to wait for cron)
- Success message indicates X invoices auto-generated
- Emails sent immediately

---

## Phase 5: Stress & Edge Case Testing

### Test 5.1: Concurrent Invoice Generation

**Objective:** Prevent duplicate invoice generation

**Test Setup:**
Create multiple DIs eligible for invoicing (all 30+ days old, same contract)

**Steps:**
1. Run invoice automation command twice in quick succession:
```bash
php spark jobs:invoice-automation & php spark jobs:invoice-automation
```

**Expected Result:**
- Each DI should only have ONE invoice generated
- invoice_generated flag prevents duplicates
- Second run should find 0 eligible DIs (already processed)

---

### Test 5.2: Missing Email Configuration

**Objective:** Handle gracefully when email settings invalid

**Steps:**
1. Temporarily comment out email configuration in `.env`
2. Run invoice automation
3. Check error logs

**Expected Result:**
- Invoice still generated (invoice generation doesn't fail)
- Error logged: "Failed to queue email notification"
- System continues processing other DIs
- Manual email sending possible later

---

### Test 5.3: Database Performance

**Objective:** Verify query performance with large datasets

**Test Setup:**
Generate 100+ eligible DIs (or use production-like data)

**Steps:**
```bash
php spark jobs:invoice-automation --verbose
```

**Expected Result:**
- Total execution time < 30 seconds for 100 DIs
- No memory exceeded errors
- Index on (invoice_generated, completed_at, contract_id, status) utilized
- Check with EXPLAIN:
```sql
EXPLAIN SELECT di.id, di.di_number, di.completed_at, 
       c.customer_name, k.no_kontrak AS contract_number,
       q.assigned_to AS sales_user_id
FROM delivery_instructions di
JOIN kontrak k ON k.id = di.contract_id
JOIN customers c ON c.id = k.customer_id
LEFT JOIN spk s ON s.id = di.spk_id
LEFT JOIN quotations q ON q.id_quotation = s.quotation_id
WHERE di.status = 'DELIVERED'
  AND di.contract_id IS NOT NULL
  AND di.invoice_generated = 0
  AND DATE_ADD(di.completed_at, INTERVAL 30 DAY) <= NOW();
```

---

## Phase 6: User Acceptance Testing (UAT)

### Test 6.1: User Role Permissions

**Objective:** Verify permissions work correctly

**Test as Marketing User:**
- ✅ Can create contracts directly
- ✅ Can view customer management (read-only contracts)
- ✅ Can redirect to Kontrak page for contract CRUD
- ✅ Can convert prospects to customers
- ✅ Cannot see invoice automation settings

**Test as Accounting User:**
- ✅ Can view invoices
- ✅ Can receive invoice notification emails
- ✅ Cannot edit contracts (optional restriction)
- ✅ Cannot run invoice automation CLI (optional restriction)

**Test as Admin User:**
- ✅ Full access to all features
- ✅ Can configure cron jobs
- ✅ Can run invoice automation manually
- ✅ Can view all logs

---

### Test 6.2: UI/UX Validation

**Checklist:**
- ✅ All modals are 85vw width (modal-wide)
- ✅ No horizontal scrollbars in modals
- ✅ Buttons have clear labels and icons
- ✅ Success/error messages display properly with SweetAlert2
- ✅ Loading indicators shown during AJAX operations
- ✅ Confirmation dialogs shown for destructive actions
- ✅ DataTables pagination works correctly
- ✅ Form validation shows inline errors
- ✅ Mobile responsive (test on 768px viewport)
- ✅ No console errors in browser DevTools

---

### Test 6.3: Documentation Review

**Checklist:**
- ✅ `databases/CRON_CONFIGURATION.txt` exists and is clear
- ✅ `app/Jobs/InvoiceAutomationJob.php` has code comments
- ✅ `app/Commands/InvoiceAutomation.php` has --help output
- ✅ Email templates have proper HTML structure
- ✅ Database migration files have comments
- ✅ This testing guide is comprehensive

---

## Phase 7: Production Deployment Checklist

### Pre-Deployment

- [ ] Backup production database
```bash
mysqldump -u root -p optima > optima_backup_$(date +%Y%m%d_%H%M%S).sql
```

- [ ] Review all modified files:
  - `app/Views/marketing/kontrak.php` (direct contract creation modal)
  - `app/Views/marketing/customer_management.php` (view-only contracts)
  - `app/Views/marketing/quotations.php` (convert to customer button)
  - `app/Controllers/Marketing.php` (late-linking trigger + conversion method)
  - `app/Models/QuotationModel.php` (customer_converted_at field)
  - `app/Jobs/InvoiceAutomationJob.php` (automation logic)
  - `app/Commands/InvoiceAutomation.php` (CLI command)
  - `app/Config/Routes.php` (convertProspectToCustomer route)
  - `app/Views/emails/invoice_ready_acc.php` (ACC email template)
  - `app/Views/emails/invoice_ready_marketing.php` (Marketing email template)

- [ ] Test on staging environment (if available)
- [ ] Update `.env` with production email addresses
- [ ] Clear cache: `php spark cache:clear`

### Deployment Steps

1. [ ] Upload files to production server
2. [ ] Run database migrations:
```bash
mysql -u production_user -p production_db < databases/migrations/2026-02-19_add_invoice_generated_flag.sql
mysql -u production_user -p production_db < databases/migrations/2026-02-19_add_customer_converted_at_column.sql
```

3. [ ] Verify database columns added:
```bash
mysql -u production_user -p production_db -e "SHOW COLUMNS FROM delivery_instructions LIKE 'invoice_generated%';"
mysql -u production_user -p production_db -e "SHOW COLUMNS FROM quotations LIKE 'customer_converted%';"
```

4. [ ] Configure production cron job:
```bash
crontab -e
# Add:
0 1 * * * cd /path/to/production/optima && php spark jobs:invoice-automation >> /var/log/optima/cron-invoice-automation.log 2>&1
```

5. [ ] Test invoice automation in dry-run mode:
```bash
php spark jobs:invoice-automation --dry-run --verbose
```

6. [ ] Monitor first automated run tomorrow at 1:00 AM
7. [ ] Check logs: `writable/logs/log-YYYY-MM-DD.log`

### Post-Deployment Validation

- [ ] Invoice automation cron runs successfully
- [ ] Emails delivered to ACC and Marketing teams
- [ ] New contracts can be created without quotation
- [ ] Customer management shows view-only contracts
- [ ] Prospects can be converted to customers
- [ ] Late-linking triggers instant invoice generation
- [ ] All modal sizes are consistent (85vw)
- [ ] No PHP errors in server logs
- [ ] User notifications working

---

## Troubleshooting Guide

### Issue 1: Invoices Not Generated

**Symptoms:** Cron runs but 0 invoices generated

**Diagnosis:**
```sql
-- Check eligible DIs
SELECT COUNT(*) FROM delivery_instructions
WHERE status = 'DELIVERED'
  AND contract_id IS NOT NULL
  AND invoice_generated = 0
  AND DATE_ADD(completed_at, INTERVAL 30 DAY) <= NOW();

-- If count > 0, check for errors
```

**Solutions:**
- Check logs: `writable/logs/log-YYYY-MM-DD.log`
- Verify InvoiceModel::createFromDI() method exists
- Check database permissions (INSERT privilege on invoices table)
- Run manually with verbose: `php spark jobs:invoice-automation --verbose`

---

### Issue 2: Emails Not Sent

**Symptoms:** Invoices generated but no emails received

**Diagnosis:**
- Check `.env` email configuration
- Check SMTP logs
- Check queue table (if using database queue)

**Solutions:**
```bash
# Test SMTP connection
php spark queue:process

# Check email queue
mysql -u root -p optima -e "SELECT * FROM queue ORDER BY created_at DESC LIMIT 10;"

# Manual email test (add to controller temporarily)
$email = \Config\Services::email();
$email->setTo('finance@sml.co.id');
$email->setSubject('Test Email');
$email->setMessage('This is a test');
$result = $email->send();
echo $email->printDebugger();
```

---

### Issue 3: Convert to Customer Button Not Appearing

**Symptoms:** Button missing in quotation detail modal

**Diagnosis:**
- Check quotation workflow_stage (must be 'DEAL')
- Check quotation stage (must be 'ACCEPTED')
- Check created_customer_id (must be NULL)

**Solutions:**
```sql
-- Verify quotation status
SELECT id_quotation, quotation_number, workflow_stage, stage, created_customer_id
FROM quotations
WHERE id_quotation = <your_quotation_id>;

-- If workflow_stage is wrong, update:
UPDATE quotations
SET workflow_stage = 'DEAL', stage = 'ACCEPTED'
WHERE id_quotation = <your_quotation_id> AND created_customer_id IS NULL;
```

---

### Issue 4: Late-Linking Not Triggering Instant Invoice

**Symptoms:** SPK linked but no invoices generated for old DIs

**Diagnosis:**
- Check Marketing controller linkSPKToContract() method includes late-linking code
- Check logs for errors during linking process

**Solutions:**
- Verify code at line ~8470 in Marketing.php includes InvoiceAutomationJob instantiation
- Check DI completed_at dates (must be >= 30 days ago)
- Check invoice_generated flag (must be 0)
- Run SQL to verify:
```sql
SELECT di.id, di.di_number, di.status, di.completed_at,
       DATEDIFF(NOW(), di.completed_at) AS days_passed,
       di.invoice_generated
FROM delivery_instructions di
WHERE di.spk_id = <your_spk_id>
  AND di.status = 'DELIVERED'
  AND di.contract_id IS NOT NULL;
```

---

## Success Metrics

After completing all tests, you should have:

- ✅ 35+ modals standardized to 85vw width
- ✅ Direct contract creation working without quotation requirement
- ✅ Customer management enforcing view-only contracts (no CRUD)
- ✅ Invoice automation generating invoices 30 days after DI completion
- ✅ Email notifications sent to ACC (2 addresses) and Marketing
- ✅ Late-linking triggering instant invoice generation
- ✅ Prospect-to-customer conversion working for DEAL quotations
- ✅ CLI command with dry-run and verbose modes working
- ✅ Cron job configured and running daily at 1:00 AM
- ✅ Database migrations applied successfully
- ✅ All email templates rendering correctly
- ✅ Zero PHP errors in logs
- ✅ Zero console errors in browser

---

## Rollback Plan

If critical issues found in production:

1. **Stop cron job immediately:**
```bash
crontab -e
# Comment out the invoice automation line
# 0 1 * * * ...
```

2. **Revert database migrations:**
```sql
-- Revert invoice automation
ALTER TABLE delivery_instructions 
DROP COLUMN invoice_generated, 
DROP COLUMN invoice_generated_at,
DROP INDEX idx_invoice_automation;

-- Revert customer conversion
ALTER TABLE quotations 
DROP COLUMN customer_converted_at;
```

3. **Restore from backup:**
```bash
mysql -u root -p optima < optima_backup_YYYYMMDD_HHMMSS.sql
```

4. **Revert code changes (Git):**
```bash
git log --oneline -10  # Find commit hash before changes
git revert <commit_hash>
git push origin main
```

---

## Support & Maintenance

**Log Files:**
- Application logs: `writable/logs/log-YYYY-MM-DD.log`
- Cron logs: `/var/log/optima/cron-invoice-automation.log`
- SMTP logs: Check `.env` email.SMTPDebug setting

**Monitoring:**
- Daily: Check cron log for successful runs
- Weekly: Verify invoice counts match DI completion counts
- Monthly: Review email delivery rates

**Maintenance Tasks:**
- Quarterly: Review and optimize invoice_automation index
- Yearly: Archive old invoices and delivery instructions

---

**End of Testing Guide**
