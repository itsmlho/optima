<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\InventoryAttachmentModel;

class InventoryApi extends BaseController
{
    public function availableAttachments()
    {
        $attachmentId = (int) $this->request->getGet('attachment_id');
        $m = new InventoryAttachmentModel();
        $rows = $attachmentId ? $m->getAvailableForAttachment($attachmentId) : [];
        return $this->response->setJSON($rows);
    }

    public function availableChargers()
    {
        $m = new InventoryAttachmentModel();
        $rows = $m->getAvailableChargers();
        return $this->response->setJSON($rows);
    }

    public function availableBatteries()
    {
        $m = new InventoryAttachmentModel();
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

        $m = new InventoryAttachmentModel();
        $battery = $m->getUnitBattery($unitId);
        $charger = $m->getUnitCharger($unitId);

        return $this->response->setJSON([
            'unit_id' => $unitId,
            'battery' => $battery,
            'charger' => $charger
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
        $componentType = $this->request->getPost('component_type'); // 'battery' or 'charger'

        if (!$unitId || !$newAttachmentId || !in_array($componentType, ['battery', 'charger'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false, 
                'message' => 'Missing required parameters'
            ]);
        }

        $m = new InventoryAttachmentModel();
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get unit info for location notes
            $unitInfo = $db->table('inventory_unit')
                ->select('no_unit')
                ->where('id_inventory_unit', $unitId)
                ->get()->getRowArray();
            $unitNumber = $unitInfo['no_unit'] ?? null;

            // Detach old component if exists
            if ($oldAttachmentId > 0) {
                $m->detachFromUnit($oldAttachmentId);
            }

            // Attach new component
            $m->attachToUnit($newAttachmentId, $unitId, $unitNumber);

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