<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\DeliveryInstructionModel;
use App\Models\RecurringBillingScheduleModel;
use App\Models\NotificationModel;

/**
 * Generate Workflow Notifications Command
 * 
 * Creates notifications for:
 * - Unlinked DIs (AWAITING_CONTRACT status)
 * - Upcoming recurring invoices
 * - Overdue contract renewals
 * 
 * Run via cron: php spark workflow:notify
 */
class GenerateWorkflowNotifications extends BaseCommand
{
    protected $group = 'Workflow';
    protected $name = 'workflow:notify';
    protected $description = 'Generate notifications for workflow events (unlinked DIs, upcoming invoices)';
    protected $usage = 'workflow:notify [options]';
    protected $arguments = [];
    protected $options = [
        '--dry-run' => 'Preview notifications without sending them',
        '--type' => 'Notification type: unlinked-di, upcoming-invoices, all (default: all)'
    ];

    public function run(array $params)
    {
        $dryRun = array_key_exists('dry-run', $params) || in_array('--dry-run', $params);
        $type = $params['type'] ?? 'all';

        CLI::write('=== Workflow Notification Generator ===', 'yellow');
        CLI::write('Mode: ' . ($dryRun ? 'DRY RUN (no notifications sent)' : 'LIVE'), 'cyan');
        CLI::write('Type: ' . $type, 'cyan');
        CLI::newLine();

        $totalSent = 0;

        try {
            // 1. Unlinked DI Notifications
            if ($type === 'all' || $type === 'unlinked-di') {
                $count = $this->generateUnlinkedDINotifications($dryRun);
                $totalSent += $count;
                CLI::write("✓ Unlinked DI notifications: {$count}", 'green');
            }

            // 2. Upcoming Invoice Notifications
            if ($type === 'all' || $type === 'upcoming-invoices') {
                $count = $this->generateUpcomingInvoiceNotifications($dryRun);
                $totalSent += $count;
                CLI::write("✓ Upcoming invoice notifications: {$count}", 'green');
            }

            CLI::newLine();
            CLI::write("Total notifications: {$totalSent}", 'yellow');
            CLI::write('Done!', 'green');

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }

    /**
     * Generate notifications for unlinked DIs (AWAITING_CONTRACT status)
     */
    protected function generateUnlinkedDINotifications($dryRun = false)
    {
        $diModel = new DeliveryInstructionModel();
        $notificationModel = new NotificationModel();
        $db = \Config\Database::connect();

        // Get unlinked DIs with days pending > 3
        $unlinkedDIs = $diModel->getUnlinkedDeliveries();
        
        if (empty($unlinkedDIs)) {
            CLI::write('  No unlinked DIs found', 'white');
            return 0;
        }

        CLI::write("  Found {count} unlinked DI(s)", ['count' => count($unlinkedDIs)], 'white');

        // Get Finance team user IDs
        $financeUsers = $this->getFinanceTeamUsers();
        
        if (empty($financeUsers)) {
            CLI::write('  Warning: No Finance team users found', 'yellow');
            return 0;
        }

        $notificationsSent = 0;

        foreach ($unlinkedDIs as $di) {
            $daysPending = $di['days_pending'];
            
            // Skip if less than 3 days (grace period)
            if ($daysPending < 3) {
                continue;
            }

            // Determine urgency level
            $urgency = 'warning';
            if ($daysPending > 14) {
                $urgency = 'error'; // Critical - over 2 weeks
            } elseif ($daysPending > 7) {
                $urgency = 'warning'; // High priority - over 1 week
            }

            $title = "⚠️ DI Menunggu Kontrak: {$di['nomor_di']}";
            $message = "DI {$di['nomor_di']} (SPK: {$di['nomor_spk']}) menunggu linking kontrak selama {$daysPending} hari. "
                     . "Customer: {$di['pelanggan']}. Mohon segera link ke kontrak untuk enable invoicing.";

            $options = [
                'type' => $urgency,
                'icon' => 'file-text',
                'module' => 'delivery_instructions',
                'id' => $di['id'],
                'url' => "/marketing/di/detail/{$di['id']}"
            ];

            if (!$dryRun) {
                // Check if notification already sent today
                $existingToday = $db->table('notifications')
                    ->where('related_module', 'delivery_instructions')
                    ->where('related_id', $di['id'])
                    ->where('DATE(created_at)', date('Y-m-d'))
                    ->countAllResults();

                if ($existingToday == 0) {
                    $notificationModel->sendToMultiple($financeUsers, $title, $message, $options);
                    $notificationsSent++;
                } else {
                    CLI::write("  Skipped DI {$di['nomor_di']} (already notified today)", 'white');
                }
            } else {
                CLI::write("  [DRY RUN] Would send to " . count($financeUsers) . " users: {$title}", 'white');
                $notificationsSent++;
            }
        }

        return $notificationsSent;
    }

    /**
     * Generate notifications for upcoming recurring invoices (next 7 days)
     */
    protected function generateUpcomingInvoiceNotifications($dryRun = false)
    {
        $scheduleModel = new RecurringBillingScheduleModel();
        $notificationModel = new NotificationModel();
        $db = \Config\Database::connect();

        // Get upcoming invoices in next 7 days
        $upcomingInvoices = $scheduleModel->getUpcomingInvoices(7);
        
        if (empty($upcomingInvoices)) {
            CLI::write('  No upcoming invoices in next 7 days', 'white');
            return 0;
        }

        CLI::write("  Found {count} upcoming invoice(s)", ['count' => count($upcomingInvoices)], 'white');

        // Get Finance team user IDs
        $financeUsers = $this->getFinanceTeamUsers();
        
        if (empty($financeUsers)) {
            CLI::write('  Warning: No Finance team users found', 'yellow');
            return 0;
        }

        $notificationsSent = 0;

        foreach ($upcomingInvoices as $invoice) {
            $daysUntilDue = $invoice['days_until_due'];
            
            // Send notification 3 days before and 1 day before
            if ($daysUntilDue == 3 || $daysUntilDue == 1) {
                $title = "📅 Invoice Akan Jatuh Tempo: Kontrak {$invoice['no_kontrak']}";
                $message = "Recurring invoice untuk kontrak {$invoice['no_kontrak']} ({$invoice['nama_customer']}) "
                         . "akan jatuh tempo dalam {$daysUntilDue} hari pada {$invoice['next_billing_date']}. "
                         . "Frequency: {$invoice['frequency']}.";

                $options = [
                    'type' => $daysUntilDue == 1 ? 'warning' : 'info',
                    'icon' => 'calendar',
                    'module' => 'recurring_billing_schedules',
                    'id' => $invoice['id'],
                    'url' => "/finance/invoices"
                ];

                if (!$dryRun) {
                    // Check if notification already sent today
                    $existingToday = $db->table('notifications')
                        ->where('related_module', 'recurring_billing_schedules')
                        ->where('related_id', $invoice['id'])
                        ->where('DATE(created_at)', date('Y-m-d'))
                        ->countAllResults();

                    if ($existingToday == 0) {
                        $notificationModel->sendToMultiple($financeUsers, $title, $message, $options);
                        $notificationsSent++;
                    } else {
                        CLI::write("  Skipped invoice {$invoice['no_kontrak']} (already notified today)", 'white');
                    }
                } else {
                    CLI::write("  [DRY RUN] Would send to " . count($financeUsers) . " users: {$title}", 'white');
                    $notificationsSent++;
                }
            }
        }

        return $notificationsSent;
    }

    /**
     * Get Finance team user IDs
     */
    protected function getFinanceTeamUsers()
    {
        $db = \Config\Database::connect();

        // Get users from Finance division (division_id for Finance/Accounting)
        $query = $db->table('users u')
            ->select('u.id')
            ->join('divisions d', 'd.id = u.division_id', 'left')
            ->where('u.is_active', 1)
            ->groupStart()
                ->like('d.name', 'Finance', 'both')
                ->orLike('d.name', 'Accounting', 'both')
                ->orLike('d.name', 'Keuangan', 'both')
            ->groupEnd()
            ->get();

        $users = $query->getResultArray();
        
        return array_column($users, 'id');
    }
}
