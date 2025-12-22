# Trigger Events Master Data

## Overview
Tabel `trigger_events` adalah **master data** untuk semua event notifikasi yang tersedia di aplikasi OPTIMA. Tabel ini berfungsi sebagai **single source of truth** untuk memastikan konsistensi penamaan event dan mencegah kesalahan penulisan event code.

## Database Structure

### Table: `trigger_events`
```sql
CREATE TABLE trigger_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_code VARCHAR(100) NOT NULL UNIQUE,
    event_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category VARCHAR(50) NULL,
    module VARCHAR(50) NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

### Foreign Key Relationship
```
notification_rules.trigger_event -> trigger_events.event_code
(ON UPDATE CASCADE, ON DELETE RESTRICT)
```

## Statistics
- **Total Events**: 111
- **Categories**: 22 categories
- **Top Categories by Event Count**:
  - Delivery: 13 events
  - Work Order: 11 events
  - Purchase Order: 10 events
  - Inventory: 9 events
  - SPK: 7 events

## Event Categories

### 1. Attachment Events (6)
- `attachment_added` - Attachment Ditambahkan
- `attachment_attached` - Attachment Dipasang
- `attachment_broken` - Attachment Rusak
- `attachment_detached` - Attachment Dilepas
- `attachment_maintenance` - Attachment Maintenance
- `attachment_swapped` - Attachment Di-Swap

### 2. Work Order Events (11)
- `workorder_assigned` - Work Order Ditugaskan
- `workorder_completed` - Work Order Selesai
- `workorder_delayed` - Work Order Terlambat
- `workorder_sparepart_added` - Sparepart Ditambahkan ke Work Order
- `workorder_status_changed` - Status Work Order Berubah
- `work_order_assigned` - Work Order Ditugaskan ke Teknisi
- `work_order_cancelled` - Work Order Dibatalkan
- `work_order_completed` - Work Order Selesai Dikerjakan
- `work_order_created` - Work Order Dibuat
- `work_order_in_progress` - Work Order Dalam Pengerjaan
- `work_order_unit_verified` - Verifikasi Unit Work Order ⭐ **NEW**

### 3. Delivery Events (13)
- `delivery_arrived` - Delivery Tiba
- `delivery_assigned` - Delivery Ditugaskan
- `delivery_completed` - Delivery Selesai
- `delivery_created` - Delivery Dibuat
- `delivery_delayed` - Delivery Terlambat
- `delivery_in_transit` - Delivery Dalam Perjalanan
- `delivery_status_changed` - Status Delivery Berubah
- `di_approved` - DI Disetujui
- `di_cancelled` - DI Dibatalkan
- `di_created` - DI Dibuat
- `di_delivered` - DI Terkirim
- `di_in_progress` - DI Dalam Proses
- `di_submitted` - DI Disubmit

### 4. Purchase Order Events (10)
- `po_approved` - PO Disetujui
- `po_attachment_created` - PO Attachment Dibuat
- `po_created` - PO Dibuat
- `po_received` - PO Diterima
- `po_rejected` - PO Ditolak
- `po_sparepart_created` - PO Sparepart Dibuat
- `po_unit_created` - PO Unit Dibuat
- `po_verification_updated` - Verifikasi PO Diupdate
- `po_verified` - PO Diverifikasi
- `purchase_order_created` - Purchase Order Dibuat

### 5. Inventory Events (9)
- `inventory_unit_added` - Unit Ditambahkan
- `inventory_unit_low_stock` - Unit Stok Rendah
- `inventory_unit_maintenance` - Unit Maintenance
- `inventory_unit_rental_active` - Unit Rental Aktif
- `inventory_unit_returned` - Unit Dikembalikan
- `inventory_unit_status_changed` - Status Unit Berubah
- `unit_location_updated` - Lokasi Unit Diupdate
- `unit_prep_completed` - Unit Prep Selesai
- `unit_prep_started` - Unit Prep Dimulai

### 6. Customer Events (5)
- `customer_created` - Customer Dibuat
- `customer_deleted` - Customer Dihapus
- `customer_location_added` - Lokasi Customer Ditambahkan
- `customer_status_changed` - Status Customer Berubah
- `customer_updated` - Customer Diupdate

### 7. Sparepart Events (5)
- `sparepart_added` - Sparepart Ditambahkan
- `sparepart_low_stock` - Sparepart Stok Rendah
- `sparepart_out_of_stock` - Sparepart Habis
- `sparepart_returned` - Sparepart Dikembalikan
- `sparepart_used` - Sparepart Digunakan

### 8. Other Categories
- Contract Events (3)
- Payment Events (3)
- PMPS Events (3)
- Invoice Events (4)
- Quotation Events (6)
- SPK Events (7)
- Warehouse Events (4)
- User Events (5)
- And more...

## Usage Benefits

### ✅ Konsistensi Event Naming
```php
// ❌ BEFORE: Prone to typos
send_notification('work_order_verifed', $data); // Typo!
send_notification('workorder_verified', $data);  // Inconsistent!

// ✅ AFTER: Foreign key constraint ensures valid events
send_notification('work_order_unit_verified', $data); // ✓ Valid
send_notification('work_order_verifed', $data);       // ✗ Will fail FK constraint
```

### ✅ Validation at Database Level
Foreign key constraint mencegah:
- Event code yang salah eja
- Event code yang tidak terdaftar
- Duplikasi event dengan nama berbeda

### ✅ Easy Event Discovery
```sql
-- List all available events
SELECT event_code, event_name, category, module 
FROM trigger_events 
WHERE is_active = 1 
ORDER BY category, event_name;

-- Check if event exists before creating notification rule
SELECT COUNT(*) FROM trigger_events 
WHERE event_code = 'new_event_name';
```

### ✅ Documentation & Maintenance
- Semua event terdokumentasi di satu tempat
- Mudah untuk audit dan review
- Clear categorization by module and category

## How to Add New Event

### Step 1: Add to `trigger_events` table
```sql
INSERT INTO trigger_events (event_code, event_name, description, category, module, is_active)
VALUES (
    'your_new_event_code',
    'Your Event Name',
    'Description when this event is triggered',
    'category_name',
    'module_name',
    1
);
```

### Step 2: Create helper function in `notification_helper.php`
```php
if (!function_exists('notify_your_new_event')) {
    function notify_your_new_event($data)
    {
        return send_notification('your_new_event_code', [
            'module' => 'your_module',
            'id' => $data['id'] ?? null,
            'your_variable' => $data['your_variable'] ?? '',
            // ... other variables
        ]);
    }
}
```

### Step 3: Create notification rule in database
```sql
INSERT INTO notification_rules (
    name,
    trigger_event,
    title_template,
    message_template,
    target_divisions,
    type,
    is_active
) VALUES (
    'Your Notification Rule Name',
    'your_new_event_code',  -- Must exist in trigger_events!
    'Title: {{variable}}',
    'Message: {{variable}}',
    'Division',
    'info',
    1
);
```

## Files

### SQL Scripts
1. `create_trigger_events_table.sql` - Create table and populate all events
2. `add_trigger_events_foreign_key.sql` - Add foreign key constraint

### Location
- **Helper**: `app/Helpers/notification_helper.php`
- **Database**: `optima_ci.trigger_events`
- **Related Tables**: `notification_rules`, `notifications`

## Migration Notes

✅ **Completed Tasks:**
1. Created `trigger_events` table with 111 events
2. Categorized all events by module and category
3. Added foreign key constraint from `notification_rules`
4. Fixed collation compatibility issues
5. Verified all existing notification rules reference valid events

## Examples

### Get Events by Category
```sql
SELECT event_code, event_name 
FROM trigger_events 
WHERE category = 'work_order' 
  AND is_active = 1;
```

### Check Notification Rule Usage
```sql
SELECT 
    te.event_code,
    te.event_name,
    COUNT(nr.id) as rule_count
FROM trigger_events te
LEFT JOIN notification_rules nr ON te.event_code = nr.trigger_event
GROUP BY te.id
ORDER BY rule_count DESC;
```

### Find Unused Events
```sql
SELECT te.event_code, te.event_name
FROM trigger_events te
LEFT JOIN notification_rules nr ON te.event_code = nr.trigger_event
WHERE nr.id IS NULL
  AND te.is_active = 1;
```

---

**Created**: December 22, 2025  
**Last Updated**: December 22, 2025  
**Status**: ✅ Production Ready
