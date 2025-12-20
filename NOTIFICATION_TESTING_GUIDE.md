# 🧪 NOTIFICATION TESTING GUIDE
**Quick Reference untuk Testing Notification System**

---

## 🎯 TESTING CHECKLIST

### ✅ Phase 1: Basic Function Test (30 Functions)

Copy checklist ini dan check setiap function yang sudah ditest:

#### Customer Management (4 tests)
```
[ ] Create Customer → Check notification bell
[ ] Update Customer → Verify notification shows changes
[ ] Delete Customer → Confirm deletion notification
[ ] Add Customer Location → Check location added notification
```

#### Marketing & Quotation (5 tests)
```
[ ] Create Quotation → Verify quotation created notification
[ ] Update Quotation Stage → Check stage change notification
[ ] Create Contract → Verify contract notification sent
[ ] Create SPK → Check SPK creation notification
[ ] Assign SPK Items → Verify assignment notification
```

#### Purchase Order (7 tests)
```
[ ] Create PO Unit → Check warehouse receives notification
[ ] Create PO Attachment → Verify attachment PO notification
[ ] Create PO Sparepart → Check sparepart PO notification
[ ] Cancel PO → Verify rejection notification
[ ] Create Supplier → Check supplier created notification
[ ] Update Supplier → Verify update notification
[ ] Delete Supplier → Confirm deletion notification
```

#### Warehouse Verification (3 tests)
```
[ ] Verify PO Unit → Check verification notification
[ ] Verify PO Attachment → Verify attachment verification
[ ] Verify PO Sparepart → Check sparepart verification
```

#### Finance (2 tests)
```
[ ] Create Invoice → Verify invoice notification (mock)
[ ] Update Payment Status → Check payment notification (mock)
```

#### Warehouse Operations (3 tests)
```
[ ] Update Sparepart Stock → Verify sparepart used notification
[ ] Update Unit Status → Check unit status change notification
[ ] Update Attachment → Verify attachment update notification
```

#### Service Operations (3 tests)
```
[ ] Assign Items to SPK → Check assignment notification
[ ] Complete Unit Verification → Verify completion notification
[ ] Assign Employee to Area → Check employee assignment
[ ] Unassign Employee → Verify unassignment notification
```

---

## 📝 TESTING SCENARIOS

### Scenario 1: Customer Lifecycle
**Objective:** Test complete customer management flow

1. **Create Customer**
   - Action: Go to Customer Management → Add New Customer
   - Fill: Customer code, name, address, contact
   - Expected: Notification "Customer Created: [Name]"
   
2. **Update Customer**
   - Action: Edit customer → Change phone number
   - Expected: Notification "Customer Updated: [Name] - Changes: Phone: xxx → yyy"
   
3. **Add Location**
   - Action: Add new location to customer
   - Expected: Notification "Location Added: [Location Name] for [Customer]"
   
4. **Delete Customer**
   - Action: Delete customer (if no contracts)
   - Expected: Notification "Customer Deleted: [Name]"

**✅ Pass Criteria:** All 4 notifications received in correct order

---

### Scenario 2: Purchase Order Flow
**Objective:** Test PO creation and verification

1. **Create PO Unit**
   - Action: Purchasing → Create PO Unit
   - Fill: PO number, supplier, unit specs
   - Expected: Notification to Warehouse role "New PO Unit to verify: [PO Number]"
   
2. **Verify PO Unit**
   - Action: Warehouse → Verify PO → Set status "Sesuai"
   - Fill: Serial numbers, verification notes
   - Expected: Notification "PO Verified: [PO Number] - Status: Sesuai"

**✅ Pass Criteria:** 
- Purchasing role receives PO created notification
- Warehouse role receives verification notification
- Notification links work correctly

---

### Scenario 3: Quotation to SPK
**Objective:** Test sales workflow from quotation to execution

1. **Create Quotation**
   - Action: Marketing → New Quotation
   - Fill: Customer info, quotation items
   - Expected: Notification "Quotation Created: [Number]"
   
2. **Update Stage to ACCEPTED**
   - Action: Update quotation stage
   - Expected: Notification "Quotation Stage Changed: DRAFT → ACCEPTED"
   
3. **Create Contract**
   - Action: Convert quotation to contract
   - Expected: Notification "Contract Created: [Contract Number]"
   
4. **Create SPK**
   - Action: Create SPK from contract
   - Expected: Notification "SPK Created: [SPK Number]"
   
5. **Assign Unit to SPK**
   - Action: Service → Assign Items
   - Expected: Notification "SPK Assigned: [SPK Number] - Unit: [Unit ID]"

**✅ Pass Criteria:** Complete workflow tracked with 5 notifications

---

## 🔍 VERIFICATION METHODS

### Method 1: UI Check (Manual)
```
1. Login as test user
2. Perform action (e.g., create customer)
3. Check notification bell (top right)
4. Click notification → Verify redirect to correct page
5. Check notification marked as read
```

### Method 2: Database Check (SQL)
```sql
-- Check latest notifications
SELECT * FROM notifications 
ORDER BY created_at DESC 
LIMIT 10;

-- Check notifications for specific trigger
SELECT * FROM notifications 
WHERE trigger_event = 'customer_created'
ORDER BY created_at DESC;

-- Check user notifications
SELECT n.*, nr.name as rule_name 
FROM notifications n
LEFT JOIN notification_rules nr ON n.trigger_event = nr.trigger_event
WHERE JSON_CONTAINS(n.target_users, '{"user_id": 1}')
ORDER BY n.created_at DESC;
```

### Method 3: Log Check (File)
```bash
# Check notification logs
tail -f writable/logs/log-2025-12-19.php | grep -i "notification"

# Check for errors
tail -f writable/logs/log-2025-12-19.php | grep -i "error"

# Check specific trigger
tail -f writable/logs/log-2025-12-19.php | grep "customer_created"
```

---

## 🐛 COMMON ISSUES & FIXES

### Issue 1: Notification Not Appearing
**Symptoms:** Action performed but no notification in bell

**Debug Steps:**
```php
// 1. Check if helper loaded
helper('notification');

// 2. Check if function exists
if (!function_exists('notify_customer_created')) {
    log_message('error', 'Function does not exist!');
}

// 3. Check function return
$result = notify_customer_created($data);
log_message('info', 'Notification result: ' . json_encode($result));

// 4. Check database insert
SELECT * FROM notifications WHERE created_at > NOW() - INTERVAL 5 MINUTE;
```

**Common Causes:**
- Helper not loaded before notify_* call
- Notification rule not active in database
- Target user criteria not met
- Database connection issue

---

### Issue 2: Wrong User Receives Notification
**Symptoms:** Notification sent to incorrect role/user

**Debug Steps:**
```sql
-- Check notification rule configuration
SELECT * FROM notification_rules 
WHERE trigger_event = 'customer_created';

-- Check target_users column
-- Should contain JSON like: {"roles": ["admin"], "divisions": ["marketing"]}

-- Check actual notification targets
SELECT target_users FROM notifications 
WHERE trigger_event = 'customer_created'
ORDER BY created_at DESC LIMIT 1;
```

**Fix:**
```sql
-- Update notification rule
UPDATE notification_rules 
SET target_users = '{"roles": ["admin", "warehouse"], "divisions": ["purchasing"]}'
WHERE trigger_event = 'po_unit_created';
```

---

### Issue 3: Notification Link Broken
**Symptoms:** Click notification but 404 error

**Debug Steps:**
```php
// Check URL in notification data
SELECT id, trigger_event, data 
FROM notifications 
WHERE id = [NOTIFICATION_ID];

// Verify URL format
// Should be: base_url('/module/action/id')
```

**Fix:**
```php
// In controller - ensure proper URL format
notify_customer_created([
    'id' => $customerId,
    'url' => base_url('/customers/view/' . $customerId) // ✅ Correct
    // NOT: '/customers/view/' . $customerId // ❌ Wrong (missing base_url)
]);
```

---

## 📊 TESTING MATRIX

| Module | Create | Update | Delete | Verify | Status |
|--------|--------|--------|--------|--------|--------|
| Customer | ☐ | ☐ | ☐ | N/A | Pending |
| Supplier | ☐ | ☐ | ☐ | N/A | Pending |
| PO Unit | ☐ | N/A | ☐ | ☐ | Pending |
| PO Attachment | ☐ | N/A | ☐ | ☐ | Pending |
| PO Sparepart | ☐ | N/A | ☐ | ☐ | Pending |
| Quotation | ☐ | ☐ | N/A | N/A | Pending |
| Contract | ☐ | N/A | N/A | N/A | Pending |
| SPK | ☐ | N/A | N/A | ☐ | Pending |
| Invoice | ☐ | ☐ | N/A | N/A | Pending |
| Sparepart | N/A | ☐ | N/A | N/A | Pending |
| Unit | N/A | ☐ | N/A | ☐ | Pending |
| Attachment | N/A | ☐ | N/A | N/A | Pending |
| Employee Area | ☐ | N/A | ☐ | N/A | Pending |

**Legend:**
- ☐ = To Test
- ✅ = Passed
- ❌ = Failed
- N/A = Not Applicable

---

## 🎯 TEST DATA TEMPLATES

### Customer Test Data
```json
{
  "customer_code": "CUST-TEST-001",
  "customer_name": "PT Test Company",
  "contact_person": "John Doe",
  "phone": "081234567890",
  "email": "test@example.com",
  "address": "Jl. Test No. 123",
  "city": "Jakarta",
  "province": "DKI Jakarta"
}
```

### PO Test Data
```json
{
  "no_po": "PO-TEST-2025-001",
  "tanggal_po": "2025-12-19",
  "id_supplier": 1,
  "tipe_po": "Unit",
  "keterangan_po": "Test PO untuk notification"
}
```

### Quotation Test Data
```json
{
  "quotation_number": "QUO-TEST-001",
  "prospect_name": "PT Test Prospect",
  "quotation_date": "2025-12-19",
  "valid_until": "2025-12-31",
  "stage": "DRAFT"
}
```

---

## 📈 PERFORMANCE TESTING

### Load Test Scenario
```
1. Create 100 customers simultaneously
2. Monitor:
   - Notification insert time
   - Database query performance
   - Memory usage
   - No duplicate notifications

Expected:
- Insert time < 100ms per notification
- No database locks
- All 100 notifications created
```

### Stress Test
```
1. Update 1000 quotation stages in batch
2. Monitor:
   - Notification queue handling
   - Database connection pool
   - System responsiveness

Expected:
- No notification loss
- System remains responsive
- Queue processes within 5 seconds
```

---

## ✅ ACCEPTANCE CRITERIA

### Functional Requirements
- [ ] All 30 functions trigger notifications correctly
- [ ] Notifications appear in user bell immediately
- [ ] Notification links navigate to correct pages
- [ ] Notifications marked as read after click
- [ ] Only authorized users receive notifications

### Non-Functional Requirements
- [ ] Notification insert time < 100ms
- [ ] No duplicate notifications for same event
- [ ] System handles 1000+ notifications per day
- [ ] Database table doesn't grow unbounded (need archiving strategy)

### User Experience
- [ ] Notification text is clear and actionable
- [ ] Icons/colors match event type
- [ ] Bell badge shows correct unread count
- [ ] Notification list paginated properly

---

## 📋 TEST REPORT TEMPLATE

```markdown
## Notification Test Report

**Date:** [Date]  
**Tester:** [Name]  
**Build:** [Version]

### Summary
- Total Tests: 30
- Passed: [Count]
- Failed: [Count]
- Pending: [Count]

### Failed Tests Detail
1. **[Function Name]**
   - Expected: [Description]
   - Actual: [Description]
   - Error Log: [Log excerpt]
   - Root Cause: [Analysis]
   - Fix: [Solution]

### Performance Metrics
- Average notification insert time: [ms]
- Peak concurrent notifications: [count]
- Database size impact: [MB]

### Recommendations
1. [Recommendation 1]
2. [Recommendation 2]

**Overall Status:** ✅ Pass / ❌ Fail
```

---

## 🚀 QUICK TEST COMMANDS

### Clear All Test Notifications
```sql
DELETE FROM notifications 
WHERE created_at > '2025-12-19 00:00:00'
AND (data LIKE '%TEST%' OR data LIKE '%test%');
```

### Reset Notification Rules
```sql
UPDATE notification_rules 
SET is_active = 1 
WHERE is_active = 0;
```

### Check System Health
```sql
-- Check notification count today
SELECT COUNT(*) as total_today 
FROM notifications 
WHERE DATE(created_at) = CURDATE();

-- Check unread notifications
SELECT COUNT(*) as unread 
FROM notifications 
WHERE is_read = 0;

-- Check error rate
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN data LIKE '%error%' THEN 1 ELSE 0 END) as errors
FROM notifications 
WHERE DATE(created_at) = CURDATE();
```

---

**Happy Testing! 🧪**

*Last Updated: 19 Desember 2025*
