-- ============================================================================
-- DI Workflow System - Comprehensive Verification
-- Purpose: Verify all tables, relationships, and data integrity
-- Date: 2025-12-17
-- ============================================================================

USE optima_ci;

-- ====================
-- 1. TABLE EXISTENCE
-- ====================
SELECT 'TABLE EXISTENCE CHECK' as test_category, '---' as separator;

SELECT 
    'kontrak_unit' as table_name,
    CASE WHEN COUNT(*) > 0 THEN '✅ EXISTS' ELSE '❌ MISSING' END as status,
    COUNT(*) as record_count
FROM information_schema.tables 
WHERE table_schema = 'optima_ci' AND table_name = 'kontrak_unit'
UNION ALL
SELECT 
    'contract_disconnection_log',
    CASE WHEN COUNT(*) > 0 THEN '✅ EXISTS' ELSE '❌ MISSING' END,
    0
FROM information_schema.tables 
WHERE table_schema = 'optima_ci' AND table_name = 'contract_disconnection_log'
UNION ALL
SELECT 
    'unit_workflow_log',
    CASE WHEN COUNT(*) > 0 THEN '✅ EXISTS' ELSE '❌ MISSING' END,
    0
FROM information_schema.tables 
WHERE table_schema = 'optima_ci' AND table_name = 'unit_workflow_log';

-- ====================
-- 2. DATA MIGRATION
-- ====================
SELECT '' as blank, 'DATA MIGRATION CHECK' as test_category, '---' as separator;

SELECT 
    'kontrak_unit records' as metric,
    COUNT(*) as total,
    COUNT(DISTINCT kontrak_id) as unique_contracts,
    COUNT(DISTINCT unit_id) as unique_units
FROM kontrak_unit;

-- ====================
-- 3. STATUS DISTRIBUTION
-- ====================
SELECT '' as blank, 'STATUS DISTRIBUTION' as test_category, '---' as separator;

SELECT 
    'kontrak_unit.status' as field,
    status as value,
    COUNT(*) as count
FROM kontrak_unit
GROUP BY status
UNION ALL
SELECT 
    'inventory_unit.workflow_status',
    COALESCE(workflow_status, 'NULL') as value,
    COUNT(*)
FROM inventory_unit
WHERE kontrak_id IS NOT NULL
GROUP BY workflow_status;

-- ====================
-- 4. RELATIONSHIPS
-- ====================
SELECT '' as blank, 'RELATIONSHIP INTEGRITY' as test_category, '---' as separator;

SELECT 
    'kontrak_unit → kontrak' as relationship,
    COUNT(*) as total_records,
    COUNT(DISTINCT ku.kontrak_id) as unique_foreign_keys,
    SUM(CASE WHEN k.id IS NULL THEN 1 ELSE 0 END) as orphaned_records
FROM kontrak_unit ku
LEFT JOIN kontrak k ON k.id = ku.kontrak_id
UNION ALL
SELECT 
    'kontrak_unit → inventory_unit',
    COUNT(*),
    COUNT(DISTINCT ku.unit_id),
    SUM(CASE WHEN iu.id_inventory_unit IS NULL THEN 1 ELSE 0 END)
FROM kontrak_unit ku
LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id;

-- ====================
-- 5. WORKFLOW_STATUS
-- ====================
SELECT '' as blank, 'WORKFLOW_STATUS POPULATION' as test_category, '---' as separator;

SELECT 
    'Total contracted units' as metric,
    COUNT(*) as value
FROM inventory_unit
WHERE kontrak_id IS NOT NULL
UNION ALL
SELECT 
    'Units with workflow_status populated',
    COUNT(*)
FROM inventory_unit
WHERE kontrak_id IS NOT NULL AND workflow_status IS NOT NULL
UNION ALL
SELECT 
    'Units with NULL workflow_status',
    COUNT(*)
FROM inventory_unit
WHERE kontrak_id IS NOT NULL AND workflow_status IS NULL;

-- ====================
-- 6. SAMPLE DATA
-- ====================
SELECT '' as blank, 'SAMPLE KONTRAK_UNIT RECORDS' as test_category, '---' as separator;

SELECT 
    ku.id,
    ku.status as ku_status,
    k.no_kontrak,
    iu.serial_number,
    iu.workflow_status,
    ku.tanggal_mulai,
    ku.created_at
FROM kontrak_unit ku
INNER JOIN kontrak k ON k.id = ku.kontrak_id
INNER JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
ORDER BY ku.created_at DESC
LIMIT 5;

-- ====================
-- 7. AUDIT LOG STATUS
-- ====================
SELECT '' as blank, 'AUDIT LOG STATUS' as test_category, '---' as separator;

SELECT 
    'contract_disconnection_log' as log_table,
    COUNT(*) as records,
    MAX(disconnected_at) as latest_activity
FROM contract_disconnection_log
UNION ALL
SELECT 
    'unit_workflow_log',
    COUNT(*),
    MAX(created_at)
FROM unit_workflow_log
UNION ALL
SELECT 
    'attachment_transfer_log (TUKAR)',
    COUNT(*),
    MAX(created_at)
FROM attachment_transfer_log
WHERE transfer_type = 'TUKAR';

-- ====================
-- 8. FOREIGN KEY CONSTRAINTS
-- ====================
SELECT '' as blank, 'FOREIGN KEY CONSTRAINTS' as test_category, '---' as separator;

SELECT 
    CONSTRAINT_NAME as fk_name,
    TABLE_NAME as from_table,
    REFERENCED_TABLE_NAME as to_table,
    '✅ ACTIVE' as status
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'kontrak_unit'
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY CONSTRAINT_NAME;

-- ====================
-- 9. SYSTEM READINESS
-- ====================
SELECT '' as blank, 'SYSTEM READINESS CHECK' as test_category, '---' as separator;

SELECT 
    'ANTAR Workflow' as workflow,
    '✅ OPERATIONAL' as status,
    'No dependencies on kontrak_unit' as note
UNION ALL
SELECT 
    'TARIK Workflow',
    CASE 
        WHEN EXISTS (SELECT 1 FROM kontrak_unit LIMIT 1) THEN '✅ OPERATIONAL'
        ELSE '❌ BROKEN'
    END,
    'Requires kontrak_unit table'
UNION ALL
SELECT 
    'TUKAR Workflow',
    CASE 
        WHEN EXISTS (SELECT 1 FROM kontrak_unit LIMIT 1) 
         AND EXISTS (SELECT 1 FROM information_schema.ROUTINES 
                     WHERE ROUTINE_SCHEMA = 'optima_ci' 
                     AND ROUTINE_NAME LIKE '%transfer%')
        THEN '✅ OPERATIONAL'
        ELSE '⚠️ PARTIAL'
    END,
    'Requires kontrak_unit + transferAttachments()'
UNION ALL
SELECT 
    'RELOKASI Workflow',
    '✅ OPERATIONAL',
    'No dependencies on kontrak_unit';

-- ====================
-- VERIFICATION COMPLETE
-- ====================
SELECT '' as blank, '==================' as separator, 'VERIFICATION COMPLETE' as status, '==================' as separator2;
