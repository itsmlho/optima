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
            return redirect()->to('/')->with('error', 'Akses ditolak');
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
     * Get usage list grouped by Work Order + SPK (DataTable)
     * Combines WO spareparts and SPK spareparts in one view
     */
    public function getUsageGrouped()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $request = $this->request;
        $draw    = $request->getPost('draw')   ?? 1;
        $start   = (int)($request->getPost('start')  ?? 0);
        $length  = (int)($request->getPost('length') ?? 25);
        $search  = $request->getPost('search')['value'] ?? '';
        $source  = $request->getPost('source') ?? 'ALL'; // 'WO', 'SPK', or 'ALL'

        try {
            $db = \Config\Database::connect();

            $searchEsc = $db->escapeLikeString($search);

            // --- WO sub-query ---
            $woWhere = '';
            if (!empty($search)) {
                $woWhere = "AND (wo.work_order_number LIKE '%{$searchEsc}%'
                              OR c.customer_name    LIKE '%{$searchEsc}%'
                              OR iu.no_unit         LIKE '%{$searchEsc}%'
                              OR iu_src_wo.no_unit  LIKE '%{$searchEsc}%')";
            }
            $woSql = "
                SELECT
                    'WO'                                        AS record_source,
                    wo.id                                       AS record_id,
                    wo.work_order_number                        AS reference_number,
                    wo.report_date                              AS record_date,
                    wo.created_at                               AS created_at,
                    COALESCE(MAX(c.customer_name), '-')         AS customer_name,
                    COALESCE(MAX(iu.no_unit), '-')              AS unit_number,
                    COALESCE(MAX(mu.merk_unit), '')             AS merk_unit,
                    COALESCE(MAX(mu.model_unit), '')            AS model_unit,
                    COUNT(DISTINCT wosp.id)                     AS total_items,
                    SUM(wosp.is_from_warehouse = 1)             AS warehouse_items,
                    SUM(wosp.is_from_warehouse = 0)             AS nonwarehouse_items
                FROM work_orders wo
                INNER JOIN work_order_spareparts wosp ON wosp.work_order_id = wo.id
                LEFT  JOIN inventory_unit iu      ON iu.id_inventory_unit      = wo.unit_id
                LEFT  JOIN model_unit mu          ON mu.id_model_unit           = iu.model_unit_id
                LEFT  JOIN kontrak_unit ku        ON ku.unit_id = iu.id_inventory_unit
                                                 AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                                                 AND ku.is_temporary = 0
                LEFT  JOIN kontrak k              ON k.id  = ku.kontrak_id
                LEFT  JOIN customers c            ON c.id  = k.customer_id
                LEFT  JOIN inventory_unit iu_src_wo ON iu_src_wo.id_inventory_unit = wosp.source_unit_id
                WHERE wo.deleted_at IS NULL
                AND wo.sparepart_validated = 1
                {$woWhere}
                GROUP BY wo.id, wo.work_order_number, wo.report_date, wo.created_at
            ";

            // --- SPK sub-query ---
            $spkWhere = '';
            if (!empty($search)) {
                $spkWhere = "AND (s.nomor_spk        LIKE '%{$searchEsc}%'
                              OR s.pelanggan        LIKE '%{$searchEsc}%'
                              OR iu.no_unit         LIKE '%{$searchEsc}%'
                              OR iu_src_spk.no_unit LIKE '%{$searchEsc}%')";
            }
            $spkSql = "
                SELECT
                    'SPK'                                       AS record_source,
                    s.id                                        AS record_id,
                    s.nomor_spk                                 AS reference_number,
                    DATE(s.dibuat_pada)                         AS record_date,
                    s.dibuat_pada                               AS created_at,
                    COALESCE(s.pelanggan, '-')                  AS customer_name,
                    COALESCE(MAX(iu.no_unit), '-')              AS unit_number,
                    COALESCE(MAX(mu.merk_unit), '')             AS merk_unit,
                    COALESCE(MAX(mu.model_unit), '')            AS model_unit,
                    COUNT(DISTINCT ssp.id)                      AS total_items,
                    SUM(ssp.is_from_warehouse = 1)              AS warehouse_items,
                    SUM(ssp.is_from_warehouse = 0)              AS nonwarehouse_items
                FROM spk s
                INNER JOIN spk_spareparts ssp ON ssp.spk_id = s.id
                LEFT  JOIN kontrak k              ON k.id = s.kontrak_id
                LEFT  JOIN kontrak_unit ku        ON ku.kontrak_id = k.id
                                                 AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                                                 AND ku.is_temporary = 0
                LEFT  JOIN inventory_unit iu      ON iu.id_inventory_unit = ku.unit_id
                LEFT  JOIN model_unit mu          ON mu.id_model_unit = iu.model_unit_id
                LEFT  JOIN inventory_unit iu_src_spk ON iu_src_spk.id_inventory_unit = ssp.source_unit_id
                WHERE 1=1
                {$spkWhere}
                GROUP BY s.id, s.nomor_spk, s.pelanggan, s.dibuat_pada
            ";

            if ($source === 'WO') {
                $baseSql = $woSql;
            } elseif ($source === 'SPK') {
                $baseSql = $spkSql;
            } else {
                $baseSql = "({$woSql}) UNION ALL ({$spkSql})";
            }

            // Total count
            $countResult = $db->query("SELECT COUNT(*) as cnt FROM ({$baseSql}) AS combined")->getRowArray();
            $totalRecords = (int)($countResult['cnt'] ?? 0);

            // Paginated result
            $rows = $db->query(
                "SELECT * FROM ({$baseSql}) AS combined ORDER BY created_at DESC LIMIT {$length} OFFSET {$start}"
            )->getResultArray();

            $data = [];
            foreach ($rows as $row) {
                $data[] = [
                    'record_source'    => $row['record_source'],
                    'record_id'        => $row['record_id'],
                    'reference_number' => $row['reference_number'],
                    'report_date'      => $row['record_date']  ? date('d/m/Y', strtotime($row['record_date']))  : '-',
                    'created_at'       => $row['created_at']   ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-',
                    'customer_name'    => $row['customer_name'],
                    'unit_number'      => $row['unit_number'],
                    'unit_info'        => trim($row['merk_unit'] . ' ' . $row['model_unit']) ?: '-',
                    'total_items'      => (int)$row['total_items'],
                    'warehouse_items'  => (int)$row['warehouse_items'],
                    'nonwarehouse_items' => (int)$row['nonwarehouse_items'],
                ];
            }

            return $this->response->setJSON([
                'draw'            => intval($draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data'            => $data,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getUsageGrouped error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw'            => intval($draw),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get sparepart details for a specific SPK (expand row)
     */
    public function getSpkSpareparts(int $spkId)
    {
        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON([]);
        }

        try {
            $db = \Config\Database::connect();

            $spareparts = $db->table('spk_spareparts ssp')
                ->select('ssp.*, iu.no_unit as source_unit_number')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ssp.source_unit_id', 'left')
                ->where('ssp.spk_id', $spkId)
                ->get()->getResultArray();

            if (empty($spareparts)) {
                return $this->response->setJSON([]);
            }

            $spkInfo = $db->table('spk s')
                ->select('s.nomor_spk, DATE(s.dibuat_pada) as spk_date, s.pelanggan as customer_name,
                          iu.no_unit as unit_number, CONCAT_WS(" ", mu.merk_unit, mu.model_unit) as unit_info')
                ->join('kontrak k', 'k.id = s.kontrak_id', 'left')
                ->join('kontrak_unit ku', 'ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->where('s.id', $spkId)
                ->get()->getRowArray();

            $result = [];
            foreach ($spareparts as $item) {
                $result[] = [
                    'id'               => $item['id'],
                    'sparepart_code'   => $item['sparepart_code'] ?? '-',
                    'sparepart_name'   => $item['sparepart_name'],
                    'item_type'        => $item['item_type'] ?? 'sparepart',
                    'is_from_warehouse'=> (int)($item['is_from_warehouse'] ?? 1),
                    'source_type'      => $item['source_type'] ?? 'WAREHOUSE',
                    'source_unit_number' => $item['source_unit_number'] ?? null,
                    'quantity_brought' => $item['quantity_brought'],
                    'quantity_used'    => $item['quantity_used'] ?? 0,
                    'quantity_return'  => 0,
                    'usage_notes'      => $item['source_notes'] ?? $item['notes'] ?? '-',
                    'work_order_number'=> $spkInfo['nomor_spk'] ?? '-',
                    'report_date'      => isset($spkInfo['spk_date']) ? date('d/m/Y', strtotime($spkInfo['spk_date'])) : '-',
                    'mechanic_name'    => '-',
                    'customer_name'    => $spkInfo['customer_name'] ?? '-',
                    'unit_number'      => $spkInfo['unit_number']   ?? '-',
                    'unit_info'        => $spkInfo['unit_info']     ?? '-',
                ];
            }

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'getSpkSpareparts error: ' . $e->getMessage());
            return $this->response->setJSON([]);
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
            
            // Get all spareparts for this work order, join source unit for KANIBAL display
            $query = $db->table('work_order_spareparts wosp')
                ->select('wosp.*, iu_src.no_unit as source_unit_number')
                ->join('inventory_unit iu_src', 'iu_src.id_inventory_unit = wosp.source_unit_id', 'left')
                ->where('wosp.work_order_id', $workOrderId)
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
                ->join('customers c', 'c.id = k.customer_id', 'left')
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
                    'source_type' => $item['source_type'] ?? 'WAREHOUSE',
                    'source_unit_id' => $item['source_unit_id'] ?? null,
                    'source_unit_number' => $item['source_unit_number'] ?? null,
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
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.' . ' at line ' . $e->getLine());
            return $this->response->setJSON([]);
        }
    }

    /**
     * Get usage list (DataTable) - Tab Pemakaian
     */
    public function getUsage()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
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
                ->join('customers c', 'c.id = k.customer_id', 'left')
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
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
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
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $request    = $this->request;
        $draw       = $request->getPost('draw')   ?? 1;
        $start      = (int)($request->getPost('start')  ?? 0);
        $length     = (int)($request->getPost('length') ?? 25);
        $search     = $request->getPost('search')['value'] ?? '';
        $statusFilter = $request->getPost('status') ?? 'PENDING';
        $source     = $request->getPost('source') ?? 'ALL'; // 'WO', 'SPK', or 'ALL'

        try {
            $db = \Config\Database::connect();

            $searchEsc = $db->escapeLikeString($search);

            // --- WO returns sub-query ---
            $woStatusWhere  = $statusFilter !== 'ALL' ? "AND wosr.status = {$db->escape($statusFilter)}" : '';
            $woSearchWhere  = '';
            if (!empty($search)) {
                $woSearchWhere = "AND (wo.work_order_number LIKE '%{$searchEsc}%'
                                   OR wosr.sparepart_name  LIKE '%{$searchEsc}%'
                                   OR wosr.sparepart_code  LIKE '%{$searchEsc}%'
                                   OR c.customer_name      LIKE '%{$searchEsc}%'
                                   OR iu.no_unit           LIKE '%{$searchEsc}%')";
            }
            $woSql = "
                SELECT
                    'WO'                                        AS source_type,
                    wosr.id,
                    COALESCE(wo.work_order_number, '-')         AS reference_number,
                    wosr.sparepart_code,
                    wosr.sparepart_name,
                    COALESCE(wosp.item_type, 'sparepart')        AS item_type,
                    COALESCE(wosp.is_from_warehouse, 1)          AS is_from_warehouse,
                    wosr.quantity_brought,
                    wosr.quantity_used,
                    wosr.quantity_return,
                    wosr.satuan,
                    wosr.status,
                    wosr.return_notes,
                    wosr.created_at,
                    wosr.confirmed_at,
                    COALESCE(e.staff_name, '') AS mechanic_name,
                    COALESCE(c.customer_name, '-') AS customer_name,
                    COALESCE(iu.no_unit, '-')      AS unit_number,
                    COALESCE(u.username, '-')       AS confirmed_by_name,
                    wo.report_date
                FROM work_order_sparepart_returns wosr
                LEFT JOIN work_order_spareparts wosp ON wosp.id = wosr.work_order_sparepart_id
                LEFT JOIN work_orders wo          ON wo.id = wosr.work_order_id
                LEFT JOIN employees e             ON e.id  = wo.mechanic_id
                LEFT JOIN inventory_unit iu       ON iu.id_inventory_unit = wo.unit_id
                LEFT JOIN kontrak_unit ku         ON ku.unit_id = iu.id_inventory_unit
                                                 AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                                                 AND ku.is_temporary = 0
                LEFT JOIN kontrak k               ON k.id  = ku.kontrak_id
                LEFT JOIN customers c             ON c.id  = k.customer_id
                LEFT JOIN users u                 ON u.id  = wosr.confirmed_by
                WHERE 1=1 {$woStatusWhere} {$woSearchWhere}
            ";

            // --- SPK returns sub-query ---
            $spkStatusWhere = $statusFilter !== 'ALL' ? "AND ssr.status = {$db->escape($statusFilter)}" : '';
            $spkSearchWhere = '';
            if (!empty($search)) {
                $spkSearchWhere = "AND (s.nomor_spk         LIKE '%{$searchEsc}%'
                                    OR ssr.sparepart_name   LIKE '%{$searchEsc}%'
                                    OR ssr.sparepart_code   LIKE '%{$searchEsc}%'
                                    OR s.pelanggan          LIKE '%{$searchEsc}%')";
            }
            $spkSql = "
                SELECT
                    'SPK'                                       AS source_type,
                    ssr.id,
                    s.nomor_spk                                 AS reference_number,
                    ssr.sparepart_code,
                    ssr.sparepart_name,
                    ssr.item_type,
                    ssr.is_from_warehouse,
                    ssr.quantity_brought,
                    ssr.quantity_used,
                    ssr.quantity_return,
                    ssr.satuan,
                    ssr.status,
                    ssr.return_notes,
                    ssr.created_at,
                    ssr.confirmed_at,
                    ''                                          AS mechanic_name,
                    COALESCE(s.pelanggan, '-')                  AS customer_name,
                    COALESCE(iu.no_unit, '-')                   AS unit_number,
                    COALESCE(u.username, '-')                    AS confirmed_by_name,
                    DATE(s.dibuat_pada)                         AS report_date
                FROM spk_sparepart_returns ssr
                LEFT JOIN spk s                   ON s.id  = ssr.spk_id
                LEFT JOIN kontrak k               ON k.id  = s.kontrak_id
                LEFT JOIN kontrak_unit ku         ON ku.kontrak_id = k.id
                                                 AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                                                 AND ku.is_temporary = 0
                LEFT JOIN inventory_unit iu       ON iu.id_inventory_unit = ku.unit_id
                LEFT JOIN users u                 ON u.id  = ssr.confirmed_by
                WHERE 1=1 {$spkStatusWhere} {$spkSearchWhere}
            ";

            if ($source === 'WO') {
                $baseSqlReturns = $woSql;
            } elseif ($source === 'SPK') {
                $baseSqlReturns = $spkSql;
            } else {
                $baseSqlReturns = "({$woSql}) UNION ALL ({$spkSql})";
            }

            $totalRecords = (int)($db->query(
                "SELECT COUNT(*) AS cnt FROM ({$baseSqlReturns}) AS combined"
            )->getRowArray()['cnt'] ?? 0);

            $rows = $db->query(
                "SELECT * FROM ({$baseSqlReturns}) AS combined ORDER BY created_at DESC LIMIT {$length} OFFSET {$start}"
            )->getResultArray();

            $data = [];
            foreach ($rows as $row) {
                $data[] = [
                    'id'                => $row['id'],
                    'source_type'       => $row['source_type'],
                    'reference_number'  => $row['reference_number'] ?? '-',
                    'sparepart_code'    => $row['sparepart_code'] ?? '-',
                    'sparepart_name'    => $row['sparepart_name'] ?? '-',
                    'item_type'         => $row['item_type'] ?? 'sparepart',
                    'is_from_warehouse' => (int)($row['is_from_warehouse'] ?? 1),
                    'quantity_brought'  => $row['quantity_brought'],
                    'quantity_used'     => $row['quantity_used'],
                    'quantity_return'   => $row['quantity_return'],
                    'satuan'            => $row['satuan'] ?? 'PCS',
                    'status'            => $row['status'] ?? 'PENDING',
                    'return_notes'      => $row['return_notes'] ?? '',
                    'mechanic_name'     => $row['mechanic_name'] ?: '-',
                    'customer_name'     => $row['customer_name'] ?? '-',
                    'unit_number'       => $row['unit_number'] ?? '-',
                    'confirmed_by_name' => $row['confirmed_by_name'] ?? '-',
                    'created_at'        => $row['created_at']   ? date('d/m/Y H:i', strtotime($row['created_at']))   : '-',
                    'confirmed_at'      => $row['confirmed_at'] ? date('d/m/Y H:i', strtotime($row['confirmed_at'])) : '-',
                    'report_date'       => $row['report_date']  ? date('d/m/Y', strtotime($row['report_date']))       : '-',
                ];
            }

            return $this->response->setJSON([
                'draw'            => intval($draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data'            => $data,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'draw'            => intval($draw),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get return detail
     */
    public function getReturnDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $source = $this->request->getGet('source') ?? 'WO';

        try {
            if ($source === 'SPK') {
                $db = \Config\Database::connect();
                $return = $db->table('spk_sparepart_returns ssr')
                    ->select('
                        ssr.*,
                        s.nomor_spk as work_order_number,
                        DATE(s.dibuat_pada) as report_date,
                        COALESCE(s.pelanggan, \'-\') as customer_name,
                        COALESCE(iu.no_unit, \'-\') as unit_number,
                        \'\' as mechanic_name,
                        u.username as confirmed_by_name
                    ')
                    ->join('spk s', 's.id = ssr.spk_id', 'left')
                    ->join('kontrak k', 'k.id = s.kontrak_id', 'left')
                    ->join('kontrak_unit ku', 'ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                    ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id', 'left')
                    ->join('users u', 'u.id = ssr.confirmed_by', 'left')
                    ->where('ssr.id', $id)
                    ->get()->getRowArray();
            } else {
                $db = \Config\Database::connect();
                $return = $db->table('work_order_sparepart_returns wosr')
                    ->select('
                        wosr.*,
                        COALESCE(wo.work_order_number, \'-\') AS work_order_number,
                        DATE(wo.report_date)                  AS report_date,
                        COALESCE(wosp.item_type, \'sparepart\') AS item_type,
                        COALESCE(wosp.is_from_warehouse, 1)   AS is_from_warehouse,
                        COALESCE(e.staff_name, \'-\')          AS mechanic_name,
                        COALESCE(c.customer_name, \'-\')       AS customer_name,
                        COALESCE(iu.no_unit, \'-\')            AS unit_number,
                        COALESCE(u.username, \'-\')            AS confirmed_by_name
                    ')
                    ->join('work_order_spareparts wosp', 'wosp.id = wosr.work_order_sparepart_id', 'left')
                    ->join('work_orders wo',              'wo.id  = wosr.work_order_id',           'left')
                    ->join('employees e',                 'e.id   = wo.mechanic_id',               'left')
                    ->join('inventory_unit iu',           'iu.id_inventory_unit = wo.unit_id',     'left')
                    ->join('kontrak_unit ku',             'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                    ->join('kontrak k',                   'k.id  = ku.kontrak_id',                 'left')
                    ->join('customers c',                 'c.id  = k.customer_id',                 'left')
                    ->join('users u',                     'u.id  = wosr.confirmed_by',             'left')
                    ->where('wosr.id', $id)
                    ->get()->getRowArray();
            }

            if (!$return) {
                return $this->response->setJSON(['success' => false, 'message' => 'Return record not found']);
            }

            if (!empty($return['created_at'])) {
                $return['created_at_formatted'] = date('d/m/Y H:i', strtotime($return['created_at']));
            }
            if (!empty($return['confirmed_at'])) {
                $return['confirmed_at_formatted'] = date('d/m/Y H:i', strtotime($return['confirmed_at']));
            }
            if (!empty($return['report_date'])) {
                $return['report_date_formatted'] = date('d/m/Y', strtotime($return['report_date']));
            }

            return $this->response->setJSON(['success' => true, 'data' => $return]);

        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    /**
     * Get usage detail
     */
    public function getUsageDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
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
                ->join('customers c', 'c.id = k.customer_id', 'left')
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
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Confirm return
     */
    public function confirmReturn($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User belum login. Silakan login terlebih dahulu.']);
        }

        $notes = $this->request->getPost('notes') ?? null;

        try {
            $db = \Config\Database::connect();
            $return = $db->table('work_order_sparepart_returns')
                ->where('id', $id)
                ->get()->getRowArray();

            if (!$return) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Return record not found'
                ]);
            }

            if (($return['status'] ?? '') !== 'PENDING') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Return sudah dikonfirmasi atau tidak dalam status Pending'
                ]);
            }

            $updated = $db->table('work_order_sparepart_returns')->update([
                'status'       => 'CONFIRMED',
                'confirmed_by' => $userId,
                'confirmed_at' => date('Y-m-d H:i:s'),
                'return_notes' => $notes ?? $return['return_notes'],
            ], ['id' => $id]);

            if ($updated) {
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
            log_message('error', '[confirmReturn] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Confirm an SPK sparepart return (warehouse receives item back)
     */
    public function confirmSpkReturn($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User belum login. Silakan login terlebih dahulu.']);
        }

        $notes = $this->request->getPost('notes') ?? null;

        try {
            $db = \Config\Database::connect();

            $record = $db->table('spk_sparepart_returns')
                ->where('id', $id)
                ->get()->getRowArray();

            if (!$record) {
                return $this->response->setJSON(['success' => false, 'message' => 'SPK return record not found']);
            }

            if ($record['status'] !== 'PENDING') {
                return $this->response->setJSON(['success' => false, 'message' => 'Return sudah dikonfirmasi atau dibatalkan']);
            }

            $updated = $db->table('spk_sparepart_returns')->update(
                [
                    'status'       => 'CONFIRMED',
                    'confirmed_by' => $userId,
                    'confirmed_at' => date('Y-m-d H:i:s'),
                    'return_notes' => $notes ?? $record['return_notes'],
                ],
                ['id' => $id]
            );

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pengembalian sparepart SPK berhasil dikonfirmasi',
                ]);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengonfirmasi pengembalian']);

        } catch (\Exception $e) {
            log_message('error', 'confirmSpkReturn error. Silakan coba lagi.');
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    /**
     * Get non-warehouse (bekas/kanibal/manual) sparepart entries
     * Combines WO and SPK spareparts where is_from_warehouse = 0
     */
    public function getManualEntries()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $request    = $this->request;
        $draw       = $request->getPost('draw')   ?? 1;
        $start      = (int)($request->getPost('start')  ?? 0);
        $length     = (int)($request->getPost('length') ?? 25);
        $search     = $request->getPost('search')['value'] ?? '';
        $source     = $request->getPost('source') ?? 'ALL'; // 'WO', 'SPK', or 'ALL'

        try {
            $db        = \Config\Database::connect();
            $searchEsc = $db->escapeLikeString($search);

            $woSearchWhere = '';
            if (!empty($search)) {
                $woSearchWhere = "AND (wo.work_order_number LIKE '%{$searchEsc}%'
                                   OR wosp.sparepart_name  LIKE '%{$searchEsc}%'
                                   OR c.customer_name      LIKE '%{$searchEsc}%')";
            }
            $woSql = "
                SELECT
                    'WO'                               AS source_type,
                    wo.work_order_number               AS reference_number,
                    wosp.sparepart_name                AS item_name,
                    wosp.item_type,
                    wosp.source_type                   AS item_source,
                    COALESCE(wosp.source_notes,'')     AS source_notes,
                    wosp.quantity_brought,
                    wosp.satuan,
                    COALESCE(c.customer_name, '-')     AS customer_name,
                    COALESCE(iu.no_unit, '-')          AS unit_number,
                    wosp.created_at
                FROM work_order_spareparts wosp
                INNER JOIN work_orders wo         ON wo.id = wosp.work_order_id
                LEFT  JOIN inventory_unit iu      ON iu.id_inventory_unit = wo.unit_id
                LEFT  JOIN kontrak_unit ku        ON ku.unit_id = iu.id_inventory_unit
                                                 AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                                                 AND ku.is_temporary = 0
                LEFT  JOIN kontrak k              ON k.id  = ku.kontrak_id
                LEFT  JOIN customers c            ON c.id  = k.customer_id
                WHERE wosp.is_from_warehouse = 0
                {$woSearchWhere}
            ";

            $spkSearchWhere = '';
            if (!empty($search)) {
                $spkSearchWhere = "AND (s.nomor_spk           LIKE '%{$searchEsc}%'
                                    OR ssp.sparepart_name     LIKE '%{$searchEsc}%'
                                    OR s.pelanggan            LIKE '%{$searchEsc}%')";
            }
            $spkSql = "
                SELECT
                    'SPK'                              AS source_type,
                    s.nomor_spk                        AS reference_number,
                    ssp.sparepart_name                 AS item_name,
                    ssp.item_type,
                    ssp.source_type                    AS item_source,
                    COALESCE(ssp.source_notes,'')      AS source_notes,
                    ssp.quantity_brought,
                    ssp.satuan,
                    COALESCE(s.pelanggan, '-')         AS customer_name,
                    COALESCE(iu.no_unit, '-')          AS unit_number,
                    ssp.created_at
                FROM spk_spareparts ssp
                INNER JOIN spk s                  ON s.id  = ssp.spk_id
                LEFT  JOIN kontrak k              ON k.id  = s.kontrak_id
                LEFT  JOIN kontrak_unit ku        ON ku.kontrak_id = k.id
                                                 AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
                                                 AND ku.is_temporary = 0
                LEFT  JOIN inventory_unit iu      ON iu.id_inventory_unit = ku.unit_id
                WHERE ssp.is_from_warehouse = 0
                {$spkSearchWhere}
            ";

            if ($source === 'WO') {
                $baseSqlManual = $woSql;
            } elseif ($source === 'SPK') {
                $baseSqlManual = $spkSql;
            } else {
                $baseSqlManual = "({$woSql}) UNION ALL ({$spkSql})";
            }

            $totalRecords = (int)($db->query(
                "SELECT COUNT(*) AS cnt FROM ({$baseSqlManual}) AS combined"
            )->getRowArray()['cnt'] ?? 0);

            $rows = $db->query(
                "SELECT * FROM ({$baseSqlManual}) AS combined ORDER BY created_at DESC LIMIT {$length} OFFSET {$start}"
            )->getResultArray();

            $data = [];
            foreach ($rows as $row) {
                $data[] = [
                    'source_type'      => $row['source_type'],
                    'reference_number' => $row['reference_number'] ?? '-',
                    'item_name'        => $row['item_name'] ?? '-',
                    'item_type'        => $row['item_type'] ?? 'sparepart',
                    'item_source'      => $row['item_source'] ?? 'BEKAS',
                    'source_notes'     => $row['source_notes'] ?? '-',
                    'quantity_brought' => $row['quantity_brought'],
                    'satuan'           => $row['satuan'] ?? 'PCS',
                    'customer_name'    => $row['customer_name'] ?? '-',
                    'unit_number'      => $row['unit_number'] ?? '-',
                    'created_at'       => $row['created_at'] ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-',
                ];
            }

            return $this->response->setJSON([
                'draw'            => intval($draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data'            => $data,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getManualEntries error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw'            => intval($draw),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage(),
            ]);
        }
    }
}

