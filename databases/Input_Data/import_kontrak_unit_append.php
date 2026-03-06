<?php
// Import kontrak_unit CSV in APPEND mode (no truncate)
$db = new mysqli('localhost', 'root', '', 'optima_ci');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$csvFile = $argv[1] ?? '';

if (empty($csvFile) || !file_exists($csvFile)) {
    die("Usage: php import_kontrak_unit_append.php <csv_file>\n\nRun validation first:\nphp validate_kontrak_unit.php <csv_file>\n");
}

// Helper: Convert DD/MM/YYYY to YYYY-MM-DD with validation
function convertDate($dateStr) {
    if (empty($dateStr) || $dateStr === 'NULL') {
        return null;
    }
    
    $parts = explode('/', $dateStr);
    if (count($parts) !== 3) {
        return null;
    }
    
    list($day, $month, $year) = $parts;
    
    if (!checkdate($month, $day, $year)) {
        // Try to fix
        $lastDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if ($day > $lastDay) {
            $day = $lastDay;
        }
        
        if (!checkdate($month, $day, $year)) {
            return null;
        }
    }
    
    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

echo "=== IMPORTING KONTRAK_UNIT (APPEND MODE) ===\n";
echo "File: $csvFile\n\n";

$handle = fopen($csvFile, 'r');
if (!$handle) {
    die("Cannot open file: $csvFile\n");
}

// Read header
$header = fgetcsv($handle, 0, ';', '"', '');
echo "Columns: " . implode(', ', $header) . "\n\n";

// Backup current data
echo "Creating backup...\n";
$db->query("CREATE TABLE IF NOT EXISTS kontrak_unit_backup_manual_" . date('Ymd_His') . " AS SELECT * FROM kontrak_unit");
echo "Backup created.\n\n";

// Get current max ID
$max_id_result = $db->query("SELECT COALESCE(MAX(id), 0) as max_id FROM kontrak_unit");
$current_max_id = $max_id_result->fetch_object()->max_id;
$next_id = $current_max_id + 1;

echo "Current max ID: $current_max_id\n";
echo "Next ID will start from: $next_id\n\n";

// Import rows
$inserted = 0;
$skipped = 0;
$errors = 0;
$rowNum = 1;
$batch = [];
$batchSize = 100;

while (($data = fgetcsv($handle, 0, ';', '"', '')) !== false) {
    $rowNum++;
    
    if (count($data) < count($header)) {
        $skipped++;
        echo "  Skipped row $rowNum: Insufficient columns\n";
        continue;
    }
    
    // Map data
    $row = array_combine($header, $data);
    
    // Extract and validate
    $kontrak_id = $row['kontrak_id'];
    $unit_id = $row['unit_id'];
    $harga_sewa = isset($row['harga_sewa']) && !empty($row['harga_sewa']) && $row['harga_sewa'] !== 'NULL' ? $row['harga_sewa'] : 'NULL';
    $is_spare = isset($row['is_spare']) && !empty($row['is_spare']) && $row['is_spare'] !== 'NULL' ? $row['is_spare'] : 0;
    $tanggal_mulai = convertDate($row['tanggal_mulai']) ?? '2000-01-01';
    $tanggal_selesai = convertDate($row['tanggal_selesai'] ?? '');
    $status = $row['status'];
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    
    // Check if this combination already exists
    $check = $db->query("SELECT id FROM kontrak_unit WHERE kontrak_id = $kontrak_id AND unit_id = $unit_id");
    if ($check->num_rows > 0) {
        $skipped++;
        echo "  Skipped row $rowNum: Duplicate (kontrak_id=$kontrak_id, unit_id=$unit_id already exists)\n";
        continue;
    }
    
    // Build VALUES
    $values = "($next_id, $kontrak_id, $unit_id, '$tanggal_mulai', " .
              ($tanggal_selesai ? "'$tanggal_selesai'" : 'NULL') . ", '$status', " .
              "NULL, NULL, NULL, NULL, NULL, '$created_at', '$updated_at', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, " .
              ($harga_sewa !== 'NULL' ? $harga_sewa : 'NULL') . ", $is_spare, NULL)";
    
    $batch[] = $values;
    $next_id++;
    
    // Insert batch
    if (count($batch) >= $batchSize) {
        $sql = "INSERT INTO kontrak_unit (id, kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, " .
               "tanggal_tarik, stage_tarik, tanggal_tukar, unit_pengganti_id, unit_sebelumnya_id, created_at, updated_at, " .
               "created_by, updated_by, is_temporary, original_unit_id, temporary_replacement_unit_id, temporary_replacement_date, " .
               "maintenance_start, maintenance_reason, relocation_from_location_id, relocation_to_location_id, harga_sewa, is_spare, customer_location_id) " .
               "VALUES " . implode(', ', $batch);
        
        if ($db->query($sql)) {
            $inserted += count($batch);
            echo "  Progress: $inserted inserted\n";
        } else {
            echo "  Batch error: " . $db->error . "\n";
            echo "  Trying individual inserts...\n";
            
            foreach ($batch as $valueSet) {
                $sqlSingle = "INSERT INTO kontrak_unit (id, kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, " .
                           "tanggal_tarik, stage_tarik, tanggal_tukar, unit_pengganti_id, unit_sebelumnya_id, created_at, updated_at, " .
                           "created_by, updated_by, is_temporary, original_unit_id, temporary_replacement_unit_id, temporary_replacement_date, " .
                           "maintenance_start, maintenance_reason, relocation_from_location_id, relocation_to_location_id, harga_sewa, is_spare, customer_location_id) " .
                           "VALUES $valueSet";
                
                if ($db->query($sqlSingle)) {
                    $inserted++;
                } else {
                    $errors++;
                    echo "    Error: " . $db->error . "\n";
                }
            }
        }
        
        $batch = [];
    }
}

// Insert remaining batch
if (!empty($batch)) {
    $sql = "INSERT INTO kontrak_unit (id, kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, " .
           "tanggal_tarik, stage_tarik, tanggal_tukar, unit_pengganti_id, unit_sebelumnya_id, created_at, updated_at, " .
           "created_by, updated_by, is_temporary, original_unit_id, temporary_replacement_unit_id, temporary_replacement_date, " .
           "maintenance_start, maintenance_reason, relocation_from_location_id, relocation_to_location_id, harga_sewa, is_spare, customer_location_id) " .
           "VALUES " . implode(', ', $batch);
    
    if ($db->query($sql)) {
        $inserted += count($batch);
    } else {
        foreach ($batch as $valueSet) {
            $sqlSingle = "INSERT INTO kontrak_unit (id, kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, " .
                       "tanggal_tarik, stage_tarik, tanggal_tukar, unit_pengganti_id, unit_sebelumnya_id, created_at, updated_at, " .
                       "created_by, updated_by, is_temporary, original_unit_id, temporary_replacement_unit_id, temporary_replacement_date, " .
                       "maintenance_start, maintenance_reason, relocation_from_location_id, relocation_to_location_id, harga_sewa, is_spare, customer_location_id) " .
                       "VALUES $valueSet";
            
            if ($db->query($sqlSingle)) {
                $inserted++;
            } else {
                $errors++;
            }
        }
    }
}

fclose($handle);

// Verify
$total_after = $db->query("SELECT COUNT(*) as cnt FROM kontrak_unit")->fetch_object()->cnt;

echo "\n=== IMPORT COMPLETE ===\n";
echo "Inserted: $inserted\n";
echo "Skipped (duplicates): $skipped\n";
echo "Errors: $errors\n";
echo "Total kontrak_unit before: $current_max_id\n";
echo "Total kontrak_unit after: $total_after\n";

// Sample check
echo "\nLast 5 imported rows:\n";
$result = $db->query("SELECT id, kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status FROM kontrak_unit ORDER BY id DESC LIMIT 5");
while ($row = $result->fetch_object()) {
    echo "  id=$row->id, kontrak=$row->kontrak_id, unit=$row->unit_id, $row->tanggal_mulai ~ " . ($row->tanggal_selesai ?? 'NULL') . ", $row->status\n";
}

$db->close();
echo "\nDone!\n";
