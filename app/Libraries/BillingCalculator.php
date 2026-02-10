<?php

namespace App\Libraries;

use DateTime;
use Exception;

/**
 * Billing Calculator Library
 * Handles different billing methods: CYCLE, PRORATE, MONTHLY_FIXED
 * Supports staggered delivery and mid-period amendments
 */
class BillingCalculator
{
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Calculate billing based on contract method
     * 
     * @param int $contractId Contract ID
     * @param string $periodStart Billing period start date (Y-m-d)
     * @param string $periodEnd Billing period end date (Y-m-d)
     * @return array Billing calculation result
     */
    public function calculate($contractId, $periodStart, $periodEnd)
    {
        $contract = $this->getContract($contractId);
        
        if (!$contract) {
            throw new Exception('Contract not found');
        }
        
        // Check for mid-period amendment
        $amendment = $this->checkAmendmentInPeriod($contractId, $periodStart, $periodEnd);
        
        if ($amendment) {
            return $this->calculateAmendmentSplit($contractId, $periodStart, $periodEnd, $amendment);
        }
        
        // Normal billing based on method
        switch ($contract['billing_method']) {
            case 'CYCLE':
                return $this->calculateCycleBilling($contract, $periodStart, $periodEnd);
            
            case 'PRORATE':
                return $this->calculateProrateBilling($contract, $periodStart, $periodEnd);
            
            case 'MONTHLY_FIXED':
                return $this->calculateFixedMonthlyBilling($contract, $periodStart, $periodEnd);
            
            default:
                throw new Exception('Invalid billing method: ' . $contract['billing_method']);
        }
    }
    
    /**
     * Get contract details
     */
    protected function getContract($contractId)
    {
        return $this->db->table('kontrak')
                        ->select('*, 
                                 (nilai_total / NULLIF(total_units, 0)) as unit_rate')
                        ->where('id', $contractId)
                        ->get()
                        ->getRowArray();
    }
    
    /**
     * Cycle Billing: 30-day rolling periods
     */
    protected function calculateCycleBilling($contract, $periodStart, $periodEnd)
    {
        $startDate = new DateTime($periodStart);
        $endDate = new DateTime($periodEnd);
        $daysDiff = $startDate->diff($endDate)->days + 1; // Include end date
        
        // Monthly rate per unit
        $monthlyRate = $contract['unit_rate'];
        
        // Calculate amount based on 30-day cycle
        $amount = ($daysDiff / 30) * $monthlyRate * $contract['total_units'];
        
        return [
            'method' => 'CYCLE',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'days' => $daysDiff,
            'monthly_rate' => $monthlyRate,
            'total_units' => $contract['total_units'],
            'amount' => round($amount, 2),
            'note' => "30-day cycle billing ({$daysDiff} days)"
        ];
    }
    
    /**
     * Prorate Billing: First month prorated, then 1st of month
     */
    protected function calculateProrateBilling($contract, $periodStart, $periodEnd)
    {
        $startDate = new DateTime($periodStart);
        $endDate = new DateTime($periodEnd);
        
        // Check if this is first billing period
        $contractStart = new DateTime($contract['billing_start_date'] ?? $contract['tanggal_mulai']);
        $isFirstPeriod = ($startDate->format('Y-m-d') === $contractStart->format('Y-m-d'));
        
        $monthlyRate = $contract['unit_rate'] * $contract['total_units'];
        
        if ($isFirstPeriod) {
            // Prorate from start date to end of month
            $endOfMonth = new DateTime($startDate->format('Y-m-t'));
            $daysInMonth = (int)$startDate->format('t');
            $daysRemaining = $startDate->diff($endOfMonth)->days + 1;
            
            $prorateAmount = ($daysRemaining / $daysInMonth) * $monthlyRate;
            
            return [
                'method' => 'PRORATE',
                'period_start' => $startDate->format('Y-m-d'),
                'period_end' => $endOfMonth->format('Y-m-d'),
                'days' => $daysRemaining,
                'days_in_month' => $daysInMonth,
                'is_prorate' => true,
                'monthly_rate' => $monthlyRate,
                'amount' => round($prorateAmount, 2),
                'note' => "Prorated first month ({$daysRemaining}/{$daysInMonth} days)"
            ];
        } else {
            // Full month billing (1st to last day)
            $daysInPeriod = $startDate->diff($endDate)->days + 1;
            
            return [
                'method' => 'PRORATE',
                'period_start' => $startDate->format('Y-m-d'),
                'period_end' => $endDate->format('Y-m-d'),
                'days' => $daysInPeriod,
                'is_prorate' => false,
                'monthly_rate' => $monthlyRate,
                'amount' => round($monthlyRate, 2),
                'note' => 'Full month billing'
            ];
        }
    }
    
    /**
     * Fixed Monthly Billing: Always bill on specific date
     */
    protected function calculateFixedMonthlyBilling($contract, $periodStart, $periodEnd)
    {
        $startDate = new DateTime($periodStart);
        $endDate = new DateTime($periodEnd);
        $daysDiff = $startDate->diff($endDate)->days + 1;
        
        $monthlyRate = $contract['unit_rate'] * $contract['total_units'];
        
        return [
            'method' => 'MONTHLY_FIXED',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'days' => $daysDiff,
            'monthly_rate' => $monthlyRate,
            'amount' => round($monthlyRate, 2),
            'note' => 'Fixed monthly billing' . ($contract['billing_notes'] ? ': ' . $contract['billing_notes'] : '')
        ];
    }
    
    /**
     * Check if there's active amendment in period
     */
    public function checkAmendmentInPeriod($contractId, $periodStart, $periodEnd)
    {
        $amendment = $this->db->table('contract_amendments')
                              ->where('parent_contract_id', $contractId)
                              ->where('effective_date >=', $periodStart)
                              ->where('effective_date <=', $periodEnd)
                              ->where('status', 'APPROVED')
                              ->whereIn('amendment_type', ['PRICE_CHANGE', 'QUANTITY_CHANGE'])
                              ->get()
                              ->getRowArray();
        
        return $amendment ? $amendment : null;
    }
    
    /**
     * Calculate prorate split for mid-period amendment
     */
    public function calculateAmendmentSplit($contractId, $periodStart, $periodEnd, $amendment)
    {
        $effectiveDate = new DateTime($amendment['effective_date']);
        $startDate = new DateTime($periodStart);
        $endDate = new DateTime($periodEnd);
        
        // Days before and after change
        $daysBeforeChange = $startDate->diff($effectiveDate)->days;
        $daysAfterChange = $effectiveDate->diff($endDate)->days;
        $totalDays = $startDate->diff($endDate)->days + 1;
        
        // Get old and new pricing
        $oldValue = json_decode($amendment['old_value'], true);
        $newValue = json_decode($amendment['new_value'], true);
        
        $oldPrice = $oldValue['monthly_rate'] ?? $oldValue['nilai_total'];
        $newPrice = $newValue['monthly_rate'] ?? $newValue['nilai_total'];
        
        // Calculate split amounts
        $amountOld = ($daysBeforeChange / $totalDays) * $oldPrice;
        $amountNew = ($daysAfterChange / $totalDays) * $newPrice;
        
        return [
            'has_amendment' => true,
            'amendment_id' => $amendment['id'],
            'amendment_number' => $amendment['amendment_number'],
            'effective_date' => $amendment['effective_date'],
            'split_billing' => [
                [
                    'period' => $startDate->format('Y-m-d') . ' to ' . $effectiveDate->modify('-1 day')->format('Y-m-d'),
                    'days' => $daysBeforeChange,
                    'rate' => $oldPrice,
                    'amount' => round($amountOld, 2),
                    'note' => 'Before amendment'
                ],
                [
                    'period' => $amendment['effective_date'] . ' to ' . $endDate->format('Y-m-d'),
                    'days' => $daysAfterChange,
                    'rate' => $newPrice,
                    'amount' => round($amountNew, 2),
                    'note' => 'After amendment (new rate)'
                ]
            ],
            'total_amount' => round($amountOld + $amountNew, 2),
            'note' => "Split billing due to amendment #{$amendment['amendment_number']}"
        ];
    }
    
    /**
     * Calculate next billing period based on method
     */
    public function calculateNextPeriod($contractId, $lastBillingEnd)
    {
        $contract = $this->getContract($contractId);
        $lastEnd = new DateTime($lastBillingEnd);
        
        switch ($contract['billing_method']) {
            case 'CYCLE':
                $nextStart = (clone $lastEnd)->modify('+1 day');
                $nextEnd = (clone $nextStart)->modify('+30 days')->modify('-1 day');
                break;
                
            case 'PRORATE':
                $nextStart = new DateTime($lastEnd->format('Y-m-01'));
                $nextStart->modify('+1 month');
                $nextEnd = new DateTime($nextStart->format('Y-m-t'));
                break;
                
            case 'MONTHLY_FIXED':
                $nextStart = (clone $lastEnd)->modify('+1 day');
                $nextEnd = (clone $nextStart)->modify('+1 month')->modify('-1 day');
                break;
                
            default:
                throw new Exception('Invalid billing method');
        }
        
        return [
            'start' => $nextStart->format('Y-m-d'),
            'end' => $nextEnd->format('Y-m-d')
        ];
    }
}
