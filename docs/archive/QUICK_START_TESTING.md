# 🧪 QUICK START TESTING GUIDE

**Test Date:** December 19, 2024  
**Status:** Ready for Testing  
**Estimated Time:** 15 minutes

---

## ✅ DEPLOYMENT VERIFIED

**Database Status:**
- ✅ 116 total notification rules deployed
- ✅ 39 new rules from Phase 1, 2, 3
- ✅ All rules set to active (is_active = 1)

---

## 🎯 5 IMMEDIATE TESTS

### Test 1: Customer Created (2 minutes)

**Steps:**
1. Login ke sistem Optima
2. Navigate: **Customer Management**
3. Click: **"Add New Customer"**
4. Fill form:
   ```
   Customer Code: TEST-DEPLOY-001
   Customer Name: Test Deployment Customer
   Phone: 081234567890
   Email: test@deploy.com
   ```
5. Add primary location (any area)
6. Click **Save**

**Expected Result:**
- ✅ Customer created successfully
- ✅ Check Notification bell icon (top right)
- ✅ Find notification: "Customer Baru: Test Deployment Customer"
- ✅ Recipients: Marketing Manager, Sales, Admin

**Verification Query:**
```sql
SELECT * FROM notifications 
WHERE notification_type = 'customer_created' 
ORDER BY created_at DESC LIMIT 1;
```

---

### Test 2: Customer Updated (2 minutes)

**Steps:**
1. Find customer **TEST-DEPLOY-001**
2. Click **Edit**
3. Change phone: 081234567890 → 081999888777
4. Change email: test@deploy.com → updated@deploy.com
5. Click **Save**

**Expected Result:**
- ✅ Update successful
- ✅ Notification: "Customer Diperbarui: Test Deployment Customer"
- ✅ Message shows changes: "Phone: 081234567890 → 081999888777, Email: test@deploy.com → updated@deploy.com"

---

### Test 3: Customer Status Changed (2 minutes)

**Steps:**
1. Edit customer **TEST-DEPLOY-001** again
2. Change **Is Active**: ✅ Active → ❌ Inactive
3. Click **Save**

**Expected Result:**
- ✅ **TWO notifications created:**
  1. "Customer Diperbarui" (general update)
  2. "Status Customer Berubah" (specific status change - HIGH priority)
- ✅ Status notification shows: "dari Active ke Inactive"

---

### Test 4: Warehouse Stock Alert (1 minute)

**Steps:**
1. Navigate: **Warehouse Division** → **Dashboard**
2. Page loads automatically
3. View "Low Stock Alerts" section

**Expected Result:**
- ✅ Dashboard shows 3 low stock items
- ✅ **2 notifications created** (High + Medium urgency only):
  - "Alert Stok Rendah: Brake Pad Set"
  - "Alert Stok Rendah: Tire Set"
- ✅ No notification for "Cleaning Detergent" (Low urgency filtered)

**Note:** Stock alert notifications trigger every dashboard load. In production, add throttling (1 alert per 24h per item).

---

### Test 5: Quotation Sent (3 minutes)

**Steps:**
1. Navigate: **Marketing** → **Quotations**
2. Find quotation with stage = "QUOTATION"
3. Make sure quotation has specifications (minimum 1 item)
4. Click **"Send to Customer"** button
5. Confirm action

**Expected Result:**
- ✅ Quotation stage changes to "SENT"
- ✅ sent_at timestamp populated
- ✅ Notification: "Quotation Terkirim: Q-2024-12-XXX"
- ✅ Message includes customer name, email, send method
- ✅ Recipients: Marketing Manager, Sales, Admin

---

## 📊 VERIFICATION QUERIES

### Check Today's Notifications
```sql
SELECT 
    n.id,
    n.notification_type as event,
    n.title,
    LEFT(n.message, 100) as message_preview,
    n.created_at,
    u.username as recipient
FROM notifications n
LEFT JOIN users u ON n.user_id = u.id
WHERE DATE(n.created_at) = CURDATE()
ORDER BY n.created_at DESC
LIMIT 20;
```

### Count Notifications by Type (Today)
```sql
SELECT 
    notification_type,
    COUNT(*) as count
FROM notifications
WHERE DATE(created_at) = CURDATE()
  AND notification_type IN (
    'customer_created', 'customer_updated', 'customer_status_changed',
    'warehouse_stock_alert', 'quotation_sent_to_customer'
  )
GROUP BY notification_type
ORDER BY count DESC;
```

### Check Active Notification Rules
```sql
SELECT 
    trigger_event,
    name,
    type,
    priority,
    is_active,
    target_divisions,
    target_roles
FROM notification_rules
WHERE trigger_event IN (
    'customer_created', 'customer_updated', 'customer_status_changed',
    'warehouse_stock_alert', 'quotation_sent_to_customer'
)
ORDER BY priority, trigger_event;
```

---

## ✅ SUCCESS CHECKLIST

After completing all 5 tests, you should have:

- [ ] Test 1: 1 customer_created notification
- [ ] Test 2: 1 customer_updated notification  
- [ ] Test 3: 2 notifications (customer_updated + customer_status_changed)
- [ ] Test 4: 2 warehouse_stock_alert notifications
- [ ] Test 5: 1 quotation_sent_to_customer notification

**Total Expected:** 7 notifications minimum

Run this query to verify:
```sql
SELECT 
    CASE 
        WHEN notification_type = 'customer_created' THEN '✅ Test 1'
        WHEN notification_type = 'customer_updated' THEN '✅ Test 2 & 3'
        WHEN notification_type = 'customer_status_changed' THEN '✅ Test 3'
        WHEN notification_type = 'warehouse_stock_alert' THEN '✅ Test 4'
        WHEN notification_type = 'quotation_sent_to_customer' THEN '✅ Test 5'
        ELSE 'Other'
    END as test_case,
    COUNT(*) as notifications_count
FROM notifications
WHERE DATE(created_at) = CURDATE()
GROUP BY test_case
ORDER BY test_case;
```

---

## 🐛 TROUBLESHOOTING

### Issue: No Notification Created

**Check 1: Rule exists and active**
```sql
SELECT * FROM notification_rules 
WHERE trigger_event = 'customer_created' 
AND is_active = 1;
```
Expected: At least 1 row

**Check 2: Helper function loaded**
```php
// In controller, check if this returns true:
helper('notification');
var_dump(function_exists('notify_customer_created')); // Should be true
```

**Check 3: User has target role**
```sql
SELECT u.username, r.name as role_name
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
LEFT JOIN roles r ON mhr.role_id = r.id
WHERE u.id = [YOUR_USER_ID];
```
User's role should match target_roles in notification_rules

---

### Issue: Notification Not Visible in UI

**Check notification exists:**
```sql
SELECT * FROM notifications
WHERE user_id = [YOUR_USER_ID]
AND DATE(created_at) = CURDATE()
ORDER BY created_at DESC;
```

If exists but not visible:
- Reload page (Ctrl+F5)
- Check notification panel filter (All vs Unread)
- Clear browser cache

---

### Issue: Duplicate Notifications

**Expected duplicates:**
- Test 3 creates TWO notifications by design (update + status_change)
- Test 4 creates multiple alerts (one per low stock item)

**Unwanted duplicates:**
Check if function called multiple times:
```sql
SELECT 
    notification_type,
    COUNT(*) as count,
    GROUP_CONCAT(DISTINCT user_id) as users
FROM notifications
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
GROUP BY notification_type
HAVING COUNT(*) > 2;
```

---

## 📈 PERFORMANCE MONITORING

### Monitor Notification Creation Rate
```sql
SELECT 
    DATE(created_at) as date,
    HOUR(created_at) as hour,
    COUNT(*) as notifications_per_hour
FROM notifications
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY date, hour
ORDER BY date DESC, hour DESC;
```

### Check Slowest Notification Types
```sql
SELECT 
    notification_type,
    COUNT(*) as total_sent,
    AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_processing_time_sec
FROM notifications
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY notification_type
ORDER BY avg_processing_time_sec DESC;
```

---

## 🎯 NEXT STEPS AFTER TESTING

### If All Tests Pass ✅
1. **Enable in Production** - System is ready
2. **Brief Stakeholders** - Inform team about new notifications
3. **Monitor for 48 hours** - Track delivery success rate
4. **Collect Feedback** - Ask users about notification quality

### If Tests Fail ❌
1. **Review Error Logs** - Check `writable/logs/`
2. **Verify Database** - Run verification queries above
3. **Check Code** - Ensure helper functions exist
4. **Contact Support** - Refer to DEPLOYMENT_COMPLETE.md

---

## 📞 DOCUMENTATION REFERENCES

- **Full Deployment Report:** `docs/DEPLOYMENT_COMPLETE.md`
- **Phase 1 Details:** `docs/NOTIFICATION_PHASE1_IMPLEMENTATION_SUMMARY.md`
- **Phase 2 Details:** `docs/NOTIFICATION_PHASE2_COMPLETION_REPORT.md`
- **Phase 3 Details:** `docs/NOTIFICATION_PHASE3_COMPLETION_REPORT.md`
- **Detailed Test Scenarios:** `docs/NOTIFICATION_PHASE3_TEST_GUIDE.md`

---

**Testing Guide Version:** 1.0  
**Last Updated:** December 19, 2024  
**Estimated Testing Time:** 15 minutes  
**Expected Success Rate:** 100% (all 5 tests should pass)

🚀 **Happy Testing!** 🚀
