-- Check DEAL Quotations with Specifications for SPK Creation
-- Run this to verify data exists

-- 1. Check all DEAL quotations
SELECT 
    q.id_quotation,
    q.quotation_number,
    q.prospect_name,
    q.is_deal,
    q.deal_date,
    q.created_customer_id,
    q.created_contract_id,
    c.customer_name
FROM quotations q
LEFT JOIN customers c ON c.id = q.created_customer_id
WHERE q.is_deal = 1
ORDER BY q.deal_date DESC
LIMIT 20;

-- 2. Check specifications table structure
SHOW COLUMNS FROM quotation_specifications;

-- 3. Check which DEAL quotations have specifications
SELECT 
    q.id_quotation,
    q.quotation_number,
    q.prospect_name,
    COUNT(qs.id_specification) as total_specs,
    SUM(CASE WHEN s.id IS NULL THEN 1 ELSE 0 END) as available_specs
FROM quotations q
LEFT JOIN quotation_specifications qs ON qs.id_quotation = q.id_quotation
LEFT JOIN spk s ON s.quotation_specification_id = qs.id_specification
WHERE q.is_deal = 1
  AND q.created_customer_id IS NOT NULL
GROUP BY q.id_quotation
HAVING total_specs > 0
ORDER BY q.deal_date DESC;

-- 4. Check specific quotation specs (change ID if needed)
SELECT 
    qs.*,
    d.nama_departemen,
    tu.jenis as jenis_tipe_unit,
    COUNT(s.id) as spk_count
FROM quotation_specifications qs
LEFT JOIN departemen d ON d.id_departemen = qs.departemen_id
LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = qs.tipe_unit_id
LEFT JOIN spk s ON s.quotation_specification_id = qs.id_specification
WHERE qs.id_quotation = 20  -- Change this to your quotation ID
GROUP BY qs.id_specification;

-- 5. Verify column name in quotation_specifications table
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_specifications' 
  AND COLUMN_NAME LIKE '%quotation%';
