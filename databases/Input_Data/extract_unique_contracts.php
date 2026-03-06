<?php
/**
 * Extract Unique Contracts from Accounting CSV
 * 
 * Purpose: Identify 348 unique contracts from 2,008 unit relationships
 * Strategy: Group by customer_id + contract_number + dates
 * Output: kontrak_from_accounting.csv (ready for import)
 */

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "=== EXTRACT UNIQUE CONTRACTS FROM ACCOUNTING DATA ===\n\n";

// Read accounting CSV
$file = 'kontrak_acc.csv';
if (!file_exists($file)) {
    die("ERROR: File $file not found!\n");
}

$handle = fopen($file, 'r');
fgetcsv($handle, 0, ';', '"', ''); // Skip header

$raw_data = [];
$line_num = 1;

while (($row = fgetcsv($handle, 0, ';', '"', '')) !== false) {
    $line_num++;
    
    if (count($row) < 9) {
        echo "WARNING: Line $line_num has insufficient columns, skipping.\n";
        continue;
    }
    
    $raw_data[] = [
        'line' => $line_num,
        'customer_id' => trim($row[0]),
        'customer_location_id' => trim($row[1]),
        'unit_id' => trim($row[2]),
        'unit_delivered_date' => trim($row[3]),
        'kontrak_number' => trim($row[4]),
        'po_number' => trim($row[5]),
        'tanggal_mulai' => trim($row[6]),
        'tanggal_berakhir' => trim($row[7]),
        'harga_sewa' => trim($row[8])
    ];
}
fclose($handle);

echo "Total rows read: " . count($raw_data) . "\n\n";

// Group contracts
$contracts = [];
$skipped_rows = [];
$unit_mapping = []; // To track which units belong to which contract

foreach ($raw_data as $row) {
    // Skip if customer_location_id is invalid
    if (empty($row['customer_location_id']) || $row['customer_location_id'] === 'TIDAK KETEMU') {
        $skipped_rows[] = [
            'line' => $row['line'],
            'reason' => 'Invalid customer_location_id',
            'data' => $row
        ];
        continue;
    }
    
    // Generate contract number (prefer Kontrak, fallback to PO)
    $contract_num = !empty($row['kontrak_number']) ? $row['kontrak_number'] : $row['po_number'];
    
    // Normalize contract number for pattern matching
    $contract_upper = strtoupper(trim($contract_num));
    
    // Determine grouping strategy
    $is_recurring_po = (
        stripos($contract_upper, 'PO') !== false && 
        (stripos($contract_upper, 'BULAN') !== false || stripos($contract_upper, 'PERBULAN') !== false)
    ) || in_array($contract_upper, ['PO PERBULAN', 'PO/BULAN', 'PAKAI PO PERBULAN', 'PO00060404']);
    
    $is_empty_contract = empty(trim($contract_num)) || in_array(trim($contract_num), ['-', 'N/A', 'TBD']);
    
    // Generate unique key for grouping
    if ($is_empty_contract) {
        // Empty contracts: group by customer only (1 umbrella contract for all spot rentals)
        $key = sprintf('SPOT_%s', $row['customer_id']);
        $contract_num = 'SPOT RENTAL'; // Standardize name
    } elseif ($is_recurring_po) {
        // Recurring PO: group by customer + contract only (ignore dates for rolling PO)
        $key = sprintf('%s|%s',
            $row['customer_id'],
            $contract_num
        );
    } else {
        // Normal contracts: group by customer + contract + dates (original logic)
        $key = sprintf('%s|%s|%s|%s',
            $row['customer_id'],
            $contract_num,
            $row['tanggal_mulai'],
            $row['tanggal_berakhir']
        );
    }
    
    // Clean harga_sewa (remove commas)
    $harga_clean = (float) str_replace(',', '', $row['harga_sewa']);
    
    if (!isset($contracts[$key])) {
        $contracts[$key] = [
            'customer_id' => $row['customer_id'],
            'customer_location_id' => $row['customer_location_id'],
            'no_kontrak' => $contract_num,
            'kontrak_number' => $row['kontrak_number'],
            'po_number' => $row['po_number'],
            'tanggal_mulai' => $row['tanggal_mulai'],
            'tanggal_berakhir' => $row['tanggal_berakhir'],
            'nilai_total' => 0,
            'total_units' => 0,
            'unit_ids' => [],
            'harga_per_unit' => []
        ];
    }
    
    // Aggregate data
    $contracts[$key]['nilai_total'] += $harga_clean;
    $contracts[$key]['total_units']++;
    $contracts[$key]['unit_ids'][] = $row['unit_id'];
    $contracts[$key]['harga_per_unit'][$row['unit_id']] = $harga_clean;
    
    // Track mapping for later
    $unit_mapping[] = [
        'contract_key' => $key,
        'unit_id' => $row['unit_id'],
        'customer_location_id' => $row['customer_location_id'],
        'harga_sewa' => $harga_clean,
        'unit_delivered_date' => $row['unit_delivered_date']
    ];
}

echo "Unique contracts identified: " . count($contracts) . "\n";
echo "Skipped rows: " . count($skipped_rows) . "\n\n";

// Date conversion function
function convertDate($dateStr) {
    if (empty($dateStr)) return null;
    
    $dateStr = trim($dateStr);
    
    // Try DD/MM/YYYY format
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateStr, $m)) {
        $day = $m[1];
        $month = $m[2];
        $year = $m[3];
        if (checkdate($month, $day, $year)) {
            return "$year-$month-$day";
        }
    }
    
    // Try MM/DD/YYYY format
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateStr, $m)) {
        $month = $m[1];
        $day = $m[2];
        $year = $m[3];
        if (checkdate($month, $day, $year)) {
            return "$year-$month-$day";
        }
    }
    
    // Try MM/DD/YY format
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{2})$/', $dateStr, $m)) {
        $month = $m[1];
        $day = $m[2];
        $year = '20' . $m[3]; // Assume 2000s
        if (checkdate($month, $day, $year)) {
            return "$year-$month-$day";
        }
    }
    
    return null;
}

// Prepare output CSV
$output_file = 'kontrak_from_accounting.csv';
$output = fopen($output_file, 'w');

// Header
fputcsv($output, [
    'customer_id',
    'no_kontrak',
    'customer_po_number',
    'rental_type',
    'jenis_sewa',
    'billing_method',
    'tanggal_mulai',
    'tanggal_berakhir',
    'nilai_total',
    'total_units',
    'status',
    'contract_key'
], ';', '"', '');

$valid_count = 0;
$draft_count = 0;
$contract_id_map = []; // contract_key => row_number for mapping

foreach ($contracts as $key => $contract) {
    // Convert dates
    $start_date = convertDate($contract['tanggal_mulai']);
    $end_date = convertDate($contract['tanggal_berakhir']);
    
    // Determine status (ACTIVE, EXPIRED, PENDING, CANCELLED)
    $status = 'ACTIVE';
    if (empty($contract['no_kontrak']) || empty($start_date) || empty($end_date)) {
        $status = 'PENDING'; // Changed from DRAFT to PENDING (valid ENUM value)
        $draft_count++;
    } else {
        $valid_count++;
    }
    
    // Determine rental_type: CONTRACT if has nomor_kontrak, PO_ONLY if only PO, DAILY_SPOT otherwise
    if (!empty($contract['kontrak_number'])) {
        $rental_type = 'CONTRACT';
    } elseif (!empty($contract['po_number'])) {
        $rental_type = 'PO_ONLY';
    } else {
        $rental_type = 'DAILY_SPOT';
    }
    
    // Use the contract number that was already set during grouping (handles SPOT RENTAL rename)
    $no_kontrak = $contract['no_kontrak'];
    $po_number = !empty($contract['po_number']) ? $contract['po_number'] : '';
    
    fputcsv($output, [
        $contract['customer_id'],
        $no_kontrak,
        $po_number,
        $rental_type,
        'BULANAN', // jenis_sewa (BULANAN or HARIAN)
        'MONTHLY_FIXED', // billing_method (CYCLE, PRORATE, or MONTHLY_FIXED)
        $start_date ?: '',
        $end_date ?: '',
        $contract['nilai_total'],
        $contract['total_units'],
        $status,
        $key // For mapping back to kontrak_unit
    ], ';', '"', '');
    
    $contract_id_map[$key] = count($contract_id_map) + 1; // Sequential ID
}

fclose($output);

echo "=== OUTPUT GENERATED ===\n";
echo "File: $output_file\n";
echo "Valid contracts (ACTIVE): $valid_count\n";
echo "Draft contracts (DRAFT): $draft_count\n";
echo "Total contracts: " . count($contracts) . "\n\n";

// Save unit mapping
$mapping_file = 'unit_to_contract_mapping.csv';
$mapping_output = fopen($mapping_file, 'w');
fputcsv($mapping_output, ['contract_key', 'unit_id', 'customer_location_id', 'harga_sewa', 'unit_delivered_date'], ';', '"', '');

foreach ($unit_mapping as $map) {
    fputcsv($mapping_output, [
        $map['contract_key'],
        $map['unit_id'],
        $map['customer_location_id'],
        $map['harga_sewa'],
        $map['unit_delivered_date']
    ], ';', '"', '');
}
fclose($mapping_output);

echo "Unit mapping saved: $mapping_file\n";
echo "Total unit mappings: " . count($unit_mapping) . "\n\n";

// Save skipped rows report
if (count($skipped_rows) > 0) {
    $skipped_file = 'skipped_rows_report.txt';
    file_put_contents($skipped_file, "=== SKIPPED ROWS REPORT ===\n\n");
    foreach ($skipped_rows as $skip) {
        file_put_contents($skipped_file, 
            "Line {$skip['line']}: {$skip['reason']}\n" .
            "  Customer: {$skip['data']['customer_id']}, Location: {$skip['data']['customer_location_id']}\n" .
            "  Unit: {$skip['data']['unit_id']}, PO: {$skip['data']['po_number']}\n\n",
            FILE_APPEND
        );
    }
    echo "Skipped rows report: $skipped_file\n";
}

echo "\n=== SUMMARY ===\n";
echo "✓ Extracted " . count($contracts) . " unique contracts from " . count($raw_data) . " rows\n";
echo "✓ Generated $output_file for kontrak import\n";
echo "✓ Generated $mapping_file for kontrak_unit import\n";
echo "✓ Ready for next step: Backup & Import\n";

$db->close();
