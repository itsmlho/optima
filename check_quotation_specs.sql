-- Check Quotation #20 and its specifications

-- 1. Check Quotation exists
SELECT 
    id_quotation, 
    quotation_number, 
    prospect_name,
    workflow_stage,
    created_contract_id
FROM quotations 
WHERE id_quotation = 20;

-- 2. Check Specifications for Quotation #20
SELECT 
    id_specification,
    id_quotation,
    specification_name,
    specification_type,
    quantity,
    monthly_price,
    daily_price,
    is_active,
    created_at
FROM quotation_specifications
WHERE id_quotation = 20;

-- 3. Count all quotations with specifications
SELECT 
    q.id_quotation,
    q.quotation_number,
    q.prospect_name,
    COUNT(qs.id_specification) as spec_count
FROM quotations q
LEFT JOIN quotation_specifications qs ON qs.id_quotation = q.id_quotation
WHERE q.workflow_stage = 'DEAL'
GROUP BY q.id_quotation
HAVING spec_count > 0
ORDER BY q.id_quotation DESC
LIMIT 10;
