<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'System Administration | OPTIMA',
            'page_title' => 'System Administration',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/admin' => 'Administration'
            ],
            'system_status' => $this->getSystemStatus(),
            'recent_activities' => $this->getRecentActivities()
        ];

        return view('admin/index', $data);
    }

    public function settings()
    {
        $data = [
            'title' => 'System Settings | OPTIMA',
            'page_title' => 'System Settings',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/admin' => 'Administration',
                '/admin/settings' => 'Settings'
            ],
            'settings' => $this->getSystemSettings()
        ];

        return view('admin/settings', $data);
    }

    public function configuration()
    {
        $data = [
            'title' => 'System Configuration | OPTIMA',
            'page_title' => 'System Configuration',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/admin' => 'Administration',
                '/admin/configuration' => 'Configuration'
            ],
            'config_data' => $this->getConfigurationData()
        ];

        return view('admin/configuration', $data);
    }

    public function updateSettings()
    {
        // Mock successful response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Settings updated successfully',
            'token' => csrf_hash()
        ]);
    }

    public function updateConfiguration()
    {
        // Mock successful response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Configuration updated successfully',
            'token' => csrf_hash()
        ]);
    }

    public function systemBackup()
    {
        // Mock backup functionality
        return $this->response->setJSON([
            'success' => true,
            'message' => 'System backup created successfully',
            'backup_file' => 'backup_' . date('Y-m-d_H-i-s') . '.sql',
            'token' => csrf_hash()
        ]);
    }

    public function systemRestore()
    {
        // Mock restore functionality
        return $this->response->setJSON([
            'success' => true,
            'message' => 'System restored successfully',
            'token' => csrf_hash()
        ]);
    }

    private function getSystemStatus()
    {
        return [
            'database_status' => 'Connected',
            'cache_status' => 'Active',
            'storage_usage' => 65.4,
            'memory_usage' => 42.8,
            'cpu_usage' => 35.2,
            'uptime' => '15 days, 8 hours',
            'last_backup' => '2024-01-15 02:00:00',
            'active_users' => 24
        ];
    }

    private function getRecentActivities()
    {
        return [
            [
                'user' => 'admin',
                'activity' => 'Updated system settings',
                'timestamp' => '2024-01-15 14:30:00',
                'ip_address' => '192.168.1.100'
            ],
            [
                'user' => 'manager1',
                'activity' => 'Created new user account',
                'timestamp' => '2024-01-15 13:45:00',
                'ip_address' => '192.168.1.105'
            ],
            [
                'user' => 'admin',
                'activity' => 'Performed system backup',
                'timestamp' => '2024-01-15 02:00:00',
                'ip_address' => '192.168.1.100'
            ]
        ];
    }

    private function getSystemSettings()
    {
        return [
            'company_name' => 'OPTIMA Equipment Rental',
            'company_address' => 'Jl. Industri No. 123, Jakarta',
            'company_phone' => '021-12345678',
            'company_email' => 'info@optima.com',
            'timezone' => 'Asia/Jakarta',
            'date_format' => 'd/m/Y',
            'currency' => 'IDR',
            'language' => 'id',
            'maintenance_mode' => false,
            'auto_backup' => true,
            'backup_frequency' => 'daily',
            'session_timeout' => 30,
            'max_login_attempts' => 5
        ];
    }

    private function getConfigurationData()
    {
        return [
            'database' => [
                'host' => 'localhost',
                'port' => '3306',
                'name' => 'optima_db',
                'charset' => 'utf8mb4'
            ],
            'email' => [
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => '587',
                'smtp_user' => 'noreply@optima.com',
                'smtp_encryption' => 'tls'
            ],
            'cache' => [
                'driver' => 'file',
                'ttl' => 3600,
                'prefix' => 'optima_'
            ],
            'session' => [
                'driver' => 'files',
                'cookie_name' => 'optima_session',
                'expiration' => 7200
            ],
            'upload' => [
                'max_size' => '10MB',
                'allowed_types' => 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
                'upload_path' => 'uploads/'
            ]
        ];
    }
} 