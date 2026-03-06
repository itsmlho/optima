<?php
// Import kontrak.csv with full validation
$db = new mysqli('localhost', 'root', '', 'optima_ci');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$csvFile = __DIR__ . '/kontrak.csv';

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
    
    // Validate date
    if (!checkdate($month, $day, $year)) {
        // Try to fix common errors (invalid day)
        $lastDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if ($day > $lastDay) {
            $day = $lastDay;
        }
        
        // Re-validate
        if (!checkdate($month, $day, $year)) {
            return null;
        }
    }
    
    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

// Helper: Convert DD/MM/YYYY HH:MM to YYYY-MM-DD HH:MM:SS
function convertDateTime($datetimeStr) {
    if (empty($datetimeStr) || $datetimeStr === 'NULL') {
        return null;
    }
    
    // Format: DD/MM/YYYY HH:MM
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/', $datetimeStr, $matches)) {
        $day = $matches[1];
        $month = $matches[2];
        $year = $matches[3];
        $hour = $matches[4];
        $minute = $matches[5];
        
        return sprintf('%04d-%02d-%02d %02d:%02d:00', $year, $month, $day, $hour, $minute);
    }
    
    return null;
}

// Step 1: Read CSV and validate
echo "=== STEP 1: Reading and Validating CSV ===\n";
$handle = fopen($csvFile, 'r');
if (!$handle) {
    die("Cannot open CSV file: $csvFile\n");
}

// Read header
$header = fgetcsv($handle, 0, ';', '"', '');
echo "CSV columns: " . implode(', ', $header) . "\n";
echo "Column count: " . count($header) . "\n\n";

// Validate customers and locations exist
$customerIds = [];
$locationIds = [];
$errors = [];
$warnings = [];
$rowNum = 1;

while (($data = fgetcsv($handle, 0, ';', '"', '')) !== false) {
    $rowNum++;
    
    if (count($data) < count($header)) {
        $errors[] = "Row $rowNum: Insufficient columns";
        continue;
    }
    
    $customerId = $data[1]; // customer_id
    $locationId = $data[8]; // customer_location_id
    
    if (!empty($customerId) && $customerId !== 'NULL') {
        $customerIds[$customerId] = true;
    }
    
    if (!empty($locationId) && $locationId !== 'NULL') {
        $locationIds[$locationId] = true;
    }
}

fclose($handle);

echo "Total rows to import: " . ($rowNum - 1) . "\n";
echo "Unique customer_ids: " . count($customerIds) . "\n";
echo "Unique location_ids: " . count($locationIds) . "\n\n";

// Check customer_ids exist
echo "=== STEP 2: Validating Foreign Keys ===\n";
$missingCustomers = [];
foreach (array_keys($customerIds) as $customerId) {
    $result = $db->query("SELECT id FROM customers WHERE id = $customerId");
    if ($result->num_rows === 0) {
        $missingCustomers[] = $customerId;
    }
}

$missingLocations = [];
foreach (array_keys($locationIds) as $locationId) {
    $result = $db->query("SELECT id FROM customer_locations WHERE id = $locationId");
    if ($result->num_rows === 0) {
        $missingLocations[] = $locationId;
    }
}

if (!empty($missingCustomers)) {
    echo "WARNING: Missing customer_ids: " . implode(', ', $missingCustomers) . "\n";
    $warnings[] = "Missing customers: " . count($missingCustomers);
}

if (!empty($missingLocations)) {
    echo "WARNING: Missing location_ids: " . implode(', ', $missingLocations) . "\n";
    $warnings[] = "Missing locations: " . count($missingLocations);
}

if (empty($missingCustomers) && empty($missingLocations)) {
    echo "✓ All foreign keys valid!\n";
}
echo "\n";

// Ask for confirmation
if (!empty($warnings) || !empty($errors)) {
    echo "Found " . count($warnings) . " warnings and " . count($errors) . " errors.\n";
    echo "Continue? (y/n): ";
    $input = trim(fgets(STDIN));
    if (strtolower($input) !== 'y') {
        die("Import cancelled.\n");
    }
}

// Step 3: Backup and truncate
echo "=== STEP 3: Backup and Truncate ===\n";
$db->query("SET SESSION sql_mode = '';");
$db->query("CREATE TABLE IF NOT EXISTS kontrak_backup_20260305 AS SELECT * FROM kontrak");
$db->query("CREATE TABLE IF NOT EXISTS kontrak_unit_backup_20260305 AS SELECT * FROM kontrak_unit");

echo "Backing up kontrak table...\n";
$db->query("TRUNCATE TABLE kontrak_backup_20260305");
$db->query("INSERT INTO kontrak_backup_20260305 SELECT * FROM kontrak");

echo "Backing up kontrak_unit table...\n";
$db->query("TRUNCATE TABLE kontrak_unit_backup_20260305");
$db->query("INSERT INTO kontrak_unit_backup_20260305 SELECT * FROM kontrak_unit");

echo "Truncating tables...\n";
$db->query("SET FOREIGN_KEY_CHECKS = 0");
$db->query("TRUNCATE TABLE kontrak_unit");
$db->query("TRUNCATE TABLE kontrak");
$db->query("SET FOREIGN_KEY_CHECKS = 1");
echo "Tables truncated.\n\n";

// Step 4: Import data
echo "=== STEP 4: Importing Data ===\n";
$handle = fopen($csvFile, 'r');
fgetcsv($handle, 0, ';', '"', ''); // Skip header

$insertedCount = 0;
$errorCount = 0;
$batch = [];
$batchSize = 100;
$rowNum = 1;

while (($data = fgetcsv($handle, 0, ';', '"', '')) !== false) {
    $rowNum++;
    
    // Skip if insufficient columns
    if (count($data) < count($header)) {
        $errorCount++;
        echo "  Skipped row $rowNum: Insufficient columns (got " . count($data) . ", expected " . count($header) . ")\n";
        continue;
    }
    
    // Debug: print row data for problematic rows
    if ($rowNum >= 400 && $rowNum <= 410) {
        echo "  Debug row $rowNum: id={$data[0]}, no_kontrak={$data[9]}, columns=" . count($data) . "\n";
    }
    
    // Parse row
    $id = $data[0];
    $customerId = ($data[1] !== 'NULL' && !empty($data[1])) ? $data[1] : 'NULL';
    $parentContractId = ($data[2] !== 'NULL' && !empty($data[2])) ? $data[2] : 'NULL';
    $isRenewal = isset($data[3]) && $data[3] !== '' && $data[3] !== 'NULL' ? $data[3] : 0;
    $renewalGeneration = isset($data[4]) && $data[4] !== '' && $data[4] !== 'NULL' ? $data[4] : 0;
    $renewalInitiatedAt = convertDateTime($data[5] ?? '');
    $renewalInitiatedBy = ($data[6] !== 'NULL' && !empty($data[6])) ? $data[6] : 'NULL';
    $renewalCompletedAt = convertDateTime($data[7] ?? '');
    $customerLocationId = ($data[8] !== 'NULL' && !empty($data[8])) ? $data[8] : 'NULL';
    $noKontrak = $db->real_escape_string($data[9] ?? '');
    $rentalType = !empty($data[10]) && $data[10] !== 'NULL' ? $data[10] : 'CONTRACT';
    $customerPoNumber = $db->real_escape_string($data[11] ?? '');
    // Sanitize nilai_total - remove non-numeric values
    $nilaiTotalRaw = isset($data[12]) && $data[12] !== '' && $data[12] !== 'NULL' ? $data[12] : '0';
    if (!is_numeric($nilaiTotalRaw)) {
        // Non-numeric value (spare, TRIAL, etc) - set to 0
        $nilaiTotal = 0;
    } else {
        $nilaiTotal = $nilaiTotalRaw;
    }
    $totalUnits = isset($data[13]) && $data[13] !== '' && $data[13] !== 'NULL' ? $data[13] : 0;
    $jenisSewa = !empty($data[14]) && $data[14] !== 'NULL' ? $data[14] : 'BULANAN';
    $billingMethod = !empty($data[15]) && $data[15] !== 'NULL' ? $data[15] : 'CYCLE';
    $tanggalMulai = convertDate($data[16] ?? '') ?? '2000-01-01';
    $tanggalBerakhir = convertDate($data[17] ?? '');
    $status = !empty($data[18]) && $data[18] !== 'NULL' ? $data[18] : 'PENDING';
    $dibuatOleh = ($data[19] !== 'NULL' && !empty($data[19])) ? $data[19] : 'NULL';
    $dibuatPada = convertDateTime($data[20] ?? '') ?? date('Y-m-d H:i:s');
    $diperbaruiPada = convertDateTime($data[21] ?? '') ?? date('Y-m-d H:i:s');
    $billingNotes = $db->real_escape_string($data[22] ?? '');
    $billingStartDate = convertDate($data[23] ?? '');
    $fastTrack = isset($data[24]) && $data[24] !== '' && $data[24] !== 'NULL' ? $data[24] : 0;
    $spotRentalNumber = $db->real_escape_string($data[25] ?? '');
    $estimatedDurationDays = ($data[26] !== 'NULL' && !empty($data[26])) ? $data[26] : 'NULL';
    $actualReturnDate = convertDate($data[27] ?? '');
    $requiresPoApproval = isset($data[28]) && $data[28] !== '' && $data[28] !== 'NULL' ? $data[28] : 0;
    $operatorQuantity = isset($data[29]) && $data[29] !== '' && $data[29] !== 'NULL' ? $data[29] : 0;
    $operatorMonthlyRate = isset($data[30]) && $data[30] !== '' && $data[30] !== 'NULL' ? $data[30] : 0;
    
    // Build INSERT
    $values = "($id, $customerId, $parentContractId, $isRenewal, $renewalGeneration, " .
              ($renewalInitiatedAt ? "'$renewalInitiatedAt'" : 'NULL') . ", $renewalInitiatedBy, " .
              ($renewalCompletedAt ? "'$renewalCompletedAt'" : 'NULL') . ", $customerLocationId, " .
              "'$noKontrak', '$rentalType', " . (empty($customerPoNumber) ? 'NULL' : "'$customerPoNumber'") . ", " .
              "$nilaiTotal, $totalUnits, '$jenisSewa', '$billingMethod', '$tanggalMulai', " .
              ($tanggalBerakhir ? "'$tanggalBerakhir'" : 'NULL') . ", '$status', $dibuatOleh, " .
              "'$dibuatPada', '$diperbaruiPada', " . (empty($billingNotes) ? 'NULL' : "'$billingNotes'") . ", " .
              ($billingStartDate ? "'$billingStartDate'" : 'NULL') . ", $fastTrack, " .
              (empty($spotRentalNumber) ? 'NULL' : "'$spotRentalNumber'") . ", $estimatedDurationDays, " .
              ($actualReturnDate ? "'$actualReturnDate'" : 'NULL') . ", $requiresPoApproval, " .
              "$operatorQuantity, $operatorMonthlyRate)";
    
    $batch[] = $values;
    
    // Insert batch
    if (count($batch) >= $batchSize) {
        $sql = "INSERT INTO kontrak (id, customer_id, parent_contract_id, is_renewal, renewal_generation, " .
               "renewal_initiated_at, renewal_initiated_by, renewal_completed_at, customer_location_id, " .
               "no_kontrak, rental_type, customer_po_number, nilai_total, total_units, jenis_sewa, " .
               "billing_method, tanggal_mulai, tanggal_berakhir, status, dibuat_oleh, dibuat_pada, " .
               "diperbarui_pada, billing_notes, billing_start_date, fast_track, spot_rental_number, " .
               "estimated_duration_days, actual_return_date, requires_po_approval, operator_quantity, " .
               "operator_monthly_rate) VALUES " . implode(', ', $batch);
        
        if (@$db->query($sql)) {
            $insertedCount += count($batch);
            echo "  Progress: $insertedCount/" . ($rowNum - 1) . "\n";
        } else {
            echo "  Batch error at row $rowNum: " . $db->error . "\n";
            echo "  SQL Preview: " . substr($sql, 0, 500) . "...\n";
            echo "  Last 3 value sets:\n";
            foreach (array_slice($batch, -3) as $idx => $vs) {
                echo "    " . substr($vs, 0, 200) . "...\n";
            }
            echo "  Trying individual inserts...\n";
            
            foreach ($batch as $idx => $valueSet) {
                $sqlSingle = "INSERT INTO kontrak (id, customer_id, parent_contract_id, is_renewal, renewal_generation, " .
                           "renewal_initiated_at, renewal_initiated_by, renewal_completed_at, customer_location_id, " .
                           "no_kontrak, rental_type, customer_po_number, nilai_total, total_units, jenis_sewa, " .
                           "billing_method, tanggal_mulai, tanggal_berakhir, status, dibuat_oleh, dibuat_pada, " .
                           "diperbarui_pada, billing_notes, billing_start_date, fast_track, spot_rental_number, " .
                           "estimated_duration_days, actual_return_date, requires_po_approval, operator_quantity, " .
                           "operator_monthly_rate) VALUES $valueSet";
                
                if ($db->query($sqlSingle)) {
                    $insertedCount++;
                } else {
                    $errorCount++;
                    echo "    Row " . ($rowNum - count($batch) + $idx) . " failed: " . $db->error . "\n";
                }
            }
        }
        
        $batch = [];
    }
}

// Insert remaining batch
if (!empty($batch)) {
    $sql = "INSERT INTO kontrak (id, customer_id, parent_contract_id, is_renewal, renewal_generation, " .
           "renewal_initiated_at, renewal_initiated_by, renewal_completed_at, customer_location_id, " .
           "no_kontrak, rental_type, customer_po_number, nilai_total, total_units, jenis_sewa, " .
           "billing_method, tanggal_mulai, tanggal_berakhir, status, dibuat_oleh, dibuat_pada, " .
           "diperbarui_pada, billing_notes, billing_start_date, fast_track, spot_rental_number, " .
           "estimated_duration_days, actual_return_date, requires_po_approval, operator_quantity, " .
           "operator_monthly_rate) VALUES " . implode(', ', $batch);
    
    if (@$db->query($sql)) {
        $insertedCount += count($batch);
    } else {
        foreach ($batch as $valueSet) {
            $sqlSingle = "INSERT INTO kontrak (id, customer_id, parent_contract_id, is_renewal, renewal_generation, " .
                       "renewal_initiated_at, renewal_initiated_by, renewal_completed_at, customer_location_id, " .
                       "no_kontrak, rental_type, customer_po_number, nilai_total, total_units, jenis_sewa, " .
                       "billing_method, tanggal_mulai, tanggal_berakhir, status, dibuat_oleh, dibuat_pada, " .
                       "diperbarui_pada, billing_notes, billing_start_date, fast_track, spot_rental_number, " .
                       "estimated_duration_days, actual_return_date, requires_po_approval, operator_quantity, " .
                       "operator_monthly_rate) VALUES $valueSet";
            
            if ($db->query($sqlSingle)) {
                $insertedCount++;
            } else {
                $errorCount++;
            }
        }
    }
}

fclose($handle);

// Verify
$result = $db->query("SELECT COUNT(*) as cnt FROM kontrak");
$finalCount = $result->fetch_object()->cnt;

echo "\n=== IMPORT COMPLETE ===\n";
echo "Inserted: $insertedCount\n";
echo "Errors: $errorCount\n";
echo "Final row count: $finalCount\n\n";

// Sample check
echo "First 5 rows:\n";
$result = $db->query("SELECT id, no_kontrak, customer_id, tanggal_mulai, tanggal_berakhir, status FROM kontrak ORDER BY id LIMIT 5");
while ($row = $result->fetch_object()) {
    echo "  id=$row->id, kontrak=$row->no_kontrak, customer=$row->customer_id, $row->tanggal_mulai ~ $row->tanggal_berakhir, $row->status\n";
}

echo "\nLast 5 rows:\n";
$result = $db->query("SELECT id, no_kontrak, customer_id, tanggal_mulai, tanggal_berakhir, status FROM kontrak ORDER BY id DESC LIMIT 5");
while ($row = $result->fetch_object()) {
    echo "  id=$row->id, kontrak=$row->no_kontrak, customer=$row->customer_id, $row->tanggal_mulai ~ $row->tanggal_berakhir, $row->status\n";
}

$db->close();
echo "\nDone!\n";
