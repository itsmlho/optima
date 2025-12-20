# Quick Testing Guide - Cross-Division Notifications

## ✅ Implementation Complete - Ready to Test

**All 10 tasks completed:**
- 9 helper functions created ✅
- 10 controller notification calls added ✅
- 6 database rules updated/created ✅
- 0 syntax errors ✅

---

## Test Scenarios (13 Total)

### 🔥 Priority 1: SPK Stages (3 tests)

#### Test 1: Unit Preparation Stage
**Steps:**
1. Login as Service user
2. Open SPK detail page
3. Click "Approve Persiapan Unit"
4. Fill form and submit

**Expected:**
- ✅ Marketing receives: "Unit Preparation Completed - SPK-XXX"
- ✅ Warehouse receives: "Unit Preparation Completed - SPK-XXX"

**Check:** `/marketing/notifications` and `/warehouse/notifications`

---

#### Test 2: Fabrication Stage
**Steps:**
1. Login as Service user
2. Open SPK with approved persiapan_unit
3. Click "Approve Fabrikasi"
4. Fill form and submit

**Expected:**
- ✅ Marketing receives: "Fabrication Completed - SPK-XXX"
- ✅ Warehouse receives: "Fabrication Completed - SPK-XXX"

---

#### Test 3: PDI Stage
**Steps:**
1. Login as Service user
2. Open SPK with approved fabrikasi
3. Click "Approve PDI"
4. Fill form and submit

**Expected:**
- ✅ Marketing receives: "PDI Completed - SPK Ready - SPK-XXX"
- ✅ Operational receives: "PDI Completed - SPK Ready - SPK-XXX"

---

### 🔧 Priority 2: Attachments (4 tests)

#### Test 4: Add Attachment
**Steps:**
1. Login as Service user
2. Go to unit verification modal
3. Add new attachment (battery/charger/attachment)
4. Submit

**Expected:**
- ✅ Warehouse receives: "Attachment Added" notification

---

#### Test 5: Attach to Unit
**Steps:**
1. Login as Warehouse user
2. Go to attachment management
3. Attach attachment to a unit
4. Submit

**Expected:**
- ✅ Service receives: "Attachment Attached to Unit" notification

---

#### Test 6: Detach from Unit
**Steps:**
1. Login as Warehouse user
2. Select attached attachment
3. Detach from unit
4. Provide reason and submit

**Expected:**
- ✅ Service receives: "Attachment Detached from Unit" notification

---

#### Test 7: Swap Between Units
**Steps:**
1. Login as Warehouse user
2. Select attached attachment
3. Swap to different unit
4. Provide reason and submit

**Expected:**
- ✅ Service receives: "Attachment Swapped" notification

---

### 📦 Priority 3: Other Workflows (3 tests)

#### Test 8: Create SPK from Quotation (Batch)
**Steps:**
1. Login as Marketing user
2. Open quotation with specifications
3. Select multiple specifications
4. Click "Create SPK"

**Expected:**
- ✅ Service receives notification for EACH created SPK
- ✅ Example: 3 SPKs created = 3 notifications

---

#### Test 9: PO Delivery Created
**Steps:**
1. Login as Purchasing user
2. Create PO delivery schedule
3. Fill delivery details and submit

**Expected:**
- ✅ Warehouse receives: "PO Delivery Created" notification
- ✅ Purchasing receives: "PO Delivery Created" notification

---

#### Test 10: Sparepart Return Confirmed
**Steps:**
1. Login as Warehouse user
2. Go to sparepart usage page
3. Confirm sparepart return
4. Add notes and submit

**Expected:**
- ✅ Service receives: "Sparepart Return Confirmed" notification

---

### 🔄 Priority 4: Updated Rules (3 tests)

#### Test 11: Unit Status Changed
**Steps:**
1. Login as Warehouse user
2. Edit unit details
3. Change status (e.g., AVAILABLE → MAINTENANCE)
4. Submit

**Expected:**
- ✅ Warehouse receives notification (existing)
- ✅ Service receives notification (NEW - added today)

---

#### Test 12: PO Verified
**Steps:**
1. Login as Warehouse user
2. Go to PO verification
3. Verify PO items
4. Submit verification

**Expected:**
- ✅ Purchasing receives notification (existing)
- ✅ Warehouse receives notification (NEW - added today)

---

#### Test 13: DI Created (Verification)
**Steps:**
1. Login as Marketing user
2. Create new DI from SPK
3. Fill form and submit

**Expected:**
- ✅ Operational receives: "DI Created" notification
- ✅ (This was already working, just verify still works)

---

## Database Verification Queries

### Check New Rules
```sql
SELECT id, name, trigger_event, target_divisions 
FROM notification_rules 
WHERE id IN (143, 144, 145, 146);
```

**Expected:**
- ID 143: spk_unit_prep_completed → marketing,warehouse
- ID 144: spk_fabrication_completed → marketing,warehouse
- ID 145: spk_pdi_completed → marketing,operational
- ID 146: sparepart_returned → service

### Check Updated Rules
```sql
SELECT id, trigger_event, target_divisions 
FROM notification_rules 
WHERE id IN (55, 76);
```

**Expected:**
- ID 55: inventory_unit_status_changed → warehouse,service (was: warehouse)
- ID 76: po_verified → purchasing,warehouse (was: purchasing,accounting)

---

## Notification Locations by Division

### Marketing
- URL: `/marketing/notifications`
- Expected: SPK stages (3), DI completed

### Service
- URL: `/service/notifications`
- Expected: Attachments (3), Unit status, Sparepart return, PO verified

### Warehouse
- URL: `/warehouse/notifications`
- Expected: SPK stages (2), Attachments (1), PO delivery, PO verified

### Operational
- URL: `/operational/notifications`
- Expected: SPK PDI completed, DI created

### Purchasing
- URL: `/purchasing/notifications`
- Expected: PO delivery, PO verified

---

## Troubleshooting

### No Notification Received?
1. Check `writable/logs/log-*.php` for errors
2. Verify rule is active: `SELECT * FROM notification_rules WHERE trigger_event = 'xxx'`
3. Check user's division: `SELECT division FROM users WHERE id = ?`
4. Verify function exists: Search notification_helper.php for `notify_xxx`

### Wrong Division Receives Notification?
1. Check target_divisions: `SELECT target_divisions FROM notification_rules WHERE trigger_event = 'xxx'`
2. Should match user's division field

### Function Not Found Error?
1. Check if function_exists() is used
2. Verify function name matches helper file
3. Check helper file is loaded: `helper('notification');`

---

## Success Indicators

✅ **All Working:**
- No errors in logs
- Notifications appear in bell icon
- Correct divisions receive notifications
- Notification count increases in badge

❌ **Issues:**
- Check logs immediately
- Verify database rules
- Check user division assignment
- Review function names in code

---

## Log Files to Monitor

1. **Application Log:** `writable/logs/log-2024-12-19.php`
2. **Error Log:** Check for PHP errors
3. **Notification Log:** Search for "notify_" in logs

---

## Quick Commands

### View Recent Notifications
```sql
SELECT * FROM notifications 
ORDER BY created_at DESC 
LIMIT 20;
```

### Count by Trigger Event
```sql
SELECT trigger_event, COUNT(*) as count 
FROM notifications 
WHERE DATE(created_at) = CURDATE() 
GROUP BY trigger_event;
```

### Check User's Division
```sql
SELECT id, username, division 
FROM users 
WHERE id = ?;
```

---

## Support

**Issues?** Check:
1. Logs: `writable/logs/`
2. Database: Run verification queries above
3. Documentation: `docs/IMPLEMENTATION_COMPLETE_SUMMARY.md`

**Status:** ✅ READY FOR TESTING
**Date:** December 19, 2024
