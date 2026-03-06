# Migration Guide: Removing customer_location_id from kontrak table

## Overview
The `kontrak` table no longer has `customer_location_id` column. It now directly has `customer_id`, and location tracking is done at the `kontrak_unit` level.

## Schema Change
```sql
-- OLD:
kontrak
├── customer_location_id (FK to customer_locations)
└── location ambiguous for multi-location contracts

-- NEW:
kontrak
├── customer_id (FK to customers) - direct link
└── kontrak_unit
    ├── kontrak_id
    ├── unit_id
    └── customer_location_id (specific per unit)
```

## Fix Strategy by Use Case

### 1. **Queries that need customer info only**
```php
// OLD:
->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
->join('customers c', 'c.id = cl.customer_id', 'left')

// NEW:
->join('customers c', 'c.id = k.customer_id', 'left')
```

### 2. **Queries that need location data (multi-location support)**
```php
// OLD:
->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
->select('cl.location_name')

// NEW - Option A: Show all locations (comma-separated):
->select('(SELECT GROUP_CONCAT(DISTINCT cl2.location_name SEPARATOR ", ") 
          FROM kontrak_unit ku2 
          JOIN customer_locations cl2 ON ku2.customer_location_id = cl2.id 
          WHERE ku2.kontrak_id = k.id) as location_names')

// NEW - Option B: Show location count:
->select('(SELECT COUNT(DISTINCT ku.customer_location_id) 
          FROM kontrak_unit ku 
          WHERE ku.kontrak_id = k.id) as location_count')
```

### 3. **Queries that filter by customer**
```php
// OLD:
->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
->where('cl.customer_id', $customerId)

// NEW:
->where('k.customer_id', $customerId)
```

### 4. **Invoice/Billing queries that need detailed location breakdown**
```php
// Query kontrak_unit directly for per-location breakdown
$units = $this->db->table('kontrak_unit ku')
    ->select('ku.*, cl.location_name, cl.address')
    ->join('customer_locations cl', 'ku.customer_location_id = cl.id', 'left')
    ->where('ku.kontrak_id', $kontrakId)
    ->get()->getResultArray();

// Group by location for billing
$byLocation = [];
foreach ($units as $unit) {
    $loc = $unit['customer_location_id'];
    if (!isset($byLocation[$loc])) {
        $byLocation[$loc] = [
            'location_name' => $unit['location_name'],
            'units' => [],
            'total' => 0
        ];
    }
    $byLocation[$loc]['units'][] = $unit;
    $byLocation[$loc]['total'] += $unit['harga_sewa'];
}
```

## Files Affected (95 instances)

### Controllers (67 instances):
- ✅ CustomerManagementController.php (8 instances) - FIXED
- ⏳ Kontrak.php (12 instances)
- Marketing.php (15 instances)
- WorkOrderController.php (7 instances)
- Warehouse.php (3 instances)
- Warehouse/UnitInventoryController.php (4 instances)
- Warehouse/SparepartUsageController.php (5 instances)
- Customers.php (2 instances)
- Dashboard.php (2 instances)
- Operational.php (3 instances)
- MarketingOptimized.php (2 instances)
- Finance.php (1 instance)
- ContractNotifications.php (1 instance)
- BatchContractOperations.php (1 instance)
- ActivityLogViewer.php (1 instance)
- UnitAssetController.php (1 instance)

### Models (21 instances):
- KontrakModel.php (4 instances)
- InventoryUnitModel.php (6 instances)
- InvoiceModel.php (2instances)
- WorkOrderModel.php (3 instances)
- OptimizedWorkOrderModel.php (1 instance)
- OptimizedUnitAssetModel.php (3 instances)
- QuotationModel.php (1 instance)
- RecurringBillingScheduleModel.php (1 instance)
- CustomerModel.php (1 instance)

### Services & Helpers (7 instances):
- DeliveryInstructionService.php (2 instances)
- SearchIndexService.php (1 instance)
- notification_helper.php (1 instance)

## Testing Checklist

After fixes:
- [ ] Customer management page loads without errors
- [ ] Contract listing shows location data (all locations)
- [ ] Contract detail page displays correctly
- [ ] Contract edit form works
- [ ] Invoice generation shows location breakdown
- [ ] Work orders display location info
- [ ] Unit inventory shows contract locations
- [ ] Dashboard statistics work
- [ ] CSV exports include location data
- [ ] Multi-location contracts display all locations

## Migration SQL
```sql
-- Already executed:
ALTER TABLE kontrak DROP FOREIGN KEY fk_kontrak_customer_location;
ALTER TABLE kontrak DROP COLUMN customer_location_id;
```

## Rollback (if needed)
```sql
ALTER TABLE kontrak ADD COLUMN customer_location_id INT AFTER customer_id;
ALTER TABLE kontrak ADD CONSTRAINT fk_kontrak_customer_location 
    FOREIGN KEY (customer_location_id) REFERENCES customer_locations(id);

-- Note: Data cannot be restored automatically - would need to choose
-- one location from kontrak_unit per contract
```
