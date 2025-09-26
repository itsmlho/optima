<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AreaModel;
use App\Models\StaffModel;
use App\Models\AreaStaffAssignmentModel;
use App\Models\CustomerModel;

class ServiceAreaManagementController extends Controller
{
    protected $areaModel;
    protected $staffModel;
    protected $assignmentModel;
    protected $customerModel;
    
    public function __construct()
    {
        $this->areaModel = new AreaModel();
        $this->staffModel = new StaffModel();
        $this->assignmentModel = new AreaStaffAssignmentModel();
        $this->customerModel = new CustomerModel();
    }

    /**
     * Display service area management dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Service Area & Employee Management',
            'areas' => $this->areaModel->findAll(),
            'totalAreas' => $this->areaModel->countAllResults(),
            'totalEmployees' => $this->staffModel->where('is_active', 1)->countAllResults(),
            'totalAssignments' => $this->assignmentModel->where('is_active', 1)->countAllResults(),
            'employeesByRole' => $this->getEmployeesByRoleStats(),
            'assignmentsByArea' => $this->getAssignmentsByAreaStats()
        ];
        
        return view('service/area_employee_management', $data);
    }

    // Area Management Methods

    /**
     * Get areas with pagination and search
     */
    public function getAreas()
    {
        $request = $this->request;
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $orderColumnIndex = $request->getPost('order')[0]['column'];
        $orderDir = $request->getPost('order')[0]['dir'];
        
        // Define columns for ordering
        $columns = ['area_code', 'area_name', 'description', 'customers_count', 'employees_count', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'area_name';
        
        // Get total records
        $totalRecords = $this->areaModel->countAllResults();
        
        // Build query with search and additional data
        $builder = $this->areaModel->builder();
        $builder->select('areas.*');
                
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('area_code', $searchValue)
                    ->orLike('area_name', $searchValue)
                    ->orLike('description', $searchValue)
                    ->groupEnd();
        }
        
        $filteredRecords = $builder->countAllResults(false);
        
        // Apply ordering and pagination
        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);
        
        $areas = $builder->get()->getResultArray();
        
        // Add statistics and action buttons
        foreach ($areas as &$area) {
            // Get customer count
            $area['customers_count'] = $this->customerModel->where('area_id', $area['id'])->countAllResults();
            
            // Get employee count
            $area['employees_count'] = $this->assignmentModel->where('area_id', $area['id'])
                                                             ->where('is_active', 1)
                                                             ->countAllResults();
            
            // Get assignment summary
            $assignmentStats = $this->getAreaAssignmentSummary($area['id']);
            $area['assignment_summary'] = $assignmentStats;
            
            $area['actions'] = '
                <button class="btn btn-info btn-sm" onclick="viewArea(' . $area['id'] . ')">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn btn-warning btn-sm" onclick="editArea(' . $area['id'] . ')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-primary btn-sm" onclick="manageEmployees(' . $area['id'] . ')">
                    <i class="fas fa-users"></i> Employees
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteArea(' . $area['id'] . ')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            ';
        }
        
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $areas
        ];
        
        return $this->response->setJSON($response);
    }

    /**
     * Show area details
     */
    public function showArea($id)
    {
        $area = $this->areaModel->find($id);
        if (!$area) {
            return $this->response->setJSON(['success' => false, 'message' => 'Area not found']);
        }
        
        // Get area with additional data
        $customers = $this->customerModel->findByArea($id);
        $assignments = $this->assignmentModel->getAssignmentsByArea($id);
        $assignmentStats = $this->getAreaAssignmentSummary($id);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'area' => $area,
                'customers' => $customers,
                'assignments' => $assignments,
                'stats' => $assignmentStats
            ]
        ]);
    }

    /**
     * Create new area
     */
    public function storeArea()
    {
        $rules = [
            'area_code' => 'required|is_unique[areas.area_code]|max_length[10]',
            'area_name' => 'required|max_length[255]',
            'description' => 'permit_empty|max_length[500]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'area_code' => strtoupper($this->request->getPost('area_code')),
            'area_name' => $this->request->getPost('area_name'),
            'description' => $this->request->getPost('description')
        ];
        
        try {
            $areaId = $this->areaModel->insert($data);
            
            if ($areaId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Area created successfully',
                    'data' => ['id' => $areaId]
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating area: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create area'
        ]);
    }

    /**
     * Update area
     */
    public function updateArea($id)
    {
        $area = $this->areaModel->find($id);
        if (!$area) {
            return $this->response->setJSON(['success' => false, 'message' => 'Area not found']);
        }
        
        $rules = [
            'area_code' => "required|max_length[10]|is_unique[areas.area_code,id,$id]",
            'area_name' => 'required|max_length[255]',
            'description' => 'permit_empty|max_length[500]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'area_code' => strtoupper($this->request->getPost('area_code')),
            'area_name' => $this->request->getPost('area_name'),
            'description' => $this->request->getPost('description')
        ];
        
        try {
            $updated = $this->areaModel->update($id, $data);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Area updated successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating area: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update area'
        ]);
    }

    /**
     * Delete area
     */
    public function deleteArea($id)
    {
        try {
            // Check if area has customers
            $customersCount = $this->customerModel->where('area_id', $id)->countAllResults();
            
            if ($customersCount > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete area with assigned customers. Please reassign customers first.'
                ]);
            }
            
            // Delete area assignments first
            $this->assignmentModel->where('area_id', $id)->delete();
            
            // Delete area
            $deleted = $this->areaModel->delete($id);
            
            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Area deleted successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting area: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete area'
        ]);
    }

    // Employee Management Methods

    /**
     * Get employees with pagination and search
     */
    public function getEmployees()
    {
        $request = $this->request;
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $orderColumnIndex = $request->getPost('order')[0]['column'];
        $orderDir = $request->getPost('order')[0]['dir'];
        
        // Define columns for ordering
        $columns = ['staff_code', 'staff_name', 'role', 'phone', 'email', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'staff_name';
        
        // Get total records (only active employees)
        $totalRecords = $this->staffModel->where('is_active', 1)->countAllResults();
        
        // Build query with search
        $builder = $this->staffModel->builder();
        $builder->select('staff.*')
                ->where('staff.is_active', 1); // Only show active employees
                
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('staff_code', $searchValue)
                    ->orLike('staff_name', $searchValue)
                    ->orLike('role', $searchValue)
                    ->orLike('phone', $searchValue)
                    ->orLike('email', $searchValue)
                    ->groupEnd();
        }
        
        $filteredRecords = $builder->countAllResults(false);
        
        // Apply ordering and pagination
        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);
        
        $employees = $builder->get()->getResultArray();
        
        // Add assignment info and action buttons
        foreach ($employees as &$employee) {
            // Get current assignments
            $assignments = $this->assignmentModel->getAssignmentsByStaff($employee['id']);
            $employee['assignments'] = $assignments;
            $employee['areas_count'] = count($assignments);
            
            $employee['actions'] = '
                <button class="btn btn-info btn-sm" onclick="viewEmployee(' . $employee['id'] . ')">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn btn-warning btn-sm" onclick="editEmployee(' . $employee['id'] . ')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-primary btn-sm" onclick="manageAssignments(' . $employee['id'] . ')">
                    <i class="fas fa-map-marker-alt"></i> Areas
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteEmployee(' . $employee['id'] . ')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            ';
        }
        
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $employees
        ];
        
        return $this->response->setJSON($response);
    }

    /**
     * Show employee details
     */
    public function showEmployee($id)
    {
        $employee = $this->staffModel->find($id);
        if (!$employee) {
            return $this->response->setJSON(['success' => false, 'message' => 'Employee not found']);
        }
        
        // Get employee assignments
        $assignments = $this->assignmentModel->getAssignmentsByStaff($id);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'employee' => $employee,
                'assignments' => $assignments
            ]
        ]);
    }

    /**
     * Create new employee
     */
    public function storeEmployee()
    {
        $rules = [
            'staff_code' => 'required|is_unique[staff.staff_code]|max_length[20]',
            'staff_name' => 'required|max_length[255]',
            'role' => 'required|in_list[ADMIN,FOREMAN,MECHANIC,HELPER,SUPERVISOR]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[100]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'staff_code' => strtoupper($this->request->getPost('staff_code')),
            'staff_name' => $this->request->getPost('staff_name'),
            'role' => strtoupper($this->request->getPost('role')),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'description' => $this->request->getPost('description')
        ];
        
        try {
            $employeeId = $this->staffModel->insert($data);
            
            if ($employeeId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Employee created successfully',
                    'data' => ['id' => $employeeId]
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating employee: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create employee'
        ]);
    }

    /**
     * Update employee
     */
    public function updateEmployee($id)
    {
        $employee = $this->staffModel->find($id);
        if (!$employee) {
            return $this->response->setJSON(['success' => false, 'message' => 'Employee not found']);
        }
        
        $rules = [
            'staff_code' => "required|max_length[20]|is_unique[staff.staff_code,id,$id]",
            'staff_name' => 'required|max_length[255]',
            'role' => 'required|in_list[ADMIN,FOREMAN,MECHANIC,HELPER,SUPERVISOR]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[100]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'staff_code' => strtoupper($this->request->getPost('staff_code')),
            'staff_name' => $this->request->getPost('staff_name'),
            'role' => strtoupper($this->request->getPost('role')),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'description' => $this->request->getPost('description')
        ];
        
        try {
            $updated = $this->staffModel->update($id, $data);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Employee updated successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating employee: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update employee'
        ]);
    }

    /**
     * Delete employee (soft delete - set is_active = 0)
     */
    public function deleteEmployee($id)
    {
        try {
            // Set employee as inactive instead of hard delete
            $updated = $this->staffModel->update($id, ['is_active' => 0]);
            
            if ($updated) {
                // Also deactivate all assignments for this employee
                $this->assignmentModel->where('staff_id', $id)->set(['is_active' => 0])->update();
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Employee deactivated successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deactivating employee: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to deactivate employee'
        ]);
    }

    // Assignment Management Methods

    /**
     * Get area assignments for specific area
     */
    public function getAreaAssignments($areaId)
    {
        $assignments = $this->assignmentModel->getAssignmentsByArea($areaId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * Get employee assignments for specific employee
     */
    public function getEmployeeAssignments($employeeId)
    {
        $assignments = $this->assignmentModel->getAssignmentsByStaff($employeeId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * Store area-employee assignment
     */
    public function storeAssignment()
    {
        $rules = [
            'area_id' => 'required|integer',
            'staff_id' => 'required|integer',
            'assignment_type' => 'required|in_list[PRIMARY,BACKUP,TEMPORARY]',
            'start_date' => 'required|valid_date[Y-m-d]',
            'end_date' => 'permit_empty|valid_date[Y-m-d]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $areaId = $this->request->getPost('area_id');
        $staffId = $this->request->getPost('staff_id');
        $assignmentType = $this->request->getPost('assignment_type');
        $role = $this->request->getPost('role');
        
        // Check if assignment already exists
        $existingAssignment = $this->assignmentModel->where('area_id', $areaId)
                                                   ->where('staff_id', $staffId)
                                                   ->where('is_active', 1)
                                                   ->first();
        
        if ($existingAssignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Employee is already assigned to this area'
            ]);
        }
        
        // If this is a PRIMARY assignment, check if there's already a primary for this role in this area
        if ($assignmentType === 'PRIMARY' && $role) {
            $existingPrimary = $this->assignmentModel->builder()
                                                    ->join('staff', 'area_staff_assignments.staff_id = staff.id')
                                                    ->where('area_staff_assignments.area_id', $areaId)
                                                    ->where('staff.role', $role)
                                                    ->where('area_staff_assignments.assignment_type', 'PRIMARY')
                                                    ->where('area_staff_assignments.is_active', 1)
                                                    ->get()
                                                    ->getFirstRow();
            
            if ($existingPrimary) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "There is already a PRIMARY {$role} assigned to this area"
                ]);
            }
        }
        
        $data = [
            'area_id' => $areaId,
            'staff_id' => $staffId,
            'assignment_type' => $assignmentType,
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'is_active' => 1,
            'notes' => $this->request->getPost('notes')
        ];
        
        try {
            $assignmentId = $this->assignmentModel->insert($data);
            
            if ($assignmentId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment created successfully',
                    'data' => ['id' => $assignmentId]
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating assignment: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create assignment'
        ]);
    }

    /**
     * Update assignment
     */
    public function updateAssignment($id)
    {
        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }
        
        $rules = [
            'assignment_type' => 'required|in_list[PRIMARY,BACKUP,TEMPORARY]',
            'start_date' => 'required|valid_date[Y-m-d]',
            'end_date' => 'permit_empty|valid_date[Y-m-d]',
            'is_active' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'assignment_type' => $this->request->getPost('assignment_type'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'notes' => $this->request->getPost('notes')
        ];
        
        try {
            $updated = $this->assignmentModel->update($id, $data);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment updated successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating assignment: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update assignment'
        ]);
    }

    /**
     * Delete assignment (soft delete - set is_active = 0)
     */
    public function deleteAssignment($id)
    {
        try {
            // Set assignment as inactive instead of hard delete
            $updated = $this->assignmentModel->update($id, ['is_active' => 0]);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment deactivated successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deactivating assignment: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to deactivate assignment'
        ]);
    }

    /**
     * Show single assignment detail
     */
    public function showAssignment($id)
    {
        $assignment = $this->assignmentModel->select('area_staff_assignments.*, staff.staff_name, staff.staff_role as role, areas.area_name, areas.area_code')
                                            ->join('staff', 'staff.id = area_staff_assignments.staff_id')
                                            ->join('areas', 'areas.id = area_staff_assignments.area_id')
                                            ->where('area_staff_assignments.id', $id)
                                            ->first();
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }
        return $this->response->setJSON(['success' => true, 'data' => $assignment]);
    }

    // Utility Methods

    /**
     * Get employees by role statistics
     */
    private function getEmployeesByRoleStats()
    {
        $builder = $this->staffModel->builder();
        $builder->select('role, COUNT(id) as employee_count')
                ->where('is_active', 1) // Only count active employees
                ->groupBy('role')
                ->orderBy('employee_count', 'DESC');
                
        return $builder->get()->getResultArray();
    }

    /**
     * Get assignments by area statistics
     */
    private function getAssignmentsByAreaStats()
    {
        $builder = $this->assignmentModel->builder();
        $builder->select('areas.area_name, COUNT(area_staff_assignments.id) as assignment_count')
                ->join('areas', 'area_staff_assignments.area_id = areas.id', 'left')
                ->where('area_staff_assignments.is_active', 1)
                ->groupBy('areas.id, areas.area_name')
                ->orderBy('assignment_count', 'DESC');
                
        return $builder->get()->getResultArray();
    }

    /**
     * Get assignment summary for specific area
     */
    private function getAreaAssignmentSummary($areaId)
    {
        $builder = $this->assignmentModel->builder();
        $builder->select('staff.role, COUNT(area_staff_assignments.id) as count, 
                         SUM(CASE WHEN area_staff_assignments.assignment_type = "PRIMARY" THEN 1 ELSE 0 END) as primary_count')
                ->join('staff', 'area_staff_assignments.staff_id = staff.id')
                ->where('area_staff_assignments.area_id', $areaId)
                ->where('area_staff_assignments.is_active', 1)
                ->groupBy('staff.role');
                
        return $builder->get()->getResultArray();
    }

    /**
     * Get available employees for assignment
     */
    public function getAvailableEmployees($areaId = null, $role = null)
    {
        $builder = $this->staffModel->builder();
        $builder->select('staff.*')
                ->where('staff.is_active', 1); // Only show active employees
        
        if ($role) {
            $builder->where('staff.role', $role);
        }
        
        // Exclude employees already assigned to this area
        if ($areaId) {
            $builder->whereNotIn('staff.id', function($builder) use ($areaId) {
                return $builder->select('staff_id')
                              ->from('area_staff_assignments')
                              ->where('area_id', $areaId)
                              ->where('is_active', 1);
            });
        }
        
        $employees = $builder->orderBy('staff_name', 'ASC')->get()->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $employees
        ]);
    }
}