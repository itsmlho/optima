<?php

namespace App\Models;

use CodeIgniter\Model;

class TipeUnitModel extends Model
{
    protected $table = 'tipe_unit';
    protected $primaryKey = 'id_tipe_unit';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    // Table columns based on schema: id_tipe_unit, tipe, jenis, id_departemen
    protected $allowedFields = ['tipe','jenis','id_departemen'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}