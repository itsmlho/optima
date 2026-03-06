<?php
// Validation script untuk kontrak_unit CSV sebelum import
$db = new mysqli('localhost', 'root', '', 'optima_ci');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$csvFile = $argv[1] ?? '';

if (empty($csvFile) || !file_exists($csvFile)) {
    die("Usage: php validate_kontrak_unit.php <csv_file>\n");
}

echo "=== VALIDATING KONTRAK_UNIT CSV ===\n";
echo "File: $csvFile\n\n";

// Helper: Convert DD/MM/YYYY to YYYY-MM-DD with validation
function convertDate($dateStr) {
    if (empty($dateStr) || $dateStr === 'NULL') {
        return null;
    }
    
    $parts = explode('/', $dateStr);
    if (count($parts) !== 3) {
        return false; // Invalid format
    }
    
    list($day, $month, $year) = $parts;
    
    if (!checkdate($month, $day, $year)) {
        return false;
    }
    
    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

$handle = fopen($csvFile, 'r');
if (!$handle) {
    die("Cannot open file: $csvFile\n");
}

// Read header
$header = fgetcsv($handle, 0, ';', '"', '');
echo "Columns: " . implode(', ', $header) . "\n";
echo "Column count: " . count($header) . "\n\n";

// Validate header
$required_columns = ['kontrak_id', 'unit_id', 'tanggal_mulai', 'status'];
$missing_columns = array_diff($required_columns, $header);

if (!empty($missing_columns)) {
    die("ERROR: Missing required columns: " . implode(', ', $missing_columns) . "\n");
}

echo "✓ Header validated\n\n";

// Collect all kontrak_ids and unit_ids
$kontrak_ids = [];
$unit_ids = [];
$errors = [];
$warnings = [];
$rowNum = 1;

$valid_statuses = ['ACTIVE', 'PULLED', 'REPLACED', 'INACTIVE', 'MAINTENANCE', 'UNDER_REPAIR', 'TEMP_REPLACED', 'TEMP_ACTIVE', 'TEMP_ENDED'];

while (($data = fgetcsv($handle, 0, ';', '"', '')) !== false) {
    $rowNum++;
    
    if (count($data) < count($header)) {
        $errors[] = "Row $rowNum: Insufficient columns";
        continue;
    }
    
    // Map data
    $row = array_combine($header, $data);
    
    // Validate kontrak_id
    if (empty($row['kontrak_id']) || !is_numeric($row['kontrak_id'])) {
        $errors[] = "Row $rowNum: Invalid kontrak_id";
    } else {
        $kontrak_ids[$row['kontrak_id']] = true;
    }
    
    // Validate unit_id
    if (empty($row['unit_id']) || !is_numeric($row['unit_id'])) {
        $errors[] = "Row $rowNum: Invalid unit_id";
    } else {
        $unit_ids[$row['unit_id']] = true;
    }
    
    // Validate tanggal_mulai
    if (empty($row['tanggal_mulai'])) {
        $errors[] = "Row $rowNum: tanggal_mulai is required";
    } else {
        $converted = convertDate($row['tanggal_mulai']);
        if ($converted === false) {
            $errors[] = "Row $rowNum: Invalid tanggal_mulai format (use DD/MM/YYYY): {$row['tanggal_mulai']}";
        }
    }
    
    // Validate tanggal_selesai (optional)
    if (!empty($row['tanggal_selesai']) && $row['tanggal_selesai'] !== 'NULL') {
        $converted = convertDate($row['tanggal_selesai']);
        if ($converted === false) {
            $errors[] = "Row $rowNum: Invalid tanggal_selesai format (use DD/MM/YYYY): {$row['tanggal_selesai']}";
        }
    }
    
    // Validate status
    if (empty($row['status'])) {
        $errors[] = "Row $rowNum: status is required";
    } elseif (!in_array($row['status'], $valid_statuses)) {
        $errors[] = "Row $rowNum: Invalid status '{$row['status']}'. Valid: " . implode(', ', $valid_statuses);
    }
    
    // Validate harga_sewa (optional, but must be numeric if provided)
    if (isset($row['harga_sewa']) && !empty($row['harga_sewa']) && $row['harga_sewa'] !== 'NULL') {
        if (!is_numeric($row['harga_sewa'])) {
            $errors[] = "Row $rowNum: harga_sewa must be numeric: {$row['harga_sewa']}";
        }
    }
    
    // Validate is_spare (optional, must be 0 or 1)
    if (isset($row['is_spare']) && !empty($row['is_spare']) && $row['is_spare'] !== 'NULL') {
        if (!in_array($row['is_spare'], ['0', '1'])) {
            $errors[] = "Row $rowNum: is_spare must be 0 or 1: {$row['is_spare']}";
        }
    }
}

fclose($handle);

$total_rows = $rowNum - 1;
echo "Total rows to import: $total_rows\n";
echo "Unique kontrak_ids: " . count($kontrak_ids) . "\n";
echo "Unique unit_ids: " . count($unit_ids) . "\n\n";

// Check kontrak_ids exist in database
echo "=== VALIDATING FOREIGN KEYS ===\n";
$missing_kontrak_ids = [];
foreach (array_keys($kontrak_ids) as $kontrak_id) {
    $result = $db->query("SELECT id FROM kontrak WHERE id = $kontrak_id");
    if ($result->num_rows === 0) {
        $missing_kontrak_ids[] = $kontrak_id;
    }
}

if (!empty($missing_kontrak_ids)) {
    $errors[] = "Missing kontrak_ids in database: " . implode(', ', $missing_kontrak_ids);
    echo "✗ Missing kontrak_ids: " . implode(', ', array_slice($missing_kontrak_ids, 0, 10)) . (count($missing_kontrak_ids) > 10 ? '...' : '') . "\n";
} else {
    echo "✓ All kontrak_ids exist in database\n";
}

// Check unit_ids exist in inventory_unit
$missing_unit_ids = [];
foreach (array_keys($unit_ids) as $unit_id) {
    $result = $db->query("SELECT id_inventory_unit FROM inventory_unit WHERE id_inventory_unit = $unit_id");
    if ($result->num_rows === 0) {
        $missing_unit_ids[] = $unit_id;
    }
}

if (!empty($missing_unit_ids)) {
    $errors[] = "Missing unit_ids in inventory_unit: " . implode(', ', $missing_unit_ids);
    echo "✗ Missing unit_ids: " . implode(', ', array_slice($missing_unit_ids, 0, 10)) . (count($missing_unit_ids) > 10 ? '...' : '') . "\n";
} else {
    echo "✓ All unit_ids exist in inventory_unit\n";
}

// Check for duplicate units in active contracts
echo "\n=== CHECKING FOR CONFLICTS ===\n";
$conflicts = [];
foreach (array_keys($unit_ids) as $unit_id) {
    $result = $db->query("
        SELECT ku.id, ku.kontrak_id, k.no_kontrak
        FROM kontrak_unit ku
        JOIN kontrak k ON ku.kontrak_id = k.id
        WHERE ku.unit_id = $unit_id 
        AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
    ");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_object();
        $warnings[] = "Unit $unit_id already in ACTIVE contract (kontrak_id={$row->kontrak_id}, no={$row->no_kontrak})";
    }
}

if (!empty($warnings)) {
    echo "⚠ " . count($warnings) . " warnings:\n";
    foreach (array_slice($warnings, 0, 10) as $warning) {
        echo "  - $warning\n";
    }
    if (count($warnings) > 10) {
        echo "  ... and " . (count($warnings) - 10) . " more\n";
    }
} else {
    echo "✓ No conflicts found\n";
}

// Summary
echo "\n=== VALIDATION SUMMARY ===\n";
echo "Total rows: $total_rows\n";
echo "Errors: " . count($errors) . "\n";
echo "Warnings: " . count($warnings) . "\n";

if (!empty($errors)) {
    echo "\n✗✗✗ VALIDATION FAILED ✗✗✗\n\n";
    echo "Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\nPlease fix errors before importing.\n";
    exit(1);
} else {
    echo "\n✓✓✓ VALIDATION PASSED ✓✓✓\n";
    if (!empty($warnings)) {
        echo "\nWarnings (review before import):\n";
        foreach ($warnings as $warning) {
            echo "  - $warning\n";
        }
    }
    echo "\nFile is ready to import!\n";
    echo "Run: php import_kontrak_unit_append.php $csvFile\n";
    exit(0);
}

$db->close();
