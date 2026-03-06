<?php
echo "Analyzing kontrak with MULTIPLE customer locations...\n";

$handle = fopen('kontrak_acc.csv', 'r');
fgetcsv($handle, 0, ';', '"', ''); // Skip header

$kontrak_locs = [];
while ($row = fgetcsv($handle, 0, ';', '"', '')) {
    if (empty($row[4])) continue; // Skip if no kontrak number
    
    $kontrak = trim($row[4]);
    $cust_loc = $row[1];
    
    if (!isset($kontrak_locs[$kontrak])) {
        $kontrak_locs[$kontrak] = [];
    }
    
    if (!in_array($cust_loc, $kontrak_locs[$kontrak])) {
        $kontrak_locs[$kontrak][] = $cust_loc;
    }
}
fclose($handle);

// Find contracts with multiple locations
$multi = [];
foreach ($kontrak_locs as $kontrak => $locs) {
    if (count($locs) > 1) {
        $multi[$kontrak] = $locs;
    }
}

echo "Total kontrak: " . count($kontrak_locs) . "\n";
echo "Kontrak with MULTIPLE locations: " . count($multi) . "\n\n";

if (count($multi) > 0) {
    echo "Examples:\n";
    $count = 0;
    foreach ($multi as $kontrak => $locs) {
        echo "  \"" . substr($kontrak, 0, 50) . "\" -> " . count($locs) . " locations: " . implode(', ', $locs) . "\n";
        $count++;
        if ($count >= 10) break;
    }
} else {
    echo "✓ All contracts are single-location!\n";
}
