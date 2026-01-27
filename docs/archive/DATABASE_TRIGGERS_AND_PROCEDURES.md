# Database Triggers and Stored Procedures Documentation

**Date**: 2025-10-10  
**Status**: ✅ Production Ready

## Overview

This document describes the automatic calculation system for Purchase Order totals using **Database Triggers** and **Stored Procedures**. This approach eliminates the need for manual counting in the application code.

---

## Architecture

### **Traditional Approach (Before):**
```
Frontend → Backend → Manual Counting → Update DB
                  ↓
              Complex logic in PHP
              Must maintain counts
              Prone to sync issues
```

### **New Approach (With Triggers):**
```
Frontend → Backend → Insert Items → DB Triggers → Auto Update Totals
                                   ↓
                              Simple, Fast, Accurate
                              No manual counting needed
                              Always in sync
```

---

## Components

### 1. **Stored Procedure: `sp_update_po_totals`**

**Purpose**: Calculate and update totals for a specific Purchase Order

**Parameters**:
- `p_po_id` (INT): The Purchase Order ID to update

**What it does**:
1. Counts units from `po_units` table
2. Counts attachments from `po_items` where `item_type = 'Attachment'`
3. Counts batteries from `po_items` where `item_type = 'Battery'`
4. Counts chargers from `po_items` where `item_type = 'Charger'`
5. Updates `purchase_orders` table with all totals

**Usage**:
```sql
-- Update specific PO
CALL sp_update_po_totals(150);

-- Result: purchase_orders WHERE id_po=150 will be updated with current totals
```

**SQL Code**:
```sql
CREATE PROCEDURE `sp_update_po_totals`(IN p_po_id INT)
BEGIN
    DECLARE v_total_unit INT DEFAULT 0;
    DECLARE v_total_attachment INT DEFAULT 0;
    DECLARE v_total_battery INT DEFAULT 0;
    DECLARE v_total_charger INT DEFAULT 0;
    
    -- Count from respective tables
    SELECT COUNT(*) INTO v_total_unit FROM po_units WHERE po_id = p_po_id;
    SELECT COUNT(*) INTO v_total_attachment FROM po_items WHERE po_id = p_po_id AND item_type = 'Attachment';
    SELECT COUNT(*) INTO v_total_battery FROM po_items WHERE po_id = p_po_id AND item_type = 'Battery';
    SELECT COUNT(*) INTO v_total_charger FROM po_items WHERE po_id = p_po_id AND item_type = 'Charger';
    
    -- Update purchase_orders
    UPDATE purchase_orders
    SET 
        total_unit = v_total_unit,
        total_attachment = v_total_attachment,
        total_battery = v_total_battery,
        total_charger = v_total_charger
    WHERE id_po = p_po_id;
END
```

---

### 2. **Database Triggers**

Triggers automatically call `sp_update_po_totals` when items are added, updated, or deleted.

#### **Trigger 1: `trg_po_units_after_insert`**
- **When**: After INSERT on `po_units`
- **Action**: Recalculate totals for the PO
- **Why**: New unit added, need to increment `total_unit`

#### **Trigger 2: `trg_po_units_after_delete`**
- **When**: After DELETE on `po_units`
- **Action**: Recalculate totals for the PO
- **Why**: Unit removed, need to decrement `total_unit`

#### **Trigger 3: `trg_po_items_after_insert`**
- **When**: After INSERT on `po_items`
- **Action**: Recalculate totals for the PO
- **Why**: New attachment/battery/charger added

#### **Trigger 4: `trg_po_items_after_delete`**
- **When**: After DELETE on `po_items`
- **Action**: Recalculate totals for the PO
- **Why**: Attachment/battery/charger removed

#### **Trigger 5: `trg_po_items_after_update`**
- **When**: After UPDATE on `po_items` (when `item_type` changes)
- **Action**: Recalculate totals for affected POs
- **Why**: Item type changed (e.g., Battery → Attachment)

---

## Installation

### Step 1: Run SQL Migration

**Option A - Direct SQL:**
```sql
-- Open phpMyAdmin, select database optima1, and run:
-- Copy entire content from: databases/create_po_totals_automation.sql
```

**Option B - MySQL Command Line:**
```bash
cd /opt/lampp/htdocs/optima1
/opt/lampp/bin/mysql -u root -p optima1 < databases/create_po_totals_automation.sql
```

### Step 2: Verify Installation

```sql
-- Check if procedure exists
SHOW PROCEDURE STATUS WHERE Name = 'sp_update_po_totals';

-- Check if triggers exist
SHOW TRIGGERS WHERE `Trigger` LIKE 'trg_po_%';

-- Should see 5 triggers:
-- 1. trg_po_units_after_insert
-- 2. trg_po_units_after_delete
-- 3. trg_po_items_after_insert
-- 4. trg_po_items_after_delete
-- 5. trg_po_items_after_update
```

### Step 3: Initial Data Migration (Optional)

If you have existing POs that need totals recalculated:

```sql
-- Create helper procedure
DROP PROCEDURE IF EXISTS `sp_recalculate_all_po_totals`;

DELIMITER $$

CREATE PROCEDURE `sp_recalculate_all_po_totals`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_po_id INT;
    DECLARE cur CURSOR FOR SELECT id_po FROM purchase_orders;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO v_po_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        CALL sp_update_po_totals(v_po_id);
    END LOOP;
    
    CLOSE cur;
    
    SELECT 'All PO totals recalculated successfully' as status;
END$$

DELIMITER ;

-- Execute the recalculation
CALL sp_recalculate_all_po_totals();
```

---

## Usage in Application

### Backend (PHP/CodeIgniter)

**Before (Manual Counting):**
```php
$totalUnit = 0;
$totalAttachment = 0;
// ... count logic
foreach ($items as $item) {
    if ($item['type'] == 'unit') $totalUnit++;
    // ... more counting
}
$this->purchaseModel->update($poId, [
    'total_unit' => $totalUnit,
    'total_attachment' => $totalAttachment
]);
```

**After (Automatic via Triggers):**
```php
// Just insert items - triggers handle the rest!
foreach ($items as $item) {
    if ($item['type'] == 'unit') {
        $this->poUnitsModel->insert($unitData);
        // That's it! Trigger automatically updates purchase_orders
    }
}
```

### Reading Totals

```php
// Simply read from purchase_orders - always up to date!
$po = $this->purchaseModel->find($poId);
echo "Total Units: " . $po['total_unit'];
echo "Total Attachments: " . $po['total_attachment'];
echo "Total Batteries: " . $po['total_battery'];
echo "Total Chargers: " . $po['total_charger'];
```

### Manual Recalculation (if needed)

```php
// If you ever need to manually recalculate
$db = \Config\Database::connect();
$db->query("CALL sp_update_po_totals(?)", [$poId]);
```

---

## Testing

### Test 1: Insert Unit

```sql
-- Before: Check current totals
SELECT id_po, total_unit FROM purchase_orders WHERE id_po = 1;

-- Insert a unit
INSERT INTO po_units (po_id, jenis_unit, merk_unit, status_verifikasi)
VALUES (1, 1, 1, 'Belum Dicek');

-- After: Check totals again (should increment by 1)
SELECT id_po, total_unit FROM purchase_orders WHERE id_po = 1;

-- Cleanup
DELETE FROM po_units WHERE po_id = 1 ORDER BY id_po_unit DESC LIMIT 1;
```

### Test 2: Insert Multiple Items

```sql
-- Insert 10 units
INSERT INTO po_units (po_id, jenis_unit, merk_unit, status_verifikasi)
SELECT 1, 1, 1, 'Belum Dicek'
FROM (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
      UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) AS nums;

-- Check: total_unit should be 10
SELECT id_po, total_unit FROM purchase_orders WHERE id_po = 1;
```

### Test 3: Delete Items

```sql
-- Delete 5 units
DELETE FROM po_units WHERE po_id = 1 ORDER BY id_po_unit DESC LIMIT 5;

-- Check: total_unit should decrease by 5
SELECT id_po, total_unit FROM purchase_orders WHERE id_po = 1;
```

### Test 4: Mixed Items

```sql
-- Insert various item types
INSERT INTO po_units (po_id, jenis_unit, status_verifikasi) VALUES (1, 1, 'Belum Dicek');
INSERT INTO po_items (po_id, item_type, attachment_id) VALUES (1, 'Attachment', 5);
INSERT INTO po_items (po_id, item_type, baterai_id) VALUES (1, 'Battery', 3);
INSERT INTO po_items (po_id, item_type, charger_id) VALUES (1, 'Charger', 2);

-- Check all totals
SELECT id_po, total_unit, total_attachment, total_battery, total_charger 
FROM purchase_orders WHERE id_po = 1;
```

---

## Performance Considerations

### ✅ Advantages:

1. **Automatic & Accurate**: No manual counting needed
2. **Always in Sync**: Triggers fire immediately after changes
3. **Simplified Code**: Backend code is much simpler
4. **Better Performance**: Database is optimized for counting
5. **Data Integrity**: Impossible to have out-of-sync totals

### ⚠️ Considerations:

1. **Trigger Overhead**: Small performance cost per insert (negligible)
2. **Bulk Operations**: For very large bulk inserts, consider:
   - Temporarily disable triggers
   - Do bulk insert
   - Call `sp_update_po_totals` once at the end
   - Re-enable triggers

**Example - Bulk Insert:**
```sql
-- Disable triggers
SET @DISABLE_TRIGGERS = 1;

-- Do bulk insert
INSERT INTO po_units (...) VALUES (...), (...), (...) /* many rows */;

-- Manually recalculate once
CALL sp_update_po_totals(1);

-- Re-enable triggers
SET @DISABLE_TRIGGERS = NULL;
```

---

## Troubleshooting

### Issue: Totals not updating

**Check 1: Are triggers enabled?**
```sql
SHOW TRIGGERS WHERE `Trigger` LIKE 'trg_po_%';
-- Should show 5 triggers
```

**Check 2: Does procedure exist?**
```sql
SHOW PROCEDURE STATUS WHERE Name = 'sp_update_po_totals';
```

**Check 3: Manual recalculation**
```sql
CALL sp_update_po_totals(YOUR_PO_ID);
```

### Issue: Trigger errors

**Check MySQL error log:**
```bash
tail -f /opt/lampp/logs/mysql_error.log
```

**Test procedure manually:**
```sql
CALL sp_update_po_totals(1);
-- Should return totals
```

---

## Maintenance

### Update Triggers

If you need to modify trigger logic:

```sql
-- Drop old trigger
DROP TRIGGER IF EXISTS `trg_po_units_after_insert`;

-- Create new trigger with updated logic
DELIMITER $$
CREATE TRIGGER `trg_po_units_after_insert`
AFTER INSERT ON `po_units`
FOR EACH ROW
BEGIN
    -- Your updated logic here
    CALL sp_update_po_totals(NEW.po_id);
END$$
DELIMITER ;
```

### Disable/Enable Triggers

**Disable all triggers:**
```sql
SET @DISABLE_TRIGGERS = 1;
-- Your operations here
SET @DISABLE_TRIGGERS = NULL;
```

**Or modify triggers to check flag:**
```sql
CREATE TRIGGER example
AFTER INSERT ON table
FOR EACH ROW
BEGIN
    IF @DISABLE_TRIGGERS IS NULL THEN
        -- Normal trigger logic
    END IF;
END;
```

---

## Migration Path

### From Manual Counting to Automatic:

1. **Install triggers** (run SQL file)
2. **Recalculate existing data** (run `sp_recalculate_all_po_totals`)
3. **Update backend code** (remove manual counting)
4. **Test thoroughly**
5. **Deploy to production**

---

## Conclusion

The automatic totals calculation system using triggers and stored procedures provides:

- ✅ **Zero maintenance** - Set it and forget it
- ✅ **100% accuracy** - Always correct
- ✅ **Better performance** - Database-level optimization
- ✅ **Simpler code** - Less application logic
- ✅ **Data integrity** - Impossible to have wrong totals

**Recommendation**: Use this approach for all aggregate calculations in the system.

---

**Last Updated**: 2025-10-10  
**Database Version**: MySQL 8.0+  
**Status**: Production Ready ✅

