<?php

namespace App\Traits;

/**
 * DateFilterTrait
 * 
 * Global trait for handling date range filtering in DataTables
 * Usage: Add 'use DateFilterTrait;' in your controller
 * Then call: $this->applyDateFilter($builder, 'your_date_column');
 */
trait DateFilterTrait
{
    /**
     * Apply date range filter to query builder
     * 
     * @param object $builder CodeIgniter Query Builder instance
     * @param string $dateColumn The column name to filter (default: 'created_at')
     * @param string $startDateParam POST parameter name for start date (default: 'start_date')
     * @param string $endDateParam POST parameter name for end date (default: 'end_date')
     * @return object Query Builder instance
     */
    protected function applyDateFilter($builder, $dateColumn = 'created_at', $startDateParam = 'start_date', $endDateParam = 'end_date')
    {
        // Get date range from POST or GET request (POST takes priority)
        $startDate = $this->request->getPost($startDateParam) ?: $this->request->getGet($startDateParam);
        $endDate = $this->request->getPost($endDateParam) ?: $this->request->getGet($endDateParam);
        
        // Apply filter if both dates are provided
        if ($startDate && $endDate) {
            $builder->where($dateColumn . ' >=', $startDate);
            $builder->where($dateColumn . ' <=', $endDate);
            
            log_message('info', "DateFilter applied: {$dateColumn} BETWEEN {$startDate} AND {$endDate}");
        } else {
            log_message('info', "DateFilter skipped: No date range provided");
        }
        
        return $builder;
    }
    
    /**
     * Get date filter parameters from request
     * Useful for logging or custom queries
     * 
     * @return array ['start_date' => string|null, 'end_date' => string|null]
     */
    protected function getDateFilterParams()
    {
        return [
            'start_date' => $this->request->getPost('start_date') ?: $this->request->getGet('start_date'),
            'end_date' => $this->request->getPost('end_date') ?: $this->request->getGet('end_date')
        ];
    }
    
    /**
     * Check if date filter is active
     * 
     * @return bool
     */
    protected function hasDateFilter()
    {
        $startDate = $this->request->getPost('start_date') ?: $this->request->getGet('start_date');
        $endDate = $this->request->getPost('end_date') ?: $this->request->getGet('end_date');
        
        return !empty($startDate) && !empty($endDate);
    }
}
