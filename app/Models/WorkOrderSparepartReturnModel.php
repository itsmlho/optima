<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\WorkOrderAssignmentModel;

class WorkOrderSparepartReturnModel extends Model
{
    protected $table = 'work_order_sparepart_returns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'work_order_id',
        'work_order_sparepart_id',
        'sparepart_code',
        'sparepart_name',
        'quantity_brought',
        'quantity_used',
        'quantity_return',
        'satuan',
        'status',
        'return_notes',
        'confirmed_by',
        'confirmed_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'work_order_id' => 'required|integer',
        'work_order_sparepart_id' => 'required|integer',
        'sparepart_code' => 'required|max_length[50]',
        'sparepart_name' => 'required|max_length[255]',
        'quantity_brought' => 'required|integer|greater_than[0]',
        'quantity_used' => 'required|integer|greater_than_equal_to[0]',
        'quantity_return' => 'required|integer|greater_than[0]',
        'satuan' => 'required|max_length[50]',
        'status' => 'permit_empty|in_list[PENDING,CONFIRMED,CANCELLED]'
    ];

    protected $validationMessages = [
        'work_order_id' => [
            'required' => 'Work Order ID harus diisi',
            'integer' => 'Work Order ID harus berupa angka'
        ],
        'quantity_return' => [
            'required' => 'Quantity return harus diisi',
            'integer' => 'Quantity return harus berupa angka',
            'greater_than' => 'Quantity return harus lebih dari 0'
        ]
    ];

    /**
     * Create return record for sparepart
     */
    public function createReturn($workOrderId, $sparepartData)
    {
        try {
            // Calculate return quantity
            $quantityBrought = (int)($sparepartData['quantity_brought'] ?? 0);
            $quantityUsed = (int)($sparepartData['quantity_used'] ?? 0);
            $quantityReturn = $quantityBrought - $quantityUsed;

            // Only create return if there's a return quantity
            if ($quantityReturn <= 0) {
                return null;
            }

            $data = [
                'work_order_id' => $workOrderId,
                'work_order_sparepart_id' => $sparepartData['id'] ?? null,
                'sparepart_code' => $sparepartData['sparepart_code'] ?? '',
                'sparepart_name' => $sparepartData['sparepart_name'] ?? '',
                'quantity_brought' => $quantityBrought,
                'quantity_used' => $quantityUsed,
                'quantity_return' => $quantityReturn,
                'satuan' => $sparepartData['satuan'] ?? 'PCS',
                'status' => 'PENDING',
                'return_notes' => $sparepartData['return_notes'] ?? null
            ];

            return $this->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Error creating sparepart return: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending returns
     */
    public function getPendingReturns($filters = [])
    {
        $builder = $this->select('
            wosr.*,
            wo.work_order_number,
            wo.report_date,
            wo.status_id,
            wos.status_name as wo_status,
            c.customer_name,
            iu.no_unit as unit_number
        ')
        ->join('work_orders wo', 'wo.id = wosr.work_order_id', 'left')
        ->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left')
        ->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left')
        ->join('kontrak k', 'k.id = iu.kontrak_id', 'left')
        ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
        ->join('customers c', 'c.id = cl.customer_id', 'left')
        ->where('wosr.status', 'PENDING');

        // Apply filters
        if (!empty($filters['work_order_id'])) {
            $builder->where('wosr.work_order_id', $filters['work_order_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('wosr.created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('wosr.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->orderBy('wosr.created_at', 'DESC')->findAll();
    }

    /**
     * Get return by ID with details
     */
    public function getReturnDetail($id)
    {
        $db = \Config\Database::connect();
        
        $return = $db->table('work_order_sparepart_returns')
            ->select('
                work_order_sparepart_returns.*,
                wo.work_order_number,
                wo.report_date,
                wo.id as work_order_id,
                wo.mechanic_id,
                wo.helper_id,
                ms.staff_name as mechanic_name,
                hs.staff_name as helper_name,
                wo.status_id,
                wos.status_name as wo_status,
                wos.status_color as wo_status_color,
                c.customer_name,
                cl.location_name,
                iu.no_unit as unit_number,
                mu.merk_unit,
                mu.model_unit,
                u.username as confirmed_by_name
            ')
            ->join('work_orders wo', 'wo.id = work_order_sparepart_returns.work_order_id', 'left')
            ->join('work_order_staff_backup_final ms', 'ms.id = wo.mechanic_id', 'left')
            ->join('work_order_staff_backup_final hs', 'hs.id = wo.helper_id', 'left')
            ->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = wo.unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('kontrak k', 'k.id = iu.kontrak_id', 'left')
            ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
            ->join('customers c', 'c.id = cl.customer_id', 'left')
            ->join('users u', 'u.id = work_order_sparepart_returns.confirmed_by', 'left')
            ->where('work_order_sparepart_returns.id', $id)
            ->get()
            ->getRowArray();
        
        if (!$return) {
            return null;
        }
        
        // Get mechanics and helpers for this work order - menggunakan pendekatan sederhana
        $mechanicHelperNames = [];
        
        // Dari work_orders (mechanic_id dan helper_id)
        if (!empty($return['mechanic_name'])) {
            $mechanicHelperNames[] = $return['mechanic_name'] . ' (MECHANIC)';
        }
        if (!empty($return['helper_name'])) {
            $mechanicHelperNames[] = $return['helper_name'] . ' (HELPER)';
        }
        
        // Dari work_order_assignments
        if (!empty($return['work_order_id'])) {
            $assignmentModel = new WorkOrderAssignmentModel();
            
            // Get all assignments for this work order
            $assignments = $assignmentModel
                ->where('work_order_id', $return['work_order_id'])
                ->where('is_active', 1)
                ->orderBy('role', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();
            
            if (!empty($assignments)) {
                // Get all unique staff_ids
                $staffIds = array_unique(array_filter(array_column($assignments, 'staff_id')));
                
                // Get staff names in one query
                $staffMap = [];
                if (!empty($staffIds)) {
                    $staffData = $db->table('work_order_staff_backup_final')
                        ->whereIn('id', $staffIds)
                        ->get()
                        ->getResultArray();
                    
                    foreach ($staffData as $staff) {
                        $staffMap[$staff['id']] = $staff['staff_name'];
                    }
                }
                
                // Format names
                foreach ($assignments as $assignment) {
                    $staffName = $staffMap[$assignment['staff_id']] ?? null;
                    if ($staffName) {
                        $roleLabel = $assignment['role'] === 'MECHANIC' ? 'MECHANIC' : 'HELPER';
                        $mechanicHelperNames[] = $staffName . ' (' . $roleLabel . ')';
                    }
                }
            }
        }
        
        // Remove duplicates and format
        $return['mechanic_name'] = !empty($mechanicHelperNames) ? implode(', ', array_unique($mechanicHelperNames)) : '-';
        
        return $return;
    }

    /**
     * Confirm return
     */
    public function confirmReturn($id, $userId, $notes = null)
    {
        try {
            $data = [
                'status' => 'CONFIRMED',
                'confirmed_by' => $userId,
                'confirmed_at' => date('Y-m-d H:i:s'),
                'return_notes' => $notes
            ];

            return $this->update($id, $data);
        } catch (\Exception $e) {
            log_message('error', 'Error confirming sparepart return: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get returns by work order
     */
    public function getReturnsByWorkOrder($workOrderId)
    {
        return $this->where('work_order_id', $workOrderId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        try {
            // Check if table exists
            if (!$this->db->tableExists($this->table)) {
                log_message('warning', 'Table work_order_sparepart_returns does not exist yet');
                return [
                    'pending' => 0,
                    'confirmed' => 0,
                    'total' => 0,
                    'pending_quantity' => 0
                ];
            }

            $db = \Config\Database::connect();
            
            $stats = [
                'pending' => $this->where('status', 'PENDING')->countAllResults(false),
                'confirmed' => $this->where('status', 'CONFIRMED')->countAllResults(false),
                'total' => $this->countAllResults(false)
            ];

            // Get total quantity pending return
            $pendingQty = $db->table($this->table)
                ->selectSum('quantity_return')
                ->where('status', 'PENDING')
                ->get()
                ->getRowArray();
            
            $stats['pending_quantity'] = (int)($pendingQty['quantity_return'] ?? 0);

            return $stats;
        } catch (\Exception $e) {
            log_message('error', 'Error getting statistics: ' . $e->getMessage());
            return [
                'pending' => 0,
                'confirmed' => 0,
                'total' => 0,
                'pending_quantity' => 0
            ];
        }
    }
}

