-- OPTIMA Performance Optimization - Database Indexes
-- Implementasi strategis untuk meningkatkan performa query

-- ===============================
-- CRITICAL PERFORMANCE INDEXES
-- ===============================

-- 1. Inventory Unit Indexes (Most Frequently Accessed)
CREATE INDEX IF NOT EXISTS idx_inventory_unit_status ON inventory_unit(status_unit_id);
CREATE INDEX IF NOT EXISTS idx_inventory_unit_kontrak ON inventory_unit(kontrak_id);
CREATE INDEX IF NOT EXISTS idx_inventory_unit_customer ON inventory_unit(customer_id);
CREATE INDEX IF NOT EXISTS idx_inventory_unit_no_unit ON inventory_unit(no_unit);

-- 2. Work Orders Performance
CREATE INDEX IF NOT EXISTS idx_work_orders_status_priority ON work_orders(status_id, priority_id);
CREATE INDEX IF NOT EXISTS idx_work_orders_assigned_tech ON work_orders(assigned_technician_id, status_id);
CREATE INDEX IF NOT EXISTS idx_work_orders_created_date ON work_orders(created_at);

-- 3. Purchase Orders Optimization
CREATE INDEX IF NOT EXISTS idx_purchase_orders_supplier ON purchase_orders(supplier_id, created_at);
CREATE INDEX IF NOT EXISTS idx_po_units_verification ON po_units(po_id, status_verifikasi);
CREATE INDEX IF NOT EXISTS idx_po_units_po_status ON po_units(po_id, status_verifikasi, created_at);

-- 4. Contract Management
CREATE INDEX IF NOT EXISTS idx_kontrak_customer_location ON kontrak(customer_location_id, status);
CREATE INDEX IF NOT EXISTS idx_kontrak_status_date ON kontrak(status, tanggal_kontrak);
CREATE INDEX IF NOT EXISTS idx_customer_locations_customer ON customer_locations(customer_id);

-- 5. Activity Logging Performance
CREATE INDEX IF NOT EXISTS idx_activity_log_user_date ON activity_log(user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_activity_log_entity ON activity_log(entity_type, entity_id);

-- 6. RBAC System Optimization
CREATE INDEX IF NOT EXISTS idx_user_roles_user ON user_roles(user_id);
CREATE INDEX IF NOT EXISTS idx_user_permissions_user ON user_permissions(user_id);
CREATE INDEX IF NOT EXISTS idx_role_permissions_role ON role_permissions(role_id);

-- 7. System Management
CREATE INDEX IF NOT EXISTS idx_notifications_user_read ON notifications(user_id, is_read, created_at);
CREATE INDEX IF NOT EXISTS idx_otp_codes_user_valid ON otp_codes(user_id, is_used, expires_at);

-- ===============================
-- COMPOSITE INDEXES FOR COMPLEX QUERIES
-- ===============================

-- Dashboard queries optimization
CREATE INDEX IF NOT EXISTS idx_inventory_dashboard ON inventory_unit(status_unit_id, customer_id, created_at);
CREATE INDEX IF NOT EXISTS idx_work_orders_dashboard ON work_orders(status_id, priority_id, assigned_technician_id, created_at);

-- Reporting queries optimization
CREATE INDEX IF NOT EXISTS idx_kontrak_reporting ON kontrak(status, customer_location_id, tanggal_kontrak);
CREATE INDEX IF NOT EXISTS idx_po_reporting ON purchase_orders(supplier_id, status, created_at);

-- Search functionality optimization
CREATE INDEX IF NOT EXISTS idx_customers_search ON customers(customer_name, customer_code);
CREATE INDEX IF NOT EXISTS idx_suppliers_search ON suppliers(nama_supplier, kode_supplier);

-- ===============================
-- QUERY OPTIMIZATION NOTES
-- ===============================
/*
1. Indexes dibuat dengan IF NOT EXISTS untuk mencegah error jika sudah ada
2. Composite indexes diurutkan berdasarkan selectivity (paling selektif pertama)
3. Foreign key indexes untuk JOIN performance
4. Date indexes untuk range queries dan reporting
5. Text search indexes untuk frequently searched fields

MONITORING:
- Monitor query performance dengan EXPLAIN sebelum dan sesudah
- Check index usage dengan SHOW INDEX FROM table_name
- Monitor slow query log untuk identifikasi bottlenecks

MAINTENANCE:
- ANALYZE TABLE setelah bulk operations
- OPTIMIZE TABLE secara berkala untuk InnoDB
- Monitor index fragmentation
*/