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
        'admin_staff_id',
        'foreman_staff_id',
        'mechanic_staff_id',
        'helper_staff_id',
        'repair_description',
        'notes',
        'sparepart_used',
        'time_to_repair',
        'completion_date',
        'area',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'unit_id' => 'required|integer',
        'order_type' => 'required|in_list[COMPLAINT,PMPS,FABRIKASI,PERSIAPAN]',
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
                COUNT(*) as total_work_orders,
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

    // Get work order staff by role
    public function getStaffByRole($role = null)
    {
        $builder = $this->db->table('work_order_staff')
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
                           ->select('iu.id_inventory_unit as id, iu.no_unit, mu.merk_unit, mu.model_unit, tu.tipe, kap.kapasitas_unit, k.pelanggan')
                           ->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left')
                           ->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left')
                           ->join('kapasitas kap', 'iu.kapasitas_unit_id = kap.id_kapasitas', 'left')
                           ->join('kontrak k', 'iu.kontrak_id = k.id', 'left')
                           ->where('iu.status_unit', 'AVAILABLE'); // Only available units
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('iu.no_unit', $search)
                    ->orLike('mu.merk_unit', $search)
                    ->orLike('mu.model_unit', $search)
                    ->orLike('k.pelanggan', $search)
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
    public function searchWorkOrders($search = '', $status = null, $priority = null, $startDate = null, $endDate = null)
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
                          COALESCE(k.pelanggan, "Belum Ada Kontrak") as pelanggan,
                          COALESCE(k.lokasi, iu.lokasi_unit, "Lokasi Tidak Diketahui") as lokasi,
                          COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                          COALESCE(mu.model_unit, "Unknown") as model_unit,
                          admin.staff_name as admin_staff,
                          foreman.staff_name as foreman_staff,
                          mechanic.staff_name as mechanic_staff,
                          helper.staff_name as helper_staff')
                ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
                ->join('work_order_priorities wop', 'wo.priority_id = wop.id', 'left')
                ->join('work_order_categories woc', 'wo.category_id = woc.id', 'left')
                ->join('work_order_subcategories wosc', 'wo.subcategory_id = wosc.id', 'left')
                ->join('inventory_unit iu', 'wo.unit_id = iu.id_inventory_unit', 'left')
                ->join('kontrak k', 'iu.kontrak_id = k.id', 'left')
                ->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left')
                ->join('work_order_staff admin', 'wo.admin_staff_id = admin.id', 'left')
                ->join('work_order_staff foreman', 'wo.foreman_staff_id = foreman.id', 'left')
                ->join('work_order_staff mechanic', 'wo.mechanic_staff_id = mechanic.id', 'left')
                ->join('work_order_staff helper', 'wo.helper_staff_id = helper.id', 'left');

        // Exclude soft deleted records
        $builder->where('wo.deleted_at', null);

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('wo.complaint_description', $search)
                    ->orLike('iu.no_unit', $search)
                    ->orLike('k.pelanggan', $search)
                    ->orLike('wos.status_name', $search)
                    ->groupEnd();
        }

        // Apply status filter
        if (!empty($status)) {
            $builder->where('wo.status_id', $status);
        }

        // Apply priority filter
        if (!empty($priority)) {
            $builder->where('wo.priority_id', $priority);
        }

        // Apply date range filter
        if (!empty($startDate)) {
            $builder->where('DATE(wo.report_date) >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('DATE(wo.report_date) <=', $endDate);
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
    public function countAllWorkOrders()
    {
        return $this->countAllResults();
    }

    /**
     * Get staff by role
     */
    public function getStaff($role)
    {
        return $this->db->table('work_order_staff')
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
        $builder = $this->db->table('work_orders wo');
        
        $result = $builder
            ->select('wo.*, 
                      iu.no_unit as unit_number, 
                      iu.tahun_unit, 
                      iu.serial_number as unit_serial, 
                      iu.lokasi_unit as unit_location,
                      iu.aksesoris as unit_accessories,
                      mu.merk_unit as unit_brand, 
                      mu.model_unit,
                      COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "Unknown") as unit_type,
                      k.pelanggan as unit_customer, 
                      k.lokasi,
                      sus.status_unit as unit_status,
                      d.nama_departemen as unit_departemen,
                      wop.priority_name, wop.priority_color,
                      woc.category_name, wosc.subcategory_name,
                      wos.status_name, wos.status_color, wos.status_code,
                      as.staff_name as admin_staff_name,
                      fs.staff_name as foreman_staff_name,  
                      ms.staff_name as mechanic_staff_name,
                      hs.staff_name as helper_staff_name')
            ->join('inventory_unit iu', 'wo.unit_id = iu.id_inventory_unit', 'left')
            ->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left')
            ->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left')
            ->join('kontrak k', 'iu.kontrak_id = k.id', 'left')
            ->join('status_unit sus', 'iu.status_unit_id = sus.id_status', 'left')
            ->join('departemen d', 'iu.departemen_id = d.id_departemen', 'left')
            ->join('work_order_priorities wop', 'wo.priority_id = wop.id', 'left')
            ->join('work_order_categories woc', 'wo.category_id = woc.id', 'left')
            ->join('work_order_subcategories wosc', 'wo.subcategory_id = wosc.id', 'left')
            ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
            ->join('work_order_staff as', 'wo.admin_staff_id = as.id', 'left')
            ->join('work_order_staff fs', 'wo.foreman_staff_id = fs.id', 'left')
            ->join('work_order_staff ms', 'wo.mechanic_staff_id = ms.id', 'left')
            ->join('work_order_staff hs', 'wo.helper_staff_id = hs.id', 'left')
            ->where('wo.id', $id)
            ->get()
            ->getRowArray();
            
        if ($result) {
            // Format unit info for legacy compatibility
            $result['unit_info'] = $result['unit_number'] . ' - ' . $result['unit_customer'] . ' (' . $result['unit_brand'] . ' ' . $result['model_unit'] . ')';
            
            // Format status badge
            $result['status_badge'] = '<span class="badge bg-'.$result['status_color'].'">'.$result['status_name'].'</span>';
            
            // Ensure unit fields have fallback values
            $result['unit_number'] = $result['unit_number'] ?? '-';
            $result['unit_brand'] = $result['unit_brand'] ?? 'Unknown';
            $result['model_unit'] = $result['model_unit'] ?? 'Unknown';
            $result['unit_type'] = $result['unit_type'] ?? 'Unknown';
            $result['unit_serial'] = $result['unit_serial'] ?? '-';
            $result['unit_location'] = $result['unit_location'] ?? '-';
            $result['unit_customer'] = $result['unit_customer'] ?? 'Belum Ada Kontrak';
            $result['unit_status'] = $result['unit_status'] ?? 'Unknown';
            
            // Get unit components (attachments, batteries, chargers)
            $result['unit_attachments'] = $this->getUnitAttachments($result['unit_id']);
            $result['unit_batteries'] = $this->getUnitBatteries($result['unit_id']);
            $result['unit_chargers'] = $this->getUnitChargers($result['unit_id']);
            
            // Format dates
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
        
        return $this->db->table('inventory_attachment ia')
            ->select('a.tipe, a.merk, a.model, ia.sn_attachment')
            ->join('attachment a', 'ia.attachment_id = a.id_attachment', 'inner')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'attachment')
            ->where('ia.attachment_id IS NOT NULL')
            ->get()
            ->getResultArray();
    }

    /**
     * Get unit batteries
     */
    public function getUnitBatteries($unitId)
    {
        if (!$unitId) return [];
        
        return $this->db->table('inventory_attachment ia')
            ->select('b.merk_baterai, b.tipe_baterai, b.jenis_baterai, ia.sn_baterai')
            ->join('baterai b', 'ia.baterai_id = b.id', 'inner')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'battery')
            ->where('ia.baterai_id IS NOT NULL')
            ->get()
            ->getResultArray();
    }

    /**
     * Get unit chargers
     */
    public function getUnitChargers($unitId)
    {
        if (!$unitId) return [];
        
        return $this->db->table('inventory_attachment ia')
            ->select('c.merk_charger, c.tipe_charger, ia.sn_charger')
            ->join('charger c', 'ia.charger_id = c.id_charger', 'inner')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'charger')
            ->where('ia.charger_id IS NOT NULL')
            ->get()
            ->getResultArray();
    }
}