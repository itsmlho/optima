<?php

namespace App\Controllers;

class SimpleActivityLog extends BaseController
{
    public function __construct()
    {
        helper('simple_activity_log');
    }
    
    /**
     * Halaman utama Activity Log
     */
    public function index()
    {
        $data = [
            'title' => 'Activity Log - Semua Aktivitas System'
        ];
        
        return view('admin/simple_activity_log', $data);
    }
    
    /**
     * API untuk DataTables - Get activity logs dengan filter
     */
    public function getDataTable()
    {
        $request = service('request');
        
        // Get DataTables parameters
        $draw = $request->getPost('draw');
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 25;
        $search = $request->getPost('search')['value'] ?? '';
        
        // Get filter parameters
        $filterUsername = $request->getPost('filter_username') ?? '';
        $filterAction = $request->getPost('filter_action') ?? '';
        $filterModule = $request->getPost('filter_module') ?? '';
        $filterDateFrom = $request->getPost('filter_date_from') ?? '';
        $filterDateTo = $request->getPost('filter_date_to') ?? '';
        
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('system_activity_log');
            
            // Apply filters
            if (!empty($filterUsername)) {
                $builder->like('username', $filterUsername);
            }
            
            if (!empty($filterAction)) {
                $builder->where('action_type', $filterAction);
            }
            
            if (!empty($filterModule)) {
                $builder->where('module_name', $filterModule);
            }
            
            if (!empty($filterDateFrom)) {
                $builder->where('created_at >=', $filterDateFrom . ' 00:00:00');
            }
            
            if (!empty($filterDateTo)) {
                $builder->where('created_at <=', $filterDateTo . ' 23:59:59');
            }
            
            // Global search
            if (!empty($search)) {
                $builder->groupStart()
                    ->like('username', $search)
                    ->orLike('description', $search)
                    ->orLike('table_name', $search)
                    ->orLike('module_name', $search)
                    ->groupEnd();
            }
            
            // Get total records before limit
            $totalRecords = $builder->countAllResults(false);
            
            // Apply pagination and ordering
            $builder->orderBy('created_at', 'DESC');
            $builder->limit($length, $start);
            
            $logs = $builder->get()->getResultArray();
            
            // Format data for DataTables
            $data = [];
            foreach ($logs as $log) {
                $actionBadge = $this->getActionBadge($log['action_type']);
                $moduleBadge = $this->getModuleBadge($log['module_name']);
                
                $data[] = [
                    'id' => $log['id'],
                    'username' => '<strong>' . esc($log['username']) . '</strong>',
                    'action' => $actionBadge,
                    'module' => $moduleBadge,
                    'description' => esc($log['description']),
                    'table_record' => $log['table_name'] ? esc($log['table_name']) . 
                                     ($log['record_id'] ? ' #' . $log['record_id'] : '') : '-',
                    'file_info' => $log['file_name'] ? '<small>📁 ' . esc($log['file_name']) . 
                                  ($log['file_type'] ? ' (' . $log['file_type'] . ')' : '') . '</small>' : '-',
                    'created_at' => date('d/m/Y H:i:s', strtotime($log['created_at'])),
                    'ip_address' => $log['ip_address'] ?? '-'
                ];
            }
            
            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Helper untuk badge action type
     */
    private function getActionBadge($action)
    {
        $badges = [
            'CREATE' => '<span class="badge bg-success">CREATE</span>',
            'READ' => '<span class="badge bg-info">READ</span>',
            'UPDATE' => '<span class="badge bg-warning">UPDATE</span>',
            'DELETE' => '<span class="badge bg-danger">DELETE</span>',
            'PRINT' => '<span class="badge bg-primary">PRINT</span>',
            'DOWNLOAD' => '<span class="badge bg-secondary">DOWNLOAD</span>',
            'LOGIN' => '<span class="badge bg-success">LOGIN</span>',
            'LOGOUT' => '<span class="badge bg-dark">LOGOUT</span>'
        ];
        
        return $badges[$action] ?? '<span class="badge bg-light">' . $action . '</span>';
    }
    
    /**
     * Helper untuk badge module
     */
    private function getModuleBadge($module)
    {
        if (!$module) return '-';
        
        $colors = [
            'Marketing' => 'primary',
            'Service' => 'info',
            'Operational' => 'warning',
            'Accounting' => 'success',
            'Purchasing' => 'danger',
            'Warehouse' => 'secondary',
            'Perizinan' => 'dark',
            'Admin' => 'light',
            'Reports' => 'outline-primary'
        ];
        
        $color = $colors[$module] ?? 'outline-secondary';
        return '<span class="badge bg-' . $color . '">' . $module . '</span>';
    }
}
