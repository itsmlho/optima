<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderSparepartUsageModel extends Model
{
    protected $table            = 'work_order_sparepart_usage';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'work_order_sparepart_id',
        'work_order_id',
        'quantity_used',
        'quantity_returned',
        'usage_notes',
        'return_notes',
        'used_at',
        'returned_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'work_order_sparepart_id' => 'required|integer',
        'work_order_id' => 'required|integer',
        'quantity_used' => 'required|integer|greater_than_equal_to[0]',
        'quantity_returned' => 'integer|greater_than_equal_to[0]'
    ];
    
    protected $validationMessages   = [
        'work_order_sparepart_id' => [
            'required' => 'Sparepart ID harus diisi',
            'integer' => 'Sparepart ID harus berupa angka'
        ],
        'work_order_id' => [
            'required' => 'Work Order ID harus diisi',
            'integer' => 'Work Order ID harus berupa angka'
        ],
        'quantity_used' => [
            'required' => 'Kuantitas yang digunakan harus diisi',
            'integer' => 'Kuantitas harus berupa angka',
            'greater_than_equal_to' => 'Kuantitas tidak boleh negatif'
        ],
        'quantity_returned' => [
            'integer' => 'Kuantitas return harus berupa angka',
            'greater_than_equal_to' => 'Kuantitas return tidak boleh negatif'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setUsedAt'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setReturnedAt'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set used_at timestamp on insert
     */
    protected function setUsedAt(array $data)
    {
        if (isset($data['data']['quantity_used']) && $data['data']['quantity_used'] > 0) {
            $data['data']['used_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Set returned_at timestamp on update
     */
    protected function setReturnedAt(array $data)
    {
        if (isset($data['data']['quantity_returned']) && $data['data']['quantity_returned'] > 0) {
            $data['data']['returned_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Record sparepart usage
     */
    public function recordUsage($workOrderSparepartId, $workOrderId, $quantityUsed, $usageNotes = null)
    {
        $data = [
            'work_order_sparepart_id' => $workOrderSparepartId,
            'work_order_id' => $workOrderId,
            'quantity_used' => $quantityUsed,
            'usage_notes' => $usageNotes,
            'used_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    /**
     * Record multiple sparepart usage (for work order completion)
     */
    public function recordMultipleUsage($workOrderId, $usageData)
    {
        if (empty($usageData)) {
            return true;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($usageData as $usage) {
                $data = [
                    'work_order_sparepart_id' => $usage['sparepart_id'],
                    'work_order_id' => $workOrderId,
                    'quantity_used' => $usage['quantity_used'],
                    'quantity_returned' => $usage['quantity_returned'] ?? 0,
                    'usage_notes' => $usage['usage_notes'] ?? null,
                    'return_notes' => $usage['return_notes'] ?? null,
                    'used_at' => date('Y-m-d H:i:s'),
                    'returned_at' => isset($usage['quantity_returned']) && $usage['quantity_returned'] > 0 ? date('Y-m-d H:i:s') : null
                ];

                $this->insert($data);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error recording multiple usage: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update return information
     */
    public function updateReturn($id, $quantityReturned, $returnNotes = null)
    {
        $data = [
            'quantity_returned' => $quantityReturned,
            'return_notes' => $returnNotes,
            'returned_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($id, $data);
    }

    /**
     * Get usage by work order
     */
    public function getUsageByWorkOrder($workOrderId)
    {
        return $this->select('work_order_sparepart_usage.*, work_order_spareparts.sparepart_code, work_order_spareparts.sparepart_name, work_order_spareparts.quantity_brought, work_order_spareparts.satuan')
                   ->join('work_order_spareparts', 'work_order_spareparts.id = work_order_sparepart_usage.work_order_sparepart_id')
                   ->where('work_order_sparepart_usage.work_order_id', $workOrderId)
                   ->orderBy('work_order_spareparts.sparepart_code', 'ASC')
                   ->findAll();
    }

    /**
     * Get usage summary for work order
     */
    public function getUsageSummary($workOrderId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                COUNT(*) as total_items,
                SUM(quantity_used) as total_used,
                SUM(quantity_returned) as total_returned,
                COUNT(CASE WHEN used_at IS NOT NULL THEN 1 END) as items_used,
                COUNT(CASE WHEN returned_at IS NOT NULL THEN 1 END) as items_returned
            FROM work_order_sparepart_usage 
            WHERE work_order_id = ?
        ", [$workOrderId]);

        return $query->getRowArray();
    }

    /**
     * Check if sparepart has been used
     */
    public function isSparepartUsed($workOrderSparepartId)
    {
        $usage = $this->where('work_order_sparepart_id', $workOrderSparepartId)->first();
        return $usage !== null && $usage['quantity_used'] > 0;
    }

    /**
     * Get pending returns (used but not returned)
     */
    public function getPendingReturns($workOrderId)
    {
        return $this->select('work_order_sparepart_usage.*, work_order_spareparts.sparepart_code, work_order_spareparts.sparepart_name, work_order_spareparts.quantity_brought, work_order_spareparts.satuan')
                   ->join('work_order_spareparts', 'work_order_spareparts.id = work_order_sparepart_usage.work_order_sparepart_id')
                   ->where('work_order_sparepart_usage.work_order_id', $workOrderId)
                   ->where('work_order_sparepart_usage.quantity_used >', 0)
                   ->where('work_order_sparepart_usage.returned_at IS NULL')
                   ->orderBy('work_order_spareparts.sparepart_code', 'ASC')
                   ->findAll();
    }
}