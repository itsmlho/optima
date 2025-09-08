<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ActivityLogViewer extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Activity Log | OPTIMA',
            'page_title' => 'System Activity Log',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/admin/activity-log' => 'Activity Log'
            ]
        ];

        return view('admin/activity_log', $data);
    }

    public function getData()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        
        // DataTables parameters
        $draw = $request->getPost('draw');
        $start = $request->getPost('start') ?: 0;
        $length = $request->getPost('length') ?: 25;
        $searchValue = $request->getPost('search')['value'] ?? '';
        $orderColumn = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';

        // Base query - menggunakan struktur tabel yang sudah dioptimasi
        $builder = $db->table('system_activity_log sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sal.user_id', 'left');

        // Search functionality
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('sal.action_description', $searchValue)
                ->orLike('sal.table_name', $searchValue)
                ->orLike('sal.action_type', $searchValue)
                ->orLike('sal.module_name', $searchValue)
                ->orLike('u.username', $searchValue)
                ->groupEnd();
        }

        // Count total records
        $totalRecords = $db->table('system_activity_log')->countAllResults();
        
        // Count filtered records
        $filteredRecords = $builder->countAllResults(false);

        // Order mapping
        $columns = ['sal.created_at', 'u.username', 'sal.module_name', 'sal.action_type', 'sal.table_name', 'sal.action_description', 'sal.business_impact'];
        $orderBy = $columns[$orderColumn] ?? 'sal.created_at';
        
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        $query = $builder->get();
        $data = [];

        foreach ($query->getResultArray() as $row) {
            $data[] = [
                'created_at' => date('d/m/Y H:i:s', strtotime($row['created_at'])),
                'username' => $row['username'] ?? 'System',
                'module_name' => $row['module_name'] ?? '-',
                'action_type' => '<span class="badge bg-' . $this->getActionBadgeColor($row['action_type']) . '">' . $row['action_type'] . '</span>',
                'table_name' => $row['table_name'],
                'record_id' => $row['record_id'],
                'action_description' => $row['action_description'],
                'business_impact' => '<span class="badge bg-' . $this->getImpactBadgeColor($row['business_impact']) . '">' . $row['business_impact'] . '</span>',
                'is_critical' => $row['is_critical'] ? '<i class="fas fa-exclamation-triangle text-warning"></i>' : '',
                'actions' => '<button class="btn btn-sm btn-info" onclick="viewDetails(' . $row['id'] . ')"><i class="fas fa-eye"></i></button>'
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function getDetails($id)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('system_activity_log sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sal.user_id', 'left');
        $builder->where('sal.id', $id);
        
        $row = $builder->get()->getRowArray();
        
        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'id' => $row['id'],
                'created_at' => date('d F Y H:i:s', strtotime($row['created_at'])),
                'username' => $row['username'] ?? 'System',
                'full_name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                'module_name' => $row['module_name'],
                'action_type' => $row['action_type'],
                'table_name' => $row['table_name'],
                'record_id' => $row['record_id'],
                'action_description' => $row['action_description'],
                'old_values' => $row['old_values'] ? json_decode($row['old_values'], true) : null,
                'new_values' => $row['new_values'] ? json_decode($row['new_values'], true) : null,
                'affected_fields' => $row['affected_fields'] ? json_decode($row['affected_fields'], true) : null,
                'workflow_stage' => $row['workflow_stage'],
                'business_impact' => $row['business_impact'],
                'is_critical' => $row['is_critical']
            ]
        ]);
    }

    private function getActionBadgeColor($action)
    {
        switch ($action) {
            case 'CREATE': return 'success';
            case 'UPDATE': return 'warning';
            case 'DELETE': return 'danger';
            case 'PRINT': return 'info';
            case 'DOWNLOAD': return 'secondary';
            default: return 'primary';
        }
    }

    private function getImpactBadgeColor($impact)
    {
        switch ($impact) {
            case 'LOW': return 'success';
            case 'MEDIUM': return 'warning';
            case 'HIGH': return 'danger';
            case 'CRITICAL': return 'dark';
            default: return 'secondary';
        }
    }
}
