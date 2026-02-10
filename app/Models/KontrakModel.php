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
        'customer_po_number',  // Renamed from no_po_marketing
        'rental_type',         // New: CONTRACT, PO_ONLY, DAILY_SPOT
        'customer_location_id',
        'nilai_total',
        'total_units',
        'jenis_sewa',
        'billing_method',      // NEW: CYCLE, PRORATE, MONTHLY_FIXED
        'billing_notes',       // NEW: Special billing instructions
        'billing_start_date',  // NEW: Override billing start date
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
        'parent_contract_id',  // NEW: For renewal chain
        'is_renewal',          // NEW: Is this a renewed contract
        'renewal_generation',  // NEW: Generation number
        'renewal_initiated_at', // NEW: When renewal started
        'renewal_initiated_by', // NEW: Who initiated renewal
        'dibuat_oleh'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'dibuat_pada';
    protected $updatedField = 'diperbarui_pada';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'no_kontrak' => 'required|max_length[100]|is_unique[kontrak.no_kontrak]',
        'customer_location_id' => 'required|is_natural_no_zero',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_berakhir' => 'required|valid_date',
        'status' => 'permit_empty|in_list[ACTIVE,EXPIRED,PENDING,CANCELLED]',
        'rental_type' => 'permit_empty|in_list[CONTRACT,PO_ONLY,DAILY_SPOT]'
    ];

    protected $validationMessages = [
        'no_kontrak' => [
            'required' => 'Nomor kontrak harus diisi.',
            'is_unique' => 'Nomor kontrak sudah digunakan.'
        ],
        'customer_location_id' => [
            'required' => 'Customer location harus dipilih.',
            'is_natural_no_zero' => 'Customer location harus berupa angka yang valid.'
        ],
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
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function beforeInsert(array $data)
    {
        if (!isset($data['data']['dibuat_oleh'])) {
            $data['data']['dibuat_oleh'] = session()->get('user_id');
        }
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        return $data;
    }

    /**
     * Get contracts for DataTables with statistics
     */
    public function getContractsForDataTable($start, $length, $search, $orderColumn, $orderDir)
    {
        $builder = $this->db->table($this->table . ' k');
        
        // Join with customer_locations, customers, and users table for counting
        $builder->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left');
        $builder->join('customers c', 'cl.customer_id = c.id', 'left');
        $builder->join('users u', 'k.dibuat_oleh = u.id', 'left');
        
        // Filter out invalid IDs (0 or null)
        $builder->where('k.id >', 0);
        $builder->where('k.id IS NOT NULL', null, false);
        
        // Search functionality for counting with new database structure
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('k.no_kontrak', $search)
                    ->orLike('c.customer_name', $search)
                    ->orLike('cl.location_name', $search)
                    ->orLike('cl.address', $search)
                    ->orLike('k.status', $search)
                    ->groupEnd();
        }

        // Get total records with search filter
        $totalRecords = $builder->countAllResults(false);

        // Reset builder for actual data query
        $builder = $this->db->table($this->table . ' k');
        
        // Join again for data query
        $builder->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left');
        $builder->join('customers c', 'cl.customer_id = c.id', 'left');
        $builder->join('users u', 'k.dibuat_oleh = u.id', 'left');
        
        // Explicitly select all columns we need with dynamic calculations and user name from new structure
        $builder->select('k.id, k.no_kontrak, k.customer_po_number, k.rental_type,
                         c.customer_name as pelanggan, 
                         cl.contact_person as pic, 
                         cl.phone as kontak, 
                         cl.location_name as lokasi, 
                         k.jenis_sewa,
                         (SELECT COALESCE(SUM(harga_sewa_bulanan), 0) FROM inventory_unit iu WHERE iu.kontrak_id = k.id) as nilai_total,
                         (SELECT COUNT(*) FROM inventory_unit iu WHERE iu.kontrak_id = k.id) as total_units,
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
                    ->orLike('cl.location_name', $search)
                    ->orLike('cl.address', $search)
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

        return $this->getContractsForDataTable($start, $length, $searchValue, $orderColumn, $orderDir);
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
        $builder->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left');
        $builder->join('customers c', 'cl.customer_id = c.id', 'left');
        $builder->groupStart()
                ->like('k.no_kontrak', $searchValue)
                ->orLike('c.customer_name', $searchValue)
                ->orLike('cl.location_name', $searchValue)
                ->orLike('cl.address', $searchValue)
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

        $expiring = $this->db->table($this->table)
            ->where('status', 'ACTIVE')
            ->where('tanggal_berakhir <=', date('Y-m-d', strtotime('+30 days')))
            ->where('tanggal_berakhir >=', date('Y-m-d'))
            ->countAllResults();

        $expired = $this->db->table($this->table)
            ->groupStart()
                ->where('status', 'EXPIRED')
                ->orWhere('tanggal_berakhir <', date('Y-m-d'))
            ->groupEnd()
            ->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'expiring' => $expiring,
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
                cl.location_name,
                cl.contact_person,
                cl.phone,
                cl.address,
                CONCAT(u.first_name, " ", u.last_name) as dibuat_oleh_nama
            ')
            ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
            ->join('customers c', 'cl.customer_id = c.id', 'left')
            ->join('users u', 'k.dibuat_oleh = u.id', 'left')
            ->where('k.id', $id)
            ->get()
            ->getRowArray();
            
        if (!$contract) {
            return null;
        }

        // Then get the dynamic calculations from inventory_unit (exclude temporary units for accurate billing)
        $calculations = $this->db->query("
            SELECT 
                COUNT(CASE WHEN ku.is_temporary != 1 THEN 1 END) as actual_units,
                COALESCE(SUM(CASE WHEN ku.is_temporary != 1 THEN iu.harga_sewa_bulanan ELSE 0 END), 0) as total_nilai
            FROM inventory_unit iu 
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.kontrak_id = iu.kontrak_id
            WHERE iu.kontrak_id = ?
        ", [$id])->getRowArray();

        // Override the calculated fields
        if ($calculations) {
            $contract['total_units'] = (int)$calculations['actual_units'];
            $contract['nilai_total'] = (float)$calculations['total_nilai'];
        }

        return $contract;
    }
}
