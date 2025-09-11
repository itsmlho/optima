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
        $data = [
            'title' => 'Warehouse Division | OPTIMA',
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
        $inventoryUnitModel = new InventoryUnitModel();
        if ($this->request->isAJAX()) {
            try {
                $start = $this->request->getPost('start') ?? 0;
                $length = $this->request->getPost('length') ?? 10;
                $searchValue = $this->request->getPost('search')['value'] ?? '';
                $statusFilter = $this->request->getPost('status_unit'); // param tetap sama dari front-end
                $departemenFilter = $this->request->getPost('departemen_id');
                $lokasiFilter = $this->request->getPost('lokasi_unit');

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

                $data = $inventoryUnitModel->getDataTable($start, $length, $orderColumn, $orderDir, $searchValue, $statusFilter, $departemenFilter, $lokasiFilter);
                $recordsFiltered = $inventoryUnitModel->countFiltered($searchValue, $statusFilter, $departemenFilter, $lokasiFilter);
                $recordsTotal = $inventoryUnitModel->countAllData();

                // Hitung dynamic counts untuk semua status (abaikan filter status, tapi hormati search + departemen + lokasi)
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
                if ($lokasiFilter) $countBuilder->like('iu.lokasi_unit', $lokasiFilter);
                $allFiltered = (clone $countBuilder)->countAllResults();
                $stockCount    = (clone $countBuilder)->where('iu.status_unit_id', 7)->countAllResults();
                $nonAssetCount = (clone $countBuilder)->where('iu.status_unit_id', 8)->countAllResults();
                $rentalCount   = (clone $countBuilder)->where('iu.status_unit_id', 3)->countAllResults();
                $soldCount     = (clone $countBuilder)->where('iu.status_unit_id', 9)->countAllResults();
                // $workshopCount = (clone $countBuilder)->where('iu.status_unit_id', 2)->countAllResults();
                $dynamicStats = [
                    'total'    => $allFiltered,
                    'in_stock' => $stockCount,
                    'non_asset' => $nonAssetCount,
                    'rented'   => $rentalCount,
                    'sold'     => $soldCount,
                    // 'workshop' => $workshopCount,
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
        // Tambah count status 8 (Stok Non Aset) jika ada
        try {
            $dbTmp = Database::connect();
            if ($dbTmp->tableExists('inventory_unit')) {
                $stats['non_asset'] = $dbTmp->table('inventory_unit')->where('status_unit_id',8)->countAllResults();
            } else {
                $stats['non_asset'] = 0;
            }
        } catch (\Throwable $e) { $stats['non_asset'] = 0; }
        // Ambil opsi departemen & lokasi bila tabel tersedia untuk filter dropdown
        $db = Database::connect();
        $departemen = [];
        $lokasiList = [];
        try {
            if ($db->tableExists('departemen')) {
                $departemen = $db->table('departemen')->select('id_departemen,nama_departemen')->orderBy('nama_departemen','ASC')->get()->getResultArray();
            }
            // Lokasi unik dari inventory_unit
            if ($db->tableExists('inventory_unit')) {
                $lokasiList = $db->table('inventory_unit')->select('DISTINCT lokasi_unit')->where('lokasi_unit IS NOT NULL')->orderBy('lokasi_unit','ASC')->get()->getResultArray();
            }
        } catch (\Throwable $e) { /* ignore */ }
        $data = [
            'title' => 'Inventory Unit',
            'stats' => $stats,
            'departemen_options' => $departemen,
            'lokasi_options' => array_map(function($r){ return $r['lokasi_unit']; }, $lokasiList),
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
        $out = fopen('php://output', 'w');
        fputcsv($out, ['No Unit','Serial Number','Model Unit','Departemen','Lokasi','Status Unit','Status Aset','Tahun','Tanggal Kirim','Keterangan']);
        foreach ($rows as $r) {
            $noUnit = $r['no_unit'] ?? '';
            // Kosongkan No Unit bila status masih STOCK ASET (belum diberi nomor aset oleh manager)
            $statusLower = strtolower($r['status_unit'] ?? '');
            if ((isset($r['status_unit']) && $statusLower === 'stock aset') || (int)($r['status_unit_id'] ?? 0) === 7) {
                $noUnit = '';
            }
            fputcsv($out, [
                $noUnit,
                $r['serial_number'] ?? '',
                $r['model_unit_display'] ?? '',
                $r['nama_departemen'] ?? '',
                $r['lokasi_unit'] ?? '',
                $r['status_unit'] ?? '',
                $r['status_aset'] ?? '',
                $r['tahun_unit'] ?? '',
                $r['tanggal_kirim'] ?? '',
                $r['keterangan'] ?? ''
            ]);
        }
        fclose($out);
        return $this->response; 
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
        $inventoryUnitModel = new InventoryUnitModel();
        $data = $inventoryUnitModel
            ->select('inventory_unit.id_inventory_unit, inventory_unit.no_unit, inventory_unit.serial_number, inventory_unit.status_unit_id, inventory_unit.lokasi_unit, COALESCE(mu.merk_unit, "Unknown") as merk_unit')
            ->join('model_unit mu', 'mu.id_model_unit = inventory_unit.model_unit_id', 'left')
            ->find($id);
        if ($data) {
            $response_data = [
                'id_inventory_unit' => $data['id_inventory_unit'], // internal id
                'no_unit'           => $data['no_unit'],
                'serial_number_po'  => $data['serial_number'],
                'merk_unit'         => $data['merk_unit'],
                'status_unit'       => $data['status_unit_id'],
                'lokasi_unit'       => $data['lokasi_unit'],
            ];
            return $this->response->setJSON(['success' => true, 'data' => $response_data]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.']);
    }

    /**
     * Mengambil detail lengkap unit dengan semua informasi terkait
     */
    public function getUnitFullDetail($id)
    {
        try {
            $db = \Config\Database::connect();
            $query = $db->query('
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    iu.serial_number as serial_number_po,
                    iu.status_unit_id as status_unit,
                    COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                    iu.lokasi_unit,
                    iu.status_unit_id as status_unit_raw,
                    iu.created_at as tanggal_masuk,
                    iu.keterangan,
                    iu.departemen_id as jenis_unit,
                    iu.model_unit_id,
                    iu.tipe_unit_id,
                    iu.tahun_unit as tahun_po,
                    iu.kapasitas_unit_id as kapasitas_id,
                    iu.model_mast_id as mast_id,
                    iu.sn_mast as sn_mast_po,
                    iu.model_mesin_id as mesin_id,
                    iu.sn_mesin as sn_mesin_po,
                    iu.model_baterai_id as baterai_id,
                    iu.sn_baterai as sn_baterai_po,
                    iu.ban_id,
                    iu.roda_id,
                    iu.valve_id,
                    COALESCE(mu.model_unit, "Unknown") as model_unit,
                    COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "Unknown") as nama_tipe_unit,
                    COALESCE(su.status_unit, "Unknown") as status_unit_name,
                    COALESCE(d.nama_departemen, "Unknown") as nama_departemen,
                    COALESCE(k.kapasitas_unit, 0) as kapasitas_unit,
                    COALESCE(tm.tipe_mast, "-") as tipe_mast,
                    COALESCE(m.merk_mesin, "-") as merk_mesin,
                    COALESCE(m.model_mesin, "-") as model_mesin,
                    COALESCE(b.tipe_baterai, "-") as tipe_baterai,
                    COALESCE(b.merk_baterai, "-") as merk_baterai,
                    COALESCE(tb.tipe_ban, "-") as tipe_ban,
                    COALESCE(jr.tipe_roda, "-") as tipe_roda,
                    COALESCE(v.jumlah_valve, "-") as jumlah_valve,
                    COALESCE(po.no_po, "-") as no_po,
                    po.tanggal_po,
                    COALESCE(po.status, "-") as status_po,
                    COALESCE(s.nama_supplier, "-") as nama_supplier,
                    "Sesuai" as status_verifikasi,
                    "Verifikasi berhasil" as catatan_verifikasi,
                    "Baru" as status_penjualan
                FROM inventory_unit iu
                LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
                LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
                LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
                LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
                LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
                LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
                LEFT JOIN mesin m ON m.id = iu.model_mesin_id
                LEFT JOIN baterai b ON b.id = iu.model_baterai_id
                LEFT JOIN tipe_ban tb ON tb.id_ban = iu.ban_id
                LEFT JOIN jenis_roda jr ON jr.id_roda = iu.roda_id
                LEFT JOIN valve v ON v.id_valve = iu.valve_id
                LEFT JOIN purchase_orders po ON po.id_po = iu.id_po
                LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id
                WHERE iu.id_inventory_unit = ?
            ', [$id]);
            $data = $query->getRowArray();
            if (!$data) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.']);
            }
            return $this->response->setJSON(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Memperbarui data stok unit.
     */
    public function updateUnit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
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
                    'status_unit' => $this->request->getPost('status_unit')
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
            'title' => 'Inventory Attachment | OPTIMA',
            'page_title' => 'Inventory Attachment',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse' => 'Warehouse',
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

}