<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use App\Models\InventorySparepartModel;
use App\Models\InventoryUnitModel;
use App\Models\InventoryAttachmentModel;
use Config\Database;

class Warehouse extends BaseController
{
    use ActivityLoggingTrait;
    public function index()
    {
        // Check permission for accessing warehouse dashboard
        if (!$this->hasPermission('warehouse.access')) {
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to access warehouse dashboard');
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
            'low_stock_alerts' => $this->getLowStockAlerts()
        ];

        return view('warehouse/index', $data);
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
        $data = [
            'stok' => $this->request->getPost('stok'),
            'lokasi_rak' => $this->request->getPost('lokasi_rak')
        ];

        if ($inventoryModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Stok berhasil diperbarui.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui stok.', 'errors' => $inventoryModel->errors()]);
        }
    }

    // INVENTORY UNIT
    public function inventUnit()
    {
        // Check permission for viewing unit inventory
        if (!$this->hasPermission('warehouse.unit_inventory.view')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied: You do not have permission to view unit inventory'
                ])->setStatusCode(403);
            }
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to view unit inventory');
        }
        
        $inventoryUnitModel = new InventoryUnitModel();
        if ($this->request->isAJAX()) {
            try {
                $start = $this->request->getPost('start') ?? 0;
                $length = $this->request->getPost('length') ?? 10;
                $searchValue = $this->request->getPost('search')['value'] ?? '';
                $statusFilter = $this->request->getPost('status_unit'); // param tetap sama dari front-end
                $departemenFilter = $this->request->getPost('departemen_id');

                $orderMap = [
                    'iu.no_unit', // manual asset number (nullable)
                    'iu.id_inventory_unit', // internal id
                    'iu.serial_number',
                    'mu.merk_unit',
                    'mu.model_unit',
                    'tu.tipe', // kolom nama_tipe_unit sudah tidak ada, gunakan tipe
                    'su.status_unit',
                    'iu.lokasi_unit',
                    'iu.created_at'
                ];
                $orderColumnIndex = $this->request->getPost('order')[0]['column'] ?? 0;
                $orderColumn = $orderMap[$orderColumnIndex] ?? 'iu.created_at';
                $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';

                $data = $inventoryUnitModel->getDataTable($start, $length, $orderColumn, $orderDir, $searchValue, $statusFilter, $departemenFilter);
                $recordsFiltered = $inventoryUnitModel->countFiltered($searchValue, $statusFilter, $departemenFilter);
                $recordsTotal = $inventoryUnitModel->countAllData();

                // Hitung dynamic counts untuk semua status (abaikan filter status, tapi hormati search + departemen)
                $db = Database::connect();
                $countBuilder = $db->table('inventory_unit iu');
                // Joins minimal untuk pencarian konsisten
                if ($searchValue) {
                    $countBuilder->groupStart()
                        ->like('iu.serial_number', $searchValue)
                        ->orLike('iu.lokasi_unit', $searchValue)
                        ->groupEnd();
                }
                if ($departemenFilter) $countBuilder->where('iu.departemen_id', $departemenFilter);
                
                $allFiltered = (clone $countBuilder)->countAllResults();
                
                // Stock Unit counts: AVAILABLE_STOCK (1), STOCK_NON_ASET (2), BOOKED (3), RETURNED (9)
                $availableStockCount = (clone $countBuilder)->where('iu.status_unit_id', 1)->countAllResults();
                $stockNonAsetCount   = (clone $countBuilder)->where('iu.status_unit_id', 2)->countAllResults();
                $bookedCount         = (clone $countBuilder)->where('iu.status_unit_id', 3)->countAllResults();
                $returnedCount       = (clone $countBuilder)->where('iu.status_unit_id', 9)->countAllResults();
                
                // Rental counts: RENTAL_ACTIVE (7), RENTAL_INACTIVE (11)
                $rentalActiveCount   = (clone $countBuilder)->where('iu.status_unit_id', 7)->countAllResults();
                $rentalInactiveCount = (clone $countBuilder)->where('iu.status_unit_id', 11)->countAllResults();
                
                // Progress counts: IN_PREPARATION (4), READY_TO_DELIVER (5), IN_DELIVERY (6), MAINTENANCE (8)
                $inPreparationCount  = (clone $countBuilder)->where('iu.status_unit_id', 4)->countAllResults();
                $readyToDeliverCount = (clone $countBuilder)->where('iu.status_unit_id', 5)->countAllResults();
                $inDeliveryCount     = (clone $countBuilder)->where('iu.status_unit_id', 6)->countAllResults();
                $maintenanceCount    = (clone $countBuilder)->where('iu.status_unit_id', 8)->countAllResults();
                
                // Sold count: SOLD (10)
                $soldCount           = (clone $countBuilder)->where('iu.status_unit_id', 10)->countAllResults();
                
                $dynamicStats = [
                    'total'              => $allFiltered,
                    'available_stock'    => $availableStockCount,
                    'stock_non_aset'     => $stockNonAsetCount,
                    'booked'             => $bookedCount,
                    'returned'           => $returnedCount,
                    'rental_active'      => $rentalActiveCount,
                    'rental_inactive'    => $rentalInactiveCount,
                    'in_preparation'     => $inPreparationCount,
                    'ready_to_deliver'   => $readyToDeliverCount,
                    'in_delivery'        => $inDeliveryCount,
                    'maintenance'        => $maintenanceCount,
                    'sold'               => $soldCount,
                ];

                return $this->response->setJSON([
                    'draw' => intval($this->request->getPost('draw')),
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                    'stats' => $dynamicStats,
                    'csrf_hash' => csrf_hash()
                ]);
            } catch (\Exception $e) {
                return $this->response->setStatusCode(500)->setJSON([
                    'draw' => intval($this->request->getPost('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                    'csrf_hash' => csrf_hash()
                ]);
            }
        }
        $stats = $inventoryUnitModel->getStats();
        // Tambah count status baru sesuai database terbaru dan kategori
        try {
            $dbTmp = Database::connect();
            if ($dbTmp->tableExists('inventory_unit')) {
                // Individual status counts
                $stats['available_stock'] = $dbTmp->table('inventory_unit')->where('status_unit_id', 1)->countAllResults(); // AVAILABLE_STOCK
                $stats['stock_non_aset']  = $dbTmp->table('inventory_unit')->where('status_unit_id', 2)->countAllResults(); // STOCK_NON_ASET
                $stats['booked']          = $dbTmp->table('inventory_unit')->where('status_unit_id', 3)->countAllResults(); // BOOKED
                $stats['in_preparation']  = $dbTmp->table('inventory_unit')->where('status_unit_id', 4)->countAllResults(); // IN_PREPARATION
                $stats['ready_to_deliver'] = $dbTmp->table('inventory_unit')->where('status_unit_id', 5)->countAllResults(); // READY_TO_DELIVER
                $stats['in_delivery']     = $dbTmp->table('inventory_unit')->where('status_unit_id', 6)->countAllResults(); // IN_DELIVERY
                $stats['rental_active']   = $dbTmp->table('inventory_unit')->where('status_unit_id', 7)->countAllResults(); // RENTAL_ACTIVE
                $stats['maintenance']     = $dbTmp->table('inventory_unit')->where('status_unit_id', 8)->countAllResults(); // MAINTENANCE
                $stats['returned']        = $dbTmp->table('inventory_unit')->where('status_unit_id', 9)->countAllResults(); // RETURNED
                $stats['sold']            = $dbTmp->table('inventory_unit')->where('status_unit_id', 10)->countAllResults(); // SOLD
                $stats['rental_inactive'] = $dbTmp->table('inventory_unit')->where('status_unit_id', 11)->countAllResults(); // RENTAL_INACTIVE
            } else {
                // Set all to 0 if table doesn't exist
                $defaultStats = ['available_stock', 'stock_non_aset', 'booked', 'in_preparation', 'ready_to_deliver', 'in_delivery', 'rental_active', 'maintenance', 'returned', 'sold', 'rental_inactive'];
                foreach ($defaultStats as $stat) {
                    $stats[$stat] = 0;
                }
            }
        } catch (\Throwable $e) { 
            $stats['available_stock'] = 0;
            $stats['stock_non_aset'] = 0;
            $stats['rental_active'] = 0;
            $stats['sold'] = 0;
            $stats['maintenance'] = 0;
        }
        // Ambil opsi departemen untuk filter dropdown
        $db = Database::connect();
        $departemen = [];
        try {
            if ($db->tableExists('departemen')) {
                $departemen = $db->table('departemen')->select('id_departemen,nama_departemen')->orderBy('nama_departemen','ASC')->get()->getResultArray();
            }
        } catch (\Throwable $e) { /* ignore */ }
        $data = [
            'title' => 'Inventory Unit',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/inventory' => 'Inventory Unit'
            ],
            'stats' => $stats,
            'departemen_options' => $departemen,
        ];
        return view('warehouse/inventory/invent_unit', $data);
    }

    /** Export CSV for unified inventory units */
    public function exportInventUnit()
    {
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
        return view('warehouse/export_unit_inventory');
    }

    public function exportAttachmentInventory()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.inventory_attachment')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.inventory_attachment');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'inventory_attachment', 0, 'Export Attachment Inventory CSV', [
                'module_name' => 'WAREHOUSE',
                'submenu_item' => 'Attachment Inventory',
                'business_impact' => 'LOW'
            ]);
        }
        return view('warehouse/export_attachment_inventory');
    }

    public function exportBatteryInventory()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.inventory_battery')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.inventory_battery');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'inventory_attachment', 0, 'Export Battery Inventory CSV', [
                'module_name' => 'WAREHOUSE',
                'submenu_item' => 'Battery Inventory',
                'business_impact' => 'LOW'
            ]);
        }
        return view('warehouse/export_battery_inventory');
    }

    public function exportChargerInventory()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.inventory_charger')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.inventory_charger');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'inventory_attachment', 0, 'Export Charger Inventory CSV', [
                'module_name' => 'WAREHOUSE',
                'submenu_item' => 'Charger Inventory',
                'business_impact' => 'LOW'
            ]);
        }
        return view('warehouse/export_charger_inventory');
    }

    /** Konfirmasi perubahan status menjadi RENTAL (3) langsung di controller baru */
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
                ->join('kontrak k', 'k.id = iu.kontrak_id', 'left')
                ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
                ->join('customers c', 'c.id = cl.customer_id', 'left')
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
                    COALESCE(ku.kapasitas_unit, "Unknown") as kapasitas_unit,
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
                    
                    -- Contract & Customer Info
                    iu.kontrak_id,
                    iu.customer_id,
                    iu.customer_location_id,
                    COALESCE(k.no_kontrak, "-") as no_kontrak,
                    COALESCE(k.status, "-") as status_kontrak,
                    k.tanggal_mulai as kontrak_mulai,
                    k.tanggal_berakhir as kontrak_berakhir,
                    k.jenis_sewa,
                    COALESCE(c.customer_name, "-") as customer_name,
                    COALESCE(c.customer_code, "-") as customer_code,
                    COALESCE(cl.location_name, "-") as customer_location_name,
                    COALESCE(cl.address, "-") as customer_address,
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
                    iu.delivery_instruction_id,
                    iu.workflow_status,
                    iu.contract_disconnect_date,
                    iu.contract_disconnect_stage
                FROM inventory_unit iu
                LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
                LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
                LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
                LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
                LEFT JOIN kapasitas ku ON ku.id_kapasitas = iu.kapasitas_unit_id
                LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
                LEFT JOIN mesin m ON m.id = iu.model_mesin_id
                LEFT JOIN tipe_ban tb ON tb.id_ban = iu.ban_id
                LEFT JOIN jenis_roda jr ON jr.id_roda = iu.roda_id
                LEFT JOIN valve v ON v.id_valve = iu.valve_id
                LEFT JOIN kontrak k ON k.id = iu.kontrak_id
                LEFT JOIN customers c ON c.id = iu.customer_id
                LEFT JOIN customer_locations cl ON cl.id = iu.customer_location_id
                LEFT JOIN areas a ON a.id = iu.area_id
                LEFT JOIN purchase_orders po ON po.id_po = iu.id_po
                LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id
                WHERE iu.id_inventory_unit = ?
            ', [$id]);
            
            $data = $query->getRowArray();
            if (!$data) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.']);
            }
            
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
                    ia.catatan_inventory as attachment_catatan,
                    COALESCE(CONCAT(att.tipe, " - ", att.merk, " ", att.model), "-") as attachment_name,
                    COALESCE(att.tipe, "-") as attachment_type,
                    COALESCE(bat.tipe_baterai, "-") as baterai_type,
                    COALESCE(ch.tipe_charger, "-") as charger_type,
                    COALESCE(sa.nama_status, ia.attachment_status) as status_attachment_name
                FROM inventory_attachment ia
                LEFT JOIN attachment att ON att.id_attachment = ia.attachment_id
                LEFT JOIN baterai bat ON bat.id = ia.baterai_id  
                LEFT JOIN charger ch ON ch.id_charger = ia.charger_id
                LEFT JOIN status_attachment sa ON sa.id_status_attachment = ia.status_attachment_id
                WHERE ia.id_inventory_unit = ?
                ORDER BY ia.tipe_item, ia.tanggal_masuk DESC
            ', [$id]);
            
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
        if ($inventoryUnitModel->update($id, $data)) {
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

    public function inventAttachment()
    {
        if ($this->request->isAJAX()) {
            try {
                $attachmentModel = new InventoryAttachmentModel();
                
                $request = [
                    'start' => $this->request->getPost('start'),
                    'length' => $this->request->getPost('length'),
                    'search' => $this->request->getPost('search'),
                    'order' => $this->request->getPost('order'),
                    'status_unit' => $this->request->getPost('status_unit'),
                    'tipe_item' => $this->request->getPost('tipe_item'),
                    'status_filter' => $this->request->getPost('status_filter')
                ];

                $result = $attachmentModel->getDataTable($request);
                
                return $this->response->setJSON([
                    'draw' => $this->request->getPost('draw'),
                    'recordsTotal' => $attachmentModel->countAll(),
                    'recordsFiltered' => $result['recordsFiltered'],
                    'data' => $result['data'],
                    'csrf_hash' => csrf_hash()
                ]);
            } catch (\Exception $e) {
                log_message('error', '[Warehouse::inventAttachment] Error: ' . $e->getMessage());
                log_message('error', $e->getTraceAsString());
                
                return $this->response->setJSON([
                    'draw' => $this->request->getPost('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(500);
            }
        }

        $attachmentModel = new InventoryAttachmentModel();
        $data = [
            'title' => 'Inventory Attachment',
            'page_title' => 'Inventory Attachment',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/inventory/invent_attachment' => 'Inventory Attachment'
            ],
            'stats' => $attachmentModel->getStats()
        ];

        return view('warehouse/inventory/invent_attachment', $data);
    }

    public function getAttachmentDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            $attachmentModel = new InventoryAttachmentModel();
            $attachment = $attachmentModel->getAttachmentDetail($id);

            if (!$attachment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak ditemukan'
                ])->setStatusCode(404);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $attachment
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::getAttachmentDetail] Error: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail attachment: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function updateAttachment($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            $attachmentModel = new InventoryAttachmentModel();
            
            // Validate attachment exists
            $attachment = $attachmentModel->find($id);
            if (!$attachment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Get update data
            $updateData = [
                'status_unit' => $this->request->getPost('status_unit'),
                'lokasi_unit' => $this->request->getPost('lokasi_unit'),
                'kondisi_unit' => $this->request->getPost('kondisi_unit')
            ];

            // Remove empty values
            $updateData = array_filter($updateData, function($value) {
                return $value !== null && $value !== '';
            });

            if ($attachmentModel->update($id, $updateData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data attachment berhasil diperbarui'
                ]);
            } else {
                $errors = $attachmentModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui data attachment',
                    'errors' => $errors
                ])->setStatusCode(400);
            }
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::updateAttachment] Error: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui attachment: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // private function getSparepartStats()
    // {
    //     return [
    //         'total_items' => 156,
    //         'low_stock_items' => 12,
    //         'out_of_stock' => 3,
    //         'total_value' => 45750000
    //     ];
    // }

    // private function getWarehouseLocations()
    // {
    //     return ['A-01', 'A-02', 'B-01', 'B-02', 'C-01', 'C-02'];
    // }

    private function getNonAssets()
    {
        return [
            [
                'id' => 1,
                'item_code' => 'NA-001',
                'item_name' => 'Safety Helmet',
                'category' => 'Safety Equipment',
                'description' => 'High-quality safety helmet for construction work',
                'stock' => 50,
                'min_stock' => 20,
                'unit' => 'pcs',
                'unit_price' => 125000,
                'location' => 'Safety Cabinet A',
                'last_updated' => '2024-01-15'
            ],
            [
                'id' => 2,
                'item_code' => 'NA-002',
                'item_name' => 'Safety Vest',
                'category' => 'Safety Equipment',
                'description' => 'High-visibility safety vest',
                'stock' => 35,
                'min_stock' => 15,
                'unit' => 'pcs',
                'unit_price' => 85000,
                'location' => 'Safety Cabinet B',
                'last_updated' => '2024-01-15'
            ],
            [
                'id' => 3,
                'item_code' => 'NA-003',
                'item_name' => 'Hand Tools Set',
                'category' => 'Maintenance Supplies',
                'description' => 'Complete hand tools set for maintenance',
                'stock' => 15,
                'min_stock' => 5,
                'unit' => 'set',
                'unit_price' => 450000,
                'location' => 'Tool Cabinet 1',
                'last_updated' => '2024-01-14'
            ],
            [
                'id' => 4,
                'item_code' => 'NA-004',
                'item_name' => 'Office Paper A4',
                'category' => 'Office Supplies',
                'description' => 'A4 size office paper for printing',
                'stock' => 100,
                'min_stock' => 50,
                'unit' => 'pack',
                'unit_price' => 25000,
                'location' => 'Office Storage',
                'last_updated' => '2024-01-10'
            ],
            [
                'id' => 5,
                'item_code' => 'NA-005',
                'item_name' => 'Cleaning Detergent',
                'category' => 'Cleaning Supplies',
                'description' => 'Industrial cleaning detergent',
                'stock' => 25,
                'min_stock' => 10,
                'unit' => 'bottle',
                'unit_price' => 75000,
                'location' => 'Cleaning Storage',
                'last_updated' => '2024-01-05'
            ]
        ];
    }

    private function getInventoryStats()
    {
        return [
            'total_items' => 225,
            'office_supplies' => 45,
            'consumables' => 68,
            'total_value' => 1875000
        ];
    }

    private function getNonAssetStats()
    {
        // Legacy method - redirect to getInventoryStats
        return $this->getInventoryStats();
    }

    private function getInventoryItems()
    {
        return $this->getNonAssets(); // Reuse existing data for now
    }

    private function getNonAssetCategories()
    {
        return ['Safety Equipment', 'Tools', 'Office Equipment', 'Maintenance'];
    }

    private function getWarehouseStats()
    {
        return [
            'total_spareparts' => 156,
            'total_non_assets' => 225,
            'low_stock_items' => 15,
            'total_inventory_value' => 47625000,
            'warehouse_utilization' => 78.5,
            'inventory_turnover' => 4.2
        ];
    }

    private function getInventoryOverview()
    {
        return [
            'spareparts' => [
                'total_items' => 156,
                'total_value' => 45750000,
                'low_stock' => 12,
                'categories' => 8
            ],
            'non_assets' => [
                'total_items' => 225,
                'total_value' => 1875000,
                'low_stock' => 3,
                'categories' => 5
            ]
        ];
    }

    private function getRecentTransactions()
    {
        return [
            [
                'id' => 'TXN-2024-001',
                'type' => 'IN',
                'item' => 'Engine Oil Filter',
                'quantity' => 25,
                'date' => '2024-01-15 10:30:00',
                'reference' => 'PO-2024-001'
            ],
            [
                'id' => 'TXN-2024-002',
                'type' => 'OUT',
                'item' => 'Brake Pad Set',
                'quantity' => 4,
                'date' => '2024-01-15 14:15:00',
                'reference' => 'WO-2024-001'
            ],
            [
                'id' => 'TXN-2024-003',
                'type' => 'IN',
                'item' => 'Safety Helmet',
                'quantity' => 30,
                'date' => '2024-01-14 09:45:00',
                'reference' => 'PO-2024-002'
            ],
            [
                'id' => 'TXN-2024-004',
                'type' => 'OUT',
                'item' => 'Hydraulic Oil',
                'quantity' => 8,
                'date' => '2024-01-14 16:20:00',
                'reference' => 'WO-2024-002'
            ]
        ];
    }

    private function getLowStockAlerts()
    {
        return [
            [
                'item_code' => 'SP-FL-002',
                'item_name' => 'Brake Pad Set',
                'current_stock' => 8,
                'min_stock' => 12,
                'category' => 'Sparepart',
                'urgency' => 'High'
            ],
            [
                'item_code' => 'SP-FL-004',
                'item_name' => 'Tire Set',
                'current_stock' => 6,
                'min_stock' => 8,
                'category' => 'Sparepart',
                'urgency' => 'Medium'
            ],
            [
                'item_code' => 'NA-005',
                'item_name' => 'Cleaning Detergent',
                'current_stock' => 25,
                'min_stock' => 10,
                'category' => 'Non-Asset',
                'urgency' => 'Low'
            ]
        ];
    }

    // Master data API endpoints for dynamic dropdowns
    public function masterMerk($type)
    {
        try {
            $db = \Config\Database::connect();
            $data = [];
            
            switch($type) {
                case 'attachment':
                    $query = $db->query('SELECT DISTINCT merk as value, merk as text FROM attachment ORDER BY merk');
                    $data = $query->getResultArray();
                    break;
                case 'battery':
                    $query = $db->query('SELECT DISTINCT merk_baterai as value, merk_baterai as text FROM baterai ORDER BY merk_baterai');
                    $data = $query->getResultArray();
                    break;
                case 'charger':
                    $query = $db->query('SELECT DISTINCT merk_charger as value, merk_charger as text FROM charger ORDER BY merk_charger');
                    $data = $query->getResultArray();
                    break;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading merk data: ' . $e->getMessage()
            ]);
        }
    }
    
    public function masterTipe($type)
    {
        try {
            $db = \Config\Database::connect();
            $data = [];
            
            switch($type) {
                case 'attachment':
                    $query = $db->query('SELECT DISTINCT tipe as value, tipe as text FROM attachment ORDER BY tipe');
                    $data = $query->getResultArray();
                    break;
                case 'battery':
                    $query = $db->query('SELECT DISTINCT tipe_baterai as value, tipe_baterai as text FROM baterai ORDER BY tipe_baterai');
                    $data = $query->getResultArray();
                    break;
                case 'charger':
                    $query = $db->query('SELECT DISTINCT tipe_charger as value, tipe_charger as text FROM charger ORDER BY tipe_charger');
                    $data = $query->getResultArray();
                    break;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading tipe data: ' . $e->getMessage()
            ]);
        }
    }
    
    public function masterJenis($type)
    {
        try {
            $db = \Config\Database::connect();
            $data = [];
            
            if ($type === 'battery') {
                $query = $db->query('SELECT DISTINCT jenis_baterai as value, jenis_baterai as text FROM baterai ORDER BY jenis_baterai');
                $data = $query->getResultArray();
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading jenis data: ' . $e->getMessage()
            ]);
        }
    }
    
    public function masterModel($type)
    {
        try {
            $db = \Config\Database::connect();
            $data = [];
            
            if ($type === 'attachment') {
                $query = $db->query('SELECT DISTINCT model as value, model as text FROM attachment ORDER BY model');
                $data = $query->getResultArray();
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading model data: ' . $e->getMessage()
            ]);
        }
    }
    
    // Save new master data endpoints
    public function saveMasterMerk($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $value = $this->request->getPost('value');
            if (empty($value)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Value tidak boleh kosong']);
            }
            
            $db = \Config\Database::connect();
            
            switch($type) {
                case 'attachment':
                    // Check if merk already exists
                    $exists = $db->query('SELECT COUNT(*) as count FROM attachment WHERE merk = ?', [$value])->getRow()->count;
                    if ($exists > 0) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Merk sudah ada']);
                    }
                    // Insert new attachment with default values
                    $db->query('INSERT INTO attachment (merk, tipe, model) VALUES (?, ?, ?)', [$value, 'Default Type', 'Default Model']);
                    break;
                case 'battery':
                    // Check if merk already exists
                    $exists = $db->query('SELECT COUNT(*) as count FROM baterai WHERE merk_baterai = ?', [$value])->getRow()->count;
                    if ($exists > 0) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Merk sudah ada']);
                    }
                    // Insert new battery with default values
                    $db->query('INSERT INTO baterai (merk_baterai, tipe_baterai, jenis_baterai) VALUES (?, ?, ?)', [$value, 'Default Type', 'Default Jenis']);
                    break;
                case 'charger':
                    // Check if merk already exists
                    $exists = $db->query('SELECT COUNT(*) as count FROM charger WHERE merk_charger = ?', [$value])->getRow()->count;
                    if ($exists > 0) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Merk sudah ada']);
                    }
                    // Insert new charger with default values
                    $db->query('INSERT INTO charger (merk_charger, tipe_charger) VALUES (?, ?)', [$value, 'Default Type']);
                    break;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Merk berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error saving merk: ' . $e->getMessage()
            ]);
        }
    }
    
    public function saveMasterTipe($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $value = $this->request->getPost('value');
            if (empty($value)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Value tidak boleh kosong']);
            }
            
            $db = \Config\Database::connect();
            
            switch($type) {
                case 'attachment':
                    // Check if tipe already exists
                    $exists = $db->query('SELECT COUNT(*) as count FROM attachment WHERE tipe = ?', [$value])->getRow()->count;
                    if ($exists > 0) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Tipe sudah ada']);
                    }
                    // Insert new attachment with default values
                    $db->query('INSERT INTO attachment (merk, tipe, model) VALUES (?, ?, ?)', ['Default Merk', $value, 'Default Model']);
                    break;
                case 'battery':
                    // Check if tipe already exists
                    $exists = $db->query('SELECT COUNT(*) as count FROM baterai WHERE tipe_baterai = ?', [$value])->getRow()->count;
                    if ($exists > 0) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Tipe sudah ada']);
                    }
                    // Insert new battery with default values
                    $db->query('INSERT INTO baterai (merk_baterai, tipe_baterai, jenis_baterai) VALUES (?, ?, ?)', ['Default Merk', $value, 'Default Jenis']);
                    break;
                case 'charger':
                    // Check if tipe already exists
                    $exists = $db->query('SELECT COUNT(*) as count FROM charger WHERE tipe_charger = ?', [$value])->getRow()->count;
                    if ($exists > 0) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Tipe sudah ada']);
                    }
                    // Insert new charger with default values
                    $db->query('INSERT INTO charger (merk_charger, tipe_charger) VALUES (?, ?)', ['Default Merk', $value]);
                    break;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tipe berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error saving tipe: ' . $e->getMessage()
            ]);
        }
    }
    
    public function saveMasterJenis($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $value = $this->request->getPost('value');
            if (empty($value)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Value tidak boleh kosong']);
            }
            
            if ($type === 'battery') {
                $db = \Config\Database::connect();
                
                // Check if jenis already exists
                $exists = $db->query('SELECT COUNT(*) as count FROM baterai WHERE jenis_baterai = ?', [$value])->getRow()->count;
                if ($exists > 0) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Jenis sudah ada']);
                }
                
                // Insert new battery with default values
                $db->query('INSERT INTO baterai (merk_baterai, tipe_baterai, jenis_baterai) VALUES (?, ?, ?)', ['Default Merk', 'Default Type', $value]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Jenis berhasil ditambahkan'
                ]);
            }
            
            return $this->response->setJSON(['success' => false, 'message' => 'Jenis hanya tersedia untuk battery']);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error saving jenis: ' . $e->getMessage()
            ]);
        }
    }
    
    public function saveMasterModel($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $value = $this->request->getPost('value');
            if (empty($value)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Value tidak boleh kosong']);
            }
            
            if ($type === 'attachment') {
                $db = \Config\Database::connect();
                
                // Check if model already exists
                $exists = $db->query('SELECT COUNT(*) as count FROM attachment WHERE model = ?', [$value])->getRow()->count;
                if ($exists > 0) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Model sudah ada']);
                }
                
                // Insert new attachment with default values
                $db->query('INSERT INTO attachment (merk, tipe, model) VALUES (?, ?, ?)', ['Default Merk', 'Default Type', $value]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Model berhasil ditambahkan'
                ]);
            }
            
            return $this->response->setJSON(['success' => false, 'message' => 'Model hanya tersedia untuk attachment']);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error saving model: ' . $e->getMessage()
            ]);
        }
    }
    
    // Separate methods for each item type
    public function attachmentData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $attachmentModel = new InventoryAttachmentModel();
            
            $request = [
                'start' => $this->request->getPost('start'),
                'length' => $this->request->getPost('length'),
                'search' => $this->request->getPost('search'),
                'order' => $this->request->getPost('order'),
                'tipe_item' => 'attachment' // Fixed to attachment only
            ];

            $result = $attachmentModel->getDataTable($request);
            
            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw'),
                'recordsTotal' => $attachmentModel->countAll(),
                'recordsFiltered' => $result['recordsFiltered'],
                'data' => $result['data'],
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::attachmentData] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }
    
    public function batteryData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $attachmentModel = new InventoryAttachmentModel();
            
            $request = [
                'start' => $this->request->getPost('start'),
                'length' => $this->request->getPost('length'),
                'search' => $this->request->getPost('search'),
                'order' => $this->request->getPost('order'),
                'tipe_item' => 'battery' // Fixed to battery only
            ];

            $result = $attachmentModel->getDataTable($request);
            
            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw'),
                'recordsTotal' => $attachmentModel->countAll(),
                'recordsFiltered' => $result['recordsFiltered'],
                'data' => $result['data'],
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::batteryData] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }
    
    public function chargerData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $attachmentModel = new InventoryAttachmentModel();
            
            $request = [
                'start' => $this->request->getPost('start'),
                'length' => $this->request->getPost('length'),
                'search' => $this->request->getPost('search'),
                'order' => $this->request->getPost('order'),
                'tipe_item' => 'charger' // Fixed to charger only
            ];

            $result = $attachmentModel->getDataTable($request);
            
            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw'),
                'recordsTotal' => $attachmentModel->countAll(),
                'recordsFiltered' => $result['recordsFiltered'],
                'data' => $result['data'],
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::chargerData] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }

    public function saveMasterData($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $db = \Config\Database::connect();
            $merk = $this->request->getPost('merk');
            $tipe = $this->request->getPost('tipe');
            
            if ($type === 'attachment') {
                $model = $this->request->getPost('model');
                
                // Check if combination already exists
                $checkQuery = $db->query('SELECT id_attachment FROM attachment WHERE merk = ? AND tipe = ? AND model = ?', [$merk, $tipe, $model]);
                if ($checkQuery->getNumRows() > 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Kombinasi merk, tipe, dan model sudah ada'
                    ]);
                }
                
                // Insert new attachment
                $db->query('INSERT INTO attachment (merk, tipe, model) VALUES (?, ?, ?)', [$merk, $tipe, $model]);
                
            } elseif ($type === 'battery') {
                $jenis = $this->request->getPost('jenis');
                
                // Check if combination already exists
                $checkQuery = $db->query('SELECT id FROM baterai WHERE merk_baterai = ? AND tipe_baterai = ? AND jenis_baterai = ?', [$merk, $tipe, $jenis]);
                if ($checkQuery->getNumRows() > 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Kombinasi merk, tipe, dan jenis sudah ada'
                    ]);
                }
                
                // Insert new battery
                $db->query('INSERT INTO baterai (merk_baterai, tipe_baterai, jenis_baterai) VALUES (?, ?, ?)', [$merk, $tipe, $jenis]);
                
            } elseif ($type === 'charger') {
                // Check if combination already exists
                $checkQuery = $db->query('SELECT id_charger FROM charger WHERE merk_charger = ? AND tipe_charger = ?', [$merk, $tipe]);
                if ($checkQuery->getNumRows() > 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Kombinasi merk dan tipe sudah ada'
                    ]);
                }
                
                // Insert new charger
                $db->query('INSERT INTO charger (merk_charger, tipe_charger) VALUES (?, ?)', [$merk, $tipe]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Master data berhasil ditambahkan'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::saveMasterData] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function addInventoryItem()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        try {
            $tipeItem = $this->request->getPost('tipe_item');
            $db = \Config\Database::connect();
            
            // Get or create master data IDs based on form data
            $attachmentId = null;
            $bateraiId = null;
            $chargerId = null;
            
            if ($tipeItem === 'attachment') {
                $merk = $this->request->getPost('attachment_merk');
                $tipe = $this->request->getPost('attachment_tipe');
                $model = $this->request->getPost('attachment_model');
                
                // Find or create attachment record
                $attachmentQuery = $db->query('SELECT id_attachment FROM attachment WHERE merk = ? AND tipe = ? AND model = ?', [$merk, $tipe, $model]);
                $attachment = $attachmentQuery->getRow();
                
                if ($attachment) {
                    $attachmentId = $attachment->id_attachment;
                } else {
                    // Create new attachment record
                    $db->query('INSERT INTO attachment (merk, tipe, model) VALUES (?, ?, ?)', [$merk, $tipe, $model]);
                    $attachmentId = $db->insertID();
                }
            } elseif ($tipeItem === 'battery') {
                $merk = $this->request->getPost('battery_merk');
                $tipe = $this->request->getPost('battery_tipe');
                $jenis = $this->request->getPost('battery_jenis');
                
                // Find or create battery record
                $batteryQuery = $db->query('SELECT id FROM baterai WHERE merk_baterai = ? AND tipe_baterai = ? AND jenis_baterai = ?', [$merk, $tipe, $jenis]);
                $battery = $batteryQuery->getRow();
                
                if ($battery) {
                    $bateraiId = $battery->id;
                } else {
                    // Create new battery record
                    $db->query('INSERT INTO baterai (merk_baterai, tipe_baterai, jenis_baterai) VALUES (?, ?, ?)', [$merk, $tipe, $jenis]);
                    $bateraiId = $db->insertID();
                }
            } elseif ($tipeItem === 'charger') {
                $merk = $this->request->getPost('charger_merk');
                $tipe = $this->request->getPost('charger_tipe');
                
                // Find or create charger record
                $chargerQuery = $db->query('SELECT id_charger FROM charger WHERE merk_charger = ? AND tipe_charger = ?', [$merk, $tipe]);
                $charger = $chargerQuery->getRow();
                
                if ($charger) {
                    $chargerId = $charger->id_charger;
                } else {
                    // Create new charger record
                    $db->query('INSERT INTO charger (merk_charger, tipe_charger) VALUES (?, ?)', [$merk, $tipe]);
                    $chargerId = $db->insertID();
                }
            }
            
            // Prepare inventory data with default values
            $inventoryData = [
                'tipe_item' => $tipeItem,
                'po_id' => 1, // Default PO ID, should be handled properly in real implementation
                'attachment_id' => $attachmentId,
                'baterai_id' => $bateraiId,
                'charger_id' => $chargerId,
                'sn_attachment' => $tipeItem === 'attachment' ? $this->request->getPost('sn_attachment') : null,
                'sn_baterai' => $tipeItem === 'battery' ? $this->request->getPost('sn_baterai') : null,
                'sn_charger' => $tipeItem === 'charger' ? $this->request->getPost('sn_charger') : null,
                'kondisi_fisik' => $this->request->getPost('kondisi_fisik') ?: 'Baik',
                'lokasi_penyimpanan' => $this->request->getPost('lokasi_penyimpanan') ?: 'Workshop',
                'attachment_status' => 'AVAILABLE', // Default status for new items
                'id_inventory_unit' => null, // Not attached to any unit yet
                'catatan_inventory' => $this->request->getPost('catatan'),
                'status_unit' => 7, // Default status STOCK ASET (AVAILABLE)
                'tanggal_masuk' => date('Y-m-d H:i:s')
            ];
            
            // Insert into inventory_attachment
            $attachmentModel = new InventoryAttachmentModel();
            if ($attachmentModel->insert($inventoryData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Item berhasil ditambahkan ke inventory'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menambahkan item ke inventory'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::addInventoryItem] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available units for attach/swap dropdown
     */
    public function getAvailableUnits()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all units with existing attachment info
            $units = $db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.status_unit_id, su.status_unit as status_unit_name, CONCAT(mu.merk_unit, " - ", mu.model_unit) as model_unit')
                ->select('(SELECT COUNT(*) FROM inventory_attachment WHERE id_inventory_unit = iu.id_inventory_unit AND tipe_item = "attachment") as has_attachment')
                ->select('(SELECT COUNT(*) FROM inventory_attachment WHERE id_inventory_unit = iu.id_inventory_unit AND tipe_item = "battery") as has_battery')
                ->select('(SELECT COUNT(*) FROM inventory_attachment WHERE id_inventory_unit = iu.id_inventory_unit AND tipe_item = "charger") as has_charger')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
                ->orderBy('iu.no_unit', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'units' => $units
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::getAvailableUnits] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat daftar unit: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Attach attachment to unit
     */
    public function attachToUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            $attachmentModel = new InventoryAttachmentModel();
            $db = \Config\Database::connect();
            
            $attachmentId = $this->request->getPost('attachment_id');
            $unitId = $this->request->getPost('unit_id');
            $notes = $this->request->getPost('notes');
            
            if (!$attachmentId || !$unitId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }
            
            // Get attachment type
            $newAttachment = $attachmentModel->find($attachmentId);
            if (!$newAttachment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak ditemukan'
                ]);
            }
            
            $attachmentType = $newAttachment['tipe_item'];
            
            // Get unit number
            $unit = $db->table('inventory_unit')
                ->select('no_unit')
                ->where('id_inventory_unit', $unitId)
                ->get()->getRowArray();
            
            if (!$unit) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan'
                ]);
            }
            
            $db->transStart();
            
            // Check if unit already has attachment of same type
            $existingAttachment = $db->table('inventory_attachment')
                ->where('id_inventory_unit', $unitId)
                ->where('tipe_item', $attachmentType)
                ->get()->getRowArray();
            
            $message = '';
            
            if ($existingAttachment) {
                // Auto-detach existing attachment
                $attachmentModel->detachFromUnit($existingAttachment['id_inventory_attachment'], 'Auto-detach karena ada replacement');
                $message = "Attachment lama dilepas dan dipasang yang baru ke Unit {$unit['no_unit']}";
                
                // Log detach
                $this->logActivity('auto_detach', 'inventory_attachment', $existingAttachment['id_inventory_attachment'], "Attachment lama dilepas dari Unit {$unit['no_unit']} (auto)", [
                    'old_attachment_id' => $existingAttachment['id_inventory_attachment'],
                    'new_attachment_id' => $attachmentId,
                    'unit_id' => $unitId
                ]);
            } else {
                $message = "Berhasil memasang attachment ke Unit {$unit['no_unit']}";
            }
            
            // Attach new attachment (trigger akan auto-update status dan lokasi)
            $result = $attachmentModel->attachToUnit($attachmentId, $unitId, $unit['no_unit']);
            
            if ($result) {
                $db->transComplete();
                
                if ($db->transStatus() === false) {
                    throw new \Exception('Transaction failed');
                }
                
                // Log activity
                $this->logActivity('attach_to_unit', 'inventory_attachment', (int)$attachmentId, "Attachment dipasang ke Unit {$unit['no_unit']}", [
                    'attachment_id' => $attachmentId,
                    'unit_id' => $unitId,
                    'notes' => $notes,
                    'had_existing' => !empty($existingAttachment)
                ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memasang attachment'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::attachToUnit] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Swap attachment between units
     */
    public function swapUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            $attachmentModel = new InventoryAttachmentModel();
            
            $attachmentId = $this->request->getPost('attachment_id');
            $fromUnitId = $this->request->getPost('from_unit_id');
            $toUnitId = $this->request->getPost('to_unit_id');
            $reason = $this->request->getPost('reason');
            
            if (!$attachmentId || !$fromUnitId || !$toUnitId || !$reason) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }
            
            $db = \Config\Database::connect();
            $db->transStart();
            
            // Get attachment type
            $movingAttachment = $attachmentModel->find($attachmentId);
            $attachmentType = $movingAttachment['tipe_item'];
            
            // Check if target unit already has attachment of same type
            $existingAttachment = $db->table('inventory_attachment')
                ->where('id_inventory_unit', $toUnitId)
                ->where('tipe_item', $attachmentType)
                ->get()->getRowArray();
            
            if ($existingAttachment) {
                // Auto-detach existing attachment from target unit
                $attachmentModel->detachFromUnit($existingAttachment['id_inventory_attachment'], 'Auto-detach karena ada replacement (swap)');
                
                // Log auto-detach
                $this->logActivity('auto_detach', 'inventory_attachment', $existingAttachment['id_inventory_attachment'], "Attachment lama dilepas dari unit tujuan (auto swap)", [
                    'old_attachment_id' => $existingAttachment['id_inventory_attachment'],
                    'moving_attachment_id' => $attachmentId,
                    'unit_id' => $toUnitId
                ]);
            }
            
            // Use swap method
            $result = $attachmentModel->swapAttachmentBetweenUnits($attachmentId, $fromUnitId, $toUnitId, $reason);
            
            if ($result) {
                $db->transComplete();
                
                if ($db->transStatus() === false) {
                    throw new \Exception('Transaction failed');
                }
                
                // Get unit numbers for message
                $fromUnit = $db->table('inventory_unit')->select('no_unit')->where('id_inventory_unit', $fromUnitId)->get()->getRowArray();
                $toUnit = $db->table('inventory_unit')->select('no_unit')->where('id_inventory_unit', $toUnitId)->get()->getRowArray();
                
                // Log activity
                $this->logActivity('swap_unit', 'inventory_attachment', (int)$attachmentId, "Attachment dipindah dari Unit {$fromUnit['no_unit']} ke Unit {$toUnit['no_unit']}", [
                    'attachment_id' => $attachmentId,
                    'from_unit_id' => $fromUnitId,
                    'to_unit_id' => $toUnitId,
                    'reason' => $reason
                ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Berhasil memindahkan attachment dari Unit {$fromUnit['no_unit']} ke Unit {$toUnit['no_unit']}"
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memindahkan attachment'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::swapUnit] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Detach attachment from unit
     */
    public function detachFromUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            $attachmentModel = new InventoryAttachmentModel();
            
            $attachmentId = $this->request->getPost('attachment_id');
            $reason = $this->request->getPost('reason');
            $newLocation = $this->request->getPost('new_location');
            
            if (!$attachmentId || !$reason) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }
            
            // Get current unit for logging
            $db = \Config\Database::connect();
            $attachment = $db->table('inventory_attachment ia')
                ->select('ia.id_inventory_unit, iu.no_unit')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ia.id_inventory_unit', 'left')
                ->where('ia.id_inventory_attachment', $attachmentId)
                ->get()->getRowArray();
            
            // Detach from unit (trigger akan auto-update status dan lokasi)
            $result = $attachmentModel->detachFromUnit($attachmentId, $reason);
            
            if ($result) {
                // Update lokasi if custom location provided
                if ($newLocation && $newLocation != 'Workshop') {
                    $attachmentModel->update($attachmentId, ['lokasi_penyimpanan' => $newLocation]);
                }
                
                // Log activity
                $unitInfo = $attachment['no_unit'] ?? $attachment['id_inventory_unit'] ?? 'Unknown';
                $this->logActivity('detach_from_unit', 'inventory_attachment', (int)$attachmentId, "Attachment dilepas dari Unit {$unitInfo}", [
                    'attachment_id' => $attachmentId,
                    'from_unit_id' => $attachment['id_inventory_unit'] ?? null,
                    'reason' => $reason,
                    'new_location' => $newLocation
                ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Berhasil melepas attachment dari Unit {$unitInfo}"
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal melepas attachment'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::detachFromUnit] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

}