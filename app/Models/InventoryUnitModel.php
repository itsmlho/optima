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
        'no_unit_na', // nomor non-asset (NA-001 to NA-500) dengan gap-filling strategy
        'id_po',
        'tahun_unit',
        'status_unit_id',
        'lokasi_unit',
        'departemen_id',
        'tanggal_kirim',
        'keterangan',
        'harga_sewa_bulanan', // Harga sewa per bulan untuk kontrak
        'harga_sewa_harian',  // Harga sewa per hari untuk kontrak
        'kontrak_id',  // Foreign key ke tabel kontrak
        'customer_id',
        'customer_location_id',
        'area_id',
        'kontrak_spesifikasi_id', // Foreign key ke kontrak_spesifikasi
        'tipe_unit_id',
        'model_unit_id',
        'kapasitas_unit_id',
        'model_mast_id',
        'tinggi_mast',
        'sn_mast',
        'model_mesin_id',
        'sn_mesin',
        'roda_id',
        'ban_id',
        'valve_id',
        'aksesoris',
        'spk_id',
        'delivery_instruction_id',
        'di_workflow_id',
        'workflow_status',
        'contract_disconnect_date',
        'contract_disconnect_stage',
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

    /**
     * Apply status filter (supports single or multiple comma-separated values)
     */
    private function applyStatusFilter($builder, $statusFilter)
    {
        if ($statusFilter !== null && $statusFilter !== '') {
            // Support multiple status IDs separated by comma
            if (strpos($statusFilter, ',') !== false) {
                $statusIds = array_map('trim', explode(',', $statusFilter));
                $statusIds = array_filter($statusIds, 'is_numeric'); // Only numeric values
                if (!empty($statusIds)) {
                    $builder->whereIn('iu.status_unit_id', $statusIds);
                }
            } else {
                $builder->where('iu.status_unit_id', $statusFilter);
            }
        }
        return $builder;
    }

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

    public function getDataTable($start, $length, $orderColumn, $orderDir, $searchValue, $statusFilter = null, $departemenFilter = null)
    {
        try {
            $builder = $this->db->table($this->table . ' as iu');
            $builder->select('iu.id_inventory_unit,
                              COALESCE(iu.no_unit, iu.no_unit_na) as no_unit,
                              iu.no_unit as nomor_aset,
                              iu.no_unit_na,
                              iu.serial_number as serial_number_po,
                              iu.status_unit_id as status_unit,
                              iu.status_unit_id as status_unit_id,
                              COALESCE(c.customer_name, "Belum Ada Kontrak") as pelanggan,
                              COALESCE(cl.location_name, iu.lokasi_unit, "Lokasi Tidak Diketahui") as lokasi,
                              COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                              COALESCE(mu.model_unit, "Unknown") as model_unit,
                              COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "Unknown") as nama_tipe_unit,
                              COALESCE(su.status_unit, CASE 
                                  WHEN iu.status_unit_id = 1 THEN "AVAILABLE_STOCK"
                                  WHEN iu.status_unit_id = 2 THEN "STOCK_NON_ASET"
                                  WHEN iu.status_unit_id = 3 THEN "BOOKED"
                                  WHEN iu.status_unit_id = 4 THEN "IN_PREPARATION"
                                  WHEN iu.status_unit_id = 5 THEN "READY_TO_DELIVER"
                                  WHEN iu.status_unit_id = 6 THEN "IN_DELIVERY"
                                  WHEN iu.status_unit_id = 7 THEN "RENTAL_ACTIVE"
                                  WHEN iu.status_unit_id = 8 THEN "MAINTENANCE"
                                  WHEN iu.status_unit_id = 9 THEN "RETURNED"
                                  WHEN iu.status_unit_id = 10 THEN "SOLD"
                                  WHEN iu.status_unit_id = 11 THEN "RENTAL_INACTIVE"
                                  ELSE CONCAT("Status ", iu.status_unit_id)
                              END) as status_unit_name,
                              iu.departemen_id,
                              COALESCE(d.nama_departemen, "-") as nama_departemen,
                              iu.lokasi_unit as lokasi_unit_internal,
                              iu.lokasi_unit,
                              iu.created_at as tanggal_masuk,
                              c.customer_name,
                              cl.location_name as customer_location_name');
            $tableExists = $this->checkTablesExist(['model_unit', 'tipe_unit', 'status_unit', 'departemen', 'kontrak']);
            
            // Join with kontrak table for pelanggan and lokasi
            if ($tableExists['kontrak']) {
                $builder->join('kontrak as k', 'k.id = iu.kontrak_id', 'left')
                        ->join('customer_locations as cl', 'cl.id = iu.customer_location_id', 'left')
                        ->join('customers as c', 'c.id = iu.customer_id', 'left');
            }
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
            // Apply status filter (supports multi-status)
            $this->applyStatusFilter($builder, $statusFilter);
            if ($departemenFilter !== null && $departemenFilter !== '') {
                $builder->where('iu.departemen_id', $departemenFilter);
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

    public function countFiltered($searchValue, $statusFilter = null, $departemenFilter = null)
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
            // Apply status filter (supports multi-status)
            $this->applyStatusFilter($builder, $statusFilter);
            if ($departemenFilter !== null && $departemenFilter !== '') {
                $builder->where('iu.departemen_id', $departemenFilter);
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

    /**
     * Get units for dropdown selection
     */
    public function getUnitsForDropdown()
    {
        $builder = $this->db->table($this->table . ' as iu');
        $builder->select('iu.id_inventory_unit, 
                          COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, 
                          iu.serial_number,
                          iu.lokasi_unit,
                          COALESCE(c.customer_name, "Belum Ada Kontrak") as pelanggan,
                          COALESCE(cl.location_name, iu.lokasi_unit, "Lokasi Tidak Diketahui") as lokasi,
                          COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                          COALESCE(mu.model_unit, "Unknown") as model_unit,
                          COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "Unknown") as tipe')
                ->join('kontrak as k', 'k.id = iu.kontrak_id', 'left')
                ->join('customer_locations as cl', 'cl.id = k.customer_location_id', 'left')
                ->join('customers as c', 'c.id = cl.customer_id', 'left')
                ->join('model_unit as mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('tipe_unit as tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                ->where('iu.status_unit_id !=', 2) // Exclude WORKSHOP-RUSAK
                ->orderBy('iu.no_unit', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get unit detail with contract info for work orders
     */
    public function getUnitDetailForWorkOrder($unitId)
    {
        $builder = $this->db->table($this->table . ' as iu');
        $builder->select('iu.id_inventory_unit, 
                          iu.no_unit, 
                          iu.serial_number,
                          iu.lokasi_unit as lokasi_unit_internal,
                          iu.kontrak_id,
                          k.no_kontrak,
                          c.customer_name as pelanggan,
                          cl.location_name as lokasi_kontrak,
                          cl.contact_person as pic_kontrak,
                          COALESCE(cl.location_name, iu.lokasi_unit) as lokasi,
                          COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                          COALESCE(mu.model_unit, "Unknown") as model_unit,
                          COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "Unknown") as tipe,
                          COALESCE(ku.kapasitas, "Unknown") as kapasitas')
                ->join('kontrak as k', 'k.id = iu.kontrak_id', 'left')
                ->join('customer_locations as cl', 'cl.id = k.customer_location_id', 'left')
                ->join('customers as c', 'c.id = cl.customer_id', 'left')
                ->join('model_unit as mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('tipe_unit as tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                ->join('kapasitas_unit as ku', 'ku.id_kapasitas = iu.kapasitas_unit_id', 'left')
                ->where('iu.id_inventory_unit', $unitId);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Generate Non-Asset Number with Gap-Filling Strategy
     * Format: NA-001 to NA-500 (max 500 nameplates)
     * Strategy: Fill gaps first (reuse vacated numbers), then sequential
     * 
     * @return string Non-asset number (e.g., "NA-001")
     * @throws \Exception if capacity is full (all 500 slots occupied)
     */
    public function generateNonAssetNumber(): string
    {
        $maxCapacity = 500; // Max nameplate capacity: NA-001 to NA-500
        
        // Get all existing non-asset numbers
        $existingNumbers = $this->db->table('inventory_unit')
            ->select('no_unit_na')
            ->where('no_unit_na IS NOT NULL')
            ->where('no_unit_na LIKE "NA-%"')
            ->get()
            ->getResultArray();
        
        // Extract numeric parts from existing numbers
        $usedNumbers = [];
        foreach ($existingNumbers as $row) {
            if (preg_match('/NA-(\d+)/', $row['no_unit_na'], $matches)) {
                $usedNumbers[] = (int) $matches[1];
            }
        }
        
        // Find first available number (fill gaps first)
        for ($i = 1; $i <= $maxCapacity; $i++) {
            if (!in_array($i, $usedNumbers)) {
                return "NA-" . str_pad($i, 3, '0', STR_PAD_LEFT);
            }
        }
        
        // All slots full (NA-001 to NA-500)
        throw new \Exception("Kapasitas nomor Non-Asset penuh (maksimal {$maxCapacity} unit). Silakan konversi unit ke Asset atau hapus unit tidak terpakai.");
    }

    /**
     * Get display number for any unit (Asset or Non-Asset)
     * 
     * @param int $unitId
     * @return string|null Display number (e.g., "FL-001" or "NA-001" or "TEMP-123")
     */
    public function getDisplayNumber(int $unitId): ?string
    {
        $unit = $this->find($unitId);
        
        if (!$unit) {
            return null;
        }
        
        // Asset with no_unit (no prefix)
        if ($unit['no_unit']) {
            return (string) $unit['no_unit'];
        }
        
        // Non-Asset with no_unit_na
        if ($unit['no_unit_na']) {
            return $unit['no_unit_na'];
        }
        
        // No number assigned yet
        return "TEMP-" . $unit['id_inventory_unit'];
    }

    /**
     * Convert Non-Asset to Asset
     * Clears no_unit_na (makes it available for reuse), assigns no_unit, changes status
     * 
     * @param int $unitId
     * @param int|null $newAssetNumber If null, will auto-generate
     * @return array Conversion result with old and new numbers
     * @throws \Exception if unit is not Non-Asset
     */
    public function convertToAsset(int $unitId, ?int $newAssetNumber = null): array
    {
        $unit = $this->find($unitId);
        
        if (!$unit || $unit['status_unit_id'] != 8) {
            throw new \Exception('Unit bukan Non-Asset (status_unit_id harus 8)');
        }
        
        // Auto-generate asset number if not provided
        if (!$newAssetNumber) {
            // Get next asset number
            $lastAsset = $this->db->table('inventory_unit')
                ->selectMax('no_unit')
                ->where('no_unit IS NOT NULL')
                ->get()
                ->getRowArray();
            
            $newAssetNumber = isset($lastAsset['no_unit']) ? ((int)$lastAsset['no_unit']) + 1 : 1;
        }
        
        $oldNonAssetNumber = $unit['no_unit_na'];
        
        // Update: Clear no_unit_na (makes it available), set no_unit, change status
        $this->update($unitId, [
            'status_unit_id' => 7,        // STOCK ASET
            'no_unit' => $newAssetNumber,  // Asset number
            'no_unit_na' => null           // Clear (now available for reuse)
        ]);
        
        log_message('info', "[InventoryUnitModel::convertToAsset] Unit {$unitId}: {$oldNonAssetNumber} → {$newAssetNumber} (converted to Asset)");
        
        return [
            'success' => true,
            'old_number' => $oldNonAssetNumber,
            'new_number' => (string) $newAssetNumber,
            'freed_number' => $oldNonAssetNumber ? "{$oldNonAssetNumber} is now available for reuse" : null
        ];
    }
}