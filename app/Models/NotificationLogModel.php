<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationLogModel extends Model
{
    protected $table = 'notification_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'notification_id', 'rule_id', 'total_recipients',
        'successful_deliveries', 'failed_deliveries', 
        'processing_time_ms', 'trigger_data'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    /**
     * Get statistics for notification system
     */
    public function getStatistics($days = 30)
    {
        $builder = $this->db->table($this->table);
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        
        $result = $builder->selectSum('total_recipients', 'total_sent')
                         ->selectSum('successful_deliveries', 'total_delivered')
                         ->selectSum('failed_deliveries', 'total_failed')
                         ->selectAvg('processing_time_ms', 'avg_processing_time')
                         ->get()->getRowArray();

        $result['notification_count'] = $this->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                                            ->countAllResults();

        return $result;
    }
}
