<?php

/**
 * Comprehensive Activity Logging Helpers for ALL Modules
 * Purchasing, Warehouse, Marketing, Service, Operational, Accounting, Perizinan
 */

if (!function_exists('log_module_activity')) {
    /**
     * Log activity for any module with comprehensive context
     */
    function log_module_activity(string $module, string $feature, string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        try {
            $logModel = new \App\Models\SystemActivityLogModel();
            
            $data = [
                'module_name' => strtoupper($module),
                'feature_name' => $feature,
                'sub_feature' => $options['sub_feature'] ?? null,
                'table_name' => $table,
                'record_id' => $recordId,
                'action_type' => strtoupper($action),
                'action_description' => $description,
                'business_impact' => $options['business_impact'] ?? 'LOW',
                'compliance_relevant' => $options['compliance_relevant'] ?? false,
                'financial_impact' => $options['financial_impact'] ?? null,
                'workflow_stage' => $options['workflow_stage'] ?? null,
                'is_critical' => $options['is_critical'] ?? false,
                
                // All possible references
                'related_kontrak_id' => $options['kontrak_id'] ?? null,
                'related_spk_id' => $options['spk_id'] ?? null,
                'related_di_id' => $options['di_id'] ?? null,
                'related_purchase_order_id' => $options['po_id'] ?? null,
                'related_vendor_id' => $options['vendor_id'] ?? null,
                'related_customer_id' => $options['customer_id'] ?? null,
                'related_invoice_id' => $options['invoice_id'] ?? null,
                'related_payment_id' => $options['payment_id'] ?? null,
                'related_permit_id' => $options['permit_id'] ?? null,
                'related_warehouse_id' => $options['warehouse_id'] ?? null,
                
                // Enhanced context
                'device_type' => detect_device_type(),
                'browser_name' => detect_browser(),
                'operating_system' => detect_os(),
                'referrer_url' => $_SERVER['HTTP_REFERER'] ?? null,
                
                // Performance metrics
                'execution_time_ms' => $options['execution_time'] ?? null,
                'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024, 2),
                'query_count' => $options['query_count'] ?? null,
                'cache_hit' => $options['cache_hit'] ?? null,
                
                // Batch support
                'batch_id' => $options['batch_id'] ?? null,
                'batch_sequence' => $options['batch_sequence'] ?? null,
                'parent_activity_id' => $options['parent_activity_id'] ?? null,
                
                // Change tracking
                'old_values' => isset($options['old_data']) ? json_encode($options['old_data']) : null,
                'new_values' => isset($options['new_data']) ? json_encode($options['new_data']) : null,
                'affected_fields' => isset($options['changed_fields']) ? json_encode($options['changed_fields']) : null,
            ];
            
            return $logModel->logActivity($data);
        } catch (\Exception $e) {
            log_message('error', 'Module activity logging failed: ' . $e->getMessage());
            return false;
        }
    }
}

// =============================================================================
// PURCHASING MODULE HELPERS
// =============================================================================

if (!function_exists('log_purchasing_activity')) {
    function log_purchasing_activity(string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_module_activity('PURCHASING', $options['feature'] ?? 'General', $action, $table, $recordId, $description, array_merge($options, [
            'business_impact' => $options['business_impact'] ?? 'MEDIUM'
        ]));
    }
}

if (!function_exists('log_po_create')) {
    function log_po_create(int $poId, array $poData, array $options = []): bool
    {
        return log_purchasing_activity('PO_CREATE', 'purchase_orders', $poId, 
            "Purchase Order {$poData['po_number']} dibuat untuk vendor {$poData['vendor_name']}", [
                'feature' => 'Purchase Orders',
                'business_impact' => 'HIGH',
                'financial_impact' => $poData['total_amount'] ?? null,
                'vendor_id' => $poData['vendor_id'] ?? null,
                'new_data' => $poData
            ]
        );
    }
}

if (!function_exists('log_vendor_activity')) {
    function log_vendor_activity(string $action, int $vendorId, string $vendorName, array $options = []): bool
    {
        return log_purchasing_activity($action, 'vendors', $vendorId,
            "Vendor {$vendorName} {$action}", array_merge($options, [
                'feature' => 'Vendor Management',
                'vendor_id' => $vendorId
            ])
        );
    }
}

// =============================================================================
// WAREHOUSE MODULE HELPERS
// =============================================================================

if (!function_exists('log_warehouse_activity')) {
    function log_warehouse_activity(string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_module_activity('WAREHOUSE', $options['feature'] ?? 'Inventory', $action, $table, $recordId, $description, array_merge($options, [
            'business_impact' => $options['business_impact'] ?? 'MEDIUM'
        ]));
    }
}

if (!function_exists('log_stock_movement')) {
    function log_stock_movement(string $type, int $itemId, array $movementData, array $options = []): bool
    {
        return log_warehouse_activity("STOCK_{$type}", 'stock_movements', $movementData['movement_id'], 
            "Stock {$type}: {$movementData['quantity']} unit {$movementData['item_name']}", [
                'feature' => 'Stock Management',
                'warehouse_id' => $movementData['warehouse_id'] ?? null,
                'new_data' => $movementData
            ]
        );
    }
}

// =============================================================================
// MARKETING MODULE HELPERS
// =============================================================================

if (!function_exists('log_marketing_activity')) {
    function log_marketing_activity(string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_module_activity('MARKETING', $options['feature'] ?? 'Sales', $action, $table, $recordId, $description, array_merge($options, [
            'business_impact' => $options['business_impact'] ?? 'MEDIUM'
        ]));
    }
}

if (!function_exists('log_kontrak_activity')) {
    function log_kontrak_activity(string $action, int $kontrakId, array $kontrakData, array $options = []): bool
    {
        return log_marketing_activity($action, 'kontrak', $kontrakId,
            "Kontrak {$kontrakData['nomor_po']} untuk {$kontrakData['pelanggan']} - {$action}", [
                'feature' => 'Contract Management',
                'business_impact' => 'HIGH',
                'financial_impact' => $kontrakData['total_value'] ?? null,
                'kontrak_id' => $kontrakId,
                'customer_id' => $kontrakData['customer_id'] ?? null,
                'new_data' => $kontrakData
            ]
        );
    }
}

if (!function_exists('log_unit_assignment')) {
    function log_unit_assignment(int $unitId, int $kontrakId, array $assignmentData, array $options = []): bool
    {
        return log_marketing_activity('UNIT_ASSIGN', 'inventory_unit', $unitId,
            "Unit {$assignmentData['unit_number']} diassign ke kontrak {$assignmentData['po_number']}", [
                'feature' => 'Unit Assignment',
                'business_impact' => 'HIGH',
                'financial_impact' => $assignmentData['monthly_rate'] ?? null,
                'kontrak_id' => $kontrakId,
                'new_data' => $assignmentData
            ]
        );
    }
}

// =============================================================================
// SERVICE MODULE HELPERS
// =============================================================================

if (!function_exists('log_service_activity')) {
    function log_service_activity(string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_module_activity('SERVICE', $options['feature'] ?? 'Workshop', $action, $table, $recordId, $description, array_merge($options, [
            'business_impact' => $options['business_impact'] ?? 'MEDIUM'
        ]));
    }
}

if (!function_exists('log_spk_activity')) {
    function log_spk_activity(string $action, int $spkId, array $spkData, array $options = []): bool
    {
        return log_service_activity($action, 'spk', $spkId,
            "SPK {$spkData['nomor_spk']} untuk {$spkData['pelanggan']} - {$action}", [
                'feature' => 'Service Work Orders',
                'business_impact' => 'HIGH',
                'spk_id' => $spkId,
                'kontrak_id' => $spkData['kontrak_id'] ?? null,
                'new_data' => $spkData
            ]
        );
    }
}

// =============================================================================
// OPERATIONAL MODULE HELPERS
// =============================================================================

if (!function_exists('log_operational_activity')) {
    function log_operational_activity(string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_module_activity('OPERATIONAL', $options['feature'] ?? 'Logistics', $action, $table, $recordId, $description, array_merge($options, [
            'business_impact' => $options['business_impact'] ?? 'HIGH'
        ]));
    }
}

if (!function_exists('log_delivery_activity')) {
    function log_delivery_activity(string $action, int $diId, array $deliveryData, array $options = []): bool
    {
        return log_operational_activity($action, 'delivery_instructions', $diId,
            "DI {$deliveryData['nomor_di']} untuk {$deliveryData['pelanggan']} - {$action}", [
                'feature' => 'Delivery Management',
                'business_impact' => 'CRITICAL',
                'di_id' => $diId,
                'spk_id' => $deliveryData['spk_id'] ?? null,
                'kontrak_id' => $deliveryData['kontrak_id'] ?? null,
                'new_data' => $deliveryData
            ]
        );
    }
}

// =============================================================================
// ACCOUNTING MODULE HELPERS
// =============================================================================

if (!function_exists('log_accounting_activity')) {
    function log_accounting_activity(string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_module_activity('ACCOUNTING', $options['feature'] ?? 'Finance', $action, $table, $recordId, $description, array_merge($options, [
            'business_impact' => $options['business_impact'] ?? 'HIGH',
            'compliance_relevant' => true
        ]));
    }
}

if (!function_exists('log_invoice_activity')) {
    function log_invoice_activity(string $action, int $invoiceId, array $invoiceData, array $options = []): bool
    {
        return log_accounting_activity($action, 'invoices', $invoiceId,
            "Invoice {$invoiceData['invoice_number']} untuk {$invoiceData['customer_name']} - {$action}", [
                'feature' => 'Invoicing',
                'business_impact' => 'HIGH',
                'financial_impact' => $invoiceData['amount'] ?? null,
                'invoice_id' => $invoiceId,
                'customer_id' => $invoiceData['customer_id'] ?? null,
                'kontrak_id' => $invoiceData['kontrak_id'] ?? null,
                'new_data' => $invoiceData
            ]
        );
    }
}

if (!function_exists('log_payment_activity')) {
    function log_payment_activity(string $action, int $paymentId, array $paymentData, array $options = []): bool
    {
        return log_accounting_activity($action, 'payments', $paymentId,
            "Payment Rp " . number_format($paymentData['amount'], 0, ',', '.') . " dari {$paymentData['customer_name']} - {$action}", [
                'feature' => 'Payment Processing',
                'business_impact' => 'CRITICAL',
                'financial_impact' => $paymentData['amount'] ?? null,
                'payment_id' => $paymentId,
                'invoice_id' => $paymentData['invoice_id'] ?? null,
                'customer_id' => $paymentData['customer_id'] ?? null,
                'new_data' => $paymentData
            ]
        );
    }
}

// =============================================================================
// PERIZINAN MODULE HELPERS
// =============================================================================

if (!function_exists('log_perizinan_activity')) {
    function log_perizinan_activity(string $action, string $table, int $recordId, string $description, array $options = []): bool
    {
        return log_module_activity('PERIZINAN', $options['feature'] ?? 'Permits', $action, $table, $recordId, $description, array_merge($options, [
            'business_impact' => $options['business_impact'] ?? 'HIGH',
            'compliance_relevant' => true
        ]));
    }
}

if (!function_exists('log_permit_activity')) {
    function log_permit_activity(string $action, int $permitId, array $permitData, array $options = []): bool
    {
        return log_perizinan_activity($action, 'permits', $permitId,
            "Permit {$permitData['permit_number']} - {$permitData['permit_type']} - {$action}", [
                'feature' => 'Permit Management',
                'business_impact' => 'CRITICAL',
                'permit_id' => $permitId,
                'new_data' => $permitData
            ]
        );
    }
}

// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

if (!function_exists('detect_device_type')) {
    function detect_device_type(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            return 'MOBILE';
        } elseif (strpos($userAgent, 'Tablet') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'TABLET';
        } elseif (strpos($userAgent, 'API') !== false || strpos($userAgent, 'curl') !== false) {
            return 'API';
        } else {
            return 'DESKTOP';
        }
    }
}

if (!function_exists('detect_browser')) {
    function detect_browser(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        if (strpos($userAgent, 'Opera') !== false) return 'Opera';
        
        return 'Unknown';
    }
}

if (!function_exists('detect_os')) {
    function detect_os(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iOS') !== false) return 'iOS';
        
        return 'Unknown';
    }
}

if (!function_exists('get_module_activity_stats')) {
    /**
     * Get activity statistics for a specific module
     */
    function get_module_activity_stats(string $module, array $filters = []): array
    {
        try {
            $db = \Config\Database::connect();
            // Query langsung ke system_activity_log - TIDAK PAKAI VIEW
            $builder = $db->table('system_activity_log');
            
            $builder->select([
                'module_name',
                'feature_name', 
                'action_type',
                'business_impact',
                'COUNT(*) as activity_count',
                'COUNT(DISTINCT user_id) as unique_users',
                'AVG(execution_time_ms) as avg_execution_time',
                'SUM(CASE WHEN compliance_relevant = 1 THEN 1 ELSE 0 END) as compliance_activities',
                'SUM(CASE WHEN is_critical = 1 THEN 1 ELSE 0 END) as critical_activities',
                'SUM(COALESCE(financial_impact, 0)) as total_financial_impact',
                'MIN(created_at) as first_activity',
                'MAX(created_at) as last_activity',
                'DATE(created_at) as activity_date'
            ]);
            
            $builder->where('module_name', strtoupper($module));
            
            if (isset($filters['date_from'])) {
                $builder->where('created_at >=', $filters['date_from']);
            }
            if (isset($filters['date_to'])) {
                $builder->where('created_at <=', $filters['date_to']);
            }
            if (isset($filters['feature'])) {
                $builder->where('feature_name', $filters['feature']);
            }
            
            // Group by untuk analytics
            $builder->groupBy(['module_name', 'feature_name', 'action_type', 'business_impact', 'DATE(created_at)']);
            $builder->orderBy('activity_count', 'DESC');
            
            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Get module stats failed: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('generate_batch_id')) {
    /**
     * Generate UUID for batch operations
     */
    function generate_batch_id(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
