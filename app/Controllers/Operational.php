<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Controllers\BaseController;
use App\Models\DeliveryInstructionModel;
use App\Models\DeliveryItemModel;
use App\Models\KontrakModel;
use App\Traits\ActivityLoggingTrait;
use App\Services\DeliveryInstructionService;
use App\Services\UnitTimelineService;
use App\Config\JenisPerintahKerja;
use App\Config\TujuanPerintahKerja;
use App\Models\InventoryAttachmentModel;
use App\Models\InventoryBatteryModel;
use App\Models\InventoryChargerModel;
use App\Models\InventoryComponentHelper;

class Operational extends BaseController
{
    use ActivityLoggingTrait;
    
    protected $db;
    protected $diModel;
    protected $diItemModel;
    protected $kontrakModel;
    protected $diService;
    protected $unitTimelineService;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->diModel = new DeliveryInstructionModel();
        $this->diItemModel = new DeliveryItemModel();
        $this->kontrakModel = new KontrakModel();
        $this->diService = new DeliveryInstructionService();
        $this->unitTimelineService = new UnitTimelineService();
    }

    /**
     * Tracking search endpoint
     */
    public function trackingSearch()
    {
        // Check permission for tracking search
        if (!$this->hasPermission('operational.tracking.view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied: You do not have permission to search tracking'])->setStatusCode(403);
        }
        
        // Allow both AJAX and regular POST requests for testing
        if (!$this->request->isAJAX() && !$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $searchType = $this->request->getJSON(true)['search_type'] ?? '';
        $searchValue = $this->request->getJSON(true)['search_value'] ?? '';

        log_message('info', 'Tracking search request - Type: ' . $searchType . ', Value: ' . $searchValue);

        if (empty($searchValue)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Search value required']);
        }

        try {
            // Auto-detect search type if not provided
            if (empty($searchType)) {
                $searchType = $this->detectSearchType($searchValue);
                log_message('info', 'Auto-detected search type: ' . $searchType);
            }

            switch ($searchType) {
                case 'kontrak':
                    return $this->searchByKontrak($searchValue);
                case 'spk':
                    return $this->searchBySPK($searchValue);
                case 'di':
                    return $this->searchByDI($searchValue);
                default:
                    return $this->response->setJSON(['success' => false, 'message' => 'Invalid search type: ' . $searchType]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Tracking search error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Search failed: ' . $e->getMessage()]);
        }
    }

    private function detectSearchType($value)
    {
        // Auto-detect based on format
        if (preg_match('/^SPK\//i', $value)) {
            return 'spk';
        } elseif (preg_match('/^DI\//i', $value)) {
            return 'di';
        } elseif (is_numeric($value)) {
            // If numeric, try as ID (could be SPK or DI ID)
            // Check SPK first
            $spk = $this->db->table('spk')->where('id', $value)->get()->getRowArray();
            if ($spk) return 'spk';
            
            // Then check DI
            $di = $this->db->table('delivery_instructions')->where('id', $value)->get()->getRowArray();
            if ($di) return 'di';
            
            return 'kontrak';
        } else {
            // Default to kontrak (e.g., LG-9812310)
            return 'kontrak';
        }
    }

    private function searchByKontrak($kontrakNo)
    {
        // Check permission: Operational perlu akses ke marketing kontrak (cross-division)
        // Operational Head/Staff punya: marketing.kontrak.view (resource permission)
        if (!$this->canAccess('marketing') && !$this->canViewResource('marketing', 'kontrak')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: You do not have permission to view kontrak'
            ])->setStatusCode(403);
        }
        
        log_message('info', 'Searching for kontrak: ' . $kontrakNo);
        
        // Try different search methods
        $kontrak = null;
        
        // Method 1: Search by no_kontrak
        $kontrak = $this->kontrakModel->where('no_kontrak', $kontrakNo)->first();
        
        if (!$kontrak) {
            // Method 2: Search by po_kontrak_nomor in SPK table
            $spk = $this->db->table('spk')
                ->where('po_kontrak_nomor', $kontrakNo)
                ->get()
                ->getRowArray();
            
            if ($spk) {
                log_message('info', 'Found SPK with po_kontrak_nomor: ' . $kontrakNo);
                // Get kontrak from SPK
                $kontrak = $this->kontrakModel->where('id', $spk['kontrak_id'])->first();
            }
        }
        
        if (!$kontrak) {
            log_message('info', 'Kontrak not found: ' . $kontrakNo);
            return $this->response->setJSON(['success' => false, 'message' => 'Kontrak not found']);
        }

        log_message('info', 'Kontrak found: ' . json_encode($kontrak));

        // Get all SPKs for this kontrak with creator name
        $spks = $this->db->table('spk s')
            ->select('s.*, COALESCE(CONCAT(u.first_name, " ", u.last_name), u.username, s.dibuat_oleh) as created_by_name')
            ->join('users u', 'u.id = s.dibuat_oleh', 'left')
            ->where('s.kontrak_id', $kontrak['id'])
            ->get()
            ->getResultArray();

        log_message('info', 'Found ' . count($spks) . ' SPKs for kontrak: ' . $kontrakNo);

        if (count($spks) > 1) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'search_type' => 'kontrak',
                    'multiple_spks' => true,
                    'spks' => $spks,
                    'kontrak' => $kontrak
                ]
            ]);
        } else {
            // Single SPK, get its DI
            $spk = $spks[0] ?? null;
            if (!$spk) {
                return $this->response->setJSON(['success' => false, 'message' => 'No SPK found for this kontrak']);
            }

            $dis = $this->db->table('delivery_instructions')
                ->where('spk_id', $spk['id'])
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'search_type' => 'kontrak',
                    'kontrak' => $kontrak,
                    'spk' => $spk,
                    'di' => $dis[0] ?? null,
                    'multiple_spks' => false
                ]
            ]);
        }
    }

    private function searchBySPK($spkNo)
    {
        log_message('info', 'Searching for SPK: ' . $spkNo);
        
        // Search for SPK by ID or nomor_spk
        $spk = $this->db->table('spk')
            ->where('id', $spkNo)
            ->orWhere('nomor_spk', $spkNo)
            ->get()
            ->getRowArray();

        if (!$spk) {
            log_message('info', 'SPK not found: ' . $spkNo);
            return $this->response->setJSON(['success' => false, 'message' => 'SPK not found']);
        }

        log_message('info', 'SPK found: ' . json_encode($spk));

        // Add stage_status and prepared_units_detail data (same as SPK print and DI print)
        $stageStatus = $this->getSpkStageStatusData($spk['id']);
        $spk['stage_status'] = $stageStatus;
        
        $preparedUnitsDetail = $this->getPreparedUnitsDetail($spk['id'], $stageStatus);
        $spk['prepared_units_detail'] = $preparedUnitsDetail;

        log_message('info', 'SPK enriched with prepared_units_detail: ' . count($preparedUnitsDetail));

        // Get all DIs for this SPK
        $dis = $this->db->table('delivery_instructions')
            ->where('spk_id', $spk['id'])
            ->get()
            ->getResultArray();

        log_message('info', 'Found ' . count($dis) . ' DIs for SPK: ' . $spkNo);

        if (count($dis) > 1) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'search_type' => 'spk',
                    'multiple_dis' => true,
                    'dis' => $dis,
                    'spk' => $spk
                ]
            ]);
        } else {
            // Single DI, return tracking data
            $di = $dis[0] ?? null;
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'search_type' => 'spk',
                    'spk' => $spk,
                    'di' => $di,
                    'multiple_dis' => false
                ]
            ]);
        }
    }

    private function searchByDI($diNo)
    {
        log_message('info', 'Searching for DI: ' . $diNo);
        
        // Search for DI
        $di = $this->db->table('delivery_instructions')
            ->where('nomor_di', $diNo)
            ->get()
            ->getRowArray();

        if (!$di) {
            log_message('info', 'DI not found: ' . $diNo);
            return $this->response->setJSON(['success' => false, 'message' => 'DI not found']);
        }

        log_message('info', 'DI found: ' . json_encode($di));

        // Get SPK for this DI with enriched data
        $spk = $this->db->table('spk')
            ->where('id', $di['spk_id'])
            ->get()
            ->getRowArray();

        if ($spk) {
            // Add stage_status and prepared_units_detail data (same as SPK print and DI print)
            $stageStatus = $this->getSpkStageStatusData($spk['id']);
            $spk['stage_status'] = $stageStatus;
            
            $preparedUnitsDetail = $this->getPreparedUnitsDetail($spk['id'], $stageStatus);
            $spk['prepared_units_detail'] = $preparedUnitsDetail;
        }

        log_message('info', 'SPK found for DI with prepared_units_detail: ' . count($spk['prepared_units_detail'] ?? []));

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'search_type' => 'di',
                'spk' => $spk,
                'di' => $di,
                'multiple_dis' => false
            ]
        ]);
    }

    public function delivery()
    {
        return view('operational/delivery', [
            'title' => 'Delivery Instructions',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/operational/delivery' => 'Delivery Process'
            ]
        ]);

    }

    /**
     * Server-side DataTables endpoint for delivery table
     */
    public function deliveryData()
    {
        $request = $this->request;
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 25;
        $search = $request->getPost('search')['value'] ?? '';
        $statusFilter = $request->getPost('status_filter') ?? 'all';
        
        // Start with base query
        $builder = $this->diModel->builder();
        $builder->select('
            delivery_instructions.*, 
            spk.pic as spk_pic, 
            spk.kontak as spk_kontak,
            spk.nomor_spk,
            jpk.nama as jenis_perintah,
            tpk.nama as tujuan_perintah,
            delivery_instructions.perencanaan_tanggal_approve,
            delivery_instructions.berangkat_tanggal_approve,
            delivery_instructions.sampai_tanggal_approve
        ')
        ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
        ->join('jenis_perintah_kerja jpk', 'jpk.id = delivery_instructions.jenis_perintah_kerja_id', 'left')
        ->join('tujuan_perintah_kerja tpk', 'tpk.id = delivery_instructions.tujuan_perintah_kerja_id', 'left');
        
        // Apply status filter
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'SUBMITTED') {
                $builder->groupStart()
                    ->where('delivery_instructions.status_di IS NULL')
                    ->orWhere('delivery_instructions.status_di', 'DIAJUKAN')
                    ->groupEnd();
            } elseif ($statusFilter === 'INPROGRESS') {
                $builder->whereIn('delivery_instructions.status_di', [
                    'DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN'
                ]);
            } elseif ($statusFilter === 'DELIVERED') {
                $builder->whereIn('delivery_instructions.status_di', ['SAMPAI_LOKASI', 'SELESAI']);
            } elseif ($statusFilter === 'CANCELLED') {
                $builder->where('delivery_instructions.status_di', 'DIBATALKAN');
            }
        }
        
        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('delivery_instructions.nomor_di', $search)
                ->orLike('delivery_instructions.pelanggan', $search)
                ->orLike('delivery_instructions.nama_supir', $search)
                ->orLike('spk.nomor_spk', $search)
                ->groupEnd();
        }
        
        // Get total filtered count
        $totalFiltered = $builder->countAllResults(false);
        
        // Sorting
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';
        $columns = ['nomor_di', 'pelanggan', 'total_items', 'jenis_perintah', 'requested_delivery_date', 'status_di', 'nama_supir'];
        
        if (isset($columns[$orderColumnIndex])) {
            if ($orderColumnIndex <= 1 || $orderColumnIndex >= 5) {
                // Direct DI columns
                $builder->orderBy('delivery_instructions.' . $columns[$orderColumnIndex], $orderDir);
            } else {
                // Joined columns
                $builder->orderBy($columns[$orderColumnIndex], $orderDir);
            }
        } else {
            $builder->orderBy('delivery_instructions.id', 'DESC');
        }
        
        // Pagination
        $data = $builder->limit($length, $start)->get()->getResultArray();
        
        // Add items information for each DI
        foreach ($data as &$row) {
            $items = $this->diItemModel
                ->where('di_id', $row['id'])
                ->countAllResults();
            $row['total_items'] = $items;
            
            // Get total units and attachments
            $itemDetails = $this->diItemModel
                ->where('di_id', $row['id'])
                ->findAll();
            
            $unitCount = 0;
            $attachmentCount = 0;
            foreach ($itemDetails as $item) {
                if ($item['item_type'] === 'UNIT') {
                    $unitCount++;
                } elseif ($item['item_type'] === 'ATTACHMENT') {
                    $attachmentCount++;
                }
            }
            $row['total_units'] = $unitCount;
            $row['total_attachments'] = $attachmentCount;
        }
        
        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $this->diModel->countAll(),
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    /**
     * Statistics endpoint for delivery dashboard
     */
    public function deliveryStats()
    {
        $statusFilter = $this->request->getPost('status_filter') ?? 'all';
        
        $builder = $this->diModel->builder();
        
        // Total count
        $total = $builder->countAllResults(false);
        
        // Submitted count
        $submittedBuilder = clone $builder;
        $submitted = $submittedBuilder
            ->groupStart()
            ->where('status_di IS NULL')
            ->orWhere('status_di', 'DIAJUKAN')
            ->groupEnd()
            ->countAllResults(false);
        
        // In Progress count
        $inprogressBuilder = clone $builder;
        $inprogress = $inprogressBuilder
            ->whereIn('status_di', ['DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN'])
            ->countAllResults(false);
        
        // Delivered count
        $deliveredBuilder = clone $builder;
        $delivered = $deliveredBuilder
            ->whereIn('status_di', ['SAMPAI_LOKASI', 'SELESAI'])
            ->countAllResults();
        
        return $this->response->setJSON([
            'total' => $total,
            'submitted' => $submitted,
            'inprogress' => $inprogress,
            'delivered' => $delivered
        ]);
    }

    public function tracking()
    {
        return view('operational/tracking', [
            'title' => 'Track Kontrak -> SPK -> DI',
        ]);
    }
    

    public function diList()
    {
        // Use same query structure as Marketing for consistency
        $rows = $this->diModel
            ->select('
                delivery_instructions.*, 
                spk.pic as spk_pic, 
                spk.kontak as spk_kontak,
                spk.nomor_spk,
                spk.kontrak_id as spk_kontrak_id,
                jpk.nama as jenis_perintah,
                tpk.nama as tujuan_perintah,
                q.quotation_number
            ')
            ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
            ->join('quotation_specifications qs', 'qs.id_specification = spk.quotation_specification_id', 'left')
            ->join('quotations q', 'q.id_quotation = qs.id_quotation', 'left')
            ->join('jenis_perintah_kerja jpk', 'jpk.id = delivery_instructions.jenis_perintah_kerja_id', 'left')
            ->join('tujuan_perintah_kerja tpk', 'tpk.id = delivery_instructions.tujuan_perintah_kerja_id', 'left')
            ->orderBy('delivery_instructions.id','DESC')
            ->findAll();
            
        // Add items information for each DI
        foreach ($rows as &$row) {
            // Try delivery_items first
            $items = $this->diItemModel
                ->select('
                    delivery_items.*, 
                    iu.no_unit, 
                    mu.merk_unit, 
                    mu.model_unit,
                    a.tipe as att_tipe, 
                    a.merk as att_merk, 
                    a.model as att_model
                ')
                ->join('inventory_unit iu','iu.id_inventory_unit = delivery_items.unit_id','left')
                ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                ->join('attachment a', 'a.id_attachment = delivery_items.attachment_id', 'left')
                ->where('delivery_items.di_id', $row['id'])
                ->findAll();
            
            // Note: If delivery_items is empty, items will be empty array
            // This is expected behavior - DI may not have items assigned yet
                
            // Format item labels for operational view
            $itemLabels = [];
            $unitCount = 0;
            $attachmentCount = 0;
            
            foreach ($items as $item) {
                if ($item['item_type'] === 'UNIT') {
                    $label = trim(($item['no_unit'] ?: 'Unit') . ' - ' . ($item['merk_unit'] ?: '') . ' ' . ($item['model_unit'] ?: ''));
                    $itemLabels[] = ['unit_label' => $label, 'type' => 'unit'];
                    $unitCount++;
                } elseif ($item['item_type'] === 'ATTACHMENT') {
                    $label = trim(($item['att_tipe'] ?: 'Attachment') . ' ' . ($item['att_merk'] ?: '') . ' ' . ($item['att_model'] ?: ''));
                    $itemLabels[] = ['attachment_label' => $label, 'type' => 'attachment'];
                    $attachmentCount++;
                }
            }
            
            $row['items'] = $itemLabels;
            $row['total_units'] = $unitCount;
            $row['total_attachments'] = $attachmentCount;
        }
        
        return $this->response->setJSON(['data'=>$rows,'csrf_hash'=>csrf_hash()]);
    }

    public function diUpdateStatus($id)
    {
        log_message('info', "=== diUpdateStatus called for DI {$id} ===");
        
        // Check permission for updating DI status
        if (!$this->hasPermission('operational.delivery_instructions.edit')) {
            log_message('warning', "Permission denied for user attempting to update DI {$id}");
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied: You do not have permission to update DI status'])->setStatusCode(403);
        }
        
        if (!$this->request->isAJAX()) {
            log_message('warning', "Non-AJAX request to diUpdateStatus for DI {$id}");
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        
        $action = $this->request->getPost('action');
        log_message('info', "Action requested: {$action}");
        
        try {
            $di = $this->diModel->find((int)$id);
            if (!$di) {
                log_message('error', "DI {$id} not found in database");
                return $this->response->setJSON(['success'=>false,'message'=>'DI tidak ditemukan']);
            }
            
            log_message('info', "Current DI status: " . ($di['status_di'] ?? 'NULL'));
            
            $updateData = [];
            
            switch($action) {
                case 'assign_driver':
                    $updateData['nama_supir'] = $this->request->getPost('nama_supir');
                    $updateData['no_hp_supir'] = $this->request->getPost('no_hp_supir');
                    $updateData['no_sim_supir'] = $this->request->getPost('no_sim_supir');
                    $updateData['kendaraan'] = $this->request->getPost('kendaraan');
                    $updateData['no_polisi_kendaraan'] = $this->request->getPost('no_polisi_kendaraan');
                    $updateData['status_di'] = 'SIAP_KIRIM';
                    log_message('info', "Setting status_di to SIAP_KIRIM for assign_driver action");
                    break;
                    
                case 'approve_departure':
                    $updateData['berangkat_tanggal_approve'] = date('Y-m-d');
                    $updateData['catatan_berangkat'] = $this->request->getPost('catatan_berangkat');
                    $updateData['status_di'] = 'DALAM_PERJALANAN';
                    break;
                    
                case 'confirm_arrival':
                    $updateData['sampai_tanggal_approve'] = date('Y-m-d');
                    $updateData['catatan_sampai'] = $this->request->getPost('catatan_sampai');
                    $updateData['status_di'] = 'SAMPAI_LOKASI';
                    break;
                    
                case 'complete_delivery':
                    $updateData['status_di'] = 'SELESAI';
                    
                    // Log unit timeline events when DI completes
                    try {
                        $this->logUnitTimelineOnDICompletion((int)$id, $di);
                    } catch (\Exception $e) {
                        log_message('error', "Failed to log unit timeline for DI {$id}: " . $e->getMessage());
                        // Don't fail the DI completion if timeline logging fails
                    }
                    break;
                    
                case 'cancel':
                    $updateData['status_di'] = 'DIBATALKAN';
                    break;
                    
                default:
                    return $this->response->setJSON(['success'=>false,'message'=>'Aksi tidak valid']);
            }
            
            // Send notifications based on action
            helper('notification');
            $deliveryData = [
                'id' => $id,
                'nomor_delivery' => $di['nomor_di'] ?? '',
                'driver_name' => $updateData['nama_supir'] ?? $di['nama_supir'] ?? '',
                'customer_name' => $di['nama_customer'] ?? '',
                'destination' => $di['alamat_tujuan'] ?? '',
                'vehicle' => $updateData['kendaraan'] ?? $di['kendaraan'] ?? '',
                'url' => base_url('/operational/delivery/' . $id)
            ];
            
            switch($action) {
                case 'assign_driver':
                    notify_delivery_assigned($deliveryData);
                    break;
                case 'approve_departure':
                    notify_delivery_in_transit($deliveryData);
                    break;
                case 'confirm_arrival':
                    notify_delivery_arrived($deliveryData);
                    break;
                case 'complete_delivery':
                    notify_delivery_completed($deliveryData);
                    break;
            }
            
            // Update will trigger the sync_di_status_temp_on_update trigger
            $oldDi = $this->diModel->find((int)$id);
            if ($oldDi && !is_array($oldDi)) { $oldDi = (array)$oldDi; }
            
            log_message('info', "Updating DI {$id} with data: " . json_encode($updateData));
            $updated = $this->diModel->update((int)$id, $updateData);
            
            if ($updated) {
                // Verify the update
                $verifyDi = $this->diModel->find((int)$id);
                log_message('info', "✅ DI {$id} updated successfully. New status: " . ($verifyDi['status_di'] ?? 'NULL'));
                
                // Log DI status update using trait
                $this->logUpdate('delivery_instruction', $id, $oldDi, $updateData, [
                    'di_id' => $id,
                    'action' => $action,
                    'old_status' => $di['status_di'] ?? null,
                    'new_status' => $updateData['status_di'] ?? null
                ]);
                
                log_message('info', "=== diUpdateStatus completed successfully for DI {$id} ===");
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Status DI berhasil diperbarui',
                    'old_status' => $di['status_di'] ?? null,
                    'new_status' => $verifyDi['status_di'] ?? null,
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                log_message('error', "❌ Failed to update DI {$id} - Model update() returned false");
                return $this->response->setJSON(['success'=>false,'message'=>'Gagal memperbarui status DI']);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error updating DI status: ' . $e->getMessage());
            return $this->response->setJSON(['success'=>false,'message'=>'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    public function diUpdateStatusLegacy($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        
        $status = $this->request->getPost('status');
        // Allowed DI statuses (English enum values)
        $allowed = ['SUBMITTED','PROCESSED','SHIPPED','DELIVERED','CANCELLED'];
        
        if (!in_array($status, $allowed, true)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Status tidak valid. Status yang diizinkan: ' . implode(', ', $allowed)]);
        }
        
        // Map legacy status -> status_eksekusi
        $exec = null;
        if ($status === 'DELIVERED') {
            $exec = 'DELIVERED';
        } elseif ($status === 'PROCESSED' || $status === 'SHIPPED') {
            $exec = 'DISPATCHED';
        } elseif ($status === 'SUBMITTED') {
            // keep null, planning not yet approved
            $exec = null;
        } elseif ($status === 'CANCELLED') {
            $exec = 'CANCELLED';
        }
        
        $payload = [
            'status'=>$status,
            'diperbarui_pada'=>date('Y-m-d H:i:s')
        ];
        if ($exec !== null) { 
            $payload['status_eksekusi'] = $exec; 
        }
        
        $updated = $this->diModel->update((int)$id, $payload);
        
        if ($updated) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Status DI berhasil diperbarui',
                'csrf_hash' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON(['success'=>false,'message'=>'Gagal memperbarui status DI']);
        }
    }

    public function diDetail($id)
    {
        try {
            // Get DI with user name resolution for dibuat_oleh field and JOIN jenis_perintah_kerja & tujuan_perintah_kerja
            $di = $this->db->table('delivery_instructions di')
                ->select('di.*, 
                    COALESCE(CONCAT(u.first_name, " ", u.last_name), u.username, di.dibuat_oleh) as dibuat_oleh_name,
                    jpk.nama as jenis_perintah,
                    jpk.kode as jenis_perintah_kode,
                    tpk.nama as tujuan_perintah,
                    tpk.kode as tujuan_perintah_kode')
                ->join('users u', 'u.id = di.dibuat_oleh', 'left')
                ->join('jenis_perintah_kerja jpk', 'jpk.id = di.jenis_perintah_kerja_id', 'left')
                ->join('tujuan_perintah_kerja tpk', 'tpk.id = di.tujuan_perintah_kerja_id', 'left')
                ->where('di.id', (int)$id)
                ->get()->getRowArray();
                
            if (!$di) {
                return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'DI tidak ditemukan']);
            }
            
            $spk = null;
            if (!empty($di['spk_id'])) {
                try {
                    $spk = $this->db->table('spk s')
                        ->select('s.*, COALESCE(CONCAT(u.first_name, " ", u.last_name), u.username, s.dibuat_oleh) as created_by_name')
                        ->join('users u', 'u.id = s.dibuat_oleh', 'left')
                        ->where('s.id', (int)$di['spk_id'])
                        ->get()->getRowArray();
                } catch (\Exception $e) {
                    log_message('error', 'Error loading SPK for DI detail: ' . $e->getMessage());
                    $spk = null;
                }
            }

        // Get all delivery items with detailed information, grouping by unit
        $rawItems = $this->db->query("
            SELECT
                di.*,
                iu.no_unit, 
                mu.merk_unit, 
                mu.model_unit,
                iu.departemen_id,
                d.nama_departemen as departemen_nama,
                k.kapasitas_unit as kapasitas_unit_nama,
                iu.kapasitas_unit_id,
                CASE
                    WHEN iu.departemen_id = 2 THEN 'Electric'
                    ELSE 'Diesel'
                END as jenis_power
            FROM delivery_items di
            LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = di.unit_id
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
            LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
            WHERE di.di_id = ?
            AND di.item_type = 'UNIT'
        ", [(int)$id])->getResultArray();

        // Group items by unit with complete information
        $groupedItems = [];

        foreach ($rawItems as $item) {
            $unitId = $item['unit_id'];
            if (!isset($groupedItems[$unitId])) {
                $groupedItems[$unitId] = [
                    'unit_info' => [
                        'id' => $unitId,
                        'no_unit' => $item['no_unit'] ?: '-',
                        'merk_unit' => $item['merk_unit'] ?: '',
                        'model_unit' => $item['model_unit'] ?: '',
                        'kapasitas_unit_id' => $item['kapasitas_unit_id'] ?: '-',
                        'kapasitas_unit_nama' => $item['kapasitas_unit_nama'] ?: $item['kapasitas_unit_id'] ?: '-',
                        'departemen_id' => $item['departemen_id'] ?: '-',
                        'departemen_nama' => $item['departemen_nama'] ?: $item['departemen_id'] ?: '-',
                        'jenis_power' => $item['jenis_power'] ?: 'Diesel',
                        'label' => trim(($item['no_unit'] ?: '-') . ' - ' . ($item['merk_unit'] ?: '') . ' ' . ($item['model_unit'] ?: ''))
                    ],
                    'battery' => null,
                    'charger' => null,
                    'attachments' => []
                ];
                
                // Get attachments for this specific unit with detailed information
                // Use DISTINCT to avoid duplicates from delivery_items
                // Note: delivery_items.attachment_id references TYPE tables (baterai/charger/attachment)
                // Use keterangan field to determine type
                $unitAttachments = $this->db->query("
                    SELECT DISTINCT
                        di.attachment_id,
                        di.item_type,
                        di.keterangan,
                        CASE 
                            WHEN di.keterangan LIKE '%Battery%' OR di.keterangan LIKE '%Baterai%' THEN 'battery'
                            WHEN di.keterangan LIKE '%Charger%' THEN 'charger'
                            ELSE 'attachment'
                        END as tipe_item,
                        COALESCE(b.merk_baterai, c.merk_charger, a.merk) as merk,
                        COALESCE(b.tipe_baterai, c.tipe_charger, a.tipe) as tipe,
                        COALESCE(b.jenis_baterai, a.model) as model_or_jenis,
                        a.tipe as attachment_type,
                        a.merk as attachment_merk,
                        a.model as attachment_model,
                        b.merk_baterai, b.tipe_baterai, b.jenis_baterai,
                        c.merk_charger, c.tipe_charger
                    FROM delivery_items di
                    LEFT JOIN attachment a ON a.id_attachment = di.attachment_id
                    LEFT JOIN baterai b ON b.id = di.attachment_id
                    LEFT JOIN charger c ON c.id_charger = di.attachment_id
                    WHERE di.di_id = ? 
                    AND di.item_type = 'ATTACHMENT' 
                    AND di.parent_unit_id = ?
                ", [(int)$id, $unitId])->getResultArray();
                
                foreach ($unitAttachments as $attachment) {
                    switch ($attachment['tipe_item']) {
                        case 'battery':
                            $groupedItems[$unitId]['battery'] = [
                                'id' => $attachment['attachment_id'],
                                'merk_baterai' => $attachment['merk_baterai'] ?: '-',
                                'tipe_baterai' => $attachment['tipe_baterai'] ?: '-',
                                'jenis_baterai' => $attachment['jenis_baterai'] ?: '-',
                                'label' => ($attachment['merk_baterai'] ?: '-') . ' • ' . ($attachment['tipe_baterai'] ?: '-') . ' • ' . ($attachment['jenis_baterai'] ?: '-')
                            ];
                            break;
                        case 'charger':
                            $groupedItems[$unitId]['charger'] = [
                                'id' => $attachment['attachment_id'],
                                'merk_charger' => $attachment['merk_charger'] ?: '-',
                                'tipe_charger' => $attachment['tipe_charger'] ?: '-',
                                'label' => ($attachment['merk_charger'] ?: '-') . ' • ' . ($attachment['tipe_charger'] ?: '-')
                            ];
                            break;
                        case 'attachment':
                        default:
                            $attachmentData = [
                                'id' => $attachment['attachment_id'],
                                'tipe' => $attachment['attachment_type'] ?: '-',
                                'merk' => $attachment['attachment_merk'] ?: '-',
                                'model' => $attachment['attachment_model'] ?: '-',
                                'label' => ($attachment['attachment_type'] ?: '-') . ' • ' . ($attachment['attachment_merk'] ?: '-') . ' • ' . ($attachment['attachment_model'] ?: '-')
                            ];
                            $groupedItems[$unitId]['attachments'][] = $attachmentData;
                            break;
                    }
                }
            }
        }

        // Get any standalone attachments (without parent_unit_id) with complete detail
        // Note: delivery_items stores TYPE ID, not inventory instance
        $standaloneAttachments = $this->db->query("
            SELECT
                di.*,
                COALESCE(a.tipe, b.tipe_baterai, c.tipe_charger) as tipe,
                COALESCE(a.merk, b.merk_baterai, c.merk_charger) as merk,
                COALESCE(a.model, b.jenis_baterai, '') as model
            FROM delivery_items di
            LEFT JOIN attachment a ON a.id_attachment = di.attachment_id
            LEFT JOIN baterai b ON b.id = di.attachment_id
            LEFT JOIN charger c ON c.id_charger = di.attachment_id
            WHERE di.di_id = ?
            AND di.item_type = 'ATTACHMENT'
            AND (di.parent_unit_id IS NULL OR di.parent_unit_id = 0)
        ", [(int)$id])->getResultArray();

        // Format standalone attachments with proper labels
        // Note: delivery_items stores TYPE ID only, not inventory instances with serial numbers
        $formattedStandaloneAttachments = [];
        foreach ($standaloneAttachments as $attachment) {
            $formattedStandaloneAttachments[] = [
                'id' => $attachment['attachment_id'],
                'tipe' => $attachment['tipe'] ?: '-',
                'merk' => $attachment['merk'] ?: '-', 
                'model' => $attachment['model'] ?: '-',
                'label' => ($attachment['tipe'] ?: '-') . ' • ' . ($attachment['merk'] ?: '-') . ' • ' . ($attachment['model'] ?: '-')
            ];
        }

        // Convert grouped items to array format
        $structuredItems = array_values($groupedItems);

        // Get spesifikasi data for fallback (like in print_di)
        $spesifikasi = [];
        if ($spk && !empty($spk['spesifikasi'])) {
            $decoded = json_decode($spk['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $spesifikasi = $decoded;
            }
        }

        // Get kontrak data for additional fallback (like in print_di)  
        $kontrakData = [];
        if (!empty($di['po_kontrak_nomor'])) {
            $kontrak = $this->db->table('kontrak')
                ->where('no_kontrak', $di['po_kontrak_nomor'])
                ->get()->getRowArray();
            if ($kontrak) {
                $kontrakData = $kontrak;
                
                // Also get quotation specification for attachment data if available
                if (!empty($spk['quotation_specification_id'])) {
                    $quotationSpec = $this->db->table('quotation_specifications')
                        ->where('id_specification', $spk['quotation_specification_id'])
                        ->get()->getRowArray();
                    if ($quotationSpec) {
                        // Merge quotation specification data into kontrakData
                        $kontrakData = array_merge($kontrakData, $quotationSpec);
                    }
                }
            }
        }

        // For ATTACHMENT SPK with no delivery_items, create fallback attachment data from spesifikasi
        if (empty($structuredItems) && empty($formattedStandaloneAttachments) && 
            $spk && ($spk['jenis_spk'] === 'ATTACHMENT' || $di['jenis_spk'] === 'ATTACHMENT')) {
            
            // Create attachment data from spesifikasi as fallback
            $attachmentFromSpesifikasi = [
                'id' => null,
                'tipe' => $spesifikasi['attachment_tipe'] ?? $kontrakData['attachment_tipe'] ?? $kontrakData['attachment_name'] ?? 'Attachment',
                'merk' => $spesifikasi['attachment_merk'] ?? $kontrakData['attachment_merk'] ?? '-',
                'model' => $spesifikasi['attachment_model'] ?? $kontrakData['attachment_model'] ?? '',
                'sn_attachment' => '-'
            ];
            
            $formattedStandaloneAttachments = [$attachmentFromSpesifikasi];
        }

            return $this->response->setJSON([
                'success'=>true,
                'data'=>$di,
                'spk'=>$spk,
                'spesifikasi'=>$spesifikasi,
                'kontrak'=>$kontrakData,
                'items'=>$structuredItems,
                'attachments'=>$formattedStandaloneAttachments,
                'csrf_hash'=>csrf_hash()
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in diDetail: ' . $e->getMessage() . ' at line ' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'success'=>false,
                'message'=>'Terjadi kesalahan saat memuat detail DI: ' . $e->getMessage()
            ]);
        }
    }

    public function diApproveStage($id)
    {
        log_message('info', 'diApproveStage called for DI ' . $id);
        
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $stage = $this->request->getPost('stage');
        $tanggalApprove = date('Y-m-d'); // Use today's date automatically
        
        log_message('info', 'Stage: ' . $stage . ', Date: ' . $tanggalApprove);

        if (empty($stage)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Stage wajib diisi']);
        }

        $allowedStages = ['perencanaan', 'berangkat', 'sampai'];
        if (!in_array($stage, $allowedStages)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Stage tidak valid']);
        }

        // Find the DI
        $di = $this->diModel->find((int)$id);
        if ($di && !is_array($di)) { $di = (array)$di; }
        if (!$di) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'DI tidak ditemukan']);
        }
        
        log_message('info', 'DI found: ' . $di['nomor_di']);

        // Prepare update data based on stage
        $updateData = [
            $stage . '_tanggal_approve' => $tanggalApprove,
            'diperbarui_pada' => date('Y-m-d H:i:s')
        ];

        // Stage-specific fields
        if ($stage === 'perencanaan') {
            // Perencanaan menyimpan semua data operasional pengiriman
            $tanggalKirim = $this->request->getPost('tanggal_kirim');
            $estimasiSampai = $this->request->getPost('estimasi_sampai');
            $namaSupir = $this->request->getPost('nama_supir');
            $noHpSupir = $this->request->getPost('no_hp_supir');
            $noSimSupir = $this->request->getPost('no_sim_supir');
            $kendaraan = $this->request->getPost('kendaraan');
            $nopolKendaraan = $this->request->getPost('no_polisi_kendaraan');
            $catatanPerencanaan = $this->request->getPost('catatan_perencanaan');

            if ($tanggalKirim) $updateData['tanggal_kirim'] = $tanggalKirim;
            if ($estimasiSampai) $updateData['estimasi_sampai'] = $estimasiSampai;
            if ($namaSupir) $updateData['nama_supir'] = $namaSupir;
            if ($noHpSupir) $updateData['no_hp_supir'] = $noHpSupir;
            if ($noSimSupir) $updateData['no_sim_supir'] = $noSimSupir;
            if ($kendaraan) $updateData['kendaraan'] = $kendaraan;
            if ($nopolKendaraan) $updateData['no_polisi_kendaraan'] = $nopolKendaraan;
            if ($catatanPerencanaan) $updateData['catatan'] = $catatanPerencanaan;
            // After perencanaan, status still SIAP_KIRIM (already set by Proses DI)

        } elseif ($stage === 'berangkat') {
            // Berangkat hanya menyimpan catatan keberangkatan
            $catatanBerangkat = $this->request->getPost('catatan_berangkat');
            if ($catatanBerangkat) $updateData['catatan_berangkat'] = $catatanBerangkat;
            // On departure, update status_di
            $updateData['status_di'] = 'DALAM_PERJALANAN';

        } elseif ($stage === 'sampai') {
            $catatanSampai = $this->request->getPost('catatan_sampai');
            if ($catatanSampai) $updateData['catatan_sampai'] = $catatanSampai;
            
            // After sampai approval, update status_di to SAMPAI_LOKASI
            $updateData['status_di'] = 'SAMPAI_LOKASI';
            
            log_message('info', 'Stage sampai - updating status to SAMPAI_LOKASI');
        }

        // Update the DI
        try {
            log_message('info', 'Attempting to update DI ' . $id . ' with data: ' . json_encode($updateData));
            
            $oldDi = $this->diModel->find((int)$id);
            if ($oldDi && !is_array($oldDi)) { $oldDi = (array)$oldDi; }
            
            $this->diModel->update((int)$id, $updateData);
            
            log_message('info', 'DI ' . $id . ' updated successfully');
            
            // Log DI stage approval using trait
            $this->logUpdate('delivery_instruction', $id, $oldDi, $updateData, [
                'di_id' => $id,
                'stage' => $stage,
                'tanggal_approve' => $tanggalApprove
            ]);
            
            log_message('info', 'Activity log recorded for DI ' . $id);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to update DI ' . $id . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }

        // Check if all units in SPK have been delivered before marking as COMPLETED
        if ($stage === 'sampai' && !empty($di['spk_id'])) {
            try {
                $spkId = $di['spk_id'];
                
                // Get total prepared units from SPK
                $totalUnitsInSpk = $this->db->table('spk_unit_stages')
                    ->where('spk_id', $spkId)
                    ->where('stage_name', 'persiapan_unit')
                    ->where('tanggal_approve IS NOT NULL')
                    ->countAllResults();
                
                // Get total units that have been delivered (all DI with SAMPAI_LOKASI or SELESAI)
                $deliveredUnits = $this->db->query("
                    SELECT COUNT(DISTINCT di_items.unit_id) as total
                    FROM delivery_items di_items
                    INNER JOIN delivery_instructions di ON di.id = di_items.di_id
                    WHERE di.spk_id = ?
                    AND di.status_di IN ('SAMPAI_LOKASI', 'SELESAI')
                    AND di_items.item_type = 'UNIT'
                    AND di_items.unit_id IS NOT NULL
                ", [$spkId])->getRowArray();
                
                $totalDelivered = $deliveredUnits['total'] ?? 0;
                
                // Only mark SPK as COMPLETED if all units are delivered
                if ($totalDelivered >= $totalUnitsInSpk && $totalUnitsInSpk > 0) {
                    $this->db->table('spk')->where('id', $spkId)->update([
                        'status' => 'COMPLETED',
                        'diperbarui_pada' => date('Y-m-d H:i:s')
                    ]);
                    
                    // Log status history
                    try {
                        $this->db->table('spk_status_history')->insert([
                            'spk_id' => $spkId,
                            'status_from' => 'IN_PROGRESS',
                            'status_to' => 'COMPLETED',
                            'changed_by' => session('user_id') ?: 1,
                            'note' => 'All units delivered: ' . $di['nomor_di'] . " ($totalDelivered/$totalUnitsInSpk units)",
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to log SPK status history: ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to check/update SPK completion for DI ' . $id . ': ' . $e->getMessage());
                // Continue execution - not critical
            }
        }

        // If status becomes DELIVERED, activate associated kontrak
        if ($stage === 'sampai' && !empty($di['po_kontrak_nomor'])) {
            try {
                $this->db->table('kontrak')
                    ->groupStart()
                        ->where('no_kontrak', $di['po_kontrak_nomor'])
                        ->orWhere('no_po_marketing', $di['po_kontrak_nomor'])
                    ->groupEnd()
                    ->update(['status'=>'ACTIVE','diperbarui_pada'=>date('Y-m-d H:i:s')]);
                    
                log_message('info', 'Activated kontrak for DI ' . $id);
            } catch (\Exception $e) {
                log_message('error', 'Failed to activate kontrak for DI ' . $id . ': ' . $e->getMessage());
                // Continue execution - not critical
            }
        }

        // CRITICAL FIX: Update inventory_unit and inventory_attachment with kontrak and DI relationships
        if ($stage === 'sampai') {
            try {
                // Get kontrak data based on po_kontrak_nomor
                $kontrak = null;
                if (!empty($di['po_kontrak_nomor'])) {
                    $kontrak = $this->db->table('kontrak')
                        ->groupStart()
                            ->where('no_kontrak', $di['po_kontrak_nomor'])
                            ->orWhere('no_po_marketing', $di['po_kontrak_nomor'])
                        ->groupEnd()
                        ->get()->getRowArray();
                }

                // Get SPK data to find kontrak_spesifikasi_id
                $spk = null;
                $kontrakSpesifikasiId = null;
                if (!empty($di['spk_id'])) {
                    $spk = $this->db->table('spk')->where('id', $di['spk_id'])->get()->getRowArray();
                    if ($spk && !empty($spk['kontrak_spesifikasi_id'])) {
                        $kontrakSpesifikasiId = $spk['kontrak_spesifikasi_id'];
                    }
                }

                // Update all units in this DI to link them with kontrak and DI
                $deliveryUnits = $this->db->table('delivery_items')
                    ->where('di_id', $id)
                    ->where('item_type', 'UNIT')
                    ->get()->getResultArray();

                foreach ($deliveryUnits as $deliveryUnit) {
                    if (!empty($deliveryUnit['unit_id'])) {
                        $updateUnitData = [
                            'spk_id' => $di['spk_id'],
                            'delivery_instruction_id' => $id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];

                        // Add kontrak info if available (kontrak_unit junction is primary link)
                        // kontrak_id column will be dropped - link is via kontrak_unit table
                        if ($kontrak) {
                            // Ensure kontrak_unit junction record exists
                            $existingKu = $this->db->table('kontrak_unit')
                                ->where('kontrak_id', $kontrak['id'])
                                ->where('unit_id', $deliveryUnit['unit_id'])
                                ->where('status', 'ACTIVE')
                                ->countAllResults();
                            if ($existingKu == 0) {
                                $this->db->table('kontrak_unit')->insert([
                                    'kontrak_id' => $kontrak['id'],
                                    'unit_id' => $deliveryUnit['unit_id'],
                                    'tanggal_mulai' => date('Y-m-d'),
                                    'status' => 'ACTIVE',
                                    'is_temporary' => false,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => session('user_id')
                                ]);
                            }
                        }
                        if ($kontrakSpesifikasiId) {
                            $updateUnitData['kontrak_spesifikasi_id'] = $kontrakSpesifikasiId;
                        }

                        // Add delivery date from DI
                        if (!empty($di['tanggal_kirim'])) {
                            $updateUnitData['tanggal_kirim'] = $di['tanggal_kirim'];
                        } else {
                            // Use actual delivery date (sampai_tanggal_approve) as fallback
                            $updateUnitData['tanggal_kirim'] = date('Y-m-d');
                        }

                        // Get pricing from quotation_specifications via SPK
                        if ($kontrakSpesifikasiId) {
                            // kontrak_spesifikasi_id in SPK now refers to quotation_specifications.id
                            $quotationSpec = $this->db->table('quotation_specifications')
                                ->where('id', $kontrakSpesifikasiId)
                                ->get()->getRowArray();
                            
                            if ($quotationSpec) {
                                if (!empty($quotationSpec['harga_per_unit_bulanan'])) {
                                    $updateUnitData['harga_sewa_bulanan'] = $quotationSpec['harga_per_unit_bulanan'];
                                }
                                if (!empty($quotationSpec['harga_per_unit_harian'])) {
                                    $updateUnitData['harga_sewa_harian'] = $quotationSpec['harga_per_unit_harian'];
                                }
                            }
                        }

                        // Get accessories from SPK prepared_units
                        if ($spk && !empty($spk['spesifikasi'])) {
                            $spesifikasiData = json_decode($spk['spesifikasi'], true);
                            if (isset($spesifikasiData['prepared_units']) && is_array($spesifikasiData['prepared_units'])) {
                                foreach ($spesifikasiData['prepared_units'] as $preparedUnit) {
                                    if (isset($preparedUnit['unit_id']) && $preparedUnit['unit_id'] == $deliveryUnit['unit_id']) {
                                        if (isset($preparedUnit['aksesoris_tersedia'])) {
                                            // aksesoris_tersedia is already JSON string, use directly
                                            $updateUnitData['aksesoris'] = $preparedUnit['aksesoris_tersedia'];
                                        }
                                        break;
                                    }
                                }
                            }
                        }

                        $this->db->table('inventory_unit')
                            ->where('id_inventory_unit', $deliveryUnit['unit_id'])
                            ->update($updateUnitData);

                        log_message('info', "Updated inventory_unit {$deliveryUnit['unit_id']} with comprehensive data for DI {$id}");
                    }
                }

                // Update all attachments in this DI to link them with the delivery
                $deliveryAttachments = $this->db->table('delivery_items')
                    ->where('di_id', $id)
                    ->where('item_type', 'ATTACHMENT')
                    ->get()->getResultArray();

                foreach ($deliveryAttachments as $deliveryAttachment) {
                    if (!empty($deliveryAttachment['attachment_id']) && !empty($deliveryAttachment['parent_unit_id'])) {
                        // Find the inventory_attachments record based on the attachment
                        $inventoryAttachment = $this->db->table('inventory_attachments')
                            ->where('attachment_type_id', $deliveryAttachment['attachment_id'])
                            ->where('inventory_unit_id', $deliveryAttachment['parent_unit_id'])
                            ->get()->getRowArray();

                        if ($inventoryAttachment) {
                            $updateAttachmentData = [
                                'updated_at' => date('Y-m-d H:i:s')
                            ];

                            $this->db->table('inventory_attachments')
                                ->where('id', $inventoryAttachment['id'])
                                ->update($updateAttachmentData);

                            log_message('info', "Updated inventory_attachments {$inventoryAttachment['id']} for DI {$id}");
                        }
                    }
                }

                log_message('info', "Successfully updated inventory relationships for DI {$id} when delivered");
                
            } catch (\Exception $e) {
                log_message('error', "Failed to update inventory relationships for DI {$id}: " . $e->getMessage());
                // Continue execution - this is not critical enough to fail the delivery
            }
        }

        $stageNames = [
            'perencanaan' => 'Perencanaan Pengiriman',
            'berangkat' => 'Berangkat',
            'sampai' => 'Sampai'
        ];
        
        log_message('info', 'diApproveStage completed successfully for DI ' . $id . ', stage: ' . $stage);

        return $this->response->setJSON([
            'success'=>true,
            'message'=>'Approval ' . $stageNames[$stage] . ' berhasil disimpan',
            'csrf_hash'=>csrf_hash()
        ]);
    }

    public function diPrint($id)
    {
        $id = (int)$id;
        // Get DI with quotation_number via join
        $di = $this->db->table('delivery_instructions')
            ->select('delivery_instructions.*, spk.kontrak_id as spk_kontrak_id, q.quotation_number')
            ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
            ->join('quotation_specifications qs', 'qs.id_specification = spk.quotation_specification_id', 'left')
            ->join('quotations q', 'q.id_quotation = qs.id_quotation', 'left')
            ->where('delivery_instructions.id', $id)
            ->get()
            ->getRowArray();
        
        if (!$di) {
            return $this->response->setStatusCode(404)->setBody('Delivery Instruction tidak ditemukan');
        }

        // Get items untuk DI ini - try delivery_items first
        // Note: delivery_items stores TYPE ID only, not inventory instance
        $items = $this->db->table('delivery_items di')
            ->select('di.*, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit')
            ->select('att.tipe as att_tipe, att.merk as att_merk, att.model as att_model')
            ->join('inventory_unit iu','iu.id_inventory_unit = di.unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('attachment att', 'att.id_attachment = di.attachment_id', 'left')
            ->where('di.di_id', $id)
            ->get()->getResultArray();
        
        // Note: If delivery_items is empty, items will be empty array
        // This is expected behavior - DI may not have items assigned yet

        // Jika ada unit items, generate PDF per unit
        $unitItems = array_filter($items, function($item) {
            return $item['item_type'] === 'UNIT';
        });

        if (count($unitItems) > 1) {
            // Multiple units - redirect to multi-print
            return redirect()->to(base_url("operational/delivery/print-multi/{$id}"));
        } else if (count($unitItems) == 1) {
            // Single unit - print with unit data
            $unitItem = $unitItems[0];
            return $this->diPrintWithUnit($di, $unitItem);
        } else {
            // No units, check for attachments
            $attachmentItems = array_filter($items, function($item) {
                return $item['item_type'] === 'ATTACHMENT';
            });
            
            if (!empty($attachmentItems)) {
                // Print with attachment data
                return $this->diPrintWithAttachment($di, $attachmentItems[0]);
            } else {
                // No items, print basic DI
                return $this->diPrintBasic($di);
            }
        }
    }

    public function diPrintMulti($id)
    {
        $id = (int)$id;
        $di = $this->diModel->find($id);
        if ($di && !is_array($di)) { $di = (array)$di; }
        if (!$di) {
            return $this->response->setStatusCode(404)->setBody('Delivery Instruction tidak ditemukan');
        }

        // Get items untuk DI ini - try delivery_items first
        // Note: delivery_items stores TYPE ID only, not inventory instance
        $items = $this->db->table('delivery_items di')
            ->select('di.*, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit')
            ->select('att.tipe as att_tipe, att.merk as att_merk, att.model as att_model')
            ->join('inventory_unit iu','iu.id_inventory_unit = di.unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('attachment att', 'att.id_attachment = di.attachment_id', 'left')
            ->where('di.di_id', $id)
            ->get()->getResultArray();
        
        // Note: If delivery_items is empty, items will be empty array
        // This is expected behavior - DI may not have items assigned yet

        // Filter hanya unit items
        $unitItems = array_filter($items, function($item) {
            return $item['item_type'] === 'UNIT';
        });

        if (empty($unitItems)) {
            return $this->response->setStatusCode(404)->setBody('Tidak ada unit yang ditemukan untuk DI ini');
        }

        // Generate HTML untuk multiple DI (satu per unit)
        $html = '';
        $totalUnits = count($unitItems);
        $currentUnit = 1;

        foreach ($unitItems as $unitItem) {
            // Generate nomor DI yang unik untuk setiap unit
            $unitDI = $di;
            $unitDI['nomor_di'] = $di['nomor_di'] . '-U' . str_pad($currentUnit, 2, '0', STR_PAD_LEFT);
            
            $html .= $this->generateDIHTML($unitDI, $unitItem, $currentUnit, $totalUnits);
            
            // Add page break except for last unit
            if ($currentUnit < $totalUnits) {
                $html .= '<div style="page-break-after: always;"></div>';
            }
            
            $currentUnit++;
        }

        return $this->response->setBody($html);
    }

    private function diPrintWithUnit($di, $unitItem)
    {
        return $this->response->setBody($this->generateDIHTML($di, $unitItem, 1, 1));
    }

    private function diPrintWithAttachment($di, $attachmentItem)
    {
        return $this->response->setBody($this->generateDIHTML($di, $attachmentItem, 1, 1));
    }

    private function diPrintBasic($di)
    {
        return $this->response->setBody($this->generateDIHTML($di, null, 1, 1));
    }

    private function generateDIHTML($di, $item = null, $currentUnit = 1, $totalUnits = 1)
    {
        // Get related SPK data and spesifikasi
        $spk = [];
        $spesifikasi = [];
        
        if (!empty($di['spk_id'])) {
            $spk = $this->db->table('spk s')
                ->select('s.*, COALESCE(CONCAT(u.first_name, " ", u.last_name), u.username, s.dibuat_oleh) as created_by_name')
                ->select('COALESCE(CONCAT(u.first_name, " ", u.last_name), u.username, s.dibuat_oleh) as marketing_name', false)
                ->join('users u', 'u.id = s.dibuat_oleh', 'left')
                ->where('s.id', $di['spk_id'])
                ->get()->getRowArray();
            if ($spk && !empty($spk['spesifikasi'])) {
                $decoded = json_decode($spk['spesifikasi'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $spesifikasi = $decoded;
                }
            }
            
            // Add stage_status and prepared_units_detail data (same as SPK print)
            if ($spk) {
                $stageStatus = $this->getSpkStageStatusData($spk['id']);
                $spk['stage_status'] = $stageStatus;
                
                $preparedUnitsDetail = $this->getPreparedUnitsDetail($spk['id'], $stageStatus);
                $spk['prepared_units_detail'] = $preparedUnitsDetail;
            }
        }

        // Enrich spesifikasi with lookup names (similar to SPK print)
        $enriched = $spesifikasi;
        $mapQueries = [
            'departemen_id' => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen'],
            'kapasitas_id'  => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit'],
            'mast_id'       => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast'],
            'ban_id'        => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban'],
            'valve_id'      => ['table'=>'valve','id'=>'id_valve','name'=>'jumlah_valve'],
            'roda_id'       => ['table'=>'jenis_roda','id'=>'id_roda','name'=>'tipe_roda'],
        ];
        
        foreach ($mapQueries as $key => $cfg) {
            if (!empty($spesifikasi[$key])) {
                $rec = $this->db->table($cfg['table'])->select($cfg['name'].' as name', false)->where($cfg['id'], $spesifikasi[$key])->get()->getRowArray();
                if ($rec && isset($rec['name'])) $enriched[$key.'_name'] = $rec['name'];
            }
        }

        // Use item data if provided, otherwise fallback to SPK data
        if ($item && $item['item_type'] === 'UNIT') {
            // Load complete unit data using inventory_unit_components view
            $u = $this->db->table('inventory_unit_components iuc')
                ->select('iuc.*')
                ->select('iu.tahun_unit, iu.lokasi_unit, iu.tipe_unit_id, iu.model_unit_id, iu.kapasitas_unit_id, iu.model_mast_id, iu.model_mesin_id, iu.departemen_id')
                ->select('mu.merk_unit, mu.model_unit')
                ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model')
                ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = iuc.id_inventory_unit', 'left')
                ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                ->join('mesin m','m.id = iu.model_mesin_id','left')
                ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                ->where('iuc.id_inventory_unit', $item['unit_id'])
                ->get()->getRowArray();

            if ($u) {
                $enriched['selected']['unit'] = [
                    'id' => (int)$u['id_inventory_unit'],
                    'no_unit' => $u['no_unit'] ?? null,
                    'serial_number' => $u['serial_number'] ?? null,
                    'tahun_unit' => $u['tahun_unit'] ?? null,
                    'merk_unit' => $u['merk_unit'] ?? null,
                    'model_unit' => $u['model_unit'] ?? null,
                    'lokasi_unit' => $u['lokasi_unit'] ?? null,
                    'tipe_jenis' => $u['tipe_jenis'] ?? null,
                    'jenis_unit' => $u['jenis_unit'] ?? null,
                    'kapasitas_name' => $u['kapasitas_name'] ?? null,
                    'departemen_name' => $u['departemen_name'] ?? null,
                    'sn_mast' => $u['sn_mast'] ?? null,
                    'sn_mesin' => $u['sn_mesin'] ?? null,
                    'sn_baterai' => $u['sn_baterai'] ?? null,
                    'sn_charger' => $u['sn_charger'] ?? null,
                    'sn_mast_formatted' => !empty($u['sn_mast']) ? ($u['mast_model'] ?? 'Mast') . ' (' . $u['sn_mast'] . ')' : ($u['mast_model'] ?? ''),
                    'sn_mesin_formatted' => !empty($u['sn_mesin']) ? ($u['mesin_model'] ?? 'Mesin') . ' (' . $u['sn_mesin'] . ')' : ($u['mesin_model'] ?? ''),
                    'sn_baterai_formatted' => !empty($u['sn_baterai']) ? ($u['tipe_baterai'] ?? 'Baterai') . ' (' . $u['sn_baterai'] . ')' : ($u['tipe_baterai'] ?? ''),
                    'sn_charger_formatted' => !empty($u['sn_charger']) ? ($u['tipe_charger'] ?? 'Charger') . ' (' . $u['sn_charger'] . ')' : ($u['tipe_charger'] ?? ''),
                ];

                // Override spesifikasi with unit data
                $enriched['tipe_jenis'] = $u['tipe_jenis'] ?? $enriched['tipe_jenis'] ?? '';
                $enriched['jenis_unit'] = $u['jenis_unit'] ?? $enriched['jenis_unit'] ?? '';
                $enriched['merk_unit'] = $u['merk_unit'] ?? $enriched['merk_unit'] ?? '';
                $enriched['model_unit'] = $u['model_unit'] ?? $enriched['model_unit'] ?? '';
                $enriched['kapasitas_id_name'] = $u['kapasitas_name'] ?? $enriched['kapasitas_id_name'] ?? '';
                $enriched['departemen_id_name'] = $u['departemen_name'] ?? $enriched['departemen_id_name'] ?? '';
            }
        } else {
            // Load unit data from SPK approval workflow if available
            if ($spk && !empty($spk['persiapan_unit_id']) && $spk['persiapan_unit_id'] != '0') {
                $u = $this->db->table('inventory_unit_components iuc')
                    ->select('iuc.*')
                    ->select('iu.tahun_unit, iu.lokasi_unit, iu.tipe_unit_id, iu.model_unit_id, iu.kapasitas_unit_id, iu.model_mast_id, iu.model_mesin_id, iu.departemen_id')
                    ->select('mu.merk_unit, mu.model_unit')
                    ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                    ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model')
                    ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                    ->join('inventory_unit iu', 'iu.id_inventory_unit = iuc.id_inventory_unit', 'left')
                    ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                    ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                    ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                    ->join('mesin m','m.id = iu.model_mesin_id','left')
                    ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                    ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                    ->where('iuc.id_inventory_unit', $spk['persiapan_unit_id'])
                    ->get()->getRowArray();

                if ($u) {
                    $enriched['selected']['unit'] = [
                        'id' => (int)$u['id_inventory_unit'],
                        'no_unit' => $u['no_unit'] ?? null,
                        'serial_number' => $u['serial_number'] ?? null,
                        'tahun_unit' => $u['tahun_unit'] ?? null,
                        'merk_unit' => $u['merk_unit'] ?? null,
                        'model_unit' => $u['model_unit'] ?? null,
                        'lokasi_unit' => $u['lokasi_unit'] ?? null,
                        'tipe_jenis' => $u['tipe_jenis'] ?? null,
                        'jenis_unit' => $u['jenis_unit'] ?? null,
                        'kapasitas_name' => $u['kapasitas_name'] ?? null,
                        'departemen_name' => $u['departemen_name'] ?? null,
                        'sn_mast' => $u['sn_mast'] ?? null,
                        'sn_mesin' => $u['sn_mesin'] ?? null,
                        'sn_baterai' => $u['sn_baterai'] ?? null,
                        'sn_charger' => $u['sn_charger'] ?? null,
                        'sn_mast_formatted' => !empty($u['sn_mast']) ? ($u['mast_model'] ?? 'Mast') . ' (' . $u['sn_mast'] . ')' : ($u['mast_model'] ?? ''),
                        'sn_mesin_formatted' => !empty($u['sn_mesin']) ? ($u['mesin_model'] ?? 'Mesin') . ' (' . $u['sn_mesin'] . ')' : ($u['mesin_model'] ?? ''),
                        'sn_baterai_formatted' => !empty($u['sn_baterai']) ? ($u['tipe_baterai'] ?? 'Baterai') . ' (' . $u['sn_baterai'] . ')' : ($u['tipe_baterai'] ?? ''),
                        'sn_charger_formatted' => !empty($u['sn_charger']) ? ($u['tipe_charger'] ?? 'Charger') . ' (' . $u['sn_charger'] . ')' : ($u['tipe_charger'] ?? ''),
                    ];

                    // Override spesifikasi with unit data
                    $enriched['tipe_jenis'] = $u['tipe_jenis'] ?? $enriched['tipe_jenis'] ?? '';
                    $enriched['jenis_unit'] = $u['jenis_unit'] ?? $enriched['jenis_unit'] ?? '';
                    $enriched['merk_unit'] = $u['merk_unit'] ?? $enriched['merk_unit'] ?? '';
                    $enriched['model_unit'] = $u['model_unit'] ?? $enriched['model_unit'] ?? '';
                    $enriched['kapasitas_id_name'] = $u['kapasitas_name'] ?? $enriched['kapasitas_id_name'] ?? '';
                    $enriched['departemen_id_name'] = $u['departemen_name'] ?? $enriched['departemen_id_name'] ?? '';
                }
            }
        }

        // Load attachment data
        if ($item && $item['item_type'] === 'ATTACHMENT') {
            $componentHelper = new InventoryComponentHelper();
            $a = $componentHelper->getAttachmentByInventoryId($item['attachment_id']);

            if ($a) {
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'tipe' => $a['tipe'] ?? null,
                    'merk' => $a['merk'] ?? null,
                    'model' => $a['model'] ?? null,
                    'sn_attachment' => $a['sn_attachment'] ?? null,
                    'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? ($a['model'] ?? 'Attachment') . ' (' . $a['sn_attachment'] . ')' : ($a['model'] ?? ''),
                ];

                // Override spesifikasi with attachment data
                $enriched['attachment_tipe'] = $a['tipe'] ?? $enriched['attachment_tipe'] ?? '';
            }
        } else if ($spk && !empty($spk['fabrikasi_attachment_id'])) {
            $componentHelper = new InventoryComponentHelper();
            $a = $componentHelper->getAttachmentByInventoryId($spk['fabrikasi_attachment_id']);

            if ($a) {
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'tipe' => $a['tipe'] ?? null,
                    'merk' => $a['merk'] ?? null,
                    'model' => $a['model'] ?? null,
                    'sn_attachment' => $a['sn_attachment'] ?? null,
                    'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? ($a['model'] ?? 'Attachment') . ' (' . $a['sn_attachment'] . ')' : ($a['model'] ?? ''),
                ];

                // Override spesifikasi with attachment data
                $enriched['attachment_tipe'] = $a['tipe'] ?? $enriched['attachment_tipe'] ?? '';
            }
        }

        // Generate view with data
        $data = [
            'di' => $di,
            'spk' => $spk ?: [],
            'spesifikasi' => $enriched,
            'items' => [],
            'unit_item' => $item && $item['item_type'] === 'UNIT' ? $item : null,
            'current_unit' => $currentUnit,
            'total_units' => $totalUnits
        ];

        // Return HTML string instead of view response for multi-print
        return view('operational/print_di', $data, ['saveData' => false]);
    }

    public function trackingTest()
    {
        try {
            // Test database connection
            $test = $this->db->table('spk')->limit(1)->get()->getRowArray();
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Database connection OK',
                'sample_spk' => $test
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }





    /**
     * Get audit trail data for units
     */
    public function auditTrail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $input = json_decode($this->request->getBody(), true);
            $unitIds = $input['unit_ids'] ?? [];

            if (empty($unitIds) || !is_array($unitIds)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit IDs required']);
            }

            // Sanitize unit IDs
            $unitIds = array_filter(array_map('intval', $unitIds));

            if (empty($unitIds)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Valid unit IDs required']);
            }

            // Fetch audit trail data
            $auditData = $this->db->table('unit_activity_log')
                ->select('unit_id, activity_type, activity_description, user_name, user_role, created_at')
                ->whereIn('unit_id', $unitIds)
                ->orderBy('created_at', 'DESC')
                ->limit(50) // Limit to recent 50 activities
                ->get()
                ->getResultArray();

            // Also get workflow tracking data
            $workflowData = $this->db->table('rental_workflow_tracking')
                ->select('unit_id, workflow_stage as activity_type, stage_description as activity_description, user_name, user_role, stage_start_date as created_at')
                ->whereIn('unit_id', $unitIds)
                ->orderBy('stage_start_date', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();

            // Combine and sort all data
            $allData = array_merge($auditData, $workflowData);
            
            // Sort by date descending
            usort($allData, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            // Limit to latest 30 entries
            $allData = array_slice($allData, 0, 30);

            return $this->response->setJSON([
                'success' => true,
                'data' => $allData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'auditTrail error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false, 
                'message' => 'Internal server error'
            ]);
        }
    }

    // ========================================
    // DI Workflow Logic Methods
    // ========================================

    /**
     * Get jenis perintah kerja for dropdown
     */
    public function getJenisPerintahKerja()
    {
        try {
            $jenisPerintah = $this->db->table('jenis_perintah_kerja')
                ->select('id, kode, nama, deskripsi')
                ->where('aktif', 1)
                ->orderBy('kode')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $jenisPerintah
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getJenisPerintahKerja error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load jenis perintah kerja'
            ]);
        }
    }

    /**
     * Get tujuan perintah kerja based on jenis
     */
    public function getTujuanPerintahKerja($jenisId = null)
    {
        try {
            if (!$jenisId) {
                $jenisId = $this->request->getGet('jenis_id');
            }

            if (!$jenisId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jenis ID required'
                ]);
            }

            $tujuanPerintah = $this->db->table('tujuan_perintah_kerja')
                ->select('id, kode, nama, deskripsi')
                ->where('jenis_perintah_id', $jenisId)
                ->where('aktif', 1)
                ->orderBy('kode')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $tujuanPerintah
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getTujuanPerintahKerja error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load tujuan perintah kerja'
            ]);
        }
    }

    /**
     * Get available SPK based on jenis and tujuan perintah with contract units
     */
    public function getAvailableSpkWithUnits()
    {
        try {
            $jenisId = $this->request->getGet('jenis_id');
            $tujuanId = $this->request->getGet('tujuan_id');

            if (!$jenisId || !$tujuanId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jenis ID and Tujuan ID required'
                ]);
            }

            // Get jenis and tujuan codes
            $jenis = $this->db->table('jenis_perintah_kerja')
                ->where('id', $jenisId)
                ->get()
                ->getRowArray();

            $tujuan = $this->db->table('tujuan_perintah_kerja')
                ->where('id', $tujuanId)
                ->get()
                ->getRowArray();

            if (!$jenis || !$tujuan) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid jenis or tujuan ID'
                ]);
            }

            // Use service to get available SPK with contract info
            $spkList = $this->diService->getAvailableSpkWithContractInfo($jenis['kode'], $tujuan['kode']);

            // Add constraints information
            $constraints = $this->diService->getSpkSelectionConstraints($jenis['kode'], $tujuan['kode']);

            return $this->response->setJSON([
                'success' => true,
                'data' => $spkList,
                'constraints' => $constraints,
                'message' => $this->getSpkSelectionMessage($jenis['kode'], $tujuan['kode']),
                'workflow_type' => in_array($jenis['kode'], ['TARIK', 'TUKAR']) ? 'contract_based' : 'unit_selection'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getAvailableSpkWithUnits error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load available SPK: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get contract units for TARIK/TUKAR operations
     */
    public function getContractUnits()
    {
        // Check permission: Operational perlu akses ke marketing kontrak (cross-division)
        if (!$this->canAccess('marketing') && !$this->canViewResource('marketing', 'kontrak')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: You do not have permission to view kontrak'
            ])->setStatusCode(403);
        }
        
        try {
            $kontrakId = $this->request->getGet('kontrak_id');
            $jenisId = $this->request->getGet('jenis_id');
            $tujuanId = $this->request->getGet('tujuan_id');

            if (!$kontrakId || !$jenisId || !$tujuanId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak ID, Jenis ID, and Tujuan ID required'
                ]);
            }

            // Get codes
            $jenis = $this->db->table('jenis_perintah_kerja')
                ->where('id', $jenisId)
                ->get()
                ->getRowArray();

            $tujuan = $this->db->table('tujuan_perintah_kerja')
                ->where('id', $tujuanId)
                ->get()
                ->getRowArray();

            if (!$jenis || !$tujuan) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid jenis or tujuan ID'
                ]);
            }

            // Get contract units
            $contractUnits = $this->diService->getContractUnits($kontrakId, $jenis['kode'], $tujuan['kode']);

            // Get contract info
            $contractInfo = $this->db->table('kontrak')
                ->where('id', $kontrakId)
                ->get()
                ->getRowArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $contractUnits,
                'contract_info' => $contractInfo,
                'message' => $this->getContractUnitMessage($jenis['kode'], count($contractUnits)),
                'selection_rules' => $this->getUnitSelectionRules($jenis['kode'], $tujuan['kode'])
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getContractUnits error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load contract units: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process DI approval with unit workflow
     */
    public function processWorkflowApproval()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$data || !isset($data['di_id'], $data['stage'], $data['jenis_perintah'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $result = null;

            // Process based on jenis perintah
            if ($data['jenis_perintah'] === 'TARIK') {
                $result = $this->diService->processUnitTarik(
                    $data['unit_ids'],
                    $data['di_id'],
                    $data['stage']
                );
            } elseif ($data['jenis_perintah'] === 'TUKAR') {
                $result = $this->diService->processUnitTukar(
                    $data['old_unit_ids'],
                    $data['new_unit_ids'],
                    $data['di_id'],
                    $data['stage']
                );
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unsupported jenis perintah for workflow processing'
                ]);
            }

            if ($result['success']) {
                // Update DI status
                $this->db->table('delivery_instructions')
                    ->where('id', $data['di_id'])
                    ->update([
                        'status' => $data['stage'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                // Log activity
                $activityData = json_encode([
                    'di_id' => $data['di_id'],
                    'stage' => $data['stage'],
                    'jenis_perintah' => $data['jenis_perintah']
                ]);
                
                $this->logActivity('DI_WORKFLOW_APPROVED', $activityData, $data['di_id'], 'delivery_instructions');
            }

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'processWorkflowApproval error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to process workflow approval: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available units for selected SPK
     */
    public function getAvailableUnits()
    {
        try {
            $spkId = $this->request->getGet('spk_id');
            $jenisId = $this->request->getGet('jenis_id');
            $tujuanId = $this->request->getGet('tujuan_id');

            if (!$spkId || !$jenisId || !$tujuanId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SPK ID, Jenis ID, and Tujuan ID required'
                ]);
            }

            // Get codes
            $jenis = $this->db->table('jenis_perintah_kerja')
                ->where('id', $jenisId)
                ->get()
                ->getRowArray();

            $tujuan = $this->db->table('tujuan_perintah_kerja')
                ->where('id', $tujuanId)
                ->get()
                ->getRowArray();

            if (!$jenis || !$tujuan) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid jenis or tujuan ID'
                ]);
            }

            // Use service to get available units with business logic
            $unitList = $this->diService->getAvailableUnits($spkId, $jenis['kode'], $tujuan['kode']);

            // Get unit selection rules
            $rules = TujuanPerintahKerja::getUnitSelectionRules($tujuan['kode']);

            return $this->response->setJSON([
                'success' => true,
                'data' => $unitList,
                'rules' => $rules,
                'message' => $this->getUnitSelectionMessage($jenis['kode'], $tujuan['kode'])
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getAvailableUnits error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load available units: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate DI data before creation
     */
    public function validateDiData()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$data) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No data provided'
                ]);
            }

            // Use service to validate
            $errors = $this->diService->validateDiCreation($data);

            if (empty($errors)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Validation passed'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'validateDiData error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get workflow information for jenis and tujuan
     */
    public function getWorkflowInfo()
    {
        try {
            $jenisId = $this->request->getGet('jenis_id');
            $tujuanId = $this->request->getGet('tujuan_id');

            if (!$jenisId || !$tujuanId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jenis ID and Tujuan ID required'
                ]);
            }

            // Get codes
            $jenis = $this->db->table('jenis_perintah_kerja')
                ->where('id', $jenisId)
                ->get()
                ->getRowArray();

            $tujuan = $this->db->table('tujuan_perintah_kerja')
                ->where('id', $tujuanId)
                ->get()
                ->getRowArray();

            if (!$jenis || !$tujuan) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid jenis or tujuan ID'
                ]);
            }

            // Get recommended next steps
            $nextSteps = $this->diService->getRecommendedNextSteps($jenis['kode'], $tujuan['kode']);

            // Get constraints
            $constraints = $this->diService->getSpkSelectionConstraints($jenis['kode'], $tujuan['kode']);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'jenis' => $jenis,
                    'tujuan' => $tujuan,
                    'next_steps' => $nextSteps,
                    'constraints' => $constraints
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getWorkflowInfo error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load workflow info: ' . $e->getMessage()
            ]);
        }
    }

    // ========================================
    // Helper Methods
    // ========================================

    /**
     * Get user-friendly message for contract unit selection
     */
    private function getContractUnitMessage($jenisKode, $unitCount)
    {
        $messages = [
            'TARIK' => "Menampilkan {$unitCount} unit yang sedang disewa dan dapat ditarik dari kontrak ini",
            'TUKAR' => "Menampilkan {$unitCount} unit yang sedang disewa dan dapat ditukar dari kontrak ini"
        ];

        return $messages[$jenisKode] ?? "Menampilkan {$unitCount} unit dari kontrak ini";
    }

    /**
     * Get unit selection rules for contract-based operations
     */
    private function getUnitSelectionRules($jenisKode, $tujuanKode)
    {
        $rules = [
            'TARIK' => [
                'min_selection' => 1,
                'max_selection' => null, // unlimited
                'allow_partial' => true,
                'description' => 'Pilih unit yang akan ditarik dari lokasi pelanggan',
                'warning' => 'Unit yang ditarik akan terputus dari kontrak dan kembali ke workshop'
            ],
            'TUKAR' => [
                'min_selection' => 1,
                'max_selection' => null,
                'allow_partial' => true,
                'description' => 'Pilih unit yang akan ditukar dengan unit baru',
                'warning' => 'Unit lama akan diganti dengan unit baru dalam kontrak yang sama'
            ]
        ];

        return $rules[$jenisKode] ?? [];
    }

    /**
     * Get user-friendly message for SPK selection
     */
    private function getSpkSelectionMessage($jenisKode, $tujuanKode)
    {
        $messages = [
            'TARIK_HABIS_KONTRAK' => 'Menampilkan SPK dengan kontrak yang sudah berakhir atau non-aktif',
            'ANTAR_BARU' => 'Menampilkan SPK untuk kontrak baru atau tanpa kontrak',
            'ANTAR_TAMBAHAN' => 'Menampilkan SPK dengan kontrak aktif untuk unit tambahan',
            'TUKAR_UPGRADE' => 'Menampilkan SPK dengan kontrak aktif untuk upgrade unit',
            'TUKAR_DOWNGRADE' => 'Menampilkan SPK dengan kontrak aktif untuk downgrade unit',
        ];

        return $messages[$tujuanKode] ?? 'Menampilkan SPK yang tersedia sesuai dengan jenis dan tujuan perintah';
    }

    /**
     * Get user-friendly message for unit selection
     */
    private function getUnitSelectionMessage($jenisKode, $tujuanKode)
    {
        $messages = [
            'TARIK' => 'Menampilkan unit yang sedang disewa/beroperasi untuk ditarik',
            'ANTAR' => 'Menampilkan unit yang tersedia di gudang untuk diantarkan',
            'TUKAR' => 'Menampilkan unit yang terkait dengan kontrak untuk ditukar',
            'RELOKASI' => 'Menampilkan unit yang dapat dipindahkan antar lokasi',
        ];

        return $messages[$jenisKode] ?? 'Menampilkan unit yang sesuai dengan jenis perintah';
    }

    /**
     * Get SPK stage status data (copied from Marketing controller)
     */
    private function getSpkStageStatusData($spkId)
    {
        try {
            $spk = $this->db->table('spk')->where('id', $spkId)->get()->getRowArray();
            if (!$spk) {
                return [];
            }

            $totalUnits = (int) $spk['jumlah_unit'];
            $unitStages = [];

            // Get stage data for each unit
            for ($unitIndex = 1; $unitIndex <= $totalUnits; $unitIndex++) {
                $stages = $this->db->table('spk_unit_stages sus')
                    ->select('sus.stage_name, sus.tanggal_approve, sus.mekanik, sus.catatan, sus.unit_id, sus.area_id, sus.aksesoris_tersedia, sus.battery_inventory_attachment_id, sus.charger_inventory_attachment_id, sus.attachment_inventory_attachment_id')
                    ->where('sus.spk_id', $spkId)
                    ->where('sus.unit_index', $unitIndex)
                    ->orderBy('sus.stage_name')
                    ->get()
                    ->getResultArray();

                $stageStatus = [];
                foreach ($stages as $stage) {
                    $stageStatus[$stage['stage_name']] = [
                        'completed' => !empty($stage['tanggal_approve']),
                        'mekanik' => $stage['mekanik'] ?? null,
                        'catatan' => $stage['catatan'] ?? null,
                        'tanggal_approve' => $stage['tanggal_approve'] ?? null,
                        'unit_id' => $stage['unit_id'] ?? null,
                        'area_id' => $stage['area_id'] ?? null,
                        'aksesoris_tersedia' => $stage['aksesoris_tersedia'] ?? null,
                        'battery_inventory_attachment_id' => $stage['battery_inventory_attachment_id'] ?? null,
                        'charger_inventory_attachment_id' => $stage['charger_inventory_attachment_id'] ?? null,
                        'attachment_inventory_attachment_id' => $stage['attachment_inventory_attachment_id'] ?? null
                    ];
                }

                $unitStages[$unitIndex] = $stageStatus;
            }

            return [
                'unit_stages' => $unitStages
            ];
        } catch (\Exception $e) {
            log_message('error', 'SPK Stage Status Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get prepared units detail (copied from Marketing controller)
     */
    private function getPreparedUnitsDetail($spkId, $stageStatus)
    {
        $preparedList = [];
        
        if (isset($stageStatus['unit_stages'])) {
            foreach ($stageStatus['unit_stages'] as $unitIndex => $unitStages) {
                if (isset($unitStages['persiapan_unit']) && $unitStages['persiapan_unit']['completed']) {
                    // Get unit details from persiapan_unit stage
                    $unitData = $unitStages['persiapan_unit'] ?? [];
                    $unitId = $unitData['unit_id'] ?? null;
                    
                    // Get unit details from inventory_unit with joins
                    $unitDetails = null;
                    if ($unitId) {
                        $unitDetails = $this->db->table('inventory_unit iu')
                            ->select('iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit, tu.tipe as jenis_unit, tu.jenis as jenis_unit_type, k.kapasitas_unit as kapasitas_name, tm.tipe_mast as mast_name, jr.tipe_roda as roda_name, tb.tipe_ban as ban_name, v.jumlah_valve as valve_name, d.nama_departemen as departemen_name')
                            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                            ->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
                            ->join('tipe_mast tm', 'tm.id_mast = iu.model_mast_id', 'left')
                            ->join('jenis_roda jr', 'jr.id_roda = iu.roda_id', 'left')
                            ->join('tipe_ban tb', 'tb.id_ban = iu.ban_id', 'left')
                            ->join('valve v', 'v.id_valve = iu.valve_id', 'left')
                            ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
                            ->where('iu.id_inventory_unit', $unitId)
                            ->get()
                            ->getRowArray();
                    }
                    
                    // Get battery and charger details from persiapan_unit stage
                    $batteryDetails = null;
                    $chargerDetails = null;
                    $attachmentDetails = null;
                    
                    if (isset($unitStages['persiapan_unit'])) {
                        $persiapanData = $unitStages['persiapan_unit'];
                        $batteryId = $persiapanData['battery_inventory_attachment_id'] ?? null;
                        $chargerId = $persiapanData['charger_inventory_attachment_id'] ?? null;
                        
                        $componentHelper = new InventoryComponentHelper();
                        
                        if ($batteryId) {
                            $batteryDetails = $componentHelper->getBatteryByInventoryId($batteryId);
                        }
                        
                        if ($chargerId) {
                            $chargerDetails = $componentHelper->getChargerByInventoryId($chargerId);
                        }
                    }
                    
                    // Get attachment from fabrikasi stage (same as SPK)
                    if (isset($unitStages['fabrikasi'])) {
                        $fabrikasiData = $unitStages['fabrikasi'];
                        $attachmentId = $fabrikasiData['attachment_inventory_attachment_id'] ?? null;
                        
                        $componentHelper = new InventoryComponentHelper();
                        
                        if ($attachmentId) {
                            $attachmentDetails = $componentHelper->getAttachmentByInventoryId($attachmentId);
                        }
                    }
                    
                    // Format No Unit: [no_unit] (SN: [serial_number]) - same as SPK
                    $noUnitFormatted = '';
                    if ($unitDetails['no_unit']) {
                        $noUnitFormatted = $unitDetails['no_unit'];
                        if ($unitDetails['serial_number']) {
                            $noUnitFormatted .= ' (SN: ' . $unitDetails['serial_number'] . ')';
                        }
                    } else {
                        $noUnitFormatted = 'Unit-' . $unitId;
                    }
                    
                    // Format Jenis Unit: [jenis] - [merk] ([model]) - same as SPK
                    $jenisUnitFormatted = '';
                    if ($unitDetails['jenis_unit_type']) {
                        $jenisUnitFormatted = $unitDetails['jenis_unit_type'];
                        if ($unitDetails['merk_unit'] && $unitDetails['model_unit']) {
                            $jenisUnitFormatted .= ' - ' . $unitDetails['merk_unit'] . ' (' . $unitDetails['model_unit'] . ')';
                        } elseif ($unitDetails['merk_unit']) {
                            $jenisUnitFormatted .= ' - ' . $unitDetails['merk_unit'];
                        }
                    } else {
                        $jenisUnitFormatted = 'REACH TRUCK';
                    }
                    
                    // Format Charger: [merk] [tipe] (SN: [sn]) - same as SPK
                    $chargerFormatted = '';
                    if ($chargerDetails && $chargerDetails['merk_charger'] && $chargerDetails['tipe_charger']) {
                        $chargerFormatted = $chargerDetails['merk_charger'] . ' ' . $chargerDetails['tipe_charger'];
                        if ($chargerDetails['sn_charger']) {
                            $chargerFormatted .= ' (SN: ' . $chargerDetails['sn_charger'] . ')';
                        }
                    } else {
                        $chargerFormatted = '-';
                    }
                    
                    // Format Baterai: [merk] [tipe] [jenis] (SN: [sn]) - same as SPK
                    $bateraiFormatted = '';
                    if ($batteryDetails && $batteryDetails['merk_baterai'] && $batteryDetails['tipe_baterai']) {
                        $bateraiFormatted = $batteryDetails['merk_baterai'] . ' ' . $batteryDetails['tipe_baterai'];
                        if ($batteryDetails['jenis_baterai']) {
                            $bateraiFormatted .= ' ' . $batteryDetails['jenis_baterai'];
                        }
                        if ($batteryDetails['sn_baterai']) {
                            $bateraiFormatted .= ' (SN: ' . $batteryDetails['sn_baterai'] . ')';
                        }
                    } else {
                        $bateraiFormatted = '-';
                    }
                    
                    // Format Attachment: [merk] - [model] [tipe] (SN: [sn]) - same as SPK
                    $attachmentFormatted = '';
                    if ($attachmentDetails && $attachmentDetails['merk'] && $attachmentDetails['model']) {
                        $attachmentFormatted = $attachmentDetails['merk'] . ' - ' . $attachmentDetails['model'];
                        if ($attachmentDetails['tipe']) {
                            $attachmentFormatted .= ' ' . $attachmentDetails['tipe'];
                        }
                        if ($attachmentDetails['sn_attachment']) {
                            $attachmentFormatted .= ' (SN: ' . $attachmentDetails['sn_attachment'] . ')';
                        }
                    } else {
                        $attachmentFormatted = 'ATT-' . $unitId;
                    }
                    
                    // Combine notes from all stages (same as SPK)
                    $combinedNotes = [];
                    $stageNames = [
                        'persiapan_unit' => 'Persiapan Unit',
                        'fabrikasi' => 'Fabrikasi', 
                        'painting' => 'Painting',
                        'pdi' => 'PDI'
                    ];
                    
                    foreach ($stageNames as $stageKey => $stageName) {
                        if (isset($unitStages[$stageKey]) && !empty($unitStages[$stageKey]['catatan'])) {
                            $combinedNotes[] = $stageName . ': ' . $unitStages[$stageKey]['catatan'];
                        }
                    }
                    
                    // Add DI stage notes (perencanaan, berangkat, sampai)
                    $diStageNames = [
                        'perencanaan' => 'Perencanaan',
                        'berangkat' => 'Berangkat',
                        'sampai' => 'Sampai'
                    ];
                    
                    foreach ($diStageNames as $stageKey => $stageName) {
                        if (isset($unitStages[$stageKey]) && !empty($unitStages[$stageKey]['catatan'])) {
                            $combinedNotes[] = $stageName . ': ' . $unitStages[$stageKey]['catatan'];
                        }
                    }
                    
                    // Build prepared unit data with formatted values (same as SPK)
                    $preparedUnit = [
                        'no_unit' => $noUnitFormatted,
                        'jenis_unit' => $jenisUnitFormatted,
                        'departemen_name' => $unitDetails['departemen_name'] ?? 'ELECTRIC',
                        'kapasitas_name' => $unitDetails['kapasitas_name'] ?? '15 Ton',
                        'mast_name' => $unitDetails['mast_name'] ?? 'Triplex (3-stage FFL) - ZSM450',
                        'roda_name' => $unitDetails['roda_name'] ?? '3-Wheel',
                        'ban_name' => $unitDetails['ban_name'] ?? 'Cushion (Ban Bantal)',
                        'valve_name' => $unitDetails['valve_name'] ?? '3 Valve',
                        'charger_sn' => $chargerFormatted,
                        'baterai_sn' => $bateraiFormatted,
                        'attachment_sn' => $attachmentFormatted,
                        'aksesoris' => $persiapanData['aksesoris_tersedia'] ?? 'LAMPU UTAMA, ROTARY LAMP, SENSOR PARKING, HORN SPEAKER, APAR 1 KG, BEACON',
                        'combined_notes' => implode(' | ', $combinedNotes)
                    ];
                    
                    $preparedList[] = $preparedUnit;
                }
            }
        }
        
        return $preparedList;
    }

    /**
     * Get temporary units tracking data
     */
    public function getTemporaryUnits()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $draw = $this->request->getPost('draw') ?? 1;
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 25;
        $customerFilter = $this->request->getPost('customer_filter') ?? '';
        $durationFilter = $this->request->getPost('duration_filter') ?? '';

        try {
            $builder = $this->db->table('kontrak_unit ku');
            $builder->select('
                ku.id as kontrak_unit_id,
                ku.temporary_start_date,
                ku.original_unit_id,
                DATEDIFF(NOW(), ku.temporary_start_date) as days_borrowed,
                c.customer_name,
                k.no_kontrak,
                iu_temp.no_unit as temporary_unit,
                iu_temp.serial_number as temp_serial,
                iu_orig.no_unit as original_unit,
                iu_orig.serial_number as orig_serial,
                iu_orig.workflow_status as original_workflow_status
            ')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->join('inventory_unit iu_temp', 'iu_temp.id_inventory_unit = ku.unit_id', 'left')
            ->join('inventory_unit iu_orig', 'iu_orig.id_inventory_unit = ku.original_unit_id', 'left')
            ->where('ku.is_temporary', 1)
            ->where('ku.temporary_end_date IS NULL');

            // Apply filters
            if ($customerFilter) {
                $builder->where('c.id', $customerFilter);
            }

            if ($durationFilter) {
                switch ($durationFilter) {
                    case '7':
                        $builder->having('days_borrowed <', 7);
                        break;
                    case '30':
                        $builder->having('days_borrowed >=', 7)->having('days_borrowed <=', 30);
                        break;
                    case '60':
                        $builder->having('days_borrowed >', 30)->having('days_borrowed <=', 60);
                        break;
                    case '90':
                        $builder->having('days_borrowed >', 60);
                        break;
                }
            }

            $totalRecords = $builder->countAllResults(false);
            $data = $builder->limit($length, $start)->get()->getResultArray();

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
                'success' => true
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getTemporaryUnits error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load temporary units: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get temporary units statistics
     */
    public function getTemporaryUnitsStats()
    {
        try {
            $customerFilter = $this->request->getGet('customer_filter') ?? '';
            $durationFilter = $this->request->getGet('duration_filter') ?? '';

            $builder = $this->db->table('kontrak_unit ku');
            $builder->select('
                COUNT(*) as total_temporary,
                SUM(CASE WHEN DATEDIFF(NOW(), ku.temporary_start_date) > 30 THEN 1 ELSE 0 END) as overdue,
                AVG(DATEDIFF(NOW(), ku.temporary_start_date)) as avg_days,
                SUM(CASE WHEN iu_orig.workflow_status = "MAINTENANCE_COMPLETED" THEN 1 ELSE 0 END) as ready_to_return
            ')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->join('inventory_unit iu_orig', 'iu_orig.id_inventory_unit = ku.original_unit_id', 'left')
            ->where('ku.is_temporary', 1)
            ->where('ku.temporary_end_date IS NULL');

            if ($customerFilter) {
                $builder->where('c.id', $customerFilter);
            }

            $stats = $builder->get()->getRowArray();

            return $this->response->setJSON([
                'success' => true,
                'stats' => [
                    'total_temporary' => (int)($stats['total_temporary'] ?? 0),
                    'overdue' => (int)($stats['overdue'] ?? 0),
                    'avg_days' => (float)($stats['avg_days'] ?? 0),
                    'ready_to_return' => (int)($stats['ready_to_return'] ?? 0)
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getTemporaryUnitsStats error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load stats: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get customers with temporary units
     */
    public function getCustomersWithTemporaryUnits()
    {
        try {
            $customers = $this->db->table('customers c')
                ->select('DISTINCT c.id, c.customer_name')
                ->join('kontrak k', 'k.customer_id = c.id', 'inner')
                ->join('kontrak_unit ku', 'ku.kontrak_id = k.id', 'inner')
                ->where('ku.is_temporary', 1)
                ->where('ku.temporary_end_date IS NULL')
                ->orderBy('c.customer_name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $customers
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getCustomersWithTemporaryUnits error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load customers: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process temporary unit return
     */
    public function processTemporaryUnitReturn()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $kontrakUnitId = $this->request->getPost('kontrak_unit_id');

        if (!$kontrakUnitId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing kontrak_unit_id']);
        }

        $this->db->transStart();

        try {
            // Get kontrak_unit data
            $kontrakUnit = $this->db->table('kontrak_unit')
                ->where('id', $kontrakUnitId)
                ->where('is_temporary', 1)
                ->where('temporary_end_date IS NULL')
                ->get()
                ->getRowArray();

            if (!$kontrakUnit) {
                throw new \Exception('Temporary unit assignment not found or already returned');
            }

            // Check if original unit is ready (MAINTENANCE_COMPLETED)
            $originalUnit = $this->db->table('inventory_unit')
                ->where('id_inventory_unit', $kontrakUnit['original_unit_id'])
                ->get()
                ->getRowArray();

            if ($originalUnit['workflow_status'] !== 'MAINTENANCE_COMPLETED') {
                throw new \Exception('Original unit is not ready to return (still in maintenance)');
            }

            // 1. Disconnect temporary unit
            $this->db->table('inventory_unit')
                ->where('id_inventory_unit', $kontrakUnit['unit_id'])
                ->update([
                    'kontrak_id' => null,
                    'customer_id' => null,
                    'customer_location_id' => null,
                    'workflow_status' => 'RETURNED_FROM_TEMP_ASSIGNMENT',
                    'status_unit_id' => 1, // AVAILABLE_STOCK
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // 2. Reconnect original unit to contract
            $this->db->table('inventory_unit')
                ->where('id_inventory_unit', $kontrakUnit['original_unit_id'])
                ->update([
                    'kontrak_id' => $kontrakUnit['kontrak_id'],
                    'customer_id' => $originalUnit['customer_id'], // Restore from backup
                    'customer_location_id' => $originalUnit['customer_location_id'],
                    'workflow_status' => 'RETURNED_TO_CUSTOMER',
                    'status_unit_id' => 7, // RENTAL_ACTIVE
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // 3. Update kontrak_unit - mark temporary assignment as ended
            $this->db->table('kontrak_unit')
                ->where('id', $kontrakUnitId)
                ->update([
                    'temporary_end_date' => date('Y-m-d H:i:s'),
                    'returned_by' => session()->get('user_id') ?? null,
                    'return_notes' => 'Original unit returned from maintenance'
                ]);

            // 4. Create activity log
            $this->logActivity(
                'operational',
                'return_temporary_unit',
                'Returned temporary unit ' . $kontrakUnit['unit_id'] . ', reconnected original unit ' . $kontrakUnit['original_unit_id'],
                (string)$kontrakUnitId
            );

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Temporary unit returned successfully'
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'processTemporaryUnitReturn error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to process return: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Log unit timeline events when DI completes
     * Records deployment or retrieval based on DI type
     */
    protected function logUnitTimelineOnDICompletion(int $diId, array $diData)
    {
        // Get DI jenis perintah kerja to determine if deployment or retrieval
        $jenisPerintahId = $diData['jenis_perintah_kerja_id'] ?? null;
        
        if (!$jenisPerintahId) {
            log_message('warning', "DI {$diId} has no jenis_perintah_kerja_id, skipping timeline logging");
            return;
        }
        
        // Get jenis perintah kerja details
        $jenisPK = $this->db->table('jenis_perintah_kerja')
            ->where('id', $jenisPerintahId)
            ->get()
            ->getRowArray();
        
        if (!$jenisPK) {
            log_message('warning', "Jenis perintah kerja {$jenisPerintahId} not found for DI {$diId}");
            return;
        }
        
        $jenisKode = $jenisPK['kode'] ?? '';
        
        // Get units from delivery_items
        $deliveryItems = $this->db->table('delivery_items')
            ->where('delivery_instruction_id', $diId)
            ->where('unit_id IS NOT NULL', null, false)
            ->get()
            ->getResultArray();
        
        if (empty($deliveryItems)) {
            log_message('info', "No units found in delivery_items for DI {$diId}");
            return;
        }
        
        // Get contract info for location
        $kontrakId = $diData['kontrak_id'] ?? null;
        $locationInfo = [];
        
        if ($kontrakId) {
            $kontrak = $this->db->table('kontrak')
                ->select('pelanggan, lokasi, nomor_kontrak')
                ->where('id', $kontrakId)
                ->get()
                ->getRowArray();
                
            if ($kontrak) {
                $locationInfo = [
                    'customer' => $kontrak['pelanggan'] ?? '',
                    'location' => $kontrak['lokasi'] ?? '',
                    'contract_number' => $kontrak['nomor_kontrak'] ?? ''
                ];
            }
        }
        
        // Log based on DI type
        foreach ($deliveryItems as $item) {
            $unitId = $item['unit_id'];
            
            try {
                if ($jenisKode === 'ANTAR') {
                    // Deployment: unit sent to customer
                    $this->unitTimelineService->recordDeployment(
                        $unitId,
                        $locationInfo['customer'] ?? 'Customer',
                        $locationInfo['location'] ?? 'Unknown Location',
                        $locationInfo['contract_number'] ?? null,
                        [
                            'di_id' => $diId,
                            'di_nomor' => $diData['nomor_di'] ?? null,
                            'deployment_date' => $diData['sampai_tanggal_approve'] ?? date('Y-m-d'),
                            'driver' => $diData['nama_supir'] ?? null,
                            'vehicle' => $diData['kendaraan'] ?? null
                        ]
                    );
                    log_message('info', "✅ Logged deployment for unit {$unitId} via DI {$diId}");
                    
                } elseif ($jenisKode === 'TARIK') {
                    // Retrieval: unit pulled from customer
                    $this->unitTimelineService->recordRetrieval(
                        $unitId,
                        $locationInfo['customer'] ?? 'Customer',
                        $locationInfo['location'] ?? 'Unknown Location',
                        'DI_COMPLETED',
                        [
                            'di_id' => $diId,
                            'di_nomor' => $diData['nomor_di'] ?? null,
                            'retrieval_date' => $diData['sampai_tanggal_approve'] ?? date('Y-m-d'),
                            'driver' => $diData['nama_supir'] ?? null,
                            'vehicle' => $diData['kendaraan'] ?? null,
                            'reason' => 'Unit retrieved via delivery instruction'
                        ]
                    );
                    log_message('info', "✅ Logged retrieval for unit {$unitId} via DI {$diId}");
                    
                } elseif ($jenisKode === 'TUKAR') {
                    // Exchange: log as retrieval + deployment (handled separately)
                    $this->unitTimelineService->recordRetrieval(
                        $unitId,
                        $locationInfo['customer'] ?? 'Customer',
                        $locationInfo['location'] ?? 'Unknown Location',
                        'UNIT_EXCHANGE',
                        [
                            'di_id' => $diId,
                            'di_nomor' => $diData['nomor_di'] ?? null,
                            'exchange_date' => $diData['sampai_tanggal_approve'] ?? date('Y-m-d'),
                            'operation_type' => 'TUKAR'
                        ]
                    );
                    log_message('info', "✅ Logged exchange (retrieval) for unit {$unitId} via DI {$diId}");
                    
                } else {
                    log_message('info', "DI type {$jenisKode} does not require timeline logging for unit {$unitId}");
                }
                
            } catch (\Exception $e) {
                log_message('error', "Failed to log timeline for unit {$unitId} in DI {$diId}: " . $e->getMessage());
                // Continue with other units even if one fails
            }
        }
    }

    /**
     * View temporary units report page
     */
    public function temporaryUnitsReport()
    {
        helper('simple_rbac');
        
        if (!can_view('operational')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Temporary Units Tracking Report',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/operational/delivery' => 'Operational',
                '/operational/temporary-units-report' => 'Temporary Units Report'
            ]
        ];

        return view('operational/temporary_units_report', $data);
    }
}

