<?php

namespace App\Models;

use CodeIgniter\Model;

class PositionModel extends Model
{
    protected $table = 'positions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'code', 'description', 'division_id', 'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'code' => 'required|max_length[20]|is_unique[positions.code,id,{id}]',
        'description' => 'permit_empty',
        'division_id' => 'permit_empty|integer',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama posisi wajib diisi',
            'max_length' => 'Nama posisi maksimal 100 karakter'
        ],
        'code' => [
            'required' => 'Kode posisi wajib diisi',
            'max_length' => 'Kode posisi maksimal 20 karakter',
            'is_unique' => 'Kode posisi sudah digunakan'
        ],
        'division_id' => [
            'integer' => 'ID divisi harus berupa angka'
        ],
        'is_active' => [
            'in_list' => 'Status aktif harus 0 atau 1'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Create or update position
     */
    public function createOrUpdatePosition(array $data): int
    {
        $existing = $this->where('code', $data['code'])->first();
        if ($existing) {
            $this->update($existing['id'], $data);
            return $existing['id'];
        } else {
            $this->insert($data);
            return $this->getInsertID();
        }
    }

    /**
     * Get positions by division
     */
    public function getByDivision(int $divisionId): array
    {
        return $this->where('division_id', $divisionId)
                   ->where('is_active', 1)
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    /**
     * Get active positions
     */
    public function getActive(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }
}
