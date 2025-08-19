<?php

namespace App\Models;

use CodeIgniter\Model;

class BateraiModel extends Model
{
    protected $table = 'baterai';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['merk_baterai','tipe_baterai','jenis_baterai'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}