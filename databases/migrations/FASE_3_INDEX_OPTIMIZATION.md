# 🚀 FASE 3: OPTIMASI INDEXING - OPTIMA CI

## 🎯 Strategi Indexing untuk Performance Maksimal

### 1. Analisis Query Pattern
```sql
-- Enable query logging untuk analisis
SET GLOBAL log_queries_not_using_indexes = ON;
SET GLOBAL slow_query_log = ON;
SET GLOBAL long_query_time = 1;

-- Analisis index yang ada
SELECT 
    t.TABLE_NAME,
    t.INDEX_NAME,
    t.COLUMN_NAME,
    t.SEQ_IN_INDEX,
    t.CARDINALITY,
    s.ROWS_READ,
    s.ROWS_EXAMINED
FROM information_schema.STATISTICS t
LEFT JOIN information_schema.TABLE_STATISTICS s ON t.TABLE_NAME = s.TABLE_NAME
WHERE t.TABLE_SCHEMA = 'optima_ci'
ORDER BY t.TABLE_NAME, t.INDEX_NAME, t.SEQ_IN_INDEX;
```

### 2. CRITICAL INDEXES - PRIORITAS TINGGI

#### A. Tabel INVENTORY_UNIT (Core Business)
```sql
-- Index untuk filter status unit (query paling sering)
CREATE INDEX idx_inventory_status_dept ON inventory_unit(status_unit_id, departemen_id);

-- Index untuk search serial number (unique constraint sudah ada, tapi perlu composite)
CREATE INDEX idx_inventory_serial_status ON inventory_unit(serial_number, status_unit_id);

-- Index untuk filter tanggal (reporting & dashboard)
CREATE INDEX idx_inventory_dates ON inventory_unit(created_at, updated_at);
CREATE INDEX idx_inventory_tanggal_kirim ON inventory_unit(tanggal_kirim);

-- Index untuk workflow tracking
CREATE INDEX idx_inventory_workflow ON inventory_unit(workflow_status, di_workflow_id);

-- Index untuk customer & location filtering
CREATE INDEX idx_inventory_customer_location ON inventory_unit(customer_id, customer_location_id, area_id);

-- Index untuk kontrak relationship
CREATE INDEX idx_inventory_kontrak_detail ON inventory_unit(kontrak_id, kontrak_spesifikasi_id, status_unit_id);
```

#### B. Tabel WORK_ORDERS (Heavy Transaction)
```sql
-- Index untuk status tracking dan reporting
CREATE INDEX idx_wo_status_priority ON work_orders(status_id, priority_id, created_at);

-- Index untuk assignment dan scheduling
CREATE INDEX idx_wo_assignments ON work_orders(mechanic_id, foreman_id, admin_id);

-- Index untuk unit-based queries
CREATE INDEX idx_wo_unit_status ON work_orders(unit_id, status_id);

-- Index untuk category dan subcategory filtering
CREATE INDEX idx_wo_category_sub ON work_orders(category_id, subcategory_id);

-- Index untuk date range queries (reporting)
CREATE INDEX idx_wo_date_range ON work_orders(schedule_date, report_date);
CREATE INDEX idx_wo_created_status ON work_orders(created_at, status_id);
```

#### C. Tabel SPK (Service Work Orders)
```sql
-- Index untuk workflow status
CREATE INDEX idx_spk_workflow_status ON spk(status_eksekusi_workflow_id, created_at);

-- Index untuk kontrak dan customer
CREATE INDEX idx_spk_kontrak_customer ON spk(kontrak_id, kontrak_spesifikasi_id);

-- Index untuk jenis perintah kerja
CREATE INDEX idx_spk_jenis_tujuan ON spk(jenis_perintah_kerja_id, tujuan_perintah_kerja_id);

-- Index untuk user tracking
CREATE INDEX idx_spk_user_created ON spk(dibuat_oleh, created_at);
```

#### D. Tabel PURCHASE_ORDERS (Financial Critical)
```sql
-- Index untuk supplier dan status
CREATE INDEX idx_po_supplier_status ON purchase_orders(supplier_id, status);

-- Index untuk tanggal dan approval
CREATE INDEX idx_po_dates ON purchase_orders(tanggal_po, tanggal_approve);
CREATE INDEX idx_po_status_approval ON purchase_orders(status, approved_by);

-- Index untuk no_po search (sering dipakai untuk lookup)
CREATE INDEX idx_po_no_po ON purchase_orders(no_po);

-- Index untuk value-based filtering
CREATE INDEX idx_po_value_status ON purchase_orders(total_harga, status);
```

### 3. COMPOSITE INDEXES - PRIORITAS SEDANG

#### A. User & Authentication Tables
```sql
-- Users - login dan session management
CREATE INDEX idx_users_login ON users(username, is_active);
CREATE INDEX idx_users_email_active ON users(email, is_active);

-- User sessions - cleanup dan security
CREATE INDEX idx_user_sessions_cleanup ON user_sessions(expires_at, is_active);

-- Login attempts - security monitoring
CREATE INDEX idx_login_attempts_ip_time ON login_attempts(ip_address, attempted_at);
CREATE INDEX idx_login_attempts_username_time ON login_attempts(username, attempted_at);
```

#### B. System Activity & Logs
```sql
-- System activity log - audit dan reporting
CREATE INDEX idx_activity_user_action ON system_activity_log(username, action_type, created_at);
CREATE INDEX idx_activity_table_action ON system_activity_log(table_name, action_type, created_at);
CREATE INDEX idx_activity_date_cleanup ON system_activity_log(created_at);

-- Notifications - user-specific queries
CREATE INDEX idx_notifications_user_status ON notifications(user_id, is_read, created_at);
CREATE INDEX idx_notifications_type_time ON notifications(type, created_at);
```

#### C. Master Data Tables
```sql
-- Customers - search dan filtering
CREATE INDEX idx_customers_name_active ON customers(customer_name, is_active);
CREATE INDEX idx_customers_code_active ON customers(customer_code, is_active);

-- Suppliers - procurement queries
CREATE INDEX idx_suppliers_name_active ON suppliers(nama_supplier, status);

-- Employees - HR dan assignment queries
CREATE INDEX idx_employees_dept_active ON employees(departemen_id, is_active);
CREATE INDEX idx_employees_name_dept ON employees(nama_employee, departemen_id);
```

### 4. MAINTENANCE INDEXES - OPTIMASI BACKGROUND

#### A. Delivery Instructions
```sql
-- Workflow tracking
CREATE INDEX idx_di_workflow_status ON delivery_instructions(status_di, status_eksekusi_workflow_id);

-- SPK relationship
CREATE INDEX idx_di_spk_workflow ON delivery_instructions(spk_id, status_di);

-- Date-based queries
CREATE INDEX idx_di_dates ON delivery_instructions(created_at, tanggal_kirim_aktual);
```

#### B. Inventory Attachment
```sql
-- PO relationship dan status
CREATE INDEX idx_inv_attachment_po_status ON inventory_attachment(po_id, status_unit);

-- Unit assignment
CREATE INDEX idx_inv_attachment_unit ON inventory_attachment(id_inventory_unit, tipe_item);

-- Tracking dan audit
CREATE INDEX idx_inv_attachment_dates ON inventory_attachment(created_at, updated_at);
```

### 5. FULLTEXT INDEXES untuk Search

```sql
-- Work orders - search dalam description
ALTER TABLE work_orders ADD FULLTEXT(description, note);

-- SPK - search dalam keterangan
ALTER TABLE spk ADD FULLTEXT(keterangan, catatan_spk);

-- Customers - search nama
ALTER TABLE customers ADD FULLTEXT(customer_name, alamat);

-- Suppliers - search nama dan kontak
ALTER TABLE suppliers ADD FULLTEXT(nama_supplier, alamat);
```

### 6. INDEX MONITORING & MAINTENANCE

```sql
-- Script untuk monitoring index usage
CREATE VIEW v_index_usage AS
SELECT 
    s.TABLE_NAME,
    s.INDEX_NAME,
    s.CARDINALITY,
    su.TOTAL_READ,
    su.MEAN_TIME,
    CASE 
        WHEN su.TOTAL_READ = 0 THEN 'UNUSED'
        WHEN su.TOTAL_READ < 100 THEN 'LOW_USAGE'
        WHEN su.TOTAL_READ < 1000 THEN 'MEDIUM_USAGE'
        ELSE 'HIGH_USAGE'
    END as usage_level
FROM information_schema.STATISTICS s
LEFT JOIN information_schema.INDEX_STATISTICS su ON s.TABLE_NAME = su.TABLE_NAME 
    AND s.INDEX_NAME = su.INDEX_NAME
WHERE s.TABLE_SCHEMA = 'optima_ci'
ORDER BY su.TOTAL_READ DESC;

-- Automated maintenance script
DELIMITER //
CREATE PROCEDURE sp_optimize_indexes()
BEGIN
    -- Rebuild indexes yang fragmentasi > 30%
    DECLARE done INT DEFAULT FALSE;
    DECLARE table_name VARCHAR(64);
    DECLARE cur CURSOR FOR 
        SELECT TABLE_NAME 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = 'optima_ci' 
            AND TABLE_TYPE = 'BASE TABLE';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO table_name;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        SET @sql = CONCAT('OPTIMIZE TABLE ', table_name);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END LOOP;
    
    CLOSE cur;
END//
DELIMITER ;
```

## 📊 INDEX PERFORMANCE MONITORING

### Daily Monitoring Queries:
```sql
-- 1. Cek query yang lambat tanpa index
SELECT 
    query,
    total_time,
    mean_time,
    count_star
FROM performance_schema.events_statements_summary_by_digest 
WHERE avg_timer_wait > 1000000000  -- > 1 second
ORDER BY avg_timer_wait DESC 
LIMIT 10;

-- 2. Cek index yang tidak terpakai
SELECT 
    OBJECT_SCHEMA,
    OBJECT_NAME,
    INDEX_NAME,
    COUNT_READ,
    COUNT_FETCH
FROM performance_schema.table_io_waits_summary_by_index_usage 
WHERE INDEX_NAME IS NOT NULL 
    AND COUNT_READ = 0 
    AND COUNT_FETCH = 0
    AND OBJECT_SCHEMA = 'optima_ci';

-- 3. Monitor table scan vs index usage
SELECT 
    TABLE_NAME,
    ROUND(100 * SUM_ROWS_EXAMINED / SUM_ROWS_SENT, 2) as efficiency_ratio
FROM performance_schema.table_io_waits_summary_by_table 
WHERE OBJECT_SCHEMA = 'optima_ci'
    AND SUM_ROWS_SENT > 0
ORDER BY efficiency_ratio DESC;
```

## 🎯 Expected Performance Gains
- **Query speed**: 60-80% improvement pada filtered queries
- **Dashboard loading**: 70% faster
- **Report generation**: 50-60% faster  
- **Search functionality**: 80% faster dengan fulltext indexes
- **API response time**: 40-50% improvement
- **Database size**: Index overhead +15-20%, tapi query efficiency kompensasi

## ⚠️ Implementation Strategy
1. **Create indexes during low-traffic periods**
2. **Monitor disk space** - indexes butuh storage tambahan
3. **Test each batch** sebelum lanjut ke batch berikutnya
4. **Have rollback plan** untuk setiap index yang bermasalah
5. **Monitor application performance** setelah implementasi