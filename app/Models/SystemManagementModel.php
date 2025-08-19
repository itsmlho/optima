<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemManagementModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'log_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'activity_type', 'description', 'entity_type',
        'entity_id', 'ip_address', 'user_agent', 'session_id',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'activity_type' => 'required|max_length[50]',
        'description' => 'required|max_length[500]',
        'entity_type' => 'permit_empty|max_length[50]',
        'entity_id' => 'permit_empty|integer',
        'ip_address' => 'permit_empty|max_length[45]',
        'user_agent' => 'permit_empty|max_length[500]',
        'session_id' => 'permit_empty|max_length[100]'
    ];

    protected $validationMessages = [];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Activity Log Methods
    public function logActivity($data)
    {
        return $this->insert($data);
    }

    public function getRecentActivities($limit = 10)
    {
        $builder = $this->builder();
        $builder->select('al.*, u.first_name, u.last_name, u.email')
               ->join('users u', 'u.id = al.user_id', 'left')
               ->orderBy('al.created_at', 'DESC')
               ->limit($limit);
        
        return $builder->get()->getResultArray();
    }

    public function getFilteredLogs($filters)
    {
        $builder = $this->builder();
        $builder->select('al.*, u.first_name, u.last_name, u.email')
               ->join('users u', 'u.id = al.user_id', 'left');
        
        if (!empty($filters['user'])) {
            $builder->where('al.user_id', $filters['user']);
        }

        if (!empty($filters['action'])) {
            $builder->where('al.activity_type', $filters['action']);
        }

        if (!empty($filters['entity_type'])) {
            $builder->where('al.entity_type', $filters['entity_type']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('al.created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('al.created_at <=', $filters['date_to']);
        }

        if (!empty($filters['limit'])) {
            $builder->limit($filters['limit']);
        }

        if (!empty($filters['offset'])) {
            $builder->offset($filters['offset']);
        }

        return $builder->orderBy('al.created_at', 'DESC')->get()->getResultArray();
    }

    public function getTotalLogs($filters)
    {
        $builder = $this->builder();
        
        if (!empty($filters['user'])) {
            $builder->where('user_id', $filters['user']);
        }

        if (!empty($filters['action'])) {
            $builder->where('activity_type', $filters['action']);
        }

        if (!empty($filters['entity_type'])) {
            $builder->where('entity_type', $filters['entity_type']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to']);
        }

        return $builder->countAllResults();
    }

    public function getAvailableActions()
    {
        return $this->distinct()->select('activity_type')->findAll();
    }

    public function getAvailableEntityTypes()
    {
        return $this->distinct()->select('entity_type')->where('entity_type IS NOT NULL')->findAll();
    }

    public function getLogStatistics($filters)
    {
        $stats = [];
        
        // Activity type statistics
        $activityTypes = $this->select('activity_type, COUNT(*) as count')
                             ->groupBy('activity_type')
                             ->findAll();
        
        foreach ($activityTypes as $type) {
            $stats['activity_types'][$type['activity_type']] = $type['count'];
        }
        
        // User activity statistics
        $userStats = $this->select('user_id, COUNT(*) as count')
                         ->groupBy('user_id')
                         ->orderBy('count', 'DESC')
                         ->limit(10)
                         ->findAll();
        
        $stats['top_users'] = $userStats;
        
        // Daily activity statistics
        $dailyStats = $this->select('DATE(created_at) as date, COUNT(*) as count')
                          ->groupBy('DATE(created_at)')
                          ->orderBy('date', 'DESC')
                          ->limit(30)
                          ->findAll();
        
        $stats['daily_activity'] = $dailyStats;
        
        return $stats;
    }

    // Data Classification Methods
    public function getAllClassifications()
    {
        return $this->db->table('data_classifications')->findAll();
    }

    public function getClassificationById($classificationId)
    {
        return $this->db->table('data_classifications')->where('classification_id', $classificationId)->get()->getRowArray();
    }

    public function createClassification($data)
    {
        return $this->db->table('data_classifications')->insert($data);
    }

    public function updateClassification($classificationId, $data)
    {
        return $this->db->table('data_classifications')->where('classification_id', $classificationId)->update($data);
    }

    public function deleteClassification($classificationId)
    {
        return $this->db->table('data_classifications')->where('classification_id', $classificationId)->delete();
    }

    // Approval Workflow Methods
    public function getAllWorkflows()
    {
        return $this->db->table('approval_workflows')->findAll();
    }

    public function getWorkflowById($workflowId)
    {
        return $this->db->table('approval_workflows')->where('workflow_id', $workflowId)->get()->getRowArray();
    }

    public function createWorkflow($data)
    {
        return $this->db->table('approval_workflows')->insert($data);
    }

    public function updateWorkflow($workflowId, $data)
    {
        return $this->db->table('approval_workflows')->where('workflow_id', $workflowId)->update($data);
    }

    public function deleteWorkflow($workflowId)
    {
        return $this->db->table('approval_workflows')->where('workflow_id', $workflowId)->delete();
    }

    public function getPendingApprovals()
    {
        return $this->db->table('approval_requests')
                       ->where('status', 'pending')
                       ->orderBy('created_at', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function getApprovalHistory()
    {
        return $this->db->table('approval_requests')
                       ->orderBy('created_at', 'DESC')
                       ->limit(50)
                       ->get()
                       ->getResultArray();
    }

    public function getApprovalStats()
    {
        return [
            'total_requests' => $this->db->table('approval_requests')->countAllResults(),
            'pending_requests' => $this->db->table('approval_requests')->where('status', 'pending')->countAllResults(),
            'approved_requests' => $this->db->table('approval_requests')->where('status', 'approved')->countAllResults(),
            'rejected_requests' => $this->db->table('approval_requests')->where('status', 'rejected')->countAllResults()
        ];
    }

    // Notification Methods
    public function getAllNotifications($filters = [])
    {
        $builder = $this->db->table('notification_logs');
        
        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
    }

    public function createNotification($data)
    {
        return $this->db->table('notification_logs')->insert($data);
    }

    public function markNotificationAsRead($notificationId)
    {
        return $this->db->table('notification_logs')
                       ->where('notification_id', $notificationId)
                       ->update(['status' => 'read', 'read_at' => date('Y-m-d H:i:s')]);
    }

    public function getUnreadNotifications($userId)
    {
        return $this->db->table('notification_logs')
                       ->where('user_id', $userId)
                       ->where('status', 'unread')
                       ->orderBy('created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // Form Management Methods
    public function getAllForms($filters = [])
    {
        $builder = $this->db->table('model_formulir');
        
        if (!empty($filters['search'])) {
            $builder->like('form_name', $filters['search'])
                   ->orLike('description', $filters['search']);
        }

        if (!empty($filters['type'])) {
            $builder->where('form_type', $filters['type']);
        }

        return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
    }

    public function getFormById($formId)
    {
        return $this->db->table('model_formulir')->where('form_id', $formId)->get()->getRowArray();
    }

    public function createForm($data)
    {
        return $this->db->table('model_formulir')->insert($data);
    }

    public function updateForm($formId, $data)
    {
        return $this->db->table('model_formulir')->where('form_id', $formId)->update($data);
    }

    public function deleteForm($formId)
    {
        return $this->db->table('model_formulir')->where('form_id', $formId)->delete();
    }

    // System Statistics
    public function getSystemStats()
    {
        return [
            'total_logs' => $this->countAll(),
            'total_notifications' => $this->db->table('notification_logs')->countAllResults(),
            'total_workflows' => $this->db->table('approval_workflows')->countAllResults(),
            'total_classifications' => $this->db->table('data_classifications')->countAllResults(),
            'total_forms' => $this->db->table('model_formulir')->countAllResults(),
            'today_logs' => $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'today_notifications' => $this->db->table('notification_logs')->where('DATE(created_at)', date('Y-m-d'))->countAllResults()
        ];
    }

    // Export Methods
    public function exportActivityLogs($format = 'csv')
    {
        $logs = $this->getFilteredLogs([]);
        
        switch ($format) {
            case 'csv':
                return $this->exportToCSV($logs);
            case 'excel':
                return $this->exportToExcel($logs);
            case 'pdf':
                return $this->exportToPDF($logs);
            default:
                return $this->exportToCSV($logs);
        }
    }

    private function exportToCSV($logs)
    {
        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = WRITEPATH . 'exports/' . $filename;
        
        // Create exports directory if it doesn't exist
        if (!is_dir(WRITEPATH . 'exports/')) {
            mkdir(WRITEPATH . 'exports/', 0777, true);
        }
        
        $fp = fopen($filepath, 'w');
        
        // Write headers
        fputcsv($fp, ['Date', 'User', 'Activity', 'Description', 'EntityType', 'IP Address']);
        
        // Write data
        foreach ($logs as $log) {
            fputcsv($fp, [
                $log['created_at'],
                $log['first_name'] . ' ' . $log['last_name'],
                $log['activity_type'],
                $log['description'],
                $log['entity_type'],
                $log['ip_address']
            ]);
        }
        
        fclose($fp);
        
        return $filepath;
    }

    private function exportToExcel($logs)
    {
        // Implementation for Excel export
        return $this->exportToCSV($logs); // Fallback to CSV for now
    }

    private function exportToPDF($logs)
    {
        // Implementation for PDF export
        return $this->exportToCSV($logs); // Fallback to CSV for now
    }
} 