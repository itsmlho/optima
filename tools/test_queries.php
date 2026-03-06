<?php
/**
 * Direct DB test: verify kontrak units queries work
 */

$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Test Kontrak Units Queries ===\n\n";

// Test 1: Main query from Kontrak::getContractUnits
echo "[TEST 1] Main units query for kontrak_id=509\n";
$query = "
    SELECT
        iu.id_inventory_unit,
        iu.no_unit,
        iu.serial_number,
        COALESCE(mu.merk_unit, 'N/A') as merk,
        COALESCE(mu.model_unit, 'N/A') as model,
        COALESCE(k.kapasitas_unit, 'N/A') as kapasitas,
        COALESCE(CONCAT(tu.tipe, ' ', tu.jenis), 'N/A') as jenis_unit,
        COALESCE(d.nama_departemen, 'N/A') as departemen,
        COALESCE(su.status_unit, 'TERSEDIA') as status,
        iu.status_unit_id,
        ku.kontrak_id,
        iu.harga_sewa_bulanan,
        iu.harga_sewa_harian,
        COALESCE(cl.location_name, iu.lokasi_unit, 'Lokasi Belum Ditentukan') as lokasi
    FROM kontrak_unit ku
    JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
    LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
    LEFT JOIN kapasitas k ON iu.kapasitas_unit_id = k.id_kapasitas
    LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
    LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
    LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
    LEFT JOIN kontrak kt ON kt.id = ku.kontrak_id
    LEFT JOIN customer_locations cl ON cl.id = kt.customer_location_id
    WHERE ku.kontrak_id = ?
    ORDER BY iu.no_unit ASC
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([509]);
    $units = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "  ✅ SUCCESS - " . count($units) . " units found\n";
    foreach ($units as $unit) {
        echo "    - {$unit['no_unit']} | {$unit['merk']} {$unit['model']} | {$unit['status']} | {$unit['lokasi']}\n";
    }
} catch (Exception $e) {
    echo "  ❌ FAILED: " . $e->getMessage() . "\n";
}

// Test 2: Summary query (the one that was broken)
echo "\n[TEST 2] Summary query for kontrak_id=509\n";
$summaryQuery = "
    SELECT 
        COUNT(DISTINCT ku.kontrak_id) as total_spesifikasi,
        COUNT(*) as total_unit_dibutuhkan,
        COALESCE(SUM(iu.harga_sewa_bulanan), 0) as total_nilai_bulanan,
        COALESCE(SUM(iu.harga_sewa_harian), 0) as total_nilai_harian
    FROM kontrak_unit ku
    JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
    WHERE ku.kontrak_id = ?
    AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
";

try {
    $stmt = $pdo->prepare($summaryQuery);
    $stmt->execute([509]);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  ✅ SUCCESS\n";
    echo "    total_unit: {$summary['total_unit_dibutuhkan']}\n";
    echo "    nilai_bulanan: " . number_format($summary['total_nilai_bulanan']) . "\n";
    echo "    nilai_harian: " . number_format($summary['total_nilai_harian']) . "\n";
} catch (Exception $e) {
    echo "  ❌ FAILED: " . $e->getMessage() . "\n";
}

// Test 3: MarketingOptimized stats query
echo "\n[TEST 3] Available units stats (MarketingOptimized)\n";
$statsQuery = "
    SELECT 
        COUNT(*) as total_units,
        COUNT(CASE WHEN ku.unit_id IS NULL THEN 1 END) as available_units,
        COUNT(CASE WHEN ku.unit_id IS NOT NULL THEN 1 END) as contracted_units
    FROM inventory_unit iu
    LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
";

try {
    $result = $pdo->query($statsQuery);
    $stats = $result->fetch(PDO::FETCH_ASSOC);
    echo "  ✅ SUCCESS\n";
    echo "    Total units: {$stats['total_units']}\n";
    echo "    Available: {$stats['available_units']}\n";
    echo "    Contracted: {$stats['contracted_units']}\n";
} catch (Exception $e) {
    echo "  ❌ FAILED: " . $e->getMessage() . "\n";
}

// Test 4: Service.php fallback query
echo "\n[TEST 4] Service fallback query (kontrak_unit junction)\n";
$fallbackQuery = "
    SELECT unit_id as id_inventory_unit
    FROM kontrak_unit
    WHERE kontrak_id = ?
    AND status IN ('ACTIVE', 'TEMP_ACTIVE')
    LIMIT 1
";

try {
    $stmt = $pdo->prepare($fallbackQuery);
    $stmt->execute([509]);
    $unit = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  ✅ SUCCESS\n";
    if ($unit) {
        echo "    First unit: {$unit['id_inventory_unit']}\n";
    } else {
        echo "    No active units found (expected if contract has no active assignments)\n";
    }
} catch (Exception $e) {
    echo "  ❌ FAILED: " . $e->getMessage() . "\n";
}

// Test 5: Check a few more kontrak IDs
echo "\n[TEST 5] Spot check multiple contracts\n";
$stmt = $pdo->query("SELECT kontrak_id, COUNT(*) as cnt FROM kontrak_unit WHERE status = 'ACTIVE' GROUP BY kontrak_id ORDER BY cnt DESC LIMIT 5");
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($contracts as $c) {
    echo "  Kontrak #{$c['kontrak_id']}: {$c['cnt']} active units\n";
}

echo "\n=== All tests completed ===\n";
