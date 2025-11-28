<?php

namespace App\Jobs;

/**
 * ============================================================================
 * EMAIL SENDING JOB - Background Email Processing
 * ============================================================================
 */
class SendEmailJob
{
    public function handle(array $data): array
    {
        $email = \Config\Services::email();
        
        try {
            $email->setTo($data['to'], $data['to_name'] ?? '');
            $email->setSubject($data['subject']);
            $email->setMessage($data['message']);
            
            if (isset($data['from'])) {
                $email->setFrom($data['from'], $data['from_name'] ?? '');
            }
            
            $result = $email->send();
            
            if ($result) {
                log_message('info', "Email sent successfully to: {$data['to']}");
                return [
                    'status' => 'success',
                    'message' => 'Email sent successfully',
                    'to' => $data['to']
                ];
            } else {
                throw new \Exception('Email sending failed');
            }
            
        } catch (\Exception $e) {
            log_message('error', "Email job failed: {$e->getMessage()}");
            throw $e;
        }
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
            
            // Clear cache
            $cache = \Config\Services::cache();
            $cache->delete("user_notifications_{$data['user_id']}");
            $cache->delete("notification_count_{$data['user_id']}");
            
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
}