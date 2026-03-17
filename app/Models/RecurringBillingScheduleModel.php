<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Recurring Billing Schedule Model
 * Manages automatic invoice generation schedules for rental contracts
 * Used for monthly/quarterly/yearly billing automation
 */
class RecurringBillingScheduleModel extends Model
{
    protected $table = 'recurring_billing_schedules';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'contract_id',
        'frequency',
        'next_billing_date',
        'last_invoice_id',
        'auto_generate',
        'status'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'contract_id' => 'required|integer|is_unique[recurring_billing_schedules.contract_id]',
        'frequency' => 'required|in_list[MONTHLY,QUARTERLY,YEARLY]',
        'next_billing_date' => 'required|valid_date',
        'status' => 'required|in_list[ACTIVE,PAUSED,COMPLETED]'
    ];
    
    /**
     * Create billing schedule for a contract
     * 
     * @param int $contractId Contract ID
     * @param string $frequency Billing frequency (MONTHLY, QUARTERLY, YEARLY)
     * @return int|false Schedule ID or false on failure
     */
    public function createSchedule(int $contractId, string $frequency = 'MONTHLY')
    {
        // Check if schedule already exists
        $existing = $this->where('contract_id', $contractId)->first();
        if ($existing) {
            return $existing['id']; // Return existing schedule ID
        }
        
        // Get contract details
        $kontrakModel = new \App\Models\KontrakModel();
        $contract = $kontrakModel->find($contractId);
        
        if (!$contract) {
            return false;
        }
        
        // Calculate first billing date from contract start date
        $contractStart = $contract['tanggal_mulai'];
        $nextBillingDate = $contractStart;
        
        // For monthly billing, if contract starts mid-month, next billing is start of next month
        if ($frequency === 'MONTHLY') {
            $startDay = date('d', strtotime($contractStart));
            if ($startDay > 1) {
                // Started mid-month, bill from next month
                $nextBillingDate = date('Y-m-01', strtotime($contractStart . ' +1 month'));
            }
        }
        
        $scheduleData = [
            'contract_id' => $contractId,
            'frequency' => $frequency,
            'next_billing_date' => $nextBillingDate,
            'last_invoice_id' => null,
            'auto_generate' => true,
            'status' => 'ACTIVE'
        ];
        
        if ($this->insert($scheduleData)) {
            return $this->getInsertID();
        }
        
        return false;
    }
    
    /**
     * Get schedules with upcoming invoices
     * Used for dashboard alerts and notifications
     * 
     * @param int $days Number of days to look ahead
     * @return array List of upcoming billing schedules
     */
    public function getUpcomingInvoices(int $days = 30): array
    {
        $today = date('Y-m-d');
        $futureDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->select('recurring_billing_schedules.*, '
                           . 'kontrak.no_kontrak, kontrak.nilai_total, kontrak.total_units, '
                           . 'customers.customer_name')
                    ->join('kontrak', 'kontrak.id = recurring_billing_schedules.contract_id')
                    ->join('customers', 'customers.id = kontrak.customer_id')
                    ->where('recurring_billing_schedules.status', 'ACTIVE')
                    ->where('recurring_billing_schedules.auto_generate', 1)
                    ->where('recurring_billing_schedules.next_billing_date >=', $today)
                    ->where('recurring_billing_schedules.next_billing_date <=', $futureDate)
                    ->orderBy('recurring_billing_schedules.next_billing_date', 'ASC')
                    ->findAll();
    }
    
    /**
     * Generate invoices for all due billing schedules
     * Called by cron job or manual trigger
     * 
     * @return array Result ['count' => int, 'invoices' => array, 'errors' => array]
     */
    public function generateDueInvoices(): array
    {
        $result = ['count' => 0, 'invoices' => [], 'errors' => []];
        
        $today = date('Y-m-d');
        
        // Get all schedules due for billing
        $dueSchedules = $this->where('status', 'ACTIVE')
                             ->where('auto_generate', 1)
                             ->where('next_billing_date <=', $today)
                             ->findAll();
        
        $invoiceModel = new \App\Models\InvoiceModel();
        
        foreach ($dueSchedules as $schedule) {
            try {
                // Create recurring invoice
                $invoiceResult = $invoiceModel->createRecurringInvoice(
                    $schedule['id'],
                    1 // System user ID for auto-generation
                );
                
                if ($invoiceResult['success']) {
                    $result['count']++;
                    $result['invoices'][] = [
                        'schedule_id' => $schedule['id'],
                        'invoice_id' => $invoiceResult['invoice_id'],
                        'contract_id' => $schedule['contract_id']
                    ];
                } else {
                    $result['errors'][] = [
                        'schedule_id' => $schedule['id'],
                        'contract_id' => $schedule['contract_id'],
                        'errors' => $invoiceResult['errors']
                    ];
                }
            } catch (\Exception $e) {
                $result['errors'][] = [
                    'schedule_id' => $schedule['id'],
                    'contract_id' => $schedule['contract_id'],
                    'errors' => [$e->getMessage()]
                ];
            }
        }
        
        return $result;
    }
    
    /**
     * Pause billing schedule
     * Temporarily stop automatic invoice generation
     * 
     * @param int $contractId Contract ID
     * @param string $reason Reason for pausing
     * @return bool Success status
     */
    public function pauseSchedule(int $contractId, string $reason): bool
    {
        $schedule = $this->where('contract_id', $contractId)->first();
        
        if (!$schedule) {
            return false;
        }
        
        return (bool) $this->update($schedule['id'], [
            'status' => 'PAUSED'
            // Could store reason in a notes field if needed
        ]);
    }
    
    /**
     * Resume paused billing schedule
     * 
     * @param int $contractId Contract ID
     * @return bool Success status
     */
    public function resumeSchedule(int $contractId): bool
    {
        $schedule = $this->where('contract_id', $contractId)->first();
        
        if (!$schedule) {
            return false;
        }
        
        return (bool) $this->update($schedule['id'], [
            'status' => 'ACTIVE'
        ]);
    }
    
    /**
     * Complete billing schedule
     * Mark as completed when contract ends
     * 
     * @param int $contractId Contract ID
     * @return bool Success status
     */
    public function completeSchedule(int $contractId): bool
    {
        $schedule = $this->where('contract_id', $contractId)->first();
        
        if (!$schedule) {
            return false;
        }
        
        return (bool) $this->update($schedule['id'], [
            'status' => 'COMPLETED'
        ]);
    }
    
    /**
     * Get billing schedule for a contract
     * 
     * @param int $contractId Contract ID
     * @return array|null Schedule details
     */
    public function getScheduleByContract(int $contractId): ?array
    {
        return $this->where('contract_id', $contractId)->first();
    }
    
    /**
     * Update next billing date after invoice generation
     * 
     * @param int $scheduleId Schedule ID
     * @param string $nextDate Next billing date
     * @param int $invoiceId Last generated invoice ID
     * @return bool Success status
     */
    public function updateNextBillingDate(int $scheduleId, string $nextDate, int $invoiceId): bool
    {
        return (bool) $this->update($scheduleId, [
            'next_billing_date' => $nextDate,
            'last_invoice_id' => $invoiceId
        ]);
    }
}
