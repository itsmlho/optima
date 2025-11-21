<?php

namespace App\Controllers;

class DatabaseCheckController extends BaseController
{
    public function checkWorkOrder15073()
    {
        try {
            $db = \Config\Database::connect();
            
            echo "<h2>WORK ORDER 15073 CURRENT DATA</h2>";
            $query = "SELECT * FROM work_orders WHERE work_order_number = '15073'";
            $result = $db->query($query);
            
            if ($result) {
                $workOrder = $result->getRowArray();
                if ($workOrder) {
                    echo "<table border='1'>";
                    echo "<tr><th>Field</th><th>Value</th></tr>";
                    foreach ($workOrder as $key => $value) {
                        echo "<tr><td>$key</td><td>$value</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Work order 15073 not found";
                }
            } else {
                echo "Error querying work order: " . $db->error()['message'];
            }
            
            echo "<h2>STATUS MAPPING</h2>";
            $query = "SELECT * FROM work_order_statuses ORDER BY id";
            $result = $db->query($query);
            
            if ($result) {
                $statuses = $result->getResultArray();
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Status Name</th><th>Status Code</th></tr>";
                foreach ($statuses as $status) {
                    echo "<tr>";
                    echo "<td>{$status['id']}</td>";
                    echo "<td>{$status['status_name']}</td>";
                    echo "<td>{$status['status_code']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Error querying statuses: " . $db->error()['message'];
            }
            
            echo "<h2>RECENT WORK ORDERS</h2>";
            $query = "SELECT id, work_order_number, status_id, mechanic_id, helper_id, updated_at FROM work_orders ORDER BY id DESC LIMIT 5";
            $result = $db->query($query);
            
            if ($result) {
                $recent = $result->getResultArray();
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>WO Number</th><th>Status ID</th><th>Mechanic ID</th><th>Helper ID</th><th>Updated At</th></tr>";
                foreach ($recent as $wo) {
                    echo "<tr>";
                    echo "<td>{$wo['id']}</td>";
                    echo "<td>{$wo['work_order_number']}</td>";
                    echo "<td>{$wo['status_id']}</td>";
                    echo "<td>{$wo['mechanic_id']}</td>";
                    echo "<td>{$wo['helper_id']}</td>";
                    echo "<td>{$wo['updated_at']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Error querying recent work orders: " . $db->error()['message'];
            }
            
            echo "<h2>TEST UPDATE WORK ORDER 15073</h2>";
            echo "<p>Testing direct update...</p>";
            
            // Test update
            $updateData = [
                'status_id' => 2,
                'mechanic_id' => 1,
                'helper_id' => 2,
                'notes' => 'Test assignment via direct update'
            ];
            
            $updateResult = $db->table('work_orders')
                ->where('work_order_number', '15073')
                ->update($updateData);
            
            if ($updateResult) {
                echo "<p style='color: green;'>✅ Update successful!</p>";
                
                // Check updated data
                $query = "SELECT id, work_order_number, status_id, mechanic_id, helper_id, notes, updated_at FROM work_orders WHERE work_order_number = '15073'";
                $result = $db->query($query);
                
                if ($result) {
                    $updated = $result->getRowArray();
                    echo "<h3>Updated Data:</h3>";
                    echo "<table border='1'>";
                    echo "<tr><th>Field</th><th>Value</th></tr>";
                    echo "<tr><td>Status ID</td><td>{$updated['status_id']}</td></tr>";
                    echo "<tr><td>Mechanic ID</td><td>{$updated['mechanic_id']}</td></tr>";
                    echo "<tr><td>Helper ID</td><td>{$updated['helper_id']}</td></tr>";
                    echo "<tr><td>Notes</td><td>{$updated['notes']}</td></tr>";
                    echo "<tr><td>Updated At</td><td>{$updated['updated_at']}</td></tr>";
                    echo "</table>";
                }
            } else {
                echo "<p style='color: red;'>❌ Update failed: " . $db->error()['message'] . "</p>";
            }
            
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

