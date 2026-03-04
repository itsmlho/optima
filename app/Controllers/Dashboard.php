<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
// use App\Models\AssetManagementModel; // DEPRECATED - Not in use, references non-existent 'forklifts' table
use App\Models\InventoryUnitModel;


class Dashboard extends BaseController
{
    protected $userModel;
    // protected $AssetManagementModel; // DEPRECATED - Removed unused instantiation
    protected $inventoryUnitModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        // $this->AssetManagementModel = new AssetManagementModel(); // DEPRECATED - Not used anywhere in controller
        $this->inventoryUnitModel = new InventoryUnitModel();
    }

    /**
     * Get recent activities for dashboard widget
     */
    public function getRecentActivities()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $limit = $this->request->getGet('limit') ?? 10;
        $moduleFilter = $this->request->getGet('module');
        $userFilter = $this->request->getGet('user');

        $builder = $db->table('system_activity_log sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sal.user_id', 'left');

        if ($moduleFilter) {
            $builder->where('sal.module_name', $moduleFilter);
        }

        if ($userFilter) {
            $builder->where('sal.user_id', $userFilter);
        }

        $builder->orderBy('sal.created_at', 'DESC');
        $builder->limit($limit);

        $activities = $builder->get()->getResultArray();

        // Format for display
        $formatted = [];
        foreach ($activities as $activity) {
            $formatted[] = [
                'id' => $activity['id'],
                'formatted_time' => date('d/m/Y H:i', strtotime($activity['created_at'])),
                'time_ago' => $this->timeAgo($activity['created_at']),
                'username' => $activity['username'] ?? 'System',
                'action_type' => $activity['action_type'] ?? 'UNKNOWN',
                'action_description' => $activity['action_description'] ?? 'No description',
                'module_name' => $activity['module_name'] ?? '-',
                'business_impact' => $activity['business_impact'] ?? 'LOW',
                'is_critical' => (bool)($activity['is_critical'] ?? 0)
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $formatted
        ]);
    }

    /**
     * Get activity analytics for dashboard
     */
    public function getActivityAnalytics()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $days = $this->request->getGet('days') ?? 7;

        // Activity trends
        $trends = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM system_activity_log
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", [$days])->getResultArray();

        // Most active users
        $activeUsers = $db->query("
            SELECT u.username, u.first_name, u.last_name, COUNT(sal.id) as activity_count
            FROM system_activity_log sal
            LEFT JOIN users u ON u.id = sal.user_id
            WHERE sal.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY sal.user_id, u.username, u.first_name, u.last_name
            ORDER BY activity_count DESC
            LIMIT 5
        ", [$days])->getResultArray();

        // Most modified tables
        $activeTables = $db->query("
            SELECT table_name, COUNT(*) as modification_count
            FROM system_activity_log
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            AND action_type IN ('CREATE', 'UPDATE', 'DELETE')
            GROUP BY table_name
            ORDER BY modification_count DESC
            LIMIT 5
        ", [$days])->getResultArray();

        // Action distribution
        $actionDistribution = $db->query("
            SELECT action_type, COUNT(*) as count
            FROM system_activity_log
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY action_type
            ORDER BY count DESC
        ", [$days])->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'trends' => $trends,
                'active_users' => $activeUsers,
                'active_tables' => $activeTables,
                'action_distribution' => $actionDistribution
            ]
        ]);
    }

    /**
     * Helper: Calculate time ago
     */
    private function timeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) return $diff . ' detik yang lalu';
        if ($diff < 3600) return floor($diff / 60) . ' menit yang lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam yang lalu';
        if ($diff < 604800) return floor($diff / 86400) . ' hari yang lalu';
        return date('d/m/Y H:i', $timestamp);
    }

    /**
     * Get expiring contracts (within 30 days)
     */
    public function getExpiringContracts()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        try {
            $expiringContracts = $db->query("
                SELECT 
                    k.no_kontrak,
                    k.tanggal_berakhir,
                    DATEDIFF(k.tanggal_berakhir, CURDATE()) as days_left,
                    cl.location_name as customer_location
                FROM kontrak k
                LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
                WHERE k.status = 'ACTIVE'
                AND k.tanggal_berakhir BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                ORDER BY k.tanggal_berakhir ASC
                LIMIT 10
            ")->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $expiringContracts
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Dashboard] getExpiringContracts error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching expiring contracts: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get rental type analytics - breakdown by CONTRACT/PO_ONLY/DAILY_SPOT
     */
    public function getRentalTypeAnalytics()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        try {
            // Get overall breakdown by rental type
            $breakdown = $db->query("
                SELECT 
                    rental_type,
                    COUNT(*) as total_contracts,
                    COUNT(CASE WHEN status = 'ACTIVE' THEN 1 END) as active_contracts,
                    COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_contracts,
                    COUNT(CASE WHEN status = 'EXPIRED' THEN 1 END) as expired_contracts,
                    COUNT(CASE WHEN status = 'CANCELLED' THEN 1 END) as cancelled_contracts,
                    SUM(nilai_total) as total_value,
                    SUM(CASE WHEN status = 'ACTIVE' THEN nilai_total ELSE 0 END) as active_value,
                    SUM(total_units) as total_units,
                    SUM(CASE WHEN status = 'ACTIVE' THEN total_units ELSE 0 END) as active_units
                FROM kontrak
                GROUP BY rental_type
                ORDER BY total_contracts DESC
            ")->getResultArray();

            // Get monthly trend (last 6 months)
            $trend = $db->query("
                SELECT 
                    DATE_FORMAT(dibuat_pada, '%Y-%m') as month,
                    rental_type,
                    COUNT(*) as contracts_created,
                    SUM(nilai_total) as total_value
                FROM kontrak
                WHERE dibuat_pada >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(dibuat_pada, '%Y-%m'), rental_type
                ORDER BY month ASC, rental_type
            ")->getResultArray();

            // Get contracts with PO vs without PO
            $poStats = $db->query("
                SELECT 
                    rental_type,
                    COUNT(CASE WHEN customer_po_number IS NOT NULL AND customer_po_number != '' THEN 1 END) as with_po,
                    COUNT(CASE WHEN customer_po_number IS NULL OR customer_po_number = '' THEN 1 END) as without_po
                FROM kontrak
                WHERE status IN ('ACTIVE', 'PENDING')
                GROUP BY rental_type
            ")->getResultArray();

            // Calculate totals
            $totals = [
                'total_contracts' => 0,
                'active_contracts' => 0,
                'pending_contracts' => 0,
                'total_value' => 0,
                'active_value' => 0,
                'total_units' => 0,
                'active_units' => 0
            ];

            foreach ($breakdown as $item) {
                $totals['total_contracts'] += $item['total_contracts'];
                $totals['active_contracts'] += $item['active_contracts'];
                $totals['pending_contracts'] += $item['pending_contracts'];
                $totals['total_value'] += $item['total_value'];
                $totals['active_value'] += $item['active_value'];
                $totals['total_units'] += $item['total_units'];
                $totals['active_units'] += $item['active_units'];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'breakdown' => $breakdown,
                    'trend' => $trend,
                    'po_stats' => $poStats,
                    'totals' => $totals
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Dashboard] getRentalTypeAnalytics error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching rental type analytics: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get KPI data and chart unit data for dashboard
     */
    public function getKpiData()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        try {
            $db = \Config\Database::connect();

            // Get total units
            $queryTotal = "SELECT COUNT(*) as total FROM inventory_unit";
            $unitsTotal = $db->query($queryTotal)->getRow()->total ?? 0;

            // Get units by status
            $queryByStatus = "
                SELECT su.status_unit, COUNT(*) as total
                FROM inventory_unit iu
                JOIN status_unit su ON iu.status_unit_id = su.id_status
                GROUP BY su.status_unit, su.id_status
                ORDER BY su.id_status
            ";
            $statusData = $db->query($queryByStatus)->getResultArray();

            // Parse status data
            $chartUnit = [
                'rented' => 0,
                'ready' => 0,
                'maintenance' => 0,
                'breakdown' => 0
            ];
            
            foreach ($statusData as $row) {
                $status = strtoupper(trim($row['status_unit']));
                // Map actual status names from database
                if (strpos($status, 'RENTAL_ACTIVE') !== false || strpos($status, 'RENTAL') !== false) {
                    $chartUnit['rented'] += (int)$row['total'];
                } elseif (strpos($status, 'AVAILABLE') !== false || strpos($status, 'STOCK') !== false || strpos($status, 'READY') !== false) {
                    $chartUnit['ready'] += (int)$row['total'];
                } elseif (strpos($status, 'MAINTENANCE') !== false || strpos($status, 'SERVICE') !== false || strpos($status, 'PREPARATION') !== false) {
                    $chartUnit['maintenance'] += (int)$row['total'];
                } elseif (strpos($status, 'BREAKDOWN') !== false || strpos($status, 'RUSAK') !== false || strpos($status, 'RETURNED') !== false) {
                    $chartUnit['breakdown'] += (int)$row['total'];
                }
            }

            // Get active contracts
            $queryContracts = "SELECT COUNT(*) as total FROM kontrak WHERE status = 'ACTIVE'";
            $activeContracts = $db->query($queryContracts)->getRow()->total ?? 0;

            // Get pending delivery (today and tomorrow) - use tanggal_kirim
            $queryDelivery = "
                SELECT COUNT(*) as total
                FROM delivery_instructions
                WHERE tanggal_kirim BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                AND status_di NOT IN ('SELESAI', 'DIBATALKAN', 'Selesai', 'Batal')
            ";
            $pendingDelivery = $db->query($queryDelivery)->getRow()->total ?? 0;

            // Calculate KPI
            $kpi = [
                'total_units' => (int)$unitsTotal,
                'total_rented' => $chartUnit['rented'],
                'units_breakdown' => $chartUnit['breakdown'],
                'active_contracts' => (int)$activeContracts,
                'pending_delivery' => (int)$pendingDelivery,
                'utilization_rate' => $unitsTotal > 0 ? ($chartUnit['rented'] / $unitsTotal) * 100 : 0
            ];

            return $this->response->setJSON([
                'success' => true,
                'kpi' => $kpi,
                'chartUnit' => $chartUnit
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Dashboard] getKpiData error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to load KPI data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get report delivery data for dashboard widget
     */
    public function getReportDelivery()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        try {
            // Get data from last 6 months to include all available data
            $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
            
            // Total delivered last 6 months
            $totalDelivered = $db->table('delivery_instructions')
                ->where('dibuat_pada >=', $sixMonthsAgo)
                ->countAllResults();

            // By Jenis Perintah (Command Type)
            $byJenisPerintah = $db->table('delivery_instructions di')
                ->select('jpk.nama as jenis_perintah, jpk.kode, COUNT(di.id) as total')
                ->join('jenis_perintah_kerja jpk', 'jpk.id = di.jenis_perintah_kerja_id', 'left')
                ->where('di.dibuat_pada >=', $sixMonthsAgo)
                ->groupBy('jpk.id')
                ->get()
                ->getResultArray();

            // By Tujuan Perintah (Command Destination)
            $byTujuanPerintah = $db->table('delivery_instructions di')
                ->select('tpk.nama as tujuan_perintah, tpk.kode, COUNT(di.id) as total')
                ->join('tujuan_perintah_kerja tpk', 'tpk.id = di.tujuan_perintah_kerja_id', 'left')
                ->where('di.dibuat_pada >=', $sixMonthsAgo)
                ->groupBy('tpk.id')
                ->get()
                ->getResultArray();

            // By Status Progress
            $byStatus = $db->table('delivery_instructions')
                ->select('status_di, COUNT(id) as total')
                ->where('dibuat_pada >=', $sixMonthsAgo)
                ->groupBy('status_di')
                ->get()
                ->getResultArray();

            // Prepare chart data (for bar chart) - use by status
            $chartLabels = [];
            $chartData = [];
            foreach ($byStatus as $status) {
                $chartLabels[] = $status['status_di'] ?? 'Unknown';
                $chartData[] = (int)$status['total'];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'total_delivered' => $totalDelivered,
                    'by_jenis_perintah' => $byJenisPerintah,
                    'by_tujuan_perintah' => $byTujuanPerintah,
                    'by_status' => $byStatus,
                    'chart_labels' => $chartLabels,
                    'chart_data' => $chartData
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getReportDelivery: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching delivery report: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get team performance data grouped by location type
     */
    public function getTeamPerformance()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        try {
            // Simplified query - focus on Work Orders only since SPK structure unclear
            $mechanics = $db->query("
                SELECT 
                    e.staff_name as name,
                    COALESCE(a.area_name, 'Unassigned') as area,
                    COALESCE(a.area_type, 'BRANCH') as area_type,
                    COUNT(DISTINCT CASE 
                        WHEN wo.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                        THEN wo.id END) as wo_week,
                    COUNT(DISTINCT CASE 
                        WHEN wo.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                        THEN wo.id END) as wo_month,
                    0 as spk_week,
                    0 as spk_month
                FROM employees e
                LEFT JOIN area_employee_assignments aea ON aea.employee_id = e.id AND aea.is_active = 1
                LEFT JOIN areas a ON a.id = aea.area_id
                LEFT JOIN work_orders wo ON wo.mechanic_id = e.id AND wo.order_type = 'COMPLAINT' AND wo.deleted_at IS NULL
                WHERE e.is_active = 1
                AND (e.staff_role LIKE '%MECHANIC%' OR e.staff_role LIKE '%MEKANIK%' OR e.staff_role LIKE '%Mechanic%')
                GROUP BY e.id, e.staff_name, a.area_name, a.area_type
                HAVING (wo_week > 0 OR wo_month > 0)
                ORDER BY a.area_type DESC, wo_month DESC
                LIMIT 20
            ")->getResultArray();

            // If no mechanics with MECHANIC role found, try to get any active employees with work orders
            if (empty($mechanics)) {
                $mechanics = $db->query("
                    SELECT 
                        e.staff_name as name,
                        COALESCE(a.area_name, 'Unassigned') as area,
                        COALESCE(a.area_type, 'BRANCH') as area_type,
                        COUNT(DISTINCT CASE 
                            WHEN wo.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                            THEN wo.id END) as wo_week,
                        COUNT(DISTINCT CASE 
                            WHEN wo.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                            THEN wo.id END) as wo_month,
                        0 as spk_week,
                        0 as spk_month
                    FROM employees e
                    LEFT JOIN area_employee_assignments aea ON aea.employee_id = e.id AND aea.is_active = 1
                    LEFT JOIN areas a ON a.id = aea.area_id
                    LEFT JOIN work_orders wo ON wo.mechanic_id = e.id AND wo.order_type = 'COMPLAINT' AND wo.deleted_at IS NULL
                    WHERE e.is_active = 1
                    GROUP BY e.id, e.staff_name, a.area_name, a.area_type
                    HAVING (wo_week > 0 OR wo_month > 0)
                    ORDER BY wo_month DESC
                    LIMIT 20
                ")->getResultArray();
            }

            // Group by location type
            $central = [];
            $branch = [];
            
            foreach ($mechanics as $mechanic) {
                $data = [
                    'name' => $mechanic['name'],
                    'area' => $mechanic['area'] ?? 'Unassigned',
                    'wo_week' => (int)$mechanic['wo_week'],
                    'wo_month' => (int)$mechanic['wo_month'],
                    'spk_week' => (int)$mechanic['spk_week'],
                    'spk_month' => (int)$mechanic['spk_month']
                ];
                
                $areaType = strtoupper($mechanic['area_type'] ?? '');
                
                if ($areaType === 'CENTRAL') {
                    $central[] = $data;
                } else {
                    $branch[] = $data;
                }
            }

            // Debug log
            log_message('debug', '[Dashboard] Team Performance - Total mechanics: ' . count($mechanics) . ', Central: ' . count($central) . ', Branch: ' . count($branch));

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'central' => array_slice($central, 0, 5),
                    'branch' => array_slice($branch, 0, 5)
                ],
                'debug' => [
                    'total_found' => count($mechanics),
                    'central_count' => count($central),
                    'branch_count' => count($branch)
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getTeamPerformance: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching team performance: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get quotations performance data
     */
    public function getQuotationsPerformance()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        try {
            // Total quotations in last 6 months
            $totalQuotations = $db->table('quotations')
                ->where('quotation_date >=', date('Y-m-d', strtotime('-6 months')))
                ->countAllResults();

            // Deals converted (is_deal = 1 or stage = ACCEPTED)
            $dealsConverted = $db->table('quotations')
                ->where('quotation_date >=', date('Y-m-d', strtotime('-6 months')))
                ->groupStart()
                    ->where('is_deal', 1)
                    ->orWhere('stage', 'ACCEPTED')
                ->groupEnd()
                ->countAllResults();

            // Calculate conversion rate
            $conversionRate = $totalQuotations > 0 ? round(($dealsConverted / $totalQuotations) * 100, 1) : 0;

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'total_quotations' => $totalQuotations,
                    'deals_converted' => $dealsConverted,
                    'conversion_rate' => $conversionRate
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getQuotationsPerformance: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching quotations performance: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get top spare parts used from WO complaints
     */
    public function getTopSpareParts()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        try {
            // Get top 5 spare parts used in complaint-type work orders
            $topParts = $db->query("
                SELECT 
                    wos.sparepart_name,
                    SUM(CASE WHEN wo.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN wos.quantity_used ELSE 0 END) as week_usage,
                    SUM(CASE WHEN wo.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN wos.quantity_used ELSE 0 END) as month_usage
                FROM work_order_spareparts wos
                INNER JOIN work_orders wo ON wo.id = wos.work_order_id
                WHERE wo.order_type = 'COMPLAINT'
                    AND wos.quantity_used > 0
                    AND wo.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    AND wo.deleted_at IS NULL
                GROUP BY wos.sparepart_name
                ORDER BY month_usage DESC
                LIMIT 5
            ")->getResultArray();

            $formattedData = [];
            foreach ($topParts as $part) {
                $formattedData[] = [
                    'name' => $part['sparepart_name'],
                    'week' => (int)$part['week_usage'],
                    'month' => (int)$part['month_usage']
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getTopSpareParts: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching top spare parts: ' . $e->getMessage()
            ]);
        }
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        // --- COMMAND CENTER / ENTERPRISE DASHBOARD DATA ---
        // Kita menggunakan Query Builder langsung untuk performa dan fleksibilitas data lintas-divisi.

        $db = \Config\Database::connect();
        
        // 1. KPI UTAMA: Fleet Utilization (Updated for Schema 2026)
        try {
            // Source: inventory_unit (per UnitAssetModel notes)
            $db = \Config\Database::connect();
            
            // Total Units
            $unitsTotal = $db->table('inventory_unit')->countAllResults(); // > 0

            if ($unitsTotal > 0) {
                 // Rent/Sewaan
                 // Strategy: Join status_unit because main table only has status_unit_id
                 $unitsRent = $db->table('inventory_unit')
                                ->join('status_unit', 'status_unit.id_status = inventory_unit.status_unit_id')
                                ->groupStart()
                                    ->like('status_unit.status_unit', 'Rent') 
                                    ->orLike('status_unit.status_unit', 'Sewaan')
                                ->groupEnd()
                                ->countAllResults();

                 // Breakdown/Rusak
                 $unitsBreakdown = $db->table('inventory_unit')
                                ->join('status_unit', 'status_unit.id_status = inventory_unit.status_unit_id')
                                ->groupStart()
                                    ->like('status_unit.status_unit', 'Breakdown')
                                    ->orLike('status_unit.status_unit', 'Rusak')
                                    ->orLike('status_unit.status_unit', 'Service')
                                ->groupEnd()
                                ->countAllResults();
            } else {
                 $unitsRent = 0;
                 $unitsBreakdown = 0;
                 // Fallback to Demo if table exists but empty?
                 // throw new \Exception("Empty Data"); 
            }
            
            $utilization = ($unitsTotal > 0) ? ($unitsRent / $unitsTotal) * 100 : 0;
        } catch (\Throwable $e) {
            // Catch table not found or other SQL errors -> Enable Demo Mode
            throw $e; 
        }

        // 2. MARKETING: Active Contracts
        // Schema: 'kontrak' table, 'status' (Enum: ACTIVE/EXPIRED/PENDING/CANCELLED)
        try {
             $activeContracts = $db->table('kontrak')->where('status', 'ACTIVE')->countAllResults();
        } catch (\Throwable $e) { $activeContracts = 0; }

        // 3. OPERATIONAL: Pending Logistics
        // Schema: 'delivery_instructions', status_di (NOT status)
        try {
            $pendingDelivery = $db->table('delivery_instructions')
                                ->whereNotIn('status_di', ['SELESAI', 'Completed', 'Cancelled', 'Batal'])
                                ->countAllResults();
        } catch (\Throwable $e) { $pendingDelivery = 0; }
        
        // Demo Switch (If really empty)
        if (!isset($unitsTotal) || $unitsTotal == 0) throw new \Exception("Switch to Demo (Empty)");

        $kpi = [
            'utilization_rate' => $utilization,
            'total_rented' => $unitsRent,
            'total_units' => $unitsTotal,
            'units_breakdown' => $unitsBreakdown,
            'active_contracts' => $activeContracts,
            'pending_delivery' => $pendingDelivery
        ];

        // 4. CHART DATA
        $chartUnit = [
            'rented' => $unitsRent,
            'ready' => $unitsTotal - $unitsRent - $unitsBreakdown, // Simplified
            'maintenance' => $unitsBreakdown,
            'breakdown' => 0
        ];

        // 5. ALERTS
        $alerts = [];
        
        // Low Stock (Schema: inventory_spareparts join sparepart on id_sparepart)
        try {
            $alerts['low_stock'] = $db->table('inventory_spareparts')
                                    ->select('sparepart.desc_sparepart as name, inventory_spareparts.stok as qty, 5 as min_stock')
                                    ->join('sparepart', 'sparepart.id_sparepart = inventory_spareparts.sparepart_id')
                                    ->where('inventory_spareparts.stok <=', 5)
                                    ->limit(5)
                                    ->get()->getResultArray();
        } catch (\Throwable $e) {
             $alerts['low_stock'] = [];
        }

        // Contract Expirations (Schema: kontrak -> customer_locations)
        try {
            $alerts['expiring_contracts'] = $db->table('kontrak')
                                            ->select('kontrak.*, customers.customer_name as customer, customer_locations.location_name')
                                            ->join('customer_locations', 'customer_locations.id = kontrak.customer_location_id', 'left')
                                            ->join('customers', 'customers.id = customer_locations.customer_id', 'left') // Assumption on customers.id
                                            ->where('tanggal_berakhir <=', date('Y-m-d', strtotime('+30 days')))
                                            ->where('status', 'ACTIVE')
                                            ->limit(5)
                                            ->get()->getResultArray();
            // Map to View keys
            foreach($alerts['expiring_contracts'] as &$con) {
                $con['end_date'] = $con['tanggal_berakhir'];
                $con['unit_code'] = $con['no_kontrak']; 
                if(empty($con['customer'])) $con['customer'] = 'N/A';
            }
        } catch (\Throwable $e) {
             $alerts['expiring_contracts'] = [];
        }
        
        // Maintenance (Mocked for now as logic is complex with work_order_statuses)
        $alerts['maintenance'] = [
             ['code' => 'FL-TOY-08', 'type' => 'Toyota 3T', 'next_service_date' => date('Y-m-d', strtotime('+3 days')), 'status' => 'Upcoming'],
             ['code' => 'EXC-KOM-02', 'type' => 'Komatsu PC200', 'next_service_date' => date('Y-m-d', strtotime('+5 days')), 'status' => 'Upcoming'],
        ];



        // Prepare View Data
        $data = [
            'title' => 'Executive Dashboard',
            'page_title' => 'Operational Command Center',
            'breadcrumbs' => ['/' => 'Dashboard'],
            'kpi' => $kpi,
            'charts' => [
                'unit_status' => $chartUnit,
                'sales_trend' => [ // Mock Sales Trend Data for visual impact
                    'labels' => ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                    'quotations' => [45, 52, 48, 60, 55, 65],
                    'contracts' => [30, 35, 32, 45, 40, 48]
                ]
            ],
            'alerts' => $alerts
        ];

        return view('dashboard', $data);
    }

    public function index_old_backup()
    {
        return redirect()->to('/dashboard');
    }

    private function unused_logic_storage() {
        // Code below is preserved from original file but currently unused
        $dummy = [
            'title' => 'Dashboard',
            'dashboard_active' => false
        ];
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
            
            // Count attachments from new 3-table structure
            $totalAttachmentsCount = $db->table('inventory_attachments')
                ->where('attachment_type_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $totalChargersCount = $db->table('inventory_chargers')
                ->where('charger_type_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $totalBateraiCount = $db->table('inventory_batteries')
                ->where('battery_type_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $totalAssets = $totalUnits + $totalAttachmentsCount + $totalChargersCount + $totalBateraiCount;
            
            // Active Contracts
            $totalContracts = $db->table('kontrak')
                ->where('status', 'ACTIVE')
                ->countAllResults();
            
            // Contract growth calculation
            $lastMonthContracts = $db->table('kontrak')
                ->where('status', 'ACTIVE')
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
            
            // Attachments (dari inventory_attachments table)
            $totalAttachments = $db->table('inventory_attachments')
                ->where('attachment_type_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $activeAttachments = $db->table('inventory_attachments')
                ->where('attachment_type_id IS NOT NULL', null, false)
                ->where('status', 'IN_USE')
                ->countAllResults();
            
            $attachmentUtilization = $totalAttachments > 0 
                ? round(($activeAttachments / $totalAttachments) * 100, 1) 
                : 0;
            
            // Chargers (dari inventory_chargers table)
            $totalChargers = $db->table('inventory_chargers')
                ->where('charger_type_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $activeChargers = $db->table('inventory_chargers')
                ->where('charger_type_id IS NOT NULL', null, false)
                ->where('status', 'IN_USE')
                ->countAllResults();
            
            // Baterai (dari inventory_batteries table)
            $totalBaterai = $db->table('inventory_batteries')
                ->where('battery_type_id IS NOT NULL', null, false)
                ->countAllResults();
            
            $activeBaterai = $db->table('inventory_batteries')
                ->where('battery_type_id IS NOT NULL', null, false)
                ->where('status', 'IN_USE')
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
                ->where('status', 'ACTIVE')
                ->countAllResults();
            
            // Expiring contracts (next 30 days)
            $expiringContracts = $db->table('kontrak')
                ->where('status', 'ACTIVE')
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
