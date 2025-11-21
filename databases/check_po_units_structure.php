<?php
/**
 * Script untuk mengecek struktur tabel po_units
 * Jalankan: php databases/check_po_units_structure.php
 */

require __DIR__ . '/../vendor/autoload.php';

$config = new \Config\Database();
$db = \Config\Database::connect();

echo "=== Checking po_units table structure ===\n\n";

// Cek apakah tabel ada
if (!$db->tableExists('po_units')) {
    echo "ERROR: Table po_units tidak ditemukan!\n";
    exit(1);
}

echo "✓ Table po_units exists\n\n";

// Cek struktur tabel
$fields = $db->getFieldData('po_units');

echo "Fields in po_units table:\n";
echo str_repeat("-", 80) . "\n";
printf("%-30s %-15s %-10s %-10s\n", "Field Name", "Type", "Null", "Default");
echo str_repeat("-", 80) . "\n";

$hasCatatanVerifikasi = false;
foreach ($fields as $field) {
    printf("%-30s %-15s %-10s %-10s\n", 
        $field->name, 
        $field->type, 
        $field->nullable ? 'YES' : 'NO',
        $field->default ?? 'NULL'
    );
    
    if ($field->name === 'catatan_verifikasi') {
        $hasCatatanVerifikasi = true;
    }
}

echo str_repeat("-", 80) . "\n\n";

// Cek field catatan_verifikasi
if ($hasCatatanVerifikasi) {
    echo "✓ Field 'catatan_verifikasi' exists\n";
} else {
    echo "✗ Field 'catatan_verifikasi' NOT FOUND!\n";
    echo "\nTo add it, run:\n";
    echo "ALTER TABLE `po_units` ADD COLUMN `catatan_verifikasi` TEXT NULL COMMENT 'Catatan verifikasi' AFTER `status_verifikasi`;\n";
}

echo "\n=== Checking allowedFields in POUnitsModel ===\n\n";
$model = new \App\Models\POUnitsModel();
$reflection = new ReflectionClass($model);
$allowedFieldsProperty = $reflection->getProperty('allowedFields');
$allowedFieldsProperty->setAccessible(true);
$allowedFields = $allowedFieldsProperty->getValue($model);

echo "Allowed fields in model:\n";
foreach ($allowedFields as $field) {
    echo "  - $field\n";
}

if (in_array('catatan_verifikasi', $allowedFields)) {
    echo "\n✓ 'catatan_verifikasi' is in allowedFields\n";
} else {
    echo "\n✗ 'catatan_verifikasi' is NOT in allowedFields!\n";
}

echo "\n=== Done ===\n";

