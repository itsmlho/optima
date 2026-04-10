<?php

namespace App\Models;

use CodeIgniter\Model;

class KontrakModel extends Model
{
    protected $table = 'kontrak';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'no_kontrak',
        'customer_po_number',   // Nomor PO dari customer (opsional)
        'rental_type',          // CONTRACT, PO_ONLY, DAILY_SPOT
        // customer_location_id REMOVED - moved to kontrak_unit table (March 5, 2026)
        // nilai_total DIHAPUS dari allowedFields:
        // nilai real dihitung otomatis dari kontrak_unit → inventory_unit.harga_sewa_bulanan
        // Lihat: findWithDynamicCalculation() dan getContractsForDataTable()
        'total_units',
        'jenis_sewa',
        'billing_method',       // CYCLE, PRORATE, MONTHLY_FIXED
        'billing_notes',        // Catatan billing khusus
        'billing_start_date',   // Override tanggal mulai billing
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
        'parent_contract_id',   // Untuk renewal chain
        'is_renewal',           // Apakah ini kontrak renewal
        'renewal_generation',   // Nomor generasi renewal
        'renewal_initiated_at', // Kapan renewal dimulai
        'renewal_initiated_by', // Siapa yang memulai renewal
        'dibuat_oleh',
        'customer_id',          // Direct customer reference (untuk kontrak tanpa lokasi spesifik)
        'payment_due_day',      // PO_ONLY: tanggal jatuh tempo (1-31)
        'estimated_duration_days', // DAILY_SPOT: estimasi durasi sewa (max 30)
        'spot_rental_number',   // DAILY_SPOT: nomor sewa spot
        'actual_return_date',   // DAILY_SPOT: tanggal pengembalian aktual
        'fast_track',           // Flag fast-track processing
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'dibuat_pada';
    protected $updatedField = 'diperbarui_pada';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        // no_kontrak TIDAK lagi unique: satu nomor surat bisa mencakup banyak unit
        // (legacy: dulu 1 baris per unit dengan nomor sama → sekarang 1 baris per kesepakatan)
        'no_kontrak'           => 'permit_empty|max_length[100]',
        // customer_location_id REMOVED - moved to kontrak_unit table (March 5, 2026)
        'tanggal_mulai'        => 'permit_empty|valid_date',
        'tanggal_berakhir'     => 'permit_empty|valid_date',
        'status'               => 'permit_empty|in_list[ACTIVE,EXPIRED,PENDING,CANCELLED]',
        'rental_type'          => 'permit_empty|in_list[CONTRACT,PO_ONLY,DAILY_SPOT]'
    ];

    protected $validationMessages = [
        'no_kontrak' => [
            'required' => 'Nomor kontrak harus diisi.',
            'is_unique' => 'Nomor kontrak sudah digunakan.'
        ],
        // customer_location_id validation REMOVED (March 5, 2026)
        'tanggal_mulai' => [
            'required' => 'Tanggal mulai harus diisi.',
            'valid_date' => 'Format tanggal mulai tidak valid.'
        ],
        'tanggal_berakhir' => [
            'required' => 'Tanggal berakhir harus diisi.',
            'valid_date' => 'Format tanggal berakhir tidak valid.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['beforeInsert'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['beforeUpdate'];
    protected $afterUpdate = ['logContractChanges'];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function beforeInsert(array $data)
    {
        if (!isset($data['data']['dibuat_oleh'])) {
            // NOTE: Callers should pass 'dibuat_oleh' explicitly in CLI/cron contexts
            $data['data']['dibuat_oleh'] = session()->get('user_id') ?? null;
        }
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        return $data;
    }

    /**
     * Log contract changes to contract_timeline
     */
    protected function logContractChanges(array $data)
    {
        // Only log if status changed
        if (!isset($data['id']) || !isset($data['data']['status'])) {
            return $data;
        }

        try {
            // Get old status
            $old = $this->find($data['id']);
            if ($old && isset($old['status']) && $old['status'] !== $data['data']['status']) {
                $timeline = new \App\Services\ContractTimelineService();
                $timeline->recordStatusChange(
                    $data['id'],
                    $old['status'],
                    $data['data']['status'],
                    null,
                    session()->get('user_id')
                );
                log_message('info', "✅ Contract #{$data['id']} status changed: {$old['status']} → {$data['data']['status']}");
            }
        } catch (\Throwable $e) {
            // Don't fail the update if timeline logging fails
            log_message('error', '[KontrakModel] Timeline logging failed: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * Get contracts for DataTables with statistics
     */
    public function getContractsForDataTable($start, $length, $search, $orderColumn, $orderDir, $filters = [])
    {
        $builder = $this->db->table($this->table . ' k');
        
        // Join with customers directly (new schema) and users table for counting
        $builder->join('customers c', 'c.id = k.customer_id', 'left');
        $builder->join('users u', 'k.dibuat_oleh = u.id', 'left');
        
        // Filter out invalid IDs (0 or null)
        $builder->where('k.id >', 0);
        $builder->where('k.id IS NOT NULL', null, false);

        // Apply tab-based filtering
        if (!empty($filters['tab'])) {
            switch ($filters['tab']) {
                case 'active':
                    $builder->where('k.status', 'ACTIVE');
                    $builder->where('k.tanggal_berakhir >=', date('Y-m-d')); // Not yet expired
                    break;
                case 'expired':
                    // Include both EXPIRED status AND ACTIVE contracts past their end date
                    $builder->groupStart()
                        ->where('k.status', 'EXPIRED')
                        ->orGroupStart()
                            ->where('k.status', 'ACTIVE')
                            ->where('k.tanggal_berakhir <', date('Y-m-d'))
                        ->groupEnd()
                    ->groupEnd();
                    break;
                case 'expiring':
                    // Contracts that will expire within specified days
                    $builder->where('k.status', 'ACTIVE');
                    if (!empty($filters['expiring_days'])) {
                        // Future expiring dates only
                        $expiringDate = date('Y-m-d', strtotime("+{$filters['expiring_days']} days"));
                        $builder->where('k.tanggal_berakhir <=', $expiringDate);
                        $builder->where('k.tanggal_berakhir >=', date('Y-m-d'));
                    }
                    break;
                // 'all' tab - no status filter
            }
        }

        // Apply additional filters
        if (!empty($filters['rental_type'])) {
            $builder->where('k.rental_type', $filters['rental_type']);
        }
        if (!empty($filters['customer_id'])) {
            $builder->where('c.id', $filters['customer_id']);
        }
        
        // Search functionality for counting with new database structure
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('k.no_kontrak', $search)
                    ->orLike('c.customer_name', $search)
                    ->orLike('k.status', $search)
                    ->groupEnd();
        }

        // Get total records with search filter
        $totalRecords = $builder->countAllResults(false);

        // Reset builder for actual data query
        $builder = $this->db->table($this->table . ' k');
        
        // Join again for data query
        $builder->join('customers c', 'c.id = k.customer_id', 'left');
        $builder->join('users u', 'k.dibuat_oleh = u.id', 'left');

        // Re-apply tab-based filtering for data query
        if (!empty($filters['tab'])) {
            switch ($filters['tab']) {
                case 'active':
                    $builder->where('k.status', 'ACTIVE');
                    $builder->where('k.tanggal_berakhir >=', date('Y-m-d')); // Not yet expired
                    break;
                case 'expired':
                    // Include both EXPIRED status AND ACTIVE contracts past their end date
                    $builder->groupStart()
                        ->where('k.status', 'EXPIRED')
                        ->orGroupStart()
                            ->where('k.status', 'ACTIVE')
                            ->where('k.tanggal_berakhir <', date('Y-m-d'))
                        ->groupEnd()
                    ->groupEnd();
                    break;
                case 'expiring':
                    $builder->where('k.status', 'ACTIVE');
                    $builder->where('k.rental_type !=', 'PO_ONLY'); // PO Bulanan: open-ended, never expires
                    if (!empty($filters['expiring_days'])) {
                        // Future expiring dates only
                        $expiringDate = date('Y-m-d', strtotime("+{$filters['expiring_days']} days"));
                        $builder->where('k.tanggal_berakhir <=', $expiringDate);
                        $builder->where('k.tanggal_berakhir >=', date('Y-m-d'));
                    }
                    break;
            }
        }

        // Re-apply additional filters for data query
        if (!empty($filters['rental_type'])) {
            $builder->where('k.rental_type', $filters['rental_type']);
        }
        if (!empty($filters['customer_id'])) {
            $builder->where('c.id', $filters['customer_id']);
        }
        
        // Explicitly select all columns we need with dynamic calculations and user name from new structure
        $builder->select('k.id, k.no_kontrak, k.customer_po_number, k.rental_type,
                         c.customer_name as pelanggan, 
                         (SELECT cl.contact_person FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as pic, 
                         (SELECT cl.phone FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as kontak, 
                         (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as lokasi, 
                         k.jenis_sewa,
                         (SELECT COALESCE(SUM(CASE WHEN (ku.is_spare IS NULL OR ku.is_spare = 0) THEN COALESCE(ku.harga_sewa, iu.harga_sewa_bulanan) ELSE 0 END), 0) FROM kontrak_unit ku JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id WHERE ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0) as nilai_total,
                         (SELECT COUNT(*) FROM kontrak_unit ku JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id WHERE ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0) as total_units,
                         k.tanggal_mulai, k.tanggal_berakhir, k.status, k.dibuat_pada, k.diperbarui_pada,
                         CONCAT(u.first_name, " ", u.last_name) as dibuat_oleh_nama');
        
        // Filter out invalid IDs (0 or null)
        $builder->where('k.id >', 0);
        $builder->where('k.id IS NOT NULL', null, false);
        
        // Apply search again for data query
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('k.no_kontrak', $search)
                    ->orLike('c.customer_name', $search)
                    ->orLike('k.status', $search)
                    ->groupEnd();
        }

        // Order with new database structure
        $columns = ['k.no_kontrak', 'c.customer_name', 'k.tanggal_mulai', 'k.tanggal_berakhir', 'k.status'];
        if (isset($columns[$orderColumn])) {
            $builder->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $builder->orderBy('k.dibuat_pada', 'DESC');
        }

        // Limit
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $contracts = $builder->get()->getResultArray();

        // Get statistics
        $stats = $this->getContractStatistics();

        return [
            'data' => $contracts,
            'recordsTotal' => $this->countAll(),
            'recordsFiltered' => $totalRecords,
            'stats' => $stats
        ];
    }

    /**
     * Override countAll to exclude invalid IDs
     */
    public function countAll(bool $reset = true, bool $test = false): int
    {
        $builder = $this->db->table($this->table);
        $builder->where('id >', 0);
        $builder->where('id IS NOT NULL', null, false);
        return $builder->countAllResults();
    }
    public function getDataTable($request)
    {
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? '';
        $orderColumn = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';

        // Pass custom filters including tab-based filtering
        $filters = [
            'tab'           => $request->getPost('tab') ?? 'all',
            'expiring_days' => $request->getPost('expiring_days') ?? null,
            'rental_type'   => $request->getPost('rental_type') ?? '',
            'customer_id'   => $request->getPost('customer_id') ?? '',
        ];

        return $this->getContractsForDataTable($start, $length, $searchValue, $orderColumn, $orderDir, $filters);
    }

    /**
     * Count all data for DataTables
     */
    public function countAllData()
    {
        return $this->countAll();
    }

    /**
     * Count filtered data for DataTables
     */
    public function countFilteredData($request)
    {
        $searchValue = $request->getPost('search')['value'] ?? '';
        
        if (empty($searchValue)) {
            return $this->countAll();
        }

        $builder = $this->db->table($this->table . ' k');
        $builder->join('customers c', 'c.id = k.customer_id', 'left');
        $builder->groupStart()
                ->like('k.no_kontrak', $searchValue)
                ->orLike('c.customer_name', $searchValue)
                ->orLike('k.status', $searchValue)
                ->groupEnd();

        return $builder->countAllResults();
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        return $this->getContractStatistics();
    }

    /**
     * Get contract statistics
     */
    public function getContractStatistics()
    {
        // Use separate builders to avoid condition carry-over
        $total = $this->db->table($this->table)->countAllResults();

        $active = $this->db->table($this->table)
            ->where('status', 'ACTIVE')
            ->countAllResults();

        // Expiring within 30 days
        $expiring_30 = $this->db->table($this->table)
            ->where('status', 'ACTIVE')
            ->where('tanggal_berakhir <=', date('Y-m-d', strtotime('+30 days')))
            ->where('tanggal_berakhir >=', date('Y-m-d'))
            ->countAllResults();

        // Expiring within 90 days
        $expiring_90 = $this->db->table($this->table)
            ->where('status', 'ACTIVE')
            ->where('tanggal_berakhir <=', date('Y-m-d', strtotime('+90 days')))
            ->where('tanggal_berakhir >=', date('Y-m-d'))
            ->countAllResults();

        // Expiring within 180 days (6 months)
        $expiring_180 = $this->db->table($this->table)
            ->where('status', 'ACTIVE')
            ->where('tanggal_berakhir <=', date('Y-m-d', strtotime('+180 days')))
            ->where('tanggal_berakhir >=', date('Y-m-d'))
            ->countAllResults();

        // Already past end date (Sudah Lewat)
        $expired_past = $this->db->table($this->table)
            ->where('status', 'ACTIVE')
            ->where('tanggal_berakhir <', date('Y-m-d'))
            ->countAllResults();

        // All expired (status EXPIRED or past date)
        $expired = $this->db->table($this->table)
            ->groupStart()
                ->where('status', 'EXPIRED')
                ->orWhere('tanggal_berakhir <', date('Y-m-d'))
            ->groupEnd()
            ->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'expiring_30' => $expiring_30,
            'expiring_90' => $expiring_90,
            'expiring_180' => $expiring_180,
            'expired_past' => $expired_past,
            'expired' => $expired,
        ];
    }

    /**
     * Check if contract number is unique
     */
    public function isContractNumberUnique($contractNumber, $excludeId = null)
    {
        $builder = $this->where('no_kontrak', $contractNumber);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() === 0;
    }

    /**
     * Find contract with dynamic calculations from inventory_unit
     */
    public function findWithDynamicCalculation($id)
    {
        // First get the basic contract data with user, customer, and location information
        $contract = $this->db->table($this->table . ' k')
            ->select('
                k.*,
                c.customer_name,
                (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name,
                (SELECT cl.contact_person FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as contact_person,
                (SELECT cl.phone FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as phone,
                (SELECT cl.address FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as address,
                CONCAT(u.first_name, " ", u.last_name) as dibuat_oleh_nama
            ')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->join('users u', 'k.dibuat_oleh = u.id', 'left')
            ->where('k.id', $id)
            ->get()
            ->getRowArray();
            
        if (!$contract) {
            return null;
        }

        // Then get the dynamic calculations from kontrak_unit junction (exclude temporary units for accurate billing)
        $calculations = $this->db->query("
            SELECT 
                COUNT(CASE WHEN ku.is_temporary != 1 THEN 1 END) as actual_units,
                COALESCE(SUM(CASE WHEN ku.is_temporary != 1 AND (ku.is_spare IS NULL OR ku.is_spare = 0) THEN COALESCE(ku.harga_sewa, iu.harga_sewa_bulanan) ELSE 0 END), 0) as total_nilai
            FROM kontrak_unit ku
            JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
            WHERE ku.kontrak_id = ? AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
        ", [$id])->getRowArray();

        // Override the calculated fields
        if ($calculations) {
            $contract['total_units'] = (int)$calculations['actual_units'];
            $contract['nilai_total'] = (float)$calculations['total_nilai'];
        }

        return $contract;
    }
}
