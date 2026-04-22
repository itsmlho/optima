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
        'payment_terms',        // NET_30, NET_45, NET_60, COD, PREPAID (inherited from customer, overridable)
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
        'rental_type'          => 'permit_empty|in_list[CONTRACT,PO_ONLY,DAILY_SPOT]',
        'billing_method'       => 'permit_empty|in_list[CYCLE,PRORATE,MONTHLY_FIXED]',
        'payment_terms'        => 'permit_empty|in_list[NET_30,NET_45,NET_60,COD,PREPAID]'
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
     * Get contracts for DataTables.
     *
     * Uses derived-table LEFT JOINs instead of correlated subqueries so the DB
     * only touches kontrak_unit once per batch instead of once per row.
     * Stats are intentionally excluded here — the frontend fetches them via
     * /marketing/rental/stats independently.
     */
    public function getContractsForDataTable($start, $length, $search, $orderColumn, $orderDir, $filters = [])
    {
        $today  = date('Y-m-d');
        $params = [];

        // ── Build WHERE conditions ────────────────────────────────────────────
        $whereConditions = ['k.id > 0'];

        if (!empty($filters['tab'])) {
            switch ($filters['tab']) {
                case 'active':
                    $whereConditions[] = "k.status = 'ACTIVE'";
                    $whereConditions[] = 'k.tanggal_berakhir >= ?';
                    $params[]          = $today;
                    break;
                case 'expired':
                    $whereConditions[] = "(k.status = 'EXPIRED' OR (k.status = 'ACTIVE' AND k.tanggal_berakhir < ?))";
                    $params[]          = $today;
                    break;
                case 'expiring':
                    $whereConditions[] = "k.status = 'ACTIVE'";
                    $whereConditions[] = "k.rental_type != 'PO_ONLY'";
                    if (!empty($filters['expiring_days'])) {
                        $expiringDate      = date('Y-m-d', strtotime("+{$filters['expiring_days']} days"));
                        $whereConditions[] = 'k.tanggal_berakhir <= ?';
                        $params[]          = $expiringDate;
                        $whereConditions[] = 'k.tanggal_berakhir >= ?';
                        $params[]          = $today;
                    }
                    break;
            }
        }

        if (!empty($filters['rental_type'])) {
            $whereConditions[] = 'k.rental_type = ?';
            $params[]          = $filters['rental_type'];
        }
        if (!empty($filters['customer_id'])) {
            $whereConditions[] = 'c.id = ?';
            $params[]          = $filters['customer_id'];
        }
        if (!empty($search)) {
            $s                 = '%' . $search . '%';
            $whereConditions[] = '(k.no_kontrak LIKE ? OR c.customer_name LIKE ? OR k.status LIKE ?)';
            $params[]          = $s;
            $params[]          = $s;
            $params[]          = $s;
        }

        $where = 'WHERE ' . implode(' AND ', $whereConditions);

        // ── Count queries (2 cheap queries, no subqueries) ───────────────────
        $totalAll     = (int) $this->db->query(
            'SELECT COUNT(*) AS cnt FROM kontrak WHERE id > 0'
        )->getRow()->cnt;

        $totalRecords = (int) $this->db->query(
            "SELECT COUNT(*) AS cnt FROM kontrak k LEFT JOIN customers c ON c.id = k.customer_id {$where}",
            $params
        )->getRow()->cnt;

        // ── Order ─────────────────────────────────────────────────────────────
        $columns  = ['k.no_kontrak', 'c.customer_name', 'k.tanggal_mulai', 'k.tanggal_berakhir', 'k.status'];
        $orderCol = $columns[$orderColumn] ?? 'k.dibuat_pada';
        $orderDir = ($orderDir === 'asc') ? 'ASC' : 'DESC';

        // ── Data query — 2 derived-table JOINs replace 5 correlated subqueries
        $dataParams = $params;
        $limitSql   = '';
        if ($length != -1) {
            $limitSql     = 'LIMIT ? OFFSET ?';
            $dataParams[] = (int) $length;
            $dataParams[] = (int) $start;
        }

        $dataSql = "
            SELECT
                k.id, k.no_kontrak, k.customer_po_number, k.rental_type,
                c.customer_name AS pelanggan,
                kl.contact_person AS pic,
                kl.phone          AS kontak,
                kl.location_name  AS lokasi,
                k.jenis_sewa,
                COALESCE(us.nilai_total,  0) AS nilai_total,
                COALESCE(us.total_units,  0) AS total_units,
                k.tanggal_mulai, k.tanggal_berakhir, k.status, k.dibuat_pada, k.diperbarui_pada,
                CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) AS dibuat_oleh_nama
            FROM kontrak k
            LEFT JOIN customers c   ON c.id = k.customer_id
            LEFT JOIN users u       ON u.id = k.dibuat_oleh
            LEFT JOIN (
                SELECT ku.kontrak_id,
                       MIN(cl.contact_person) AS contact_person,
                       MIN(cl.phone)          AS phone,
                       MIN(cl.location_name)  AS location_name
                FROM   kontrak_unit ku
                JOIN   customer_locations cl ON cl.id = ku.customer_location_id
                GROUP  BY ku.kontrak_id
            ) kl ON kl.kontrak_id = k.id
            LEFT JOIN (
                SELECT ku.kontrak_id,
                       COALESCE(SUM(CASE WHEN (ku.is_spare IS NULL OR ku.is_spare = 0)
                                         THEN COALESCE(ku.harga_sewa, iu.harga_sewa_bulanan)
                                         ELSE 0 END), 0) AS nilai_total,
                       COUNT(*) AS total_units
                FROM   kontrak_unit ku
                JOIN   inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
                WHERE  ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
                GROUP  BY ku.kontrak_id
            ) us ON us.kontrak_id = k.id
            {$where}
            ORDER BY {$orderCol} {$orderDir}
            {$limitSql}
        ";

        $contracts = $this->db->query($dataSql, $dataParams)->getResultArray();

        return [
            'data'            => $contracts,
            'recordsTotal'    => $totalAll,
            'recordsFiltered' => $totalRecords,
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
     * Get contract statistics — single query via conditional aggregation.
     * Previously ran 7 separate COUNT queries; now runs 1.
     */
    public function getContractStatistics()
    {
        $today = date('Y-m-d');
        $d30   = date('Y-m-d', strtotime('+30 days'));
        $d90   = date('Y-m-d', strtotime('+90 days'));
        $d180  = date('Y-m-d', strtotime('+180 days'));

        $sql = "
            SELECT
                COUNT(*) AS total,
                COUNT(CASE WHEN status = 'ACTIVE' AND tanggal_berakhir >= ? THEN 1 END) AS active,
                COUNT(CASE WHEN status = 'ACTIVE' AND tanggal_berakhir <= ? AND tanggal_berakhir >= ? THEN 1 END) AS expiring_30,
                COUNT(CASE WHEN status = 'ACTIVE' AND tanggal_berakhir <= ? AND tanggal_berakhir >= ? THEN 1 END) AS expiring_90,
                COUNT(CASE WHEN status = 'ACTIVE' AND tanggal_berakhir <= ? AND tanggal_berakhir >= ? THEN 1 END) AS expiring_180,
                COUNT(CASE WHEN status = 'ACTIVE' AND tanggal_berakhir < ?  THEN 1 END) AS expired_past,
                COUNT(CASE WHEN status = 'EXPIRED' OR (status = 'ACTIVE' AND tanggal_berakhir < ?) THEN 1 END) AS expired
            FROM kontrak
            WHERE id > 0
        ";

        $row = $this->db->query($sql, [
            $today,
            $d30,   $today,
            $d90,   $today,
            $d180,  $today,
            $today,
            $today,
        ])->getRowArray();

        return [
            'total'        => (int) ($row['total']        ?? 0),
            'active'       => (int) ($row['active']       ?? 0),
            'expiring_30'  => (int) ($row['expiring_30']  ?? 0),
            'expiring_90'  => (int) ($row['expiring_90']  ?? 0),
            'expiring_180' => (int) ($row['expiring_180'] ?? 0),
            'expired_past' => (int) ($row['expired_past'] ?? 0),
            'expired'      => (int) ($row['expired']      ?? 0),
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
