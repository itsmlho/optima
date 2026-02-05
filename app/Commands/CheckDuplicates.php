<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckDuplicates extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'check:duplicates';
    protected $description = 'Check for duplicate attachments on Unit No: 1';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== Checking Unit No: 1 Attachments ===', 'yellow');
        CLI::newLine();

        $results = $db->table('inventory_attachment')
            ->select('id_inventory_attachment, tipe_item, nama_item, serial_number, attachment_status, id_inventory_unit, attachment_id, charger_id, baterai_id')
            ->where('id_inventory_unit', 1)
            ->orderBy('tipe_item', 'ASC')
            ->orderBy('nama_item', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($results)) {
            CLI::write('No attachments found for Unit No: 1', 'red');
            return;
        }

        CLI::write('Found ' . count($results) . ' attachment(s):', 'green');
        CLI::newLine();
        
        foreach ($results as $i => $row) {
            CLI::write(($i + 1) . ". ID: {$row['id_inventory_attachment']}", 'cyan');
            CLI::write("   Type: {$row['tipe_item']}");
            CLI::write("   Name: {$row['nama_item']}");
            CLI::write("   SN: {$row['serial_number']}");
            CLI::write("   Status: {$row['attachment_status']}", $row['attachment_status'] === 'IN_USE' ? 'green' : 'yellow');
            CLI::write("   Unit ID: {$row['id_inventory_unit']}");
            CLI::write("   Attachment ID: " . ($row['attachment_id'] ?: 'NULL'));
            CLI::write("   Charger ID: " . ($row['charger_id'] ?: 'NULL'));
            CLI::write("   Baterai ID: " . ($row['baterai_id'] ?: 'NULL'));
            CLI::newLine();
        }
        
        // Check for duplicates by type
        CLI::write('=== Duplicate Analysis ===', 'yellow');
        CLI::newLine();
        
        $grouped = [];
        foreach ($results as $row) {
            $key = $row['tipe_item'] . '|' . $row['nama_item'] . '|' . $row['serial_number'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = $row['id_inventory_attachment'];
        }
        
        $hasDuplicates = false;
        foreach ($grouped as $key => $ids) {
            if (count($ids) > 1) {
                $hasDuplicates = true;
                list($type, $name, $sn) = explode('|', $key);
                CLI::write("DUPLICATE: {$type} - {$name} [{$sn}]", 'red');
                CLI::write("  Record IDs: " . implode(', ', $ids));
                CLI::write("  Keep: {$ids[0]} (first record)", 'green');
                CLI::write("  Delete: " . implode(', ', array_slice($ids, 1)), 'red');
                CLI::newLine();
            }
        }
        
        if (!$hasDuplicates) {
            CLI::write('No duplicates found!', 'green');
        }
    }
}
