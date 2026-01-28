<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemActivityLogModel extends Model
{
    // ... existing code ...

    /**
     * Get most active users in specified period
     */
    public function getMostActiveUsers($days = 7, $limit = 10)
    {
        $db = $this->db;
        return $db->query("
            SELECT 
                u.id,
                u.username, 
                u.first_name, 
                u.last_name,
                COUNT(sal.id) as activity_count,
                COUNT(CASE WHEN sal.action_type = 'CREATE' THEN 1 END) as creates,
                COUNT(CASE WHEN sal.action_type = 'UPDATE' THEN 1 END) as updates,
                COUNT(CASE WHEN sal.action_type = 'DELETE' THEN 1 END) as deletes,
                COUNT(CASE WHEN sal.is_critical = 1 THEN 1 END) as critical_actions
            FROM system_activity_log sal
            LEFT JOIN users u ON u.id = sal.user_id
            WHERE sal.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY u.id, u.username, u.first_name, u.last_name
            ORDER BY activity_count DESC
            LIMIT ?
        ", [$days, $limit])->getResultArray();
    }

    /**
     * Get most modified tables
     */
    public function getMostModifiedTables($days = 7, $limit = 10)
    {
        return $this->select('table_name, COUNT(*) as modification_count')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")))
            ->whereIn('action_type', ['CREATE', 'UPDATE', 'DELETE'])
            ->groupBy('table_name')
            ->orderBy('modification_count', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get activity trends (daily counts)
     */
    public function getActivityTrends($days = 30)
    {
        $db = $this->db;
        return $db->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as total_count,
                COUNT(CASE WHEN action_type = 'CREATE' THEN 1 END) as creates,
                COUNT(CASE WHEN action_type = 'UPDATE' THEN 1 END) as updates,
                COUNT(CASE WHEN action_type = 'DELETE' THEN 1 END) as deletes,
                COUNT(CASE WHEN is_critical = 1 THEN 1 END) as critical_count
            FROM system_activity_log
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", [$days])->getResultArray();
    }

    /**
     * Get critical activities that need attention
     */
    public function getCriticalActivities($limit = 20)
    {
        return $this->select('system_activity_log.*, users.username, users.first_name, users.last_name')
            ->join('users', 'users.id = system_activity_log.user_id', 'left')
            ->where('is_critical', 1)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Archive old logs (move to archive table)
     */
    public function archiveOldLogs($months = 12)
    {
        $db = $this->db;
        $archiveDate = date('Y-m-d', strtotime("-{$months} months"));

        try {
            $db->transStart();

            // Create archive table if not exists
            $this->createArchiveTable();

            // Copy old logs to archive
            $db->query("
                INSERT INTO system_activity_log_archive 
                SELECT * FROM system_activity_log 
                WHERE created_at < ?
            ", [$archiveDate]);

            // Delete archived logs from main table
            $deleted = $db->query("
                DELETE FROM system_activity_log 
                WHERE created_at < ?
            ", [$archiveDate]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'message' => 'Archive failed'];
            }

            return [
                'success' => true, 
                'archived_count' => $db->affectedRows(),
                'archive_date' => $archiveDate
            ];

        } catch (\Exception $e) {
            log_message('error', 'Archive logs failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create archive table structure
     */
    private function createArchiveTable()
    {
        $db = $this->db;
        if (!$db->tableExists('system_activity_log_archive')) {
            $db->query("
                CREATE TABLE system_activity_log_archive LIKE system_activity_log
            ");
            log_message('info', 'Created system_activity_log_archive table');
        }
    }

    /**
     * Send email alert for critical activities
     */
    public function sendCriticalActivityAlert($activityId)
    {
        $activity = $this->find($activityId);
        
        if (!$activity || !$activity['is_critical']) {
            return false;
        }

        // Get admin emails
        $db = $this->db;
        $admins = $db->table('users')
            ->select('email, first_name')
            ->where('role', 'admin')
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        $email = \Config\Services::email();
        
        foreach ($admins as $admin) {
            $email->setTo($admin['email']);
            $email->setSubject('⚠️ Critical Activity Alert - OPTIMA System');
            
            $message = "
                <h3>Critical Activity Detected</h3>
                <p><strong>Time:</strong> {$activity['created_at']}</p>
                <p><strong>Action:</strong> {$activity['action_type']}</p>
                <p><strong>Module:</strong> {$activity['module_name']}</p>
                <p><strong>Description:</strong> {$activity['action_description']}</p>
                <p><strong>Table:</strong> {$activity['table_name']}</p>
                <p><strong>Impact:</strong> {$activity['business_impact']}</p>
                <hr>
                <p>Please review this activity in the Activity Log.</p>
                <p><a href=\"" . base_url('/admin/activity-log') . "\">View Activity Log</a></p>
            ";
            
            $email->setMessage($message);
            $email->send();
        }

        return true;
    }
}
