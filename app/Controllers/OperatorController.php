<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OperatorModel;
use App\Traits\ActivityLoggingTrait;
use App\Traits\DateFilterTrait;

/**
 * Operator Management Controller
 * 
 * Manages operator/driver master data for rental operations
 * 
 * @package App\Controllers
 * @category Marketing
 */
class OperatorController extends BaseController
{
    use ActivityLoggingTrait;
    use DateFilterTrait;
    
    protected $db;
    protected $operatorModel;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->operatorModel = new OperatorModel();
        
        // Initialize activity logger
        if (method_exists($this, 'initializeActivityLogging')) {
            $this->initializeActivityLogging();
        }
    }
    
    /**
     * Display operators management page
     */
    public function index()
    {
        if (!$this->hasPermission('marketing.operator.view')) {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }
        
        $data = [
            'title' => 'Operator Management - Marketing',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing/operators' => 'Operators'
            ],
            'totalOperators' => $this->operatorModel->countAllResults(false),
            'availableOperators' => $this->operatorModel->where('status', 'AVAILABLE')->countAllResults(false),
            'assignedOperators' => $this->operatorModel->where('status', 'ASSIGNED')->countAllResults(false),
            'loadDataTables' => true,
        ];
        
        return view('marketing/operators', $data);
    }
    
    /**
     * Get operators data for DataTables (AJAX)
     */
    public function getOperators()
    {
        try {
            if (!$this->hasPermission('marketing.operator.view')) {
                return $this->response->setJSON([
                    'draw' => 1,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ])->setStatusCode(403);
            }
            
            $request = $this->request;
            $draw = $request->getPost('draw') ?: 1;
            $start = $request->getPost('start') ?: 0;
            $length = $request->getPost('length') ?: 10;
            
            $search = $request->getPost('search') ?: [];
            $searchValue = isset($search['value']) ? $search['value'] : '';
            
            $order = $request->getPost('order') ?: [['column' => 0, 'dir' => 'asc']];
            $orderColumnIndex = isset($order[0]['column']) ? $order[0]['column'] : 0;
            $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
            
            // Define columns for ordering
            $columns = [
                'operator_code',
                'operator_name',
                'certification_level',
                'monthly_rate',
                'status',
                'created_at'
            ];
            $orderColumn = $columns[$orderColumnIndex] ?? 'operator_code';
            
            // Get total records
            $totalRecords = $this->operatorModel->countAllResults(false);
            
            // Build query with search
            $builder = $this->operatorModel->builder();
            $builder->select('operators.*');
            
            if (!empty($searchValue)) {
                $builder->groupStart()
                        ->like('operator_code', $searchValue)
                        ->orLike('operator_name', $searchValue)
                        ->orLike('phone', $searchValue)
                        ->orLike('certification_level', $searchValue)
                        ->groupEnd();
            }
            
            // Get filtered count
            $filteredRecords = empty($searchValue) ? $totalRecords : count($builder->get()->getResultArray());
            
            // Rebuild query for actual data (needed because get() consumes the builder)
            $builder = $this->operatorModel->builder();
            $builder->select('operators.*');
            
            if (!empty($searchValue)) {
                $builder->groupStart()
                        ->like('operator_code', $searchValue)
                        ->orLike('operator_name', $searchValue)
                        ->orLike('phone', $searchValue)
                        ->orLike('certification_level', $searchValue)
                        ->groupEnd();
            }
            
            // Apply ordering and pagination
            $builder->orderBy($orderColumn, $orderDir)
                    ->limit($length, $start);
            
            $operators = $builder->get()->getResultArray();
            
            // Format data for DataTables
            foreach ($operators as &$operator) {
                // Format monthly rate
                $operator['monthly_rate_formatted'] = 'Rp ' . number_format($operator['monthly_rate'], 0, ',', '.');
                
                // Status badge
                $statusClass = '';
                switch($operator['status']) {
                    case 'AVAILABLE':
                        $statusClass = 'success';
                        break;
                    case 'ASSIGNED':
                        $statusClass = 'primary';
                        break;
                    case 'ON_LEAVE':
                        $statusClass = 'warning';
                        break;
                    case 'INACTIVE':
                        $statusClass = 'secondary';
                        break;
                }
                $operator['status_badge'] = '<span class="badge badge-' . $statusClass . '">' . $operator['status'] . '</span>';
                
                // Certification badge
                $certClass = '';
                switch($operator['certification_level']) {
                    case 'EXPERT':
                        $certClass = 'danger';
                        break;
                    case 'ADVANCED':
                        $certClass = 'warning';
                        break;
                    case 'INTERMEDIATE':
                        $certClass = 'info';
                        break;
                    case 'BASIC':
                        $certClass = 'secondary';
                        break;
                }
                $operator['cert_badge'] = '<span class="badge badge-' . $certClass . '">' . $operator['certification_level'] . '</span>';
                
                // Check certification expiry
                if ($operator['certification_expiry']) {
                    $expiry = strtotime($operator['certification_expiry']);
                    $daysUntilExpiry = floor(($expiry - time()) / 86400);
                    
                    if ($daysUntilExpiry < 0) {
                        $operator['cert_status'] = '<span class="text-danger"><i class="fas fa-times-circle"></i> Expired</span>';
                    } elseif ($daysUntilExpiry <= 30) {
                        $operator['cert_status'] = '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Expiring soon (' . $daysUntilExpiry . ' days)</span>';
                    } else {
                        $operator['cert_status'] = '<span class="text-success"><i class="fas fa-check-circle"></i> Valid</span>';
                    }
                } else {
                    $operator['cert_status'] = '<span class="text-muted">-</span>';
                }
                
                // Action buttons
                $canEdit = $this->hasPermission('marketing.operator.edit');
                $canDelete = $this->hasPermission('marketing.operator.delete');
                
                $actions = '<div class="btn-group btn-group-sm">';
                $actions .= '<button class="btn btn-info btn-view" data-id="' . $operator['id'] . '" title="View"><i class="fas fa-eye"></i></button>';
                
                if ($canEdit) {
                    $actions .= '<button class="btn btn-warning btn-edit" data-id="' . $operator['id'] . '" title="Edit"><i class="fas fa-edit"></i></button>';
                }
                
                if ($canDelete && $operator['status'] !== 'ASSIGNED') {
                    $actions .= '<button class="btn btn-danger btn-delete" data-id="' . $operator['id'] . '" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                $operator['actions'] = $actions;
            }
            
            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $operators
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getOperators: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Get single operator details (AJAX)
     */
    public function getOperator($id)
    {
        try {
            if (!$this->hasPermission('marketing.operator.view')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(403);
            }
            
            $operator = $this->operatorModel->find($id);
            
            if (!$operator) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Operator not found'
                ])->setStatusCode(404);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $operator
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getOperator: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving operator: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Create new operator (AJAX)
     */
    public function create()
    {
        try {
            if (!$this->hasPermission('marketing.operator.create')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(403);
            }
            
            $rules = [
                'operator_name' => 'required|min_length[3]|max_length[255]',
                'phone' => 'permit_empty|max_length[50]',
                'email' => 'permit_empty|valid_email',
                'certification_level' => 'required|in_list[BASIC,INTERMEDIATE,ADVANCED,EXPERT]',
                'monthly_rate' => 'required|decimal',
                'daily_rate' => 'permit_empty|decimal',
                'hourly_rate' => 'permit_empty|decimal',
            ];
            
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(400);
            }
            
            // Generate operator code
            $operatorCode = $this->operatorModel->generateOperatorCode();
            
            $data = [
                'operator_code' => $operatorCode,
                'operator_name' => $this->request->getPost('operator_name'),
                'nik' => $this->request->getPost('nik'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'certification_level' => $this->request->getPost('certification_level'),
                'certification_number' => $this->request->getPost('certification_number'),
                'certification_issued_date' => $this->request->getPost('certification_issued_date') ?: null,
                'certification_expiry' => $this->request->getPost('certification_expiry') ?: null,
                'certification_issuer' => $this->request->getPost('certification_issuer'),
                'monthly_rate' => $this->request->getPost('monthly_rate') ?: 0,
                'daily_rate' => $this->request->getPost('daily_rate') ?: 0,
                'hourly_rate' => $this->request->getPost('hourly_rate') ?: 0,
                'emergency_contact' => $this->request->getPost('emergency_contact'),
                'emergency_phone' => $this->request->getPost('emergency_phone'),
                'address' => $this->request->getPost('address'),
                'notes' => $this->request->getPost('notes'),
                'status' => 'AVAILABLE',
            ];
            
            $operatorId = $this->operatorModel->insert($data);
            
            if (!$operatorId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create operator',
                    'errors' => $this->operatorModel->errors()
                ])->setStatusCode(500);
            }
            
            // Log activity
            $this->logActivity('CREATE', 'operators', $operatorId, 
                "Created new operator: {$data['operator_name']} ({$operatorCode})", 
                ['data' => $data]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Operator created successfully',
                'data' => ['id' => $operatorId, 'operator_code' => $operatorCode]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in create operator: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating operator: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Update existing operator (AJAX)
     */
    public function update($id)
    {
        try {
            if (!$this->hasPermission('marketing.operator.edit')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(403);
            }
            
            $operator = $this->operatorModel->find($id);
            if (!$operator) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Operator not found'
                ])->setStatusCode(404);
            }
            
            $rules = [
                'operator_name' => 'required|min_length[3]|max_length[255]',
                'phone' => 'permit_empty|max_length[50]',
                'email' => 'permit_empty|valid_email',
                'certification_level' => 'required|in_list[BASIC,INTERMEDIATE,ADVANCED,EXPERT]',
                'monthly_rate' => 'required|decimal',
                'daily_rate' => 'permit_empty|decimal',
                'hourly_rate' => 'permit_empty|decimal',
                'status' => 'required|in_list[AVAILABLE,ASSIGNED,ON_LEAVE,INACTIVE]'
            ];
            
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(400);
            }
            
            $data = [
                'operator_name' => $this->request->getPost('operator_name'),
                'nik' => $this->request->getPost('nik'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'certification_level' => $this->request->getPost('certification_level'),
                'certification_number' => $this->request->getPost('certification_number'),
                'certification_issued_date' => $this->request->getPost('certification_issued_date') ?: null,
                'certification_expiry' => $this->request->getPost('certification_expiry') ?: null,
                'certification_issuer' => $this->request->getPost('certification_issuer'),
                'monthly_rate' => $this->request->getPost('monthly_rate') ?: 0,
                'daily_rate' => $this->request->getPost('daily_rate') ?: 0,
                'hourly_rate' => $this->request->getPost('hourly_rate') ?: 0,
                'emergency_contact' => $this->request->getPost('emergency_contact'),
                'emergency_phone' => $this->request->getPost('emergency_phone'),
                'address' => $this->request->getPost('address'),
                'notes' => $this->request->getPost('notes'),
                'status' => $this->request->getPost('status'),
            ];
            
            $success = $this->operatorModel->update($id, $data);
            
            if (!$success) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update operator',
                    'errors' => $this->operatorModel->errors()
                ])->setStatusCode(500);
            }
            
            // Log activity
            $this->logActivity('UPDATE', 'operators', $id,
                "Updated operator: {$data['operator_name']} ({$operator['operator_code']})",
                ['before' => $operator, 'after' => $data]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Operator updated successfully'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in update operator: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating operator: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Delete operator (soft delete)
     */
    public function delete($id)
    {
        try {
            if (!$this->hasPermission('marketing.operator.delete')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(403);
            }
            
            $operator = $this->operatorModel->find($id);
            if (!$operator) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Operator not found'
                ])->setStatusCode(404);
            }
            
            // Check if operator is currently assigned
            if ($operator['status'] === 'ASSIGNED') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete operator that is currently assigned to a contract'
                ])->setStatusCode(400);
            }
            
            // Soft delete
            $success = $this->operatorModel->delete($id);
            
            if (!$success) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete operator'
                ])->setStatusCode(500);
            }
            
            // Log activity
            $this->logActivity('DELETE', 'operators', $id,
                "Deleted operator: {$operator['operator_name']} ({$operator['operator_code']})",
                ['operator' => $operator]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Operator deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in delete operator: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting operator: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Get available operators for assignment dropdown
     */
    public function getAvailableOperators()
    {
        try {
            if (!$this->hasPermission('marketing.operator.view')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(403);
            }
            
            $startDate = $this->request->getGet('start_date');
            
            if ($startDate) {
                // Check availability for specific date
                $operators = $this->operatorModel->getAvailableOperators($startDate);
            } else {
                // Get all AVAILABLE status operators
                $operators = $this->operatorModel->where('status', 'AVAILABLE')
                                                 ->orderBy('certification_level', 'DESC')
                                                 ->orderBy('operator_name', 'ASC')
                                                 ->findAll();
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $operators
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getAvailableOperators: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving available operators: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Get operators with expiring certifications
     */
    public function getExpiringCertifications()
    {
        try {
            if (!$this->hasPermission('marketing.operator.view')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(403);
            }
            
            $daysThreshold = $this->request->getGet('days') ?: 30;
            $operators = $this->operatorModel->getExpiringCertifications($daysThreshold);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $operators,
                'threshold' => $daysThreshold
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getExpiringCertifications: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving expiring certifications: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Get operator statistics
     */
    public function getStats()
    {
        try {
            if (!$this->hasPermission('marketing.operator.view')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(403);
            }
            
            $stats = [
                'total' => $this->operatorModel->countAllResults(false),
                'available' => $this->operatorModel->where('status', 'AVAILABLE')->countAllResults(false),
                'assigned' => $this->operatorModel->where('status', 'ASSIGNED')->countAllResults(false),
                'on_leave' => $this->operatorModel->where('status', 'ON_LEAVE')->countAllResults(false),
                'inactive' => $this->operatorModel->where('status', 'INACTIVE')->countAllResults(false),
                'by_certification' => [
                    'expert' => $this->operatorModel->where('certification_level', 'EXPERT')->countAllResults(false),
                    'advanced' => $this->operatorModel->where('certification_level', 'ADVANCED')->countAllResults(false),
                    'intermediate' => $this->operatorModel->where('certification_level', 'INTERMEDIATE')->countAllResults(false),
                    'basic' => $this->operatorModel->where('certification_level', 'BASIC')->countAllResults(false),
                ],
                'expiring_soon' => count($this->operatorModel->getExpiringCertifications(30))
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getStats: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
