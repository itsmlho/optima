<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryItemModel extends Model
{
    protected $table = 'delivery_items';
    protected $primaryKey = 'id_delivery_item';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        // DI workflow fields
        'di_id',
        'item_type',
        'item_role',
        'unit_id',
        'attachment_id',
        'parent_unit_id',
        'keterangan',
        'operator_required',
        'operator_quantity',
        'operator_monthly_rate_snapshot',
        'operator_daily_rate_snapshot',
        'operator_rate_source',
        // Legacy PO delivery fields
        'delivery_id',
        'po_item_id',
        'qty_delivered',
        'qty_verified',
        'kondisi_item',
        'serial_numbers',
        'keterangan',
        'verified_by',
        'verified_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'delivery_id' => 'required|integer',
        'po_item_id' => 'required|integer',
        'qty_delivered' => 'required|integer|greater_than[0]',
        'qty_verified' => 'permit_empty|integer|greater_than_equal_to[0]',
        'kondisi_item' => 'required|in_list[Baik,Rusak,Kurang,Belum Dicek]',
        'serial_numbers' => 'permit_empty',
        'keterangan' => 'permit_empty',
        'verified_by' => 'permit_empty|max_length[100]',
        'verified_at' => 'permit_empty|valid_date'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get items by delivery ID
     */
    public function getItemsByDelivery($deliveryId)
    {
        return $this->select('delivery_items.*, po_attachment.item_type, po_attachment.item_name, po_attachment.qty_ordered')
                   ->join('po_attachment', 'po_attachment.id_po_item = delivery_items.po_item_id')
                   ->where('delivery_items.delivery_id', $deliveryId)
                   ->findAll();
    }

    /**
     * Get delivery items with PO item details
     */
    public function getDeliveryItemsWithDetails($deliveryId)
    {
        return $this->select('
                delivery_items.*,
                po_attachment.item_type,
                po_attachment.item_name,
                po_attachment.qty_ordered,
                po_attachment.harga_satuan,
                po_attachment.total_harga,
                po_attachment.keterangan as po_item_keterangan
            ')
            ->join('po_attachment', 'po_attachment.id_po_item = delivery_items.po_item_id')
            ->where('delivery_items.delivery_id', $deliveryId)
            ->findAll();
    }

    /**
     * Verify delivery items
     */
    public function verifyDeliveryItems($deliveryId, $items, $verifiedBy)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($items as $item) {
                $data = [
                    'qty_verified' => $item['qty_verified'],
                    'kondisi_item' => $item['kondisi_item'],
                    'serial_numbers' => json_encode($item['serial_numbers'] ?? []),
                    'keterangan' => $item['keterangan'] ?? null,
                    'verified_by' => $verifiedBy,
                    'verified_at' => date('Y-m-d H:i:s')
                ];

                $this->update($item['delivery_item_id'], $data);

                // Update PO item received quantity
                $this->db->table('po_attachment')
                        ->set('qty_received', 'qty_received + ' . $item['qty_verified'], false)
                        ->where('id_po_item', $item['po_item_id'])
                        ->update();
            }

            // Update delivery status
            $deliveryModel = new PODeliveryModel();
            $deliveryModel->updateDeliveryStatus($deliveryId, 'Received', date('Y-m-d'));

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }

    /**
     * Get verification statistics for delivery
     */
    public function getVerificationStats($deliveryId)
    {
        $items = $this->where('delivery_id', $deliveryId)->findAll();
        
        $stats = [
            'total_items' => count($items),
            'verified_items' => 0,
            'baik' => 0,
            'rusak' => 0,
            'kurang' => 0,
            'belum_dicek' => 0,
            'total_qty_delivered' => 0,
            'total_qty_verified' => 0
        ];

        foreach ($items as $item) {
            $stats['total_qty_delivered'] += $item['qty_delivered'];
            $stats['total_qty_verified'] += $item['qty_verified'];
            
            if ($item['verified_at']) {
                $stats['verified_items']++;
            }
            
            $stats[$item['kondisi_item']] = ($stats[$item['kondisi_item']] ?? 0) + 1;
        }

        return $stats;
    }

    /**
     * Create delivery items for a delivery
     */
    public function createDeliveryItems($deliveryId, $poItems)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($poItems as $poItem) {
                $data = [
                    'delivery_id' => $deliveryId,
                    'po_item_id' => $poItem['po_item_id'],
                    'qty_delivered' => $poItem['qty_delivered'],
                    'qty_verified' => 0,
                    'kondisi_item' => 'Belum Dicek',
                    'serial_numbers' => json_encode($poItem['serial_numbers'] ?? []),
                    'keterangan' => $poItem['keterangan'] ?? null
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
     * Get delivery items by PO item
     */
    public function getDeliveriesByPOItem($poItemId)
    {
        return $this->select('
                delivery_items.*,
                po_deliveries.delivery_sequence,
                po_deliveries.packing_list_no,
                po_deliveries.expected_date,
                po_deliveries.actual_date,
                po_deliveries.status as delivery_status
            ')
            ->join('po_deliveries', 'po_deliveries.id_delivery = delivery_items.delivery_id')
            ->where('delivery_items.po_item_id', $poItemId)
            ->orderBy('po_deliveries.delivery_sequence', 'ASC')
            ->findAll();
    }
}