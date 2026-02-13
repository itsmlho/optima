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
                    ia.catatan_inventory as attachment_catatan,
                    COALESCE(CONCAT(att.tipe, " - ", att.merk, " ", att.model), "-") as attachment_name,
                    COALESCE(att.tipe, "-") as attachment_type,
                    COALESCE(bat.tipe_baterai, "-") as baterai_type,
                    COALESCE(bat.merk_baterai, "-") as merk_baterai,
                    COALESCE(bat.jenis_baterai, "-") as jenis_baterai,
                    COALESCE(ch.tipe_charger, "-") as charger_type,
                    COALESCE(ch.merk_charger, "-") as merk_charger,
                    ia.attachment_status as status_attachment_name
                FROM inventory_attachment ia
                LEFT JOIN attachment att ON att.id_attachment = ia.attachment_id
                LEFT JOIN baterai bat ON bat.id = ia.baterai_id  
                LEFT JOIN charger ch ON ch.id_charger = ia.charger_id
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
            // Get unit details for notification
            $unit = $inventoryUnitModel->find($id);
            $oldUnit = $inventoryUnitModel->find($id); // Get before data if needed
            
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
                    'old_status' => $statusNames[$unit['status_unit_id'] ?? 2] ?? 'Unknown',
                    'new_status' => $statusNames[$data['status_unit_id']] ?? 'Unknown',
                    'lokasi' => $data['lokasi_unit'],
                    'updated_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/warehouse/units')
                ]);
            }
            
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
                'attachment_status' => $this->request->getPost('attachment_status'),
                'lokasi_penyimpanan' => $this->request->getPost('lokasi_penyimpanan'),
                'kondisi_fisik' => $this->request->getPost('kondisi_fisik')
            ];

            // Remove empty values
            $updateData = array_filter($updateData, function($value) {
                return $value !== null && $value !== '';
            });

            if ($attachmentModel->update($id, $updateData)) {
                // Get attachment details for notification
                $updatedAttachment = $attachmentModel->find($id);
                
                // Send notification: Attachment Detached/Updated
                helper('notification');
                if ($updatedAttachment) {
                    notify_attachment_detached([
                        'id' => $id,
                        'attachment_type' => $attachment['tipe'] ?? 'Attachment',
                        'serial_number' => $attachment['sn_attachment'] ?? '',
                        'new_status' => $updateData['attachment_status'] ?? '',
                        'new_location' => $updateData['lokasi_penyimpanan'] ?? '',
                        'updated_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/warehouse/attachments')
                    ]);
                }
                
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
        $db = \Config\Database::connect();
        
        // Get actual counts from inventory tables
        $totalSpareparts = $db->table('inventory_sparepart')->countAllResults();
        $totalNonAssets = $db->table('inventory_attachment')->countAllResults();
        
        // Count low stock items (current_stock < minimum_stock)
        $lowStockSpareparts = $db->table('inventory_sparepart')
            ->where('stok_tersedia < stok_minimum')
            ->countAllResults();
        $lowStockAttachments = $db->table('inventory_attachment')
            ->where('stok_tersedia < stok_minimum')
            ->countAllResults();
        $lowStockItems = $lowStockSpareparts + $lowStockAttachments;
        
        // Calculate total inventory value
        $sparepartValue = $db->query("SELECT COALESCE(SUM(stok_tersedia * harga_satuan), 0) as total FROM inventory_sparepart")->getRow()->total ?? 0;
        $attachmentValue = $db->query("SELECT COALESCE(SUM(stok_tersedia * harga_satuan), 0) as total FROM inventory_attachment")->getRow()->total ?? 0;
        $totalInventoryValue = $sparepartValue + $attachmentValue;
        
        // Calculate warehouse utilization (simplified: items with stock / total items)
        $itemsWithStock = $db->query("SELECT 
            (SELECT COUNT(*) FROM inventory_sparepart WHERE stok_tersedia > 0) + 
            (SELECT COUNT(*) FROM inventory_attachment WHERE stok_tersedia > 0) as total
        ")->getRow()->total ?? 0;
        $totalItems = $totalSpareparts + $totalNonAssets;
        $warehouseUtilization = $totalItems > 0 ? ($itemsWithStock / $totalItems * 100) : 0;
        
        return [
            'total_spareparts' => $totalSpareparts,
            'total_non_assets' => $totalNonAssets,
            'low_stock_items' => $lowStockItems,
            'total_inventory_value' => $totalInventoryValue,
            'warehouse_utilization' => round($warehouseUtilization, 1),
            'inventory_turnover' => 0 // TODO: Calculate from transaction history
        ];
    }

    private function getInventoryOverview()
    {
        $db = \Config\Database::connect();
        
        // Spareparts data
        $totalSparepartItems = $db->table('inventory_sparepart')->countAllResults();
        $sparepartValue = $db->query("SELECT COALESCE(SUM(stok_tersedia * harga_satuan), 0) as total FROM inventory_sparepart")->getRow()->total ?? 0;
        $lowStockSpareparts = $db->table('inventory_sparepart')
            ->where('stok_tersedia < stok_minimum')
            ->countAllResults();
        $sparepartCategories = $db->table('inventory_sparepart')
            ->distinct()
            ->select('jenis_barang')
            ->where('jenis_barang IS NOT NULL')
            ->where('jenis_barang !=', '')
            ->countAllResults();
        
        // Non-assets data
        $totalNonAssetItems = $db->table('inventory_attachment')->countAllResults();
        $nonAssetValue = $db->query("SELECT COALESCE(SUM(stok_tersedia * harga_satuan), 0) as total FROM inventory_attachment")->getRow()->total ?? 0;
        $lowStockNonAssets = $db->table('inventory_attachment')
            ->where('stok_tersedia < stok_minimum')
            ->countAllResults();
        $nonAssetCategories = $db->table('inventory_attachment')
            ->distinct()
            ->select('jenis_barang')
            ->where('jenis_barang IS NOT NULL')
            ->where('jenis_barang !=', '')
            ->countAllResults();
        
        return [
            'spareparts' => [
                'total_items' => $totalSparepartItems,
                'total_value' => (int) $sparepartValue,
                'low_stock' => $lowStockSpareparts,
                'categories' => $sparepartCategories
            ],
            'non_assets' => [
                'total_items' => $totalNonAssetItems,
                'total_value' => (int) $nonAssetValue,
                'low_stock' => $lowStockNonAssets,
                'categories' => $nonAssetCategories
            ]
        ];
    }

    private function getRecentTransactions()
    {
        $db = \Config\Database::connect();
        
        // Try to get from transaction history if table exists
        // For now, query recent PO deliveries as IN transactions
        $transactions = [];
        
        try {
            // Get recent PO deliveries (IN transactions)
            $poDeliveries = $db->query("
                SELECT 
                    CONCAT('PO-', po.id) as id,
                    'IN' as type,
                    CONCAT(po.nomor_po, ' - ', COALESCE(po.supplier, 'N/A')) as item,
                    COALESCE(pod.quantity_delivered, 0) as quantity,
                    pod.tanggal_terima as date,
                    po.nomor_po as reference
                FROM po_delivery pod
                JOIN purchase_orders po ON po.id = pod.po_id
                WHERE pod.tanggal_terima IS NOT NULL
                ORDER BY pod.tanggal_terima DESC
                LIMIT 10
            ")->getResultArray();
            
            foreach ($poDeliveries as $delivery) {
                $transactions[] = [
                    'id' => $delivery['id'],
                    'type' => 'IN',
                    'item' => $delivery['item'],
                    'quantity' => $delivery['quantity'],
                    'date' => $delivery['date'],
                    'reference' => $delivery['reference']
                ];
            }
            
            // If no data, show placeholder
            if (empty($transactions)) {
                $transactions[] = [
                    'id' => 'N/A',
                    'type' => 'INFO',
                    'item' => 'No recent transactions',
                    'quantity' => 0,
                    'date' => date('Y-m-d H:i:s'),
                    'reference' => '-'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error fetching warehouse transactions: ' . $e->getMessage());
            $transactions[] = [
                'id' => 'ERROR',
                'type' => 'INFO',
                'item' => 'Unable to load transaction data',
                'quantity' => 0,
                'date' => date('Y-m-d H:i:s'),
                'reference' => '-'
            ];
        }
        
        return $transactions;
    }

    private function getLowStockAlerts()
    {
        $db = \Config\Database::connect();
        $alerts = [];
        
        try {
            // Get low stock spareparts
            $lowSpareparts = $db->query("
                SELECT 
                    kode_barang as item_code,
                    nama_barang as item_name,
                    stok_tersedia as current_stock,
                    stok_minimum as min_stock,
                    'Sparepart' as category
                FROM inventory_sparepart
                WHERE stok_tersedia < stok_minimum
                ORDER BY (stok_tersedia - stok_minimum) ASC
                LIMIT 10
            ")->getResultArray();
            
            // Get low stock attachments
            $lowAttachments = $db->query("
                SELECT 
                    kode_barang as item_code,
                    nama_barang as item_name,
                    stok_tersedia as current_stock,
                    stok_minimum as min_stock,
                    'Non-Asset' as category
                FROM inventory_attachment
                WHERE stok_tersedia < stok_minimum
                ORDER BY (stok_tersedia - stok_minimum) ASC
                LIMIT 10
            ")->getResultArray();
            
            // Merge and calculate urgency
            $allLowStock = array_merge($lowSpareparts, $lowAttachments);
            foreach ($allLowStock as $item) {
                $deficit = $item['min_stock'] - $item['current_stock'];
                $deficitPercent = $item['min_stock'] > 0 ? ($deficit / $item['min_stock'] * 100) : 0;
                
                // Determine urgency
                if ($deficitPercent >= 50 || $item['current_stock'] == 0) {
                    $urgency = 'High';
                } elseif ($deficitPercent >= 25) {
                    $urgency = 'Medium';
                } else {
                    $urgency = 'Low';
                }
                
                $alerts[] = [
                    'item_code' => $item['item_code'] ?? 'N/A',
                    'item_name' => $item['item_name'] ?? 'Unknown',
                    'current_stock' => (int) $item['current_stock'],
                    'min_stock' => (int) $item['min_stock'],
                    'category' => $item['category'],
                    'urgency' => $urgency
                ];
            }
            
            // If no alerts, add placeholder
            if (empty($alerts)) {
                $alerts[] = [
                    'item_code' => 'N/A',
                    'item_name' => 'No low stock items',
                    'current_stock' => 0,
                    'min_stock' => 0,
                    'category' => 'Info',
                    'urgency' => 'Low'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error fetching low stock alerts: ' . $e->getMessage());
            $alerts[] = [
                'item_code' => 'ERROR',
                'item_name' => 'Unable to load low stock data',
                'current_stock' => 0,
                'min_stock' => 0,
                'category' => 'Error',
                'urgency' => 'Low'
            ];
        }
        
        // Send notification for critical low stock items
        helper('notification');
        if (function_exists('notify_warehouse_stock_alert')) {
            foreach ($alerts as $alert) {
                if ($alert['urgency'] === 'High' || $alert['urgency'] === 'Medium') {
                    notify_warehouse_stock_alert([
                        'item_id' => null,
                        'item_name' => $alert['item_name'],
                        'current_stock' => $alert['current_stock'],
                        'minimum_stock' => $alert['min_stock'],
                        'warehouse_name' => 'Main Warehouse',
                        'unit' => 'pcs',
                        'url' => base_url('/warehouse/inventory')
                    ]);
                }
            }
        }
        
        return $alerts;
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
                
                // Send cross-division notification to Service
                helper('notification');
                if (function_exists('notify_attachment_attached')) {
                    // Get full attachment details with JOIN
                    $fullAttachment = $attachmentModel->getFullAttachmentDetail($attachmentId);
                    $attachmentInfo = $attachmentModel->buildAttachmentInfo($fullAttachment);
                    
                    notify_attachment_attached([
                        'attachment_id' => $attachmentId,
                        'unit_id' => $unitId,
                        'no_unit' => $unit['no_unit'],
                        'tipe_item' => $fullAttachment['tipe_item'] ?? '',
                        'attachment_info' => $attachmentInfo,
                        'performed_by' => session('username') ?? 'System',
                        'performed_at' => date('Y-m-d H:i:s'),
                        'notes' => $notes ?? '',
                        'url' => base_url('/warehouse/unit/view/' . $unitId),
                        'module' => 'inventory'
                    ]);
                }
                
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
            
            if (!$attachmentId || !$toUnitId || !$reason) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }
            
            $db = \Config\Database::connect();
            $db->transStart();
            
            // Get attachment with actual unit data
            $movingAttachment = $attachmentModel->find($attachmentId);
            if (!$movingAttachment) {
                log_message('error', '[Warehouse::swapUnit] Attachment not found: ' . $attachmentId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak ditemukan'
                ]);
            }
            
            // Use ACTUAL from_unit_id from database, not from form
            $actualFromUnitId = $movingAttachment['id_inventory_unit'];
            if (!$actualFromUnitId) {
                log_message('error', '[Warehouse::swapUnit] Attachment not attached to any unit');
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak terpasang di unit manapun'
                ]);
            }
            
            $attachmentType = $movingAttachment['tipe_item'];
            
            log_message('info', '[Warehouse::swapUnit] Request data: ' . json_encode([
                'attachment_id' => $attachmentId,
                'from_unit_id_form' => $fromUnitId,
                'from_unit_id_actual' => $actualFromUnitId,
                'to_unit_id' => $toUnitId,
                'attachment_type' => $attachmentType,
                'reason' => $reason
            ]));
            
            // Check if target unit already has attachment of same type
            $existingAttachment = $db->table('inventory_attachment')
                ->where('id_inventory_unit', $toUnitId)
                ->where('tipe_item', $attachmentType)
                ->where('id_inventory_attachment !=', $attachmentId)
                ->get()->getRowArray();
            
            if ($existingAttachment) {
                log_message('info', '[Warehouse::swapUnit] Found existing attachment, auto-detaching: ' . $existingAttachment['id_inventory_attachment']);
                // Auto-detach existing attachment from target unit
                $detachResult = $attachmentModel->detachFromUnit($existingAttachment['id_inventory_attachment'], 'Auto-detach karena ada replacement (swap)');
                
                if (!$detachResult) {
                    log_message('error', '[Warehouse::swapUnit] Failed to detach existing attachment');
                }
                
                // Log auto-detach
                $this->logActivity('auto_detach', 'inventory_attachment', $existingAttachment['id_inventory_attachment'], "Attachment lama dilepas dari unit tujuan (auto swap)", [
                    'old_attachment_id' => $existingAttachment['id_inventory_attachment'],
                    'moving_attachment_id' => $attachmentId,
                    'unit_id' => $toUnitId
                ]);
            }
            
            // Use swap method with ACTUAL from_unit_id
            log_message('info', '[Warehouse::swapUnit] Calling swapAttachmentBetweenUnits');
            $result = $attachmentModel->swapAttachmentBetweenUnits($attachmentId, $actualFromUnitId, $toUnitId, $reason);
            
            log_message('info', '[Warehouse::swapUnit] Swap result: ' . ($result ? 'true' : 'false'));
            
            if (!$result) {
                $db->transRollback();
                log_message('error', '[Warehouse::swapUnit] swapAttachmentBetweenUnits returned false');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memindahkan attachment - operasi swap gagal'
                ]);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                log_message('error', '[Warehouse::swapUnit] Transaction failed');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memindahkan attachment - transaksi database gagal'
                ]);
            }
            
            // Get unit numbers for message
            $fromUnit = $db->table('inventory_unit')->select('no_unit')->where('id_inventory_unit', $actualFromUnitId)->get()->getRowArray();
            $toUnit = $db->table('inventory_unit')->select('no_unit')->where('id_inventory_unit', $toUnitId)->get()->getRowArray();
            
            // Log activity
            $this->logActivity('swap_unit', 'inventory_attachment', (int)$attachmentId, "Attachment dipindah dari Unit {$fromUnit['no_unit']} ke Unit {$toUnit['no_unit']}", [
                'attachment_id' => $attachmentId,
                'from_unit_id' => $actualFromUnitId,
                'to_unit_id' => $toUnitId,
                'reason' => $reason
            ]);
            
            // Send cross-division notification to Service
            helper('notification');
            if (function_exists('notify_attachment_swapped')) {
                // Get full attachment details with JOIN
                $fullAttachment = $attachmentModel->getFullAttachmentDetail($attachmentId);
                
                // Build attachment_info with proper data
                $attachmentInfo = $attachmentModel->buildAttachmentInfo($fullAttachment);
                
                notify_attachment_swapped([
                    'attachment_id' => $attachmentId,
                    'from_unit_id' => $actualFromUnitId,
                    'from_unit_number' => $fromUnit['no_unit'],
                    'to_unit_id' => $toUnitId,
                    'to_unit_number' => $toUnit['no_unit'],
                    'tipe_item' => $fullAttachment['tipe_item'] ?? '',
                    'attachment_info' => $attachmentInfo,
                    'reason' => $reason,
                    'performed_by' => session('username') ?? 'System',
                    'performed_at' => date('Y-m-d H:i:s'),
                    'url' => base_url('/warehouse/attachment/view/' . $attachmentId),
                    'module' => 'inventory'
                ]);
            }
            
            log_message('info', '[Warehouse::swapUnit] Success');
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil memindahkan attachment dari Unit {$fromUnit['no_unit']} ke Unit {$toUnit['no_unit']}"
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::swapUnit] Exception: ' . $e->getMessage());
            log_message('error', '[Warehouse::swapUnit] Stack trace: ' . $e->getTraceAsString());
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
                
                // Send cross-division notification to Service
                helper('notification');
                if (function_exists('notify_attachment_detached')) {
                    // Get full attachment details with JOIN
                    $fullAttachment = $attachmentModel->getFullAttachmentDetail($attachmentId);
                    $attachmentInfo = $attachmentModel->buildAttachmentInfo($fullAttachment);
                    
                    notify_attachment_detached([
                        'attachment_id' => $attachmentId,
                        'unit_id' => $attachment['id_inventory_unit'] ?? null,
                        'no_unit' => $unitInfo,
                        'tipe_item' => $fullAttachment['tipe_item'] ?? '',
                        'attachment_info' => $attachmentInfo,
                        'reason' => $reason,
                        'new_location' => $newLocation ?? 'Workshop',
                        'performed_by' => session('username') ?? 'System',
                        'performed_at' => date('Y-m-d H:i:s'),
                        'url' => base_url('/warehouse/attachment/view/' . $attachmentId),
                        'module' => 'inventory'
                    ]);
                }
                
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

    /**
     * Get master attachment data for dropdown
     */
    public function masterAttachment()
    {
        try {
            $db = \Config\Database::connect();
            $attachments = $db->table('attachment')
                ->select('id_attachment as id, CONCAT(tipe, " - ", merk, " ", model) as text')
                ->orderBy('tipe', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $attachments
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::masterAttachment] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data attachment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get master baterai data for dropdown
     */
    public function masterBaterai()
    {
        try {
            $db = \Config\Database::connect();
            $batteries = $db->table('baterai')
                ->select('id, CONCAT(merk_baterai, " - ", tipe_baterai, " (", jenis_baterai, ")") as text')
                ->orderBy('merk_baterai', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $batteries
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::masterBaterai] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data baterai: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get master charger data for dropdown
     */
    public function masterCharger()
    {
        try {
            $db = \Config\Database::connect();
            $chargers = $db->table('charger')
                ->select('id_charger as id, CONCAT(merk_charger, " - ", tipe_charger) as text')
                ->orderBy('merk_charger', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $chargers
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::masterCharger] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data charger: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get units data for dropdown
     */
    public function getUnits()
    {
        try {
            $db = \Config\Database::connect();
            $units = $db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit as id, iu.no_unit as nomor_unit, mu.merk_unit as merk, mu.model_unit as model')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->orderBy('iu.no_unit', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $units
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Warehouse::getUnits] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data units: ' . $e->getMessage()
            ]);
        }
    }

}