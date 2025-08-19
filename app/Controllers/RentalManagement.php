<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RentalModel;
use App\Models\ForkliftModel;

class RentalManagement extends BaseController
{
    protected $rentalModel;
    protected $forkliftModel;

    public function __construct()
    {
        $this->rentalModel = new RentalModel();
        $this->forkliftModel = new ForkliftModel();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Rental Management',
            'pageTitle' => 'Rental Management',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => '/dashboard'],
                ['label' => 'Rental Management', 'url' => '/rentals']
            ]
        ];
        
        return view('rentals/index', $data);
    }

    public function create(): string
    {
        // Get available forklifts
        $availableForklifts = $this->forkliftModel->getAvailableUnits();
        
        $data = [
            'title' => 'Create Rental',
            'pageTitle' => 'Create New Rental',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => '/dashboard'],
                ['label' => 'Rental Management', 'url' => '/rentals'],
                ['label' => 'Create Rental', 'url' => '/rentals/create']
            ],
            'forklifts' => $availableForklifts
        ];
        
        return view('rentals/create', $data);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'forklift_id' => 'required|numeric|is_not_unique[forklifts.forklift_id]',
            'customer_name' => 'required|max_length[255]',
            'customer_company' => 'required|max_length[255]',
            'customer_email' => 'required|valid_email|max_length[255]',
            'customer_phone' => 'required|max_length[20]',
            'rental_type' => 'required|in_list[daily,weekly,monthly,yearly]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'rental_rate' => 'required|numeric|greater_than[0]',
            'rental_rate_type' => 'required|in_list[daily,weekly,monthly,yearly]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        // Check forklift availability
        $forkliftId = $this->request->getPost('forklift_id');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        if (!$this->rentalModel->checkForkliftAvailability($forkliftId, $startDate, $endDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Forklift is not available for the selected dates'
            ]);
        }

        // Calculate rental duration
        $rentalDuration = $this->calculateRentalDuration($startDate, $endDate, $this->request->getPost('rental_type'));

        $rentalData = [
            'forklift_id' => $forkliftId,
            'customer_name' => $this->request->getPost('customer_name'),
            'customer_company' => $this->request->getPost('customer_company'),
            'customer_email' => $this->request->getPost('customer_email'),
            'customer_phone' => $this->request->getPost('customer_phone'),
            'customer_address' => $this->request->getPost('customer_address'),
            'contact_person' => $this->request->getPost('contact_person'),
            'rental_type' => $this->request->getPost('rental_type'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'rental_duration' => $rentalDuration,
            'rental_rate' => $this->request->getPost('rental_rate'),
            'rental_rate_type' => $this->request->getPost('rental_rate_type'),
            'discount_amount' => $this->request->getPost('discount_amount') ?: 0,
            'tax_amount' => $this->request->getPost('tax_amount') ?: 0,
            'security_deposit' => $this->request->getPost('security_deposit') ?: 0,
            'delivery_required' => $this->request->getPost('delivery_required') === 'on',
            'delivery_address' => $this->request->getPost('delivery_address'),
            'delivery_cost' => $this->request->getPost('delivery_cost') ?: 0,
            'pickup_required' => $this->request->getPost('pickup_required') === 'on',
            'pickup_address' => $this->request->getPost('pickup_address'),
            'pickup_cost' => $this->request->getPost('pickup_cost') ?: 0,
            'operator_required' => $this->request->getPost('operator_required') === 'on',
            'operator_name' => $this->request->getPost('operator_name'),
            'operator_cost' => $this->request->getPost('operator_cost') ?: 0,
            'fuel_included' => $this->request->getPost('fuel_included') === 'on',
            'maintenance_included' => $this->request->getPost('maintenance_included') === 'on',
            'insurance_included' => $this->request->getPost('insurance_included') === 'on',
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'po_number' => $this->request->getPost('po_number'),
            'notes' => $this->request->getPost('notes'),
            'special_terms' => $this->request->getPost('special_terms'),
            'status' => 'draft',
            'contract_status' => 'pending',
            'payment_status' => 'pending',
            'created_by' => session()->get('user_id')
        ];


        if ($this->rentalModel->insert($rentalData)) {
            // Update langsung status unit di inventory_unit menjadi RENTAL (3)
            $forkliftId = $this->request->getPost('forklift_id');
            if ($forkliftId) {
                $inventoryUnitModel = new \App\Models\InventoryUnitModel();
                // Kolom kunci utama inventory_unit kini no_unit, sesuaikan jika berbeda
                $inventoryUnitModel->update($forkliftId, ['status_unit_id' => 3]);
            }
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rental created successfully!',
                'redirect' => '/rentals'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create rental'
        ]);
    }

    public function edit($id = null): string
    {
        $rental = $this->rentalModel->getRentalDetails($id);
        if (!$rental) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Rental not found');
        }

        // Get available forklifts
        $availableForklifts = $this->forkliftModel->getAvailableUnits();
        // Add current forklift to available list
        $currentForklift = $this->forkliftModel->find($rental['forklift_id']);
        if ($currentForklift) {
            $availableForklifts[] = $currentForklift;
        }

        $data = [
            'title' => 'Edit Rental',
            'pageTitle' => 'Edit Rental',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => '/dashboard'],
                ['label' => 'Rental Management', 'url' => '/rentals'],
                ['label' => 'Edit Rental', 'url' => '/rentals/edit/' . $id]
            ],
            'rental' => $rental,
            'forklifts' => $availableForklifts
        ];
        
        return view('rentals/edit', $data);
    }

    public function update($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $rental = $this->rentalModel->find($id);
        if (!$rental) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Rental not found'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'forklift_id' => 'required|numeric|is_not_unique[forklifts.forklift_id]',
            'customer_name' => 'required|max_length[255]',
            'customer_company' => 'required|max_length[255]',
            'customer_email' => 'required|valid_email|max_length[255]',
            'customer_phone' => 'required|max_length[20]',
            'rental_type' => 'required|in_list[daily,weekly,monthly,yearly]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'rental_rate' => 'required|numeric|greater_than[0]',
            'status' => 'required|in_list[draft,confirmed,active,completed,cancelled]',
            'contract_status' => 'required|in_list[pending,signed,expired]',
            'payment_status' => 'required|in_list[pending,partial,paid,overdue]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        // Check forklift availability (excluding current rental)
        $forkliftId = $this->request->getPost('forklift_id');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        if (!$this->rentalModel->checkForkliftAvailability($forkliftId, $startDate, $endDate, $id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Forklift is not available for the selected dates'
            ]);
        }

        // Calculate rental duration
        $rentalDuration = $this->calculateRentalDuration($startDate, $endDate, $this->request->getPost('rental_type'));

        $rentalData = [
            'forklift_id' => $forkliftId,
            'customer_name' => $this->request->getPost('customer_name'),
            'customer_company' => $this->request->getPost('customer_company'),
            'customer_email' => $this->request->getPost('customer_email'),
            'customer_phone' => $this->request->getPost('customer_phone'),
            'customer_address' => $this->request->getPost('customer_address'),
            'contact_person' => $this->request->getPost('contact_person'),
            'rental_type' => $this->request->getPost('rental_type'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'rental_duration' => $rentalDuration,
            'rental_rate' => $this->request->getPost('rental_rate'),
            'rental_rate_type' => $this->request->getPost('rental_rate_type'),
            'discount_amount' => $this->request->getPost('discount_amount') ?: 0,
            'tax_amount' => $this->request->getPost('tax_amount') ?: 0,
            'security_deposit' => $this->request->getPost('security_deposit') ?: 0,
            'delivery_required' => $this->request->getPost('delivery_required') === 'on',
            'delivery_address' => $this->request->getPost('delivery_address'),
            'delivery_cost' => $this->request->getPost('delivery_cost') ?: 0,
            'pickup_required' => $this->request->getPost('pickup_required') === 'on',
            'pickup_address' => $this->request->getPost('pickup_address'),
            'pickup_cost' => $this->request->getPost('pickup_cost') ?: 0,
            'operator_required' => $this->request->getPost('operator_required') === 'on',
            'operator_name' => $this->request->getPost('operator_name'),
            'operator_cost' => $this->request->getPost('operator_cost') ?: 0,
            'fuel_included' => $this->request->getPost('fuel_included') === 'on',
            'maintenance_included' => $this->request->getPost('maintenance_included') === 'on',
            'insurance_included' => $this->request->getPost('insurance_included') === 'on',
            'status' => $this->request->getPost('status'),
            'contract_status' => $this->request->getPost('contract_status'),
            'payment_status' => $this->request->getPost('payment_status'),
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'po_number' => $this->request->getPost('po_number'),
            'notes' => $this->request->getPost('notes'),
            'special_terms' => $this->request->getPost('special_terms'),
            'updated_by' => session()->get('user_id')
        ];

        if ($this->rentalModel->update($id, $rentalData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rental updated successfully!',
                'redirect' => '/rentals'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update rental'
        ]);
    }

    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $rental = $this->rentalModel->find($id);
        if (!$rental) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Rental not found'
            ]);
        }

        // Check if rental can be deleted
        if (in_array($rental['status'], ['active', 'completed'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete active or completed rental'
            ]);
        }

        if ($this->rentalModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rental deleted successfully!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete rental'
        ]);
    }

    public function getRentalList()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $searchValue = $this->request->getPost('search')['value'] ?? '';
        $orderColumn = $this->request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';

        $columns = ['rental_number', 'customer_name', 'customer_company', 'unit_code', 'start_date', 'end_date', 'status', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';

        // Get filters
        $statusFilter = $this->request->getPost('status_filter');
        $contractFilter = $this->request->getPost('contract_filter');
        $paymentFilter = $this->request->getPost('payment_filter');
        $typeFilter = $this->request->getPost('type_filter');

    $db = \Config\Database::connect();
    $builder = $db->table('rentals r');
        $builder->select('r.*, f.unit_code, f.unit_name, f.brand, f.model');
        $builder->join('forklifts f', 'r.forklift_id = f.forklift_id');
        $builder->where('r.deleted_at IS NULL');

        // Apply filters
        if ($statusFilter) {
            $builder->where('r.status', $statusFilter);
        }
        if ($contractFilter) {
            $builder->where('r.contract_status', $contractFilter);
        }
        if ($paymentFilter) {
            $builder->where('r.payment_status', $paymentFilter);
        }
        if ($typeFilter) {
            $builder->where('r.rental_type', $typeFilter);
        }

        // Search
        if ($searchValue) {
            $builder->groupStart();
            $builder->like('r.rental_number', $searchValue);
            $builder->orLike('r.customer_name', $searchValue);
            $builder->orLike('r.customer_company', $searchValue);
            $builder->orLike('f.unit_code', $searchValue);
            $builder->orLike('f.unit_name', $searchValue);
            $builder->groupEnd();
        }

        $recordsTotal = $this->rentalModel->countAll();
        $recordsFiltered = $builder->countAllResults(false);

        $builder->orderBy($orderBy, $orderDir);
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $rentals = $builder->get()->getResultArray();

        $data = [];
        foreach ($rentals as $rental) {
            $statusBadge = $this->getStatusBadge($rental['status']);
            $contractBadge = $this->getContractStatusBadge($rental['contract_status']);
            $paymentBadge = $this->getPaymentStatusBadge($rental['payment_status']);
            
            $actions = '<div class="btn-group btn-group-sm">
                <button class="btn btn-outline-info btn-sm" onclick="viewRental(' . $rental['rental_id'] . ')" title="View">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="editRental(' . $rental['rental_id'] . ')" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>';
            
            if (!in_array($rental['status'], ['active', 'completed'])) {
                $actions .= '<button class="btn btn-outline-danger btn-sm" onclick="deleteRental(' . $rental['rental_id'] . ')" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>';
            }
            
            $actions .= '</div>';

            $data[] = [
                'rental_id' => $rental['rental_id'],
                'rental_number' => '<strong>' . esc($rental['rental_number']) . '</strong>',
                'customer_info' => esc($rental['customer_name']) . '<br><small class="text-muted">' . esc($rental['customer_company']) . '</small>',
                'forklift_info' => esc($rental['unit_code']) . '<br><small class="text-muted">' . esc($rental['brand']) . ' ' . esc($rental['model']) . '</small>',
                'rental_period' => date('d/m/Y', strtotime($rental['start_date'])) . '<br><small class="text-muted">to ' . date('d/m/Y', strtotime($rental['end_date'])) . '</small>',
                'amount' => 'Rp ' . number_format($rental['final_amount'] ?? 0, 0, ',', '.'),
                'status' => $statusBadge,
                'contract_status' => $contractBadge,
                'payment_status' => $paymentBadge,
                'created_at' => date('d/m/Y H:i', strtotime($rental['created_at'])),
                'actions' => $actions
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getPost('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function getRental($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $rental = $this->rentalModel->getRentalDetails($id);
        if (!$rental) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Rental not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $rental
        ]);
    }

    public function getRentalStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $stats = $this->rentalModel->getStatistics();

        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function updateStatus($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $rental = $this->rentalModel->find($id);
        if (!$rental) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Rental not found'
            ]);
        }

        $status = $this->request->getPost('status');
        $userId = session()->get('user_id');

        if (!in_array($status, ['draft', 'confirmed', 'active', 'completed', 'cancelled'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid status'
            ]);
        }

        if ($this->rentalModel->updateRentalStatus($id, $status, $userId)) {
            // Update forklift status based on rental status
            $forkliftStatus = $this->getForkliftStatusFromRental($status);
            if ($forkliftStatus) {
                $this->forkliftModel->updateStatus($rental['forklift_id'], $forkliftStatus);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rental status updated successfully!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update rental status'
        ]);
    }

    public function getAvailableForklifts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $excludeRentalId = $this->request->getPost('exclude_rental_id');

        $availableForklifts = [];
        $allForklifts = $this->forkliftModel->getAvailableUnits();

        foreach ($allForklifts as $forklift) {
            if ($this->rentalModel->checkForkliftAvailability($forklift['forklift_id'], $startDate, $endDate, $excludeRentalId)) {
                $availableForklifts[] = $forklift;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $availableForklifts
        ]);
    }

    public function calculateRentalAmount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $rate = floatval($this->request->getPost('rental_rate'));
        $duration = intval($this->request->getPost('rental_duration'));
        $discount = floatval($this->request->getPost('discount_amount')) ?: 0;
        $taxPercent = floatval($this->request->getPost('tax_percent')) ?: 10;
        $deliveryCost = floatval($this->request->getPost('delivery_cost')) ?: 0;
        $pickupCost = floatval($this->request->getPost('pickup_cost')) ?: 0;
        $operatorCost = floatval($this->request->getPost('operator_cost')) ?: 0;

        $subtotal = $rate * $duration;
        $discountAmount = min($discount, $subtotal);
        $taxableAmount = $subtotal - $discountAmount;
        $taxAmount = $taxableAmount * ($taxPercent / 100);
        $finalAmount = $taxableAmount + $taxAmount + $deliveryCost + $pickupCost + $operatorCost;

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_additional' => $deliveryCost + $pickupCost + $operatorCost,
                'final_amount' => $finalAmount
            ]
        ]);
    }

    private function calculateRentalDuration($startDate, $endDate, $rentalType)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $diff = $start->diff($end);
        $days = $diff->days;

        switch ($rentalType) {
            case 'daily':
                return $days;
            case 'weekly':
                return ceil($days / 7);
            case 'monthly':
                return ceil($days / 30);
            case 'yearly':
                return ceil($days / 365);
            default:
                return $days;
        }
    }

    private function getForkliftStatusFromRental($rentalStatus)
    {
        switch ($rentalStatus) {
            case 'confirmed':
            case 'active':
                return 'rented';
            case 'completed':
            case 'cancelled':
                return 'available';
            default:
                return null;
        }
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'confirmed' => '<span class="badge bg-primary">Confirmed</span>',
            'active' => '<span class="badge bg-success">Active</span>',
            'completed' => '<span class="badge bg-info">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>'
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    private function getContractStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'signed' => '<span class="badge bg-success">Signed</span>',
            'expired' => '<span class="badge bg-danger">Expired</span>'
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    private function getPaymentStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'partial' => '<span class="badge bg-info">Partial</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'overdue' => '<span class="badge bg-danger">Overdue</span>'
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
} 