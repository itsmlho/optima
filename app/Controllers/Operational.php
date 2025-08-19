<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DeliveryInstructionModel;
use App\Models\DeliveryItemModel;
use App\Models\KontrakModel;

class Operational extends Controller
{
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
        // Fetch DI with aggregated items (units/attachments labels)
        $rows = $this->diModel->orderBy('id','DESC')->findAll();
        if ($rows) {
            $ids = array_column($rows, 'id');
            $items = $this->diItemModel
                ->select('delivery_items.*, iu.no_unit, mu.merk_unit, mu.model_unit')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = delivery_items.unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->whereIn('delivery_items.di_id', $ids)
                ->findAll();
            $byDi = [];
            foreach ($items as $it) {
                $lab = null;
                if ($it['item_type'] === 'UNIT') {
                    $lab = trim(($it['no_unit'] ?: '-') . ' - ' . ($it['merk_unit'] ?: '') . ' ' . ($it['model_unit'] ?: ''));
                } else if ($it['item_type'] === 'ATTACHMENT') {
                    $lab = 'ATTACHMENT #' . (string)($it['attachment_id'] ?? '');
                }
                $byDi[(int)$it['di_id']][] = $lab ?: 'Item';
            }
            foreach ($rows as &$r) {
                $r['items_label'] = isset($byDi[(int)$r['id']]) ? implode(', ', $byDi[(int)$r['id']]) : '';
            }
            unset($r);
        }
        return $this->response->setJSON(['data'=>$rows, 'csrf_hash'=>csrf_hash()]);
    }

    public function diUpdateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        $status = $this->request->getPost('status');
        // Support Indonesian status values
        $allowed = ['DIAJUKAN','DIPROSES','DIKIRIM','SAMPAI','DIBATALKAN'];
        if (!in_array($status, $allowed, true)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Status tidak valid. Status yang diizinkan: ' . implode(', ', $allowed)]);
        }
        $this->diModel->update((int)$id, [
            'status'=>$status,
            'diperbarui_pada'=>date('Y-m-d H:i:s')
        ]);
        // If SAMPAI (arrived), activate kontrak associated to this DI (best-effort)
        $di = $this->diModel->find((int)$id);
        if ($di && $status === 'SAMPAI' && !empty($di['po_kontrak_nomor'])) {
            $this->db->table('kontrak')
                ->groupStart()
                    ->where('no_kontrak', $di['po_kontrak_nomor'])
                    ->orWhere('no_po_marketing', $di['po_kontrak_nomor'])
                ->groupEnd()
                ->update(['status'=>'Aktif','diperbarui_pada'=>date('Y-m-d H:i:s')]);
        }
        return $this->response->setJSON(['success'=>true,'message'=>'Status DI diperbarui','csrf_hash'=>csrf_hash()]);
    }

    public function diDetail($id)
    {
    $di = $this->diModel->find((int)$id);
        if (!$di) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'DI tidak ditemukan']);
        }
        $spk = null;
        if (!empty($di['spk_id'])) {
            $spk = $this->db->table('spk')->where('id',(int)$di['spk_id'])->get()->getRowArray();
        }
        $items = $this->diItemModel
            ->select('delivery_items.*, iu.no_unit, mu.merk_unit, mu.model_unit, a2.tipe as att_tipe, a2.merk as att_merk, a2.model as att_model')
            ->join('inventory_unit iu','iu.id_inventory_unit = delivery_items.unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('attachment a2', 'a2.id_attachment = delivery_items.attachment_id', 'left')
            ->where('delivery_items.di_id',(int)$id)
            ->findAll();
        foreach ($items as &$it) {
            if ($it['item_type'] === 'UNIT') {
                $it['label'] = trim(($it['no_unit'] ?: '-') . ' - ' . ($it['merk_unit'] ?: '') . ' ' . ($it['model_unit'] ?: ''));
            } elseif ($it['item_type'] === 'ATTACHMENT') {
                $it['label'] = trim(($it['att_tipe'] ?: 'Attachment') . ' ' . ($it['att_merk'] ?: '') . ' ' . ($it['att_model'] ?: ''));
            } else {
                $it['label'] = 'Item';
            }
        }
        unset($it);
        return $this->response->setJSON([
            'success'=>true,
            'data'=>$di,
            'spk'=>$spk,
            'items'=>$items,
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
            // Perencanaan hanya menyimpan catatan konfirmasi
            $catatanPerencanaan = $this->request->getPost('catatan_perencanaan');
            if ($catatanPerencanaan) $updateData['catatan'] = $catatanPerencanaan;

        } elseif ($stage === 'berangkat') {
            // Berangkat menyimpan semua data operasional
            $tanggalKirim = $this->request->getPost('tanggal_kirim');
            $estimasiSampai = $this->request->getPost('estimasi_sampai');
            $namaSupir = $this->request->getPost('nama_supir');
            $noHpSupir = $this->request->getPost('no_hp_supir');
            $noSimSupir = $this->request->getPost('no_sim_supir');
            $kendaraan = $this->request->getPost('kendaraan');
            $nopolKendaraan = $this->request->getPost('no_polisi_kendaraan');
            $catatanBerangkat = $this->request->getPost('catatan_berangkat');

            if ($tanggalKirim) $updateData['tanggal_kirim'] = $tanggalKirim;
            if ($estimasiSampai) $updateData['estimasi_sampai'] = $estimasiSampai;
            if ($namaSupir) $updateData['nama_supir'] = $namaSupir;
            if ($noHpSupir) $updateData['no_hp_supir'] = $noHpSupir;
            if ($noSimSupir) $updateData['no_sim_supir'] = $noSimSupir;
            if ($kendaraan) $updateData['kendaraan'] = $kendaraan;
            if ($nopolKendaraan) $updateData['no_polisi_kendaraan'] = $nopolKendaraan;
            if ($catatanBerangkat) $updateData['catatan_berangkat'] = $catatanBerangkat;

        } elseif ($stage === 'sampai') {
            $catatanSampai = $this->request->getPost('catatan_sampai');
            if ($catatanSampai) $updateData['catatan_sampai'] = $catatanSampai;
            
            // After sampai approval, update status to SAMPAI
            $updateData['status'] = 'SAMPAI';
        }

        // Log for debugging
        log_message('debug', 'Updating DI ' . $id . ' with data: ' . json_encode($updateData));
        
        // Update the DI
        try {
            $this->diModel->update((int)$id, $updateData);
        } catch (\Exception $e) {
            log_message('error', 'Failed to update DI ' . $id . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }

        // If status becomes SAMPAI, activate associated kontrak
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
        if (!$di) {
            return $this->response->setStatusCode(404)->setBody('Delivery Instruction tidak ditemukan');
        }

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

        // Load unit data from SPK approval workflow if available
        if ($spk && !empty($spk['persiapan_unit_id']) && $spk['persiapan_unit_id'] != '0') {
            $u = $this->db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.sn_mast, iu.sn_mesin, iu.sn_baterai, iu.sn_charger')
                ->select('mu.merk_unit, mu.model_unit')
                ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model, b.tipe_baterai as baterai_model, chr.tipe_charger as charger_model')
                ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                ->join('mesin m','m.id = iu.model_mesin_id','left')
                ->join('baterai b','b.id = iu.model_baterai_id','left')
                ->join('charger chr','chr.id_charger = iu.model_charger_id','left')
                ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                ->where('iu.id_inventory_unit', $spk['persiapan_unit_id'])
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
                    'sn_baterai_formatted' => !empty($u['sn_baterai']) ? ($u['baterai_model'] ?? 'Baterai') . ' (' . $u['sn_baterai'] . ')' : ($u['baterai_model'] ?? ''),
                    'sn_charger_formatted' => !empty($u['sn_charger']) ? ($u['charger_model'] ?? 'Charger') . ' (' . $u['sn_charger'] . ')' : ($u['charger_model'] ?? ''),
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

        // Load attachment data from SPK approval workflow if available
        if ($spk && !empty($spk['fabrikasi_attachment_id'])) {
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

        return view('operational/print_di', [
            'di' => $di,
            'spk' => $spk ?: [],
            'spesifikasi' => $enriched
        ]);
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
}
