<?php

namespace App\Models;

use CodeIgniter\Model;

class POSparepartItemModel extends Model
{
    protected $table            = 'po_sparepart_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_id',
        'sparepart_id',
        'qty',
        'satuan',
        'keterangan',
        'status_verifikasi',  // <-- TAMBAHKAN INI
        'catatan_verifikasi', // <-- DAN INI
    ];

    // Nonaktifkan timestamps karena tidak ada di skema tabel ini
    protected $useTimestamps = false;
}
