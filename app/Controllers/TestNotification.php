<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class TestNotification extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Test simple notification creation
        try {
            $data = [
                'title' => 'Test Notification System',
                'message' => 'Sistem notifikasi OPTIMA telah berhasil diaktifkan! SPK DIESEL akan otomatis mengirim notifikasi ke tim Service DIESEL.',
                'type' => 'success',
                'category' => 'system_test',
                'created_by' => session()->get('user_id') ?: 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('notifications')->insert($data);
            $notificationId = $db->insertID();
            
            echo "<h2>✅ Test Notifikasi Berhasil!</h2>";
            echo "<p><strong>Notification ID:</strong> {$notificationId}</p>";
            echo "<p><strong>Title:</strong> {$data['title']}</p>";
            echo "<p><strong>Message:</strong> {$data['message']}</p>";
            echo "<hr>";
            
            // Test notification rules
            $rules = $db->table('notification_rules')->get()->getResultArray();
            echo "<h3>📋 Notification Rules Available:</h3>";
            echo "<ul>";
            foreach ($rules as $rule) {
                $status = $rule['is_active'] ? '<span style="color: green;">✅ Active</span>' : '<span style="color: red;">❌ Inactive</span>';
                echo "<li><strong>{$rule['name']}</strong> - {$rule['trigger_event']} {$status}</li>";
            }
            echo "</ul>";
            
            // Quick links
            echo "<hr>";
            echo "<h3>🚀 Quick Access:</h3>";
            echo "<a href='" . base_url('notifications') . "' style='margin-right: 10px; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Notification Center</a>";
            
            if (session()->get('role') === 'superadmin') {
                echo "<a href='" . base_url('notifications/admin') . "' style='margin-right: 10px; padding: 8px 16px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;'>Admin Rules</a>";
            }
            
            echo "<a href='" . base_url() . "' style='padding: 8px 16px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;'>Dashboard</a>";
            
        } catch (\Exception $e) {
            echo "<h2>❌ Test Gagal</h2>";
            echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
        }
    }
    
    public function testRule()
    {
        try {
            $notificationModel = new NotificationModel();
            
            // Test SPK DIESEL notification rule
            $context = [
                'spk_id' => 999,
                'nomor_spk' => 'SPK-TEST-001',
                'departemen' => 'diesel',
                'customer_name' => 'Test Customer',
                'created_by' => 'Test User',
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $result = $notificationModel->sendByRule('spk_created', $context);
            
            echo "<h2>🧪 Test Rule SPK DIESEL</h2>";
            echo "<p><strong>Context:</strong> " . json_encode($context, JSON_PRETTY_PRINT) . "</p>";
            echo "<p><strong>Result:</strong> " . json_encode($result, JSON_PRETTY_PRINT) . "</p>";
            
            if ($result['success']) {
                echo "<p style='color: green;'>✅ Notifikasi berhasil dikirim ke {$result['sent_count']} penerima!</p>";
            } else {
                echo "<p style='color: red;'>❌ Gagal mengirim notifikasi: {$result['message']}</p>";
            }
            
        } catch (\Exception $e) {
            echo "<h2>❌ Test Rule Gagal</h2>";
            echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
        }
    }
}
