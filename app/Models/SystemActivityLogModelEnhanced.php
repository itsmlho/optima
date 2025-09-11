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
        'timestamp',
        'user_id', 
        'username',
        'module_name',
        'action_type',
        'workflow_stage',
        'business_impact',
        'data_before',
        'data_after',
        'affected_records',
        'ip_address',
        'user_agent',
        'session_id',
        'notes',
        
        // ALL RELATED FIELDS
        'related_kontrak_id',
        'related_spk_id',
        'related_di_id',
        'related_tracking_id',
        'related_quotation_id',
        'related_quotation_item_id',
        'related_spk_service_id',
        'related_maintenance_id',
        'related_work_order_id',
        'related_work_order_item_id',
        'related_service_item_id',
        'related_unit_id',
        'related_delivery_process_id',
        'related_delivery_status_id',
        'related_invoice_id',
        'related_invoice_item_id',
        'related_payment_id',
        'related_po_id',
        'related_po_item_id',
        'related_po_unit_id',
        'related_po_attachment_id',
        'related_po_sparepart_id',
        'related_supplier_id',
        'related_inventory_unit_id',
        'related_inventory_attachment_id',
        'related_inventory_sparepart_id',
        'related_po_verification_id',
        'related_location_id',
        'related_silo_id',
        'related_emisi_id',
        'related_license_id',
        'related_user_id',
        'related_role_id',
        'related_permission_id',
        'related_setting_id',
        'related_config_id'
    ];

    protected $useTimestamps = false;
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // ========================================================================
    // ASSISTANT METHODS FOR EASY LOGGING
    // ========================================================================

    /**
     * Log CREATE operations with related entity tracking
     */
    public function logCreate($module, $data = [], $related = [])
    {
        return $this->logActivity('CREATE', $module, null, $data, $related);
    }

    /**
     * Log UPDATE operations with related entity tracking
     */
    public function logUpdate($module, $dataBefore = [], $dataAfter = [], $related = [])
    {
        return $this->logActivity('UPDATE', $module, $dataBefore, $dataAfter, $related);
    }

    /**
     * Log DELETE operations with related entity tracking
     */
    public function logDelete($module, $data = [], $related = [])
    {
        return $this->logActivity('DELETE', $module, $data, null, $related);
    }

    /**
     * Log VIEW operations with related entity tracking
     */
    public function logView($module, $data = [], $related = [])
    {
        return $this->logActivity('VIEW', $module, null, $data, $related);
    }

    /**
     * Central logging method with comprehensive related field support
     */
    private function logActivity($action, $module, $dataBefore = null, $dataAfter = null, $related = [])
    {
        try {
            $session = session();
            $request = service('request');

            // Base activity data
            $activityData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user_id' => $session->get('user_id') ?? 0,
                'username' => $session->get('username') ?? 'System',
                'module_name' => $module,
                'action_type' => $action,
                'data_before' => $dataBefore ? json_encode($dataBefore) : null,
                'data_after' => $dataAfter ? json_encode($dataAfter) : null,
                'affected_records' => 1,
                'ip_address' => $request->getIPAddress(),
                'user_agent' => $request->getUserAgent()->getAgentString(),
                'session_id' => $session->get('session_id') ?? session_id(),
                'workflow_stage' => $related['workflow_stage'] ?? null,
                'business_impact' => $related['business_impact'] ?? null,
                'notes' => $related['notes'] ?? null
            ];

            // Add all related fields if provided
            $relatedFields = $this->getRelatedFieldsMapping();
            foreach ($relatedFields as $field => $description) {
                if (isset($related[$field])) {
                    $activityData[$field] = $related[$field];
                }
            }

            // Use direct Query Builder for reliability
            $db = \Config\Database::connect();
            return $db->table($this->table)->insert($activityData);

        } catch (\Exception $e) {
            log_message('error', 'SystemActivityLog Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get mapping of all related fields with descriptions
     */
    public function getRelatedFieldsMapping()
    {
        return [
            // EXISTING FIELDS
            'related_kontrak_id' => 'Marketing - Kontrak/PO Rental',
            'related_spk_id' => 'Marketing - SPK (Surat Perintah Kerja)',
            'related_di_id' => 'Marketing - Delivery Instructions',
            
            // MONITORING
            'related_tracking_id' => 'Tracking Delivery/Work Orders',
            
            // MARKETING (additional)
            'related_quotation_id' => 'Buat Penawaran',
            'related_quotation_item_id' => 'Quotation Items',
            
            // SERVICE
            'related_spk_service_id' => 'SPK Service (Penyiapan Unit)',
            'related_maintenance_id' => 'Preventive Maintenance (PMPS)',
            'related_work_order_id' => 'Work Order',
            'related_work_order_item_id' => 'Work Order Items',
            'related_service_item_id' => 'Service Inventory Items',
            'related_unit_id' => 'Data Unit/Inventory Unit',
            
            // OPERATIONAL
            'related_delivery_process_id' => 'Delivery Process',
            'related_delivery_status_id' => 'Delivery Status',
            
            // ACCOUNTING
            'related_invoice_id' => 'Invoice Management',
            'related_invoice_item_id' => 'Invoice Items',
            'related_payment_id' => 'Payment Validation',
            
            // PURCHASING
            'related_po_id' => 'Purchase Order',
            'related_po_item_id' => 'PO Items',
            'related_po_unit_id' => 'PO Unit',
            'related_po_attachment_id' => 'PO Attachment & Battery',
            'related_po_sparepart_id' => 'PO Sparepart',
            'related_supplier_id' => 'Supplier',
            
            // WAREHOUSE
            'related_inventory_unit_id' => 'Inventory - Unit',
            'related_inventory_attachment_id' => 'Inventory - Attachment & Battery',
            'related_inventory_sparepart_id' => 'Inventory - Sparepart',
            'related_po_verification_id' => 'PO Verification',
            'related_location_id' => 'Warehouse Location',
            
            // PERIZINAN
            'related_silo_id' => 'SILO (Surat Izin Layak Operasi)',
            'related_emisi_id' => 'EMISI (Surat Izin Emisi Gas Buang)',
            'related_license_id' => 'License Management',
            
            // ADMINISTRATION
            'related_user_id' => 'User Management',
            'related_role_id' => 'Role Management',
            'related_permission_id' => 'Permission Management',
            'related_setting_id' => 'System Settings',
            'related_config_id' => 'Configuration'
        ];
    }

    /**
     * Assistant for MARKETING division logging
     */
    public function logMarketing($action, $data = [], $options = [])
    {
        $module = $options['module'] ?? 'MARKETING';
        $related = [];
        
        // Auto-detect related IDs
        if (isset($data['kontrak_id'])) $related['related_kontrak_id'] = $data['kontrak_id'];
        if (isset($data['spk_id'])) $related['related_spk_id'] = $data['spk_id'];
        if (isset($data['di_id'])) $related['related_di_id'] = $data['di_id'];
        if (isset($data['quotation_id'])) $related['related_quotation_id'] = $data['quotation_id'];
        
        // Merge with provided related data
        $related = array_merge($related, $options['related'] ?? []);
        
        return $this->logActivity($action, $module, 
            $options['data_before'] ?? null,
            $options['data_after'] ?? $data,
            $related
        );
    }

    /**
     * Assistant for SERVICE division logging
     */
    public function logService($action, $data = [], $options = [])
    {
        $module = $options['module'] ?? 'SERVICE';
        $related = [];
        
        // Auto-detect related IDs
        if (isset($data['spk_service_id'])) $related['related_spk_service_id'] = $data['spk_service_id'];
        if (isset($data['maintenance_id'])) $related['related_maintenance_id'] = $data['maintenance_id'];
        if (isset($data['work_order_id'])) $related['related_work_order_id'] = $data['work_order_id'];
        if (isset($data['unit_id'])) $related['related_unit_id'] = $data['unit_id'];
        
        $related = array_merge($related, $options['related'] ?? []);
        
        return $this->logActivity($action, $module,
            $options['data_before'] ?? null,
            $options['data_after'] ?? $data,
            $related
        );
    }

    /**
     * Assistant for PURCHASING division logging
     */
    public function logPurchasing($action, $data = [], $options = [])
    {
        $module = $options['module'] ?? 'PURCHASING';
        $related = [];
        
        // Auto-detect related IDs
        if (isset($data['po_id'])) $related['related_po_id'] = $data['po_id'];
        if (isset($data['supplier_id'])) $related['related_supplier_id'] = $data['supplier_id'];
        if (isset($data['po_item_id'])) $related['related_po_item_id'] = $data['po_item_id'];
        
        $related = array_merge($related, $options['related'] ?? []);
        
        return $this->logActivity($action, $module,
            $options['data_before'] ?? null,
            $options['data_after'] ?? $data,
            $related
        );
    }

    /**
     * Assistant for ACCOUNTING division logging
     */
    public function logAccounting($action, $data = [], $options = [])
    {
        $module = $options['module'] ?? 'ACCOUNTING';
        $related = [];
        
        // Auto-detect related IDs
        if (isset($data['invoice_id'])) $related['related_invoice_id'] = $data['invoice_id'];
        if (isset($data['payment_id'])) $related['related_payment_id'] = $data['payment_id'];
        
        $related = array_merge($related, $options['related'] ?? []);
        
        return $this->logActivity($action, $module,
            $options['data_before'] ?? null,
            $options['data_after'] ?? $data,
            $related
        );
    }

    /**
     * Assistant for WAREHOUSE division logging
     */
    public function logWarehouse($action, $data = [], $options = [])
    {
        $module = $options['module'] ?? 'WAREHOUSE';
        $related = [];
        
        // Auto-detect related IDs
        if (isset($data['inventory_unit_id'])) $related['related_inventory_unit_id'] = $data['inventory_unit_id'];
        if (isset($data['inventory_attachment_id'])) $related['related_inventory_attachment_id'] = $data['inventory_attachment_id'];
        if (isset($data['location_id'])) $related['related_location_id'] = $data['location_id'];
        
        $related = array_merge($related, $options['related'] ?? []);
        
        return $this->logActivity($action, $module,
            $options['data_before'] ?? null,
            $options['data_after'] ?? $data,
            $related
        );
    }

    /**
     * Assistant for PERIZINAN division logging
     */
    public function logPerizinan($action, $data = [], $options = [])
    {
        $module = $options['module'] ?? 'PERIZINAN';
        $related = [];
        
        // Auto-detect related IDs
        if (isset($data['silo_id'])) $related['related_silo_id'] = $data['silo_id'];
        if (isset($data['emisi_id'])) $related['related_emisi_id'] = $data['emisi_id'];
        if (isset($data['license_id'])) $related['related_license_id'] = $data['license_id'];
        
        $related = array_merge($related, $options['related'] ?? []);
        
        return $this->logActivity($action, $module,
            $options['data_before'] ?? null,
            $options['data_after'] ?? $data,
            $related
        );
    }

    /**
     * Assistant for ADMINISTRATION division logging
     */
    public function logAdministration($action, $data = [], $options = [])
    {
        $module = $options['module'] ?? 'ADMINISTRATION';
        $related = [];
        
        // Auto-detect related IDs
        if (isset($data['user_id'])) $related['related_user_id'] = $data['user_id'];
        if (isset($data['role_id'])) $related['related_role_id'] = $data['role_id'];
        if (isset($data['permission_id'])) $related['related_permission_id'] = $data['permission_id'];
        
        $related = array_merge($related, $options['related'] ?? []);
        
        return $this->logActivity($action, $module,
            $options['data_before'] ?? null,
            $options['data_after'] ?? $data,
            $related
        );
    }

    /**
     * Get activity logs with related entity details
     */
    public function getActivityWithRelated($filters = [])
    {
        $builder = $this->db->table($this->table);
        
        // Apply filters
        if (!empty($filters['module'])) {
            $builder->where('module_name', $filters['module']);
        }
        if (!empty($filters['action'])) {
            $builder->where('action_type', $filters['action']);
        }
        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('timestamp >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('timestamp <=', $filters['date_to']);
        }
        
        // Filter by related entity
        if (!empty($filters['related_entity'])) {
            $relatedField = 'related_' . $filters['related_entity'] . '_id';
            $builder->where($relatedField . ' IS NOT NULL');
            if (!empty($filters['related_id'])) {
                $builder->where($relatedField, $filters['related_id']);
            }
        }
        
        return $builder->orderBy('timestamp', 'DESC')
                      ->limit($filters['limit'] ?? 100)
                      ->get()
                      ->getResultArray();
    }
}
