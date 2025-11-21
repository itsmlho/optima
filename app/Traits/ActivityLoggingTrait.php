<?php

namespace App\Traits;

use App\Models\SystemActivityLogModel;

/**
 * Activity Logging Trait for OptimaPro Controllers
 * 
 * This trait provides consistent activity logging functionality
 * across all controllers using JSON relations approach.
 * 
 * @author OptimaPro Team
 * @version 1.0.0
 * @since 2025-09-09
 */
trait ActivityLoggingTrait
{
    /**
     * @var SystemActivityLogModel
     */
    protected $activityLogModel;

    /**
     * Initialize activity logging
     */
    protected function initializeActivityLogging(): void
    {
        if (!isset($this->activityLogModel)) {
            $this->activityLogModel = new SystemActivityLogModel();
        }
    }

    /**
     * Log CREATE activity with JSON relations
     * 
     * @param string $tableName Table name (e.g., 'kontrak', 'spk', 'po')
     * @param int $recordId Record ID that was created
     * @param array $newData Data that was created
     * @param array $options Additional options for logging
     * @return bool Success status
     */
    protected function logCreate(string $tableName, int $recordId, array $newData, array $options = []): bool
    {
        $this->initializeActivityLogging();
        
        $defaultOptions = [
            'user_id' => session('user_id') ?? 1,
            'description' => "Created new {$tableName} record",
            'module_name' => $this->getModuleName(),
            'submenu_item' => $options['submenu_item'] ?? $this->getSubmenuItem(),
            'workflow_stage' => $options['workflow_stage'] ?? 'CREATED',
            'business_impact' => $options['business_impact'] ?? 'MEDIUM',
            'relations' => $options['relations'] ?? [$tableName => [$recordId]],
            'is_critical' => $options['is_critical'] ?? 0
        ];
        
        $finalOptions = array_merge($defaultOptions, $options);
        
        return $this->activityLogModel->logCreate($tableName, $recordId, $newData, $finalOptions);
    }

    /**
     * Log UPDATE activity with JSON relations
     * 
     * @param string $tableName Table name
     * @param int $recordId Record ID that was updated
     * @param array $oldData Original data
     * @param array $newData Updated data
     * @param array $options Additional options for logging
     * @return bool Success status
     */
    protected function logUpdate(string $tableName, int $recordId, array $oldData, array $newData, array $options = []): bool
    {
        $this->initializeActivityLogging();
        
        $defaultOptions = [
            'user_id' => session('user_id') ?? 1,
            'description' => "Updated {$tableName} record",
            'module_name' => $this->getModuleName(),
            'submenu_item' => $options['submenu_item'] ?? $this->getSubmenuItem(),
            'workflow_stage' => $options['workflow_stage'] ?? 'UPDATED',
            'business_impact' => $options['business_impact'] ?? 'MEDIUM',
            'relations' => $options['relations'] ?? [$tableName => [$recordId]],
            'is_critical' => $options['is_critical'] ?? 0
        ];
        
        $finalOptions = array_merge($defaultOptions, $options);
        
        return $this->activityLogModel->logUpdate($tableName, $recordId, $oldData, $newData, $finalOptions);
    }

    /**
     * Log DELETE activity with JSON relations
     * 
     * @param string $tableName Table name
     * @param int $recordId Record ID that was deleted
     * @param array $deletedData Data that was deleted
     * @param array $options Additional options for logging
     * @return bool Success status
     */
    protected function logDelete(string $tableName, int $recordId, array $deletedData, array $options = []): bool
    {
        $this->initializeActivityLogging();
        
        $defaultOptions = [
            'user_id' => session('user_id') ?? 1,
            'description' => "Deleted {$tableName} record",
            'module_name' => $this->getModuleName(),
            'submenu_item' => $options['submenu_item'] ?? $this->getSubmenuItem(),
            'workflow_stage' => $options['workflow_stage'] ?? 'DELETED',
            'business_impact' => $options['business_impact'] ?? 'HIGH',
            'relations' => $options['relations'] ?? [$tableName => [$recordId]],
            'is_critical' => $options['is_critical'] ?? 1
        ];
        
        $finalOptions = array_merge($defaultOptions, $options);
        
        return $this->activityLogModel->logDelete($tableName, $recordId, $deletedData, $finalOptions);
    }

    /**
     * Log custom activity
     * 
     * @param string $action Action type (e.g., 'APPROVE', 'REJECT', 'EXPORT')
     * @param string $tableName Table name
     * @param int $recordId Record ID
     * @param string $description Activity description
     * @param array $options Additional options
     * @return bool Success status
     */
    protected function logActivity(string $action, string $tableName, int $recordId, string $description, array $options = []): bool
    {
        $this->initializeActivityLogging();
        
        $defaultOptions = [
            'user_id' => session('user_id') ?? 1,
            'module_name' => $this->getModuleName(),
            'submenu_item' => $options['submenu_item'] ?? $this->getSubmenuItem(),
            'workflow_stage' => $options['workflow_stage'] ?? $action,
            'business_impact' => $options['business_impact'] ?? 'MEDIUM',
            'relations' => $options['relations'] ?? [$tableName => [$recordId]],
            'is_critical' => $options['is_critical'] ?? 0
        ];
        
        $finalOptions = array_merge($defaultOptions, $options);
        
        return $this->activityLogModel->logActivity([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action_type' => $action,
            'action_description' => $description,
            'user_id' => $finalOptions['user_id'],
            'module_name' => $finalOptions['module_name'],
            'submenu_item' => $finalOptions['submenu_item'],
            'workflow_stage' => $finalOptions['workflow_stage'],
            'business_impact' => $finalOptions['business_impact'],
            'is_critical' => $finalOptions['is_critical'],
            'related_entities' => $this->activityLogModel->buildRelatedEntities($finalOptions['relations'])
        ]);
    }

    /**
     * Build relations array for JSON logging
     * 
     * @param string $primaryTable Primary table name
     * @param int $primaryId Primary record ID
     * @param array $additionalRelations Additional relations [table => [ids]]
     * @return array Relations array
     */
    protected function buildRelations(string $primaryTable, int $primaryId, array $additionalRelations = []): array
    {
        $relations = [$primaryTable => [$primaryId]];
        
        foreach ($additionalRelations as $table => $ids) {
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            $relations[$table] = array_map('intval', array_filter($ids));
        }
        
        return $relations;
    }

    /**
     * Get module name based on controller
     * Override this method in each controller for specific module names
     * 
     * @return string Module name
     */
    protected function getModuleName(): string
    {
        $controllerName = strtolower(str_replace('Controller', '', get_class($this)));
        
        // Map controller names to module names
        $moduleMap = [
            'kontrak' => 'MARKETING',
            'marketing' => 'MARKETING',
            'service' => 'SERVICE',
            'operational' => 'OPERATIONAL',
            'purchasing' => 'PURCHASING',
            'warehouse' => 'WAREHOUSE',
            'finance' => 'ACCOUNTING',
            'admin' => 'ADMIN',
            'auth' => 'USER_MANAGEMENT',
            'system' => 'ADMIN',
            'settings' => 'SETTINGS',
            'reports' => 'REPORTS',
            'dashboard' => 'DASHBOARD'
        ];
        
        foreach ($moduleMap as $key => $module) {
            if (strpos($controllerName, $key) !== false) {
                return $module;
            }
        }
        
        return 'ADMIN';
    }

    /**
     * Get submenu item based on controller and method
     * Override this method in each controller for specific submenu items
     * 
     * @return string Submenu item
     */
    protected function getSubmenuItem(): string
    {
        $controllerName = strtolower(str_replace('Controller', '', get_class($this)));
        $router = service('router');
        $method = $router->methodName() ?? 'index';
        
        // Default mapping
        $submenuMap = [
            'kontrak' => 'Data Kontrak',
            'service' => 'Service Management',
            'purchasing' => 'Purchase Orders',
            'warehouse' => 'Warehouse Management',
            'operational' => 'Operational Data',
            'finance' => 'Financial Management',
            'admin' => 'System Administration',
            'auth' => 'User Authentication',
            'settings' => 'System Settings',
            'reports' => 'Report Generation'
        ];
        
        foreach ($submenuMap as $key => $submenu) {
            if (strpos($controllerName, $key) !== false) {
                return $submenu;
            }
        }
        
        return ucfirst($controllerName) . ' Management';
    }

    /**
     * Log user authentication activities
     * 
     * @param string $action LOGIN, LOGOUT, FAILED_LOGIN, etc.
     * @param int $userId User ID
     * @param array $details Additional details
     * @return bool Success status
     */
    protected function logAuthActivity(string $action, int $userId, array $details = []): bool
    {
        $this->initializeActivityLogging();
        
        $description = match($action) {
            'LOGIN' => 'User logged in successfully',
            'LOGOUT' => 'User logged out',
            'FAILED_LOGIN' => 'Failed login attempt',
            'PASSWORD_CHANGED' => 'User changed password',
            'PROFILE_UPDATED' => 'User updated profile',
            default => "User {$action}"
        };
        
        $actionType = match($action) {
            'LOGIN' => 'LOGIN',
            'LOGOUT' => 'LOGOUT',
            'FAILED_LOGIN' => 'FAILED_LOGIN',
            'PASSWORD_CHANGED' => 'PASSWORD_CHANGED',
            'PROFILE_UPDATED' => 'PROFILE_UPDATED',
            default => 'AUTH_ACTIVITY'
        };
        
        return $this->logActivity($actionType, 'users', $userId, $description, [
            'module_name' => 'USER_MANAGEMENT',
            'submenu_item' => 'User Session',
            'workflow_stage' => $action,
            'business_impact' => in_array($action, ['FAILED_LOGIN', 'PASSWORD_CHANGED']) ? 'HIGH' : 'LOW',
            'is_critical' => $action === 'FAILED_LOGIN' ? 1 : 0,
            'relations' => ['users' => [$userId]]
        ]);
    }

    /**
     * Log workflow stage changes
     * 
     * @param string $tableName Table name
     * @param int $recordId Record ID
     * @param string $fromStage Previous stage
     * @param string $toStage New stage
     * @param array $options Additional options
     * @return bool Success status
     */
    protected function logWorkflowChange(string $tableName, int $recordId, string $fromStage, string $toStage, array $options = []): bool
    {
        $description = "Workflow changed from '{$fromStage}' to '{$toStage}'";
        
        $defaultOptions = [
            'workflow_stage' => $toStage,
            'business_impact' => 'MEDIUM',
            'is_critical' => 0
        ];
        
        $finalOptions = array_merge($defaultOptions, $options);
        
        return $this->logActivity('WORKFLOW_CHANGE', $tableName, $recordId, $description, $finalOptions);
    }

}
