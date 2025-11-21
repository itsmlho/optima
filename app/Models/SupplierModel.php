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
        'kode_supplier',
        'nama_supplier',
        'alias',
        'contact_person',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'npwp',
        'business_type',
        'payment_terms',
        'credit_limit',
        'currency',
        'product_categories',
        'rating',
        'total_orders',
        'total_value',
        'on_time_delivery_rate',
        'quality_score',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'is_verified',
        'notes',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;   // Auto set created_at & updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Optional: Validation
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
