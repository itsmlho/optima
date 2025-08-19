<?php

namespace App\Models;

use CodeIgniter\Model;

class TipeBanModel extends Model
{
    protected $table = 'tipe_ban';
    protected $primaryKey = 'id_ban';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['tipe_ban'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}