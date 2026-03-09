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
     * Menyediakan data kontrak yang dikelompokkan per customer untuk Grouped View.
     */
    public function getGrouped()
    {
        try {
            $db = \Config\Database::connect();

            // Build optional filter conditions
            $conditions = [];
            $params     = [];

            $rentalType = $this->request->getGet('rental_type');
            $status     = $this->request->getGet('status');
            $customerId = $this->request->getGet('customer_id');

            if ($rentalType) { $conditions[] = 'k.rental_type = ?'; $params[] = $rentalType; }
            if ($status)     { $conditions[] = 'k.status = ?';      $params[] = $status; }
            if ($customerId) { $conditions[] = 'c.id = ?';           $params[] = $customerId; }

            $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

            $sql = "
                SELECT
                    COALESCE(c.id, 0) AS customer_id,
                    COALESCE(c.customer_name, 'Unknown Customer') AS customer_name,
                    k.id            AS kontrak_id,
                    k.no_kontrak,
                    k.customer_po_number,
                    k.rental_type,
                    k.status,
                    k.tanggal_mulai,
                    k.tanggal_berakhir,
                    (SELECT COUNT(*) FROM kontrak_unit ku
                     WHERE ku.kontrak_id = k.id
                     AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                     AND COALESCE(ku.is_temporary, 0) = 0) AS total_units,
                    (SELECT COALESCE(SUM(iu.harga_sewa_bulanan), 0) FROM kontrak_unit ku
                     JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
                     WHERE ku.kontrak_id = k.id
                     AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                     AND COALESCE(ku.is_temporary, 0) = 0) AS nilai_total,
                    k.jenis_sewa,
                    (SELECT GROUP_CONCAT(DISTINCT cl.location_name SEPARATOR ', ') 
                     FROM kontrak_unit ku 
                     JOIN customer_locations cl ON cl.id = ku.customer_location_id 
                     WHERE ku.kontrak_id = k.id) AS location_name
                FROM kontrak k
                LEFT JOIN customers c ON c.id = k.customer_id
                $where
                ORDER BY c.customer_name ASC, k.tanggal_mulai DESC
                LIMIT 3000
            ";

            $rows = $db->query($sql, $params)->getResultArray();

            // Group by customer
            $customers = [];
            foreach ($rows as $row) {
                $cid  = $row['customer_id'];
                $cname = $row['customer_name'];

                if (!isset($customers[$cid])) {
                    $customers[$cid] = [
                        'customer_id'      => $cid,
                        'customer_name'    => $cname,
                        'total_contracts'  => 0,
                        'total_units'      => 0,
                        'monthly_value'    => 0,
                        'contracts'        => [],
                    ];
                }

                // Days remaining
                $daysRemaining = null;
                if (!empty($row['tanggal_berakhir'])) {
                    $ts = strtotime($row['tanggal_berakhir']);
                    if ($ts && date('Y', $ts) > 1) {
                        $endDate = new \DateTime($row['tanggal_berakhir']);
                        $today   = new \DateTime('today');
                        $diff    = $today->diff($endDate);
                        $daysRemaining = $diff->invert ? -$diff->days : $diff->days;
                    }
                }

                // Safe end date
                $safeEnd = null;
                if (!empty($row['tanggal_berakhir'])) {
                    $ts = strtotime($row['tanggal_berakhir']);
                    if ($ts && date('Y', $ts) > 1) {
                        $safeEnd = $row['tanggal_berakhir'];
                    }
                }

                $customers[$cid]['contracts'][] = [
                    'id'            => $row['kontrak_id'],
                    'no_kontrak'    => $row['no_kontrak'],
                    'po_number'     => $row['customer_po_number'],
                    'rental_type'   => $row['rental_type'],
                    'status'        => $row['status'],
                    'start_date'    => $row['tanggal_mulai'],
                    'end_date'      => $safeEnd,
                    'total_units'   => intval($row['total_units']),
                    'nilai_total'   => floatval($row['nilai_total']),
                    'jenis_sewa'    => $row['jenis_sewa'],
                    'location'      => $row['location_name'],
                    'days_remaining'=> $daysRemaining,
                ];

                $customers[$cid]['total_contracts']++;
                $customers[$cid]['total_units'] += intval($row['total_units']);
                if ($row['status'] === 'ACTIVE') {
                    $customers[$cid]['monthly_value'] += floatval($row['nilai_total']);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data'    => array_values($customers),
                'total'   => count($customers),
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getGrouped error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ]);
        }
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
                $row['po']              = esc(isset($contract['customer_po_number']) ? $contract['customer_po_number'] : '-');
                
                // Calculate days until expiry (for renewal button logic)
                if (!empty($contract['tanggal_berakhir'])) {
                    $endDate = new \DateTime($contract['tanggal_berakhir']);
                    $today = new \DateTime();
                    $interval = $today->diff($endDate);
                    $row['days_until_expiry'] = $interval->invert ? -$interval->days : $interval->days;
                } else {
                    $row['days_until_expiry'] = 999; // No expiry date
                }
                
                // Rental Type Badge
                $rentalType = $contract['rental_type'] ?? 'CONTRACT';
                $typeBadgeMap = [
                    'CONTRACT' => '<span class="badge bg-primary"><i class="fas fa-file-contract me-1"></i>Contract</span>',
                    'PO_ONLY' => '<span class="badge bg-info"><i class="fas fa-file-invoice me-1"></i>PO Only</span>',
                    'DAILY_SPOT' => '<span class="badge bg-warning"><i class="fas fa-calendar-day me-1"></i>Daily/Spot</span>'
                ];
                $row['rental_type']     = $typeBadgeMap[$rentalType] ?? '<span class="badge bg-secondary">Unknown</span>';
                
                $row['client_name']     = esc($contract['pelanggan']);
                $row['jenis_sewa']      = ucfirst($contract['jenis_sewa'] ?? 'BULANAN');
                // Raw dates for JS Days Remaining calculation (view renders its own period display)
                $row['start_date']      = $contract['tanggal_mulai'] ?? null;
                $row['end_date']        = (!empty($contract['tanggal_berakhir']) && date('Y', strtotime($contract['tanggal_berakhir'])) > 0)
                                            ? $contract['tanggal_berakhir']
                                            : null;
                $row['period']          = ($row['start_date'] ? date('d M Y', strtotime($row['start_date'])) : '-') .
                                          ' - ' .
                                          ($row['end_date'] ? date('d M Y', strtotime($row['end_date'])) : 'Open-ended');
                $row['total_units']     = intval($contract['total_units'] ?? 0);
                $row['value']           = 'Rp ' . number_format($contract['nilai_total'] ?? 0, 0, ',', '.');
                
                // Status badge with proper color
                $statusClass = 'bg-secondary';
                switch($contract['status']) {
                    case 'ACTIVE':
                        $statusClass = 'bg-success';
                        break;
                    case 'PENDING':
                        $statusClass = 'bg-warning';
                        break;
                    case 'EXPIRED':
                        $statusClass = 'bg-danger';
                        break;
                    case 'CANCELLED':
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
        
        // Get customer_location_id from form to lookup customer_id
        $customerLocationId = (int)$this->request->getPost('customer_location_id') ?: (int)$this->request->getPost('location_id');
        $customerId = null;
        
        // Query customer_id from customer_location
        if ($customerLocationId > 0) {
            $location = $this->db->table('customer_locations')
                ->select('customer_id')
                ->where('id', $customerLocationId)
                ->get()
                ->getRowArray();
            $customerId = $location['customer_id'] ?? null;
        }
        
        // Validasi input menggunakan model validation dengan struktur database baru
        // Note: customer_location_id REMOVED from kontrak table (March 5, 2026)
        $data = [
            'no_kontrak'           => trim((string)$this->request->getPost('contract_number')),
            'customer_po_number'   => $this->request->getPost('po_number'),
            'rental_type'          => $this->request->getPost('rental_type') ?: 'CONTRACT',
            'customer_id'          => $customerId,  // Use customer_id instead of customer_location_id
            'nilai_total'          => 0, // Akan dihitung otomatis dari spesifikasi
            'total_units'          => 0, // Akan dihitung otomatis dari spesifikasi
            'jenis_sewa'           => strtoupper($this->request->getPost('jenis_sewa') ?: 'BULANAN'),
            'tanggal_mulai'        => $this->request->getPost('start_date'),
            'tanggal_berakhir'     => $this->request->getPost('end_date'),
            'status'               => 'PENDING', // Set otomatis ke PENDING untuk kontrak baru
            'dibuat_oleh'          => session()->get('user_id') ?? 1, // Default user ID jika session kosong
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
            $this->logActivity('CREATE_FAILED', 'kontrak', 0, "Gagal membuat kontrak {$data['no_kontrak']} (PO: {$data['customer_po_number']}) - Validasi error: " . implode(', ', array_keys($errors)), $options);

            return $this->response->setJSON([
                'success' => false,
                'message' => lang('App.error_invalid_data'),
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
            $this->logActivity('CREATE_FAILED', 'kontrak', 0, "Gagal menyimpan kontrak {$data['no_kontrak']} (PO: {$data['customer_po_number']}) ke database - Error: {$errorMessage}", $options);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage,
                'csrf_hash' => csrf_hash()
            ]);
        }

        $newId = $this->kontrakModel->getInsertID();

        // Get customer name for logging
        $customerInfo = '';
        if (!empty($data['customer_id'])) {
            // Get customer info and first location from kontrak_unit if available
            $customerLocation = $this->db->query("SELECT c.customer_name, 
                                                         (SELECT cl.location_name 
                                                          FROM kontrak_unit ku 
                                                          JOIN customer_locations cl ON cl.id = ku.customer_location_id 
                                                          WHERE ku.kontrak_id = ? 
                                                          LIMIT 1) as location_name
                                                  FROM customers c 
                                                  WHERE c.id = ?", [$newId, $data['customer_id']])->getRowArray();
            if ($customerLocation) {
                $customerInfo = $customerLocation['customer_name'];
                if (!empty($customerLocation['location_name'])) {
                    $customerInfo .= ' - ' . $customerLocation['location_name'];
                }
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

        // Get contract details for notification
        $contract = $this->kontrakModel->find($newId);
        
        // Send notification - contract created
        if (function_exists('notify_contract_created') && $contract) {
            notify_contract_created([
                'id' => $newId,
                'contract_number' => $contract['no_kontrak'] ?? '',
                'customer_name' => $customerInfo,
                'contract_type' => $contract['jenis_sewa'] ?? '',
                'start_date' => $contract['tanggal_mulai'] ?? '',
                'end_date' => $contract['tanggal_berakhir'] ?? '',
                'total_value' => $contract['nilai_total'] ?? 0,
                'created_by' => session('username') ?? session('user_id'),
                'url' => base_url('/marketing/contracts/view/' . $newId)
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => lang('Marketing.contract_created'),
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
        
        // Get customer_location_id from form to lookup customer_id  
        $customerLocationId = (int)$this->request->getPost('customer_location_id');
        $customerId = null;
        
        // Query customer_id from customer_location
        if ($customerLocationId > 0) {
            $location = $this->db->table('customer_locations')
                ->select('customer_id')
                ->where('id', $customerLocationId)
                ->get()
                ->getRowArray();
            $customerId = $location['customer_id'] ?? null;
        }
        
        // Validate required fields first with new database structure
        // Note: customer_location_id validation REMOVED (March 5, 2026 - moved to kontrak_unit)
        $rules = [
            'start_date'      => 'required|valid_date',
            'end_date'        => 'required|valid_date',
            'status'          => 'required|in_list[ACTIVE,EXPIRED,PENDING,CANCELLED]'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', "Validation failed: " . json_encode($this->validator->getErrors()));
            return $this->response->setJSON([
                'success' => false, 
                'message' => lang('App.error_invalid_data'),
                'errors'  => $this->validator->getErrors(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        // Check for duplicate contract number (exclude current record)
        $contractNumber = trim($this->request->getPost('contract_number'));
        if (empty($contractNumber)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => lang('Marketing.contract_number') . ' ' . lang('App.required'),
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
                'message' => lang('Marketing.contract_number') . " '$contractNumber' " . lang('Marketing.already_used') . " (ID: " . (is_array($existing) ? $existing['id'] : $existing->id) . "). Current edit ID: $contractId",
                'debug_info' => [
                    'current_id' => $contractId,
                    'existing_id' => is_array($existing) ? $existing['id'] : $existing->id,
                    'contract_number' => $contractNumber
                ],
                'csrf_hash' => csrf_hash(),
            ]);
        }

        // Note: customer_location_id REMOVED from kontrak table (March 5, 2026)
        // Location tracking is now in kontrak_unit table for multi-location support
        $data = [
            'no_kontrak'           => $contractNumber,
            'customer_po_number'   => $this->request->getPost('po_number'),
            'rental_type'          => $this->request->getPost('rental_type') ?: 'CONTRACT',
            'customer_id'          => $customerId,  // Use customer_id instead of customer_location_id
            // nilai_total dihitung dari kontrak_unit (jika field ada di form, gunakan; sinon biarkan)
            // total_units TIDAK disimpan dari form — dihitung live dari kontrak_unit
            'tanggal_mulai'        => $this->request->getPost('start_date'),
            'tanggal_berakhir'     => $this->request->getPost('end_date'),
            'status'               => $this->request->getPost('status'),
            'jenis_sewa'           => $this->request->getPost('jenis_sewa') ?: 'BULANAN',
            'catatan'              => $this->request->getPost('catatan'),
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
            if (!empty($data['customer_id'])) {
                // Get customer info and first location from kontrak_unit if available
                $customerLocation = $this->db->query("SELECT c.customer_name, 
                                                             (SELECT cl.location_name 
                                                              FROM kontrak_unit ku 
                                                              JOIN customer_locations cl ON cl.id = ku.customer_location_id 
                                                              WHERE ku.kontrak_id = ? 
                                                              LIMIT 1) as location_name
                                                      FROM customers c 
                                                      WHERE c.id = ?", [$contractId, $data['customer_id']])->getRowArray();
                if ($customerLocation) {
                    $customerInfo = $customerLocation['customer_name'];
                    if (!empty($customerLocation['location_name'])) {
                        $customerInfo .= ' - ' . $customerLocation['location_name'];
                    }
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
            
            // Send notification - contract updated
            if (function_exists('notify_contract_updated')) {
                $changes = [];
                if ($oldData['status'] !== $data['status']) {
                    $changes[] = "Status: {$oldData['status']} → {$data['status']}";
                }
                if ($oldData['nilai_total'] != $data['nilai_total']) {
                    $changes[] = "Nilai: {$oldData['nilai_total']} → {$data['nilai_total']}";
                }
                
                notify_contract_updated([
                    'id' => $contractId,
                    'contract_number' => $data['no_kontrak'],
                    'customer_name' => $customerInfo,
                    'changes' => !empty($changes) ? implode(', ', $changes) : 'Contract details updated',
                    'updated_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/marketing/contracts/view/' . $contractId)
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => lang('Marketing.contract_updated'),
                'csrf_hash' => csrf_hash(),
            ]);
        } else {
            log_message('debug', "Kontrak update failed: " . json_encode($this->kontrakModel->errors()));
            return $this->response->setJSON([
                'success' => false, 
                'message' => lang('App.error_update') . ': ' . implode(', ', $this->kontrakModel->errors()),
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
                'message' => lang('Marketing.contract') . ' ID ' . lang('App.error_invalid_data') . '.'
            ]);
        }

        // Check if contract exists
        $contract = $this->kontrakModel->find($id);
        if (!$contract) {
            log_message('error', 'Kontrak::delete - Contract not found with ID: ' . $id);
            return $this->response->setJSON([
                'success' => false, 
                'message' => lang('Marketing.contract') . ' ' . lang('App.error_not_found') . '.'
            ]);
        }

        // Check if contract has related records that might cause issues
        $db = \Config\Database::connect();
        
        // Check for related kontrak_spesifikasi records - TABLE REMOVED
        // $spekCount = $db->table('kontrak_spesifikasi')->where('kontrak_id', $id)->countAllResults();
        $spekCount = 0; // Spesifikasi moved to quotations
        log_message('debug', 'Kontrak::delete - Contract has ' . $spekCount . ' spesifikasi records (migrated to quotations)');
        
        // Check for related kontrak_unit records (units assigned to this contract)
        $unitCount = $db->table('kontrak_unit')->where('kontrak_id', $id)->where('status', 'ACTIVE')->countAllResults();
        log_message('debug', 'Kontrak::delete - Contract has ' . $unitCount . ' active kontrak_unit records');
        
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
                if (!empty($contract['customer_id'])) {
                    // Get customer info and first location from kontrak_unit if available
                    $customerLocation = $this->db->query("SELECT c.customer_name, 
                                                                 (SELECT cl.location_name 
                                                                  FROM kontrak_unit ku 
                                                                  JOIN customer_locations cl ON cl.id = ku.customer_location_id 
                                                                  WHERE ku.kontrak_id = ? 
                                                                  LIMIT 1) as location_name
                                                          FROM customers c 
                                                          WHERE c.id = ?", [$id, $contract['customer_id']])->getRowArray();
                    if ($customerLocation) {
                        $customerInfo = $customerLocation['customer_name'];
                        if (!empty($customerLocation['location_name'])) {
                            $customerInfo .= ' - ' . $customerLocation['location_name'];
                        }
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
                
                // Send notification - contract deleted
                if (function_exists('notify_contract_deleted')) {
                    notify_contract_deleted([
                        'id' => $id,
                        'contract_number' => $contract['no_kontrak'] ?? '',
                        'customer_name' => $customerInfo,
                        'deleted_by' => session('username') ?? session('user_id'),
                        'deletion_reason' => 'Contract deletion requested',
                        'url' => base_url('/marketing/contracts')
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => lang('Marketing.contract_deleted')
                ]);
            } else {
                $db->transRollback();
                log_message('error', 'Kontrak::delete - Delete returned false for contract ID: ' . $id);
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => lang('App.error_delete')
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
     * Contract detail page
     */
    public function detail($id)
    {
        if (!$id || $id == '0' || $id == 0) {
            return redirect()->to('marketing/kontrak')->with('error', 'ID kontrak tidak valid.');
        }

        $contract = $this->db->table('kontrak k')
            ->select('k.*, c.customer_name')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->where('k.id', (int)$id)
            ->get()->getRowArray();

        if (!$contract) {
            return redirect()->to('marketing/kontrak')->with('error', 'Kontrak tidak ditemukan.');
        }

        return view('marketing/kontrak_detail', [
            'title'    => 'Detail Kontrak — ' . ($contract['no_kontrak'] ?? '#' . $id),
            'contract' => $contract,
        ]);
    }

    /**
     * Edit contract - return view with data
     */
    public function edit($id)
    {
        // Validate ID
        if (!$id || $id == '0' || $id == 0) {
            return redirect()->to('marketing/kontrak')->with('error', 'ID kontrak tidak valid.');
        }

        // Fetch contract with customer join so the edit view has customer_id
        $contract = $this->db->table('kontrak k')
            ->select('k.*, c.customer_name')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->where('k.id', (int)$id)
            ->get()->getRowArray();

        if (!$contract) {
            return redirect()->to('marketing/kontrak')->with('error', 'Kontrak tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Kontrak — ' . ($contract['no_kontrak'] ?? '#' . $id),
            'contract' => $contract,
        ];

        return view('marketing/kontrak_edit', $data);
    }

    /**
     * Get contract details for SPK creation / detail page AJAX
     */
    public function get($kontrakId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $kontrak = $this->db->table('kontrak k')
                ->select('k.*,
                    c.customer_name, c.customer_code, c.phone,
                    (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.kontrak_id = k.id AND ku.status = \'ACTIVE\') AS total_units,
                    k.nilai_total AS total_value,
                    k.operator_quantity,
                    k.operator_monthly_rate')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->where('k.id', (int)$kontrakId)
                ->get()->getRowArray();

            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data'    => $kontrak,
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
            // Use kontrak_unit junction table instead of inventory_unit.kontrak_id
            // Gunakan ku.customer_location_id (lokasi per unit)
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
                    ku.kontrak_id,
                    ku.customer_location_id as unit_location_id,
                    ku.harga_sewa as ku_harga_sewa,
                    ku.is_spare,
                    iu.harga_sewa_bulanan,
                    iu.harga_sewa_harian,
                    COALESCE(ku.harga_sewa, iu.harga_sewa_bulanan) as harga_efektif,
                    COALESCE(cl_unit.location_name, iu.lokasi_unit, 'Lokasi Belum Ditentukan') as lokasi
                FROM kontrak_unit ku
                JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
                LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                LEFT JOIN kapasitas k ON iu.kapasitas_unit_id = k.id_kapasitas
                LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
                LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
                LEFT JOIN customer_locations cl_unit ON cl_unit.id = ku.customer_location_id
                WHERE ku.kontrak_id = ? AND ku.status IN ('AKTIF', 'ACTIVE')
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
                $hargaEfektif = (float)($unit['harga_efektif'] ?? $hargaBulanan);
                $isSpare = (int)($unit['is_spare'] ?? 0);
                
                // Only count non-spare for totals
                if (!$isSpare) {
                    $totalHargaBulanan += $hargaEfektif;
                }
                $totalHargaHarian += $hargaHarian;
                
                $formattedUnits[] = [
                    'id' => $unit['id_inventory_unit'],
                    'id_inventory_unit' => $unit['id_inventory_unit'],
                    // Original fields
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
                    'harga_sewa_bulanan' => $hargaBulanan,
                    'ku_harga_sewa' => $unit['ku_harga_sewa'] ?? null,
                    'is_spare' => $isSpare,
                    'harga_efektif' => $hargaEfektif,
                    'harga_per_unit_bulanan' => $hargaEfektif,
                    'harga_per_unit_harian' => $hargaHarian,
                    // Frontend expected fields (for contract detail modal)
                    'location_name' => $unit['lokasi'],
                    'unit_no' => $unit['no_unit'] ?: 'N/A',
                    'unit_type' => $unit['jenis_unit'],
                    'brand_model' => trim($unit['merk'] . ' ' . $unit['model']),
                    'capacity' => $unit['kapasitas'],
                    'rate_monthly' => $hargaEfektif
                ];
            }
            
            // Get summary data via kontrak_unit junction table
            $summaryQuery = "
                SELECT 
                    COUNT(DISTINCT ku.kontrak_id) as total_spesifikasi,
                    COUNT(*) as total_unit_dibutuhkan,
                    COALESCE(SUM(CASE WHEN (ku.is_spare IS NULL OR ku.is_spare = 0) THEN COALESCE(ku.harga_sewa, iu.harga_sewa_bulanan) ELSE 0 END), 0) as total_nilai_bulanan,
                    COALESCE(SUM(iu.harga_sewa_harian), 0) as total_nilai_harian
                FROM kontrak_unit ku
                JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
                WHERE ku.kontrak_id = ?
                AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
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
     * Add unit(s) to a contract
     * POST /marketing/kontrak/addUnit
     * Accepts individual units with per-unit harga_sewa and is_spare flag
     */
    public function addUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            // Support both JSON body and form POST
            $jsonInput = $this->request->getJSON(true);
            if (!empty($jsonInput)) {
                $kontrakId = $jsonInput['kontrak_id'] ?? null;
                $units = $jsonInput['units'] ?? null;
                $customerLocationId = $jsonInput['customer_location_id'] ?? null;
                $tanggalMulai = $jsonInput['tanggal_mulai'] ?? date('Y-m-d');
                $tanggalSelesai = $jsonInput['tanggal_selesai'] ?? null;
            } else {
                $kontrakId = $this->request->getPost('kontrak_id');
                $units = $this->request->getPost('units');
                $customerLocationId = $this->request->getPost('customer_location_id');
                $tanggalMulai = $this->request->getPost('tanggal_mulai') ?? date('Y-m-d');
                $tanggalSelesai = $this->request->getPost('tanggal_selesai');
            }

            // Validate
            if (!$kontrakId || empty($units)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak ID dan unit harus diisi',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Check contract exists
            $kontrak = $this->kontrakModel->find($kontrakId);
            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Get contract dates for default
            $kontrakMulai = $kontrak['tanggal_mulai'] ?? $tanggalMulai;
            $kontrakBerakhir = $kontrak['tanggal_berakhir'] ?? $tanggalSelesai;

            // Add units to kontrak_unit junction
            $added = 0;
            $errors = [];

            // Normalize: accept both old format (unit_ids array) and new format (units array with details)
            if (!is_array($units)) {
                $units = [];
            }
            
            // Check if it's the old format (flat array of unit IDs)
            $isOldFormat = isset($units[0]) && !is_array($units[0]);
            if ($isOldFormat) {
                // Convert old format to new format
                $units = array_map(function($unitId) {
                    return ['unit_id' => $unitId, 'harga_sewa' => null, 'is_spare' => 0];
                }, $units);
            }

            foreach ($units as $unitData) {
                $unitId = is_array($unitData) ? ($unitData['unit_id'] ?? null) : $unitData;
                $hargaSewa = is_array($unitData) ? ($unitData['harga_sewa'] ?? null) : null;
                $isSpare = is_array($unitData) ? (int)($unitData['is_spare'] ?? 0) : 0;
                
                if (!$unitId) continue;

                // Check if unit already exists in this contract
                $existing = $this->db->table('kontrak_unit')
                    ->where('kontrak_id', $kontrakId)
                    ->where('unit_id', $unitId)
                    ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
                    ->get()->getRow();

                if ($existing) {
                    $errors[] = "Unit ID $unitId sudah ada di kontrak ini";
                    continue;
                }

                // If spare, force harga to 0
                if ($isSpare) {
                    $hargaSewa = 0;
                }

                // Insert into kontrak_unit
                $insertData = [
                    'kontrak_id' => $kontrakId,
                    'unit_id' => $unitId,
                    'customer_location_id' => $customerLocationId ?: null,
                    'harga_sewa' => $hargaSewa,
                    'is_spare' => $isSpare,
                    'tanggal_mulai' => $tanggalMulai ?: $kontrakMulai,
                    'tanggal_selesai' => $tanggalSelesai ?: $kontrakBerakhir,
                    'status' => 'ACTIVE',
                    'created_by' => session()->get('user_id') ?? null
                ];
                
                $this->db->table('kontrak_unit')->insert($insertData);
                $added++;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "$added unit berhasil ditambahkan ke kontrak" . (count($errors) > 0 ? '. ' . implode(', ', $errors) : ''),
                'added_count' => $added,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::addUnit Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Remove unit from a contract
     * POST /marketing/kontrak/removeUnit
     */
    public function removeUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $kontrakId = $this->request->getPost('kontrak_id');
            $unitId = $this->request->getPost('unit_id');

            if (!$kontrakId || !$unitId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak ID dan Unit ID harus diisi',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Check exists
            $exists = $this->db->table('kontrak_unit')
                ->where('kontrak_id', $kontrakId)
                ->where('unit_id', $unitId)
                ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->get()->getRow();

            if (!$exists) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan di kontrak ini',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Delete or mark as INACTIVE
            $this->db->table('kontrak_unit')
                ->where('kontrak_id', $kontrakId)
                ->where('unit_id', $unitId)
                ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->update(['status' => 'INACTIVE']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Unit berhasil dihapus dari kontrak',
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::removeUnit Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Update unit in a contract (harga_sewa, is_spare)
     * POST /marketing/kontrak/updateUnit
     */
    public function updateUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $kontrakId = $this->request->getPost('kontrak_id');
            $unitId = $this->request->getPost('unit_id');
            $hargaSewa = $this->request->getPost('harga_sewa');
            $isSpare = (int)$this->request->getPost('is_spare');

            if (!$kontrakId || !$unitId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak ID dan Unit ID harus diisi',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Check exists
            $exists = $this->db->table('kontrak_unit')
                ->where('kontrak_id', $kontrakId)
                ->where('unit_id', $unitId)
                ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->get()->getRow();

            if (!$exists) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan di kontrak ini',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // If spare, force harga to 0
            if ($isSpare) {
                $hargaSewa = 0;
            }

            // Update kontrak_unit
            $this->db->table('kontrak_unit')
                ->where('kontrak_id', $kontrakId)
                ->where('unit_id', $unitId)
                ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->update([
                    'harga_sewa' => ($hargaSewa !== null && $hargaSewa !== '') ? (float)$hargaSewa : null,
                    'is_spare' => $isSpare,
                    'updated_by' => session()->get('user_id') ?? null,
                ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Unit berhasil diupdate',
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::updateUnit Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Get available units for adding to contract
     * GET /marketing/kontrak/getAvailableUnits
     */
    public function getAvailableUnits()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $kontrakId = $this->request->getGet('kontrak_id');
            $search = $this->request->getGet('search') ?? '';

            // First get customer_id from contract to get locations
            $kontrak = null;
            $customerLocations = [];

            if ($kontrakId) {
                $kontrak = $this->db->table('kontrak k')
                    ->select('k.*')
                    ->where('k.id', $kontrakId)
                    ->get()->getRowArray();

                $custId = $kontrak['customer_id'] ?? null;
                if ($kontrak && $custId) {
                    $customerLocations = $this->db->table('customer_locations')
                        ->where('customer_id', $custId)
                        ->where('is_active', 1)
                        ->get()->getResultArray();
                }
            }

            // Show ALL units (not just available) — let user choose any unit
            // Include info about current contract assignment if unit is already contracted
            $query = "
                SELECT
                    iu.id_inventory_unit,
                    iu.no_unit,
                    iu.no_unit_na,
                    iu.serial_number,
                    iu.status_unit_id,
                    COALESCE(mu.merk_unit, 'N/A') as merk,
                    COALESCE(mu.model_unit, 'N/A') as model,
                    COALESCE(tu.tipe, 'N/A') as tipe,
                    COALESCE(kap.kapasitas_unit, 'N/A') as kapasitas,
                    iu.harga_sewa_bulanan,
                    iu.harga_sewa_harian,
                    su.status_unit,
                    COALESCE(d.nama_departemen, 'N/A') as departemen,
                    -- Current contract info (if any active assignment)
                    active_ku.kontrak_id as current_kontrak_id,
                    active_k.no_kontrak as current_kontrak_no,
                    active_c.customer_name as current_customer,
                    active_cl.location_name as current_location
                FROM inventory_unit iu
                LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                LEFT JOIN kapasitas kap ON iu.kapasitas_unit_id = kap.id_kapasitas
                LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
                LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
                -- Subquery: get latest active kontrak_unit assignment
                LEFT JOIN (
                    SELECT ku2.unit_id, ku2.kontrak_id, ku2.customer_location_id
                    FROM kontrak_unit ku2
                    WHERE ku2.status IN ('AKTIF','ACTIVE') AND (ku2.is_temporary IS NULL OR ku2.is_temporary = 0)
                ) active_ku ON active_ku.unit_id = iu.id_inventory_unit
                LEFT JOIN kontrak active_k ON active_k.id = active_ku.kontrak_id
                LEFT JOIN customers active_c ON active_c.id = active_k.customer_id
                LEFT JOIN customer_locations active_cl ON active_cl.id = active_ku.customer_location_id
                WHERE iu.status_unit_id NOT IN (13) -- Exclude SOLD/DISPOSED only
            ";

            $params = [];

            if (!empty($search)) {
                $query .= " AND (
                    CAST(iu.no_unit AS CHAR) LIKE ?
                    OR iu.no_unit_na LIKE ?
                    OR iu.serial_number LIKE ?
                    OR mu.merk_unit LIKE ?
                    OR mu.model_unit LIKE ?
                )";
                $searchTerm = "%$search%";
                $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
            }

            $query .= " ORDER BY iu.no_unit ASC LIMIT 500";

            $units = $this->db->query($query, $params)->getResultArray();

            // Format units
            $formattedUnits = array_map(function($u) use ($kontrakId) {
                $isContracted = !empty($u['current_kontrak_id']);
                $isSameContract = $isContracted && $u['current_kontrak_id'] == $kontrakId;
                
                return [
                    'id' => (int)$u['id_inventory_unit'],
                    'no_unit' => $u['no_unit'] ?: $u['no_unit_na'] ?: '-',
                    'serial_number' => $u['serial_number'] ?: '-',
                    'merk' => $u['merk'],
                    'model' => $u['model'],
                    'tipe' => $u['tipe'],
                    'kapasitas' => $u['kapasitas'],
                    'departemen' => $u['departemen'],
                    'harga_sewa_bulanan' => (float)($u['harga_sewa_bulanan'] ?? 0),
                    'harga_sewa_harian' => (float)($u['harga_sewa_harian'] ?? 0),
                    'status_unit' => $u['status_unit'],
                    'status_unit_id' => (int)$u['status_unit_id'],
                    // Contract assignment info
                    'is_contracted' => $isContracted,
                    'is_same_contract' => $isSameContract,
                    'current_kontrak_id' => $u['current_kontrak_id'] ? (int)$u['current_kontrak_id'] : null,
                    'current_kontrak_no' => $u['current_kontrak_no'],
                    'current_customer' => $u['current_customer'],
                    'current_location' => $u['current_location'],
                    // Display label for Select2
                    'label' => ($u['no_unit'] ?: $u['no_unit_na'] ?: '-') . ' — ' . $u['merk'] . ' ' . $u['model'] . ($isContracted ? ' [CONTRACTED]' : ''),
                ];
            }, $units);

            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedUnits,
                'customer_locations' => $customerLocations,
                'kontrak' => $kontrak,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getAvailableUnits Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
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
            if ($newStatus === 'ACTIVE' && $oldStatus !== 'ACTIVE') {
                $result = $this->inventoryStatusModel->updateStatusForActiveContract($kontrakId);
                if ($result) {
                    log_message('info', "Successfully updated inventory to RENTAL status for active contract {$kontrakId}");
                } else {
                    log_message('error', "Failed to update inventory to RENTAL status for active contract {$kontrakId}");
                }
            }
            
            // Contract ends or gets cancelled - set to UNIT PULANG
            elseif (in_array($newStatus, ['EXPIRED', 'CANCELLED']) && $oldStatus === 'ACTIVE') {
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
     * Get contract statistics for dashboard cards
     */
    public function getStats()
    {
        try {
            $query = "
                SELECT 
                    COUNT(*) as total_contracts,
                    COUNT(CASE WHEN rental_type = 'CONTRACT' THEN 1 END) as total_formal_contracts,
                    COUNT(CASE WHEN rental_type = 'PO_ONLY' THEN 1 END) as total_po_only,
                    COUNT(CASE WHEN rental_type = 'DAILY_SPOT' THEN 1 END) as total_daily_spot,
                    COUNT(CASE WHEN status = 'ACTIVE' THEN 1 END) as total_active,
                    COUNT(CASE WHEN status = 'EXPIRED' THEN 1 END) as total_expired,
                    COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as total_pending,
                    SUM(total_units) as total_units_rented,
                    SUM(nilai_total) as total_contract_value
                FROM kontrak
            ";

            $stats = $this->db->query($query)->getRowArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getStats - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load statistics'
            ]);
        }
    }

    /**
     * Export contracts to CSV
     */
    public function export()
    {
        try {
            // Get filter parameters
            $rentalType = $this->request->getGet('rental_type') ?? '';
            $status = $this->request->getGet('status') ?? '';
            $customerId = $this->request->getGet('customer_id') ?? '';

            // Build query with filters
            $builder = $this->db->table('kontrak k');
            $builder->select('k.*, c.customer_name, c.customer_code, 
                             u.staff_name as created_by_name')
                   ->join('customers c', 'c.id = k.customer_id', 'left')
                   ->join('users u', 'k.dibuat_oleh = u.id', 'left');

            if (!empty($rentalType)) $builder->where('k.rental_type', $rentalType);
            if (!empty($status)) $builder->where('k.status', $status);
            if (!empty($customerId)) $builder->where('c.id', $customerId);

            $builder->orderBy('k.dibuat_pada', 'DESC');
            $contracts = $builder->get()->getResultArray();

            // Generate CSV
            $filename = 'contracts_export_' . date('Ymd_His') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($output, [
                'Contract No', 'PO Number', 'Rental Type', 'Customer', 'Location', 
                'Start Date', 'End Date', 'Total Units', 'Total Value', 'Status', 'Created By'
            ]);

            // CSV Data
            foreach ($contracts as $contract) {
                fputcsv($output, [
                    $contract['no_kontrak'],
                    $contract['customer_po_number'] ?? '-',
                    $contract['rental_type'],
                    $contract['customer_name'],
                    $contract['location_name'],
                    $contract['tanggal_mulai'],
                    $contract['tanggal_berakhir'],
                    $contract['total_units'],
                    $contract['nilai_total'],
                    $contract['status'],
                    $contract['created_by_name']
                ]);
            }

            fclose($output);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::export - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export: ' . $e->getMessage());
        }
    }

    /**
     * Get customers dropdown for filters
     */
    public function getCustomersDropdown()
    {
        try {
            $customers = $this->db->table('customers')
                ->select('id, customer_name, customer_code')
                ->where('is_active', 1)
                ->orderBy('customer_name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $customers
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getCustomersDropdown - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load customers'
            ]);
        }
    }

    /**
     * Get customers list for dropdown (legacy compatibility)
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
    
    /**
     * Get expiring contracts (for renewal wizard)
     * Returns contracts expiring within 90 days
     */
    public function getExpiringContracts()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            $builder = $this->db->table('kontrak k');
            $builder->select('k.*, c.customer_name, c.customer_code');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            $builder->where('k.status', 'ACTIVE');
            $builder->where('k.end_date <=', date('Y-m-d', strtotime('+90 days')));
            $builder->where('k.end_date >=', date('Y-m-d'));
            $builder->orderBy('k.end_date', 'ASC');
            
            $contracts = $builder->get()->getResultArray();
            
            // Add unit counts and values
            foreach ($contracts as &$contract) {
                $unitsBuilder = $this->db->table('contract_units');
                $unitsBuilder->where('contract_id', $contract['id']);
                $contract['total_units'] = $unitsBuilder->countAllResults();
                
                $unitsBuilder = $this->db->table('contract_units');
                $unitsBuilder->selectSum('monthly_rate', 'total_value');
                $unitsBuilder->where('contract_id', $contract['id']);
                $result = $unitsBuilder->get()->getRowArray();
                $contract['contract_value'] = $result['total_value'] ?? 0;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getExpiringContracts error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load expiring contracts: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Create renewal contract
     * Implements gap-free transition from old contract to new
     */
    public function createRenewal()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        $this->db->transStart();
        
        try {
            $parentContractId = $this->request->getPost('parent_contract_id');
            $contractNumber = $this->request->getPost('contract_number');
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
            $billingMethod = $this->request->getPost('billing_method');
            $rentalType = $this->request->getPost('rental_type');
            $poNumber = $this->request->getPost('po_number');
            $notes = $this->request->getPost('notes');
            $unitsJson = $this->request->getPost('units');
            $customerId = $this->request->getPost('customer_id');
            $locationId = $this->request->getPost('location_id');
            
            // Parse units data
            $units = json_decode($unitsJson, true);
            
            if (!$units || count($units) === 0) {
                throw new \Exception('No units selected for renewal');
            }
            
            // Get parent contract information
            $parentContract = $this->kontrakModel->find($parentContractId);
            if (!$parentContract) {
                throw new \Exception('Parent contract not found');
            }
            
            // Calculate renewal generation
            $renewalGeneration = ($parentContract['renewal_generation'] ?? 0) + 1;
            
            // Calculate total contract value
            $totalValue = array_sum(array_column($units, 'monthly_rate'));
            
            // Create new renewal contract
            // Note: customer_location_id REMOVED from kontrak table (March 5, 2026)
            // Using customer_id instead; location tracking is in kontrak_unit table
            $renewalData = [
                'no_kontrak' => $contractNumber,
                'customer_id' => $customerId,  // Use customer_id instead of customer_location_id
                'start_date' => $startDate,
                'end_date' => $endDate,
                'nilai_kontrak' => $totalValue,
                'jenis_sewa' => 'BULANAN',
                'status' => 'DRAFT_RENEWAL',
                'rental_type' => $rentalType,
                'po_number' => $poNumber,
                'catatan' => $notes,
                'billing_method' => $billingMethod,
                'parent_contract_id' => $parentContractId,
                'is_renewal' => 1,
                'renewal_generation' => $renewalGeneration,
                'renewal_initiated_at' => date('Y-m-d H:i:s'),
                'renewal_initiated_by' => session()->get('user_id') ?? 1,
                'created_by' => session()->get('user_id') ?? 1
            ];
            
            if (!$this->kontrakModel->insert($renewalData)) {
                throw new \Exception('Failed to create renewal contract: ' . json_encode($this->kontrakModel->errors()));
            }
            
            $renewalContractId = $this->kontrakModel->getInsertID();
            
            // Add units to new contract
            $contractUnitsBuilder = $this->db->table('contract_units');
            foreach ($units as $unit) {
                $contractUnitsBuilder->insert([
                    'contract_id' => $renewalContractId,
                    'unit_id' => $unit['unit_id'],
                    'monthly_rate' => $unit['monthly_rate'],
                    'on_hire_date' => $startDate,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Create renewal workflow record
            $workflowBuilder = $this->db->table('contract_renewal_workflow');
            $workflowBuilder->insert([
                'parent_contract_id' => $parentContractId,
                'renewal_contract_id' => $renewalContractId,
                'status' => 'INITIATED',
                'initiated_by' => session()->get('user_id') ?? 1,
                'initiated_at' => date('Y-m-d H:i:s'),
                'notes' => 'Renewal initiated via wizard'
            ]);
            
            // Create unit mapping records
            $unitMapBuilder = $this->db->table('contract_renewal_unit_map');
            foreach ($units as $unit) {
                $unitMapBuilder->insert([
                    'parent_contract_id' => $parentContractId,
                    'renewal_contract_id' => $renewalContractId,
                    'parent_unit_id' => $unit['unit_id'],
                    'renewal_unit_id' => $unit['unit_id'], // Same unit by default
                    'action' => 'CARRY_OVER',
                    'old_rate' => $unit['monthly_rate'], // Will be updated if rate changed
                    'new_rate' => $unit['monthly_rate']
                ]);
            }
            
            // Log activity
            $this->logActivity(
                'contract_renewal',
                $renewalContractId,
                'create',
                "Renewal contract {$contractNumber} created from {$parentContract['no_kontrak']}",
                [
                    'parent_contract' => $parentContract['no_kontrak'],
                    'renewal_generation' => $renewalGeneration,
                    'unit_count' => count($units)
                ]
            );
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Renewal contract created successfully',
                'contract_id' => $renewalContractId,
                'contract_number' => $contractNumber
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'createRenewal error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create renewal: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get active contracts for amendment prorate calculator
     * Returns contracts with ACTIVE status
     */
    /**
     * Get DEAL quotations for SPK creation (with or without contract)
     * This is the correct approach - SPK can be created from quotation directly
     * Contract is optional and can be linked later
     */
    public function getActiveQuotationsForSPK()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            // Load DEAL quotations (customer created, has specifications)
            $builder = $this->db->table('quotations q');
            $builder->select('q.id_quotation, q.quotation_number, q.prospect_name, q.is_deal, q.deal_date');
            $builder->select('q.created_customer_id, q.created_contract_id');
            $builder->select('c.customer_name');
            $builder->select('k.id as contract_id, k.no_kontrak, k.status as contract_status');
            
            // Count UNITS (not specs) - one spec can have multiple units
            $builder->select('(SELECT SUM(qs.quantity) 
                              FROM quotation_specifications qs 
                              WHERE qs.id_quotation = q.id_quotation) as total_units');
            $builder->select('(SELECT SUM(qs.quantity - COALESCE((SELECT SUM(s.jumlah_unit) 
                                   FROM spk s 
                                   WHERE s.quotation_specification_id = qs.id_specification), 0))
                              FROM quotation_specifications qs 
                              WHERE qs.id_quotation = q.id_quotation) as available_units');
            $builder->select('(SELECT COUNT(*) FROM quotation_specifications qs WHERE qs.id_quotation = q.id_quotation) as total_specs');
            
            // Join customer info
            $builder->join('customers c', 'c.id = q.created_customer_id', 'left');
            
            // Join contract info (OPTIONAL - may be NULL)
            $builder->join('kontrak k', 'k.id = q.created_contract_id', 'left');
            
            // Filters
            $builder->where('q.is_deal', 1); // Must be DEAL
            $builder->where('q.created_customer_id IS NOT NULL'); // Must have customer
            $builder->having('total_units >', 0); // Must have units
            $builder->having('available_units >', 0); // Must have available units
            
            $builder->groupBy('q.id_quotation');
            $builder->orderBy('q.deal_date', 'DESC');
            
            // DEBUG: Log the SQL query
            $sql = $builder->getCompiledSelect(false); // false = don't reset the query
            log_message('info', 'getActiveQuotationsForSPK SQL: ' . $sql);
            
            $quotations = $builder->get()->getResultArray();
            
            log_message('info', 'getActiveQuotationsForSPK: Found ' . count($quotations) . ' DEAL quotations with available specs');
            
            // DEBUG: Log first quotation details if exists
            if (!empty($quotations)) {
                log_message('info', 'First quotation: ' . json_encode($quotations[0]));
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $quotations,
                'message' => count($quotations) === 0 ? 'No DEAL quotations with specifications found' : null
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getActiveQuotationsForSPK error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load quotations: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get specifications from quotation for SPK creation
     * Contract link is optional - SPK can be created without contract
     */
    public function getQuotationSpecificationsForSPK($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            // Get quotation info
            $builder = $this->db->table('quotations q');
            $builder->select('q.*, c.customer_name');
            $builder->select('k.no_kontrak, k.customer_po_number, k.status as contract_status');
            $builder->join('customers c', 'c.id = q.created_customer_id', 'left');
            $builder->join('kontrak k', 'k.id = q.created_contract_id', 'left');
            $builder->where('q.id_quotation', $quotationId);
            $quotation = $builder->get()->getRowArray();
            
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation not found'
                ]);
            }
            
            // Get specifications - using same field mapping as QuotationSpecificationModel
            $specBuilder = $this->db->table('quotation_specifications qs');
            $specBuilder->select('qs.*');
            $specBuilder->select('d.nama_departemen');
            $specBuilder->select('tu.tipe as nama_tipe_unit, tu.jenis as jenis_tipe_unit');
            $specBuilder->select('k.kapasitas_unit as nama_kapasitas');
            $specBuilder->select('mu.merk_unit, mu.model_unit');
            $specBuilder->select('b.jenis_baterai');
            $specBuilder->select('c.merk_charger, c.tipe_charger');
            $specBuilder->select('a.tipe as attachment_tipe, a.merk as attachment_merk');
            $specBuilder->select('v.jumlah_valve as valve_name');
            $specBuilder->select('m.tipe_mast as mast_name');
            $specBuilder->select('tb.tipe_ban as tire_name');
            $specBuilder->select('jr.tipe_roda as wheel_name');
            $specBuilder->select('(SELECT COUNT(*) FROM spk WHERE spk.quotation_specification_id = qs.id_specification) as existing_spk_count');
            $specBuilder->select('(qs.quantity - COALESCE((SELECT SUM(jumlah_unit) FROM spk WHERE spk.quotation_specification_id = qs.id_specification), 0)) as available_units');
            
            $specBuilder->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left');
            $specBuilder->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left');
            $specBuilder->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left');
            $specBuilder->join('model_unit mu', 'mu.id_model_unit = qs.brand_id', 'left');
            $specBuilder->join('baterai b', 'b.id = qs.battery_id', 'left');
            $specBuilder->join('charger c', 'c.id_charger = qs.charger_id', 'left');
            $specBuilder->join('attachment a', 'a.id_attachment = qs.attachment_id', 'left');
            $specBuilder->join('valve v', 'v.id_valve = qs.valve_id', 'left');
            $specBuilder->join('tipe_mast m', 'm.id_mast = qs.mast_id', 'left');
            $specBuilder->join('tipe_ban tb', 'tb.id_ban = qs.ban_id', 'left');
            $specBuilder->join('jenis_roda jr', 'jr.id_roda = qs.roda_id', 'left');
            
            $specBuilder->where('qs.id_quotation', $quotationId);
            $specBuilder->orderBy('qs.id_specification', 'ASC');
            
            $specifications = $specBuilder->get()->getResultArray();
            
            log_message('info', 'getQuotationSpecificationsForSPK: Quotation ' . $quotationId . ' has ' . count($specifications) . ' specs');
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $specifications,
                'quotation' => $quotation
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getQuotationSpecificationsForSPK error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load specifications: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * OLD METHOD - Keep for backward compatibility with contracts
     * Get DEAL quotations for SPK creation (with or without contract)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            // NEW APPROACH: Load contracts with quotation specifications
            // This aligns with quotation → SPK workflow
            $builder = $this->db->table('kontrak k');
            $builder->select('k.id, k.no_kontrak, k.kontrak_number, k.po_number, k.customer_id, k.start_date, k.end_date, k.status');
            $builder->select('c.customer_name, c.customer_code');
            $builder->select('q.id_quotation, q.quotation_number, q.prospect_name');
            $builder->select('(SELECT COUNT(*) FROM quotation_specifications qs WHERE qs.quotation_id = q.id_quotation) as total_specs');
            $builder->select('(SELECT COUNT(*) FROM quotation_specifications qs 
                              LEFT JOIN spk s ON s.kontrak_spesifikasi_id = qs.id_specification 
                              WHERE qs.quotation_id = q.id_quotation AND s.id_spk IS NULL) as available_specs');
            
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            $builder->join('quotations q', 'q.created_contract_id = k.id', 'left'); // Link via created_contract_id
            
            $builder->where('k.status', 'ACTIVE');
            $builder->where('q.id_quotation IS NOT NULL'); // Only contracts with quotations
            $builder->groupBy('k.id');
            $builder->orderBy('k.dibuat_pada', 'DESC');
            
            $contracts = $builder->get()->getResultArray();
            
            log_message('info', 'getActiveContracts: Found ' . count($contracts) . ' contracts with quotation specs');
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts,
                'message' => count($contracts) === 0 ? 'No contracts with quotation specifications found' : null
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getActiveContracts error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load contracts: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get specifications from quotation for a given contract
     * This replaces the old contract-based specification loading
     */
    public function getQuotationSpecificationsForContract($contractId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            // Get quotation linked to this contract
            $builder = $this->db->table('quotations q');
            $builder->select('q.id_quotation, q.quotation_number');
            $builder->where('q.created_contract_id', $contractId);
            $builder->where('q.is_deal', 1);
            $quotation = $builder->get()->getRowArray();
            
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No quotation found for this contract'
                ]);
            }
            
            // Get specifications from quotation
            $specBuilder = $this->db->table('quotation_specifications qs');
            $specBuilder->select('qs.*');
            $specBuilder->select('d.nama_departemen, tu.jenis_tipe_unit, tu.nama_tipe_unit');
            $specBuilder->select('k.nama_kapasitas, att.tipe_attachment, att.merk_attachment');
            $specBuilder->select('v.valve_name, m.mast_name, t.tire_name, w.wheel_name');
            $specBuilder->select('(SELECT COUNT(*) FROM spk WHERE spk.kontrak_spesifikasi_id = qs.id_specification) as existing_spk_count');
            $specBuilder->select('(qs.quantity - COALESCE((SELECT SUM(jumlah_unit) FROM spk WHERE spk.kontrak_spesifikasi_id = qs.id_specification), 0)) as available_units');
            
            $specBuilder->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left');
            $specBuilder->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left');
            $specBuilder->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left');
            $specBuilder->join('attachment att', 'att.id_attachment = qs.attachment_id', 'left');
            $specBuilder->join('valve v', 'v.id_valve = qs.valve_id', 'left');
            $specBuilder->join('mast m', 'm.id_mast = qs.mast_id', 'left');
            $specBuilder->join('tire t', 't.id_tire = qs.tire_id', 'left');
            $specBuilder->join('wheel w', 'w.id_wheel = qs.wheel_id', 'left');
            
            $specBuilder->where('qs.quotation_id', $quotation['id_quotation']);
            $specBuilder->orderBy('qs.id_specification', 'ASC');
            
            $specifications = $specBuilder->get()->getResultArray();
            
            log_message('info', 'getQuotationSpecificationsForContract: Contract ' . $contractId . ' has ' . count($specifications) . ' specs from quotation ' . $quotation['id_quotation']);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $specifications,
                'quotation' => $quotation
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getQuotationSpecificationsForContract error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load specifications: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Create prorate amendment for mid-period rate change
     * Calculates split billing: (days × old_rate) + (days × new_rate)
     */
    public function createProrateAmendment()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        $this->db->transStart();
        
        try {
            // Parse POST data
            $contractId = $this->request->getPost('contract_id');
            $effectiveDate = $this->request->getPost('effective_date');
            $reason = $this->request->getPost('reason');
            $notes = $this->request->getPost('notes');
            $periodStart = $this->request->getPost('period_start');
            $periodEnd = $this->request->getPost('period_end');
            $unitRatesJson = $this->request->getPost('unit_rates');
            
            // Validate contract exists
            $contract = $this->kontrakModel->find($contractId);
            if (!$contract) {
                throw new \Exception('Contract not found');
            }
            
            // Parse unit rates
            $unitRates = json_decode($unitRatesJson, true);
            if (!$unitRates || count($unitRates) === 0) {
                throw new \Exception('No unit rates provided');
            }
            
            // Calculate prorate split
            $effectiveDateObj = new \DateTime($effectiveDate);
            $periodStartObj = new \DateTime($periodStart);
            $periodEndObj = new \DateTime($periodEnd);
            
            $daysBeforeAmendment = $periodStartObj->diff($effectiveDateObj)->days;
            $daysAfterAmendment = $effectiveDateObj->diff($periodEndObj)->days + 1;
            $totalDays = $daysBeforeAmendment + $daysAfterAmendment;
            
            // Create contract_amendments record
            $amendmentData = [
                'contract_id' => $contractId,
                'amendment_type' => 'RATE_CHANGE',
                'effective_date' => $effectiveDate,
                'reason' => $reason,
                'notes' => $notes,
                'status' => 'APPROVED', // Auto-approve for now
                'created_by' => session()->get('user_id') ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'prorate_split' => json_encode([
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'effective_date' => $effectiveDate,
                    'days_before' => $daysBeforeAmendment,
                    'days_after' => $daysAfterAmendment,
                    'total_days' => $totalDays
                ])
            ];
            
            $amendmentBuilder = $this->db->table('contract_amendments');
            $amendmentBuilder->insert($amendmentData);
            $amendmentId = $this->db->insertID();
            
            // Create amendment_unit_rates records
            $totalOldAmount = 0;
            $totalNewAmount = 0;
            
            foreach ($unitRates as $unitRate) {
                $unitId = $unitRate['unit_id'];
                $oldRate = floatval($unitRate['old_rate']);
                $newRate = floatval($unitRate['new_rate']);
                
                // Calculate prorate amounts
                $oldAmount = ($oldRate / 30) * $daysBeforeAmendment;
                $newAmount = ($newRate / 30) * $daysAfterAmendment;
                
                $totalOldAmount += $oldAmount;
                $totalNewAmount += $newAmount;
                
                // Insert amendment unit rate
                $unitRateBuilder = $this->db->table('amendment_unit_rates');
                $unitRateBuilder->insert([
                    'amendment_id' => $amendmentId,
                    'unit_id' => $unitId,
                    'old_rate' => $oldRate,
                    'new_rate' => $newRate,
                    'prorate_old_amount' => $oldAmount,
                    'prorate_new_amount' => $newAmount,
                    'prorate_days_before' => $daysBeforeAmendment,
                    'prorate_days_after' => $daysAfterAmendment
                ]);
                
                // Update contract_units with new rate
                $contractUnitsBuilder = $this->db->table('contract_units');
                $contractUnitsBuilder->where('contract_id', $contractId);
                $contractUnitsBuilder->where('unit_id', $unitId);
                $contractUnitsBuilder->update([
                    'monthly_rate' => $newRate,
                    'rate_changed_at' => $effectiveDate
                ]);
            }
            
            // Update amendment with calculated amounts
            $amendmentBuilder = $this->db->table('contract_amendments');
            $amendmentBuilder->where('id', $amendmentId);
            $amendmentBuilder->update([
                'old_total_value' => $totalOldAmount,
                'new_total_value' => $totalNewAmount,
                'prorate_total' => $totalOldAmount + $totalNewAmount
            ]);
            
            // Log activity
            $this->logActivity(
                'contract_amendment',
                $amendmentId,
                'create',
                "Prorate amendment created for contract {$contract['no_kontrak']} effective {$effectiveDate}",
                [
                    'contract_number' => $contract['no_kontrak'],
                    'effective_date' => $effectiveDate,
                    'reason' => $reason,
                    'days_before' => $daysBeforeAmendment,
                    'days_after' => $daysAfterAmendment,
                    'total_amount' => $totalOldAmount + $totalNewAmount
                ]
            );
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Amendment created successfully with prorate split',
                'amendment_id' => $amendmentId,
                'prorate_total' => $totalOldAmount + $totalNewAmount
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'createProrateAmendment error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create amendment: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get all contracts for dropdown/selection
     */
    public function getAllContracts()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            $builder = $this->db->table('kontrak k');
            $builder->select('k.*, c.customer_name, 
                             (SELECT COUNT(DISTINCT ku.customer_location_id) 
                              FROM kontrak_unit ku 
                              WHERE ku.kontrak_id = k.id) as location_count');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            $builder->orderBy('k.dibuat_pada', 'DESC');
            
            $contracts = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getAllContracts error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load contracts: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get contract history with all events (amendments, renewals, unit changes)
     */
    public function getContractHistory($contractId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            $events = [];
            
            // Contract created event
            $contract = $this->kontrakModel->find($contractId);
            if ($contract) {
                $events[] = [
                    'id' => $contractId,
                    'type' => 'contract',
                    'date' => $contract['created_at'],
                    'contract_number' => $contract['no_kontrak'],
                    'description' => 'Contract created',
                    'created_by' => $contract['created_by'] ?? 'System'
                ];
            }
            
            // Amendments
            $amendmentsBuilder = $this->db->table('contract_amendments');
            $amendmentsBuilder->where('contract_id', $contractId);
            $amendments = $amendmentsBuilder->get()->getResultArray();
            
            foreach ($amendments as $amendment) {
                $prorateData = json_decode($amendment['prorate_split'] ?? '{}', true);
                
                $events[] = [
                    'id' => $amendment['id'],
                    'type' => 'amendment',
                    'date' => $amendment['effective_date'],
                    'contract_number' => $contract['no_kontrak'],
                    'description' => 'Contract amendment',
                    'reason' => $amendment['reason'],
                    'total_value' => $amendment['prorate_total'] ?? 0,
                    'prorate' => $prorateData,
                    'created_by' => $amendment['created_by'] ?? 'System'
                ];
            }
            
            // Renewals (child contracts)
            $renewalsBuilder = $this->db->table('kontrak');
            $renewalsBuilder->where('parent_contract_id', $contractId);
            $renewals = $renewalsBuilder->get()->getResultArray();
            
            foreach ($renewals as $renewal) {
                $events[] = [
                    'id' => $renewal['id'],
                    'type' => 'renewal',
                    'date' => $renewal['created_at'],
                    'contract_number' => $renewal['no_kontrak'],
                    'description' => "Renewal contract created (Generation {$renewal['renewal_generation']})",
                    'total_value' => $renewal['nilai_kontrak'],
                    'created_by' => $renewal['created_by'] ?? 'System'
                ];
            }
            
            // Unit changes
            $unitChangesBuilder = $this->db->table('contract_units cu');
            $unitChangesBuilder->select('cu.*, u.nomor_unit');
            $unitChangesBuilder->join('unit u', 'u.id = cu.unit_id', 'left');
            $unitChangesBuilder->where('cu.contract_id', $contractId);
            $unitChanges = $unitChangesBuilder->get()->getResultArray();
            
            foreach ($unitChanges as $unitChange) {
                if ($unitChange['off_hire_date']) {
                    $events[] = [
                        'id' => $unitChange['id'],
                        'type' => 'unit',
                        'date' => $unitChange['off_hire_date'],
                        'contract_number' => $contract['no_kontrak'],
                        'description' => "Unit {$unitChange['nomor_unit']} off-hired",
                        'action' => 'OFF_HIRE'
                    ];
                }
            }
            
            // Sort by date descending
            usort($events, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'contract' => $contract,
                    'events' => $events
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getContractHistory error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load contract history: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get renewal chain (parent and all descendants)
     */
    public function getRenewalChain($contractId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            $chain = [];
            
            // Find root contract (contract with no parent)
            $rootContract = $this->findRootContract($contractId);
            
            // Build chain recursively
            $this->buildRenewalChain($rootContract['id'], $chain);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'root_contract' => $rootContract,
                    'chain' => $chain
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getRenewalChain error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load renewal chain: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Helper: Find root contract (no parent)
     */
    private function findRootContract($contractId)
    {
        $contract = $this->kontrakModel->find($contractId);
        
        if (!$contract) {
            throw new \Exception('Contract not found');
        }
        
        // If has parent, recursively find root
        if ($contract['parent_contract_id']) {
            return $this->findRootContract($contract['parent_contract_id']);
        }
        
        return $contract;
    }
    
    /**
     * Helper: Build renewal chain recursively
     */
    private function buildRenewalChain($contractId, &$chain)
    {
        $builder = $this->db->table('kontrak k');
        $builder->select('k.*, c.customer_name');
        $builder->join('customers c', 'c.id = k.customer_id', 'left');
        $builder->where('k.id', $contractId);
        $contract = $builder->get()->getRowArray();
        
        if ($contract) {
            // Get units count
            $unitsCount = $this->db->table('contract_units')
                ->where('contract_id', $contractId)
                ->countAllResults();
            
            // Get total value
            $unitsSum = $this->db->table('contract_units')
                ->selectSum('monthly_rate', 'total')
                ->where('contract_id', $contractId)
                ->get()->getRowArray();
            
            $contract['units_count'] = $unitsCount;
            $contract['total_value'] = $unitsSum['total'] ?? 0;
            
            $chain[] = $contract;
            
            // Find children
            $children = $this->db->table('kontrak')
                ->where('parent_contract_id', $contractId)
                ->get()->getResultArray();
            
            foreach ($children as $child) {
                $this->buildRenewalChain($child['id'], $chain);
            }
        }
    }
    
    /**
     * Get rate change history for units/contracts
     */
    public function getRateHistory($contractId = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            // Get contract ID from URL parameter or query string
            if (!$contractId) {
                $contractId = $this->request->getGet('contract_id');
            }
            
            $unitId = $this->request->getGet('unit_id');
            $days = $this->request->getGet('days');
            
            $history = [];
            
            // Get rate changes from amendments
            $builder = $this->db->table('amendment_unit_rates aur');
            $builder->select('aur.*, ca.effective_date as date, ca.reason, k.no_kontrak as contract_number, iu.no_unit as unit_number');
            $builder->join('contract_amendments ca', 'ca.id = aur.amendment_id', 'left');
            $builder->join('kontrak_unit cu', 'cu.unit_id = aur.unit_id', 'left');
            $builder->join('kontrak k', 'k.id = cu.kontrak_id', 'left');
            $builder->join('inventory_unit iu', 'iu.id_inventory_unit = aur.unit_id', 'left');
            
            if ($contractId) {
                $builder->where('cu.kontrak_id', $contractId);
            }
            
            if ($unitId) {
                $builder->where('aur.unit_id', $unitId);
            }
            
            if ($days && $days !== 'all') {
                $builder->where('ca.effective_date >=', date('Y-m-d', strtotime("-{$days} days")));
            }
            
            $builder->orderBy('ca.effective_date', 'DESC');
            $amendments = $builder->get()->getResultArray();
            
            foreach ($amendments as $amendment) {
                $history[] = [
                    'date' => $amendment['date'],
                    'event_type' => 'Amendment',
                    'contract_number' => $amendment['contract_number'],
                    'unit_number' => $amendment['unit_number'],
                    'old_rate' => $amendment['old_rate'],
                    'new_rate' => $amendment['new_rate'],
                    'reason' => $amendment['reason']
                ];
            }
            
            // Get rate changes from renewals
            $renewalsBuilder = $this->db->table('contract_renewal_unit_map rum');
            $renewalsBuilder->select('rum.*, k1.no_kontrak as parent_contract, k2.no_kontrak as renewal_contract, k2.dibuat_pada as date, iu.no_unit as unit_number');
            $renewalsBuilder->join('kontrak k1', 'k1.id = rum.parent_contract_id', 'left');
            $renewalsBuilder->join('kontrak k2', 'k2.id = rum.renewal_contract_id', 'left');
            $renewalsBuilder->join('inventory_unit iu', 'iu.id_inventory_unit = rum.renewal_unit_id', 'left');
            
            if ($contractId) {
                $renewalsBuilder->where('k2.id', $contractId);
            }
            
            if ($unitId) {
                $renewalsBuilder->where('rum.renewal_unit_id', $unitId);
            }
            
            if ($days && $days !== 'all') {
                $renewalsBuilder->where('k2.dibuat_pada >=', date('Y-m-d', strtotime("-{$days} days")));
            }
            
            $renewalsBuilder->orderBy('k2.dibuat_pada', 'DESC');
            $renewals = $renewalsBuilder->get()->getResultArray();
            
            foreach ($renewals as $renewal) {
                if ($renewal['old_rate'] != $renewal['new_rate']) {
                    $history[] = [
                        'date' => $renewal['date'],
                        'event_type' => 'Renewal',
                        'contract_number' => $renewal['renewal_contract'],
                        'unit_number' => $renewal['unit_number'],
                        'old_rate' => $renewal['old_rate'],
                        'new_rate' => $renewal['new_rate'],
                        'reason' => 'Contract renewal'
                    ];
                }
            }
            
            // Sort by date
            usort($history, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $history
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getRateHistory error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load rate history: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get unit journey across all contracts
     */
    public function getUnitJourney($unitId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            // Get all contracts this unit has been part of
            $builder = $this->db->table('contract_units cu');
            $builder->select('cu.*, k.id as contract_id, k.no_kontrak, k.start_date, k.end_date, k.status, c.customer_name, cu.monthly_rate');
            $builder->join('kontrak k', 'k.id = cu.contract_id', 'left');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            $builder->where('cu.unit_id', $unitId);
            $builder->orderBy('cu.on_hire_date', 'ASC');
            
            $contracts = $builder->get()->getResultArray();
            
            // Get amendments for each contract
            foreach ($contracts as &$contract) {
                $amendmentsBuilder = $this->db->table('contract_amendments ca');
                $amendmentsBuilder->select('ca.*, aur.old_rate, aur.new_rate');
                $amendmentsBuilder->join('amendment_unit_rates aur', 'aur.amendment_id = ca.id', 'left');
                $amendmentsBuilder->where('ca.contract_id', $contract['contract_id']);
                $amendmentsBuilder->where('aur.unit_id', $unitId);
                
                $contract['amendments'] = $amendmentsBuilder->get()->getResultArray();
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'unit_id' => $unitId,
                    'contracts' => $contracts
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getUnitJourney error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load unit journey: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get all units for dropdown
     */
    public function getAllUnits()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            $builder = $this->db->table('inventory_unit iu');
            $builder->select('iu.id_inventory_unit AS id, iu.no_unit_na AS nomor_unit, tu.tipe AS tipe_unit');
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
            $builder->orderBy('iu.no_unit_na', 'ASC');
            
            $units = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $units
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getAllUnits error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load units: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get documents attached to a contract
     */
    public function documents($kontrakId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            if (!$this->db->tableExists('contract_documents')) {
                return $this->response->setJSON(['success' => true, 'data' => []]);
            }

            $docs = $this->db->table('contract_documents')
                ->where('kontrak_id', (int)$kontrakId)
                ->orderBy('created_at', 'DESC')
                ->get()->getResultArray();

            $formatted = array_map(function ($d) {
                return [
                    'id'          => $d['id'],
                    'file_name'   => $d['file_name'] ?? $d['filename'] ?? '—',
                    'file_path'   => $d['file_path'] ?? $d['filepath'] ?? '',
                    'uploaded_at' => $d['created_at'] ?? '',
                    'uploaded_by' => $d['uploaded_by'] ?? 'System',
                ];
            }, $docs);

            return $this->response->setJSON(['success' => true, 'data' => $formatted]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a contract document by ID
     */
    public function deleteDocument($docId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            if (!$this->db->tableExists('contract_documents')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documents table does not exist']);
            }

            $doc = $this->db->table('contract_documents')->where('id', (int)$docId)->get()->getRowArray();
            if (!$doc) {
                return $this->response->setJSON(['success' => false, 'message' => 'Document not found']);
            }

            // Delete physical file if exists
            $path = FCPATH . ltrim($doc['file_path'] ?? '', '/');
            if (file_exists($path)) {
                @unlink($path);
            }

            $this->db->table('contract_documents')->where('id', (int)$docId)->delete();

            return $this->response->setJSON(['success' => true, 'message' => 'Document deleted']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    