<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Settings | OPTIMA',
            'page_title' => 'System Settings',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/settings' => 'Settings'
            ],
            'settings' => $this->getSettings()
        ];

        return view('settings/index', $data);
    }

    public function update()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'app_name' => 'required|max_length[100]',
            'app_description' => 'permit_empty|max_length[500]',
            'company_name' => 'required|max_length[100]',
            'company_address' => 'permit_empty|max_length[500]',
            'company_phone' => 'permit_empty|max_length[20]',
            'company_email' => 'permit_empty|valid_email',
            'timezone' => 'required',
            'date_format' => 'required',
            'currency' => 'required',
            'language' => 'required',
            'items_per_page' => 'required|integer|greater_than[0]',
            'session_timeout' => 'required|integer|greater_than[0]',
            'maintenance_mode' => 'permit_empty',
            'debug_mode' => 'permit_empty',
            'email_notifications' => 'permit_empty',
            'sms_notifications' => 'permit_empty',
            'auto_backup' => 'permit_empty',
            'backup_frequency' => 'required',
            'max_login_attempts' => 'required|integer|greater_than[0]',
            'password_expiry_days' => 'required|integer|greater_than[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $settings = [
            'app_name' => $this->request->getPost('app_name'),
            'app_description' => $this->request->getPost('app_description'),
            'company_name' => $this->request->getPost('company_name'),
            'company_address' => $this->request->getPost('company_address'),
            'company_phone' => $this->request->getPost('company_phone'),
            'company_email' => $this->request->getPost('company_email'),
            'timezone' => $this->request->getPost('timezone'),
            'date_format' => $this->request->getPost('date_format'),
            'currency' => $this->request->getPost('currency'),
            'language' => $this->request->getPost('language'),
            'items_per_page' => $this->request->getPost('items_per_page'),
            'session_timeout' => $this->request->getPost('session_timeout'),
            'maintenance_mode' => $this->request->getPost('maintenance_mode') ? 1 : 0,
            'debug_mode' => $this->request->getPost('debug_mode') ? 1 : 0,
            'email_notifications' => $this->request->getPost('email_notifications') ? 1 : 0,
            'sms_notifications' => $this->request->getPost('sms_notifications') ? 1 : 0,
            'auto_backup' => $this->request->getPost('auto_backup') ? 1 : 0,
            'backup_frequency' => $this->request->getPost('backup_frequency'),
            'max_login_attempts' => $this->request->getPost('max_login_attempts'),
            'password_expiry_days' => $this->request->getPost('password_expiry_days')
        ];

        if ($this->saveSettings($settings)) {
            return redirect()->to('/settings')->with('success', 'Settings updated successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to update settings');
        }
    }

    public function testEmail()
    {
        $email = \Config\Services::email();
        
        $email->setTo($this->request->getPost('test_email'));
        $email->setSubject('OPTIMA - Email Test');
        $email->setMessage('This is a test email from OPTIMA system. If you receive this, email configuration is working correctly.');

        if ($email->send()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Test email sent successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to send test email: ' . $email->printDebugger(['headers'])
            ]);
        }
    }

    public function backup()
    {
        try {
            $db = \Config\Database::connect();
            $forge = \Config\Database::forge();
            
            // Get all tables
            $tables = $db->listTables();
            
            $backup = "-- OPTIMA Database Backup\n";
            $backup .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            $backup .= "-- Database: " . $db->getDatabase() . "\n\n";
            
            foreach ($tables as $table) {
                $backup .= "-- Table structure for table `$table`\n";
                $backup .= "DROP TABLE IF EXISTS `$table`;\n";
                
                // Get table structure
                $query = $db->query("SHOW CREATE TABLE `$table`");
                $result = $query->getRow();
                $backup .= $result->{'Create Table'} . ";\n\n";
                
                // Get table data
                $query = $db->query("SELECT * FROM `$table`");
                $results = $query->getResultArray();
                
                if (!empty($results)) {
                    $backup .= "-- Dumping data for table `$table`\n";
                    foreach ($results as $row) {
                        $values = array_map(function($value) use ($db) {
                            return $value === null ? 'NULL' : $db->escape($value);
                        }, $row);
                        $backup .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $backup .= "\n";
                }
            }
            
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = WRITEPATH . 'backups/' . $filename;
            
            // Create backup directory if it doesn't exist
            if (!is_dir(WRITEPATH . 'backups/')) {
                mkdir(WRITEPATH . 'backups/', 0755, true);
            }
            
            if (file_put_contents($filepath, $backup)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Backup created successfully',
                    'filename' => $filename
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create backup file'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ]);
        }
    }

    public function clearCache()
    {
        try {
            $cache = \Config\Services::cache();
            $cache->clean();
            
            // Clear view cache
            $viewCachePath = WRITEPATH . 'cache/';
            if (is_dir($viewCachePath)) {
                $files = glob($viewCachePath . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ]);
        }
    }

    public function getSystemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => $this->getDatabaseVersion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'disk_space' => $this->getDiskSpace(),
            'last_backup' => $this->getLastBackupDate()
        ];

        return $this->response->setJSON([
            'success' => true,
            'info' => $info
        ]);
    }

    private function getSettings()
    {
        $db = \Config\Database::connect();
        
        // Create settings table if it doesn't exist
        $this->createSettingsTable($db);
        
        $settings = $db->table('system_settings')->get()->getResultArray();
        
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting['key']] = $setting['value'];
        }
        
        // Default settings
        $defaults = [
            'app_name' => 'OPTIMA',
            'app_description' => 'Asset Management System',
            'company_name' => 'PT Sarana Mitra Luas Tbk',
            'company_address' => '',
            'company_phone' => '',
            'company_email' => '',
            'timezone' => 'Asia/Jakarta',
            'date_format' => 'd/m/Y',
            'currency' => 'IDR',
            'language' => 'id',
            'items_per_page' => '25',
            'session_timeout' => '120',
            'maintenance_mode' => '0',
            'debug_mode' => '0',
            'email_notifications' => '1',
            'sms_notifications' => '0',
            'auto_backup' => '1',
            'backup_frequency' => 'daily',
            'max_login_attempts' => '5',
            'password_expiry_days' => '90'
        ];
        
        return array_merge($defaults, $settingsArray);
    }

    private function saveSettings($settings)
    {
        $db = \Config\Database::connect();
        
        try {
            foreach ($settings as $key => $value) {
                $existing = $db->table('system_settings')
                              ->where('key', $key)
                              ->get()
                              ->getRow();
                
                if ($existing) {
                    $db->table('system_settings')
                       ->where('key', $key)
                       ->update(['value' => $value, 'updated_at' => date('Y-m-d H:i:s')]);
                } else {
                    $db->table('system_settings')
                       ->insert([
                           'key' => $key,
                           'value' => $value,
                           'created_at' => date('Y-m-d H:i:s'),
                           'updated_at' => date('Y-m-d H:i:s')
                       ]);
                }
            }
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to save settings: ' . $e->getMessage());
            return false;
        }
    }

    private function createSettingsTable($db)
    {
        if (!$db->tableExists('system_settings')) {
            $forge = \Config\Database::forge();
            
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'key' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'unique' => true
                ],
                'value' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            $forge->addKey('id', true);
            $forge->addUniqueKey('key');
            $forge->createTable('system_settings');
        }
    }

    private function getDatabaseVersion()
    {
        try {
            $db = \Config\Database::connect();
            $query = $db->query("SELECT VERSION() as version");
            $result = $query->getRow();
            return $result->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getDiskSpace()
    {
        $bytes = disk_free_space(".");
        $si_prefix = array('B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB');
        $base = 1024;
        $class = min((int)log($bytes, $base), count($si_prefix) - 1);
        return sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class];
    }

    private function getLastBackupDate()
    {
        $backupDir = WRITEPATH . 'backups/';
        if (!is_dir($backupDir)) {
            return 'Never';
        }
        
        $files = glob($backupDir . 'backup_*.sql');
        if (empty($files)) {
            return 'Never';
        }
        
        $latestFile = '';
        $latestTime = 0;
        
        foreach ($files as $file) {
            $time = filemtime($file);
            if ($time > $latestTime) {
                $latestTime = $time;
                $latestFile = $file;
            }
        }
        
        return $latestTime ? date('d/m/Y H:i:s', $latestTime) : 'Never';
    }
} 