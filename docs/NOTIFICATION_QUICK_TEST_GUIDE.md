# Quick Testing Guide - CRITICAL Priority Notifications

**Date:** 19 December 2024

---

## 🚀 Quick Start - 5 Minute Testing

### Step 1: Run Database Migration
```sql
-- Open your database client (phpMyAdmin, MySQL Workbench, etc.)
USE optima_ci;

-- Run migration file
SOURCE C:/laragon/www/optima/databases/migrations/add_critical_notification_rules_phase1.sql;

-- Quick verify (should return 8 new rules)
SELECT COUNT(*) as new_rules FROM notification_rules 
WHERE trigger_event IN (
    'invoice_created', 'payment_status_updated', 
    'po_created', 'delivery_created', 'delivery_status_changed',
    'workorder_created', 'workorder_status_changed', 'po_verification_updated'
);
```

### Step 2: Test Each Module

## 📋 Finance Module Testing

### Test 1: Create Invoice
1. Login as Finance/Accounting user
2. Go to `/finance/invoices` (or finance module)
3. Click "Create Invoice"
4. Fill form with dummy data:
   - Customer: Any customer
   - Amount: 5000000
   - Due Date: 30 days from now
5. Submit form
6. **Check:** Bell icon should show new notification
7. **Expected:** "Invoice Baru Dibuat - Invoice INV-2024-XXXXX telah dibuat..."

### Test 2: Update Payment Status
1. Go to invoice detail
2. Click "Update Payment Status"
3. Change status to "Paid"
4. Enter payment amount
5. Submit
6. **Check:** Bell icon shows notification
7. **Expected:** "Status Pembayaran Diperbarui - Status pembayaran invoice..."

---

## 📋 Purchasing Module Testing

### Test 3: Create Purchase Order
1. Login as Purchasing user
2. Go to `/purchasing`
3. Click "Create PO"
4. Fill unified PO form:
   - Supplier: Select any
   - PO Date: Today
   - Items: Add at least 1 unit/attachment/sparepart
5. Submit
6. **Check:** Multiple users (Purchasing, Finance, Accounting) should get notification
7. **Expected:** "Purchase Order Baru Dibuat - PO PO-2024-XXX telah dibuat..."

### Test 4: Create Delivery
1. Go to existing PO
2. Click "Schedule Delivery"
3. Fill delivery form:
   - Delivery Date: Future date
   - Packing List No: PL/YYYYMM/001
   - Select items to deliver
4. Submit
5. **Check:** Warehouse team gets notification
6. **Expected:** "Pengiriman Baru Dijadwalkan - Pengiriman PL/YYYYMM/001..."

### Test 5: Update Delivery Status
1. Go to delivery detail
2. Click "Update Status"
3. Change from "Scheduled" to "In Transit"
4. Submit
5. **Check:** Warehouse Manager & Purchasing get notification
6. **Expected:** "Status Pengiriman Berubah - Status pengiriman... telah berubah dari Scheduled menjadi In Transit"

---

## 📋 WorkOrder Module Testing

### Test 6: Create Work Order
1. Login as Service/Workshop user
2. Go to `/service/work-orders`
3. Click "Create Work Order"
4. Fill form:
   - Unit: Select any unit
   - Order Type: Corrective/Preventive
   - Priority: High
   - Category: Select category
   - Complaint: "Test WO - Unit tidak bisa start"
5. Submit
6. **Check:** Service team (Manager, Supervisor, Foreman, Staff) gets notification
7. **Expected:** "Work Order Baru Dibuat - Work Order WO-2024-XXX telah dibuat..."

### Test 7: Update WO Status
1. Go to WO detail
2. Click "Update Status"
3. Change status (e.g., from "Open" to "Assigned")
4. Add notes: "Assigned to Foreman John"
5. Submit
6. **Check:** Service managers get notification
7. **Expected:** "Status Work Order Berubah - Status Work Order WO-2024-XXX telah berubah dari Open menjadi Assigned"

---

## 📋 Warehouse PO Verification Testing

### Test 8: Verify PO Items
1. Login as Warehouse/QC user
2. Go to `/warehouse/po-verification`
3. Select PO with delivered items
4. For each item, set verification status:
   - Status: "Sesuai" or "Tidak Sesuai"
   - Comments: "Quantity and quality OK" or "Missing 2 units"
5. Submit verification
6. **Check:** Purchasing Manager, Finance Manager, QC Supervisor get notification
7. **Expected:** "Verifikasi PO Diperbarui - Verifikasi untuk PO PO-2024-XXX telah diperbarui dengan status: Sesuai"

---

## 🔍 How to Check Notifications

### Method 1: UI (Recommended)
1. Click **bell icon** (🔔) in top navigation
2. Should see dropdown with new notifications
3. Click notification to go to detail page
4. Notification should mark as read

### Method 2: Database Query
```sql
-- Check latest notifications
SELECT 
    n.id,
    n.title,
    n.message,
    n.is_read,
    n.created_at,
    u.username
FROM notifications n
LEFT JOIN users u ON n.user_id = u.id
WHERE n.created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
ORDER BY n.created_at DESC
LIMIT 20;

-- Check which users got notification for specific event
SELECT 
    n.title,
    u.username,
    u.division,
    u.role,
    n.created_at
FROM notifications n
LEFT JOIN users u ON n.user_id = u.id
WHERE n.title LIKE '%Invoice Baru Dibuat%'
ORDER BY n.created_at DESC;
```

### Method 3: Check Logs
```bash
# Windows PowerShell
Get-Content C:\laragon\www\optima\writable\logs\log-*.php | Select-String "Notification sent"

# Or view last 50 lines
Get-Content C:\laragon\www\optima\writable\logs\log-2024-12-19.php -Tail 50 | Select-String "notification"
```

---

## ✅ Success Criteria Checklist

### Finance Module
- [ ] Invoice creation sends notification to Finance, Accounting, Marketing teams
- [ ] Payment status update sends notification to managers
- [ ] Notification message shows invoice number, customer name, amount
- [ ] URL in notification redirects to correct invoice page

### Purchasing Module
- [ ] PO creation sends notification to Purchasing, Finance, Accounting
- [ ] Delivery schedule sends notification to Warehouse team
- [ ] Delivery status change sends notification to relevant teams
- [ ] Notification shows PO number, supplier name, delivery info

### WorkOrder Module
- [ ] WO creation sends notification to Service team (all roles)
- [ ] WO status change sends notification to managers/supervisors
- [ ] Notification shows WO number, unit code, status changes
- [ ] Priority and category information appears in notification

### WarehousePO Module
- [ ] PO verification sends notification to Purchasing, Finance, QC
- [ ] Notification shows verification status (Sesuai/Tidak Sesuai)
- [ ] Comments from verifier appear in notification message

---

## 🐛 Troubleshooting

### Issue: No notification appears

**Check 1:** Is helper loaded?
```php
// Should be in controller:
helper('notification');
```

**Check 2:** Are notification rules active?
```sql
SELECT * FROM notification_rules 
WHERE trigger_event = 'event_name' AND is_active = 1;
```

**Check 3:** Does user have correct division/role?
```sql
-- Check user
SELECT id, username, division, role FROM users WHERE id = YOUR_USER_ID;

-- Check rule targets
SELECT target_divisions, target_roles FROM notification_rules 
WHERE trigger_event = 'event_name';
```

**Check 4:** Check application logs
```bash
# Look for errors
Get-Content C:\laragon\www\optima\writable\logs\log-*.php | Select-String "Failed to send notification"

# Look for success
Get-Content C:\laragon\www\optima\writable\logs\log-*.php | Select-String "Notification sent"
```

---

### Issue: Wrong users receiving notifications

**Solution:** Update notification rule targets
```sql
-- Update target divisions
UPDATE notification_rules 
SET target_divisions = 'Finance,Accounting,Marketing'
WHERE trigger_event = 'invoice_created';

-- Update target roles
UPDATE notification_rules 
SET target_roles = 'Director,Manager,Supervisor'
WHERE trigger_event = 'payment_status_updated';
```

---

### Issue: Notification sent but not visible

**Check 1:** User's division/role doesn't match rule
```sql
SELECT u.username, u.division, u.role, nr.target_divisions, nr.target_roles
FROM users u
CROSS JOIN notification_rules nr
WHERE u.id = YOUR_USER_ID 
AND nr.trigger_event = 'event_name';
```

**Check 2:** Check notifications table directly
```sql
SELECT * FROM notifications 
WHERE user_id = YOUR_USER_ID 
AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

---

## 📊 Verification Report Template

After testing, fill this template:

```
## Testing Report - CRITICAL Notifications
Date: __________
Tester: __________

### Finance Module
✅ Invoice Created: [PASS/FAIL] - Notes: _______________
✅ Payment Updated: [PASS/FAIL] - Notes: _______________

### Purchasing Module
✅ PO Created: [PASS/FAIL] - Notes: _______________
✅ Delivery Created: [PASS/FAIL] - Notes: _______________
✅ Delivery Status: [PASS/FAIL] - Notes: _______________

### WorkOrder Module
✅ WO Created: [PASS/FAIL] - Notes: _______________
✅ WO Status: [PASS/FAIL] - Notes: _______________

### WarehousePO Module
✅ PO Verification: [PASS/FAIL] - Notes: _______________

### Overall Assessment
Coverage: __/8 tests passed
Issues Found: _____________________________
Recommendations: _________________________
```

---

## 🚀 Quick Performance Test

**Test notification delivery speed:**

```sql
-- Create test event timestamp
SELECT NOW() as start_time;

-- Trigger event (e.g., create invoice)
-- ... perform action in UI ...

-- Check when notification was created
SELECT 
    created_at,
    TIMESTAMPDIFF(SECOND, 'start_time_from_above', created_at) as seconds_delay
FROM notifications 
WHERE title LIKE '%Invoice Baru Dibuat%'
ORDER BY created_at DESC LIMIT 1;
```

**Expected:** < 2 seconds delay

---

## 📞 Contact

If you encounter issues during testing:
1. Check logs: `writable/logs/log-*.php`
2. Check database: notification_rules and notifications tables
3. Refer to full documentation: `NOTIFICATION_PHASE1_IMPLEMENTATION_COMPLETE.md`

---

**Happy Testing!** 🎉
