<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestActivityLog extends Controller
{
    public function index()
    {
        helper('simple_activity_log');
        
        $output = "<h1>Test Simple Activity Log Helper</h1>";
        $output .= "<hr>";
        
        try {
            // Test 1: Log Create
            $output .= "<h3>1. Testing log_create</h3>";
            $result1 = log_create('kontrak', 888, 'Test kontrak baru dibuat melalui web', ['no_po' => 'WEB-TEST-001', 'pelanggan' => 'Web Test Client']);
            $output .= "Result: " . ($result1 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br><br>";
            
            // Test 2: Log Update  
            $output .= "<h3>2. Testing log_update</h3>";
            $old_data = ['status' => 'Draft'];
            $new_data = ['status' => 'Active'];
            $result2 = log_update('kontrak', 888, 'Status kontrak diubah melalui web', $old_data, $new_data);
            $output .= "Result: " . ($result2 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br><br>";
            
            // Test 3: Log Print
            $output .= "<h3>3. Testing log_print</h3>";
            $result3 = log_print('kontrak', 888, 'Kontrak WEB-TEST-001 di-print melalui web');
            $output .= "Result: " . ($result3 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br><br>";
            
            // Test 4: Log Download
            $output .= "<h3>4. Testing log_download</h3>";
            $result4 = log_download('kontrak', 888, 'Kontrak WEB-TEST-001 di-download PDF melalui web');
            $output .= "Result: " . ($result4 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br><br>";
            
            // Test 5: Log Delete
            $output .= "<h3>5. Testing log_delete</h3>";
            $result5 = log_delete('kontrak', 888, 'Kontrak WEB-TEST-001 dihapus melalui web', ['no_po' => 'WEB-TEST-001']);
            $output .= "Result: " . ($result5 ? "<span style='color:green'>SUCCESS</span>" : "<span style='color:red'>FAILED</span>") . "<br><br>";
            
            $output .= "<hr><h3>All Tests Completed!</h3>";
            $output .= "<a href='" . base_url('/admin/activity-log') . "'>Lihat Activity Log</a>";
            
        } catch (\Exception $e) {
            $output .= "<div style='color:red'>";
            $output .= "<h3>ERROR:</h3>";
            $output .= "Message: " . $e->getMessage() . "<br>";
            $output .= "File: " . $e->getFile() . "<br>";
            $output .= "Line: " . $e->getLine() . "<br>";
            $output .= "</div>";
        }
        
        return $output;
    }
}
