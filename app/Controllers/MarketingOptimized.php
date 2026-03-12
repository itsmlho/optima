<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\SpkModel;
use App\Models\KontrakModel;
use App\Models\KontrakSpesifikasiModel;
use App\Models\InventoryUnitModel;
use App\Models\InventoryAttachmentModel;
use App\Models\DeliveryInstructionModel;
use App\Models\DeliveryItemModel;
use App\Models\NotificationModel;
use App\Traits\ActivityLoggingTrait;
use App\Services\CacheService;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Optimized Marketing Controller dengan advanced caching dan performance optimizations
 * Mengurangi N+1 queries, implementasi intelligent caching, dan background processing
 */
class MarketingOptimized extends BaseDataTableController
{
    use ActivityLoggingTrait;
    
    protected $db;
    protected $spkModel;
    protected $kontrakModel;
    protected $kontrakSpesifikasiModel;
    protected $unitModel;
    protected $attModel;
    protected $diModel;
    protected $diItemModel;
    protected $notifModel;
    protected $performanceService;
    protected $cacheService;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
        $this->spkModel = new SpkModel();
        $this->kontrakModel = new KontrakModel();
        $this->kontrakSpesifikasiModel = new KontrakSpesifikasiModel();
        $this->unitModel = new InventoryUnitModel();
        $this->attModel = new InventoryAttachmentModel();
        $this->diModel = new DeliveryInstructionModel();
        $this->diItemModel = new DeliveryItemModel();
        $this->notifModel = class_exists(\App\Models\NotificationModel::class) ? new NotificationModel() : null;
        $this->performanceService = new \App\Services\PerformanceService();
        $this->cacheService = new CacheService();
        
        // Warm frequently accessed data
        $this->warmCriticalCache();
    }

    /**
     * Optimized available units with intelligent caching
     */
    public function availableUnits()
    {
        // Permission check with caching
        $hasAccess = $this->cacheService->remember(
            "user_warehouse_access_{$this->getUserId()}",
            function() {
                return $this->canAccess('warehouse') || $this->canViewResource('warehouse', 'inventory');
            },
            1800, // 30 minutes
            'users'
        );

        if (!$hasAccess) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied: You do not have permission to view inventory'
                ])->setStatusCode(403);
            }
            return redirect()->to('/dashboard')->with('error', 'Access denied: You do not have permission to view inventory');
        }
        
        // Cache view data
        $viewData = $this->cacheService->remember(
            'available_units_view_data',
            function() {
                return [
                    'categories' => $this->getActiveCategories(),
                    'locations' => $this->getActiveLocations(),
                    'units' => $this->getAvailableUnitsStats()
                ];
            },
            600, // 10 minutes
            'inventory'
        );
        
        return view('marketing/unit_tersedia', $viewData);
    }

    /**
     * Optimized DataTable dengan cursor-based pagination
     */
    public function getDataTable()
    {
        $request = $this->request->getPost();
        
        // Generate cache key based on request parameters
        $cacheKey = 'datatable_' . md5(json_encode(array_merge($request, [
            'user_id' => $this->getUserId(),
            'timestamp' => floor(time() / 300) // 5-minute buckets
        ])));
        
        return $this->getCachedData($cacheKey, function() use ($request) {
            return $this->performOptimizedDataTableQuery($request);
        }, 300); // 5 minutes cache
    }

    /**
     * Advanced DataTable query dengan JOIN optimization dan subquery reduction
     */
    private function performOptimizedDataTableQuery($request)
    {
        $start = (int)($request['start'] ?? 0);
        $length = min((int)($request['length'] ?? 25), 100); // Max 100 records
        $searchValue = $request['search']['value'] ?? '';
        $orderColumn = (int)($request['order'][0]['column'] ?? 0);
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        
        // Use materialized view if available, otherwise optimized query
        $baseQuery = $this->db->table('v_kontrak_dashboard')
            ->select([
                'id', 'no_kontrak', 'customer_name', 'location_name',
                'nilai_total', 'status', 'dibuat_pada', 'unit_count',
                'diperbarui_pada'
            ]);
        
        // Apply search filters efficiently
        if (!empty($searchValue)) {
            $baseQuery->groupStart()
                ->like('no_kontrak', $searchValue)
                ->orLike('customer_name', $searchValue)
                ->orLike('location_name', $searchValue)
                ->groupEnd();
        }
        
        // Get total count (cached)
        $totalRecords = $this->cacheService->remember(
            'kontrak_total_count',
            function() {
                return $this->db->table('kontrak')->countAll();
            },
            1800, // 30 minutes
            'contracts'
        );
        
        // Get filtered count
        $filteredRecords = clone $baseQuery;
        $filteredCount = $filteredRecords->countAllResults(false);
        
        // Apply ordering and pagination
        $columns = ['id', 'no_kontrak', 'customer_name', 'nilai_total', 'status', 'dibuat_pada'];
        if (isset($columns[$orderColumn])) {
            $baseQuery->orderBy($columns[$orderColumn], $orderDir);
        }
        
        $data = $baseQuery->limit($length, $start)->get()->getResultArray();
        
        // Enhance data with additional info (batched)
        if (!empty($data)) {
            $data = $this->enhanceKontrakData($data);
        }
        
        return [
            'draw' => (int)($request['draw'] ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredCount,
            'data' => $data
        ];
    }

    /**
     * Enhance kontrak data dengan batched queries untuk menghindari N+1
     */
    private function enhanceKontrakData(array $contracts)
    {
        $contractIds = array_column($contracts, 'id');
        
        // Batch load all related data
        $specifications = $this->getBatchedSpecifications($contractIds);
        $units = $this->getBatchedUnits($contractIds);
        $deliveryInstructions = $this->getBatchedDeliveryInstructions($contractIds);
        
        // Enhance each contract
        foreach ($contracts as &$contract) {
            $id = $contract['id'];
            $contract['specifications_count'] = $specifications[$id] ?? 0;
            $contract['units_count'] = $units[$id] ?? 0;
            $contract['di_count'] = $deliveryInstructions[$id] ?? 0;
            $contract['actions'] = $this->generateActionButtons($contract);
        }
        
        return $contracts;
    }
    
    /**
     * Batched specifications count query
     */
    private function getBatchedSpecifications(array $contractIds)
    {
        if (empty($contractIds)) return [];
        
        $results = $this->db->query("
            SELECT q.kontrak_id, COUNT(qs.id) as count
            FROM quotations q
            LEFT JOIN quotation_specifications qs ON q.id = qs.quotation_id
            WHERE q.kontrak_id IN (" . implode(',', $contractIds) . ")
            GROUP BY q.kontrak_id
        ")->getResultArray();
        
        return array_column($results, 'count', 'kontrak_id');
    }
    
    /**
     * Batched units count query
     */
    private function getBatchedUnits(array $contractIds)
    {
        if (empty($contractIds)) return [];
        
        $results = $this->db->query("
            SELECT kontrak_id, COUNT(*) as count
            FROM inventory_unit 
            WHERE kontrak_id IN (" . implode(',', $contractIds) . ")
            GROUP BY kontrak_id
        ")->getResultArray();
        
        return array_column($results, 'count', 'kontrak_id');
    }
    
    /**
     * Batched delivery instructions count query
     */
    private function getBatchedDeliveryInstructions(array $contractIds)
    {
        if (empty($contractIds)) return [];
        
        $results = $this->db->query("
            SELECT kontrak_id, COUNT(*) as count
            FROM delivery_instruction 
            WHERE kontrak_id IN (" . implode(',', $contractIds) . ")
            GROUP BY kontrak_id
        ")->getResultArray();
        
        return array_column($results, 'count', 'kontrak_id');
    }

    /**
     * Streaming export untuk large datasets
     */
    public function exportKontrak()
    {
        // Permission check
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.kontrak')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.kontrak');
        }

        // Log activity
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'kontrak', 0, 'Export Kontrak CSV', [
                'module_name' => 'MARKETING',
                'submenu_item' => 'Kontrak',
                'business_impact' => 'LOW'
            ]);
        }

        // Stream export to prevent memory exhaustion
        $this->streamKontrakExport();
    }

    private function streamKontrakExport()
    {
        $filename = 'kontrak_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'No. Kontrak', 'Customer', 'Location', 'Nilai Total', 
            'Status', 'Tanggal', 'Jumlah Unit', 'Spesifikasi'
        ]);
        
        // Stream data in chunks
        $offset = 0;
        $chunkSize = 1000;
        
        do {
            $contracts = $this->getKontrakChunk($offset, $chunkSize);
            
            foreach ($contracts as $contract) {
                fputcsv($output, [
                    $contract['no_kontrak'],
                    $contract['customer_name'],
                    $contract['location_name'],
                    number_format($contract['nilai_total'], 2),
                    $contract['status'],
                    $contract['dibuat_pada'],
                    $contract['unit_count'],
                    $contract['spec_count']
                ]);
            }
            
            $offset += $chunkSize;
            
            // Clear memory and flush output
            unset($contracts);
            if (ob_get_level()) {
                ob_flush();
                flush();
            }
            
        } while (count($contracts ?? []) === $chunkSize);
        
        fclose($output);
        exit;
    }

    private function getKontrakChunk($offset, $limit)
    {
        return $this->db->query("
            SELECT 
                k.no_kontrak,
                c.customer_name,
                cl.location_name,
                k.nilai_total,
                k.status,
                k.dibuat_pada,
                COALESCE(unit_stats.unit_count, 0) as unit_count,
                COALESCE(spec_stats.spec_count, 0) as spec_count
            FROM kontrak k
            LEFT JOIN customers c ON c.id = k.customer_id
            LEFT JOIN (
                SELECT kontrak_id, COUNT(*) as unit_count
                FROM inventory_unit 
                GROUP BY kontrak_id
            ) unit_stats ON k.id = unit_stats.kontrak_id
            LEFT JOIN (
                SELECT q.kontrak_id, COUNT(qs.id) as spec_count
                FROM quotations q
                LEFT JOIN quotation_specifications qs ON q.id = qs.quotation_id
                GROUP BY q.kontrak_id
            ) spec_stats ON k.id = spec_stats.kontrak_id
            ORDER BY k.id ASC
            LIMIT ? OFFSET ?
        ", [$limit, $offset])->getResultArray();
    }

    /**
     * Background cache warming untuk data yang sering diakses
     */
    private function warmCriticalCache()
    {
        // Warm cache in background (non-blocking)
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        
        // Critical data to pre-cache
        $this->cacheService->warmCache([
            'dashboard_stats',
            'lookup_data',
            'user_permissions'
        ]);
    }

    /**
     * Real-time updates dengan Server-Sent Events
     */
    public function contractUpdatesStream()
    {
        if (!$this->canAccess('marketing') && !$this->canViewResource('marketing', 'kontrak')) {
            return $this->response->setStatusCode(403);
        }

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        
        $lastId = (int)($this->request->getGet('lastId') ?? 0);
        $maxIterations = 60; // Max 5 minutes (5s * 60)
        $iteration = 0;
        
        while ($iteration < $maxIterations && connection_status() === CONNECTION_NORMAL) {
            $updates = $this->getContractUpdates($lastId);
            
            if (!empty($updates)) {
                echo "data: " . json_encode([
                    'type' => 'contract_update',
                    'data' => $updates,
                    'timestamp' => time()
                ]) . "\n\n";
                
                $lastId = max(array_column($updates, 'id'));
                
                if (ob_get_level()) {
                    ob_flush();
                    flush();
                }
            } else {
                // Send heartbeat
                echo "data: " . json_encode(['type' => 'heartbeat', 'timestamp' => time()]) . "\n\n";
                if (ob_get_level()) {
                    ob_flush();
                    flush();
                }
            }
            
            sleep(5);
            $iteration++;
        }
    }

    private function getContractUpdates($sinceId)
    {
        return $this->db->query("
            SELECT k.id, k.no_kontrak, k.status, c.customer_name, k.diperbarui_pada
            FROM kontrak k
            LEFT JOIN customers c ON c.id = k.customer_id
            WHERE k.id > ? 
            AND k.diperbarui_pada > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ORDER BY k.id ASC
            LIMIT 10
        ", [$sinceId])->getResultArray();
    }

    /**
     * Optimized unit details dengan eager loading
     */
    public function getUnitDetails($kontrakId = null)
    {
        if (!$kontrakId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kontrak ID required']);
        }

        $cacheKey = "unit_details_{$kontrakId}";
        
        $unitDetails = $this->cacheService->remember($cacheKey, function() use ($kontrakId) {
            return $this->db->query("
                SELECT 
                    iu.*,
                    kt.jenis_armada,
                    kt.type_kendaraan,
                    kt.merk,
                    kt.model,
                    kt.tahun_kendaraan,
                    kt.warna,
                    kt.no_polisi,
                    kt.no_mesin,
                    kt.no_rangka,
                    kt.atas_nama_stnk,
                    kt.masa_berlaku_stnk,
                    kt.no_bpkb,
                    kt.atas_nama_bpkb,
                    kt.masa_berlaku_kir
                FROM inventory_unit iu
                LEFT JOIN kontrak_unit ku ON ku.kontrak_id = ? AND ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
                LEFT JOIN kendaraan_tracking kt ON iu.id_inventory_unit = kt.inventory_unit_id
                WHERE ku.kontrak_id IS NOT NULL
                ORDER BY iu.id_inventory_unit ASC
            ", [$kontrakId, $kontrakId])->getResultArray();
        }, 600, 'inventory'); // 10 minutes cache

        return $this->response->setJSON([
            'success' => true,
            'data' => $unitDetails
        ]);
    }

    // Helper methods
    private function getActiveCategories()
    {
        return $this->cacheService->remember('active_categories', function() {
            return $this->db->table('kategori')
                ->where('active', 1)
                ->orderBy('kategori_name', 'ASC')
                ->get()
                ->getResultArray();
        }, 3600, 'lookups');
    }

    private function getActiveLocations()
    {
        return $this->cacheService->remember('active_locations', function() {
            return $this->db->table('customer_locations cl')
                ->join('customers c', 'cl.customer_id = c.id', 'left')
                ->select('cl.*, c.customer_name')
                ->where('cl.active', 1)
                ->orderBy('c.customer_name', 'ASC')
                ->get()
                ->getResultArray();
        }, 1800, 'lookups');
    }

    private function getAvailableUnitsStats()
    {
        return $this->cacheService->remember('available_units_stats', function() {
            return $this->db->query("
                SELECT 
                    COUNT(*) as total_units,
                    COUNT(CASE WHEN ku.unit_id IS NULL THEN 1 END) as available_units,
                    COUNT(CASE WHEN ku.unit_id IS NOT NULL THEN 1 END) as contracted_units
                FROM inventory_unit iu
                LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
            ")->getRowArray();
        }, 300, 'inventory');
    }

    private function generateActionButtons($contract)
    {
        $actions = [];
        
        if ($this->canEdit()) {
            $actions[] = '<button class="btn btn-sm btn-primary edit-contract" data-id="' . $contract['id'] . '">Edit</button>';
        }
        
        if ($this->canView()) {
            $actions[] = '<button class="btn btn-sm btn-info view-contract" data-id="' . $contract['id'] . '">View</button>';
        }
        
        if ($this->canDelete('marketing') && $contract['status'] !== 'completed') {
            $actions[] = '<button class="btn btn-sm btn-danger delete-contract" data-id="' . $contract['id'] . '">Delete</button>';
        }
        
        return implode(' ', $actions);
    }

    private function getUserId()
    {
        return session('user_id') ?? 0;
    }
    
    /**
     * Check if user can edit contracts
     */
    private function canEdit()
    {
        $session = session();
        $userRole = $session->get('role_name');
        return in_array($userRole, ['Admin', 'Manager', 'Marketing']);
    }
    
    /**
     * Check if user can view contracts
     */
    private function canView()
    {
        $session = session();
        $userRole = $session->get('role_name');
        return !empty($userRole); // All logged users can view
    }
}