<?php
// Helper script untuk list semua kontrak dengan filter
$db = new mysqli('localhost', 'root', '', 'optima_ci');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$filter = $argv[1] ?? '';

echo "=== KONTRAK LIST ===\n";
if (!empty($filter)) {
    echo "Filter: '$filter'\n";
}
echo "\n";

$sql = "SELECT 
    k.id, 
    k.no_kontrak, 
    k.rental_type,
    k.status,
    k.tanggal_mulai,
    k.tanggal_berakhir,
    k.nilai_total,
    c.customer_name,
    cl.location_name,
    COUNT(ku.id) as unit_count
FROM kontrak k
LEFT JOIN customers c ON k.customer_id = c.id
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id
WHERE 1=1";

if (!empty($filter)) {
    $filter = $db->real_escape_string($filter);
    $sql .= " AND (
        k.no_kontrak LIKE '%$filter%' 
        OR c.customer_name LIKE '%$filter%'
        OR cl.location_name LIKE '%$filter%'
    )";
}

$sql .= " GROUP BY k.id ORDER BY k.id";

$result = $db->query($sql);

if ($result->num_rows === 0) {
    echo "No kontrak found.\n";
    exit;
}

$count = 0;
while ($row = $result->fetch_object()) {
    $count++;
    
    $no_kontrak = !empty($row->no_kontrak) ? $row->no_kontrak : '(no number)';
    $customer = $row->customer_name ?? 'N/A';
    $location = $row->location_name ?? 'N/A';
    $nilai = number_format($row->nilai_total, 0, ',', '.');
    
    echo sprintf(
        "id=%-5d | %-40s | %-10s | %10s | %s - %s | units=%d | Rp %s | %s - %s\n",
        $row->id,
        substr($no_kontrak, 0, 40),
        $row->rental_type,
        $row->status,
        $row->tanggal_mulai ?? '',
        $row->tanggal_berakhir ?? '',
        $row->unit_count,
        $nilai,
        substr($customer, 0, 30),
        substr($location, 0, 25)
    );
}

echo "\n";
echo "Total: $count kontrak\n";

// Filter by status
echo "\n=== BY STATUS ===\n";
$result = $db->query("SELECT status, COUNT(*) as cnt FROM kontrak GROUP BY status ORDER BY cnt DESC");
while ($row = $result->fetch_object()) {
    echo "  {$row->status}: {$row->cnt}\n";
}

$db->close();
