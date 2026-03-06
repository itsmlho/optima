<?php
// Helper script untuk list unit available
$db = new mysqli('localhost', 'root', '', 'optima_ci');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$filter = $argv[1] ?? '';
$statusFilter = $argv[2] ?? '';

echo "=== INVENTORY UNIT LIST ===\n";
if (!empty($filter)) {
    echo "Filter: '$filter'\n";
}
if (!empty($statusFilter)) {
    echo "Status filter: '$statusFilter'\n";
}
echo "\n";

$sql = "SELECT 
    iu.id_inventory_unit as id,
    iu.no_unit,
    iu.serial_number,
    tu.tipe,
    CONCAT(mu.merk_unit, ' ', mu.model_unit) as model,
    iu.fuel_type,
    su.status_unit,
    (SELECT COUNT(*) FROM kontrak_unit ku 
     WHERE ku.unit_id = iu.id_inventory_unit 
     AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')) as active_contracts
FROM inventory_unit iu
LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
WHERE 1=1";

if (!empty($filter)) {
    $filter = $db->real_escape_string($filter);
    $sql .= " AND (
        iu.no_unit LIKE '%$filter%' 
        OR tu.tipe LIKE '%$filter%'
        OR mu.merk_unit LIKE '%$filter%'
        OR mu.model_unit LIKE '%$filter%'
        OR iu.serial_number LIKE '%$filter%'
    )";
}

if (!empty($statusFilter)) {
    $statusFilter = $db->real_escape_string($statusFilter);
    $sql .= " AND su.status_unit = '$statusFilter'";
}

$sql .= " ORDER BY iu.id_inventory_unit";

$result = $db->query($sql);

if ($result->num_rows === 0) {
    echo "No units found.\n";
    exit;
}

echo sprintf(
    "%-6s | %-10s | %-15s | %-20s | %-25s | %-12s | %-12s | %s\n",
    "ID",
    "No Unit",
    "Tipe",
    "Model",
    "Serial Number",
    "Fuel",
    "Status",
    "Contract"
);
echo str_repeat("-", 140) . "\n";

$count = 0;
$available_count = 0;

while ($row = $result->fetch_object()) {
    $count++;
    
    $contract_status = $row->active_contracts > 0 ? "IN USE ($row->active_contracts)" : "AVAILABLE";
    if ($row->active_contracts == 0) {
        $available_count++;
    }
    
    echo sprintf(
        "%-6d | %-10s | %-15s | %-20s | %-25s | %-12s | %-12s | %s\n",
        $row->id,
        $row->no_unit ?? '-',
        substr($row->tipe ?? '-', 0, 15),
        substr($row->model ?? '-', 0, 20),
        substr($row->serial_number ?? '-', 0, 25),
        substr($row->fuel_type ?? '-', 0, 12),
        substr($row->status_unit ?? '-', 0, 12),
        $contract_status
    );
}

echo "\n";
echo "Total: $count units\n";
echo "Available (not in active contract): $available_count units\n";
echo "In use: " . ($count - $available_count) . " units\n";

// Group by tipe
echo "\n=== BY TIPE ===\n";
$result = $db->query("SELECT tu.tipe, COUNT(*) as cnt FROM inventory_unit iu LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit GROUP BY tu.tipe ORDER BY cnt DESC LIMIT 10");
while ($row = $result->fetch_object()) {
    echo "  {$row->tipe}: {$row->cnt}\n";
}

$db->close();
