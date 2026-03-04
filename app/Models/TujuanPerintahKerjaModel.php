<?php

namespace App\Models;

use CodeIgniter\Model;

class TujuanPerintahKerjaModel extends Model
{
    protected $table            = 'tujuan_perintah_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_tujuan',
        'tujuan',
        'keterangan',
        'is_active',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules      = [
        'nama_tujuan' => 'required|max_length[100]',
        'tujuan'      => 'permit_empty|max_length[50]',
    ];
    protected $validationMessages   = [
        'nama_tujuan' => [
            'required'   => 'Nama tujuan harus diisi',
            'max_length' => 'Nama tujuan maksimal 100 karakter',
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
     * Get all active purposes
     */
    public function getActivePurposes(): array
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get purpose by tujuan code
     */
    public function getByTujuan(string $tujuan): ?array
    {
        return $this->where('tujuan', $tujuan)->first();
    }
}
