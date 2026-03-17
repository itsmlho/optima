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
        'item_type',
        'quantity_brought',
        'satuan',
        'notes',
        'is_from_warehouse',
        'source_type',
        'source_unit_id',
        'source_notes',
        'quantity_used',
        'is_additional',
        'sparepart_validated'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'work_order_id' => 'required|integer',
        'sparepart_code' => 'permit_empty|max_length[50]',   // NULL allowed for manual entries
        'sparepart_name' => 'required|max_length[255]',
        'item_type' => 'permit_empty|in_list[sparepart,tool]',
        'quantity_brought' => 'required|integer|greater_than[0]',
        'satuan' => 'required|max_length[50]',
        'is_from_warehouse' => 'permit_empty|in_list[0,1]',
        'source_type' => 'permit_empty|in_list[WAREHOUSE,BEKAS,KANIBAL]',
        'source_unit_id' => 'permit_empty|integer',
        'source_notes' => 'permit_empty|max_length[1000]'
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
        'item_type' => [
            'in_list' => 'Item type harus sparepart atau tool'
        ],
        'quantity_brought' => [
            'required' => 'Quantity harus diisi',
            'integer' => 'Quantity harus berupa angka',
            'greater_than' => 'Quantity harus lebih dari 0'
        ],
        'satuan' => [
            'required' => 'Satuan harus diisi',
            'max_length' => 'Satuan maksimal 50 karakter'
        ],
        'source_type' => [
            'in_list' => 'Source type harus WAREHOUSE, BEKAS, atau KANIBAL'
        ],
        'source_unit_id' => [
            'integer' => 'Unit ID harus berupa angka'
        ],
        'source_notes' => [
            'max_length' => 'Catatan sumber maksimal 1000 karakter'
        ]
    ];

    /**
     * Before Insert/Update: Validate KANIBAL requires source_unit_id
     */
    protected $beforeInsert = ['validateKanibalSource'];
    protected $beforeUpdate = ['validateKanibalSource'];

    protected function validateKanibalSource(array $data)
    {
        if (isset($data['data']['source_type']) && $data['data']['source_type'] === 'KANIBAL') {
            if (empty($data['data']['source_unit_id'])) {
                throw new \Exception('Unit sumber harus dipilih untuk sparepart KANIBAL/Copotan');
            }
        }
        return $data;
    }

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
                // Accept rows that have at least a name (manual entries have no sparepart_code)
                $hasIdentifier = !empty($sparepart['sparepart_name']) || !empty($sparepart['sparepart_code']);
                if ($hasIdentifier && !empty($sparepart['quantity_brought'])) {
                    $insertData[] = [
                        'work_order_id'     => $workOrderId,
                        'sparepart_code'    => $sparepart['sparepart_code'] ?? null,
                        'sparepart_name'    => $sparepart['sparepart_name'] ?? '',
                        'item_type'         => $sparepart['item_type'] ?? 'sparepart',
                        'quantity_brought'  => $sparepart['quantity_brought'],
                        'satuan'            => $sparepart['satuan'] ?? 'pcs',
                        'notes'             => $sparepart['notes'] ?? null,
                        'source_type'       => $sparepart['source_type'] ?? 'WAREHOUSE',
                        'is_from_warehouse' => $sparepart['is_from_warehouse'] ?? 1,
                        'source_unit_id'    => $sparepart['source_unit_id'] ?? null,
                        'source_notes'      => $sparepart['source_notes'] ?? null,
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
            wos.item_type,
            wos.quantity_brought as qty,
            wos.satuan,
            wos.notes,
            wos.is_from_warehouse,
            wos.source_type,
            wos.source_unit_id,
            wos.source_notes,
            iu.no_unit as source_unit_number,
            iu.no_unit_na as source_unit_number_alt
        ')
        ->join('inventory_unit iu', 'wos.source_unit_id = iu.id_inventory_unit', 'left')
        ->where('wos.work_order_id', $workOrderId)
        ->orderBy('wos.id', 'ASC')
        ->findAll();
    }
    
    /**
     * Alias for getWorkOrderSpareparts (compatibility)
     */
    public function getSparePartsWithUsage($workOrderId)
    {
        return $this->getWorkOrderSpareparts($workOrderId);
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