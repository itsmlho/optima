<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Repair DI Links Command
 *
 * Fills in missing contract_id and pelanggan_id on delivery_instructions rows
 * that were created before these columns existed.
 *
 * Usage:
 *   php spark di:repair-links [--dry-run]
 *
 * Options:
 *   --dry-run   Show what would be changed without writing to DB
 */
class RepairDILinks extends BaseCommand
{
    protected $group       = 'Marketing';
    protected $name        = 'di:repair-links';
    protected $description = 'Repairs missing contract_id / pelanggan_id on legacy DI rows.';

    protected $options = [
        '--dry-run' => 'Show changes without saving to database.',
    ];

    public function run(array $params)
    {
        $isDryRun = array_key_exists('dry-run', $params) || CLI::getOption('dry-run');

        $db = \Config\Database::connect();

        CLI::write('=== DI Link Repair ===', 'yellow');
        if ($isDryRun) {
            CLI::write('DRY-RUN mode — no changes will be saved.', 'cyan');
        }

        // Get all DIs that are missing contract_id or pelanggan_id
        $dis = $db->table('delivery_instructions di')
            ->select('di.id, di.nomor_di, di.spk_id, di.po_kontrak_nomor, di.pelanggan, di.contract_id, di.pelanggan_id')
            ->where('(di.contract_id IS NULL OR di.pelanggan_id IS NULL)', null, false)
            ->get()
            ->getResultArray();

        if (empty($dis)) {
            CLI::write('No DI rows need repair. All records have contract_id and pelanggan_id.', 'green');
            return;
        }

        CLI::write(count($dis) . ' DI row(s) need repair.', 'yellow');
        CLI::newLine();

        $repaired    = 0;
        $ambiguous   = 0;
        $noSpk       = 0;

        foreach ($dis as $di) {
            $nomor   = $di['nomor_di'];
            $spkId   = $di['spk_id'];

            if (empty($spkId)) {
                CLI::write("  SKIP  [{$nomor}] — no SPK linked.", 'light_gray');
                $noSpk++;
                continue;
            }

            // Get SPK
            $spk = $db->table('spk')->where('id', $spkId)->get()->getRowArray();
            if (!$spk) {
                CLI::write("  SKIP  [{$nomor}] — SPK #{$spkId} not found.", 'light_gray');
                $ambiguous++;
                continue;
            }

            $resolvedContractId  = $di['contract_id'];
            $resolvedCustomerId  = $di['pelanggan_id'];

            // Try to resolve contract via spk.kontrak_id
            if (empty($resolvedContractId) && !empty($spk['kontrak_id'])) {
                $resolvedContractId = (int)$spk['kontrak_id'];
            }

            // Try to resolve customer via kontrak.customer_id
            if (!empty($resolvedContractId) && empty($resolvedCustomerId)) {
                $kontrak = $db->table('kontrak')->where('id', $resolvedContractId)->get()->getRowArray();
                if ($kontrak && !empty($kontrak['customer_id'])) {
                    $resolvedCustomerId = (int)$kontrak['customer_id'];
                }
            }

            // Try to resolve customer via quotation → quotation_specification → customer
            if (empty($resolvedCustomerId) && !empty($spk['quotation_specification_id'])) {
                $row = $db->table('quotation_specifications qs')
                    ->select('q.created_customer_id')
                    ->join('quotations q', 'q.id_quotation = qs.id_quotation', 'left')
                    ->where('qs.id_specification', $spk['quotation_specification_id'])
                    ->get()->getRowArray();
                if ($row && !empty($row['created_customer_id'])) {
                    $resolvedCustomerId = (int)$row['created_customer_id'];
                }
            }

            // Try to match contract by PO number if still not resolved
            if (empty($resolvedContractId) && !empty($di['po_kontrak_nomor'])) {
                $matchByPO = $db->table('kontrak')
                    ->where('no_kontrak', $di['po_kontrak_nomor'])
                    ->orWhere('customer_po_number', $di['po_kontrak_nomor'])
                    ->get()->getRowArray();
                if ($matchByPO) {
                    $resolvedContractId = (int)$matchByPO['id'];
                    if (empty($resolvedCustomerId) && !empty($matchByPO['customer_id'])) {
                        $resolvedCustomerId = (int)$matchByPO['customer_id'];
                    }
                }
            }

            if (empty($resolvedContractId) && empty($resolvedCustomerId)) {
                CLI::write("  AMBIGUOUS [{$nomor}] — cannot resolve contract or customer from SPK #{$spkId}.", 'red');
                $ambiguous++;
                continue;
            }

            $changes = [];
            if (empty($di['contract_id']) && !empty($resolvedContractId)) {
                $changes['contract_id'] = $resolvedContractId;
            }
            if (empty($di['pelanggan_id']) && !empty($resolvedCustomerId)) {
                $changes['pelanggan_id'] = $resolvedCustomerId;
            }

            if (empty($changes)) {
                CLI::write("  OK    [{$nomor}] — already has required data.", 'green');
                continue;
            }

            $changeDesc = implode(', ', array_map(
                fn($k, $v) => "{$k}={$v}",
                array_keys($changes), array_values($changes)
            ));

            if ($isDryRun) {
                CLI::write("  DRY   [{$nomor}] — would set: {$changeDesc}", 'cyan');
            } else {
                $changes['diperbarui_pada'] = date('Y-m-d H:i:s');
                $db->table('delivery_instructions')->where('id', $di['id'])->update($changes);
                CLI::write("  FIXED [{$nomor}] — set: {$changeDesc}", 'green');
                $repaired++;
            }
        }

        CLI::newLine();
        CLI::write('=== Summary ===', 'yellow');
        CLI::write("  Repaired : {$repaired}", 'green');
        CLI::write("  Ambiguous: {$ambiguous} (manual check required)", 'red');
        CLI::write("  No SPK   : {$noSpk}", 'light_gray');

        if ($isDryRun) {
            CLI::write('Run without --dry-run to apply changes.', 'cyan');
        } else if ($repaired > 0) {
            CLI::write('Done. Repaired DIs are now ready for Finance linking.', 'green');
        }
    }
}
