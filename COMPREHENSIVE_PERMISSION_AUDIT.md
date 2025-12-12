# COMPREHENSIVE PERMISSION AUDIT - OPTIMA SYSTEM
*Generated from sidebar_new.php analysis*

## OVERVIEW
Analisis komprehensif seluruh halaman dan fungsi pada sistem OPTIMA berdasarkan sidebar_new.php untuk implementasi sistem permission yang granular dengan struktur module.page.action.subaction.component.

---

## MODULE STRUCTURE ANALYSIS

### 1. MARKETING MODULE
**Base Permissions**: `marketing.*.*`

#### 1.1 Customer Management (`marketing.customer.*`)
- **Page**: `/marketing/customer-management`
- **Core Permissions**:
  - `marketing.customer.index` - View customer list
  - `marketing.customer.create` - Add new customer
  - `marketing.customer.edit` - Edit customer data
  - `marketing.customer.delete` - Delete customer
  - `marketing.customer.export` - Export customer data
  - `marketing.customer.search` - Search customers
  - `marketing.customer.view_detail` - View detailed customer info

#### 1.2 Customer Database (`marketing.customer_db.*`)
- **Page**: `/marketing/customer-database`
- **Core Permissions**:
  - `marketing.customer_db.index` - View database
  - `marketing.customer_db.search` - Search database
  - `marketing.customer_db.filter` - Filter data
  - `marketing.customer_db.export` - Export database

#### 1.3 Quotation System (`marketing.quotation.*`)
- **Page**: `/marketing/quotation-system`
- **Core Permissions**:
  - `marketing.quotation.index` - View quotation list
  - `marketing.quotation.create` - Create new quotation
  - `marketing.quotation.edit` - Edit quotation
  - `marketing.quotation.delete` - Delete quotation
  - `marketing.quotation.approve` - Approve quotation
  - `marketing.quotation.reject` - Reject quotation
  - `marketing.quotation.print` - Print quotation
  - `marketing.quotation.export` - Export quotation data
  - `marketing.quotation.view_detail` - View quotation details

#### 1.4 SPK Management (`marketing.spk.*`)
- **Page**: `/marketing/spk-system`
- **Core Permissions**:
  - `marketing.spk.index` - View SPK list
  - `marketing.spk.create` - Create SPK
  - `marketing.spk.edit` - Edit SPK
  - `marketing.spk.delete` - Delete SPK
  - `marketing.spk.approve` - Approve SPK
  - `marketing.spk.close` - Close SPK
  - `marketing.spk.print` - Print SPK
  - `marketing.spk.export` - Export SPK data

#### 1.5 Delivery Instructions (`marketing.delivery.*`)
- **Page**: `/marketing/delivery-instructions`
- **Core Permissions**:
  - `marketing.delivery.index` - View delivery instructions
  - `marketing.delivery.create` - Create delivery instruction
  - `marketing.delivery.edit` - Edit delivery instruction
  - `marketing.delivery.delete` - Delete delivery instruction
  - `marketing.delivery.approve` - Approve delivery
  - `marketing.delivery.print` - Print instruction

### 2. SERVICE MODULE
**Base Permissions**: `service.*.*`

#### 2.1 Work Orders (`service.workorder.*`)
- **Page**: `/service/work-orders`
- **Core Permissions**:
  - `service.workorder.index` - View work orders
  - `service.workorder.create` - Create work order
  - `service.workorder.edit` - Edit work order
  - `service.workorder.delete` - Delete work order
  - `service.workorder.assign` - Assign technician
  - `service.workorder.complete` - Complete work order
  - `service.workorder.print` - Print work order
  - `service.workorder.export` - Export work order data

#### 2.2 PMPS Management (`service.pmps.*`)
- **Page**: `/service/pmps`
- **Core Permissions**:
  - `service.pmps.index` - View PMPS schedule
  - `service.pmps.create` - Create PMPS schedule
  - `service.pmps.edit` - Edit PMPS schedule
  - `service.pmps.delete` - Delete PMPS schedule
  - `service.pmps.execute` - Execute PMPS
  - `service.pmps.complete` - Complete PMPS
  - `service.pmps.report` - Generate PMPS report

#### 2.3 Area Management (`service.area.*`)
- **Page**: `/service/area-management`
- **Core Permissions**:
  - `service.area.index` - View service areas
  - `service.area.create` - Create service area
  - `service.area.edit` - Edit service area
  - `service.area.delete` - Delete service area
  - `service.area.assign_user` - Assign user to area
  - `service.area.manage_branch` - Manage branch access

#### 2.4 User Management (`service.user.*`)
- **Page**: `/service/user-management`
- **Core Permissions**:
  - `service.user.index` - View service users
  - `service.user.create` - Create service user
  - `service.user.edit` - Edit service user
  - `service.user.delete` - Delete service user
  - `service.user.assign_area` - Assign service area
  - `service.user.assign_branch` - Assign branch access
  - `service.user.manage_permissions` - Manage user permissions

### 3. OPERATIONAL MODULE
**Base Permissions**: `operational.*.*`

#### 3.1 Delivery Process (`operational.delivery.*`)
- **Page**: `/operational/delivery`
- **Core Permissions**:
  - `operational.delivery.index` - View deliveries
  - `operational.delivery.create` - Create delivery
  - `operational.delivery.edit` - Edit delivery
  - `operational.delivery.delete` - Delete delivery
  - `operational.delivery.dispatch` - Dispatch delivery
  - `operational.delivery.complete` - Complete delivery
  - `operational.delivery.track` - Track delivery status

### 4. ACCOUNTING MODULE
**Base Permissions**: `accounting.*.*`

#### 4.1 Invoice Management (`accounting.invoice.*`)
- **Page**: `/accounting/invoice-page`
- **Core Permissions**:
  - `accounting.invoice.index` - View invoices
  - `accounting.invoice.create` - Create invoice
  - `accounting.invoice.edit` - Edit invoice
  - `accounting.invoice.delete` - Delete invoice
  - `accounting.invoice.approve` - Approve invoice
  - `accounting.invoice.print` - Print invoice
  - `accounting.invoice.export` - Export invoice data
  - `accounting.invoice.payment_track` - Track payments

#### 4.2 Payment Validation (`accounting.payment.*`)
- **Page**: `/accounting/payment-validation`
- **Core Permissions**:
  - `accounting.payment.index` - View payments
  - `accounting.payment.validate` - Validate payment
  - `accounting.payment.approve` - Approve payment
  - `accounting.payment.reject` - Reject payment
  - `accounting.payment.export` - Export payment data

### 5. PURCHASING MODULE
**Base Permissions**: `purchasing.*.*`

#### 5.1 PO Management (`purchasing.po.*`)
- **Page**: `/purchasing/po-page`
- **Core Permissions**:
  - `purchasing.po.index` - View purchase orders
  - `purchasing.po.create` - Create PO
  - `purchasing.po.edit` - Edit PO
  - `purchasing.po.delete` - Delete PO
  - `purchasing.po.approve` - Approve PO
  - `purchasing.po.print` - Print PO
  - `purchasing.po.export` - Export PO data

#### 5.2 PO Sparepart (`purchasing.po_sparepart.*`)
- **Page**: `/purchasing/po-sparepart-list`
- **Core Permissions**:
  - `purchasing.po_sparepart.index` - View sparepart POs
  - `purchasing.po_sparepart.create` - Create sparepart PO
  - `purchasing.po_sparepart.edit` - Edit sparepart PO
  - `purchasing.po_sparepart.delete` - Delete sparepart PO
  - `purchasing.po_sparepart.approve` - Approve sparepart PO

#### 5.3 PO Reject (`purchasing.po_reject.*`)
- **Page**: `/warehouse/purchase-orders/rejected-items`
- **Core Permissions**:
  - `purchasing.po_reject.index` - View rejected items
  - `purchasing.po_reject.review` - Review rejection
  - `purchasing.po_reject.approve` - Approve after review
  - `purchasing.po_reject.export` - Export rejection data

#### 5.4 Supplier Management (`purchasing.supplier.*`)
- **Page**: `/purchasing/supplier-management-page`
- **Core Permissions**:
  - `purchasing.supplier.index` - View suppliers
  - `purchasing.supplier.create` - Create supplier
  - `purchasing.supplier.edit` - Edit supplier
  - `purchasing.supplier.delete` - Delete supplier
  - `purchasing.supplier.evaluate` - Evaluate supplier performance
  - `purchasing.supplier.export` - Export supplier data

### 6. WAREHOUSE MODULE
**Base Permissions**: `warehouse.*.*`

#### 6.1 Unit Inventory (`warehouse.unit_inventory.*`)
- **Page**: `/warehouse/inventory/invent_unit`
- **Core Permissions**:
  - `warehouse.unit_inventory.index` - View unit inventory
  - `warehouse.unit_inventory.create` - Add inventory item
  - `warehouse.unit_inventory.edit` - Edit inventory item
  - `warehouse.unit_inventory.delete` - Delete inventory item
  - `warehouse.unit_inventory.transfer` - Transfer unit
  - `warehouse.unit_inventory.export` - Export inventory data

#### 6.2 Attachment & Battery Inventory (`warehouse.attachment_inventory.*`)
- **Page**: `/warehouse/inventory/invent_attachment`
- **Core Permissions**:
  - `warehouse.attachment_inventory.index` - View attachment inventory
  - `warehouse.attachment_inventory.create` - Add attachment item
  - `warehouse.attachment_inventory.edit` - Edit attachment item
  - `warehouse.attachment_inventory.delete` - Delete attachment item
  - `warehouse.attachment_inventory.transfer` - Transfer attachment
  - `warehouse.attachment_inventory.export` - Export inventory data

#### 6.3 Sparepart Inventory (`warehouse.sparepart_inventory.*`)
- **Page**: `/warehouse/inventory/invent_sparepart`
- **Core Permissions**:
  - `warehouse.sparepart_inventory.index` - View sparepart inventory
  - `warehouse.sparepart_inventory.create` - Add sparepart item
  - `warehouse.sparepart_inventory.edit` - Edit sparepart item
  - `warehouse.sparepart_inventory.delete` - Delete sparepart item
  - `warehouse.sparepart_inventory.transfer` - Transfer sparepart
  - `warehouse.sparepart_inventory.export` - Export inventory data

#### 6.4 Sparepart Usage & Returns (`warehouse.sparepart_usage.*`)
- **Page**: `/warehouse/sparepart-usage`
- **Core Permissions**:
  - `warehouse.sparepart_usage.index` - View usage history
  - `warehouse.sparepart_usage.create` - Record usage
  - `warehouse.sparepart_usage.edit` - Edit usage record
  - `warehouse.sparepart_usage.delete` - Delete usage record
  - `warehouse.sparepart_usage.return` - Process return
  - `warehouse.sparepart_usage.export` - Export usage data

#### 6.5 PO Verification (`warehouse.po_verification.*`)
- **Page**: `/warehouse/purchase-orders/wh-verification`
- **Core Permissions**:
  - `warehouse.po_verification.index` - View verification queue
  - `warehouse.po_verification.verify` - Verify PO items
  - `warehouse.po_verification.approve` - Approve verification
  - `warehouse.po_verification.reject` - Reject verification
  - `warehouse.po_verification.export` - Export verification data

### 7. PERIZINAN MODULE
**Base Permissions**: `perizinan.*.*`

#### 7.1 SILO Management (`perizinan.silo.*`)
- **Page**: `/perizinan/silo`
- **Core Permissions**:
  - `perizinan.silo.index` - View SILO documents
  - `perizinan.silo.create` - Create SILO application
  - `perizinan.silo.edit` - Edit SILO application
  - `perizinan.silo.delete` - Delete SILO application
  - `perizinan.silo.submit` - Submit application
  - `perizinan.silo.approve` - Approve SILO
  - `perizinan.silo.print` - Print SILO document

#### 7.2 EMISI Management (`perizinan.emisi.*`)
- **Page**: `/perizinan/emisi`
- **Core Permissions**:
  - `perizinan.emisi.index` - View EMISI documents
  - `perizinan.emisi.create` - Create EMISI application
  - `perizinan.emisi.edit` - Edit EMISI application
  - `perizinan.emisi.delete` - Delete EMISI application
  - `perizinan.emisi.submit` - Submit application
  - `perizinan.emisi.approve` - Approve EMISI
  - `perizinan.emisi.print` - Print EMISI document

### 8. ADMINISTRATION MODULE
**Base Permissions**: `admin.*.*`

#### 8.1 Administration Dashboard (`admin.dashboard.*`)
- **Page**: `/admin`
- **Core Permissions**:
  - `admin.dashboard.index` - View admin dashboard
  - `admin.dashboard.stats` - View system statistics
  - `admin.dashboard.reports` - Generate admin reports

#### 8.2 Configuration (`admin.config.*`)
- **Page**: `/settings`
- **Core Permissions**:
  - `admin.config.index` - View configuration
  - `admin.config.edit` - Edit system configuration
  - `admin.config.backup` - Backup configuration
  - `admin.config.restore` - Restore configuration

---

## PERMISSION CATEGORIES

### Navigation Permissions
- **Purpose**: Control sidebar menu visibility
- **Pattern**: `{module}.{page}.navigation`
- **Example**: `marketing.quotation.navigation`

### Data Access Permissions
- **Read**: `{module}.{page}.read`
- **Create**: `{module}.{page}.create`
- **Edit**: `{module}.{page}.edit`
- **Delete**: `{module}.{page}.delete`

### Action Permissions
- **Approve**: `{module}.{page}.approve`
- **Reject**: `{module}.{page}.reject`
- **Export**: `{module}.{page}.export`
- **Print**: `{module}.{page}.print`

### Component Permissions
- **Buttons**: `{module}.{page}.{action}.button`
- **Forms**: `{module}.{page}.{action}.form`
- **Modals**: `{module}.{page}.{action}.modal`

---

## IMPLEMENTATION PRIORITY

### Phase 1: Core Navigation (HIGH PRIORITY)
1. Marketing module permissions
2. Service module permissions
3. Administration module permissions

### Phase 2: Operational Modules (MEDIUM PRIORITY)
1. Purchasing module permissions
2. Warehouse module permissions
3. Accounting module permissions

### Phase 3: Specialized Modules (LOW PRIORITY)
1. Operational delivery permissions
2. Perizinan module permissions

---

## NEXT STEPS

1. **Create Permission Records**: Generate all identified permissions in database
2. **Update Controllers**: Implement permission checks in all controllers
3. **Update Views**: Add permission checks for UI elements
4. **Update Navigation**: Implement conditional navigation based on permissions
5. **Role Assignment**: Create default role-permission mappings
6. **Testing**: Comprehensive testing of permission system

---

*Document generated: <?= date('Y-m-d H:i:s') ?>*
*Total Modules: 8*
*Total Pages: 30+*
*Total Permissions: 200+*