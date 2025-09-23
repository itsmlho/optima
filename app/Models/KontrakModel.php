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
        'no_po_marketing', 
        'pelanggan',
        'pic',
        'kontak',
        'lokasi',
        'nilai_total',
        'total_units',
        'jenis_sewa',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
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
        'pelanggan' => 'required|max_length[255]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_berakhir' => 'required|valid_date',
        'status' => 'permit_empty|in_list[Aktif,Berakhir,Pending,Dibatalkan]'
    ];

    protected $validationMessages = [
        'no_kontrak' => [
            'required' => 'Nomor kontrak harus diisi.',
            'is_unique' => 'Nomor kontrak sudah digunakan.'
        ],
        'pelanggan' => [
            'required' => 'Nama pelanggan harus diisi.'
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
        
        // Join with users table for counting
        $builder->join('users u', 'k.dibuat_oleh = u.id', 'left');
        
        // Filter out invalid IDs (0 or null)
        $builder->where('k.id >', 0);
        $builder->where('k.id IS NOT NULL', null, false);
        
        // Search functionality for counting
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('k.no_kontrak', $search)
                    ->orLike('k.pelanggan', $search)
                    ->orLike('k.lokasi', $search)
                    ->orLike('k.status', $search)
                    ->groupEnd();
        }

        // Get total records with search filter
        $totalRecords = $builder->countAllResults(false);

        // Reset builder for actual data query
        $builder = $this->db->table($this->table . ' k');
        
        // Explicitly select all columns we need with dynamic calculations and user name
        $builder->select('k.id, k.no_kontrak, k.no_po_marketing, k.pelanggan, k.pic, k.kontak, k.lokasi, k.jenis_sewa,
                         (SELECT COALESCE(SUM(harga_sewa_bulanan), 0) FROM inventory_unit iu WHERE iu.kontrak_id = k.id) as nilai_total,
                         (SELECT COUNT(*) FROM inventory_unit iu WHERE iu.kontrak_id = k.id) as total_units,
                         k.tanggal_mulai, k.tanggal_berakhir, k.status, k.dibuat_pada, k.diperbarui_pada,
                         CONCAT(u.first_name, " ", u.last_name) as dibuat_oleh_nama');
        
        // Join with users table
        $builder->join('users u', 'k.dibuat_oleh = u.id', 'left');
        
        // Filter out invalid IDs (0 or null)
        $builder->where('k.id >', 0);
        $builder->where('k.id IS NOT NULL', null, false);
        
        // Apply search again for data query
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('k.no_kontrak', $search)
                    ->orLike('k.pelanggan', $search)
                    ->orLike('k.lokasi', $search)
                    ->orLike('k.status', $search)
                    ->groupEnd();
        }

        // Order
        $columns = ['k.no_kontrak', 'k.pelanggan', 'k.tanggal_mulai', 'k.tanggal_berakhir', 'k.status'];
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

        $builder = $this->db->table($this->table);
        $builder->groupStart()
                ->like('no_kontrak', $searchValue)
                ->orLike('pelanggan', $searchValue)
                ->orLike('lokasi', $searchValue)
                ->orLike('status', $searchValue)
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
            ->where('status', 'Aktif')
            ->countAllResults();

        $expiring = $this->db->table($this->table)
            ->where('status', 'Aktif')
            ->where('tanggal_berakhir <=', date('Y-m-d', strtotime('+30 days')))
            ->where('tanggal_berakhir >=', date('Y-m-d'))
            ->countAllResults();

        $expired = $this->db->table($this->table)
            ->groupStart()
                ->where('status', 'Berakhir')
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
        // First get the basic contract data with user information
        $contract = $this->db->table($this->table . ' k')
            ->select('
                k.*,
                CONCAT(u.first_name, " ", u.last_name) as dibuat_oleh_nama
            ')
            ->join('users u', 'k.dibuat_oleh = u.id', 'left')
            ->where('k.id', $id)
            ->get()
            ->getRowArray();
            
        if (!$contract) {
            return null;
        }

        // Then get the dynamic calculations
        $calculations = $this->db->query("
            SELECT 
                COUNT(iu.id_inventory_unit) as actual_units,
                COALESCE(SUM(iu.harga_sewa_bulanan), 0) as total_nilai
            FROM kontrak_spesifikasi ks
            LEFT JOIN inventory_unit iu ON ks.id = iu.kontrak_spesifikasi_id
            WHERE ks.kontrak_id = ?
        ", [$id])->getRowArray();

        // Override the calculated fields
        if ($calculations) {
            $contract['total_units'] = (int)$calculations['actual_units'];
            $contract['nilai_total'] = (float)$calculations['total_nilai'];
        }

        return $contract;
    }
}
