<?php

namespace App\Models;

use CodeIgniter\Model;

class TipeMastModel extends Model
{
    protected $table = 'tipe_mast';
    protected $primaryKey = 'id_mast';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['tipe_mast', 'tinggi_mast'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}