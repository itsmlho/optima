<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use App\Models\InventorySparepartModel;
use App\Models\InventoryUnitModel;
use App\Models\InventoryAttachmentModel;
use App\Models\InventoryBatteryModel;
use App\Models\InventoryChargerModel;
use App\Services\ExportService;
use Config\Database;

class Warehouse extends BaseController
{
    use ActivityLoggingTrait;
    
    protected $exportService;
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->exportService = new ExportService();
    }
    public function index()
    {
        // Check permission for accessing warehouse dashboard
        if (!$this->hasPermission('warehouse.access')) {
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to access warehouse dashboard');
        }
        
        // Extract attachment/unit ID from URL for auto-opening modal (from notification deep linking)
        $uri = service('uri');
        $autoOpenAttachmentId = null;
        $autoOpenUnitId = null;
        
        // Check if URL matches /warehouse/attachment/view/{id}
        $segments = $uri->getSegments();
        if (count($segments) >= 3 && $segments[1] === 'attachment' && $segments[2] === 'view' && isset($segments[3]) && is_numeric($segments[3])) {
            $autoOpenAttachmentId = (int)$segments[3];
        }
        // Check if URL matches /warehouse/unit/view/{id}
        elseif (count($segments) >= 3 && $segments[1] === 'unit' && $segments[2] === 'view' && isset($segments[3]) && is_numeric($segments[3])) {
            $autoOpenUnitId = (int)$segments[3];
        }
        
        $data = [
            'title' => 'Warehouse Division',
            'page_title' => 'Warehouse Division Dashboard',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse' => 'Warehouse Division'
            ],
            'warehouse_stats' => $this->getWarehouseStats(),
            'inventory_overview' => $this->getInventoryOverview(),
            'recent_transactions' => $this->getRecentTransactions(),
            'low_stock_alerts' => $this->getLowStockAlerts(),
            'autoOpenAttachmentId' => $autoOpenAttachmentId,
            'autoOpenUnitId' => $autoOpenUnitId
        ];

        return view('warehouse/index', $data);
    }

    /**
     * Get warehouse statistics for dashboard
     */
    protected function getWarehouseStats()
    {
        $unitModel = new InventoryUnitModel();
        $attachmentModel = new InventoryAttachmentModel();
        $sparepartModel = new InventorySparepartModel();

        return [
            'total_units' => $unitModel->countAllResults(false),
            'total_attachments' => $attachmentModel->countAllResults(false),
            'total_spareparts' => $sparepartModel->countAllResults(false),
        ];
    }

    /**
     * Get inventory overview - placeholder for future implementation
     */
    protected function getInventoryOverview()
    {
        return [];
    }

    /**
     * Get recent transactions - placeholder for future implementation
     */
    protected function getRecentTransactions()
    {
        return [];
    }

    /**
     * Get low stock alerts from spareparts
     */
    protected function getLowStockAlerts()
    {
        $sparepartModel = new InventorySparepartModel();

        // Get spareparts with low stock (stok <= 10)
        $lowStockItems = $sparepartModel
            ->select('inventory_spareparts.stok, sparepart.kode, sparepart.desc_sparepart')
            ->join('sparepart', 'sparepart.id_sparepart = inventory_spareparts.sparepart_id')
            ->where('inventory_spareparts.stok > 0')
            ->where('inventory_spareparts.stok <=', 10)
            ->findAll();

        $alerts = [];
        foreach ($lowStockItems as $item) {
            $alerts[] = [
                'item_name' => $item['desc_sparepart'] ?? '-',
                'item_code' => $item['kode'] ?? '-',
                'quantity' => $item['stok'] ?? 0,
            ];
        }

        return $alerts;
    }

    //INVENTORY SPAREPART
    public function inventSparepart()
    {
        // Check permission for viewing sparepart inventory
        if (!$this->hasPermission('warehouse.sparepart_inventory.view')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied: You do not have permission to view sparepart inventory'
                ])->setStatusCode(403);
            }
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to view sparepart inventory');
        }
        
        $inventoryModel = new InventorySparepartModel();

        if ($this->request->isAJAX()) {
            $start = $this->request->getPost('start') ?? 0;
            $length = $this->request->getPost('length') ?? 10;
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            
            $orderMap = ['id', 'kode', 'desc_sparepart', 'stok', 'lokasi_rak', 'updated_at'];
            $orderColumnIndex = $this->request->getPost('order')[0]['column'] ?? 0;
            $orderColumn = $orderMap[$orderColumnIndex] ?? 'id';
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';

            $data = $inventoryModel->getDataTable($start, $length, $orderColumn, $orderDir, $searchValue);
            
            return $this->response->setJSON([
                "draw" => $this->request->getPost('draw'),
                "recordsTotal" => $inventoryModel->countAllData(),
                "recordsFiltered" => $inventoryModel->countFiltered($searchValue),
                "data" => $data,
            ]);
        }
        
        $data = [
            'title' => 'Inventory - Stok Sparepart',
            'stats' => $inventoryModel->getStats(),
        ];

        return view('warehouse/inventory/invent_sparepart', $data);
    }

    public function getInventorySparepart($id)
    {
        $inventoryModel = new InventorySparepartModel();
        $data = $inventoryModel
            ->select('inventory_spareparts.*, s.kode, s.desc_sparepart')
            ->join('sparepart s', 's.id_sparepart = inventory_spareparts.sparepart_id')
            ->find($id);

        if ($data) {
            return $this->response->setJSON(['success' => true, 'data' => $data]);
        }
        return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Item not found']);
    }

    public function updateInventorySparepart($id)
    {
        $inventoryModel = new InventorySparepartModel();
        
        // Get old data for comparison
        $oldData = $inventoryModel->find($id);
        
        $data = [
            'stok' => $this->request->getPost('stok'),
            'lokasi_rak' => $this->request->getPost('lokasi_rak')
        ];

        if ($inventoryModel->update($id, $data)) {
            // Get sparepart details for notification (with minimum_stock)
            $sparepart = $inventoryModel
                ->select('inventory_spareparts.*, s.desc_sparepart, s.kode, s.minimum_stock')
                ->join('sparepart s', 's.id_sparepart = inventory_spareparts.sparepart_id', 'left')
                ->find($id);
            
            // Send notification: Sparepart Used/Updated
            helper('notification');
            if ($sparepart) {
                notify_sparepart_used([
                    'id' => $id,
                    'nama_sparepart' => $sparepart['desc_sparepart'] ?? '',
                    'kode' => $sparepart['kode'] ?? '',
                    'qty' => $data['stok'],
                    'lokasi' => $data['lokasi_rak'],
                    'updated_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/warehouse/spareparts')
                ]);
                
                // ⭐ REAL-TIME TRIGGER: Check stock level and send alerts
                $newStock = (int)$data['stok'];
                $minStock = (int)($sparepart['minimum_stock'] ?? 0);
                
                // Only send alert if minimum stock is configured
                if ($minStock > 0) {
                    if ($newStock == 0) {
                        // OUT OF STOCK - CRITICAL ALERT!
                        notify_sparepart_out_of_stock([
                            'id' => $id,
                            'nama_sparepart' => $sparepart['desc_sparepart'],
                            'kode' => $sparepart['kode'],
                            'lokasi' => $data['lokasi_rak'],
                            'url' => base_url('/warehouse/spareparts/' . $id)
                        ]);
                        
                        log_message('critical', "[Warehouse] STOCK OUT: {$sparepart['kode']} - {$sparepart['desc_sparepart']}");
                        
                    } elseif ($newStock <= $minStock) {
                        // LOW STOCK - WARNING ALERT
                        notify_sparepart_low_stock([
                            'id' => $id,
                            'nama_sparepart' => $sparepart['desc_sparepart'],
                            'kode' => $sparepart['kode'],
                            'stok_saat_ini' => $newStock,
                            'minimum_stock' => $minStock,
                            'lokasi' => $data['lokasi_rak'],
                            'url' => base_url('/warehouse/spareparts/' . $id)
                        ]);
                        
                        log_message('warning', "[Warehouse] LOW STOCK: {$sparepart['kode']} - Stock: {$newStock} (Min: {$minStock})");
                    }
                }
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Stok berhasil diperbarui.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui stok.', 'errors' => $inventoryModel->errors()]);
        }
    }

    // INVENTORY UNIT — legacy method removed; use Warehouse\UnitInventoryController instead

    /** Export CSV for unified inventory units */
    public function exportInventUnit()
    {
        // Activity Log: EXPORT inventory units
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'inventory_unit', 0, 'Export Inventory Unit CSV (Direct)', [
                'module_name' => 'WAREHOUSE',
                'submenu_item' => 'Inventory Management',
                'business_impact' => 'LOW'
            ]);
        }
        
        $db = Database::connect();
        if (!$db->tableExists('inventory_unit')) {
            return $this->response->setStatusCode(500)->setBody('Tabel inventory_unit tidak ditemukan');
        }
        $builder = $db->table('inventory_unit iu');
    $builder->select('iu.no_unit, iu.id_inventory_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_aset, iu.tanggal_kirim, iu.keterangan, ' .
            'COALESCE(su.status_unit, iu.status_unit_id) as status_unit, COALESCE(d.nama_departemen, "-") as nama_departemen, ' .
            'CONCAT(COALESCE(mu.merk_unit,"-"), " - ", COALESCE(mu.model_unit,"-")) AS model_unit_display');
        if ($db->tableExists('status_unit')) $builder->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left');
        if ($db->tableExists('departemen')) $builder->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left');
        if ($db->tableExists('model_unit')) $builder->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
        $rows = $builder->get()->getResultArray();
        $filename = 'inventory_units_'.date('Y-m-d_H-i-s').'.csv';
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        $headers = ['No Unit', 'ID', 'Serial Number', 'Tahun', 'Lokasi', 'Status Aset', 'Tanggal Kirim', 'Keterangan', 'Status Unit', 'Departemen', 'Model Unit'];
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, array_values($row));
        }
        fclose($output);
        return $this->response;
    }

    public function exportUnitInventory()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.inventory_unit')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.inventory_unit');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'inventory_unit', 0, 'Export Unit Inventory CSV', [
                'module_name' => 'WAREHOUSE',
                'submenu_item' => 'Unit Inventory',
                'business_impact' => 'LOW'
            ]);
        }
        
        // Get data from database
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                iu.*,
                d.nama_departemen,
                su.status_unit,
                mu.merk_unit, mu.model_unit,
                tu.tipe,
                k.kapasitas_unit,
                tm.tipe_mast,
                m.merk_mesin, m.model_mesin,
                ctr.no_kontrak, ctr.status AS status_kontrak,
                sl.status AS status_silo,
                jr.tipe_roda,
                tb.tipe_ban,
                v.jumlah_valve
            FROM inventory_unit iu
            LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
            LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
            LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
            LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
            LEFT JOIN mesin m ON m.id = iu.model_mesin_id
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
            LEFT JOIN kontrak ctr ON ctr.id = ku.kontrak_id
            LEFT JOIN silo sl ON sl.unit_id = iu.id_inventory_unit
            LEFT JOIN jenis_roda jr ON jr.id_roda = iu.roda_id
            LEFT JOIN tipe_ban tb ON tb.id_ban = iu.ban_id
            LEFT JOIN valve v ON v.id_valve = iu.valve_id
            ORDER BY iu.id_inventory_unit DESC
        ");
        $units = $query->getResultArray();
        
        // Prepare headers
        $headers = ['No', 'No Unit', 'Department', 'Status', 'Model', 'Type', 'Capacity', 'Mast', 'Engine', 'Contract', 'Silo Status', 'Wheel', 'Tire', 'Valve'];
        
        // Prepare data rows
        $data = [];
        $no = 1;
        foreach ($units as $unit) {
            $data[] = [
                $no++,
                $unit['no_unit'] ?? '',
                $unit['nama_departemen'] ?? '',
                $unit['status_unit'] ?? '',
                trim(($unit['merk_unit'] ?? '') . ' ' . ($unit['model_unit'] ?? '')),
                $unit['tipe'] ?? '',
                $unit['kapasitas_unit'] ?? '',
                $unit['tipe_mast'] ?? '',
                trim(($unit['merk_mesin'] ?? '') . ' ' . ($unit['model_mesin'] ?? '')),
                $unit['no_kontrak'] ?? '',
                $unit['status_silo'] ?? '',
                $unit['tipe_roda'] ?? '',
                $unit['tipe_ban'] ?? '',
                $unit['jumlah_valve'] ?? ''
            ];
        }
        
        // Export using ExportService
        return $this->exportService->exportToExcel($data, $headers, 'Unit Inventory Detailed');
    }

    public function confirmUnitToAsset($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success'=>false,'message'=>'Metode tidak diizinkan'])->setStatusCode(405);
        }
        $model = new InventoryUnitModel();
        $unit = $model->find($id);
        if (!$unit) return $this->response->setJSON(['success'=>false,'message'=>'Unit tidak ditemukan'])->setStatusCode(404);
        $currentStatus = (int)($unit['status_unit_id'] ?? 0);
        // Hanya boleh konfirmasi jika status saat ini 8 (Stok Non Aset)
        if ($currentStatus !== 8) {
            return $this->response->setJSON(['success'=>false,'message'=>'Hanya unit dengan status STOK NON ASET yang dapat dikonfirmasi menjadi aset.']);
        }
        // Tidak boleh jika sudah punya no_unit
        if (!empty($unit['no_unit'])) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unit sudah memiliki No Unit.']);
        }
        $noUnit = trim($this->request->getPost('no_unit') ?? '');
        if ($noUnit === '') {
            return $this->response->setJSON(['success'=>false,'message'=>'No Unit wajib diisi.']);
        }
        // Validasi unik sederhana
        $exists = $model->where('no_unit', $noUnit)->first();
        if ($exists) {
            return $this->response->setJSON(['success'=>false,'message'=>'No Unit sudah digunakan.']);
        }
        $updateData = [
            'no_unit' => $noUnit,
            'status_unit_id' => 7 // ubah ke Stock Aset setelah diberi nomor
        ];
        if ($model->update($id, $updateData)) {
            return $this->response->setJSON(['success'=>true,'message'=>'Unit berhasil dikonfirmasi menjadi ASET dengan No Unit baru.']);
        }
        return $this->response->setJSON(['success'=>false,'message'=>'Gagal mengkonfirmasi unit.']);
    }

    /**
     * Assign Non-Asset Number with Gap-Filling Strategy
     * Format: NA-001 to NA-500
     */
    public function assignNonAssetNumber()
    {
        try {
            $id = $this->request->getPost('id');
            $model = new \App\Models\InventoryUnitModel();
            
            $unit = $model->find($id);
            
            if (!$unit) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan'
                ]);
            }
            
            // Check if it's non-asset
            if ($unit['status_unit_id'] != 8) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit bukan Non-Asset (status_unit_id harus 8). Hanya unit Non-Asset yang bisa diberi nomor NA-xxx.'
                ]);
            }
            
            // Check if already has number
            if ($unit['no_unit_na']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit sudah memiliki nomor: ' . $unit['no_unit_na']
                ]);
            }
            
            // Generate new number (with gap-filling logic)
            $newNumber = $model->generateNonAssetNumber();
            
            // Update unit
            if ($model->update($id, ['no_unit_na' => $newNumber])) {
                log_message('info', '[Warehouse::assignNonAssetNumber] Unit ' . $id . ' assigned number: ' . $newNumber);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Nomor Non-Asset berhasil di-assign',
                    'no_unit_na' => $newNumber,
                    'display' => $newNumber
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate nomor Non-Asset'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::assignNonAssetNumber] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mengambil detail satu unit untuk modal edit.
     */
    public function getUnitDetail($id)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get complete unit data with customer location
            $data = $db->table('inventory_unit iu')
                ->select('iu.*, mu.merk_unit, mu.model_unit, ' .
                    'CONCAT("Forklift ", tu.jenis, " ", tu.tipe) as nama_tipe_unit, d.nama_departemen, su.status_unit as status_unit_name, ' .
                    'cl.location_name as customer_location_name, cl.city as customer_city, cl.address as customer_address, ' .
                    'c.customer_name, c.customer_code')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
                ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
                ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->where('iu.id_inventory_unit', $id)
                ->get()
                ->getRowArray();
            
            if ($data) {
                // Format data untuk modal
                $response_data = array_merge($data, [
                    'serial_number_po' => $data['serial_number'] ?? '-',
                    'status_unit' => $data['status_unit_id'],
                    'tanggal_update' => $data['updated_at'] ?? null,
                    // Customer location for display
                    'is_rental_active' => ($data['status_unit_id'] == 7),
                    'display_location' => null,
                    'location_label' => 'Lokasi'
                ]);
                
                // Set display location based on status
                if ($data['status_unit_id'] == 7 && !empty($data['customer_location_name'])) {
                    $response_data['display_location'] = $data['customer_location_name'] . ' - ' . ($data['customer_city'] ?? '');
                    $response_data['location_label'] = 'Lokasi Customer';
                } else {
                    $response_data['display_location'] = $data['lokasi_unit'];
                    $response_data['location_label'] = 'Lokasi';
                }
                
                return $this->response->setJSON(['success' => true, 'data' => $response_data]);
            }
            
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.']);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::getUnitDetail] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper function untuk menentukan lokasi attachment berdasarkan status dan unit
     */
    private function getAttachmentSmartLocation($attachmentData, $unitData) 
    {
        $attachmentStatus = $attachmentData['attachment_status'] ?? '';
        $unitStatusId = (int)($unitData['status_unit_id'] ?? 0);
        
        // Jika attachment sedang digunakan (USED), ikuti lokasi unit
        if ($attachmentStatus === 'USED') {
            if ($unitStatusId === 7) { // RENTAL_ACTIVE
                $customerInfo = $unitData['customer_name'] ? ' (' . $unitData['customer_name'] . ')' : '';
                return ($unitData['customer_location_name'] ?? 'Lokasi Customer') . $customerInfo;
            } elseif (in_array($unitStatusId, [1, 2, 3, 8, 9])) { // Stock/Maintenance/etc
                return $unitData['lokasi_unit'] ?? 'Gudang';
            } elseif (in_array($unitStatusId, [4, 5, 6])) { // Progress stages
                return 'Dalam Proses Pengiriman';
            }
        }
        
        // Untuk status lain, gunakan lokasi penyimpanan fisik
        $staticLocation = $attachmentData['lokasi_penyimpanan'] ?? '';
        
        // Smart default berdasarkan status attachment
        if (empty($staticLocation)) {
            switch ($attachmentStatus) {
                case 'AVAILABLE':
                    return 'Gudang Utama';
                case 'MAINTENANCE':
                    return 'Workshop';
                case 'RESERVED':
                    return 'Reserved';
                case 'RUSAK':
                    return 'Area Perbaikan';
                default:
                    return 'Lokasi Tidak Diketahui';
            }
        }
        
        return $staticLocation;
    }
    
    /**
     * Helper function untuk mendapatkan label lokasi attachment
     */
    private function getAttachmentLocationLabel($attachmentData) 
    {
        $attachmentStatus = $attachmentData['attachment_status'] ?? '';
        
        switch ($attachmentStatus) {
            case 'USED':
                return 'Lokasi Aktif';
            case 'AVAILABLE':
                return 'Lokasi Penyimpanan';
            case 'MAINTENANCE':
                return 'Lokasi Service';
            case 'RESERVED':
                return 'Lokasi Reserved';
            case 'RUSAK':
                return 'Lokasi Perbaikan';
            default:
                return 'Lokasi';
        }
    }
    private function getDisplayLocation($unitData) 
    {
        $statusId = (int)$unitData['status_unit_id'];
        
        // Jika unit sedang rental aktif (status 7), prioritaskan lokasi customer
        if ($statusId === 7 && !empty($unitData['customer_location_name'])) {
            $customerInfo = $unitData['customer_name'] ? ' (' . $unitData['customer_name'] . ')' : '';
            return $unitData['customer_location_name'] . $customerInfo;
        }
        
        // Untuk status lain, gunakan lokasi gudang internal
        return $unitData['lokasi_unit'] ?: 'Lokasi tidak diketahui';
    }
    
    /**
     * Helper function untuk mendapatkan label lokasi berdasarkan status
     */
    private function getLocationLabel($statusId) 
    {
        $statusId = (int)$statusId;
        
        if ($statusId === 7) {
            return 'Lokasi Customer';
        } elseif (in_array($statusId, [1, 2, 8])) {
            return 'Lokasi Gudang';
        } elseif (in_array($statusId, [3, 4, 5, 6])) {
            return 'Lokasi Staging';
        } else {
            return 'Lokasi';
        }
    }

    /**
     * Mengambil detail lengkap unit dengan semua informasi terkait
     */
    public function getUnitFullDetail($id)
    {
        try {
            $db = \Config\Database::connect();
            
            // Main unit query with comprehensive joins
            $query = $db->query('
                SELECT 
                    -- Unit Basic Info
                    iu.id_inventory_unit,
                    iu.no_unit,
                    iu.serial_number,
                    iu.status_unit_id,
                    iu.lokasi_unit,
                    iu.created_at as tanggal_masuk,
                    iu.updated_at as tanggal_update,
                    iu.keterangan,
                    iu.tahun_unit,
                    iu.harga_sewa_bulanan,
                    iu.harga_sewa_harian,
                    iu.aksesoris,
                    iu.tanggal_kirim,
                    
                    -- Unit Specifications
                    COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                    COALESCE(mu.model_unit, "Unknown") as model_unit,
                    COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "Unknown") as nama_tipe_unit,
                    COALESCE(kap.kapasitas_unit, "Unknown") as kapasitas_unit,
                    COALESCE(su.status_unit, "Unknown") as status_unit_name,
                    COALESCE(d.nama_departemen, "Unknown") as nama_departemen,
                    
                    -- Mast Info
                    iu.tinggi_mast,
                    iu.sn_mast,
                    COALESCE(tm.tipe_mast, "-") as tipe_mast,
                    
                    -- Engine Info  
                    iu.sn_mesin,
                    COALESCE(m.merk_mesin, "-") as merk_mesin,
                    COALESCE(m.model_mesin, "-") as model_mesin,
                    
                    -- Tire & Wheel Info
                    COALESCE(tb.tipe_ban, "-") as tipe_ban,
                    COALESCE(jr.tipe_roda, "-") as tipe_roda,
                    COALESCE(v.jumlah_valve, "-") as jumlah_valve,
                    
                    -- Contract & Customer Info (from junction table)
                    ku.kontrak_id,
                    c.id as customer_id,
                    COALESCE(k.no_kontrak, "-") as no_kontrak,
                    COALESCE(k.status, "-") as status_kontrak,
                    k.tanggal_mulai as kontrak_mulai,
                    k.tanggal_berakhir as kontrak_berakhir,
                    k.jenis_sewa,
                    COALESCE(c.customer_name, "-") as customer_name,
                    COALESCE(c.customer_code, "-") as customer_code,
                    COALESCE((SELECT cl.location_name FROM kontrak_unit ku2 JOIN customer_locations cl ON cl.id = ku2.customer_location_id WHERE ku2.kontrak_id = k.id LIMIT 1), "-") as customer_location_name,
                    COALESCE((SELECT cl.address FROM kontrak_unit ku2 JOIN customer_locations cl ON cl.id = ku2.customer_location_id WHERE ku2.kontrak_id = k.id LIMIT 1), "-") as customer_address,
                    COALESCE(cl.city, "-") as customer_city,
                    COALESCE(cl.contact_person, "-") as customer_contact,
                    COALESCE(cl.phone, "-") as customer_phone,
                    COALESCE(cl.email, "-") as customer_email,
                    
                    -- Area Info
                    iu.area_id,
                    COALESCE(a.area_name, "-") as area_name,
                    COALESCE(a.area_code, "-") as area_code,
                    COALESCE(a.area_description, "-") as area_description,
                    
                    -- PO Info
                    COALESCE(po.no_po, "-") as no_po,
                    po.tanggal_po,
                    COALESCE(po.status, "-") as status_po,
                    COALESCE(s.nama_supplier, "-") as nama_supplier,
                    
                    -- SPK & Delivery Info
                    iu.spk_id,
                    COALESCE(spk.nomor_spk, "-") as nomor_spk,
                    iu.delivery_instruction_id,
                    COALESCE(di.nomor_di, "-") as nomor_di,
                    iu.workflow_status,
                    iu.contract_disconnect_date,
                    iu.contract_disconnect_stage,
                    
                    -- SILO Info
                    silo.id_silo,
                    silo.status as silo_status,
                    silo.nomor_silo as silo_number,
                    silo.tanggal_terbit_silo as silo_issue_date,
                    silo.tanggal_expired_silo as silo_expiry_date,
                    silo.file_silo as silo_file_path,
                    silo.nama_pt_pjk3 as silo_pjk3_name,
                    silo.nomor_surat_keterangan_pjk3 as silo_pjk3_letter,
                    silo.lokasi_disnaker as silo_disnaker_location
                FROM inventory_unit iu
                LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
                LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
                LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
                LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
                LEFT JOIN kapasitas kap ON kap.id_kapasitas = iu.kapasitas_unit_id
                LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
                LEFT JOIN mesin m ON m.id = iu.model_mesin_id
                LEFT JOIN tipe_ban tb ON tb.id_ban = iu.ban_id
                LEFT JOIN jenis_roda jr ON jr.id_roda = iu.roda_id
                LEFT JOIN valve v ON v.id_valve = iu.valve_id
                LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0
                LEFT JOIN kontrak k ON k.id = ku.kontrak_id
                LEFT JOIN customers c ON c.id = k.customer_id
                LEFT JOIN areas a ON a.id = iu.area_id
                LEFT JOIN purchase_orders po ON po.id_po = iu.id_po
                LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id
                LEFT JOIN spk ON spk.id = iu.spk_id
                LEFT JOIN delivery_instructions di ON di.id = iu.delivery_instruction_id
                LEFT JOIN silo ON silo.unit_id = iu.id_inventory_unit
                WHERE iu.id_inventory_unit = ?
            ', [$id]);
            
            $data = $query->getRowArray();
            if (!$data) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.']);
            }
            
            // DEBUG: Log customer data
            log_message('debug', 'Unit Detail - Customer Data: ' . json_encode([
                'customer_id' => $data['customer_id'] ?? 'NULL',
                'customer_name' => $data['customer_name'] ?? 'NULL',
                'customer_location_id' => $data['customer_location_id'] ?? 'NULL',
                'customer_location_name' => $data['customer_location_name'] ?? 'NULL'
            ]));
            
            // Get attachments for this unit
            $attachmentQuery = $db->query('
                SELECT 
                    ia.id_inventory_attachment,
                    ia.tipe_item,
                    ia.sn_attachment,
                    ia.sn_baterai,
                    ia.sn_charger,
                    ia.kondisi_fisik,
                    ia.kelengkapan,
                    ia.catatan_fisik,
                    ia.lokasi_penyimpanan,
                    ia.attachment_status,
                    ia.tanggal_masuk as attachment_tanggal_masuk,
                    ia.notes as attachment_catatan,
                    COALESCE(CONCAT(att.tipe, " - ", att.merk, " ", att.model), "-") as attachment_name,
                    COALESCE(att.tipe, "-") as attachment_type,
                    "-" as baterai_type,
                    "-" as merk_baterai,
                    "-" as jenis_baterai,
                    "-" as charger_type,
                    "-" as merk_charger,
                    ia.status as status_attachment_name
                FROM inventory_attachments ia
                LEFT JOIN attachment att ON att.id_attachment = ia.attachment_type_id
                WHERE ia.inventory_unit_id = ?
                UNION ALL
                SELECT 
                    ib.notes as attachment_catatan,
                    "-" as attachment_name,
                    "-" as attachment_type,
                    COALESCE(bat.tipe_baterai, "-") as baterai_type,
                    COALESCE(bat.merk_baterai, "-") as merk_baterai,
                    COALESCE(bat.jenis_baterai, "-") as jenis_baterai,
                    "-" as charger_type,
                    "-" as merk_charger,
                    ib.status as status_attachment_name
                FROM inventory_batteries ib
                LEFT JOIN baterai bat ON bat.id = ib.battery_type_id  
                WHERE ib.inventory_unit_id = ?
                UNION ALL
                SELECT 
                    ic.notes as attachment_catatan,
                    "-" as attachment_name,
                    "-" as attachment_type,
                    "-" as baterai_type,
                    "-" as merk_baterai,
                    "-" as jenis_baterai,
                    COALESCE(ch.tipe_charger, "-") as charger_type,
                    COALESCE(ch.merk_charger, "-") as merk_charger,
                    ic.status as status_attachment_name
                FROM inventory_chargers ic
                LEFT JOIN charger ch ON ch.id_charger = ic.charger_type_id
                WHERE ic.inventory_unit_id = ?
                ORDER BY status_attachment_name
            ', [$id, $id, $id]);
            
            $attachments = $attachmentQuery->getResultArray();
            
            // Process attachments with smart location
            foreach ($attachments as &$attachment) {
                $attachment['smart_location'] = $this->getAttachmentSmartLocation($attachment, $data);
                $attachment['location_label'] = $this->getAttachmentLocationLabel($attachment);
                $attachment['is_following_unit'] = ($attachment['attachment_status'] === 'USED');
            }
            
            $data['attachments'] = $attachments;
            
            // Get contract specifications from quotation if contract exists
            if ($data['kontrak_id']) {
                // Get quotation linked to this contract
                $quotationQuery = $db->query('
                    SELECT q.id_quotation as quotation_id
                    FROM quotations q
                    WHERE q.created_contract_id = ?
                    LIMIT 1
                ', [$data['kontrak_id']]);
                
                $quotation = $quotationQuery->getRowArray();
                
                if ($quotation) {
                    // Get quotation specifications
                    $kontrakSpekQuery = $db->query('
                        SELECT 
                            qs.*,
                            COALESCE(tu.tipe, "-") as spek_tipe_unit,
                            COALESCE(ku.kapasitas_unit, "-") as spek_kapasitas
                        FROM quotation_specifications qs
                        LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = qs.tipe_unit_id
                        LEFT JOIN kapasitas ku ON ku.id_kapasitas = qs.kapasitas_id
                        WHERE qs.id_quotation = ?
                    ', [$quotation['quotation_id']]);
                    
                    $data['kontrak_spesifikasi'] = $kontrakSpekQuery->getResultArray();
                } else {
                    $data['kontrak_spesifikasi'] = [];
                }
            } else {
                $data['kontrak_spesifikasi'] = [];
            }
            
            // Add processed location display data
            $data['display_location'] = $this->getDisplayLocation($data);
            $data['location_label'] = $this->getLocationLabel($data['status_unit_id']);
            $data['is_rental_active'] = ((int)$data['status_unit_id'] === 7);
            
            return $this->response->setJSON(['success' => true, 'data' => $data]);
            
        } catch (\Exception $e) {
            log_message('error', 'getUnitFullDetail error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengambil history/timeline lengkap unit dari semua sumber data
     * SPK Persiapan, DI, Work Order, Sparepart, Kontrak, Attachment
     */
    public function getUnitHistory($id)
    {
        try {
            $db = \Config\Database::connect();
            $logModel = new \App\Models\SystemActivityLogModel();
            $timeline = [];

            // ── A. Baca dari system_activity_log (real-time logged events) ──
            // 1. Log langsung di tabel inventory_unit
            $directLogs = $logModel->getRecordHistory('inventory_unit', (int)$id, 100);

            // 2. Log yang terkait via related_entities JSON
            $relatedLogs = $logModel->findByRelatedEntity('inventory_unit', [(int)$id], 100);

            // 3. Merge unik (hindari duplikat berdasarkan id)
            $allLogIds = [];
            $allLogs = [];
            foreach (array_merge($directLogs, $relatedLogs) as $log) {
                if (!isset($allLogIds[$log['id']])) {
                    $allLogIds[$log['id']] = true;
                    $allLogs[] = $log;
                }
            }

            // Map logs ke timeline format
            $actionMap = [
                'item_created'       => ['icon' => 'fas fa-plus-circle',  'color' => 'success',   'label' => 'Unit Masuk Inventory'],
                'unit_updated'       => ['icon' => 'fas fa-edit',          'color' => 'secondary', 'label' => 'Data Unit Diperbarui'],
                'UPDATE'             => ['icon' => 'fas fa-edit',          'color' => 'secondary', 'label' => 'Data Unit Diperbarui'],
                'attach_to_unit'     => ['icon' => 'fas fa-link',          'color' => 'primary',   'label' => 'Attachment Dipasang'],
                'detach_from_unit'   => ['icon' => 'fas fa-unlink',        'color' => 'warning',   'label' => 'Attachment Dilepas'],
                'auto_detach'        => ['icon' => 'fas fa-exchange-alt',  'color' => 'info',      'label' => 'Auto-Swap Attachment'],
                'swap_unit'          => ['icon' => 'fas fa-random',        'color' => 'info',      'label' => 'Attachment Dipindah'],
                'CREATE'             => ['icon' => 'fas fa-plus-circle',   'color' => 'success',   'label' => 'Data Dibuat'],
                'WORKFLOW_CHANGE'    => ['icon' => 'fas fa-project-diagram','color' => 'primary',  'label' => 'Perubahan Status'],
            ];

            foreach ($allLogs as $log) {
                $actionType = strtolower($log['action_type'] ?? 'update');
                $map = $actionMap[$log['action_type'] ?? ''] ?? $actionMap[strtoupper($log['action_type'] ?? '')] ?? null;

                // Decode old/new values untuk diff display
                $oldVals = !empty($log['old_values']) ? json_decode($log['old_values'], true) : null;
                $newVals = !empty($log['new_values']) ? json_decode($log['new_values'], true) : null;
                $description = $log['action_description'] ?? '';

                if ($oldVals && $newVals) {
                    $diffs = [];
                    foreach ($newVals as $k => $v) {
                        if (isset($oldVals[$k]) && $oldVals[$k] != $v) {
                            $diffs[] = "{$k}: {$oldVals[$k]} → {$v}";
                        }
                    }
                    if ($diffs) {
                        $description .= ' (' . implode(', ', $diffs) . ')';
                    }
                }

                $timeline[] = [
                    'type'        => $actionType,
                    'icon'        => $map['icon']  ?? 'fas fa-circle',
                    'color'       => $map['color'] ?? 'secondary',
                    'title'       => $map['label'] ?? ucwords(str_replace('_', ' ', $log['action_type'] ?? '')),
                    'description' => $description,
                    'date'        => $log['created_at'] ?? null,
                    'ref_number'  => null,
                    'user'        => $log['username'] ?? $log['user_full_name'] ?? null,
                    'source'      => 'log',
                ];
            }

            // ── B. Seed events dari data statis (untuk data sebelum logging) ──
            // Query unit dasar with current contract from junction table
            $unit = $db->query('
                SELECT iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.created_at,
                       ku.kontrak_id, iu.workflow_status
                FROM inventory_unit iu
                LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0
                WHERE iu.id_inventory_unit = ?
            ', [$id])->getRowArray();

            if (!$unit) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.'])->setStatusCode(404);
            }

            // B1. Unit masuk inventory (created date) — hanya jika belum ada log item_created
            if ($unit['created_at']) {
                $exists = array_filter($timeline, fn($t) => $t['type'] === 'item_created' || $t['type'] === 'create');
                if (empty($exists)) {
                    $timeline[] = [
                        'type'        => 'item_created',
                        'icon'        => 'fas fa-plus-circle',
                        'color'       => 'success',
                        'title'       => 'Unit Terdaftar di Inventory',
                        'description' => 'No. Unit: ' . ($unit['no_unit'] ?? '-') . ' | SN: ' . ($unit['serial_number'] ?? '-'),
                        'date'        => $unit['created_at'],
                        'ref_number'  => null,
                        'source'      => 'seed',
                    ];
                }
            }

            // B2. SPK Persiapan terkait unit ini (via spk_unit pivot jika ada)
            try {
                $spkRows = $db->query('
                    SELECT s.id_spk, s.no_spk, s.tanggal_persiapan, s.tujuan_persiapan
                    FROM spk_unit su
                    JOIN spk s ON s.id_spk = su.spk_id
                    WHERE su.unit_id = ?
                    LIMIT 5
                ', [$id])->getResultArray();

                foreach ($spkRows as $spk) {
                    $timeline[] = [
                        'type'        => 'spk',
                        'icon'        => 'fas fa-clipboard-list',
                        'color'       => 'warning',
                        'title'       => 'SPK Persiapan Unit',
                        'description' => 'No. SPK: ' . ($spk['no_spk'] ?? '-') . ($spk['tujuan_persiapan'] ? ' | ' . $spk['tujuan_persiapan'] : ''),
                        'date'        => $spk['tanggal_persiapan'] ?? $unit['created_at'],
                        'ref_number'  => $spk['no_spk'],
                        'source'      => 'seed',
                    ];
                }
            } catch (\Exception $e) {
                // tabel spk_unit tidak ada atau kolom berubah — skip seed ini
                log_message('info', '[getUnitHistory] SPK seed skip: ' . $e->getMessage());
            }

            // B3. Delivery Instruction terkait unit ini (via di_unit pivot atau kolom di kontrak_unit)
            try {
                $diRows = $db->query('
                    SELECT di.id_di, di.no_di, di.tanggal_di
                    FROM delivery_instruction_unit diu
                    JOIN delivery_instructions di ON di.id_di = diu.di_id
                    WHERE diu.unit_id = ?
                    LIMIT 5
                ', [$id])->getResultArray();

                foreach ($diRows as $di) {
                    $timeline[] = [
                        'type'        => 'di',
                        'icon'        => 'fas fa-truck',
                        'color'       => 'primary',
                        'title'       => 'Delivery Instruction',
                        'description' => 'No. DI: ' . ($di['no_di'] ?? '-'),
                        'date'        => $di['tanggal_di'],
                        'ref_number'  => $di['no_di'],
                        'source'      => 'seed',
                    ];
                }
            } catch (\Exception $e) {
                log_message('info', '[getUnitHistory] DI seed skip: ' . $e->getMessage());
            }


            // B4-current. Kontrak aktif saat ini (dari kontrak_unit junction table - source of truth)
            // Hanya seed jika belum ada log kontrak di activity_log
            $hasKontrakLog = !empty(array_filter($timeline, fn($t) => str_contains($t['type'] ?? '', 'kontrak')));
            if (!empty($unit['kontrak_id']) && !$hasKontrakLog) {
                try {
                    $aktifKontrak = $db->query('
                        SELECT k.id, k.no_kontrak, ku.tanggal_mulai
                        FROM kontrak k
                        INNER JOIN kontrak_unit ku ON ku.kontrak_id = k.id AND ku.unit_id = ? AND ku.is_temporary = 0
                        WHERE k.id = ? AND ku.status IN ("ACTIVE","TEMP_ACTIVE")
                        LIMIT 1
                    ', [$id, $unit['kontrak_id']])->getRowArray();

                    if ($aktifKontrak) {
                        $timeline[] = [
                            'type'        => 'kontrak',
                            'icon'        => 'fas fa-file-contract',
                            'color'       => 'success',
                            'title'       => 'Kontrak Sewa',
                            'description' => 'No. Kontrak: ' . ($aktifKontrak['no_kontrak'] ?? '-') . ' | Aktif',
                            'date'        => $aktifKontrak['tanggal_mulai'] ?? $unit['created_at'],
                            'ref_number'  => $aktifKontrak['no_kontrak'],
                            'source'      => 'seed',
                        ];
                    }
                } catch (\Exception $e) {
                    log_message('info', '[getUnitHistory] Kontrak-current seed skip: ' . $e->getMessage());
                }
            }

            // B4. Kontrak historis terkait unit ini (via kontrak_unit pivot)
            // Hanya ambil yang sudah selesai/ditarik — status ACTIVE di kontrak_unit
            // rentan dirty data, dan kontrak aktif seharusnya ter-log di system_activity_log.
            try {
                $kontrakRows = $db->query('
                    SELECT k.id, k.no_kontrak, ku.tanggal_mulai, ku.tanggal_selesai,
                           ku.status as ku_status, ku.tanggal_tarik
                    FROM kontrak_unit ku
                    JOIN kontrak k ON k.id = ku.kontrak_id
                    WHERE ku.unit_id = ?
                      AND ku.status IN (\'PULLED\', \'REPLACED\', \'INACTIVE\', \'TEMP_ENDED\')
                    ORDER BY COALESCE(ku.tanggal_tarik, ku.tanggal_selesai, ku.tanggal_mulai) ASC
                    LIMIT 10
                ', [$id])->getResultArray();

                $statusLabel = [
                    'PULLED'     => 'Ditarik',
                    'REPLACED'   => 'Diganti Unit Lain',
                    'INACTIVE'   => 'Kontrak Berakhir',
                    'TEMP_ENDED' => 'Penggantian Sementara Selesai',
                ];

                foreach ($kontrakRows as $kontrak) {
                    $stLabel = $statusLabel[$kontrak['ku_status']] ?? $kontrak['ku_status'];
                    $endDate  = $kontrak['tanggal_tarik'] ?? $kontrak['tanggal_selesai'] ?? $kontrak['tanggal_mulai'];
                    $timeline[] = [
                        'type'        => 'kontrak',
                        'icon'        => 'fas fa-file-contract',
                        'color'       => 'secondary',
                        'title'       => 'Kontrak Sewa Selesai',
                        'description' => 'No. Kontrak: ' . ($kontrak['no_kontrak'] ?? '-') . ' | ' . $stLabel,
                        'date'        => $endDate,
                        'ref_number'  => $kontrak['no_kontrak'],
                        'source'      => 'seed',
                    ];
                }
            } catch (\Exception $e) {
                log_message('info', '[getUnitHistory] Kontrak seed skip: ' . $e->getMessage());
            }


            // ── C. Sort semua events secara kronologis (terlama dulu) ──

            usort($timeline, function ($a, $b) {
                $ta = !empty($a['date']) ? strtotime($a['date']) : 0;
                $tb = !empty($b['date']) ? strtotime($b['date']) : 0;
                return $ta - $tb;
            });

            return $this->response->setJSON([
                'success'  => true,
                'total'    => count($timeline),
                'timeline' => $timeline,
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Warehouse::getUnitHistory] Error: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat history: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }



    /**
     * Memperbarui data stok unit.
     * Support cross-division access: Service bisa update inventory setelah maintenance
     */
    public function updateUnit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }
        
        // Check permission: Warehouse punya warehouse.manage, Service punya warehouse.inventory.manage
        if (!$this->canManage('warehouse') && !$this->canManageResource('warehouse', 'inventory')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: You do not have permission to update inventory'
            ])->setStatusCode(403);
        }
        
        $inventoryUnitModel = new InventoryUnitModel();
        $data = [
            'status_unit_id' => $this->request->getPost('status_unit'),
            'lokasi_unit' => $this->request->getPost('lokasi_unit'),
        ];
        $rules = [
            'status_unit' => 'required|in_list[7,3,9,2]',
            'lokasi_unit' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'message' => $this->validator->getErrors()]);
        }
        // Ambil data lama sebelum update untuk log diff
        $oldUnit = $inventoryUnitModel->find($id);
        if ($inventoryUnitModel->update($id, $data)) {
            // Get unit details for notification
            $unit = $inventoryUnitModel->find($id);
            
            // Send notification: Inventory Unit Status Changed
            helper('notification');
            if ($unit) {
                $statusNames = [
                    2 => 'STOCK NON-ASET',
                    3 => 'RENTAL',
                    7 => 'STOCK FISIK',
                    9 => 'SOLD'
                ];
                
                notify_inventory_unit_status_changed([
                    'id' => $id,
                    'no_unit' => $unit['no_unit'] ?? '',
                    'old_status' => $statusNames[$oldUnit['status_unit_id'] ?? 2] ?? 'Unknown', // Use oldUnit for old status
                    'new_status' => $statusNames[$data['status_unit_id']] ?? 'Unknown',
                    'lokasi' => $data['lokasi_unit'],
                    'updated_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/warehouse/units')
                ]);
            }

            // Log perubahan data unit
            $this->logUpdate('inventory_unit', (int)$id,
                ['status_unit_id' => $oldUnit['status_unit_id'] ?? null, 'lokasi_unit' => $oldUnit['lokasi_unit'] ?? null],
                ['status_unit_id' => $data['status_unit_id'], 'lokasi_unit' => $data['lokasi_unit']],
                [
                    'description' => 'Data unit diperbarui: status & lokasi',
                    'workflow_stage' => 'unit_updated',
                    'relations' => ['inventory_unit' => [(int)$id]]
                ]
            );
            
            return $this->response->setJSON(['success' => true, 'message' => 'Data unit berhasil diperbarui.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui data.']);
    }

    /**
     * Menghapus permanen satu unit inventory.
     * Aturan bisnis sederhana: tidak boleh menghapus unit dengan status RENTAL (3).
     * (Dapat diperluas nanti: mencegah hapus SOLD (9) atau unit yang memiliki relasi transaksi.)
     */
    public function deleteUnit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Akses ditolak.'
            ]);
        }

        $model = new InventoryUnitModel();
        $unit = $model->find($id);
        if (!$unit) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Unit tidak ditemukan.'
            ]);
        }

        // Cegah hapus jika status RENTAL
        if ((int)($unit['status_unit_id'] ?? 0) === 3) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit sedang status RENTAL dan tidak dapat dihapus.'
            ]);
        }

        try {
            if ($model->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Unit berhasil dihapus.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus unit.',
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Throwable $e) {
            // Kemungkinan kegagalan karena constraint (FK). Beri pesan ramah.
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Tidak dapat menghapus unit. Pastikan tidak ada relasi aktif. Detail: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Debug endpoint untuk troubleshooting DataTables
     */
    public function debugInventUnit()
    {
        $inventoryUnitModel = new InventoryUnitModel();
        try {
            $count = $inventoryUnitModel->countAllResults();
            $sample = $inventoryUnitModel->limit(1)->findAll();
            $testData = $inventoryUnitModel->getDataTable(0, 5, 'iu.created_at', 'desc', '', null);
            return $this->response->setJSON([
                'success' => true,
                'total_records' => $count,
                'sample_record' => $sample,
                'test_datatable' => $testData,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}