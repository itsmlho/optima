<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class ActivityLog extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Activity Log | OPTIMA',
            'page_title' => 'Activity Log',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/activity-log' => 'Activity Log'
            ],
            'users' => $this->userModel->findAll(),
            'activity_types' => $this->getActivityTypes()
        ];

        return view('activity-log/index', $data);
    }

    public function getData()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        
        // Create activity_logs table if it doesn't exist
        $this->createActivityLogsTable($db);

        // DataTables parameters
        $draw = $request->getPost('draw');
        $start = $request->getPost('start') ?: 0;
        $length = $request->getPost('length') ?: 25;
        $searchValue = $request->getPost('search')['value'] ?? '';
        $orderColumn = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';

        // Filters
        $userFilter = $request->getPost('user_filter');
        $typeFilter = $request->getPost('type_filter');
        $dateFromFilter = $request->getPost('date_from_filter');
        $dateToFilter = $request->getPost('date_to_filter');

        $columns = ['al.created_at', 'u.first_name', 'al.activity_type', 'al.description', 'al.ip_address'];
        $orderBy = $columns[$orderColumn] ?? 'al.created_at';

        $builder = $db->table('activity_logs al')
                     ->select('al.*, u.first_name, u.last_name, u.email')
                     ->join('users u', 'u.id = al.user_id', 'left');

        // Apply filters
        if (!empty($userFilter)) {
            $builder->where('al.user_id', $userFilter);
        }

        if (!empty($typeFilter)) {
            $builder->where('al.activity_type', $typeFilter);
        }

        if (!empty($dateFromFilter)) {
            $builder->where('DATE(al.created_at) >=', $dateFromFilter);
        }

        if (!empty($dateToFilter)) {
            $builder->where('DATE(al.created_at) <=', $dateToFilter);
        }

        // Search
        if (!empty($searchValue)) {
            $builder->groupStart()
                   ->like('u.first_name', $searchValue)
                   ->orLike('u.last_name', $searchValue)
                   ->orLike('al.activity_type', $searchValue)
                   ->orLike('al.description', $searchValue)
                   ->orLike('al.ip_address', $searchValue)
                   ->groupEnd();
        }

        // Get total records
        $totalRecords = $builder->countAllResults(false);

        // Apply ordering and pagination
        $data = $builder->orderBy($orderBy, $orderDir)
                       ->limit($length, $start)
                       ->get()
                       ->getResultArray();

        // Format data for DataTables
        $formattedData = [];
        foreach ($data as $row) {
            $formattedData[] = [
                'created_at' => date('d/m/Y H:i:s', strtotime($row['created_at'])),
                'user' => $row['first_name'] . ' ' . $row['last_name'],
                'activity_type' => $this->formatActivityType($row['activity_type']),
                'description' => $row['description'],
                'ip_address' => $row['ip_address'],
                'details' => $this->formatDetails($row)
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $formattedData
        ]);
    }

    public function log($activityType, $description, $details = null, $userId = null)
    {
        $db = \Config\Database::connect();
        
        // Create activity_logs table if it doesn't exist
        $this->createActivityLogsTable($db);

        $logData = [
            'user_id' => $userId ?: session()->get('user_id'),
            'activity_type' => $activityType,
            'description' => $description,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $db->table('activity_logs')->insert($logData);
    }

    public function export()
    {
        $request = service('request');
        $format = $request->getGet('format') ?: 'csv';
        
        $db = \Config\Database::connect();
        
        $builder = $db->table('activity_logs al')
                     ->select('al.*, u.first_name, u.last_name, u.email')
                     ->join('users u', 'u.id = al.user_id', 'left')
                     ->orderBy('al.created_at', 'DESC');

        $data = $builder->get()->getResultArray();

        if ($format === 'csv') {
            return $this->exportCSV($data);
        } elseif ($format === 'excel') {
            return $this->exportExcel($data);
        } else {
            return redirect()->back()->with('error', 'Invalid export format');
        }
    }

    public function clear()
    {
        $request = service('request');
        $olderThan = $request->getPost('older_than') ?: 30; // days

        $db = \Config\Database::connect();
        
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$olderThan} days"));
        
        $deletedCount = $db->table('activity_logs')
                          ->where('created_at <', $cutoffDate)
                          ->delete();

        // Log this action
        $this->log('system_maintenance', "Cleared {$deletedCount} activity log entries older than {$olderThan} days");

        return $this->response->setJSON([
            'success' => true,
            'message' => "Successfully cleared {$deletedCount} log entries",
            'deleted_count' => $deletedCount
        ]);
    }

    public function getStats()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('activity_logs')) {
            return $this->response->setJSON([
                'success' => true,
                'stats' => [
                    'total_logs' => 0,
                    'today_logs' => 0,
                    'this_week_logs' => 0,
                    'this_month_logs' => 0,
                    'top_activities' => [],
                    'top_users' => []
                ]
            ]);
        }

        $stats = [
            'total_logs' => $db->table('activity_logs')->countAll(),
            'today_logs' => $db->table('activity_logs')
                              ->where('DATE(created_at)', date('Y-m-d'))
                              ->countAllResults(),
            'this_week_logs' => $db->table('activity_logs')
                                  ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                                  ->countAllResults(),
            'this_month_logs' => $db->table('activity_logs')
                                   ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
                                   ->countAllResults()
        ];

        // Top activities
        $topActivities = $db->table('activity_logs')
                           ->select('activity_type, COUNT(*) as count')
                           ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
                           ->groupBy('activity_type')
                           ->orderBy('count', 'DESC')
                           ->limit(5)
                           ->get()
                           ->getResultArray();

        // Top users
        $topUsers = $db->table('activity_logs al')
                      ->select('u.first_name, u.last_name, COUNT(*) as count')
                      ->join('users u', 'u.id = al.user_id', 'left')
                      ->where('al.created_at >=', date('Y-m-d', strtotime('-30 days')))
                      ->groupBy('al.user_id')
                      ->orderBy('count', 'DESC')
                      ->limit(5)
                      ->get()
                      ->getResultArray();

        $stats['top_activities'] = $topActivities;
        $stats['top_users'] = $topUsers;

        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function getActivityTypes()
    {
        return [
            'login' => 'User Login',
            'logout' => 'User Logout',
            'profile_update' => 'Profile Update',
            'password_change' => 'Password Change',
            'asset_create' => 'Asset Created',
            'asset_update' => 'Asset Updated',
            'asset_delete' => 'Asset Deleted',
            'rental_create' => 'Rental Created',
            'rental_update' => 'Rental Updated',
            'rental_complete' => 'Rental Completed',
            'maintenance_create' => 'Maintenance Created',
            'maintenance_update' => 'Maintenance Updated',
            'maintenance_complete' => 'Maintenance Completed',
            'settings_update' => 'Settings Updated',
            'backup_create' => 'Backup Created',
            'system_maintenance' => 'System Maintenance',
            'data_export' => 'Data Export',
            'data_import' => 'Data Import',
            'user_create' => 'User Created',
            'user_update' => 'User Updated',
            'user_delete' => 'User Deleted',
            'role_change' => 'Role Changed'
        ];
    }

    private function formatActivityType($type)
    {
        $types = $this->getActivityTypes();
        $label = $types[$type] ?? ucfirst(str_replace('_', ' ', $type));
        
        $badgeClass = 'secondary';
        switch ($type) {
            case 'login':
            case 'asset_create':
            case 'rental_create':
            case 'maintenance_create':
            case 'user_create':
                $badgeClass = 'success';
                break;
            case 'logout':
            case 'asset_delete':
            case 'user_delete':
                $badgeClass = 'danger';
                break;
            case 'profile_update':
            case 'asset_update':
            case 'rental_update':
            case 'maintenance_update':
            case 'settings_update':
            case 'user_update':
                $badgeClass = 'info';
                break;
            case 'password_change':
            case 'role_change':
                $badgeClass = 'warning';
                break;
            case 'rental_complete':
            case 'maintenance_complete':
                $badgeClass = 'primary';
                break;
        }
        
        return "<span class=\"badge badge-{$badgeClass}\">{$label}</span>";
    }

    private function formatDetails($row)
    {
        if (empty($row['details'])) {
            return '-';
        }
        
        return "<button class=\"btn btn-sm btn-outline-primary\" onclick=\"showDetails('" . 
               htmlspecialchars($row['details']) . "')\"><i class=\"fas fa-eye\"></i></button>";
    }

    private function exportCSV($data)
    {
        $filename = 'activity_log_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'Date & Time',
            'User',
            'Activity Type',
            'Description',
            'IP Address',
            'User Agent'
        ]);
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, [
                date('d/m/Y H:i:s', strtotime($row['created_at'])),
                $row['first_name'] . ' ' . $row['last_name'],
                $row['activity_type'],
                $row['description'],
                $row['ip_address'],
                $row['user_agent']
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportExcel($data)
    {
        // Simple Excel export using HTML table format
        $filename = 'activity_log_' . date('Y-m-d_H-i-s') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo '<table border="1">';
        echo '<tr>';
        echo '<th>Date & Time</th>';
        echo '<th>User</th>';
        echo '<th>Activity Type</th>';
        echo '<th>Description</th>';
        echo '<th>IP Address</th>';
        echo '<th>User Agent</th>';
        echo '</tr>';
        
        foreach ($data as $row) {
            echo '<tr>';
            echo '<td>' . date('d/m/Y H:i:s', strtotime($row['created_at'])) . '</td>';
            echo '<td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['activity_type']) . '</td>';
            echo '<td>' . htmlspecialchars($row['description']) . '</td>';
            echo '<td>' . htmlspecialchars($row['ip_address']) . '</td>';
            echo '<td>' . htmlspecialchars($row['user_agent']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        exit;
    }

    private function createActivityLogsTable($db)
    {
        if (!$db->tableExists('activity_logs')) {
            $forge = \Config\Database::forge();
            
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true
                ],
                'activity_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'description' => [
                    'type' => 'TEXT'
                ],
                'details' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'ip_address' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => true
                ],
                'user_agent' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            $forge->addKey('id', true);
            $forge->addKey('user_id');
            $forge->addKey('activity_type');
            $forge->addKey('created_at');
            $forge->createTable('activity_logs');
        }
    }
} 