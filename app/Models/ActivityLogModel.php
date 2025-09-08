<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'system_activity_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'module_name',
        'action_type',
        'table_name',
        'record_id',
        'action_description',
        'old_values',
        'new_values',
        'business_impact',
        'is_critical',
        'ip_address',
        'user_agent',
        'session_id',
        'context_data',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false; // Karena kita manage manual di created_at
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    /**
     * Get activity logs with user information for DataTables
     */
    public function getDataTablesData($request)
    {
        $builder = $this->db->table($this->table . ' sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sal.user_id', 'left');

        // Search functionality
        $searchValue = $request['search']['value'] ?? '';
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('sal.action_description', $searchValue)
                ->orLike('sal.table_name', $searchValue)
                ->orLike('sal.action_type', $searchValue)
                ->orLike('sal.module_name', $searchValue)
                ->orLike('u.username', $searchValue)
                ->groupEnd();
        }

        // Get total and filtered counts
        $totalRecords = $this->countAll();
        $filteredRecords = $builder->countAllResults(false);

        // Order by column
        $orderColumn = $request['order'][0]['column'] ?? 0;
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        
        $columns = ['created_at', 'username', 'module_name', 'action_type', 'table_name', 
                   'action_description', 'business_impact', 'is_critical', 'actions'];
        
        if (isset($columns[$orderColumn])) {
            $orderField = $columns[$orderColumn];
            if ($orderField === 'username') {
                $orderField = 'u.username';
            } elseif ($orderField === 'actions') {
                $orderField = 'sal.created_at';
            } else {
                $orderField = 'sal.' . $orderField;
            }
            $builder->orderBy($orderField, $orderDir);
        } else {
            $builder->orderBy('sal.created_at', 'desc');
        }

        // Limit results
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 25;
        $builder->limit($length, $start);
        
        return [
            'data' => $builder->get()->getResultArray(),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords
        ];
    }

    /**
     * Get activity log details with user information
     */
    public function getLogWithUser($id)
    {
        $builder = $this->db->table($this->table . ' sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name, u.email');
        $builder->join('users u', 'u.id = sal.user_id', 'left');
        $builder->where('sal.id', $id);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics($period = '7 days')
    {
        $builder = $this->db->table($this->table);
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        
        $stats = [
            'total' => $builder->countAllResults(false),
            'critical' => $builder->where('is_critical', 1)->countAllResults(false),
            'by_action' => [],
            'by_module' => []
        ];

        // Group by action type
        $builder = $this->db->table($this->table);
        $builder->select('action_type, COUNT(*) as count');
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        $builder->groupBy('action_type');
        $stats['by_action'] = $builder->get()->getResultArray();

        // Group by module
        $builder = $this->db->table($this->table);
        $builder->select('module_name, COUNT(*) as count');
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        $builder->groupBy('module_name');
        $stats['by_module'] = $builder->get()->getResultArray();

        return $stats;
    }

    /**
     * Clean old logs (older than specified days)
     */
    public function cleanOldLogs($days = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-$days days"));
        return $this->where('created_at <', $cutoffDate)->delete();
    }

    /**
     * Log activity - wrapper method untuk helper
     */
    public function logActivity($data)
    {
        // Ensure created_at is set
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        // Set default values
        $data['ip_address'] = $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $data['user_agent'] = $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $data['session_id'] = $data['session_id'] ?? session_id();

        return $this->insert($data);
    }

    /**
     * Get filtered data for monitoring dashboard
     */
    public function getFilteredDataTablesData($request)
    {
        $builder = $this->db->table($this->table . ' sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sal.user_id', 'left');

        // Apply time range filter
        if (!empty($request['timeRange'])) {
            $timeMap = [
                '1h' => '1 hour',
                '24h' => '24 hours',
                '7d' => '7 days', 
                '30d' => '30 days'
            ];
            $period = $timeMap[$request['timeRange']] ?? '24 hours';
            $builder->where('sal.created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        }

        // Apply module filter
        if (!empty($request['module'])) {
            $builder->where('sal.module_name', $request['module']);
        }

        // Apply action filter
        if (!empty($request['action'])) {
            $builder->where('sal.action_type', $request['action']);
        }

        // Apply user filter
        if (!empty($request['user'])) {
            $builder->like('u.username', $request['user']);
        }

        // Search functionality
        $searchValue = $request['search']['value'] ?? '';
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('sal.action_description', $searchValue)
                ->orLike('sal.table_name', $searchValue)
                ->orLike('sal.action_type', $searchValue)
                ->orLike('sal.module_name', $searchValue)
                ->orLike('u.username', $searchValue)
                ->groupEnd();
        }

        // Get total and filtered counts
        $totalRecords = $this->countAll();
        $filteredRecords = $builder->countAllResults(false);

        // Order by column
        $orderColumn = $request['order'][0]['column'] ?? 0;
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        
        $columns = ['created_at', 'username', 'module_name', 'action_type', 'table_name', 
                   'action_description', 'business_impact', 'is_critical'];
        
        if (isset($columns[$orderColumn])) {
            $orderField = $columns[$orderColumn];
            if ($orderField === 'username') {
                $orderField = 'u.username';
            } else {
                $orderField = 'sal.' . $orderField;
            }
            $builder->orderBy($orderField, $orderDir);
        } else {
            $builder->orderBy('sal.created_at', 'desc');
        }

        // Limit results
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 15;
        $builder->limit($length, $start);
        
        return [
            'data' => $builder->get()->getResultArray(),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords
        ];
    }

    /**
     * Get monitoring statistics with additional metrics
     */
    public function getMonitoringStatistics($period = '24 hours')
    {
        $builder = $this->db->table($this->table);
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        
        $stats = [
            'total' => $builder->countAllResults(false),
            'critical' => $builder->where('is_critical', 1)->countAllResults(false),
            'by_action' => [],
            'by_module' => []
        ];

        // Group by action type
        $builder = $this->db->table($this->table);
        $builder->select('action_type, COUNT(*) as count');
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        $builder->groupBy('action_type');
        $stats['by_action'] = $builder->get()->getResultArray();

        // Group by module
        $builder = $this->db->table($this->table);
        $builder->select('module_name, COUNT(*) as count');
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        $builder->groupBy('module_name');
        $stats['by_module'] = $builder->get()->getResultArray();

        return $stats;
    }

    /**
     * Get active users count in period
     */
    public function getActiveUsersCount($period = '24 hours')
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(DISTINCT user_id) as count');
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        $result = $builder->get()->getRowArray();
        
        return $result['count'] ?? 0;
    }

    /**
     * Get activity trend for charts
     */
    public function getActivityTrend($period = '24 hours')
    {
        $format = strpos($period, 'hour') !== false ? '%H:00' : '%Y-%m-%d';
        
        $builder = $this->db->table($this->table);
        $builder->select("DATE_FORMAT(created_at, '$format') as time_label, COUNT(*) as count");
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        $builder->groupBy('time_label');
        $builder->orderBy('time_label');
        
        $results = $builder->get()->getResultArray();
        
        return [
            'labels' => array_column($results, 'time_label'),
            'data' => array_column($results, 'count')
        ];
    }

    /**
     * Get recent activities for live feed
     */
    public function getRecentActivities($limit = 10)
    {
        $builder = $this->db->table($this->table . ' sal');
        $builder->select('sal.*, u.username');
        $builder->join('users u', 'u.id = sal.user_id', 'left');
        $builder->orderBy('sal.created_at', 'desc');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get filtered export data
     */
    public function getFilteredExportData($filters)
    {
        $builder = $this->db->table($this->table . ' sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sal.user_id', 'left');

        // Apply filters same as getFilteredDataTablesData
        if (!empty($filters['timeRange'])) {
            $timeMap = [
                '1h' => '1 hour',
                '24h' => '24 hours',
                '7d' => '7 days',
                '30d' => '30 days'
            ];
            $period = $timeMap[$filters['timeRange']] ?? '24 hours';
            $builder->where('sal.created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        }

        if (!empty($filters['module'])) {
            $builder->where('sal.module_name', $filters['module']);
        }

        if (!empty($filters['action'])) {
            $builder->where('sal.action_type', $filters['action']);
        }

        if (!empty($filters['user'])) {
            $builder->like('u.username', $filters['user']);
        }

        $builder->orderBy('sal.created_at', 'desc');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get recent count for health check
     */
    public function getRecentCount($period = '1 hour')
    {
        $builder = $this->db->table($this->table);
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        
        return $builder->countAllResults();
    }

    /**
     * Get critical count for health check
     */
    public function getCriticalCount($period = '24 hours')
    {
        $builder = $this->db->table($this->table);
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$period")));
        $builder->where('is_critical', 1);
        
        return $builder->countAllResults();
    }
}