<?php

namespace App\Models;

use CodeIgniter\Model;

class AttachmentModel extends Model
{
    protected $table = 'attachment';
    protected $primaryKey = 'id_attachment';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    // Updated to match new schema
    protected $allowedFields = ['tipe','merk','model'];

    // Dates
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}