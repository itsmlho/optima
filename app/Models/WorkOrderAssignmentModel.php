<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderAssignmentModel extends Model
{
    protected $table = 'work_order_assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'work_order_id',
        'staff_id',
        'role',
        'assignment_order',
        'assigned_date',
        'assigned_by',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'work_order_id' => 'required|integer',
        'staff_id' => 'required|integer',
        'role' => 'required|in_list[MECHANIC,HELPER]',
        'assignment_order' => 'required|integer|greater_than[0]|less_than[3]'
    ];

    protected $validationMessages = [
        'work_order_id' => [
            'required' => 'Work Order ID harus diisi',
            'integer' => 'Work Order ID harus berupa angka'
        ],
        'staff_id' => [
            'required' => 'Staff ID harus diisi',
            'integer' => 'Staff ID harus berupa angka'
        ],
        'role' => [
            'required' => 'Role harus diisi',
            'in_list' => 'Role harus MECHANIC atau HELPER'
        ],
        'assignment_order' => [
            'required' => 'Assignment order harus diisi',
            'integer' => 'Assignment order harus berupa angka',
            'greater_than' => 'Assignment order harus lebih dari 0',
            'less_than' => 'Assignment order maksimal 2'
        ]
    ];

    /**
     * Assign multiple staff to work order
     */
    public function assignStaff($workOrderId, $assignments, $assignedBy = null)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Remove existing assignments for this work order
            $this->where('work_order_id', $workOrderId)->delete();

            // Insert new assignments
            $insertData = [];
            foreach ($assignments as $assignment) {
                $insertData[] = [
                    'work_order_id' => $workOrderId,
                    'staff_id' => $assignment['staff_id'],
                    'role' => $assignment['role'],
                    'assignment_order' => $assignment['assignment_order'] ?? 1,
                    'assigned_date' => date('Y-m-d H:i:s'),
                    'assigned_by' => $assignedBy,
                    'is_active' => 1
                ];
            }

            if (!empty($insertData)) {
                $this->insertBatch($insertData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            log_message('error', 'Error in WorkOrderAssignmentModel::assignStaff - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get staff assignments for work order
     */
    public function getWorkOrderAssignments($workOrderId)
    {
        // Query assignments tanpa JOIN
        $assignments = $this->where('work_order_id', $workOrderId)
            ->where('is_active', 1)
            ->orderBy('role', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
        
        // Get staff names separately
        if (!empty($assignments)) {
            $staffIds = array_unique(array_column($assignments, 'staff_id'));
            $staffData = $this->db->table('employees')
                ->whereIn('id', $staffIds)
                ->get()
                ->getResultArray();
            
            // Map staff data by id
            $staffMap = [];
            foreach ($staffData as $staff) {
                $staffMap[$staff['id']] = $staff;
            }
            
            // Merge staff names into assignments
            foreach ($assignments as &$assignment) {
                $assignment['staff_name'] = $staffMap[$assignment['staff_id']]['staff_name'] ?? null;
                $assignment['staff_role'] = $staffMap[$assignment['staff_id']]['staff_role'] ?? null;
            }
        }
        
        return $assignments;
    }

    /**
     * Get mechanics for work order
     */
    public function getMechanics($workOrderId)
    {
        // Query mechanics tanpa JOIN
        $mechanics = $this->where('work_order_id', $workOrderId)
            ->where('role', 'MECHANIC')
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->findAll();
        
        // Get staff names separately
        if (!empty($mechanics)) {
            $staffIds = array_unique(array_column($mechanics, 'staff_id'));
            $staffData = $this->db->table('employees')
                ->whereIn('id', $staffIds)
                ->get()
                ->getResultArray();
            
            // Map staff data by id
            $staffMap = [];
            foreach ($staffData as $staff) {
                $staffMap[$staff['id']] = $staff;
            }
            
            // Merge staff names into mechanics
            foreach ($mechanics as &$mechanic) {
                $mechanic['staff_name'] = $staffMap[$mechanic['staff_id']]['staff_name'] ?? null;
                $mechanic['staff_role'] = $staffMap[$mechanic['staff_id']]['staff_role'] ?? null;
            }
        }
        
        return $mechanics;
    }

    /**
     * Get helpers for work order
     */
    public function getHelpers($workOrderId)
    {
        // Query helpers tanpa JOIN
        $helpers = $this->where('work_order_id', $workOrderId)
            ->where('role', 'HELPER')
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->findAll();
        
        // Get staff names separately
        if (!empty($helpers)) {
            $staffIds = array_unique(array_column($helpers, 'staff_id'));
            $staffData = $this->db->table('employees')
                ->whereIn('id', $staffIds)
                ->get()
                ->getResultArray();
            
            // Map staff data by id
            $staffMap = [];
            foreach ($staffData as $staff) {
                $staffMap[$staff['id']] = $staff;
            }
            
            // Merge staff names into helpers
            foreach ($helpers as &$helper) {
                $helper['staff_name'] = $staffMap[$helper['staff_id']]['staff_name'] ?? null;
                $helper['staff_role'] = $staffMap[$helper['staff_id']]['staff_role'] ?? null;
            }
        }
        
        return $helpers;
    }

    /**
     * Remove staff assignment
     */
    public function removeAssignment($workOrderId, $staffId, $role)
    {
        return $this->where('work_order_id', $workOrderId)
                   ->where('staff_id', $staffId)
                   ->where('role', $role)
                   ->set('is_active', 0)
                   ->update();
    }

    /**
     * Check if staff is already assigned to work order
     */
    public function isStaffAssigned($workOrderId, $staffId, $role)
    {
        $assignment = $this->where('work_order_id', $workOrderId)
                          ->where('staff_id', $staffId)
                          ->where('role', $role)
                          ->where('is_active', 1)
                          ->first();

        return $assignment !== null;
    }
}