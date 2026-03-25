<?php

namespace App\Services;

use App\Models\InvoiceModel;
use App\Models\KontrakModel;
use App\Libraries\BillingCalculator;
use CodeIgniter\I18n\Time;

/**
 * Back-Billing Service
 * 
 * Detects and auto-generates missed invoices for active contracts
 * Supports staggered deliveries and multiple billing methods
 * 
 * Usage:
 *   $service = new BackBillingService();
 *   $missing = $service->detectMissingInvoices();
 *   $result = $service->generateBackBilling($contractId, $userId);
 */
class BackBillingService
{
    protected $db;
    protected $invoiceModel;
    protected $kontrakModel;
    protected $billingCalculator;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->invoiceModel = new InvoiceModel();
        $this->kontrakModel = new KontrakModel();
        $this->billingCalculator = new BillingCalculator();
    }
    
    /**
     * Detect all missing invoices across all active contracts
     * Scans unit_billing_schedules for overdue billings
     * 
     * @param int|null $contractId Optional: detect for specific contract only
     * @return array Array of missing invoice periods
     */
    public function detectMissingInvoices(?int $contractId = null): array
    {
        $builder = $this->db->table('unit_billing_schedules ubs');
        $builder->select('
            ubs.id as schedule_id,
            ubs.contract_id,
            ubs.unit_id,
            ubs.on_hire_date,
            ubs.billing_method,
            ubs.next_billing_date,
            ubs.last_billed_date,
            ubs.monthly_rate,
            k.no_kontrak,
            k.rental_type,
            k.tanggal_berakhir as contract_end_date,
            k.actual_return_date,
            k.estimated_duration_days,
            k.payment_due_day,
            c.customer_name,
            u.nomor_unit
        ');
        $builder->join('kontrak k', 'k.id = ubs.contract_id', 'left');
        $builder->join('customers c', 'c.id = k.customer_id', 'left');
        $builder->join('unit u', 'u.id = ubs.unit_id', 'left');
        
        // Only ACTIVE schedules
        $builder->where('ubs.status', 'ACTIVE');
        
        // Next billing date is in the past (overdue)
        $builder->where('ubs.next_billing_date <', date('Y-m-d'));
        
        // DAILY_SPOT: one-time billing handled separately, skip from cycle detection
        $builder->where('k.rental_type !=', 'DAILY_SPOT');
        
        // Optional: filter by contract
        if ($contractId) {
            $builder->where('ubs.contract_id', $contractId);
        }
        
        $builder->orderBy('ubs.next_billing_date', 'ASC');
        
        $overdueSchedules = $builder->get()->getResultArray();
        
        $missingInvoices = [];
        
        foreach ($overdueSchedules as $schedule) {
            // Calculate all missing periods for this unit
            $missingPeriods = $this->calculateMissingPeriods($schedule);
            
            foreach ($missingPeriods as $period) {
                $missingInvoices[] = [
                    'schedule_id'     => $schedule['schedule_id'],
                    'contract_id'     => $schedule['contract_id'],
                    'contract_number' => $schedule['no_kontrak'],
                    'rental_type'     => $schedule['rental_type'],
                    'customer_name'   => $schedule['customer_name'],
                    'unit_id'         => $schedule['unit_id'],
                    'unit_number'     => $schedule['nomor_unit'],
                    'billing_method'  => $schedule['billing_method'],
                    'period_start'    => $period['period_start'],
                    'period_end'      => $period['period_end'],
                    'monthly_rate'    => $schedule['monthly_rate'],
                    'days_overdue'    => $this->calculateDaysOverdue($period['period_end']),
                    'estimated_amount' => $period['estimated_amount']
                ];
            }
        }
        
        return $missingInvoices;
    }
    
    /**
     * Calculate missing billing periods for a unit schedule
     * 
     * @param array $schedule Unit billing schedule data
     * @return array Array of missing periods with amounts
     */
    protected function calculateMissingPeriods(array $schedule): array
    {
        $periods = [];
        $currentStart = $schedule['last_billed_date'] 
            ? date('Y-m-d', strtotime($schedule['last_billed_date'] . ' +1 day'))
            : $schedule['on_hire_date'];
        
        $today = date('Y-m-d');
        $maxIterations = 24; // Safety: max 2 years of missing invoices
        $iteration = 0;
        
        while ($currentStart < $today && $iteration < $maxIterations) {
            $iteration++;
            
            // Calculate period end based on billing method
            $periodEnd = $this->calculatePeriodEnd($currentStart, $schedule['billing_method']);
            
            // PO_ONLY: no fixed end date — cap only at today, not contract_end_date
            $rentalType = $schedule['rental_type'] ?? 'CONTRACT';
            if ($rentalType !== 'PO_ONLY') {
                // Don't exceed contract end date for CONTRACT and DAILY_SPOT
                if ($schedule['contract_end_date'] && $periodEnd > $schedule['contract_end_date']) {
                    $periodEnd = $schedule['contract_end_date'];
                }
            }
            
            // Calculate billing amount using BillingCalculator
            try {
                $billing = $this->billingCalculator->calculate(
                    $schedule['contract_id'],
                    $currentStart,
                    $periodEnd
                );
                
                $periods[] = [
                    'period_start' => $currentStart,
                    'period_end' => $periodEnd,
                    'estimated_amount' => $billing['amount'],
                    'days' => $billing['days'],
                    'method' => $billing['method']
                ];
            } catch (\Exception $e) {
                log_message('error', 'BackBillingService: Failed to calculate billing - ' . $e->getMessage());
                
                // Fallback: simple calculation
                $days = (strtotime($periodEnd) - strtotime($currentStart)) / 86400 + 1;
                $periods[] = [
                    'period_start' => $currentStart,
                    'period_end' => $periodEnd,
                    'estimated_amount' => ($schedule['monthly_rate'] / 30) * $days,
                    'days' => $days,
                    'method' => 'FALLBACK'
                ];
            }
            
            // Move to next period
            $currentStart = date('Y-m-d', strtotime($periodEnd . ' +1 day'));
            
            // Safety: don't create future invoices
            if ($currentStart >= $today) {
                break;
            }
        }
        
        return $periods;
    }
    
    /**
     * Calculate period end date based on billing method
     * 
     * @param string $startDate Period start date
     * @param string $billingMethod Billing method (CYCLE/PRORATE/MONTHLY_FIXED)
     * @return string Period end date
     */
    protected function calculatePeriodEnd(string $startDate, string $billingMethod): string
    {
        switch ($billingMethod) {
            case 'CYCLE':
                // 30-day rolling cycle
                return date('Y-m-d', strtotime($startDate . ' +29 days'));
                
            case 'PRORATE':
                // End of current month
                return date('Y-m-t', strtotime($startDate));
                
            case 'MONTHLY_FIXED':
                // End of 30-day period (can be customized)
                return date('Y-m-d', strtotime($startDate . ' +29 days'));
                
            default:
                return date('Y-m-d', strtotime($startDate . ' +29 days'));
        }
    }
    
    /**
     * Calculate days overdue from period end
     * 
     * @param string $periodEnd Period end date
     * @return int Days overdue
     */
    protected function calculateDaysOverdue(string $periodEnd): int
    {
        $end = new Time($periodEnd);
        $now = new Time('now');
        return $now->difference($end)->getDays();
    }
    
    /**
     * Generate back-billing invoices for a contract
     * Auto-creates all missing invoices based on detection
     * 
     * @param int $contractId Contract ID
     * @param int $userId User ID creating back-billing
     * @param array $options Optional parameters
     * @return array Result with success status and created invoices
     */
    public function generateBackBilling(int $contractId, int $userId, array $options = []): array
    {
        $result = [
            'success' => false,
            'created_invoices' => [],
            'errors' => [],
            'total_amount' => 0
        ];
        
        // Detect missing invoices for this contract
        $missingInvoices = $this->detectMissingInvoices($contractId);
        
        if (empty($missingInvoices)) {
            $result['errors'][] = 'No missing invoices detected for this contract';
            return $result;
        }
        
        // Group by billing period to create consolidated invoices
        $periodGroups = $this->groupByPeriod($missingInvoices);
        
        $this->db->transStart();
        
        foreach ($periodGroups as $group) {
            try {
                // Generate invoice number
                $invoiceNumber = $this->invoiceModel->generateInvoiceNumber();
                
                // Rental type determines invoice type and description label
                $rentalType  = $group['units'][0]['rental_type'] ?? 'CONTRACT';
                $invoiceType = match($rentalType) {
                    'PO_ONLY'    => 'PO_BILLING',
                    'DAILY_SPOT' => 'SPOT_BILLING',
                    default      => 'BACK_BILLING',
                };
                $periodLabel = match($rentalType) {
                    'PO_ONLY'    => 'PO Billing for period',
                    'DAILY_SPOT' => 'Spot Rental Billing',
                    default      => 'Back-billing for period',
                };
                
                // Calculate totals
                $subtotal = array_sum(array_column($group['units'], 'estimated_amount'));
                $taxPercent = $options['tax_percent'] ?? 11.00;
                $taxAmount = $subtotal * ($taxPercent / 100);
                $totalAmount = $subtotal + $taxAmount;
                
                // Create invoice
                $invoiceData = [
                    'invoice_number' => $invoiceNumber,
                    'contract_id' => $contractId,
                    'di_id' => null,
                    'customer_id' => $group['customer_id'],
                    'invoice_type'           => $invoiceType,
                    'billing_period_start' => $group['period_start'],
                    'billing_period_end' => $group['period_end'],
                    'issue_date' => date('Y-m-d'),
                    'due_date' => date('Y-m-d', strtotime('+30 days')),
                    'subtotal' => $subtotal,
                    'discount_amount' => 0,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'status' => $options['auto_approve'] ?? false ? 'APPROVED' : 'DRAFT',
                    'notes' => "{$periodLabel} {$group['period_start']} to {$group['period_end']}. Created via auto-detection.",
                    'created_by' => $userId
                ];
                
                if ($this->invoiceModel->insert($invoiceData)) {
                    $invoiceId = $this->invoiceModel->getInsertID();
                    
                    // Add invoice items (per unit)
                    $this->addInvoiceItems($invoiceId, $group['units']);
                    
                    // Update unit_billing_schedules
                    $this->updateBillingSchedules($group['units'], $group['period_end']);
                    
                    $result['created_invoices'][] = [
                        'invoice_id' => $invoiceId,
                        'invoice_number' => $invoiceNumber,
                        'amount' => $totalAmount,
                        'period' => "{$group['period_start']} to {$group['period_end']}"
                    ];
                    
                    $result['total_amount'] += $totalAmount;
                }
            } catch (\Exception $e) {
                log_message('error', 'BackBillingService: Failed to create invoice - ' . $e->getMessage());
                $result['errors'][] = "Period {$group['period_start']}: " . $e->getMessage();
            }
        }
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            $result['success'] = false;
            $result['errors'][] = 'Transaction failed: Database error during back-billing generation';
        } else {
            $result['success'] = true;
        }
        
        return $result;
    }
    
    /**
     * Group missing invoices by billing period
     * 
     * @param array $missingInvoices Array of missing invoice data
     * @return array Grouped periods
     */
    protected function groupByPeriod(array $missingInvoices): array
    {
        $groups = [];
        
        foreach ($missingInvoices as $invoice) {
            $key = $invoice['period_start'] . '_' . $invoice['period_end'];
            
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'period_start' => $invoice['period_start'],
                    'period_end'   => $invoice['period_end'],
                    'customer_id'  => $invoice['contract_id'], // Will be fetched from contract
                    'rental_type'  => $invoice['rental_type'] ?? 'CONTRACT',
                    'units'        => []
                ];
            }
            
            $groups[$key]['units'][] = $invoice;
        }
        
        return array_values($groups);
    }
    
    /**
     * Add invoice items for back-billing
     * 
     * @param int $invoiceId Invoice ID
     * @param array $units Array of unit data
     * @return void
     */
    protected function addInvoiceItems(int $invoiceId, array $units): void
    {
        $invoiceItemModel = new \App\Models\InvoiceItemModel();
        
        foreach ($units as $unit) {
            $rentalType = $unit['rental_type'] ?? 'CONTRACT';
            $invoiceItemModel->insert([
                'invoice_id' => $invoiceId,
                'item_type' => 'RENTAL',
                'unit_id' => $unit['unit_id'],
                'description' => "Rental ({$rentalType}) - {$unit['unit_number']} ({$unit['billing_method']})",
                'quantity' => 1,
                'unit_price' => $unit['estimated_amount'],
                'subtotal' => $unit['estimated_amount'],
                'tax_amount' => $unit['estimated_amount'] * 0.11,
                'total_amount' => $unit['estimated_amount'] * 1.11
            ]);
        }
    }
    
    /**
     * Update unit billing schedules after back-billing creation
     * 
     * @param array $units Array of unit data
     * @param string $periodEnd Period end date
     * @return void
     */
    protected function updateBillingSchedules(array $units, string $periodEnd): void
    {
        $scheduleTable = $this->db->table('unit_billing_schedules');
        
        foreach ($units as $unit) {
            $scheduleTable->where('id', $unit['schedule_id'])
                         ->update([
                             'last_billed_date' => $periodEnd,
                             'next_billing_date' => date('Y-m-d', strtotime($periodEnd . ' +1 day')),
                             'updated_at' => date('Y-m-d H:i:s')
                         ]);
        }
    }
    
    /**
     * Get back-billing statistics
     * 
     * @return array Statistics data
     */
    public function getStatistics(): array
    {
        $missing = $this->detectMissingInvoices();
        
        $stats = [
            'total_missing' => count($missing),
            'total_contracts_affected' => count(array_unique(array_column($missing, 'contract_id'))),
            'total_estimated_amount' => array_sum(array_column($missing, 'estimated_amount')),
            'oldest_overdue_days' => !empty($missing) ? max(array_column($missing, 'days_overdue')) : 0,
            'by_billing_method' => []
        ];
        
        // Group by billing method
        foreach ($missing as $invoice) {
            $method = $invoice['billing_method'];
            if (!isset($stats['by_billing_method'][$method])) {
                $stats['by_billing_method'][$method] = [
                    'count' => 0,
                    'amount' => 0
                ];
            }
            $stats['by_billing_method'][$method]['count']++;
            $stats['by_billing_method'][$method]['amount'] += $invoice['estimated_amount'];
        }
        
        return $stats;
    }
}
