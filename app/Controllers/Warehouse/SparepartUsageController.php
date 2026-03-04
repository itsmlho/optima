<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\WorkOrderSparepartUsageModel;
use App\Models\WorkOrderSparepartModel;
use App\Models\WorkOrderAssignmentModel;

class SparepartUsageController extends BaseController
{
    protected $usageModel;
    protected $returnModel;
    protected $assignmentModel;

    public function __construct()
    {
        $this->usageModel = new WorkOrderSparepartUsageModel();
        $this->returnModel = new WorkOrderSparepartModel();
        $this->assignmentModel = new WorkOrderAssignmentModel();
    }

    /**
     * Index - Combined page with 2 tabs
     */
    public function index()
    {
        // Check permission
        if (!$this->canAccess('warehouse')) {
            return redirect()->to('/')->with('error', 'Access Denied');
        }

        // Check if tables exist
        $db = \Config\Database::connect();
        $usageTableExists = $db->tableExists('work_order_sparepart_usage');
        $returnTableExists = $db->tableExists('work_order_sparepart_returns');

        $data = [
            'title' => 'Sparepart Usage & Returns | OPTIMA',
            'page_title' => 'Pemakaian & Pengembalian Sparepart',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/sparepart-usage' => 'Sparepart Usage & Returns'
            ],
            'usage_table_exists' => $usageTableExists,
            'return_table_exists' => $returnTableExists,
            'stats' => [
                // Count from work_order_spareparts where quantity_used > 0
                'usage_total' => $usageTableExists ? $db->table('work_order_spareparts')->where('quantity_used >', 0)->where('quantity_used IS NOT NULL')->countAllResults() : 0,
                
                // ← NEW: Separate counts for warehouse vs non-warehouse
                'usage_warehouse' => $usageTableExists ? $db->table('work_order_spareparts')
                    ->where('quantity_used >', 0)
                    ->where('quantity_used IS NOT NULL')
                    ->where('is_from_warehouse', 1)
                    ->countAllResults() : 0,
                    
                'usage_non_warehouse' => $usageTableExists ? $db->table('work_order_spareparts')
                    ->where('quantity_used >', 0)
                    ->where('quantity_used IS NOT NULL')
                    ->where('is_from_warehouse', 0)
                    ->countAllResults() : 0,
                
                // Return stats - check if status column exists first
                'return_pending' => 0, // Disabled until work_order_sparepart_returns table is properly created
                'return_confirmed' => 0 // Disabled until work_order_sparepart_returns table is properly created
            ]
        ];

        return view('warehouse/sparepart_usage', $data);
    }

    /**
     * Get usage list grouped by Work Order (DataTable)
     */
    public function getUsageGrouped()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied']);
        }

        $request = $this->request;
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $search = $request->getPost('search')['value'] ?? '';

        try {
            $db = \Config\Database::connect();
            
            // Get work orders that have sparepart usage
            $builder = $db->table('work_orders wo')
                ->select('
                    wo.id as work_order_id,
                    wo.work_order_number,
                    wo.report_date,
                    wo.created_at,
                    c.customer_name,
                    iu.no_unit as unit_number,
                    mu.merk_unit,
                    mu.model_unit,
                    COUNT(DISTINCT wosp.id) as total_items,
                    SUM(CASE WHEN wosp.is_from_warehouse = 1 THEN 1 ELSE 0 END) as warehouse_items,
                    SUM(CASE WHEN wosp.is_from_warehouse = 0 THEN 1 ELSE 0 END) as nonwarehouse_items
                ')
                ->join('work_order_spareparts wosp', 'wosp.work_order_id = wo.id', 'inner')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->groupBy('wo.id');

            // Apply search
            if (!empty($search)) {
                $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('c.customer_name', $search)
                    ->orLike('iu.no_unit', $search)
                    ->orLike('mu.merk_unit', $search)
                ->groupEnd();
            }

            // Get total records
            $totalRecords = $builder->countAllResults(false);

            // Apply pagination
            $builder->limit($length, $start);

            // Apply ordering - default by date desc
            $builder->orderBy('wo.created_at', 'DESC');

            $results = $builder->get()->getResultArray();

            // Format data
            $data = [];
            foreach ($results as $row) {
                $data[] = [
                    'work_order_id' => $row['work_order_id'],
                    'work_order_number' => $row['work_order_number'],
                    'report_date' => $row['report_date'] ? date('d/m/Y', strtotime($row['report_date'])) : '-',
                    'created_at' => $row['created_at'] ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-',
                    'customer_name' => $row['customer_name'] ?? '-',
                    'unit_number' => $row['unit_number'] ?? '-',
                    'unit_info' => trim(($row['merk_unit'] ?? '') . ' ' . ($row['model_unit'] ?? '')) ?: '-',
                    'total_items' => (int)$row['total_items'],
                    'warehouse_items' => (int)$row['warehouse_items'],
                    'nonwarehouse_items' => (int)$row['nonwarehouse_items']
                ];
            }

            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting grouped usage: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get sparepart details for a specific work order
     */
    public function getWorkOrderSpareparts($workOrderId)
    {
        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON([]);
        }

        try {
            $db = \Config\Database::connect();
            
            log_message('info', 'Fetching spareparts for WO ID: ' . $workOrderId);
            
            // Get all spareparts for this work order (remove quantity_used filter)
            $query = $db->table('work_order_spareparts')
                ->where('work_order_id', $workOrderId)
                ->get();
            
            $spareparts = $query->getResultArray();
            
            log_message('info', 'Found ' . count($spareparts) . ' spareparts for WO ID: ' . $workOrderId);
            
            if (empty($spareparts)) {
                log_message('warning', 'No spareparts found for WO ID: ' . $workOrderId);
                return $this->response->setJSON([]);
            }
            
            // Get work order info
            $woQuery = $db->table('work_orders wo')
                ->select('
                    wo.work_order_number,
                    wo.report_date,
                    e.staff_name as mechanic_name,
                    c.customer_name,
                    iu.no_unit as unit_number,
                    CONCAT_WS(" ", mu.merk_unit, mu.model_unit) as unit_info
                ')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->join('employees e', 'e.id = wo.mechanic_id', 'left')
                ->where('wo.id', $workOrderId)
                ->get();
                
            $woInfo = $woQuery->getRowArray();
            
            // Get returns
            $returnsQuery = $db->table('work_order_sparepart_returns')
                ->where('work_order_id', $workOrderId)
                ->get();
            $returns = $returnsQuery->getResultArray();
            
            $returnMap = [];
            foreach ($returns as $ret) {
                $returnMap[$ret['work_order_sparepart_id']] = $ret['quantity_return'];
            }
            
            // Format data
            $result = [];
            foreach ($spareparts as $item) {
                $result[] = [
                    'id' => $item['id'],
                    'sparepart_code' => $item['sparepart_code'],
                    'sparepart_name' => $item['sparepart_name'],
                    'item_type' => $item['item_type'] ?? 'sparepart',
                    'is_from_warehouse' => $item['is_from_warehouse'] ?? 1,
                    'quantity_brought' => $item['quantity_brought'],
                    'quantity_used' => $item['quantity_used'] ?? 0,
                    'quantity_return' => $returnMap[$item['id']] ?? 0,
                    'usage_notes' => $item['notes'] ?? '-',
                    'work_order_number' => $woInfo['work_order_number'] ?? '-',
                    'report_date' => isset($woInfo['report_date']) ? date('d/m/Y', strtotime($woInfo['report_date'])) : '-',
                    'mechanic_name' => $woInfo['mechanic_name'] ?? '-',
                    'customer_name' => $woInfo['customer_name'] ?? '-',
                    'unit_number' => $woInfo['unit_number'] ?? '-',
                    'unit_info' => $woInfo['unit_info'] ?? '-'
                ];
            }
            
            log_message('info', 'Returning ' . count($result) . ' formatted items for WO ID: ' . $workOrderId);
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Error getting WO spareparts: ' . $e->getMessage() . ' at line ' . $e->getLine());
            return $this->response->setJSON([]);
        }
    }

    /**
     * Get usage list (DataTable) - Tab Pemakaian
     */
    public function getUsage()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied']);
        }

        $request = $this->request;
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $search = $request->getPost('search')['value'] ?? '';

        try {
            $db = \Config\Database::connect();
            
            // Build query - Data usage ada di work_order_spareparts (quantity_used) bukan di work_order_sparepart_usage
            // Karena saat validasi sparepart, data disimpan langsung ke work_order_spareparts.quantity_used
            $builder = $db->table('work_order_spareparts wosp')
                ->select('
                    wosp.id,
                    wosp.work_order_id,
                    wosp.sparepart_code,
                    wosp.sparepart_name,
                    wosp.item_type,
                    wosp.is_from_warehouse,
                    wosp.quantity_brought,
                    wosp.quantity_used,
                    COALESCE(wosr.quantity_return, 0) as quantity_returned,
                    wosp.satuan,
                    wosp.notes as usage_notes,
                    wosp.updated_at as used_at,
                    wo.work_order_number,
                    wo.report_date,
                    wo.mechanic_id,
                    wo.helper_id,
                    COALESCE(mech_emp.staff_name, "Unknown Mechanic") as mechanic_name,
                    COALESCE(help_emp.staff_name, "Unknown Helper") as helper_name,
                    wos.status_name as wo_status,
                    c.customer_name,
                    iu.no_unit as unit_number
                ')
                ->join('work_orders wo', 'wo.id = wosp.work_order_id', 'left')
                ->join('employees mech_emp', 'mech_emp.id = wo.mechanic_id', 'left')
                ->join('employees help_emp', 'help_emp.id = wo.helper_id', 'left')
                ->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left')
                ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->join('work_order_sparepart_returns wosr', 'wosr.work_order_id = wosp.work_order_id AND wosr.work_order_sparepart_id = wosp.id', 'left')
                ->where('wosp.quantity_used >', 0) // Only show records with usage
                ->where('wosp.quantity_used IS NOT NULL'); // Ensure quantity_used is not null

            // Apply search
            if (!empty($search)) {
                $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('wosp.sparepart_code', $search)
                    ->orLike('wosp.sparepart_name', $search)
                    ->orLike('c.customer_name', $search)
                    ->orLike('iu.no_unit', $search)
                ->groupEnd();
            }

            // Get total records
            $totalRecords = $builder->countAllResults(false);

            // Apply pagination
            $builder->limit($length, $start);

            // Apply ordering
            $order = $request->getPost('order')[0] ?? null;
            if ($order) {
                $columnIndex = $order['column'] ?? 0;
                $columnDir = $order['dir'] ?? 'DESC';
                
                $columns = [
                    0 => 'wosp.updated_at',
                    1 => 'wo.work_order_number',
                    2 => 'wosp.sparepart_name',
                    3 => 'c.customer_name',
                    4 => 'wosp.quantity_used'
                ];
                
                if (isset($columns[$columnIndex])) {
                    $builder->orderBy($columns[$columnIndex], $columnDir);
                } else {
                    $builder->orderBy('wosp.updated_at', 'DESC');
                }
            } else {
                $builder->orderBy('wosp.updated_at', 'DESC');
            }

            $results = $builder->get()->getResultArray();

            // Get mechanics/helpers dari work_order_assignments juga
            $workOrderIds = array_unique(array_filter(array_column($results, 'work_order_id')));
            $assignmentMechanics = [];
            
            if (!empty($workOrderIds)) {
                // Get all assignments
                $allAssignments = $db->table('work_order_assignments')
                    ->whereIn('work_order_id', $workOrderIds)
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();
                
                // Get staff names from employees table
                $staffIds = array_unique(array_filter(array_column($allAssignments, 'staff_id')));
                $staffMap = [];
                if (!empty($staffIds)) {
                    $staffData = $db->table('employees')
                        ->whereIn('id', $staffIds)
                        ->get()
                        ->getResultArray();
                    
                    foreach ($staffData as $staff) {
                        $staffMap[$staff['id']] = $staff['staff_name'];
                    }
                }
                
                // Group by work_order_id
                foreach ($allAssignments as $assignment) {
                    $woId = $assignment['work_order_id'];
                    $staffId = $assignment['staff_id'];
                    $role = $assignment['role'];
                    
                    if (!isset($assignmentMechanics[$woId])) {
                        $assignmentMechanics[$woId] = [];
                    }
                    
                    if (isset($staffMap[$staffId]) && !empty($staffMap[$staffId])) {
                        $roleLabel = $role === 'MECHANIC' ? 'MECHANIC' : 'HELPER';
                        $assignmentMechanics[$woId][] = $staffMap[$staffId] . ' (' . $roleLabel . ')';
                    }
                }
            }

            // Format data
            $data = [];
            foreach ($results as $row) {
                $woId = $row['work_order_id'];
                
                // Combine mechanic_name dan helper_name dari work_orders dengan assignments
                $mechanicHelperNames = [];
                
                // Dari work_orders - tampilkan hanya nama valid (bukan Unknown atau kosong)
                if (!empty($row['mechanic_name']) && !in_array(trim($row['mechanic_name']), ['Unknown Mechanic', 'Unknown', '-', 'NULL'])) {
                    $mechanicHelperNames[] = $row['mechanic_name'] . ' (MECHANIC)';
                }
                if (!empty($row['helper_name']) && !in_array(trim($row['helper_name']), ['Unknown Helper', 'Unknown', '-', 'NULL'])) {
                    $mechanicHelperNames[] = $row['helper_name'] . ' (HELPER)';
                }
                
                // Dari work_order_assignments
                if (isset($assignmentMechanics[$woId])) {
                    $mechanicHelperNames = array_merge($mechanicHelperNames, $assignmentMechanics[$woId]);
                }
                
                // Remove duplicates and format
                $mechanicHelperNames = !empty($mechanicHelperNames) ? implode(', ', array_unique($mechanicHelperNames)) : '-';
                
                $data[] = [
                    'id' => $row['id'],
                    'work_order_id' => $row['work_order_id'],
                    'work_order_number' => $row['work_order_number'] ?? '-',
                    'sparepart_code' => $row['sparepart_code'] ?? '-',
                    'sparepart_name' => $row['sparepart_name'] ?? '-',
                    'item_type' => $row['item_type'] ?? 'sparepart',
                    'is_from_warehouse' => $row['is_from_warehouse'] ?? 1,
                    'customer_name' => $row['customer_name'] ?? '-',
                    'unit_number' => $row['unit_number'] ?? '-',
                    'mechanic_name' => $mechanicHelperNames,
                    'quantity_brought' => (int)($row['quantity_brought'] ?? 0),
                    'quantity_used' => (int)($row['quantity_used'] ?? 0),
                    'quantity_return' => (int)($row['quantity_returned'] ?? 0),
                    'satuan' => $row['satuan'] ?? 'PCS',
                    'created_at' => $row['used_at'] ? date('d/m/Y H:i', strtotime($row['used_at'])) : '-',
                    'used_at' => $row['used_at'] ? date('d/m/Y H:i', strtotime($row['used_at'])) : '-',
                    'report_date' => $row['report_date'] ? date('d/m/Y', strtotime($row['report_date'])) : '-',
                    'usage_notes' => $row['usage_notes'] ?? '-'
                ];
            }

            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting sparepart usage: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get returns list (DataTable) - Tab Pengembalian
     */
    public function getReturns()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied']);
        }

        $request = $this->request;
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $search = $request->getPost('search')['value'] ?? '';
        $status = $request->getPost('status') ?? 'PENDING';

        try {
            $db = \Config\Database::connect();
            
            // Check if table and status column exist
            if (!$db->tableExists('work_order_sparepart_returns')) {
                return $this->response->setJSON([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Table work_order_sparepart_returns does not exist'
                ]);
            }
            
            // Check if status column exists
            $fields = $db->getFieldNames('work_order_sparepart_returns');
            $hasStatusColumn = in_array('status', $fields);
            
            // Build query
            $builder = $db->table('work_order_sparepart_returns wosr')
                ->select('
                    wosr.*,
                    wo.work_order_number,
                    wo.report_date,
                    wo.id as work_order_id,
                    wo.mechanic_id,
                    wo.helper_id,
                    COALESCE(mech_emp.staff_name, "Unknown Mechanic") as mechanic_name,
                    COALESCE(help_emp.staff_name, "Unknown Helper") as helper_name,
                    c.customer_name,
                    iu.no_unit as unit_number,
                    mdu.merk_unit,
                    mdu.model_unit,
                    u.username as confirmed_by_name
                ')
                ->join('work_orders wo', 'wo.id = wosr.work_order_id', 'left')
                ->join('employees mech_emp', 'mech_emp.id = wo.mechanic_id', 'left')
                ->join('employees help_emp', 'help_emp.id = wo.helper_id', 'left')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left')
                ->join('model_unit mdu', 'mdu.id_model_unit = iu.model_unit_id', 'left')
                ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->join('users u', 'u.id = wosr.confirmed_by', 'left');
            
            // Only filter by status if column exists
            if ($hasStatusColumn && $status !== 'ALL') {
                $builder->where('wosr.status', $status);
            }

            // Apply search
            if (!empty($search)) {
                $builder->groupStart()
                    ->like('wo.work_order_number', $search)
                    ->orLike('wosr.sparepart_code', $search)
                    ->orLike('wosr.sparepart_name', $search)
                    ->orLike('c.customer_name', $search)
                    ->orLike('iu.no_unit', $search)
                ->groupEnd();
            }

            // Get total records
            $totalRecords = $builder->countAllResults(false);

            // Apply pagination
            $builder->limit($length, $start);

            // Apply ordering
            $order = $request->getPost('order')[0] ?? null;
            if ($order) {
                $columnIndex = $order['column'] ?? 0;
                $columnDir = $order['dir'] ?? 'DESC';
                
                $columns = [
                    0 => 'wosr.created_at',
                    1 => 'wo.work_order_number',
                    2 => 'wosr.sparepart_name',
                    3 => 'c.customer_name',
                    4 => 'wosr.quantity_return'
                ];
                
                if (isset($columns[$columnIndex])) {
                    $builder->orderBy($columns[$columnIndex], $columnDir);
                } else {
                    $builder->orderBy('wosr.created_at', 'DESC');
                }
            } else {
                $builder->orderBy('wosr.created_at', 'DESC');
            }

            $results = $builder->get()->getResultArray();

            // Get mechanics/helpers dari work_order_assignments juga
            $workOrderIds = array_unique(array_filter(array_column($results, 'work_order_id')));
            $assignmentMechanics = [];
            
            if (!empty($workOrderIds)) {
                // Get all assignments
                $allAssignments = $db->table('work_order_assignments')
                    ->whereIn('work_order_id', $workOrderIds)
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();
                
                // Get staff names from employees table
                $staffIds = array_unique(array_filter(array_column($allAssignments, 'staff_id')));
                $staffMap = [];
                if (!empty($staffIds)) {
                    $staffData = $db->table('employees')
                        ->whereIn('id', $staffIds)
                        ->get()
                        ->getResultArray();
                    
                    foreach ($staffData as $staff) {
                        $staffMap[$staff['id']] = $staff['staff_name'];
                    }
                }
                
                // Group by work_order_id
                foreach ($allAssignments as $assignment) {
                    $woId = $assignment['work_order_id'];
                    $staffId = $assignment['staff_id'];
                    $role = $assignment['role'];
                    
                    if (!isset($assignmentMechanics[$woId])) {
                        $assignmentMechanics[$woId] = [];
                    }
                    
                    if (isset($staffMap[$staffId]) && !empty($staffMap[$staffId])) {
                        $roleLabel = $role === 'MECHANIC' ? 'MECHANIC' : 'HELPER';
                        $assignmentMechanics[$woId][] = $staffMap[$staffId] . ' (' . $roleLabel . ')';
                    }
                }
            }

            // Format data
            $data = [];
            foreach ($results as $row) {
                $woId = $row['work_order_id'] ?? null;
                
                // Combine mechanic_name dan helper_name dari work_orders dengan assignments
                $mechanicHelperNames = [];
                
                // Dari work_orders - tampilkan hanya nama valid (bukan Unknown atau kosong)
                if (!empty($row['mechanic_name']) && !in_array(trim($row['mechanic_name']), ['Unknown Mechanic', 'Unknown', '-', 'NULL'])) {
                    $mechanicHelperNames[] = $row['mechanic_name'] . ' (MECHANIC)';
                }
                if (!empty($row['helper_name']) && !in_array(trim($row['helper_name']), ['Unknown Helper', 'Unknown', '-', 'NULL'])) {
                    $mechanicHelperNames[] = $row['helper_name'] . ' (HELPER)';
                }
                
                // Dari work_order_assignments
                if ($woId && isset($assignmentMechanics[$woId])) {
                    $mechanicHelperNames = array_merge($mechanicHelperNames, $assignmentMechanics[$woId]);
                }
                
                // Remove duplicates and format
                $mechanicHelperNames = !empty($mechanicHelperNames) ? implode(', ', array_unique($mechanicHelperNames)) : '-';
                
                $data[] = [
                    'id' => $row['id'],
                    'work_order_number' => $row['work_order_number'] ?? '-',
                    'sparepart_code' => $row['sparepart_code'],
                    'sparepart_name' => $row['sparepart_name'],
                    'customer_name' => $row['customer_name'] ?? '-',
                    'unit_number' => $row['unit_number'] ?? '-',
                    'mechanic_name' => $mechanicHelperNames,
                    'quantity_brought' => $row['quantity_brought'],
                    'quantity_used' => $row['quantity_used'],
                    'quantity_return' => $row['quantity_return'],
                    'satuan' => $row['satuan'],
                    'status' => $row['status'] ?? 'N/A',
                    'created_at' => $row['created_at'] ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-',
                    'confirmed_at' => $row['confirmed_at'] ? date('d/m/Y H:i', strtotime($row['confirmed_at'])) : '-',
                    'confirmed_by_name' => $row['confirmed_by_name'] ?? '-',
                    'report_date' => $row['report_date'] ? date('d/m/Y', strtotime($row['report_date'])) : '-'
                ];
            }

            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting sparepart returns: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get return detail
     */
    public function getReturnDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied']);
        }

        try {
            $return = $this->returnModel->getReturnDetail($id);
            
            if (!$return) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Return record not found'
                ]);
            }

            // Format dates
            if ($return['created_at']) {
                $return['created_at_formatted'] = date('d/m/Y H:i', strtotime($return['created_at']));
            }
            if ($return['confirmed_at']) {
                $return['confirmed_at_formatted'] = date('d/m/Y H:i', strtotime($return['confirmed_at']));
            }
            if ($return['report_date']) {
                $return['report_date_formatted'] = date('d/m/Y', strtotime($return['report_date']));
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $return
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting return detail: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get usage detail
     */
    public function getUsageDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied']);
        }

        try {
            $db = \Config\Database::connect();
            
            // Get usage detail from work_order_spareparts (bukan work_order_sparepart_usage)
            $usage = $db->table('work_order_spareparts wosp')
                ->select('
                    wosp.id,
                    wosp.work_order_id,
                    wosp.sparepart_code,
                    wosp.sparepart_name,
                    wosp.item_type,
                    wosp.is_from_warehouse,
                    wosp.quantity_brought,
                    wosp.quantity_used,
                    COALESCE(wosr.quantity_return, 0) as quantity_returned,
                    wosr.created_at as returned_at,
                    wosp.satuan,
                    wosp.notes as usage_notes,
                    wosp.updated_at as used_at,
                    wo.work_order_number,
                    wo.report_date,
                    wos.status_name as wo_status,
                    wos.status_color as wo_status_color,
                    c.customer_name,
                    cl.location_name,
                    iu.no_unit as unit_number,
                    mu.merk_unit,
                    mu.model_unit
                ')
                ->join('work_orders wo', 'wo.id = wosp.work_order_id', 'left')
                ->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->join('work_order_sparepart_returns wosr', 'wosr.work_order_id = wosp.work_order_id AND wosr.work_order_sparepart_id = wosp.id', 'left')
                ->where('wosp.id', $id)
                ->get()
                ->getRowArray();
            
            if (!$usage) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usage record not found'
                ]);
            }
            
            // Get mechanics and helpers for this work order - menggunakan pendekatan sederhana
            $mechanicHelperNames = '-';
            if (!empty($usage['work_order_id'])) {
                // Get all assignments for this work order
                $assignments = $db->table('work_order_assignments')
                    ->where('work_order_id', $usage['work_order_id'])
                    ->where('is_active', 1)
                    ->orderBy('role', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->getResultArray();
                
                if (!empty($assignments)) {
                    $mechanicHelpers = [];
                    
                    // Get staff names for each assignment
                    foreach ($assignments as $assignment) {
                        $staffId = $assignment['staff_id'];
                        $role = $assignment['role'];
                        
                        // Get staff name
                        $staff = $db->table('users')
                            ->where('id', $staffId)
                            ->get()
                            ->getRowArray();
                        
                        if ($staff && (!empty($staff['first_name']) || !empty($staff['last_name']))) {
                            $roleLabel = $role === 'MECHANIC' ? 'MECHANIC' : 'HELPER';
                            $mechanicHelpers[] = trim($staff['first_name'] . ' ' . $staff['last_name']) . ' (' . $roleLabel . ')';
                        }
                    }
                    
                    if (!empty($mechanicHelpers)) {
                        $mechanicHelperNames = implode(', ', $mechanicHelpers);
                    }
                }
            }
            
            $usage['mechanic_name'] = $mechanicHelperNames;

            // Format dates
            if (!empty($usage['used_at'])) {
                $usage['used_at_formatted'] = date('d/m/Y H:i', strtotime($usage['used_at']));
            } else {
                $usage['used_at_formatted'] = '-';
            }
            if (!empty($usage['returned_at'])) {
                $usage['returned_at_formatted'] = date('d/m/Y H:i', strtotime($usage['returned_at']));
            } else {
                $usage['returned_at_formatted'] = 'Belum dikembalikan';
            }
            if (!empty($usage['report_date'])) {
                $usage['report_date_formatted'] = date('d/m/Y', strtotime($usage['report_date']));
            } else {
                $usage['report_date_formatted'] = '-';
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $usage
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting usage detail: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Confirm return
     */
    public function confirmReturn($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied']);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in']);
        }

        $notes = $this->request->getPost('notes') ?? null;

        try {
            $return = $this->returnModel->find($id);
            
            if (!$return) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Return record not found'
                ]);
            }

            // Check if status exists and is PENDING
            if (isset($return['status']) && $return['status'] !== 'PENDING') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Return sudah dikonfirmasi atau dibatalkan'
                ]);
            }
            
            // If status column doesn't exist, skip status check
            if (!isset($return['status'])) {
                log_message('warning', 'Status column does not exist in work_order_sparepart_returns table');
            }

            $confirmed = $this->returnModel->confirmReturn($id, $userId, $notes);

            if ($confirmed) {
                // Send cross-division notification to Service
                helper('notification');
                if (function_exists('notify_sparepart_returned')) {
                    $returnDetails = $this->returnModel->find($id);
                    $db = \Config\Database::connect();
                    $sparepart = $db->table('sparepart')->where('id', $returnDetails['sparepart_id'] ?? 0)->get()->getRowArray();
                    
                    notify_sparepart_returned([
                        'return_id' => $id,
                        'sparepart_id' => $returnDetails['sparepart_id'] ?? null,
                        'sparepart_name' => $sparepart['name'] ?? 'Unknown',
                        'quantity' => $returnDetails['quantity'] ?? 0,
                        'condition' => $returnDetails['condition'] ?? 'Baik',
                        'returned_by' => $returnDetails['returned_by_name'] ?? '',
                        'returned_from' => $returnDetails['work_order_number'] ?? '',
                        'confirmed_by' => session('username') ?? 'System',
                        'confirmed_at' => date('Y-m-d H:i:s'),
                        'notes' => $notes ?? '',
                        'url' => base_url('/warehouse/sparepart-usage/return-detail/' . $id)
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pengembalian sparepart berhasil dikonfirmasi'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengonfirmasi pengembalian'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error confirming return: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}

