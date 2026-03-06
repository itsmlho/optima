<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Invoice Model
 * Core invoicing system with contract-based billing control
 * Implements invoice locking mechanism for unlinked deliveries
 */
class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'invoice_number',
        'contract_id',       // Opsional: invoice bisa dibuat tanpa kontrak
        'po_reference',      // Opsional: nomor PO dari customer
        'di_id',
        'customer_id',
        'invoice_type',
        'billing_period_start',
        'billing_period_end',
        'issue_date',
        'due_date',
        'subtotal',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total_amount',
        'status',
        'payment_date',
        'payment_method',
        'payment_reference',
        'notes',
        'created_by',
        'approved_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'invoice_number'       => 'required|string|max_length[50]|is_unique[invoices.invoice_number]',
        // contract_id OPSIONAL: invoice bisa dibuat tanpa kontrak/PO (cukup list unit + harga)
        'contract_id'          => 'permit_empty|integer',
        'customer_id'          => 'required|integer',
        'invoice_type'         => 'required|in_list[ONE_TIME,RECURRING_RENTAL,ADDENDUM,MANUAL]',
        'billing_period_start' => 'required|valid_date',
        'billing_period_end'   => 'required|valid_date',
        'issue_date'           => 'required|valid_date',
        'due_date'             => 'required|valid_date',
        'status'               => 'required|in_list[DRAFT,PENDING_APPROVAL,APPROVED,SENT,PAID,OVERDUE,CANCELLED]',
        'created_by'           => 'required|integer'
    ];
    
    /**
     * Generate next invoice number with prefix INV/YYYYMM/NNN
     * Uses stored procedure with database locking
     * 
     * @return string Invoice number
     */
    public function generateInvoiceNumber(): string
    {
        try {
            // Call stored procedure
            $query = $this->db->query("CALL sp_generate_invoice_number(@invoice_number)");
            $result = $this->db->query("SELECT @invoice_number as invoice_number")->getRow();
            
            if ($result && !empty($result->invoice_number)) {
                return $result->invoice_number;
            }
        } catch (\Exception $e) {
            // Fallback to manual generation
            log_message('warning', 'Failed to use stored procedure for invoice number: ' . $e->getMessage());
        }
        
        // Fallback manual generation
        $prefix = 'INV/' . date('Ym') . '/';
        $row = $this->db->table($this->table)
                        ->like('invoice_number', $prefix)
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getRowArray();
        
        $seq = 1;
        if ($row && isset($row['invoice_number'])) {
            $parts = explode('/', $row['invoice_number']);
            $seq = isset($parts[2]) ? ((int)$parts[2] + 1) : 1;
        }
        
        return $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Create invoice from Delivery Instruction (one-time invoice)
     * VALIDATION GUARDRAIL: Checks billing readiness before creation
     * 
     * @param int $diId Delivery Instruction ID
     * @param int $contractId Contract ID (should match DI's contract)
     * @param int $userId User creating invoice
     * @param array $options Optional parameters (due_days, tax_percent, discount, notes)
     * @return array Result ['success' => bool, 'invoice_id' => int|null, 'errors' => array]
     */
    public function createFromDI(int $diId, int $contractId, int $userId, array $options = []): array
    {
        $result = ['success' => false, 'invoice_id' => null, 'errors' => []];
        
        // LAYER 1: Validate billing readiness
        $diModel = new \App\Models\DeliveryInstructionModel();
        $validationErrors = $diModel->validateBillingReadiness($diId);
        
        if (!empty($validationErrors)) {
            $result['errors'] = $validationErrors;
            $result['locked'] = true;
            return $result;
        }
        
        // Get DI details
        $di = $diModel->select('delivery_instructions.*, kontrak.no_kontrak, '
                             . 'kontrak.customer_id, customers.customer_name')
                      ->join('kontrak', 'kontrak.id = delivery_instructions.contract_id', 'left')
                      ->join('customers', 'customers.id = kontrak.customer_id', 'left')
                      ->find($diId);
        
        if (!$di) {
            $result['errors'][] = 'Delivery Instruction not found';
            return $result;
        }
        
        // Verify contract match
        if ($di['contract_id'] != $contractId) {
            $result['errors'][] = 'Contract ID mismatch';
            return $result;
        }
        
        // Generate invoice number
        $invoiceNumber = $this->generateInvoiceNumber();
        
        // Calculate billing period from BAST date
        $billingStart = $di['billing_start_date'] ?? $di['bast_date'];
        $billingEnd = $billingStart; // For one-time, end = start
        
        // Calculate due date
        $dueDays = $options['due_days'] ?? 30;
        $issueDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime($issueDate . " +{$dueDays} days"));
        
        // Prepare invoice data
        $invoiceData = [
            'invoice_number' => $invoiceNumber,
            'contract_id' => $contractId,
            'di_id' => $diId,
            'customer_id' => $di['customer_id'],
            'invoice_type' => 'ONE_TIME',
            'billing_period_start' => $billingStart,
            'billing_period_end' => $billingEnd,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'subtotal' => 0, // Will be calculated from items
            'discount_amount' => $options['discount_amount'] ?? 0,
            'tax_percent' => $options['tax_percent'] ?? 11.00,
            'tax_amount' => 0, // Will be calculated by trigger
            'total_amount' => 0, // Will be calculated by trigger
            'status' => 'DRAFT',
            'notes' => $options['notes'] ?? 'Invoice generated from Delivery Instruction ' . $di['nomor_di'],
            'created_by' => $userId
        ];
        
        // Insert invoice
        if ($this->insert($invoiceData)) {
            $invoiceId = $this->getInsertID();
            
            // Add items from delivery
            $invoiceItemModel = new \App\Models\InvoiceItemModel();
            $itemCount = $invoiceItemModel->addItemsFromDI($invoiceId, $diId);
            
            $result['success'] = true;
            $result['invoice_id'] = $invoiceId;
            $result['message'] = "Invoice {$invoiceNumber} created with {$itemCount} items";
            
            return $result;
        }
        
        $result['errors'][] = 'Failed to create invoice';
        return $result;
    }
    
    /**
     * Create recurring invoice from billing schedule
     * Used for monthly/periodic rental billing
     * 
     * @param int $scheduleId Billing schedule ID
     * @param int $userId User creating invoice
     * @return array Result ['success' => bool, 'invoice_id' => int|null, 'errors' => array]
     */
    public function createRecurringInvoice(int $scheduleId, int $userId): array
    {
        $result = ['success' => false, 'invoice_id' => null, 'errors' => []];

        $scheduleModel = new \App\Models\RecurringBillingScheduleModel();
        $schedule = $scheduleModel->find($scheduleId);

        if (!$schedule) {
            $result['errors'][] = 'Billing schedule not found';
            return $result;
        }

        // Get contract
        $kontrakModel = new \App\Models\KontrakModel();
        $contract = $kontrakModel->select('kontrak.*, kontrak.customer_id')
                                 ->find($schedule['contract_id']);

        if (!$contract) {
            $result['errors'][] = 'Contract not found';
            return $result;
        }

        // Calculate billing period
        $billingStart = $schedule['next_billing_date'];
        $frequency    = $schedule['frequency'] ?? 'MONTHLY';
        $billingEnd   = match ($frequency) {
            'QUARTERLY' => date('Y-m-d', strtotime($billingStart . ' +3 months -1 day')),
            'YEARLY'    => date('Y-m-d', strtotime($billingStart . ' +1 year -1 day')),
            default     => date('Y-m-d', strtotime($billingStart . ' +1 month -1 day')), // MONTHLY
        };

        // Use BillingCalculator for accurate amount
        $billingCalculator = new \App\Libraries\BillingCalculator();
        try {
            $billingResult    = $billingCalculator->calculate($contract['id'], $billingStart, $billingEnd);
            $calculatedAmount = $billingResult['amount'];
            $billingMethod    = $billingResult['method'];
            $billingDays      = $billingResult['days'];
        } catch (\Exception $e) {
            log_message('error', 'InvoiceModel::createRecurringInvoice - BillingCalculator error: ' . $e->getMessage());
            $calculatedAmount = 0;
            $billingMethod    = 'LEGACY';
            $billingDays      = 0;
        }

        // Check for applicable amendments
        $amendmentModel = new \App\Models\ContractAmendmentModel();
        $effectiveRate  = $amendmentModel->getEffectiveRate($contract['id'], $billingStart);

        $issueDate = date('Y-m-d');
        $dueDate   = date('Y-m-d', strtotime($issueDate . ' +30 days'));

        // ─── BEGIN ATOMIC BLOCK ─────────────────────────────────────────────────────
        $this->db->transStart();
        try {
            // Generate invoice number inside the transaction
            $invoiceNumber = $this->generateInvoiceNumber();

            $invoiceData = [
                'invoice_number'        => $invoiceNumber,
                'contract_id'           => $contract['id'],
                'di_id'                 => null,
                'customer_id'           => $contract['customer_id'],
                'invoice_type'          => $effectiveRate ? 'ADDENDUM' : 'RECURRING_RENTAL',
                'billing_period_start'  => $billingStart,
                'billing_period_end'    => $billingEnd,
                'issue_date'            => $issueDate,
                'due_date'              => $dueDate,
                'subtotal'              => $calculatedAmount > 0 ? $calculatedAmount : 0,
                'discount_amount'       => 0,
                'tax_percent'           => 11.00,
                'tax_amount'            => $calculatedAmount > 0 ? ($calculatedAmount * 0.11) : 0,
                'total_amount'          => $calculatedAmount > 0 ? ($calculatedAmount * 1.11) : 0,
                'status'                => 'DRAFT',
                'notes'                 => "Recurring rental invoice for period {$billingStart} to {$billingEnd} ({$billingDays} days, {$billingMethod})",
                'created_by'            => $userId,
            ];

            if (!$this->insert($invoiceData)) {
                throw new \Exception('Failed to insert invoice record.');
            }
            $invoiceId = $this->getInsertID();

            // Add items from contract specifications
            $invoiceItemModel = new \App\Models\InvoiceItemModel();
            $itemCount = $invoiceItemModel->addItemsFromContract($contract['id'], $effectiveRate);

            // Guard: cannot have an invoice with zero items
            if ((int)$itemCount === 0) {
                throw new \Exception(
                    "Kontrak #{$contract['id']} tidak memiliki item spesifikasi aktif. " .
                    "Invoice tidak dapat dibuat dengan 0 item."
                );
            }

            // If BillingCalculator failed, derive totals from items
            if ($calculatedAmount === 0) {
                $this->recalculateInvoiceTotals($invoiceId);
            }

            // Advance the billing schedule only after invoice is confirmed
            $nextBillingDate = date('Y-m-d', strtotime($billingEnd . ' +1 day'));
            $scheduleModel->update($scheduleId, [
                'next_billing_date' => $nextBillingDate,
                'last_invoice_id'   => $invoiceId,
            ]);

            $this->db->transComplete();
            // ─── END ATOMIC BLOCK ────────────────────────────────────────────────────

            $result['success']        = true;
            $result['invoice_id']     = $invoiceId;
            $result['invoice_number'] = $invoiceNumber;
            $result['message']        = "Invoice {$invoiceNumber} created with {$itemCount} items (Method: {$billingMethod})";
            return $result;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[InvoiceModel::createRecurringInvoice] schedule #' . $scheduleId . ': ' . $e->getMessage());
            $result['errors'][] = $e->getMessage();
            return $result;
        }
    }
    
    /**
     * Recalculate invoice totals from items
     * Used when BillingCalculator fails or for legacy invoices
     * 
     * @param int $invoiceId Invoice ID
     * @return bool Success status
     */
    protected function recalculateInvoiceTotals(int $invoiceId): bool
    {
        $invoiceItemModel = new \App\Models\InvoiceItemModel();
        $items = $invoiceItemModel->where('invoice_id', $invoiceId)->findAll();
        
        $subtotal = array_sum(array_column($items, 'subtotal'));
        $taxAmount = $subtotal * 0.11;
        $totalAmount = $subtotal + $taxAmount;
        
        return $this->update($invoiceId, [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ]);
    }
    
    /**
     * Update invoice status with audit trail
     * 
     * @param int $invoiceId Invoice ID
     * @param string $newStatus New status
     * @param int $userId User changing status
     * @param string|null $notes Optional notes
     * @return bool Success status
     */
    public function updateStatus(int $invoiceId, string $newStatus, int $userId, ?string $notes = null): bool
    {
        $invoice = $this->find($invoiceId);
        
        if (!$invoice) {
            return false;
        }
        
        // Validate status transition (optional - add business rules here)
        
        // Update invoice
        $updateData = ['status' => $newStatus];
        
        if ($newStatus === 'APPROVED') {
            $updateData['approved_by'] = $userId;
        }
        
        if ($newStatus === 'PAID' && empty($invoice['payment_date'])) {
            $updateData['payment_date'] = date('Y-m-d');
        }
        
        return (bool) $this->update($invoiceId, $updateData);
    }
    
    /**
     * Get all invoices for a contract
     * 
     * @param int $contractId Contract ID
     * @return array List of invoices
     */
    public function getInvoicesByContract(int $contractId): array
    {
        return $this->where('contract_id', $contractId)
                    ->orderBy('billing_period_start', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get invoice with full details (customer, contract, DI)
     * 
     * @param int $invoiceId Invoice ID
     * @return array|null Invoice with joined data
     */
    public function getInvoiceDetails(int $invoiceId): ?array
    {
        return $this->select('invoices.*, '
                           . 'customers.name as customer_name, customers.email as customer_email, '
                           . 'kontrak.no_kontrak, '
                           . 'delivery_instructions.nomor_di, '
                           . 'users.username as created_by_username')
                    ->join('customers', 'customers.id = invoices.customer_id', 'left')
                    ->join('kontrak', 'kontrak.id = invoices.contract_id', 'left')
                    ->join('delivery_instructions', 'delivery_instructions.id = invoices.di_id', 'left')
                    ->join('users', 'users.id = invoices.created_by', 'left')
                    ->find($invoiceId);
    }
}
