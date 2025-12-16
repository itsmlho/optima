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
            log_message('error', 'Error in Quotation::getDataTable: ' . $e->getMessage());
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
            log_message('error', 'Error in Quotation::getStats: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading statistics'
            ]);
        }
    }

    /**
     * Generate new quotation number
     */
    public function generateNumber()
    {
        try {
            $number = $this->quotationModel->generateQuotationNumber();
            return $this->response->setJSON([
                'success' => true,
                'number' => $number
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error generating quotation number: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error generating quotation number'
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
                    'message' => 'Validation failed',
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
                    'message' => 'Failed to create quotation'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error creating quotation: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get quotation details
     */
    public function get($id)
    {
        try {
            $quotation = $this->quotationModel->getQuotationWithSpecs($id);
            
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation not found'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $quotation
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting quotation: ' . $e->getMessage());
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
                    'message' => 'Quotation not found'
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
                    'message' => 'Validation failed',
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
                    'message' => 'Failed to update quotation'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error updating quotation: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
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
                    'message' => 'Quotation not found'
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
                    'message' => 'Failed to delete quotation'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error deleting quotation: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
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
                    'message' => 'Failed to update quotation stage'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error updating quotation stage: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
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
                    'message' => 'Failed to mark quotation as deal'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error marking quotation as deal: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
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
            log_message('error', 'Error getting quotation statistics: ' . $e->getMessage());
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
                    'message' => 'Quotation not found'
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
            log_message('error', 'Error getting quotation detail: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load quotation: ' . $e->getMessage()
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
                    'message' => 'Quotation not found'
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
            log_message('error', 'Error getting quotation specifications: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load specifications: ' . $e->getMessage()
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
                    'message' => 'Quotation not found'
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
            $data = [
                'id_quotation' => $quotationId,
                'specification_name' => $this->request->getPost('specification_name'),
                'specification_type' => $this->request->getPost('specification_type') ?: 'UNIT',
                'quantity' => (int)$this->request->getPost('quantity'),
                'monthly_price' => (float)$this->request->getPost('unit_price'),
                'daily_price' => (float)$this->request->getPost('harga_per_unit_harian'),
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
                'is_active' => 1
            ];
            
            // Handle accessories array - store in dedicated column
            $aksesoris = $this->request->getPost('aksesoris');
            if ($aksesoris && is_array($aksesoris)) {
                $data['unit_accessories'] = implode(', ', $aksesoris);
            } else {
                $data['unit_accessories'] = '';
            }
            
            // Calculate total price with NEW formula: (quantity * monthly_price) + daily_price
            $data['total_price'] = ($data['quantity'] * $data['monthly_price']) + $data['daily_price'];

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
                    'message' => 'Failed to add specification'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error adding quotation specification: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add specification: ' . $e->getMessage()
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

            $data = $this->request->getPost();
            
            // Verify specification exists
            $specification = $this->quotationSpecificationModel->find($specId);
            if (!$specification) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Specification not found'
                ]);
            }

            // Check quotation workflow stage
            $quotation = $this->quotationModel->find($specification['id_quotation']);
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation not found'
                ]);
            }

            if (!in_array($quotation['workflow_stage'], ['QUOTATION', 'SENT'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Specifications can only be modified for quotations in QUOTATION or SENT stage. Current stage: ' . $quotation['workflow_stage']
                ]);
            }

            // Rename old field names to new ones if sent from frontend
            if (isset($data['unit_price'])) {
                $data['monthly_price'] = $data['unit_price'];
                unset($data['unit_price']);
            }
            if (isset($data['harga_per_unit_harian'])) {
                $data['daily_price'] = $data['harga_per_unit_harian'];
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

            // Calculate total price with NEW formula: (quantity * monthly_price) + daily_price
            if (isset($data['quantity']) || isset($data['monthly_price']) || isset($data['daily_price'])) {
                $qty = $data['quantity'] ?? $specification['quantity'];
                $monthlyPrice = $data['monthly_price'] ?? $specification['monthly_price'];
                $dailyPrice = $data['daily_price'] ?? $specification['daily_price'];
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

            if ($this->quotationSpecificationModel->update($specId, $data)) {
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
                    'message' => 'Failed to update specification'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error updating quotation specification: ' . $e->getMessage());
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
                    'message' => 'Specification not found'
                ]);
            }

            // Check quotation workflow stage
            $quotation = $this->quotationModel->find($specification['id_quotation']);
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation not found'
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
                    'message' => 'Failed to delete specification'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error deleting quotation specification: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete specification'
            ]);
        }
    }

    // ============= DROPDOWN DATA ENDPOINTS =============

    /**
     * Get departments for dropdown
     */
    public function getDepartments()
    {
        try {
            $departments = $this->db->table('departemen')
                ->select('id_departemen, nama_departemen')
                ->where('is_active', 1)
                ->orderBy('nama_departemen')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $departments
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting departments: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load departments'
            ]);
        }
    }

    /**
     * Get unit types for dropdown
     */
    public function getUnitTypes()
    {
        try {
            $unitTypes = $this->db->table('tipe_unit tu')
                ->select('tu.id_tipe_unit, tu.nama_tipe_unit, tu.tipe, tu.jenis, d.nama_departemen')
                ->join('departemen d', 'tu.departemen_id = d.id_departemen', 'left')
                ->where('tu.is_active', 1)
                ->orderBy('tu.nama_tipe_unit')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $unitTypes
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting unit types: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load unit types'
            ]);
        }
    }

    /**
     * Get capacities for dropdown
     */
    public function getCapacities()
    {
        try {
            $capacities = $this->db->table('kapasitas')
                ->select('id_kapasitas, kapasitas, satuan')
                ->where('is_active', 1)
                ->orderBy('kapasitas')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $capacities
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting capacities: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load capacities'
            ]);
        }
    }

    /**
     * Get chargers for dropdown
     */
    public function getChargers()
    {
        try {
            $chargers = $this->db->table('charger')
                ->select('id_charger, merk_charger, tipe_charger')
                ->orderBy('merk_charger, tipe_charger')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $chargers
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting chargers: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load chargers'
            ]);
        }
    }

    /**
     * Get unit brands for dropdown
     */
    public function getUnitBrands()
    {
        try {
            $brands = $this->db->table('merk_unit')
                ->select('merk_unit')
                ->distinct()
                ->orderBy('merk_unit')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $brands
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting unit brands: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load unit brands'
            ]);
        }
    }

    /**
     * Get batteries for dropdown
     */
    public function getBatteries()
    {
        try {
            $batteries = $this->db->table('jenis_baterai')
                ->select('jenis_baterai')
                ->distinct()
                ->orderBy('jenis_baterai')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $batteries
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting batteries: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load batteries'
            ]);
        }
    }

    /**
     * Get attachment types for dropdown
     */
    public function getAttachmentTypes()
    {
        try {
            $attachments = $this->db->table('attachment_tipe')
                ->select('attachment_tipe')
                ->distinct()
                ->orderBy('attachment_tipe')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $attachments
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting attachment types: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load attachment types'
            ]);
        }
    }

    /**
     * Get valves for dropdown
     */
    public function getValves()
    {
        try {
            $valves = $this->db->table('valve')
                ->select('id_valve, nama_valve')
                ->orderBy('nama_valve')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $valves
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting valves: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load valves'
            ]);
        }
    }

    /**
     * Get masts for dropdown
     */
    public function getMasts()
    {
        try {
            $masts = $this->db->table('mast')
                ->select('id_mast, nama_mast')
                ->orderBy('nama_mast')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $masts
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting masts: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load masts'
            ]);
        }
    }

    /**
     * Get tires for dropdown
     */
    public function getTires()
    {
        try {
            $tires = $this->db->table('ban')
                ->select('id_ban, nama_ban')
                ->orderBy('nama_ban')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $tires
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting tires: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load tires'
            ]);
        }
    }

    /**
     * Get wheels for dropdown
     */
    public function getWheels()
    {
        try {
            $wheels = $this->db->table('roda')
                ->select('id_roda, nama_roda')
                ->orderBy('nama_roda')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $wheels
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting wheels: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load wheels'
            ]);
        }
    }
    
}