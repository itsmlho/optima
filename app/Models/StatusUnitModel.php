<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusUnitModel extends Model
{
    protected $table = 'status_unit';
    protected $primaryKey = 'id_status';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['status_unit'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}