<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;
use App\Models\PermissionModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ActivityLogController extends BaseController
{
    protected $activityLogModel;
    protected $permissionModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->permissionModel = new PermissionModel();
    }

    /**
     * Display activity log dashboard
     */
    public function index()
    {
        // Check if user has permission to view activity logs
        if (!$this->hasPermission('logs.view')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $data = [
            'title' => 'Activity Logs',
            'recentActivities' => $this->activityLogModel->getRecentActivities(10),
            'activitySummary' => $this->activityLogModel->getActivitySummary()
        ];

        return view('admin/activity_logs/index', $data);
    }

    /**
     * Display activity logs list with filters
     */
    public function list()
    {
        // Check if user has permission to view activity logs
        if (!$this->hasPermission('logs.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $filters = [
            'user_id' => $this->request->getGet('user_id'),
            'action' => $this->request->getGet('action'),
            'entity_type' => $this->request->getGet('entity_type'),
            'entity_id' => $this->request->getGet('entity_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search'),
            'limit' => $this->request->getGet('limit') ?? 50,
            'offset' => $this->request->getGet('offset') ?? 0
        ];

        $logs = $this->activityLogModel->getActivityLogs($filters);
        $total = $this->activityLogModel->getActivityLogsCount($filters);

        return $this->response->setJSON([
            'success' => true,
            'data' => $logs,
            'total' => $total,
            'filters' => $filters
        ]);
    }

    /**
     * Export activity logs
     */
    public function export()
    {
        // Check if user has permission to export activity logs
        if (!$this->hasPermission('logs.export')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $filters = [
            'user_id' => $this->request->getGet('user_id'),
            'action' => $this->request->getGet('action'),
            'entity_type' => $this->request->getGet('entity_type'),
            'entity_id' => $this->request->getGet('entity_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        $logs = $this->activityLogModel->getActivityLogs($filters);

        // Create CSV content
        $csvContent = "User,Action,Entity Type,Entity ID,Details,IP Address,User Agent,Created At\n";
        
        foreach ($logs as $log) {
            $csvContent .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log['user_name'] ?? 'System',
                $log['action'],
                $log['entity_type'],
                $log['entity_id'] ?? '',
                str_replace('"', '""', $log['details'] ?? ''),
                $log['ip_address'],
                str_replace('"', '""', $log['user_agent'] ?? ''),
                $log['created_at']
            );
        }

        // Set headers for CSV download
        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csvContent);
    }

    /**
     * Get activity log details
     */
    public function detail($id = null)
    {
        // Check if user has permission to view activity logs
        if (!$this->hasPermission('logs.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $log = $this->activityLogModel->find($id);
        if (!$log) {
            return $this->response->setJSON(['error' => 'Activity log tidak ditemukan'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $log
        ]);
    }

    /**
     * Get activity summary
     */
    public function summary()
    {
        // Check if user has permission to view activity logs
        if (!$this->hasPermission('logs.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $filters = [
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $summary = $this->activityLogModel->getActivitySummary($filters);

        return $this->response->setJSON([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get recent activities
     */
    public function recent()
    {
        // Check if user has permission to view activity logs
        if (!$this->hasPermission('logs.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $limit = $this->request->getGet('limit') ?? 10;
        $activities = $this->activityLogModel->getRecentActivities($limit);

        return $this->response->setJSON([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Clean old activity logs
     */
    public function clean()
    {
        // Check if user has permission to manage activity logs
        if (!$this->hasPermission('logs.manage')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $days = $this->request->getPost('days') ?? 90;
        
        if ($days < 30) {
            return $this->response->setJSON([
                'error' => 'Minimal 30 hari untuk pembersihan log'
            ])->setStatusCode(400);
        }

        $deletedCount = $this->activityLogModel->cleanOldLogs($days);

        // Log the cleanup activity
        $this->activityLogModel->logActivity([
            'user_id' => session()->get('user_id'),
            'action' => 'Clean Activity Logs',
            'entity_type' => 'activity_log',
            'details' => json_encode([
                'days' => $days,
                'deleted_count' => $deletedCount
            ])
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => "Berhasil menghapus {$deletedCount} log aktivitas yang lebih dari {$days} hari"
        ]);
    }

    /**
     * Get available actions for filtering
     */
    public function getActions()
    {
        // Check if user has permission to view activity logs
        if (!$this->hasPermission('logs.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $builder = $this->activityLogModel->builder();
        $actions = $builder->select('DISTINCT action')
                          ->orderBy('action', 'ASC')
                          ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => array_column($actions, 'action')
        ]);
    }

    /**
     * Get available entity types for filtering
     */
    public function getEntityTypes()
    {
        // Check if user has permission to view activity logs
        if (!$this->hasPermission('logs.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $builder = $this->activityLogModel->builder();
        $entityTypes = $builder->select('DISTINCT entity_type')
                              ->orderBy('entity_type', 'ASC')
                              ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => array_column($entityTypes, 'entity_type')
        ]);
    }

    /**
     * Get activity statistics
     */
    public function statistics()
    {
        // Check if user has permission to view activity logs
        if (!$this->hasPermission('logs.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];

        $summary = $this->activityLogModel->getActivitySummary($filters);
        $totalLogs = $this->activityLogModel->getActivityLogsCount($filters);

        // Get top users by activity
        $builder = $this->activityLogModel->builder();
        $builder->select('users.name, COUNT(*) as activity_count')
               ->join('users', 'users.user_id = activity_logs.user_id', 'left')
               ->where('activity_logs.created_at >=', $dateFrom)
               ->where('activity_logs.created_at <=', $dateTo . ' 23:59:59')
               ->groupBy('activity_logs.user_id, users.name')
               ->orderBy('activity_count', 'DESC')
               ->limit(10);

        $topUsers = $builder->findAll();

        $statistics = [
            'total_logs' => $totalLogs,
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'action_summary' => $summary,
            'top_users' => $topUsers
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Check if current user has specific permission
     */
    private function hasPermission($permissionKey)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return false;
        }

        return $this->permissionModel->userHasPermission($userId, $permissionKey);
    }
} 