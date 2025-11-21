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
            // Cover both cases: units linked via kontrak_spesifikasi_id and units linked directly via kontrak_id
            $this->db->query("
                UPDATE inventory_unit iu
                LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET iu.status_unit_id = 3
                WHERE (ks.kontrak_id = ? OR iu.kontrak_id = ?) AND iu.status_unit_id != 3
            ", [$kontrakId, $kontrakId]);

            // Update inventory_attachment status to RENTAL (id: 3) for items linked to this contract
            // This covers attachments referenced by inventory_unit regardless of how the unit is linked
            $this->db->query("
                UPDATE inventory_attachment ia
                JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
                LEFT JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ia.status_unit = 3
                WHERE (ks.kontrak_id = ? OR iu.kontrak_id = ?) AND ia.status_unit != 3
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

            // Update inventory_attachment status to UNIT PULANG (id: 4) for items linked to this contract
            $this->db->query("
                UPDATE inventory_attachment ia
                JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ia.status_unit = 4
                WHERE ks.kontrak_id = ? AND ia.status_unit = 3
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

            // Update inventory_attachment status to STOCK ASET (id: 7) for items linked to this contract
            $this->db->query("
                UPDATE inventory_attachment ia
                JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                SET ia.status_unit = 7
                WHERE ks.kontrak_id = ? AND ia.status_unit = 4
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
                COUNT(ia.id_inventory_attachment) as attachment_count
            FROM inventory_unit iu
            JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
            JOIN status_unit su ON iu.status_unit_id = su.id_status
            LEFT JOIN inventory_attachment ia ON iu.id_inventory_unit = ia.id_inventory_unit
            WHERE ks.kontrak_id = ?
            GROUP BY iu.id_inventory_unit
        ", [$kontrakId])->getResultArray();

        $attachments = $this->db->query("
            SELECT 
                ia.id_inventory_attachment,
                ia.tipe_item,
                su.status_unit as attachment_status,
                iu.no_unit
            FROM inventory_attachment ia
            JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
            JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
            JOIN status_unit su ON ia.status_unit = su.id_status
            WHERE ks.kontrak_id = ?
        ", [$kontrakId])->getResultArray();

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
            // Get units with GASOLINE or DIESEL departments that have charger/battery attachments
            $unitsWithInvalidAttachments = $this->db->query("
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    d.nama_departemen,
                    ia.id_inventory_attachment,
                    ia.tipe_item
                FROM inventory_unit iu
                JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
                JOIN departemen d ON iu.departemen_id = d.id_departemen
                JOIN inventory_attachment ia ON iu.id_inventory_unit = ia.id_inventory_unit
                WHERE ks.kontrak_id = ?
                AND d.nama_departemen IN ('GASOLINE', 'DIESEL')
                AND ia.tipe_item IN ('charger', 'battery')
            ", [$kontrakId])->getResultArray();

            if (!empty($unitsWithInvalidAttachments)) {
                log_message('info', "Found " . count($unitsWithInvalidAttachments) . " invalid attachments for GASOLINE/DIESEL units in contract {$kontrakId}");
                
                foreach ($unitsWithInvalidAttachments as $attachment) {
                    // Detach charger/battery from GASOLINE/DIESEL units
                    $this->db->query("
                        UPDATE inventory_attachment 
                        SET id_inventory_unit = NULL,
                            status_unit = 7,
                            lokasi_penyimpanan = 'Detached from GASOLINE/DIESEL unit'
                        WHERE id_inventory_attachment = ?
                    ", [$attachment['id_inventory_attachment']]);
                    
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
                $this->db->query("
                    UPDATE inventory_attachment 
                    SET id_inventory_unit = ?,
                        status_unit = 3,
                        lokasi_penyimpanan = ?
                    WHERE id_inventory_attachment = ?
                ", [
                    $spkData['id_inventory_unit'],
                    "Terpasang di Unit {$spkData['no_unit']}",
                    $attachment['id_inventory_attachment']
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
                ia.id_inventory_attachment,
                ia.tipe_item
            FROM spk_fabrication_attachments sfa
            JOIN inventory_attachment ia ON sfa.attachment_id = ia.id_inventory_attachment
            WHERE sfa.spk_id = ?
        ", [$spkId])->getResultArray();
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
            if ($kontrak['status'] === 'Aktif') {
                return $this->updateStatusForActiveContract($kontrakId);
            }

            return true;

        } catch (\Exception $e) {
            log_message('error', "Error updating status after SPK workflow for contract {$kontrakId}: " . $e->getMessage());
            return false;
        }
    }
}
