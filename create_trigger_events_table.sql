-- ============================================================================
-- Create Trigger Events Table - Master Data untuk Notification Events
-- ============================================================================

-- Drop table if exists (untuk fresh install)
-- DROP TABLE IF EXISTS trigger_events;

-- Create trigger_events table
CREATE TABLE IF NOT EXISTS trigger_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_code VARCHAR(100) NOT NULL UNIQUE COMMENT 'Unique event identifier (sama dengan notification_rules.trigger_event)',
    event_name VARCHAR(255) NOT NULL COMMENT 'Human-readable event name',
    description TEXT NULL COMMENT 'Detailed description of when this event is triggered',
    category VARCHAR(50) NULL COMMENT 'Event category: work_order, delivery, inventory, customer, etc.',
    module VARCHAR(50) NULL COMMENT 'Application module: service, warehouse, marketing, etc.',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Is this event active?',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_event_code (event_code),
    INDEX idx_category (category),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Master data untuk semua trigger events yang tersedia di aplikasi';

-- Populate with existing events from notification_rules
INSERT INTO trigger_events (event_code, event_name, category, module, description, is_active) VALUES

-- Attachment Events
('attachment_added', 'Attachment Ditambahkan', 'attachment', 'warehouse', 'Notifikasi saat attachment baru ditambahkan ke inventory', 1),
('attachment_attached', 'Attachment Dipasang', 'attachment', 'warehouse', 'Notifikasi saat attachment dipasang ke unit', 1),
('attachment_broken', 'Attachment Rusak', 'attachment', 'warehouse', 'Notifikasi saat attachment dilaporkan rusak', 1),
('attachment_detached', 'Attachment Dilepas', 'attachment', 'warehouse', 'Notifikasi saat attachment dilepas dari unit', 1),
('attachment_maintenance', 'Attachment Maintenance', 'attachment', 'warehouse', 'Notifikasi saat attachment memerlukan maintenance', 1),
('attachment_swapped', 'Attachment Di-Swap', 'attachment', 'warehouse', 'Notifikasi saat attachment di-swap antar unit', 1),

-- Budget Events
('budget_threshold_exceeded', 'Budget Threshold Terlampaui', 'budget', 'finance', 'Notifikasi saat budget threshold terlampaui', 1),

-- Contract Events
('contract_created', 'Kontrak Dibuat', 'contract', 'marketing', 'Notifikasi saat kontrak baru dibuat', 1),
('customer_contract_created', 'Kontrak Customer Dibuat', 'contract', 'marketing', 'Notifikasi saat kontrak customer baru dibuat', 1),
('customer_contract_expired', 'Kontrak Customer Kadaluarsa', 'contract', 'marketing', 'Notifikasi saat kontrak customer kadaluarsa atau hampir kadaluarsa', 1),

-- Customer Events
('customer_created', 'Customer Dibuat', 'customer', 'marketing', 'Notifikasi saat customer baru dibuat', 1),
('customer_deleted', 'Customer Dihapus', 'customer', 'marketing', 'Notifikasi saat customer dihapus', 1),
('customer_location_added', 'Lokasi Customer Ditambahkan', 'customer', 'marketing', 'Notifikasi saat lokasi baru ditambahkan ke customer', 1),
('customer_status_changed', 'Status Customer Berubah', 'customer', 'marketing', 'Notifikasi saat status customer berubah', 1),
('customer_updated', 'Customer Diupdate', 'customer', 'marketing', 'Notifikasi saat data customer diupdate', 1),

-- Delivery Events
('delivery_arrived', 'Delivery Tiba', 'delivery', 'operational', 'Notifikasi saat delivery tiba di tujuan', 1),
('delivery_assigned', 'Delivery Ditugaskan', 'delivery', 'operational', 'Notifikasi saat delivery ditugaskan ke driver', 1),
('delivery_completed', 'Delivery Selesai', 'delivery', 'operational', 'Notifikasi saat delivery selesai', 1),
('delivery_created', 'Delivery Dibuat', 'delivery', 'operational', 'Notifikasi saat delivery instruction dibuat', 1),
('delivery_delayed', 'Delivery Terlambat', 'delivery', 'operational', 'Notifikasi saat delivery mengalami keterlambatan', 1),
('delivery_in_transit', 'Delivery Dalam Perjalanan', 'delivery', 'operational', 'Notifikasi saat delivery dalam perjalanan', 1),
('delivery_status_changed', 'Status Delivery Berubah', 'delivery', 'operational', 'Notifikasi saat status delivery berubah', 1),

-- DI (Delivery Instruction) Events
('di_approved', 'DI Disetujui', 'delivery', 'operational', 'Notifikasi saat DI disetujui', 1),
('di_cancelled', 'DI Dibatalkan', 'delivery', 'operational', 'Notifikasi saat DI dibatalkan', 1),
('di_created', 'DI Dibuat', 'delivery', 'operational', 'Notifikasi saat DI dibuat', 1),
('di_delivered', 'DI Terkirim', 'delivery', 'operational', 'Notifikasi saat DI berhasil dikirim', 1),
('di_in_progress', 'DI Dalam Proses', 'delivery', 'operational', 'Notifikasi saat DI dalam proses pengerjaan', 1),
('di_submitted', 'DI Disubmit', 'delivery', 'operational', 'Notifikasi saat DI disubmit untuk approval', 1),

-- Employee Events
('employee_assigned', 'Pegawai Ditugaskan', 'employee', 'hr', 'Notifikasi saat pegawai ditugaskan ke pekerjaan', 1),
('employee_unassigned', 'Pegawai Dibatalkan Tugasnya', 'employee', 'hr', 'Notifikasi saat pegawai dibatalkan dari tugas', 1),

-- Inspection Events
('inspection_completed', 'Inspeksi Selesai', 'inspection', 'service', 'Notifikasi saat inspeksi selesai dilakukan', 1),
('inspection_scheduled', 'Inspeksi Dijadwalkan', 'inspection', 'service', 'Notifikasi saat inspeksi dijadwalkan', 1),

-- Inventory Unit Events
('inventory_unit_added', 'Unit Ditambahkan', 'inventory', 'warehouse', 'Notifikasi saat unit baru ditambahkan ke inventory', 1),
('inventory_unit_low_stock', 'Unit Stok Rendah', 'inventory', 'warehouse', 'Notifikasi saat stok unit rendah', 1),
('inventory_unit_maintenance', 'Unit Maintenance', 'inventory', 'warehouse', 'Notifikasi saat unit memerlukan maintenance', 1),
('inventory_unit_rental_active', 'Unit Rental Aktif', 'inventory', 'warehouse', 'Notifikasi saat unit rental menjadi aktif', 1),
('inventory_unit_returned', 'Unit Dikembalikan', 'inventory', 'warehouse', 'Notifikasi saat unit dikembalikan dari customer', 1),
('inventory_unit_status_changed', 'Status Unit Berubah', 'inventory', 'warehouse', 'Notifikasi saat status unit berubah', 1),

-- Invoice Events
('invoice_created', 'Invoice Dibuat', 'invoice', 'finance', 'Notifikasi saat invoice baru dibuat', 1),
('invoice_overdue', 'Invoice Jatuh Tempo', 'invoice', 'finance', 'Notifikasi saat invoice jatuh tempo', 1),
('invoice_paid', 'Invoice Dibayar', 'invoice', 'finance', 'Notifikasi saat invoice dibayar', 1),
('invoice_sent', 'Invoice Dikirim', 'invoice', 'finance', 'Notifikasi saat invoice dikirim ke customer', 1),

-- Maintenance Events
('maintenance_completed', 'Maintenance Selesai', 'maintenance', 'service', 'Notifikasi saat maintenance selesai', 1),
('maintenance_scheduled', 'Maintenance Dijadwalkan', 'maintenance', 'service', 'Notifikasi saat maintenance dijadwalkan', 1),

-- Password Events
('password_reset', 'Password Direset', 'security', 'system', 'Notifikasi saat password user direset', 1),

-- Payment Events
('payment_overdue', 'Payment Jatuh Tempo', 'payment', 'finance', 'Notifikasi saat payment jatuh tempo', 1),
('payment_received', 'Payment Diterima', 'payment', 'finance', 'Notifikasi saat payment diterima', 1),
('payment_status_updated', 'Status Payment Diupdate', 'payment', 'finance', 'Notifikasi saat status payment diupdate', 1),

-- Permission Events
('permission_changed', 'Permission Berubah', 'security', 'system', 'Notifikasi saat permission user berubah', 1),

-- PMPS (Preventive Maintenance) Events
('pmps_completed', 'PMPS Selesai', 'pmps', 'service', 'Notifikasi saat PMPS selesai', 1),
('pmps_due_soon', 'PMPS Segera Jatuh Tempo', 'pmps', 'service', 'Notifikasi saat PMPS akan segera jatuh tempo', 1),
('pmps_overdue', 'PMPS Terlambat', 'pmps', 'service', 'Notifikasi saat PMPS terlambat', 1),

-- Purchase Order Events
('po_approved', 'PO Disetujui', 'purchase_order', 'purchasing', 'Notifikasi saat PO disetujui', 1),
('po_attachment_created', 'PO Attachment Dibuat', 'purchase_order', 'purchasing', 'Notifikasi saat PO attachment dibuat', 1),
('po_created', 'PO Dibuat', 'purchase_order', 'purchasing', 'Notifikasi saat PO dibuat', 1),
('po_received', 'PO Diterima', 'purchase_order', 'purchasing', 'Notifikasi saat PO diterima', 1),
('po_rejected', 'PO Ditolak', 'purchase_order', 'purchasing', 'Notifikasi saat PO ditolak', 1),
('po_sparepart_created', 'PO Sparepart Dibuat', 'purchase_order', 'purchasing', 'Notifikasi saat PO sparepart dibuat', 1),
('po_unit_created', 'PO Unit Dibuat', 'purchase_order', 'purchasing', 'Notifikasi saat PO unit dibuat', 1),
('po_verification_updated', 'Verifikasi PO Diupdate', 'purchase_order', 'purchasing', 'Notifikasi saat verifikasi PO diupdate', 1),
('po_verified', 'PO Diverifikasi', 'purchase_order', 'purchasing', 'Notifikasi saat PO diverifikasi', 1),
('purchase_order_created', 'Purchase Order Dibuat', 'purchase_order', 'purchasing', 'Notifikasi saat purchase order dibuat', 1),

-- Quotation Events
('quotation_approved', 'Quotation Disetujui', 'quotation', 'marketing', 'Notifikasi saat quotation disetujui', 1),
('quotation_created', 'Quotation Dibuat', 'quotation', 'marketing', 'Notifikasi saat quotation dibuat', 1),
('quotation_follow_up_required', 'Quotation Perlu Follow Up', 'quotation', 'marketing', 'Notifikasi saat quotation perlu follow up', 1),
('quotation_rejected', 'Quotation Ditolak', 'quotation', 'marketing', 'Notifikasi saat quotation ditolak', 1),
('quotation_sent_to_customer', 'Quotation Dikirim ke Customer', 'quotation', 'marketing', 'Notifikasi saat quotation dikirim ke customer', 1),
('quotation_updated', 'Quotation Diupdate', 'quotation', 'marketing', 'Notifikasi saat quotation diupdate', 1),

-- Role Events
('role_created', 'Role Dibuat', 'security', 'system', 'Notifikasi saat role baru dibuat', 1),
('role_updated', 'Role Diupdate', 'security', 'system', 'Notifikasi saat role diupdate', 1),

-- Service Assignment Events
('service_assignment_completed', 'Service Assignment Selesai', 'service', 'service', 'Notifikasi saat service assignment selesai', 1),
('service_assignment_created', 'Service Assignment Dibuat', 'service', 'service', 'Notifikasi saat service assignment dibuat', 1),
('service_assignment_updated', 'Service Assignment Diupdate', 'service', 'service', 'Notifikasi saat service assignment diupdate', 1),

-- Sparepart Events
('sparepart_added', 'Sparepart Ditambahkan', 'sparepart', 'warehouse', 'Notifikasi saat sparepart ditambahkan', 1),
('sparepart_low_stock', 'Sparepart Stok Rendah', 'sparepart', 'warehouse', 'Notifikasi saat stok sparepart rendah', 1),
('sparepart_out_of_stock', 'Sparepart Habis', 'sparepart', 'warehouse', 'Notifikasi saat sparepart habis', 1),
('sparepart_returned', 'Sparepart Dikembalikan', 'sparepart', 'warehouse', 'Notifikasi saat sparepart dikembalikan', 1),
('sparepart_used', 'Sparepart Digunakan', 'sparepart', 'warehouse', 'Notifikasi saat sparepart digunakan', 1),

-- SPK (Surat Perintah Kerja) Events
('spk_assigned', 'SPK Ditugaskan', 'spk', 'operational', 'Notifikasi saat SPK ditugaskan', 1),
('spk_cancelled', 'SPK Dibatalkan', 'spk', 'operational', 'Notifikasi saat SPK dibatalkan', 1),
('spk_completed', 'SPK Selesai', 'spk', 'operational', 'Notifikasi saat SPK selesai', 1),
('spk_created', 'SPK Dibuat', 'spk', 'operational', 'Notifikasi saat SPK dibuat', 1),
('spk_fabrication_completed', 'SPK Fabrikasi Selesai', 'spk', 'operational', 'Notifikasi saat SPK fabrikasi selesai', 1),
('spk_pdi_completed', 'SPK PDI Selesai', 'spk', 'operational', 'Notifikasi saat SPK PDI selesai', 1),
('spk_unit_prep_completed', 'SPK Unit Prep Selesai', 'spk', 'operational', 'Notifikasi saat SPK unit prep selesai', 1),

-- Supplier Events
('supplier_created', 'Supplier Dibuat', 'supplier', 'purchasing', 'Notifikasi saat supplier baru dibuat', 1),
('supplier_deleted', 'Supplier Dihapus', 'supplier', 'purchasing', 'Notifikasi saat supplier dihapus', 1),
('supplier_updated', 'Supplier Diupdate', 'supplier', 'purchasing', 'Notifikasi saat supplier diupdate', 1),

-- Unit Location Events
('unit_location_updated', 'Lokasi Unit Diupdate', 'inventory', 'warehouse', 'Notifikasi saat lokasi unit diupdate', 1),

-- Unit Prep Events
('unit_prep_completed', 'Unit Prep Selesai', 'inventory', 'warehouse', 'Notifikasi saat unit prep selesai', 1),
('unit_prep_started', 'Unit Prep Dimulai', 'inventory', 'warehouse', 'Notifikasi saat unit prep dimulai', 1),

-- User Events
('user_activated', 'User Diaktifkan', 'user', 'system', 'Notifikasi saat user diaktifkan', 1),
('user_created', 'User Dibuat', 'user', 'system', 'Notifikasi saat user baru dibuat', 1),
('user_deactivated', 'User Dinonaktifkan', 'user', 'system', 'Notifikasi saat user dinonaktifkan', 1),
('user_deleted', 'User Dihapus', 'user', 'system', 'Notifikasi saat user dihapus', 1),
('user_updated', 'User Diupdate', 'user', 'system', 'Notifikasi saat user diupdate', 1),

-- Warehouse Events
('warehouse_stocktake_completed', 'Stocktake Selesai', 'warehouse', 'warehouse', 'Notifikasi saat stocktake selesai', 1),
('warehouse_stock_alert', 'Alert Stok Warehouse', 'warehouse', 'warehouse', 'Notifikasi alert stok warehouse', 1),
('warehouse_transfer_completed', 'Transfer Warehouse Selesai', 'warehouse', 'warehouse', 'Notifikasi saat transfer warehouse selesai', 1),
('warehouse_unit_updated', 'Unit Warehouse Diupdate', 'warehouse', 'warehouse', 'Notifikasi saat unit warehouse diupdate', 1),

-- Work Order Events
('workorder_assigned', 'Work Order Ditugaskan', 'work_order', 'service', 'Notifikasi saat work order ditugaskan', 1),
('workorder_completed', 'Work Order Selesai', 'work_order', 'service', 'Notifikasi saat work order selesai', 1),
('workorder_delayed', 'Work Order Terlambat', 'work_order', 'service', 'Notifikasi saat work order terlambat', 1),
('workorder_sparepart_added', 'Sparepart Ditambahkan ke Work Order', 'work_order', 'service', 'Notifikasi saat sparepart ditambahkan ke work order', 1),
('workorder_status_changed', 'Status Work Order Berubah', 'work_order', 'service', 'Notifikasi saat status work order berubah', 1),
('work_order_assigned', 'Work Order Ditugaskan', 'work_order', 'service', 'Notifikasi saat work order ditugaskan ke teknisi', 1),
('work_order_cancelled', 'Work Order Dibatalkan', 'work_order', 'service', 'Notifikasi saat work order dibatalkan', 1),
('work_order_completed', 'Work Order Selesai', 'work_order', 'service', 'Notifikasi saat work order selesai dikerjakan', 1),
('work_order_created', 'Work Order Dibuat', 'work_order', 'service', 'Notifikasi saat work order baru dibuat', 1),
('work_order_in_progress', 'Work Order Dalam Pengerjaan', 'work_order', 'service', 'Notifikasi saat work order sedang dikerjakan', 1),
('work_order_unit_verified', 'Verifikasi Unit Work Order', 'work_order', 'service', 'Notifikasi saat verifikasi unit work order selesai dengan perubahan data', 1)

ON DUPLICATE KEY UPDATE
    event_name = VALUES(event_name),
    description = VALUES(description),
    category = VALUES(category),
    module = VALUES(module),
    updated_at = NOW();

-- Show results
SELECT 'Trigger Events table created and populated successfully!' as Status;
SELECT COUNT(*) as total_events FROM trigger_events;
SELECT category, COUNT(*) as count FROM trigger_events GROUP BY category ORDER BY count DESC;
