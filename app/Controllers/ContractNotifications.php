<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KontrakModel;
use App\Models\NotificationModel;
use App\Models\UserModel;

/**
 * Contract Notifications Controller
 * Handles automated notifications for contract events (expiry, renewal, etc.)
 */
class ContractNotifications extends BaseController
{
    protected $kontrakModel;
    protected $notificationModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->kontrakModel = new KontrakModel();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Check and send notifications for contracts expiring soon
     * This can be called via cron job or manually
     * 
     * @param int $days Number of days before expiry to notify (default: 30)
     * @return mixed JSON response or view
     */
    public function checkExpiringContracts($days = 30)
    {
        try {
            log_message('info', '[ContractNotifications] Checking for contracts expiring in ' . $days . ' days');

            // Get contracts expiring within specified days
            $expiringContracts = $this->getExpiringContracts($days);
            
            if (empty($expiringContracts)) {
                log_message('info', '[ContractNotifications] No expiring contracts found');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'No expiring contracts found',
                    'contracts_checked' => 0,
                    'notifications_sent' => 0
                ]);
            }

            // Send notifications
            $notificationsSent = 0;
            $errors = [];

            foreach ($expiringContracts as $contract) {
                try {
                    $sent = $this->sendExpiryNotification($contract, $days);
                    if ($sent) {
                        $notificationsSent++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'contract_id' => $contract['id'],
                        'contract_number' => $contract['no_kontrak'],
                        'error' => $e->getMessage()
                    ];
                    log_message('error', '[ContractNotifications] Error sending notification for contract ' . $contract['no_kontrak'] . ': ' . $e->getMessage());
                }
            }

            log_message('info', '[ContractNotifications] Check complete: ' . $notificationsSent . ' notifications sent for ' . count($expiringContracts) . ' contracts');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contract expiry check completed',
                'contracts_checked' => count($expiringContracts),
                'notifications_sent' => $notificationsSent,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            log_message('error', '[ContractNotifications] Error in checkExpiringContracts: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error checking expiring contracts: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get contracts expiring within specified days
     * 
     * @param int $days Number of days before expiry
     * @return array List of expiring contracts
     */
    protected function getExpiringContracts($days)
    {
        $query = $this->db->query("
            SELECT 
                k.id,
                k.no_kontrak,
                k.customer_po_number,
                k.rental_type,
                k.tanggal_mulai,
                k.tanggal_berakhir,
                DATEDIFF(k.tanggal_berakhir, CURDATE()) as days_left,
                k.nilai_total,
                k.total_units,
                k.jenis_sewa,
                cl.nama_lokasi as customer_location,
                c.nama_customer as customer_name,
                u.id as user_id,
                u.email as user_email,
                u.first_name,
                u.last_name
            FROM kontrak k
            LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
            LEFT JOIN customers c ON c.id = cl.customer_id
            LEFT JOIN users u ON u.id = k.dibuat_oleh
            WHERE k.status = 'ACTIVE'
            AND k.tanggal_berakhir BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
            AND k.tanggal_berakhir >= CURDATE()
            ORDER BY k.tanggal_berakhir ASC
        ", [$days]);

        return $query->getResultArray();
    }

    /**
     * Send expiry notification for a contract
     * 
     * @param array $contract Contract data
     * @param int $days Days before expiry
     * @return bool Success status
     */
    protected function sendExpiryNotification($contract, $days)
    {
        // Determine notification urgency based on days left
        $daysLeft = $contract['days_left'];
        
        if ($daysLeft <= 7) {
            $type = 'error';
            $icon = 'exclamation-triangle';
            $urgency = 'URGENT';
        } elseif ($daysLeft <= 14) {
            $type = 'warning';
            $icon = 'exclamation-circle';
            $urgency = 'IMPORTANT';
        } else {
            $type = 'info';
            $icon = 'info-circle';
            $urgency = 'NOTICE';
        }

        // Format contract details
        $rentalTypeLabel = match($contract['rental_type']) {
            'CONTRACT' => 'Contract',
            'PO_ONLY' => 'PO Only',
            'DAILY_SPOT' => 'Daily/Spot',
            default => 'Unknown'
        };

        // Create notification title
        $title = sprintf(
            "%s: Contract %s expiring in %d days",
            $urgency,
            $contract['no_kontrak'],
            $daysLeft
        );

        // Create notification message
        $message = sprintf(
            "Contract %s (%s) will expire on %s (%d days left).\n\nCustomer: %s\nLocation: %s\nRental Type: %s\nTotal Value: Rp %s\n\nAction required: Contact customer for renewal or prepare for contract closure.",
            $contract['no_kontrak'],
            $contract['customer_po_number'] ? 'PO: ' . $contract['customer_po_number'] : 'No PO',
            date('d M Y', strtotime($contract['tanggal_berakhir'])),
            $daysLeft,
            $contract['customer_name'] ?? 'Unknown',
            $contract['customer_location'] ?? 'Unknown',
            $rentalTypeLabel,
            number_format($contract['nilai_total'] ?? 0, 0, ',', '.')
        );

        // Determine target users (creator + marketing team)
        $targetUsers = [];
        
        // Add contract creator
        if (!empty($contract['user_id'])) {
            $targetUsers[] = $contract['user_id'];
        }

        // Add marketing team members (division_id = marketing/sales)
        $marketingUsers = $this->userModel
            ->select('id')
            ->where('is_active', 1)
            ->groupStart()
                ->where('division_id', 3) // Assuming 3 is marketing
                ->orLike('roles', 'marketing')
            ->groupEnd()
            ->findAll();

        foreach ($marketingUsers as $user) {
            if (!in_array($user['id'], $targetUsers)) {
                $targetUsers[] = $user['id'];
            }
        }

        // Send notification to all target users
        if (empty($targetUsers)) {
            log_message('warning', '[ContractNotifications] No target users found for contract ' . $contract['no_kontrak']);
            return false;
        }

        $options = [
            'type' => $type,
            'icon' => $icon,
            'module' => 'marketing',
            'id' => $contract['id'],
            'url' => 'marketing/kontrak/view/' . $contract['id'],
            'expires_at' => date('Y-m-d H:i:s', strtotime($contract['tanggal_berakhir']))
        ];

        $result = $this->notificationModel->sendToMultiple($targetUsers, $title, $message, $options);

        log_message('info', sprintf(
            '[ContractNotifications] Sent notification for contract %s to %d users (Days left: %d, Type: %s)',
            $contract['no_kontrak'],
            count($targetUsers),
            $daysLeft,
            $type
        ));

        return $result;
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats()
    {
        try {
            $stats = [
                'expiring_7_days' => 0,
                'expiring_14_days' => 0,
                'expiring_30_days' => 0,
                'total_active_contracts' => 0
            ];

            // Get total active contracts
            $stats['total_active_contracts'] = $this->kontrakModel
                ->where('status', 'ACTIVE')
                ->countAllResults();

            // Get expiring counts
            $stats['expiring_7_days'] = count($this->getExpiringContracts(7));
            $stats['expiring_14_days'] = count($this->getExpiringContracts(14));
            $stats['expiring_30_days'] = count($this->getExpiringContracts(30));

            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            log_message('error', '[ContractNotifications] Error getting stats: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting notification stats: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Manual trigger for testing (admin only)
     */
    public function testNotifications()
    {
        // Check admin permission
        helper('simple_rbac');
        if (!can_manage('admin')) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        // Run check
        $result = $this->checkExpiringContracts(30);
        
        return view('admin/notification_test_result', [
            'title' => 'Contract Notification Test',
            'result' => json_decode($this->response->getBody(), true)
        ]);
    }
}
