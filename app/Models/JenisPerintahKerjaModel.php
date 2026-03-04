<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisPerintahKerjaModel extends Model
{
    protected $table            = 'jenis_perintah_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
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
        'nama' => 'required|max_length[100]',
        'kode' => 'permit_empty|max_length[20]|is_unique[jenis_perintah_kerja.kode,id,{id}]',
    ];
    protected $validationMessages   = [
        'nama' => [
            'required'   => 'Nama jenis perintah kerja harus diisi',
            'max_length' => 'Nama maksimal 100 karakter',
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
     * Get all active types
     */
    public function getActiveTypes(): array
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get type by code
     */
    public function getByCode(string $code): ?array
    {
        return $this->where('kode', $code)->first();
    }
}
