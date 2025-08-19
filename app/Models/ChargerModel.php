<?php

namespace App\Models;

use CodeIgniter\Model;

class ChargerModel extends Model
{
    protected $table = 'charger';
    protected $primaryKey = 'id_charger';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['merk_charger','tipe_charger'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}