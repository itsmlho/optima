<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * InventoryComponentHelper
 * 
 * Helper model untuk backward compatibility dengan struktur inventory lama
 * Provides unified interface untuk query attachments, batteries, chargers
 */
class InventoryComponentHelper extends Model
{
    protected $db;
    protected $attachmentModel;
    protected $batteryModel;
    protected $chargerModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->attachmentModel = new InventoryAttachmentModel();
        $this->batteryModel = new InventoryBatteryModel();
        $this->chargerModel = new InventoryChargerModel();
    }
    
    /**
     * Get unit components (battery, charger, attachment) for a specific unit
     * Replacement for old queries to inventory_attachment with tipe_item filter
     */
    public function getUnitComponents($unitId)
    {
        $components = [
            'battery' => null,
            'charger' => null,
            'attachment' => null
        ];

        // Get battery info from inventory_batteries table
        $battery = $this->db->table('inventory_batteries ib')
            ->select('ib.id as id_inventory_attachment, ib.battery_type_id as baterai_id, ib.serial_number as sn_baterai, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'ib.battery_type_id = b.id', 'left')
            ->where('ib.inventory_unit_id', $unitId)
            ->whereIn('ib.status', ['AVAILABLE', 'IN_USE', 'SPARE'])
            ->get()->getRowArray();

        if ($battery) {
            $components['battery'] = $battery;
        }

        // Get charger info from inventory_chargers table
        $charger = $this->db->table('inventory_chargers ic')
            ->select('ic.id as id_inventory_attachment, ic.charger_type_id as charger_id, ic.serial_number as sn_charger, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'ic.charger_type_id = c.id_charger', 'left')
            ->where('ic.inventory_unit_id', $unitId)
            ->whereIn('ic.status', ['AVAILABLE', 'IN_USE', 'SPARE'])
            ->get()->getRowArray();

        if ($charger) {
            $components['charger'] = $charger;
        }

        // Get attachment info from inventory_attachments table
        $attachment = $this->db->table('inventory_attachments ia')
            ->select('ia.id as id_inventory_attachment, ia.attachment_type_id as attachment_id, ia.serial_number as sn_attachment, a.tipe, a.merk, a.model')
            ->join('attachment a', 'ia.attachment_type_id = a.id_attachment', 'left')
            ->where('ia.inventory_unit_id', $unitId)
            ->whereIn('ia.status', ['IN_USE', 'SPARE'])
            ->get()->getRowArray();

        if ($attachment) {
            $components['attachment'] = $attachment;
        }

        return $components;
    }
    
    /**
     * Get component detail by ID and type
     * @param int $id - Component ID
     * @param string $type - 'attachment', 'battery', or 'charger'
     * @return array|null
     */
    public function getComponentById($id, $type)
    {
        switch (strtolower($type)) {
            case 'battery':
                $result = $this->db->table('inventory_batteries ib')
                    ->select('ib.id as id_inventory_attachment, ib.serial_number as sn_baterai, ib.storage_location as lokasi_penyimpanan, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
                    ->join('baterai b', 'ib.battery_type_id = b.id', 'left')
                    ->where('ib.id', $id)
                    ->get()->getRowArray();
                break;
                
            case 'charger':
                $result = $this->db->table('inventory_chargers ic')
                    ->select('ic.id as id_inventory_attachment, ic.serial_number as sn_charger, ic.storage_location as lokasi_penyimpanan, c.merk_charger, c.tipe_charger')
                    ->join('charger c', 'ic.charger_type_id = c.id_charger', 'left')
                    ->where('ic.id', $id)
                    ->get()->getRowArray();
                break;
                
            case 'attachment':
            default:
                $result = $this->db->table('inventory_attachments ia')
                    ->select('ia.id as id_inventory_attachment, ia.serial_number as sn_attachment, ia.storage_location as lokasi_penyimpanan, a.tipe, a.merk, a.model')
                    ->join('attachment a', 'ia.attachment_type_id = a.id_attachment', 'left')
                    ->where('ia.id', $id)
                    ->get()->getRowArray();
                break;
        }
        
        return $result ?? null;
    }
    
    /**
     * Get battery by inventory ID (for backward compatibility)
     * @param int $inventoryAttachmentId - Old ID from inventory_attachment table
     * @return array|null
     */
    public function getBatteryByInventoryId($inventoryAttachmentId)
    {
        return $this->db->table('inventory_batteries ib')
            ->select('ib.id as id_inventory_attachment, ib.battery_type_id as baterai_id, ib.serial_number as sn_baterai, ib.storage_location as lokasi_penyimpanan, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'ib.battery_type_id = b.id', 'left')
            ->where('ib.id', $inventoryAttachmentId)
            ->get()->getRowArray();
    }
    
    /**
     * Get charger by inventory ID (for backward compatibility)
     */
    public function getChargerByInventoryId($inventoryAttachmentId)
    {
        return $this->db->table('inventory_chargers ic')
            ->select('ic.id as id_inventory_attachment, ic.charger_type_id as charger_id, ic.serial_number as sn_charger, ic.storage_location as lokasi_penyimpanan, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'ic.charger_type_id = c.id_charger', 'left')
            ->where('ic.id', $inventoryAttachmentId)
            ->get()->getRowArray();
    }
    
    /**
     * Get attachment by inventory ID (for backward compatibility)
     */
    public function getAttachmentByInventoryId($inventoryAttachmentId)
    {
        return $this->db->table('inventory_attachments ia')
            ->select('ia.id as id_inventory_attachment, ia.attachment_type_id as attachment_id, ia.serial_number as sn_attachment, ia.storage_location as lokasi_penyimpanan, a.tipe, a.merk, a.model')
            ->join('attachment a', 'ia.attachment_type_id = a.id_attachment', 'left')
            ->where('ia.id', $inventoryAttachmentId)
            ->get()->getRowArray();
    }
    
    /**
     * Get available batteries for selection (AVAILABLE or SPARE status)
     */
    public function getAvailableBatteries()
    {
        return $this->db->table('inventory_batteries ib')
            ->select('ib.id as id_inventory_attachment, ib.battery_type_id as baterai_id, ib.serial_number as sn_baterai, ib.storage_location as lokasi_penyimpanan, b.merk_baterai, b.tipe_baterai, b.jenis_baterai, ib.status as attachment_status')
            ->join('baterai b', 'ib.battery_type_id = b.id', 'left')
            ->whereIn('ib.status', ['AVAILABLE', 'SPARE'])
            ->orderBy('ib.created_at', 'DESC')
            ->get()->getResultArray();
    }
    
    /**
     * Get available chargers for selection
     */
    public function getAvailableChargers()
    {
        return $this->db->table('inventory_chargers ic')
            ->select('ic.id as id_inventory_attachment, ic.charger_type_id as charger_id, ic.serial_number as sn_charger, ic.storage_location as lokasi_penyimpanan, c.merk_charger, c.tipe_charger, ic.status as attachment_status')
            ->join('charger c', 'ic.charger_type_id = c.id_charger', 'left')
            ->whereIn('ic.status', ['AVAILABLE', 'SPARE'])
            ->orderBy('ic.created_at', 'DESC')
            ->get()->getResultArray();
    }
    
    /**
     * Get available attachments for selection
     */
    public function getAvailableAttachments()
    {
        return $this->db->table('inventory_attachments ia')
            ->select('ia.id as id_inventory_attachment, ia.attachment_type_id as attachment_id, ia.serial_number as sn_attachment, ia.storage_location as lokasi_penyimpanan, a.tipe, a.merk, a.model, ia.status as attachment_status')
            ->join('attachment a', 'ia.attachment_type_id = a.id_attachment', 'left')
            ->whereIn('ia.status', ['AVAILABLE', 'SPARE'])
            ->orderBy('ia.created_at', 'DESC')
            ->get()->getResultArray();
    }
    
    /**
     * Update component status and location
     * @param int $id - Component ID  
     * @param string $type - 'attachment', 'battery', or 'charger'
     * @param array $data - Data to update (status, storage_location, inventory_unit_id, etc.)
     */
    public function updateComponent($id, $type, $data)
    {
        // Map old column names to new ones
        $mappedData = [];
        
        if (isset($data['attachment_status'])) {
            $mappedData['status'] = $data['attachment_status'];
        }
        if (isset($data['lokasi_penyimpanan'])) {
            $mappedData['storage_location'] = $data['lokasi_penyimpanan'];
        }
        if (isset($data['id_inventory_unit'])) {
            $mappedData['inventory_unit_id'] = $data['id_inventory_unit'];
        }
        
        // Also include any direct new-format keys
        foreach (['status', 'storage_location', 'inventory_unit_id', 'updated_at'] as $key) {
            if (isset($data[$key])) {
                $mappedData[$key] = $data[$key];
            }
        }
        
        switch (strtolower($type)) {
            case 'battery':
                return $this->db->table('inventory_batteries')->where('id', $id)->update($mappedData);
            case 'charger':
                return $this->db->table('inventory_chargers')->where('id', $id)->update($mappedData);
            case 'attachment':
            default:
                return $this->db->table('inventory_attachments')->where('id', $id)->update($mappedData);
        }
    }
    
    /**
     * Check if component exists in any of the 3 tables and return its type
     * @param int $id
     * @return string|null - 'attachment', 'battery', 'charger', or null if not found
     */
    public function getComponentType($id)
    {
        if ($this->db->table('inventory_attachments')->where('id', $id)->countAllResults() > 0) {
            return 'attachment';
        }
        if ($this->db->table('inventory_batteries')->where('id', $id)->countAllResults() > 0) {
            return 'battery';
        }
        if ($this->db->table('inventory_chargers')->where('id', $id)->countAllResults() > 0) {
            return 'charger';
        }
        return null;
    }
    
    /**
     * Find component by ID from any of the 3 tables without knowing the type
     * Used for DI creation where we need to get the type_id (baterai_id, charger_id, attachment_id)
     * Returns component data with tipe_item and the corresponding type_id field
     */
    public function findComponentByIdAny($id)
    {
        // Try battery first
        $battery = $this->db->table('inventory_batteries ib')
            ->select('ib.battery_type_id as baterai_id, "battery" as tipe_item')
            ->where('ib.id', $id)
            ->get()->getRowArray();
        
        if ($battery) {
            return $battery;
        }
        
        // Try charger
        $charger = $this->db->table('inventory_chargers ic')
            ->select('ic.charger_type_id as charger_id, "charger" as tipe_item')
            ->where('ic.id', $id)
            ->get()->getRowArray();
        
        if ($charger) {
            return $charger;
        }
        
        // Try attachment
        $attachment = $this->db->table('inventory_attachments ia')
            ->select('ia.attachment_type_id as attachment_id, "attachment" as tipe_item')
            ->where('ia.id', $id)
            ->get()->getRowArray();
        
        if ($attachment) {
            return $attachment;
        }
        
        return null;
    }
}
