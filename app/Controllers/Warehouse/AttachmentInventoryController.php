<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use App\Models\InventoryAttachmentModel;
use App\Models\InventoryBatteryModel;
use App\Models\InventoryChargerModel;
use App\Models\InventoryForkModel;
use App\Services\ExportService;

class AttachmentInventoryController extends BaseController
{
    use ActivityLoggingTrait;

    protected $exportService;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->exportService = new ExportService();
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

        // Get data from database
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT
                ia.*,
                iu.no_unit,
                a.tipe, a.merk, a.model
            FROM inventory_attachments ia
            LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ia.inventory_unit_id
            LEFT JOIN attachment a ON a.id_attachment = ia.attachment_type_id
            ORDER BY ia.id DESC
        ");
        $attachments = $query->getResultArray();

        // Prepare headers
        $headers = ['No', 'PO ID', 'No Unit', 'Attachment Detail', 'SN Attachment', 'Status', 'Created Date'];

        // Prepare data rows
        $data = [];
        $no = 1;
        foreach ($attachments as $attachment) {
            $detail = trim(($attachment['merk'] ?? '') . ' ' . ($attachment['model'] ?? '') . ' ' . ($attachment['tipe'] ?? ''));
            $data[] = [
                $no++,
                $attachment['po_id'] ?? '',
                $attachment['no_unit'] ?? '',
                $detail,
                $attachment['serial_number'] ?? '',
                $attachment['status'] ?? '',
                $attachment['created_at'] ?? ''
            ];
        }

        // Export using ExportService
        return $this->exportService->exportToExcel($data, $headers, 'Attachment Inventory Detailed');
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

        // Get data from database
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT
                ia.*,
                iu.no_unit,
                b.merk_baterai, b.tipe_baterai, b.jenis_baterai
            FROM inventory_batteries ia
            LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ia.inventory_unit_id
            LEFT JOIN baterai b ON b.id = ia.battery_type_id
            ORDER BY ia.id DESC
        ");
        $batteries = $query->getResultArray();

        // Prepare headers
        $headers = ['No', 'PO ID', 'No Unit', 'Battery Detail', 'SN Battery', 'Status', 'Created Date'];

        // Prepare data rows
        $data = [];
        $no = 1;
        foreach ($batteries as $battery) {
            $detail = trim(($battery['merk_baterai'] ?? '') . ' ' . ($battery['tipe_baterai'] ?? '') . ' ' . ($battery['jenis_baterai'] ?? ''));
            $data[] = [
                $no++,
                $battery['po_id'] ?? '',
                $battery['no_unit'] ?? '',
                $detail,
                $battery['serial_number'] ?? '',
                $battery['status'] ?? '',
                $battery['created_at'] ?? ''
            ];
        }

        // Export using ExportService
        return $this->exportService->exportToExcel($data, $headers, 'Battery Inventory Detailed');
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

        // Get data from database
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT
                ia.*,
                iu.no_unit,
                c.merk_charger,
                c.tipe_charger
            FROM inventory_chargers ia
            LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ia.inventory_unit_id
            LEFT JOIN charger c ON c.id_charger = ia.charger_type_id
            ORDER BY ia.id DESC
        ");
        $chargers = $query->getResultArray();

        // Prepare headers
        $headers = ['No', 'PO ID', 'No Unit', 'Charger Detail', 'SN Charger', 'Status', 'Created Date'];

        // Prepare data rows
        $data = [];
        $no = 1;
        foreach ($chargers as $charger) {
            $detail = trim(($charger['merk_charger'] ?? '') . ' ' . ($charger['tipe_charger'] ?? ''));
            if (empty($detail)) {
                $detail = '-';
            }
            $data[] = [
                $no++,
                $charger['po_id'] ?? '-',
                $charger['no_unit'] ?? '-',
                $detail,
                $charger['serial_number'] ?? '-',
                $charger['status'] ?? '-',
                $charger['created_at'] ?? '-'
            ];
        }

        // Export using ExportService
        return $this->exportService->exportToExcel($data, $headers, 'Charger Inventory Detailed');
    }

    public function inventAttachment()
    {
        if ($this->request->isAJAX()) {
            try {
                $request = [
                    'start' => $this->request->getPost('start'),
                    'length' => $this->request->getPost('length'),
                    'search' => $this->request->getPost('search'),
                    'order' => $this->request->getPost('order'),
                    'status_unit' => $this->request->getPost('status_unit'),
                    'tipe_item' => $this->request->getPost('tipe_item'),
                    'status_filter' => $this->request->getPost('status_filter'),
                    'model_filter' => $this->request->getPost('model_filter'),
                    'chemistry_filter' => $this->request->getPost('chemistry_filter')
                ];

                // Debug logging
                log_message('debug', '[AttachmentInventoryController::inventAttachment] Request: ' . json_encode($request));

                // Determine which model to use based on tipe_item
                $tipeItem = strtolower($request['tipe_item'] ?? 'attachment');

                switch ($tipeItem) {
                    case 'battery':
                        $model = new InventoryBatteryModel();
                        break;
                    case 'charger':
                        $model = new InventoryChargerModel();
                        break;
                    case 'fork':
                        $model = new InventoryForkModel();
                        break;
                    case 'attachment':
                    default:
                        $model = new InventoryAttachmentModel();
                        break;
                }

                $result = $model->getDataTable($request);

                return $this->response->setJSON([
                    'draw' => $this->request->getPost('draw'),
                    'recordsTotal' => $model->countAll(),
                    'recordsFiltered' => $result['recordsFiltered'],
                    'data' => $result['data'],
                    'csrf_hash' => csrf_hash()
                ]);
            } catch (\Exception $e) {
                log_message('error', '[AttachmentInventoryController::inventAttachment] Error: ' . $e->getMessage());
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

        // Fetch all 4 type stats in a single UNION ALL query (4 queries → 1)
        $allStats        = InventoryAttachmentModel::getAllTypesStats();
        $attachmentStats = $allStats['attachment'];
        $batteryStats    = $allStats['battery'];
        $chargerStats    = $allStats['charger'];
        $forkStats       = $allStats['fork'];

        // Send stats per type so JavaScript can update status counts dynamically
        $detailed_stats = [
            'by_type' => [
                'attachment' => $attachmentStats['total'],
                'battery' => $batteryStats['total'],
                'charger' => $chargerStats['total'],
                'fork' => $forkStats['total'],
                'total' => $attachmentStats['total'] + $batteryStats['total'] + $chargerStats['total'] + $forkStats['total']
            ],
            // Stats per type for dynamic status filter updates
            'attachment' => $attachmentStats,
            'battery' => $batteryStats,
            'charger' => $chargerStats,
            'fork' => $forkStats,
            // Default to attachment stats (initial selected type)
            'by_status' => [
                'all' => $attachmentStats['total'],
                'available' => $attachmentStats['available'],
                'in_use' => $attachmentStats['in_use'],
                'spare' => $attachmentStats['spare'],
                'maintenance' => $attachmentStats['maintenance'],
                'broken' => $attachmentStats['broken'],
            ]
        ];

        $data = [
            'title' => 'Inventory Attachment',
            'page_title' => 'Inventory Attachment',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/inventory/attachments' => 'Inventory Attachment'
            ],
            'stats' => $attachmentStats,
            'detailed_stats' => $detailed_stats
        ];

        return view('warehouse/inventory/attachments/index', $data);
    }

    public function inventBatteryCharger()
    {
        if ($this->request->isAJAX()) {
            $dataType = $this->request->getPost('data_type') ?: 'battery';
            try {
                if ($dataType === 'charger') {
                    $model = new InventoryChargerModel();
                    $request = [
                        'start'         => $this->request->getPost('start'),
                        'length'        => $this->request->getPost('length'),
                        'search'        => $this->request->getPost('search'),
                        'order'         => $this->request->getPost('order'),
                        'tipe_item'     => 'charger',
                        'status_filter' => $this->request->getPost('status_filter'),
                    ];
                    $result = $model->getDataTable($request);
                    return $this->response->setJSON([
                        'draw'            => $this->request->getPost('draw'),
                        'recordsTotal'    => $model->countAll(),
                        'recordsFiltered' => $result['recordsFiltered'],
                        'data'            => $result['data'],
                        'csrf_hash'       => csrf_hash(),
                    ]);
                } else {
                    $model = new InventoryBatteryModel();
                    $request = [
                        'start'            => $this->request->getPost('start'),
                        'length'           => $this->request->getPost('length'),
                        'search'           => $this->request->getPost('search'),
                        'order'            => $this->request->getPost('order'),
                        'tipe_item'        => 'battery',
                        'status_filter'    => $this->request->getPost('status_filter'),
                        'chemistry_filter' => $this->request->getPost('chemistry_filter'),
                    ];
                    $result = $model->getDataTable($request);
                    return $this->response->setJSON([
                        'draw'            => $this->request->getPost('draw'),
                        'recordsTotal'    => $model->countAll(),
                        'recordsFiltered' => $result['recordsFiltered'],
                        'data'            => $result['data'],
                        'csrf_hash'       => csrf_hash(),
                    ]);
                }
            } catch (\Exception $e) {
                log_message('error', '[inventBatteryCharger] ' . $e->getMessage());
                return $this->response->setJSON([
                    'draw' => $this->request->getPost('draw'),
                    'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [],
                    'error' => $e->getMessage(), 'csrf_hash' => csrf_hash(),
                ])->setStatusCode(500);
            }
        }

        $allStats = InventoryAttachmentModel::getAllTypesStats();
        $batStats = $allStats['battery'];
        $chgStats = $allStats['charger'];

        // Chemistry breakdown for battery (LEAD ACID / LITHIUM)
        $db = \Config\Database::connect();
        $chemRows = $db->table('inventory_batteries ib')
            ->select('b.jenis_baterai, COUNT(*) as cnt')
            ->join('baterai b', 'b.id = ib.battery_type_id', 'left')
            ->groupBy('b.jenis_baterai')
            ->get()->getResultArray();
        $leadAcidCount = 0;
        $lithiumCount  = 0;
        foreach ($chemRows as $row) {
            $tipe = strtoupper((string)($row['jenis_baterai'] ?? ''));
            if (str_contains($tipe, 'LEAD') || str_contains($tipe, 'ACID')) $leadAcidCount += (int)$row['cnt'];
            elseif (str_contains($tipe, 'LITHI') || str_contains($tipe, 'LI-')) $lithiumCount += (int)$row['cnt'];
        }

        $data = [
            'title'           => 'Battery & Charger Inventory',
            'page_title'      => 'Battery & Charger Inventory',
            'breadcrumbs'     => ['/' => 'Dashboard', '/warehouse/inventory/battery-charger' => 'Battery & Charger'],
            'bat_stats'       => $batStats,
            'chg_stats'       => $chgStats,
            'lead_acid_count' => $leadAcidCount,
            'lithium_count'   => $lithiumCount,
        ];

        return view('warehouse/inventory/battery-charger/index', $data);
    }

    public function getAttachmentDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        try {
            // Try to find in all tables (with tipe_item to differentiate)
            $attachment = null;
            $tipe = null;

            // Try attachment table first
            $attachmentModel = new InventoryAttachmentModel();
            $attachment = $attachmentModel->getAttachmentDetail($id);
            if ($attachment) {
                $attachment['tipe_item'] = 'attachment';
                $tipe = 'attachment';
            }

            // If not found, try battery table
            if (!$attachment) {
                $batteryModel = new InventoryBatteryModel();
                $attachment = $batteryModel->getBatteryDetail($id);
                if ($attachment) {
                    $attachment['tipe_item'] = 'battery';
                    $tipe = 'battery';
                }
            }

            // If still not found, try charger table
            if (!$attachment) {
                $chargerModel = new InventoryChargerModel();
                $attachment = $chargerModel->getChargerDetail($id);
                if ($attachment) {
                    $attachment['tipe_item'] = 'charger';
                    $tipe = 'charger';
                }
            }
            if (!$attachment) {
                $forkModel = new InventoryForkModel();
                $attachment = $forkModel->getForkDetail($id);
                if ($attachment) {
                    $attachment['tipe_item'] = 'fork';
                    $tipe = 'fork';
                }
            }

            if (!$attachment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Add backward compatibility fields
            $attachment['id_inventory_attachment'] = $attachment['id'];

            // Map new column names to old names for view compatibility
            if ($tipe === 'attachment') {
                $attachment['kondisi_fisik'] = $attachment['physical_condition'] ?? '';
                $attachment['lokasi_penyimpanan'] = $attachment['storage_location'] ?? '';
                $attachment['attachment_status'] = $attachment['status'] ?? '';
                $attachment['id_inventory_unit'] = $attachment['inventory_unit_id'] ?? null;
            } elseif ($tipe === 'battery') {
                $attachment['kondisi_fisik'] = $attachment['physical_condition'] ?? '';
                $attachment['lokasi_penyimpanan'] = $attachment['storage_location'] ?? '';
                $attachment['attachment_status'] = $attachment['status'] ?? '';
                $attachment['id_inventory_unit'] = $attachment['inventory_unit_id'] ?? null;
            } elseif ($tipe === 'charger') {
                $attachment['kondisi_fisik'] = $attachment['physical_condition'] ?? '';
                $attachment['lokasi_penyimpanan'] = $attachment['storage_location'] ?? '';
                $attachment['attachment_status'] = $attachment['status'] ?? '';
                $attachment['id_inventory_unit'] = $attachment['inventory_unit_id'] ?? null;
            } elseif ($tipe === 'fork') {
                $attachment['kondisi_fisik'] = $attachment['physical_condition'] ?? '';
                $attachment['lokasi_penyimpanan'] = $attachment['storage_location'] ?? '';
                $attachment['attachment_status'] = $attachment['status'] ?? '';
                $attachment['id_inventory_unit'] = $attachment['inventory_unit_id'] ?? null;
            }

            // Enrich with unit merk/model/type if attached to a unit
            $unitId = $attachment['inventory_unit_id'] ?? null;
            if ($unitId) {
                $db = \Config\Database::connect();
                $unitRow = $db->table('inventory_unit iu')
                    ->select('iu.serial_number as unit_sn, mu.merk_unit as unit_merk, mu.model_unit as unit_model, iu.fuel_type as unit_jenis')
                    ->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left')
                    ->where('iu.id_inventory_unit', $unitId)
                    ->get()->getRowArray();
                if ($unitRow) {
                    $attachment['unit_sn']    = $unitRow['unit_sn']    ?? null;
                    $attachment['unit_merk']  = $unitRow['unit_merk']  ?? null;
                    $attachment['unit_model'] = $unitRow['unit_model'] ?? null;
                    $attachment['unit_jenis'] = $unitRow['unit_jenis'] ?? null;
                }
            }

            // If item is SOLD, enrich with sale record from component_sale_records
            $attachment['sale_record'] = null;
            if (($attachment['status'] ?? '') === 'SOLD') {
                try {
                    $db = $db ?? \Config\Database::connect();
                    if ($db->tableExists('component_sale_records')) {
                        $typeMap = [
                            'attachment' => 'ATTACHMENT',
                            'battery'    => 'BATTERY',
                            'charger'    => 'CHARGER',
                            'fork'       => 'FORK',
                        ];
                        $assetType = $typeMap[$tipe] ?? strtoupper($tipe);
                        $saleRow = $db->table('component_sale_records csr')
                            ->select('csr.no_dokumen, csr.tanggal_jual, csr.nama_pembeli,
                                      csr.alamat_pembeli, csr.telepon_pembeli, csr.harga_jual,
                                      csr.metode_pembayaran, csr.no_bast, csr.status,
                                      csr.linked_unit_sale_id,
                                      CONCAT(IFNULL(u.first_name,""), " ", IFNULL(u.last_name,"")) AS sold_by_name')
                            ->join('users u', 'u.id = csr.sold_by_user_id', 'left')
                            ->where('csr.asset_id', (int)$id)
                            ->where('csr.asset_type', $assetType)
                            ->where('csr.status', 'COMPLETED')
                            ->orderBy('csr.tanggal_jual', 'DESC')
                            ->limit(1)
                            ->get()->getRowArray();
                        $attachment['sale_record'] = $saleRow ?: null;
                    }
                } catch (\Throwable $e2) {
                    log_message('warning', 'getAttachmentDetail sale_record: ' . $e2->getMessage());
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $attachment
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::getAttachmentDetail] Error: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Mengambil history/timeline lengkap attachment/battery/charger
     * Sumber: system_activity_log (real-time log) + seed events (legacy support)
     */
    public function getAttachmentHistory($id)
    {
        try {
            $db = \Config\Database::connect();
            $timeline = [];

            // 0. Validasi component exists in all 3 tables
            $componentHelper = new \App\Models\InventoryComponentHelper();
            $componentType = $componentHelper->detectComponentType($id);

            if (!$componentType) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Component tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Get component details based on type
            if ($componentType === 'battery') {
                $attachment = $componentHelper->getBatteryByInventoryId($id);
            } elseif ($componentType === 'charger') {
                $attachment = $componentHelper->getChargerByInventoryId($id);
            } elseif ($componentType === 'fork') {
                $attachment = $componentHelper->getForkByInventoryId($id);
            } else {
                $attachment = $componentHelper->getAttachmentByInventoryId($id);
            }

            if (!$attachment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Component tidak ditemukan'
                ])->setStatusCode(404);
            }

            $sn         = $attachment['sn_attachment'] ?? $attachment['sn_baterai'] ?? $attachment['sn_charger'] ?? $attachment['sn_fork'] ?? '-';
            $tipeLabel  = ucfirst($componentType ?? 'item');

            // ===== REAL LOG EVENTS =====
            // Baca dari system_activity_logs untuk semua event yang sudah di-log
            $activityLogModel = new \App\Models\SystemActivityLogModel();
            $logRows = $activityLogModel->findByRelatedEntity('inventory_attachment', (int)$id);

            $actionMap = [
                'attach_to_unit'       => ['icon' => 'fas fa-link',          'color' => 'primary'],
                'detach_from_unit'     => ['icon' => 'fas fa-unlink',         'color' => 'danger'],
                'auto_detach'          => ['icon' => 'fas fa-exchange-alt',   'color' => 'warning'],
                'swap_unit'            => ['icon' => 'fas fa-random',         'color' => 'info'],
                'attachment_updated'   => ['icon' => 'fas fa-pen',            'color' => 'secondary'],
                'item_created'         => ['icon' => 'fas fa-plus-circle',    'color' => 'success'],
                'unit_updated'         => ['icon' => 'fas fa-sync-alt',       'color' => 'secondary'],
            ];

            foreach ($logRows as $log) {
                $action  = $log['action'] ?? 'unknown';
                $meta    = $actionMap[$action] ?? ['icon' => 'fas fa-history', 'color' => 'muted'];
                $context = is_string($log['context_data'] ?? null)
                    ? (json_decode($log['context_data'], true) ?? [])
                    : ($log['context_data'] ?? []);
                $changes = is_string($log['changes_summary'] ?? null)
                    ? (json_decode($log['changes_summary'], true) ?? [])
                    : ($log['changes_summary'] ?? []);

                $details = [];
                if (!empty($context['unit_no_unit']))    $details['Unit']        = $context['unit_no_unit'];
                if (!empty($context['unit_status']))     $details['Status Unit'] = $context['unit_status'];
                if (!empty($context['location']))        $details['Lokasi']      = $context['location'];
                if (!empty($context['kondisi_fisik']))   $details['Kondisi']     = $context['kondisi_fisik'];
                if (!empty($context['description']))     $details['Keterangan']  = $context['description'];
                if (!empty($context['performed_by']))    $details['Oleh']        = $context['performed_by'];

                // Tampilkan diff untuk update events
                if (!empty($changes) && is_array($changes)) {
                    foreach ($changes as $field => $diff) {
                        if (is_array($diff) && isset($diff['old'], $diff['new'])) {
                            $details["$field"] = ($diff['old'] ?? '-') . ' → ' . ($diff['new'] ?? '-');
                        }
                    }
                }

                $title = match($action) {
                    'attach_to_unit'     => 'Dipasang ke Unit ' . ($context['unit_no_unit'] ?? ''),
                    'detach_from_unit'   => 'Dilepas dari Unit ' . ($context['unit_no_unit'] ?? ''),
                    'auto_detach'        => 'Auto-dilepas (Swap) dari Unit ' . ($context['unit_no_unit'] ?? ''),
                    'swap_unit'          => 'Unit Di-swap — Pindah ke Unit ' . ($context['new_unit_no_unit'] ?? ''),
                    'attachment_updated' => 'Data ' . $tipeLabel . ' Diperbarui',
                    'item_created'       => $tipeLabel . ' Ditambahkan ke Inventory',
                    default              => ucfirst(str_replace('_', ' ', $action)),
                };

                $timeline[] = [
                    'type'        => $action,
                    'icon'        => $meta['icon'],
                    'color'       => $meta['color'],
                    'date'        => $log['created_at'],
                    'title'       => $title,
                    'subtitle'    => $log['description'] ?? '',
                    'details'     => $details,
                    'performed_by'=> $log['performed_by'] ?? null,
                    'log_id'      => $log['id'],
                ];
            }

            // ===== COMPONENT AUDIT LOG EVENTS (SPK, WO, Unit Audit) =====
            // Pull events from component_audit_log (logged by ComponentAuditService)
            if ($db->tableExists('component_audit_log')) {
                $auditService = new \App\Services\ComponentAuditService($db);
                $auditTypeMap = [
                    'attachment' => 'ATTACHMENT',
                    'battery'    => 'BATTERY',
                    'charger'    => 'CHARGER',
                    'fork'       => 'FORK',
                ];
                $auditComponentType = $auditTypeMap[$componentType] ?? null;

                if ($auditComponentType) {
                    $auditRows = $auditService->getComponentHistory($auditComponentType, (int)$id, 100);

                    // Collect log IDs already captured from system_activity_logs to avoid duplicates
                    $loggedAuditIds = [];
                    foreach ($logRows as $log) {
                        $ctx = is_string($log['context_data'] ?? null)
                            ? (json_decode($log['context_data'], true) ?? [])
                            : ($log['context_data'] ?? []);
                        if (!empty($ctx['audit_log_id'])) {
                            $loggedAuditIds[] = (int)$ctx['audit_log_id'];
                        }
                    }

                    $eventTypeIconMap = [
                        'ASSIGNED'      => ['icon' => 'fas fa-link',          'color' => 'primary'],
                        'REMOVED'       => ['icon' => 'fas fa-unlink',         'color' => 'danger'],
                        'TRANSFERRED'   => ['icon' => 'fas fa-exchange-alt',   'color' => 'info'],
                        'REPLACED'      => ['icon' => 'fas fa-sync-alt',       'color' => 'warning'],
                        'BULK_RELEASED' => ['icon' => 'fas fa-boxes',          'color' => 'secondary'],
                        'VERIFIED'      => ['icon' => 'fas fa-check-circle',   'color' => 'success'],
                    ];

                    $triggeredByLabelMap = [
                        'PERSIAPAN_UNIT'         => 'SPK – Persiapan Unit',
                        'KANIBAL_PERSIAPAN_UNIT' => 'SPK – Kanibal Persiapan',
                        'FABRIKASI'              => 'SPK – Fabrikasi',
                        'KANIBAL_FABRIKASI'      => 'SPK – Kanibal Fabrikasi',
                        'UNIT_VERIFICATION'      => 'Work Order – Verifikasi Unit',
                        'UNIT_AUDIT_VERIFICATION'=> 'Unit Audit',
                        'MANUAL'                 => 'Manual',
                    ];

                    foreach ($auditRows as $audit) {
                        if (in_array((int)$audit['id'], $loggedAuditIds)) {
                            continue;
                        }

                        $eventType = strtoupper($audit['event_type'] ?? 'ASSIGNED');
                        $meta      = $eventTypeIconMap[$eventType] ?? ['icon' => 'fas fa-history', 'color' => 'secondary'];

                        $triggeredBy = $audit['triggered_by'] ?? 'MANUAL';
                        $sourceLabel = $triggeredByLabelMap[$triggeredBy] ?? ucfirst(strtolower(str_replace('_', ' ', $triggeredBy)));

                        // Resolve SPK / Work Order numbers if available
                        $spkNumber = null;
                        if (!empty($audit['spk_id'])) {
                            $spkRow = $db->table('spk')
                                ->select('nomor_spk')
                                ->where('id', $audit['spk_id'])
                                ->get()
                                ->getRowArray();
                            $spkNumber = $spkRow['nomor_spk'] ?? null;
                        }

                        $woNumber = null;
                        if (!empty($audit['work_order_id'])) {
                            $woRow = $db->table('work_orders')
                                ->select('work_order_number')
                                ->where('id', $audit['work_order_id'])
                                ->get()
                                ->getRowArray();
                            $woNumber = $woRow['work_order_number'] ?? null;
                        }

                        // Resolve unit numbers for from/to unit
                        $fromUnitNo = null;
                        $toUnitNo   = null;
                        if (!empty($audit['from_unit_id'])) {
                            $fromUnit = $db->table('inventory_unit')
                                ->select('no_unit')
                                ->where('id_inventory_unit', $audit['from_unit_id'])
                                ->get()->getRowArray();
                            $fromUnitNo = $fromUnit['no_unit'] ?? "Unit #{$audit['from_unit_id']}";
                        }
                        if (!empty($audit['to_unit_id'])) {
                            $toUnit = $db->table('inventory_unit')
                                ->select('no_unit')
                                ->where('id_inventory_unit', $audit['to_unit_id'])
                                ->get()->getRowArray();
                            $toUnitNo = $toUnit['no_unit'] ?? "Unit #{$audit['to_unit_id']}";
                        }

                        $title = $audit['event_title'] ?? match($eventType) {
                            'ASSIGNED'      => 'Dipasang ke Unit ' . ($toUnitNo ?? '-'),
                            'REMOVED',
                            'BULK_RELEASED' => 'Dilepas dari Unit ' . ($fromUnitNo ?? '-'),
                            'TRANSFERRED'   => 'Dipindah: ' . ($fromUnitNo ?? '-') . ' → ' . ($toUnitNo ?? '-'),
                            'REPLACED'      => 'Diganti pada Unit ' . ($toUnitNo ?? $fromUnitNo ?? '-'),
                            'VERIFIED'      => 'Diverifikasi pada Unit ' . ($toUnitNo ?? '-'),
                            default         => ucfirst(strtolower($eventType)),
                        };

                        $details = array_filter([
                            'Dari Unit'   => $fromUnitNo,
                            'Ke Unit'     => $toUnitNo,
                            'SPK'         => $spkNumber,
                            'Work Order'  => $woNumber,
                            'Sumber'      => $sourceLabel,
                            'Stage'       => $audit['stage_name'] ?? null,
                            'Catatan'     => $audit['notes'] ?? null,
                        ]);

                        $timeline[] = [
                            'type'         => 'audit_' . strtolower($eventType),
                            'icon'         => $meta['icon'],
                            'color'        => $meta['color'],
                            'date'         => $audit['performed_at'] ?? $audit['created_at'],
                            'title'        => $title,
                            'subtitle'     => trim($sourceLabel . ' ' . ($spkNumber ? '(' . $spkNumber . ')' : ($woNumber ? '(' . $woNumber . ')' : ''))),
                            'description'  => $audit['notes'] ?? null,
                            'details'      => $details,
                            'performed_by' => $audit['performed_by'] ?? null,
                            'source'       => 'audit_log',
                            'audit_log_id' => (int)$audit['id'],
                        ];
                    }
                }
            }

            // ===== SEED EVENTS =====
            // Untuk data lama (sebelum audit log ada), seed dari data struktural DB

            // Seed: Item masuk pertama kali (jika belum ada log item_created)
            $hasItemCreated = !empty(array_filter($logRows, fn($r) => ($r['action'] ?? '') === 'item_created'));
            $entryDate = $attachment['tanggal_masuk'] ?? $attachment['created_at'];
            if ($entryDate && !$hasItemCreated) {
                $timeline[] = [
                    'type'    => 'item_created',
                    'icon'    => 'fas fa-plus-circle',
                    'color'   => 'success',
                    'date'    => $entryDate,
                    'title'   => $tipeLabel . ' Masuk Inventory',
                    'subtitle'=> 'SN: ' . $sn,
                    'details' => array_filter([
                        'Status'       => $attachment['attachment_status'] ?? null,
                        'Lokasi Awal'  => $attachment['lokasi_penyimpanan'] ?? null,
                        'Kondisi'      => $attachment['kondisi_fisik'] ?? null,
                    ]),
                ];
            }

            // Seed: Saat ini terpasang di unit (jika belum ada log attach_to_unit yang lebih baru)
            if (!empty($attachment['id_inventory_unit'])) {
                $hasAttachLog = !empty(array_filter($logRows, fn($r) => in_array($r['action'] ?? '', ['attach_to_unit', 'swap_unit'])));
                if (!$hasAttachLog) {
                    $unitRow = $db->query('
                        SELECT iu.id_inventory_unit, iu.no_unit, iu.serial_number,
                               su.status_unit as nama_status
                        FROM inventory_unit iu
                        LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
                        WHERE iu.id_inventory_unit = ?
                        LIMIT 1
                    ', [$attachment['id_inventory_unit']])->getRowArray();

                    if ($unitRow) {
                        $timeline[] = [
                            'type'    => 'attach_to_unit',
                            'icon'    => 'fas fa-link',
                            'color'   => 'primary',
                            'date'    => $attachment['tanggal_masuk'] ?? $attachment['created_at'],
                            'title'   => 'Terpasang di Unit ' . ($unitRow['no_unit'] ?? '-'),
                            'subtitle'=> 'SN Unit: ' . ($unitRow['serial_number'] ?? '-'),
                            'details' => array_filter([
                                'No Unit'      => $unitRow['no_unit'] ?? null,
                                'Status Unit'  => $unitRow['nama_status'] ?? null,
                            ]),
                        ];
                    }
                }
            }

            // ===== MOVEMENT EVENTS from unit_movements =====
            // Map component type to the enum value used in unit_movements.component_type
            $movementTypeMap = [
                'attachment' => 'ATTACHMENT',
                'battery'    => 'BATTERY',
                'charger'    => 'CHARGER',
                'fork'       => 'FORK',
            ];
            $movementComponentType = $movementTypeMap[$componentType] ?? null;

            if ($movementComponentType && $db->tableExists('unit_movements')) {
                $movements = $db->table('unit_movements')
                    ->where('component_id', $id)
                    ->where('component_type', $movementComponentType)
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getResultArray();

                // Collect movement IDs already represented in system_activity_logs to avoid duplicates
                $loggedMovementIds = [];
                foreach ($logRows as $log) {
                    $ctx = is_string($log['context_data'] ?? null)
                        ? (json_decode($log['context_data'], true) ?? [])
                        : ($log['context_data'] ?? []);
                    if (!empty($ctx['movement_id'])) {
                        $loggedMovementIds[] = (int)$ctx['movement_id'];
                    }
                }

                foreach ($movements as $mv) {
                    if (in_array((int)$mv['id'], $loggedMovementIds)) {
                        continue; // already represented via system_activity_log
                    }

                    $origin      = $mv['origin_location']      ?? $mv['origin_type']      ?? '-';
                    $destination = $mv['destination_location'] ?? $mv['destination_type'] ?? '-';
                    $status      = $mv['status'] ?? 'DRAFT';

                    $statusLabel = match($status) {
                        'DRAFT'      => 'Draft',
                        'IN_TRANSIT' => 'Dalam Perjalanan',
                        'ARRIVED'    => 'Tiba',
                        'CANCELLED'  => 'Dibatalkan',
                        default      => $status,
                    };

                    $icon  = match($status) {
                        'ARRIVED'    => 'fas fa-check-circle',
                        'IN_TRANSIT' => 'fas fa-truck',
                        'CANCELLED'  => 'fas fa-times-circle',
                        default      => 'fas fa-clipboard-list',
                    };
                    $color = match($status) {
                        'ARRIVED'    => 'success',
                        'IN_TRANSIT' => 'primary',
                        'CANCELLED'  => 'danger',
                        default      => 'secondary',
                    };

                    $sjNumber = $mv['surat_jalan_number'] ?? null;
                    $mvNumber = $mv['movement_number']    ?? null;

                    $details = array_filter([
                        'Dari'          => $origin,
                        'Ke'            => $destination,
                        'Status'        => $statusLabel,
                        'Driver'        => $mv['driver_name']     ?? null,
                        'Kendaraan'     => $mv['vehicle_number']  ?? null,
                        'No. SJ'        => $sjNumber,
                        'No. Movement'  => $mvNumber,
                        'Catatan'       => $mv['notes']           ?? null,
                    ]);

                    $eventDate = $mv['movement_date'] ?? $mv['updated_at'] ?? $mv['created_at'];

                    $timeline[] = [
                        'type'         => 'movement_' . strtolower($status),
                        'icon'         => $icon,
                        'color'        => $color,
                        'date'         => $eventDate,
                        'title'        => "Surat Jalan: {$origin} → {$destination}",
                        'subtitle'     => $sjNumber ? "SJ: {$sjNumber}" : ($mvNumber ? "MV: {$mvNumber}" : ''),
                        'description'  => "Status: {$statusLabel}" . ($mv['driver_name'] ? " | Driver: {$mv['driver_name']}" : ''),
                        'details'      => $details,
                        'ref_number'   => $sjNumber ?? $mvNumber,
                        'source'       => 'movement',
                        'movement_id'  => (int)$mv['id'],
                    ];
                }
            }

            // Sort timeline by date descending (newest first)
            usort($timeline, function ($a, $b) {
                $dateA = strtotime($a['date'] ?? '1970-01-01');
                $dateB = strtotime($b['date'] ?? '1970-01-01');
                return $dateB - $dateA;
            });

            return $this->response->setJSON([
                'success'       => true,
                'attachment_id' => $id,
                'sn'            => $sn,
                'tipe'          => $tipeLabel,
                'total'         => count($timeline),
                'timeline'      => $timeline,
            ]);

        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::getAttachmentHistory] Error: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat history. Silakan coba lagi.',
            ])->setStatusCode(500);
        }
    }

    public function updateAttachment($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        try {
            $tipeItem = strtolower($this->request->getPost('tipe_item') ?? 'attachment');
            switch ($tipeItem) {
                case 'battery':
                    $model = new InventoryBatteryModel();
                    break;
                case 'charger':
                    $model = new InventoryChargerModel();
                    break;
                case 'fork':
                    $model = new InventoryForkModel();
                    break;
                default:
                    $model = new InventoryAttachmentModel();
                    $tipeItem = 'attachment';
            }

            $existing = $model->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                ])->setStatusCode(404);
            }

            $rawSerialNumber = trim((string)($this->request->getPost('serial_number') ?? ''));
            $rawItemNumber   = strtoupper(trim((string)($this->request->getPost('item_number') ?? '')));

            $updateData = array_filter([
                'status'            => $this->request->getPost('status'),
                'storage_location'  => $this->request->getPost('storage_location'),
                'physical_condition'=> $this->request->getPost('physical_condition'),
                'completeness'      => $this->request->getPost('completeness'),
                'notes'             => $this->request->getPost('notes'),
                'serial_number'     => $rawSerialNumber !== '' ? $rawSerialNumber : null,
                'item_number'       => $rawItemNumber   !== '' ? $rawItemNumber   : null,
            ], fn($v) => $v !== null && $v !== '');

            // Pass id into updateData so {id} placeholder in is_unique validation resolves correctly
            $updateDataWithId = array_merge($updateData, ['id' => (int)$id]);
            if ($model->update($id, $updateDataWithId)) {
                $this->logUpdate('inventory_attachment', (int)$id,
                    array_intersect_key($existing, $updateData),
                    $updateData,
                    [
                        'description'    => ucfirst($tipeItem) . ' data updated',
                        'workflow_stage' => 'attachment_updated',
                        'relations'      => ['inventory_attachment' => [(int)$id]]
                    ]
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => ucfirst($tipeItem) . ' data successfully updated'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update item',
                'errors'  => $model->errors()
            ])->setStatusCode(400);

        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::updateAttachment] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    public function deleteAttachment($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }
        $canDeleteItem = $this->hasPermission('warehouse.attachment_inventory.delete')
            || $this->hasPermission('warehouse.attachment_inventory.edit')
            || $this->hasPermission('warehouse.unit_inventory.edit')
            || $this->canDelete('warehouse')
            || $this->canManage('warehouse');
        if (!$canDeleteItem) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        try {
            $db = \Config\Database::connect();
            // Find the item across all tables
            $attachmentModel = new InventoryAttachmentModel();
            $batteryModel    = new InventoryBatteryModel();
            $chargerModel    = new InventoryChargerModel();
            $forkModel       = new InventoryForkModel();

            $item     = $attachmentModel->find($id);
            $tipeItem = 'attachment';
            $model    = $attachmentModel;

            if (!$item) {
                $item = $batteryModel->find($id);
                if ($item) { $tipeItem = 'battery'; $model = $batteryModel; }
            }
            if (!$item) {
                $item = $chargerModel->find($id);
                if ($item) { $tipeItem = 'charger'; $model = $chargerModel; }
            }
            if (!$item) {
                $item = $forkModel->find($id);
                if ($item) { $tipeItem = 'fork'; $model = $forkModel; }
            }

            if (!$item) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Item #{$id} not found"
                ])->setStatusCode(404);
            }

            // Prevent deleting items that are IN_USE
            if (($item['status'] ?? '') === 'IN_USE') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete an item that is currently IN_USE. Please detach it from the unit first.'
                ])->setStatusCode(422);
            }

            $deleted = $model->delete($id);

            if (!$deleted) {
                $dbError = $db->error();
                log_message('error', '[AttachmentInventoryController::deleteAttachment] delete() returned false. DB: ' . json_encode($dbError));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Item tidak dapat dihapus karena masih memiliki data terkait di sistem.',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(422);
            }

            $this->logActivity(
                'delete',
                'inventory_attachment',
                (int)$id,
                "Deleted {$tipeItem} inventory item #{$id} (SN: " . ($item['serial_number'] ?? '-') . ")",
                ['item_id' => $id, 'tipe_item' => $tipeItem, 'item_data' => $item]
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => ucfirst($tipeItem) . " item deleted successfully.",
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::deleteAttachment] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }

    public function addInventoryItem()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        try {
            $tipeItem = $this->request->getPost('tipe_item');

            $commonData = [
                'inventory_unit_id'  => $this->request->getPost('unit_id') ?: null,
                'physical_condition' => $this->request->getPost('physical_condition') ?: 'GOOD',
                'storage_location'   => $this->request->getPost('storage_location') ?: 'Workshop',
                'status'             => $this->request->getPost('status') ?: 'AVAILABLE',
                'notes'              => $this->request->getPost('catatan') ?: null,
                'received_at'        => date('Y-m-d'),
            ];

            if ($tipeItem === 'attachment') {
                $typeId = (int)$this->request->getPost('attachment_id');
                if (!$typeId) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Attachment Type is required']);
                }
                $model = new InventoryAttachmentModel();
                $count = $model->countAll() + 1;
                $inventoryData = array_merge($commonData, [
                    'item_number'        => 'ATT' . str_pad($count, 4, '0', STR_PAD_LEFT),
                    'attachment_type_id' => $typeId,
                    'serial_number'      => $this->request->getPost('sn_attachment') ?: null,
                ]);

            } elseif ($tipeItem === 'battery') {
                $typeId = (int)$this->request->getPost('baterai_id');
                if (!$typeId) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Battery Type is required']);
                }
                $model = new InventoryBatteryModel();
                $db2 = \Config\Database::connect();
                // Determine B vs BL prefix — check BOTH tipe_baterai AND jenis_baterai
                // because some brands store chemistry in tipe_baterai (e.g. BSLBAT→LITHIUM)
                // and others store it in jenis_baterai (e.g. GS YUASA→Lead Acid)
                $batRow = $db2->table('baterai')->select('jenis_baterai, tipe_baterai')->where('id', $typeId)->get()->getRowArray();
                $jenisBaterai = strtoupper((string)($batRow['jenis_baterai'] ?? ''));
                $tipeBaterai  = strtoupper((string)($batRow['tipe_baterai']  ?? ''));
                $isLithium = str_contains($tipeBaterai, 'LITHIUM') || str_contains($jenisBaterai, 'LITHIUM') ||
                    str_contains($jenisBaterai, 'LI-ION') || str_contains($jenisBaterai, 'LI ION') ||
                    str_contains($jenisBaterai, 'LIFEPO') || str_contains($jenisBaterai, 'LFP') ||
                    str_contains($jenisBaterai, 'NMC') || str_contains($jenisBaterai, 'NCA');
                $batPrefix = $isLithium ? 'BL' : 'B';
                // Accept manual item_number or auto-generate
                $manualBatItemNum = strtoupper(trim((string)($this->request->getPost('item_number_battery') ?? '')));
                if ($manualBatItemNum !== '') {
                    $batItemNumber = $manualBatItemNum;
                } else {
                    // Use last item number per prefix — NOT MAX(id)+1
                    $regexpPat = '^' . $batPrefix . '[0-9]';
                    $lastBat   = $db2->table('inventory_batteries')
                        ->select('item_number')
                        ->where("item_number REGEXP '{$regexpPat}'")
                        ->orderBy('LENGTH(item_number)', 'DESC')
                        ->orderBy('item_number', 'DESC')
                        ->limit(1)->get()->getRowArray();
                    $lastBatNum = $lastBat['item_number'] ?? null;
                    if ($lastBatNum !== null) {
                        $numPart = ltrim(substr($lastBatNum, strlen($batPrefix)), '0') ?: '0';
                        $nextNum = (int)$numPart + 1;
                        $padLen  = max(4, strlen($lastBatNum) - strlen($batPrefix));
                    } else {
                        $nextNum = 1;
                        $padLen  = 4;
                    }
                    $batItemNumber = $batPrefix . str_pad((string)$nextNum, $padLen, '0', STR_PAD_LEFT);
                }
                $inventoryData = array_merge($commonData, [
                    'item_number'    => $batItemNumber,
                    'battery_type_id'=> $typeId,
                    'serial_number'  => $this->request->getPost('sn_baterai') ?: null,
                ]);

            } elseif ($tipeItem === 'charger') {
                $typeId = (int)$this->request->getPost('charger_id');
                if (!$typeId) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Charger Type is required']);
                }
                $model = new InventoryChargerModel();
                $db2 = \Config\Database::connect();
                // Accept manual item_number or auto-generate
                $manualChgItemNum = strtoupper(trim((string)($this->request->getPost('item_number_charger') ?? '')));
                if ($manualChgItemNum !== '') {
                    $chgItemNumber = $manualChgItemNum;
                } else {
                    // Use last item number per prefix — NOT MAX(id)+1
                    $lastChg = $db2->table('inventory_chargers')
                        ->select('item_number')
                        ->where("item_number REGEXP '^C[0-9]'")
                        ->orderBy('LENGTH(item_number)', 'DESC')
                        ->orderBy('item_number', 'DESC')
                        ->limit(1)->get()->getRowArray();
                    $lastChgNum = $lastChg['item_number'] ?? null;
                    if ($lastChgNum !== null) {
                        $numPart = ltrim(substr($lastChgNum, 1), '0') ?: '0';
                        $nextNum = (int)$numPart + 1;
                        $padLen  = max(4, strlen($lastChgNum) - 1);
                    } else {
                        $nextNum = 1;
                        $padLen  = 4;
                    }
                    $chgItemNumber = 'C' . str_pad((string)$nextNum, $padLen, '0', STR_PAD_LEFT);
                }
                $inventoryData = array_merge($commonData, [
                    'item_number'     => $chgItemNumber,
                    'charger_type_id' => $typeId,
                    'serial_number'   => $this->request->getPost('sn_charger') ?: null,
                ]);
            } elseif ($tipeItem === 'fork') {
                $typeId = (int)$this->request->getPost('fork_id');
                $qtyPairs = max(1, (int)$this->request->getPost('qty_pairs'));
                if (!$typeId) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Fork Type is required']);
                }

                $db = \Config\Database::connect();
                $db->transStart();

                $stockCount = (int)$db->table('inventory_fork_stocks')->countAllResults() + 1;
                $stockItemNumber = 'FS' . str_pad((string)$stockCount, 4, '0', STR_PAD_LEFT);
                $db->table('inventory_fork_stocks')->insert([
                    'item_number' => $stockItemNumber,
                    'fork_id' => $typeId,
                    'qty_available_pairs' => $qtyPairs,
                    'physical_condition' => $commonData['physical_condition'],
                    'status' => $commonData['status'],
                    'storage_location' => $commonData['storage_location'],
                    'received_at' => $commonData['received_at'],
                    'notes' => $commonData['notes'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $stockId = (int)$db->insertID();

                $forkModel = new InventoryForkModel();
                $base = (int)$forkModel->countAll() + 1;
                for ($i = 0; $i < $qtyPairs; $i++) {
                    $forkModel->insert([
                        'item_number' => 'F' . str_pad((string)($base + $i), 4, '0', STR_PAD_LEFT),
                        'fork_id' => $typeId,
                        'fork_stock_id' => $stockId,
                        'qty_pairs' => 1,
                        'physical_condition' => $commonData['physical_condition'],
                        'status' => 'AVAILABLE',
                        'storage_location' => $commonData['storage_location'],
                        'received_at' => $commonData['received_at'],
                        'notes' => $commonData['notes'],
                    ]);
                }

                $db->transComplete();
                if ($db->transStatus() === false) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to add fork stock']);
                }

                $this->logActivity('item_created', 'inventory_attachment', $stockId, "Fork stock added ({$qtyPairs} pair)", [
                    'workflow_stage' => 'item_created',
                    'relations' => ['inventory_attachment' => [$stockId]]
                ]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Fork stock berhasil ditambahkan'
                ]);

            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid item type: ' . $tipeItem]);
            }

            if ($model->insert($inventoryData)) {
                $newId = $model->getInsertID();
                $this->logActivity('item_created', 'inventory_attachment', (int)$newId,
                    ucfirst($tipeItem) . ' added to inventory (SN: ' . ($inventoryData['serial_number'] ?? '-') . ')',
                    [
                        'workflow_stage' => 'item_created',
                        'relations'      => ['inventory_attachment' => [(int)$newId]]
                    ]
                );
                return $this->response->setJSON([
                    'success' => true,
                    'message' => ucfirst($tipeItem) . ' successfully added to inventory'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add item',
                'errors'  => $model->errors()
            ]);

        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::addInventoryItem] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

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
                    // Filter HANYA data bersih: tipe_baterai IN ('LEAD ACID','LITHIUM')
                    // dengan exact match ke ?tipe= param
                    $filterTipe = strtoupper(trim((string)($this->request->getGet('tipe') ?? '')));
                    $b = $db->table('baterai')
                        ->select('merk_baterai as value, merk_baterai as text')
                        ->distinct()
                        ->whereIn('tipe_baterai', ['LEAD ACID', 'LITHIUM'])
                        ->orderBy('merk_baterai');
                    if ($filterTipe !== '') $b->where('tipe_baterai', $filterTipe);
                    $data = $b->get()->getResultArray();
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
                'message' => 'Gagal memuat data merk. Silakan coba lagi.'
            ]);
        }
    }

    public function masterTipe($type)
    {
        try {
            $db   = \Config\Database::connect();
            $merk = trim((string)($this->request->getGet('merk') ?? ''));
            $data = [];

            switch ($type) {
                case 'attachment':
                    $b = $db->table('attachment')->select('DISTINCT tipe as value, tipe as text')->orderBy('tipe');
                    if ($merk !== '') $b->where('merk', $merk);
                    $data = $b->get()->getResultArray();
                    break;
                case 'battery':
                    // Return distinct tipe_baterai filtered by merk; exclude empty/dash values
                    $b = $db->table('baterai')
                        ->select('DISTINCT tipe_baterai as value, tipe_baterai as text')
                        ->where('tipe_baterai !=', '')
                        ->where('tipe_baterai !=', '-')
                        ->orderBy('tipe_baterai');
                    if ($merk !== '') $b->where('merk_baterai', $merk);
                    $data = $b->get()->getResultArray();
                    break;
                case 'charger':
                    // For charger: tipe gives the id_charger directly
                    $b = $db->table('charger')
                        ->select('id_charger as id, tipe_charger as value, tipe_charger as text')
                        ->orderBy('tipe_charger');
                    if ($merk !== '') $b->where('merk_charger', $merk);
                    $data = $b->get()->getResultArray();
                    break;
            }

            return $this->response->setJSON(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memuat data tipe. Silakan coba lagi.']);
        }
    }

    public function masterJenis($type)
    {
        try {
            $db = \Config\Database::connect();
            $data = [];

            if ($type === 'battery') {
                $merk      = trim((string)($this->request->getGet('merk') ?? ''));
                $tipeParam = strtoupper(trim((string)($this->request->getGet('tipe') ?? '')));
                // Hanya data bersih: exact match tipe + merk
                $b = $db->table('baterai')
                    ->select('id, jenis_baterai as value, jenis_baterai as text')
                    ->whereIn('tipe_baterai', ['LEAD ACID', 'LITHIUM'])
                    ->where('jenis_baterai !=', '')
                    ->orderBy('jenis_baterai');
                if ($tipeParam !== '') $b->where('tipe_baterai', $tipeParam);
                if ($merk !== '')      $b->where('merk_baterai', $merk);
                $data = $b->get()->getResultArray();
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data jenis. Silakan coba lagi.'
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
                'message' => 'Gagal memuat data model. Silakan coba lagi.'
            ]);
        }
    }

    // Save new master data endpoints
    public function saveMasterMerk($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
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
                'message' => 'Gagal menyimpan merk. Silakan coba lagi.'
            ]);
        }
    }

    public function saveMasterTipe($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
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
                'message' => 'Gagal menyimpan tipe. Silakan coba lagi.'
            ]);
        }
    }

    public function saveMasterJenis($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
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
                'message' => 'Gagal menyimpan jenis. Silakan coba lagi.'
            ]);
        }
    }

    public function saveMasterModel($type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
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
                'message' => 'Gagal menyimpan model. Silakan coba lagi.'
            ]);
        }
    }

    // Separate methods for each item type
    public function attachmentData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
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
            log_message('error', '[AttachmentInventoryController::attachmentData] Error: ' . $e->getMessage());
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
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        try {
            $batteryModel = new InventoryBatteryModel();

            $request = [
                'start' => $this->request->getPost('start'),
                'length' => $this->request->getPost('length'),
                'search' => $this->request->getPost('search'),
                'order' => $this->request->getPost('order'),
                'tipe_item' => 'battery' // Fixed to battery only
            ];

            $result = $batteryModel->getDataTable($request);

            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw'),
                'recordsTotal' => $batteryModel->countAll(),
                'recordsFiltered' => $result['recordsFiltered'],
                'data' => $result['data'],
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::batteryData] Error: ' . $e->getMessage());
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
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        try {
            $chargerModel = new InventoryChargerModel();

            $request = [
                'start' => $this->request->getPost('start'),
                'length' => $this->request->getPost('length'),
                'search' => $this->request->getPost('search'),
                'order' => $this->request->getPost('order'),
                'tipe_item' => 'charger' // Fixed to charger only
            ];

            $result = $chargerModel->getDataTable($request);

            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw'),
                'recordsTotal' => $chargerModel->countAll(),
                'recordsFiltered' => $result['recordsFiltered'],
                'data' => $result['data'],
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::chargerData] Error: ' . $e->getMessage());
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
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
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
            log_message('error', '[AttachmentInventoryController::saveMasterData] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    public function getAvailableUnits()
    {
        try {
            $db = \Config\Database::connect();
            $q  = trim((string) $this->request->getGet('q'));

            // Require at least 1 character to avoid loading all units at once
            if ($q === '') {
                return $this->response->setJSON(['success' => true, 'units' => []]);
            }

            // Get units filtered by search term
            $builder = $db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.status_unit_id, su.status_unit as status_unit_name, CONCAT(mu.merk_unit, " - ", mu.model_unit) as model_unit')
                ->select('(SELECT COUNT(*) FROM inventory_attachments WHERE inventory_unit_id = iu.id_inventory_unit) as has_attachment')
                ->select('(SELECT COUNT(*) FROM inventory_batteries WHERE inventory_unit_id = iu.id_inventory_unit) as has_battery')
                ->select('(SELECT COUNT(*) FROM inventory_chargers WHERE inventory_unit_id = iu.id_inventory_unit) as has_charger')
                ->select('(SELECT COUNT(*) FROM inventory_forks WHERE inventory_unit_id = iu.id_inventory_unit AND detached_at IS NULL) as has_fork')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
                ->groupStart()
                    ->like('iu.no_unit', $q)
                    ->orLike('iu.serial_number', $q)
                    ->orLike('mu.model_unit', $q)
                ->groupEnd()
                ->orderBy('iu.no_unit', 'ASC')
                ->limit(50);

            $units = $builder->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'units' => $units
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::getAvailableUnits] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat daftar unit. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Attach attachment to unit
     */
    public function attachToUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        try {
            $db = \Config\Database::connect();
            $componentHelper = new \App\Models\InventoryComponentHelper();

            $attachmentId = $this->request->getPost('attachment_id');
            $unitId = $this->request->getPost('unit_id');
            $notes = $this->request->getPost('notes');

            if (!$attachmentId || !$unitId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }

            // Get component type
            $attachmentType = $componentHelper->detectComponentType($attachmentId);
            if (!$attachmentType) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak ditemukan'
                ]);
            }

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
            $tableName = match($attachmentType) {
                'battery' => 'inventory_batteries',
                'charger' => 'inventory_chargers',
                'attachment' => 'inventory_attachments',
                'fork' => 'inventory_forks',
                default => null
            };

            if (!$tableName) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid attachment type'
                ]);
            }

            $existingAttachment = $db->table($tableName)
                ->where('inventory_unit_id', $unitId)
                ->get()->getRowArray();

            $message = '';

            if ($existingAttachment) {
                // Auto-detach existing attachment
                $existingId = $existingAttachment['id'] ?? $existingAttachment['id_inventory_attachment'] ?? null;
                if ($attachmentType === 'fork' && $existingId) {
                    $db->table('inventory_forks')->where('id', $existingId)->update([
                        'inventory_unit_id' => null,
                        'status' => 'AVAILABLE',
                        'detached_at' => date('Y-m-d H:i:s'),
                        'storage_location' => 'Workshop',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $stockId = $existingAttachment['fork_stock_id'] ?? null;
                    $qty = (int)($existingAttachment['qty_pairs'] ?? 1);
                    if ($stockId) {
                        $db->query('UPDATE inventory_fork_stocks SET qty_available_pairs = qty_available_pairs + ? WHERE id = ?', [$qty, $stockId]);
                    }
                } elseif ($existingId) {
                    $attachmentModel = new InventoryAttachmentModel();
                    $attachmentModel->detachFromUnit($existingId, 'Auto-detach karena ada replacement');
                }
                $message = "Attachment lama dilepas dan dipasang yang baru ke Unit {$unit['no_unit']}";

                // Log detach
                $this->logActivity('auto_detach', 'inventory_attachment', (int)($existingAttachment['id'] ?? $existingAttachment['id_inventory_attachment'] ?? 0), "Attachment lama dilepas dari Unit {$unit['no_unit']} (auto)", [
                    'old_attachment_id' => $existingAttachment['id'] ?? $existingAttachment['id_inventory_attachment'] ?? null,
                    'new_attachment_id' => $attachmentId,
                    'unit_id' => $unitId
                ]);
            } else {
                $message = "Berhasil memasang attachment ke Unit {$unit['no_unit']}";
            }

            if ($attachmentType === 'fork') {
                $fork = $db->table('inventory_forks')->where('id', $attachmentId)->get()->getRowArray();
                if (!$fork) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Fork tidak ditemukan']);
                }
                $stockId = $fork['fork_stock_id'] ?? null;
                $qty = (int)($fork['qty_pairs'] ?? 1);
                if ($stockId) {
                    $stock = $db->table('inventory_fork_stocks')->where('id', $stockId)->get()->getRowArray();
                    if (!$stock || (int)$stock['qty_available_pairs'] < $qty) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Stok fork tidak mencukupi']);
                    }
                    $db->query('UPDATE inventory_fork_stocks SET qty_available_pairs = qty_available_pairs - ? WHERE id = ?', [$qty, $stockId]);
                }
                $result = $db->table('inventory_forks')->where('id', $attachmentId)->update([
                    'inventory_unit_id' => $unitId,
                    'status' => 'IN_USE',
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'detached_at' => null,
                    'storage_location' => 'Installed in Unit ' . $unit['no_unit'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $attachmentModel = new InventoryAttachmentModel();
                // Attach new attachment (trigger akan auto-update status dan lokasi)
                $result = $attachmentModel->attachToUnit($attachmentId, $unitId, $unit['no_unit']);
            }

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
                if ($attachmentType !== 'fork' && function_exists('notify_attachment_attached')) {
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
                    'message' => $message,
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memasang attachment',
                    'csrf_hash' => csrf_hash()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::attachToUnit] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Swap attachment between units
     */
    public function swapUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
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

            // Detect component type from the ID (check all 3 tables)
            $componentHelper = new \App\Models\InventoryComponentHelper();
            $attachmentType = $componentHelper->detectComponentType($attachmentId);

            if (!$attachmentType) {
                log_message('error', '[AttachmentInventoryController::swapUnit] Cannot determine component type for ID: ' . $attachmentId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak ditemukan di database'
                ]);
            }

            // Get component data from appropriate table
            $tableName = match($attachmentType) {
                'battery' => 'inventory_batteries',
                'charger' => 'inventory_chargers',
                'attachment' => 'inventory_attachments',
                'fork' => 'inventory_forks',
                default => null
            };

            $movingAttachment = $db->table($tableName)->where('id', $attachmentId)->get()->getRowArray();
            if (!$movingAttachment) {
                log_message('error', '[AttachmentInventoryController::swapUnit] Attachment not found: ' . $attachmentId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak ditemukan'
                ]);
            }

            // Use ACTUAL from_unit_id from database, not from form
            $actualFromUnitId = $movingAttachment['inventory_unit_id'];
            if (!$actualFromUnitId) {
                log_message('error', '[AttachmentInventoryController::swapUnit] Attachment not attached to any unit');
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak terpasang di unit manapun'
                ]);
            }

            // attachmentType already detected earlier via componentHelper

            log_message('info', '[AttachmentInventoryController::swapUnit] Request data: ' . json_encode([
                'attachment_id' => $attachmentId,
                'from_unit_id_form' => $fromUnitId,
                'from_unit_id_actual' => $actualFromUnitId,
                'to_unit_id' => $toUnitId,
                'attachment_type' => $attachmentType,
                'reason' => $reason
            ]));

            // Check if target unit already has attachment of same type
            $existingAttachment = $db->table($tableName)
                ->where('inventory_unit_id', $toUnitId)
                ->where('id !=', $attachmentId)
                ->get()->getRowArray();

            if ($existingAttachment) {
                $existingId = $existingAttachment['id'] ?? $existingAttachment['id_inventory_attachment'] ?? null;
                log_message('info', '[AttachmentInventoryController::swapUnit] Found existing attachment, auto-detaching: ' . $existingId);
                // Auto-detach existing attachment from target unit
                if ($attachmentType === 'fork' && $existingId) {
                    $detachResult = $db->table('inventory_forks')->where('id', $existingId)->update([
                        'inventory_unit_id' => null,
                        'status' => 'AVAILABLE',
                        'detached_at' => date('Y-m-d H:i:s'),
                        'storage_location' => 'Workshop',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $stockId = $existingAttachment['fork_stock_id'] ?? null;
                    $qty = (int)($existingAttachment['qty_pairs'] ?? 1);
                    if ($stockId) {
                        $db->query('UPDATE inventory_fork_stocks SET qty_available_pairs = qty_available_pairs + ? WHERE id = ?', [$qty, $stockId]);
                    }
                } else {
                    $attachmentModel = new InventoryAttachmentModel();
                    $detachResult = $attachmentModel->detachFromUnit($existingId, 'Auto-detach karena ada replacement (swap)');
                }

                if (!$detachResult) {
                    log_message('error', '[AttachmentInventoryController::swapUnit] Failed to detach existing attachment');
                }

                // Log auto-detach
                $this->logActivity('auto_detach', 'inventory_attachment', (int)$existingId, "Attachment lama dilepas dari unit tujuan (auto swap)", [
                    'old_attachment_id' => $existingId,
                    'moving_attachment_id' => $attachmentId,
                    'unit_id' => $toUnitId
                ]);
            }

            // Use swap method with ACTUAL from_unit_id
            log_message('info', '[AttachmentInventoryController::swapUnit] Calling swapAttachmentBetweenUnits');
            if ($attachmentType === 'fork') {
                $result = $db->table('inventory_forks')->where('id', $attachmentId)->update([
                    'inventory_unit_id' => $toUnitId,
                    'status' => 'IN_USE',
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'detached_at' => null,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $attachmentModel = new InventoryAttachmentModel();
                $result = $attachmentModel->swapAttachmentBetweenUnits($attachmentId, $actualFromUnitId, $toUnitId, $reason);
            }

            log_message('info', '[AttachmentInventoryController::swapUnit] Swap result: ' . ($result ? 'true' : 'false'));

            if (!$result) {
                $db->transRollback();
                log_message('error', '[AttachmentInventoryController::swapUnit] swapAttachmentBetweenUnits returned false');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memindahkan attachment - operasi swap gagal'
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', '[AttachmentInventoryController::swapUnit] Transaction failed');
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
            if ($attachmentType !== 'fork' && function_exists('notify_attachment_swapped')) {
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

            log_message('info', '[AttachmentInventoryController::swapUnit] Success');
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil memindahkan attachment dari Unit {$fromUnit['no_unit']} ke Unit {$toUnit['no_unit']}",
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::swapUnit] Exception: ' . $e->getMessage());
            log_message('error', '[AttachmentInventoryController::swapUnit] Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Detach attachment from unit
     */
    public function detachFromUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        try {
            $attachmentId = $this->request->getPost('attachment_id');
            $reason = $this->request->getPost('reason');
            $newLocation = $this->request->getPost('new_location');

            if (!$attachmentId || !$reason) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }

            // Get current unit for logging - detect component type first
            $db = \Config\Database::connect();

            // Detect component type
            $componentHelper = new \App\Models\InventoryComponentHelper();
            $componentType = $componentHelper->detectComponentType($attachmentId);

            if (!$componentType) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attachment tidak ditemukan di database'
                ]);
            }

            $tableName = match($componentType) {
                'battery' => 'inventory_batteries',
                'charger' => 'inventory_chargers',
                'attachment' => 'inventory_attachments',
                'fork' => 'inventory_forks',
                default => null
            };

            $attachment = $db->table($tableName . ' ia')
                ->select('ia.inventory_unit_id, iu.no_unit')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ia.inventory_unit_id', 'left')
                ->where('ia.id', $attachmentId)
                ->get()->getRowArray();

            if ($componentType === 'fork') {
                $forkRow = $db->table('inventory_forks')->where('id', $attachmentId)->get()->getRowArray();
                $result = $db->table('inventory_forks')->where('id', $attachmentId)->update([
                    'inventory_unit_id' => null,
                    'status' => 'AVAILABLE',
                    'detached_at' => date('Y-m-d H:i:s'),
                    'storage_location' => $newLocation ?: 'Workshop',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $stockId = $forkRow['fork_stock_id'] ?? null;
                $qty = (int)($forkRow['qty_pairs'] ?? 1);
                if ($result && $stockId) {
                    $db->query('UPDATE inventory_fork_stocks SET qty_available_pairs = qty_available_pairs + ? WHERE id = ?', [$qty, $stockId]);
                }
            } else {
                $attachmentModel = new InventoryAttachmentModel();
                // Detach from unit (trigger akan auto-update status dan lokasi)
                $result = $attachmentModel->detachFromUnit($attachmentId, $reason);
            }

            if ($result) {
                // Update lokasi if custom location provided
                if ($newLocation && $newLocation != 'Workshop') {
                    $attachmentModel->update($attachmentId, ['lokasi_penyimpanan' => $newLocation]);
                }

                // Log activity
                $unitInfo = $attachment['no_unit'] ?? $attachment['inventory_unit_id'] ?? 'Unknown';
                $this->logActivity('detach_from_unit', 'inventory_attachment', (int)$attachmentId, "Attachment dilepas dari Unit {$unitInfo}", [
                    'attachment_id' => $attachmentId,
                    'from_unit_id' => $attachment['inventory_unit_id'] ?? null,
                    'reason' => $reason,
                    'new_location' => $newLocation
                ]);

                // Send cross-division notification to Service
                helper('notification');
                if ($componentType !== 'fork' && function_exists('notify_attachment_detached')) {
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
                    'message' => "Berhasil melepas attachment dari Unit {$unitInfo}",
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal melepas attachment',
                    'csrf_hash' => csrf_hash()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::detachFromUnit] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Get master attachment data for dropdown
     */
    public function masterAttachment()
    {
        try {
            $lookup = new \App\Services\MasterDataLookupService();
            $attachments = $lookup->attachmentOptions();

            return $this->response->setJSON([
                'success' => true,
                'data' => $attachments
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::masterAttachment] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data attachment. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get master baterai data for dropdown (supports ?q= search for Select2 AJAX)
     */
    public function masterBaterai()
    {
        try {
            $q = trim((string)($this->request->getGet('q') ?? ''));
            $lookup = new \App\Services\MasterDataLookupService();
            $batteries = $lookup->batteryOptions($q, 30);

            return $this->response->setJSON([
                'success' => true,
                'data' => $batteries
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::masterBaterai] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data baterai. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get master charger data for dropdown (supports ?q= search for Select2 AJAX)
     */
    public function masterCharger()
    {
        try {
            $q = trim((string)($this->request->getGet('q') ?? ''));
            $lookup = new \App\Services\MasterDataLookupService();
            $chargers = $lookup->chargerOptions($q, 30);

            return $this->response->setJSON([
                'success' => true,
                'data' => $chargers
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::masterCharger] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data charger. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Return last item_number and suggested next number for a given table + prefix.
     * GET ?type=battery|charger|attachment&prefix=B|BL|C
     */
    public function lastItemNumber()
    {
        try {
            $type   = strtolower((string)($this->request->getGet('type') ?? 'battery'));
            $prefix = strtoupper(preg_replace('/[^A-Za-z]/', '', (string)($this->request->getGet('prefix') ?? '')));

            $tableMap = [
                'battery'    => 'inventory_batteries',
                'charger'    => 'inventory_chargers',
                'attachment' => 'inventory_attachments',
            ];
            $table = $tableMap[$type] ?? null;

            if ($table === null || $prefix === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid params'])->setStatusCode(400);
            }

            $db = \Config\Database::connect();

            // Use REGEXP '^PREFIX[0-9]' so prefix 'B' does NOT match 'BL...' items
            $regexpPattern = '^' . $prefix . '[0-9]';
            $lastRow = $db->table($table)
                ->select('item_number')
                ->where("item_number REGEXP '{$regexpPattern}'")
                ->orderBy('LENGTH(item_number)', 'DESC')
                ->orderBy('item_number', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            $lastItemNumber = $lastRow['item_number'] ?? null;

            // Suggested next = extract numeric part from last item_number + 1
            // This ensures continuity with existing numbering (e.g. B2177 → B2178, BL0775 → BL0776)
            if ($lastItemNumber !== null) {
                $numericPart = ltrim(substr($lastItemNumber, strlen($prefix)), '0') ?: '0';
                $nextNum = (int)$numericPart + 1;
            } else {
                $nextNum = 1;
            }
            // Determine pad length: match existing length pattern from last item (min 4 digits)
            $padLen = $lastItemNumber !== null
                ? max(4, strlen($lastItemNumber) - strlen($prefix))
                : 5;
            $suggested = $prefix . str_pad((string)$nextNum, $padLen, '0', STR_PAD_LEFT);

            return $this->response->setJSON([
                'success'   => true,
                'last'      => $lastItemNumber,
                'suggested' => $suggested,
                'prefix'    => $prefix,
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::lastItemNumber] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Server error'])->setStatusCode(500);
        }
    }

    public function masterForks()
    {
        try {
            $lookup = new \App\Services\MasterDataLookupService();
            return $this->response->setJSON([
                'success' => true,
                'data' => $lookup->forkOptions()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::masterForks] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data fork. Silakan coba lagi.'
            ]);
        }
    }

    public function forkStocks()
    {
        try {
            $db = \Config\Database::connect();
            if ($this->request->getMethod() === 'post') {
                $forkId = (int)$this->request->getPost('fork_id');
                $qty = max(1, (int)$this->request->getPost('qty_pairs'));
                if (!$forkId) {
                    return $this->response->setJSON(['success' => false, 'message' => 'fork_id wajib']);
                }
                $count = (int)$db->table('inventory_fork_stocks')->countAllResults() + 1;
                $db->table('inventory_fork_stocks')->insert([
                    'item_number' => 'FS' . str_pad((string)$count, 4, '0', STR_PAD_LEFT),
                    'fork_id' => $forkId,
                    'qty_available_pairs' => $qty,
                    'physical_condition' => $this->request->getPost('physical_condition') ?: 'GOOD',
                    'status' => $this->request->getPost('status') ?: 'AVAILABLE',
                    'storage_location' => $this->request->getPost('storage_location') ?: 'Workshop',
                    'received_at' => date('Y-m-d'),
                    'notes' => $this->request->getPost('notes') ?: null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                return $this->response->setJSON(['success' => true, 'message' => 'Fork stock berhasil ditambah']);
            }

            $rows = $db->table('inventory_fork_stocks fs')
                ->select('fs.*, f.name as fork_name, f.length_mm, f.fork_class')
                ->join('fork f', 'f.id = fs.fork_id', 'left')
                ->orderBy('fs.id', 'DESC')
                ->get()->getResultArray();
            return $this->response->setJSON(['success' => true, 'data' => $rows]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::forkStocks] Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memuat fork stock']);
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
                ->select('iu.id_inventory_unit as id, COALESCE(iu.no_unit, iu.no_unit_na) as nomor_unit, iu.serial_number')
                ->select('mu.merk_unit as merk, mu.model_unit as model')
                ->select("COALESCE(TRIM(CONCAT(COALESCE(tu.tipe, ''), ' ', COALESCE(tu.jenis, ''))), '') AS jenis", false)
                ->select('COALESCE(kap.kapasitas_unit, "") AS kapasitas')
                ->select('COALESCE(su.status_unit, "") AS status')
                ->select('COALESCE(iu.lokasi_unit, "") AS lokasi')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                ->join('kapasitas kap', 'kap.id_kapasitas = iu.kapasitas_unit_id', 'left')
                ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
                ->orderBy('iu.no_unit', 'ASC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $units
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AttachmentInventoryController::getUnits] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data units. Silakan coba lagi.'
            ]);
        }
    }

    // ──────────────────────────────────────────────────────
    //  PUBLIC VIEW TOKEN + PUBLIC PAGE
    // ──────────────────────────────────────────────────────

    /**
     * Return the table name for a given tipe_item string.
     */
    private function tableForType(string $type): ?string
    {
        return match (strtolower($type)) {
            'attachment' => 'inventory_attachments',
            'battery'    => 'inventory_batteries',
            'charger'    => 'inventory_chargers',
            'fork'       => 'inventory_forks',
            default      => null,
        };
    }

    /**
     * Return or generate the public_view_token for a component row.
     */
    private function ensurePublicToken(int $id, string $type): ?string
    {
        if ($id <= 0) {
            return null;
        }
        $table = $this->tableForType($type);
        if ($table === null) {
            return null;
        }
        try {
            $db = \Config\Database::connect();
            if (! $db->fieldExists('public_view_token', $table)) {
                return null;
            }
            $pk = 'id';
            $row = $db->table($table)
                ->select('public_view_token')
                ->where($pk, $id)
                ->get()
                ->getRowArray();

            if (! $row) {
                return null;
            }
            $token = trim((string) ($row['public_view_token'] ?? ''));
            if ($token !== '') {
                return $token;
            }
            $token = bin2hex(random_bytes(24));
            $db->table($table)->where($pk, $id)->update([
                'public_view_token' => $token,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
            return $token;
        } catch (\Throwable $e) {
            log_message('warning', '[AttachmentInventoryController::ensurePublicToken] ' . $e->getMessage());
            return null;
        }
    }

    /**
     * AJAX: generate/return public_view_token for a component.
     * GET warehouse/inventory/attachments/get-token/{id}/{type}
     */
    public function getPublicToken(int $id, string $type): \CodeIgniter\HTTP\ResponseInterface
    {
        $token = $this->ensurePublicToken($id, $type);
        if ($token === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Token tidak dapat dibuat.'])->setStatusCode(422);
        }
        return $this->response->setJSON([
            'success'    => true,
            'token'      => $token,
            'public_url' => base_url('attachment-view/' . $token),
        ]);
    }

    /**
     * Public read-only asset page — no login required.
     * GET /attachment-view/{token}
     */
    public function publicView(string $token): \CodeIgniter\HTTP\ResponseInterface
    {
        $token = trim($token);
        if ($token === '') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db     = \Config\Database::connect();
        $tables = [
            'inventory_attachments' => ['pk' => 'id_inventory_attachment', 'type' => 'attachment'],
            'inventory_batteries'   => ['pk' => 'id_inventory_battery',    'type' => 'battery'],
            'inventory_chargers'    => ['pk' => 'id_inventory_charger',    'type' => 'charger'],
            'inventory_forks'       => ['pk' => 'id_inventory_fork',       'type' => 'fork'],
        ];

        $component = null;
        $tipeItem  = null;

        foreach ($tables as $table => $meta) {
            if (! $db->tableExists($table)) {
                continue;
            }
            if (! $db->fieldExists('public_view_token', $table)) {
                continue;
            }
            $row = $db->table($table)
                ->where('public_view_token', $token)
                ->get()
                ->getRowArray();
            if ($row) {
                $component = $row;
                $tipeItem  = $meta['type'];
                break;
            }
        }

        if (! $component) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Enrich with unit info if assigned
        $unitInfo = null;
        $unitId   = (int) ($component['id_inventory_unit'] ?? 0);
        if ($unitId > 0 && $db->tableExists('inventory_unit')) {
            try {
                $unitInfo = $db->table('inventory_unit iu')
                    ->select('iu.no_unit, iu.serial_number AS unit_serial_number, su.status_unit, mu.merk_unit, mu.model_unit')
                    ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
                    ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                    ->where('iu.id_inventory_unit', $unitId)
                    ->get()
                    ->getRowArray();
            } catch (\Throwable $e) {
                log_message('warning', '[AttachmentInventoryController::publicView] unit: ' . $e->getMessage());
            }
        }

        return view('warehouse/inventory/attachments/public', [
            'component'  => $component,
            'tipe_item'  => $tipeItem,
            'unit_info'  => $unitInfo,
            'public_url' => base_url('attachment-view/' . $token),
            'title'      => strtoupper($tipeItem) . ' Asset View',
        ]);
    }
}
