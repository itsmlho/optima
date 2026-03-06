<?php
$db = new mysqli('localhost', 'root', '', 'optima_ci');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Total kontrak_unit
$result = $db->query('SELECT COUNT(*) as cnt FROM kontrak_unit');
$total = $result->fetch_object()->cnt;
echo "Total kontrak_unit: $total\n";

// ABC Kogen units
$result = $db->query('
    SELECT COUNT(ku.id) as cnt 
    FROM kontrak_unit ku 
    JOIN kontrak k ON ku.kontrak_id = k.id 
    JOIN customers c ON k.customer_id = c.id 
    WHERE c.customer_name LIKE "%ABC Kogen%"
');
$abcUnits = $result->fetch_object()->cnt;
echo "ABC Kogen units: $abcUnits\n";

// ABC Kogen contracts
$result = $db->query('
    SELECT COUNT(DISTINCT k.id) as cnt 
    FROM kontrak k 
    JOIN customers c ON k.customer_id = c.id 
    WHERE c.customer_name LIKE "%ABC Kogen%"
');
$abcContracts = $result->fetch_object()->cnt;
echo "ABC Kogen contracts: $abcContracts\n";

// Sample of ABC Kogen contracts with unit counts
echo "\n=== ABC Kogen Contracts (first 10) ===\n";
$result = $db->query('
    SELECT k.nomor_kontrak, COUNT(ku.id) as unit_count
    FROM kontrak k 
    JOIN customers c ON k.customer_id = c.id 
    LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id
    WHERE c.customer_name LIKE "%ABC Kogen%"
    GROUP BY k.id
    ORDER BY k.id
    LIMIT 10
');

while ($row = $result->fetch_object()) {
    echo "  {$row->nomor_kontrak}: {$row->unit_count} units\n";
}

$db->close();
