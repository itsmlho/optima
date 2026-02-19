<?php

namespace App\Jobs;

use App\Models\DeliveryInstructionModel;
use App\Models\InvoiceModel;
use App\Models\KontrakModel;
use App\Services\SimpleQueueService;

/**
 * Invoice Automation Job
 * 
 * Automatically generates invoices for delivery instructions (DIs) that:
 * 1. Have been completed (status = DELIVERED)
 * 2. Have a contract/PO linked (contract_id IS NOT NULL)
 * 3. Are 30 days past completion date
 * 4. Haven't had invoices generated yet (invoice_generated = 0)
 * 
 * Also handles late-linking scenarios where contracts are linked after 30 days
 */
class InvoiceAutomationJob
{
    protected $diModel;
    protected $invoiceModel;
    protected $contractModel;
    protected $queueService;
    
    public function __construct()
    {
        $this->diModel = new DeliveryInstructionModel();
        $this->invoiceModel = new InvoiceModel();
        $this->contractModel = new KontrakModel();
        $this->queueService = new SimpleQueueService();
    }
    
    /**
     * Main execution method - called by cron job
     * 
     * @return array Result summary
     */
    public function run()
    {
        log_message('info', '[InvoiceAutomation] Starting invoice automation job...');
        
        // Get DIs eligible for invoice generation
        $eligibleDIs = $this->getEligibleDeliveryInstructions();
        
        $generated = 0;
        $errors = 0;
        
        foreach ($eligibleDIs as $di) {
            try {
                $invoiceId = $this->generateInvoiceForDI($di);
                
                if ($invoiceId) {
                    // Send notifications
                    $this->sendInvoiceNotifications($invoiceId, $di);
                    $generated++;
                    
                    log_message('info', "[InvoiceAutomation] Invoice generated for DI #{$di['id']}, Invoice ID: {$invoiceId}");
                } else {
                    $errors++;
                    log_message('error', "[InvoiceAutomation] Failed to generate invoice for DI #{$di['id']}");
                }
                
            } catch (\Exception $e) {
                $errors++;
                log_message('error', "[InvoiceAutomation] Exception for DI #{$di['id']}: " . $e->getMessage());
            }
        }
        
        log_message('info', "[InvoiceAutomation] Job completed. Generated: {$generated}, Errors: {$errors}");
        
        return [
            'success' => true,
            'generated' => $generated,
            'errors' => $errors
        ];
    }
    
    /**
     * Get DIs that are eligible for invoice generation
     * 
     * Rules:
     * 1. Status = SELESAI (DI completed - actual database uses 'SELESAI' not 'DELIVERED')
     * 2. Contract/PO is linked (spk.kontrak_id IS NOT NULL)
     * 3. 30 days have passed since DI sampai_tanggal_approve date
     * 4. Invoice not yet generated (invoice_generated = 0)
     * 
     * @return array List of eligible DIs
     */
    protected function getEligibleDeliveryInstructions()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('delivery_instructions di');
        
        $builder->select('di.*, 
                         k.id AS kontrak_id,
                         k.no_kontrak AS contract_number,
                         c.customer_name,
                         q.assigned_to AS sales_user_id,
                         di.nomor_di AS di_number,
                         di.sampai_tanggal_approve AS completed_at')
                ->join('spk s', 's.id = di.spk_id', 'inner')
                ->join('kontrak k', 'k.id = s.kontrak_id', 'inner')
                ->join('customer_contracts cc', 'cc.kontrak_id = k.id', 'inner')
                ->join('customers c', 'c.id = cc.customer_id', 'inner')
                ->join('quotations q', 'q.id_quotation = s.quotation_specification_id', 'left')
                ->where('di.status_di', 'SELESAI')
                ->where('s.kontrak_id IS NOT NULL')
                ->where('di.invoice_generated', 0)
                ->where('di.sampai_tanggal_approve IS NOT NULL')
                ->where('DATE_ADD(di.sampai_tanggal_approve, INTERVAL 30 DAY) <=', date('Y-m-d'));
        
        $results = $builder->get()->getResultArray();
        
        log_message('info', '[InvoiceAutomation] Found ' . count($results) . ' eligible DIs for invoice generation');
        
        return $results;
    }
    
    /**
     * Generate invoice for specific DI
     * 
     * @param array $di Delivery instruction data
     * @return int|false Invoice ID if successful, false otherwise
     */
    protected function generateInvoiceForDI($di)
    {
        // Validate billing readiness (3-layer check)
        $validation = $this->diModel->validateBillingReadiness($di['id']);
        
        if (!$validation['ready']) {
            log_message('warning', "[InvoiceAutomation] DI #{$di['id']} failed validation: " . $validation['message']);
            return false;
        }
        
        // Get contract details
        $contract = $this->contractModel->find($di['contract_id']);
        
        if (!$contract) {
            log_message('error', "[InvoiceAutomation] Contract not found for DI #{$di['id']}");
            return false;
        }
        
        // Create invoice using existing model method
        $invoiceId = $this->invoiceModel->createFromDI(
            $di['id'],
            $di['contract_id'],
            $senderUserId = 1 // System-generated
        );
        
        if ($invoiceId) {
            // Mark DI as invoice generated
            $this->diModel->update($di['id'], [
                'invoice_generated' => 1,
                'invoice_generated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return $invoiceId;
    }
    
    /**
     * Send email and in-app notifications
     * 
     * @param int $invoiceId Invoice ID
     * @param array $diData Delivery instruction data
     */
    protected function sendInvoiceNotifications($invoiceId, $diData)
    {
        $invoice = $this->invoiceModel->find($invoiceId);
        
        if (!$invoice) {
            return;
        }
        
        // Prepare email data
        $emailData = [
            'invoice' => $invoice,
            'di' => $diData,
            'customer_name' => $diData['customer_name'] ?? 'N/A',
            'contract_number' => $diData['contract_number'] ?? 'N/A'
        ];
        
        // Get email addresses from environment or use defaults
        $accEmail1 = getenv('ACC_EMAIL_1') ?: 'finance@sml.co.id';
        $accEmail2 = getenv('ACC_EMAIL_2') ?: 'anselin_smlforklift@yahoo.com';
        $marketingEmail = getenv('MARKETING_EMAIL') ?: 'marketing@sml.co.id';
        
        // Send email to ACC team (primary + CC to secondary ACC)
        $this->queueService->push(SendEmailJob::class, [
            'to' => $accEmail1,
            'cc' => [$accEmail2],
            'subject' => 'Invoice Ready for Processing: ' . $invoice['invoice_number'] . ' - ' . ($diData['customer_name'] ?? 'Customer'),
            'template' => 'emails/invoice_ready_acc',
            'data' => $emailData
        ]);
        
        // Send separate notification email to Marketing team
        $this->queueService->push(SendEmailJob::class, [
            'to' => $marketingEmail,
            'subject' => 'Invoice Auto-Generated: ' . $invoice['invoice_number'] . ' - ' . ($diData['customer_name'] ?? 'Customer'),
            'template' => 'emails/invoice_ready_marketing',
            'data' => $emailData
        ]);
        
        // Create in-app notification for marketing user who handled the quotation
        if (!empty($diData['sales_user_id'])) {
            $notificationModel = new \App\Models\NotificationModel();
            $notificationModel->createNotification([
                'user_id' => $diData['sales_user_id'],
                'title' => 'Invoice Auto-Generated',
                'message' => "Invoice {$invoice['invoice_number']} has been automatically generated for {$diData['customer_name']}. Accounting team has been notified and will process the invoice shortly.",
                'type' => 'success',
                'category' => 'invoice',
                'url' => "/finance/invoices/{$invoiceId}",
                'is_system_generated' => 1
            ]);
        }
        
        log_message('info', "[InvoiceAutomation] Notifications sent for Invoice #{$invoiceId}");
    }
    
    /**
     * Handle late-linking scenario
     * 
     * When contract is linked to DI AFTER 30 days have passed, instantly generate invoice
     * This function should be called from Marketing controller when linking contract to SPK/DI
     * 
     * @param int $diId Delivery instruction ID
     * @return int|false Invoice ID if generated, false otherwise
     */
    public function handleLateLinkedDI($diId)
    {
        $db = \Config\Database::connect();
        
        // Get DI with all related data
        $builder = $db->table('delivery_instructions di');
        $di = $builder->select('di.*, 
                               di.sampai_tanggal_approve AS completed_at,
                               k.id AS kontrak_id,
                               k.no_kontrak AS contract_number,
                               c.customer_name,
                               q.assigned_to AS sales_user_id')
                    ->join('spk s', 's.id = di.spk_id', 'left')
                    ->join('kontrak k', 'k.id = s.kontrak_id', 'left')
                    ->join('customer_contracts cc', 'cc.kontrak_id = k.id', 'left')
                    ->join('customers c', 'c.id = cc.customer_id', 'left')
                    ->join('quotations q', 'q.id_quotation = s.quotation_specification_id', 'left')
                    ->where('di.id', $diId)
                    ->get()
                    ->getRowArray();
        
        if (!$di) {
            return false;
        }
        
        // Check if DI is SELESAI and >30 days old
        if ($di['status_di'] === 'SELESAI' && $di['kontrak_id'] && $di['sampai_tanggal_approve']) {
            $completedDate = strtotime($di['sampai_tanggal_approve']);
            $daysPassed = (time() - $completedDate) / (60 * 60 * 24);
            
            if ($daysPassed >= 30 && !$di['invoice_generated']) {
                log_message('info', "[InvoiceAutomation] Late-linking detected for DI #{$diId}. Days passed: " . floor($daysPassed));
                
                // Instantly generate invoice
                $invoiceId = $this->generateInvoiceForDI($di);
                
                if ($invoiceId) {
                    $this->sendInvoiceNotifications($invoiceId, $di);
                    return $invoiceId;
                }
            }
        }
        
        return false;
    }
}
