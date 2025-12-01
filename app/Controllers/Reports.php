<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Dompdf\Dompdf;
use Dompdf\Options;

class Reports extends BaseController
{
    use ActivityLoggingTrait;
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Check permission: Reports bisa diakses oleh admin atau user dengan akses ke modul apapun
        // Untuk sekarang, kita check admin access
        if (!$this->canAccess('admin')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }
        
        $data = [
            'title' => 'Reports Dashboard | OPTIMA',
            'page_title' => 'Reports Dashboard',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/reports' => 'Reports'
            ],
            'loadDataTables' => true, // Enable DataTables loading
        ];
            'report_categories' => $this->getReportCategories(),
            'recent_reports' => $this->getRecentReports(),
            'report_stats' => $this->getReportStats()
        ];

        return view('reports/index', $data);
    }

    public function rental()
    {
        $data = [
            'title' => 'Rental Reports | OPTIMA',
            'page_title' => 'Rental Reports',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/reports' => 'Reports',
                '/reports/rental' => 'Rental Reports'
            ],
            'rental_data' => $this->getRentalReportData(),
            'filters' => $this->getFilters()
        ];

        return view('reports/rental', $data);
    }

    public function maintenance()
    {
        $data = [
            'title' => 'Maintenance Reports | OPTIMA',
            'page_title' => 'Maintenance Reports',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/reports' => 'Reports',
                '/reports/maintenance' => 'Maintenance Reports'
            ],
            'maintenance_data' => $this->getMaintenanceReportData(),
            'filters' => $this->getFilters()
        ];

        return view('reports/maintenance', $data);
    }

    public function financial()
    {
        $data = [
            'title' => 'Financial Reports | OPTIMA',
            'page_title' => 'Financial Reports',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/reports' => 'Reports',
                '/reports/financial' => 'Financial Reports'
            ],
            'financial_data' => $this->getFinancialReportData(),
            'filters' => $this->getFilters()
        ];

        return view('reports/financial', $data);
    }

    public function inventory()
    {
        $data = [
            'title' => 'Inventory Reports | OPTIMA',
            'page_title' => 'Inventory Reports',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/reports' => 'Reports',
                '/reports/inventory' => 'Inventory Reports'
            ],
            'inventory_data' => $this->getInventoryReportData(),
            'filters' => $this->getFilters()
        ];

        return view('reports/inventory', $data);
    }

    public function custom()
    {
        $data = [
            'title' => 'Custom Reports | OPTIMA',
            'page_title' => 'Custom Report Builder',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/reports' => 'Reports',
                '/reports/custom' => 'Custom Reports'
            ],
            'report_templates' => $this->getReportTemplates(),
            'data_sources' => $this->getDataSources()
        ];

        return view('reports/custom', $data);
    }

    // API Methods for Report Generation
    public function generateReport()
    {
        $request = service('request');
        $reportType = $request->getPost('type');
        $format = $request->getPost('format') ?: 'pdf';
        $dateFrom = $request->getPost('date_from');
        $dateTo = $request->getPost('date_to');
        $filters = $request->getPost('filters') ?: [];

        try {
            // Generate report data based on type
            $reportData = $this->getReportData($reportType, $dateFrom, $dateTo, $filters);
            
            // Save report record
            $reportId = $this->saveReportRecord($reportType, $format, $reportData);
            
            // Generate file based on format
            $filename = $this->generateReportFile($reportType, $format, $reportData, $reportId);
            
            // Log activity
            $this->logActivity('report_generated', "Generated {$reportType} report in {$format} format");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Report generated successfully',
                'report_id' => $reportId,
                'filename' => $filename,
                'download_url' => base_url("reports/download/{$reportId}")
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Report generation failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ]);
        }
    }

    public function generateCustomReport()
    {
        $request = service('request');
        $reportName = $request->getPost('report_name');
        $reportType = $request->getPost('report_type');
        $format = $request->getPost('format') ?: 'pdf';
        $dateFrom = $request->getPost('date_from');
        $dateTo = $request->getPost('date_to');
        $fields = $request->getPost('fields') ?: [];
        $filters = $request->getPost('filters') ?: [];

        try {
            // Generate custom report data
            $reportData = $this->getCustomReportData($reportType, $dateFrom, $dateTo, $fields, $filters);
            
            // Save report record
            $reportId = $this->saveReportRecord($reportType, $format, $reportData, $reportName);
            
            // Generate file
            $filename = $this->generateReportFile($reportType, $format, $reportData, $reportId, $reportName);
            
            // Log activity
            $this->logActivity('custom_report_generated', "Generated custom report: {$reportName}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Custom report generated successfully',
                'report_id' => $reportId,
                'filename' => $filename,
                'download_url' => base_url("reports/download/{$reportId}")
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Custom report generation failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Custom report generation failed: ' . $e->getMessage()
            ]);
        }
    }

    public function download($reportId)
    {
        $report = $this->getReportById($reportId);
        
        if (!$report) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Report not found');
        }
        
        $filepath = WRITEPATH . 'reports/' . $report['filename'];
        
        if (!file_exists($filepath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Report file not found');
        }
        
        // Log download activity
        $this->logActivity('report_downloaded', "Downloaded report: {$report['name']}");
        
        return $this->response->download($filepath, null);
    }

    public function delete($reportId)
    {
        $report = $this->getReportById($reportId);
        
        if (!$report) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Report not found'
            ]);
        }
        
        try {
            // Delete file
            $filepath = WRITEPATH . 'reports/' . $report['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Delete database record
            $this->db->table('reports')->where('id', $reportId)->delete();
            
            // Log activity
            $this->logActivity('report_deleted', "Deleted report: {$report['name']}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Report deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete report: ' . $e->getMessage()
            ]);
        }
    }

    // Private Methods
    private function getReportCategories()
    {
        return [
            'rental' => [
                'name' => 'Rental Reports',
                'count' => 15,
                'icon' => 'fas fa-handshake',
                'color' => 'primary'
            ],
            'financial' => [
                'name' => 'Financial Reports',
                'count' => 8,
                'icon' => 'fas fa-dollar-sign',
                'color' => 'success'
            ],
            'maintenance' => [
                'name' => 'Maintenance Reports',
                'count' => 12,
                'icon' => 'fas fa-wrench',
                'color' => 'warning'
            ],
            'inventory' => [
                'name' => 'Inventory Reports',
                'count' => 6,
                'icon' => 'fas fa-boxes',
                'color' => 'info'
            ]
        ];
    }

    private function getRecentReports()
    {
        $this->createReportsTable();
        
        try {
            return $this->db->table('reports r')
                           ->select('r.*, u.first_name, u.last_name')
                           ->join('users u', 'u.id = r.generated_by', 'left')
                           ->orderBy('r.created_at', 'DESC')
                           ->limit(10)
                           ->get()
                           ->getResultArray();
        } catch (\Exception $e) {
            // If tables don't exist, return empty array
            return [];
        }
    }

    private function getReportStats()
    {
        $this->createReportsTable();
        
        try {
            return [
                'total_reports' => $this->db->table('reports')->countAll(),
                'this_month_reports' => $this->db->table('reports')
                                       ->where('MONTH(created_at)', date('m'))
                                       ->where('YEAR(created_at)', date('Y'))
                                       ->countAllResults(),
                'pending_reports' => $this->db->table('reports')
                                             ->where('status', 'generating')
                                             ->countAllResults(),
                'completed_reports' => $this->db->table('reports')
                                               ->where('status', 'completed')
                                               ->countAllResults()
            ];
        } catch (\Exception $e) {
            // If tables don't exist, return default stats
            return [
                'total_reports' => 0,
                'this_month_reports' => 0,
                'pending_reports' => 0,
                'completed_reports' => 0
            ];
        }
    }

    private function getReportData($reportType, $dateFrom, $dateTo, $filters = [])
    {
        switch ($reportType) {
            case 'rental_monthly':
                return []; // Rental monthly not implemented - use inventory_unit status instead
            case 'contract_performance':
                return $this->getContractPerformanceData($dateFrom, $dateTo, $filters);
            case 'unit_utilization':
                return $this->getUnitUtilizationData($dateFrom, $dateTo, $filters);
            case 'revenue':
                return $this->getRevenueData($dateFrom, $dateTo, $filters);
            case 'expenses':
                return $this->getExpensesData($dateFrom, $dateTo, $filters);
            case 'profit_loss':
                return $this->getProfitLossData($dateFrom, $dateTo, $filters);
            case 'maintenance_schedule':
                return $this->getMaintenanceScheduleData($dateFrom, $dateTo, $filters);
            case 'work_orders':
                return $this->getWorkOrdersData($dateFrom, $dateTo, $filters);
            case 'downtime':
                return $this->getDowntimeData($dateFrom, $dateTo, $filters);
            case 'stock_levels':
                return $this->getStockLevelsData($dateFrom, $dateTo, $filters);
            case 'sparepart_usage':
                return $this->getSparepartUsageData($dateFrom, $dateTo, $filters);
            case 'asset_valuation':
                return $this->getAssetValuationData($dateFrom, $dateTo, $filters);
            default:
                throw new \InvalidArgumentException('Invalid report type');
        }
    }

    private function generateReportFile($reportType, $format, $reportData, $reportId, $customName = null)
    {
        $reportName = $customName ?: $this->getReportTypeName($reportType);
        $filename = $this->generateFilename($reportName, $format);
        $filepath = WRITEPATH . 'reports/' . $filename;
        
        // Create reports directory if it doesn't exist
        if (!is_dir(WRITEPATH . 'reports/')) {
            mkdir(WRITEPATH . 'reports/', 0755, true);
        }
        
        switch ($format) {
            case 'pdf':
                $this->generatePDF($reportData, $filepath, $reportName);
                break;
            case 'excel':
                $this->generateExcel($reportData, $filepath, $reportName);
                break;
            case 'csv':
                $this->generateCSV($reportData, $filepath);
                break;
            default:
                throw new \InvalidArgumentException('Invalid format');
        }
        
        // Update report record with file path
        $this->db->table('reports')
                 ->where('id', $reportId)
                 ->update([
                     'file_path' => $filepath,
                     'file_size' => filesize($filepath),
                     'status' => 'completed',
                     'updated_at' => date('Y-m-d H:i:s')
                 ]);
        
        return $filename;
    }

    private function generatePDF($reportData, $filepath, $reportName)
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->generateReportHTML($reportData, $reportName);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        file_put_contents($filepath, $dompdf->output());
    }

    private function generateExcel($reportData, $filepath, $reportName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', $reportName);
        $sheet->mergeCells('A1:' . $this->getLastColumn($reportData) . '1');
        
        // Set headers
        $headers = array_keys($reportData['data'][0] ?? []);
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', ucfirst(str_replace('_', ' ', $header)));
            $col++;
        }
        
        // Set data
        $row = 4;
        foreach ($reportData['data'] as $item) {
            $col = 'A';
            foreach ($item as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
    }

    private function generateCSV($reportData, $filepath)
    {
        $file = fopen($filepath, 'w');
        
        // Write headers
        if (!empty($reportData['data'])) {
            fputcsv($file, array_keys($reportData['data'][0]));
            
            // Write data
            foreach ($reportData['data'] as $row) {
                fputcsv($file, $row);
            }
        }
        
        fclose($file);
    }

    private function generateReportHTML($reportData, $reportName)
    {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$reportName}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; margin-bottom: 30px; }
                .company-info { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .summary { background-color: #f9f9f9; padding: 15px; margin: 20px 0; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>OPTIMA - PT Sarana Mitra Luas Tbk</h1>
                <h2>{$reportName}</h2>
                <p>Generated on: " . date('d/m/Y H:i:s') . "</p>
            </div>
            
            <div class='company-info'>
                <strong>Company:</strong> PT Sarana Mitra Luas Tbk<br>
                <strong>Address:</strong> Jl. Industri No. 123, Jakarta<br>
                <strong>Phone:</strong> 021-12345678
            </div>";
        
        if (!empty($reportData['summary'])) {
            $html .= "<div class='summary'>";
            $html .= "<h3>Summary</h3>";
            foreach ($reportData['summary'] as $key => $value) {
                $html .= "<strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> {$value}<br>";
            }
            $html .= "</div>";
        }
        
        if (!empty($reportData['data'])) {
            $html .= "<table>";
            $html .= "<thead><tr>";
            foreach (array_keys($reportData['data'][0]) as $header) {
                $html .= "<th>" . ucfirst(str_replace('_', ' ', $header)) . "</th>";
            }
            $html .= "</tr></thead>";
            
            $html .= "<tbody>";
            foreach ($reportData['data'] as $row) {
                $html .= "<tr>";
                foreach ($row as $cell) {
                    $html .= "<td>{$cell}</td>";
                }
                $html .= "</tr>";
            }
            $html .= "</tbody>";
            $html .= "</table>";
        }
        
        $html .= "
            <div class='footer'>
                <p>This report was generated automatically by OPTIMA system.</p>
                <p>© " . date('Y') . " PT Sarana Mitra Luas Tbk. All rights reserved.</p>
            </div>
        </body>
        </html>";
        
        return $html;
    }

    private function saveReportRecord($reportType, $format, $reportData, $customName = null)
    {
        $this->createReportsTable();
        
        $reportName = $customName ?: $this->getReportTypeName($reportType);
        
        $data = [
            'name' => $reportName,
            'type' => $reportType,
            'format' => $format,
            'generated_by' => session()->get('user_id') ?: 1,
            'status' => 'generating',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->table('reports')->insert($data);
        return $this->db->insertID();
    }

    private function createReportsTable()
    {
        // Create users table first if it doesn't exist
        $this->createUsersTable();
        
        if (!$this->db->tableExists('reports')) {
            $forge = \Config\Database::forge();
            
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255
                ],
                'type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'format' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'file_path' => [
                    'type' => 'VARCHAR',
                    'constraint' => 500,
                    'null' => true
                ],
                'file_size' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true
                ],
                'generated_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['generating', 'completed', 'failed'],
                    'default' => 'generating'
                ],
                'parameters' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            $forge->addKey('id', true);
            $forge->addKey('generated_by');
            $forge->addKey('type');
            $forge->addKey('status');
            $forge->createTable('reports');
        }
    }

    private function createUsersTable()
    {
        if (!$this->db->tableExists('users')) {
            try {
                $forge = \Config\Database::forge();
                
                $forge->addField([
                    'id' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true
                    ],
                    'first_name' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50
                    ],
                    'last_name' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50
                    ],
                    'email' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100
                    ],
                    'password' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255
                    ],
                    'phone' => [
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'null' => true
                    ],
                    'avatar' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true
                    ],
                    'role' => [
                        'type' => 'ENUM',
                        'constraint' => ['admin', 'manager', 'user', 'technician'],
                        'default' => 'user'
                    ],
                    'status' => [
                        'type' => 'ENUM',
                        'constraint' => ['active', 'inactive', 'locked'],
                        'default' => 'active'
                    ],
                    'remember_token' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true
                    ],
                    'reset_token' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true
                    ],
                    'reset_token_expires' => [
                        'type' => 'DATETIME',
                        'null' => true
                    ],
                    'email_verified_at' => [
                        'type' => 'DATETIME',
                        'null' => true
                    ],
                    'last_login' => [
                        'type' => 'DATETIME',
                        'null' => true
                    ],
                    'login_attempts' => [
                        'type' => 'INT',
                        'constraint' => 3,
                        'default' => 0
                    ],
                    'created_at' => [
                        'type' => 'DATETIME',
                        'null' => true
                    ],
                    'updated_at' => [
                        'type' => 'DATETIME',
                        'null' => true
                    ],
                    'deleted_at' => [
                        'type' => 'DATETIME',
                        'null' => true
                    ]
                ]);
                
                $forge->addKey('id', true);
                $forge->addUniqueKey('email');
                $forge->addKey('role');
                $forge->addKey('status');
                $forge->createTable('users');
                
                // Insert default admin user if not exists
                $existingUser = $this->db->table('users')->where('email', 'admin@optima.com')->get()->getRowArray();
                if (!$existingUser) {
                    $this->db->table('users')->insert([
                        'first_name' => 'Admin',
                        'last_name' => 'System',
                        'email' => 'admin@optima.com',
                        'password' => password_hash('admin123', PASSWORD_DEFAULT),
                        'role' => 'admin',
                        'status' => 'active',
                        'email_verified_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but don't throw exception to prevent breaking the application
                log_message('error', 'Failed to create users table: ' . $e->getMessage());
            }
        } else {
            // Table exists, check if admin user exists
            try {
                $existingUser = $this->db->table('users')->where('email', 'admin@optima.com')->get()->getRowArray();
                if (!$existingUser) {
                    $this->db->table('users')->insert([
                        'first_name' => 'Admin',
                        'last_name' => 'System',
                        'email' => 'admin@optima.com',
                        'password' => password_hash('admin123', PASSWORD_DEFAULT),
                        'role' => 'admin',
                        'status' => 'active',
                        'email_verified_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but don't throw exception
                log_message('error', 'Failed to insert admin user: ' . $e->getMessage());
            }
        }
    }

    // Mock data methods - replace with actual database queries
    private function getContractPerformanceData($dateFrom, $dateTo, $filters)
    {
        return [
            'summary' => [
                'total_contracts' => 38,
                'completed_on_time' => 36,
                'delayed_contracts' => 2,
                'performance_rate' => '94.7%'
            ],
            'data' => [
                ['contract_id' => 'CNT-001', 'client' => 'PT ABC', 'planned_duration' => '30 days', 'actual_duration' => '28 days', 'status' => 'Completed'],
                ['contract_id' => 'CNT-002', 'client' => 'PT XYZ', 'planned_duration' => '45 days', 'actual_duration' => '47 days', 'status' => 'Delayed'],
                // Add more mock data...
            ]
        ];
    }

    private function getUnitUtilizationData($dateFrom, $dateTo, $filters)
    {
        return [
            'summary' => [
                'total_units' => 125,
                'utilized_units' => 98,
                'utilization_rate' => '78.4%',
                'idle_units' => 27
            ],
            'data' => [
                ['unit_id' => 'FL-001', 'unit_type' => 'Forklift 3T', 'utilization_hours' => 180, 'idle_hours' => 20, 'utilization_rate' => '90%'],
                ['unit_id' => 'FL-002', 'unit_type' => 'Forklift 5T', 'utilization_hours' => 165, 'idle_hours' => 35, 'utilization_rate' => '82.5%'],
                // Add more mock data...
            ]
        ];
    }

    // Add more data methods for other report types...
    private function getRevenueData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }
    private function getExpensesData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }
    private function getProfitLossData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }
    private function getMaintenanceScheduleData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }
    private function getWorkOrdersData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }
    private function getDowntimeData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }
    private function getStockLevelsData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }
    private function getSparepartUsageData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }
    private function getAssetValuationData($dateFrom, $dateTo, $filters) { /* Mock implementation */ return ['summary' => [], 'data' => []]; }

    private function getReportTypeName($type)
    {
        $names = [
            'rental_monthly' => 'Monthly Rental Summary',
            'contract_performance' => 'Contract Performance Report',
            'unit_utilization' => 'Unit Utilization Report',
            'revenue' => 'Revenue Report',
            'expenses' => 'Expense Report',
            'profit_loss' => 'Profit & Loss Report',
            'maintenance_schedule' => 'Maintenance Schedule Report',
            'work_orders' => 'Work Orders Report',
            'downtime' => 'Downtime Analysis Report',
            'stock_levels' => 'Stock Levels Report',
            'sparepart_usage' => 'Sparepart Usage Report',
            'asset_valuation' => 'Asset Valuation Report'
        ];
        
        return $names[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    private function generateFilename($reportName, $format)
    {
        $timestamp = date('Y-m-d_H-i-s');
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $reportName);
        return $safeName . '_' . $timestamp . '.' . $format;
    }

    private function getLastColumn($reportData)
    {
        if (empty($reportData['data'])) return 'A';
        $columnCount = count(array_keys($reportData['data'][0]));
        return chr(65 + $columnCount - 1); // A=65, B=66, etc.
    }

    private function getReportById($reportId)
    {
        return $this->db->table('reports')
                       ->where('id', $reportId)
                       ->get()
                       ->getRowArray();
    }

    private function logActivity($action, $description)
    {
        // Log to activity log using ActivityLoggingTrait or SystemActivityLogModel
        try {
            $activityModel = new \App\Models\SystemActivityLogModel();
            $activityModel->insert([
                'table_name' => 'reports',
                'action' => $action,
                'description' => $description,
                'user_id' => session()->get('user_id') ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
        }
    }

    // Additional helper methods for custom reports, filters, etc.
    private function getFilters()
    {
        return [
            'date_range' => ['last_7_days', 'last_30_days', 'last_3_months', 'last_year', 'custom'],
            'status' => ['active', 'completed', 'cancelled', 'pending'],
            'unit_type' => ['forklift_3t', 'forklift_5t', 'reach_truck', 'pallet_jack'],
            'client_type' => ['corporate', 'individual', 'government']
        ];
    }

    private function getDataSources()
    {
        return [
            'rentals' => 'Rental Data',
            'maintenance' => 'Maintenance Records',
            'inventory' => 'Inventory Data',
            'financial' => 'Financial Transactions',
            'users' => 'User Data',
            'assets' => 'Asset Information'
        ];
    }

    private function getReportTemplates()
    {
        return [
            'monthly_summary' => 'Monthly Summary Template',
            'performance_analysis' => 'Performance Analysis Template',
            'financial_overview' => 'Financial Overview Template',
            'maintenance_report' => 'Maintenance Report Template',
            'inventory_status' => 'Inventory Status Template'
        ];
    }

    private function getCustomReportData($reportType, $dateFrom, $dateTo, $fields, $filters)
    {
        // This would implement custom report data generation based on selected fields and filters
        return $this->getReportData($reportType, $dateFrom, $dateTo, $filters);
    }

    // Mock data methods for different report categories
    private function getRentalReportData()
    {
        $db = \Config\Database::connect();
        
        // Get real rental data from inventory_unit with status RENTAL_ACTIVE (7)
        $activeRentals = $db->table('inventory_unit iu')
            ->select('iu.*, k.no_kontrak, k.pelanggan')
            ->join('kontrak k', 'iu.kontrak_id = k.id', 'left')
            ->where('iu.status_unit_id', 7) // RENTAL_ACTIVE
            ->get()->getResultArray();
            
        $totalRentals = $db->table('inventory_unit')
            ->where('status_unit_id', 7)
            ->countAllResults();
            
        return [
            'summary' => [
                'total_rentals' => $totalRentals,
                'active_contracts' => count($activeRentals),
                'completed_contracts' => 0, // TODO: implement history tracking
                'total_revenue' => 'Rp ' . number_format(array_sum(array_column($activeRentals, 'harga_sewa_bulanan')), 0, ',', '.')
            ],
            'recent_rentals' => array_slice($activeRentals, 0, 10) // Limit to 10 recent
        ];
    }

    private function getMaintenanceReportData()
    {
        return [
            'summary' => [
                'total_work_orders' => 78,
                'completed_maintenance' => 65,
                'pending_maintenance' => 13,
                'overdue_maintenance' => 5
            ],
            'recent_maintenance' => [
                ['id' => 'MNT-001', 'unit' => 'FL-001', 'type' => 'Preventive', 'status' => 'Completed'],
                ['id' => 'MNT-002', 'unit' => 'FL-002', 'type' => 'Corrective', 'status' => 'Pending']
            ]
        ];
    }

    private function getFinancialReportData()
    {
        return [
            'summary' => [
                'total_revenue' => 'Rp 750,000,000',
                'total_expenses' => 'Rp 450,000,000',
                'net_profit' => 'Rp 300,000,000',
                'profit_margin' => '40%'
            ],
            'monthly_data' => [
                ['month' => 'January', 'revenue' => 'Rp 65,000,000', 'expenses' => 'Rp 40,000,000'],
                ['month' => 'February', 'revenue' => 'Rp 70,000,000', 'expenses' => 'Rp 42,000,000']
            ]
        ];
    }

    private function getInventoryReportData()
    {
        return [
            'summary' => [
                'total_units' => 125,
                'available_units' => 87,
                'rented_units' => 28,
                'maintenance_units' => 10
            ],
            'inventory_levels' => [
                ['category' => 'Forklift 3T', 'total' => 45, 'available' => 32, 'rented' => 10],
                ['category' => 'Forklift 5T', 'total' => 35, 'available' => 25, 'rented' => 8]
            ]
        ];
    }
} 