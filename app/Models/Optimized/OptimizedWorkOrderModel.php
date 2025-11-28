<?php

namespace App\Models\Optimized;

use App\Models\WorkOrderModel;
use CodeIgniter\Database\BaseBuilder;

/**
 * Optimized WorkOrder Model with JOIN optimization
 * Menggantikan multiple JOIN dengan database views dan subqueries untuk performance
 */
class OptimizedWorkOrderModel extends WorkOrderModel
{
    /**
     * Get work orders for DataTable with optimized queries
     * Menggunakan view database dan lazy loading untuk mengurangi kompleksitas JOIN
     */
    public function getWorkOrdersForDataTableOptimized($search = '', $status = '', $excludeStatus = [], $startDate = '', $endDate = '')
    {
        $db = \Config\Database::connect();
        
        // Gunakan view yang sudah dibuat di Phase 2 jika ada, atau buat query yang lebih efisien
        if ($this->hasView('v_work_orders_summary')) {
            $builder = $db->table('v_work_orders_summary wo');
        } else {
            // Fallback ke optimized base query
            $builder = $this->getOptimizedBaseQuery();
        }

        // Apply filters dengan index yang tepat
        $this->applyFiltersOptimized($builder, $search, $status, $excludeStatus, $startDate, $endDate);

        return $builder;
    }

    /**
     * Base query yang dioptimasi dengan minimal JOIN
     */
    protected function getOptimizedBaseQuery()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('work_orders wo');

        // SELECT hanya field yang diperlukan dengan subqueries untuk lookup
        $builder->select('
            wo.id,
            wo.work_order_number,
            wo.unit_id,
            wo.complaint_description,
            wo.work_description,
            wo.start_date,
            wo.due_date,
            wo.completion_date,
            wo.created_at,
            wo.updated_at,
            
            (SELECT wos.status_name FROM work_order_statuses wos WHERE wos.id = wo.status_id) as status_name,
            (SELECT wop.priority_name FROM work_order_priorities wop WHERE wop.id = wo.priority_id) as priority_name,
            (SELECT woc.category_name FROM work_order_categories woc WHERE woc.id = wo.category_id) as category_name,
            (SELECT wosc.subcategory_name FROM work_order_subcategories wosc WHERE wosc.id = wo.subcategory_id) as subcategory_name,
            
            iu.no_unit,
            iu.lokasi_unit,
            
            CASE 
                WHEN EXISTS(SELECT 1 FROM kontrak k 
                           JOIN customer_locations cl ON k.customer_location_id = cl.id
                           JOIN customers c ON cl.customer_id = c.id 
                           WHERE k.id = iu.kontrak_id)
                THEN (SELECT c.customer_name FROM kontrak k 
                      JOIN customer_locations cl ON k.customer_location_id = cl.id
                      JOIN customers c ON cl.customer_id = c.id 
                      WHERE k.id = iu.kontrak_id LIMIT 1)
                ELSE "Belum Ada Kontrak"
            END as pelanggan,
            
            (SELECT staff_name FROM employees WHERE id = wo.admin_id) as admin_staff,
            (SELECT staff_name FROM employees WHERE id = wo.foreman_id) as foreman_staff,
            (SELECT staff_name FROM employees WHERE id = wo.mechanic_id) as mechanic_staff,
            (SELECT staff_name FROM employees WHERE id = wo.helper_id) as helper_staff
        ');

        // Minimal JOIN hanya untuk yang benar-benar diperlukan
        $builder->join('inventory_unit iu', 'wo.unit_id = iu.id_inventory_unit', 'left');
        
        // Filter soft deleted
        $builder->where('wo.deleted_at', null);

        return $builder;
    }

    /**
     * Apply filters dengan optimasi index
     */
    protected function applyFiltersOptimized($builder, $search, $status, $excludeStatus, $startDate, $endDate)
    {
        // Search dengan index yang tepat
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('wo.complaint_description', $search)
                    ->orLike('iu.no_unit', $search)
                    ->groupEnd();
        }

        // Status filter dengan index
        if (!empty($status)) {
            if (is_numeric($status)) {
                $builder->where('wo.status_id', $status);
            }
        }

        // Exclude status dengan NOT IN yang lebih efisien
        if (!empty($excludeStatus)) {
            if (is_array($excludeStatus)) {
                $builder->whereNotIn('wo.status_id', $excludeStatus);
            } else {
                $builder->where('wo.status_id !=', $excludeStatus);
            }
        }

        // Date range dengan index pada created_at
        if (!empty($startDate)) {
            $builder->where('wo.created_at >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('wo.created_at <=', $endDate);
        }
    }

    /**
     * Get work order details dengan lazy loading
     */
    public function getWorkOrderDetailsOptimized($id)
    {
        $cache = \Config\Services::cache();
        $cacheKey = "work_order_detail_{$id}";
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        // Base work order
        $workOrder = $this->find($id);
        if (!$workOrder) {
            return null;
        }

        // Lazy load related data hanya jika diperlukan
        $workOrder['status'] = $this->getStatusById($workOrder['status_id']);
        $workOrder['priority'] = $this->getPriorityById($workOrder['priority_id']);
        $workOrder['unit'] = $this->getUnitById($workOrder['unit_id']);
        
        // Cache result selama 15 menit
        $cache->save($cacheKey, $workOrder, 900);
        
        return $workOrder;
    }

    /**
     * Helper methods untuk lazy loading
     */
    protected function getStatusById($statusId)
    {
        $db = \Config\Database::connect();
        return $db->table('work_order_statuses')
                  ->where('id', $statusId)
                  ->get()
                  ->getRowArray();
    }

    protected function getPriorityById($priorityId)
    {
        $db = \Config\Database::connect();
        return $db->table('work_order_priorities')
                  ->where('id', $priorityId)
                  ->get()
                  ->getRowArray();
    }

    protected function getUnitById($unitId)
    {
        $db = \Config\Database::connect();
        return $db->table('inventory_unit')
                  ->where('id_inventory_unit', $unitId)
                  ->get()
                  ->getRowArray();
    }

    /**
     * Check if database view exists
     */
    protected function hasView($viewName)
    {
        $db = \Config\Database::connect();
        try {
            $result = $db->query("SHOW TABLES LIKE '{$viewName}'")->getResult();
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create optimized database view untuk work orders
     */
    public function createWorkOrderView()
    {
        $db = \Config\Database::connect();
        
        $sql = "
        CREATE OR REPLACE VIEW v_work_orders_summary AS
        SELECT 
            wo.id,
            wo.work_order_number,
            wo.unit_id,
            wo.complaint_description,
            wo.work_description,
            wo.start_date,
            wo.due_date,
            wo.completion_date,
            wo.created_at,
            wo.updated_at,
            wo.status_id,
            wo.priority_id,
            wo.category_id,
            wo.subcategory_id,
            wo.admin_id,
            wo.foreman_id,
            wo.mechanic_id,
            wo.helper_id,
            
            wos.status_name,
            wop.priority_name,
            woc.category_name,
            wosc.subcategory_name,
            
            iu.no_unit,
            iu.lokasi_unit,
            
            COALESCE(c.customer_name, 'Belum Ada Kontrak') as pelanggan,
            COALESCE(cl.location_name, iu.lokasi_unit, 'Lokasi Tidak Diketahui') as lokasi,
            COALESCE(mu.merk_unit, 'Unknown') as merk_unit,
            COALESCE(mu.model_unit, 'Unknown') as model_unit,
            
            admin.staff_name as admin_staff,
            foreman.staff_name as foreman_staff,
            mechanic.staff_name as mechanic_staff,
            helper.staff_name as helper_staff
            
        FROM work_orders wo
        LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
        LEFT JOIN work_order_priorities wop ON wo.priority_id = wop.id
        LEFT JOIN work_order_categories woc ON wo.category_id = woc.id
        LEFT JOIN work_order_subcategories wosc ON wo.subcategory_id = wosc.id
        LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
        LEFT JOIN kontrak k ON iu.kontrak_id = k.id
        LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
        LEFT JOIN customers c ON c.id = cl.customer_id
        LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
        LEFT JOIN employees admin ON wo.admin_id = admin.id
        LEFT JOIN employees foreman ON wo.foreman_id = foreman.id
        LEFT JOIN employees mechanic ON wo.mechanic_id = mechanic.id
        LEFT JOIN employees helper ON wo.helper_id = helper.id
        WHERE wo.deleted_at IS NULL
        ";

        try {
            $db->query($sql);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to create work order view: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get work orders dengan pagination yang optimized
     */
    public function getWorkOrdersPaginated($page = 1, $perPage = 10, $filters = [])
    {
        $offset = ($page - 1) * $perPage;
        
        $builder = $this->getOptimizedBaseQuery();
        
        // Apply filters
        $this->applyFiltersOptimized(
            $builder, 
            $filters['search'] ?? '', 
            $filters['status'] ?? '', 
            $filters['excludeStatus'] ?? [], 
            $filters['startDate'] ?? '', 
            $filters['endDate'] ?? ''
        );
        
        // Order by dengan index
        $builder->orderBy('wo.created_at', 'DESC');
        
        // Get total count untuk pagination
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);
        
        // Get paginated results
        $results = $builder->limit($perPage, $offset)->get()->getResultArray();
        
        return [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * DataTable optimized method
     */
    public function getDataTableOptimized($params = [])
    {
        $search = $params['search'] ?? '';
        $start = $params['start'] ?? 0;
        $length = $params['length'] ?? 25;
        $orderColumn = $params['orderColumn'] ?? 'wo.created_at';
        $orderDir = $params['orderDir'] ?? 'DESC';
        $conditions = $params['conditions'] ?? [];

        $builder = $this->getOptimizedBaseQuery();
        
        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('wo.complaint_description', $search)
                    ->orLike('iu.no_unit', $search)
                    ->groupEnd();
        }

        // Apply additional conditions
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }

        // Get total count
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);
        
        // Get filtered count
        $filteredBuilder = clone $builder;
        $filtered = $filteredBuilder->countAllResults(false);

        // Apply ordering and pagination
        $builder->orderBy($orderColumn, $orderDir)
               ->limit($length, $start);

        $data = $builder->get()->getResultArray();

        return [
            'data' => $data,
            'total' => $total,
            'filtered' => $filtered
        ];
    }
}