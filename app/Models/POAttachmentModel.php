<?php

namespace App\Models;

use CodeIgniter\Model;

class POAttachmentModel extends Model
{
    protected $table = 'po_attachment';
    protected $primaryKey = 'id_po_attachment';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    // Daftar kolom yang diizinkan
    protected $allowedFields = [
        'po_id',
        'item_type',
        'item_id',
        'qty_ordered',
        'qty_received',
        'harga_satuan',
        'total_harga',
        'attachment_id',
        'baterai_id',
        'charger_id',
        'serial_number',
        'serial_number_charger',
        'keterangan',
        'status_verifikasi',
        'catatan_verifikasi',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getPOStats()
    {
        $stats = [
            'total'         => $this->countAllResults(false), // false agar tidak me-reset query builder
            'belum_dicek'   => $this->where('status_verifikasi', 'Belum Dicek')->countAllResults(false),
            'sesuai'        => $this->where('status_verifikasi', 'Sesuai')->countAllResults(false),
            'tidak_sesuai'  => $this->where('status_verifikasi', 'Tidak Sesuai')->countAllResults(false),
        ];
        // Reset query builder setelah selesai
        $this->resetQuery();
        return $stats;
    }

    /**
     * Mengambil opsi status verifikasi yang tersedia.
     * @return array
     */
    public function getVerificationStatusOptions()
    {
        // Ambil dari tipe ENUM di kolom status_verifikasi
        $query = "SHOW COLUMNS FROM po_attachment LIKE 'status_verifikasi'";
        $row = $this->db->query($query)->getRow()->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $row, $matches);
        $enum = explode("','", $matches[1]);
        return $enum;
    }
}
