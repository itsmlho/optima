# Hour Meter Implementation Guide

## Overview
System untuk tracking hour meter (HM) unit forklift. Hour meter digunakan untuk tracking total jam operasional unit sebagai basis maintenance scheduling dan monitoring kondisi unit.

## Database Schema

### 1. Table: `work_orders`
```sql
`hm` INT(11) DEFAULT NULL COMMENT 'Hour Meter at time of work order'
```
**Purpose**: Menyimpan pembacaan hour meter **saat work order dibuat/diselesaikan** (historical data)

### 2. Table: `inventory_unit` (NEW)
```sql
`hour_meter` INT(11) DEFAULT NULL COMMENT 'Current hour meter reading'
```
**Purpose**: Menyimpan pembacaan hour meter **terkini** dari unit (current state)

## Migration

**File**: `databases/migrations/add_hour_meter_to_inventory_unit.sql`

```sql
-- Add hour_meter column to inventory_unit
ALTER TABLE `inventory_unit` 
ADD COLUMN `hour_meter` INT(11) DEFAULT NULL COMMENT 'Current hour meter reading' 
AFTER `workflow_status`;

-- Add index for faster queries
ALTER TABLE `inventory_unit` 
ADD INDEX `idx_hour_meter` (`hour_meter`);

-- Populate existing units from latest work orders
UPDATE `inventory_unit` iu
INNER JOIN (
    SELECT unit_id, MAX(hm) as latest_hm
    FROM `work_orders`
    WHERE hm IS NOT NULL
    GROUP BY unit_id
) wo ON iu.id_inventory_unit = wo.unit_id
SET iu.hour_meter = wo.latest_hm
WHERE iu.hour_meter IS NULL;
```

**How to run**:
```powershell
# Connect to MySQL
mysql -u root -p optima_ci

# Run migration
source c:/laragon/www/optima/databases/migrations/add_hour_meter_to_inventory_unit.sql
```

## Implementation Details

### 1. Unit Verification Form
**File**: `app/Views/service/unit_verification.php`

Added hour meter input field:
```html
<tr>
    <td>Hour Meter (HM)</td>
    <td><span class="text-muted fst-italic">Input manual</span></td>
    <td>
        <input type="number" 
               class="form-control form-control-sm" 
               id="verify-hm" 
               name="hm" 
               placeholder="Hour meter saat ini" 
               min="0" 
               step="1">
    </td>
    <td class="text-center">
        <input type="checkbox" 
               class="form-check-input" 
               id="check-hm">
    </td>
</tr>
```

### 2. Backend - Save Hour Meter
**File**: `app/Controllers/WorkOrderController.php`
**Function**: `saveUnitVerification()`

**Logic Flow**:
1. When unit verification is saved, hour meter is captured from form input
2. Hour meter is saved to **2 places**:
   - `work_orders.hm` → Historical record of HM at this service
   - `inventory_unit.hour_meter` → Current HM reading for this unit

```php
// Update inventory_unit with current hour meter
$hourMeter = $this->request->getPost('hm');
if (!empty($hourMeter) && is_numeric($hourMeter)) {
    $inventoryUpdateData['hour_meter'] = (int)$hourMeter;
}

// Update work_order with historical hour meter
$woUpdateData = [
    'hm' => $this->request->getPost('hm') ?: null,
    // ... other fields
];
```

### 3. Display Hour Meter
**File**: `app/Views/service/print_work_order.php`

Hour meter sudah ditampilkan di print work order:
```php
<td><?= esc($workOrder['hm'] ?? '-') ?></td>
```

## Business Logic

### When to Update Hour Meter?

**During Unit Verification** (Work Order Completion):
- ✅ Mechanic inputs current hour meter reading
- ✅ System saves to `work_orders.hm` (historical record)
- ✅ System updates `inventory_unit.hour_meter` (current state)

**Purpose of Dual Storage**:

| Table | Column | Purpose | Usage |
|-------|--------|---------|-------|
| `work_orders` | `hm` | Historical snapshot | Service history tracking, calculate usage between services |
| `inventory_unit` | `hour_meter` | Current reading | Quick access to latest HM, maintenance scheduling |

### Example Scenario

**Unit #123 Service History**:
```
Work Order #1 (Jan 15): HM = 1000 hours
Work Order #2 (Feb 20): HM = 1150 hours (used 150 hours)
Work Order #3 (Mar 25): HM = 1300 hours (used 150 hours)

Current State:
inventory_unit.hour_meter = 1300
```

**Benefits**:
- Track total unit usage over time
- Calculate hours between services
- Predict next maintenance based on HM intervals
- Monitor unit condition based on usage

## Validation

### Frontend Validation
- Input type: `number`
- Min value: `0`
- Step: `1` (integers only)
- Optional field (can be empty)

### Backend Validation
```php
if (!empty($hourMeter) && is_numeric($hourMeter)) {
    $inventoryUpdateData['hour_meter'] = (int)$hourMeter;
}
```

## Testing Checklist

- [ ] Run database migration successfully
- [ ] Create new work order → verify no errors
- [ ] Complete unit verification → input hour meter value
- [ ] Check `work_orders.hm` saved correctly
- [ ] Check `inventory_unit.hour_meter` updated
- [ ] Print work order → hour meter displays
- [ ] Try without hour meter input → should save as NULL
- [ ] Try with invalid input (negative, decimal) → should handle gracefully

## Future Enhancements

### 1. Hour Meter Validation
- Validate new HM > previous HM (can't go backwards)
- Alert if HM increase is suspiciously high/low
- Required field for certain unit types

### 2. Hour Meter Analytics
- Display hour meter history graph
- Calculate average usage per month
- Maintenance reminder based on HM intervals

### 3. Automatic Maintenance Scheduling
```sql
-- Units needing maintenance (every 500 hours)
SELECT 
    u.no_unit,
    u.hour_meter,
    u.hour_meter - COALESCE(last_service.hm, 0) as hours_since_service
FROM inventory_unit u
LEFT JOIN (
    SELECT unit_id, MAX(hm) as hm
    FROM work_orders
    WHERE status_id = (SELECT id FROM work_order_statuses WHERE status_code = 'COMPLETED')
    GROUP BY unit_id
) last_service ON u.id_inventory_unit = last_service.unit_id
WHERE u.hour_meter - COALESCE(last_service.hm, 0) >= 500;
```

## Related Files

**Backend**:
- `app/Controllers/WorkOrderController.php` (lines 2850-2920)
- `app/Models/WorkOrderModel.php` (line 535-560)

**Frontend**:
- `app/Views/service/unit_verification.php` (HM input form)
- `app/Views/service/print_work_order.php` (HM display)

**Database**:
- `databases/migrations/add_hour_meter_to_inventory_unit.sql`
- `databases/optima_db_24-11-25_FINAL.sql` (lines 5528, 5401)

## Notes

- Hour meter is optional during unit verification
- If not provided, saves as NULL (no forced requirement)
- Print work order shows "-" if hour meter is NULL
- Index added for performance on hour_meter queries

---

**Implementation Status**: ✅ COMPLETE  
**Database Migration Required**: ⚠️ YES - Run migration script  
**Breaking Changes**: ❌ NO - Backward compatible  
**Date**: 2025-01-XX
