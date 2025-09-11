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
        // Core fields - ESSENTIAL
        'table_name', 'record_id', 'action_type', 'action_description',
        'old_values', 'new_values', 'affected_fields', 'user_id',
        
        // Business context - IMPORTANT  
        'workflow_stage', 'is_critical', 'module_name', 'business_impact', 'submenu_item',
        
        // JSON Relations - FLEXIBLE APPROACH
        'related_entities'
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
        
        // Auto-fill system context using only available fields
        $logData = array_merge([
            'user_id' => $session->get('user_id') ?? null,
        ], $data);

        // Remove any fields that don't exist in the table - Updated for JSON approach
        $allowedFields = [
            'table_name', 'record_id', 'action_type', 'action_description',
            'old_values', 'new_values', 'affected_fields', 'user_id',
            'workflow_stage', 'is_critical', 'module_name', 'business_impact', 'submenu_item',
            'related_entities'
        ];
        
        $filteredData = [];
        foreach ($logData as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $filteredData[$key] = $value;
            }
        }

        // Remove null values to avoid SQL syntax errors
        $cleanData = [];
        foreach ($filteredData as $key => $value) {
            if ($value !== null) {
                $cleanData[$key] = $value;
            }
        }

        // Use direct database insert to avoid Model field filtering issues
        $db = \Config\Database::connect();
        
        try {
            $result = $db->table('system_activity_log')->insert($cleanData);
            return $result !== false;
        } catch (\Exception $e) {
            log_message('error', 'Activity Log Insert Error: ' . $e->getMessage());
            log_message('error', 'Data attempted: ' . json_encode($cleanData));
            return false;
        }
    }

    /**
     * Log CREATE activity with JSON relations
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
            'module_name' => $options['module_name'] ?? null,
            'submenu_item' => $options['submenu_item'] ?? null,
            'business_impact' => $options['business_impact'] ?? 'LOW',
            'related_entities' => $this->buildRelatedEntities($options['relations'] ?? [])
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
            'module_name' => $options['module_name'] ?? null,
            'submenu_item' => $options['submenu_item'] ?? null,
            'business_impact' => $options['business_impact'] ?? 'LOW',
            'related_entities' => $this->buildRelatedEntities($options['relations'] ?? [])
        ]);
    }

    /**
     * Log DELETE activity with JSON relations
     */
    public function logDelete(string $tableName, int $recordId, array $deletedData, array $options = []): bool
    {
        // Use direct query builder to bypass potential Model field filtering issues
        $db = \Config\Database::connect();
        
        $data = [
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action_type' => 'DELETE',
            'action_description' => $options['description'] ?? "Deleted {$tableName} record",
            'old_values' => json_encode($deletedData),
            'affected_fields' => json_encode(array_keys($deletedData)),
            'user_id' => $options['user_id'] ?? null,
            'workflow_stage' => $options['workflow_stage'] ?? null,
            'is_critical' => $options['is_critical'] ?? 1,
            'module_name' => $options['module_name'] ?? null,
            'submenu_item' => $options['submenu_item'] ?? null,
            'business_impact' => $options['business_impact'] ?? null,
            'related_entities' => $this->buildRelatedEntities($options['relations'] ?? [])
        ];
        
        // Remove null values to avoid SQL issues
        $cleanData = [];
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $cleanData[$key] = $value;
            }
        }
        
        try {
            return $db->table('system_activity_log')->insert($cleanData);
        } catch (\Exception $e) {
            log_message('error', 'SystemActivityLogModel::logDelete failed: ' . $e->getMessage());
            return false;
        }
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
     * Get activity statistics with JSON relations support
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

    /**
     * Build JSON relations object from array of relations
     * 
     * @param array $relations Format: ['kontrak' => [123, 456], 'spk' => [789]]
     * @return string|null JSON string or null if empty
     */
    public function buildRelatedEntities(array $relations): ?string
    {
        if (empty($relations)) {
            return null;
        }

        // Clean and validate relations
        $cleanRelations = [];
        foreach ($relations as $entityType => $entityIds) {
            if (!is_array($entityIds)) {
                $entityIds = [$entityIds];
            }
            
            // Filter out null/empty IDs and convert to integers
            $validIds = array_filter($entityIds, function($id) {
                return !is_null($id) && is_numeric($id) && $id > 0;
            });
            
            if (!empty($validIds)) {
                $cleanRelations[$entityType] = array_map('intval', $validIds);
            }
        }

        return !empty($cleanRelations) ? json_encode($cleanRelations) : null;
    }

    /**
     * Search logs by related entity
     * 
     * @param string $entityType e.g., 'kontrak', 'spk', 'di'
     * @param int|array $entityIds Single ID or array of IDs
     * @param int $limit
     * @return array
     */
    public function findByRelatedEntity(string $entityType, $entityIds, int $limit = 50): array
    {
        if (!is_array($entityIds)) {
            $entityIds = [$entityIds];
        }

        $builder = $this->select('*')
                       ->where('related_entities IS NOT NULL');

        // Use JSON_CONTAINS for each entity ID
        $conditions = [];
        foreach ($entityIds as $id) {
            $conditions[] = "JSON_CONTAINS(related_entities, '{$id}', '$.{$entityType}')";
        }

        if (!empty($conditions)) {
            $builder->where('(' . implode(' OR ', $conditions) . ')');
        }

        return $builder->orderBy('created_at', 'DESC')
                       ->limit($limit)
                       ->findAll();
    }

    /**
     * Get entities that have any relations
     * 
     * @param int $limit
     * @return array
     */
    public function getLogsWithRelations(int $limit = 50): array
    {
        return $this->select('*')
                   ->where('related_entities IS NOT NULL')
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}
