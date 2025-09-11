<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;

class ActivityLog extends BaseController
{
    protected $activityLogModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Activity Log',
            'page_title' => 'System Activity Log',
            'breadcrumbs' => [
                '/dashboard' => 'Dashboard',
                '/admin/activity-log' => 'Activity Log'
            ],
            'hide_breadcrumb' => false
        ];

        return view('admin/activity_log', $data);
    }

    public function getData()
    {
        $request = service('request');
        
        // DataTables parameters
        $draw = $request->getPost('draw');
        $requestData = [
            'start' => $request->getPost('start') ?: 0,
            'length' => $request->getPost('length') ?: 25,
            'search' => $request->getPost('search') ?: ['value' => ''],
            'order' => $request->getPost('order') ?: [['column' => 0, 'dir' => 'desc']]
        ];

        // Get data from model
        $result = $this->activityLogModel->getDataTablesData($requestData);
        
        // Format data for DataTables
        $data = [];
        foreach ($result['data'] as $row) {
            $data[] = $this->formatDataTableRow($row);
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $data
        ]);
    }

    private function formatDataTableRow($row)
    {
        $username = !empty($row['username']) ? $row['username'] : 'System';
        
        // Format business impact badge
        $impactColors = [
            'LOW' => 'success',
            'MEDIUM' => 'warning', 
            'HIGH' => 'danger',
            'CRITICAL' => 'dark'
        ];
        $impactColor = $impactColors[$row['business_impact']] ?? 'secondary';
        $businessImpact = '<span class="badge bg-' . $impactColor . '">' . $row['business_impact'] . '</span>';

        // Format action type badge
        $actionColors = [
            'CREATE' => 'success',
            'UPDATE' => 'primary',
            'DELETE' => 'danger',
            'PRINT' => 'info',
            'DOWNLOAD' => 'secondary',
            'LOGIN' => 'warning',
            'LOGOUT' => 'warning',
            'ASSIGN' => 'primary'
        ];
        $actionColor = $actionColors[$row['action_type']] ?? 'secondary';
        $actionType = '<span class="badge bg-' . $actionColor . '">' . $row['action_type'] . '</span>';

        // Format critical indicator
        $isCritical = $row['is_critical'] ? '<i class="fas fa-exclamation-triangle text-warning"></i>' : '';

        // Format created_at
        $createdAt = date('d/m/Y H:i:s', strtotime($row['created_at']));

        return [
            'created_at' => $createdAt,
            'username' => $username,
            'module_name' => $row['module_name'],
            'action_type' => $actionType,
            'table_name' => $row['table_name'],
            'record_id' => $row['record_id'],
            'action_description' => $row['action_description'],
            'business_impact' => $businessImpact,
            'is_critical' => $isCritical,
            'actions' => '<button class="btn btn-sm btn-info" onclick="viewDetails(' . $row['id'] . ')"><i class="fas fa-eye"></i></button>'
        ];
    }

    public function details($id)
    {
        $result = $this->activityLogModel->getLogWithUser($id);
        
        if (!$result) {
            return $this->response->setJSON(['error' => 'Record not found'], 404);
        }
        
        return $this->response->setJSON($result);
    }

    public function statistics()
    {
        $period = $this->request->getGet('period') ?: '7 days';
        $stats = $this->activityLogModel->getStatistics($period);
        
        return $this->response->setJSON($stats);
    }

    public function export()
    {
        $request = service('request');
        $format = $request->getGet('format') ?: 'csv';
        
        // Get all data (without pagination)
        $requestData = [
            'start' => 0,
            'length' => 999999, // Large number to get all records
            'search' => ['value' => ''],
            'order' => [['column' => 0, 'dir' => 'desc']]
        ];

        $result = $this->activityLogModel->getDataTablesData($requestData);
        
        if ($format === 'csv') {
            return $this->exportCSV($result['data']);
        } else {
            return $this->exportJSON($result['data']);
        }
    }

    private function exportCSV($data)
    {
        $filename = 'activity_log_' . date('Y-m-d_H-i-s') . '.csv';
        
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'Date/Time', 'User', 'Module', 'Action', 'Table', 'Record ID', 
            'Description', 'Impact', 'Critical', 'IP Address'
        ]);
        
        foreach ($data as $row) {
            fputcsv($output, [
                $row['created_at'],
                $row['username'] ?: 'System',
                $row['module_name'],
                $row['action_type'],
                $row['table_name'],
                $row['record_id'],
                $row['action_description'],
                $row['business_impact'],
                $row['is_critical'] ? 'Yes' : 'No',
                $row['ip_address']
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportJSON($data)
    {
        $filename = 'activity_log_' . date('Y-m-d_H-i-s') . '.json';
        
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        return $this->response->setJSON($data);
    }

    /**
     * Clean old logs - Admin function
     */
    public function clean()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $days = $this->request->getPost('days') ?: 90;
        $deletedCount = $this->activityLogModel->cleanOldLogs($days);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => "Successfully deleted $deletedCount old log entries",
            'deleted_count' => $deletedCount
        ]);
    }
}
