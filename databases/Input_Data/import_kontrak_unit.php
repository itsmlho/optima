<?php
/**
 * Import kontrak_unit.csv into kontrak_unit table
 * - Truncates existing data
 * - Converts DD/MM/YYYY dates to YYYY-MM-DD
 * - Handles NULL values
 * - Batch insert for performance
 */

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$db->set_charset('utf8mb4');

// Read CSV
$file = __DIR__ . '/kontrak_unit.csv';
if (!file_exists($file)) {
    die("CSV file not found: $file\n");
}

$handle = fopen($file, 'r');
$header = fgetcsv($handle, 0, ';', '"', '\\');
echo "CSV columns: " . implode(', ', $header) . "\n";
echo "Column count: " . count($header) . "\n\n";

// Map CSV columns to DB columns
// CSV: id;kontrak_id;unit_id;tanggal_mulai;tanggal_selesai;status;tanggal_tarik;stage_tarik;
//      tanggal_tukar;unit_pengganti_id;unit_sebelumnya_id;created_at;updated_at;created_by;updated_by;
//      is_temporary;original_unit_id;temporary_replacement_unit_id;temporary_replacement_date;
//      maintenance_start;maintenance_reason;relocation_from_location_id;relocation_to_location_id

// Helper: convert DD/MM/YYYY to YYYY-MM-DD (with validation)
function convertDate($val) {
    if (empty($val) || strtoupper($val) === 'NULL') return null;
    // DD/MM/YYYY
    if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $val, $m)) {
        $d = (int)$m[1]; $mo = (int)$m[2]; $y = (int)$m[3];
        // Fix invalid dates (e.g. Feb 29 in non-leap year)
        if (!checkdate($mo, $d, $y)) {
            // Try last valid day of that month
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $mo, $y);
            $d = min($d, $lastDay);
        }
        return sprintf('%04d-%02d-%02d', $y, $mo, $d);
    }
    return $val;
}

// Helper: convert DD/MM/YYYY HH:MM to YYYY-MM-DD HH:MM:SS
function convertDatetime($val) {
    if (empty($val) || strtoupper($val) === 'NULL') return null;
    // DD/MM/YYYY HH:MM
    if (preg_match('#^(\d{2})/(\d{2})/(\d{4})\s+(\d{2}):(\d{2})$#', $val, $m)) {
        return "{$m[3]}-{$m[2]}-{$m[1]} {$m[4]}:{$m[5]}:00";
    }
    // DD/MM/YYYY HH:MM:SS
    if (preg_match('#^(\d{2})/(\d{2})/(\d{4})\s+(\d{2}):(\d{2}):(\d{2})$#', $val, $m)) {
        return "{$m[3]}-{$m[2]}-{$m[1]} {$m[4]}:{$m[5]}:{$m[6]}";
    }
    return $val;
}

function nullVal($val) {
    if ($val === '' || strtoupper(trim($val)) === 'NULL') return null;
    return $val;
}

// Collect all rows
$rows = [];
$lineNum = 1;
while (($data = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {
    $lineNum++;
    // Skip empty rows
    if (empty(trim($data[0] ?? ''))) continue;
    if (count($data) !== count($header)) {
        echo "WARNING: Line $lineNum has " . count($data) . " columns (expected " . count($header) . "), skipping\n";
        continue;
    }
    $row = array_combine($header, $data);
    $rows[] = $row;
}
fclose($handle);

echo "Total rows to import: " . count($rows) . "\n";

// Disable foreign key checks & truncate
$db->query('SET FOREIGN_KEY_CHECKS = 0');
$db->query('TRUNCATE TABLE kontrak_unit');
echo "Table truncated.\n";

// Prepare INSERT statement
$insertCols = 'id, kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, tanggal_tarik, stage_tarik, ' .
    'tanggal_tukar, unit_pengganti_id, unit_sebelumnya_id, created_at, updated_at, created_by, updated_by, ' .
    'is_temporary, original_unit_id, temporary_replacement_unit_id, temporary_replacement_date, ' .
    'maintenance_start, maintenance_reason, relocation_from_location_id, relocation_to_location_id';

$batchSize = 100;
$inserted = 0;
$errors = 0;

for ($i = 0; $i < count($rows); $i += $batchSize) {
    $batch = array_slice($rows, $i, $batchSize);
    $values = [];

    foreach ($batch as $row) {
        $id = (int)$row['id'];
        $kontrakId = (int)$row['kontrak_id'];
        $unitId = (int)$row['unit_id'];
        $tglMulai = convertDate($row['tanggal_mulai']) ?? '2000-01-01';
        $tglSelesai = convertDate($row['tanggal_selesai']);
        $status = nullVal($row['status']) ?? 'ACTIVE';
        $tglTarik = convertDatetime(nullVal($row['tanggal_tarik']));
        $stageTarik = nullVal($row['stage_tarik']);
        $tglTukar = convertDatetime(nullVal($row['tanggal_tukar']));
        $unitPengganti = nullVal($row['unit_pengganti_id']);
        $unitSebelumnya = nullVal($row['unit_sebelumnya_id']);
        $createdAt = convertDatetime($row['created_at']);
        $updatedAt = convertDatetime($row['updated_at']);
        $createdBy = nullVal($row['created_by']);
        $updatedBy = nullVal($row['updated_by']);
        $isTemp = (int)($row['is_temporary'] ?? 0);
        $origUnit = nullVal($row['original_unit_id']);
        $tempReplUnit = nullVal($row['temporary_replacement_unit_id']);
        $tempReplDate = convertDatetime(nullVal($row['temporary_replacement_date']));
        $maintStart = convertDatetime(nullVal($row['maintenance_start']));
        $maintReason = nullVal($row['maintenance_reason']);
        $relocFrom = nullVal($row['relocation_from_location_id']);
        $relocTo = nullVal($row['relocation_to_location_id']);

        $vals = [];
        $vals[] = $id;
        $vals[] = $kontrakId;
        $vals[] = $unitId;
        $vals[] = $tglMulai !== null ? "'" . $db->real_escape_string($tglMulai) . "'" : 'NULL';
        $vals[] = $tglSelesai !== null ? "'" . $db->real_escape_string($tglSelesai) . "'" : 'NULL';
        $vals[] = "'" . $db->real_escape_string($status) . "'";
        $vals[] = $tglTarik !== null ? "'" . $db->real_escape_string($tglTarik) . "'" : 'NULL';
        $vals[] = $stageTarik !== null ? "'" . $db->real_escape_string($stageTarik) . "'" : 'NULL';
        $vals[] = $tglTukar !== null ? "'" . $db->real_escape_string($tglTukar) . "'" : 'NULL';
        $vals[] = $unitPengganti !== null ? (int)$unitPengganti : 'NULL';
        $vals[] = $unitSebelumnya !== null ? (int)$unitSebelumnya : 'NULL';
        $vals[] = $createdAt !== null ? "'" . $db->real_escape_string($createdAt) . "'" : 'NOW()';
        $vals[] = $updatedAt !== null ? "'" . $db->real_escape_string($updatedAt) . "'" : 'NOW()';
        $vals[] = $createdBy !== null ? (int)$createdBy : 'NULL';
        $vals[] = $updatedBy !== null ? (int)$updatedBy : 'NULL';
        $vals[] = $isTemp;
        $vals[] = $origUnit !== null ? (int)$origUnit : 'NULL';
        $vals[] = $tempReplUnit !== null ? (int)$tempReplUnit : 'NULL';
        $vals[] = $tempReplDate !== null ? "'" . $db->real_escape_string($tempReplDate) . "'" : 'NULL';
        $vals[] = $maintStart !== null ? "'" . $db->real_escape_string($maintStart) . "'" : 'NULL';
        $vals[] = $maintReason !== null ? "'" . $db->real_escape_string($maintReason) . "'" : 'NULL';
        $vals[] = $relocFrom !== null ? (int)$relocFrom : 'NULL';
        $vals[] = $relocTo !== null ? (int)$relocTo : 'NULL';

        $values[] = '(' . implode(', ', $vals) . ')';
    }

    $sql = "INSERT INTO kontrak_unit ($insertCols) VALUES " . implode(",\n", $values);
    
    if (@$db->query($sql)) {
        $inserted += count($batch);
    } else {
        echo "BATCH ERROR at rows " . ($i + 1) . "-" . ($i + count($batch)) . ": " . $db->error . "\n";
        echo "  Trying individual inserts...\n";
        // Try individual inserts for this batch
        foreach ($values as $idx => $valStr) {
            $singleSql = "INSERT INTO kontrak_unit ($insertCols) VALUES " . $valStr;
            if (@$db->query($singleSql)) {
                $inserted++;
            } else {
                $errors++;
                $rowId = $batch[$idx]['id'] ?? '?';
                echo "  FAILED id=$rowId: " . $db->error . "\n";
            }
        }
    }
    
    // Progress
    if (($i + $batchSize) % 500 < $batchSize) {
        echo "  Progress: " . min($i + $batchSize, count($rows)) . "/" . count($rows) . "\n";
    }
}

// Re-enable foreign key checks
$db->query('SET FOREIGN_KEY_CHECKS = 1');

// Verify
$result = $db->query('SELECT COUNT(*) as c FROM kontrak_unit');
$finalCount = $result->fetch_assoc()['c'];

echo "\n=== IMPORT COMPLETE ===\n";
echo "Inserted: $inserted\n";
echo "Errors: $errors\n";
echo "Final row count: $finalCount\n";

// Quick verification - show first 5 and last 5
$r = $db->query('SELECT id, kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status FROM kontrak_unit ORDER BY id LIMIT 5');
echo "\nFirst 5 rows:\n";
while ($row = $r->fetch_assoc()) {
    echo "  id={$row['id']}, kontrak={$row['kontrak_id']}, unit={$row['unit_id']}, {$row['tanggal_mulai']} ~ {$row['tanggal_selesai']}, {$row['status']}\n";
}

$r = $db->query('SELECT id, kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status FROM kontrak_unit ORDER BY id DESC LIMIT 5');
echo "\nLast 5 rows:\n";
while ($row = $r->fetch_assoc()) {
    echo "  id={$row['id']}, kontrak={$row['kontrak_id']}, unit={$row['unit_id']}, {$row['tanggal_mulai']} ~ {$row['tanggal_selesai']}, {$row['status']}\n";
}

$db->close();
echo "\nDone!\n";
