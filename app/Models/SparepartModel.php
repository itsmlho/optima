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

    /**
     * Get active spareparts for dropdown
     */
    public function getActiveSpareparts()
    {
        return $this->select('id_sparepart as id, CONCAT(kode, " - ", desc_sparepart) as text')
                    ->orderBy('desc_sparepart', 'ASC')
                    ->findAll();
    }
}