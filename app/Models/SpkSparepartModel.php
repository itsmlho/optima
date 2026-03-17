<?php

namespace App\Models;

use CodeIgniter\Model;

class SpkSparepartModel extends Model
{
    protected $table = 'spk_spareparts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'spk_id',
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
        'spk_id' => 'required|integer',
        'sparepart_code' => 'permit_empty|max_length[50]',
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
        'spk_id' => [
            'required' => 'SPK ID harus diisi',
            'integer' => 'SPK ID harus berupa angka'
        ],
        'sparepart_code' => [
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
                throw new \RuntimeException('Source unit harus diisi untuk sparepart KANIBAL');
            }
        }
        return $data;
    }

    /**
     * Add spareparts to SPK (during planning phase)
     *
     * @param int $spkId
     * @param array $spareparts Array of sparepart data
     * @param string|null $notes General notes
     * @return bool
     */
    public function addSpareparts($spkId, $spareparts, $notes = null)
    {
        if (empty($spareparts) || !is_array($spareparts)) {
            return false;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($spareparts as $sparepart) {
                // Filter empty entries
                if (empty($sparepart['sparepart_name']) && empty($sparepart['sparepart_code'])) {
                    continue;
                }

                $data = [
                    'spk_id' => $spkId,
                    'sparepart_code' => !empty($sparepart['sparepart_code']) ? $sparepart['sparepart_code'] : null,
                    'sparepart_name' => $sparepart['sparepart_name'] ?? '',
                    'item_type' => $sparepart['item_type'] ?? 'sparepart',
                    'quantity_brought' => (int)($sparepart['quantity_brought'] ?? 1),
                    'satuan' => $sparepart['satuan'] ?? 'PCS',
                    'notes' => $notes ?? ($sparepart['notes'] ?? null),
                    'is_from_warehouse' => isset($sparepart['is_from_warehouse']) ? (int)$sparepart['is_from_warehouse'] : 1,
                    'source_type' => $sparepart['source_type'] ?? 'WAREHOUSE',
                    'source_unit_id' => !empty($sparepart['source_unit_id']) ? (int)$sparepart['source_unit_id'] : null,
                    'source_notes' => $sparepart['source_notes'] ?? null,
                    'is_additional' => (int)($sparepart['is_additional'] ?? 0),
                    'sparepart_validated' => 0
                ];

                $insertResult = $this->insert($data);
                if ($insertResult === false) {
                    $errors = $this->errors();
                    $errorMsg = implode(', ', $errors);
                    log_message('error', 'SpkSparepartModel insert failed for sparepart "' . ($data['sparepart_name'] ?? '') . '": ' . $errorMsg);
                    $db->transRollback();
                    return false;
                }
            }

            $db->transComplete();
            return $db->transStatus();

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error adding SPK spareparts: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all spareparts for a specific SPK with source tracking
     *
     * @param int $spkId
     * @return array
     */
    public function getSpkSpareparts($spkId)
    {
        return $this->select('
                spk_spareparts.*,
                iu.no_unit as source_unit_number,
                iu.id_inventory_unit as source_unit_id_inventory,
                mu.merk_unit as source_merk,
                mu.model_unit as source_model
            ')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = spk_spareparts.source_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->where('spk_id', $spkId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get sparepart return detail from spk_sparepart_returns
     */
    public function getReturnDetail($id)
    {
        $db = \Config\Database::connect();
        return $db->table('spk_sparepart_returns ssr')
            ->select('
                ssr.*,
                spk.no_spk as spk_number,
                spk.tgl_spk as spk_date,
                c.customer_name,
                iu.no_unit as unit_number,
                u.username as confirmed_by_name
            ')
            ->join('spk_spareparts ss', 'ss.id = ssr.spk_sparepart_id', 'left')
            ->join('spk', 'spk.id = ssr.spk_id', 'left')
            ->join('customers c', 'c.id = spk.customer_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = spk.unit_id', 'left')
            ->join('users u', 'u.id = ssr.confirmed_by', 'left')
            ->where('ssr.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Confirm a sparepart return
     */
    public function confirmReturn($id, $userId, $notes = null)
    {
        $db = \Config\Database::connect();
        $data = [
            'status'       => 'CONFIRMED',
            'confirmed_by' => $userId,
            'confirmed_at' => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];
        if ($notes !== null) {
            $data['return_notes'] = $notes;
        }

        return $db->table('spk_sparepart_returns')
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Remove a sparepart from SPK (before validation)
     */
    public function removeSparepart($spkId, $sparepartId)
    {
        return $this->where('spk_id', $spkId)
                   ->where('id', $sparepartId)
                   ->where('sparepart_validated', 0) // Only allow removal before validation
                   ->delete();
    }

    /**
     * Check if SPK has validated spareparts
     */
    public function hasValidatedSpareparts($spkId)
    {
        return $this->where('spk_id', $spkId)
                    ->where('sparepart_validated', 1)
                    ->countAllResults() > 0;
    }
}
