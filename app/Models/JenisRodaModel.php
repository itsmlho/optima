<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisRodaModel extends Model
{
    protected $table = 'jenis_roda';
    protected $primaryKey = 'id_roda';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['tipe_roda'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}