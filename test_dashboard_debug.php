<?php
// Debug Dashboard Data
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$dbConfig = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'optima_ci',
];

try {
    $db = new mysqli(
        $dbConfig['hostname'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database']
    );
    
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    
    $db->set_charset('utf8mb4');
    
    echo "<h1>Dashboard Debug</h1>";
    echo "<style>table{border-collapse:collapse;width:100%;margin:20px 0}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#4CAF50;color:white}h2{color:#333;margin-top:30px}</style>";
    
    // 1. Test Total Assets
    echo "<h2>1. Total Assets</h2>";
    $qUnits = $db->query("SELECT COUNT(*) as total FROM inventory_unit");
    $totalUnits = $qUnits ? $qUnits->fetch_object()->total : 0;
    echo "<p>Units: <strong>{$totalUnits}</strong></p>";
    
    $qAtt = $db->query("SELECT COUNT(*) as total FROM inventory_attachment WHERE tipe_item = 'attachment' AND attachment_id IS NOT NULL");
    $totalAtt = $qAtt ? $qAtt->fetch_object()->total : 0;
    echo "<p>Attachments: <strong>{$totalAtt}</strong></p>";
    
    $qCharge = $db->query("SELECT COUNT(*) as total FROM inventory_attachment WHERE tipe_item = 'charger' AND charger_id IS NOT NULL");
    $totalCharge = $qCharge ? $qCharge->fetch_object()->total : 0;
    echo "<p>Chargers: <strong>{$totalCharge}</strong></p>";
    
    $qBat = $db->query("SELECT COUNT(*) as total FROM inventory_attachment WHERE tipe_item = 'battery' AND baterai_id IS NOT NULL");
    $totalBat = $qBat ? $qBat->fetch_object()->total : 0;
    echo "<p>Baterai: <strong>{$totalBat}</strong></p>";
    
    $totalAssets = $totalUnits + $totalAtt + $totalCharge + $totalBat;
    echo "<p style='color:green;font-size:20px'><strong>TOTAL ASSETS: {$totalAssets}</strong></p>";
    
    // 2. Test Active Contracts
    echo "<h2>2. Active Contracts</h2>";
    $qContracts = $db->query("SELECT COUNT(*) as total FROM kontrak WHERE status = 'Aktif'");
    $activeContracts = $qContracts ? $qContracts->fetch_object()->total : 0;
    echo "<p>Active Contracts: <strong>{$activeContracts}</strong></p>";
    
    // Check all contract statuses
    $qAllStatus = $db->query("SELECT status, COUNT(*) as count FROM kontrak GROUP BY status");
    echo "<p>All contract statuses:</p>";
    echo "<table><tr><th>Status</th><th>Count</th></tr>";
    while ($row = $qAllStatus->fetch_assoc()) {
        echo "<tr><td>{$row['status']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
    
    // 3. Test WO This Month
    echo "<h2>3. Work Orders This Month</h2>";
    $currentMonth = date('Y-m-01');
    $qWO = $db->query("SELECT COUNT(*) as total FROM work_orders WHERE DATE(created_at) >= '{$currentMonth}'");
    $woThisMonth = $qWO ? $qWO->fetch_object()->total : 0;
    echo "<p>WO This Month: <strong>{$woThisMonth}</strong></p>";
    
    // Check WO statuses
    $qWOStatus = $db->query("
        SELECT wos.status_name, COUNT(wo.id) as count 
        FROM work_orders wo
        LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
        WHERE DATE(wo.created_at) >= '{$currentMonth}'
        GROUP BY wo.status_id, wos.status_name
    ");
    echo "<p>WO by status this month:</p>";
    echo "<table><tr><th>Status</th><th>Count</th></tr>";
    while ($row = $qWOStatus->fetch_assoc()) {
        echo "<tr><td>{$row['status_name']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
    
    // 4. Test SPK This Month
    echo "<h2>4. SPK This Month</h2>";
    $qSPK = $db->query("SELECT COUNT(*) as total FROM spk WHERE DATE(dibuat_pada) >= '{$currentMonth}'");
    $spkThisMonth = $qSPK ? $qSPK->fetch_object()->total : 0;
    echo "<p>SPK This Month: <strong>{$spkThisMonth}</strong></p>";
    
    // 5. Test DI This Month
    echo "<h2>5. DI This Month</h2>";
    $qDI = $db->query("SELECT COUNT(*) as total FROM delivery_instructions WHERE DATE(tanggal_pengiriman) >= '{$currentMonth}'");
    $diThisMonth = $qDI ? $qDI->fetch_object()->total : 0;
    echo "<p>DI This Month: <strong>{$diThisMonth}</strong></p>";
    
    // 6. Summary
    echo "<h2 style='background:#4CAF50;color:white;padding:15px;border-radius:5px'>SUMMARY</h2>";
    echo "<table>";
    echo "<tr><th>Metric</th><th>Value</th></tr>";
    echo "<tr><td>Total Assets</td><td style='font-size:20px;font-weight:bold'>{$totalAssets}</td></tr>";
    echo "<tr><td>Active Contracts</td><td style='font-size:20px;font-weight:bold'>{$activeContracts}</td></tr>";
    echo "<tr><td>WO This Month</td><td style='font-size:20px;font-weight:bold'>{$woThisMonth}</td></tr>";
    echo "<tr><td>SPK This Month</td><td style='font-size:20px;font-weight:bold'>{$spkThisMonth}</td></tr>";
    echo "<tr><td>DI This Month</td><td style='font-size:20px;font-weight:bold'>{$diThisMonth}</td></tr>";
    echo "<tr><td>SPK + DI</td><td style='font-size:20px;font-weight:bold'>" . ($spkThisMonth + $diThisMonth) . "</td></tr>";
    echo "</table>";
    
    $db->close();
    
} catch (Exception $e) {
    echo "<p style='color:red;font-size:20px'>ERROR: " . $e->getMessage() . "</p>";
}
