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
}