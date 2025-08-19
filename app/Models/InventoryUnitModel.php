<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryUnitModel extends Model
{
    protected $table            = 'inventory_unit';
    // Internal PK (auto increment) setelah migrasi opsi 2: rename kolom lama no_unit -> id_inventory_unit
    protected $primaryKey       = 'id_inventory_unit';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
    'serial_number',
    'no_unit', // nomor aset manual (nullable & unik) diisi saat konversi jadi aset
        'id_po',
        'tahun_unit',
        'status_unit_id',
        'status_aset',
        'lokasi_unit',
        'departemen_id',
        'tanggal_kirim',
        'keterangan',
        'tipe_unit_id',
        'model_unit_id',
        'kapasitas_unit_id',
        'model_mast_id',
        'tinggi_mast',
        'sn_mast',
        'model_mesin_id',
        'sn_mesin',
        'model_attachment_id',
        'sn_attachment',
        'model_baterai_id',
        'sn_baterai',
        'model_charger_id',
        'sn_charger',
        'roda_id',
        'ban_id',
        'valve_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules = [
        'serial_number'  => 'permit_empty|max_length[255]',
        'status_unit_id' => 'required|integer',
    ];
    protected $validationMessages = [
        'status_unit_id' => [
            'required' => 'Status unit harus diisi',
            'integer'  => 'Status unit harus berupa angka'
        ]
    ];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    // Mapping lama->baru untuk kompatibilitas (dipakai di SELECT alias)
    // id_inventory_unit -> no_unit, serial_number_po -> serial_number, status_unit -> status_unit_id, tanggal_masuk -> created_at

    /** Check if a unit is available as stock (status_unit_id in [7,8]). */
    public function isStockAvailable(int $unitId): bool
    {
        $row = $this->select('status_unit_id')->where('id_inventory_unit', $unitId)->first();
        return $row && in_array((int)$row['status_unit_id'], [7,8], true);
    }

    /** Get basic unit info including no_unit for labelling. */
    public function getBasic(int $unitId): ?array
    {
        return $this->select('id_inventory_unit, no_unit, serial_number, lokasi_unit')->where('id_inventory_unit', $unitId)->first();
    }

    /**
     * Update the unit with attachment model/sn based on an inventory_attachment row.
     * Best-effort: if inventory attachment not found, nothing happens.
     */
    public function attachAttachmentFromInventoryAttachment(int $unitId, int $inventoryAttachmentId): bool
    {
        $attRow = $this->db->table('inventory_attachment')->select('attachment_id, sn_attachment')->where('id_inventory_attachment', $inventoryAttachmentId)->get()->getRowArray();
        if (!$attRow) return false;
        // Use available columns: store linked attachment model/SN into unit extra fields if exist
        $payload = [
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($this->db->getFieldData('inventory_unit')) {
            // blindly set common fields if they exist
            $fields = array_column($this->db->getFieldData('inventory_unit'), 'name');
            if (in_array('model_attachment_id', $fields, true)) $payload['model_attachment_id'] = $attRow['attachment_id'] ?? null;
            if (in_array('sn_attachment', $fields, true)) $payload['sn_attachment'] = $attRow['sn_attachment'] ?? null;
        }
        return (bool)$this->update($unitId, $payload);
    }

    public function getDataTable($start, $length, $orderColumn, $orderDir, $searchValue, $statusFilter = null, $departemenFilter = null, $lokasiFilter = null)
    {
        try {
            $builder = $this->db->table($this->table . ' as iu');
            $builder->select('iu.id_inventory_unit,
                              iu.no_unit as no_unit,
                              iu.no_unit as nomor_aset,
                              iu.serial_number as serial_number_po,
                              iu.status_unit_id as status_unit,
                              COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                              COALESCE(mu.model_unit, "Unknown") as model_unit,
                              COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "Unknown") as nama_tipe_unit,
                              COALESCE(su.status_unit, CASE 
                                  WHEN iu.status_unit_id = 7 THEN "STOCK ASET"
                                  WHEN iu.status_unit_id = 3 THEN "RENTAL"
                                  WHEN iu.status_unit_id = 9 THEN "JUAL"
                                  WHEN iu.status_unit_id = 2 THEN "WORKSHOP-RUSAK"
                                  ELSE CONCAT("Status ", iu.status_unit_id)
                              END) as status_unit_name,
                              iu.departemen_id,
                              COALESCE(d.nama_departemen, "-") as nama_departemen,
                              iu.lokasi_unit,
                              iu.created_at as tanggal_masuk');
            $tableExists = $this->checkTablesExist(['model_unit', 'tipe_unit', 'status_unit', 'departemen']);
            if ($tableExists['model_unit']) {
                $builder->join('model_unit as mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
            }
            if ($tableExists['tipe_unit']) {
                $builder->join('tipe_unit as tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
            }
            if ($tableExists['status_unit']) {
                $builder->join('status_unit as su', 'su.id_status = iu.status_unit_id', 'left');
            }
            if ($tableExists['departemen']) {
                $builder->join('departemen as d', 'd.id_departemen = iu.departemen_id', 'left');
            }
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('iu.serial_number', $searchValue)
                    ->orLike('iu.lokasi_unit', $searchValue);
                if ($tableExists['model_unit']) {
                    $builder->orLike('mu.merk_unit', $searchValue)
                            ->orLike('mu.model_unit', $searchValue);
                }
                if ($tableExists['tipe_unit']) {
                    $builder->orLike('tu.tipe', $searchValue)
                            ->orLike('tu.jenis', $searchValue);
                }
                if ($tableExists['status_unit']) {
                    $builder->orLike('su.status_unit', $searchValue);
                }
                if ($tableExists['departemen']) {
                    $builder->orLike('d.nama_departemen', $searchValue);
                }
                $builder->groupEnd();
            }
            if ($statusFilter !== null && $statusFilter !== '') {
                $builder->where('iu.status_unit_id', $statusFilter);
            }
            if ($departemenFilter !== null && $departemenFilter !== '') {
                $builder->where('iu.departemen_id', $departemenFilter);
            }
            if ($lokasiFilter !== null && $lokasiFilter !== '') {
                // gunakan LIKE untuk fleksibilitas (mis: POS 1, POS 2)
                $builder->like('iu.lokasi_unit', $lokasiFilter);
            }
            // Whitelist order column to prevent SQL error
            // Kolom order disesuaikan: nama_tipe_unit tidak fisik -> gunakan tu.tipe sebagai fallback
            $allowedOrder = ['iu.no_unit','iu.id_inventory_unit','iu.serial_number','mu.merk_unit','mu.model_unit','tu.tipe','d.nama_departemen','su.status_unit','iu.lokasi_unit','iu.created_at'];
            if (!in_array($orderColumn, $allowedOrder, true)) {
                log_message('warning', 'Invalid order column received: ' . $orderColumn . ' fallback to iu.created_at');
                $orderColumn = 'iu.created_at';
            }
            $orderDir = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';
            $builder->orderBy($orderColumn, $orderDir)->limit($length, $start);
            $result = $builder->get()->getResultArray();
            return $result;
        } catch (\Throwable $e) {
            log_message('error', '[InventoryUnitModel::getDataTable] Exception: ' . $e->getMessage());
            try { log_message('error', 'Last query: ' . ($this->db->getLastQuery() ?? 'N/A')); } catch (\Throwable $ie) { }
            return [];
        }
    }

    public function countAllData()
    {
        return $this->db->table($this->table)->countAllResults();
    }

    public function countFiltered($searchValue, $statusFilter = null, $departemenFilter = null, $lokasiFilter = null)
    {
        try {
            $builder = $this->db->table($this->table . ' as iu');
            $tableExists = $this->checkTablesExist(['model_unit', 'tipe_unit', 'status_unit', 'departemen']);
            if ($tableExists['model_unit']) {
                $builder->join('model_unit as mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
            }
            if ($tableExists['tipe_unit']) {
                $builder->join('tipe_unit as tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
            }
            if ($tableExists['status_unit']) {
                $builder->join('status_unit as su', 'su.id_status = iu.status_unit_id', 'left');
            }
            if ($tableExists['departemen']) {
                $builder->join('departemen as d', 'd.id_departemen = iu.departemen_id', 'left');
            }
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('iu.serial_number', $searchValue)
                    ->orLike('iu.lokasi_unit', $searchValue);
                if ($tableExists['model_unit']) {
                    $builder->orLike('mu.merk_unit', $searchValue)
                            ->orLike('mu.model_unit', $searchValue);
                }
                if ($tableExists['tipe_unit']) {
                    $builder->orLike('tu.tipe', $searchValue)
                            ->orLike('tu.jenis', $searchValue);
                }
                if ($tableExists['status_unit']) {
                    $builder->orLike('su.status_unit', $searchValue);
                }
                if ($tableExists['departemen']) {
                    $builder->orLike('d.nama_departemen', $searchValue);
                }
                $builder->groupEnd();
            }
            if ($statusFilter !== null && $statusFilter !== '') {
                $builder->where('iu.status_unit_id', $statusFilter);
            }
            if ($departemenFilter !== null && $departemenFilter !== '') {
                $builder->where('iu.departemen_id', $departemenFilter);
            }
            if ($lokasiFilter !== null && $lokasiFilter !== '') {
                $builder->like('iu.lokasi_unit', $lokasiFilter);
            }
            return $builder->countAllResults();
        } catch (\Throwable $e) {
            log_message('error', '[InventoryUnitModel::countFiltered] Exception: ' . $e->getMessage());
            try { log_message('error', 'Last query: ' . ($this->db->getLastQuery() ?? 'N/A')); } catch (\Throwable $ie) { }
            return 0;
        }
    }

    public function getStats()
    {
    $total     = $this->db->table($this->table)->countAllResults();
    $in_stock  = $this->db->table($this->table)->where('status_unit_id', 7)->countAllResults();
    $non_asset = $this->db->table($this->table)->where('status_unit_id', 8)->countAllResults();
    $rented    = $this->db->table($this->table)->where('status_unit_id', 3)->countAllResults();
    $sold      = $this->db->table($this->table)->where('status_unit_id', 9)->countAllResults();
    $workshop  = $this->db->table($this->table)->where('status_unit_id', 2)->countAllResults();

        return [
            'total'     => $total,
            'in_stock'  => $in_stock,
            'non_asset' => $non_asset,
            'rented'    => $rented,
            'sold'      => $sold,
            'workshop'  => $workshop,
        ];
    }

    private function checkTablesExist($tables)
    {
        $result = [];
        foreach ($tables as $table) {
            try {
                $query = $this->db->query("SHOW TABLES LIKE '{$table}'");
                $result[$table] = $query->getNumRows() > 0;
            } catch (\Exception $e) {
                $result[$table] = false;
                log_message('debug', "Table {$table} does not exist: " . $e->getMessage());
            }
        }
        return $result;
    }
}