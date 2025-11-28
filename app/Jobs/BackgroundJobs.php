<?php

namespace App\Jobs;

/**
 * ============================================================================
 * EMAIL SENDING JOB - Background Email Processing
 * ============================================================================
 * Handles email sending in background to prevent blocking
 * ============================================================================
 */
class SendEmailJob
{
    /**
     * Handle email sending job
     */
    public function handle(array $data): array
    {
        $email = \Config\Services::email();
        
        try {
            // Configure email
            $email->setTo($data['to'], $data['to_name'] ?? '');
            $email->setSubject($data['subject']);
            $email->setMessage($data['message']);
            
            // Set from if provided
            if (isset($data['from'])) {
                $email->setFrom($data['from'], $data['from_name'] ?? '');
            }
            
            // Add CC if provided
            if (isset($data['cc']) && is_array($data['cc'])) {
                foreach ($data['cc'] as $cc_email => $cc_name) {
                    $email->setCC($cc_email, $cc_name);
                }
            }
            
            // Add attachments if provided
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $email->attach($attachment);
                }
            }
            
            // Send email
            $result = $email->send();
            
            if ($result) {
                // Log successful send
                log_message('info', "Email sent successfully to: {$data['to']}");
                
                // Update database if email_log_id provided
                if (isset($data['email_log_id'])) {
                    $this->updateEmailLog($data['email_log_id'], 'sent');
                }
                
                return [
                    'status' => 'success',
                    'message' => 'Email sent successfully',
                    'to' => $data['to']
                ];
            } else {
                throw new \Exception('Email sending failed: ' . $email->printDebugger());
            }
            
        } catch (\Exception $e) {
            // Log error
            log_message('error', "Email job failed: {$e->getMessage()}");
            
            // Update database if email_log_id provided
            if (isset($data['email_log_id'])) {
                $this->updateEmailLog($data['email_log_id'], 'failed', $e->getMessage());
            }
            
            throw $e; // Re-throw to trigger retry
        }
    }
    
    /**
     * Update email log status
     */
    private function updateEmailLog(int $logId, string $status, ?string $error = null)
    {
        $db = \Config\Database::connect();
        
        $updateData = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($error) {
            $updateData['error_message'] = $error;
        }
        
        if ($status === 'sent') {
            $updateData['sent_at'] = date('Y-m-d H:i:s');
        }
        
        $db->table('email_logs')->where('id', $logId)->update($updateData);
    }
}

/**
 * ============================================================================
 * NOTIFICATION PROCESSING JOB
 * ============================================================================
 */
class ProcessNotificationJob 
{
    public function handle(array $data): array
    {
        $db = \Config\Database::connect();
        
        try {
            // Create notification in database
            $notificationData = [
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'] ?? 'info',
                'icon' => $data['icon'] ?? 'bell',
                'url' => $data['url'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $notificationId = $db->table('notifications')->insert($notificationData, true);
            
            // Clear user notification cache
            $cache = \Config\Services::cache();
            $cache->delete("user_notifications_{$data['user_id']}");
            $cache->delete("notification_count_{$data['user_id']}");
            
            // If email notification requested
            if ($data['send_email'] ?? false) {
                $queueService = new \App\Services\SimpleQueueService();
                $queueService->push(\App\Jobs\SendEmailJob::class, [
                    'to' => $data['user_email'],
                    'subject' => "Notifikasi: {$data['title']}",
                    'message' => $this->buildEmailTemplate($data),
                    'email_log_id' => null
                ]);
            }
            
            return [
                'status' => 'success',
                'notification_id' => $notificationId,
                'message' => 'Notification processed successfully'
            ];
            
        } catch (\Exception $e) {
            log_message('error', "Notification job failed: {$e->getMessage()}");
            throw $e;
        }
    }
    
    private function buildEmailTemplate(array $data): string
    {
        return view('emails/notification_email', [
            'title' => $data['title'],
            'message' => $data['message'],
            'url' => $data['url'] ?? null,
            'app_name' => 'OPTIMA System'
        ]);
    }
}

/**
 * ============================================================================
 * REPORT GENERATION JOB
 * ============================================================================
 */
class GenerateReportJob
{
    public function handle(array $data): array
    {
        try {
            $reportType = $data['report_type'];
            $userId = $data['user_id'];
            $parameters = $data['parameters'] ?? [];
            
            // Generate report based on type
            switch ($reportType) {
                case 'inventory_summary':
                    $result = $this->generateInventoryReport($parameters);
                    break;
                    
                case 'work_orders_summary':
                    $result = $this->generateWorkOrdersReport($parameters);
                    break;
                    
                case 'purchase_orders_summary':
                    $result = $this->generatePurchaseOrdersReport($parameters);
                    break;
                    
                default:
                    throw new \Exception("Unknown report type: {$reportType}");
            }
            
            // Save report file
            $filename = "report_{$reportType}_" . date('Y-m-d_H-i-s') . '.pdf';
            $filepath = WRITEPATH . 'reports/' . $filename;
            
            if (!is_dir(WRITEPATH . 'reports/')) {
                mkdir(WRITEPATH . 'reports/', 0755, true);
            }
            
            file_put_contents($filepath, $result['content']);
            
            // Notify user that report is ready
            $queueService = new \App\Services\SimpleQueueService();
            $queueService->push(\App\Jobs\ProcessNotificationJob::class, [
                'user_id' => $userId,
                'title' => 'Laporan Siap',
                'message' => "Laporan {$reportType} telah selesai dibuat dan siap diunduh.",
                'type' => 'success',
                'icon' => 'file-pdf',
                'url' => "/reports/download/{$filename}"
            ]);
            
            return [
                'status' => 'success',
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => filesize($filepath)
            ];
            
        } catch (\Exception $e) {
            log_message('error', "Report generation failed: {$e->getMessage()}");
            
            // Notify user of failure
            $queueService = new \App\Services\SimpleQueueService();
            $queueService->push(\App\Jobs\ProcessNotificationJob::class, [
                'user_id' => $data['user_id'],
                'title' => 'Gagal Membuat Laporan',
                'message' => 'Terjadi kesalahan saat membuat laporan. Silakan coba lagi.',
                'type' => 'error',
                'icon' => 'exclamation-triangle'
            ]);
            
            throw $e;
        }
    }
    
    private function generateInventoryReport(array $parameters): array
    {
        // Implementation for inventory report
        // This is a placeholder - implement based on your needs
        return ['content' => 'PDF content here'];
    }
    
    private function generateWorkOrdersReport(array $parameters): array
    {
        // Implementation for work orders report
        return ['content' => 'PDF content here'];
    }
    
    private function generatePurchaseOrdersReport(array $parameters): array
    {
        // Implementation for purchase orders report
        return ['content' => 'PDF content here'];
    }
}