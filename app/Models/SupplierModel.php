<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'suppliers';     // Nama table
    protected $primaryKey       = 'id_supplier';               // Primary key

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';               // Bisa 'object' juga
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id_supplier',
        'nama_supplier',
        'kontak_person',
        'telepon',
        'alamat',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;   // Auto set created_at & updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Optional: Validation
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
