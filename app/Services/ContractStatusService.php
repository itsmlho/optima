<?php

namespace App\Services;

/**
 * Contract Status Service
 * 
 * Handles automatic status updates for contracts and customers:
 * - Contracts: ACTIVE → EXPIRED when past end date without renewal
 * - Customers: is_active → 0 when no active contracts remain
 * 
 * Designed to run daily via scheduler/cron or triggered after key events.
 */
class ContractStatusService
{
    protected $db;
    protected $timelineService;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->timelineService = new ContractTimelineService();
    }

    /**
     * Run full status refresh for all contracts and customers.
     * Call this daily via cron or manually.
     * 
     * @return array Summary of updates made
     */
    public function refreshAllStatuses(): array
    {
        $contractsUpdated = $this->refreshContractStatuses();
        $customersUpdated = $this->refreshCustomerStatuses();

        return [
            'contracts_expired' => $contractsUpdated,
            'customers_deactivated' => $customersUpdated,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Update status of contracts that have passed their end date.
     * ACTIVE contracts with tanggal_berakhir < today become EXPIRED.
     * Does NOT affect contracts with pending/active renewal.
     * 
     * @return int Number of contracts updated
     */
    public function refreshContractStatuses(): int
    {
        $today = date('Y-m-d');
        $updated = 0;

        // Find ACTIVE contracts that have expired (tanggal_berakhir < today).
        // Only auto-expire CONTRACT type; PO_ONLY and DAILY_SPOT do not expire by date.
        $expiredContracts = $this->db->table('kontrak')
            ->select('id, no_kontrak, customer_id, status, tanggal_berakhir')
            ->where('status', 'ACTIVE')
            ->where('tanggal_berakhir <', $today)
            ->groupStart()
                ->where('rental_type', 'CONTRACT')
                ->orWhere('rental_type', null)
                ->orWhere('rental_type', '')
            ->groupEnd()
            ->get()
            ->getResultArray();

        foreach ($expiredContracts as $contract) {
            // Check if there's an active renewal for this contract
            $hasActiveRenewal = $this->hasActiveRenewal($contract['id']);
            
            if (!$hasActiveRenewal) {
                // Update contract status to EXPIRED
                $this->db->table('kontrak')
                    ->where('id', $contract['id'])
                    ->update([
                        'status' => 'EXPIRED',
                        'diperbarui_pada' => date('Y-m-d H:i:s'),
                    ]);

                // Record timeline event
                $this->timelineService->recordStatusChange(
                    (int)$contract['id'],
                    'ACTIVE',
                    'EXPIRED',
                    'Auto-expired: contract end date has passed',
                    null // System action
                );

                $updated++;

                log_message('info', "[ContractStatusService] Contract #{$contract['id']} ({$contract['no_kontrak']}) auto-expired");
            }
        }

        return $updated;
    }

    /**
     * Update customer is_active based on their contract statuses.
     * Customer becomes inactive (is_active=0) if they have no ACTIVE/PENDING contracts.
     * Customer becomes active (is_active=1) if they have at least one ACTIVE/PENDING contract.
     * 
     * @return int Number of customers updated
     */
    public function refreshCustomerStatuses(): int
    {
        $updated = 0;

        // Get all customers
        $customers = $this->db->table('customers')
            ->select('id, customer_name, is_active')
            ->get()
            ->getResultArray();

        foreach ($customers as $customer) {
            // Count active contracts for this customer
            $activeContractCount = $this->db->table('kontrak')
                ->where('customer_id', $customer['id'])
                ->whereIn('status', ['ACTIVE', 'PENDING'])
                ->countAllResults();

            $shouldBeActive = ($activeContractCount > 0) ? 1 : 0;
            $currentlyActive = (int)$customer['is_active'];

            // Only update if status needs to change
            if ($shouldBeActive !== $currentlyActive) {
                $this->db->table('customers')
                    ->where('id', $customer['id'])
                    ->update([
                        'is_active' => $shouldBeActive,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $statusText = $shouldBeActive ? 'ACTIVE' : 'INACTIVE';
                log_message('info', "[ContractStatusService] Customer #{$customer['id']} ({$customer['customer_name']}) status changed to {$statusText} (active contracts: {$activeContractCount})");

                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Check if a contract has an active renewal (new contract that replaces it).
     * 
     * @param int $contractId Original contract ID
     * @return bool True if there's an active renewal
     */
    protected function hasActiveRenewal(int $contractId): bool
    {
        // Method 1: Check contract_renewal_workflow table
        $renewal = $this->db->table('contract_renewal_workflow')
            ->where('original_contract_id', $contractId)
            ->whereIn('status', ['PENDING', 'APPROVED', 'COMPLETED'])
            ->get()
            ->getRow();

        if ($renewal) {
            return true;
        }

        // Method 2: Check if there's a newer contract with parent_contract_id = this contract
        $childContract = $this->db->table('kontrak')
            ->where('parent_contract_id', $contractId)
            ->whereIn('status', ['ACTIVE', 'PENDING'])
            ->get()
            ->getRow();

        return $childContract !== null;
    }

    /**
     * Trigger status update for a specific contract (e.g., after workflow completion).
     * Also refreshes the customer status.
     * 
     * @param int $contractId Contract ID to check
     * @return array Result of the update
     */
    public function triggerStatusUpdate(int $contractId): array
    {
        $contract = $this->db->table('kontrak')
            ->select('id, no_kontrak, customer_id, status, tanggal_berakhir, rental_type')
            ->where('id', $contractId)
            ->get()
            ->getRowArray();

        if (!$contract) {
            return ['success' => false, 'message' => 'Contract not found'];
        }

        $contractUpdated = false;
        $customerUpdated = false;

        // Check if contract should be expired (only CONTRACT type; PO_ONLY/DAILY_SPOT do not auto-expire)
        $today = date('Y-m-d');
        $isExpirableType = in_array($contract['rental_type'] ?? '', ['CONTRACT', ''], true) || ($contract['rental_type'] ?? null) === null;
        if ($contract['status'] === 'ACTIVE' && $contract['tanggal_berakhir'] < $today && $isExpirableType) {
            if (!$this->hasActiveRenewal($contractId)) {
                $this->db->table('kontrak')
                    ->where('id', $contractId)
                    ->update([
                        'status' => 'EXPIRED',
                        'diperbarui_pada' => date('Y-m-d H:i:s'),
                    ]);

                $this->timelineService->recordStatusChange(
                    $contractId,
                    'ACTIVE',
                    'EXPIRED',
                    'Auto-expired: triggered after workflow completion',
                    null
                );

                $contractUpdated = true;
            }
        }

        // Refresh customer status
        if ($contract['customer_id']) {
            $activeContractCount = $this->db->table('kontrak')
                ->where('customer_id', $contract['customer_id'])
                ->whereIn('status', ['ACTIVE', 'PENDING'])
                ->countAllResults();

            $customer = $this->db->table('customers')
                ->where('id', $contract['customer_id'])
                ->get()
                ->getRowArray();

            if ($customer) {
                $shouldBeActive = ($activeContractCount > 0) ? 1 : 0;
                if ((int)$customer['is_active'] !== $shouldBeActive) {
                    $this->db->table('customers')
                        ->where('id', $contract['customer_id'])
                        ->update([
                            'is_active' => $shouldBeActive,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    $customerUpdated = true;
                }
            }
        }

        return [
            'success' => true,
            'contract_updated' => $contractUpdated,
            'customer_updated' => $customerUpdated,
        ];
    }

    /**
     * Get summary of contract statuses.
     * Useful for dashboard or monitoring.
     * 
     * @return array Status counts
     */
    public function getStatusSummary(): array
    {
        $today = date('Y-m-d');

        return [
            'active' => $this->db->table('kontrak')->where('status', 'ACTIVE')->countAllResults(),
            'expired' => $this->db->table('kontrak')->where('status', 'EXPIRED')->countAllResults(),
            'pending' => $this->db->table('kontrak')->where('status', 'PENDING')->countAllResults(),
            'cancelled' => $this->db->table('kontrak')->where('status', 'CANCELLED')->countAllResults(),
            'active_past_end_date' => $this->db->table('kontrak')
                ->where('status', 'ACTIVE')
                ->where('tanggal_berakhir <', $today)
                ->countAllResults(),
            'customers_active' => $this->db->table('customers')->where('is_active', 1)->countAllResults(),
            'customers_inactive' => $this->db->table('customers')->where('is_active', 0)->countAllResults(),
        ];
    }
}
