<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryItemModel extends Model
{
    protected $table = 'delivery_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'di_id','item_type','unit_id','attachment_id','qty','keterangan'
    ];
    protected $useTimestamps = false;
}
