# QUICK START: Database Template Standardization
## Fast Track Guide for Busy Developers

⏱️ **Time Required:** 15-30 minutes  
🎯 **Goal:** Synchronize database templates with standardized variables  
⚠️ **Risk:** Low (backward compatible + rollback ready)

---

## 🚀 Quick 6-Step Process

### Step 1: Backup (2 minutes) ⚠️ MANDATORY
```bash
cd C:\laragon\www\optima
mysqldump -u root -p optima_ci notification_rules > notification_rules_backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql
```
> **CRITICAL:** Jangan skip step ini!

---

### Step 2: Verify Current State (1 minute)
```sql
-- Open HeidiSQL or MySQL Workbench
-- Run this to see what will be updated:

SELECT trigger_event, title_template, message_template
FROM notification_rules
WHERE trigger_event IN ('delivery_created', 'work_order_created', 'sparepart_used')
  AND is_active = 1;
```
> **Expected:** You'll see templates with old variable names

---

### Step 3: Run Migration (2 minutes)
```bash
# PowerShell
cd C:\laragon\www\optima
Get-Content databases\standardize_notification_variables.sql | mysql -u root -p optima_ci
```

**OR via HeidiSQL:**
1. Open `databases/standardize_notification_variables.sql`
2. Select all (Ctrl+A)
3. Execute (F9)

---

### Step 4: Verify Updates (2 minutes)
```sql
-- Check if updates applied:
SELECT 
    trigger_event,
    CASE 
        WHEN message_template LIKE '%{{customer}}%' THEN '✓ Has customer'
        WHEN message_template LIKE '%{{departemen}}%' THEN '✓ Has departemen'
        WHEN message_template LIKE '%{{no_unit}}%' THEN '✓ Has no_unit'
        ELSE 'Check manually'
    END as check_result
FROM notification_rules
WHERE trigger_event IN ('delivery_created', 'work_order_created', 'sparepart_used');
```
> **Expected:** All should show checkmarks

---

### Step 5: Clear Cache & Test UI (3 minutes)

#### A. Clear Browser Cache:
```
Chrome/Edge: Ctrl+Shift+Delete → Clear cache
OR: Ctrl+F5 (hard reload)
```

#### B. Test Available Variables Modal:
1. Go to: http://localhost/optima/notifications/admin
2. Click "Available Variables" button
3. **Verify:**
   - ✅ Green banner at top showing standards
   - ✅ Green "STANDARD" badges on `no_unit`, `customer`, `quantity`
   - ✅ Yellow "ALIAS" badges on `unit_code`, `customer_name`, `qty`
   - ✅ Search works
   - ✅ Click variable to copy

---

### Step 6: Test Notifications (5-10 minutes)

Test 3 critical scenarios:

#### Test 1: Attachment Swap
```
1. Go to Warehouse → Inventory Unit
2. Select unit with charger
3. Swap charger
4. Check notification
Expected: Shows "MERK MODEL TYPE" not empty
```

#### Test 2: Delivery Creation
```
1. Go to Deliveries → Create New
2. Fill form and save
3. Check notification
Expected: Shows customer name
```

#### Test 3: Work Order Creation
```
1. Go to Work Orders → Create New
2. Fill form and save
3. Check notification
Expected: Shows department name and unit number
```

---

## ✅ Success Indicators

After completing all steps, you should see:

| Check | Expected Result |
|-------|----------------|
| Database | Templates use `{{no_unit}}`, `{{customer}}`, `{{quantity}}` |
| Admin UI | Available Variables shows STANDARD/ALIAS badges |
| Notifications | All show correct data (no empty values) |
| Logs | No errors in `writable/logs/` |

---

## 🆘 If Something Goes Wrong

### Problem: SQL Error During Migration

**Quick Fix:**
```bash
# Rollback
mysql -u root -p optima_ci < notification_rules_backup_YYYYMMDD_HHMMSS.sql

# Check error in SQL file
# Find problematic query
# Run queries one by one manually
```

---

### Problem: Variables Still Show Old Names in UI

**Quick Fix:**
```bash
# Re-generate JSON
python extract_notification_variables.py

# Force browser refresh
Ctrl+Shift+F5

# Check file
cat public/assets/data/notification_variables.json | Select -First 20
```

---

### Problem: Notification Shows Empty Value

**Quick Fix:**
```php
// Check if helper provides variable
// Open: app/Helpers/notification_helper.php
// Find: function notify_[event_name]
// Verify array contains the variable

// Example:
function notify_delivery_created($delivery_id) {
    return send_notification('delivery_created', [
        'customer' => $data['customer_name'] ?? '',  // ← Check this exists
        // ...
    ]);
}
```

---

## 📊 What Got Updated

### Code (Already Done ✅):
- 50+ helper functions
- attachment_info fix
- departemen added to 12 events
- customer standardized in 10 events

### Database (You're Doing Now 🔄):
- delivery_created templates
- work_order_created templates
- sparepart_used templates
- Optional: ALL templates (conservative vs aggressive)

### UI (Already Done ✅):
- notification_variables.json auto-generated
- Available Variables modal enhanced
- Standards documentation added

---

## 📝 Command Reference Card

```bash
# BACKUP
mysqldump -u root -p optima_ci notification_rules > backup.sql

# MIGRATE
mysql -u root -p optima_ci < databases/standardize_notification_variables.sql

# ROLLBACK
mysql -u root -p optima_ci < backup.sql

# VERIFY
mysql -u root -p optima_ci -e "SELECT trigger_event, message_template FROM notification_rules WHERE trigger_event='delivery_created'"

# REGENERATE UI
python extract_notification_variables.py

# CHECK LOGS
tail -f writable/logs/log-*.php | grep notification
```

---

## 🎯 TL;DR (Too Long; Didn't Read)

```bash
# 1. Backup
mysqldump -u root -p optima_ci notification_rules > backup.sql

# 2. Migrate
mysql -u root -p optima_ci < databases/standardize_notification_variables.sql

# 3. Verify
# - Check database templates updated
# - Clear browser cache (Ctrl+F5)
# - Test Available Variables modal
# - Test 3 notifications

# 4. Done! ✅
```

---

## ⏭️ After This

Once database migration successful:

1. ✅ **Monitor** notifications for 24 hours
2. ✅ **Update** any custom templates in admin panel
3. ✅ **Train** team on new standard variable names
4. ✅ **Document** any issues found
5. ✅ **Remove** backup file after 1 week of stable operation

---

**Need Help?** Check these files:
- Full Guide: `DATABASE_TEMPLATE_UPDATE_GUIDE.md`
- Complete Summary: `THREE_LAYER_SYNCHRONIZATION_SUMMARY.md`
- Implementation Details: `NOTIFICATION_FIX_SUMMARY.md`

**Estimated Total Time:** 15-30 minutes  
**Difficulty:** Easy 🟢  
**Impact:** High ⚡  
**Risk:** Low 🛡️

---

**Ready?** Start with Step 1 (Backup)! ⬆️
