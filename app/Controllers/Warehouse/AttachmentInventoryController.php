<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use App\Models\InventoryAttachmentModel;
use App\Models\InventoryBatteryModel;
use App\Models\InventoryChargerModel;
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

        // Build detailed stats from 3 separate models
        $attachmentModel = new InventoryAttachmentModel();
        $batteryModel = new InventoryBatteryModel();
        $chargerModel = new InventoryChargerModel();

        $attachmentStats = $attachmentModel->getStats();
        $batteryStats = $batteryModel->getStats();
        $chargerStats = $chargerModel->getStats();

        // Send stats per type so JavaScript can update status counts dynamically
        $detailed_stats = [
            'by_type' => [
                'attachment' => $attachmentStats['total'],
                'battery' => $batteryStats['total'],
                'charger' => $chargerStats['total'],
                'total' => $attachmentStats['total'] + $batteryStats['total'] + $chargerStats['total']
            ],
            // Stats per type for dynamic status filter updates
            'attachment' => $attachmentStats,
            'battery' => $batteryStats,
            'charger' => $chargerStats,
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

    public function getAttachmentDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            // Try to find in all 3 tables (with tipe_item to differentiate)
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
                'message' => 'Terjadi kesalahan saat memuat detail: ' . $e->getMessage()
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
            } else {
                $attachment = $componentHelper->getAttachmentByInventoryId($id);
            }

            if (!$attachment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Component tidak ditemukan'
                ])->setStatusCode(404);
            }

            $sn         = $attachment['sn_attachment'] ?? $attachment['sn_baterai'] ?? $attachment['sn_charger'] ?? '-';
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
                'message' => 'Terjadi kesalahan saat memuat history: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function updateAttachment($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
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

            $updateData = array_filter([
                'status'            => $this->request->getPost('status'),
                'storage_location'  => $this->request->getPost('storage_location'),
                'physical_condition'=> $this->request->getPost('physical_condition'),
                'completeness'      => $this->request->getPost('completeness'),
                'notes'             => $this->request->getPost('notes'),
            ], fn($v) => $v !== null && $v !== '');

            if ($model->update($id, $updateData)) {
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
                'message' => 'Server error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deleteAttachment($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            // Find the item across all 3 tables
            $attachmentModel = new InventoryAttachmentModel();
            $batteryModel    = new InventoryBatteryModel();
            $chargerModel    = new InventoryChargerModel();

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

            $model->delete($id);

            $this->logActivity(
                'delete',
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
                'message' => 'Server error: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }

    public function addInventoryItem()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
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
                $count = $model->countAll() + 1;
                $inventoryData = array_merge($commonData, [
                    'item_number'    => 'B' . str_pad($count, 4, '0', STR_PAD_LEFT),
                    'battery_type_id'=> $typeId,
                    'serial_number'  => $this->request->getPost('sn_baterai') ?: null,
                ]);

            } elseif ($tipeItem === 'charger') {
                $typeId = (int)$this->request->getPost('charger_id');
                if (!$typeId) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Charger Type is required']);
                }
                $model = new InventoryChargerModel();
                $count = $model->countAll() + 1;
                $inventoryData = array_merge($commonData, [
                    'item_number'     => 'C' . str_pad($count, 4, '0', STR_PAD_LEFT),
                    'charger_type_id' => $typeId,
                    'serial_number'   => $this->request->getPost('sn_charger') ?: null,
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
                'message' => 'Server error: ' . $e->getMessage()
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
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
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
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
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
            log_message('error', '[AttachmentInventoryController::saveMasterData] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getAvailableUnits()
    {
        try {
            $db = \Config\Database::connect();

            // Get all units with existing attachment info
            $units = $db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.status_unit_id, su.status_unit as status_unit_name, CONCAT(mu.merk_unit, " - ", mu.model_unit) as model_unit')
                ->select('(SELECT COUNT(*) FROM inventory_attachments WHERE inventory_unit_id = iu.id_inventory_unit) as has_attachment')
                ->select('(SELECT COUNT(*) FROM inventory_batteries WHERE inventory_unit_id = iu.id_inventory_unit) as has_battery')
                ->select('(SELECT COUNT(*) FROM inventory_chargers WHERE inventory_unit_id = iu.id_inventory_unit) as has_charger')
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
            log_message('error', '[AttachmentInventoryController::getAvailableUnits] Error: ' . $e->getMessage());
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
            $tableName = match($attachmentType) {
                'battery' => 'inventory_batteries',
                'charger' => 'inventory_chargers',
                'attachment' => 'inventory_attachments',
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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
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
                log_message('info', '[AttachmentInventoryController::swapUnit] Found existing attachment, auto-detaching: ' . $existingAttachment['id_inventory_attachment']);
                // Auto-detach existing attachment from target unit
                $detachResult = $attachmentModel->detachFromUnit($existingAttachment['id_inventory_attachment'], 'Auto-detach karena ada replacement (swap)');

                if (!$detachResult) {
                    log_message('error', '[AttachmentInventoryController::swapUnit] Failed to detach existing attachment');
                }

                // Log auto-detach
                $this->logActivity('auto_detach', 'inventory_attachment', $existingAttachment['id_inventory_attachment'], "Attachment lama dilepas dari unit tujuan (auto swap)", [
                    'old_attachment_id' => $existingAttachment['id_inventory_attachment'],
                    'moving_attachment_id' => $attachmentId,
                    'unit_id' => $toUnitId
                ]);
            }

            // Use swap method with ACTUAL from_unit_id
            log_message('info', '[AttachmentInventoryController::swapUnit] Calling swapAttachmentBetweenUnits');
            $result = $attachmentModel->swapAttachmentBetweenUnits($attachmentId, $actualFromUnitId, $toUnitId, $reason);

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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
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
                default => null
            };

            $attachment = $db->table($tableName . ' ia')
                ->select('ia.inventory_unit_id, iu.no_unit')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ia.inventory_unit_id', 'left')
                ->where('ia.id', $attachmentId)
                ->get()->getRowArray();

            // Detach from unit (trigger akan auto-update status dan lokasi)
            $result = $attachmentModel->detachFromUnit($attachmentId, $reason);

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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
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
            log_message('error', '[AttachmentInventoryController::masterAttachment] Error: ' . $e->getMessage());
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
            log_message('error', '[AttachmentInventoryController::masterBaterai] Error: ' . $e->getMessage());
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
            log_message('error', '[AttachmentInventoryController::masterCharger] Error: ' . $e->getMessage());
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
            log_message('error', '[AttachmentInventoryController::getUnits] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data units: ' . $e->getMessage()
            ]);
        }
    }
}
