<?php
/**
 * Status Unit Helper - Functions for managing unit and attachment status updates
 * based on kontrak status changes
 */

class StatusUnitHelper
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Sync unit and attachment status based on kontrak status
     * Called when:
     * 1. Kontrak status changes
     * 2. Units are assigned/removed from kontrak
     */
    public function syncStatusByKontrak($kontrakId)
    {
        // Get kontrak status
        $kontrak = $this->db->table('kontrak')
            ->select('status')
            ->where('id', $kontrakId)
            ->get()
            ->getRowArray();
            
        if (!$kontrak) {
            return false;
        }
        
        $status = $kontrak['status'];
        $newStatusId = $this->getStatusIdByKontrakStatus($status);
        
        // Update units via kontrak_unit junction table (Phase 1A refactored)
        $this->db->query("
            UPDATE inventory_unit iu
            JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
            SET iu.status_unit_id = ?
            WHERE ku.kontrak_id = ? AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
        ", [$newStatusId, $kontrakId]);
            
        // Update attachments for units in this kontrak via junction
        $this->db->query("
            UPDATE inventory_attachment ia
            JOIN inventory_unit iu ON iu.id_inventory_unit = ia.id_inventory_unit
            JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
            SET ia.attachment_status = 
                CASE
                    WHEN ? IN (3, 5) THEN 'IN_USE'
                    WHEN ? = 4 THEN 'AVAILABLE'
                    ELSE ia.attachment_status
                END
            WHERE ku.kontrak_id = ? AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
        ", [$newStatusId, $newStatusId, $kontrakId]);
        
        return true;
    }
    
    /**
     * Get appropriate status_unit ID based on kontrak status
     */
    private function getStatusIdByKontrakStatus($kontrakStatus)
    {
        switch ($kontrakStatus) {
            case 'ACTIVE':
                return 3; // RENTAL
            case 'EXPIRED':
                return 4; // UNIT PULANG
            case 'CANCELLED':
                return 8; // STOCK NON ASET (default for cancelled)
            case 'PENDING':
            default:
                return 6; // BOOKING
        }
    }
    
    /**
     * Sync status when unit is assigned to kontrak
     */
    public function syncStatusOnUnitAssignment($unitId, $kontrakId)
    {
        if (!$kontrakId) {
            // Unit removed from kontrak - return to stock
            $unit = $this->db->table('inventory_unit')
                ->select('status_aset')
                ->where('id_inventory_unit', $unitId)
                ->get()
                ->getRowArray();
                
            $stockStatus = ($unit && $unit['status_aset'] == 1) ? 7 : 8; // STOCK ASET or STOCK NON ASET
            
            $this->db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update(['status_unit_id' => $stockStatus]);
                
            // Update status in all 3 component tables
            $this->db->table('inventory_batteries')
                ->where('inventory_unit_id', $unitId)
                ->update(['status' => 'AVAILABLE']);
                
            $this->db->table('inventory_chargers')
                ->where('inventory_unit_id', $unitId)
                ->update(['status' => 'AVAILABLE']);
                
            $this->db->table('inventory_attachments')
                ->where('inventory_unit_id', $unitId)
                ->update(['status' => 'AVAILABLE']);
                
            return;
        }
        
        // Unit assigned to kontrak - sync based on kontrak status
        $this->syncStatusByKontrak($kontrakId);
    }
    
    /**
     * Get status summary for debugging
     */
    public function getStatusSummary($kontrakId = null)
    {
        $query = $this->db->table('kontrak k')
            ->select('k.id as kontrak_id, k.no_kontrak, k.status as kontrak_status')
            ->select('COUNT(ku.unit_id) as total_units')
            ->select('GROUP_CONCAT(DISTINCT CONCAT(su.status_unit, " (", iu.status_unit_id, ")")) as unit_statuses')
            ->join('kontrak_unit ku', 'ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE")', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id', 'left')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->groupBy('k.id, k.no_kontrak, k.status')
            ->orderBy('k.id');
            
        if ($kontrakId) {
            $query->where('k.id', $kontrakId);
        }
        
        return $query->get()->getResultArray();
    }
}
?>
