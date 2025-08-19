<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchasingModel extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id_po';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields    = [
        'no_po',
        'tanggal_po',
        'supplier_id',
        'invoice_no',
        'invoice_date',
        'bl_date',
        'keterangan_po',
        'tipe_po',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'no_po' => 'required|max_length[50]',
        'tanggal_po' => 'required|valid_date',
        'supplier_id' => 'required|integer',
        'invoice_no' => 'permit_empty',
        'invoice_date' => 'permit_empty|valid_date',
        'bl_date' => 'permit_empty|valid_date',
        'tipe_po' => 'required|in_list[Unit,Attachment & Battery,Sparepart,Dinamis]',
        // PERBAIKAN: Tambahkan 'Selesai dengan Catatan' ke dalam daftar yang diizinkan
        'status' => 'permit_empty|in_list[pending,approved,completed,cancelled,Selesai dengan Catatan]',
    ];

    // protected $validationMessages = [
    //     'po_number' => [
    //         'is_unique' => 'PO number sudah ada dalam sistem.'
    //     ]
    // ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    function countPO($where = []): int
    {
        return empty($where)
        ? $this->countAll()
        : $this->where($where)->countAllResults();
    }

    function getNextPONumber($tipe = "Unit")
    {
        // Get the last PO number for this type
        $get_last_number = $this->select("no_po")
            ->where("tipe_po", $tipe)
            ->like('no_po', 'PO-' . $tipe . '-' . date("Y-m-"), 'after') // Only get current month
            ->orderBy('no_po', "DESC")
            ->get()
            ->getRowArray();
        
        if ($get_last_number) {
            $explode_no_po = explode("-", $get_last_number["no_po"]);
            $last_number_po = (int)end($explode_no_po);
            $new_number = $last_number_po + 1;
        } else {
            // If no previous PO found for current month, start from 1
            $new_number = 1;
        }
        
        $new_po_number = "PO-" . $tipe . "-" . date("Y-m-") . sprintf("%04d", $new_number);
        
        // Check if this number already exists (extra safety)
        while ($this->where('no_po', $new_po_number)->countAllResults() > 0) {
            $new_number++;
            $new_po_number = "PO-" . $tipe . "-" . date("Y-m-") . sprintf("%04d", $new_number);
        }
        
        return $new_po_number;
    }
}
