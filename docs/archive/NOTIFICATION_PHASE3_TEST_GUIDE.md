# 🧪 NOTIFICATION SYSTEM - PHASE 3 TEST GUIDE

**Testing Date:** December 19, 2024  
**Phase:** 3 (MEDIUM Priority)  
**Total Test Scenarios:** 17  
**Quick Tests:** 5 (Immediately testable)  
**Future Tests:** 12 (Awaiting feature implementation)

---

## 🎯 TESTING OVERVIEW

This guide provides step-by-step testing procedures for all 17 Phase 3 notification functions. Tests are organized by category and marked with their current testability status.

### Testing Status Legend
- ✅ **Ready to Test** - Feature exists, test immediately
- ⏳ **Pending** - Helper ready, awaiting controller implementation
- 🔧 **Requires Setup** - Need additional configuration/data

---

## 📋 QUICK TEST CHECKLIST

### Immediately Testable (5 Tests)
- [ ] Test 1: Customer Created ✅
- [ ] Test 2: Customer Updated ✅
- [ ] Test 3: Customer Status Changed ✅
- [ ] Test 4: Warehouse Stock Alert ✅
- [ ] Test 5: Quotation Sent to Customer ✅

### Awaiting Implementation (12 Tests)
- [ ] Test 6-17: Will be available after feature development ⏳

---

## 🧪 CATEGORY 1: CUSTOMER MANAGEMENT (3 Tests)

### Test 1: Customer Created ✅

**Priority:** MEDIUM  
**Status:** Ready to Test  
**Estimated Time:** 3 minutes

#### Pre-requisites
- User has `marketing.customer.create` permission
- Access to Customer Management module

#### Test Steps
1. Navigate to **Customer Management** (sidebar menu)
2. Click **"Add New Customer"** button
3. Fill in customer details:
   ```
   Customer Code: CUST-TEST-PH3-001
   Customer Name: Phase 3 Test Customer
   Is Active: Yes
   ```
4. Fill in primary location:
   ```
   Area: Select any area
   Location Name: Head Office
   Location Type: HEAD_OFFICE
   Address: Jl. Test Phase 3 No. 123
   City: Jakarta
   Province: DKI Jakarta
   Postal Code: 12345
   ```
5. Fill in contact person:
   ```
   Contact Person: John Doe
   Position: Manager
   Phone: 081234567890
   Email: john@phase3test.com
   ```
6. Click **"Save"** button

#### Expected Results
✅ **Success Indicators:**
- Success toast/alert: "Customer and primary location created successfully"
- Redirected to customer list or detail page
- Customer appears in customer table

✅ **Notification Verification:**
1. Check **Notifications Panel** (bell icon, top right)
2. Find notification titled: **"New Customer: Phase 3 Test Customer"**
3. Notification message should include:
   - Customer code: CUST-TEST-PH3-001
   - Customer type: Regular
   - Phone: 081234567890
   - Email: john@phase3test.com
4. Notification recipients: Admin, Marketing Manager, Sales
5. Notification priority: MEDIUM
6. Click notification → should navigate to customer detail page

#### Verification Query
```sql
-- Check notification was created
SELECT 
    n.id,
    n.event_name,
    n.title,
    n.message,
    n.priority,
    n.is_read,
    n.created_at,
    u.username as recipient
FROM notifications n
LEFT JOIN users u ON n.user_id = u.id
WHERE n.event_name = 'customer_created'
ORDER BY n.created_at DESC
LIMIT 5;

-- Check customer was created
SELECT 
    id,
    customer_code,
    customer_name,
    is_active,
    created_at
FROM customers
WHERE customer_code = 'CUST-TEST-PH3-001';
```

#### Troubleshooting
❌ **No notification created:**
- Check: notification_helper.php is loaded (helper('notification'))
- Check: notify_customer_created() function exists
- Check: notification_rules table has 'customer_created' rule with is_active=1

❌ **Notification not visible:**
- Check: Current user has role in target_roles (admin/marketing_manager/sales)
- Check: Notification panel is refreshing (reload page)

---

### Test 2: Customer Updated ✅

**Priority:** MEDIUM  
**Status:** Ready to Test  
**Estimated Time:** 3 minutes

#### Pre-requisites
- User has `marketing.customer.edit` permission
- Customer CUST-TEST-PH3-001 exists from Test 1

#### Test Steps
1. Navigate to **Customer Management**
2. Find customer **CUST-TEST-PH3-001**
3. Click **"Edit"** button (pencil icon)
4. Make the following changes:
   ```
   Customer Name: "Phase 3 Test Customer" → "Phase 3 Updated Customer"
   Phone: "081234567890" → "081999888777"
   Email: "john@phase3test.com" → "john.updated@phase3test.com"
   ```
5. Click **"Save"** button

#### Expected Results
✅ **Success Indicators:**
- Success toast: "Customer updated successfully"
- Changes reflected in customer detail/list

✅ **Notification Verification:**
1. Check **Notifications Panel**
2. Find notification: **"Customer Updated: Phase 3 Updated Customer"**
3. Message should show **changes**:
   ```
   Customer name: Phase 3 Test Customer → Phase 3 Updated Customer
   Phone: 081234567890 → 081999888777
   Email: john@phase3test.com → john.updated@phase3test.com
   ```
4. Notification includes **updated_by** username
5. Recipients: Admin, Marketing Manager, Sales
6. Priority: MEDIUM

#### Verification Query
```sql
-- Check update notification
SELECT 
    title,
    message,
    priority,
    created_at
FROM notifications
WHERE event_name = 'customer_updated'
  AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
ORDER BY created_at DESC;

-- Check customer changes
SELECT 
    customer_name,
    customer_code,
    updated_at
FROM customers
WHERE customer_code = 'CUST-TEST-PH3-001';
```

#### Advanced Test: Partial Update
**Goal:** Test that only changed fields appear in notification

**Steps:**
1. Edit customer CUST-TEST-PH3-001 again
2. Only change **Email:** "john.updated@phase3test.com" → "final@test.com"
3. Don't change name or phone
4. Save

**Expected:**
- Notification message shows only: "Email: john.updated@phase3test.com → final@test.com"
- No mention of customer_name or phone (unchanged)

---

### Test 3: Customer Status Changed ✅

**Priority:** HIGH  
**Status:** Ready to Test  
**Estimated Time:** 3 minutes

#### Pre-requisites
- User has `marketing.customer.edit` permission
- Customer CUST-TEST-PH3-001 exists (currently Active)

#### Test Steps
1. Navigate to **Customer Management**
2. Find customer **CUST-TEST-PH3-001**
3. Click **"Edit"** button
4. Change **"Is Active"** from:
   - ✅ Active (checked) → ❌ Inactive (unchecked)
5. Click **"Save"** button

#### Expected Results
✅ **Success Indicators:**
- Success toast: "Customer updated successfully"
- Customer status shows as "Inactive" in list

✅ **Notification Verification:**
**Note:** This action creates **TWO notifications** (by design)

**Notification 1: General Update**
- Title: "Customer Updated: Phase 3 Updated Customer"
- Event: customer_updated
- Priority: MEDIUM
- Message includes: "Is active: 1 → 0"

**Notification 2: Status Change** (More Specific)
- Title: "Customer Status Changed: Phase 3 Updated Customer"
- Event: customer_status_changed
- Priority: **HIGH** (elevated priority)
- Message includes:
  ```
  Status changed from Active to Inactive
  Reason: Status updated from Customer Management
  Changed by: [Your Username]
  ```
- Recipients: Admin, Marketing Manager, Sales

#### Verification Query
```sql
-- Check BOTH notifications were created
SELECT 
    event_name,
    title,
    message,
    priority,
    created_at
FROM notifications
WHERE (event_name = 'customer_updated' OR event_name = 'customer_status_changed')
  AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
ORDER BY created_at DESC;

-- Expected result: 2 rows (one for each event_name)
```

#### Test Variations

**Variation A: Inactive → Active**
1. Edit same customer again
2. Change **Is Active** from ❌ Inactive → ✅ Active
3. Expected: "Status changed from Inactive to Active"

**Variation B: Status + Other Changes**
1. Edit customer
2. Change BOTH:
   - Status: Active → Inactive
   - Phone: 081999888777 → 081111222333
3. Expected: TWO notifications
   - customer_updated (includes phone change AND status)
   - customer_status_changed (focuses on status only)

---

## 🧪 CATEGORY 2: WAREHOUSE EXTENDED (3 Tests)

### Test 4: Warehouse Stock Alert ✅

**Priority:** HIGH  
**Status:** Ready to Test (Auto-trigger)  
**Estimated Time:** 2 minutes

#### Pre-requisites
- User has `warehouse.access` permission
- System has getLowStockAlerts() function active

#### Test Steps
1. Navigate to **Warehouse Division** (sidebar menu)
2. View the **Dashboard**
3. System automatically calls `getLowStockAlerts()` on page load
4. Check **"Low Stock Alerts"** section on dashboard

#### Expected Results
✅ **Dashboard Display:**
- 3 items shown in low stock alerts table:
  1. Brake Pad Set (8/12) - High urgency 🔴
  2. Tire Set (6/8) - Medium urgency 🟡
  3. Cleaning Detergent (25/10) - Low urgency 🟢

✅ **Notification Verification:**
1. Check **Notifications Panel**
2. Find **TWO notifications** (not three - Low urgency excluded):
   ```
   1. "Low Stock Alert: Brake Pad Set"
      Message: "URGENT: Brake Pad Set stock is low. 
               Current: 8 pcs, Minimum: 12 pcs. 
               Warehouse: Main Warehouse"
      Priority: HIGH
   
   2. "Low Stock Alert: Tire Set"
      Message: "URGENT: Tire Set stock is low. 
               Current: 6 pcs, Minimum: 8 pcs. 
               Warehouse: Main Warehouse"
      Priority: HIGH
   ```
3. **No notification** for Cleaning Detergent (Low urgency filtered out)
4. Recipients: Warehouse Manager, Procurement, Admin

#### Verification Query
```sql
-- Check stock alert notifications (last 24 hours)
SELECT 
    title,
    message,
    priority,
    created_at
FROM notifications
WHERE event_name = 'warehouse_stock_alert'
  AND created_at >= CURDATE()
ORDER BY created_at DESC;

-- Should show 2 notifications (High + Medium urgency only)
```

#### Important Notes
⚠️ **Notification Frequency:**
- Notifications trigger **every time** warehouse dashboard loads
- In production, add throttling to prevent spam:
  - Only notify once per item per day
  - Only notify if stock decreased since last alert
  
📝 **Future Enhancement:**
```php
// Add to getLowStockAlerts() in production:
$lastAlertTime = cache()->get("stock_alert_{$alert['item_code']}");
if (!$lastAlertTime || time() - $lastAlertTime > 86400) { // 24 hours
    notify_warehouse_stock_alert([...]);
    cache()->save("stock_alert_{$alert['item_code']}", time(), 86400);
}
```

---

### Test 5: Warehouse Transfer Completed ⏳

**Priority:** MEDIUM  
**Status:** Pending Implementation  
**Feature Required:** Warehouse Transfer Module

#### Placeholder Test (For Future Use)

**When Feature is Ready:**
1. Create warehouse transfer (e.g., Main → Branch Warehouse)
2. Add items to transfer (minimum 1)
3. Change transfer status to "Completed"
4. Click "Complete Transfer" button

**Expected Notification:**
- Title: "Transfer Completed: TRF-2024-12-XXX"
- Message includes: from_warehouse, to_warehouse, item_count, completed_by
- Recipients: Warehouse Manager, Admin

**Helper Function:** ✅ Ready in notification_helper.php (line ~1172)
**Database Rule:** ✅ Ready in Phase 3 SQL migration
**Controller:** ⏳ Needs implementation

---

### Test 6: Warehouse Stocktake Completed ⏳

**Priority:** HIGH  
**Status:** Pending Implementation  
**Feature Required:** Stocktake Management Module

#### Placeholder Test (For Future Use)

**When Feature is Ready:**
1. Create new stocktake for a warehouse
2. Count physical inventory for items
3. Record discrepancies (if any)
4. Click "Complete Stocktake" button

**Expected Notification:**
- Title: "Stocktake Completed: [Warehouse Name]"
- Message includes: stocktake_code, items_counted, discrepancies, completed_by
- Recipients: Warehouse Manager, Finance Manager, Admin
- Priority: HIGH (affects financial reports)

**Helper Function:** ✅ Ready (line ~1195)
**Database Rule:** ✅ Ready
**Controller:** ⏳ Needs implementation

---

## 🧪 CATEGORY 3: OPERATIONAL WORKFLOWS (4 Tests)

### Test 7-10: Operations Module ⏳

**Status:** All Pending Implementation  
**Required Features:**
- Unit Inspection Management
- Unit Maintenance Scheduling
- Inspection/Maintenance Completion Workflow

#### Test 7: Inspection Scheduled ⏳
- **Helper:** ✅ Ready (line ~1220)
- **Expected:** Notification when unit inspection scheduled
- **Recipients:** Operations Manager, Mechanic Leader, Admin

#### Test 8: Inspection Completed ⏳
- **Helper:** ✅ Ready (line ~1242)
- **Expected:** Notification with inspection result and findings
- **Priority:** HIGH (safety-critical)
- **Recipients:** Operations Manager, Fleet Manager, Admin

#### Test 9: Maintenance Scheduled ⏳
- **Helper:** ✅ Ready (line ~1266)
- **Expected:** Notification with maintenance details and assigned mechanic
- **Recipients:** Operations Manager, Mechanic Leader, Admin

#### Test 10: Maintenance Completed ⏳
- **Helper:** ✅ Ready (line ~1290)
- **Expected:** Notification with actual hours, parts, and cost
- **Priority:** HIGH (affects unit availability)
- **Recipients:** Operations Manager, Finance Manager, Admin

---

## 🧪 CATEGORY 4: FINANCE EXTENDED (3 Tests)

### Test 11-13: Finance Module ⏳

**Status:** All Pending Implementation  
**Required Features:**
- Payment Recording
- Overdue Payment Checker (Scheduled Job)
- Budget Monitoring (Scheduled Job)

#### Test 11: Payment Received ⏳
- **Helper:** ✅ Ready (line ~1316)
- **Expected:** Notification when customer payment recorded
- **Recipients:** Finance Manager, Accounting, Admin

#### Test 12: Payment Overdue ⏳
- **Helper:** ✅ Ready (line ~1337)
- **Expected:** Daily notification for overdue invoices
- **Priority:** CRITICAL (cash flow impact)
- **Recipients:** Finance Manager, Management, Marketing Manager, Admin

#### Test 13: Budget Threshold Exceeded ⏳
- **Helper:** ✅ Ready (line ~1359)
- **Expected:** Notification when department budget exceeds 90%
- **Priority:** HIGH (financial control)
- **Recipients:** Finance Manager, Management, Admin

---

## 🧪 CATEGORY 5: SPK MANAGEMENT (2 Tests)

### Test 14-15: SPK Module ⏳

**Status:** Pending Implementation  
**Required Features:**
- SPK Creation Workflow
- SPK Completion Process

#### Test 14: SPK Created ⏳
- **Helper:** ✅ Ready (line ~1383)
- **Expected:** Notification when new SPK (Surat Perintah Kerja) created
- **Recipients:** Operations Manager, Mechanic Leader, Admin

#### Test 15: SPK Completed ⏳
- **Helper:** ✅ Ready (line ~1408)
- **Expected:** Notification with SPK completion details
- **Priority:** HIGH (operational tracking)
- **Recipients:** Operations Manager, Fleet Manager, Admin

---

## 🧪 CATEGORY 6: ADDITIONAL MARKETING (2 Tests)

### Test 16: Quotation Sent to Customer ✅

**Priority:** MEDIUM  
**Status:** Ready to Test  
**Estimated Time:** 3 minutes

#### Pre-requisites
- User has quotation management permission
- At least one quotation in "QUOTATION" stage
- Quotation has specifications added

#### Test Steps
1. Navigate to **Marketing > Quotations**
2. Find a quotation with stage = "QUOTATION"
3. Click quotation to view details
4. Click **"Send to Customer"** button
5. Confirm the action

#### Expected Results
✅ **Success Indicators:**
- Success message: "Quotation sent successfully"
- Quotation stage changes from "QUOTATION" → "SENT"
- sent_at timestamp populated

✅ **Notification Verification:**
1. Check **Notifications Panel**
2. Find notification: **"Quotation Sent: Q-2024-12-XXX"**
3. Message includes:
   - Customer name
   - Customer email
   - Send method: email
   - Sent by: [Your Username]
4. Recipients: Marketing Manager, Sales, Admin
5. Priority: MEDIUM

#### Verification Query
```sql
-- Check notification and quotation status
SELECT 
    n.title,
    n.message,
    q.quotation_number,
    q.workflow_stage,
    q.sent_at,
    n.created_at as notification_created
FROM notifications n
LEFT JOIN quotations q ON q.id = n.reference_id
WHERE n.event_name = 'quotation_sent_to_customer'
ORDER BY n.created_at DESC
LIMIT 5;
```

#### Edge Cases to Test

**Case A: Quotation without Specifications**
1. Try to send quotation with 0 specifications
2. Expected: Error message "Cannot send quotation without specifications"
3. Expected: NO notification created

**Case B: Quotation Not in QUOTATION Stage**
1. Try to send quotation already in "SENT" stage
2. Expected: Error "Quotation must be in QUOTATION stage to send"
3. Expected: NO notification created

---

### Test 17: Quotation Follow-up Required ⏳

**Priority:** MEDIUM  
**Status:** Pending Implementation  
**Required Feature:** Scheduled Job (Daily Check)

#### Placeholder Test (For Future Use)

**Implementation Plan:**
```php
// Create scheduled job: app/Commands/CheckQuotationFollowups.php
class CheckQuotationFollowups extends BaseCommand {
    public function run(array $params) {
        $quotations = $this->quotationModel
            ->where('workflow_stage', 'SENT')
            ->where('sent_at <', date('Y-m-d', strtotime('-3 days')))
            ->findAll();
        
        foreach ($quotations as $quotation) {
            $daysSinceSent = (time() - strtotime($quotation['sent_at'])) / 86400;
            
            helper('notification');
            notify_quotation_follow_up_required([
                'id' => $quotation['id'],
                'quote_number' => $quotation['quotation_number'],
                'customer_name' => $quotation['customer_name'],
                'days_since_sent' => floor($daysSinceSent),
                'last_contact' => $quotation['last_contact_date'] ?? 'No contact recorded',
                'assigned_to' => $quotation['assigned_salesperson'] ?? 'Unassigned',
                'follow_up_priority' => $daysSinceSent > 7 ? 'high' : 'normal'
            ]);
        }
    }
}
```

**When Implemented:**
1. Run command: `php spark quotations:check-followups`
2. Expected: Notifications for quotations sent 3+ days ago with no response
3. Recipients: Marketing Manager, Assigned Salesperson, Admin

**Helper Function:** ✅ Ready (line ~1433)
**Database Rule:** ✅ Ready
**Scheduled Job:** ⏳ Needs creation

---

## 📊 TESTING SUMMARY

### Immediate Test Results Expected
After running Tests 1-5, your notifications table should show:

```sql
SELECT 
    event_name,
    COUNT(*) as notification_count,
    MIN(created_at) as first_notification,
    MAX(created_at) as last_notification
FROM notifications
WHERE event_name IN (
    'customer_created',
    'customer_updated', 
    'customer_status_changed',
    'warehouse_stock_alert',
    'quotation_sent_to_customer'
)
GROUP BY event_name;
```

**Expected Output:**
```
+-------------------------------+--------------------+---------------------+---------------------+
| event_name                    | notification_count | first_notification  | last_notification   |
+-------------------------------+--------------------+---------------------+---------------------+
| customer_created              | 1                  | 2024-12-19 10:00:00 | 2024-12-19 10:00:00 |
| customer_updated              | 2-3                | 2024-12-19 10:05:00 | 2024-12-19 10:15:00 |
| customer_status_changed       | 1-2                | 2024-12-19 10:10:00 | 2024-12-19 10:10:00 |
| warehouse_stock_alert         | 2+                 | 2024-12-19 10:20:00 | 2024-12-19 10:25:00 |
| quotation_sent_to_customer    | 1                  | 2024-12-19 10:30:00 | 2024-12-19 10:30:00 |
+-------------------------------+--------------------+---------------------+---------------------+
```

### Future Testing Roadmap
- **Phase 3 Remaining:** 12 functions awaiting feature development
- **Phase 4 (LOW):** 24 functions to implement next
- **Target:** 100% notification coverage (126 functions)

---

## 🐛 TROUBLESHOOTING GUIDE

### Issue: Notification Not Created

**Diagnostic Steps:**
```sql
-- 1. Check helper function exists
SELECT routine_name 
FROM information_schema.routines 
WHERE routine_name LIKE 'notify_%';
-- (This won't work for PHP functions, just illustrative)

-- 2. Check notification rule is active
SELECT event_name, is_active, target_roles
FROM notification_rules
WHERE event_name = 'customer_created';
-- Expected: is_active = 1

-- 3. Check user has target role
SELECT u.username, r.role_name
FROM users u
LEFT JOIN user_roles ur ON u.id = ur.user_id
LEFT JOIN roles r ON ur.role_id = r.id
WHERE u.id = [YOUR_USER_ID];
-- Should match target_roles in notification_rules
```

**Common Causes:**
- Helper not loaded: Add `helper('notification')` before calling notify_*
- Function guard failing: Ensure `function_exists('notify_*')` returns true
- Rule disabled: Set `is_active = 1` in notification_rules
- User role mismatch: User's role not in target_roles

---

### Issue: Notification Created But Not Visible

**Diagnostic Steps:**
```sql
-- Check if notification exists for your user
SELECT * FROM notifications
WHERE user_id = [YOUR_USER_ID]
  AND created_at >= CURDATE()
ORDER BY created_at DESC;
```

**Common Causes:**
- Notification panel not refreshing: Reload page
- Frontend filtering: Check "Show All" vs "Unread Only"
- User role not in target audience: Verify role membership

---

### Issue: Duplicate Notifications

**Expected Behavior:**
- Some events create **multiple notifications by design**:
  - customer_updated + customer_status_changed (when status changes)
  
**Unwanted Duplicates:**
```sql
-- Find duplicate notifications (same event, same user, within 1 minute)
SELECT event_name, user_id, COUNT(*) as duplicates
FROM notifications
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY event_name, user_id, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i')
HAVING COUNT(*) > 1;
```

**Solution:** Add throttling check in helper functions (see Test 4 notes)

---

## ✅ TEST COMPLETION CHECKLIST

### Phase 3 Immediate Tests (5 Tests)
- [ ] Test 1: Customer Created - PASSED
- [ ] Test 2: Customer Updated - PASSED
- [ ] Test 3: Customer Status Changed - PASSED (both notifications)
- [ ] Test 4: Warehouse Stock Alert - PASSED (2 notifications)
- [ ] Test 5: Quotation Sent to Customer - PASSED

### Verification Queries Run
- [ ] All 5 notification types appear in notifications table
- [ ] Correct priority levels assigned
- [ ] Recipients match expected roles
- [ ] Notification messages include all required fields

### Documentation
- [ ] Test results documented
- [ ] Issues/bugs reported (if any)
- [ ] Performance notes recorded
- [ ] User feedback collected

---

## 📞 TEST SUPPORT

**Documentation References:**
- **Completion Report:** `NOTIFICATION_PHASE3_COMPLETION_REPORT.md`
- **Implementation Guide:** `NOTIFICATION_PHASE3_IMPLEMENTATION_GUIDE.md`
- **Phase 2 Tests:** `NOTIFICATION_PHASE2_QUICK_TEST_GUIDE.md`

**Code References:**
- **Helper Functions:** `app/Helpers/notification_helper.php` (lines 1074-1575)
- **Controllers:** CustomerManagementController.php, Warehouse.php, Marketing.php
- **SQL Migration:** `databases/migrations/add_medium_priority_notification_rules_phase3.sql`

---

*Test Guide Version: 1.0*  
*Last Updated: December 19, 2024*  
*Phase: 3 of 4+*  
*Quick Tests Ready: 5/17*
