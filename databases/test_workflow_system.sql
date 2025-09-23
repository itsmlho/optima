-- ========================================
-- Test and verify the enhanced DI workflow system
-- ========================================

USE optima_db;

-- Test 1: Check all workflow tables exist
SELECT 'Checking workflow tables...' as test_step;
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ENGINE
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'optima_db' 
AND TABLE_NAME IN (
    'unit_workflow_log',
    'contract_disconnection_log', 
    'di_workflow_stages',
    'unit_replacement_log'
);

-- Test 2: Check workflow columns in inventory_unit
SELECT 'Checking inventory_unit workflow columns...' as test_step;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_db' 
AND TABLE_NAME = 'inventory_unit'
AND COLUMN_NAME IN (
    'di_workflow_id',
    'workflow_status',
    'contract_disconnect_date',
    'contract_disconnect_stage'
);

-- Test 3: Check views exist
SELECT 'Checking workflow views...' as test_step;
SELECT 
    TABLE_NAME,
    VIEW_DEFINITION
FROM information_schema.VIEWS 
WHERE TABLE_SCHEMA = 'optima_db' 
AND TABLE_NAME IN (
    'contract_unit_summary',
    'unit_workflow_status'
);

-- Test 4: Check stored procedures
SELECT 'Checking stored procedures...' as test_step;
SELECT 
    ROUTINE_NAME,
    ROUTINE_TYPE,
    PARAMETER_STYLE
FROM information_schema.ROUTINES 
WHERE ROUTINE_SCHEMA = 'optima_db' 
AND ROUTINE_NAME = 'ProcessUnitTarik';

-- Test 5: Check triggers
SELECT 'Checking triggers...' as test_step;
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    TRIGGER_SCHEMA
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = 'optima_db'
AND TRIGGER_NAME = 'tr_di_create_workflow_stages';

-- Test 6: Sample data check - contracts with units
SELECT 'Sample contract data...' as test_step;
SELECT 
    k.id,
    k.no_kontrak,
    k.pelanggan,
    k.status,
    COUNT(iu.id_inventory_unit) as unit_count
FROM kontrak k
LEFT JOIN inventory_unit iu ON k.id = iu.kontrak_id
GROUP BY k.id
LIMIT 5;

-- Test 7: Sample data check - jenis and tujuan perintah kerja
SELECT 'Sample jenis perintah kerja...' as test_step;
SELECT * FROM jenis_perintah_kerja LIMIT 5;

SELECT 'Sample tujuan perintah kerja...' as test_step;
SELECT * FROM tujuan_perintah_kerja LIMIT 5;

SELECT 'Enhanced DI Workflow System Verification Complete!' as final_result;