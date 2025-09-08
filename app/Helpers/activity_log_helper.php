<?php

/**
 * System Activity Log Helper Functions
 * Simple functions for logging activities throughout the application
 */

if (!function_exists('log_activity')) {
    /**
     * Log any system activity
     */
    function log_activity(string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        try {
            $logModel = new \App\Models\SystemActivityLogModel();
            return $logModel->logWorkflow($action, $table, $recordId, $description, $options);
        } catch (\Exception $e) {
            log_message('error', 'Activity logging failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('log_create')) {
    /**
     * Log record creation
     */
    function log_create(string $table, int $recordId, array $data, array $options = []): bool
    {
        try {
            $logModel = new \App\Models\SystemActivityLogModel();
            return $logModel->logCreate($table, $recordId, $data, $options);
        } catch (\Exception $e) {
            log_message('error', 'Create logging failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('log_update')) {
    /**
     * Log record update
     */
    function log_update(string $table, int $recordId, array $oldData, array $newData, array $options = []): bool
    {
        try {
            $logModel = new \App\Models\SystemActivityLogModel();
            return $logModel->logUpdate($table, $recordId, $oldData, $newData, $options);
        } catch (\Exception $e) {
            log_message('error', 'Update logging failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('log_delete')) {
    /**
     * Log record deletion
     */
    function log_delete(string $table, int $recordId, array $data, array $options = []): bool
    {
        try {
            $logModel = new \App\Models\SystemActivityLogModel();
            return $logModel->logDelete($table, $recordId, $data, $options);
        } catch (\Exception $e) {
            log_message('error', 'Delete logging failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('log_assign')) {
    /**
     * Log assignment activities (units to contracts, etc)
     */
    function log_assign(string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_activity('ASSIGN', $table, $recordId, $description, array_merge($options, ['is_critical' => true]));
    }
}

if (!function_exists('log_approve')) {
    /**
     * Log approval activities
     */
    function log_approve(string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_activity('APPROVE', $table, $recordId, $description, array_merge($options, ['is_critical' => true]));
    }
}

if (!function_exists('log_workflow_stage')) {
    /**
     * Log workflow stage changes
     */
    function log_workflow_stage(string $table, int $recordId, string $stage, string $description, array $options = []): bool
    {
        return log_activity('UPDATE', $table, $recordId, $description, array_merge($options, [
            'workflow_stage' => $stage,
            'is_critical' => true
        ]));
    }
}

if (!function_exists('get_record_history')) {
    /**
     * Get activity history for a specific record
     */
    function get_record_history(string $table, int $recordId, int $limit = 50): array
    {
        try {
            $logModel = new \App\Models\SystemActivityLogModel();
            return $logModel->getRecordHistory($table, $recordId, $limit);
        } catch (\Exception $e) {
            log_message('error', 'Get history failed: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('get_workflow_timeline')) {
    /**
     * Get workflow timeline for tracking
     */
    function get_workflow_timeline(array $criteria, int $limit = 100): array
    {
        try {
            $logModel = new \App\Models\SystemActivityLogModel();
            return $logModel->getWorkflowTimeline($criteria, $limit);
        } catch (\Exception $e) {
            log_message('error', 'Get timeline failed: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('format_activity_description')) {
    /**
     * Format activity description with context
     */
    function format_activity_description(string $action, string $table, array $context = []): string
    {
        $actions = [
            'CREATE' => 'dibuat',
            'UPDATE' => 'diperbarui',
            'DELETE' => 'dihapus',
            'ASSIGN' => 'diassign',
            'UNASSIGN' => 'dibatalkan assignmentnya',
            'APPROVE' => 'disetujui',
            'REJECT' => 'ditolak',
            'COMPLETE' => 'diselesaikan',
            'CANCEL' => 'dibatalkan'
        ];

        $tables = [
            'kontrak' => 'Kontrak',
            'spk' => 'SPK',
            'delivery_instructions' => 'Delivery Instruction',
            'inventory_unit' => 'Unit Inventory',
            'kontrak_spesifikasi' => 'Spesifikasi Kontrak'
        ];

        $actionText = $actions[$action] ?? strtolower($action);
        $tableText = $tables[$table] ?? ucfirst(str_replace('_', ' ', $table));
        
        $description = "{$tableText} {$actionText}";
        
        // Add context if provided
        if (!empty($context)) {
            if (isset($context['nomor'])) {
                $description .= " dengan nomor {$context['nomor']}";
            }
            if (isset($context['pelanggan'])) {
                $description .= " untuk pelanggan {$context['pelanggan']}";
            }
            if (isset($context['harga'])) {
                $description .= " dengan harga Rp " . number_format($context['harga'], 0, ',', '.');
            }
        }

        return $description;
    }
}
