<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemActivityLogModel extends Model
{
    protected $table            = 'system_activity_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        // Core fields (8 fields) - ESSENTIAL
        'table_name', 'record_id', 'action_type', 'action_description',
        'old_values', 'new_values', 'affected_fields', 'user_id',
        
        // Business context (3 fields) - IMPORTANT  
        'workflow_stage', 'is_critical', 'module_name', 'business_impact',
        
        // Related entities (3 fields) - BUSINESS CRITICAL
        'related_kontrak_id', 'related_spk_id', 'related_di_id'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null; // No updated_at for logs

    /**
     * Log system activity with automatic context detection
     */
    public function logActivity(array $data): bool
    {
        $request = service('request');
        $session = session();
        
        // Auto-fill system context
        $logData = array_merge([
            'user_id' => $session->get('user_id') ?? null,
            'session_id' => $session->session_id ?? null,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => substr($request->getUserAgent()->getAgentString(), 0, 500),
            'request_method' => $request->getMethod(),
            'request_url' => substr($request->getPath(), 0, 255),
        ], $data);

        return $this->insert($logData) !== false;
    }

    /**
     * Log CREATE activity
     */
    public function logCreate(string $tableName, int $recordId, array $newData, array $options = []): bool
    {
        return $this->logActivity([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action_type' => 'CREATE',
            'action_description' => $options['description'] ?? "New {$tableName} record created",
            'new_values' => json_encode($newData),
            'affected_fields' => json_encode(array_keys($newData)),
            'workflow_stage' => $options['workflow_stage'] ?? null,
            'is_critical' => $options['is_critical'] ?? false,
            'related_kontrak_id' => $options['related_kontrak_id'] ?? null,
            'related_spk_id' => $options['related_spk_id'] ?? null,
            'related_di_id' => $options['related_di_id'] ?? null,
        ]);
    }

    /**
     * Log UPDATE activity  
     */
    public function logUpdate(string $tableName, int $recordId, array $oldData, array $newData, array $options = []): bool
    {
        // Only log changed fields
        $changes = [];
        $affectedFields = [];
        
        foreach ($newData as $field => $newValue) {
            $oldValue = $oldData[$field] ?? null;
            if ($oldValue !== $newValue) {
                $changes['old'][$field] = $oldValue;
                $changes['new'][$field] = $newValue;
                $affectedFields[] = $field;
            }
        }

        if (empty($affectedFields)) {
            return true; // No changes, skip logging
        }

        return $this->logActivity([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action_type' => 'UPDATE',
            'action_description' => $options['description'] ?? "Updated {$tableName} record: " . implode(', ', $affectedFields),
            'old_values' => json_encode($changes['old']),
            'new_values' => json_encode($changes['new']),
            'affected_fields' => json_encode($affectedFields),
            'workflow_stage' => $options['workflow_stage'] ?? null,
            'is_critical' => $options['is_critical'] ?? false,
            'related_kontrak_id' => $options['related_kontrak_id'] ?? null,
            'related_spk_id' => $options['related_spk_id'] ?? null,
            'related_di_id' => $options['related_di_id'] ?? null,
        ]);
    }

    /**
     * Log DELETE activity
     */
    public function logDelete(string $tableName, int $recordId, array $deletedData, array $options = []): bool
    {
        return $this->logActivity([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action_type' => 'DELETE',
            'action_description' => $options['description'] ?? "Deleted {$tableName} record",
            'old_values' => json_encode($deletedData),
            'affected_fields' => json_encode(array_keys($deletedData)),
            'workflow_stage' => $options['workflow_stage'] ?? null,
            'is_critical' => $options['is_critical'] ?? true, // Deletes are usually critical
            'related_kontrak_id' => $options['related_kontrak_id'] ?? null,
            'related_spk_id' => $options['related_spk_id'] ?? null,
            'related_di_id' => $options['related_di_id'] ?? null,
        ]);
    }

    /**
     * Log business workflow activities (ASSIGN, APPROVE, etc)
     */
    public function logWorkflow(string $action, string $tableName, int $recordId, string $description, array $options = []): bool
    {
        return $this->logActivity([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action_type' => strtoupper($action),
            'action_description' => $description,
            'new_values' => isset($options['data']) ? json_encode($options['data']) : null,
            'workflow_stage' => $options['workflow_stage'] ?? null,
            'is_critical' => $options['is_critical'] ?? true, // Workflow actions are usually critical
            'related_kontrak_id' => $options['related_kontrak_id'] ?? null,
            'related_spk_id' => $options['related_spk_id'] ?? null,
            'related_di_id' => $options['related_di_id'] ?? null,
        ]);
    }

    /**
     * Get activity log for a specific record
     */
    public function getRecordHistory(string $tableName, int $recordId, int $limit = 50): array
    {
        return $this->select('system_activity_log.*, users.username, CONCAT(users.first_name, " ", users.last_name) as user_full_name')
                    ->join('users', 'users.id = system_activity_log.user_id', 'left')
                    ->where('table_name', $tableName)
                    ->where('record_id', $recordId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get workflow timeline for tracking
     */
    public function getWorkflowTimeline(array $criteria, int $limit = 100): array
    {
        $builder = $this->db->table('v_workflow_tracking');
        
        if (isset($criteria['kontrak_id'])) {
            $builder->where('related_kontrak_id', $criteria['kontrak_id']);
        }
        if (isset($criteria['spk_id'])) {
            $builder->where('related_spk_id', $criteria['spk_id']);
        }
        if (isset($criteria['di_id'])) {
            $builder->where('related_di_id', $criteria['di_id']);
        }
        if (isset($criteria['document_reference'])) {
            $builder->like('document_reference', $criteria['document_reference']);
        }

        return $builder->orderBy('created_at', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats(array $filters = []): array
    {
        $builder = $this->db->table('system_activity_log sal')
                           ->select('sal.action_type, sal.table_name, COUNT(*) as count, 
                                   COUNT(DISTINCT sal.user_id) as unique_users,
                                   MAX(sal.created_at) as last_activity')
                           ->groupBy('sal.action_type, sal.table_name');

        if (isset($filters['date_from'])) {
            $builder->where('sal.created_at >=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $builder->where('sal.created_at <=', $filters['date_to']);
        }
        if (isset($filters['user_id'])) {
            $builder->where('sal.user_id', $filters['user_id']);
        }
        if (isset($filters['is_critical'])) {
            $builder->where('sal.is_critical', $filters['is_critical']);
        }

        return $builder->orderBy('count', 'DESC')
                       ->get()
                       ->getResultArray();
    }
}
