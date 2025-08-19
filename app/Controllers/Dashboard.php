<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AssetManagementModel;
use App\Models\ForkliftModel;
use App\Models\RentalModel;


class Dashboard extends BaseController
{
    protected $userModel;
    protected $AssetManagementModel;


    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->AssetManagementModel = new AssetManagementModel();
        
        // Auto-create missing tables if needed
        $this->createMissingTables();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard | OPTIMA',
            'page_title' => 'Dashboard Utama',
            'breadcrumbs' => [
                '/' => 'Dashboard'
            ],
            'user_count' => $this->userModel->countAll(),
            'forklift_count' => $this->AssetManagementModel->countAll(),
        ];

        return view('dashboard', $data);
    }

    public function service()
    {
        $data = [
            'title' => 'Service Dashboard | OPTIMA',
            'page_title' => 'Dashboard Service',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/dashboard/service' => 'Service'
            ],
            'service_stats' => $this->getServiceStats(),
        ];

        return view('dashboard/service', $data);
    }

    public function rolling()
    {
        $data = [
            'title' => 'Rolling Unit Dashboard | OPTIMA',
            'page_title' => 'Dashboard Rolling Unit',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/dashboard/rolling' => 'Rolling Unit'
            ],
            'rolling_stats' => $this->getRollingStats(),
        ];

        return view('dashboard/rolling', $data);
    }

    public function marketing()
    {
        $data = [
            'title' => 'Marketing Dashboard | OPTIMA',
            'page_title' => 'Dashboard Marketing',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/dashboard/marketing' => 'Marketing'
            ],
            'marketing_stats' => $this->getMarketingStats(),
        ];

        return view('dashboard/marketing', $data);
    }

    public function warehouse()
    {
        $data = [
            'title' => 'Warehouse & Assets Dashboard | OPTIMA',
            'page_title' => 'Dashboard Warehouse & Assets',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/dashboard/warehouse' => 'Warehouse & Assets'
            ],
            'warehouse_stats' => $this->getWarehouseStats(),
        ];

        return view('dashboard/warehouse', $data);
    }

    private function getServiceStats()
    {
        // Get service-related statistics
        return [
            'total_work_orders' => 0, // Will be implemented later
            'pending_pmps' => 0,
            'completed_services' => 0,
            'maintenance_alerts' => 0,
        ];
    }

    private function getRollingStats()
    {
        try {
            // Get rolling unit statistics
            return [
                'total_units' => $this->forkliftModel->countAll(),
                'active_units' => $this->forkliftModel->where('status', 'available')->countAllResults(),
                'in_maintenance' => $this->forkliftModel->where('status', 'maintenance')->countAllResults(),
                'rented_units' => $this->forkliftModel->where('status', 'rented')->countAllResults(),
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting rolling stats: ' . $e->getMessage());
            return [
                'total_units' => 0,
                'active_units' => 0,
                'in_maintenance' => 0,
                'rented_units' => 0,
            ];
        }
    }

    private function getMarketingStats()
    {
        // Get marketing statistics
        return [
            'pending_quotations' => 0, // Will be implemented later
            'monthly_revenue' => 0, // Will be implemented later
        ];
    }

    private function getWarehouseStats()
    {
        try {
            // Get warehouse statistics
            return [
                'total_assets' => $this->forkliftModel->countAll(),
                'available_units' => $this->forkliftModel->where('status', 'available')->countAllResults(),
                'total_spareparts' => 0, // Will be implemented later
                'low_stock_items' => 0, // Will be implemented later
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting warehouse stats: ' . $e->getMessage());
            return [
                'total_assets' => 0,
                'available_units' => 0,
                'total_spareparts' => 0,
                'low_stock_items' => 0,
            ];
        }
    }

    private function createMissingTables()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        try {
            // Create forklifts table if not exists
            if (!$db->tableExists('forklifts')) {
                $fields = [
                    'id' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true,
                    ],
                    'unit_code' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'unique' => true,
                    ],
                    'model' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                    ],
                    'brand' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                    ],
                    'year' => [
                        'type' => 'YEAR',
                    ],
                    'capacity' => [
                        'type' => 'DECIMAL',
                        'constraint' => '10,2',
                    ],
                    'status' => [
                        'type' => 'ENUM',
                        'constraint' => ['available', 'rented', 'maintenance', 'retired'],
                        'default' => 'available',
                    ],
                    'location' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                    ],
                    'last_maintenance' => [
                        'type' => 'DATE',
                        'null' => true,
                    ],
                    'next_maintenance' => [
                        'type' => 'DATE',
                        'null' => true,
                    ],
                    'notes' => [
                        'type' => 'TEXT',
                        'null' => true,
                    ],
                    'created_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                    'updated_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                ];

                $forge->addField($fields);
                $forge->addKey('id', true);
                $forge->addUniqueKey('unit_code');
                $forge->addKey('status');
                $forge->addKey('location');
                $forge->createTable('forklifts');

                // Insert sample data
                $sampleData = [
                    [
                        'unit_code' => 'FL001',
                        'model' => 'Toyota 8FBE20',
                        'brand' => 'Toyota',
                        'year' => 2022,
                        'capacity' => 2.00,
                        'status' => 'available',
                        'location' => 'Warehouse A',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'unit_code' => 'FL002',
                        'model' => 'Mitsubishi FD25N',
                        'brand' => 'Mitsubishi',
                        'year' => 2021,
                        'capacity' => 2.50,
                        'status' => 'rented',
                        'location' => 'Customer Site',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'unit_code' => 'FL003',
                        'model' => 'Komatsu FD30T',
                        'brand' => 'Komatsu',
                        'year' => 2020,
                        'capacity' => 3.00,
                        'status' => 'maintenance',
                        'location' => 'Service Bay',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ];

                $db->table('forklifts')->insertBatch($sampleData);
            }

            // Create work_orders table if not exists
            if (!$db->tableExists('work_orders')) {
                $fields = [
                    'id' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true,
                    ],
                    'wo_number' => [
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'unique' => true,
                    ],
                    'forklift_id' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'customer_name' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                    ],
                    'issue_description' => [
                        'type' => 'TEXT',
                    ],
                    'priority' => [
                        'type' => 'ENUM',
                        'constraint' => ['low', 'medium', 'high', 'urgent'],
                        'default' => 'medium',
                    ],
                    'status' => [
                        'type' => 'ENUM',
                        'constraint' => ['pending', 'in_progress', 'completed', 'cancelled'],
                        'default' => 'pending',
                    ],
                    'assigned_technician' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                    ],
                    'estimated_completion' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                    'actual_completion' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                    'work_performed' => [
                        'type' => 'TEXT',
                        'null' => true,
                    ],
                    'parts_used' => [
                        'type' => 'TEXT',
                        'null' => true,
                    ],
                    'labor_hours' => [
                        'type' => 'DECIMAL',
                        'constraint' => '5,2',
                        'null' => true,
                    ],
                    'cost_estimate' => [
                        'type' => 'DECIMAL',
                        'constraint' => '12,2',
                        'null' => true,
                    ],
                    'final_cost' => [
                        'type' => 'DECIMAL',
                        'constraint' => '12,2',
                        'null' => true,
                    ],
                    'created_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                    'updated_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                ];

                $forge->addField($fields);
                $forge->addKey('id', true);
                $forge->addUniqueKey('wo_number');
                $forge->addKey('forklift_id');
                $forge->addKey('status');
                $forge->addKey('priority');
                $forge->createTable('work_orders');

                // Insert sample work orders
                $sampleWorkOrders = [
                    [
                        'wo_number' => 'WO001',
                        'forklift_id' => 1,
                        'customer_name' => 'PT. Gudang Sentral',
                        'issue_description' => 'Engine overheating issue',
                        'priority' => 'high',
                        'status' => 'in_progress',
                        'assigned_technician' => 'Ahmad Hidayat',
                        'estimated_completion' => date('Y-m-d H:i:s', strtotime('+2 days')),
                        'cost_estimate' => 500000.00,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'wo_number' => 'WO002',
                        'forklift_id' => 2,
                        'customer_name' => 'CV. Logistik Prima',
                        'issue_description' => 'Hydraulic leak repair',
                        'priority' => 'medium',
                        'status' => 'pending',
                        'cost_estimate' => 750000.00,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ];

                $db->table('work_orders')->insertBatch($sampleWorkOrders);
            }

        } catch (\Exception $e) {
            log_message('error', 'Failed to create missing tables: ' . $e->getMessage());
        }
    }
} 