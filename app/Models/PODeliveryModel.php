<?php

namespace App\Models;

use CodeIgniter\Model;

class PODeliveryModel extends Model
{
    protected $table = 'po_deliveries';
    protected $primaryKey = 'id_delivery';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'po_id',
        'delivery_sequence',
        'packing_list_no',
        'delivery_date',
        'expected_date',
        'actual_date',
        'status',
        'total_items',
        'total_value',
        'keterangan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'po_id' => 'required|integer',
        'delivery_sequence' => 'required|integer',
        'packing_list_no' => 'permit_empty|max_length[100]',
        'delivery_date' => 'permit_empty|valid_date',
        'expected_date' => 'permit_empty|valid_date',
        'actual_date' => 'permit_empty|valid_date',
        'status' => 'required|in_list[Scheduled,In Transit,Received,Partial,Cancelled]',
        'total_items' => 'permit_empty|integer',
        'total_value' => 'permit_empty|decimal',
        'keterangan' => 'permit_empty'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get deliveries by PO ID
     */
    public function getDeliveriesByPO($poId)
    {
        return $this->where('po_id', $poId)
                   ->orderBy('delivery_sequence', 'ASC')
                   ->findAll();
    }

    /**
     * Get delivery with items
     */
    public function getDeliveryWithItems($deliveryId)
    {
        $delivery = $this->find($deliveryId);
        if (!$delivery) {
            return null;
        }

        $deliveryItemModel = new DeliveryItemModel();
        $delivery['items'] = $deliveryItemModel->getItemsByDelivery($deliveryId);

        return $delivery;
    }

    /**
     * Get delivery statistics for PO
     */
    public function getDeliveryStats($poId)
    {
        $deliveries = $this->where('po_id', $poId)->findAll();
        
        $stats = [
            'total_deliveries' => count($deliveries),
            'scheduled' => 0,
            'in_transit' => 0,
            'received' => 0,
            'partial' => 0,
            'cancelled' => 0,
            'total_items_delivered' => 0,
            'total_value_delivered' => 0
        ];

        foreach ($deliveries as $delivery) {
            $stats[$delivery['status']] = ($stats[$delivery['status']] ?? 0) + 1;
            $stats['total_items_delivered'] += $delivery['total_items'];
            $stats['total_value_delivered'] += $delivery['total_value'];
        }

        return $stats;
    }

    /**
     * Update delivery status
     */
    public function updateDeliveryStatus($deliveryId, $status, $actualDate = null)
    {
        $data = ['status' => $status];
        if ($actualDate) {
            $data['actual_date'] = $actualDate;
        }

        return $this->update($deliveryId, $data);
    }

    /**
     * Create delivery schedule for PO
     */
    public function createDeliverySchedule($poId, $deliveries)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($deliveries as $index => $delivery) {
                $data = [
                    'po_id' => $poId,
                    'delivery_sequence' => $index + 1,
                    'packing_list_no' => $delivery['packing_list_no'] ?? null,
                    'expected_date' => $delivery['expected_date'] ?? null,
                    'status' => 'Scheduled',
                    'total_items' => $delivery['total_items'] ?? 0,
                    'total_value' => $delivery['total_value'] ?? 0,
                    'keterangan' => $delivery['keterangan'] ?? null
                ];

                $this->insert($data);
            }

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }

    /**
     * Get next packing list number
     */
    public function getNextPackingListNumber($poId)
    {
        $lastDelivery = $this->where('po_id', $poId)
                           ->orderBy('delivery_sequence', 'DESC')
                           ->first();

        if ($lastDelivery && $lastDelivery['packing_list_no']) {
            // Extract number from existing packing list
            preg_match('/PL-(\d+)-(\d+)/', $lastDelivery['packing_list_no'], $matches);
            if (count($matches) >= 3) {
                $sequence = (int)$matches[2] + 1;
                return "PL-{$matches[1]}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            }
        }

        // Generate new packing list number
        $poNumber = $this->db->table('purchase_orders')
                            ->select('no_po')
                            ->where('id_po', $poId)
                            ->get()
                            ->getRow();

        if ($poNumber) {
            $poNum = preg_replace('/[^0-9]/', '', $poNumber->no_po);
            return "PL-{$poNum}-001";
        }

        return "PL-" . date('Ymd') . "-001";
    }
}
