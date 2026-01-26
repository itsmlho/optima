<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AssetManagementModel;
use App\Models\InventoryUnitModel;


class Dashboard extends BaseController
{
    protected $userModel;
    protected $AssetManagementModel;
    protected $inventoryUnitModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->AssetManagementModel = new AssetManagementModel();
        $this->inventoryUnitModel = new InventoryUnitModel();
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        try {
            $data = $this->prepareViewData([
                'title' => 'Dashboard',
                'page_title' => 'Dashboard Utama',
                'breadcrumbs' => [
                    '/' => 'Dashboard'
                ],
                'summary' => $this->getSummaryMetrics(),
                'assets' => $this->getAssetsData(),
                'workorders' => $this->getWorkOrdersData(),
                'pmps' => $this->getPMPSData(),
                'spk' => $this->getSPKData(),
                'di' => $this->getDIData(),
                'customers' => $this->getCustomersData(),
                'loadCharts' => true,
            ]);

            return view('dashboard', $data);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard error: ' . $e->getMessage());
            // Return dashboard with empty data on error
            $data = $this->prepareViewData([
                'title' => 'Dashboard',
                'page_title' => 'Dashboard Utama',
                'breadcrumbs' => [
                    '/' => 'Dashboard'
                ],
                'summary' => ['total_assets' => 0, 'active_contracts' => 0, 'contract_growth' => 0, 'wo_this_month' => 0, 'wo_completed' => 0, 'wo_pending' => 0, 'spk_di_this_month' => 0, 'spk_count' => 0, 'di_count' => 0],
                'assets' => ['units' => ['total' => 0, 'available' => 0, 'rented' => 0, 'maintenance' => 0, 'out_of_service' => 0], 'attachments' => ['total' => 0, 'used' => 0], 'chargers' => ['total' => 0, 'used' => 0], 'baterai' => ['total' => 0, 'used' => 0]],
                'workorders' => ['by_category' => [], 'by_area' => []],
                'pmps' => ['total' => 0, 'pending' => 0, 'completed' => 0, 'top_locations' => []],
                'spk' => ['by_jenis' => [], 'by_status' => []],
                'di' => ['total' => 0, 'pending' => 0, 'completed' => 0, 'top_locations' => []],
                'customers' => ['total' => 0, 'growth' => 0, 'active_contracts' => 0, 'expiring_contracts' => 0],
                'loadCharts' => true,
            ]);
            return view('dashboard', $data);
        }
    }

    /**
     * Get summary metrics for dashboard cards
     */
    private function getSummaryMetrics()
    {
        try {
            $db = \Config\Database::connect();
            
            // Total Assets (Unit + Attachment + Charger + Baterai)
            $totalUnits = $db->table('inventory_unit')->countAllResults();
            
            // Count attachments from inventory_attachment table
            $totalAttachmentsCount = $db->table('inventory_attachment')
                ->where('tipe_item', 'attachment')
                ->where('attachment_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $totalChargersCount = $db->table('inventory_attachment')
                ->where('tipe_item', 'charger')
                ->where('charger_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $totalBateraiCount = $db->table('inventory_attachment')
                ->where('tipe_item', 'battery')
                ->where('baterai_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $totalAssets = $totalUnits + $totalAttachmentsCount + $totalChargersCount + $totalBateraiCount;
            
            // Active Contracts
            $totalContracts = $db->table('kontrak')
                ->where('status', 'Aktif')
                ->countAllResults();
            
            // Contract growth calculation
            $lastMonthContracts = $db->table('kontrak')
                ->where('status', 'Aktif')
                ->where('DATE(created_at) >=', date('Y-m-01', strtotime('-1 month')))
                ->where('DATE(created_at) <', date('Y-m-01'))
                ->countAllResults();
            
            $contractGrowth = 0;
            if ($lastMonthContracts > 0) {
                $contractGrowth = (($totalContracts - $lastMonthContracts) / $lastMonthContracts) * 100;
            }
            
            // Work Orders this month
            $woThisMonth = $db->table('work_orders')
                ->where('created_at >=', date('Y-m-01'))
                ->countAllResults();
            
            $woCompleted = $db->table('work_orders wo')
                ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
                ->where('wo.created_at >=', date('Y-m-01'))
                ->where('wos.is_final_status', 1)
                ->countAllResults();
            
            $woPending = $woThisMonth - $woCompleted;
            
            // SPK & DI this month
            $spkThisMonth = $db->table('spk')
                ->where('dibuat_pada >=', date('Y-m-01'))
                ->countAllResults();
            
            $diThisMonth = $db->table('delivery_instructions')
                ->where('tanggal_kirim >=', date('Y-m-01'))
                ->countAllResults();
            
            return [
                'total_assets' => $totalAssets,
                'active_contracts' => $totalContracts,
                'contract_growth' => round($contractGrowth, 1),
                'wo_this_month' => $woThisMonth,
                'wo_completed' => $woCompleted,
                'wo_pending' => $woPending,
                'spk_di_this_month' => $spkThisMonth + $diThisMonth,
                'spk_count' => $spkThisMonth,
                'di_count' => $diThisMonth
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting summary metrics: ' . $e->getMessage());
            // Debug: write to file
            file_put_contents(WRITEPATH . 'debug_dashboard.txt', 
                date('Y-m-d H:i:s') . " - getSummaryMetrics ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n", 
                FILE_APPEND);
            return [
                'total_assets' => 0,
                'active_contracts' => 0,
                'contract_growth' => 0,
                'wo_this_month' => 0,
                'wo_completed' => 0,
                'wo_pending' => 0,
                'spk_di_this_month' => 0,
                'spk_count' => 0,
                'di_count' => 0
            ];
        }
    }

    /**
     * Get assets data (Units, Attachments, Chargers, Baterai)
     */
    private function getAssetsData()
    {
        try {
            $db = \Config\Database::connect();
            
            // Inventory Units by status
            $unitAvailable = $db->table('inventory_unit')
                ->where('status_unit_id', 1) // Available
                ->countAllResults();
            
            $unitRented = $db->table('inventory_unit')
                ->where('status_unit_id', 2) // Rented
                ->countAllResults();
            
            $unitMaintenance = $db->table('inventory_unit')
                ->where('status_unit_id', 4) // Maintenance
                ->countAllResults();
            
            $unitOutOfService = $db->table('inventory_unit')
                ->where('status_unit_id', 5) // Out of Service
                ->countAllResults();
            
            // Attachments (dari inventory_attachment dengan tipe_item='attachment')
            $totalAttachments = $db->table('inventory_attachment')
                ->where('tipe_item', 'attachment')
                ->where('attachment_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $activeAttachments = $db->table('inventory_attachment')
                ->where('tipe_item', 'attachment')
                ->where('attachment_id IS NOT NULL', null, false)
                ->where('attachment_status', 'USED')
                ->countAllResults();
            
            $attachmentUtilization = $totalAttachments > 0 
                ? round(($activeAttachments / $totalAttachments) * 100, 1) 
                : 0;
            
            // Chargers (dari inventory_attachment dengan tipe_item='charger')
            $totalChargers = $db->table('inventory_attachment')
                ->where('tipe_item', 'charger')
                ->where('charger_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $activeChargers = $db->table('inventory_attachment')
                ->where('tipe_item', 'charger')
                ->where('charger_id IS NOT NULL', null, false)
                ->where('attachment_status', 'USED')
                ->countAllResults();
            
            // Baterai (dari inventory_attachment dengan tipe_item='battery')
            $totalBaterai = $db->table('inventory_attachment')
                ->where('tipe_item', 'battery')
                ->where('baterai_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $activeBaterai = $db->table('inventory_attachment')
                ->where('tipe_item', 'battery')
                ->where('baterai_id IS NOT NULL', null, false)
                ->where('attachment_status', 'USED')
                ->countAllResults();
            
            return [
                'units' => [
                    'available' => $unitAvailable,
                    'rented' => $unitRented,
                    'maintenance' => $unitMaintenance,
                    'out_of_service' => $unitOutOfService,
                    'total' => $unitAvailable + $unitRented + $unitMaintenance + $unitOutOfService
                ],
                'attachments' => [
                    'total' => $totalAttachments,
                    'active' => $activeAttachments,
                    'utilization' => $attachmentUtilization
                ],
                'chargers' => [
                    'total' => $totalChargers,
                    'active' => $activeChargers
                ],
                'batteries' => [
                    'total' => $totalBaterai,
                    'active' => $activeBaterai
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting assets data: ' . $e->getMessage());
            return [
                'units' => ['available' => 0, 'rented' => 0, 'maintenance' => 0, 'out_of_service' => 0, 'total' => 0],
                'attachments' => ['total' => 0, 'active' => 0, 'utilization' => 0],
                'chargers' => ['total' => 0, 'active' => 0],
                'batteries' => ['total' => 0, 'active' => 0]
            ];
        }
    }

    /**
     * Get Work Orders data (by category and by area)
     */
    private function getWorkOrdersData()
    {
        try {
            $db = \Config\Database::connect();
            
            // WO by Category (Top 5) - bulan ini saja
            $woByCategory = $db->table('work_orders wo')
                ->select('woc.category_name as category, COUNT(wo.id) as count')
                ->join('work_order_categories woc', 'wo.category_id = woc.id', 'left')
                ->where('DATE(wo.created_at) >=', date('Y-m-01'))
                ->where('woc.category_name IS NOT NULL')
                ->groupBy('wo.category_id')
                ->orderBy('count', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            
            // WO by Area (Top 5 with percentage)
            $totalWO = $db->table('work_orders')->countAllResults();
            
            $woByArea = $db->table('work_orders wo')
                ->select('a.nama_area as area_name, COUNT(wo.id) as count')
                ->join('inventory_unit iu', 'wo.unit_id = iu.id_inventory_unit', 'left')
                ->join('areas a', 'iu.area_id = a.id', 'left')
                ->where('a.nama_area IS NOT NULL')
                ->groupBy('iu.area_id')
                ->orderBy('count', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            
            // Calculate percentages
            foreach ($woByArea as &$area) {
                $area['percentage'] = $totalWO > 0 ? round(($area['count'] / $totalWO) * 100, 1) : 0;
            }
            
            return [
                'by_category' => $woByCategory,
                'by_area' => $woByArea
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting work orders data: ' . $e->getMessage());
            return [
                'by_category' => [],
                'by_area' => []
            ];
        }
    }

    /**
     * Get PMPS data (overdue, next 7 days, next 30 days)
     */
    private function getPMPSData()
    {
        try {
            $db = \Config\Database::connect();
            
            // Overdue PMPS work orders
            $overdue = $db->table('work_orders wo')
                ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
                ->where('wo.order_type', 'PMPS')
                ->where('wos.is_final_status !=', 1)
                ->where('wo.due_date <', date('Y-m-d'))
                ->countAllResults();
            
            // Next 7 days
            $next7Days = $db->table('work_orders wo')
                ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
                ->where('wo.order_type', 'PMPS')
                ->where('wos.is_final_status !=', 1)
                ->where('wo.due_date >=', date('Y-m-d'))
                ->where('wo.due_date <=', date('Y-m-d', strtotime('+7 days')))
                ->countAllResults();
            
            // Next 30 days
            $next30Days = $db->table('work_orders wo')
                ->join('work_order_statuses wos', 'wo.status_id = wos.id', 'left')
                ->where('wo.order_type', 'PMPS')
                ->where('wos.is_final_status !=', 1)
                ->where('wo.due_date >=', date('Y-m-d', strtotime('+8 days')))
                ->where('wo.due_date <=', date('Y-m-d', strtotime('+30 days')))
                ->countAllResults();
            
            return [
                'overdue' => $overdue,
                'next_7_days' => $next7Days,
                'next_30_days' => $next30Days
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting PMPS data: ' . $e->getMessage());
            return ['overdue' => 0, 'next_7_days' => 0, 'next_30_days' => 0];
        }
    }

    /**
     * Get SPK data (by jenis perintah and by status)
     */
    private function getSPKData()
    {
        try {
            $db = \Config\Database::connect();
            
            // SPK by Jenis Perintah Kerja - bulan ini
            // Note: Many SPK have NULL jenis_perintah_kerja_id, so fallback to jenis_spk
            $spkByJenis = $db->table('spk s')
                ->select('COALESCE(jpk.nama, s.jenis_spk, "Tidak Diketahui") as jenis, COUNT(s.id) as count')
                ->join('jenis_perintah_kerja jpk', 's.jenis_perintah_kerja_id = jpk.id', 'left')
                ->where('DATE(s.dibuat_pada) >=', date('Y-m-01'))
                ->groupBy('COALESCE(jpk.nama, s.jenis_spk, "Tidak Diketahui")')
                ->get()
                ->getResultArray();
            
            // SPK by Status - bulan ini
            $spkByStatus = $db->table('spk')
                ->select('status_spk as status, COUNT(id) as count')
                ->where('DATE(dibuat_pada) >=', date('Y-m-01'))
                ->where('status_spk IS NOT NULL')
                ->groupBy('status_spk')
                ->get()
                ->getResultArray();
            
            return [
                'by_jenis_perintah' => $spkByJenis,
                'by_status' => $spkByStatus
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting SPK data: ' . $e->getMessage());
            return ['by_jenis_perintah' => [], 'by_status' => []];
        }
    }

    /**
     * Get Delivery Instructions data
     */
    private function getDIData()
    {
        try {
            $db = \Config\Database::connect();
            
            // Total DI
            $totalDI = $db->table('delivery_instructions')->countAllResults();
            
            // DI by status
            $diPending = $db->table('delivery_instructions')
                ->where('status_di', 'PENDING')
                ->countAllResults();
            
            $diCompleted = $db->table('delivery_instructions')
                ->where('status_di', 'SELESAI')
                ->countAllResults();
            
            // Top 5 locations
            $topLocations = $db->table('delivery_instructions')
                ->select('lokasi as location, COUNT(id) as count')
                ->groupBy('lokasi')
                ->orderBy('count', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            
            return [
                'total' => $totalDI,
                'pending' => $diPending,
                'completed' => $diCompleted,
                'top_locations' => $topLocations
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting DI data: ' . $e->getMessage());
            return ['total' => 0, 'pending' => 0, 'completed' => 0, 'top_locations' => []];
        }
    }

    /**
     * Get Customers data (total, growth, contracts)
     */
    private function getCustomersData()
    {
        try {
            $db = \Config\Database::connect();
            
            // Total customers
            $totalCustomers = $db->table('customers')->countAllResults();
            
            // Customers from last month
            $lastMonthCustomers = $db->table('customers')
                ->where('DATE(created_at) <', date('Y-m-01'))
                ->countAllResults();
            
            // Growth calculation
            $customerGrowth = 0;
            if ($lastMonthCustomers > 0) {
                $customerGrowth = (($totalCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100;
            }
            
            // Active contracts
            $activeContracts = $db->table('kontrak')
                ->where('status', 'Aktif')
                ->countAllResults();
            
            // Expiring contracts (next 30 days)
            $expiringContracts = $db->table('kontrak')
                ->where('status', 'Aktif')
                ->where('tanggal_berakhir >=', date('Y-m-d'))
                ->where('tanggal_berakhir <=', date('Y-m-d', strtotime('+30 days')))
                ->countAllResults();
            
            return [
                'total' => $totalCustomers,
                'growth' => round($customerGrowth, 1),
                'active_contracts' => $activeContracts,
                'expiring_contracts' => $expiringContracts
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting customers data: ' . $e->getMessage());
            return ['total' => 0, 'growth' => 0, 'active_contracts' => 0, 'expiring_contracts' => 0];
        }
    }
}
