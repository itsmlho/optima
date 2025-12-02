<?php

$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "=== STEP-BY-STEP MIGRATION ===" . PHP_EOL;

// Step 1: Create backup tables
echo "Step 1: Creating backup tables..." . PHP_EOL;
$mysqli->query('CREATE TABLE IF NOT EXISTS migration_backup_kontrak AS SELECT * FROM kontrak');
$mysqli->query('CREATE TABLE IF NOT EXISTS migration_backup_kontrak_spesifikasi AS SELECT * FROM kontrak_spesifikasi');
echo "✅ Backup tables created" . PHP_EOL;

// Step 2: Check current quotation_specifications structure
echo PHP_EOL . "Step 2: Checking current quotation_specifications structure..." . PHP_EOL;
$result = $mysqli->query('DESCRIBE quotation_specifications');
$existing_columns = [];
while($row = $result->fetch_assoc()) {
    $existing_columns[] = $row['Field'];
}
echo "Current columns: " . implode(', ', $existing_columns) . PHP_EOL;

// Step 3: Add migration columns if they don't exist
echo PHP_EOL . "Step 3: Adding migration columns..." . PHP_EOL;

$migration_columns = [
    'spek_kode VARCHAR(50)' => 'spek_kode',
    'jumlah_tersedia INT DEFAULT 0' => 'jumlah_tersedia',
    'harga_per_unit_harian DECIMAL(15,2)' => 'harga_per_unit_harian',
    'departemen_id INT' => 'departemen_id',
    'tipe_unit_id INT' => 'tipe_unit_id',
    'kapasitas_id INT' => 'kapasitas_id',
    'charger_id INT' => 'charger_id',
    'mast_id INT' => 'mast_id',
    'ban_id INT' => 'ban_id',
    'roda_id INT' => 'roda_id',
    'valve_id INT' => 'valve_id',
    'jenis_baterai VARCHAR(100)' => 'jenis_baterai',
    'attachment_tipe VARCHAR(100)' => 'attachment_tipe',
    'attachment_merk VARCHAR(100)' => 'attachment_merk',
    'original_kontrak_id INT UNSIGNED' => 'original_kontrak_id',
    'original_kontrak_spek_id INT UNSIGNED' => 'original_kontrak_spek_id'
];

foreach ($migration_columns as $column_def => $column_name) {
    if (!in_array($column_name, $existing_columns)) {
        $sql = "ALTER TABLE quotation_specifications ADD COLUMN $column_def";
        if ($mysqli->query($sql)) {
            echo "✅ Added column: $column_name" . PHP_EOL;
        } else {
            echo "❌ Failed to add column $column_name: " . $mysqli->error . PHP_EOL;
        }
    } else {
        echo "⏭️  Column $column_name already exists" . PHP_EOL;
    }
}

// Step 4: Create indexes
echo PHP_EOL . "Step 4: Creating indexes..." . PHP_EOL;
$indexes = [
    'idx_original_kontrak' => 'original_kontrak_id',
    'idx_original_kontrak_spek' => 'original_kontrak_spek_id'
];

foreach ($indexes as $index_name => $column) {
    $sql = "CREATE INDEX $index_name ON quotation_specifications ($column)";
    if ($mysqli->query($sql)) {
        echo "✅ Created index: $index_name" . PHP_EOL;
    } else {
        if (strpos($mysqli->error, 'already exists') !== false || strpos($mysqli->error, 'Duplicate key') !== false) {
            echo "⏭️  Index $index_name already exists" . PHP_EOL;
        } else {
            echo "❌ Failed to create index $index_name: " . $mysqli->error . PHP_EOL;
        }
    }
}

// Step 5: Check updated structure
echo PHP_EOL . "Step 5: Verifying updated structure..." . PHP_EOL;
$result = $mysqli->query('DESCRIBE quotation_specifications');
$updated_columns = [];
while($row = $result->fetch_assoc()) {
    $updated_columns[] = $row['Field'];
}
echo "Total columns now: " . count($updated_columns) . PHP_EOL;
echo "New migration columns available: " . (in_array('original_kontrak_spek_id', $updated_columns) ? 'YES' : 'NO') . PHP_EOL;

$mysqli->close();

?>