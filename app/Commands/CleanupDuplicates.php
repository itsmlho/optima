<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupDuplicates extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:cleanup-duplicates';
    protected $description = 'Cleanup duplicate attachment records in inventory_attachment table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== Cleanup Duplicate Attachments ===', 'yellow');
        CLI::newLine();
        
        // 1. Find duplicates
        CLI::write('Step 1: Finding duplicates...', 'blue');
        
        $duplicates = $db->query("
            SELECT 
                id_inventory_unit,
                tipe_item,
                COALESCE(attachment_id, 0) as attachment_id,
                COALESCE(charger_id, 0) as charger_id,
                COALESCE(baterai_id, 0) as baterai_id,
                COUNT(*) as count,
                GROUP_CONCAT(id_inventory_attachment ORDER BY id_inventory_attachment) as all_ids
            FROM inventory_attachment
            WHERE id_inventory_unit IS NOT NULL
            GROUP BY id_inventory_unit, tipe_item, attachment_id, charger_id, baterai_id
            HAVING COUNT(*) > 1
        ")->getResultArray();
        
        if (empty($duplicates)) {
            CLI::write('✓ No duplicates found!', 'green');
            return;
        }
        
        CLI::write('Found ' . count($duplicates) . ' duplicate groups:', 'red');
        CLI::newLine();
        
        $tableData = [['Unit ID', 'Type', 'Att ID', 'Chr ID', 'Bat ID', 'Count', 'IDs']];
        foreach ($duplicates as $dup) {
            $tableData[] = [
                $dup['id_inventory_unit'],
                $dup['tipe_item'],
                $dup['attachment_id'],
                $dup['charger_id'],
                $dup['baterai_id'],
                $dup['count'],
                $dup['all_ids'],
            ];
        }
        CLI::table($tableData);
        CLI::newLine();
        
        // 2. Ask confirmation
        $proceed = CLI::prompt('Proceed with cleanup? This will keep the FIRST record and delete duplicates', ['y', 'n']);
        
        if ($proceed !== 'y') {
            CLI::write('Cleanup cancelled.', 'yellow');
            return;
        }
        
        // 3. Cleanup duplicates
        CLI::write('Step 2: Cleaning up duplicates...', 'blue');
        
        $deletedCount = 0;
        
        foreach ($duplicates as $dup) {
            $ids = explode(',', $dup['all_ids']);
            $keepId = array_shift($ids); // Keep first ID
            $deleteIds = $ids; // Delete rest
            
            if (!empty($deleteIds)) {
                // DEPRECATED: Table structure has changed
                CLI::write("  ✗ Cannot delete - table structure changed", 'red');
                /*
                $db->table('inventory_attachment')
                    ->whereIn('id_inventory_attachment', $deleteIds)
                    ->delete();
                
                $deletedCount += count($deleteIds);
                
                CLI::write("  ✓ Kept ID {$keepId}, deleted: " . implode(', ', $deleteIds), 'green');
                */
            }
        }
        
        CLI::newLine();
        CLI::write("✓ Cleanup completed! Deleted {$deletedCount} duplicate records.", 'green');
        
        // 4. Verify cleanup
        CLI::newLine();
        CLI::write('Step 3: Verifying cleanup...', 'blue');
        
        $remaining = $db->query("
            SELECT 
                id_inventory_unit,
                tipe_item,
                COUNT(*) as count
            FROM inventory_attachment
            WHERE id_inventory_unit IS NOT NULL
            GROUP BY id_inventory_unit, tipe_item, 
                COALESCE(attachment_id, 0), 
                COALESCE(charger_id, 0), 
                COALESCE(baterai_id, 0)
            HAVING COUNT(*) > 1
        ")->getResultArray();
        
        if (empty($remaining)) {
            CLI::write('✓ All duplicates cleaned successfully!', 'green');
        } else {
            CLI::write('⚠ Warning: ' . count($remaining) . ' duplicate groups still exist', 'red');
        }
        
        CLI::newLine();
        CLI::write('=== Cleanup Complete ===', 'yellow');
    }
}
