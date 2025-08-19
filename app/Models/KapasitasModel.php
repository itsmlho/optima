<?php

namespace App\Models;

use CodeIgniter\Model;

class KapasitasModel extends Model
{
    protected $table = 'kapasitas';
    protected $primaryKey = 'id_kapasitas';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['kapasitas_unit'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}