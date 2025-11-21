<?php

namespace App\Models;

use CodeIgniter\Model;

class KontrakSpesifikasiModel extends Model
{
    protected $table            = 'kontrak_spesifikasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kontrak_id',
        'spek_kode',
        'jumlah_dibutuhkan',
        'jumlah_tersedia',
        'harga_per_unit_bulanan',
        'harga_per_unit_harian',
        'catatan_spek',
        'departemen_id',
        'tipe_unit_id',
        'tipe_jenis',
        'kapasitas_id',
        'merk_unit',
        'model_unit',
        'attachment_tipe',
        'attachment_merk',
        'jenis_baterai',
        'charger_id',
        'mast_id',
        'ban_id',
        'roda_id',
        'valve_id',
        'aksesoris'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'dibuat_pada';
    protected $updatedField  = 'diperbarui_pada';

    // Validation
    protected $validationRules = [
        'kontrak_id'        => 'required|integer',
        'spek_kode'         => 'required|max_length[50]',
        'jumlah_dibutuhkan' => 'required|integer|greater_than[0]',
        'jumlah_tersedia'   => 'permit_empty|integer|greater_than_equal_to[0]',
        'harga_per_unit_bulanan' => 'permit_empty|decimal',
        'harga_per_unit_harian'  => 'permit_empty|decimal',
        'departemen_id'     => 'permit_empty|integer',
        'tipe_unit_id'      => 'permit_empty|integer',
        'kapasitas_id'      => 'permit_empty|integer',
        'charger_id'        => 'permit_empty|integer',
        'mast_id'           => 'permit_empty|integer',
        'ban_id'            => 'permit_empty|integer',
        'roda_id'           => 'permit_empty|integer',
        'valve_id'          => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'kontrak_id' => [
            'required' => 'Kontrak ID harus diisi',
            'integer'  => 'Kontrak ID harus berupa angka'
        ],
        'spek_kode' => [
            'required'   => 'Kode spesifikasi harus diisi',
            'max_length' => 'Kode spesifikasi maksimal 50 karakter'
        ],
        'jumlah_dibutuhkan' => [
            'required'     => 'Jumlah unit harus diisi',
            'integer'      => 'Jumlah unit harus berupa angka',
            'greater_than' => 'Jumlah unit harus lebih dari 0'
        ],
        'jumlah_tersedia' => [
            'integer' => 'Jumlah tersedia harus berupa angka',
            'greater_than_equal_to' => 'Jumlah tersedia tidak boleh negatif'
        ],
        'departemen_id' => [
            'integer' => 'ID Departemen harus berupa angka'
        ],
        'tipe_unit_id' => [
            'integer' => 'ID Tipe Unit harus berupa angka'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all specifications for a contract with lookup names
     */
    public function getByKontrakId($kontrakId)
    {
        $result = $this->db->table($this->table . ' ks')
            ->select('
                ks.*,
                d.nama_departemen,
                tu.tipe as tipe_unit_name,
                tu.jenis as jenis_unit_name,
                k.kapasitas_unit as kapasitas_name,
                tm.tipe_mast as mast_name,
                tb.tipe_ban as ban_name,
                jr.tipe_roda as roda_name,
                v.jumlah_valve as valve_name,
                c.tipe_charger as charger_name,
                COALESCE(inventory_summary.actual_units, 0) as jumlah_tersedia,
                COALESCE(spk_summary.jumlah_spk, 0) as jumlah_spk
            ')
            ->join('departemen d', 'd.id_departemen = ks.departemen_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = ks.tipe_unit_id', 'left')
            ->join('kapasitas k', 'k.id_kapasitas = ks.kapasitas_id', 'left')
            ->join('tipe_mast tm', 'tm.id_mast = ks.mast_id', 'left')
            ->join('tipe_ban tb', 'tb.id_ban = ks.ban_id', 'left')
            ->join('jenis_roda jr', 'jr.id_roda = ks.roda_id', 'left')
            ->join('valve v', 'v.id_valve = ks.valve_id', 'left')
            ->join('charger c', 'c.id_charger = ks.charger_id', 'left')
            ->join('(
                SELECT 
                    kontrak_spesifikasi_id,
                    COUNT(*) as actual_units
                FROM inventory_unit 
                WHERE kontrak_spesifikasi_id IS NOT NULL
                GROUP BY kontrak_spesifikasi_id
            ) inventory_summary', 'ks.id = inventory_summary.kontrak_spesifikasi_id', 'left')
            ->join('(
                SELECT 
                    kontrak_spesifikasi_id,
                    COUNT(*) as jumlah_spk
                FROM spk 
                WHERE status NOT IN ("CANCELLED", "REJECTED")
                GROUP BY kontrak_spesifikasi_id
            ) spk_summary', 'ks.id = spk_summary.kontrak_spesifikasi_id', 'left')
            ->where('ks.kontrak_id', $kontrakId)
            ->orderBy('ks.spek_kode', 'ASC')
            ->get()
            ->getResultArray();
        
        log_message('debug', 'KontrakSpesifikasiModel::getByKontrakId - Query result for kontrak ' . $kontrakId . ': ' . json_encode($result));
        
        // Parse JSON aksesoris field for each record
        foreach ($result as &$row) {
            if (!empty($row['aksesoris'])) {
                $decoded = json_decode($row['aksesoris'], true);
                $row['aksesoris'] = is_array($decoded) ? $decoded : [];
            } else {
                $row['aksesoris'] = [];
            }
        }
        
        return $result;
    }

    /**
     * Get available units for a specification
     */
    public function getAvailableUnits($spesifikasiId)
    {
        $spek = $this->find($spesifikasiId);
        if (!$spek) return [];

        $db = \Config\Database::connect();
        $builder = $db->table('inventory_unit iu');
        
        $builder->select('
                iu.id_inventory_unit,
                iu.no_unit,
                iu.serial_number,
                iu.lokasi_unit,
                mu.merk_unit,
                mu.model_unit,
                tu.tipe,
                tu.jenis,
                k.kapasitas_unit,
                d.nama_departemen
            ')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
            ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
            ->where('iu.status_unit_id', 7) // STOCK
            ->where('iu.kontrak_id IS NULL'); // Not assigned

        // Filter berdasarkan spesifikasi
        if ($spek->departemen_id) {
            $builder->where('iu.departemen_id', $spek->departemen_id);
        }
        if ($spek->tipe_unit_id) {
            $builder->where('iu.tipe_unit_id', $spek->tipe_unit_id);
        }
        if ($spek->kapasitas_id) {
            $builder->where('iu.kapasitas_unit_id', $spek->kapasitas_id);
        }
        if ($spek->merk_unit) {
            $builder->like('mu.merk_unit', $spek->merk_unit);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get assigned units for a specification
     */
    public function getAssignedUnits($spesifikasiId)
    {
        $db = \Config\Database::connect();
        return $db->table('inventory_unit iu')
            ->select('
                iu.id_inventory_unit,
                iu.no_unit,
                iu.serial_number,
                iu.lokasi_unit,
                iu.harga_sewa_bulanan,
                iu.harga_sewa_harian,
                mu.merk_unit,
                mu.model_unit
            ')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->where('iu.kontrak_spesifikasi_id', $spesifikasiId)
            ->orderBy('iu.no_unit', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Assign units to specification with complete workflow data transfer
     */
    public function assignUnits($spesifikasiId, $unitIds, $hargaBulanan = null, $hargaHarian = null)
    {
        // Load helper for logging
        helper('activity_log');
        
        $spek = $this->find($spesifikasiId);
        if (!$spek) return false;

        $db = \Config\Database::connect();
        $db->transStart();

        // Get kontrak data for additional workflow information
        $kontrak = $db->table('kontrak')->where('id', $spek['kontrak_id'])->get()->getRowArray();

        foreach ($unitIds as $unitId) {
            // Get old unit data for logging
            $oldUnitData = $db->table('inventory_unit')
                             ->where('id_inventory_unit', $unitId)
                             ->get()->getRowArray();

            // Use harga from spesifikasi if not explicitly provided
            $finalHargaBulanan = $hargaBulanan ?? $spek['harga_per_unit_bulanan'];
            $finalHargaHarian = $hargaHarian ?? $spek['harga_per_unit_harian'];

            // Validate that harga is provided for unit specifications (not attachments)
            if (empty($finalHargaBulanan) && empty($spek['attachment_tipe'])) {
                log_message('error', "Cannot assign unit {$unitId} to specification {$spesifikasiId}: harga_per_unit_bulanan is required for unit specifications");
                throw new \Exception("Harga sewa bulanan harus diisi untuk spesifikasi unit");
            }

            // Get kontrak status to determine appropriate unit status
            $kontrakStatus = $kontrak['status'] ?? 'Pending';
            
            // Set status based on contract status
            $newStatusId = 7; // Default: STOCK ASET (available)
            if ($kontrakStatus === 'Aktif') {
                $newStatusId = 3; // RENTAL - only when contract is active
            } else {
                $newStatusId = 7; // Keep as STOCK ASET when contract is pending
            }

            $updateData = [
                'kontrak_id' => $spek['kontrak_id'],
                'kontrak_spesifikasi_id' => $spesifikasiId,
                'status_unit_id' => $newStatusId,
                'harga_sewa_bulanan' => $finalHargaBulanan,
                'harga_sewa_harian' => $finalHargaHarian,
                'lokasi_pelanggan' => $kontrak['pelanggan'] ?? null
            ];

            $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update($updateData);

            // Log the unit assignment
            log_assign('inventory_unit', $unitId, 
                format_activity_description('ASSIGN', 'inventory_unit', [
                    'nomor' => $kontrak['no_po_marketing'] ?? 'N/A',
                    'pelanggan' => $kontrak['pelanggan'] ?? 'N/A',
                    'harga' => $finalHargaBulanan
                ]), [
                    'workflow_stage' => 'KONTRAK',
                    'related_kontrak_id' => $spek['kontrak_id'],
                    'data' => [
                        'spesifikasi_id' => $spesifikasiId,
                        'old_status' => $oldUnitData['status_unit_id'] ?? null,
                        'new_status' => $newStatusId,
                        'kontrak_status' => $kontrakStatus,
                        'harga_bulanan' => $finalHargaBulanan,
                        'harga_harian' => $finalHargaHarian
                    ]
                ]
            );
        }

        // Update spesifikasi jumlah_tersedia
        $assignedCount = count($unitIds);
        $currentTersedia = $spek['jumlah_tersedia'] ?? 0;
        $this->update($spesifikasiId, [
            'jumlah_tersedia' => $currentTersedia + $assignedCount
        ]);

        // Log spesifikasi update
        log_update('kontrak_spesifikasi', $spesifikasiId,
            ['jumlah_tersedia' => $currentTersedia],
            ['jumlah_tersedia' => $currentTersedia + $assignedCount], [
                'description' => "Spesifikasi {$spek['spek_kode']} diperbarui: {$assignedCount} unit di-assign",
                'workflow_stage' => 'KONTRAK',
                'related_kontrak_id' => $spek['kontrak_id']
            ]
        );

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Unassign units from specification with complete workflow cleanup
     */
    public function unassignUnits($unitIds)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Get affected spesifikasi for count update
        $affectedSpeks = $db->table('inventory_unit')
            ->select('kontrak_spesifikasi_id')
            ->whereIn('id_inventory_unit', $unitIds)
            ->where('kontrak_spesifikasi_id IS NOT NULL')
            ->get()->getResultArray();

        // Reset units to stock status
        $updated = $db->table('inventory_unit')
            ->whereIn('id_inventory_unit', $unitIds)
            ->update([
                'kontrak_id' => null,
                'kontrak_spesifikasi_id' => null,
                'status_unit_id' => 7, // STOCK
                'workflow_status' => 'draft',
                'harga_sewa_bulanan' => null,
                'harga_sewa_harian' => null,
                'lokasi_pelanggan' => null,
                'aksesoris_spk' => null,
                'spk_id' => null,
                'di_id' => null
            ]);

        // Update spesifikasi counts
        foreach ($affectedSpeks as $spek) {
            if ($spek['kontrak_spesifikasi_id']) {
                $currentSpec = $this->find($spek['kontrak_spesifikasi_id']);
                if ($currentSpec) {
                    $this->update($spek['kontrak_spesifikasi_id'], [
                        'jumlah_tersedia' => max(0, ($currentSpec['jumlah_tersedia'] ?? 0) - 1)
                    ]);
                }
            }
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Generate next spec code for a contract
     */
    public function generateNextSpekKode($kontrakId)
    {
        $lastSpec = $this->where('kontrak_id', $kontrakId)
            ->orderBy('spek_kode', 'DESC')
            ->first();

        if (!$lastSpec) {
            return 'A';
        }

        $lastCode = $lastSpec['spek_kode'];
        return chr(ord($lastCode) + 1); // A -> B -> C -> etc
    }

    /**
     * Get specification summary for SPK creation
     */
    public function getSpekForSpk($kontrakId)
    {
        return $this->select('
                ks.id,
                ks.spek_kode,
                ks.jumlah_dibutuhkan,
                ks.jumlah_tersedia,
                ks.catatan_spek,
                CASE 
                    WHEN ks.jumlah_tersedia >= ks.jumlah_dibutuhkan THEN "READY"
                    WHEN ks.jumlah_tersedia > 0 THEN "PARTIAL"
                    ELSE "PENDING"
                END as status_spek
            ')
            ->where('kontrak_id', $kontrakId)
            ->orderBy('spek_kode', 'ASC')
            ->findAll();
    }
    
    /**
     * Generate next specification code for a contract
     */
    public function getNextSpekKode($kontrakId)
    {
        log_message('info', 'getNextSpekKode called for kontrakId: ' . $kontrakId);
        
        $lastSpec = $this->select('spek_kode')
            ->where('kontrak_id', $kontrakId)
            ->orderBy('spek_kode', 'DESC')
            ->first();
        
        log_message('info', 'getNextSpekKode - lastSpec result: ' . json_encode($lastSpec));
        
        if (!$lastSpec) {
            log_message('info', 'getNextSpekKode - no existing specs, returning SPEC-001');
            return 'SPEC-001';
        }
        
        // Extract number from last spec code (e.g., SPEC-001 -> 001)
        $lastNumber = (int) substr($lastSpec['spek_kode'], -3);
        $nextNumber = $lastNumber + 1;
        
        $nextCode = 'SPEC-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        log_message('info', 'getNextSpekKode - lastNumber: ' . $lastNumber . ', nextNumber: ' . $nextNumber . ', nextCode: ' . $nextCode);
        
        return $nextCode;
    }

    /**
     * Get contract specifications summary
     */
    public function getKontrakSummary($kontrakId)
    {
        $db = \Config\Database::connect();
        
        // Get summary data with actual inventory unit calculation
        $query = "
            SELECT 
                COUNT(ks.id) as total_spesifikasi,
                SUM(ks.jumlah_dibutuhkan) as total_unit_dibutuhkan,
                SUM(COALESCE(inventory_summary.actual_units, 0)) as total_unit_tersedia,
                SUM(ks.harga_per_unit_bulanan * ks.jumlah_dibutuhkan) as total_nilai_bulanan,
                SUM(ks.harga_per_unit_harian * ks.jumlah_dibutuhkan) as total_nilai_harian
            FROM kontrak_spesifikasi ks
            LEFT JOIN (
                SELECT 
                    kontrak_spesifikasi_id,
                    COUNT(*) as actual_units
                FROM inventory_unit 
                WHERE kontrak_spesifikasi_id IS NOT NULL
                GROUP BY kontrak_spesifikasi_id
            ) inventory_summary ON ks.id = inventory_summary.kontrak_spesifikasi_id
            WHERE ks.kontrak_id = ?
        ";
        
        $result = $db->query($query, [$kontrakId])->getRowArray();
        
        return [
            'total_spesifikasi' => (int)($result['total_spesifikasi'] ?? 0),
            'total_unit_dibutuhkan' => (int)($result['total_unit_dibutuhkan'] ?? 0),
            'total_unit_tersedia' => (int)($result['total_unit_tersedia'] ?? 0),
            'total_nilai_bulanan' => (float)($result['total_nilai_bulanan'] ?? 0),
            'total_nilai_harian' => (float)($result['total_nilai_harian'] ?? 0),
        ];
    }

    /**
     * Get contract specifications with details (alternative name for getByKontrakId)
     */
    public function getByKontrakWithDetails($kontrakId)
    {
        return $this->getByKontrakId($kontrakId);
    }

    /**
     * Get the insert ID
     */
    public function getInsertID()
    {
        return $this->insertID;
    }
    public function insert($data = null, bool $returnID = true)
    {
        log_message('info', 'KontrakSpesifikasiModel::insert - Starting insert process');
        log_message('info', 'KontrakSpesifikasiModel::insert - Data to insert: ' . json_encode($data));
        
        // Ensure foreign key checks are enabled
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        log_message('info', 'KontrakSpesifikasiModel::insert - Foreign key checks enabled');
        
        // Check current foreign key check status
        $fkCheckResult = $this->db->query('SELECT @@FOREIGN_KEY_CHECKS as fk_check')->getRow();
        log_message('info', 'KontrakSpesifikasiModel::insert - Foreign key check status: ' . ($fkCheckResult ? $fkCheckResult->fk_check : 'NULL'));
        
        // Validate foreign keys before insert
        if (isset($data['kontrak_id'])) {
            $kontrakExists = $this->db->table('kontrak')->where('id', $data['kontrak_id'])->countAllResults();
            log_message('info', 'KontrakSpesifikasiModel::insert - Kontrak validation: kontrak_id=' . $data['kontrak_id'] . ', exists=' . $kontrakExists);
            if ($kontrakExists == 0) {
                log_message('error', 'Foreign key constraint: kontrak_id ' . $data['kontrak_id'] . ' does not exist');
                $this->errors[] = 'Kontrak dengan ID ' . $data['kontrak_id'] . ' tidak ditemukan';
                return false;
            }
        }
        
        try {
            log_message('info', 'KontrakSpesifikasiModel::insert - Calling parent::insert');
            $result = parent::insert($data, $returnID);
            log_message('info', 'KontrakSpesifikasiModel::insert - Parent insert result: ' . json_encode($result));
            log_message('info', 'KontrakSpesifikasiModel::insert - Model insertID: ' . $this->insertID);
            log_message('info', 'KontrakSpesifikasiModel::insert - DB insertID: ' . $this->db->insertID());
            
            if ($result === false) {
                $dbError = $this->db->error();
                log_message('error', 'KontrakSpesifikasiModel::insert - Insert failed, database error: ' . json_encode($dbError));
                log_message('error', 'KontrakSpesifikasiModel::insert - Last query: ' . $this->db->getLastQuery());
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'KontrakSpesifikasiModel::insert - Exception caught: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Get database error after exception
            $dbError = $this->db->error();
            log_message('error', 'KontrakSpesifikasiModel::insert - Database error after exception: ' . json_encode($dbError));
            
            return false;
        }
    }
}
