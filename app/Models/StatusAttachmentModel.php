<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusAttachmentModel extends Model
{
    protected $table            = 'status_attachment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_status',
        'kode',
        'keterangan',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [
        'nama_status' => 'required|max_length[100]',
        'kode'        => 'permit_empty|max_length[50]',
    ];
    protected $validationMessages   = [
        'nama_status' => [
            'required'   => 'Nama status harus diisi',
            'max_length' => 'Nama status maksimal 100 karakter',
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
     * Get all attachment statuses
     */
    public function getAllStatuses(): array
    {
        return $this->findAll();
    }

    /**
     * Get status by code
     */
    public function getByCode(string $code): ?array
    {
        return $this->where('kode', $code)->first();
    }
}
