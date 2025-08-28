<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryItemModel extends Model
{
    protected $table = 'delivery_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'di_id','item_type','unit_id','attachment_id','keterangan'
    ];
    
    // Explicitly disable automatic timestamps
    protected $useTimestamps = false;
    protected $createdField = '';  // Disable automatic created_at
    protected $updatedField = '';  // Disable automatic updated_at
}
