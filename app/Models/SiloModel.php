<?php

namespace App\Models;

use CodeIgniter\Model;

class SiloModel extends Model
{
    protected $table            = 'silo';
    protected $primaryKey       = 'id_silo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'unit_id',
        'status',
        'tanggal_pengajuan_pjk3',
        'catatan_pengajuan_pjk3',
        'tanggal_testing_pjk3',
        'hasil_testing_pjk3',
        'nomor_surat_keterangan_pjk3',
        'tanggal_surat_keterangan_pjk3',
        'file_surat_keterangan_pjk3',
        'tanggal_pengajuan_uptd',
        'catatan_pengajuan_uptd',
        'tanggal_proses_uptd',
        'catatan_proses_uptd',
        'nomor_silo',
        'tanggal_terbit_silo',
        'tanggal_expired_silo',
        'file_silo',
        'created_by',
        'updated_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Status constants
    const STATUS_BELUM_ADA = 'BELUM_ADA';
    const STATUS_PENGAJUAN_PJK3 = 'PENGAJUAN_PJK3';
    const STATUS_TESTING_PJK3 = 'TESTING_PJK3';
    const STATUS_SURAT_KETERANGAN_PJK3 = 'SURAT_KETERANGAN_PJK3';
    const STATUS_PENGAJUAN_UPTD = 'PENGAJUAN_UPTD';
    const STATUS_PROSES_UPTD = 'PROSES_UPTD';
    const STATUS_SILO_TERBIT = 'SILO_TERBIT';
    const STATUS_SILO_EXPIRED = 'SILO_EXPIRED';

    /**
     * Get all SILO with unit information
     */
    public function getAllWithUnit($filters = [])
    {
        try {
            // Check if table exists
            if (!$this->db->tableExists($this->table)) {
                log_message('warning', 'Table silo does not exist yet');
                return [];
            }

            $builder = $this->db->table($this->table . ' s');
                $builder->select('s.*,
                    iu.no_unit,
                    iu.serial_number,
                    tu.tipe as tipe_unit,
                    CONCAT(mu.merk_unit, " - ", mu.model_unit) as model_unit');
            $builder->join('inventory_unit iu', 'iu.id_inventory_unit = s.unit_id', 'left');
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
            $builder->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left');

            // Apply filters
            if (!empty($filters['status'])) {
                // Handle special case for 'progres' status
                if ($filters['status'] === 'progres') {
                    $progresStatuses = [
                        self::STATUS_PENGAJUAN_PJK3,
                        self::STATUS_TESTING_PJK3,
                        self::STATUS_SURAT_KETERANGAN_PJK3,
                        self::STATUS_PENGAJUAN_UPTD,
                        self::STATUS_PROSES_UPTD,
                    ];
                    $builder->whereIn('s.status', $progresStatuses);
                } else {
                    $builder->where('s.status', $filters['status']);
                }
            }

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $builder->groupStart();
                $builder->like('iu.serial_number', $search);
                $builder->orLike('iu.no_unit', $search);
                $builder->orLike('s.nomor_silo', $search);
                $builder->orLike('s.nomor_surat_keterangan_pjk3', $search);
                $builder->groupEnd();
            }

            if (!empty($filters['expiring_soon'])) {
                $days = (int)$filters['expiring_soon'];
                $builder->where('s.tanggal_expired_silo IS NOT NULL');
                $builder->where('s.tanggal_expired_silo <=', date('Y-m-d', strtotime("+{$days} days")));
                $builder->where('s.tanggal_expired_silo >=', date('Y-m-d'));
            }

            if (!empty($filters['expired'])) {
                $builder->where('s.tanggal_expired_silo <', date('Y-m-d'));
            }

            $builder->orderBy('s.created_at', 'DESC');

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'SiloModel::getAllWithUnit Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Get SILO by unit ID
     */
    public function getByUnitId($unitId)
    {
        return $this->where('unit_id', $unitId)->first();
    }

    /**
     * Get units without SILO (for tab "Belum Ada SILO")
     */
    public function getUnitsWithoutSilo($search = '')
    {
        try {
            log_message('debug', 'SiloModel::getUnitsWithoutSilo - Called with search: ' . $search);
            
            // First, get all units
            $builder = $this->db->table('inventory_unit iu');
            $builder->select('iu.id_inventory_unit as id_silo, iu.no_unit, iu.serial_number, 
                NULL as status, NULL as nomor_silo, NULL as tanggal_terbit_silo, NULL as tanggal_expired_silo,
                tu.tipe as tipe_unit, CONCAT(mu.merk_unit, " - ", mu.model_unit) as model_unit');
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
            $builder->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
            
            // Check if silo table exists
            if ($this->db->tableExists($this->table)) {
                log_message('debug', 'SiloModel::getUnitsWithoutSilo - SILO table exists, filtering...');
                
                // Get units with active SILO first
                $activeSiloBuilder = $this->db->table('silo');
                $activeSiloBuilder->select('unit_id');
                $activeSiloBuilder->where('status', self::STATUS_SILO_TERBIT);
                $activeSiloBuilder->groupStart();
                $activeSiloBuilder->where('tanggal_expired_silo IS NULL');
                $activeSiloBuilder->orWhere('tanggal_expired_silo >=', date('Y-m-d'));
                $activeSiloBuilder->groupEnd();
                
                $activeSiloResult = $activeSiloBuilder->get()->getResultArray();
                $activeSiloUnitIds = array_column($activeSiloResult, 'unit_id');
                
                log_message('debug', 'SiloModel::getUnitsWithoutSilo - Found ' . count($activeSiloUnitIds) . ' units with active SILO');
                
                // Exclude units with active SILO
                if (!empty($activeSiloUnitIds)) {
                    $builder->whereNotIn('iu.id_inventory_unit', $activeSiloUnitIds);
                }
            } else {
                log_message('debug', 'SiloModel::getUnitsWithoutSilo - SILO table does not exist, returning all units');
            }

            // Apply search filter
            if (!empty($search)) {
                $builder->groupStart();
                $builder->like('iu.serial_number', $search);
                $builder->orLike('iu.no_unit', $search);
                $builder->groupEnd();
            }

            $builder->orderBy('iu.no_unit', 'ASC');

            $result = $builder->get()->getResultArray();
            log_message('debug', 'SiloModel::getUnitsWithoutSilo - Found ' . count($result) . ' units without SILO');
            if (count($result) > 0) {
                log_message('debug', 'SiloModel::getUnitsWithoutSilo - First unit: ' . json_encode($result[0]));
            }
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'SiloModel::getUnitsWithoutSilo Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Get SILO by ID with unit information
     */
    public function getByIdWithUnit($siloId)
    {
        $builder = $this->db->table($this->table . ' s');
        $builder->select('s.*, 
            iu.no_unit, 
            iu.serial_number,
            iu.tahun_unit,
                    tu.tipe as tipe_unit,
                    CONCAT(mu.merk_unit, " - ", mu.model_unit) as model_unit');
        $builder->join('inventory_unit iu', 'iu.id_inventory_unit = s.unit_id', 'left');
        $builder->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
        $builder->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
        $builder->where('s.id_silo', $siloId);

        return $builder->get()->getRowArray();
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $stats = [
            'sudah_ada' => 0,
            'progres' => 0,
            'belum_ada' => 0,
            'expiring_soon' => 0,
            'expired' => 0,
        ];

        try {
            // Check if table exists
            if (!$this->db->tableExists($this->table)) {
                log_message('warning', 'Table silo does not exist yet, returning default stats');
                return $stats;
            }

            // Count by status
            $stats['sudah_ada'] = $this->where('status', self::STATUS_SILO_TERBIT)->countAllResults();
            
            $progresStatuses = [
                self::STATUS_PENGAJUAN_PJK3,
                self::STATUS_TESTING_PJK3,
                self::STATUS_SURAT_KETERANGAN_PJK3,
                self::STATUS_PENGAJUAN_UPTD,
                self::STATUS_PROSES_UPTD,
            ];
            $stats['progres'] = $this->whereIn('status', $progresStatuses)->countAllResults();

            // Count units without SILO
            $unitModel = new \App\Models\UnitAssetModel();
            $totalUnits = $unitModel->countAllResults();
            $unitsWithSilo = $this->countAllResults();
            $stats['belum_ada'] = max(0, $totalUnits - $unitsWithSilo);

            // Count expiring soon (30 days)
            $stats['expiring_soon'] = $this->where('status', self::STATUS_SILO_TERBIT)
                ->where('tanggal_expired_silo IS NOT NULL')
                ->where('tanggal_expired_silo <=', date('Y-m-d', strtotime('+30 days')))
                ->where('tanggal_expired_silo >=', date('Y-m-d'))
                ->countAllResults();

            // Count expired
            $stats['expired'] = $this->where('tanggal_expired_silo <', date('Y-m-d'))
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'SiloModel::getStatistics Error: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get next status in workflow
     */
    public function getNextStatus($currentStatus)
    {
        $workflow = [
            self::STATUS_BELUM_ADA => self::STATUS_PENGAJUAN_PJK3,
            self::STATUS_PENGAJUAN_PJK3 => self::STATUS_TESTING_PJK3,
            self::STATUS_TESTING_PJK3 => self::STATUS_SURAT_KETERANGAN_PJK3,
            self::STATUS_SURAT_KETERANGAN_PJK3 => self::STATUS_PENGAJUAN_UPTD,
            self::STATUS_PENGAJUAN_UPTD => self::STATUS_PROSES_UPTD,
            self::STATUS_PROSES_UPTD => self::STATUS_SILO_TERBIT,
        ];

        return $workflow[$currentStatus] ?? null;
    }

    /**
     * Check if unit can create new SILO application
     */
    public function canCreateApplication($unitId)
    {
        $existing = $this->getByUnitId($unitId);
        
        if (!$existing) {
            return true; // No existing SILO
        }

        // Can create new if current status is expired
        if ($existing['status'] === self::STATUS_SILO_EXPIRED) {
            return true;
        }

        // Can create new if expired date passed
        if (!empty($existing['tanggal_expired_silo']) && 
            $existing['tanggal_expired_silo'] < date('Y-m-d')) {
            return true;
        }

        return false;
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            self::STATUS_BELUM_ADA => 'Belum Ada SILO',
            self::STATUS_PENGAJUAN_PJK3 => 'Pengajuan ke PJK3',
            self::STATUS_TESTING_PJK3 => 'Testing PJK3',
            self::STATUS_SURAT_KETERANGAN_PJK3 => 'Surat Keterangan PJK3',
            self::STATUS_PENGAJUAN_UPTD => 'Pengajuan ke UPTD',
            self::STATUS_PROSES_UPTD => 'Proses UPTD',
            self::STATUS_SILO_TERBIT => 'SILO Terbit',
            self::STATUS_SILO_EXPIRED => 'SILO Expired',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get status color for badge
     */
    public function getStatusColor($status)
    {
        $colors = [
            self::STATUS_BELUM_ADA => 'danger',
            self::STATUS_PENGAJUAN_PJK3 => 'warning',
            self::STATUS_TESTING_PJK3 => 'warning',
            self::STATUS_SURAT_KETERANGAN_PJK3 => 'info',
            self::STATUS_PENGAJUAN_UPTD => 'warning',
            self::STATUS_PROSES_UPTD => 'warning',
            self::STATUS_SILO_TERBIT => 'success',
            self::STATUS_SILO_EXPIRED => 'danger',
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Add history record
     */
    public function addHistory($siloId, $statusLama, $statusBaru, $keterangan = null, $changedBy = null)
    {
        $historyData = [
            'silo_id' => $siloId,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'keterangan' => $keterangan,
            'changed_by' => $changedBy ?? session()->get('user_id'),
            'changed_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->table('silo_history')->insert($historyData);
    }

    /**
     * Get history for SILO
     */
    public function getHistory($siloId)
    {
        $builder = $this->db->table('silo_history sh');
        $builder->select('sh.*, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sh.changed_by', 'left');
        $builder->where('sh.silo_id', $siloId);
        $builder->orderBy('sh.changed_at', 'ASC');

        return $builder->get()->getResultArray();
    }
}

