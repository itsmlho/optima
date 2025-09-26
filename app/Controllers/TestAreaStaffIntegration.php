<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AreaModel;
use App\Models\StaffModel;
use App\Models\AreaStaffAssignmentModel;
use App\Models\CustomerModel;

class TestAreaStaffIntegration extends Controller
{
    public function index()
    {
        $html = '<h1>Area-based Staff Assignment Integration Test</h1>';
        $html .= '<p>Testing workflow: Create area → staff → assignment → work order → verify auto assignment</p>';
        
        try {
            // Initialize models
            $areaModel = new AreaModel();
            $staffModel = new StaffModel();
            $assignmentModel = new AreaStaffAssignmentModel();
            $customerModel = new CustomerModel();

            $html .= '<h2>Step 1: Create Test Area</h2>';

            // Create test area
            $testAreaData = [
                'area_code' => 'TEST-AREA-' . date('His'),
                'area_name' => 'Test Area for Integration',
                'description' => 'Test area for auto staff assignment integration'
            ];

            $areaId = $areaModel->insert($testAreaData);
            if ($areaId) {
                $html .= "✅ Test area created with ID: {$areaId}<br>";
                $html .= "Area Code: {$testAreaData['area_code']}<br>";
            } else {
                $html .= "❌ Failed to create test area<br>";
                return $html;
            }

            $html .= '<h2>Step 2: Create Test Staff (All Roles)</h2>';

            // Create staff for all roles
            $roles = ['ADMIN', 'FOREMAN', 'MECHANIC', 'HELPER'];
            $staffIds = [];

            foreach ($roles as $role) {
                $staffData = [
                    'staff_code' => 'TST-' . $role . '-' . date('His'),
                    'staff_name' => 'Test ' . ucfirst(strtolower($role)),
                    'role' => $role,
                    'phone' => '081234567' . substr(microtime(), -3),
                    'email' => strtolower($role) . '_test@example.com',
                    'is_active' => 1
                ];
                
                $staffId = $staffModel->insert($staffData);
                if ($staffId) {
                    $staffIds[$role] = $staffId;
                    $html .= "✅ {$role} staff created - ID: {$staffId}, Code: {$staffData['staff_code']}<br>";
                } else {
                    $html .= "❌ Failed to create {$role} staff<br>";
                }
            }

            $html .= '<h2>Step 3: Create PRIMARY Assignments</h2>';

            // Create PRIMARY assignments for all roles
            foreach ($roles as $role) {
                if (isset($staffIds[$role])) {
                    $assignmentData = [
                        'area_id' => $areaId,
                        'staff_id' => $staffIds[$role],
                        'assignment_type' => 'PRIMARY',
                        'start_date' => date('Y-m-d'),
                        'end_date' => null,
                        'is_active' => 1,
                        'notes' => 'Test PRIMARY assignment for ' . $role
                    ];
                    
                    $assignmentId = $assignmentModel->insert($assignmentData);
                    if ($assignmentId) {
                        $html .= "✅ PRIMARY assignment created for {$role} - Assignment ID: {$assignmentId}<br>";
                    } else {
                        $html .= "❌ Failed to create assignment for {$role}<br>";
                    }
                }
            }

            $html .= '<h2>Step 4: Create Test Customer in Area</h2>';

            // Create test customer in the area
            $customerData = [
                'customer_code' => 'TESTCUST-' . date('His'),
                'customer_name' => 'Test Customer for Integration',
                'area_id' => $areaId,
                'address' => 'Test Address',
                'phone' => '081234567890',
                'email' => 'testcustomer@example.com',
                'is_active' => 1
            ];

            $customerId = $customerModel->insert($customerData);
            if ($customerId) {
                $html .= "✅ Test customer created - ID: {$customerId}, Code: {$customerData['customer_code']}<br>";
            } else {
                $html .= "❌ Failed to create test customer<br>";
                return $html;
            }

            $html .= '<h2>Step 5: Create/Get Test Unit</h2>';

            // Create a test unit
            $db = \Config\Database::connect();
            $unitData = [
                'no_unit' => 'TEST-UNIT-' . date('His'),
                'customer_id' => $customerId,
                'tipe_unit' => 'ELECTRIC',
                'status' => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('inventory_unit')->insert($unitData);
            $unitId = $db->insertID();
            
            if ($unitId) {
                $html .= "✅ Test unit created - ID: {$unitId}, No Unit: {$unitData['no_unit']}<br>";
            } else {
                $html .= "❌ Failed to create test unit<br>";
                return $html;
            }

            $html .= '<h2>Step 6: Test Auto Staff Assignment Simulation</h2>';

            // Simulate the auto assignment logic
            $assignedStaff = $this->simulateAutoAssignStaff($unitId);
            
            $html .= '<h3>Auto Assignment Results:</h3>';
            $html .= '<table border="1" style="border-collapse: collapse; margin: 10px 0;">';
            $html .= '<tr style="background-color: #f0f0f0;"><th>Role</th><th>Assigned Staff ID</th><th>Staff Name</th></tr>';
            
            $rolesLower = ['admin', 'foreman', 'mechanic', 'helper'];
            foreach ($rolesLower as $role) {
                $staffIdField = $role . '_staff_id';
                $assignedId = $assignedStaff[$staffIdField] ?? null;
                $assignedName = $assignedStaff['assigned_staff_names'][$role] ?? 'None';
                
                $status = $assignedId ? "✅" : "❌";
                $html .= '<tr>';
                $html .= "<td>{$status} " . strtoupper($role) . "</td>";
                $html .= "<td>{$assignedId}</td>";
                $html .= "<td>{$assignedName}</td>";
                $html .= '</tr>';
            }
            $html .= '</table>';
            
            // Verify assignments match our created staff
            $allAssigned = true;
            foreach ($rolesLower as $role) {
                $staffIdField = $role . '_staff_id';
                $expectedId = $staffIds[strtoupper($role)] ?? null;
                $actualId = $assignedStaff[$staffIdField] ?? null;
                
                if ($expectedId && $actualId == $expectedId) {
                    $html .= "✅ {$role} assignment correct: Expected {$expectedId}, Got {$actualId}<br>";
                } else {
                    $html .= "❌ {$role} assignment mismatch: Expected {$expectedId}, Got {$actualId}<br>";
                    $allAssigned = false;
                }
            }
            
            if ($allAssigned) {
                $html .= '<h2>🎉 INTEGRATION TEST PASSED!</h2>';
                $html .= '<p style="color: green; font-weight: bold;">All staff were correctly auto-assigned based on area assignments.</p>';
            } else {
                $html .= '<h2>⚠️ INTEGRATION TEST ISSUES</h2>';
                $html .= '<p style="color: orange; font-weight: bold;">Some staff assignments didn\'t match expectations.</p>';
            }

            $html .= '<h2>Step 7: Test Data Created</h2>';
            $html .= '<p>You can now create a Work Order using Unit ID: <strong>' . $unitId . '</strong> (No Unit: ' . $unitData['no_unit'] . ') to verify auto staff assignment in real Work Order creation.</p>';
            $html .= '<p><a href="' . base_url('work-orders/create') . '" target="_blank">➤ Create Work Order</a></p>';
            
            $html .= '<p>Test data created:</p>';
            $html .= '<ul>';
            $html .= "<li>Area ID: {$areaId}</li>";
            $html .= "<li>Customer ID: {$customerId}</li>";
            $html .= "<li>Unit ID: {$unitId}</li>";
            foreach ($staffIds as $role => $id) {
                $html .= "<li>{$role} Staff ID: {$id}</li>";
            }
            $html .= '</ul>';
            
        } catch (\Exception $e) {
            $html .= '<h2>❌ INTEGRATION TEST FAILED</h2>';
            $html .= '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
            $html .= '<p>Stack trace: ' . $e->getTraceAsString() . '</p>';
        }
        
        $html .= '<hr>';
        $html .= '<p>Integration test completed at: ' . date('Y-m-d H:i:s') . '</p>';
        
        return $html;
    }
    
    private function simulateAutoAssignStaff($unitId)
    {
        try {
            $assignedStaff = [
                'admin_staff_id' => null,
                'foreman_staff_id' => null,
                'mechanic_staff_id' => null,
                'helper_staff_id' => null,
                'assigned_staff_names' => []
            ];
            
            // Use AreaStaffAssignmentModel to get staff
            $assignmentModel = new AreaStaffAssignmentModel();
            $roles = ['ADMIN', 'FOREMAN', 'MECHANIC', 'HELPER'];
            
            foreach ($roles as $role) {
                $staff = $assignmentModel->getStaffForUnit($unitId, $role);
                
                if (!empty($staff)) {
                    // Get first PRIMARY assignment, or any if no PRIMARY found
                    $selectedStaff = null;
                    foreach ($staff as $person) {
                        if ($person['assignment_type'] === 'PRIMARY') {
                            $selectedStaff = $person;
                            break;
                        }
                    }
                    
                    // If no PRIMARY found, use first available
                    if (!$selectedStaff && !empty($staff)) {
                        $selectedStaff = $staff[0];
                    }
                    
                    if ($selectedStaff) {
                        $staffIdField = strtolower($role) . '_staff_id';
                        $assignedStaff[$staffIdField] = $selectedStaff['staff_id'];
                        $assignedStaff['assigned_staff_names'][strtolower($role)] = $selectedStaff['staff_name'];
                    }
                }
            }
            
            return $assignedStaff;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in simulateAutoAssignStaff: ' . $e->getMessage());
            return [
                'admin_staff_id' => null,
                'foreman_staff_id' => null,
                'mechanic_staff_id' => null,
                'helper_staff_id' => null,
                'assigned_staff_names' => []
            ];
        }
    }
}