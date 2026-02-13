<?php
// Debug script to check DEAL quotations - Simple version
$host = 'localhost';
$db = 'optima_ci';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>1. All DEAL Quotations</h2>";
    $query1 = "SELECT id_quotation, quotation_number, prospect_name, is_deal, created_customer_id, created_contract_id 
               FROM quotations 
               WHERE is_deal = 1 
               ORDER BY deal_date DESC 
               LIMIT 10";
    $stmt1 = $pdo->query($query1);
    $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($result1, true) . "</pre>";
    
    echo "<h2>2. Quotation Specifications Count</h2>";
    $query2 = "SELECT q.id_quotation, q.quotation_number, 
                      COUNT(qs.id_specification) as total_specs
               FROM quotations q
               LEFT JOIN quotation_specifications qs ON qs.id_quotation = q.id_quotation
               WHERE q.is_deal = 1
               GROUP BY q.id_quotation
               ORDER BY q.deal_date DESC
               LIMIT 10";
    $stmt2 = $pdo->query($query2);
    $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($result2, true) . "</pre>";
    
    echo "<h2>3. Available Specs (not yet used in SPK)</h2>";
    $query3 = "SELECT q.id_quotation, q.quotation_number,
                      COUNT(qs.id_specification) as total_specs,
                      SUM(CASE WHEN s.id IS NULL THEN 1 ELSE 0 END) as available_specs
               FROM quotations q
               LEFT JOIN quotation_specifications qs ON qs.id_quotation = q.id_quotation
               LEFT JOIN spk s ON s.quotation_specification_id = qs.id_specification
               WHERE q.is_deal = 1 AND q.created_customer_id IS NOT NULL
               GROUP BY q.id_quotation
               HAVING total_specs > 0
               ORDER BY q.deal_date DESC
               LIMIT 10";
    $stmt3 = $pdo->query($query3);
    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($result3, true) . "</pre>";
    
    echo "<h2>4. Full Query with JOINs (Same as API)</h2>";
    $query4 = "SELECT q.id_quotation, q.quotation_number, q.prospect_name,
                      q.created_customer_id, q.created_contract_id,
                      c.customer_name,
                      k.id as contract_id, k.no_kontrak, k.status as contract_status,
                      (SELECT COUNT(*) FROM quotation_specifications qs WHERE qs.id_quotation = q.id_quotation) as total_specs,
                      (SELECT COUNT(*) FROM quotation_specifications qs 
                       LEFT JOIN spk s ON s.quotation_specification_id = qs.id_specification 
                       WHERE qs.id_quotation = q.id_quotation AND s.id IS NULL) as available_specs
               FROM quotations q
               LEFT JOIN customers c ON c.id = q.created_customer_id
               LEFT JOIN kontrak k ON k.id = q.created_contract_id
               WHERE q.is_deal = 1 AND q.created_customer_id IS NOT NULL
               GROUP BY q.id_quotation
               HAVING total_specs > 0 AND available_specs > 0
               ORDER BY q.deal_date DESC
               LIMIT 10";
    $stmt4 = $pdo->query($query4);
    $result4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($result4, true) . "</pre>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Database Error:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
