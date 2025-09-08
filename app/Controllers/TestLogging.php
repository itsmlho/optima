<?php

namespace App\Controllers;

class TestLogging extends BaseController
{
    public function index()
    {
        // Load helper
        helper('simple_activity_log');
        
        $output = "<h1>Test Simple Activity Logging</h1><br>";
        
        // Test CREATE
        $result1 = log_create('kontrak', 999, 'Test create kontrak baru dari controller', 'Marketing');
        $output .= "Test CREATE: " . ($result1 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br>";
        
        // Test UPDATE  
        $result2 = log_update('kontrak', 999, 'Test update data kontrak dari controller', 'Marketing');
        $output .= "Test UPDATE: " . ($result2 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br>";
        
        // Test PRINT
        $result3 = log_print('Test print kontrak ke PDF dari controller', 'kontrak_999.pdf', 'PDF', 'kontrak', 999, 'Marketing');
        $output .= "Test PRINT: " . ($result3 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br>";
        
        // Test DOWNLOAD
        $result4 = log_download('Test download laporan Excel dari controller', 'laporan_kontrak.xlsx', 'Excel', 'Reports');
        $output .= "Test DOWNLOAD: " . ($result4 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br>";
        
        // Test LOGIN
        $result5 = log_login('admin');
        $output .= "Test LOGIN: " . ($result5 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br>";
        
        // Get recent logs
        $logs = get_activity_logs(['limit' => 10]);
        $output .= "<h2>Recent Activity Logs:</h2>";
        $output .= "<table border='1' style='border-collapse:collapse; width:100%'>";
        $output .= "<tr><th>ID</th><th>Username</th><th>Action</th><th>Description</th><th>Time</th></tr>";
        
        foreach ($logs as $log) {
            $output .= "<tr>";
            $output .= "<td>{$log['id']}</td>";
            $output .= "<td>{$log['username']}</td>";
            $output .= "<td>{$log['action_type']}</td>";
            $output .= "<td>{$log['description']}</td>";
            $output .= "<td>{$log['created_at']}</td>";
            $output .= "</tr>";
        }
        $output .= "</table>";
        
        return $output;
    }
}
