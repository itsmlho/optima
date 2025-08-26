<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface; 
use App\Models\InventoryUnitModel;
use App\Models\InventoryAttachmentModel;
use App\Models\SpkModel;


class Service extends BaseController
{
    protected $db;
    protected $unitModel;
    protected $attModel;
    protected $spkModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->unitModel = new InventoryUnitModel();
        $this->attModel = new InventoryAttachmentModel();
        $this->spkModel = new SpkModel();
    }
    public function index()
    {
        $data = [
            'title' => 'Service Division | OPTIMA',
            'page_title' => 'Service Division Dashboard',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service' => 'Service Division'
            ],
            'service_stats' => $this->getServiceStats(),
            'recent_work_orders' => $this->getRecentWorkOrders(),
            'maintenance_alerts' => $this->getMaintenanceAlerts(),
            'technicians' => $this->getTechnicians()
        ];

        return view('service/index', $data);
    }

    public function workOrders()
    {
        $data = [
            'title' => 'Work Orders | OPTIMA',
            'page_title' => 'Work Orders',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service' => 'Service',
                '/service/work-orders' => 'Work Orders'
            ],
            'workorders' => $this->getWorkOrders(),
            'mode' => 'active',
            'active_statuses' => ['OPEN', 'KENDALA', 'PENDING'],
        ];

        return view('service/work_orders', $data);
    }

    public function workOrderHistory()
    {
        $data = [
            'title' => 'Work Order History | OPTIMA',
            'page_title' => 'Work Order History',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service' => 'Service',
                '/service/work-orders' => 'Work Orders',
                '/service/work-orders/history' => 'History'
            ],
            'work_order_history' => $this->getWorkOrderHistory(),
            'mode' => 'history',
            'history_statuses' => ['CLOSED'],
        ];

        return view('service/work_order_history', $data);
    }

    public function pmps()
    {
        $data = [
            'title' => 'PMPS | OPTIMA',
            'page_title' => 'Preventive Maintenance Planned Service',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service' => 'Service',
                '/service/pmps' => 'PMPS'
            ],
            'pmps_data' => $this->getPmpsData(),
        ];

        return view('service/pmps', $data);
    }

    // --- SPK Service Handlers ---
    public function spkService()
    {
        $data = [
            'title' => 'SPK Service | OPTIMA',
            'page_title' => 'SPK dari Marketing',
        ];
        return view('service/spk_service', $data);
    }

    public function spkList()
    {
        $list = $this->db->table('spk')->orderBy('id','DESC')->get()->getResultArray();
        return $this->response->setJSON(['data'=>$list,'csrf_hash'=>csrf_hash()]);
    }

    public function spkDetail($id)
    {
        $row = $this->db->table('spk')->where('id', (int)$id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
        }
        $spec = [];
        if (!empty($row['spesifikasi'])) {
            $decoded = json_decode($row['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $spec = $decoded;
            }
        }
        // Enrich names for ID-based fields (best-effort)
        $enriched = $spec;
        $mapQueries = [
            'departemen_id' => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen'],
            'kapasitas_id'  => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit'],
            'mast_id'       => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast'],
            'ban_id'        => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban'],
            'valve_id'      => ['table'=>'valve','id'=>'id_valve','name'=>'jumlah_valve'],
            'roda_id'       => ['table'=>'jenis_roda','id'=>'id_roda','name'=>'tipe_roda'],
        ];
        foreach ($mapQueries as $key => $cfg) {
            if (!empty($spec[$key])) {
                $val = $spec[$key];
                $rec = $this->db->table($cfg['table'])->select($cfg['name'].' as name', false)->where($cfg['id'], $val)->get()->getRowArray();
                if ($rec && isset($rec['name'])) {
                    $enriched[$key.'_name'] = $rec['name'];
                }
            }
        }
        // Enrich selected items (unit & attachment) with full details
        // First, check if data comes from approval workflow
        if (!empty($row['persiapan_unit_id'])) {
            // Prioritaskan cari dengan id_inventory_unit
            $u = $this->db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id, iu.model_mast_id, iu.roda_id, iu.ban_id, iu.valve_id')
                ->select('iu.sn_mast, iu.sn_mesin, iu.sn_baterai, iu.sn_charger')
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
                ->where('iu.id_inventory_unit', $row['persiapan_unit_id'])
                ->get()->getRowArray();
            // Fallback ke no_unit jika tidak ditemukan
            if (!$u) {
                $u = $this->db->table('inventory_unit iu')
                    ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id, iu.model_mast_id, iu.roda_id, iu.ban_id, iu.valve_id')
                    ->select('iu.sn_mast, iu.sn_mesin, iu.sn_baterai, iu.sn_charger')
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
                    ->where('iu.no_unit', $row['persiapan_unit_id'])
                    ->get()->getRowArray();
            }
            if ($u) {
                $label = trim(($u['no_unit'] ?: '-') . ' - ' . ($u['merk_unit'] ?: '-') . ' ' . ($u['model_unit'] ?: '') . ' @ ' . ($u['lokasi_unit'] ?: '-'));
                // Ambil data valve, mast, roda, ban
                $valve = $this->db->table('valve')->select('jumlah_valve')->where('id_valve', $u['valve_id'] ?? null)->get()->getRowArray();
                $mast = $this->db->table('tipe_mast')->select('tipe_mast')->where('id_mast', $u['model_mast_id'] ?? null)->get()->getRowArray();
                $roda = $this->db->table('jenis_roda')->select('tipe_roda')->where('id_roda', $u['roda_id'] ?? null)->get()->getRowArray();
                $ban = $this->db->table('tipe_ban')->select('tipe_ban')->where('id_ban', $u['ban_id'] ?? null)->get()->getRowArray();
                $enriched['selected']['unit'] = [
                    'id' => (int)$u['id_inventory_unit'],
                    'label' => $label,
                    'no_unit' => $u['no_unit'] ?? null,
                    'serial_number' => $u['serial_number'] ?? null,
                    'tahun_unit' => $u['tahun_unit'] ?? null,
                    'merk_unit' => $u['merk_unit'] ?? null,
                    'model_unit' => $u['model_unit'] ?? null,
                    'tipe_jenis' => $u['tipe_jenis'] ?? null,
                    'jenis_unit' => $u['jenis_unit'] ?? null,
                    'lokasi_unit' => $u['lokasi_unit'] ?? null,
                    'kapasitas_name' => $u['kapasitas_name'] ?? null,
                    'departemen_name' => $u['departemen_name'] ?? null,
                    'valve' => $valve['jumlah_valve'] ?? '',
                    'mast' => $mast['tipe_mast'] ?? '',
                    'roda' => $roda['tipe_roda'] ?? '',
                    'ban' => $ban['tipe_ban'] ?? '',
                    // Format: Model (SN) atau hanya Model jika SN kosong
                    'sn_mast' => $u['sn_mast'] ?? null,
                    'sn_mesin' => $u['sn_mesin'] ?? null, 
                    'sn_baterai' => $u['sn_baterai'] ?? null,
                    'sn_charger' => $u['sn_charger'] ?? null,
                    'sn_mast_formatted' => !empty($u['sn_mast']) ? ($u['mast_model'] ?? 'Mast') . ' (' . $u['sn_mast'] . ')' : ($u['mast_model'] ?? ''),
                    'sn_mesin_formatted' => !empty($u['sn_mesin']) ? ($u['mesin_model'] ?? 'Mesin') . ' (' . $u['sn_mesin'] . ')' : ($u['mesin_model'] ?? ''),
                    'sn_baterai_formatted' => !empty($u['sn_baterai']) ? ($u['baterai_model'] ?? 'Baterai') . ' (' . $u['sn_baterai'] . ')' : ($u['baterai_model'] ?? ''),
                    'sn_charger_formatted' => !empty($u['sn_charger']) ? ($u['charger_model'] ?? 'Charger') . ' (' . $u['sn_charger'] . ')' : ($u['charger_model'] ?? ''),
                ];
            }
        }
        
        if (!empty($row['fabrikasi_attachment_id'])) {
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $row['fabrikasi_attachment_id'])
                ->get()->getRowArray();
                
            if ($a) {
                $label = trim(($a['tipe'] ?: '-') . ' ' . ($a['merk'] ?: '') . ' ' . ($a['model'] ?: ''));
                $suffix = [];
                if (!empty($a['sn_attachment'])) $suffix[] = 'SN: '.$a['sn_attachment'];
                if (!empty($a['lokasi_penyimpanan'])) $suffix[] = '@ '.$a['lokasi_penyimpanan'];
                if ($suffix) $label .= ' ['.implode(', ', $suffix).']';
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'label' => $label,
                    'tipe' => $a['tipe'] ?? null,
                    'merk' => $a['merk'] ?? null,
                    'model' => $a['model'] ?? null,
                    'sn_attachment' => $a['sn_attachment'] ?? null,
                    'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? ($a['model'] ?? 'Attachment') . ' (' . $a['sn_attachment'] . ')' : ($a['model'] ?? ''),
                ];
            }
        }
        
    // Legacy: Load from spesifikasi selected if no approval workflow data
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            if (empty($enriched['selected']['unit']) && !empty($sel['unit_id'])) {
                $u = $this->db->table('inventory_unit iu')
                    ->select('iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.sn_mast, iu.sn_mesin, iu.sn_baterai, iu.sn_charger, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                    ->where('iu.id_inventory_unit', (int)$sel['unit_id'])
                    ->get()->getRowArray();
                if ($u) {
                    $label = trim(($u['no_unit'] ?: '-') . ' - ' . ($u['merk_unit'] ?: '-') . ' ' . ($u['model_unit'] ?: '') . ' @ ' . ($u['lokasi_unit'] ?: '-'));
                    $enriched['selected']['unit'] = [
                        'id' => (int)$sel['unit_id'],
                        'label' => $label,
                        'no_unit' => $u['no_unit'] ?? null,
                        'serial_number' => $u['serial_number'] ?? null,
                        'tahun_unit' => $u['tahun_unit'] ?? null,
                        'merk_unit' => $u['merk_unit'] ?? null,
                        'model_unit' => $u['model_unit'] ?? null,
                        'lokasi_unit' => $u['lokasi_unit'] ?? null,
                        'sn_mast' => $u['sn_mast'] ?? null,
                        'sn_mesin' => $u['sn_mesin'] ?? null,
                        'sn_baterai' => $u['sn_baterai'] ?? null,
                        'sn_charger' => $u['sn_charger'] ?? null,
                    ];
                }
            }
            if (empty($enriched['selected']['attachment']) && !empty($sel['inventory_attachment_id'])) {
                $a = $this->db->table('inventory_attachment ia')
                    ->select('a.tipe, a.merk, a.model, ia.sn_attachment, ia.lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = ia.attachment_id','left')
                    ->where('ia.id_inventory_attachment', (int)$sel['inventory_attachment_id'])
                    ->get()->getRowArray();
                if ($a) {
                    $label = trim(($a['tipe'] ?: '-') . ' ' . ($a['merk'] ?: '') . ' ' . ($a['model'] ?: ''));
                    $suffix = [];
                    if (!empty($a['sn_attachment'])) $suffix[] = 'SN: '.$a['sn_attachment'];
                    if (!empty($a['lokasi_penyimpanan'])) $suffix[] = '@ '.$a['lokasi_penyimpanan'];
                    if ($suffix) $label .= ' ['.implode(', ', $suffix).']';
                    $enriched['selected']['attachment'] = [
                        'id' => (int)$sel['inventory_attachment_id'],
                        'label' => $label,
                        'tipe' => $a['tipe'] ?? null,
                        'merk' => $a['merk'] ?? null,
                        'model' => $a['model'] ?? null,
                        'sn_attachment' => $a['sn_attachment'] ?? null,
                        'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    ];
                }
            }
        }
    // Also load kontrak_spesifikasi record (parity with Marketing controller)
        $kontrak_spec = null;
        if (!empty($row['kontrak_spesifikasi_id'])) {
            $kontrak_spec = $this->db->table('kontrak_spesifikasi')
                ->where('id', $row['kontrak_spesifikasi_id'])
                ->get()
                ->getRowArray();

            // Decode aksesoris JSON if stored as string
            if ($kontrak_spec && isset($kontrak_spec['aksesoris']) && is_string($kontrak_spec['aksesoris'])) {
                try {
                    $decoded_aks = json_decode($kontrak_spec['aksesoris'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $kontrak_spec['aksesoris'] = $decoded_aks;
                    }
                } catch (\Exception $e) {
                    // keep original string on failure
                }
            }
        }

        // Attach prepared_units progress if any
        $preparedUnits = [];
        if (!empty($enriched['prepared_units']) && is_array($enriched['prepared_units'])) {
            $preparedUnits = $enriched['prepared_units'];
        } elseif (!empty($spec['prepared_units']) && is_array($spec['prepared_units'])) {
            $preparedUnits = $spec['prepared_units'];
        }

        // Enrich prepared_units into prepared_units_detail for distinct display in Service detail
        if (!empty($preparedUnits)) {
            $preparedDetails = [];
            foreach ($preparedUnits as $pu) {
                $uInfo = null; $aInfo = null; $unitLabel=''; $attLabel='';
                if (!empty($pu['unit_id'])) {
                    $uInfo = $this->db->table('inventory_unit iu')
                        ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.lokasi_unit, mu.merk_unit, mu.model_unit, tu.tipe as tipe_jenis')
                        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                        ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                        ->where('iu.id_inventory_unit', $pu['unit_id'])
                        ->get()->getRowArray();
                    if ($uInfo) {
                        $unitLabel = trim(($uInfo['no_unit'] ?: '-') . ' - ' . ($uInfo['merk_unit'] ?: '-') . ' ' . ($uInfo['model_unit'] ?: '') . ' @ ' . ($uInfo['lokasi_unit'] ?: '-'));
                    }
                }
                if (!empty($pu['attachment_id'])) {
                    $aInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan, att.tipe, att.merk, att.model')
                        ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                        ->where('ia.id_inventory_attachment', $pu['attachment_id'])
                        ->get()->getRowArray();
                    if ($aInfo) {
                        $attLabel = trim(($aInfo['tipe'] ?: '-') . ' ' . ($aInfo['merk'] ?: '') . ' ' . ($aInfo['model'] ?: ''));
                        $suf = [];
                        if (!empty($aInfo['sn_attachment'])) $suf[] = 'SN: '.$aInfo['sn_attachment'];
                        if (!empty($aInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$aInfo['lokasi_penyimpanan'];
                        if ($suf) $attLabel .= ' ['.implode(', ', $suf).']';
                    }
                }
                $preparedDetails[] = [
                    'unit_id' => $pu['unit_id'] ?? null,
                    'unit_label' => $unitLabel,
                    'no_unit' => $uInfo['no_unit'] ?? '',
                    'serial_number' => $uInfo['serial_number'] ?? '',
                    'merk_unit' => $uInfo['merk_unit'] ?? '',
                    'model_unit' => $uInfo['model_unit'] ?? '',
                    'tipe_jenis' => $uInfo['tipe_jenis'] ?? '',
                    'attachment_id' => $pu['attachment_id'] ?? null,
                    'attachment_label' => $attLabel,
                    'mekanik' => $pu['mekanik'] ?? '',
                    'aksesoris_tersedia' => $pu['aksesoris_tersedia'] ?? '',
                    'catatan' => $pu['catatan'] ?? '',
                    'timestamp' => $pu['timestamp'] ?? ''
                ];
            }
            $enriched['prepared_units_detail'] = $preparedDetails;
        }

        return $this->response->setJSON([
            'success'=>true,
            'data'=>$row,
            'spesifikasi'=>$enriched,
            'kontrak_spec'=>$kontrak_spec,
            'prepared_units'=>$preparedUnits,
            'csrf_hash'=>csrf_hash()
        ]);
    }

    public function spkConfirmReady($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        $this->db->table('spk')->where('id',$id)->update(['status'=>'READY','diperbarui_pada'=>date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success'=>true,'message'=>'Unit siap','csrf_hash'=>csrf_hash()]);
    }

    public function spkUpdateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $status = $this->request->getPost('status');
        if (!$status) {
            return $this->response->setJSON(['success'=>false,'message'=>'Status tidak boleh kosong']);
        }

        // Validate allowed status transitions
        $allowedStatus = ['SUBMITTED', 'IN_PROGRESS', 'READY', 'DELIVERED', 'CANCELLED'];
        if (!in_array($status, $allowedStatus)) {
            return $this->response->setJSON(['success'=>false,'message'=>'Status tidak valid']);
        }

        $this->db->table('spk')->where('id', $id)->update([
            'status' => $status,
            'diperbarui_pada' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success'=>true,'message'=>'Status SPK berhasil diperbarui','csrf_hash'=>csrf_hash()]);
    }

    public function spkApproveStage($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $stage = $this->request->getPost('stage');
        $mekanik = $this->request->getPost('mekanik');
        $estimasi_mulai = $this->request->getPost('estimasi_mulai');
        $estimasi_selesai = $this->request->getPost('estimasi_selesai');

        if (!$stage || !$mekanik || !$estimasi_mulai || !$estimasi_selesai) {
            return $this->response->setJSON(['success'=>false,'message'=>'Semua field harus diisi']);
        }

        // Validate allowed stages
        $allowedStages = ['persiapan_unit', 'fabrikasi', 'painting', 'pdi'];
        if (!in_array($stage, $allowedStages)) {
            return $this->response->setJSON(['success'=>false,'message'=>'Stage tidak valid']);
        }

        $updateData = [
            $stage . '_mekanik' => $mekanik,
            $stage . '_estimasi_mulai' => $estimasi_mulai,
            $stage . '_estimasi_selesai' => $estimasi_selesai,
            $stage . '_tanggal_approve' => date('Y-m-d H:i:s'),
            'diperbarui_pada' => date('Y-m-d H:i:s')
        ];

        // Handle stage-specific data
        if ($stage === 'persiapan_unit') {
            $unit_id = $this->request->getPost('unit_id');
            $aksesoris_tersedia = $this->request->getPost('aksesoris_tersedia'); // JSON array of checked accessories
            $update_no_unit = $this->request->getPost('update_no_unit');
            $no_unit_action = $this->request->getPost('no_unit_action');
            $battery_inventory_id = $this->request->getPost('battery_inventory_id');
            $charger_inventory_id = $this->request->getPost('charger_inventory_id');

            if (!$unit_id) {
                return $this->response->setJSON(['success'=>false,'message'=>'Unit harus dipilih']);
            }

            // Get unit with status and department information
            $unit = $this->db->table('inventory_unit')
                ->select('no_unit, status_unit_id, departemen_id')
                ->where('id_inventory_unit', $unit_id)
                ->get()->getRowArray();
            if (!$unit) {
                return $this->response->setJSON(['success'=>false,'message'=>'Unit tidak ditemukan']);
            }

            // Check Electric department requirement (id=2)
            if ($unit['departemen_id'] == 2) {
                if (!$battery_inventory_id || !$charger_inventory_id) {
                    return $this->response->setJSON(['success'=>false,'message'=>'Unit Electric memerlukan Battery dan Charger']);
                }
                
                // Validate battery and charger are available
                $batteryAvailable = $this->db->table('inventory_attachment')
                    ->where('id_inventory_attachment', $battery_inventory_id)
                    ->where('status_unit', 7)
                    ->where('id_inventory_unit', null)
                    ->where('baterai_id IS NOT NULL', null, false)
                    ->countAllResults();
                    
                $chargerAvailable = $this->db->table('inventory_attachment')
                    ->where('id_inventory_attachment', $charger_inventory_id)
                    ->where('status_unit', 7)
                    ->where('id_inventory_unit', null)
                    ->where('charger_id IS NOT NULL', null, false)
                    ->countAllResults();
                    
                if (!$batteryAvailable) {
                    return $this->response->setJSON(['success'=>false,'message'=>'Battery yang dipilih tidak tersedia']);
                }
                if (!$chargerAvailable) {
                    return $this->response->setJSON(['success'=>false,'message'=>'Charger yang dipilih tidak tersedia']);
                }
                
                // Store Electric department selections
                $updateData['persiapan_battery_id'] = $battery_inventory_id;
                $updateData['persiapan_charger_id'] = $charger_inventory_id;
            }

            // Hanya Non Aset (status 8) yang boleh generate/update no_unit
            if ($unit['status_unit_id'] == 8) {
                // Proses generate/update no_unit jika diminta
                if ($update_no_unit === 'true') {
                    if (empty($unit['no_unit']) || $unit['no_unit'] == 0) {
                        if ($no_unit_action === 'AUTO_GENERATE') {
                            // Generate new no_unit
                            $newNoUnit = $this->generateNoUnit();
                        } else if (!empty($no_unit_action) && $no_unit_action !== 'AUTO_GENERATE') {
                            // Use manual input (should be integer)
                            $newNoUnit = intval($no_unit_action);
                            // Validate it's a positive integer
                            if ($newNoUnit < 1) {
                                return $this->response->setJSON(['success'=>false,'message'=>'No Unit harus berupa angka positif']);
                            }
                            // Validate uniqueness
                            $existing = $this->db->table('inventory_unit')
                                ->where('no_unit', $newNoUnit)
                                ->where('id_inventory_unit !=', $unit_id)
                                ->countAllResults();
                            if ($existing > 0) {
                                return $this->response->setJSON(['success'=>false,'message'=>"No Unit '$newNoUnit' sudah digunakan. Silakan gunakan nomor lain."]);
                            }
                        }
                        if (isset($newNoUnit)) {
                            // Update the unit with new no_unit
                            $this->db->table('inventory_unit')
                                ->where('id_inventory_unit', $unit_id)
                                ->update([
                                    'no_unit' => $newNoUnit,
                                    'status_unit_id' => 3 // RENTAL status
                                ]);
                            $unit['no_unit'] = $newNoUnit;
                        }
                    }
                }
                // Store id_inventory_unit untuk Non Aset
                $updateData['persiapan_unit_id'] = $unit_id;
            } else {
                // Untuk status 7 (Aset), tidak perlu generate/update no_unit
                $updateData['persiapan_unit_id'] = $unit_id;
            }
            $updateData['persiapan_aksesoris_tersedia'] = $aksesoris_tersedia;
        }
        elseif ($stage === 'fabrikasi') {
            $attachment_id = $this->request->getPost('attachment_id');
            if ($attachment_id) {
                $updateData['fabrikasi_attachment_id'] = $attachment_id;
            }

        } elseif ($stage === 'pdi') {
            $catatan = $this->request->getPost('catatan');
            if (!$catatan) {
                return $this->response->setJSON(['success'=>false,'message'=>'Catatan PDI harus diisi']);
            }

            // Load current SPK to accumulate prepared units for multi-unit SPK
            $current = $this->db->table('spk')->where('id', $id)->get()->getRowArray();
            $spec = [];
            if (!empty($current['spesifikasi'])) {
                $dec = json_decode($current['spesifikasi'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($dec)) $spec = $dec;
            }
            $prepared = isset($spec['prepared_units']) && is_array($spec['prepared_units']) ? $spec['prepared_units'] : [];

            // Append current cycle result
            $prepared[] = [
                'unit_id' => $current['persiapan_unit_id'] ?? null,
                'attachment_id' => $current['fabrikasi_attachment_id'] ?? null,
                'aksesoris_tersedia' => $current['persiapan_aksesoris_tersedia'] ?? null,
                'mekanik' => $mekanik,
                'catatan' => $catatan,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            $spec['prepared_units'] = $prepared;

            // Determine status: READY only when all units prepared
            $totalUnits = (int)($current['jumlah_unit'] ?? 1);
            $isComplete = count($prepared) >= max(1, $totalUnits);

            // Save updated spec JSON
            $updateData['spesifikasi'] = json_encode($spec);
            $updateData['pdi_catatan'] = $catatan;
            $updateData['status'] = $isComplete ? 'READY' : 'IN_PROGRESS';

            if (!$isComplete) {
                // Reset stage fields to allow next unit cycle
                $updateData = array_merge($updateData, [
                    'persiapan_unit_id' => null,
                    'persiapan_unit_mekanik' => null,
                    'persiapan_unit_estimasi_mulai' => null,
                    'persiapan_unit_estimasi_selesai' => null,
                    'persiapan_unit_tanggal_approve' => null,
                    'persiapan_aksesoris_tersedia' => null,
                    'fabrikasi_attachment_id' => null,
                    'fabrikasi_mekanik' => null,
                    'fabrikasi_estimasi_mulai' => null,
                    'fabrikasi_estimasi_selesai' => null,
                    'fabrikasi_tanggal_approve' => null,
                    'painting_mekanik' => null,
                    'painting_estimasi_mulai' => null,
                    'painting_estimasi_selesai' => null,
                    'painting_tanggal_approve' => null,
                    'pdi_mekanik' => null,
                    'pdi_estimasi_mulai' => null,
                    'pdi_estimasi_selesai' => null,
                    'pdi_tanggal_approve' => null
                ]);
            }
        }

        $this->db->table('spk')->where('id', $id)->update($updateData);

        $message = ($stage === 'pdi') ? 'PDI selesai, SPK siap untuk delivery' : 'Approval berhasil disimpan';
        return $this->response->setJSON(['success'=>true,'message'=>$message,'csrf_hash'=>csrf_hash()]);
    }

    private function generateNoUnit()
    {
        // Generate no_unit with integer format (highest + 1)
        // Only consider Aset units (status 3=RENTAL, 7=STOCK ASET)
        
        // Find the highest existing no_unit from Aset units only
        $query = $this->db->table('inventory_unit')
            ->select('no_unit')
            ->where('no_unit IS NOT NULL')
            ->where('no_unit >', 0)
            ->whereIn('status_unit_id', [3, 7]) // Only Aset units
            ->orderBy('no_unit', 'DESC')
            ->limit(1)
            ->get();
            
        $lastNoUnit = $query->getRowArray();
        
        if ($lastNoUnit && $lastNoUnit['no_unit']) {
            // Increment the highest number
            $newNumber = intval($lastNoUnit['no_unit']) + 1;
        } else {
            // Start from 1 if no existing numbers
            $newNumber = 1;
        }
        
        return $newNumber;
    }

    // Print HTML (tanpa Dompdf)
    public function spkPrint($id)
    {
        $id = (int)$id;
        $row = $this->spkModel->find($id);
        if (!$row) {
            return $this->response->setStatusCode(404)->setBody('SPK tidak ditemukan');
        }
        // reuse enrichment from spkPdf/spkDetail
        $spec = [];
        if (!empty($row['spesifikasi'])) {
            $dec = json_decode($row['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($dec)) $spec = $dec;
        }
        $enriched = $spec;
        $mapQueries = [
            'departemen_id' => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen'],
            'kapasitas_id'  => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit'],
            'mast_id'       => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast'],
            'ban_id'        => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban'],
            'valve_id'      => ['table'=>'valve','id'=>'id_valve','name'=>'jumlah_valve'],
            'roda_id'       => ['table'=>'jenis_roda','id'=>'id_roda','name'=>'tipe_roda'],
        ];
        foreach ($mapQueries as $key => $cfg) {
            if (!empty($spec[$key])) {
                $rec = $this->db->table($cfg['table'])->select($cfg['name'].' as name', false)->where($cfg['id'], $spec[$key])->get()->getRowArray();
                if ($rec && isset($rec['name'])) $enriched[$key.'_name'] = $rec['name'];
            }
        }
        
    // Load unit data from approval workflow if available
        // Handle both Aset (stored as no_unit) and Non Aset (stored as id_inventory_unit)
        if (!empty($row['persiapan_unit_id']) && $row['persiapan_unit_id'] != '0') {
            // Prioritaskan cari dengan id_inventory_unit
            $u = $this->db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id, iu.model_mast_id, iu.roda_id, iu.ban_id, iu.valve_id')
                ->select('iu.sn_mast, iu.sn_mesin, iu.sn_baterai, iu.sn_charger')
                ->select('mu.merk_unit, mu.model_unit')
                ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model, b.tipe_baterai as baterai_model, chr.tipe_charger as charger_model')
                ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name, su.status_unit')
                ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                ->join('mesin m','m.id = iu.model_mesin_id','left')
                ->join('baterai b','b.id = iu.model_baterai_id','left')
                ->join('charger chr','chr.id_charger = iu.model_charger_id','left')
                ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                ->join('status_unit su','su.id_status = iu.status_unit_id','left')
                ->where('iu.id_inventory_unit', $row['persiapan_unit_id'])
                ->get()->getRowArray();
            // Fallback ke no_unit jika tidak ditemukan
            if (!$u) {
                $u = $this->db->table('inventory_unit iu')
                    ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id, iu.model_mast_id, iu.roda_id, iu.ban_id, iu.valve_id')
                    ->select('iu.sn_mast, iu.sn_mesin, iu.sn_baterai, iu.sn_charger')
                    ->select('mu.merk_unit, mu.model_unit')
                    ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                    ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model, b.tipe_baterai as baterai_model, chr.tipe_charger as charger_model')
                    ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name, su.status_unit')
                    ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                    ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                    ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                    ->join('mesin m','m.id = iu.model_mesin_id','left')
                    ->join('baterai b','b.id = iu.model_baterai_id','left')
                    ->join('charger chr','chr.id_charger = iu.model_charger_id','left')
                    ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                    ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                    ->join('status_unit su','su.id_status = iu.status_unit_id','left')
                    ->where('iu.no_unit', $row['persiapan_unit_id'])
                    ->get()->getRowArray();
            }
            if ($u) {
                    $enriched['selected']['unit'] = [
                        'id' => (int)$u['id_inventory_unit'],
                        'no_unit' => $u['no_unit'] ?? null,
                        'serial_number' => $u['serial_number'] ?? null,
                        'merk_unit' => $u['merk_unit'] ?? null,
                        'model_unit' => $u['model_unit'] ?? null,
                        'tipe_jenis' => $u['tipe_jenis'] ?? null,
                        'jenis_unit' => $u['jenis_unit'] ?? null,
                        'tahun_unit' => $u['tahun_unit'] ?? null,
                        'lokasi_unit' => $u['lokasi_unit'] ?? null,
                        'kapasitas_name' => $u['kapasitas_name'] ?? null,
                        'departemen_name' => $u['departemen_name'] ?? null,
                        'status_unit' => $u['status_unit'] ?? null,
                        'status_unit_id' => $u['status_unit_id'] ?? null,
                        // Format: Model (SN) atau hanya Model jika SN kosong
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
                
                // Override spesifikasi with unit data  
                $enriched['tipe_jenis'] = $u['tipe_jenis'] ?? $enriched['tipe_jenis'] ?? '';
                $enriched['merk_unit'] = $u['merk_unit'] ?? $enriched['merk_unit'] ?? '';
                $enriched['model_unit'] = $u['model_unit'] ?? $enriched['model_unit'] ?? '';
                $enriched['kapasitas_id_name'] = $u['kapasitas_name'] ?? $enriched['kapasitas_id_name'] ?? '';
                $enriched['departemen_id_name'] = $u['departemen_name'] ?? $enriched['departemen_id_name'] ?? '';
            }
        }
        
        // Enrich prepared_units list for multi-unit SPK
        if (!empty($spec['prepared_units']) && is_array($spec['prepared_units'])) {
            $preparedDetails = [];
            foreach ($spec['prepared_units'] as $idx => $pu) {
                $uInfo = null; $aInfo = null; $unitLabel=''; $attLabel='';
                if (!empty($pu['unit_id'])) {
                    $uInfo = $this->db->table('inventory_unit iu')
                        ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.lokasi_unit, mu.merk_unit, mu.model_unit, tu.tipe as tipe_jenis')
                        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                        ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                        ->where('iu.id_inventory_unit', $pu['unit_id'])
                        ->get()->getRowArray();
                    if ($uInfo) {
                        $unitLabel = trim(($uInfo['no_unit'] ?: '-') . ' - ' . ($uInfo['merk_unit'] ?: '-') . ' ' . ($uInfo['model_unit'] ?: '') . ' @ ' . ($uInfo['lokasi_unit'] ?: '-'));
                    }
                }
                if (!empty($pu['attachment_id'])) {
                    $aInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan, att.tipe, att.merk, att.model')
                        ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                        ->where('ia.id_inventory_attachment', $pu['attachment_id'])
                        ->get()->getRowArray();
                    if ($aInfo) {
                        $attLabel = trim(($aInfo['tipe'] ?: '-') . ' ' . ($aInfo['merk'] ?: '') . ' ' . ($aInfo['model'] ?: ''));
                        $suf = [];
                        if (!empty($aInfo['sn_attachment'])) $suf[] = 'SN: '.$aInfo['sn_attachment'];
                        if (!empty($aInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$aInfo['lokasi_penyimpanan'];
                        if ($suf) $attLabel .= ' ['.implode(', ', $suf).']';
                    }
                }
                $preparedDetails[] = [
                    'unit_id' => $pu['unit_id'] ?? null,
                    'unit_label' => $unitLabel,
                    'no_unit' => $uInfo['no_unit'] ?? '',
                    'serial_number' => $uInfo['serial_number'] ?? '',
                    'merk_unit' => $uInfo['merk_unit'] ?? '',
                    'model_unit' => $uInfo['model_unit'] ?? '',
                    'tipe_jenis' => $uInfo['tipe_jenis'] ?? '',
                    'attachment_id' => $pu['attachment_id'] ?? null,
                    'attachment_label' => $attLabel,
                    'mekanik' => $pu['mekanik'] ?? '',
                    'aksesoris_tersedia' => $pu['aksesoris_tersedia'] ?? '',
                    'catatan' => $pu['catatan'] ?? '',
                    'timestamp' => $pu['timestamp'] ?? ''
                ];
            }
            $enriched['prepared_units_detail'] = $preparedDetails;
        }
        
        // Load attachment data from approval workflow if available  
        if (!empty($row['fabrikasi_attachment_id'])) {
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $row['fabrikasi_attachment_id'])
                ->get()->getRowArray();
                
            if ($a) {
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'tipe' => $a['tipe'] ?? null,
                    'merk' => $a['merk'] ?? null,
                    'model' => $a['model'] ?? null,
                    'sn_attachment' => $a['sn_attachment'] ?? null,
                    'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    // Format: Model (SN)
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? ($a['model'] ?? 'Attachment') . ' (' . $a['sn_attachment'] . ')' : ($a['model'] ?? ''),
                ];
                
                // Override spesifikasi with attachment data
                $enriched['attachment_tipe'] = $a['tipe'] ?? $enriched['attachment_tipe'] ?? '';
            }
        }
        
        // Legacy: enrich selected items from spesifikasi (fallback if no approval workflow data)
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            if (empty($enriched['selected'])) $enriched['selected'] = [];
            
            // Only load legacy unit data if no approval workflow data exists
            if (empty($enriched['selected']['unit']) && !empty($sel['unit_id'])) {
                $u = $this->unitModel
                    ->select('inventory_unit.no_unit, inventory_unit.serial_number, inventory_unit.tahun_unit, inventory_unit.lokasi_unit, inventory_unit.sn_mast, inventory_unit.sn_mesin, inventory_unit.sn_baterai, inventory_unit.sn_charger, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu','mu.id_model_unit = inventory_unit.model_unit_id','left')
                    ->where('inventory_unit.id_inventory_unit', (int)$sel['unit_id'])
                    ->first();
                if ($u) {
                    $enriched['selected']['unit'] = [
                        'id' => (int)$sel['unit_id'],
                        'no_unit' => $u['no_unit'] ?? null,
                        'serial_number' => $u['serial_number'] ?? null,
                        'tahun_unit' => $u['tahun_unit'] ?? null,
                        'merk_unit' => $u['merk_unit'] ?? null,
                        'model_unit' => $u['model_unit'] ?? null,
                        'lokasi_unit' => $u['lokasi_unit'] ?? null,
                        'sn_mast' => $u['sn_mast'] ?? null,
                        'sn_mesin' => $u['sn_mesin'] ?? null,
                        'sn_baterai' => $u['sn_baterai'] ?? null,
                        'sn_charger' => $u['sn_charger'] ?? null,
                    ];
                }
            }
            
            // Only load legacy attachment data if no approval workflow data exists
            if (empty($enriched['selected']['attachment']) && !empty($sel['inventory_attachment_id'])) {
                $a = $this->attModel
                    ->select('a.tipe, a.merk, a.model, inventory_attachment.sn_attachment, inventory_attachment.lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = inventory_attachment.attachment_id','left')
                    ->where('inventory_attachment.id_inventory_attachment', (int)$sel['inventory_attachment_id'])
                    ->first();
                if ($a) {
                    $enriched['selected']['attachment'] = [
                        'tipe' => $a['tipe'] ?? null,
                        'merk' => $a['merk'] ?? null,
                        'model' => $a['model'] ?? null,
                        'sn_attachment' => $a['sn_attachment'] ?? null,
                        'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    ];
                }
            }
        }
    return view('marketing/print_spk', ['spk'=>$row, 'spesifikasi'=>$enriched]);
    }

    /** Simple list for unit picking (Only STOCK units: status 7 & 8) */
    public function dataUnitSimple()
    {
        $q = trim((string)($this->request->getGet('q') ?? ''));
        $allowed = $this->getAllowedServiceDepartemenIds();
        if (!$allowed) { return $this->response->setJSON(['success'=>true,'data'=>[],'csrf_hash'=>csrf_hash()]); }
        
        $qb = $this->serviceBaseQuery($allowed)
            ->select('iu.id_inventory_unit as id, iu.no_unit, mu.merk_unit, mu.model_unit, iu.lokasi_unit, iu.status_unit_id')
            ->select('d.nama_departemen, iu.departemen_id')
            ->whereIn('iu.status_unit_id', [7, 8]) // Only STOCK ASET (7) and STOCK NON ASET (8)
            ->orderBy('iu.no_unit','ASC')
            ->limit(50);
        
        if ($q !== '') {
            $qb->groupStart()
                ->like('iu.no_unit', $q)
                ->orLike('iu.serial_number', $q)
                ->orLike('mu.merk_unit', $q)
                ->orLike('mu.model_unit', $q)
                ->orLike('iu.lokasi_unit', $q)
            ->groupEnd();
        }
        $rows = $qb->get()->getResultArray();
        $data = array_map(function($r){
            // Check if unit needs no_unit (only for STOCK ASET - status 7)
            $needsNoUnit = ($r['status_unit_id'] == 7 && (empty($r['no_unit']) || $r['no_unit'] == 0));
            $noUnitDisplay = $r['no_unit'] ?: ($r['status_unit_id'] == 8 ? '[Non Aset]' : '[Akan di-generate]');
            
            // Format department display for the label
            $deptDisplay = '';
            if (!empty($r['nama_departemen'])) {
                $deptDisplay = ' - (' . $r['nama_departemen'] . ')';
            }
            
            // Create label with department: [Non Aset] - (departemen) or 3 - (departemen)
            $labelBase = $noUnitDisplay . $deptDisplay;
            
            return [
                'id' => (int)$r['id'],
                'label' => trim($labelBase." - ".($r['merk_unit']?:'-')." ".($r['model_unit']?:'')." @ ".($r['lokasi_unit']?:'-')),
                'no_unit' => $r['no_unit'],
                'merk_unit' => $r['merk_unit'],
                'model_unit' => $r['model_unit'],
                'lokasi_unit' => $r['lokasi_unit'],
                'status_unit_id' => $r['status_unit_id'],
                'departemen_id' => $r['departemen_id'],
                'departemen_name' => $r['nama_departemen'],
                'needs_no_unit' => $needsNoUnit
            ];
        }, $rows);
        return $this->response->setJSON(['success'=>true,'data'=>$data,'csrf_hash'=>csrf_hash()]);
    }

    /** Simple list for attachment picking from inventory (statuses: 7/8) */
    public function dataAttachmentSimple()
    {
        $q = trim((string)($this->request->getGet('q') ?? ''));
        $qb = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment as id, a.tipe, a.merk, a.model, ia.sn_attachment, ia.lokasi_penyimpanan, ia.status_unit')
            ->join('attachment a', 'a.id_attachment = ia.attachment_id', 'left')
            ->whereIn('ia.status_unit', [7,8])
            ->orderBy('ia.id_inventory_attachment','DESC')
            ->limit(50);
        if ($q !== '') {
            $qb->groupStart()
                ->like('a.tipe', $q)
                ->orLike('a.merk', $q)
                ->orLike('a.model', $q)
                ->orLike('ia.sn_attachment', $q)
                ->orLike('ia.lokasi_penyimpanan', $q)
            ->groupEnd();
        }
        $rows = $qb->get()->getResultArray();
        $data = array_map(function($r){
            $label = trim(($r['tipe'] ?: '-') . ' ' . ($r['merk'] ?: '') . ' ' . ($r['model'] ?: ''));
            $suffix = [];
            if (!empty($r['sn_attachment'])) $suffix[] = 'SN: '.$r['sn_attachment'];
            if (!empty($r['lokasi_penyimpanan'])) $suffix[] = '@ '.$r['lokasi_penyimpanan'];
            if ($suffix) $label .= ' ['.implode(', ', $suffix).']';
            return ['id'=>(int)$r['id'],'label'=>$label];
        }, $rows);
        return $this->response->setJSON(['success'=>true,'data'=>$data,'csrf_hash'=>csrf_hash()]);
    }

    /** Assign chosen unit and (optional) inventory attachment to SPK, then mark READY */
    public function spkAssignItems()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        $spkId = (int)($this->request->getPost('spk_id') ?? 0);
        $unitId = (int)($this->request->getPost('unit_id') ?? 0);
        $invAttachmentId = (int)($this->request->getPost('inventory_attachment_id') ?? 0);
        if ($spkId<=0 || $unitId<=0) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'SPK dan Unit wajib dipilih']);
        }
        $spk = $this->db->table('spk')->where('id',$spkId)->get()->getRowArray();
        if (!$spk) return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
        // Validate unit availability (status 7/8)
        $unit = $this->db->table('inventory_unit')->select('id_inventory_unit, status_unit_id')->where('id_inventory_unit',$unitId)->get()->getRowArray();
        if (!$unit || !in_array((int)$unit['status_unit_id'], [7,8], true)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Unit tidak tersedia (bukan stok aset/non aset)']);
        }
        // Validate attachment inventory if provided
        $invAtt = null;
        if ($invAttachmentId > 0) {
            $invAtt = $this->db->table('inventory_attachment')->select('id_inventory_attachment, status_unit')->where('id_inventory_attachment',$invAttachmentId)->get()->getRowArray();
            if (!$invAtt || !in_array((int)$invAtt['status_unit'], [7,8], true)) {
                return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Attachment inventory tidak tersedia']);
            }
        }
        // Merge into spesifikasi JSON: add selected items
        $spec = [];
        if (!empty($spk['spesifikasi'])) {
            $dec = json_decode($spk['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($dec)) $spec = $dec;
        }
        $spec['selected'] = [
            'unit_id' => $unitId,
            'inventory_attachment_id' => $invAttachmentId ?: null,
        ];
        $this->db->transStart();
        $prevStatus = $spk['status'] ?? null;
        $this->db->table('spk')->where('id',$spkId)->update([
            'spesifikasi' => json_encode($spec),
            'status' => 'READY',
            'diperbarui_pada' => date('Y-m-d H:i:s'),
        ]);
        // If an inventory attachment is selected, attach to the unit and mark the attachment as used
        if ($invAtt) {
            try {
                // Get selected unit basic info (no_unit)
                $unitInfo = $this->db->table('inventory_unit iu')
                    ->select('iu.no_unit')
                    ->where('iu.id_inventory_unit', $unitId)
                    ->get()->getRowArray();
                // Get attachment fields (attachment_id, sn)
                $attInfo = $this->db->table('inventory_attachment ia')
                    ->select('ia.attachment_id, ia.sn_attachment')
                    ->where('ia.id_inventory_attachment', $invAttachmentId)
                    ->get()->getRowArray();
                if ($attInfo) {
                    // Update unit to record attached attachment (model + SN)
                    $this->db->table('inventory_unit')->where('id_inventory_unit', $unitId)->update([
                        'model_attachment_id' => $attInfo['attachment_id'] ?? null,
                        'sn_attachment' => $attInfo['sn_attachment'] ?? null,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                // Mark attachment as used and note which unit
                $note = 'Digunakan pada unit ' . (($unitInfo['no_unit'] ?? '') ?: ('#'.$unitId));
                $this->db->table('inventory_attachment')->where('id_inventory_attachment', $invAttachmentId)->update([
                    'status_unit' => 3, // move from stock (7/8) to non-available state
                    'lokasi_penyimpanan' => $note,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) {
                // best-effort; do not fail the whole transaction if this linkage fails
            }
        }
        if ($prevStatus && $prevStatus !== 'READY') {
            $this->db->table('spk_status_history')->insert([
                'spk_id' => $spkId,
                'status_from' => $prevStatus,
                'status_to' => 'READY',
                'changed_by' => session('user_id') ?: 1,
                'note' => 'Items assigned by Service',
                'changed_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>'Gagal menetapkan item']);
        }
        // Notify Marketing role that SPK is READY
        try {
            if ($this->db->tableExists('notifications')) {
                // Ensure schema (target_role/url) exists
                try { (new \App\Controllers\Notifications())->index(); } catch (\Throwable $e) { /* ignore */ }
                $notif = [
                    'title' => 'SPK READY',
                    'message' => 'SPK ' . ($spk['nomor_spk'] ?? ('#'.$spkId)) . ' siap untuk dibuat DI oleh Marketing.',
                    'type' => 'success',
                    'user_id' => null,
                    'target_role' => 'marketing',
                    'url' => base_url('marketing/di'),
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $this->db->table('notifications')->insert($notif);
            }
        } catch (\Throwable $e) { /* best-effort only */ }
        return $this->response->setJSON(['success'=>true,'message'=>'Item ditetapkan dan SPK READY','csrf_hash'=>csrf_hash()]);
    }

    /** Service prepares DI from an SPK: creates DI + delivery_items (UNIT) and marks SPK COMPLETED */
    public function diPrepare()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        $spkId = (int)($this->request->getPost('spk_id') ?? 0);
        $unitId = (int)($this->request->getPost('unit_id') ?? 0);
        $tanggalKirim = $this->request->getPost('tanggal_kirim') ?: null;
        $catatan = $this->request->getPost('catatan') ?: null;
        if ($spkId<=0 || $unitId<=0) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'SPK dan Unit wajib dipilih']);
        }
        // Load SPK
        $spk = $this->db->table('spk')->where('id',$spkId)->get()->getRowArray();
        if (!$spk) { return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']); }
        // Create DI
        $diPayload = [
            'nomor_di' => $this->generateDiNumber(),
            'spk_id' => $spkId,
            'po_kontrak_nomor' => $spk['po_kontrak_nomor'],
            'pelanggan' => $spk['pelanggan'],
            'lokasi' => $spk['lokasi'],
            'tanggal_kirim' => $tanggalKirim,
            'catatan' => $catatan,
            'status' => 'SUBMITTED',
            'dibuat_oleh' => session('user_id') ?: 1,
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ];
        $this->db->transStart();
        $this->db->table('delivery_instructions')->insert($diPayload);
        $diId = (int)$this->db->insertID();
        // Insert items (UNIT)
        $this->db->table('delivery_items')->insert([
            'di_id' => $diId,
            'item_type' => 'UNIT',
            'unit_id' => $unitId,
            'attachment_id' => null,
            'keterangan' => null,
        ]);
        // Mark SPK COMPLETED and log history
        $prevStatus = $spk['status'] ?? null;
        $this->db->table('spk')->where('id',$spkId)->update(['status'=>'COMPLETED','diperbarui_pada'=>date('Y-m-d H:i:s')]);
        if ($prevStatus) {
            $this->db->table('spk_status_history')->insert([
                'spk_id' => $spkId,
                'status_from' => $prevStatus,
                'status_to' => 'COMPLETED',
                'changed_by' => session('user_id') ?: 1,
                'note' => 'DI prepared by Service',
                'changed_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>'Gagal menyiapkan DI']);
        }
        return $this->response->setJSON(['success'=>true,'message'=>'DI dibuat','di_id'=>$diId,'nomor'=>$diPayload['nomor_di'],'csrf_hash'=>csrf_hash()]);
    }

    private function generateDiNumber(): string
    {
        $prefix = 'DI/'.date('Ym').'/';
        $last = $this->db->table('delivery_instructions')->like('nomor_di',$prefix)->orderBy('id','DESC')->get()->getRowArray();
        $seq = 1;
        if ($last && isset($last['nomor_di'])) {
            $parts = explode('/', $last['nomor_di']);
            $seq = isset($parts[2]) ? (int)$parts[2] + 1 : 1;
        }
        return $prefix . str_pad((string)$seq,3,'0',STR_PAD_LEFT);
    }

    public function dataUnit()
    {
        // View now uses DataTables (AJAX). We only pass meta & (optional) user dept capability.
        $allowed = $this->getAllowedServiceDepartemenIds();
        $departemenOptions = $this->getDepartemenOptions($allowed);
        $lokasiOptions = $this->getLokasiOptions($allowed);
        $statusOptions = $this->getStatusOptions();
        // Component option lists for full edit
        $modelUnitOptions = $this->getModelUnitOptions();
        $tipeUnitOptions = $this->getTipeUnitOptions();
        $kapasitasOptions = $this->getKapasitasOptions();
        $mastOptions = $this->getMastOptions();
        $mesinOptions = $this->getMesinOptions();
        $bateraiOptions = $this->getBateraiOptions();
        $banOptions = $this->getBanOptions();
        $rodaOptions = $this->getRodaOptions();
        $valveOptions = $this->getValveOptions();
    $attachmentOptions = $this->getAttachmentOptions();
    $chargerOptions = $this->getChargerOptions();
        $data = [
            'title' => 'Data Unit Service | OPTIMA',
            'page_title' => 'Data Unit Service',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service' => 'Service',
                '/service/data-unit' => 'Data Unit'
            ],
            'can_view_both_departments' => count($allowed) > 1,
            'departemen_options' => $departemenOptions,
            'lokasi_options' => $lokasiOptions,
            'status_options' => $statusOptions,
            'model_unit_options' => $modelUnitOptions,
            'tipe_unit_options' => $tipeUnitOptions,
            'kapasitas_options' => $kapasitasOptions,
            'mast_options' => $mastOptions,
            'mesin_options' => $mesinOptions,
            'baterai_options' => $bateraiOptions,
            'ban_options' => $banOptions,
            'roda_options' => $rodaOptions,
            'valve_options' => $valveOptions,
            'attachment_options' => $attachmentOptions,
            'charger_options' => $chargerOptions,
        ];
        return view('service/data_unit', $data);
    }

    /**
     * Server-side data for Service Data Unit table.
     * Filters: status(optional), departemen scope limited to ELECTRIC (id=2) and DIESEL & GASOLINE (id=1,3)
     */
    public function dataUnitData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Bad request',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $draw   = (int)($this->request->getPost('draw') ?? 0);
        $start  = (int)($this->request->getPost('start') ?? 0);
        $length = (int)($this->request->getPost('length') ?? 25);
        if ($length <= 0) { $length = 25; }
        if ($length === -1) { $length = null; }

        // Robust status & search extraction
        $rawStatus = $this->request->getPost('status');
        $statusFilter = trim((string)($rawStatus ?? ''));
        if ($statusFilter === 'undefined') { $statusFilter = ''; }

        $searchArr = $this->request->getPost('search');
        $searchValue = '';
        if (is_array($searchArr)) {
            $searchValue = trim((string)($searchArr['value'] ?? ''));
        }

        $departemenId = trim((string)($this->request->getPost('departemen_id') ?? ''));
        $lokasiFilter = trim((string)($this->request->getPost('lokasi_unit') ?? ''));

        try {
            $allowed = $this->getAllowedServiceDepartemenIds();
            $base = $this->serviceBaseQuery($allowed);
            $countQ = $this->serviceBaseQuery($allowed);

            // Apply filters (except status for counts base)
            if ($departemenId !== '') {
                $ids = array_values(array_intersect(array_map('intval', explode(',', $departemenId)), $allowed));
                if ($ids) { $base->whereIn('iu.departemen_id', $ids); $countQ->whereIn('iu.departemen_id', $ids); }
            }
            if ($lokasiFilter !== '') {
                $base->like('iu.lokasi_unit', $lokasiFilter);
                $countQ->like('iu.lokasi_unit', $lokasiFilter);
            }
            if ($searchValue !== '') {
                $base->groupStart()
                    ->like('iu.no_unit', $searchValue)
                    ->orLike('iu.serial_number', $searchValue)
                    ->orLike('mu.merk_unit', $searchValue)
                    ->orLike('mu.model_unit', $searchValue)
                    ->orLike('tu.tipe', $searchValue)
                    ->orLike('tu.jenis', $searchValue)
                    ->orLike('iu.lokasi_unit', $searchValue)
                ->groupEnd();
                $countQ->groupStart()
                    ->like('iu.no_unit', $searchValue)
                    ->orLike('iu.serial_number', $searchValue)
                    ->orLike('mu.merk_unit', $searchValue)
                    ->orLike('mu.model_unit', $searchValue)
                    ->orLike('tu.tipe', $searchValue)
                    ->orLike('tu.jenis', $searchValue)
                    ->orLike('iu.lokasi_unit', $searchValue)
                ->groupEnd();
            }
            // recordsTotal (no search / lokasi / manual dept): full scope allowed
            $recordsTotal = $this->serviceBaseQuery($allowed)->countAllResults();
            // recordsFiltered (without status filter yet)
            $recordsFiltered = null; // compute after optional status
            if ($statusFilter !== '') {
                $base->where('iu.status_unit_id', (int)$statusFilter);
                $countQ->where('iu.status_unit_id', (int)$statusFilter);
            }
            $recordsFiltered = $countQ->countAllResults();

            // Status counts using fresh builders (avoid cloning issues)
            $statusList = [7,8,3,2];
            $statusCounts = [];
            foreach ($statusList as $st) {
                $sQB = $this->serviceBaseQuery($allowed);
                if ($departemenId !== '') {
                    $ids = array_values(array_intersect(array_map('intval', explode(',', $departemenId)), $allowed));
                    if ($ids) { $sQB->whereIn('iu.departemen_id', $ids); }
                }
                if ($lokasiFilter !== '') { $sQB->like('iu.lokasi_unit', $lokasiFilter); }
                // do not apply search to counts (can change if needed); keep consistent with other modules? choose not to include search for broad counts.
                $statusCounts[$st] = $sQB->where('iu.status_unit_id',$st)->countAllResults();
            }

            $base->orderBy('iu.no_unit','ASC')->orderBy('iu.id_inventory_unit','ASC');
            if ($length !== null) { $base->limit($length, $start); }
            $rows = $base->get()->getResultArray();

            $data = [];
            foreach ($rows as $r) {
                $id = (int)$r['id_inventory_unit'];
                $data[] = [
                    'id' => $id,
                    'no_unit' => $r['no_unit'],
                    'serial_number' => $r['serial_number'],
                    'merk_unit' => $r['merk_unit'],
                    'model_unit' => $r['model_unit'],
                    'tipe_full' => $r['tipe_full'],
                    'kapasitas_unit' => $r['kapasitas_unit'],
                    'status_unit_id' => $r['status_unit_id'],
                    'status_unit_name' => strtoupper($r['status_unit_name']),
                    'lokasi_unit' => $r['lokasi_unit'],
                    'nama_departemen' => $r['nama_departemen'],
                    'actions' => $this->buildServiceActions($id)
                ];
            }

            return $this->response->setJSON([
                'draw'=>$draw,
                'recordsTotal'=>$recordsTotal,
                'recordsFiltered'=>$recordsFiltered,
                'data'=>$data,
                'stats'=>[ 'status_counts'=>$statusCounts ],
                'csrf_hash'=>csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
                'csrf_hash' => csrf_hash(),
            ]);
        }
    }

    /** Detail unit (service) — reuse marketing style but may append future maintenance info */
    public function unitDetail($id)
    {
        $id = (int)$id; if($id<=0) return $this->response->setJSON(['success'=>false,'message'=>'ID tidak valid','csrf_hash'=>csrf_hash()]);
        try {
            $allowed = $this->getAllowedServiceDepartemenIds();
            if (!$allowed) { return $this->response->setJSON(['success'=>false,'message'=>'Akses departemen ditolak','csrf_hash'=>csrf_hash()]); }
            $sql = 'SELECT 
                iu.id_inventory_unit, iu.no_unit, iu.serial_number as serial_number_po,
                iu.status_unit_id as status_unit, iu.status_unit_id as status_unit_id,
                COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                COALESCE(mu.model_unit, "Unknown") as model_unit,
                iu.lokasi_unit, iu.keterangan,
                iu.departemen_id as departemen_id,
                iu.model_unit_id as model_unit_id,
                iu.tipe_unit_id as tipe_unit_id,
                iu.tahun_unit as tahun_po,
                iu.kapasitas_unit_id as kapasitas_unit_id,
                iu.model_mast_id as model_mast_id, iu.tinggi_mast, iu.sn_mast as sn_mast_po,
                iu.model_mesin_id as model_mesin_id, iu.sn_mesin as sn_mesin_po,
                iu.model_baterai_id as model_baterai_id, iu.sn_baterai as sn_baterai_po,
                iu.ban_id as ban_id, iu.roda_id as roda_id, iu.valve_id as valve_id,
                iu.model_attachment_id as model_attachment_id, iu.sn_attachment as sn_attachment_po,
                iu.model_charger_id as model_charger_id, iu.sn_charger as sn_charger_po,
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
                "Sesuai" as status_verifikasi,
                "Verifikasi berhasil" as catatan_verifikasi
            FROM inventory_unit iu
                LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
                LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
                LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
                LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
                LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
                LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
                LEFT JOIN mesin m ON m.id = iu.model_mesin_id
                LEFT JOIN baterai b ON b.id = iu.model_baterai_id
                LEFT JOIN attachment at ON at.id_attachment = iu.model_attachment_id
                LEFT JOIN charger ch ON ch.id_charger = iu.charger_id
                LEFT JOIN tipe_ban tb ON tb.id_ban = iu.ban_id
                LEFT JOIN jenis_roda jr ON jr.id_roda = iu.roda_id
                LEFT JOIN valve v ON v.id_valve = iu.valve_id
            WHERE iu.id_inventory_unit = ? AND iu.departemen_id IN ('.implode(',', $allowed).')';
            $row = $this->db->query($sql, [$id])->getRowArray();
            if(!$row) return $this->response->setJSON(['success'=>false,'message'=>'Unit tidak ditemukan','csrf_hash'=>csrf_hash()]);
            return $this->response->setJSON(['success'=>true,'data'=>$row,'csrf_hash'=>csrf_hash()]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>$e->getMessage(),'csrf_hash'=>csrf_hash()]);
        }
    }

    /** Update editable fields for a unit (all components except no_unit) */
    public function unitUpdate($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad Request','csrf_hash'=>csrf_hash()]);
        }
        $id = (int)$id; if($id<=0) return $this->response->setJSON(['success'=>false,'message'=>'ID tidak valid','csrf_hash'=>csrf_hash()]);
        $lokasi = trim((string)$this->request->getPost('lokasi_unit'));
        $status = (int)$this->request->getPost('status_unit_id');
        $keterangan = trim((string)$this->request->getPost('keterangan'));
        $serial = trim((string)$this->request->getPost('serial_number'));
        $snMast = trim((string)$this->request->getPost('sn_mast'));
        $snMesin = trim((string)$this->request->getPost('sn_mesin'));
        $snBaterai = trim((string)$this->request->getPost('sn_baterai'));
        $tahun = trim((string)$this->request->getPost('tahun_unit'));
    // Foreign key component IDs
    $modelUnitId = (int)$this->request->getPost('model_unit_id');
    $tipeUnitId = (int)$this->request->getPost('tipe_unit_id');
    $kapasitasId = (int)$this->request->getPost('kapasitas_unit_id');
    $mastId = (int)$this->request->getPost('model_mast_id');
    $tinggiMast = trim((string)$this->request->getPost('tinggi_mast'));
    $mesinId = (int)$this->request->getPost('model_mesin_id');
    $bateraiId = (int)$this->request->getPost('model_baterai_id');
    $attachmentId = (int)$this->request->getPost('model_attachment_id');
    $chargerId = (int)$this->request->getPost('model_charger_id');
    $banId = (int)$this->request->getPost('ban_id');
    $rodaId = (int)$this->request->getPost('roda_id');
    $valveId = (int)$this->request->getPost('valve_id');
    $departemenIdNew = (int)$this->request->getPost('departemen_id');
    $snAttachment = trim((string)$this->request->getPost('sn_attachment'));
    $snCharger = trim((string)$this->request->getPost('sn_charger'));
        try {
            $allowed = $this->getAllowedServiceDepartemenIds();
            // Ensure unit exists & belongs to allowed departemen
            $unit = $this->db->table('inventory_unit')
                ->select('id_inventory_unit, departemen_id')
                ->where('id_inventory_unit',$id)
                ->get()->getRowArray();
            if(!$unit) return $this->response->setJSON(['success'=>false,'message'=>'Unit tidak ditemukan','csrf_hash'=>csrf_hash()]);
            if(!in_array((int)$unit['departemen_id'],$allowed,true)) return $this->response->setJSON(['success'=>false,'message'=>'Akses ditolak','csrf_hash'=>csrf_hash()]);

            $dataUpdate = [];
            if($lokasi !== '') { $dataUpdate['lokasi_unit'] = $lokasi; }
            if($status > 0) { $dataUpdate['status_unit_id'] = $status; }
            $dataUpdate['keterangan'] = $keterangan !== '' ? $keterangan : null;
            if($serial !== '') { $dataUpdate['serial_number'] = $serial; }
            if($snMast !== '') { $dataUpdate['sn_mast'] = $snMast; }
            if($snMesin !== '') { $dataUpdate['sn_mesin'] = $snMesin; }
            if($snBaterai !== '') { $dataUpdate['sn_baterai'] = $snBaterai; }
            if($tahun !== '') { $dataUpdate['tahun_unit'] = $tahun; }
            if($modelUnitId>0) { $dataUpdate['model_unit_id'] = $modelUnitId; }
            if($tipeUnitId>0) { $dataUpdate['tipe_unit_id'] = $tipeUnitId; }
            if($kapasitasId>0) { $dataUpdate['kapasitas_unit_id'] = $kapasitasId; }
            if($mastId>0) { $dataUpdate['model_mast_id'] = $mastId; }
            if($tinggiMast !== '') { $dataUpdate['tinggi_mast'] = $tinggiMast; }
            if($mesinId>0) { $dataUpdate['model_mesin_id'] = $mesinId; }
            if($bateraiId>0) { $dataUpdate['model_baterai_id'] = $bateraiId; }
            if($attachmentId>0) { $dataUpdate['model_attachment_id'] = $attachmentId; }
            if($chargerId>0) { $dataUpdate['model_charger_id'] = $chargerId; }
            if($banId>0) { $dataUpdate['ban_id'] = $banId; }
            if($rodaId>0) { $dataUpdate['roda_id'] = $rodaId; }
            if($valveId>0) { $dataUpdate['valve_id'] = $valveId; }
            if($departemenIdNew>0 && in_array($departemenIdNew,$allowed,true)) { $dataUpdate['departemen_id'] = $departemenIdNew; }
            if($snAttachment !== '') { $dataUpdate['sn_attachment'] = $snAttachment; }
            if($snCharger !== '') { $dataUpdate['sn_charger'] = $snCharger; }
            if(!$dataUpdate) { return $this->response->setJSON(['success'=>false,'message'=>'Tidak ada perubahan','csrf_hash'=>csrf_hash()]); }
            // Fetch old data for diff (minimal select)
            $old = $this->db->table('inventory_unit')->select('*')->where('id_inventory_unit',$id)->get()->getRowArray();
            $this->db->table('inventory_unit')->where('id_inventory_unit',$id)->update($dataUpdate);
            if($this->db->affectedRows()>=0){
                $new = $this->db->table('inventory_unit')->select('*')->where('id_inventory_unit',$id)->get()->getRowArray();
                $changes=[]; if($old && $new){ foreach($dataUpdate as $k=>$v){ $oldVal=$old[$k]??null; $newVal=$new[$k]??null; if($oldVal!=$newVal){ $changes[$k]=['old'=>$oldVal,'new'=>$newVal]; } } }
                $this->logActivity('service_unit_update', 'Update Unit Service #'.$id, [ 'unit_id'=>$id, 'changes'=>$changes ]);
                return $this->response->setJSON(['success'=>true,'message'=>'Berhasil disimpan','csrf_hash'=>csrf_hash(),'updated'=>$dataUpdate,'changes'=>$changes]);
            }
            return $this->response->setJSON(['success'=>false,'message'=>'Gagal update','csrf_hash'=>csrf_hash()]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>$e->getMessage(),'csrf_hash'=>csrf_hash()]);
        }
    }

    /** Export CSV of current filtered units (simple, client can call with query params) */
    public function exportDataUnits()
    {
        $allowed = $this->getAllowedServiceDepartemenIds();
        $status  = $this->request->getGet('status');
        $depart  = $this->request->getGet('departemen_id');
        $search  = $this->request->getGet('q');

        $qb = $this->serviceBaseQuery($allowed);
        if ($status !== null && $status !== '') { $qb->where('iu.status_unit_id', (int)$status); }
        if ($depart) {
            $ids = array_values(array_intersect(array_map('intval', explode(',', $depart)), $allowed));
            if ($ids) { $qb->whereIn('iu.departemen_id', $ids); }
        }
        if ($search) {
            $qb->groupStart()
                ->like('iu.no_unit', $search)
                ->orLike('iu.serial_number', $search)
                ->orLike('mu.merk_unit', $search)
                ->orLike('mu.model_unit', $search)
                ->orLike('tu.tipe', $search)
                ->orLike('tu.jenis', $search)
            ->groupEnd();
        }
        $qb->orderBy('iu.no_unit','ASC');
        $rows = $qb->get()->getResultArray();

        $filename = 'service_units_'.date('Ymd_His').'.csv';
        $output = fopen('php://temp', 'w');
        fputcsv($output, ['NoUnit','Serial','Merk','Model','Tipe','Kapasitas','Status','Lokasi','Departemen']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['no_unit'], $r['serial_number'], $r['merk_unit'], $r['model_unit'], $r['tipe_full'],
                $r['kapasitas_unit'], $r['status_unit_name'], $r['lokasi_unit'], $r['nama_departemen']
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $this->response->setHeader('Content-Type','text/csv')
            ->setHeader('Content-Disposition','attachment; filename="'.$filename.'"')
            ->setBody($csv);
    }

    /** Basic maintenance history stub (replace with real joins later) */
    public function maintenanceHistory($id)
    {
        $id = (int)$id; if($id<=0) return $this->response->setJSON(['success'=>false,'message'=>'ID tidak valid','csrf_hash'=>csrf_hash()]);
        // Placeholder static rows; adapt to actual maintenance tables when ready.
        $history = [
            ['date'=>'2025-07-01','type'=>'PM Monthly','notes'=>'Routine check OK','downtime_hours'=>1.5],
            ['date'=>'2025-05-15','type'=>'Repair','notes'=>'Ganti seal hydraulic','downtime_hours'=>6],
            ['date'=>'2025-03-10','type'=>'PM Quarterly','notes'=>'Tune-up + filter','downtime_hours'=>3]
        ];
        return $this->response->setJSON(['success'=>true,'data'=>$history,'csrf_hash'=>csrf_hash()]);
    }

    private function serviceBaseQuery(array $allowed = [1,2,3]): \CodeIgniter\Database\BaseBuilder
    {
        // Limit to departemen ELECTRIC (2) and DIESEL & GASOLINE (1,3)
        $qb = $this->db->table('inventory_unit iu')
            ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.status_unit_id, iu.lokasi_unit')
            ->select('COALESCE(mu.merk_unit, "-") AS merk_unit, COALESCE(mu.model_unit, "") AS model_unit')
            ->select('COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "-") AS tipe_full')
            ->select('COALESCE(kap.kapasitas_unit, "-") AS kapasitas_unit')
            ->select('COALESCE(d.nama_departemen, "-") AS nama_departemen')
            ->select('su.status_unit AS status_unit_name')
            ->join('status_unit su','su.id_status = iu.status_unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
            ->join('kapasitas kap','kap.id_kapasitas = iu.kapasitas_unit_id','left')
            ->join('departemen d','d.id_departemen = iu.departemen_id','left')
            ->whereIn('iu.departemen_id',$allowed);
        return $qb;
    }

    /** Placeholder RBAC logic: obtain allowed departemen IDs for service scope */
    private function getAllowedServiceDepartemenIds(): array
    {
        // Future: derive from session / permissions. For now restrict to 1,2,3 intersection.
        $default = [1,2,3];
        // Example session key (if exists) 'user_departemen_ids'
        $sess = session();
        $userAllowed = $sess->get('user_departemen_ids');
        if (is_array($userAllowed) && $userAllowed) {
            $filtered = array_values(array_intersect(array_map('intval',$userAllowed), $default));
            return $filtered ?: $default;
        }
        return $default;
    }

    private function buildServiceActions(int $id): string
    {
        // Use JS function svcView() defined in the view
        $woUrl = base_url('service/work-orders') . '?unit=' . $id;
        return '<div class="dropdown">'
            . '<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>'
            . '<ul class="dropdown-menu">'
            . '<li><a class="dropdown-item" href="#" onclick="svcView(' . $id . ')"><i class="fas fa-eye me-2 text-info"></i>Lihat</a></li>'
            . '<li><a class="dropdown-item" href="#" onclick="svcEdit(' . $id . ')"><i class="fas fa-edit me-2 text-primary"></i>Edit</a></li>'
            . '<li><a class="dropdown-item" href="' . $woUrl . '"><i class="fas fa-wrench me-2 text-warning"></i>Work Order</a></li>'
            . '</ul></div>';
    }

    private function getDepartemenOptions(array $allowed): array
    {
        if (!$allowed) return [];
        return $this->db->table('departemen')
            ->select('id_departemen, nama_departemen')
            ->whereIn('id_departemen', $allowed)
            ->orderBy('nama_departemen','ASC')
            ->get()->getResultArray();
    }

    private function getLokasiOptions(array $allowed): array
    {
        if (!$allowed) return [];
        $rows = $this->db->table('inventory_unit')
            ->distinct()
            ->select('lokasi_unit')
            ->whereIn('departemen_id', $allowed)
            ->where('lokasi_unit IS NOT NULL')
            ->where('lokasi_unit <>','')
            ->orderBy('lokasi_unit','ASC')
            ->get()->getResultArray();
        return array_values(array_filter(array_map(fn($r)=> $r['lokasi_unit'] ?? '', $rows)));
    }

    private function getStatusOptions(): array
    {
        // Basic status list used in Service module (can be filtered later)
        $rows = $this->db->table('status_unit')
            ->select('id_status as id, status_unit as name')
            ->orderBy('status_unit','ASC')
            ->get()->getResultArray();
        return $rows ?: [];
    }

    private function getModelUnitOptions(): array
    {
        return $this->db->table('model_unit')
            ->select('id_model_unit as id, CONCAT(COALESCE(merk_unit,"-")," / ",COALESCE(model_unit,"")) as name')
            ->orderBy('merk_unit','ASC')->orderBy('model_unit','ASC')
            ->limit(500)->get()->getResultArray();
    }
    private function getTipeUnitOptions(): array
    {
        return $this->db->table('tipe_unit')
            ->select('id_tipe_unit as id, CONCAT(COALESCE(tipe,"-")," ",COALESCE(jenis,"")) as name')
            ->orderBy('tipe','ASC')->orderBy('jenis','ASC')
            ->limit(500)->get()->getResultArray();
    }
    private function getKapasitasOptions(): array
    {
        return $this->db->table('kapasitas')
            ->select('id_kapasitas as id, kapasitas_unit as name')
            ->orderBy('kapasitas_unit','ASC')
            ->get()->getResultArray();
    }
    private function getMastOptions(): array
    {
        return $this->db->table('tipe_mast')
            ->select('id_mast as id, tipe_mast as name')
            ->orderBy('tipe_mast','ASC')->get()->getResultArray();
    }
    private function getMesinOptions(): array
    {
        return $this->db->table('mesin')
            ->select('id as id, CONCAT(COALESCE(merk_mesin,"-")," ",COALESCE(model_mesin,"")) as name')
            ->orderBy('merk_mesin','ASC')->orderBy('model_mesin','ASC')->limit(500)->get()->getResultArray();
    }
    private function getBateraiOptions(): array
    {
        return $this->db->table('baterai')
            ->select('id as id, CONCAT(COALESCE(merk_baterai,"-")," ",COALESCE(tipe_baterai,"")) as name')
            ->orderBy('merk_baterai','ASC')->orderBy('tipe_baterai','ASC')->limit(500)->get()->getResultArray();
    }
    private function getBanOptions(): array
    {
        return $this->db->table('tipe_ban')
            ->select('id_ban as id, tipe_ban as name')
            ->orderBy('tipe_ban','ASC')->get()->getResultArray();
    }
    private function getRodaOptions(): array
    {
        return $this->db->table('jenis_roda')
            ->select('id_roda as id, tipe_roda as name')
            ->orderBy('tipe_roda','ASC')->get()->getResultArray();
    }
    private function getValveOptions(): array
    {
        return $this->db->table('valve')
            ->select('id_valve as id, jumlah_valve as name')
            ->orderBy('jumlah_valve','ASC')->get()->getResultArray();
    }
    private function getAttachmentOptions(): array
    {
        return $this->db->table('attachment')
            // Table 'attachment' columns: id_attachment, tipe, merk, model
            ->select('id_attachment as id, CONCAT(COALESCE(merk,"-")," / ",COALESCE(model,"")) as name')
            ->orderBy('merk','ASC')->orderBy('model','ASC')->limit(500)->get()->getResultArray();
    }
    private function getChargerOptions(): array
    {
        return $this->db->table('charger')
            // Table 'charger' columns: id_charger, merk_charger, tipe_charger (no model_charger column)
            ->select('id_charger as id, CONCAT(COALESCE(merk_charger,"-")," ",COALESCE(tipe_charger,"")) as name')
            ->orderBy('merk_charger','ASC')->orderBy('tipe_charger','ASC')->limit(500)->get()->getResultArray();
    }

    private function logActivity(string $action, string $description, array $details = []): void
    {
        try {
            $db = \Config\Database::connect();
            $userId = null; // adapt if session user id available
            $sess = session();
            if($sess && $sess->has('user_id')) { $userId = (int)$sess->get('user_id'); }
            $db->table('activity_logs')->insert([
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => 'inventory_unit',
                'entity_id' => $details['unit_id'] ?? null,
                'description' => $description,
                'details' => $details ? json_encode($details) : null,
                'ip_address' => service('request')->getIPAddress(),
                'user_agent' => service('request')->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            // Silently ignore logging errors
        }
    }

    // Private methods untuk data
    private function getWorkOrders()
    {
        return [
            [
                'id' => 'WO-2024-001',
                'unit' => 'FL-003',
                'type' => 'Engine Maintenance',
                'status' => 'In Progress',
                'priority' => 'High',
                'assigned_to' => 'John Doe',
                'created_at' => '2024-01-15 08:00:00'
            ],
            [
                'id' => 'WO-2024-002',
                'unit' => 'FL-007',
                'type' => 'Hydraulic System',
                'status' => 'Pending',
                'priority' => 'Medium',
                'assigned_to' => 'Jane Smith',
                'created_at' => '2024-01-15 09:30:00'
            ],
            [
                'id' => 'WO-2024-003',
                'unit' => 'FL-012',
                'type' => 'Brake Inspection',
                'status' => 'Completed',
                'priority' => 'Low',
                'assigned_to' => 'Mike Johnson',
                'created_at' => '2024-01-15 10:15:00'
            ],
        ];
    }

    private function getWorkOrderHistory()
    {
        return [
            [
                'id' => 'WO-2024-003',
                'unit' => 'FL-012',
                'type' => 'Brake Inspection',
                'status' => 'Completed',
                'assigned_to' => 'Mike Johnson',
                'completed_at' => '2024-01-15 16:00:00',
                'duration' => '6 hours'
            ],
            [
                'id' => 'WO-2024-004',
                'unit' => 'FL-002',
                'type' => 'Oil Change',
                'status' => 'Completed',
                'assigned_to' => 'John Doe',
                'completed_at' => '2024-01-14 14:30:00',
                'duration' => '2 hours'
            ],
        ];
    }

    private function getPmpsData()
    {
        return [
            [
                'unit' => 'FL-001',
                'type' => 'Monthly Service',
                'due_date' => '2024-01-16',
                'status' => 'Due Tomorrow',
                'last_service' => '2023-12-16'
            ],
            [
                'unit' => 'FL-005',
                'type' => '3-Month Inspection',
                'due_date' => '2024-01-22',
                'status' => 'Due Next Week',
                'last_service' => '2023-10-22'
            ],
        ];
    }

    private function getServiceUnits()
    {
        return [
            [
                'unit_code' => 'FL-001',
                'brand' => 'Toyota',
                'model' => '8FG25',
                'year' => '2020',
                'status' => 'Available',
                'last_service' => '2023-12-16'
            ],
            [
                'unit_code' => 'FL-002',
                'brand' => 'Mitsubishi',
                'model' => 'FG25N',
                'year' => '2019',
                'status' => 'In Service',
                'last_service' => '2023-12-14'
            ],
        ];
    }
    
    // API Methods untuk Work Orders
    public function workOrderList()
    {
        $data = [
            [
                'id' => 1,
                'wo_number' => 'WO-2024-001',
                'unit' => 'Forklift-001',
                'description' => 'Engine maintenance and oil change',
                'status' => 'OPEN',
                'priority' => 'MEDIUM',
                'assigned_to' => 'John Doe',
                'created_at' => '2024-01-15',
                'due_date' => '2024-01-20',
                'estimated_hours' => 8
            ],
            [
                'id' => 2,
                'wo_number' => 'WO-2024-002',
                'unit' => 'Forklift-002',
                'description' => 'Brake system inspection',
                'status' => 'KENDALA',
                'priority' => 'HIGH',
                'assigned_to' => 'Jane Smith',
                'created_at' => '2024-01-14',
                'due_date' => '2024-01-18',
                'estimated_hours' => 4
            ],
            [
                'id' => 3,
                'wo_number' => 'WO-2024-003',
                'unit' => 'Forklift-003',
                'description' => 'Hydraulic system repair',
                'status' => 'PENDING',
                'priority' => 'LOW',
                'assigned_to' => 'Mike Johnson',
                'created_at' => '2024-01-13',
                'due_date' => '2024-01-25',
                'estimated_hours' => 6
            ]
        ];

        $stats = [
            'total' => count($data),
            'open' => 1,
            'kendala' => 1,
            'pending' => 1,
            'completed' => 0
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'stats' => $stats,
            'token' => csrf_hash()
        ]);
    }

    public function workOrderCreate()
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Work order created successfully',
            'token' => csrf_hash()
        ]);
    }

    public function workOrderGet($id)
    {
        $workOrder = [
            'wo_id' => $id,
            'wo_number' => 'WO-2024-' . str_pad($id, 3, '0', STR_PAD_LEFT),
            'unit' => 'Forklift-' . str_pad($id, 3, '0', STR_PAD_LEFT),
            'description' => 'Engine maintenance and oil change',
            'status' => 'OPEN',
            'priority' => 'MEDIUM',
            'assigned_to' => 'John Doe',
            'due_date' => '2024-01-20',
            'estimated_hours' => 8
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $workOrder,
            'token' => csrf_hash()
        ]);
    }

    public function workOrderUpdate()
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Work order updated successfully',
            'token' => csrf_hash()
        ]);
    }

    public function workOrderDelete()
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Work order deleted successfully',
            'token' => csrf_hash()
        ]);
    }

    private function getServiceStats()
    {
        return [
            'active_work_orders' => 24,
            'completed_today' => 8,
            'overdue_pmps' => 3,
            'units_in_service' => 12,
            'completion_rate' => 92,
            'on_time_delivery' => 85,
            'customer_satisfaction' => 88
        ];
    }

    private function getRecentWorkOrders()
    {
        return [
            [
                'id' => 'WO-2024-001',
                'type' => 'Engine Maintenance',
                'unit' => 'FL-003',
                'assigned_to' => 'John Doe',
                'status' => 'In Progress',
                'created_at' => '2024-01-15 14:30:00'
            ],
            [
                'id' => 'WO-2024-002',
                'type' => 'Hydraulic System',
                'unit' => 'FL-007',
                'assigned_to' => 'Jane Smith',
                'status' => 'Pending',
                'created_at' => '2024-01-15 12:15:00'
            ],
            [
                'id' => 'WO-2024-003',
                'type' => 'Brake Inspection',
                'unit' => 'FL-012',
                'assigned_to' => 'Mike Johnson',
                'status' => 'Completed',
                'created_at' => '2024-01-14 16:45:00'
            ]
        ];
    }

    private function getMaintenanceAlerts()
    {
        return [
            [
                'unit' => 'FL-001',
                'service_type' => 'Monthly Service',
                'due_date' => '2024-01-16',
                'status' => 'Overdue',
                'last_service' => '2023-12-16'
            ],
            [
                'unit' => 'FL-005',
                'service_type' => '3-Month Inspection',
                'due_date' => '2024-01-22',
                'status' => 'Due Soon',
                'last_service' => '2023-10-22'
            ],
            [
                'unit' => 'FL-008',
                'service_type' => 'Oil Change',
                'due_date' => '2024-01-30',
                'status' => 'Scheduled',
                'last_service' => '2023-11-15'
            ]
        ];
    }

    private function getTechnicians()
    {
        return [
            [
                'name' => 'John Doe',
                'position' => 'Senior Technician',
                'status' => 'Available',
                'avatar' => 'https://via.placeholder.com/80x80'
            ],
            [
                'name' => 'Jane Smith',
                'position' => 'Hydraulic Specialist',
                'status' => 'Busy',
                'avatar' => 'https://via.placeholder.com/80x80'
            ],
            [
                'name' => 'Mike Johnson',
                'position' => 'Engine Specialist',
                'status' => 'Available',
                'avatar' => 'https://via.placeholder.com/80x80'
            ],
            [
                'name' => 'Sarah Wilson',
                'position' => 'Electrical Specialist',
                'status' => 'Off Duty',
                'avatar' => 'https://via.placeholder.com/80x80'
            ]
        ];
    }

    /**
     * Check if no_unit already exists in database
     */
    public function checkNoUnitExists()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request method']);
        }

        try {
            $input = json_decode($this->request->getBody(), true);
            $noUnit = $input['no_unit'] ?? null;

            if (!$noUnit || !is_numeric($noUnit)) {
                return $this->response->setJSON([
                    'exists' => false,
                    'message' => 'Invalid no_unit format'
                ]);
            }

            // Check in inventory_unit table
            $query = $this->db->table('inventory_unit')
                              ->where('no_unit', (int)$noUnit)
                              ->countAllResults();

            $exists = ($query > 0);

            return $this->response->setJSON([
                'exists' => $exists,
                'no_unit' => (int)$noUnit,
                'message' => $exists ? "No Unit {$noUnit} sudah digunakan" : "No Unit {$noUnit} tersedia"
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error checking no_unit exists: ' . $e->getMessage());
            return $this->response->setJSON([
                'exists' => false,
                'error' => 'Database error occurred'
            ]);
        }
    }

    public function approveFabrikasi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['message'=>'Bad request']);
        }

        $spkId   = (int) $this->request->getPost('spk_id');
        $unitId  = (int) $this->request->getPost('unit_id'); // dari persiapan
        $invAttId = (int) $this->request->getPost('attachment_inventory_id'); // wajib kalau ada attachment
        $invChgId = (int) $this->request->getPost('charger_inventory_id');    // wajib jika departemen ELECTRIC
        $mekanik  = (string) $this->request->getPost('mekanik');

        $db = db_connect();
        $spk = $db->table('spk')->where('id',$spkId)->get()->getRowArray();
        if (!$spk) return $this->response->setStatusCode(404)->setJSON(['message'=>'SPK tidak ditemukan']);

        $spec = json_decode($spk['spesifikasi'] ?? '{}', true) ?: [];
        $departemenId = (int) ($spec['departemen_id'] ?? 0);

        $model = new InventoryAttachmentModel();

        // Assign attachment fisik (jika dipilih)
        if ($invAttId) {
            $userId = (int) (session('user_id') ?? 0);
            $ok = $model->assignToUnit($invAttId, $unitId, $userId, "Fabrikasi SPK #$spkId");
            if (!$ok) return $this->response->setStatusCode(500)->setJSON(['message'=>'Gagal assign attachment']);
        }

        // Jika departemen electric dan user pilih charger (inventory dengan charger_id)
        if ($departemenId === 2 && $invChgId) {
            $userId = (int) (session('user_id') ?? 0);
            $ok = $model->assignToUnit($invChgId, $unitId, $userId, "Fabrikasi (charger) SPK #$spkId");
            if (!$ok) return $this->response->setStatusCode(500)->setJSON(['message'=>'Gagal assign charger']);
        }

        // Simpan jejak pilihan fabrikasi terakhir ke JSON (akan dipush ke prepared_units saat PDI)
        $spec['fabrikasi_last'] = [
            'unit_id'                  => $unitId,
            'attachment_inventory_id'  => $invAttId ?: null,
            'charger_inventory_id'     => ($departemenId === 2 ? ($invChgId ?: null) : null),
            'mekanik'                  => $mekanik,
            'timestamp'                => date('Y-m-d H:i:s'),
        ];

        $db->table('spk')->where('id',$spkId)->update([
            'fabrikasi_mekanik'        => $mekanik ?: null,
            'fabrikasi_tanggal_approve'=> date('Y-m-d H:i:s'),
            'spesifikasi'              => json_encode($spec, JSON_UNESCAPED_UNICODE),
            // status tetap IN_PROGRESS
        ]);

        return $this->response->setJSON(['success'=>true]);
    }

    public function approvePdi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['message'=>'Bad request']);
        }
        $spkId   = (int) $this->request->getPost('spk_id');
        $catatan = (string) $this->request->getPost('catatan');

        $db   = db_connect();
        $spk  = $db->table('spk')->where('id',$spkId)->get()->getRowArray();
        if (!$spk) return $this->response->setStatusCode(404)->setJSON(['message'=>'SPK tidak ditemukan']);

        $spec = json_decode($spk['spesifikasi'] ?? '{}', true) ?: [];
        $last = $spec['fabrikasi_last'] ?? null;
        if (!$last || empty($last['unit_id'])) {
            return $this->response->setStatusCode(400)->setJSON(['message'=>'Data fabrikasi belum lengkap']);
        }

        $prepared = $spec['prepared_units'] ?? [];
        $prepared[] = [
            'unit_id'                 => (string)$last['unit_id'],
            'attachment_inventory_id' => $last['attachment_inventory_id'] ?? null,
            'charger_inventory_id'    => $last['charger_inventory_id'] ?? null,
            'mekanik'                 => $last['mekanik'] ?? null,
            'catatan'                 => $catatan ?: null,
            'timestamp'               => date('Y-m-d H:i:s'),
        ];
        $spec['prepared_units'] = $prepared;
        unset($spec['fabrikasi_last']); // reset untuk siklus berikutnya

        // hitung progress → READY bila terpenuhi
        $total    = (int) ($spk['jumlah_unit'] ?? 1);
        $isReady  = (count($prepared) >= max(1,$total));

        $db->table('spk')->where('id',$spkId)->update([
            'pdi_tanggal_approve' => date('Y-m-d H:i:s'),
            'pdi_catatan'         => $catatan ?: null,
            'status'              => $isReady ? 'READY' : 'IN_PROGRESS',
            'spesifikasi'         => json_encode($spec, JSON_UNESCAPED_UNICODE),
        ]);

        return $this->response->setJSON(['success'=>true, 'ready'=>$isReady]);
    }
}