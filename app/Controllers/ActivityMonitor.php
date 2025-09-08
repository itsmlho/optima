<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use Exception;

class ActivityMonitor extends BaseController
{
    protected $activityLogModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Activity Monitor | OPTIMA',
            'page_title' => 'Activity Monitoring Dashboard',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/admin/activity-monitor' => 'Activity Monitor'
            ]
        ];

        return view('admin/activity_monitor', $data);
    }

    public function getData()
    {
        $request = service('request');
        
        // DataTables parameters with additional filters
        $draw = $request->getPost('draw');
        $requestData = [
            'start' => $request->getPost('start') ?: 0,
            'length' => $request->getPost('length') ?: 15,
            'search' => $request->getPost('search') ?: ['value' => ''],
            'order' => $request->getPost('order') ?: [['column' => 0, 'dir' => 'desc']],
            'timeRange' => $request->getPost('timeRange') ?: '24h',
            'module' => $request->getPost('module') ?: '',
            'action' => $request->getPost('action') ?: '',
            'user' => $request->getPost('user') ?: ''
        ];

        // Get data from model with filters
        $result = $this->activityLogModel->getFilteredDataTablesData($requestData);
        
        // Format data for monitoring view
        $data = [];
        foreach ($result['data'] as $row) {
            $data[] = $this->formatMonitoringRow($row);
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $data
        ]);
    }

    private function formatMonitoringRow($row)
    {
        $username = !empty($row['username']) ? $row['username'] : 'System';
        
        // Format business impact badge with monitoring focus
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
            'LOGOUT' => 'light',
            'ASSIGN' => 'primary'
        ];
        $actionColor = $actionColors[$row['action_type']] ?? 'secondary';
        $actionType = '<span class="badge bg-' . $actionColor . '">' . $row['action_type'] . '</span>';

        // Format status indicator
        $statusIcon = $row['is_critical'] ? 
            '<i class="fas fa-exclamation-triangle text-danger" title="Critical"></i>' : 
            '<i class="fas fa-check-circle text-success" title="Normal"></i>';

        // Format created_at
        $createdAt = date('H:i:s', strtotime($row['created_at'])) . '<br><small>' . date('d/m/Y', strtotime($row['created_at'])) . '</small>';

        return [
            'created_at' => $createdAt,
            'username' => $username,
            'module_name' => $row['module_name'],
            'action_type' => $actionType,
            'action_description' => $row['action_description'],
            'business_impact' => $businessImpact,
            'is_critical' => $statusIcon,
            'id' => $row['id'] // For detail modal
        ];
    }

    public function statistics()
    {
        $period = $this->request->getGet('period') ?: '24h';
        
        // Convert period to SQL format
        $periodMap = [
            '1h' => '1 hour',
            '24h' => '24 hours', 
            '7d' => '7 days',
            '30d' => '30 days'
        ];
        $sqlPeriod = $periodMap[$period] ?? '24 hours';
        
        $stats = $this->activityLogModel->getMonitoringStatistics($sqlPeriod);
        
        // Add active users count (users who performed actions in the period)
        $stats['active_users'] = $this->activityLogModel->getActiveUsersCount($sqlPeriod);
        
        // Generate trend data for charts
        $stats['trend'] = $this->activityLogModel->getActivityTrend($sqlPeriod);
        
        return $this->response->setJSON($stats);
    }

    public function recent()
    {
        $limit = $this->request->getGet('limit') ?: 10;
        $recentActivities = $this->activityLogModel->getRecentActivities($limit);
        
        return $this->response->setJSON($recentActivities);
    }

    public function details($id)
    {
        $result = $this->activityLogModel->getLogWithUser($id);
        
        if (!$result) {
            return $this->response->setJSON(['error' => 'Activity not found'], 404);
        }
        
        return $this->response->setJSON($result);
    }

    public function export()
    {
        $request = service('request');
        $format = $request->getGet('format') ?: 'csv';
        
        // Get filter parameters
        $filters = [
            'timeRange' => $request->getGet('timeRange') ?: '24h',
            'module' => $request->getGet('module') ?: '',
            'action' => $request->getGet('action') ?: '',
            'user' => $request->getGet('user') ?: ''
        ];
        
        // Get all filtered data
        $result = $this->activityLogModel->getFilteredExportData($filters);
        
        if ($format === 'csv') {
            return $this->exportMonitoringCSV($result, $filters);
        } else {
            return $this->exportMonitoringJSON($result, $filters);
        }
    }

    private function exportMonitoringCSV($data, $filters)
    {
        $filename = 'activity_monitor_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add report header with filter info
        fputcsv($output, ['OPTIMA Activity Monitoring Report']);
        fputcsv($output, ['Generated:', date('Y-m-d H:i:s')]);
        fputcsv($output, ['Time Range:', $filters['timeRange']]);
        fputcsv($output, ['Module Filter:', $filters['module'] ?: 'All']);
        fputcsv($output, ['Action Filter:', $filters['action'] ?: 'All']);
        fputcsv($output, ['User Filter:', $filters['user'] ?: 'All']);
        fputcsv($output, []); // Empty row
        
        // CSV Headers
        fputcsv($output, [
            'Date/Time', 'User', 'Module', 'Action', 'Table', 'Record ID', 
            'Description', 'Business Impact', 'Critical', 'IP Address', 'Session ID'
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
                $row['ip_address'],
                $row['session_id']
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportMonitoringJSON($data, $filters)
    {
        $filename = 'activity_monitor_report_' . date('Y-m-d_H-i-s') . '.json';
        
        $export = [
            'report_info' => [
                'title' => 'OPTIMA Activity Monitoring Report',
                'generated' => date('Y-m-d H:i:s'),
                'filters' => $filters,
                'total_records' => count($data)
            ],
            'activities' => $data
        ];
        
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        return $this->response->setJSON($export);
    }

    public function healthCheck()
    {
        // System health check endpoint
        $health = [
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'database' => $this->checkDatabaseHealth(),
            'logs' => $this->checkLogHealth(),
            'system' => $this->checkSystemHealth()
        ];
        
        return $this->response->setJSON($health);
    }

    private function checkDatabaseHealth()
    {
        try {
            $count = $this->activityLogModel->countAll();
            return [
                'status' => 'ok',
                'total_logs' => $count
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed'
            ];
        }
    }

    private function checkLogHealth()
    {
        $recentCount = $this->activityLogModel->getRecentCount('1 hour');
        $criticalCount = $this->activityLogModel->getCriticalCount('24 hours');
        
        return [
            'recent_activity' => $recentCount,
            'critical_activities_24h' => $criticalCount,
            'health_score' => $criticalCount > 10 ? 'warning' : 'good'
        ];
    }

    private function checkSystemHealth()
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'server_time' => date('Y-m-d H:i:s')
        ];
    }
}
