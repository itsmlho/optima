<?php

namespace App\Models;

use CodeIgniter\Model;

class MesinModel extends Model
{
    protected $table = 'mesin';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['merk_mesin','model_mesin','bahan_bakar'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}