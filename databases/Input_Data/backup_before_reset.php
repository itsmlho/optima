<?php
/**
 * Backup Current Kontrak & Kontrak_Unit Tables
 * 
 * Purpose: Create safety backup before RESET operation
 * Timestamp: <?= date('Y-m-d H:i:s') ?>

 */

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "=== BACKUP EXISTING DATA ===\n\n";

$timestamp = date('Ymd_His');

// Backup kontrak table
echo "Backing up kontrak table...\n";
$kontrak_result = $db->query("SELECT * FROM kontrak");
$kontrak_count = $kontrak_result->num_rows;

$backup_file = "backup_kontrak_pre_reset_{$timestamp}.sql";
$sql = "-- Backup of kontrak table\n";
$sql .= "-- Created: " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- Rows: $kontrak_count\n\n";

if ($kontrak_count > 0) {
    // Get column names
    $columns = [];
    $fields = $db->query("SHOW COLUMNS FROM kontrak");
    while ($field = $fields->fetch_object()) {
        $columns[] = $field->Field;
    }
    
    $sql .= "INSERT INTO kontrak (" . implode(', ', $columns) . ") VALUES\n";
    
    $rows = [];
    while ($row = $kontrak_result->fetch_assoc()) {
        $values = [];
        foreach ($columns as $col) {
            $val = $row[$col];
            if ($val === null) {
                $values[] = 'NULL';
            } else {
                $values[] = "'" . $db->real_escape_string($val) . "'";
            }
        }
        $rows[] = '(' . implode(', ', $values) . ')';
    }
    
    $sql .= implode(",\n", $rows) . ";\n";
}

file_put_contents($backup_file, $sql);
echo "✓ Kontrak backup saved: $backup_file ($kontrak_count rows)\n\n";

// Backup kontrak_unit table
echo "Backing up kontrak_unit table...\n";
$ku_result = $db->query("SELECT * FROM kontrak_unit");
$ku_count = $ku_result->num_rows;

$backup_file_ku = "backup_kontrak_unit_pre_reset_{$timestamp}.sql";
$sql = "-- Backup of kontrak_unit table\n";
$sql .= "-- Created: " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- Rows: $ku_count\n\n";

if ($ku_count > 0) {
    // Get column names
    $columns = [];
    $fields = $db->query("SHOW COLUMNS FROM kontrak_unit");
    while ($field = $fields->fetch_object()) {
        $columns[] = $field->Field;
    }
    
    $sql .= "INSERT INTO kontrak_unit (" . implode(', ', $columns) . ") VALUES\n";
    
    $rows = [];
    while ($row = $ku_result->fetch_assoc()) {
        $values = [];
        foreach ($columns as $col) {
            $val = $row[$col];
            if ($val === null) {
                $values[] = 'NULL';
            } else {
                $values[] = "'" . $db->real_escape_string($val) . "'";
            }
        }
        $rows[] = '(' . implode(', ', $values) . ')';
    }
    
    $sql .= implode(",\n", $rows) . ";\n";
}

file_put_contents($backup_file_ku, $sql);
echo "✓ Kontrak_unit backup saved: $backup_file_ku ($ku_count rows)\n\n";

echo "=== BACKUP SUMMARY ===\n";
echo "Kontrak rows backed up: $kontrak_count\n";
echo "Kontrak_unit rows backed up: $ku_count\n";
echo "\nBackup files:\n";
echo "  - $backup_file\n";
echo "  - $backup_file_ku\n";
echo "\n✓ Backup complete. Safe to proceed with RESET.\n";

$db->close();
