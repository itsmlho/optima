<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DatabaseFix extends Controller
{
    public function fixStatusColumn()
    {
        $db = \Config\Database::connect();
        
        echo "<h2>Fixing Status Column Issue</h2>";
        echo "<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
        </style>";
        
        // Check if purchase_orders table exists
        if ($db->tableExists('purchase_orders')) {
            echo "<p class='success'>✓ purchase_orders table exists</p>";
            
            // Check table structure
            $fields = $db->getFieldNames('purchase_orders');
            echo "<p class='info'>Current columns in purchase_orders: " . implode(', ', $fields) . "</p>";
            
            // Check if status column exists
            if (in_array('status', $fields)) {
                echo "<p class='success'>✓ status column already exists</p>";
            } else {
                echo "<p class='error'>✗ status column missing - adding it...</p>";
                
                // Add status column if it doesn't exist
                $sql = "ALTER TABLE purchase_orders ADD COLUMN status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending'";
                try {
                    $db->query($sql);
                    echo "<p class='success'>✓ status column added successfully</p>";
                } catch (\Exception $e) {
                    echo "<p class='error'>✗ Error adding status column: " . $e->getMessage() . "</p>";
                }
            }
            
            // Show current table structure
            echo "<h3>Current Table Structure:</h3>";
            $result = $db->query("DESCRIBE purchase_orders");
            echo "<pre>";
            foreach ($result->getResultArray() as $row) {
                echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . $row['Default'] . " | " . $row['Extra'] . "\n";
            }
            echo "</pre>";
            
        } else {
            echo "<p class='error'>✗ purchase_orders table doesn't exist - creating it...</p>";
            
            // Create the table manually
            $sql = "CREATE TABLE purchase_orders (
                id_po INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                no_po VARCHAR(50) NOT NULL,
                tanggal_po DATE NOT NULL,
                supplier_id INT(11) UNSIGNED,
                tipe_po VARCHAR(50) NOT NULL,
                status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )";
            try {
                $db->query($sql);
                echo "<p class='success'>✓ purchase_orders table created</p>";
            } catch (\Exception $e) {
                echo "<p class='error'>✗ Error creating table: " . $e->getMessage() . "</p>";
            }
        }
        
        // Check if suppliers table exists
        if ($db->tableExists('suppliers')) {
            echo "<p class='success'>✓ suppliers table exists</p>";
        } else {
            echo "<p class='error'>✗ suppliers table doesn't exist - creating it...</p>";
            
            $sql = "CREATE TABLE suppliers (
                id_supplier INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama_supplier VARCHAR(255) NOT NULL,
                alamat TEXT NULL,
                telepon VARCHAR(20) NULL,
                email VARCHAR(100) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )";
            try {
                $db->query($sql);
                echo "<p class='success'>✓ suppliers table created</p>";
                
                // Insert sample data
                $sampleData = [
                    [
                        'nama_supplier' => 'PT Toyota Astra Motor',
                        'alamat' => 'Jakarta, Indonesia',
                        'telepon' => '021-1234567',
                        'email' => 'info@toyota.co.id',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'nama_supplier' => 'PT Komatsu Indonesia',
                        'alamat' => 'Jakarta, Indonesia',
                        'telepon' => '021-2345678',
                        'email' => 'info@komatsu.co.id',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'nama_supplier' => 'PT Hitachi Construction Machinery Indonesia',
                        'alamat' => 'Jakarta, Indonesia',
                        'telepon' => '021-3456789',
                        'email' => 'info@hitachi.co.id',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                ];
                
                $db->table('suppliers')->insertBatch($sampleData);
                echo "<p class='success'>✓ Sample suppliers data inserted</p>";
            } catch (\Exception $e) {
                echo "<p class='error'>✗ Error creating suppliers table: " . $e->getMessage() . "</p>";
            }
        }
        
        // Test the PurchasingModel
        echo "<h3>Testing PurchasingModel:</h3>";
        try {
            $purchasingModel = new \App\Models\PurchasingModel();
            $stats = $purchasingModel->getPOStats('Unit');
            echo "<p class='success'>✓ PurchasingModel getPOStats() works: " . json_encode($stats) . "</p>";
        } catch (\Exception $e) {
            echo "<p class='error'>✗ PurchasingModel error: " . $e->getMessage() . "</p>";
        }
        
        echo "<p><strong>Status column fix completed!</strong></p>";
        echo "<p><a href='/purchasing'>Go to Purchasing Dashboard</a></p>";
        echo "<p><a href='/purchasing/po-unit'>Go to PO Unit</a></p>";
    }

    public function addSampleData()
    {
        $db = \Config\Database::connect();
        
        echo "<h2>Adding Sample Purchase Order Data</h2>";
        echo "<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
        </style>";
        
        // Check if we have suppliers
        $suppliers = $db->table('suppliers')->get()->getResultArray();
        if (empty($suppliers)) {
            echo "<p class='error'>No suppliers found. Please run the fix-status-column first.</p>";
            return;
        }
        
        // Check if we have purchase orders
        $existingPOs = $db->table('purchase_orders')->countAllResults();
        if ($existingPOs > 0) {
            echo "<p class='info'>Found {$existingPOs} existing purchase orders.</p>";
        }
        
        // Add sample purchase orders
        $samplePOs = [
            [
                'no_po' => 'PO-Unit-2025-07-0001',
                'tanggal_po' => '2025-07-16',
                'supplier_id' => $suppliers[0]['id_supplier'],
                'tipe_po' => 'Unit',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'no_po' => 'PO-Unit-2025-07-0002',
                'tanggal_po' => '2025-07-15',
                'supplier_id' => $suppliers[1]['id_supplier'],
                'tipe_po' => 'Unit',
                'status' => 'approved',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'no_po' => 'PO-Unit-2025-07-0003',
                'tanggal_po' => '2025-07-14',
                'supplier_id' => $suppliers[2]['id_supplier'],
                'tipe_po' => 'Unit',
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        try {
            $db->table('purchase_orders')->insertBatch($samplePOs);
            echo "<p class='success'>✓ Added " . count($samplePOs) . " sample purchase orders</p>";
            
            // Show the data
            $pos = $db->table('purchase_orders po')
                     ->select('po.*, s.nama_supplier')
                     ->join('suppliers s', 's.id_supplier = po.supplier_id', 'left')
                     ->where('po.tipe_po', 'Unit')
                     ->get()
                     ->getResultArray();
            
            echo "<h3>Current Purchase Orders:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>PO Number</th><th>Date</th><th>Supplier</th><th>Status</th></tr>";
            foreach ($pos as $po) {
                echo "<tr>";
                echo "<td>{$po['no_po']}</td>";
                echo "<td>{$po['tanggal_po']}</td>";
                echo "<td>{$po['nama_supplier']}</td>";
                echo "<td>{$po['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } catch (\Exception $e) {
            echo "<p class='error'>✗ Error adding sample data: " . $e->getMessage() . "</p>";
        }
        
        echo "<p><strong>Sample data added!</strong></p>";
        echo "<p><a href='/purchasing/po-unit'>Go to PO Unit</a></p>";
    }
} 