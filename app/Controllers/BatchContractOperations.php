<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KontrakModel;
use App\Models\InventoryUnitModel;
use App\Traits\ActivityLoggingTrait;

/**
 * Batch Contract Operations Controller
 * Handles bulk operations on contracts (status updates, renewals, etc.)
 */
class BatchContractOperations extends BaseController
{
    use ActivityLoggingTrait;
    
    protected $kontrakModel;
    protected $inventoryUnitModel;
    protected $db;

    public function __construct()
    {
        $this->kontrakModel = new KontrakModel();
        $this->inventoryUnitModel = new InventoryUnitModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Update expired contracts to EXPIRED status
     * Also updates associated inventory units to "UNIT PULANG" status
     * 
     * @return mixed JSON response
     */
    public function updateExpiredContracts()
    {
        try {
            log_message('info', '[BatchContractOperations] Starting expired contracts batch update');

            // Find all ACTIVE contracts that have passed their end date
            $expiredContracts = $this->db->query("
                SELECT 
                    k.id,
                    k.no_kontrak,
                    k.customer_po_number,
                    k.tanggal_berakhir,
                    k.total_units,
                    DATEDIFF(CURDATE(), k.tanggal_berakhir) as days_overdue,
                    cl.nama_lokasi as customer_location,
                    c.nama_customer as customer_name
                FROM kontrak k
                LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
                LEFT JOIN customers c ON c.id = cl.customer_id
                WHERE k.status = 'ACTIVE'
                AND k.tanggal_berakhir < CURDATE()
                ORDER BY k.tanggal_berakhir ASC
            ")->getResultArray();

            if (empty($expiredContracts)) {
                log_message('info', '[BatchContractOperations] No expired contracts found');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'No expired contracts found',
                    'contracts_updated' => 0,
                    'units_updated' => 0
                ]);
            }

            $contractsUpdated = 0;
            $unitsUpdated = 0;
            $errors = [];

            foreach ($expiredContracts as $contract) {
                try {
                    // Update contract status to EXPIRED
                    $updated = $this->kontrakModel->update($contract['id'], [
                        'status' => 'EXPIRED'
                    ]);

                    if ($updated) {
                        $contractsUpdated++;

                        // Update associated units to "UNIT PULANG" status (ID: 11)
                        $unitsAffected = $this->updateInventoryUnitsForExpiredContract($contract['id']);
                        $unitsUpdated += $unitsAffected;

                        // Log activity
                        $this->logActivity(
                            'CONTRACT_AUTO_EXPIRED',
                            'marketing',
                            $contract['id'],
                            sprintf(
                                'Contract %s automatically expired (%d days overdue, %d units returned)',
                                $contract['no_kontrak'],
                                $contract['days_overdue'],
                                $unitsAffected
                            )
                        );

                        log_message('info', sprintf(
                            '[BatchContractOperations] Updated contract %s to EXPIRED (%d days overdue, %d units)',
                            $contract['no_kontrak'],
                            $contract['days_overdue'],
                            $unitsAffected
                        ));
                    }

                } catch (\Exception $e) {
                    $errors[] = [
                        'contract_id' => $contract['id'],
                        'contract_number' => $contract['no_kontrak'],
                        'error' => $e->getMessage()
                    ];
                    log_message('error', '[BatchContractOperations] Error updating contract ' . $contract['no_kontrak'] . ': ' . $e->getMessage());
                }
            }

            log_message('info', sprintf(
                '[BatchContractOperations] Batch update complete: %d contracts updated, %d units returned',
                $contractsUpdated,
                $unitsUpdated
            ));

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Batch update completed',
                'contracts_checked' => count($expiredContracts),
                'contracts_updated' => $contractsUpdated,
                'units_updated' => $unitsUpdated,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            log_message('error', '[BatchContractOperations] Error in updateExpiredContracts: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating expired contracts: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update inventory units for expired contract
     * Sets all units to "UNIT PULANG" status (ID: 11)
     * 
     * @param int $contractId Contract ID
     * @return int Number of units updated
     */
    protected function updateInventoryUnitsForExpiredContract($contractId)
    {
        try {
            // Get all units associated with this contract via kontrak_spesifikasi
            $query = $this->db->query("
                SELECT DISTINCT iu.id
                FROM inventory_unit iu
                INNER JOIN kontrak_spesifikasi ks ON ks.inventory_unit_id = iu.id
                WHERE ks.kontrak_id = ?
                AND iu.status_unit_id != 11
            ", [$contractId]);

            $units = $query->getResultArray();

            if (empty($units)) {
                return 0;
            }

            $unitIds = array_column($units, 'id');

            // Update all units to "UNIT PULANG" status (ID: 11)
            $this->db->table('inventory_unit')
                ->whereIn('id', $unitIds)
                ->update(['status_unit_id' => 11]);

            return count($unitIds);

        } catch (\Exception $e) {
            log_message('error', '[BatchContractOperations] Error updating units for contract ' . $contractId . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get batch operation statistics
     */
    public function getBatchStats()
    {
        try {
            $stats = [
                'expired_contracts_pending' => 0,
                'units_needing_return' => 0,
                'last_batch_run' => null
            ];

            // Get count of expired contracts still marked as ACTIVE
            $stats['expired_contracts_pending'] = $this->db->query("
                SELECT COUNT(*) as count
                FROM kontrak
                WHERE status = 'ACTIVE'
                AND tanggal_berakhir < CURDATE()
            ")->getRow()->count;

            // Get count of units that need to be returned
            $stats['units_needing_return'] = $this->db->query("
                SELECT COUNT(DISTINCT iu.id) as count
                FROM inventory_unit iu
                INNER JOIN kontrak_spesifikasi ks ON ks.inventory_unit_id = iu.id
                INNER JOIN kontrak k ON k.id = ks.kontrak_id
                WHERE k.status = 'ACTIVE'
                AND k.tanggal_berakhir < CURDATE()
                AND iu.status_unit_id != 11
            ")->getRow()->count;

            // Get last batch run time from activity log
            $lastRun = $this->db->query("
                SELECT created_at
                FROM system_activity_log
                WHERE action_type = 'CONTRACT_AUTO_EXPIRED'
                ORDER BY created_at DESC
                LIMIT 1
            ")->getRow();

            if ($lastRun) {
                $stats['last_batch_run'] = $lastRun->created_at;
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            log_message('error', '[BatchContractOperations] Error getting stats: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting batch stats: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Manual test trigger (admin only)
     */
    public function testBatchUpdate()
    {
        // Check admin permission
        helper('simple_rbac');
        if (!can_manage('admin')) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        // Run batch update
        $result = $this->updateExpiredContracts();
        
        return view('admin/batch_update_result', [
            'title' => 'Batch Contract Update Test',
            'result' => json_decode($this->response->getBody(), true)
        ]);
    }
}
