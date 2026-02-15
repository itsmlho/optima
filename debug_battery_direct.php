<?php
/**
 * Debug Battery Filter - Direct Database Check
 * Run: php debug_battery_direct.php
 */

require __DIR__ . '/vendor/autoload.php';

$config = new \Config\Database();
$db = \Config\Database::connect();

echo "==========================================\n";
echo "BATTERY FILTER DEBUG - DIRECT DB CHECK\n";
echo "==========================================\n\n";

// 1. Battery Statistics
echo "1. BATTERY STATISTICS:\n";
$query1 = $db->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN baterai_id IS NULL THEN 1 ELSE 0 END) as null_baterai_id,
        SUM(CASE WHEN baterai_id IS NOT NULL THEN 1 ELSE 0 END) as has_baterai_id
    FROM inventory_attachment 
    WHERE LOWER(tipe_item) = 'battery'
");
$stats = $query1->getRow();
print_r($stats);
echo "\n";

// 2. Tipe Item Values
echo "2. DISTINCT tipe_item VALUES:\n";
$query2 = $db->query("
    SELECT tipe_item, COUNT(*) as jumlah
    FROM inventory_attachment
    GROUP BY tipe_item
");
$tipeItems = $query2->getResultArray();
foreach ($tipeItems as $item) {
    echo "  - {$item['tipe_item']}: {$item['jumlah']} records\n";
}
echo "\n";

// 3. Jenis Baterai List
echo "3. JENIS BATERAI IN baterai TABLE:\n";
$query3 = $db->query("
    SELECT jenis_baterai, COUNT(*) as jumlah
    FROM baterai
    WHERE jenis_baterai IS NOT NULL
    GROUP BY jenis_baterai
");
$jenisList = $query3->getResultArray();
foreach ($jenisList as $jenis) {
    echo "  - {$jenis['jenis_baterai']}: {$jenis['jumlah']} records\n";
}
echo "\n";

// 4. Test LITHIUM Query
echo "4. TEST QUERY - LITHIUM:\n";
$query4 = $db->query("
    SELECT COUNT(*) as count
    FROM inventory_attachment ia
    LEFT JOIN baterai b ON ia.baterai_id = b.id
    WHERE LOWER(ia.tipe_item) = 'battery'
    AND ia.baterai_id IS NOT NULL 
    AND b.jenis_baterai IS NOT NULL 
    AND UPPER(b.jenis_baterai) LIKE '%LITHIUM%'
");
echo "  Primary (baterai table): " . $query4->getRow()->count . " records\n";

$query5 = $db->query("
    SELECT COUNT(*) as count
    FROM inventory_attachment ia
    LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
    WHERE LOWER(ia.tipe_item) = 'battery'
    AND (UPPER(a.model) LIKE '%LITHIUM%' OR UPPER(a.tipe) LIKE '%LITHIUM%')
");
echo "  Fallback (attachment table): " . $query5->getRow()->count . " records\n\n";

// 5. Test LEAD Query
echo "5. TEST QUERY - LEAD ACID:\n";
$query6 = $db->query("
    SELECT COUNT(*) as count
    FROM inventory_attachment ia
    LEFT JOIN baterai b ON ia.baterai_id = b.id
    WHERE LOWER(ia.tipe_item) = 'battery'
    AND ia.baterai_id IS NOT NULL 
    AND b.jenis_baterai IS NOT NULL 
    AND UPPER(b.jenis_baterai) LIKE '%LEAD%'
");
echo "  Primary (baterai table): " . $query6->getRow()->count . " records\n";

$query7 = $db->query("
    SELECT COUNT(*) as count
    FROM inventory_attachment ia
    LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
    WHERE LOWER(ia.tipe_item) = 'battery'
    AND (UPPER(a.model) LIKE '%LEAD%' OR UPPER(a.tipe) LIKE '%LEAD%')
");
echo "  Fallback (attachment table): " . $query7->getRow()->count . " records\n\n";

// 6. Sample Battery Records
echo "6. SAMPLE BATTERY RECORDS (first 5):\n";
$query8 = $db->query("
    SELECT 
        ia.id_inventory_attachment,
        ia.tipe_item,
        ia.baterai_id,
        ia.attachment_id,
        b.jenis_baterai,
        a.model as att_model,
        a.tipe as att_tipe
    FROM inventory_attachment ia
    LEFT JOIN baterai b ON ia.baterai_id = b.id
    LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
    WHERE LOWER(ia.tipe_item) = 'battery'
    LIMIT 5
");
$samples = $query8->getResultArray();
foreach ($samples as $sample) {
    echo "  ID: {$sample['id_inventory_attachment']}, ";
    echo "baterai_id: " . ($sample['baterai_id'] ?? 'NULL') . ", ";
    echo "jenis_baterai: " . ($sample['jenis_baterai'] ?? 'NULL') . ", ";
    echo "att_model: " . ($sample['att_model'] ?? 'NULL') . "\n";
}

echo "\n==========================================\n";
echo "DEBUG COMPLETE\n";
echo "==========================================\n";
