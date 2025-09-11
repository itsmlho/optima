<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DeliveryInstructionModel;
use App\Models\DeliveryItemModel;
use App\Models\KontrakModel;
use App\Traits\ActivityLoggingTrait;

class Operational extends Controller
{
    use ActivityLoggingTrait;
    
    protected $db;
    protected $diModel;
    protected $diItemModel;
    protected $kontrakModel;

    public function __construct()
    {
    $this->db = \Config\Database::connect();
    $this->diModel = new DeliveryInstructionModel();
    $this->diItemModel = new DeliveryItemModel();
    $this->kontrakModel = new KontrakModel();
    }

    public function delivery()
    {
        return view('operational/delivery', [
            'title' => 'Delivery Instructions'
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
                jpk.nama as jenis_perintah,
                tpk.nama as tujuan_perintah
            ')
            ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
            ->join('jenis_perintah_kerja jpk', 'jpk.id = delivery_instructions.jenis_perintah_kerja_id', 'left')
            ->join('tujuan_perintah_kerja tpk', 'tpk.id = delivery_instructions.tujuan_perintah_kerja_id', 'left')
            ->orderBy('delivery_instructions.id','DESC')
            ->findAll();
            
        // Add items information for each DI
        foreach ($rows as &$row) {
            // Get items for this DI
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
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        
        $action = $this->request->getPost('action');
        
        try {
            $di = $this->diModel->find((int)$id);
            if (!$di) {
                return $this->response->setJSON(['success'=>false,'message'=>'DI tidak ditemukan']);
            }
            
            $updateData = [];
            
            switch($action) {
                case 'assign_driver':
                    $updateData['nama_supir'] = $this->request->getPost('nama_supir');
                    $updateData['no_hp_supir'] = $this->request->getPost('no_hp_supir');
                    $updateData['no_sim_supir'] = $this->request->getPost('no_sim_supir');
                    $updateData['kendaraan'] = $this->request->getPost('kendaraan');
                    $updateData['no_polisi_kendaraan'] = $this->request->getPost('no_polisi_kendaraan');
                    $updateData['status'] = 'PROCESSED';
                    break;
                    
                case 'approve_departure':
                    $updateData['berangkat_tanggal_approve'] = date('Y-m-d');
                    $updateData['catatan_berangkat'] = $this->request->getPost('catatan_berangkat');
                    $updateData['status'] = 'SHIPPED';
                    break;
                    
                case 'confirm_arrival':
                    $updateData['sampai_tanggal_approve'] = date('Y-m-d');
                    $updateData['catatan_sampai'] = $this->request->getPost('catatan_sampai');
                    $updateData['status'] = 'DELIVERED';
                    break;
                    
                case 'cancel':
                    $updateData['status'] = 'CANCELLED';
                    break;
                    
                default:
                    return $this->response->setJSON(['success'=>false,'message'=>'Aksi tidak valid']);
            }
            
            // Update will trigger the sync_di_status_temp_on_update trigger
            $oldDi = $this->diModel->find((int)$id);
            if ($oldDi && !is_array($oldDi)) { $oldDi = (array)$oldDi; }
            $updated = $this->diModel->update((int)$id, $updateData);
            
            if ($updated) {
                // Log DI status update using trait
                $this->logUpdate('delivery_instruction', $id, $oldDi, $updateData, [
                    'di_id' => $id,
                    'action' => $action,
                    'old_status' => $di['status'] ?? null,
                    'new_status' => $updateData['status'] ?? null
                ]);
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Status DI berhasil diperbarui',
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
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
        $di = $this->diModel->find((int)$id);
        if ($di && !is_array($di)) { $di = (array)$di; }
        if (!$di) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'DI tidak ditemukan']);
        }
        $spk = null;
        if (!empty($di['spk_id'])) {
            $spk = $this->db->table('spk')->where('id',(int)$di['spk_id'])->get()->getRowArray();
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
                $unitAttachments = $this->db->query("
                    SELECT
                        di.*,
                        ia.tipe_item,
                        CASE ia.tipe_item
                            WHEN 'battery' THEN b.merk_baterai
                            WHEN 'charger' THEN c.merk_charger
                            ELSE a.merk
                        END as merk,
                        CASE ia.tipe_item
                            WHEN 'battery' THEN b.tipe_baterai
                            WHEN 'charger' THEN c.tipe_charger
                            ELSE a.tipe
                        END as tipe,
                        CASE ia.tipe_item
                            WHEN 'battery' THEN b.jenis_baterai
                            WHEN 'charger' THEN NULL
                            ELSE a.model
                        END as model_or_jenis,
                        a.tipe as attachment_type,
                        a.merk as attachment_merk,
                        a.model as attachment_model,
                        b.merk_baterai, b.tipe_baterai, b.jenis_baterai,
                        c.merk_charger, c.tipe_charger
                    FROM delivery_items di
                    LEFT JOIN inventory_attachment ia ON (
                        (ia.attachment_id = di.attachment_id AND ia.tipe_item = 'attachment') OR
                        (ia.baterai_id = di.attachment_id AND ia.tipe_item = 'battery') OR
                        (ia.charger_id = di.attachment_id AND ia.tipe_item = 'charger')
                    )
                    LEFT JOIN attachment a ON a.id_attachment = di.attachment_id AND ia.tipe_item = 'attachment'
                    LEFT JOIN baterai b ON b.id = di.attachment_id AND ia.tipe_item = 'battery'
                    LEFT JOIN charger c ON c.id_charger = di.attachment_id AND ia.tipe_item = 'charger'
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
        $standaloneAttachments = $this->db->query("
            SELECT
                di.*,
                a.tipe, a.merk, a.model,
                ia.sn_attachment
            FROM delivery_items di
            LEFT JOIN attachment a ON a.id_attachment = di.attachment_id
            LEFT JOIN inventory_attachment ia ON ia.attachment_id = di.attachment_id AND ia.tipe_item = 'attachment'
            WHERE di.di_id = ?
            AND di.item_type = 'ATTACHMENT'
            AND (di.parent_unit_id IS NULL OR di.parent_unit_id = 0)
        ", [(int)$id])->getResultArray();

        // Format standalone attachments with proper labels
        $formattedStandaloneAttachments = [];
        foreach ($standaloneAttachments as $attachment) {
            $formattedStandaloneAttachments[] = [
                'id' => $attachment['attachment_id'],
                'tipe' => $attachment['tipe'] ?: '-',
                'merk' => $attachment['merk'] ?: '-', 
                'model' => $attachment['model'] ?: '-',
                'sn_attachment' => $attachment['sn_attachment'] ?: '-',
                'label' => ($attachment['tipe'] ?: '-') . ' • ' . ($attachment['merk'] ?: '-') . ' • ' . ($attachment['model'] ?: '-')
            ];
        }

        // Convert grouped items to array format
        $structuredItems = array_values($groupedItems);

        return $this->response->setJSON([
            'success'=>true,
            'data'=>$di,
            'spk'=>$spk,
            'items'=>$structuredItems,
            'attachments'=>$formattedStandaloneAttachments,
            'csrf_hash'=>csrf_hash()
        ]);
    }

    public function diApproveStage($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $stage = $this->request->getPost('stage');
        $tanggalApprove = date('Y-m-d'); // Use today's date automatically

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

        } elseif ($stage === 'berangkat') {
            // Berangkat hanya menyimpan catatan keberangkatan
            $catatanBerangkat = $this->request->getPost('catatan_berangkat');
            if ($catatanBerangkat) $updateData['catatan_berangkat'] = $catatanBerangkat;
            // On departure, mark status_eksekusi to DISPATCHED
            $updateData['status_eksekusi'] = 'DISPATCHED';

        } elseif ($stage === 'sampai') {
            $catatanSampai = $this->request->getPost('catatan_sampai');
            if ($catatanSampai) $updateData['catatan_sampai'] = $catatanSampai;
            
            // After sampai approval, update status to DELIVERED
            $updateData['status'] = 'DELIVERED';
            $updateData['status_eksekusi'] = 'DELIVERED';
        }

        // Log for debugging
        log_message('debug', 'Updating DI ' . $id . ' with data: ' . json_encode($updateData));
        
        // Update the DI
        try {
            // If perencanaan approved and not yet dispatched, use READY nomenclature
            if ($stage === 'perencanaan' && empty($di['berangkat_tanggal_approve']) && empty($di['sampai_tanggal_approve'])) {
                $updateData['status_eksekusi'] = 'READY';
            }
            
            $oldDi = $this->diModel->find((int)$id);
            if ($oldDi && !is_array($oldDi)) { $oldDi = (array)$oldDi; }
            
            $this->diModel->update((int)$id, $updateData);
            
            // Log DI stage approval using trait
            $this->logUpdate('delivery_instruction', $id, $oldDi, $updateData, [
                'di_id' => $id,
                'stage' => $stage,
                'tanggal_approve' => $tanggalApprove
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to update DI ' . $id . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }

    // If status becomes DELIVERED, update SPK status to COMPLETED
    if ($stage === 'sampai' && !empty($di['spk_id'])) {
            $this->db->table('spk')->where('id', $di['spk_id'])->update([
                'status' => 'COMPLETED',
                'diperbarui_pada' => date('Y-m-d H:i:s')
            ]);
            
            // Log status history
            try {
                $this->db->table('spk_status_history')->insert([
                    'spk_id' => $di['spk_id'],
                    'status_from' => 'IN_PROGRESS',
                    'status_to' => 'COMPLETED',
                    'changed_by' => session('user_id') ?: 1,
                    'note' => 'DI delivered: ' . $di['nomor_di'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } catch (\Exception $e) {
                // Continue if history logging fails (best effort)
                log_message('error', 'Failed to log SPK status history: ' . $e->getMessage());
            }
        }

    // If status becomes DELIVERED, activate associated kontrak
    if ($stage === 'sampai' && !empty($di['po_kontrak_nomor'])) {
            $this->db->table('kontrak')
                ->groupStart()
                    ->where('no_kontrak', $di['po_kontrak_nomor'])
                    ->orWhere('no_po_marketing', $di['po_kontrak_nomor'])
                ->groupEnd()
                ->update(['status'=>'Aktif','diperbarui_pada'=>date('Y-m-d H:i:s')]);
        }

        $stageNames = [
            'perencanaan' => 'Perencanaan Pengiriman',
            'berangkat' => 'Berangkat',
            'sampai' => 'Sampai'
        ];

        return $this->response->setJSON([
            'success'=>true,
            'message'=>'Approval ' . $stageNames[$stage] . ' berhasil disimpan',
            'csrf_hash'=>csrf_hash()
        ]);
    }

    public function diPrint($id)
    {
        $id = (int)$id;
        $di = $this->diModel->find($id);
        if ($di && !is_array($di)) { $di = (array)$di; }
        if (!$di) {
            return $this->response->setStatusCode(404)->setBody('Delivery Instruction tidak ditemukan');
        }

        // Get items untuk DI ini
        $items = $this->db->table('delivery_items di')
            ->select('di.*, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit, 
                      a.tipe as att_tipe, a.merk as att_merk, a.model as att_model')
            ->join('inventory_unit iu','iu.id_inventory_unit = di.unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('attachment a', 'a.id_attachment = di.attachment_id', 'left')
            ->where('di.di_id', $id)
            ->get()->getResultArray();

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

        // Get items untuk DI ini
        $items = $this->db->table('delivery_items di')
            ->select('di.*, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit, 
                      a.tipe as att_tipe, a.merk as att_merk, a.model as att_model')
            ->join('inventory_unit iu','iu.id_inventory_unit = di.unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('attachment a', 'a.id_attachment = di.attachment_id', 'left')
            ->where('di.di_id', $id)
            ->get()->getResultArray();

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
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $item['attachment_id'])
                ->get()->getRowArray();

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
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $spk['fabrikasi_attachment_id'])
                ->get()->getRowArray();

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

    public function trackingSearch()
    {
        // Allow both AJAX and regular POST requests
        if (!$this->request->isAJAX() && !$this->request->is('post')) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            // Handle different input methods
            $input = null;
            $contentType = $this->request->getHeaderLine('Content-Type');
            
            if (strpos($contentType, 'application/json') !== false) {
                $rawInput = $this->request->getBody();
                log_message('info', 'Raw JSON input: ' . $rawInput);
                $input = json_decode($rawInput, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    log_message('error', 'JSON decode error: ' . json_last_error_msg());
                    return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid JSON data']);
                }
            } else {
                $input = $this->request->getPost();
            }

            if (!$input) {
                log_message('error', 'No input data received');
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid input data']);
            }

            $searchType = $input['search_type'] ?? '';
            $searchValue = trim($input['search_value'] ?? '');

            log_message('info', 'Search request - Type: ' . $searchType . ', Value: ' . $searchValue);

            if (empty($searchValue)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nomor pencarian wajib diisi']);
            }

            $result = null;

            switch ($searchType) {
                case 'kontrak':
                    $result = $this->searchByKontrak($searchValue);
                    break;
                case 'spk':
                    $result = $this->searchBySpk($searchValue);
                    break;
                case 'di':
                    $result = $this->searchByDi($searchValue);
                    break;
                default:
                    return $this->response->setJSON(['success' => false, 'message' => 'Tipe pencarian tidak valid']);
            }

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Tracking search error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    private function searchByKontrak($kontrakNo)
    {
        try {
            // Search SPK by kontrak number
            $spk = $this->db->table('spk')
                ->where('po_kontrak_nomor', $kontrakNo)
                ->orWhere('nomor_spk LIKE', '%' . $kontrakNo . '%')
                ->get()->getRowArray();

            if (!$spk) {
                log_message('info', 'SPK not found for kontrak: ' . $kontrakNo);
                return null;
            }

            log_message('info', 'SPK found for kontrak: ' . json_encode($spk));

            // Get related DI
            $di = $this->db->table('delivery_instructions')
                ->where('spk_id', $spk['id'])
                ->orWhere('po_kontrak_nomor', $kontrakNo)
                ->get()->getRowArray();

            if ($di) {
                log_message('info', 'DI found for kontrak: ' . json_encode($di));
            }

            return [
                'po_kontrak_nomor' => $spk['po_kontrak_nomor'] ?? $kontrakNo,
                'spk' => $spk,
                'di' => $di
            ];

        } catch (\Exception $e) {
            log_message('error', 'searchByKontrak error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function searchBySpk($spkNo)
    {
        try {
            // Simple SPK search first to avoid complex join issues
            $spk = $this->db->table('spk')
                ->where('nomor_spk', $spkNo)
                ->orWhere('nomor_spk LIKE', '%' . $spkNo . '%')
                ->get()->getRowArray();

            if (!$spk) {
                log_message('info', 'SPK not found: ' . $spkNo);
                return null;
            }

            log_message('info', 'SPK found: ' . json_encode($spk));

            // Get related DI 
            $di = $this->db->table('delivery_instructions')
                ->where('spk_id', $spk['id'])
                ->get()->getRowArray();

            if ($di) {
                log_message('info', 'DI found: ' . json_encode($di));
            }

            return [
                'po_kontrak_nomor' => $spk['po_kontrak_nomor'] ?? '',
                'spk' => $spk,
                'di' => $di
            ];

        } catch (\Exception $e) {
            log_message('error', 'searchBySpk error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function searchByDi($diNo)
    {
        try {
            // Simple DI search first
            $di = $this->db->table('delivery_instructions')
                ->where('nomor_di', $diNo)
                ->orWhere('nomor_di LIKE', '%' . $diNo . '%')
                ->get()->getRowArray();

            if (!$di) {
                log_message('info', 'DI not found: ' . $diNo);
                return null;
            }

            log_message('info', 'DI found: ' . json_encode($di));

            // Get related SPK
            $spk = null;
            if (!empty($di['spk_id'])) {
                $spk = $this->db->table('spk')
                    ->where('id', $di['spk_id'])
                    ->get()->getRowArray();
                
                if ($spk) {
                    log_message('info', 'Related SPK found: ' . json_encode($spk));
                }
            }

            return [
                'po_kontrak_nomor' => $di['po_kontrak_nomor'] ?? ($spk['po_kontrak_nomor'] ?? ''),
                'spk' => $spk,
                'di' => $di
            ];

        } catch (\Exception $e) {
            log_message('error', 'searchByDi error: ' . $e->getMessage());
            throw $e;
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
}
