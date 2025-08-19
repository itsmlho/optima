<?php

namespace App\Models;

use CodeIgniter\Model;

class SpkStatusHistoryModel extends Model
{
    protected $table = 'spk_status_history';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'spk_id','status_from','status_to','changed_by','note','changed_at'
    ];
    public $useTimestamps = false;
}
