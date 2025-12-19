# Quick Reference - Notification Implementation Priority

**Last Updated:** 19 Desember 2024

---

## 📊 Current Status

- **Total CRUD Functions:** 126
- **With Notifications:** 12 (9.5%)
- **Missing Notifications:** 114 (90.5%)

---

## 🔴 CRITICAL - Implement Immediately

### Finance Module (2 functions) - **HIGHEST PRIORITY**
```php
// app/Controllers/Finance.php

1. createInvoice()
   Event: invoice_created
   Target: Finance, Accounting, Marketing
   
2. updatePaymentStatus($id)
   Event: payment_status_updated
   Target: Finance Director, Accounting, Marketing
```

### Purchasing Module (4 functions)
```php
// app/Controllers/Purchasing.php

3. storeUnifiedPO()
   Event: po_created
   Target: Purchasing team, Finance, Accounting
   
4. createUnifiedPO()
   Event: po_created
   Target: Purchasing team, Finance, Accounting
   
5. createDelivery()
   Event: delivery_created
   Target: Warehouse, Purchasing, Quality Control
   
6. updateDeliveryStatus()
   Event: delivery_status_changed
   Target: Warehouse Manager, Purchasing
```

### Work Order Module (3 functions)
```php
// app/Controllers/WorkOrderController.php

7. store()
   Event: workorder_created
   Target: Service team, Workshop Manager
   
8. updateStatus()
   Event: workorder_status_changed
   Target: Service Manager, Workshop Manager
```

```php
// app/Controllers/WarehousePO.php

9. updateVerification()
   Event: po_verification_updated
   Target: Purchasing, Finance, Quality Control
```

**CRITICAL Total:** 9 functions | **Deadline:** 2 weeks

---

## 🟠 HIGH Priority - Implement Next

### Marketing/Quotation (5 functions)
```php
// app/Controllers/Marketing.php

1. storeQuotation()
   Event: quotation_created
   Target: Marketing Manager, Marketing Supervisor
   
2. updateQuotationStage($quotationId)
   Event: quotation_stage_changed
   Target: Marketing team + next stage handler
   
3. createPurchaseOrder($quotationId)
   Event: po_created_from_quotation
   Target: Purchasing team
   
4. updateContractComplete()
   Event: contract_completed
   Target: Accounting, Operational, Marketing Manager

// app/Controllers/Quotation.php

5. store()
   Event: quotation_created
   Target: Marketing Manager, Marketing Supervisor
```

### Work Order Extended (4 functions)
```php
// app/Controllers/WorkOrderController.php

6. updateWithTTR($workOrderId)
   Event: workorder_ttr_updated
   Target: Service Manager, Quality Control
   
7. saveUnitVerification()
   Event: unit_verification_saved
   Target: QC, Service Manager
   
8. saveSparepartValidation()
   Event: sparepart_validation_saved
   Target: Warehouse, Purchasing
   
9. saveSparepartUsage()
   Event: sparepart_used
   Target: Warehouse, Finance
```

### Service Assignments (4 functions)
```php
// app/Controllers/ServiceAreaManagementController.php

10. saveAssignment()
11. storeAssignment()
    Event: service_assignment_created
    Target: Service team, Assigned employee
    
12. updateAssignment($id)
    Event: service_assignment_updated
    Target: Service team, Affected employees
    
13. deleteAssignment($id)
    Event: service_assignment_deleted
    Target: Service Manager
```

### Unit Management (2 functions)
```php
// app/Controllers/UnitRolling.php

14. updateLocation()
    Event: unit_location_updated
    Target: Operational, Logistics, Service

// app/Controllers/Warehouse.php

15. updateUnit($id)
    Event: warehouse_unit_updated
    Target: Warehouse Manager, Service
```

### Kontrak Management (3 functions)
```php
// app/Controllers/Kontrak.php

16. store()
    Event: contract_created
    Target: Marketing, Accounting, Operational
    
17. update($id)
    Event: contract_updated
    Target: Marketing Manager, Accounting
    
18. delete($id)
    Event: contract_deleted
    Target: Marketing Manager, Accounting Manager
```

### User/Permission Security (4 functions)
```php
// app/Controllers/Admin/AdvancedUserManagement.php

19. removeFromDivision()
    Event: user_removed_from_division
    Target: Division Head, HR
    
20. saveCustomPermissions($userId)
    Event: user_permissions_updated
    Target: Admin, Security

// app/Controllers/Admin/PermissionController.php

21. store()
    Event: permission_created
    Target: Admin, Security
    
22. update($permissionId)
    Event: permission_updated
    Target: Admin, Security

// app/Controllers/Admin/RoleController.php

23. saveRole()
    Event: role_saved
    Target: Admin, Security, Division Heads
```

**HIGH Total:** 22 functions | **Deadline:** 4 weeks

---

## 🟡 MEDIUM Priority

### Purchasing Extended (8 functions)
- PO variants (Unit, Sparepart, Dinamis)
- PO attachments
- Supplier management

### Service Employee (2 functions)
- Employee create/update

### Warehouse Inventory (2 functions)
- Inventory updates
- Item additions

### User Management (3 functions)
- User CRUD operations

### Marketing Workflow (2 functions)
- Workflow status updates

**MEDIUM Total:** 17 functions | **Timeline:** Week 5-6

---

## 🟢 LOW Priority

### Master Data (5 functions)
- Warehouse master data maintenance

### Quotation Specs (3 functions)
- Specification CRUD

### Profile Updates (4 functions)
- Personal profile changes

### Admin Settings (2 functions)
- System configuration

### Miscellaneous (10+ functions)
- Various admin operations

**LOW Total:** 24+ functions | **Timeline:** Week 7-8

---

## 📋 Implementation Checklist

### Per Function Implementation:
- [ ] Buat helper function di `notification_helper.php`
- [ ] Add notification call di controller function
- [ ] Buat notification rule di database
- [ ] Test dengan dummy data
- [ ] Verify user menerima notifikasi
- [ ] Check log untuk errors
- [ ] Update documentation

### Example Implementation:
```php
// 1. Create helper function
function notify_invoice_created($data) {
    $eventData = [
        'invoice_number' => $data['invoice_number'],
        'customer_name' => $data['customer_name'],
        'amount' => $data['amount']
    ];
    return send_notification('invoice_created', $eventData);
}

// 2. Add to controller
public function createInvoice() {
    // ... existing code ...
    
    if ($invoiceId) {
        // Send notification
        notify_invoice_created([
            'invoice_number' => $invoiceNumber,
            'customer_name' => $customerName,
            'amount' => $totalAmount
        ]);
    }
    
    // ... rest of code ...
}

// 3. Add database rule
INSERT INTO notification_rules 
(trigger_event, target_divisions, target_roles, title_template, message_template)
VALUES 
('invoice_created', 
 'Finance,Accounting,Marketing',
 'Manager,Supervisor,Staff',
 'Invoice Baru Dibuat',
 'Invoice {{invoice_number}} untuk {{customer_name}} senilai {{amount}} telah dibuat');
```

---

## 🎯 Quick Wins (Jika Resource Terbatas)

Fokus ke 5 ini dulu untuk **40% impact**:

1. ✅ **Finance::createInvoice()** - Revenue critical
2. ✅ **Purchasing::storeUnifiedPO()** - Highest volume
3. ✅ **WorkOrderController::updateStatus()** - Customer-facing
4. ✅ **Marketing::updateQuotationStage()** - Sales visibility
5. ✅ **WarehousePO::updateVerification()** - Payment approval

**Time:** 1-2 hari | **Impact:** Massive

---

## 📈 Progress Tracking

| Phase | Target | Functions | Status | Deadline |
|-------|--------|-----------|--------|----------|
| Phase 0 | 9.5% | 12/126 | ✅ Done | Current |
| Phase 1 | 25% | 32/126 | ⏳ Pending | Week 2 |
| Phase 2 | 50% | 63/126 | ⏳ Pending | Week 4 |
| Phase 3 | 75% | 95/126 | ⏳ Pending | Week 6 |
| Phase 4 | 95% | 120/126 | ⏳ Pending | Week 8 |

---

## 🔧 Helper Templates

### Generic CRUD Notification:
```php
function notify_generic_crud($module, $action, $data) {
    $event = strtolower($module) . '_' . strtolower($action);
    return send_notification($event, $data);
}
```

### Get Current User Info:
```php
$currentUser = [
    'user_id' => session()->get('user_id'),
    'user_name' => session()->get('user_name'),
    'division' => session()->get('division'),
    'role' => session()->get('role')
];
```

### Template Variables Common:
- `{{user_name}}` - Who did the action
- `{{item_id}}` - ID of created/updated item
- `{{item_name}}` - Name/description
- `{{timestamp}}` - When action occurred
- `{{amount}}` - For financial transactions
- `{{status}}` - Current status

---

## 📞 Support

Jika ada pertanyaan saat implementasi:
1. Check [NOTIFICATION_COMPLETE_SYSTEM_AUDIT.md](./NOTIFICATION_COMPLETE_SYSTEM_AUDIT.md) untuk detail
2. Check `app/Helpers/notification_helper.php` untuk existing patterns
3. Check `notification_rules` table untuk existing events

---

**Next Step:** Start dengan CRITICAL priority functions!
