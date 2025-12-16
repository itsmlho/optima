<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KontrakModel;
use App\Models\InventoryUnitModel;
use App\Models\InventoryStatusModel;
use App\Traits\ActivityLoggingTrait;

class Kontrak extends BaseController
{
    use ActivityLoggingTrait;
    protected $kontrakModel;
    protected $inventoryUnitModel;
    protected $inventoryStatusModel;
    protected $db;

    public function __construct()
    {
        $this->kontrakModel = new KontrakModel();
        $this->inventoryUnitModel = new InventoryUnitModel();
        $this->inventoryStatusModel = new InventoryStatusModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan halaman utama Manajemen Kontrak.
     */
    public function index()
    {
        // Load simple_rbac helper
        helper('simple_rbac');
        
        $data = [
            'title' => 'Manajemen Kontrak Rental',
            'can_view_marketing' => can_view('marketing'),
            'can_create_marketing' => can_create('marketing'),
            'can_export_marketing' => can_export('marketing'),
            'loadDataTables' => true, // Enable DataTables loading
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
                $row['jenis_sewa']      = ucfirst($contract['jenis_sewa'] ?? 'BULANAN');
                $row['period']          = date('d M Y', strtotime($contract['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($contract['tanggal_berakhir']));
                $row['total_units']     = intval($contract['total_units'] ?? 0);
                $row['value']           = 'Rp ' . number_format($contract['nilai_total'] ?? 0, 0, ',', '.');
                
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
        
        // Validasi input menggunakan model validation dengan struktur database baru
        $data = [
            'no_kontrak'        => trim((string)$this->request->getPost('contract_number')),
            'no_po_marketing'   => $this->request->getPost('po_number'),
            'customer_location_id' => (int)$this->request->getPost('customer_location_id'),
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

            // Log validation failure using trait
            $options = [
                'business_impact' => 'LOW',
                'is_critical' => 0,
                'relations' => []
            ];
            $this->logActivity('CREATE_FAILED', 'kontrak', 0, "Gagal membuat kontrak {$data['no_po_marketing']} - Validasi error: " . implode(', ', array_keys($errors)), $options);

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
            
            // Log database failure using trait
            $options = [
                'business_impact' => 'MEDIUM',
                'is_critical' => 1,
                'relations' => []
            ];
            $this->logActivity('CREATE_FAILED', 'kontrak', 0, "Gagal menyimpan kontrak {$data['no_po_marketing']} ke database - Error: {$errorMessage}", $options);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage,
                'csrf_hash' => csrf_hash()
            ]);
        }

        $newId = $this->kontrakModel->getInsertID();

        // Get customer name for logging
        $customerInfo = '';
        if (!empty($data['customer_location_id'])) {
            $customerLocation = $this->db->query("SELECT c.customer_name, cl.location_name 
                                                 FROM customer_locations cl 
                                                 LEFT JOIN customers c ON cl.customer_id = c.id 
                                                 WHERE cl.id = ?", [$data['customer_location_id']])->getRowArray();
            if ($customerLocation) {
                $customerInfo = $customerLocation['customer_name'] . ' - ' . $customerLocation['location_name'];
            }
        }

        // Log successful creation with trait
        $this->logCreate('kontrak', $newId, $data, [
            'description' => 'Kontrak created: ' . $data['no_kontrak'] . ' (Client: ' . $customerInfo . ')',
            'submenu_item' => 'Data Kontrak',
            'workflow_stage' => 'DRAFT',
            'business_impact' => 'MEDIUM',
            'relations' => [
                'kontrak' => [$newId]
            ]
        ]);

        // Update quotation workflow if quotation_id is provided
        $quotationId = $this->request->getPost('quotation_id');
        if ($quotationId) {
            $quotationDb = \Config\Database::connect();
            $quotationDb->table('quotations')
                ->where('id_quotation', $quotationId)
                ->update(['customer_contract_complete' => 1]);
            
            log_message('info', "Updated quotation {$quotationId} contract_complete flag after contract creation");
        }

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
        
        // Validate required fields first with new database structure
        $rules = [
            'customer_location_id' => 'required|is_natural_no_zero',
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
            'customer_location_id' => (int)$this->request->getPost('customer_location_id'),
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
        
        // Get old data for logging
        $oldData = $this->kontrakModel->find($contractId);
        if (is_object($oldData)) {
            $oldData = $oldData->toArray();
        }
        
        if ($this->kontrakModel->update($contractId, $data)) {
            // Update inventory status based on contract status change
            if (isset($oldData['status']) && $oldData['status'] !== $data['status']) {
                $this->updateInventoryStatusForContract($contractId, $oldData['status'], $data['status']);
            }
            
            // Log the update activity with trait
            $relations = ['kontrak' => [$contractId]];
            
            // Add related entities if status changed significantly
            if (isset($oldData['status']) && $oldData['status'] !== $data['status']) {
                try {
                    $relatedSpk = $this->db->table('spk')->where('kontrak_id', $contractId)->select('id')->get();
                    if ($relatedSpk && !empty($relatedSpkArray = $relatedSpk->getResultArray())) {
                        $relations['spk'] = array_column($relatedSpkArray, 'id');
                    }
                } catch (\Exception $e) {
                    log_message('debug', 'Kontrak::update - SPK table error: ' . $e->getMessage());
                }
            }
            
            // Get customer info for logging
            $customerInfo = '';
            if (!empty($data['customer_location_id'])) {
                $customerLocation = $this->db->query("SELECT c.customer_name, cl.location_name 
                                                     FROM customer_locations cl 
                                                     LEFT JOIN customers c ON cl.customer_id = c.id 
                                                     WHERE cl.id = ?", [$data['customer_location_id']])->getRowArray();
                if ($customerLocation) {
                    $customerInfo = $customerLocation['customer_name'] . ' - ' . $customerLocation['location_name'];
                }
            }

            $this->logUpdate('kontrak', $contractId, $oldData, $data, [
                'description' => 'Kontrak updated: ' . $data['no_kontrak'] . ' (Client: ' . $customerInfo . ')',
                'submenu_item' => 'Data Kontrak',
                'workflow_stage' => 'UPDATED',
                'business_impact' => 'MEDIUM',
                'relations' => $relations
            ]);
            
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
        
        // Check for related kontrak_spesifikasi records - TABLE REMOVED
        // $spekCount = $db->table('kontrak_spesifikasi')->where('kontrak_id', $id)->countAllResults();
        $spekCount = 0; // Spesifikasi moved to quotations
        log_message('debug', 'Kontrak::delete - Contract has ' . $spekCount . ' spesifikasi records (migrated to quotations)');
        
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
                // Log the deletion activity using trait
                $relations = ['kontrak' => [$id]];
                
                // Get customer info for logging
                $customerInfo = 'Unknown';
                if (!empty($contract['customer_location_id'])) {
                    $customerLocation = $this->db->query("SELECT c.customer_name, cl.location_name 
                                                         FROM customer_locations cl 
                                                         LEFT JOIN customers c ON cl.customer_id = c.id 
                                                         WHERE cl.id = ?", [$contract['customer_location_id']])->getRowArray();
                    if ($customerLocation) {
                        $customerInfo = $customerLocation['customer_name'] . ' - ' . $customerLocation['location_name'];
                    }
                }

                $this->logDelete('kontrak', $id, $contract, [
                    'description' => 'Kontrak deleted: ' . $contract['no_kontrak'] . ' (Client: ' . $customerInfo . ')',
                    'submenu_item' => 'Data Kontrak',
                    'workflow_stage' => 'DELETE_CONFIRMED',
                    'business_impact' => 'HIGH',
                    'relations' => $relations
                ]);
                
                // Complete transaction and check status
                $db->transComplete();
                
                // Check if transaction was successful
                if ($db->transStatus() === false) {
                    log_message('error', 'Kontrak::delete - Transaction failed after transComplete for contract ID: ' . $id);
                    return $this->response->setJSON([
                        'success' => false, 
                        'message' => 'Gagal menghapus kontrak: Transaction rollback.'
                    ]);
                }
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
        log_message('debug', '=== Kontrak::detail START === ID: ' . $id);
        
        // Validate ID
        if (!$id || $id == '0' || $id == 0) {
            log_message('error', 'Invalid contract ID: ' . $id);
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'ID kontrak tidak valid.'
            ]);
        }

        try {
            $contract = $this->kontrakModel->findWithDynamicCalculation($id);
            log_message('debug', 'Contract data: ' . json_encode($contract));
        } catch (\Exception $e) {
            log_message('error', 'Error in findWithDynamicCalculation: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
        
        if ($contract) {
            // Add backward compatibility aliases for SPK modal
            $contract['pelanggan'] = $contract['customer_name'] ?? '';
            $contract['pic'] = $contract['contact_person'] ?? '';
            $contract['kontak'] = $contract['phone'] ?? '';
            $contract['lokasi'] = $contract['location_name'] ?? '';
            $contract['alamat'] = $contract['address'] ?? '';
            
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
                    iu.serial_number,
                    COALESCE(mu.merk_unit, 'N/A') as merk,
                    COALESCE(mu.model_unit, 'N/A') as model,
                    COALESCE(k.kapasitas_unit, 'N/A') as kapasitas,
                    COALESCE(CONCAT(tu.tipe, ' ', tu.jenis), 'N/A') as jenis_unit,
                    COALESCE(d.nama_departemen, 'N/A') as departemen,
                    COALESCE(su.status_unit, 'TERSEDIA') as status,
                    iu.status_unit_id,
                    iu.kontrak_id,
                    iu.harga_sewa_bulanan,
                    iu.harga_sewa_harian,
                    COALESCE(cl.location_name, iu.lokasi_unit, 'Lokasi Belum Ditentukan') as lokasi
                FROM inventory_unit iu
                LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                LEFT JOIN kapasitas k ON iu.kapasitas_unit_id = k.id_kapasitas
                LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
                LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
                LEFT JOIN customer_locations cl ON iu.customer_location_id = cl.id
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
                    'serial_number' => $unit['serial_number'] ?: '-',
                    'merk' => $unit['merk'],
                    'model' => $unit['model'],
                    'kapasitas' => $unit['kapasitas'],
                    'jenis_unit' => $unit['jenis_unit'],
                    'departemen' => $unit['departemen'],
                    'status' => $unit['status'],
                    'status_unit_id' => $unit['status_unit_id'],
                    'lokasi' => $unit['lokasi'],
                    'harga_per_unit_bulanan' => $hargaBulanan,
                    'harga_per_unit_harian' => $hargaHarian
                ];
            }
            
            // Get summary data from inventory_unit instead of kontrak_spesifikasi
            $summaryQuery = "
                SELECT 
                    COUNT(DISTINCT kontrak_id) as total_spesifikasi,
                    COUNT(*) as total_unit_dibutuhkan,
                    COALESCE(SUM(harga_sewa_bulanan), 0) as total_nilai_bulanan,
                    COALESCE(SUM(harga_sewa_harian), 0) as total_nilai_harian
                FROM inventory_unit
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
                    'message' => 'Contract not found'
                ]);
            }

            // Get specifications from quotation_specifications table
            $builder = $this->db->table('quotation_specifications qs');
            $builder->select('qs.id_specification, qs.specification_name, qs.specification_type, 
                qs.quantity, qs.monthly_price, qs.daily_price, qs.total_price,
                qs.brand_id, qs.departemen_id, qs.tipe_unit_id, qs.kapasitas_id,
                qs.charger_id, qs.mast_id, qs.ban_id, qs.roda_id, qs.valve_id, 
                qs.battery_id, qs.attachment_id, qs.kontrak_id,
                d.nama_departemen,
                tu.tipe as nama_tipe_unit, tu.jenis as jenis_unit,
                k.kapasitas_unit as nama_kapasitas,
                ch.tipe_charger, ch.merk_charger,
                m.tipe_mast, m.tinggi_mast,
                bn.tipe_ban,
                r.tipe_roda,
                v.jumlah_valve,
                bt.jenis_baterai, bt.merk_baterai, bt.tipe_baterai,
                att.tipe as attachment_type, att.merk as attachment_brand, att.model as attachment_model');
            $builder->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left');
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left');
            $builder->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left');
            $builder->join('charger ch', 'ch.id_charger = qs.charger_id', 'left');
            $builder->join('tipe_mast m', 'm.id_mast = qs.mast_id', 'left');
            $builder->join('tipe_ban bn', 'bn.id_ban = qs.ban_id', 'left');
            $builder->join('jenis_roda r', 'r.id_roda = qs.roda_id', 'left');
            $builder->join('valve v', 'v.id_valve = qs.valve_id', 'left');
            $builder->join('baterai bt', 'bt.id = qs.battery_id', 'left');
            $builder->join('attachment att', 'att.id_attachment = qs.attachment_id', 'left');
            $builder->where('qs.kontrak_id', $kontrakId);
            $builder->where('qs.is_active', 1);
            $spesifikasi = $builder->get()->getResultArray();

            // Calculate summary
            $summary = [
                'total_specifications' => count($spesifikasi),
                'total_quantity' => array_sum(array_column($spesifikasi, 'quantity')),
                'total_monthly_value' => array_sum(array_column($spesifikasi, 'monthly_price')),
                'total_daily_value' => array_sum(array_column($spesifikasi, 'daily_price')),
                'total_value' => array_sum(array_column($spesifikasi, 'total_price'))
            ];

            log_message('debug', 'Found ' . count($spesifikasi) . ' specifications for kontrak ' . $kontrakId);

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
     * Update inventory status based on contract status changes
     */
    private function updateInventoryStatusForContract($kontrakId, $oldStatus, $newStatus)
    {
        try {
            log_message('info', "Updating inventory status for contract {$kontrakId}: {$oldStatus} -> {$newStatus}");

            // Contract becomes active - set to RENTAL
            if ($newStatus === 'Aktif' && $oldStatus !== 'Aktif') {
                $result = $this->inventoryStatusModel->updateStatusForActiveContract($kontrakId);
                if ($result) {
                    log_message('info', "Successfully updated inventory to RENTAL status for active contract {$kontrakId}");
                } else {
                    log_message('error', "Failed to update inventory to RENTAL status for active contract {$kontrakId}");
                }
            }
            
            // Contract ends or gets cancelled - set to UNIT PULANG
            elseif (in_array($newStatus, ['Berakhir', 'Dibatalkan']) && $oldStatus === 'Aktif') {
                $result = $this->inventoryStatusModel->updateStatusForEndedContract($kontrakId);
                if ($result) {
                    log_message('info', "Successfully updated inventory to UNIT PULANG status for ended contract {$kontrakId}");
                } else {
                    log_message('error', "Failed to update inventory to UNIT PULANG status for ended contract {$kontrakId}");
                }
            }

        } catch (\Exception $e) {
            log_message('error', "Error updating inventory status for contract {$kontrakId}: " . $e->getMessage());
        }
    }

    /**
     * Trigger status update after SPK/DI workflow completion
     * Called when SPK is marked complete or DI is processed
     */
    public function triggerStatusUpdateAfterWorkflow($kontrakId)
    {
        try {
            $result = $this->inventoryStatusModel->updateStatusAfterSPKWorkflow($kontrakId);
            
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? 'Status updated successfully' : 'Status update failed'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::triggerStatusUpdateAfterWorkflow Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error triggering status update: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle attachment linking during SPK fabrication
     * This should be called during the fabrication process
     */
    public function linkFabricationAttachments($spkId)
    {
        try {
            $result = $this->inventoryStatusModel->linkAttachmentsFromSPK($spkId);
            
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? 'Attachments linked successfully' : 'Attachment linking failed'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::linkFabricationAttachments Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error linking attachments: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get current inventory status for a contract (for debugging/monitoring)
     */
    public function getInventoryStatus($kontrakId)
    {
        try {
            $status = $this->inventoryStatusModel->getInventoryStatusByContract($kontrakId);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getInventoryStatus Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting inventory status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get customers list for dropdown
     */
    public function getCustomers()
    {
        try {
            $builder = $this->db->table('customers');
            $builder->select('id, customer_code, customer_name');
            $builder->where('is_active', 1);
            $builder->orderBy('customer_name', 'ASC');
            
            $customers = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $customers,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error loading customers: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }
    
    /**
     * Get customer locations by customer ID
     */
    public function getLocationsByCustomer($customerId)
    {
        try {
            $builder = $this->db->table('customer_locations');
            $builder->select('id, location_name, address, city, contact_person, phone, is_primary');
            $builder->where('customer_id', (int)$customerId);
            $builder->where('is_active', 1);
            $builder->orderBy('is_primary', 'DESC');
            $builder->orderBy('location_name', 'ASC');
            
            $locations = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $locations,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error loading locations: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }
}
