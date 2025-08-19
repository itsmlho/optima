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
        'no_kontrak' => 'required|max_length[100]|is_unique[kontrak.no_kontrak,id,{id}]',
        'pelanggan' => 'required|max_length[255]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_berakhir' => 'required|valid_date',
        'status' => 'required|in_list[Aktif,Berakhir,Pending,Dibatalkan]'
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
        $builder = $this->db->table($this->table);
        
        // Search functionality for counting
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('no_kontrak', $search)
                    ->orLike('pelanggan', $search)
                    ->orLike('lokasi', $search)
                    ->orLike('status', $search)
                    ->groupEnd();
        }

        // Get total records with search filter
        $totalRecords = $builder->countAllResults(false);

        // Reset builder for actual data query
        $builder = $this->db->table($this->table);
        
        // Explicitly select all columns we need
        $builder->select('id, no_kontrak, no_po_marketing, pelanggan, pic, kontak, lokasi, nilai_total, total_units, tanggal_mulai, tanggal_berakhir, status, dibuat_pada, diperbarui_pada');
        
        // Apply search again for data query
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('no_kontrak', $search)
                    ->orLike('pelanggan', $search)
                    ->orLike('lokasi', $search)
                    ->orLike('status', $search)
                    ->groupEnd();
        }

        // Order
        $columns = ['no_kontrak', 'pelanggan', 'tanggal_mulai', 'tanggal_berakhir', 'status'];
        if (isset($columns[$orderColumn])) {
            $builder->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $builder->orderBy('dibuat_pada', 'DESC');
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
     * DataTables server-side processing method
     */
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
        $builder = $this->db->table($this->table);
        
        $total = $builder->countAllResults(false);
        
        $active = $builder->where('status', 'Aktif')->countAllResults(false);
        
        $expiring = $builder->where('status', 'Aktif')
                           ->where('tanggal_berakhir <=', date('Y-m-d', strtotime('+30 days')))
                           ->where('tanggal_berakhir >=', date('Y-m-d'))
                           ->countAllResults(false);
        
        $expired = $builder->where('status', 'Berakhir')
                          ->orWhere('tanggal_berakhir <', date('Y-m-d'))
                          ->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'expiring' => $expiring,
            'expired' => $expired
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
}
