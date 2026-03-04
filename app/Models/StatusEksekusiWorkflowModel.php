<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusEksekusiWorkflowModel extends Model
{
    protected $table            = 'status_eksekusi_workflow';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_status',
        'kode',
        'keterangan',
        'is_active',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules      = [
        'nama_status' => 'required|max_length[100]',
        'kode'        => 'permit_empty|max_length[20]|is_unique[status_eksekusi_workflow.kode,id,{id}]',
    ];
    protected $validationMessages   = [
        'nama_status' => [
            'required'   => 'Nama status harus diisi',
            'max_length' => 'Nama status maksimal 100 karakter',
        ],
        'kode' => [
            'is_unique' => 'Kode sudah digunakan',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all active statuses
     */
    public function getActiveStatuses(): array
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get status by code
     */
    public function getByCode(string $code): ?array
    {
        return $this->where('kode', $code)->first();
    }
}
