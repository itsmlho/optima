# Database Template Update Guide
## Standardized Variable Migration for notification_rules

**Generated:** 2025-12-22  
**Purpose:** Update database templates to use standardized variable names  
**Safety:** Conservative approach with backup instructions

---

## 🎯 Overview

This migration updates `title_template` and `message_template` fields in `notification_rules` table to use standardized variable names matching the updated helper functions.

### Standards Applied:
- **Unit:** `{{no_unit}}` (replaces unit_code, unit_no)
- **Customer:** `{{customer}}` (replaces customer_name)  
- **Quantity:** `{{quantity}}` (replaces qty)
- **Sparepart:** `{{sparepart_name}}` (replaces nama_sparepart)
- **Delivery:** `{{nomor_delivery}}` (replaces delivery_number)

---

## ⚠️ IMPORTANT: Pre-Migration Steps

### 1. Backup Database First
```sql
-- Full database backup
mysqldump -u root -p optima_ci > optima_ci_backup_before_var_standardization_$(date +%Y%m%d_%H%M%S).sql

-- Or just notification_rules table
mysqldump -u root -p optima_ci notification_rules > notification_rules_backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Verify Current Templates
```sql
-- Check templates that will be affected
SELECT id, trigger_event, title_template, message_template
FROM notification_rules
WHERE title_template LIKE '%{{unit_code}}%' 
   OR title_template LIKE '%{{customer_name}}%'
   OR title_template LIKE '%{{qty}}%'
   OR message_template LIKE '%{{unit_code}}%'
   OR message_template LIKE '%{{customer_name}}%'
   OR message_template LIKE '%{{qty}}%';
```

---

## 🚀 Migration Options

### Option A: Conservative Updates (Recommended)

Update only critical templates with missing variables or wrong names:

```sql
-- File: databases/standardize_notification_variables.sql
-- Run this file with option A queries uncommented

-- Example queries from the file:
-- 1. delivery_created - add customer
-- 2. work_order_created - add departemen and fix unit
-- 3. delivery_status_changed - add updated_at
-- 4. sparepart_used - standardize quantity/sparepart_name
```

**Run:**
```bash
mysql -u root -p optima_ci < databases/standardize_notification_variables.sql
```

### Option B: Aggressive Standardization (Optional)

Replace ALL old variable names with standardized ones:

```sql
-- Global REPLACE for all templates
-- WARNING: This updates ALL records in notification_rules

-- Unit field standardization
UPDATE notification_rules
SET 
    title_template = REPLACE(REPLACE(title_template, '{{unit_code}}', '{{no_unit}}'), '{{unit_no}}', '{{no_unit}}'),
    message_template = REPLACE(REPLACE(message_template, '{{unit_code}}', '{{no_unit}}'), '{{unit_no}}', '{{no_unit}}')
WHERE title_template LIKE '%{{unit_code}}%' 
   OR title_template LIKE '%{{unit_no}}%'
   OR message_template LIKE '%{{unit_code}}%'
   OR message_template LIKE '%{{unit_no}}%';

-- Customer field standardization
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{customer_name}}', '{{customer}}'),
    message_template = REPLACE(message_template, '{{customer_name}}', '{{customer}}')
WHERE title_template LIKE '%{{customer_name}}%' 
   OR message_template LIKE '%{{customer_name}}%';

-- Quantity standardization
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{qty}}', '{{quantity}}'),
    message_template = REPLACE(message_template, '{{qty}}', '{{quantity}}')
WHERE title_template LIKE '%{{qty}}%' 
   OR message_template LIKE '%{{qty}}%';

-- Sparepart name standardization
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{nama_sparepart}}', '{{sparepart_name}}'),
    message_template = REPLACE(message_template, '{{nama_sparepart}}', '{{sparepart_name}}')
WHERE title_template LIKE '%{{nama_sparepart}}%' 
   OR message_template LIKE '%{{nama_sparepart}}%';

-- Delivery number standardization
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{delivery_number}}', '{{nomor_delivery}}'),
    message_template = REPLACE(message_template, '{{delivery_number}}', '{{nomor_delivery}}')
WHERE title_template LIKE '%{{delivery_number}}%' 
   OR message_template LIKE '%{{delivery_number}}%';
```

---

## ✅ Post-Migration Verification

### 1. Check Updated Templates
```sql
-- Verify delivery_created has customer
SELECT id, trigger_event, title_template, message_template
FROM notification_rules
WHERE trigger_event = 'delivery_created';

-- Verify work_order_created has departemen and no_unit
SELECT id, trigger_event, title_template, message_template
FROM notification_rules
WHERE trigger_event = 'work_order_created';

-- Check all standardized templates
SELECT 
    trigger_event,
    title_template,
    message_template,
    CASE 
        WHEN title_template LIKE '%{{no_unit}}%' OR message_template LIKE '%{{no_unit}}%' THEN 'no_unit ✓'
        WHEN title_template LIKE '%{{customer}}%' OR message_template LIKE '%{{customer}}%' THEN 'customer ✓'
        WHEN title_template LIKE '%{{quantity}}%' OR message_template LIKE '%{{quantity}}%' THEN 'quantity ✓'
        ELSE 'OK'
    END as standard_check
FROM notification_rules
WHERE is_active = 1
ORDER BY trigger_event;
```

### 2. Test Notifications
After migration, test key notifications:

```php
// Test attachment swap (should show attachment_info)
Test: Swap charger on a unit
Expected: Notification shows "JUNGHEINRICH (JHR) 24V / 205AH Lead Acid"

// Test delivery creation (should show customer)
Test: Create new delivery
Expected: Notification shows customer name

// Test work order (should show departemen)
Test: Create work order
Expected: Notification shows department name

// Test PMPS (should show departemen + no_unit)
Test: PMPS due soon
Expected: Notification shows department and unit number
```

### 3. Count Affected Records
```sql
-- See how many templates were updated
SELECT 
    'delivery_created' as event,
    COUNT(*) as count
FROM notification_rules
WHERE trigger_event = 'delivery_created' 
  AND (title_template LIKE '%{{customer}}%' OR message_template LIKE '%{{customer}}%')

UNION ALL

SELECT 
    'work_order_created',
    COUNT(*)
FROM notification_rules
WHERE trigger_event = 'work_order_created'
  AND (title_template LIKE '%{{departemen}}%' OR message_template LIKE '%{{departemen}}%')

UNION ALL

SELECT 
    'unit field standardized',
    COUNT(*)
FROM notification_rules
WHERE title_template LIKE '%{{no_unit}}%' OR message_template LIKE '%{{no_unit}}%';
```

---

## 🔄 Rollback Instructions

If something goes wrong:

### Quick Rollback from Backup
```bash
# Restore full database backup
mysql -u root -p optima_ci < optima_ci_backup_before_var_standardization_YYYYMMDD_HHMMSS.sql

# Or restore just notification_rules table
mysql -u root -p optima_ci < notification_rules_backup_YYYYMMDD_HHMMSS.sql
```

### Manual Rollback (if no backup)
```sql
-- Reverse standardizations (only if you ran Option B)
UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{no_unit}}', '{{unit_code}}'),
    message_template = REPLACE(message_template, '{{no_unit}}', '{{unit_code}}')
WHERE title_template LIKE '%{{no_unit}}%' 
   OR message_template LIKE '%{{no_unit}}%';

UPDATE notification_rules
SET 
    title_template = REPLACE(title_template, '{{customer}}', '{{customer_name}}'),
    message_template = REPLACE(message_template, '{{customer}}', '{{customer_name}}')
WHERE title_template LIKE '%{{customer}}%' 
   OR message_template LIKE '%{{customer}}%';
```

---

## 📋 Execution Checklist

- [ ] Backup database (full or notification_rules table)
- [ ] Verify current templates with SELECT queries
- [ ] Choose migration option (A or B)
- [ ] Run migration SQL
- [ ] Verify templates updated correctly
- [ ] Test 3-5 critical notifications
- [ ] Clear browser cache and reload admin panel
- [ ] Check "Available Variables" modal shows standardized names
- [ ] Monitor first 24 hours for any notification issues
- [ ] Remove backup files after 1 week of stable operation

---

## 🆘 Troubleshooting

### Issue: Variables still showing old names in admin panel

**Solution:**
1. Clear browser cache (Ctrl+F5)
2. Verify JSON file exists: `public/assets/data/notification_variables.json`
3. Check browser console for loading errors
4. Re-run: `python extract_notification_variables.py`

### Issue: Notifications not showing data

**Solution:**
1. Check helper functions provide the variable (check `notification_helper.php`)
2. Verify template uses correct variable name
3. Check controller passes data to notify function
4. Test with: `log_message('debug', 'Notification data: ' . print_r($data, true));`

### Issue: Some notifications show undefined variables

**Solution:**
1. Run audit: `python deep_variable_analysis.py`
2. Check if helper function provides that variable
3. Add missing variable to helper function if needed
4. Update template to use available variables only

---

## 📞 Support

If you encounter issues:
1. Check [NOTIFICATION_FIX_SUMMARY.md](NOTIFICATION_FIX_SUMMARY.md) for implementation details
2. Review [VARIABLE_STANDARDIZATION_MASTER_REPORT.md](VARIABLE_STANDARDIZATION_MASTER_REPORT.md) for complete audit
3. Test with Python audit script: `python deep_variable_analysis.py`

---

## ✨ Success Metrics

After successful migration:
- ✅ All templates use standardized variable names
- ✅ No {{undefined}} or empty values in notifications
- ✅ Available Variables modal shows standards with badges
- ✅ Backward compatibility maintained (old names still work)
- ✅ Working rate: 93%+ (110+/118 events fully functional)

---

**Last Updated:** 2025-12-22  
**Migration File:** databases/standardize_notification_variables.sql  
**Variables JSON:** public/assets/data/notification_variables.json  
**Admin Panel:** app/Views/notifications/admin_panel.php (updated)
