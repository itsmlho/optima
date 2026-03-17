<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Refresh Invoice Status Command
 *
 * Updates invoice status from APPROVED/SENT to OVERDUE when due_date < today.
 * Sends notify_invoice_overdue for each invoice newly marked overdue.
 *
 * Usage: php spark finance:refresh-invoice-status
 *
 * Schedule daily via cron (e.g. after midnight):
 *   0 1 * * * cd /path/to/optima && php spark finance:refresh-invoice-status
 */
class RefreshInvoiceStatus extends BaseCommand
{
    protected $group       = 'Finance';
    protected $name        = 'finance:refresh-invoice-status';
    protected $description = 'Update APPROVED/SENT invoices to OVERDUE when past due_date; send notifications';
    protected $usage       = 'finance:refresh-invoice-status [options]';
    protected $arguments   = [];
    protected $options     = [
        '--dry-run' => 'Preview updates without applying them or sending notifications',
    ];

    public function run(array $params)
    {
        $dryRun = array_key_exists('dry-run', $params) || in_array('--dry-run', $params);

        CLI::write('=================================', 'cyan');
        CLI::write('Refresh Invoice Status (OVERDUE)', 'cyan');
        CLI::write('=================================', 'cyan');
        CLI::newLine();
        CLI::write('Mode: ' . ($dryRun ? 'DRY RUN (no changes)' : 'LIVE'), $dryRun ? 'yellow' : 'green');
        CLI::newLine();

        helper('notification');
        helper('url');

        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Find invoices: status APPROVED or SENT, due_date < today
        $overdueCandidates = $db->table('invoices i')
            ->select('i.id, i.invoice_number, i.due_date, i.total_amount, i.status, c.customer_name')
            ->join('customers c', 'c.id = i.customer_id', 'left')
            ->whereIn('i.status', ['APPROVED', 'SENT'])
            ->where('i.due_date <', $today)
            ->get()
            ->getResultArray();

        if (empty($overdueCandidates)) {
            CLI::write('No invoices to update.', 'green');
            return EXIT_SUCCESS;
        }

        CLI::write('Found ' . count($overdueCandidates) . ' invoice(s) to mark OVERDUE:', 'yellow');
        foreach ($overdueCandidates as $inv) {
            $daysOverdue = (int) floor((strtotime($today) - strtotime($inv['due_date'])) / 86400);
            CLI::write("  - {$inv['invoice_number']} (due {$inv['due_date']}, {$daysOverdue} days overdue)", 'white');
        }
        CLI::newLine();

        if ($dryRun) {
            CLI::write('Dry run: no updates or notifications sent.', 'yellow');
            return EXIT_SUCCESS;
        }

        $updated = 0;
        $notified = 0;

        foreach ($overdueCandidates as $inv) {
            $db->table('invoices')
                ->where('id', $inv['id'])
                ->update([
                    'status'      => 'OVERDUE',
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            $updated++;

            $daysOverdue = (int) floor((strtotime($today) - strtotime($inv['due_date'])) / 86400);
            $result = notify_invoice_overdue([
                'id'             => $inv['id'],
                'invoice_number' => $inv['invoice_number'],
                'nomor_invoice'  => $inv['invoice_number'],
                'customer_name'  => $inv['customer_name'] ?? '',
                'total_amount'   => $inv['total_amount'],
                'amount'         => $inv['total_amount'],
                'due_date'       => $inv['due_date'],
                'tanggal_jatuh_tempo' => $inv['due_date'],
                'days_overdue'   => $daysOverdue,
                'url'            => base_url('finance/invoices/' . $inv['id']),
            ]);
            if ($result && (!is_array($result) || ($result['success'] ?? false))) {
                $notified++;
            }
        }

        CLI::write("Updated: {$updated} invoice(s)", 'green');
        CLI::write("Notifications sent: {$notified}", 'green');
        CLI::newLine();
        CLI::write('Done.', 'cyan');
        return EXIT_SUCCESS;
    }
}
