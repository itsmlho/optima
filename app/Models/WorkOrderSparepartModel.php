<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderSparepartModel extends Model
{
    protected $table = 'work_order_spareparts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'work_order_id',
        'sparepart_code',
        'sparepart_name',
        'quantity_brought',
        'satuan',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'work_order_id' => 'required|integer',
        'sparepart_code' => 'required|max_length[50]',
        'sparepart_name' => 'required|max_length[255]',
        'quantity_brought' => 'required|integer|greater_than[0]',
        'satuan' => 'required|max_length[50]'
    ];

    protected $validationMessages = [
        'work_order_id' => [
            'required' => 'Work Order ID harus diisi',
            'integer' => 'Work Order ID harus berupa angka'
        ],
        'sparepart_code' => [
            'required' => 'Kode Sparepart harus diisi',
            'max_length' => 'Kode Sparepart maksimal 50 karakter'
        ],
        'sparepart_name' => [
            'required' => 'Nama Sparepart harus diisi',
            'max_length' => 'Nama Sparepart maksimal 255 karakter'
        ],
        'quantity_brought' => [
            'required' => 'Quantity harus diisi',
            'integer' => 'Quantity harus berupa angka',
            'greater_than' => 'Quantity harus lebih dari 0'
        ],
        'satuan' => [
            'required' => 'Satuan harus diisi',
            'max_length' => 'Satuan maksimal 50 karakter'
        ]
    ];

    /**
     * Add multiple spareparts to work order
     */
    public function addSpareparts($workOrderId, $spareparts, $notes = null)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Remove existing spareparts for this work order
            $this->where('work_order_id', $workOrderId)->delete();

            // Insert new spareparts
            $insertData = [];
            foreach ($spareparts as $sparepart) {
                if (!empty($sparepart['sparepart_code']) && !empty($sparepart['quantity_brought'])) {
                    $insertData[] = [
                        'work_order_id' => $workOrderId,
                        'sparepart_code' => $sparepart['sparepart_code'],
                        'sparepart_name' => $sparepart['sparepart_name'] ?? '',
                        'quantity_brought' => $sparepart['quantity_brought'],
                        'satuan' => $sparepart['satuan'] ?? 'pcs',
                        'notes' => $notes
                    ];
                }
            }

            if (!empty($insertData)) {
                $this->insertBatch($insertData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            log_message('error', 'Error in WorkOrderSparepartModel::addSpareparts - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get spareparts for work order
     */
    public function getWorkOrderSpareparts($workOrderId)
    {
        return $this->select('
            wos.*,
            wos.sparepart_code as code,
            wos.sparepart_name as name,
            wos.quantity_brought as qty,
            wos.satuan,
            wos.notes
        ')
        ->where('wos.work_order_id', $workOrderId)
        ->orderBy('wos.id', 'ASC')
        ->findAll();
    }

    /**
     * Remove sparepart from work order
     */
    public function removeSparepart($workOrderId, $sparepartId)
    {
        return $this->where('work_order_id', $workOrderId)
                   ->where('sparepart_id', $sparepartId)
                   ->delete();
    }

    /**
     * Check if sparepart is already assigned to work order
     */
    public function isSparepartAssigned($workOrderId, $sparepartId)
    {
        $sparepart = $this->where('work_order_id', $workOrderId)
                          ->where('sparepart_id', $sparepartId)
                          ->first();

        return $sparepart !== null;
    }
}