<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KontrakModel;
use App\Models\KontrakSpesifikasiModel;
use App\Models\InventoryUnitModel;

class Kontrak extends BaseController
{
    protected $kontrakModel;
    protected $kontrakSpesifikasiModel;
    protected $inventoryUnitModel;
    protected $db;

    public function __construct()
    {
        $this->kontrakModel = new KontrakModel();
        $this->kontrakSpesifikasiModel = new KontrakSpesifikasiModel();
        $this->inventoryUnitModel = new InventoryUnitModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan halaman utama Manajemen Kontrak.
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Kontrak Rental',
        ];
        return view('marketing/kontrak', $data);
    }

    /**
     * Menyediakan data untuk DataTables Server-Side.
     */
    public function getDataTable()
    {
        try {
            $result = $this->kontrakModel->getDataTable($this->request);
            
            $data = [];
            
            foreach ($result['data'] as $contract) {
                $row = [];
                $row['id']              = $contract['id'];
                $row['contract_number'] = '<strong>' . esc($contract['no_kontrak']) . '</strong>';
                $row['po']              = esc(isset($contract['no_po_marketing']) ? $contract['no_po_marketing'] : '-');
                $row['client_name']     = esc($contract['pelanggan']);
                $row['period']          = date('d M Y', strtotime($contract['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($contract['tanggal_berakhir']));
                
                // Status badge with proper color
                $statusClass = 'bg-secondary';
                switch($contract['status']) {
                    case 'Aktif':
                        $statusClass = 'bg-success';
                        break;
                    case 'Pending':
                        $statusClass = 'bg-warning';
                        break;
                    case 'Berakhir':
                        $statusClass = 'bg-danger';
                        break;
                    case 'Dibatalkan':
                        $statusClass = 'bg-secondary';
                        break;
                }
                $row['status'] = '<span class="badge ' . $statusClass . '">' . esc($contract['status']) . '</span>';
                
                $row['actions'] = '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewContractUnits(' . $contract['id'] . ')" title="Lihat Unit Terkait">
                            <i class="fas fa-truck"></i>
                        </button>
                        <button class="btn btn-warning" onclick="editContract(' . $contract['id'] . ')" title="Edit Kontrak">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteContract(' . $contract['id'] . ')" title="Hapus Kontrak">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
                $data[] = $row;
            }

            $response = [
                "draw"            => intval($this->request->getPost('draw') ?? 1),
                "recordsTotal"    => $result['recordsTotal'],
                "recordsFiltered" => $result['recordsFiltered'],
                "data"            => $data,
                "stats"           => $result['stats']
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Kontrak::getDataTable: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Server error: ' . $e->getMessage(),
                'draw' => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }

    /**
     * Generate contract number
     */
    public function generateNumber()
    {
        try {
            // Get the latest contract number for current year
            $currentYear = date('Y');
            $prefix = 'KTR/' . $currentYear . '/';
            
            $latest = $this->kontrakModel
                ->like('no_kontrak', $prefix, 'after')
                ->orderBy('id', 'DESC')
                ->first();
            
            $sequence = 1;
            if ($latest && isset($latest['no_kontrak'])) {
                // Extract sequence number from the latest contract
                $parts = explode('/', $latest['no_kontrak']);
                if (count($parts) >= 3) {
                    $sequence = (int)$parts[2] + 1;
                }
            }
            
            $newNumber = $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => ['contract_number' => $newNumber],
                'csrf_hash' => csrf_hash()
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error generating contract number: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal generate nomor kontrak',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Check for duplicate contract number
     */
    public function checkDuplicate()
    {
        try {
            $contractNumber = trim($this->request->getPost('contract_number') ?? '');
            $excludeId = $this->request->getPost('exclude_id');
            
            if (empty($contractNumber)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nomor kontrak harus diisi',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            $query = $this->kontrakModel->where('no_kontrak', $contractNumber);
            
            if ($excludeId) {
                $query->where('id !=', $excludeId);
            }
            
            $existing = $query->first();
            
            if ($existing) {
                return $this->response->setJSON([
                    'success' => true,
                    'duplicate' => true,
                    'existing_id' => is_array($existing) ? ($existing['id'] ?? null) : ($existing->id ?? null),
                    'message' => 'Nomor kontrak sudah digunakan',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'duplicate' => false,
                'message' => 'Nomor kontrak tersedia',
                'csrf_hash' => csrf_hash()
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error checking duplicate contract: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memeriksa duplikasi nomor kontrak',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function store()
    {
        // Load simple logging helper
        helper('simple_activity_log');
        
        // Validasi input menggunakan model validation
        $data = [
            'no_kontrak'        => trim((string)$this->request->getPost('contract_number')),
            'no_po_marketing'   => $this->request->getPost('po_number'),
            'pelanggan'         => $this->request->getPost('client_name'),
            'pic'               => $this->request->getPost('pic') ?: null,
            'kontak'            => $this->request->getPost('kontak') ?: null,
            'lokasi'            => $this->request->getPost('lokasi') ?: null,
            'nilai_total'       => 0, // Akan dihitung otomatis dari spesifikasi
            'total_units'       => 0, // Akan dihitung otomatis dari spesifikasi
            'jenis_sewa'        => strtoupper($this->request->getPost('jenis_sewa') ?: 'BULANAN'),
            'tanggal_mulai'     => $this->request->getPost('start_date'),
            'tanggal_berakhir'  => $this->request->getPost('end_date'),
            'status'            => 'Pending', // Set otomatis ke Pending untuk kontrak baru
            'dibuat_oleh'       => session()->get('user_id') ?? 1, // Default user ID jika session kosong
        ];

        // Validate using KontrakModel
        if (!$this->kontrakModel->validate($data)) {
            $errors = $this->kontrakModel->errors();
            $existingId = null;

            // Check if it's a duplicate contract number
            if (!empty($errors['no_kontrak']) && strpos($errors['no_kontrak'], 'sudah digunakan') !== false) {
                $contractNumber = trim((string)$this->request->getPost('contract_number'));
                if ($contractNumber !== '') {
                    $existing = $this->kontrakModel->where('no_kontrak', $contractNumber)->first();
                    if ($existing) {
                        $existingId = is_array($existing) ? ($existing['id'] ?? null) : ($existing->id ?? null);
                    }
                }
            }

            // Log validation failure menggunakan simple logging
            log_activity('CREATE', 'kontrak', 0, "GAGAL membuat kontrak {$data['no_po_marketing']} - Validasi error: " . implode(', ', array_keys($errors)), null, $data);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $errors,
                'duplicate' => $existingId ? true : false,
                'existing_id' => $existingId,
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Insert the data
        $insertResult = $this->kontrakModel->insert($data);
        
        if (!$insertResult) {
            $errors = $this->kontrakModel->errors();
            $dbError = $this->db->error();
            $errorMessage = 'Gagal menyimpan data ke database: ' . (is_array($errors) ? implode(', ', $errors) : 'Unknown error') . 
                           ($dbError && isset($dbError['message']) ? ' | DB Error: ' . $dbError['message'] : '');
            
            // Log database failure menggunakan simple logging
            log_activity('CREATE', 'kontrak', 0, "GAGAL menyimpan kontrak {$data['no_po_marketing']} ke database - Error: {$errorMessage}", null, $data);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage,
                'csrf_hash' => csrf_hash()
            ]);
        }

        $newId = $this->kontrakModel->getInsertID();

        // Log successful creation menggunakan simple logging
        log_create('kontrak', $newId, "Berhasil membuat kontrak baru: {$data['no_po_marketing']} untuk pelanggan {$data['pelanggan']}", $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Kontrak berhasil ditambahkan',
            'data' => ['id' => $newId],
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Update kontrak
     */
    public function update($id)
    {
        // Debug logging untuk semua input
        log_message('debug', "=== Kontrak Update START ===");
        log_message('debug', "Contract ID from URL: $id");
        log_message('debug', "POST data: " . json_encode($this->request->getPost()));
        
        // Validate required fields first
        $rules = [
            'client_name'     => 'required',
            'start_date'      => 'required|valid_date',
            'end_date'        => 'required|valid_date',
            'status'          => 'required|in_list[Aktif,Pending,Berakhir,Dibatalkan]'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', "Validation failed: " . json_encode($this->validator->getErrors()));
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Validasi gagal.',
                'errors'  => $this->validator->getErrors(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        // Check for duplicate contract number (exclude current record)
        $contractNumber = trim($this->request->getPost('contract_number'));
        if (empty($contractNumber)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Nomor kontrak harus diisi.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        // Debug logging
        log_message('debug', "Update kontrak ID: $id, Contract Number: $contractNumber");
        
        // Cast ID to integer to ensure proper comparison
        $contractId = (int) $id;
        
        // Debug: Let's see the exact query being executed
        log_message('debug', "Searching for contracts with no_kontrak='$contractNumber' AND id != $contractId");
        
        $existing = $this->kontrakModel
            ->where('no_kontrak', $contractNumber)
            ->where('id !=', $contractId)
            ->first();

        log_message('debug', "Existing contract found: " . ($existing ? 'YES' : 'NO'));
        if ($existing) {
            log_message('debug', "Existing contract ID: " . (is_array($existing) ? $existing['id'] : $existing->id));
        }

        if ($existing) {
            // Debug: Mari kita lihat apa isi dari existing record
            $existingArray = is_array($existing) ? $existing : $existing->toArray();
            log_message('debug', "Existing record: " . json_encode($existingArray));
            
            return $this->response->setJSON([
                'success' => false, 
                'message' => "Nomor kontrak '$contractNumber' sudah digunakan oleh kontrak lain (ID: " . (is_array($existing) ? $existing['id'] : $existing->id) . "). Current edit ID: $contractId",
                'debug_info' => [
                    'current_id' => $contractId,
                    'existing_id' => is_array($existing) ? $existing['id'] : $existing->id,
                    'contract_number' => $contractNumber
                ],
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $data = [
            'no_kontrak'        => $contractNumber,
            'no_po_marketing'   => $this->request->getPost('po_number'),
            'pelanggan'         => $this->request->getPost('client_name'),
            'pic'               => $this->request->getPost('pic') ?: null,
            'kontak'            => $this->request->getPost('kontak') ?: null,
            'lokasi'            => $this->request->getPost('lokasi'),
            'nilai_total'       => $this->request->getPost('contract_value') ?: 0,
            'total_units'       => $this->request->getPost('total_units') ?: 0,
            'tanggal_mulai'     => $this->request->getPost('start_date'),
            'tanggal_berakhir'  => $this->request->getPost('end_date'),
            'status'            => $this->request->getPost('status'),
            'jenis_sewa'        => $this->request->getPost('jenis_sewa') ?: 'BULANAN',
            'catatan'           => $this->request->getPost('catatan'),
        ];

        // Disable model validation temporarily for update to avoid is_unique conflict
        $this->kontrakModel->skipValidation(true);
        
        if ($this->kontrakModel->update($contractId, $data)) {
            log_message('debug', "Kontrak updated successfully");
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Kontrak berhasil diperbarui.',
                'csrf_hash' => csrf_hash(),
            ]);
        } else {
            log_message('debug', "Kontrak update failed: " . json_encode($this->kontrakModel->errors()));
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal memperbarui data: ' . implode(', ', $this->kontrakModel->errors()),
                'csrf_hash' => csrf_hash(),
            ]);
        }
    }

    /**
     * Hapus kontrak
     */
    public function delete($id)
    {
        log_message('debug', '=== Kontrak::delete START ===');
        log_message('debug', 'Kontrak::delete called with ID: ' . $id);
        log_message('debug', 'Kontrak::delete - Raw ID: ' . var_export($id, true));
        log_message('debug', 'Kontrak::delete - Request method: ' . $this->request->getMethod());
        log_message('debug', 'Kontrak::delete - POST data: ' . json_encode($this->request->getPost()));
        
        // Validate ID
        if (!$id || $id == '0' || $id == 0) {
            log_message('error', 'Kontrak::delete - Invalid ID: ' . $id);
            log_message('debug', '=== Kontrak::delete END (invalid ID) ===');
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'ID kontrak tidak valid.'
            ]);
        }

        // Check if contract exists
        $contract = $this->kontrakModel->find($id);
        if (!$contract) {
            log_message('error', 'Kontrak::delete - Contract not found with ID: ' . $id);
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Kontrak tidak ditemukan.'
            ]);
        }

        // Check if contract has related records that might cause issues
        $db = \Config\Database::connect();
        
        // Check for related kontrak_spesifikasi records
        $spekCount = $db->table('kontrak_spesifikasi')->where('kontrak_id', $id)->countAllResults();
        log_message('debug', 'Kontrak::delete - Contract has ' . $spekCount . ' spesifikasi records');
        
        // Check for related inventory_unit records
        $unitCount = $db->table('inventory_unit')->where('kontrak_id', $id)->countAllResults();
        log_message('debug', 'Kontrak::delete - Contract has ' . $unitCount . ' inventory_unit records');
        
        log_message('debug', 'Kontrak::delete - Attempting to delete contract: ' . $contract['no_kontrak']);
        
        try {
            // Use database transaction for safety
            $db->transStart();
            
            $result = $this->kontrakModel->delete($id);
            log_message('debug', 'Kontrak::delete - Delete result: ' . ($result ? 'true' : 'false'));
            
            if ($result) {
                $db->transComplete();
                log_message('debug', 'Kontrak::delete - Successfully deleted contract ID: ' . $id);
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Kontrak berhasil dihapus.'
                ]);
            } else {
                $db->transRollback();
                log_message('error', 'Kontrak::delete - Delete returned false for contract ID: ' . $id);
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Gagal menghapus kontrak.'
                ]);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Kontrak::delete - Exception: ' . $e->getMessage());
            log_message('error', 'Kontrak::delete - Exception trace: ' . $e->getTraceAsString());
            log_message('debug', '=== Kontrak::delete END (exception) ===');
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get contract detail
     */
    public function detail($id)
    {
        // Validate ID
        if (!$id || $id == '0' || $id == 0) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'ID kontrak tidak valid.'
            ]);
        }

        $contract = $this->kontrakModel->find($id);
        if ($contract) {
            return $this->response->setJSON([
                'success' => true, 
                'data' => $contract
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Kontrak tidak ditemukan.'
            ]);
        }
    }

    /**
     * Edit contract - return view with data
     */
    public function edit($id)
    {
        // Validate ID
        if (!$id || $id == '0' || $id == 0) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'ID kontrak tidak valid.'
            ]);
        }

        $contract = $this->kontrakModel->find($id);
        if (!$contract) {
            return redirect()->to('marketing/contracts')->with('error', 'Kontrak tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Kontrak',
            'contract' => $contract
        ];
        
        return view('marketing/kontrak_edit', $data);
    }

    /**
     * Get contract details for SPK creation
     */
    public function get($kontrakId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $kontrak = $this->kontrakModel->find($kontrakId);
            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $kontrak,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Get units related to a contract for display in detail modal.
     */
    public function getContractUnits($kontrakId)
    {
        try {
            // Validasi kontrak ID
            $kontrak = $this->kontrakModel->find($kontrakId);
            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }
            
            // Query dengan JOIN untuk mendapatkan data yang lebih lengkap
            $query = "
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    COALESCE(mu.merk_unit, 'N/A') as merk,
                    COALESCE(mu.model_unit, 'N/A') as model,
                    COALESCE(k.kapasitas_unit, 'N/A') as kapasitas,
                    COALESCE(CONCAT(tu.tipe, ' ', tu.jenis), 'N/A') as jenis_unit,
                    COALESCE(d.nama_departemen, 'N/A') as departemen,
                    COALESCE(su.status_unit, 'TERSEDIA') as status,
                    iu.status_unit_id,
                    iu.kontrak_id,
                    iu.harga_sewa_bulanan,
                    iu.harga_sewa_harian
                FROM inventory_unit iu
                LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                LEFT JOIN kapasitas k ON iu.kapasitas_unit_id = k.id_kapasitas
                LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
                LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
                WHERE iu.kontrak_id = ?
                ORDER BY iu.no_unit ASC
            ";
            
            $result = $this->db->query($query, [$kontrakId]);
            $units = $result->getResultArray();
            
            // Format response
            $formattedUnits = [];
            $totalHargaBulanan = 0;
            $totalHargaHarian = 0;
            
            foreach ($units as $unit) {
                $hargaBulanan = (float)($unit['harga_sewa_bulanan'] ?? 0);
                $hargaHarian = (float)($unit['harga_sewa_harian'] ?? 0);
                
                $totalHargaBulanan += $hargaBulanan;
                $totalHargaHarian += $hargaHarian;
                
                $formattedUnits[] = [
                    'id' => $unit['id_inventory_unit'],
                    'no_unit' => $unit['no_unit'] ?: '-',
                    'merk' => $unit['merk'],
                    'model' => $unit['model'],
                    'kapasitas' => $unit['kapasitas'],
                    'jenis_unit' => $unit['jenis_unit'],
                    'departemen' => $unit['departemen'],
                    'status' => $unit['status'],
                    'harga_per_unit_bulanan' => $hargaBulanan,
                    'harga_per_unit_harian' => $hargaHarian
                ];
            }
            
            // Get summary data from kontrak_spesifikasi
            $summaryQuery = "
                SELECT 
                    COUNT(*) as total_spesifikasi,
                    COALESCE(SUM(jumlah_dibutuhkan), 0) as total_unit_dibutuhkan,
                    COALESCE(SUM(harga_per_unit_bulanan * jumlah_dibutuhkan), 0) as total_nilai_bulanan,
                    COALESCE(SUM(harga_per_unit_harian * jumlah_dibutuhkan), 0) as total_nilai_harian
                FROM kontrak_spesifikasi
                WHERE kontrak_id = ?
            ";
            
            $summaryResult = $this->db->query($summaryQuery, [$kontrakId]);
            $summary = $summaryResult->getRowArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedUnits,
                'summary' => [
                    'total_spesifikasi' => (int)($summary['total_spesifikasi'] ?? 0),
                    'total_unit_dibutuhkan' => (int)($summary['total_unit_dibutuhkan'] ?? 0),
                    'total_nilai_bulanan' => (float)($summary['total_nilai_bulanan'] ?? 0),
                    'total_nilai_harian' => (float)($summary['total_nilai_harian'] ?? 0),
                    'unit_tersedia' => count($formattedUnits)
                ],
                'total' => count($formattedUnits),
                'kontrak_id' => $kontrakId,
                'kontrak_number' => $kontrak['no_kontrak']
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getContractUnits Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving contract units: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get specifications for a contract
     */
    public function getKontrakSpesifikasi($kontrakId)
    {
        try {
            log_message('debug', 'getKontrakSpesifikasi called with ID: ' . $kontrakId);
            
            $kontrak = $this->kontrakModel->find($kontrakId);
            if (!$kontrak) {
                log_message('debug', 'Kontrak not found for ID: ' . $kontrakId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }

            $spesifikasi = $this->kontrakSpesifikasiModel->getByKontrakId($kontrakId);
            $summary = $this->kontrakSpesifikasiModel->getKontrakSummary($kontrakId);

            log_message('debug', 'Found ' . count($spesifikasi) . ' spesifikasi for kontrak ' . $kontrakId);
            log_message('debug', 'Spesifikasi data: ' . json_encode($spesifikasi));
            log_message('debug', 'Summary data: ' . json_encode($summary));

            return $this->response->setJSON([
                'success' => true,
                'data' => $spesifikasi,
                'summary' => $summary,
                'kontrak' => $kontrak
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getKontrakSpesifikasi Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving contract specifications: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Add new specification to contract
     */
    public function addSpesifikasi()
    {
        // Debug logging
        log_message('info', 'Kontrak::addSpesifikasi - Method called');
        log_message('info', 'Kontrak::addSpesifikasi - Request received');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        
        try {
            $kontrakId = $this->request->getPost('kontrak_id');
            
            // Debug kontrak ID
            log_message('info', 'Kontrak ID received: ' . $kontrakId);
            
            if (!$kontrakId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak ID wajib diisi'
                ]);
            }
            
            $kontrak = $this->kontrakModel->find($kontrakId);
            
            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }

            // Generate next specification code
            $spekKode = $this->kontrakSpesifikasiModel->getNextSpekKode($kontrakId);
            log_message('info', 'Generated spek_kode: ' . $spekKode . ' for kontrak_id: ' . $kontrakId);

            // Get all form data
            $departemenId = $this->request->getPost('departemen_id');
            $tipeUnitId = $this->request->getPost('tipe_unit_id');
            $kapasitasId = $this->request->getPost('kapasitas_id');
            $chargerId = $this->request->getPost('charger_id');
            $mastId = $this->request->getPost('mast_id');
            $banId = $this->request->getPost('ban_id');
            $rodaId = $this->request->getPost('roda_id');
            $valveId = $this->request->getPost('valve_id');

            // Validate foreign key references before inserting
            $validationErrors = [];
            log_message('info', 'Starting foreign key validation for contract ' . $kontrakId);
            log_message('info', 'IDs to validate: kapasitas_id=' . $kapasitasId . ', mast_id=' . $mastId . ', charger_id=' . $chargerId . ', ban_id=' . $banId . ', roda_id=' . $rodaId . ', valve_id=' . $valveId);
            
            // Validate departemen_id
            if (!empty($departemenId)) {
                $deptCount = $this->db->table('departemen')->where('id_departemen', (int)$departemenId)->countAllResults();
                log_message('info', 'Validating departemen_id=' . $departemenId . ', count=' . $deptCount);
                if ($deptCount == 0) {
                    $validationErrors[] = 'Departemen yang dipilih tidak valid';
                    log_message('error', 'Invalid departemen_id: ' . $departemenId);
                }
            }
            
            // Validate tipe_unit_id
            if (!empty($tipeUnitId)) {
                $tipeCount = $this->db->table('tipe_unit')->where('id_tipe_unit', (int)$tipeUnitId)->countAllResults();
                log_message('info', 'Validating tipe_unit_id=' . $tipeUnitId . ', count=' . $tipeCount);
                if ($tipeCount == 0) {
                    $validationErrors[] = 'Tipe Unit yang dipilih tidak valid';
                    log_message('error', 'Invalid tipe_unit_id: ' . $tipeUnitId);
                }
            }
            
            // Validate kapasitas_id
            if (!empty($kapasitasId)) {
                $kapasitasCount = $this->db->table('kapasitas')->where('id_kapasitas', (int)$kapasitasId)->countAllResults();
                log_message('info', 'Validating kapasitas_id=' . $kapasitasId . ', count=' . $kapasitasCount);
                if ($kapasitasCount == 0) {
                    $validationErrors[] = 'Kapasitas yang dipilih tidak valid';
                    log_message('error', 'Invalid kapasitas_id: ' . $kapasitasId);
                }
            }
            
            // Validate charger_id
            if (!empty($chargerId)) {
                $chargerCount = $this->db->table('charger')->where('id_charger', (int)$chargerId)->countAllResults();
                log_message('info', 'Validating charger_id=' . $chargerId . ', count=' . $chargerCount);
                if ($chargerCount == 0) {
                    $validationErrors[] = 'Charger yang dipilih tidak valid';
                    log_message('error', 'Invalid charger_id: ' . $chargerId);
                }
            }
            
            // Validate mast_id
            if (!empty($mastId)) {
                $mastCount = $this->db->table('tipe_mast')->where('id_mast', (int)$mastId)->countAllResults();
                log_message('info', 'Validating mast_id=' . $mastId . ', count=' . $mastCount);
                if ($mastCount == 0) {
                    $validationErrors[] = 'Tipe Mast yang dipilih tidak valid';
                    log_message('error', 'Invalid mast_id: ' . $mastId);
                }
            }
            
            // Validate ban_id
            if (!empty($banId)) {
                $banCount = $this->db->table('tipe_ban')->where('id_ban', (int)$banId)->countAllResults();
                log_message('info', 'Validating ban_id=' . $banId . ', count=' . $banCount);
                if ($banCount == 0) {
                    $validationErrors[] = 'Tipe Ban yang dipilih tidak valid';
                    log_message('error', 'Invalid ban_id: ' . $banId);
                }
            }
            
            // Validate roda_id
            if (!empty($rodaId)) {
                $rodaCount = $this->db->table('jenis_roda')->where('id_roda', (int)$rodaId)->countAllResults();
                log_message('info', 'Validating roda_id=' . $rodaId . ', count=' . $rodaCount);
                if ($rodaCount == 0) {
                    $validationErrors[] = 'Jenis Roda yang dipilih tidak valid';
                    log_message('error', 'Invalid roda_id: ' . $rodaId);
                }
            }
            
            // Validate valve_id
            if (!empty($valveId)) {
                $valveCount = $this->db->table('valve')->where('id_valve', (int)$valveId)->countAllResults();
                log_message('info', 'Validating valve_id=' . $valveId . ', count=' . $valveCount);
                if ($valveCount == 0) {
                    $validationErrors[] = 'Valve yang dipilih tidak valid';
                    log_message('error', 'Invalid valve_id: ' . $valveId);
                }
            }
            
            // If there are validation errors, return them
            log_message('info', 'Foreign key validation completed. Errors found: ' . count($validationErrors));
            if (!empty($validationErrors)) {
                log_message('error', 'Kontrak::addSpesifikasi - Foreign key validation errors: ' . json_encode($validationErrors));
                log_message('error', 'POST data that caused errors: ' . json_encode($this->request->getPost()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data yang dipilih tidak valid: ' . implode(', ', $validationErrors),
                    'validation_errors' => $validationErrors,
                    'csrf_hash' => csrf_hash()
                ]);
            }

            $data = [
                'kontrak_id' => $kontrakId,
                'spek_kode' => $spekKode,
                'jumlah_dibutuhkan' => $this->request->getPost('jumlah_dibutuhkan') ?: 1,
                'jumlah_tersedia' => 0, // Set default value explicitly
                'harga_per_unit_bulanan' => $this->request->getPost('harga_per_unit_bulanan') ?: null,
                'harga_per_unit_harian' => $this->request->getPost('harga_per_unit_harian') ?: null,
                'catatan_spek' => $this->request->getPost('catatan_spek'),
                'departemen_id' => $departemenId,
                'tipe_unit_id' => $tipeUnitId,
                'tipe_jenis' => $this->request->getPost('tipe_jenis'),
                'kapasitas_id' => $kapasitasId,
                'merk_unit' => $this->request->getPost('merk_unit'),
                'model_unit' => $this->request->getPost('model_unit'),
                'attachment_tipe' => $this->request->getPost('attachment_tipe'),
                'attachment_merk' => $this->request->getPost('attachment_merk'),
                'jenis_baterai' => $this->request->getPost('jenis_baterai'),
                'charger_id' => $chargerId,
                'mast_id' => $mastId,
                'ban_id' => $banId,
                'roda_id' => $rodaId,
                'valve_id' => $valveId,
                'aksesoris' => $this->request->getPost('aksesoris') ? json_encode($this->request->getPost('aksesoris')) : null
            ];
            
            // Debug specific kapasitas_id field
            log_message('info', 'Kapasitas ID from POST: ' . ($this->request->getPost('kapasitas_id') ?: 'NULL'));
            log_message('info', 'Kapasitas ID in data array: ' . ($data['kapasitas_id'] ?: 'NULL'));
            
            // Debug data yang akan diinsert
            log_message('info', 'Data to insert: ' . json_encode($data));
            log_message('info', 'Data spek_kode: ' . $data['spek_kode'] . ', kontrak_id: ' . $data['kontrak_id']);

            // Try to insert with comprehensive error handling
            $insertResult = $this->kontrakSpesifikasiModel->insert($data);
            
            // If insert didn't fail (result is not false), consider it successful
            if ($insertResult !== false) {
                log_message('info', 'Insert did not fail, considering it successful. Result: ' . json_encode($insertResult));
                
                // Try to get the insert ID
                $spesifikasiId = $this->kontrakSpesifikasiModel->getInsertID();
                
                // If we can't get the insert ID, try to find the record
                if (!$spesifikasiId) {
                    log_message('info', 'Insert ID not available, trying to find record');
                    $query = $this->db->table('kontrak_spesifikasi')
                        ->where('kontrak_id', $kontrakId)
                        ->where('spek_kode', $data['spek_kode'])
                        ->get();
                    
                    if ($query->getNumRows() > 0) {
                        $inserted = $query->getRow();
                        $spesifikasiId = $inserted->id;
                        log_message('info', 'Found record with ID: ' . $spesifikasiId);
                    }
                }
                
                // Even if we can't find the record, if insert didn't fail, consider it successful
                if (!$spesifikasiId) {
                    log_message('warning', 'Could not find inserted record, but insert did not fail. Using generated spek_kode: ' . $data['spek_kode']);
                    // Create a dummy ID or use the spek_kode for success response
                    $spesifikasiId = 'inserted_' . $data['spek_kode'];
                }
                
                log_message('info', 'SUCCESS: Spesifikasi insert completed with ID: ' . $spesifikasiId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Spesifikasi berhasil ditambahkan',
                    'spesifikasi_id' => $spesifikasiId,
                    'spek_kode' => $spekKode,
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                log_message('error', 'FAILURE: Insert failed with result: ' . json_encode($insertResult));
                // Get detailed error information
                $errors = $this->kontrakSpesifikasiModel->errors();
                $dbError = $this->db->error();
                
                // Try to get more detailed error info
                $lastQuery = $this->db->getLastQuery();
                $errorCode = $dbError['code'] ?? 0;
                $errorMessage = $dbError['message'] ?? '';
                
                log_message('error', 'Kontrak::addSpesifikasi - Insert result: ' . json_encode($insertResult));
                log_message('error', 'Kontrak::addSpesifikasi - Could not find inserted record');
                log_message('error', 'Kontrak::addSpesifikasi - Model validation errors: ' . json_encode($errors));
                log_message('error', 'Kontrak::addSpesifikasi - Database error: ' . json_encode($dbError));
                log_message('error', 'Kontrak::addSpesifikasi - Last query: ' . $lastQuery);
                
                // Construct a more informative error message
                $errorMsg = 'Gagal menyimpan spesifikasi.';
                
                if (!empty($errors)) {
                    $errorMsg .= ' Validation errors: ' . implode(', ', $errors);
                } elseif (!empty($errorMessage)) {
                    $errorMsg .= ' Database error: ' . $errorMessage;
                } elseif ($errorCode > 0) {
                    $errorMsg .= ' Database error code: ' . $errorCode;
                } elseif ($insertResult === false) {
                    $errorMsg .= ' Insert failed.';
                } else {
                    $errorMsg .= ' Could not verify data was saved.';
                }
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg,
                    'validation_errors' => $errors,
                    'db_error' => $dbError,
                    'debug_info' => [
                        'last_query' => $lastQuery,
                        'db_error_code' => $errorCode,
                        'db_error_message' => $errorMessage,
                        'insert_result' => $insertResult,
                        'searched_kontrak_id' => $kontrakId,
                        'searched_spek_kode' => $data['spek_kode']
                    ],
                    'csrf_hash' => csrf_hash()
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::addSpesifikasi Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error adding specification: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Update specification
     */
    public function updateSpesifikasi($spesifikasiId)
    {
        try {
            $spesifikasi = $this->kontrakSpesifikasiModel->find($spesifikasiId);
            if (!$spesifikasi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan'
                ]);
            }

            $data = [
                'jumlah_dibutuhkan' => $this->request->getPost('jumlah_dibutuhkan') ?: $spesifikasi['jumlah_dibutuhkan'],
                'harga_per_unit_bulanan' => $this->request->getPost('harga_per_unit_bulanan'),
                'harga_per_unit_harian' => $this->request->getPost('harga_per_unit_harian'),
                'catatan_spek' => $this->request->getPost('catatan_spek'),
                'departemen_id' => $this->request->getPost('departemen_id'),
                'tipe_unit_id' => $this->request->getPost('tipe_unit_id'),
                'tipe_jenis' => $this->request->getPost('tipe_jenis'),
                'kapasitas_id' => $this->request->getPost('kapasitas_id'),
                'merk_unit' => $this->request->getPost('merk_unit'),
                'model_unit' => $this->request->getPost('model_unit'),
                'attachment_tipe' => $this->request->getPost('attachment_tipe'),
                'attachment_merk' => $this->request->getPost('attachment_merk'),
                'jenis_baterai' => $this->request->getPost('jenis_baterai'),
                'charger_id' => $this->request->getPost('charger_id'),
                'mast_id' => $this->request->getPost('mast_id'),
                'ban_id' => $this->request->getPost('ban_id'),
                'roda_id' => $this->request->getPost('roda_id'),
                'valve_id' => $this->request->getPost('valve_id'),
                'aksesoris' => $this->request->getPost('aksesoris') ? json_encode($this->request->getPost('aksesoris')) : null
            ];

            // Remove null values to avoid overwriting existing data
            $data = array_filter($data, function($value) {
                return $value !== null && $value !== '';
            });

            if ($this->kontrakSpesifikasiModel->update($spesifikasiId, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Spesifikasi berhasil diperbarui'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui spesifikasi: ' . implode(', ', $this->kontrakSpesifikasiModel->errors())
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::updateSpesifikasi Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating specification: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get specification detail for editing
     */
    public function spesifikasiDetail($spesifikasiId)
    {
        try {
            $builder = $this->db->table('kontrak_spesifikasi ks');
            $builder->select('ks.*, tu.tipe as tipe_unit_nama');
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = ks.tipe_unit_id', 'left');
            $builder->where('ks.id', $spesifikasiId);
            
            $spesifikasi = $builder->get()->getRowArray();
            
            if (!$spesifikasi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $spesifikasi,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::spesifikasiDetail Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting specification detail: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete specification
     */
    public function deleteSpesifikasi($spesifikasiId)
    {
        try {
            // Validate request method
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Invalid request method'
                ]);
            }

            // Log the incoming request for debugging
            log_message('debug', 'Delete spesifikasi request for ID: ' . $spesifikasiId);

            $spesifikasi = $this->kontrakSpesifikasiModel->find($spesifikasiId);
            if (!$spesifikasi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan'
                ]);
            }

            // Check if units are assigned to this specification
            $assignedUnits = $this->inventoryUnitModel
                ->where('kontrak_spesifikasi_id', $spesifikasiId)
                ->countAllResults();

            if ($assignedUnits > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Tidak dapat menghapus spesifikasi. Masih ada {$assignedUnits} unit yang terkait."
                ]);
            }

            // Attempt to delete
            $deleteResult = $this->kontrakSpesifikasiModel->delete($spesifikasiId);
            log_message('debug', 'Delete result: ' . ($deleteResult ? 'success' : 'failed'));

            if ($deleteResult) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Spesifikasi berhasil dihapus',
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                $errors = $this->kontrakSpesifikasiModel->errors();
                log_message('error', 'Delete spesifikasi failed with errors: ' . json_encode($errors));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus spesifikasi: ' . (is_array($errors) ? implode(', ', $errors) : 'Unknown error')
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::deleteSpesifikasi Error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error deleting specification: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available units for assignment to specification
     */
    public function getAvailableUnits()
    {
        try {
            $spesifikasiId = $this->request->getGet('spesifikasi_id');
            $spesifikasi = $this->kontrakSpesifikasiModel->find($spesifikasiId);
            
            if (!$spesifikasi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan'
                ]);
            }

            // Build query for available units based on specification criteria
            $builder = $this->db->table('inventory_unit iu');
            $builder->select('iu.*, mu.merk_unit, mu.model_unit, d.nama_departemen, tu.tipe, tu.jenis, k.kapasitas_unit, su.status_unit');
            $builder->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
            $builder->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left');
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
            $builder->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left');
            $builder->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left');
            
            // Filter by specification criteria
            $builder->where('iu.kontrak_id IS NULL'); // Not assigned to any contract
            $builder->where('iu.status_unit_id', 7); // STOK status only
            
            if ($spesifikasi->departemen_id) {
                $builder->where('iu.departemen_id', $spesifikasi->departemen_id);
            }
            if ($spesifikasi->tipe_unit_id) {
                $builder->where('iu.tipe_unit_id', $spesifikasi->tipe_unit_id);
            }
            if ($spesifikasi->kapasitas_id) {
                $builder->where('iu.kapasitas_unit_id', $spesifikasi->kapasitas_id);
            }
            if ($spesifikasi->merk_unit) {
                $builder->where('mu.merk_unit', $spesifikasi->merk_unit);
            }

            $units = $builder->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $units,
                'spesifikasi' => $spesifikasi
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getAvailableUnits Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving available units: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Debug method to test spesifikasi insert
     */
    public function debugTestInsert()
    {
        // Allow access in development environment
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Debug endpoint only available in development mode'
            ]);
        }
        
        try {
            // Get first contract for testing
            $kontrak = $this->kontrakModel->first();
            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No contracts found'
                ]);
            }
            
            // Generate test spec code
            $spekKode = 'DEBUG-' . date('His');
            
            // Prepare minimal test data
            $testData = [
                'kontrak_id' => $kontrak->id,
                'spek_kode' => $spekKode,
                'jumlah_dibutuhkan' => 1,
                'jumlah_tersedia' => 0,
                'catatan_spek' => 'Debug test insert'
            ];
            
            log_message('info', 'Debug test insert data: ' . json_encode($testData));
            
            $insertId = $this->kontrakSpesifikasiModel->insert($testData);
            
            if ($insertId) {
                $inserted = $this->kontrakSpesifikasiModel->find($insertId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Debug insert successful',
                    'insert_id' => $insertId,
                    'inserted_data' => $inserted,
                    'test_data' => $testData
                ]);
            } else {
                $errors = $this->kontrakSpesifikasiModel->errors();
                $dbError = $this->db->error();
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Debug insert failed',
                    'validation_errors' => $errors,
                    'db_error' => $dbError,
                    'test_data' => $testData
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Debug insert exception: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Assign units to specification with complete workflow data transfer
     */
    public function assignUnitsToSpesifikasi()
    {
        try {
            $spesifikasiId = $this->request->getPost('spesifikasi_id');
            $unitIds = $this->request->getPost('unit_ids');
            
            if (!$spesifikasiId || !$unitIds || !is_array($unitIds)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }

            $spesifikasi = $this->kontrakSpesifikasiModel->find($spesifikasiId);
            if (!$spesifikasi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan'
                ]);
            }

            // Convert object to array if needed
            if (is_object($spesifikasi)) {
                $spesifikasi = $spesifikasi->toArray();
            }

            $kontrak = $this->kontrakModel->find($spesifikasi['kontrak_id']);
            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }

            // Convert kontrak object to array if needed
            if (is_object($kontrak)) {
                $kontrak = $kontrak->toArray();
            }

            // Use the enhanced assignUnits method with automatic price transfer
            $result = $this->kontrakSpesifikasiModel->assignUnits($spesifikasiId, $unitIds);

            if (!$result) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengassign unit ke spesifikasi'
                ]);
            }

            $successCount = count($unitIds);

            return $this->response->setJSON([
                'success' => true,
                'message' => "{$successCount} unit berhasil di-assign ke spesifikasi {$spesifikasi['spek_kode']} dengan harga Rp " . number_format($spesifikasi['harga_per_unit_bulanan'], 0, ',', '.') . "/bulan",
                'data' => [
                    'assigned_count' => $successCount,
                    'harga_bulanan' => $spesifikasi['harga_per_unit_bulanan'],
                    'harga_harian' => $spesifikasi['harga_per_unit_harian'],
                    'lokasi_pelanggan' => $kontrak['pelanggan']
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::assignUnitsToSpesifikasi Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error assigning units: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Alias method for frontend routing: /marketing/kontrak/spesifikasi/{kontrakId}
     */
    public function spesifikasi($kontrakId)
    {
        return $this->getKontrakSpesifikasi($kontrakId);
    }
}
