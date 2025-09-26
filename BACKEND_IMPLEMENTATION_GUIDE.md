# Work Order Backend Implementation Guide

## 1. Work Order Numbering System

### Current Implementation: **Backend Database Auto-increment**
- ✅ **Recommended approach**: Auto-generated in backend for data consistency
- Work Order Number format: Simple sequential numbers (1, 2, 3, ...)
- Method: `generateWorkOrderNumber()` generates next number based on MAX(work_order_number) + 1
- Used in: `store()` method automatically assigns number when creating new work order

```php
// Current implementation
private function generateWorkOrderNumber()
{
    $query = $db->query("SELECT MAX(CAST(work_order_number AS UNSIGNED)) as max_number FROM work_orders");
    $result = $query->getRowArray();
    $nextNumber = ($result && !empty($result['max_number'])) ? intval($result['max_number']) + 1 : 1;
    return (string)$nextNumber;
}
```

### Alternative Options:
❌ **Database AUTO_INCREMENT**: Kurang fleksibel untuk custom numbering format
❌ **Frontend JavaScript**: Tidak reliable karena race conditions

## 2. Time to Repair (TTR) Calculation

### Current Implementation: **Backend Calculation**
- ✅ **Recommended approach**: Backend handles all time calculations
- Automatic calculation when status changes
- Method: `calculateTTR()` computes repair time based on status history

```php
// Current implementation
private function calculateTTR($workOrderId)
{
    // Get start time (when status changed from PENDING to IN_PROGRESS)
    $startQuery = "SELECT created_at FROM work_order_status_history 
                   WHERE work_order_id = ? AND status_code = 'IN_PROGRESS' 
                   ORDER BY created_at ASC LIMIT 1";
    
    // Get end time (when status changed to COMPLETED)
    $endQuery = "SELECT created_at FROM work_order_status_history 
                 WHERE work_order_id = ? AND status_code = 'COMPLETED' 
                 ORDER BY created_at DESC LIMIT 1";
    
    // Calculate difference in hours
    return $this->calculateHoursDifference($startTime, $endTime);
}
```

### Alternative Options:
❌ **Database Triggers**: Complex to maintain and debug
❌ **Frontend Calculation**: Tidak akurat karena timezone dan client-side issues

## 3. Backend Endpoints Status

### ✅ Completed Endpoints:
1. **POST /work-orders/generate-number** - Generate next WO number
2. **POST /work-orders/search-units** - Search units with autocomplete
3. **POST /work-orders/search-staff** - Search staff by role
4. **POST /work-orders/get-priority** - Get priority name by ID
5. **POST /work-orders/get-subcategory-priority** - Get default priority for subcategory
6. **POST /work-orders** - Create new work order (store method)

### 🔧 Database Table Mappings:
- `work_order_priorities` ✅ (was: priorities)
- `work_order_subcategories` ✅ (was: subcategories)  
- `work_order_statuses` ✅ (was: work_order_status)
- `inventory_unit` ✅ (for units data)
- `work_order_staff` ✅ (for staff data)

## 4. Frontend Integration

### Current Modal Features:
- ✅ Auto-generated WO numbers (no manual input)
- ✅ Unit search with real-time results
- ✅ Staff search by role (Admin, Foreman, Mechanic, Helper)
- ✅ Category/Subcategory with auto-priority
- ✅ Default status (PENDING) set automatically

### JavaScript Integration Points:
```javascript
// Get next WO number
fetch('/optima1/public/work-orders/generate-number')

// Search units
fetch('/optima1/public/work-orders/search-units', {
    body: new URLSearchParams({search: 'TR'})
})

// Search staff by role
fetch('/optima1/public/work-orders/search-staff', {
    body: new URLSearchParams({search: 'john', role: 'mechanic'})
})
```

## 5. Recommendations

### ✅ **Use Backend for Both**:
1. **Work Order Numbering**: Backend ensures consistency and prevents duplicates
2. **TTR Calculation**: Backend handles all time math accurately

### 🚀 **Next Steps**:
1. Test all endpoints via `/optima1/public/test_backend.html`
2. Integrate frontend JavaScript with backend endpoints
3. Add status change workflows for automatic TTR calculation
4. Implement validation and error handling

### 📊 **Performance Considerations**:
- Database queries optimized with proper indexing
- Search queries limited to 10 results
- Error logging for troubleshooting
- Transaction safety for data consistency

## 6. Testing

Access test page at: `http://localhost/optima1/public/test_backend.html`

Test all endpoints to ensure:
- ✅ Generate Number works
- ✅ Unit Search returns results  
- ✅ Staff Search filters by role
- ✅ Priority lookup functions
- ✅ Work Order creation completes