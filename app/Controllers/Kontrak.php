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
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data ke database: ' . (is_array($errors) ? implode(', ', $errors) : 'Unknown error') . 
                           ($dbError && isset($dbError['message']) ? ' | DB Error: ' . $dbError['message'] : ''),
                'csrf_hash' => csrf_hash()
            ]);
        }

        $newId = $this->kontrakModel->getInsertID();

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
        $rules = [
            'contract_number' => "required|is_unique[kontrak.no_kontrak,id,$id]",
            'client_name'     => 'required',
            'start_date'      => 'required|valid_date',
            'end_date'        => 'required|valid_date',
            'status'          => 'required|in_list[Aktif,Pending,Berakhir,Dibatalkan]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Validasi gagal.',
                'errors'  => $this->validator->getErrors(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $data = [
            'no_kontrak'        => $this->request->getPost('contract_number'),
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
        ];

        if ($this->kontrakModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Kontrak berhasil diperbarui.',
                'csrf_hash' => csrf_hash(),
            ]);
        } else {
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
            
            // Query dengan foreign key yang benar - simplified untuk debugging
            $query = "
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    'N/A' as merk,
                    'N/A' as model,
                    'N/A' as kapasitas,
                    'N/A' as jenis_unit,
                    'N/A' as departemen,
                    'TERSEDIA' as status,
                    iu.status_unit_id,
                    iu.kontrak_id
                FROM inventory_unit iu
                WHERE iu.kontrak_id = ?
                ORDER BY iu.no_unit ASC
            ";
            
            $result = $this->db->query($query, [$kontrakId]);
            $units = $result->getResultArray();
            
            // Format response
            $formattedUnits = [];
            foreach ($units as $unit) {
                $formattedUnits[] = [
                    'id' => $unit['id_inventory_unit'],
                    'no_unit' => $unit['no_unit'] ?: '-',
                    'merk' => $unit['merk'],
                    'model' => $unit['model'],
                    'kapasitas' => $unit['kapasitas'],
                    'jenis_unit' => $unit['jenis_unit'],
                    'departemen' => $unit['departemen'],
                    'status' => $unit['status']
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedUnits,
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

            $data = [
                'kontrak_id' => $kontrakId,
                'spek_kode' => $spekKode,
                'jumlah_dibutuhkan' => $this->request->getPost('jumlah_dibutuhkan') ?: 1,
                'jumlah_tersedia' => 0, // Set default value explicitly
                'harga_per_unit_bulanan' => $this->request->getPost('harga_per_unit_bulanan') ?: null,
                'harga_per_unit_harian' => $this->request->getPost('harga_per_unit_harian') ?: null,
                'catatan_spek' => $this->request->getPost('catatan_spek'),
                'departemen_id' => $this->request->getPost('departemen_id') ?: null,
                'tipe_unit_id' => $this->request->getPost('tipe_unit_id') ?: null,
                'tipe_jenis' => $this->request->getPost('tipe_jenis'),
                'kapasitas_id' => $this->request->getPost('kapasitas_id') ?: null,
                'merk_unit' => $this->request->getPost('merk_unit'),
                'model_unit' => $this->request->getPost('model_unit'),
                'attachment_tipe' => $this->request->getPost('attachment_tipe'),
                'attachment_merk' => $this->request->getPost('attachment_merk'),
                'jenis_baterai' => $this->request->getPost('jenis_baterai'),
                'charger_id' => $this->request->getPost('charger_id') ?: null,
                'mast_id' => $this->request->getPost('mast_id') ?: null,
                'ban_id' => $this->request->getPost('ban_id') ?: null,
                'roda_id' => $this->request->getPost('roda_id') ?: null,
                'valve_id' => $this->request->getPost('valve_id') ?: null,
                'aksesoris' => $this->request->getPost('aksesoris') ? json_encode($this->request->getPost('aksesoris')) : null
            ];
            
            // Debug specific kapasitas_id field
            log_message('info', 'Kapasitas ID from POST: ' . ($this->request->getPost('kapasitas_id') ?: 'NULL'));
            log_message('info', 'Kapasitas ID in data array: ' . ($data['kapasitas_id'] ?: 'NULL'));
            
            // Debug data yang akan diinsert
            log_message('info', 'Data to insert: ' . json_encode($data));

            // Try to insert with comprehensive error handling
            $spesifikasiId = $this->kontrakSpesifikasiModel->insert($data);

            if ($spesifikasiId) {
                log_message('info', 'Spesifikasi berhasil disimpan dengan ID: ' . $spesifikasiId);
                
                // Verify the data was actually inserted
                $inserted = $this->kontrakSpesifikasiModel->find($spesifikasiId);
                log_message('info', 'Verification - Inserted data: ' . json_encode($inserted));
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Spesifikasi berhasil ditambahkan',
                    'spesifikasi_id' => $spesifikasiId,
                    'spek_kode' => $spekKode,
                    'inserted_data' => $inserted,
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                // Get detailed error information
                $errors = $this->kontrakSpesifikasiModel->errors();
                $dbError = $this->db->error();
                
                log_message('error', 'Kontrak::addSpesifikasi - Model validation errors: ' . json_encode($errors));
                log_message('error', 'Kontrak::addSpesifikasi - Database error: ' . json_encode($dbError));
                log_message('error', 'Kontrak::addSpesifikasi - Last query: ' . $this->db->getLastQuery());
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan spesifikasi. ' . (!empty($errors) ? implode(', ', $errors) : 'Database error: ' . $dbError['message']),
                    'validation_errors' => $errors,
                    'db_error' => $dbError,
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
     * Assign units to specification
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

            $kontrak = $this->kontrakModel->find($spesifikasi->kontrak_id);
            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }

            $this->db->transStart();

            $successCount = 0;
            foreach ($unitIds as $unitId) {
                $updateData = [
                    'kontrak_id' => $spesifikasi->kontrak_id,
                    'kontrak_spesifikasi_id' => $spesifikasiId,
                    'status_unit_id' => 3, // RENTAL
                    'harga_sewa_bulanan' => $spesifikasi->harga_per_unit_bulanan,
                    'harga_sewa_harian' => $spesifikasi->harga_per_unit_harian
                ];

                if ($this->inventoryUnitModel->update($unitId, $updateData)) {
                    $successCount++;
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengassign unit ke spesifikasi'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "{$successCount} unit berhasil di-assign ke spesifikasi {$spesifikasi->spek_kode}"
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
