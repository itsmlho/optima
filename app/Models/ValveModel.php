<?php

namespace App\Models;

use CodeIgniter\Model;

class ValveModel extends Model
{
    protected $table = 'valve';
    protected $primaryKey = 'id_valve';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['jumlah_valve'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}