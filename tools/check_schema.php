<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci;charset=utf8mb4', 'root', '');

// model_unit PK
$cols = $pdo->query("SHOW COLUMNS FROM model_unit")->fetchAll(PDO::FETCH_COLUMN);
echo "model_unit: " . implode(', ', $cols) . "\n";

// tipe_unit PK  
$cols2 = $pdo->query("SHOW COLUMNS FROM tipe_unit")->fetchAll(PDO::FETCH_COLUMN);
echo "tipe_unit: " . implode(', ', $cols2) . "\n";

// status_unit
$cols3 = $pdo->query("SHOW COLUMNS FROM status_unit")->fetchAll(PDO::FETCH_COLUMN);
echo "status_unit: " . implode(', ', $cols3) . "\n";

// customers key columns
$cols4 = $pdo->query("SHOW COLUMNS FROM customers")->fetchAll(PDO::FETCH_COLUMN);
echo "customers: " . implode(', ', $cols4) . "\n";

// kontrak key columns
echo "kontrak has customer_id: ";
$r = $pdo->query("SHOW COLUMNS FROM kontrak LIKE 'customer_id'")->fetch();
echo ($r ? 'YES' : 'NO') . "\n";

// work_orders: existing area column
$r2 = $pdo->query("SHOW COLUMNS FROM work_orders LIKE 'area%'")->fetchAll(PDO::FETCH_ASSOC);
echo "work_orders area columns: " . json_encode($r2) . "\n";

// kontrak_unit sample
$rows = $pdo->query("SELECT * FROM kontrak_unit LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
echo "sample: " . json_encode($rows) . "\n\n";

// How many units have area_id set?
$r = $pdo->query("SELECT COUNT(*) total, COUNT(area_id) with_area FROM inventory_unit")->fetch();
echo "Units: total={$r['total']}, with_area_id={$r['with_area']}\n\n";

// kontrak columns?
$kcols = $pdo->query("SHOW COLUMNS FROM kontrak")->fetchAll(PDO::FETCH_COLUMN);
echo "kontrak cols: " . implode(', ', $kcols) . "\n\n";

// Units with ACTIVE contracts
$r2 = $pdo->query("
    SELECT COUNT(DISTINCT ku.unit_id) as active_units,
           COUNT(DISTINCT cl.area_id) as distinct_areas
    FROM kontrak_unit ku
    JOIN kontrak k ON k.id = ku.kontrak_id
    JOIN customer_locations cl ON cl.id = ku.customer_location_id
    WHERE k.status = 'ACTIVE' AND ku.status = 'ACTIVE'
")->fetch();
echo "Active contracted units: {$r2['active_units']}, across {$r2['distinct_areas']} areas\n\n";

// area_employee_assignments - how many active?
$r3 = $pdo->query("
    SELECT a.area_code, a.area_name, 
        COUNT(CASE WHEN e.staff_role LIKE '%FOREMAN%' THEN 1 END) as foremans,
        COUNT(CASE WHEN e.staff_role LIKE '%MECHANIC%' THEN 1 END) as mechanics
    FROM areas a
    LEFT JOIN area_employee_assignments aea ON aea.area_id = a.id AND aea.is_active=1
    LEFT JOIN employees e ON e.id = aea.employee_id AND e.is_active=1
    WHERE a.is_active=1
    GROUP BY a.id, a.area_code, a.area_name
    HAVING (foremans > 0 OR mechanics > 0)
    ORDER BY a.area_code
")->fetchAll(PDO::FETCH_ASSOC);
echo "Areas with staff assignments:\n";
foreach ($r3 as $r) echo "  {$r['area_code']} {$r['area_name']}: {$r['foremans']} foreman, {$r['mechanics']} mechanic\n";

// Check view vw_unit_with_contracts
echo "\nvw_unit_with_contracts columns:\n";
$cols = $pdo->query("SHOW COLUMNS FROM vw_unit_with_contracts")->fetchAll(PDO::FETCH_COLUMN);
echo implode(', ', $cols) . "\n";
