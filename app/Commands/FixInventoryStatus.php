<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\InventoryStatusModel;

class FixInventoryStatus extends BaseCommand
{
    protected $group        = 'maintenance';
    protected $name         = 'fix:inventory-status';
    protected $description  = 'Fix inventory status for existing active contracts';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $inventoryStatusModel = new InventoryStatusModel();

        CLI::write('=== FIXING INVENTORY STATUS FOR ACTIVE CONTRACTS ===', 'yellow');

        // Get all active contracts
        $activeContracts = $db->query("
            SELECT k.id, k.no_kontrak, k.status 
            FROM kontrak k 
            WHERE k.status = 'Aktif'
            ORDER BY k.id
        ")->getResultArray();

        CLI::write("Found " . count($activeContracts) . " active contracts", 'green');

        $fixedCount = 0;
        $errorCount = 0;

        foreach ($activeContracts as $contract) {
            $contractId = $contract['id'];
            $contractNumber = $contract['no_kontrak'];

            CLI::write("Processing contract {$contractId}: {$contractNumber}");

            // Check if this contract has units with wrong status
            $wrongStatusUnits = $db->query("
                SELECT COUNT(*) as count
                FROM inventory_unit iu
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                WHERE ks.kontrak_id = ? AND iu.status_unit_id != 3
            ", [$contractId])->getRowArray();

            if ($wrongStatusUnits['count'] > 0) {
                CLI::write("  - Found {$wrongStatusUnits['count']} units with wrong status", 'yellow');
                
                // Fix the status
                $result = $inventoryStatusModel->updateStatusForActiveContract($contractId);
                
                if ($result) {
                    CLI::write("  - ✅ Fixed inventory status for contract {$contractNumber}", 'green');
                    $fixedCount++;
                } else {
                    CLI::write("  - ❌ Failed to fix inventory status for contract {$contractNumber}", 'red');
                    $errorCount++;
                }
            } else {
                CLI::write("  - ✅ Status already correct", 'green');
            }
        }

        CLI::write('=== SUMMARY ===', 'yellow');
        CLI::write("Total contracts processed: " . count($activeContracts), 'white');
        CLI::write("Contracts fixed: {$fixedCount}", 'green');
        CLI::write("Errors: {$errorCount}", $errorCount > 0 ? 'red' : 'green');
        
        if ($fixedCount > 0) {
            CLI::write("✅ All active contracts now have correct inventory status!", 'green');
        }
    }
}
