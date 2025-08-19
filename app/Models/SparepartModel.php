<?php

namespace App\Models;

use CodeIgniter\Model;

class SparepartModel extends Model
{
    protected $table            = 'sparepart';
    protected $primaryKey       = 'id_sparepart';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['kode', 'desc_sparepart'];
    protected $useTimestamps    = true;
}