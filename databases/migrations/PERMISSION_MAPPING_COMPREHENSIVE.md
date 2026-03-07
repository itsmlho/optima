# Comprehensive Permission Mapping - Optima System

## Permission Key Format
```
module.page.action

Example:
marketing.customer.navigation  → Menu access
marketing.customer.view        → View/List data
marketing.customer.create      → Add new record
marketing.customer.edit        → Update record
marketing.customer.delete      → Delete record
marketing.quotation.approve    → Approve quotation
marketing.quotation.export     → Export data
```

## Standard Actions
- `navigation` - Menu/sidebar access
- `view` - View list and detail
- `create` - Add new record
- `edit` - Update record
- `delete` - Delete record
- `approve` - Approve workflow
- `reject` - Reject workflow
- `export` - Export to Excel/PDF
- `print` - Print document
- `import` - Import data
- `verify` - Verify/check data
- `submit` - Submit for approval
- `cancel` - Cancel transaction

---

## 1. DASHBOARD MODULE

### dashboard.home
- `dashboard.home.navigation` - Access dashboard
- `dashboard.home.view` - View dashboard widgets

### dashboard.analytics
- `dashboard.analytics.navigation` - Access analytics
- `dashboard.analytics.view` - View analytics data
- `dashboard.analytics.export` - Export analytics

---

## 2. MARKETING MODULE

### marketing.customer
- `marketing.customer.navigation` - Menu Customer
- `marketing.customer.view` - List & detail customer
- `marketing.customer.create` - Add new customer
- `marketing.customer.edit` - Edit customer data
- `marketing.customer.delete` - Delete customer
- `marketing.customer.export` - Export customer list
- `marketing.customer.import` - Import customer data

### marketing.quotation
- `marketing.quotation.navigation` - Menu Quotation
- `marketing.quotation.view` - View quotation list
- `marketing.quotation.create` - Create new quotation
- `marketing.quotation.edit` - Edit quotation
- `marketing.quotation.delete` - Delete quotation
- `marketing.quotation.approve` - Approve quotation
- `marketing.quotation.reject` - Reject quotation
- `marketing.quotation.convert_po` - Convert to PO/Contract
- `marketing.quotation.export` - Export quotation
- `marketing.quotation.print` - Print quotation

### marketing.contract
- `marketing.contract.navigation` - Menu Contract
- `marketing.contract.view` - View contract list
- `marketing.contract.create` - Create new contract
- `marketing.contract.edit` - Edit contract
- `marketing.contract.delete` - Delete contract
- `marketing.contract.approve` - Approve contract
- `marketing.contract.renew` - Renew contract
- `marketing.contract.terminate` - Terminate contract
- `marketing.contract.export` - Export contract
- `marketing.contract.print` - Print contract

### marketing.audit_approval
- `marketing.audit_approval.navigation` - Menu Audit Approval
- `marketing.audit_approval.view` - View audit list
- `marketing.audit_approval.approve` - Approve audit
- `marketing.audit_approval.reject` - Reject audit
- `marketing.audit_approval.export` - Export audit data

### marketing.performance
- `marketing.performance.navigation` - Menu Performance
- `marketing.performance.view` - View performance dashboard
- `marketing.performance.export` - Export performance report

---

## 3. SERVICE MODULE

### service.work_order
- `service.work_order.navigation` - Menu Work Order
- `service.work_order.view` - View work order list
- `service.work_order.create` - Create work order
- `service.work_order.edit` - Edit work order
- `service.work_order.delete` - Delete work order
- `service.work_order.assign` - Assign technician
- `service.work_order.complete` - Mark as completed
- `service.work_order.export` - Export work order
- `service.work_order.print` - Print work order

### service.unit_audit
- `service.unit_audit.navigation` - Menu Unit Audit
- `service.unit_audit.view` - View audit list
- `service.unit_audit.create` - Create audit
- `service.unit_audit.edit` - Edit audit
- `service.unit_audit.delete` - Delete audit
- `service.unit_audit.submit` - Submit for approval
- `service.unit_audit.export` - Export audit data

### service.unit_audit_location
- `service.unit_audit_location.navigation` - Menu Audit by Location
- `service.unit_audit_location.view` - View location audit
- `service.unit_audit_location.create` - Create location audit
- `service.unit_audit_location.export` - Export location audit

### service.maintenance
- `service.maintenance.navigation` - Menu Maintenance
- `service.maintenance.view` - View maintenance schedule
- `service.maintenance.create` - Create maintenance
- `service.maintenance.edit` - Edit maintenance
- `service.maintenance.delete` - Delete maintenance
- `service.maintenance.complete` - Mark as completed
- `service.maintenance.export` - Export maintenance

### service.area_management
- `service.area_management.navigation` - Menu Area Management
- `service.area_management.view` - View service areas
- `service.area_management.create` - Create service area
- `service.area_management.edit` - Edit service area
- `service.area_management.delete` - Delete service area

---

## 4. WAREHOUSE MODULE

### warehouse.inventory_unit
- `warehouse.inventory_unit.navigation` - Menu Inventory Unit
- `warehouse.inventory_unit.view` - View inventory
- `warehouse.inventory_unit.create` - Add unit to inventory
- `warehouse.inventory_unit.edit` - Edit inventory data
- `warehouse.inventory_unit.delete` - Delete from inventory
- `warehouse.inventory_unit.export` - Export inventory
- `warehouse.inventory_unit.print` - Print inventory report
- `warehouse.inventory_unit.adjust` - Adjust stock

### warehouse.movements
- `warehouse.movements.navigation` - Menu Unit Movements (Surat Jalan)
- `warehouse.movements.view` - View movement history
- `warehouse.movements.create` - Create movement/surat jalan
- `warehouse.movements.edit` - Edit movement
- `warehouse.movements.confirm_departure` - Confirm unit departure
- `warehouse.movements.confirm_arrival` - Confirm unit arrival
- `warehouse.movements.cancel` - Cancel movement
- `warehouse.movements.print` - Print surat jalan
- `warehouse.movements.export` - Export movement data

### warehouse.stock_opname
- `warehouse.stock_opname.navigation` - Menu Stock Opname
- `warehouse.stock_opname.view` - View stock opname
- `warehouse.stock_opname.create` - Create stock opname
- `warehouse.stock_opname.edit` - Edit stock opname
- `warehouse.stock_opname.submit` - Submit stock opname
- `warehouse.stock_opname.approve` - Approve stock opname
- `warehouse.stock_opname.export` - Export stock opname

### warehouse.receiving
- `warehouse.receiving.navigation` - Menu Receiving
- `warehouse.receiving.view` - View receiving list
- `warehouse.receiving.create` - Create receiving
- `warehouse.receiving.edit` - Edit receiving
- `warehouse.receiving.verify` - Verify received items
- `warehouse.receiving.print` - Print receiving note

---

## 5. PURCHASING MODULE

### purchasing.unit
- `purchasing.unit.navigation` - Menu Purchase Unit
- `purchasing.unit.view` - View purchase list
- `purchasing.unit.create` - Create purchase
- `purchasing.unit.edit` - Edit purchase
- `purchasing.unit.delete` - Delete purchase
- `purchasing.unit.approve` - Approve purchase
- `purchasing.unit.export` - Export purchase data

### purchasing.vendor
- `purchasing.vendor.navigation` - Menu Vendor Management
- `purchasing.vendor.view` - View vendor list
- `purchasing.vendor.create` - Add vendor
- `purchasing.vendor.edit` - Edit vendor
- `purchasing.vendor.delete` - Delete vendor
- `purchasing.vendor.export` - Export vendor list

### purchasing.po
- `purchasing.po.navigation` - Menu Purchase Order
- `purchasing.po.view` - View PO list
- `purchasing.po.create` - Create PO
- `purchasing.po.edit` - Edit PO
- `purchasing.po.delete` - Delete PO
- `purchasing.po.approve` - Approve PO
- `purchasing.po.print` - Print PO

---

## 6. FINANCE MODULE

### finance.invoice
- `finance.invoice.navigation` - Menu Invoice
- `finance.invoice.view` - View invoice list
- `finance.invoice.create` - Create invoice
- `finance.invoice.edit` - Edit invoice
- `finance.invoice.delete` - Delete invoice
- `finance.invoice.approve` - Approve invoice
- `finance.invoice.print` - Print invoice
- `finance.invoice.export` - Export invoice

### finance.payment
- `finance.payment.navigation` - Menu Payment
- `finance.payment.view` - View payment list
- `finance.payment.create` - Record payment
- `finance.payment.edit` - Edit payment
- `finance.payment.delete` - Delete payment
- `finance.payment.verify` - Verify payment
- `finance.payment.export` - Export payment

### finance.billing
- `finance.billing.navigation` - Menu Billing
- `finance.billing.view` - View billing
- `finance.billing.create` - Create billing
- `finance.billing.edit` - Edit billing
- `finance.billing.send` - Send billing to customer
- `finance.billing.export` - Export billing

---

## 7. OPERATIONAL MODULE

### operational.unit_rolling
- `operational.unit_rolling.navigation` - Menu Unit Rolling
- `operational.unit_rolling.view` - View rolling schedule
- `operational.unit_rolling.create` - Create rolling
- `operational.unit_rolling.edit` - Edit rolling
- `operational.unit_rolling.approve` - Approve rolling
- `operational.unit_rolling.execute` - Execute rolling
- `operational.unit_rolling.export` - Export rolling data

### operational.unit_asset
- `operational.unit_asset.navigation` - Menu Unit Asset
- `operational.unit_asset.view` - View asset list
- `operational.unit_asset.create` - Register asset
- `operational.unit_asset.edit` - Edit asset
- `operational.unit_asset.delete` - Delete asset
- `operational.unit_asset.transfer` - Transfer asset
- `operational.unit_asset.export` - Export asset

### operational.perizinan
- `operational.perizinan.navigation` - Menu Perizinan
- `operational.perizinan.view` - View perizinan
- `operational.perizinan.create` - Create perizinan
- `operational.perizinan.edit` - Edit perizinan
- `operational.perizinan.renew` - Renew perizinan
- `operational.perizinan.export` - Export perizinan

---

## 8. REPORTS MODULE

### reports.contract
- `reports.contract.navigation` - Menu Contract Reports
- `reports.contract.view` - View contract reports
- `reports.contract.export` - Export contract reports

### reports.revenue
- `reports.revenue.navigation` - Menu Revenue Reports
- `reports.revenue.view` - View revenue reports
- `reports.revenue.export` - Export revenue reports

### reports.unit
- `reports.unit.navigation` - Menu Unit Reports
- `reports.unit.view` - View unit reports
- `reports.unit.export` - Export unit reports

### reports.performance
- `reports.performance.navigation` - Menu Performance Reports
- `reports.performance.view` - View performance reports
- `reports.performance.export` - Export performance reports

---

## 9. SETTINGS MODULE

### settings.system
- `settings.system.navigation` - Menu System Settings
- `settings.system.view` - View system settings
- `settings.system.edit` - Edit system settings

### settings.user
- `settings.user.navigation` - Menu User Management
- `settings.user.view` - View user list
- `settings.user.create` - Create user
- `settings.user.edit` - Edit user
- `settings.user.delete` - Delete user
- `settings.user.reset_password` - Reset user password
- `settings.user.assign_role` - Assign role to user
- `settings.user.assign_permission` - Assign custom permission

### settings.role
- `settings.role.navigation` - Menu Role Management
- `settings.role.view` - View role list
- `settings.role.create` - Create role
- `settings.role.edit` - Edit role
- `settings.role.delete` - Delete role
- `settings.role.assign_permission` - Assign permissions to role

### settings.permission
- `settings.permission.navigation` - Menu Permission Management
- `settings.permission.view` - View permission list
- `settings.permission.create` - Create custom permission
- `settings.permission.edit` - Edit permission
- `settings.permission.delete` - Delete permission

### settings.division
- `settings.division.navigation` - Menu Division Management
- `settings.division.view` - View division list
- `settings.division.create` - Create division
- `settings.division.edit` - Edit division
- `settings.division.delete` - Delete division

### settings.notification
- `settings.notification.navigation` - Menu Notification Settings
- `settings.notification.view` - View notification settings
- `settings.notification.edit` - Edit notification rules

---

## 10. ACTIVITY LOG MODULE

### activity.log
- `activity.log.navigation` - Menu Activity Log
- `activity.log.view` - View activity log
- `activity.log.export` - Export activity log
- `activity.log.delete` - Delete old logs

---

## SUMMARY

**Total Modules**: 10
**Total Pages**: ~50
**Total Permissions**: ~250+

**Standard Permission Pattern per Page**:
- navigation (1)
- view (1)
- create (1)
- edit (1)
- delete (1)
- Special actions (varies by page: approve, print, export, etc.)

**Average**: 5-7 permissions per page = ~300 permissions total
