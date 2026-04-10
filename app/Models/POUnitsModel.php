<?php

namespace App\Models;

use CodeIgniter\Model;

class POUnitsModel extends Model
{
    protected $table = 'po_units';
    protected $primaryKey = 'id_po_unit';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'po_id',
        'jenis_unit',
        'status_verifikasi',
        'merk_unit',
        'model_unit_id',
        'tipe_unit_id',
        'serial_number_po',
        'tahun_po',
        'kapasitas_id',
        'mast_id',
        'tinggi_mast_po',  // Added for consistency with inventory_unit.tinggi_mast
        'sn_mast_po',
        'mesin_id',
        'sn_mesin_po',
        'attachment_id',
        'sn_attachment_po',
        'baterai_id',
        'sn_baterai_po',
        'charger_id',
        'sn_charger_po',
        'sn_fork_po',
        'ban_id',
        'roda_id',
        'valve_id',
        'fork_id',
        'status_penjualan',
        'keterangan',
        'catatan_verifikasi',
        'vendor_model_code',
        'vendor_spec_text',
        'package_flags',
        'unit_accessories',
        'po_line_group_id',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'po_id'             => 'required|integer',
        'jenis_unit'        => 'permit_empty|integer',
        'merk_unit'         => 'required|max_length[100]',
        'model_unit_id'     => 'permit_empty|integer',
        'tipe_unit_id'      => 'permit_empty|integer',
        'tahun_po'          => 'permit_empty|integer|greater_than[1900]',
        'kapasitas_id'      => 'permit_empty|integer',
        'status_penjualan'  => 'permit_empty|in_list[Baru,Bekas,Rekondisi]',
        'status_verifikasi' => 'permit_empty|in_list[Belum Dicek,Sesuai,Tidak Sesuai]',
        'mast_id'           => 'permit_empty|integer',
        'tinggi_mast_po'    => 'permit_empty|max_length[50]',
        'mesin_id'          => 'permit_empty|integer',
        'attachment_id'     => 'permit_empty|integer',
        'baterai_id'        => 'permit_empty|integer',
        'charger_id'        => 'permit_empty|integer',
        'ban_id'            => 'permit_empty|integer',
        'roda_id'           => 'permit_empty|integer',
        'valve_id'          => 'permit_empty|integer',
        'fork_id'           => 'permit_empty|integer',
        'serial_number_po'  => 'permit_empty|max_length[100]',
        'sn_mast_po'        => 'permit_empty|max_length[100]',
        'sn_mesin_po'       => 'permit_empty|max_length[100]',
        'sn_attachment_po'  => 'permit_empty|max_length[100]',
        'sn_baterai_po'     => 'permit_empty|max_length[100]',
        'sn_charger_po'     => 'permit_empty|max_length[100]',
        'sn_fork_po'        => 'permit_empty|max_length[100]',
        'keterangan'         => 'permit_empty|max_length[65535]',
        'catatan_verifikasi' => 'permit_empty|max_length[500]',
        'vendor_model_code'  => 'permit_empty|max_length[120]',
        'vendor_spec_text'   => 'permit_empty',
        'package_flags'      => 'permit_empty|max_length[255]',
        'unit_accessories'   => 'permit_empty|max_length[65535]',
        'po_line_group_id'   => 'permit_empty|max_length[36]',
    ];

    protected $validationMessages = [
        'po_id' => [
            'required' => 'PO ID harus diisi',
            'integer'  => 'PO ID harus berupa angka'
        ],
        'merk_unit' => [
            'required' => 'Brand unit harus dipilih',
            'integer'  => 'Brand unit harus berupa angka'
        ],
        'jenis_unit' => [
            'required' => 'Jenis unit harus dipilih',
            'integer'  => 'Jenis unit harus berupa angka'
        ],
        'model_unit_id' => [
            'required' => 'Model unit harus dipilih',
            'integer'  => 'Model unit harus berupa angka'
        ],
        'tipe_unit_id' => [
            'required' => 'Tipe unit harus dipilih',
            'integer'  => 'Tipe unit harus berupa angka'
        ],
        'tahun_po' => [
            'required' => 'Tahun unit harus diisi',
            'integer'  => 'Tahun unit harus berupa angka',
            'greater_than' => 'Tahun unit harus lebih dari 1900'
        ],
        'kapasitas_id' => [
            'required' => 'Kapasitas unit harus dipilih',
            'integer'  => 'Kapasitas unit harus berupa angka'
        ],
        'status_penjualan' => [
            'required' => 'Kondisi penjualan harus dipilih',
            'in_list'  => 'Kondisi penjualan harus salah satu dari: Baru, Bekas, Rekondisi'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}