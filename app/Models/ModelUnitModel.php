<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelUnitModel extends Model
{
    protected $table = 'model_unit';
    protected $primaryKey = 'id_model_unit';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['merk_unit', 'model_unit', 'bahan_bakar', 'departemen_id'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}