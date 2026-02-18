<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\InvoiceModel;
use App\Models\InvoiceItemModel;
use App\Models\RecurringBillingScheduleModel;
use App\Models\DeliveryInstructionModel;
use App\Models\KontrakModel;
use App\Models\ContractAmendmentModel;

/**
 * Finance Controller
 * 
 * Manages invoice generation, approval, and recurring billing for rental contracts
 * Implements strict validation: invoices cannot be created without contract linkage
 */
class Finance extends Controller
{
    protected $invoiceModel;
    protected $invoiceItemModel;
    protected $scheduleModel;
    protected $diModel;
    protected $kontrakModel;
    protected $amendmentModel;
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->invoiceModel = new InvoiceModel();
        $this->invoiceItemModel = new InvoiceItemModel();
        $this->scheduleModel = new RecurringBillingScheduleModel();
        $this->diModel = new DeliveryInstructionModel();
        $this->kontrakModel = new KontrakModel();
        $this->amendmentModel = new ContractAmendmentModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Finance dashboard with alerts and KPIs
     */
    public function index()
    {
        $data = [
            'title' => 'Finance Dashboard',
            'unlinked_deliveries' => $this->diModel->getUnlinkedDeliveries(),
            'upcoming_invoices' => $this->scheduleModel->getUpcomingInvoices(7), // 7 days ahead
            'overdue_invoices' => $this->invoiceModel->where('status', 'OVERDUE')->findAll(),
            'draft_invoices' => $this->invoiceModel->where('status', 'DRAFT')->findAll(),
        ];

        return view('finance/index', $data); // Use index view not dashboard
    }

    /**
     * Invoice management view (DataTables)
     */
    public function invoices()
    {
        $data = [
            'title' => 'Invoice Management'
        ];

        return view('finance/invoices', $data);
    }

    /**
     * Get invoices for DataTables
     */
    public function getInvoiceDataTable()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $builder = $this->db->table('invoices i');
            $builder->select('i.*, k.no_kontrak, c.customer_name, cl.location_name');
            $builder->join('kontrak k', 'k.id = i.contract_id', 'left');
            $builder->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left');
            $builder->join('customers c', 'c.id = cl.customer_id', 'left');

            // Search
            $search = $this->request->getGet('search')['value'] ?? '';
            if (!empty($search)) {
                $builder->groupStart()
                    ->like('i.invoice_number', $search)
                    ->orLike('k.no_kontrak', $search)
                    ->orLike('c.customer_name', $search)
                    ->groupEnd();
            }

            // Count
            $totalRecords = $builder->countAllResults(false);

            // Order
            $orderColumn = $this->request->getGet('order')[0]['column'] ?? 0;
            $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'desc';
            $columns = ['i.invoice_number', 'i.invoice_date', 'k.no_kontrak', 'c.customer_name', 'i.total_amount', 'i.status'];
            $builder->orderBy($columns[$orderColumn] ?? 'i.id', $orderDir);

            // Pagination
            $start = $this->request->getGet('start') ?? 0;
            $length = $this->request->getGet('length') ?? 10;
            $builder->limit($length, $start);

            $invoices = $builder->get()->getResultArray();

            return $this->response->setJSON([
                'draw' => intval($this->request->getGet('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $invoices
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::getInvoiceDataTable - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate invoice from Delivery Instruction (one-time billing)
     * CRITICAL: Three-layer validation prevents invoice creation without contract
     */
    public function generateInvoiceFromDI()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $diId = $this->request->getPost('di_id');
            $contractId = $this->request->getPost('contract_id');
            $invoiceDate = $this->request->getPost('invoice_date') ?? date('Y-m-d');
            $dueDate = $this->request->getPost('due_date');
            $notes = $this->request->getPost('notes');

            if (empty($diId) || empty($contractId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'DI ID and Contract ID required'
                ]);
            }

            // VALIDATION LAYER 1: Model-level validation
            $validationErrors = $this->diModel->validateBillingReadiness($diId);
            
            if (!empty($validationErrors)) {
                return $this->response->setJSON([
                    'success' => false,
                    'locked' => true,
                    'message' => 'Invoice cannot be created: Missing requirements',
                    'errors' => $validationErrors
                ]);
            }

            $userId = session()->get('user_id') ?? 1;

            // Create invoice with options
            $options = [
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'notes' => $notes
            ];

            $result = $this->invoiceModel->createFromDI($diId, $contractId, $userId, $options);

            if (isset($result['locked']) && $result['locked'] === true) {
                // Invoice creation blocked by validation
                return $this->response->setJSON([
                    'success' => false,
                    'locked' => true,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ]);
            }

            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Invoice created successfully',
                    'invoice_id' => $result['invoice_id'],
                    'invoice_number' => $result['invoice_number']
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::generateInvoiceFromDI - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to generate invoice: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate recurring invoice (monthly/quarterly/yearly)
     * Called by cron job or manual trigger
     */
    public function generateRecurringInvoice()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $scheduleId = $this->request->getPost('schedule_id');
            $userId = session()->get('user_id') ?? 1;

            if (empty($scheduleId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Schedule ID required'
                ]);
            }

            $result = $this->invoiceModel->createRecurringInvoice($scheduleId, $userId);

            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Recurring invoice created successfully',
                    'invoice_id' => $result['invoice_id'],
                    'invoice_number' => $result['invoice_number']
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::generateRecurringInvoice - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to generate recurring invoice: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batch generate all due recurring invoices
     * Typically called by cron job
     */
    public function batchGenerateRecurringInvoices()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $userId = session()->get('user_id') ?? 1;
            $result = $this->scheduleModel->generateDueInvoices();

            return $this->response->setJSON([
                'success' => true,
                'message' => "Generated {$result['count']} invoices",
                'invoices' => $result['invoices'],
                'errors' => $result['errors']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::batchGenerateRecurringInvoices - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to batch generate invoices: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Approve invoice (move from DRAFT to APPROVED)
     */
    public function approveInvoice($invoiceId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $userId = session()->get('user_id') ?? 1;
            
            $invoice = $this->invoiceModel->find($invoiceId);
            if (!$invoice) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invoice not found'
                ]);
            }

            if ($invoice['status'] !== 'DRAFT') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only DRAFT invoices can be approved'
                ]);
            }

            $updated = $this->invoiceModel->updateStatus($invoiceId, 'APPROVED', $userId);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Invoice approved successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to approve invoice'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::approveInvoice - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to approve invoice: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($invoiceId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $userId = session()->get('user_id') ?? 1;
            $paymentDate = $this->request->getPost('payment_date') ?? date('Y-m-d');
            $paymentMethod = $this->request->getPost('payment_method');
            
            $invoice = $this->invoiceModel->find($invoiceId);
            if (!$invoice) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invoice not found'
                ]);
            }

            // Update invoice status
            $updated = $this->invoiceModel->update($invoiceId, [
                'status' => 'PAID',
                'paid_date' => $paymentDate,
                'payment_method' => $paymentMethod
            ]);

            if ($updated) {
                // Log status change
                $this->invoiceModel->updateStatus($invoiceId, 'PAID', $userId);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Invoice marked as paid successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark invoice as paid'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::markAsPaid - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark invoice as paid: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * View invoice details
     */
    public function viewInvoice($invoiceId)
    {
        try {
            $invoice = $this->invoiceModel->getInvoiceDetails($invoiceId);
            
            if (!$invoice) {
                return redirect()->back()->with('error', 'Invoice not found');
            }

            $items = $this->invoiceItemModel->getItemsByInvoice($invoiceId);
            $history = $this->db->table('invoice_status_history')
                ->select('invoice_status_history.*, users.nama as changed_by_name')
                ->join('users', 'users.id = invoice_status_history.changed_by', 'left')
                ->where('invoice_status_history.invoice_id', $invoiceId)
                ->orderBy('invoice_status_history.changed_at', 'DESC')
                ->get()
                ->getResultArray();

            $data = [
                'title' => 'Invoice Details',
                'invoice' => $invoice,
                'items' => $items,
                'history' => $history
            ];

            return view('finance/invoice_detail', $data);

        } catch (\Exception $e) {
            log_message('error', 'Finance::viewInvoice - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load invoice: ' . $e->getMessage());
        }
    }

    /**
     * Get invoices by contract (for contract detail view)
     */
    public function getInvoicesByContract($contractId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $invoices = $this->invoiceModel->getInvoicesByContract($contractId);

            return $this->response->setJSON([
                'success' => true,
                'data' => $invoices
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::getInvoicesByContract - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cancel invoice
     */
    public function cancelInvoice($invoiceId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $userId = session()->get('user_id') ?? 1;
            $reason = $this->request->getPost('reason');
            
            $invoice = $this->invoiceModel->find($invoiceId);
            if (!$invoice) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invoice not found'
                ]);
            }

            if (in_array($invoice['status'], ['PAID', 'CANCELLED'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot cancel PAID or already CANCELLED invoice'
                ]);
            }

            $updated = $this->invoiceModel->update($invoiceId, [
                'status' => 'CANCELLED',
                'cancellation_reason' => $reason
            ]);

            if ($updated) {
                $this->invoiceModel->updateStatus($invoiceId, 'CANCELLED', $userId);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Invoice cancelled successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to cancel invoice'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::cancelInvoice - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to cancel invoice: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create recurring billing schedule for contract
     */
    public function createBillingSchedule()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $contractId = $this->request->getPost('contract_id');
            $frequency = $this->request->getPost('frequency') ?? 'MONTHLY';

            if (empty($contractId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contract ID required'
                ]);
            }

            // Check if schedule already exists
            $existing = $this->scheduleModel->where('contract_id', $contractId)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Billing schedule already exists for this contract'
                ]);
            }

            $scheduleId = $this->scheduleModel->createSchedule($contractId, $frequency);

            if ($scheduleId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Billing schedule created successfully',
                    'schedule_id' => $scheduleId
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create billing schedule'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::createBillingSchedule - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create billing schedule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Pause recurring billing schedule
     */
    public function pauseBillingSchedule($scheduleId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $reason = $this->request->getPost('reason');
            $updated = $this->scheduleModel->pauseSchedule($scheduleId, $reason);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Billing schedule paused successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to pause billing schedule'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::pauseBillingSchedule - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to pause billing schedule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Resume paused billing schedule
     */
    public function resumeBillingSchedule($scheduleId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $updated = $this->scheduleModel->resumeSchedule($scheduleId);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Billing schedule resumed successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to resume billing schedule'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Finance::resumeBillingSchedule - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to resume billing schedule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper: Check if DI is ready for invoicing
     */
    public function checkDIReadiness($diId)
    {
        try {
            $validation = $this->diModel->validateBillingReadiness($diId);
            $di = $this->diModel->find($diId);
            
            if ($di) {
                // Get contract and customer info
                $spk = $this->db->table('spk')->where('id', $di['spk_id'])->get()->getRowArray();
                $contract = $spk && $spk['kontrak_id'] ? $this->db->table('kontrak')->where('id', $spk['kontrak_id'])->get()->getRowArray() : null;
                
                $di['customer_name'] = $contract['pelanggan'] ?? 'Unknown';
                $di['contract_number'] = $contract['no_kontrak'] ?? '-';
            }
            
            return $this->response->setJSON([
                'locked' => !$validation['ready'],
                'errors' => $validation['errors'] ?? [],
                'di' => $di
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'locked' => true,
                'errors' => ['System error: ' . $e->getMessage()]
            ]);
        }
    }

    /**
     * Helper: Get list of DIs ready for invoicing
     */
    public function getReadyDIs()
    {
        try {
            $dis = $this->diModel
                ->select('delivery_instructions.*, spk.nomor_spk, kontrak.no_kontrak as contract_number, kontrak.pelanggan as customer_name')
                ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
                ->join('kontrak', 'kontrak.id = delivery_instructions.contract_id', 'left')
                ->whereIn('delivery_instructions.status', ['DELIVERED', 'COMPLETED'])
                ->where('delivery_instructions.contract_id IS NOT NULL')
                ->orderBy('delivery_instructions.dibuat_pada', 'DESC')
                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $dis
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Helper: Get active billing schedules
     */
    public function getActiveSchedules()
    {
        try {
            $schedules = $this->scheduleModel
                ->select('recurring_billing_schedules.*, kontrak.no_kontrak as contract_number, kontrak.pelanggan as customer_name')
                ->join('kontrak', 'kontrak.id = recurring_billing_schedules.contract_id', 'left')
                ->where('recurring_billing_schedules.status', 'ACTIVE')
                ->orderBy('recurring_billing_schedules.next_billing_date', 'ASC')
                ->findAll(100);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $schedules
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Detect missing invoices (back-billing detection)
     * Returns list of overdue billing periods with estimated amounts
     */
    public function detectBackBilling()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            $contractId = $this->request->getGet('contract_id');
            
            $backBillingService = new \App\Services\BackBillingService();
            $missingInvoices = $backBillingService->detectMissingInvoices($contractId);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $missingInvoices,
                'count' => count($missingInvoices),
                'total_estimated' => array_sum(array_column($missingInvoices, 'estimated_amount'))
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Finance::detectBackBilling - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to detect back-billing: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate back-billing invoices for a contract
     * Auto-creates all missing invoices based on detection
     */
    public function generateBackBilling()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            $contractId = $this->request->getPost('contract_id');
            $autoApprove = $this->request->getPost('auto_approve') === 'true';
            $userId = session()->get('user_id') ?? 1;
            
            if (empty($contractId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contract ID required'
                ]);
            }
            
            $backBillingService = new \App\Services\BackBillingService();
            $result = $backBillingService->generateBackBilling($contractId, $userId, [
                'auto_approve' => $autoApprove,
                'tax_percent' => 11.00
            ]);
            
            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Back-billing generated successfully',
                    'invoices' => $result['created_invoices'],
                    'total_amount' => $result['total_amount'],
                    'count' => count($result['created_invoices'])
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to generate back-billing',
                'errors' => $result['errors']
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Finance::generateBackBilling - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to generate back-billing: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get back-billing statistics for dashboard widget
     * Returns count and total amount of missing invoices
     */
    public function getBackBillingStats()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            $backBillingService = new \App\Services\BackBillingService();
            $stats = $backBillingService->getStatistics();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Finance::getBackBillingStats - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get back-billing statistics: ' . $e->getMessage()
            ]);
        }
    }
}
