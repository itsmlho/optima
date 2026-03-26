<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QuotationModel;
use App\Models\QuotationSpecificationModel;
use App\Traits\ActivityLoggingTrait;

class Quotation extends BaseController
{
    use ActivityLoggingTrait;
    
    protected $quotationModel;
    protected $quotationSpecificationModel;
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->quotationModel = new QuotationModel();
        $this->quotationSpecificationModel = new QuotationSpecificationModel();
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
    }

    /**
     * Display main quotations management page
     */
    public function index()
    {
        // Load simple_rbac helper
        helper('simple_rbac');
        
        $data = [
            'title' => 'Quotations Management',
            'can_view_marketing' => can_view('marketing'),
            'can_create_marketing' => can_create('marketing'),
            'can_export_marketing' => can_export('marketing'),
            'loadDataTables' => true, // Enable DataTables loading
        ];
        
        return view('marketing/quotations', $data);
    }

    /**
     * Get data for DataTables Server-Side
     */
    public function getDataTable()
    {
        try {
            // Get request parameters
            $draw = intval($this->request->getPost('draw') ?? 1);
            $start = intval($this->request->getPost('start') ?? 0);
            $length = intval($this->request->getPost('length') ?? 10);
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            $orderColumnIndex = $this->request->getPost('order')[0]['column'] ?? 0;
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';
            
            // Get data from model
            $result = $this->quotationModel->getQuotationsForDataTable(
                $start, 
                $length, 
                $searchValue, 
                $orderColumnIndex, 
                $orderDir
            );
            
            $data = [];
            $no = $start + 1;
            
            foreach ($result['data'] as $quotation) {
                $row = [];
                $row['DT_RowIndex'] = $no++;
                $row['quotation_number'] = esc($quotation['quotation_number']);
                $row['customer_name'] = esc($quotation['prospect_name']);
                $row['description'] = esc($quotation['quotation_title'] ?? '-');
                $row['amount'] = $quotation['total_amount'] ?? 0;
                $row['status'] = strtolower($quotation['stage'] ?? 'draft');
                $row['valid_until'] = date('d M Y', strtotime($quotation['valid_until']));
                $row['created_at'] = date('d M Y H:i', strtotime($quotation['created_at']));
                // Action buttons handled by Marketing controller
                $row['actions'] = '';
                
                $data[] = $row;
            }

            $response = [
                "draw" => $draw,
                "recordsTotal" => $result['total'],
                "recordsFiltered" => $result['total'],
                "data" => $data
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'error' => 'Terjadi kesalahan pada server. Silakan coba lagi.',
                'draw' => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }

    /**
     * Get statistics for AJAX
     */
    public function getStats()
    {
        try {
            $stats = $this->quotationModel->getStats();
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Store new quotation
     */
    public function store()
    {
        if (!can_create('marketing')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to create quotations'
            ]);
        }

        try {
            $validationRules = [
                'quotation_number' => 'required|max_length[50]',
                'prospect_name' => 'required|max_length[255]',
                'quotation_title' => 'required|max_length[255]',
                'quotation_date' => 'required|valid_date',
                'valid_until' => 'required|valid_date',
                'stage' => 'required|in_list[DRAFT,SENT,ACCEPTED,REJECTED,EXPIRED]'
            ];

            if (!$this->validate($validationRules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $data = [
                'quotation_number' => $this->request->getPost('quotation_number'),
                'prospect_name' => $this->request->getPost('prospect_name'),
                'prospect_company' => $this->request->getPost('prospect_company'),
                'prospect_email' => $this->request->getPost('prospect_email'),
                'prospect_phone' => $this->request->getPost('prospect_phone'),
                'quotation_title' => $this->request->getPost('quotation_title'),
                'quotation_date' => $this->request->getPost('quotation_date'),
                'valid_until' => $this->request->getPost('valid_until'),
                'stage' => $this->request->getPost('stage') ?? 'DRAFT',
                'total_amount' => $this->request->getPost('total_amount') ?? 0,
                'notes' => $this->request->getPost('notes'),
            ];

            $quotationId = $this->quotationModel->insert($data);

            if ($quotationId) {
                $this->logActivity('quotation_created', 'quotations', $quotationId, 'Created quotation: ' . $data['quotation_number']);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Quotation created successfully',
                    'id' => $quotationId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Quotation store error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading quotation'
            ]);
        }
    }

    /**
     * Update quotation
     */
    public function update($id)
    {
        if (!can_edit('marketing')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to edit quotations'
            ]);
        }

        try {
            $quotation = $this->quotationModel->find($id);
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            $validationRules = [
                'quotation_number' => 'required|max_length[50]',
                'prospect_name' => 'required|max_length[255]',
                'quotation_title' => 'required|max_length[255]',
                'quotation_date' => 'required|valid_date',
                'valid_until' => 'required|valid_date',
                'stage' => 'required|in_list[DRAFT,SENT,ACCEPTED,REJECTED,EXPIRED]'
            ];

            if (!$this->validate($validationRules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $data = [
                'quotation_number' => $this->request->getPost('quotation_number'),
                'prospect_name' => $this->request->getPost('prospect_name'),
                'prospect_company' => $this->request->getPost('prospect_company'),
                'prospect_email' => $this->request->getPost('prospect_email'),
                'prospect_phone' => $this->request->getPost('prospect_phone'),
                'quotation_title' => $this->request->getPost('quotation_title'),
                'quotation_date' => $this->request->getPost('quotation_date'),
                'valid_until' => $this->request->getPost('valid_until'),
                'stage' => $this->request->getPost('stage'),
                'total_amount' => $this->request->getPost('total_amount') ?? 0,
                'notes' => $this->request->getPost('notes'),
            ];

            if ($this->quotationModel->update($id, $data)) {
                $this->logActivity('quotation_updated', 'quotations', $id, 'Updated quotation: ' . $data['quotation_number']);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Quotation updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Quotation update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Delete quotation
     */
    public function delete($id)
    {
        if (!can_delete('marketing')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to delete quotations'
            ]);
        }

        try {
            $quotation = $this->quotationModel->find($id);
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            // Check if quotation is already converted to contract
            if ($quotation['stage'] === 'ACCEPTED' && $quotation['is_deal'] == 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete quotation that has been converted to deal'
                ]);
            }

            if ($this->quotationModel->delete($id)) {
                // Also delete specifications
                $this->quotationSpecificationModel->where('id_quotation', $id)->delete();
                
                $this->logActivity('quotation_deleted', 'quotations', $id, 'Deleted quotation: ' . $quotation['quotation_number']);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Quotation deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Quotation delete error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Update quotation stage
     */
    public function updateStage($id)
    {
        try {
            $stage = $this->request->getPost('stage');
            
            if ($this->quotationModel->updateStage($id, $stage)) {
                $quotation = $this->quotationModel->find($id);
                $this->logActivity('quotation_stage_updated', 'quotations', $id, 'Updated quotation stage: ' . $quotation['quotation_number'] . ' to ' . $stage);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Quotation stage updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Quotation updateStage error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Mark quotation as deal
     */
    public function markAsDeal($id)
    {
        if (!can_edit('marketing')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to mark quotations as deals'
            ]);
        }

        try {
            if ($this->quotationModel->markAsDeal($id)) {
                $quotation = $this->quotationModel->find($id);
                $this->logActivity('quotation_marked_as_deal', 'quotations', $id, 'Marked quotation as deal: ' . $quotation['quotation_number']);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Quotation marked as deal successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Quotation markAsDeal error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get stage badge HTML
     */
    private function getStageBadge($stage)
    {
        $badges = [
            'DRAFT' => '<span class="badge bg-secondary">Draft</span>',
            'SENT' => '<span class="badge bg-warning">Sent</span>',
            'ACCEPTED' => '<span class="badge bg-success">Accepted</span>',
            'REJECTED' => '<span class="badge bg-danger">Rejected</span>',
            'EXPIRED' => '<span class="badge bg-dark">Expired</span>'
        ];
        
        return $badges[$stage] ?? '<span class="badge bg-light text-dark">' . $stage . '</span>';
    }

    /**
     * Get quotation statistics
     */
    public function getStatistics()
    {
        try {
            if (!$this->session->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Session expired'
                ]);
            }

            $stats = $this->quotationModel->getQuotationStatistics();
            
            return $this->response->setJSON($stats);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting quotation statistics. Silakan coba lagi.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load statistics'
            ]);
        }
    }

    /**
     * Get single quotation data
     */
    public function getQuotation($id)
    {
        try {
            // Debug: Log the incoming request
            log_message('debug', "getQuotation called with ID: $id");
            
            if (!$this->session->get('isLoggedIn')) {
                log_message('debug', "Session check failed - user not logged in");
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Session expired'
                ]);
            }

            // Debug: Log before database query
            log_message('debug', "Attempting to query quotation with ID: $id");
            $quotation = $this->quotationModel->getQuotationDetail($id);
            
            if (!$quotation) {
                log_message('debug', "Quotation not found for ID: $id");
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }
            
            // Debug: Log successful query
            log_message('debug', "Quotation found: " . json_encode($quotation));
            
            // Map database fields to frontend expectations
            $response = [
                'status' => 'success',
                'id' => $quotation['id_quotation'],
                'quotation_number' => $quotation['quotation_number'],
                'customer_id' => $quotation['created_customer_id'] ?? null,
                'description' => $quotation['quotation_title'],
                'amount' => $quotation['total_amount'],
                'valid_until' => $quotation['valid_until'],
                'notes' => $quotation['quotation_description'] ?? '',
                // Include all original data for other uses
                'data' => $quotation
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    // ============= QUOTATION SPECIFICATIONS ENDPOINTS =============

    /**
     * Get quotation specifications (alias method for different route)
     */
    public function getQuotationSpecifications($quotationId)
    {
        return $this->getSpecifications($quotationId);
    }

    /**
     * Get quotation specifications
     */
    public function getSpecifications($quotationId)
    {
        try {
            // Debug: Log the incoming request
            log_message('debug', "getSpecifications called with quotationId: $quotationId");
            
            if (!$this->session->get('isLoggedIn')) {
                log_message('debug', "Session check failed - user not logged in");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session expired'
                ]);
            }

            // Verify quotation exists
            log_message('debug', "Verifying quotation exists for ID: $quotationId");
            $quotation = $this->quotationModel->find($quotationId);
            if (!$quotation) {
                log_message('debug', "Quotation not found for ID: $quotationId");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            log_message('debug', "Loading specifications for quotation: $quotationId");
            $specifications = $this->quotationSpecificationModel->getQuotationSpecifications($quotationId);
            
            log_message('debug', "Loading specifications summary for quotation: $quotationId");
            $summary = $this->quotationSpecificationModel->getSpecificationsSummary($quotationId);
            
            log_message('debug', "Specifications loaded successfully. Count: " . count($specifications));
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $specifications,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Add new specification to quotation - following Kontrak::addSpesifikasi pattern
     */
    public function addSpecification()
    {
        try {
            if (!$this->session->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session expired'
                ]);
            }

            $quotationId = $this->request->getPost('id_quotation');
            
            if (!$quotationId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation ID is required'
                ]);
            }

            // Verify quotation exists
            $quotation = $this->quotationModel->find($quotationId);
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            // Check workflow stage - only allow specifications for QUOTATION and SENT stages
            if (!in_array($quotation['workflow_stage'], ['QUOTATION', 'SENT'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Specifications can only be added to quotations in QUOTATION or SENT stage. Current stage: ' . $quotation['workflow_stage']
                ]);
            }

            // Prepare data using NEW field names
            // Sanitize price fields to handle empty strings properly
            $unitPrice = trim($this->request->getPost('unit_price') ?? '');
            $dailyPrice = trim($this->request->getPost('harga_per_unit_harian') ?? '');
            $operatorMonthly = trim($this->request->getPost('operator_price_monthly') ?? '');
            $operatorDaily = trim($this->request->getPost('operator_price_daily') ?? '');
            
            $data = [
                'id_quotation' => $quotationId,
                'specification_name' => $this->request->getPost('specification_name'),
                'specification_type' => $this->request->getPost('specification_type') ?: 'UNIT',
                'quantity' => (int)$this->request->getPost('quantity'),
                'spare_quantity' => (int)$this->request->getPost('spare_quantity') ?: 0,
                'is_spare_unit' => (int)$this->request->getPost('is_spare_unit') ?: 0,
                'monthly_price' => ($unitPrice !== '' && $unitPrice !== null) ? (float)$unitPrice : 0,
                'daily_price' => ($dailyPrice !== '' && $dailyPrice !== null) ? (float)$dailyPrice : 0,
                'departemen_id' => $this->request->getPost('departemen_id') ?: null,
                'tipe_unit_id' => $this->request->getPost('tipe_unit_id') ?: null,
                'kapasitas_id' => $this->request->getPost('kapasitas_id') ?: null,
                'brand_id' => $this->request->getPost('brand_id') ?: null,
                'battery_id' => $this->request->getPost('battery_id') ?: null,
                'charger_id' => $this->request->getPost('charger_id') ?: null,
                'attachment_id' => $this->request->getPost('attachment_id') ?: null,
                'valve_id' => $this->request->getPost('valve_id') ?: null,
                'mast_id' => $this->request->getPost('mast_id') ?: null,
                'ban_id' => $this->request->getPost('ban_id') ?: null,
                'roda_id' => $this->request->getPost('roda_id') ?: null,
                'include_operator' => (int)$this->request->getPost('include_operator') ?: 0,
                'operator_quantity' => (int)$this->request->getPost('operator_quantity') ?: 0,
                'operator_monthly_rate' => ($operatorMonthly !== '' && $operatorMonthly !== null) ? (float)$operatorMonthly : 0,
                'operator_daily_rate' => ($operatorDaily !== '' && $operatorDaily !== null) ? (float)$operatorDaily : 0,
                'is_active' => 1
            ];
            
            // DEBUG: Log spare and operator data being saved
            log_message('debug', '=== ADD SPECIFICATION DATA ===');
            log_message('debug', 'spare_quantity: ' . $data['spare_quantity']);
            log_message('debug', 'include_operator: ' . $data['include_operator']);
            log_message('debug', 'operator_quantity: ' . $data['operator_quantity']);
            log_message('debug', 'operator_monthly_rate: ' . $data['operator_monthly_rate']);
            log_message('debug', 'operator_daily_rate: ' . $data['operator_daily_rate']);
            
            // If spare unit (legacy flag), set prices to 0 (no billing)
            // This maintains backward compatibility with old spare unit behavior
            if ($data['is_spare_unit'] == 1) {
                $data['monthly_price'] = 0;
                $data['daily_price'] = 0;
            }
            
            // Handle accessories array - store in dedicated column
            $aksesoris = $this->request->getPost('aksesoris');
            if ($aksesoris && is_array($aksesoris)) {
                $data['unit_accessories'] = implode(', ', $aksesoris);
            } else {
                $data['unit_accessories'] = '';
            }
            
            // Calculate total price with NEW formula: (quantity * monthly_price) + daily_price
            // Only billable quantity is charged - spare_quantity does NOT affect billing
            // Spare units will have 0 total price
            // Explicitly cast to numeric types to avoid "Unsupported operand types" error
            $data['total_price'] = ((int)$data['quantity'] * (float)$data['monthly_price']) + (float)$data['daily_price'];

            $specId = $this->quotationSpecificationModel->insert($data);
            
            if ($specId) {
                // Update quotation total
                $this->quotationSpecificationModel->updateQuotationTotal($quotationId);
                
                // Get updated quotation total
                $updatedQuotation = $this->quotationModel->find($quotationId);
                
                // Log activity
                $this->logActivity(
                    'quotation_specification_created',
                    'quotation_specifications',
                    $specId,
                    'Added specification to quotation ' . $quotation['quotation_number']
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Specification added successfully',
                    'data' => [
                        'id' => $specId,
                        'quotation_total' => $updatedQuotation['total_amount'] ?? 0
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Quotation addSpecification error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Update quotation specification
     */
    public function updateSpecification($specId)
    {
        try {
            if (!$this->session->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session expired'
                ]);
            }

            // Get POST data
            $data = $this->request->getPost();
            
            // DEBUG: Log all received POST data
            log_message('debug', '=== UPDATE SPECIFICATION RECEIVED DATA (Spec ID: ' . $specId . ') ===');
            log_message('debug', 'RAW POST data: ' . json_encode($data));
            log_message('debug', 'spare_quantity in POST: ' . ($data['spare_quantity'] ?? 'NOT SET'));
            log_message('debug', 'include_operator in POST: ' . ($data['include_operator'] ?? 'NOT SET'));
            log_message('debug', 'operator_quantity in POST: ' . ($data['operator_quantity'] ?? 'NOT SET'));
            
            // Verify specification exists
            $specification = $this->quotationSpecificationModel->find($specId);
            if (!$specification) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan'
                ]);
            }

            // Check quotation workflow stage
            $quotation = $this->quotationModel->find($specification['id_quotation']);
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if (!in_array($quotation['workflow_stage'], ['QUOTATION', 'SENT'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Specifications can only be modified for quotations in QUOTATION or SENT stage. Current stage: ' . $quotation['workflow_stage']
                ]);
            }

            // Rename old field names to new ones if sent from frontend
            // Also sanitize empty strings to prevent "Incorrect decimal value" errors
            if (isset($data['unit_price'])) {
                $value = trim($data['unit_price']);
                $data['monthly_price'] = ($value !== '' && $value !== null) ? (float)$value : 0;
                unset($data['unit_price']);
            }
            if (isset($data['harga_per_unit_harian'])) {
                $value = trim($data['harga_per_unit_harian']);
                $data['daily_price'] = ($value !== '' && $value !== null) ? (float)$value : 0;
                unset($data['harga_per_unit_harian']);
            }
            if (isset($data['merk_unit'])) {
                $data['brand_id'] = $data['merk_unit'];
                unset($data['merk_unit']);
            }
            if (isset($data['jenis_baterai'])) {
                $data['battery_id'] = $data['jenis_baterai'];
                unset($data['jenis_baterai']);
            }
            if (isset($data['attachment_tipe'])) {
                $data['attachment_id'] = $data['attachment_tipe'];
                unset($data['attachment_tipe']);
            }
            
            // Handle operator fields
            if (isset($data['operator_price_monthly'])) {
                $value = trim($data['operator_price_monthly']);
                $data['operator_monthly_rate'] = ($value !== '' && $value !== null) ? (float)$value : 0;
                unset($data['operator_price_monthly']);
            }
            if (isset($data['operator_price_daily'])) {
                $value = trim($data['operator_price_daily']);
                $data['operator_daily_rate'] = ($value !== '' && $value !== null) ? (float)$value : 0;
                unset($data['operator_price_daily']);
            }
            if (isset($data['include_operator'])) {
                $data['include_operator'] = (int)$data['include_operator'];
            }
            if (isset($data['operator_quantity'])) {
                $data['operator_quantity'] = (int)$data['operator_quantity'];
            }

            // Handle spare unit flag - if spare, set prices to 0
            $isSpareUnit = isset($data['is_spare_unit']) ? (int)$data['is_spare_unit'] : $specification['is_spare_unit'];
            
            // Handle spare quantity
            if (isset($data['spare_quantity'])) {
                $data['spare_quantity'] = (int)$data['spare_quantity'];
            }
            
            if ($isSpareUnit == 1) {
                $data['monthly_price'] = 0;
                $data['daily_price'] = 0;
            }

            // Calculate total price with NEW formula: (quantity * monthly_price) + daily_price
            // Only billable quantity is charged - spare_quantity does NOT affect billing
            // Spare units will have 0 total price
            if (isset($data['quantity']) || isset($data['monthly_price']) || isset($data['daily_price'])) {
                // Explicitly cast to numeric types to avoid "Unsupported operand types" error
                $qty = (int)($data['quantity'] ?? $specification['quantity'] ?? 0);
                $monthlyPrice = (float)($data['monthly_price'] ?? $specification['monthly_price'] ?? 0);
                $dailyPrice = (float)($data['daily_price'] ?? $specification['daily_price'] ?? 0);
                $data['total_price'] = ($qty * $monthlyPrice) + $dailyPrice;
            }

            // Handle accessories array - store in dedicated column
            if (isset($data['aksesoris']) && is_array($data['aksesoris'])) {
                $data['unit_accessories'] = implode(', ', $data['aksesoris']);
                unset($data['aksesoris']); // Remove to avoid DB error
            } elseif (isset($data['aksesoris'])) {
                $data['unit_accessories'] = $data['aksesoris'];
                unset($data['aksesoris']);
            }
            
            // Remove fields that don't exist in database or shouldn't be updated
            $fieldsToRemove = [
                'notes',                    // Not a database column
                'id_quotation',             // Should not be updated
                'id_specification',         // Should not be updated  
                'csrf_test_name',           // CSRF token
                'specification_description' // Not used
            ];
            
            foreach ($fieldsToRemove as $field) {
                if (isset($data[$field])) {
                    unset($data[$field]);
                }
            }
            
            // DEBUG: Log spare and operator data being updated
            log_message('debug', '=== UPDATE SPECIFICATION DATA (Spec ID: ' . $specId . ') ===');
            if (isset($data['spare_quantity'])) {
                log_message('debug', 'spare_quantity: ' . $data['spare_quantity']);
            }
            if (isset($data['include_operator'])) {
                log_message('debug', 'include_operator: ' . $data['include_operator']);
            }
            if (isset($data['operator_quantity'])) {
                log_message('debug', 'operator_quantity: ' . $data['operator_quantity']);
            }
            if (isset($data['operator_monthly_rate'])) {
                log_message('debug', 'operator_monthly_rate: ' . $data['operator_monthly_rate']);
            }
            if (isset($data['operator_daily_rate'])) {
                log_message('debug', 'operator_daily_rate: ' . $data['operator_daily_rate']);
            }

            if ($this->quotationSpecificationModel->update($specId, $data)) {
                // Update quotation total after specification update
                $this->quotationSpecificationModel->updateQuotationTotal($specification['id_quotation']);
                
                // Log activity
                $this->logActivity(
                    'quotation_specification_updated',
                    'quotation_specifications',
                    $specId,
                    'Updated quotation specification: ' . ($data['specification_name'] ?? 'Specification')
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Specification updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Quotation updateSpecification error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update specification'
            ]);
        }
    }

    /**
     * Delete quotation specification
     */
    public function deleteSpecification($specId)
    {
        try {
            if (!$this->session->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session expired'
                ]);
            }

            // Verify specification exists
            $specification = $this->quotationSpecificationModel->find($specId);
            if (!$specification) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan'
                ]);
            }

            // Check quotation workflow stage
            $quotation = $this->quotationModel->find($specification['id_quotation']);
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if (!in_array($quotation['workflow_stage'], ['QUOTATION', 'SENT'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Specifications can only be deleted from quotations in QUOTATION or SENT stage. Current stage: ' . $quotation['workflow_stage']
                ]);
            }

            if ($this->quotationSpecificationModel->delete($specId)) {
                // Log activity
                $this->logActivity(
                    'quotation_specification_deleted',
                    'quotation_specifications',
                    $specId,
                    'Deleted quotation specification: ' . ($specification['specification_name'] ?? 'Specification')
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Specification deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Quotation deleteSpecification error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }
    
}