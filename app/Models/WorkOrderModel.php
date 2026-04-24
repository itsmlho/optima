<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderModel extends Model
{
    protected $table = 'work_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'work_order_number',
        'report_date',
        'unit_id',
        'order_type',
        'priority_id',
        'requested_repair_time',
        'category_id',
        'subcategory_id',
        'complaint_description',
        'status_id',
        'admin_id',
        'foreman_id',
        'mechanic_id',
        'helper_id',
        'repair_description',
        'notes',
        'sparepart_used',
        'time_to_repair',
        'completion_date',
        'area',
        'pic',
        'hm',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'unit_id' => 'required|integer',
        'order_type' => 'required|in_list[COMPLAINT,PMPS,FABRIKASI]',
        'priority_id' => 'required|integer',
        'category_id' => 'required|integer',
        'complaint_description' => 'required|min_length[5]',
        'status_id' => 'required|integer',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'unit_id' => [
            'required' => 'Unit harus dipilih',
            'integer' => 'Unit tidak valid'
        ],
        'order_type' => [
            'required' => 'Tipe order harus dipilih',
            'in_list' => 'Tipe order tidak valid'
        ],
        'priority_id' => [
            'required' => 'Priority harus dipilih',
            'integer' => 'Priority tidak valid'
        ],
        'category_id' => [
            'required' => 'Kategori harus dipilih',
            'integer' => 'Kategori tidak valid'
        ],
        'complaint_description' => [
            'required' => 'Deskripsi keluhan harus diisi',
            'min_length' => 'Deskripsi keluhan minimal 5 karakter'
        ],
        'status_id' => [
            'required' => 'Status harus dipilih',
            'integer' => 'Status tidak valid'
        ],
        'created_by' => [
            'required' => 'User yang membuat harus ada',
            'integer' => 'User tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Get work orders with full details using the view
    public function getWorkOrdersWithDetails($filters = [])
    {
        $builder = $this->db->table('vw_work_orders_detail');
        
        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        if (!empty($filters['priority'])) {
            $builder->where('priority', $filters['priority']);
        }
        
        if (!empty($filters['order_type'])) {
            $builder->where('tipe_order', $filters['order_type']);
        }
        
        if (!empty($filters['category'])) {
            $builder->where('kategori', $filters['category']);
        }
        
        if (!empty($filters['area'])) {
            $builder->where('area', $filters['area']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('report_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('report_date <=', $filters['date_to']);
        }
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('work_order_number', $filters['search'])
                    ->orLike('no_unit', $filters['search'])
                    ->orLike('nama_perusahaan', $filters['search'])
                    ->orLike('keluhan_unit', $filters['search'])
                    ->orLike('admin', $filters['search'])
                    ->orLike('mekanik', $filters['search'])
                    ->groupEnd();
        }
        
        // Order by report_date desc by default
        $builder->orderBy('report_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    // Get work order statistics
    public function getWorkOrderStats()
    {
        $stats = $this->db->table('work_orders wo')
            ->select('
                COUNT(CASE WHEN wo.status_id NOT IN (SELECT id FROM work_order_statuses WHERE status_name = "Closed") THEN 1 END) as total_work_orders,
                COUNT(CASE WHEN wo.status_id IN (SELECT id FROM work_order_statuses WHERE status_name = "Open") THEN 1 END) as open_work_orders,
                COUNT(CASE WHEN wo.status_id IN (SELECT id FROM work_order_statuses WHERE status_name = "In Progress") THEN 1 END) as in_progress_work_orders,
                COUNT(CASE WHEN wo.status_id IN (SELECT id FROM work_order_statuses WHERE status_name = "Completed") THEN 1 END) as completed_work_orders
            ')
            ->get()
            ->getRowArray();
            
        return $stats;
    }

    // Get work orders by category statistics
    public function getWorkOrdersByCategory()
    {
        return $this->db->table('vw_work_order_by_category')->get()->getResultArray();
    }

    // Get staff performance data
    public function getStaffPerformance()
    {
        return $this->db->table('vw_staff_performance')->get()->getResultArray();
    }

    // Get overdue work orders
    public function getOverdueWorkOrders()
    {
        return $this->db->table('vw_overdue_work_orders')->get()->getResultArray();
    }

    // Generate new work order number
    public function generateWorkOrderNumber()
    {
        $query = $this->db->query("CALL sp_generate_work_order_number(@work_order_number)");
        $result = $this->db->query("SELECT @work_order_number as work_order_number")->getRowArray();
        return $result['work_order_number'];
    }

    // Update work order status with history
    public function updateWorkOrderStatus($workOrderId, $newStatusId, $changedBy, $changeReason = '')
    {
        try {
            $this->db->query(
                "CALL sp_update_work_order_status(?, ?, ?, ?)",
                [$workOrderId, $newStatusId, $changedBy, $changeReason]
            );
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error updating work order status: ' . $e->getMessage());
            return false;
        }
    }

    // Assign staff to work order
    public function assignStaffToWorkOrder($workOrderId, $adminStaffId, $foremanStaffId, $mechanicStaffId, $helperStaffId, $assignedBy)
    {
        try {
            $this->db->query(
                "CALL sp_assign_staff_to_work_order(?, ?, ?, ?, ?, ?)",
                [$workOrderId, $adminStaffId, $foremanStaffId, $mechanicStaffId, $helperStaffId, $assignedBy]
            );
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error assigning staff to work order: ' . $e->getMessage());
            return false;
        }
    }

    // Complete work order
    public function completeWorkOrder($workOrderId, $repairDescription, $sparepartUsed, $timeToRepair, $completedBy)
    {
        try {
            $this->db->query(
                "CALL sp_complete_work_order(?, ?, ?, ?, ?)",
                [$workOrderId, $repairDescription, $sparepartUsed, $timeToRepair, $completedBy]
            );
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error completing work order: ' . $e->getMessage());
            return false;
        }
    }

    // Get work order categories
    public function getCategories()
    {
        return $this->db->table('work_order_categories')
                       ->where('is_active', 1)
                       ->orderBy('category_name')
                       ->get()
                       ->getResultArray();
    }

    // Get subcategories by category
    public function getSubcategoriesByCategory($categoryId)
    {
        return $this->db->table('work_order_subcategories')
                       ->where('category_id', $categoryId)
                       ->where('is_active', 1)
                       ->orderBy('subcategory_name')
                       ->get()
                       ->getResultArray();
    }
    
    // Alias for backward compatibility
    public function getSubcategories($categoryId)
    {
        return $this->getSubcategoriesByCategory($categoryId);
    }

    // Get work order priorities
    public function getPriorities()
    {
        return $this->db->table('work_order_priorities')
                       ->where('is_active', 1)
                       ->orderBy('priority_level', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // Get work order statuses
    public function getStatuses()
    {
        return $this->db->table('work_order_statuses')
                       ->where('is_active', 1)
                       ->orderBy('sort_order')
                       ->get()
                       ->getResultArray();
    }

    // Get employees by role
    public function getStaffByRole($role = null)
    {
        $builder = $this->db->table('employees')
                           ->where('is_active', 1);
        
        if ($role) {
            $builder->where('staff_role', $role);
        }
        
        return $builder->orderBy('staff_name')
                      ->get()
                      ->getResultArray();
    }

    // Get units for work order assignment
    public function getAvailableUnits($search = '')
    {
        $builder = $this->db->table('inventory_unit iu')
                           ->select('iu.id_inventory_unit as id, iu.no_unit, mu.merk_unit, mu.model_unit, tu.tipe, kap.kapasitas_unit, c.customer_name as pelanggan')
                           ->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left')
                           ->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left')
                           ->join('kapasitas kap', 'iu.kapasitas_unit_id = kap.id_kapasitas', 'left')
                           ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN (\'ACTIVE\',\'TEMP_ACTIVE\') AND ku.is_temporary = 0', 'left')
                           ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                           ->join('customers c', 'c.id = k.customer_id', 'left')
                           ->where('iu.status_unit', 'AVAILABLE'); // Only available units
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('iu.no_unit', $search)
                    ->orLike('mu.merk_unit', $search)
                    ->orLike('mu.model_unit', $search)
                    ->orLike('c.customer_name', $search)
                    ->groupEnd();
        }
        
        return $builder->orderBy('iu.no_unit')
                      ->limit(50)
                      ->get()
                      ->getResultArray();
    }

    // Get work order history
    public function getWorkOrderHistory($workOrderId)
    {
        return $this->db->table('work_order_status_history wosh')
                       ->select('wosh.*, wos_from.status_name as from_status, wos_to.status_name as to_status, u.first_name, u.last_name')
                       ->join('work_order_statuses wos_from', 'wosh.from_status_id = wos_from.id', 'left')
                       ->join('work_order_statuses wos_to', 'wosh.to_status_id = wos_to.id', 'left')
                       ->join('users u', 'wosh.changed_by = u.id', 'left')
                       ->where('wosh.work_order_id', $workOrderId)
                       ->orderBy('wosh.changed_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // Get work order comments
    public function getWorkOrderComments($workOrderId)
    {
        return $this->db->table('work_order_comments woc')
                       ->select('woc.*, u.first_name, u.last_name')
                       ->join('users u', 'woc.created_by = u.id', 'left')
                       ->where('woc.work_order_id', $workOrderId)
                       ->orderBy('woc.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // Add comment to work order
    public function addComment($workOrderId, $commentText, $commentType, $isInternal, $createdBy)
    {
        return $this->db->table('work_order_comments')->insert([
            'work_order_id' => $workOrderId,
            'comment_text' => $commentText,
            'comment_type' => $commentType,
            'is_internal' => $isInternal,
            'created_by' => $createdBy
        ]);
    }

    /**
     * Search work orders for DataTable
     */
    public function searchWorkOrders($search = '', $status = null, $priority = null, $startDate = null, $endDate = null, $month = null, $excludeStatus = null)
    {
        $builder = $this->db->table($this->table . ' wo');
        $builder->select('wo.*, 
                          wos.status_name as status, 
                          wos.status_code,
                          wos.status_color,
                          wop.priority_name as priority, 
                          wop.priority_color,
                          woc.category_name as category,
                          wosc.subcategory_name as subcategory,
                          iu.no_unit,
                          COALESCE(c.customer_name, "Belum Ada Kontrak") as pelanggan,
                          COALESCE(cl.location_name, iu.lokasi_unit, "Lokasi Tidak Diketahui") as lokasi,
                          COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                          COALESCE(mu.model_unit, "Unknown") as model_unit,
                          admin.staff_name as admin_staff,
                          foreman.staff_name as foreman_staff,
                          mechanic.staff_name as mechanic_staff,
                          helper.staff_name as helper_staff,
                          wo.completion_date as closed_date')
                ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
                ->join('work_order_priorities wop', 'wo.priority_id = wop.id', 'left')
                ->join('work_order_categories woc', 'wo.category_id = woc.id', 'left')
                ->join('work_order_subcategories wosc', 'wo.subcategory_id = wosc.id', 'left')
                ->join('inventory_unit iu', 'wo.unit_id = iu.id_inventory_unit', 'left')
                ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN (\'ACTIVE\',\'TEMP_ACTIVE\') AND ku.is_temporary = 0', 'left')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left')
                ->join('employees admin', 'wo.admin_id = admin.id', 'left')
                ->join('employees foreman', 'wo.foreman_id = foreman.id', 'left')
                ->join('employees mechanic', 'wo.mechanic_id = mechanic.id', 'left')
                ->join('employees helper', 'wo.helper_id = helper.id', 'left');

        // Exclude soft deleted records
        $builder->where('wo.deleted_at', null);

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('wo.complaint_description', $search)
                    ->orLike('iu.no_unit', $search)
                    ->orLike('c.customer_name', $search)
                    ->orLike('wos.status_name', $search)
                    ->groupEnd();
        }

        // Apply status filter
        if (!empty($status)) {
            if (is_numeric($status)) {
                $builder->where('wo.status_id', $status);
            } else {
                $builder->where('wos.status_name', $status);
            }
        }

        // Apply exclude status filter (for progress tab)
        if (!empty($excludeStatus)) {
            if (is_numeric($excludeStatus)) {
                $builder->where('wo.status_id !=', $excludeStatus);
            } else {
                $builder->where('wos.status_name !=', $excludeStatus);
            }
        }

        // Apply priority filter
        if (!empty($priority)) {
            if (is_numeric($priority)) {
                $builder->where('wo.priority_id', $priority);
            } else {
                $builder->where('wop.priority_name', $priority);
            }
        }

        // Apply date range filter
        if (!empty($startDate)) {
            $builder->where('DATE(wo.report_date) >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('DATE(wo.report_date) <=', $endDate);
        }

        // Apply month filter (for closed work orders)
        if (!empty($month)) {
            $builder->where('MONTH(wo.completion_date)', $month);
        }

        $builder->orderBy('wo.report_date', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get all work orders
     */
    public function getAllWorkOrders()
    {
        return $this->searchWorkOrders();
    }

    /**
     * Count all work orders
     */
    public function countAllWorkOrders($tab = null)
    {
        $builder = $this->db->table($this->table . ' wo');
        $builder->select('COUNT(*) as count')
                ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
                ->where('wo.deleted_at', null);
        
        if ($tab === 'closed') {
            $builder->where('wos.status_name', 'Closed');
        } elseif ($tab === 'progress') {
            $builder->where('wos.status_name !=', 'Closed');
        }
        
        $result = $builder->get()->getRowArray();
        return $result['count'] ?? 0;
    }

    /**
     * Count work orders by status
     */
    public function countByStatus($status)
    {
        $builder = $this->db->table($this->table . ' wo');
        $builder->select('COUNT(*) as count')
                ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
                ->where('wo.deleted_at', null)
                ->groupStart()
                    ->where('wos.status_name', $status)
                    ->orWhere('wos.status_code', $status)
                ->groupEnd();
        
        $result = $builder->get()->getRowArray();
        return $result['count'] ?? 0;
    }

    /**
     * Get staff by role
     */
    public function getStaff($role)
    {
        return $this->db->table('employees')
                       ->where('staff_role', $role)
                       ->where('is_active', 1)
                       ->orderBy('staff_name', 'ASC')
                       ->get()
                       ->getResultArray();
    }
    
    /**
     * Get status by status code
     */
    public function getStatusByCode($statusCode)
    {
        return $this->db->table('work_order_statuses')
                       ->where('status_code', $statusCode)
                       ->get()
                       ->getRowArray();
    }
    
    /**
     * Add activity log for work order
     */
    public function addActivityLog($workOrderId, $action, $notes)
    {
        // Check if activity log table exists, if not, just return true
        if (!$this->db->tableExists('work_order_activity_log')) {
            return true;
        }
        
        return $this->db->table('work_order_activity_log')->insert([
            'work_order_id' => $workOrderId,
            'action' => $action,
            'notes' => $notes,
            'created_by' => 1, // Replace with actual user ID
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get detailed work order information for view modal
     */
    public function getDetailWorkOrder($id)
    {
        // Use raw query for better control over derived table joins
        $query = "
            SELECT wo.*, 
                  iu.no_unit as unit_number, 
                  iu.tahun_unit as unit_year, 
                  iu.serial_number as unit_serial, 
                  iu.lokasi_unit as unit_location,
                  iu.aksesoris as unit_accessories,
                  iu.sn_mesin as unit_engine_sn,
                  iu.sn_mast as unit_mast_sn,
                  iu.tinggi_mast as unit_mast_height,
                  iu.hour_meter as unit_hour_meter,
                  mu.merk_unit as unit_brand, 
                  mu.model_unit,
                  COALESCE(CONCAT(tu.tipe, ' ', tu.jenis), 'Unknown') as unit_type,
                  kap.kapasitas_unit as unit_capacity,
                  c.customer_name as unit_customer,
                  a.area_name as unit_area_name,
                  area_admin.staff_name as area_admin_name,
                  area_admin.phone as area_admin_phone,
                  CONCAT_WS(' - ', area_admin.staff_name, area_admin.phone) as area_pic,
                  sus.status_unit as unit_status,
                  d.nama_departemen as unit_departemen,
                  m.model_mesin as unit_engine,
                  tm.tipe_mast as unit_mast,
                  wop.priority_name, wop.priority_color,
                  woc.category_name, wosc.subcategory_name,
                  wos.status_name, wos.status_color, wos.status_code,
                  admin_emp.staff_name as admin_staff_name,
                  foreman_emp.staff_name as foreman_staff_name,  
                  mechanic_emp.staff_name as mechanic_staff_name,
                  helper_emp.staff_name as helper_staff_name,
                  wo.pic, wo.hm
            FROM work_orders wo
            LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
            LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
            LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
            LEFT JOIN kapasitas kap ON iu.kapasitas_unit_id = kap.id_kapasitas
            LEFT JOIN mesin m ON iu.model_mesin_id = m.id
            LEFT JOIN tipe_mast tm ON iu.model_mast_id = tm.id_mast
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
            LEFT JOIN kontrak k ON ku.kontrak_id = k.id
            LEFT JOIN customers c ON c.id = k.customer_id
            LEFT JOIN areas a ON iu.area_id = a.id
            LEFT JOIN (
                SELECT aea.area_id, e.id, e.staff_name, e.phone,
                       ROW_NUMBER() OVER (PARTITION BY aea.area_id ORDER BY 
                           CASE aea.assignment_type WHEN 'PRIMARY' THEN 0 ELSE 1 END,
                           aea.start_date ASC,
                           e.id ASC
                       ) as rn
                FROM area_employee_assignments aea
                JOIN employees e ON aea.employee_id = e.id
                WHERE aea.is_active = 1 
                  AND e.staff_role LIKE '%ADMIN%'
                  AND e.is_active = 1
            ) area_admin ON area_admin.area_id = iu.area_id AND area_admin.rn = 1
            LEFT JOIN status_unit sus ON iu.status_unit_id = sus.id_status
            LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
            LEFT JOIN work_order_priorities wop ON wo.priority_id = wop.id
            LEFT JOIN work_order_categories woc ON wo.category_id = woc.id
            LEFT JOIN work_order_subcategories wosc ON wo.subcategory_id = wosc.id
            LEFT JOIN work_order_statuses wos ON wo.status_id = wos.id
            LEFT JOIN employees admin_emp ON wo.admin_id = admin_emp.id
            LEFT JOIN employees foreman_emp ON wo.foreman_id = foreman_emp.id
            LEFT JOIN employees mechanic_emp ON wo.mechanic_id = mechanic_emp.id
            LEFT JOIN employees helper_emp ON wo.helper_id = helper_emp.id
            WHERE wo.id = ?
        ";
        
        $result = $this->db->query($query, [$id])->getRowArray();
            
        if ($result) {
            // Format unit info for legacy compatibility
            $result['unit_info'] = $result['unit_number'] . ' - ' . $result['unit_customer'] . ' (' . $result['unit_brand'] . ' ' . $result['model_unit'] . ')';
            
            // Format status badge
            $result['status_badge'] = '<span class="badge bg-'.$result['status_color'].'">'.$result['status_name'].'</span>';
            
            // Format priority badge
            $result['priority_badge'] = '<span class="badge bg-'.$result['priority_color'].'">'.$result['priority_name'].'</span>';
            
            // Store hour_meter for frontend use
            $result['hour_meter'] = $result['unit_hour_meter'] ?? $result['hm'] ?? null;
            
            // Ensure unit fields have fallback values
            $result['unit_number'] = $result['unit_number'] ?? '-';
            $result['unit_brand'] = $result['unit_brand'] ?? 'Unknown';
            $result['model_unit'] = $result['model_unit'] ?? 'Unknown';
            $result['unit_type'] = $result['unit_type'] ?? 'Unknown';
            $result['unit_capacity'] = $result['unit_capacity'] ?? 'Unknown';
            $result['unit_serial'] = $result['unit_serial'] ?? '-';
            $result['unit_location'] = $result['unit_location'] ?? '-';
            $result['unit_customer'] = $result['unit_customer'] ?? 'Belum Ada Kontrak';
            $result['unit_status'] = $result['unit_status'] ?? 'Unknown';
            $result['unit_departemen'] = $result['unit_departemen'] ?? '-';
            $result['unit_year'] = $result['unit_year'] ?? '-';
            $result['unit_engine'] = $result['unit_engine'] ?? '-';
            $result['unit_engine_sn'] = $result['unit_engine_sn'] ?? '-';
            $result['unit_mast'] = $result['unit_mast'] ?? '-';
            $result['unit_mast_sn'] = $result['unit_mast_sn'] ?? '-';
            $result['unit_mast_height'] = $result['unit_mast_height'] ?? '-';
            
            // Get unit components (attachments, batteries, chargers)
            $result['unit_attachments'] = $this->getUnitAttachments($result['unit_id']);
            $result['unit_batteries'] = $this->getUnitBatteries($result['unit_id']);
            $result['unit_chargers'] = $this->getUnitChargers($result['unit_id']);
            
            // Get work order spareparts
            $result['spareparts'] = $this->getWorkOrderSpareparts($id);
            
            // Store original dates before formatting for print documents
            $result['report_date_raw'] = $result['report_date'];
            $result['completion_date_raw'] = $result['completion_date'];
            
            // Format dates for display
            if ($result['report_date']) {
                $result['report_date'] = date('d/m/Y H:i', strtotime($result['report_date']));
            }
            if ($result['completion_date']) {
                $result['completion_date'] = date('d/m/Y H:i', strtotime($result['completion_date']));
            }
        }
        
        return $result;
    }

    /**
     * Get unit attachments
     */
    public function getUnitAttachments($unitId)
    {
        if (!$unitId) return [];
        
        return $this->db->table('inventory_attachments ia')
            ->select('a.tipe, a.merk, a.model, ia.serial_number as sn_attachment')
            ->join('attachment a', 'ia.attachment_type_id = a.id_attachment', 'left')
            ->where('ia.inventory_unit_id', $unitId)
            ->where('ia.attachment_type_id IS NOT NULL')
            ->whereIn('ia.status', ['IN_USE', 'SPARE'])
            ->get()
            ->getResultArray();
    }

    /**
     * Get unit batteries
     */
    public function getUnitBatteries($unitId)
    {
        if (!$unitId) return [];
        
        return $this->db->table('inventory_batteries ib')
            ->select('b.merk_baterai, b.tipe_baterai, b.jenis_baterai, ib.serial_number as sn_baterai')
            ->join('baterai b', 'ib.battery_type_id = b.id', 'left')
            ->where('ib.inventory_unit_id', $unitId)
            ->where('ib.battery_type_id IS NOT NULL')
            ->whereIn('ib.status', ['IN_USE', 'SPARE'])
            ->get()
            ->getResultArray();
    }

    /**
     * Get unit chargers
     */
    public function getUnitChargers($unitId)
    {
        if (!$unitId) return [];
        
        return $this->db->table('inventory_chargers ic')
            ->select('c.merk_charger, c.tipe_charger, ic.serial_number as sn_charger')
            ->join('charger c', 'ic.charger_type_id = c.id_charger', 'left')
            ->where('ic.inventory_unit_id', $unitId)
            ->where('ic.charger_type_id IS NOT NULL')
            ->whereIn('ic.status', ['IN_USE', 'SPARE'])
            ->get()
            ->getResultArray();
    }
    
    /**
     * Delete existing work order assignments
     */
    public function deleteWorkOrderAssignments($workOrderId)
    {
        $db = \Config\Database::connect();
        return $db->table('work_order_assignments')
            ->where('work_order_id', $workOrderId)
            ->delete();
    }
    
    /**
     * Insert a work order assignment
     */
    public function insertWorkOrderAssignment($data)
    {
        $db = \Config\Database::connect();
        return $db->table('work_order_assignments')->insert($data);
    }
    
    /**
     * Add status history for a work order
     */
    public function addStatusHistory($workOrderId, $toStatusId, $notes = null, $fromStatusId = null)
    {
        // If fromStatusId is not provided, get current status from work order
        if ($fromStatusId === null) {
            $currentWorkOrder = $this->find($workOrderId);
            $fromStatusId = $currentWorkOrder ? $currentWorkOrder['status_id'] : null;
            log_message('debug', "addStatusHistory: fromStatusId was null, retrieved from DB: $fromStatusId");
        }
        
        $changedBy = (int)session()->get('user_id');
        if ($changedBy <= 0) {
            // Fallback: try to find any valid user in the users table
            $fallbackUser = $this->db->table('users')->select('id')->limit(1)->get()->getRowArray();
            $changedBy = $fallbackUser ? (int)$fallbackUser['id'] : null;
            log_message('warning', "addStatusHistory: session user_id empty, using fallback user_id={$changedBy} for WO {$workOrderId}");
        }

        $data = [
            'work_order_id' => $workOrderId,
            'from_status_id' => $fromStatusId,
            'to_status_id' => $toStatusId,
            'changed_by' => $changedBy,
            'change_reason' => $notes,
            'changed_at' => date('Y-m-d H:i:s')
        ];
        
        log_message('debug', 'addStatusHistory data: ' . print_r($data, true));
        
        $db = \Config\Database::connect();
        $result = $db->table('work_order_status_history')->insert($data);
        
        log_message('debug', 'addStatusHistory insert result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        if (!$result) {
            log_message('error', 'addStatusHistory insert error: ' . print_r($db->error(), true));
        }
        
        return $result;
    }
    
    /**
     * Get spareparts for work order
     */
    public function getWorkOrderSpareparts($workOrderId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('work_order_spareparts wos')
            ->select('
                wos.id,
                wos.sparepart_code as code,
                wos.sparepart_name as name,
                wos.item_type,
                wos.quantity_brought as qty,
                wos.quantity_used,
                wos.satuan,
                wos.notes,
                wos.is_from_warehouse
            ', false)
            ->where('wos.work_order_id', $workOrderId)
            ->orderBy('wos.id', 'ASC')
            ->get()
            ->getResultArray();
    }
}