<?php

namespace App\Models;

use CodeIgniter\Model;

class OptimizedWorkOrderModel extends Model
{
    protected $table = 'work_orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['work_order_number', 'unit_id', 'category_id', 'priority', 'order_type', 'issue_description', 'report_date', 'status_id', 'assigned_to', 'technician_notes', 'completion_date', 'customer_signature', 'technician_signature', 'parts_used', 'labor_hours', 'cost_estimate', 'actual_cost', 'customer_feedback', 'internal_notes', 'attachments', 'created_by', 'updated_by'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    /**
     * Get work orders with optimized queries and pagination
     */
    public function getOptimizedWorkOrders($filters = [], $page = 1, $perPage = 10)
    {
        $builder = $this->db->table($this->table . ' wo');
        
        // Optimized joins with selective fields
        $builder->select([
            'wo.id',
            'wo.work_order_number',
            'wo.complaint_description as issue_description',
            'wo.report_date',
            'wo.completion_date',
            'wo.completion_date as closed_date',
            'wo.order_type',
            'wo.created_at',
            'wo.updated_at',
            'iu.no_unit',
            'mu.merk_unit',
            'mu.model_unit', 
            'COALESCE(c.customer_name, "Belum Ada Kontrak") as pelanggan',
            'woc.category_name as category',
            'wop.priority_name as priority',
            'wop.priority_color',
            'wos.status_name as status',
            'wos.status_code',
            'wos.status_color'
        ]);
        
        // Optimized joins with proper indexes
        $builder->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left');
        $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
        $builder->join('kontrak k', 'iu.kontrak_id = k.id', 'left');
        $builder->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left');
        $builder->join('customers c', 'c.id = cl.customer_id', 'left');
        $builder->join('work_order_categories woc', 'woc.id = wo.category_id', 'left');
        $builder->join('work_order_priorities wop', 'wop.id = wo.priority_id', 'left');
        $builder->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left');
        
        // Exclude soft deleted records
        $builder->where('wo.deleted_at', null);
        
        // Apply filters efficiently
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('wo.complaint_description', $search)
                    ->orLike('iu.no_unit', $search)
                    ->orLike('c.customer_name', $search)
                    ->groupEnd();
        }
        
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'exclude_closed') {
                $builder->where('wos.status_code !=', 'CLOSED');
            } else {
                $builder->where('wos.status_code', $filters['status']);
            }
        }
        
        if (!empty($filters['priority'])) {
            $builder->where('wop.priority_name', $filters['priority']);
        }
        
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(wo.report_date) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(wo.report_date) <=', $filters['end_date']);
        }
        
        if (!empty($filters['month'])) {
            $builder->where('DATE_FORMAT(wo.report_date, "%Y-%m")', $filters['month']);
        }
        
        // Department filter for division-based access
        if (!empty($filters['department_ids'])) {
            $builder->whereIn('iu.departemen_id', $filters['department_ids']);
        }
        
        // Order by latest first for better performance
        $builder->orderBy('wo.id', 'DESC');
        
        // Get total count before applying pagination
        $totalQuery = clone $builder;
        $total = $totalQuery->countAllResults(false);
        
        // Apply pagination
        $offset = ($page - 1) * $perPage;
        $builder->limit($perPage, $offset);
        
        $result = $builder->get()->getResultArray();
        
        return [
            'data' => $result,
            'total' => $total,
            'filtered' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get work order statistics with optimized queries
     */
    public function getOptimizedStats($filters = [])
    {
        $builder = $this->db->table($this->table . ' wo');
        $builder->select([
            'COUNT(*) as total',
            'SUM(CASE WHEN wos.status_code = "OPEN" THEN 1 ELSE 0 END) as open',
            'SUM(CASE WHEN wos.status_code IN ("ASSIGNED", "IN_PROGRESS") THEN 1 ELSE 0 END) as in_progress',
            'SUM(CASE WHEN wos.status_code = "COMPLETED" THEN 1 ELSE 0 END) as completed',
            'SUM(CASE WHEN wos.status_code = "CLOSED" THEN 1 ELSE 0 END) as closed'
        ]);
        
        $builder->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left');
        
        // Exclude soft deleted records
        $builder->where('wo.deleted_at', null);
        
        // Apply same filters as main query
        if (!empty($filters['department_ids'])) {
            $builder->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left');
            $builder->whereIn('iu.departemen_id', $filters['department_ids']);
        }
        
        if (!empty($filters['month'])) {
            $builder->where('DATE_FORMAT(wo.report_date, "%Y-%m")', $filters['month']);
        }
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Search work orders with caching for common queries
     */
    public function searchOptimizedWorkOrders($search = '', $status = '', $priority = '', $startDate = '', $endDate = '', $month = '', $excludeStatus = null)
    {
        // Create cache key for this specific search
        $cacheKey = 'work_orders_' . md5($search . $status . $priority . $startDate . $endDate . $month . $excludeStatus);
        
        // Try to get from cache first (for 5 minutes)
        $cache = \Config\Services::cache();
        $cachedResult = $cache->get($cacheKey);
        
        if ($cachedResult !== null) {
            return $cachedResult;
        }
        
        // Build optimized query
        $builder = $this->db->table($this->table . ' wo');
        
        $builder->select([
            'wo.id',
            'wo.work_order_number',
            'wo.issue_description',
            'wo.report_date',
            'wo.completion_date',
            'wo.order_type',
            'wo.unit_id',
            'iu.no_unit',
            'iu.merk_unit',
            'iu.model_unit',
            'iu.departemen_id',
            'c.nama_customer as pelanggan',
            'wc.nama_kategori as category',
            'wp.nama_prioritas as priority',
            'wp.warna as priority_color',
            'ws.nama_status as status',
            'ws.kode_status as status_code',
            'ws.warna as status_color'
        ]);
        
        $builder->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left');
        $builder->join('customer c', 'c.id = iu.customer_id', 'left');
        $builder->join('work_order_categories wc', 'wc.id = wo.category_id', 'left');
        $builder->join('work_order_priorities wp', 'wp.id = wo.priority', 'left');
        $builder->join('work_order_statuses ws', 'ws.id = wo.status_id', 'left');
        
        // Apply filters
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('wo.issue_description', $search)
                    ->orLike('iu.no_unit', $search)
                    ->orLike('c.nama_customer', $search)
                    ->groupEnd();
        }
        
        if (!empty($status)) {
            $builder->where('ws.kode_status', $status);
        }
        
        if (!empty($excludeStatus)) {
            $builder->where('ws.kode_status !=', $excludeStatus);
        }
        
        if (!empty($priority)) {
            $builder->where('wp.nama_prioritas', $priority);
        }
        
        if (!empty($startDate)) {
            $builder->where('DATE(wo.report_date) >=', $startDate);
        }
        
        if (!empty($endDate)) {
            $builder->where('DATE(wo.report_date) <=', $endDate);
        }
        
        if (!empty($month)) {
            $builder->where('DATE_FORMAT(wo.report_date, "%Y-%m")', $month);
        }
        
        $builder->orderBy('wo.id', 'DESC');
        
        $result = $builder->get()->getResultArray();
        
        // Cache the result for 5 minutes
        $cache->save($cacheKey, $result, 300);
        
        return $result;
    }
}