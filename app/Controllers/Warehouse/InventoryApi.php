<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\InventoryAttachmentModel;
use App\Models\InventoryBatteryModel;
use App\Models\InventoryChargerModel;
use App\Traits\ActivityLoggingTrait;

class InventoryApi extends BaseController
{
    use ActivityLoggingTrait;
    public function availableAttachments()
    {
        $attachmentId = (int) $this->request->getGet('attachment_id');
        $tipe         = $this->request->getGet('tipe');
        $merk         = $this->request->getGet('merk');

        // When tipe/merk filters are provided, query the inventory_attachments table
        // with the attachment master table join.
        if ($tipe || $merk) {
            $db      = \Config\Database::connect();
            $builder = $db->table('inventory_attachments ia')
                ->select('ia.id as id, ia.serial_number as sn_attachment,
                          ia.storage_location as lokasi_penyimpanan, ia.status,
                          ia.physical_condition as kondisi_fisik, a.tipe, a.merk, a.model')
                ->join('attachment a', 'ia.attachment_type_id = a.id_attachment', 'left')
                ->where('ia.attachment_type_id IS NOT NULL')
                ->where('ia.status', 'AVAILABLE');

            if ($tipe) {
                $builder->where('a.tipe', $tipe);
            }
            if ($merk) {
                $builder->where('a.merk', $merk);
            }

            $rows = $builder->get()->getResultArray();
            return $this->response->setJSON(['success' => true, 'data' => $rows]);
        }

        $m    = new InventoryAttachmentModel();
        $rows = $attachmentId ? $m->getAvailableForAttachment($attachmentId) : [];
        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    public function availableChargers()
    {
        $m = new InventoryChargerModel();
        $rows = $m->getAvailableChargers();
        return $this->response->setJSON($rows);
    }

    public function availableBatteries()
    {
        $m = new InventoryBatteryModel();
        $rows = $m->getAvailableBatteries();
        return $this->response->setJSON($rows);
    }

    /**
     * Get unit's current battery and charger info
     */
    public function getUnitComponents()
    {
        $unitId = (int) $this->request->getGet('unit_id');
        if (!$unitId) {
            return $this->response->setJSON(['error' => 'Unit ID required']);
        }

        // Use correct models for each component type
        $batteryModel = new InventoryBatteryModel();
        $chargerModel = new InventoryChargerModel();
        $attachmentModel = new InventoryAttachmentModel();
        
        $battery = $batteryModel->getUnitBattery($unitId);
        $charger = $chargerModel->getUnitCharger($unitId);
        $attachment = $attachmentModel->getUnitAttachment($unitId);

        return $this->response->setJSON([
            'unit_id' => $unitId,
            'battery' => $battery,
            'charger' => $charger,
            'attachment' => $attachment
        ]);
    }

    /**
     * Handle component replacement (detach old, attach new)
     */
    public function replaceComponent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        $unitId = (int) $this->request->getPost('unit_id');
        $oldAttachmentId = (int) $this->request->getPost('old_attachment_id');
        $newAttachmentId = (int) $this->request->getPost('new_attachment_id');
        $componentType = $this->request->getPost('component_type'); // 'battery', 'charger', or 'attachment'

        if (!$unitId || !$newAttachmentId || !in_array($componentType, ['battery', 'charger', 'attachment'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false, 
                'message' => 'Missing required parameters'
            ]);
        }

        // Use the correct model based on component type
        $model = match($componentType) {
            'battery' => new \App\Models\InventoryBatteryModel(),
            'charger' => new \App\Models\InventoryChargerModel(),
            'attachment' => new \App\Models\InventoryAttachmentModel(),
            default => null
        };
        
        if (!$model) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false, 
                'message' => 'Invalid component type'
            ]);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get unit info for location notes
            $unitInfo = $db->table('inventory_unit')
                ->select('no_unit')
                ->where('id_inventory_unit', $unitId)
                ->get()->getRowArray();
            $unitNumber = $unitInfo['no_unit'] ?? null;

            // Detach old component if exists (model methods now log to component_audit_log)
            if ($oldAttachmentId > 0) {
                $model->detachFromUnit($oldAttachmentId);
            }
            
            // Attach new component (model methods now log to component_audit_log)
            $model->attachToUnit($newAttachmentId, $unitId, $unitNumber);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => ucfirst($componentType) . ' berhasil diganti',
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengganti ' . $componentType . ': ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }
}