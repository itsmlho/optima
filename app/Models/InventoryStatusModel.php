<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryStatusModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Update inventory status when contract becomes active
     */
    public function updateStatusForActiveContract($kontrakId)
    {
        try {
            $this->db->transBegin();

            // Update inventory_unit status to RENTAL (id: 3)
            // Cover both cases: units linked via kontrak_spesifikasi_id and units linked via kontrak_unit junction
            $this->db->query("
                UPDATE inventory_unit iu
                LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET iu.status_unit_id = 3
                WHERE (ks.kontrak_id = ? OR EXISTS (
                    SELECT 1 FROM kontrak_unit ku 
                    WHERE ku.unit_id = iu.id_inventory_unit AND ku.kontrak_id = ? 
                    AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                )) AND iu.status_unit_id != 3
            ", [$kontrakId, $kontrakId]);

            // Update inventory_batteries status to IN_USE for items linked to this contract
            $this->db->query("
                UPDATE inventory_batteries ib
                JOIN inventory_unit iu ON ib.inventory_unit_id = iu.id_inventory_unit
                LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ib.status = 'IN_USE'
                WHERE (ks.kontrak_id = ? OR EXISTS (
                    SELECT 1 FROM kontrak_unit ku 
                    WHERE ku.unit_id = iu.id_inventory_unit AND ku.kontrak_id = ? 
                    AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                )) AND ib.status != 'IN_USE'
            ", [$kontrakId, $kontrakId]);
            
            // Update inventory_chargers status to IN_USE for items linked to this contract
            $this->db->query("
                UPDATE inventory_chargers ic
                JOIN inventory_unit iu ON ic.inventory_unit_id = iu.id_inventory_unit
                LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ic.status = 'IN_USE'
                WHERE (ks.kontrak_id = ? OR EXISTS (
                    SELECT 1 FROM kontrak_unit ku 
                    WHERE ku.unit_id = iu.id_inventory_unit AND ku.kontrak_id = ? 
                    AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                )) AND ic.status != 'IN_USE'
            ", [$kontrakId, $kontrakId]);
            
            // Update inventory_attachments status to IN_USE for items linked to this contract
            $this->db->query("
                UPDATE inventory_attachments ia
                JOIN inventory_unit iu ON ia.inventory_unit_id = iu.id_inventory_unit
                LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ia.status = 'IN_USE'
                WHERE (ks.kontrak_id = ? OR EXISTS (
                    SELECT 1 FROM kontrak_unit ku 
                    WHERE ku.unit_id = iu.id_inventory_unit AND ku.kontrak_id = ? 
                    AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                )) AND ia.status != 'IN_USE'
            ", [$kontrakId, $kontrakId]);

            // Handle departemen-specific attachment rules
            $this->handleDepartmentAttachmentRules($kontrakId);

            $this->db->transCommit();
            
            log_message('info', "InventoryStatusModel: Updated status to RENTAL for contract {$kontrakId}");
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "InventoryStatusModel: Error updating status for active contract {$kontrakId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update inventory status when contract ends or is cancelled
     */
    public function updateStatusForEndedContract($kontrakId)
    {
        try {
            $this->db->transBegin();

            // Update inventory_unit status to UNIT PULANG (id: 4)
            $this->db->query("
                UPDATE inventory_unit iu
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET iu.status_unit_id = 4
                WHERE ks.kontrak_id = ? AND iu.status_unit_id = 3
            ", [$kontrakId]);

            // Update inventory_batteries status to AVAILABLE when contract ends
            $this->db->query("
                UPDATE inventory_batteries ib
                JOIN inventory_unit iu ON ib.inventory_unit_id = iu.id_inventory_unit
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ib.status = 'AVAILABLE',
                    ib.inventory_unit_id = NULL,
                    ib.storage_location = 'Returned from contract'
                WHERE ks.kontrak_id = ? AND ib.status = 'IN_USE'
            ", [$kontrakId]);
            
            // Update inventory_chargers status to AVAILABLE when contract ends
            $this->db->query("
                UPDATE inventory_chargers ic
                JOIN inventory_unit iu ON ic.inventory_unit_id = iu.id_inventory_unit
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ic.status = 'AVAILABLE',
                    ic.inventory_unit_id = NULL,
                    ic.storage_location = 'Returned from contract'
                WHERE ks.kontrak_id = ? AND ic.status = 'IN_USE'
            ", [$kontrakId]);
            
            // Update inventory_attachments status to AVAILABLE when contract ends
            $this->db->query("
                UPDATE inventory_attachments ia
                JOIN inventory_unit iu ON ia.inventory_unit_id = iu.id_inventory_unit
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ia.status = 'AVAILABLE',
                    ia.inventory_unit_id = NULL,
                    ia.storage_location = 'Returned from contract'
                WHERE ks.kontrak_id = ? AND ia.status = 'IN_USE'
            ", [$kontrakId]);

            $this->db->transCommit();
            
            log_message('info', "InventoryStatusModel: Updated status to UNIT PULANG for contract {$kontrakId}");
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "InventoryStatusModel: Error updating status for ended contract {$kontrakId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update inventory status after DI (Delivery Instruction) is completed
     * This should be called from DI completion process
     */
    public function updateStatusAfterDICompleted($kontrakId)
    {
        try {
            $this->db->transBegin();

            // Update inventory_unit status to STOCK ASET (id: 7)
            $this->db->query("
                UPDATE inventory_unit iu
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET iu.status_unit_id = 7
                WHERE ks.kontrak_id = ? AND iu.status_unit_id = 4
            ", [$kontrakId]);

            // Update inventory_batteries status to AVAILABLE after DI completed
            $this->db->query("
                UPDATE inventory_batteries ib
                JOIN inventory_unit iu ON ib.inventory_unit_id = iu.id_inventory_unit
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ib.status = 'AVAILABLE'
                WHERE ks.kontrak_id = ? AND ib.status = 'IN_USE'
            ", [$kontrakId]);
            
            // Update inventory_chargers status to AVAILABLE after DI completed
            $this->db->query("
                UPDATE inventory_chargers ic
                JOIN inventory_unit iu ON ic.inventory_unit_id = iu.id_inventory_unit
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ic.status = 'AVAILABLE'
                WHERE ks.kontrak_id = ? AND ic.status = 'IN_USE'
            ", [$kontrakId]);
            
            // Update inventory_attachments status to AVAILABLE after DI completed
            $this->db->query("
                UPDATE inventory_attachments ia
                JOIN inventory_unit iu ON ia.inventory_unit_id = iu.id_inventory_unit
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ia.status = 'AVAILABLE'
                WHERE ks.kontrak_id = ? AND ia.status = 'IN_USE'
            ", [$kontrakId]);

            $this->db->transCommit();
            
            log_message('info', "InventoryStatusModel: Updated status to STOCK ASET after DI completed for contract {$kontrakId}");
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "InventoryStatusModel: Error updating status after DI completed for contract {$kontrakId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get current inventory status for a contract
     */
    public function getInventoryStatusByContract($kontrakId)
    {
        $units = $this->db->query("
            SELECT 
                iu.id_inventory_unit,
                iu.no_unit,
                su.status_unit as unit_status,
                (
                    (SELECT COUNT(*) FROM inventory_batteries WHERE inventory_unit_id = iu.id_inventory_unit) +
                    (SELECT COUNT(*) FROM inventory_chargers WHERE inventory_unit_id = iu.id_inventory_unit) +
                    (SELECT COUNT(*) FROM inventory_attachments WHERE inventory_unit_id = iu.id_inventory_unit)
                ) as attachment_count
            FROM inventory_unit iu
            JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
            JOIN status_unit su ON iu.status_unit_id = su.id_status
            WHERE ks.kontrak_id = ?
            GROUP BY iu.id_inventory_unit
        ", [$kontrakId])->getResultArray();

        $attachments = $this->db->query("
            SELECT id, 'battery' as tipe_item, status, iu.no_unit
            FROM inventory_batteries ib
            JOIN inventory_unit iu ON ib.inventory_unit_id = iu.id_inventory_unit
            JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
            WHERE ks.kontrak_id = ?
            UNION ALL
            SELECT id, 'charger' as tipe_item, status, iu.no_unit
            FROM inventory_chargers ic
            JOIN inventory_unit iu ON ic.inventory_unit_id = iu.id_inventory_unit
            JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
            WHERE ks.kontrak_id = ?
            UNION ALL
            SELECT id, 'attachment' as tipe_item, status, iu.no_unit
            FROM inventory_attachments ia
            JOIN inventory_unit iu ON ia.inventory_unit_id = iu.id_inventory_unit
            JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
            WHERE ks.kontrak_id = ?
        ", [$kontrakId, $kontrakId, $kontrakId])->getResultArray();

        return [
            'units' => $units,
            'attachments' => $attachments
        ];
    }

    /**
     * Handle department-specific attachment rules
     * GASOLINE and DIESEL departments should not have charger/battery
     */
    public function handleDepartmentAttachmentRules($kontrakId)
    {
        try {
            // Get units with GASOLINE or DIESEL departments that have charger/battery components
            $unitsWithInvalidBatteries = $this->db->query("
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    d.nama_departemen,
                    ib.id,
                    'battery' as tipe_item
                FROM inventory_unit iu
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                JOIN departemen d ON iu.departemen_id = d.id_departemen
                JOIN inventory_batteries ib ON iu.id_inventory_unit = ib.inventory_unit_id
                WHERE ks.kontrak_id = ?
                AND d.nama_departemen IN ('GASOLINE', 'DIESEL')
            ", [$kontrakId])->getResultArray();
            
            $unitsWithInvalidChargers = $this->db->query("
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    d.nama_departemen,
                    ic.id,
                    'charger' as tipe_item
                FROM inventory_unit iu
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                JOIN departemen d ON iu.departemen_id = d.id_departemen
                JOIN inventory_chargers ic ON iu.id_inventory_unit = ic.inventory_unit_id
                WHERE ks.kontrak_id = ?
                AND d.nama_departemen IN ('GASOLINE', 'DIESEL')
            ", [$kontrakId])->getResultArray();
            
            $unitsWithInvalidAttachments = array_merge($unitsWithInvalidBatteries, $unitsWithInvalidChargers);

            if (!empty($unitsWithInvalidAttachments)) {
                log_message('info', "Found " . count($unitsWithInvalidAttachments) . " invalid attachments for GASOLINE/DIESEL units in contract {$kontrakId}");
                
                foreach ($unitsWithInvalidAttachments as $attachment) {
                    // Detach charger/battery from GASOLINE/DIESEL units
                    $tableName = $attachment['tipe_item'] === 'battery' ? 'inventory_batteries' : 'inventory_chargers';
                    
                    $this->db->query("
                        UPDATE {$tableName}
                        SET inventory_unit_id = NULL,
                            status = 'AVAILABLE',
                            storage_location = 'Detached from GASOLINE/DIESEL unit'
                        WHERE id = ?
                    ", [$attachment['id']]);
                    
                    log_message('info', "Detached {$attachment['tipe_item']} from {$attachment['nama_departemen']} unit {$attachment['no_unit']}");
                }
            }

        } catch (\Exception $e) {
            log_message('error', "Error handling department attachment rules: " . $e->getMessage());
        }
    }

    /**
     * Auto-link attachments from SPK fabrication process
     */
    public function linkAttachmentsFromSPK($spkId)
    {
        try {
            $this->db->transBegin();

            // Get SPK details with unit and attachment information
            $spkData = $this->db->query("
                SELECT 
                    s.id as spk_id,
                    s.kontrak_spesifikasi_id,
                    iu.id_inventory_unit,
                    iu.no_unit,
                    iu.departemen_id,
                    d.nama_departemen
                FROM spk s
                JOIN inventory_unit iu ON s.kontrak_spesifikasi_id = iu.kontrak_spesifikasi_id
                JOIN departemen d ON iu.departemen_id = d.id_departemen
                WHERE s.id = ?
            ", [$spkId])->getRowArray();

            if (!$spkData) {
                throw new \Exception("SPK {$spkId} not found or no associated unit");
            }

            // Check if this is ELECTRIC department (can have charger/battery)
            $canHaveElectricAttachments = ($spkData['nama_departemen'] === 'ELECTRIC');

            // Get available attachments for this unit type from fabrication selection
            // This should be updated based on your SPK fabrication process
            $fabricationAttachments = $this->getFabricationAttachments($spkId);

            foreach ($fabricationAttachments as $attachment) {
                // Skip charger/battery for non-ELECTRIC departments
                if (!$canHaveElectricAttachments && in_array($attachment['tipe_item'], ['charger', 'battery'])) {
                    log_message('info', "Skipping {$attachment['tipe_item']} for {$spkData['nama_departemen']} unit {$spkData['no_unit']}");
                    continue;
                }

                // Link attachment to unit
                $tableName = match($attachment['tipe_item']) {
                    'battery' => 'inventory_batteries',
                    'charger' => 'inventory_chargers',
                    'attachment' => 'inventory_attachments',
                    default => null
                };
                
                if (!$tableName) continue;
                
                $this->db->query("
                    UPDATE {$tableName}
                    SET inventory_unit_id = ?,
                        status = 'IN_USE',
                        storage_location = ?
                    WHERE id = ?
                ", [
                    $spkData['id_inventory_unit'],
                    "Terpasang di Unit {$spkData['no_unit']}",
                    $attachment['id']
                ]);

                log_message('info', "Linked {$attachment['tipe_item']} to unit {$spkData['no_unit']}");
            }

            $this->db->transCommit();
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error linking attachments from SPK {$spkId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get fabrication attachments from SPK process
     * This method should be implemented based on your SPK fabrication workflow
     */
    private function getFabricationAttachments($spkId)
    {
        // This is a placeholder - implement based on your SPK fabrication process
        // where attachments are selected during fabrication
        
        // Example: Get from spk_fabrication_attachments table or similar
        return $this->db->query("
            SELECT 
                ib.id,
                'battery' as tipe_item
            FROM spk_fabrication_attachments sfa
            JOIN inventory_batteries ib ON sfa.attachment_id = ib.id
            WHERE sfa.spk_id = ?
            UNION ALL
            SELECT 
                ic.id,
                'charger' as tipe_item
            FROM spk_fabrication_attachments sfa
            JOIN inventory_chargers ic ON sfa.attachment_id = ic.id
            WHERE sfa.spk_id = ?
            UNION ALL
            SELECT 
                ia.id,
                'attachment' as tipe_item
            FROM spk_fabrication_attachments sfa
            JOIN inventory_attachments ia ON sfa.attachment_id = ia.id
            WHERE sfa.spk_id = ?
        ", [$spkId, $spkId, $spkId])->getResultArray();
    }

    /**
     * Trigger status updates after SPK completion or DI processing
     */
    public function updateStatusAfterSPKWorkflow($kontrakId)
    {
        try {
            // Get current contract status
            $kontrak = $this->db->query("
                SELECT status FROM kontrak WHERE id = ?
            ", [$kontrakId])->getRowArray();

            if (!$kontrak) {
                return false;
            }

            // Only update if contract is active
            if ($kontrak['status'] === 'ACTIVE') {
                return $this->updateStatusForActiveContract($kontrakId);
            }

            return true;

        } catch (\Exception $e) {
            log_message('error', "Error updating status after SPK workflow for contract {$kontrakId}: " . $e->getMessage());
            return false;
        }
    }
}
