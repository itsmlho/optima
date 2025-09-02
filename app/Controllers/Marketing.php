<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\BaseBuilder;
use App\Models\SpkModel;
use App\Models\KontrakModel;
use App\Models\KontrakSpesifikasiModel;
use App\Models\InventoryUnitModel;
use App\Models\InventoryAttachmentModel;
use App\Models\DeliveryInstructionModel;
use App\Models\DeliveryItemModel;
use App\Models\NotificationModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Marketing extends Controller
{
    protected $db;
    protected $spkModel;
    protected $kontrakModel;
    protected $kontrakSpesifikasiModel;
    protected $unitModel;
    protected $attModel;
    protected $diModel;
    protected $diItemModel;
    protected $notifModel;

    public function __construct()
    {
    $this->db = \Config\Database::connect();
    $this->spkModel = new SpkModel();
    $this->kontrakModel = new KontrakModel();
    $this->kontrakSpesifikasiModel = new KontrakSpesifikasiModel();
    $this->unitModel = new InventoryUnitModel();
    $this->attModel = new InventoryAttachmentModel();
    $this->diModel = new DeliveryInstructionModel();
    $this->diItemModel = new DeliveryItemModel();
    $this->notifModel = class_exists(\App\Models\NotificationModel::class) ? new NotificationModel() : null;
    }

    public function availableUnits()
    {
        return view('marketing/unit_tersedia');
    }

    // Legacy route support (unit-tersedia) jika masih dipakai
    public function unitTersedia()
    {
        return $this->availableUnits();
    }

    // Proxy detail (optional) agar marketing bisa akses tanpa prefix inventory
    public function unitDetail($id)
    {
        try {
            $id = (int)$id;
            if ($id <= 0) return $this->response->setJSON(['success'=>false,'message'=>'ID tidak valid']);
            $sql = 'SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    iu.serial_number as serial_number_po,
                    iu.status_unit_id as status_unit,
                    COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                    iu.lokasi_unit,
                    iu.status_unit_id as status_unit_raw,
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
                /* purchase_orders & suppliers join dihapus untuk marketing detail */
                WHERE iu.id_inventory_unit = ?';
            $row = $this->db->query($sql, [$id])->getRowArray();
            if(!$row) return $this->response->setJSON(['success'=>false,'message'=>'Unit tidak ditemukan']);
            return $this->response->setJSON(['success'=>true,'data'=>$row,'csrf_hash'=>csrf_hash()]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success'=>false,
                'message'=>'Gagal mengambil detail lengkap: '.$e->getMessage(),
                'csrf_hash'=>csrf_hash()
            ]);
        }
    }

    // Placeholder views for Penawaran (Quotations), Booking, and SPK as requested
    public function penawaran()
    {
        return view('marketing/penawaran');
    }

    public function booking()
    {
        return view('marketing/booking');
    }

    public function spk()
    {
        return view('marketing/spk', [
            'title' => 'Surat Perintah Kerja (SPK)'
        ]);
    }
    public function di()
    {
        return view('marketing/di', [
            'title' => 'Delivery Instructions (DI)'
        ]);
    }

    // Generate/download SPK PDF (server-rendered HTML -> Dompdf)
    public function spkPdf($id)
    {
        $id = (int)$id;
        $row = $this->spkModel->find($id);
        if (!$row) {
            return $this->response->setStatusCode(404)->setBody('SPK tidak ditemukan');
        }
        // Enrich spesifikasi similar to spkDetail
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
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            $enriched['selected'] = $sel;
            if (!empty($sel['unit_id'])) {
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
            if (!empty($sel['inventory_attachment_id'])) {
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
        $html = view('marketing/spk_pdf', ['spk'=>$row, 'spesifikasi'=>$enriched]);
        try {
            if (!class_exists('\\Dompdf\\Dompdf')) {
                return redirect()->to(base_url('marketing/spk/print/'.$id));
            }
            $optClass = '\\Dompdf\\Options';
            $domClass = '\\Dompdf\\Dompdf';
            $options = new $optClass();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $dompdf = new $domClass($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $filenameCore = $row['po_kontrak_nomor'] ?? $row['nomor_spk'] ?? ('SPK_'.$id);
            $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filenameCore).'.pdf';
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"')
                ->setBody($dompdf->output());
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setBody('Gagal membuat PDF: '.$e->getMessage());
        }
    }

    /** Render HTML print view for browser printing (no PDF lib required) */
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
        
        // Load kontrak_spesifikasi data (for Equipment section - data permintaan marketing)
        $kontrak_spec = null;
        if (!empty($row['kontrak_spesifikasi_id'])) {
            $kontrak_spec = $this->db->table('kontrak_spesifikasi ks')
                ->select('ks.*')
                ->select('tu.jenis as kontrak_jenis_unit, tu.tipe as kontrak_tipe_unit')
                ->select('k.kapasitas_unit as kontrak_kapasitas_name')
                ->select('d.nama_departemen as kontrak_departemen_name')
                ->select('tm.tipe_mast as kontrak_mast_name')
                ->select('jr.tipe_roda as kontrak_roda_name')
                ->select('tb.tipe_ban as kontrak_ban_name')
                ->select('v.jumlah_valve as kontrak_valve_name')
                ->select('chr.merk_charger as kontrak_merk_charger, chr.tipe_charger as kontrak_tipe_charger')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = ks.tipe_unit_id', 'left')
                ->join('kapasitas k', 'k.id_kapasitas = ks.kapasitas_id', 'left')
                ->join('departemen d', 'd.id_departemen = ks.departemen_id', 'left')
                ->join('tipe_mast tm', 'tm.id_mast = ks.mast_id', 'left')
                ->join('jenis_roda jr', 'jr.id_roda = ks.roda_id', 'left')
                ->join('tipe_ban tb', 'tb.id_ban = ks.ban_id', 'left')
                ->join('valve v', 'v.id_valve = ks.valve_id', 'left')
                ->join('charger chr', 'chr.id_charger = ks.charger_id', 'left')
                ->where('ks.id', $row['kontrak_spesifikasi_id'])
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
            
            // Format charger info if available
            if (!empty($kontrak_spec['kontrak_merk_charger']) || !empty($kontrak_spec['kontrak_tipe_charger'])) {
                $kontrak_spec['kontrak_charger_model'] = trim(($kontrak_spec['kontrak_merk_charger'] ?? '') . ' ' . ($kontrak_spec['kontrak_tipe_charger'] ?? ''));
            }
        }
        
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
        
        // Build prepared_units_detail (match Service controller behavior)
        $preparedUnits = [];
        if (!empty($enriched['prepared_units']) && is_array($enriched['prepared_units'])) {
            $preparedUnits = $enriched['prepared_units'];
        } elseif (!empty($spec['prepared_units']) && is_array($spec['prepared_units'])) {
            $preparedUnits = $spec['prepared_units'];
        }
        if (!empty($preparedUnits)) {
            $preparedDetails = [];
            foreach ($preparedUnits as $pu) {
                $uInfo = null; $aInfo = null; $bInfo = null; $cInfo = null;
                $unitLabel=''; $attLabel=''; $batLabel=''; $chrLabel='';
                
                // Load unit info
                if (!empty($pu['unit_id'])) {
                    $uInfo = $this->db->table('inventory_unit iu')
                        ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.lokasi_unit, mu.merk_unit, mu.model_unit, tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                        ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                        ->select('tm.tipe_mast as mast_name, jr.tipe_roda as roda_name, tb.tipe_ban as ban_name, v.jumlah_valve as valve_name')
                        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                        ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                        ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                        ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                        ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                        ->join('jenis_roda jr','jr.id_roda = iu.roda_id','left')
                        ->join('tipe_ban tb','tb.id_ban = iu.ban_id','left')
                        ->join('valve v','v.id_valve = iu.valve_id','left')
                        ->where('iu.id_inventory_unit', $pu['unit_id'])
                        ->get()->getRowArray();
                    if ($uInfo) {
                        $unitLabel = trim(($uInfo['no_unit'] ?: '-') . ' - ' . ($uInfo['merk_unit'] ?: '-') . ' ' . ($uInfo['model_unit'] ?: '') . ' @ ' . ($uInfo['lokasi_unit'] ?: '-'));
                    }
                }
                
                // Load attachment info
                if (!empty($pu['attachment_inventory_id'])) {
                    $aInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan, att.tipe, att.merk, att.model')
                        ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                        ->where('ia.id_inventory_attachment', $pu['attachment_inventory_id'])
                        ->where('ia.tipe_item', 'attachment')
                        ->get()->getRowArray();
                    if ($aInfo) {
                        $attLabel = trim(($aInfo['tipe'] ?: '-') . ' ' . ($aInfo['merk'] ?: '') . ' ' . ($aInfo['model'] ?: ''));
                        $suf = [];
                        if (!empty($aInfo['sn_attachment'])) $suf[] = 'SN: '.$aInfo['sn_attachment'];
                        if (!empty($aInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$aInfo['lokasi_penyimpanan'];
                        if ($suf) $attLabel .= ' ['.implode(', ', $suf).']';
                    }
                }
                
                // Load battery info
                if (!empty($pu['battery_inventory_id'])) {
                    $bInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_baterai, ia.lokasi_penyimpanan, bat.merk_baterai, bat.tipe_baterai, bat.jenis_baterai')
                        ->join('baterai bat','bat.id = ia.baterai_id','left')
                        ->where('ia.id_inventory_attachment', $pu['battery_inventory_id'])
                        ->where('ia.tipe_item', 'battery')
                        ->get()->getRowArray();
                    if ($bInfo) {
                        $batLabel = trim(($bInfo['merk_baterai'] ?: '-') . ' ' . ($bInfo['tipe_baterai'] ?: '') . ' ' . ($bInfo['jenis_baterai'] ?: ''));
                        $suf = [];
                        if (!empty($bInfo['sn_baterai'])) $suf[] = 'SN: '.$bInfo['sn_baterai'];
                        if (!empty($bInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$bInfo['lokasi_penyimpanan'];
                        if ($suf) $batLabel .= ' ['.implode(', ', $suf).']';
                    }
                }
                
                // Load charger info  
                if (!empty($pu['charger_inventory_id'])) {
                    $cInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_charger, ia.lokasi_penyimpanan, chr.merk_charger, chr.tipe_charger')
                        ->join('charger chr','chr.id_charger = ia.charger_id','left')
                        ->where('ia.id_inventory_attachment', $pu['charger_inventory_id'])
                        ->where('ia.tipe_item', 'charger')
                        ->get()->getRowArray();
                    if ($cInfo) {
                        $chrLabel = trim(($cInfo['merk_charger'] ?: '-') . ' ' . ($cInfo['tipe_charger'] ?: ''));
                        $suf = [];
                        if (!empty($cInfo['sn_charger'])) $suf[] = 'SN: '.$cInfo['sn_charger'];
                        if (!empty($cInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$cInfo['lokasi_penyimpanan'];
                        if ($suf) $chrLabel .= ' ['.implode(', ', $suf).']';
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
                    'jenis_unit' => $uInfo['jenis_unit'] ?? '',
                    'kapasitas_name' => $uInfo['kapasitas_name'] ?? '',
                    'departemen_name' => $uInfo['departemen_name'] ?? '',
                    'mast_id_name' => $uInfo['mast_name'] ?? '',
                    'roda_id_name' => $uInfo['roda_name'] ?? '',
                    'ban_id_name' => $uInfo['ban_name'] ?? '',
                    'valve_id_name' => $uInfo['valve_name'] ?? '',
                    'attachment_inventory_id' => $pu['attachment_inventory_id'] ?? null,
                    'attachment_label' => $attLabel,
                    'sn_attachment_formatted' => !empty($aInfo['sn_attachment']) ? 
                        trim(($aInfo['tipe'] ?? '') . ' ' . ($aInfo['merk'] ?? '') . ' ' . ($aInfo['model'] ?? '')) . ' (SN: ' . $aInfo['sn_attachment'] . ')' : 
                        trim(($aInfo['tipe'] ?? '') . ' ' . ($aInfo['merk'] ?? '') . ' ' . ($aInfo['model'] ?? '')),
                    'battery_inventory_id' => $pu['battery_inventory_id'] ?? null,
                    'battery_label' => $batLabel,
                    'sn_baterai_formatted' => !empty($bInfo['sn_baterai']) ? 
                        trim(($bInfo['merk_baterai'] ?? '') . ' ' . ($bInfo['tipe_baterai'] ?? '') . ' ' . ($bInfo['jenis_baterai'] ?? '')) . ' (SN: ' . $bInfo['sn_baterai'] . ')' : 
                        trim(($bInfo['merk_baterai'] ?? '') . ' ' . ($bInfo['tipe_baterai'] ?? '') . ' ' . ($bInfo['jenis_baterai'] ?? '')),
                    'charger_inventory_id' => $pu['charger_inventory_id'] ?? null,
                    'charger_label' => $chrLabel,
                    'sn_charger_formatted' => !empty($cInfo['sn_charger']) ? 
                        trim(($cInfo['merk_charger'] ?? '') . ' ' . ($cInfo['tipe_charger'] ?? '')) . ' (SN: ' . $cInfo['sn_charger'] . ')' : 
                        trim(($cInfo['merk_charger'] ?? '') . ' ' . ($cInfo['tipe_charger'] ?? '')),
                    'mekanik' => $pu['mekanik'] ?? '',
                    'aksesoris' => $pu['aksesoris_tersedia'] ?? $pu['aksesoris'] ?? '',
                    'catatan' => $pu['catatan'] ?? '',
                    'timestamp' => $pu['timestamp'] ?? ''
                ];
            }
            $enriched['prepared_units_detail'] = $preparedDetails;
        }

        // Load unit data from approval workflow if available
        // Handle both Aset (stored as no_unit) and Non Aset (stored as id_inventory_unit)
        if (!empty($row['persiapan_unit_id']) && $row['persiapan_unit_id'] != '0') {
            // First try to find by no_unit (for Aset units)
            $u = $this->db->table('inventory_unit iu')
                    ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id')
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
                
            // If not found by no_unit, try by id_inventory_unit (for Non Aset units)
            if (!$u) {
                $u = $this->db->table('inventory_unit iu')
                        ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id')
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
                // Add battery and charger info to enriched data
                $enriched['baterai_model'] = $u['baterai_model'] ?? $enriched['baterai_model'] ?? '';
                $enriched['charger_model'] = $u['charger_model'] ?? $enriched['charger_model'] ?? '';
            }
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
        
        // Load battery and charger data from JSON spesifikasi (Electric department)
        if (!empty($spec['persiapan_battery_id'])) {
            $b = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_baterai, ia.lokasi_penyimpanan')
                ->select('bat.merk_baterai, bat.tipe_baterai, bat.jenis_baterai')
                ->join('baterai bat','bat.id = ia.baterai_id','left')
                ->where('ia.id_inventory_attachment', $spec['persiapan_battery_id'])
                ->where('ia.tipe_item', 'battery')
                ->get()->getRowArray();
                
            if ($b) {
                $enriched['selected']['battery'] = [
                    'id' => (int)$b['id_inventory_attachment'],
                    'merk_baterai' => $b['merk_baterai'] ?? null,
                    'tipe_baterai' => $b['tipe_baterai'] ?? null,
                    'jenis_baterai' => $b['jenis_baterai'] ?? null,
                    'sn_baterai' => $b['sn_baterai'] ?? null,
                    'lokasi_penyimpanan' => $b['lokasi_penyimpanan'] ?? null,
                    // Format: Merk Tipe Jenis (SN)
                    'sn_baterai_formatted' => !empty($b['sn_baterai']) ? 
                        trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')) . ' (SN: ' . $b['sn_baterai'] . ')' : 
                        trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')),
                ];
                
                // Override spesifikasi with battery data
                $enriched['jenis_baterai'] = trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? ''));
            }
        }
        
        if (!empty($spec['persiapan_charger_id'])) {
            $c = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_charger, ia.lokasi_penyimpanan')
                ->select('chr.merk_charger, chr.tipe_charger')
                ->join('charger chr','chr.id_charger = ia.charger_id','left')
                ->where('ia.id_inventory_attachment', $spec['persiapan_charger_id'])
                ->where('ia.tipe_item', 'charger')
                ->get()->getRowArray();
                
            if ($c) {
                $enriched['selected']['charger'] = [
                    'id' => (int)$c['id_inventory_attachment'],
                    'merk_charger' => $c['merk_charger'] ?? null,
                    'tipe_charger' => $c['tipe_charger'] ?? null,
                    'sn_charger' => $c['sn_charger'] ?? null,
                    'lokasi_penyimpanan' => $c['lokasi_penyimpanan'] ?? null,
                    // Format: Merk Tipe (SN)
                    'sn_charger_formatted' => !empty($c['sn_charger']) ? 
                        trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')) . ' (SN: ' . $c['sn_charger'] . ')' : 
                        trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')),
                ];
                
                // Override spesifikasi with charger data
                $enriched['charger_model'] = trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? ''));
            }
        }
        
        // Load attachment data from JSON spesifikasi if not loaded from fabrikasi_attachment_id
        if (empty($enriched['selected']['attachment']) && !empty($spec['fabrikasi_attachment_id'])) {
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $spec['fabrikasi_attachment_id'])
                ->where('ia.tipe_item', 'attachment')
                ->get()->getRowArray();
                
            if ($a) {
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'tipe' => $a['tipe'] ?? null,
                    'merk' => $a['merk'] ?? null,
                    'model' => $a['model'] ?? null,
                    'sn_attachment' => $a['sn_attachment'] ?? null,
                    'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    // Format: Tipe Merk Model (SN)
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? 
                        trim(($a['tipe'] ?? '') . ' ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? '')) . ' (SN: ' . $a['sn_attachment'] . ')' : 
                        trim(($a['tipe'] ?? '') . ' ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? '')),
                ];
                
                // Override spesifikasi with attachment data
                $enriched['attachment_tipe'] = trim(($a['tipe'] ?? '') . ' ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? ''));
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
        return view('marketing/print_spk', ['spk'=>$row, 'spesifikasi'=>$enriched, 'kontrak_spesifikasi'=>$kontrak_spec]);
    }



    // --- SPK Minimal APIs for integrated workflow ---
    public function spkList()
    {
    $data = $this->spkModel->orderBy('id','DESC')->findAll();
        return $this->response->setJSON(['data'=>$data,'csrf_hash'=>csrf_hash()]);
    }

    public function spkDetail($id)
    {
    $row = $this->spkModel->find((int)$id);
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
        // Enrich human-readable names for common IDs
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
        // Enrich selected items (unit & attachment) labels and details if present
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            $enriched['selected'] = $sel;
            // Unit label
            if (!empty($sel['unit_id'])) {
                $u = $this->unitModel
                    ->select('inventory_unit.no_unit, inventory_unit.serial_number, inventory_unit.tahun_unit, inventory_unit.lokasi_unit, inventory_unit.sn_mast, inventory_unit.sn_mesin, inventory_unit.sn_baterai, inventory_unit.sn_charger, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu','mu.id_model_unit = inventory_unit.model_unit_id','left')
                    ->where('inventory_unit.id_inventory_unit', (int)$sel['unit_id'])
                    ->first();
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
            // Attachment label from inventory_attachment
            if (!empty($sel['inventory_attachment_id'])) {
                $a = $this->attModel
                    ->select('a.tipe, a.merk, a.model, inventory_attachment.sn_attachment, inventory_attachment.lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = inventory_attachment.attachment_id','left')
                    ->where('inventory_attachment.id_inventory_attachment', (int)$sel['inventory_attachment_id'])
                    ->first();
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

        // Also expose prepared_units and enrich them as prepared_units_detail for multi-unit READY SPK
        $preparedUnits = [];
        if (!empty($spec['prepared_units']) && is_array($spec['prepared_units'])) {
            $preparedUnits = $spec['prepared_units'];
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
        // Get kontrak_spesifikasi data if available
        $kontrak_spec = null;
        if (!empty($row['kontrak_spesifikasi_id'])) {
            $kontrak_spec = $this->db->table('kontrak_spesifikasi')
                ->where('id', $row['kontrak_spesifikasi_id'])
                ->get()
                ->getRowArray();
                
            // Process aksesoris if it's stored as JSON
            if ($kontrak_spec && isset($kontrak_spec['aksesoris']) && is_string($kontrak_spec['aksesoris'])) {
                try {
                    $decoded_aksesoris = json_decode($kontrak_spec['aksesoris'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $kontrak_spec['aksesoris'] = $decoded_aksesoris;
                    }
                } catch (\Exception $e) {
                    // Keep as string if parsing fails
                }
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $row,
            'spesifikasi' => $enriched,
            'prepared_units' => $preparedUnits,
            'prepared_units_detail' => $enriched['prepared_units_detail'] ?? [],
            'kontrak_spec' => $kontrak_spec,
            'csrf_hash' => csrf_hash()
        ]);
    }

    // Provide kontrak options (Pending) for searchable dropdown
    public function kontrakOptions()
    {
        $q = trim($this->request->getGet('q') ?? '');
        $status = trim($this->request->getGet('status') ?? 'Pending');
        $builder = $this->kontrakModel
            ->select('kontrak.id, kontrak.no_kontrak, kontrak.no_po_marketing, kontrak.pelanggan, kontrak.lokasi')
            ->join('kontrak_spesifikasi ks', 'ks.kontrak_id = kontrak.id', 'inner')
            ->whereIn('kontrak.status', ['Aktif', 'Pending'])
            ->groupBy('kontrak.id, kontrak.no_kontrak, kontrak.no_po_marketing, kontrak.pelanggan, kontrak.lokasi');
        
        if ($q !== '') {
            $builder->groupStart()
                ->like('kontrak.no_kontrak', $q)
                ->orLike('kontrak.no_po_marketing', $q)
                ->orLike('kontrak.pelanggan', $q)
            ->groupEnd();
        }
        $rows = $builder->orderBy('kontrak.dibuat_pada', 'DESC')->limit(20)->get()->getResultArray();
        
        // map to simple text for display if needed
        $options = array_map(function($r){
            $label = trim(($r['no_kontrak'] ?: '') . ' ' . ($r['no_po_marketing'] ? '(' . $r['no_po_marketing'] . ')' : '') . ' - ' . ($r['pelanggan'] ?: '-'));
            return [
                'id' => (int)$r['id'],
                'no_kontrak' => $r['no_kontrak'],
                'no_po_marketing' => $r['no_po_marketing'],
                'pelanggan' => $r['pelanggan'],
                'lokasi' => $r['lokasi'],
                'label' => $label
            ];
        }, $rows);
        return $this->response->setJSON(['data'=>$options,'csrf_hash'=>csrf_hash()]);
    }

    // Monitoring: Kontrak → SPK status (simple aggregation)
    public function spkMonitoring()
    {
        $sql = "SELECT k.id, k.no_kontrak, k.no_po_marketing, k.pelanggan, k.lokasi,
                   COUNT(s.id) AS total_spk,
                   SUM(CASE WHEN s.status = 'SUBMITTED' THEN 1 ELSE 0 END) AS submitted,
                   SUM(CASE WHEN s.status = 'IN_PROGRESS' THEN 1 ELSE 0 END) AS in_progress,
                   SUM(CASE WHEN s.status = 'READY' THEN 1 ELSE 0 END) AS ready,
                   SUM(CASE WHEN s.status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed,
                   SUM(CASE WHEN s.status = 'DELIVERED' THEN 1 ELSE 0 END) AS delivered,
                   SUM(CASE WHEN s.status = 'CANCELLED' THEN 1 ELSE 0 END) AS cancelled,
                   MAX(s.diperbarui_pada) AS last_update
            FROM kontrak k
            LEFT JOIN spk s ON (s.po_kontrak_nomor = k.no_kontrak OR s.po_kontrak_nomor = k.no_po_marketing)
            GROUP BY k.id
            ORDER BY k.id DESC
            LIMIT 100";
        $rows = $this->db->query($sql)->getResultArray();
        return $this->response->setJSON(['data'=>$rows, 'csrf_hash'=>csrf_hash()]);
    }

    // List DIs for marketing page
    public function diList()
    {
        $rows = $this->diModel
            ->select('delivery_instructions.*, spk.pic as spk_pic, spk.kontak as spk_kontak')
            ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
            ->orderBy('delivery_instructions.id','DESC')
            ->findAll();
        return $this->response->setJSON(['data'=>$rows,'csrf_hash'=>csrf_hash()]);
    }

    // Detailed DI info (for Marketing view)
    public function diDetail($id)
    {
    $di = $this->diModel->find((int)$id);
        if (!$di) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'DI tidak ditemukan']);
        }
        // Related SPK (optional)
        $spk = null;
        if (!empty($di['spk_id'])) {
            $spk = $this->spkModel->find((int)$di['spk_id']);
        }
        // Items
        $items = $this->diItemModel
            ->select('delivery_items.*, iu.no_unit, mu.merk_unit, mu.model_unit, a2.tipe as att_tipe, a2.merk as att_merk, a2.model as att_model')
            ->join('inventory_unit iu','iu.id_inventory_unit = delivery_items.unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('attachment a2', 'a2.id_attachment = delivery_items.attachment_id', 'left')
            ->where('delivery_items.di_id',(int)$id)
            ->findAll();
        // Format labels
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

    // Options: SPK that are READY for DI creation
    public function spkReadyOptions()
    {
        $q = trim($this->request->getGet('q') ?? '');
    $b = $this->spkModel->select('id, nomor_spk, po_kontrak_nomor, pelanggan, lokasi')->where('status','READY');
        if ($q !== '') {
            $b->groupStart()
                ->like('nomor_spk',$q)
                ->orLike('po_kontrak_nomor',$q)
                ->orLike('pelanggan',$q)
            ->groupEnd();
        }
        $rows = $b->orderBy('id','DESC')->limit(50)->get()->getResultArray();
        $opts = array_map(function($r){
            return [
                'id' => (int)$r['id'],
                'label' => trim(($r['nomor_spk'] ?: '-') . ' - ' . ($r['po_kontrak_nomor'] ?: '-') . ' - ' . ($r['pelanggan'] ?: '-')),
                'pelanggan' => $r['pelanggan'] ?: '',
                'lokasi' => $r['lokasi'] ?: '',
                'po' => $r['po_kontrak_nomor'] ?: '',
                'nomor_spk' => $r['nomor_spk'] ?: '',
            ];
        }, $rows);
        return $this->response->setJSON(['data'=>$opts,'csrf_hash'=>csrf_hash()]);
    }

    public function spkCreate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $this->db->transBegin();

        try {
            // Log all received POST data for debugging
            log_message('info', 'Marketing::spkCreate - Received POST data: ' . json_encode($this->request->getPost()));
            
            // Check if this is new workflow with kontrak_spesifikasi_id or kontrak_id
            $kontrakSpesifikasiId = $this->request->getPost('kontrak_spesifikasi_id');
            $kontrakId = $this->request->getPost('kontrak_id');
            $jumlahUnit = (int)($this->request->getPost('jumlah_unit') ?: 1);

            log_message('info', 'Marketing::spkCreate - kontrak_spesifikasi_id: ' . $kontrakSpesifikasiId);
            log_message('info', 'Marketing::spkCreate - kontrak_id: ' . $kontrakId);
            log_message('info', 'Marketing::spkCreate - jumlah_unit: ' . $jumlahUnit);

            if ($kontrakSpesifikasiId && $kontrakSpesifikasiId > 0) {
                // New workflow: Create SPK based on contract specification
                log_message('info', 'Marketing::spkCreate - Using specification workflow for kontrak_spesifikasi_id: ' . $kontrakSpesifikasiId);
                $spesifikasi = $this->kontrakSpesifikasiModel->find($kontrakSpesifikasiId);
                log_message('info', 'Marketing::spkCreate - Found spesifikasi: ' . json_encode($spesifikasi));
                if (!$spesifikasi) {
                    throw new \Exception('Spesifikasi kontrak tidak ditemukan.');
                }

                // Check if enough units are needed
                $available = $spesifikasi['jumlah_dibutuhkan'] - $spesifikasi['jumlah_tersedia'];
                if ($jumlahUnit > $available) {
                    throw new \Exception("Jumlah unit melebihi yang dibutuhkan. Maksimal: {$available} unit");
                }

                // Get contract info
                $kontrak = $this->kontrakModel->find($spesifikasi['kontrak_id']);
                if (!$kontrak) {
                    throw new \Exception('Kontrak tidak ditemukan.');
                }

                // Build specification array from kontrak_spesifikasi
                $spec = [
                    'departemen_id' => $spesifikasi['departemen_id'],
                    'tipe_unit_id' => $spesifikasi['tipe_unit_id'],
                    'tipe_jenis' => $spesifikasi['tipe_jenis'],
                    'merk_unit' => $spesifikasi['merk_unit'],
                    'model_unit' => $spesifikasi['model_unit'],
                    'kapasitas_id' => $spesifikasi['kapasitas_id'],
                    'attachment_tipe' => $spesifikasi['attachment_tipe'],
                    'attachment_merk' => $spesifikasi['attachment_merk'],
                    'jenis_baterai' => $spesifikasi['jenis_baterai'],
                    'charger_id' => $spesifikasi['charger_id'],
                    'mast_id' => $spesifikasi['mast_id'],
                    'ban_id' => $spesifikasi['ban_id'],
                    'roda_id' => $spesifikasi['roda_id'],
                    'valve_id' => $spesifikasi['valve_id'],
                    'aksesoris' => []
                ];

                $payload = [
                    'nomor_spk' => method_exists($this->spkModel,'generateNextNumber') ? $this->spkModel->generateNextNumber() : $this->generateSpkNumber(),
                    'jenis_spk' => 'UNIT',
                    'kontrak_id' => $kontrak['id'],
                    'kontrak_spesifikasi_id' => $kontrakSpesifikasiId,
                    'jumlah_unit' => $jumlahUnit,
                    'po_kontrak_nomor' => $kontrak['no_kontrak'],
                    'pelanggan' => $this->request->getPost('pelanggan') ?: $kontrak['pelanggan'],
                    'pic' => $this->request->getPost('pic') ?: $kontrak['pic'],
                    'kontak' => $this->request->getPost('kontak') ?: $kontrak['kontak'],
                    'lokasi' => $this->request->getPost('lokasi') ?: $kontrak['lokasi'],
                    'delivery_plan' => $this->request->getPost('delivery_plan') ?: null,
                    'spesifikasi' => json_encode($spec),
                    'catatan' => $this->request->getPost('catatan') ?: null,
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?: 1,
                    'dibuat_pada' => date('Y-m-d H:i:s')
                ];

            } elseif ($kontrakId && $kontrakId > 0) {
                // Fallback: Create SPK based on contract ID only (when no specification is selected or kontrak_spesifikasi_id is 0)
                log_message('info', 'Marketing::spkCreate - Using kontrak_id fallback workflow for kontrak: ' . $kontrakId);
                $kontrak = $this->kontrakModel->find($kontrakId);
                log_message('info', 'Marketing::spkCreate - Found kontrak: ' . json_encode($kontrak));
                if (!$kontrak) {
                    throw new \Exception('Kontrak tidak ditemukan.');
                }

                // Get first available specification for this contract, or create basic spec
                $spesifikasiList = $this->kontrakSpesifikasiModel->getByKontrakId($kontrakId);
                log_message('info', 'Marketing::spkCreate - Found ' . count($spesifikasiList) . ' specifications for kontrak ' . $kontrakId);
                
                $firstSpecId = null;
                if (!empty($spesifikasiList)) {
                    log_message('info', 'Marketing::spkCreate - First spec sample: ' . json_encode($spesifikasiList[0]));
                }
                $spec = [];
                
                if (!empty($spesifikasiList)) {
                    // Use the first specification as template
                    $firstSpec = $spesifikasiList[0];
                    log_message('info', 'Marketing::spkCreate - Using first spec as template: ' . json_encode($firstSpec));
                    $firstSpecId = $firstSpec['id']; // Store the ID for kontrak_spesifikasi_id
                    $spec = [
                        'departemen_id' => $firstSpec['departemen_id'] ?? null,
                        'tipe_unit_id' => $firstSpec['tipe_unit_id'] ?? null,
                        'tipe_jenis' => $firstSpec['tipe_jenis'] ?? null,
                        'merk_unit' => $firstSpec['merk_unit'] ?? null,
                        'model_unit' => $firstSpec['model_unit'] ?? null,
                        'kapasitas_id' => $firstSpec['kapasitas_id'] ?? null,
                        'attachment_tipe' => $firstSpec['attachment_tipe'] ?? null,
                        'attachment_merk' => $firstSpec['attachment_merk'] ?? null,
                        'jenis_baterai' => $firstSpec['jenis_baterai'] ?? null,
                        'charger_id' => $firstSpec['charger_id'] ?? null,
                        'mast_id' => $firstSpec['mast_id'] ?? null,
                        'ban_id' => $firstSpec['ban_id'] ?? null,
                        'roda_id' => $firstSpec['roda_id'] ?? null,
                        'valve_id' => $firstSpec['valve_id'] ?? null,
                        'aksesoris' => []
                    ];
                } else {
                    log_message('info', 'Marketing::spkCreate - No specifications found for kontrak ' . $kontrakId . ', using empty spec');
                }

                $payload = [
                    'nomor_spk' => method_exists($this->spkModel,'generateNextNumber') ? $this->spkModel->generateNextNumber() : $this->generateSpkNumber(),
                    'jenis_spk' => 'UNIT',
                    'kontrak_id' => $kontrak['id'],
                    'kontrak_spesifikasi_id' => $firstSpecId, // Use the first spec ID if available
                    'jumlah_unit' => $jumlahUnit,
                    'po_kontrak_nomor' => $kontrak['no_kontrak'],
                    'pelanggan' => $this->request->getPost('pelanggan') ?: $kontrak['pelanggan'],
                    'pic' => $this->request->getPost('pic') ?: $kontrak['pic'],
                    'kontak' => $this->request->getPost('kontak') ?: $kontrak['kontak'],
                    'lokasi' => $this->request->getPost('lokasi') ?: $kontrak['lokasi'],
                    'delivery_plan' => $this->request->getPost('delivery_plan') ?: null,
                    'spesifikasi' => json_encode($spec),
                    'catatan' => $this->request->getPost('catatan') ?: null,
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?: 1,
                    'dibuat_pada' => date('Y-m-d H:i:s')
                ];

            } else {
                // Legacy workflow: manual specification input
                $spec = $this->request->getPost('spesifikasi') ?? [];
                if (isset($spec['aksesoris']) && is_string($spec['aksesoris'])) {
                    $decoded = json_decode($spec['aksesoris'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $spec['aksesoris'] = $decoded;
                    }
                }

                $jenis = strtoupper(trim((string)$this->request->getPost('jenis_spk') ?: 'UNIT'));
                $allowedJenis = ['UNIT','ATTACHMENT','TUKAR'];
                if (!in_array($jenis, $allowedJenis, true)) { $jenis = 'UNIT'; }

                $payload = [
                    'nomor_spk' => method_exists($this->spkModel,'generateNextNumber') ? $this->spkModel->generateNextNumber() : $this->generateSpkNumber(),
                    'jenis_spk' => $jenis,
                    'po_kontrak_nomor' => $this->request->getPost('po_kontrak_nomor') ?: null,
                    'pelanggan' => $this->request->getPost('pelanggan') ?: '',
                    'pic' => $this->request->getPost('pic') ?: null,
                    'kontak' => $this->request->getPost('kontak') ?: null,
                    'lokasi' => $this->request->getPost('lokasi') ?: null,
                    'delivery_plan' => $this->request->getPost('delivery_plan') ?: null,
                    'spesifikasi' => json_encode($spec),
                    'catatan' => $this->request->getPost('catatan') ?: null,
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?: 1,
                    'dibuat_pada' => date('Y-m-d H:i:s')
                ];
            }

            // Insert SPK with robust success detection
            log_message('info', 'Marketing::spkCreate - About to insert SPK');
            log_message('info', 'Marketing::spkCreate - Payload: ' . json_encode($payload));
            
            // Check if SPK number already exists before insert
            $existingSpk = $this->db->table('spk')->where('nomor_spk', $payload['nomor_spk'])->get()->getRow();
            if ($existingSpk) {
                log_message('error', 'Marketing::spkCreate - SPK number already exists: ' . $payload['nomor_spk'] . ' with ID: ' . $existingSpk->id);
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nomor SPK sudah digunakan. Silakan refresh halaman dan coba lagi.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            $insertResult = $this->spkModel->insert($payload);
            log_message('info', 'Marketing::spkCreate - Insert result: ' . json_encode($insertResult));
            
            // Commit transaction immediately after insert
            $this->db->transCommit();
            log_message('info', 'Marketing::spkCreate - Transaction committed');
            
            // Get the insert ID AFTER committing the transaction
            $insertedId = $this->spkModel->getInsertID();
            log_message('info', 'Marketing::spkCreate - getInsertID() result: ' . $insertedId);
            log_message('info', 'Marketing::spkCreate - getInsertID() type: ' . gettype($insertedId));
            
            // Also try getting the last insert ID from the database connection
            $dbInsertId = $this->db->insertID();
            log_message('info', 'Marketing::spkCreate - db->insertID() result: ' . $dbInsertId);
            
            // Test database connection after commit
            $testQuery = $this->db->table('spk')->countAll();
            log_message('info', 'Marketing::spkCreate - Database connection test after commit: ' . $testQuery . ' records found');
            
            // Verify the inserted record using the insert ID
            if ($insertedId && $insertedId > 0) {
                $spkId = $insertedId;
                log_message('info', 'Marketing::spkCreate - Using getInsertID() result: ' . $spkId);
                
                // Double-check by querying the database
                $verifyQuery = $this->db->table('spk')->where('id', $spkId)->get();
                if ($verifyQuery->getNumRows() > 0) {
                    $verified = $verifyQuery->getRow();
                    log_message('info', 'Marketing::spkCreate - Verification successful: ID=' . $verified->id . ', nomor_spk=' . $verified->nomor_spk);
                } else {
                    log_message('error', 'Marketing::spkCreate - Verification failed: Could not find record with ID=' . $spkId);
                }
            } else {
                // Fallback: try to find by nomor_spk
                log_message('info', 'Marketing::spkCreate - getInsertID() failed, trying fallback search by nomor_spk=' . $payload['nomor_spk']);
                $query = $this->db->table('spk')
                    ->where('nomor_spk', $payload['nomor_spk'])
                    ->get();
                
                log_message('info', 'Marketing::spkCreate - Fallback query executed, num_rows: ' . $query->getNumRows());
                
                if ($query->getNumRows() > 0) {
                    $inserted = $query->getRow();
                    $spkId = $inserted->id;
                    log_message('info', 'Marketing::spkCreate - Fallback successful, found ID: ' . $spkId);
                } else {
                    log_message('error', 'Marketing::spkCreate - Fallback failed: Could not find inserted SPK record');
                    $spkId = null;
                }
            }

            if ($spkId) {
                log_message('info', 'Marketing::spkCreate - SPK berhasil dibuat dengan ID: ' . $spkId);
                
                // Verify the data was actually inserted
                $insertedSpk = $this->spkModel->find($spkId);
                log_message('info', 'Marketing::spkCreate - Verification - Inserted SPK data: ' . json_encode($insertedSpk));
                
                // Notify Service team
                $this->sendSpkNotification($payload['nomor_spk']);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'SPK berhasil dibuat',
                    'nomor' => $payload['nomor_spk'],
                    'spk_id' => $spkId,
                    'inserted_data' => $insertedSpk,
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                // Get detailed error information
                $errors = $this->spkModel->errors();
                $dbError = $this->db->error();
                
                // Try to get more detailed error info
                $lastQuery = $this->db->getLastQuery();
                $errorCode = $dbError['code'] ?? 0;
                $errorMessage = $dbError['message'] ?? '';
                
                log_message('error', 'Marketing::spkCreate - Insert result: ' . json_encode($insertResult));
                log_message('error', 'Marketing::spkCreate - Could not find inserted SPK record');
                log_message('error', 'Marketing::spkCreate - Model validation errors: ' . json_encode($errors));
                log_message('error', 'Marketing::spkCreate - Database error: ' . json_encode($dbError));
                log_message('error', 'Marketing::spkCreate - Last query: ' . $lastQuery);
                log_message('error', 'Marketing::spkCreate - Payload data: ' . json_encode($payload));
                
                // Check if there are any SPK records at all
                $totalSpk = $this->db->table('spk')->countAll();
                log_message('error', 'Marketing::spkCreate - Total SPK records in database: ' . $totalSpk);
                
                // Check if the SPK number already exists
                $existingSpk = $this->db->table('spk')->where('nomor_spk', $payload['nomor_spk'])->get()->getRow();
                if ($existingSpk) {
                    log_message('error', 'Marketing::spkCreate - SPK number already exists: ' . json_encode($existingSpk));
                }
                
                // Construct a more informative error message
                $errorMsg = 'Gagal membuat SPK.';
                
                if (!empty($errors)) {
                    $errorMsg .= ' Validation errors: ' . implode(', ', $errors);
                } elseif (!empty($errorMessage)) {
                    $errorMsg .= ' Database error: ' . $errorMessage;
                } elseif ($errorCode > 0) {
                    $errorMsg .= ' Database error code: ' . $errorCode;
                } elseif ($insertResult === false) {
                    $errorMsg .= ' Insert failed.';
                } else {
                    $errorMsg .= ' Could not verify SPK was saved.';
                }
                
                // Rollback transaction since verification failed
                $this->db->transRollback();
                log_message('error', 'Marketing::spkCreate - Transaction rolled back due to verification failure');
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg,
                    'validation_errors' => $errors,
                    'db_error' => $dbError,
                    'debug_info' => [
                        'last_query' => $lastQuery,
                        'db_error_code' => $errorCode,
                        'db_error_message' => $errorMessage,
                        'insert_result' => $insertResult,
                        'searched_nomor_spk' => $payload['nomor_spk'],
                        'total_spk_records' => $totalSpk,
                        'existing_spk' => $existingSpk ? 'YES' : 'NO'
                    ],
                    'csrf_hash' => csrf_hash()
                ]);
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    private function sendSpkNotification($nomorSpk)
    {
        try {
            // Ensure notifications table exists
            if (!$this->db->tableExists('notifications')) {
                return; // Skip if notifications not available
            }

            $dataNotif = [
                'title' => 'SPK Baru',
                'message' => 'SPK ' . $nomorSpk . ' diajukan oleh Marketing untuk diproses Service.',
                'type' => 'info',
                'user_id' => null,
                'url' => base_url('service/spk_service'),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Check if target_role column exists
            $hasTargetRole = false;
            try {
                $this->db->query('SELECT target_role FROM notifications LIMIT 1');
                $hasTargetRole = true;
            } catch (\Throwable $e) { 
                $hasTargetRole = false; 
            }

            if ($hasTargetRole) { 
                $dataNotif['target_role'] = 'service'; 
            }

            if ($this->notifModel) {
                $this->notifModel->insert($dataNotif);
            } else {
                $this->db->table('notifications')->insert($dataNotif);
            }
        } catch (\Throwable $e) {
            // Silent fail; notifications are optional
        }
    }

    // Generic options endpoint for SPK specifications
    public function specOptions()
    {
        $type = trim($this->request->getGet('type') ?? '');
        // Predefined simple maps
        $map = [
            'departemen'      => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen','order'=>'nama_departemen'],
            'tipe_unit'       => null, // Special handling for DISTINCT tipe
            'jenis_unit'      => null, // DISTINCT jenis from tipe_unit filtered by tipe_unit_id
            'kapasitas'       => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit','order'=>'kapasitas_unit'],
            'mast'            => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast','order'=>'tipe_mast'],
            'ban'             => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban','order'=>'tipe_ban'],
            'charger'         => ['table'=>'charger','id'=>'id_charger','name'=>"CONCAT(merk_charger, ' - ', tipe_charger)",'order'=>'merk_charger'],
            'baterai'         => ['table'=>'baterai','id'=>'id','name'=>"CONCAT(merk_baterai, ' ', tipe_baterai)",'order'=>'merk_baterai'],
            'attachment'      => ['table'=>'attachment','id'=>'id_attachment','name'=>"CONCAT(merk, ' ', model)",'order'=>'merk'],
            'attachment_merk' => null, // DISTINCT merk from attachment
            // New simplified request types
            'tipe_jenis'      => null, // DISTINCT jenis from tipe_unit
            'merk_unit'       => null, // DISTINCT merk_unit from model_unit
            'valve'           => null, // valve.id_valve, valve.jumlah_valve
            'jenis_baterai'   => null, // DISTINCT jenis_baterai from baterai
            'attachment_tipe' => null, // DISTINCT tipe from attachment
            'roda'            => null, // jenis_roda.id_roda, jenis_roda.tipe_roda
        ];

        // Handle special DISTINCT cases and departmental filtering
        if ($type === 'tipe_unit') {
            // Get DISTINCT tipe names with MIN(id) to avoid duplicates in UI
            $rows = $this->db->table('tipe_unit')
                ->select('MIN(id_tipe_unit) as id, TRIM(tipe) as name', false)
                ->where('tipe IS NOT NULL', null, false)
                ->where("TRIM(tipe) <> ''", null, false)
                ->groupBy('TRIM(tipe)')
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }

        if ($type === 'jenis_unit') {
            $tipeUnit = trim($this->request->getGet('parent_tipe') ?? '');
            $builder = $this->db->table('tipe_unit')
                ->select('DISTINCT TRIM(jenis) as name', false)
                ->where('jenis IS NOT NULL', null, false)
                ->where("TRIM(jenis) <> ''", null, false);
            
            if ($tipeUnit !== '') {
                $builder->where('tipe', $tipeUnit);
            }
            
            $rows = $builder->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            
            // map id = name for simple string options
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }

        // Check departemen for baterai/charger locking
        if ($type === 'baterai' || $type === 'charger') {
            $departemenId = trim($this->request->getGet('departemen_id') ?? '');
            
            // Check if departemen is Electric
            $isElectric = false;
            if ($departemenId !== '') {
                $dept = $this->db->table('departemen')
                    ->select('nama_departemen')
                    ->where('id_departemen', $departemenId)
                    ->get()->getRowArray();
                
                if ($dept && (stripos($dept['nama_departemen'], 'electric') !== false || 
                             stripos($dept['nama_departemen'], 'listrik') !== false)) {
                    $isElectric = true;
                }
            }
            
            // If not electric, return empty array
            if (!$isElectric) {
                return $this->response->setJSON(['success'=>true,'data'=>[],'csrf_hash'=>csrf_hash()]);
            }
        }

        if ($type === 'tipe_jenis') {
            $rows = $this->db->table('tipe_unit')
                ->select('DISTINCT TRIM(jenis) as name', false)
        ->where('jenis IS NOT NULL', null, false)
                ->where("TRIM(jenis) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            // map id = name for simple string options
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'merk_unit') {
            $rows = $this->db->table('model_unit')
                ->select('DISTINCT TRIM(merk_unit) as name', false)
        ->where('merk_unit IS NOT NULL', null, false)
                ->where("TRIM(merk_unit) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'valve') {
            $rows = $this->db->table('valve')
                ->select('id_valve as id, jumlah_valve as name')
                ->orderBy('jumlah_valve','ASC')
                ->limit(200)
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
    if ($type === 'jenis_baterai') {
            $rows = $this->db->table('baterai')
                ->select('DISTINCT TRIM(jenis_baterai) as name', false)
        ->where('jenis_baterai IS NOT NULL', null, false)
                ->where("TRIM(jenis_baterai) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'attachment_tipe') {
            $rows = $this->db->table('attachment')
                ->select('DISTINCT TRIM(tipe) as name', false)
        ->where('tipe IS NOT NULL', null, false)
                ->where("TRIM(tipe) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'attachment_merk') {
            $rows = $this->db->table('attachment')
                ->select('DISTINCT TRIM(merk) as name', false)
                ->where('merk IS NOT NULL', null, false)
                ->where("TRIM(merk) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'roda') {
            $rows = $this->db->table('jenis_roda')
                ->select('id_roda as id, tipe_roda as name')
                ->orderBy('tipe_roda','ASC')
                ->limit(200)
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }

        // Fallback to table/column map for legacy/spec detail options
        if (!isset($map[$type])) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Unknown type','csrf_hash'=>csrf_hash()]);
        }
        $cfg = $map[$type];
        
        // Special handling for charger based on departemen filtering
        if ($type === 'charger') {
            $departemenId = trim($this->request->getGet('departemen_id') ?? '');
            
            // Check if departemen is Electric first
            $isElectric = false;
            if ($departemenId !== '') {
                $dept = $this->db->table('departemen')
                    ->select('nama_departemen')
                    ->where('id_departemen', $departemenId)
                    ->get()->getRowArray();
                
                if ($dept && (stripos($dept['nama_departemen'], 'electric') !== false || 
                             stripos($dept['nama_departemen'], 'listrik') !== false)) {
                    $isElectric = true;
                }
            }
            
            // If not electric, return empty array
            if (!$isElectric) {
                return $this->response->setJSON(['success'=>true,'data'=>[],'csrf_hash'=>csrf_hash()]);
            }
        }
        
        $builder = $this->db->table($cfg['table'])
            ->select($cfg['id'].' as id')
            ->select($cfg['name'].' as name', false)
            ->orderBy($cfg['order'],'ASC')
            ->limit(200);
        $rows = $builder->get()->getResultArray();
        return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
    }

    public function spkUpdateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        $status = $this->request->getPost('status');
        $allowed = ['DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED'];
        if (!in_array($status,$allowed,true)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Status tidak valid']);
        }
        // Log status history (best-effort)
        $prev = $this->db->table('spk')->select('status')->where('id',$id)->get()->getRowArray();
        $this->db->table('spk')->where('id',$id)->update(['status'=>$status,'diperbarui_pada'=>date('Y-m-d H:i:s')]);
        if ($prev && isset($prev['status'])) {
            $this->db->table('spk_status_history')->insert([
                'spk_id' => (int)$id,
                'status_from' => $prev['status'],
                'status_to' => $status,
                'changed_by' => session('user_id') ?: 1,
                'note' => null,
                'changed_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return $this->response->setJSON(['success'=>true,'message'=>'Status diperbarui','csrf_hash'=>csrf_hash()]);
    }

    private function generateSpkNumber(): string
    {
        $prefix = 'SPK/'.date('Ym').'/';
        $last = $this->db->table('spk')->like('nomor_spk',$prefix)->orderBy('id','DESC')->get()->getRowArray();
        $seq = 1;
        if ($last && isset($last['nomor_spk'])) {
            $parts = explode('/', $last['nomor_spk']);
            $seq = isset($parts[2]) ? (int)$parts[2] + 1 : 1;
        }
        return $prefix . str_pad((string)$seq,3,'0',STR_PAD_LEFT);
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

    public function diCreate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        
        // Check what database we're actually connected to
        try {
            $dbConfig = $this->db->getDatabase();
            error_log('DI Create - Connected database: ' . $dbConfig);
            
            // Also check if we can actually talk to the DB by testing a simple query
            $testQuery = $this->db->query('SELECT VERSION() as version');
            $dbVersion = $testQuery->getRowArray();
            if ($dbVersion) {
                error_log('MySQL Version: ' . ($dbVersion['version'] ?? 'unknown'));
            }
        } catch (\Exception $e) {
            error_log('Database check error: ' . $e->getMessage());
        }
        
        // Debug logging
        error_log('DI Create Request - POST data: ' . print_r($this->request->getPost(), true));
        
        $spkId = (int)($this->request->getPost('spk_id') ?? 0);
        $poNo = trim((string)($this->request->getPost('po_kontrak_nomor') ?? ''));
        $tanggalKirim = $this->request->getPost('tanggal_kirim') ?: null;
        $catatan = $this->request->getPost('catatan') ?: null;

        $pelanggan = $this->request->getPost('pelanggan') ?: '';
        $lokasi = $this->request->getPost('lokasi') ?: null;

    // units selected for this DI (allow multiple)
    $unitIds = $this->request->getPost('unit_ids');
    if (is_string($unitIds)) { $unitIds = [$unitIds]; }
    if (!is_array($unitIds)) { $unitIds = []; }
    $unitIds = array_values(array_unique(array_filter(array_map('intval', $unitIds))));
    error_log('DI Create Parsed Inputs: spk_id=' . $spkId . ', po=' . $poNo . ', tanggal_kirim=' . ($tanggalKirim ?: '-') . ', unit_ids=' . json_encode($unitIds));

    $selected = ['unit_id'=>null,'inventory_attachment_id'=>null];
        if ($spkId > 0) {
            // Ensure SPK is READY (Service has assigned items)
            $spk = $this->db->table('spk')->where('id',$spkId)->get()->getRowArray();
            if (!$spk) {
                error_log("DI Create Error: SPK not found with ID: $spkId");
                return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
            }
            if ($spk['status'] !== 'READY') {
                error_log("DI Create Error: SPK status is '{$spk['status']}', not READY");
                return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'SPK belum READY']);
            }
            $poNo = $spk['po_kontrak_nomor'];
            $pelanggan = $spk['pelanggan'];
            $lokasi = $spk['lokasi'];
            
            // Extract prepared units from SPK spesifikasi if no units provided
            if (empty($unitIds) && !empty($spk['spesifikasi'])) {
                $spec = json_decode($spk['spesifikasi'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($spec)) {
                    // Get units from prepared_units array
                    if (isset($spec['prepared_units']) && is_array($spec['prepared_units'])) {
                        foreach ($spec['prepared_units'] as $preparedUnit) {
                            if (isset($preparedUnit['unit_id']) && is_numeric($preparedUnit['unit_id'])) {
                                $unitIds[] = (int)$preparedUnit['unit_id'];
                            }
                        }
                        error_log('DI Create - Extracted unit IDs from SPK prepared_units: ' . json_encode($unitIds));
                    }
                    
                    // Also check for legacy 'selected' format as fallback
                    if (empty($unitIds) && isset($spec['selected'])) {
                        $selected['unit_id'] = (int)($spec['selected']['unit_id'] ?? 0) ?: null;
                        $selected['inventory_attachment_id'] = (int)($spec['selected']['inventory_attachment_id'] ?? 0) ?: null;
                    }
                }
            }
        }

        if ($poNo === '') {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'PO/Kontrak wajib diisi']);
        }

        if (empty($pelanggan)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Nama pelanggan wajib diisi']);
        }

        $payload = [
            'nomor_di' => method_exists($this->diModel,'generateNextNumber') ? $this->diModel->generateNextNumber() : $this->generateDiNumber(),
            'spk_id' => $spkId ?: null,
            'po_kontrak_nomor' => $poNo,
            'pelanggan' => $pelanggan,
            'lokasi' => $lokasi,
            'status' => 'SUBMITTED',  // Use English status to match current database enum
            'dibuat_oleh' => session('user_id') ?: 1,
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ];
        
        // Only add optional fields if they have values
        if ($tanggalKirim) {
            $payload['tanggal_kirim'] = $tanggalKirim;
        }
        if ($catatan) {
            $payload['catatan'] = $catatan;
        }
        // Start manual transaction
        $this->db->transBegin();
        
        // Try the insert and catch any errors
        try {
            error_log('DI Create - About to insert payload: ' . json_encode($payload));
            $insertResult = $this->diModel->insert($payload);
            error_log('DI Create - Insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));
            
            if (!$insertResult) {
                $errors = $this->diModel->errors();
                error_log('DI Model Insert Errors: ' . print_r($errors, true));
                // Fallback to DB error if model errors are empty
                $dbError = $this->db->error();
                error_log('DI Insert DB Error after model failure: ' . print_r($dbError, true));
                $msg = '';
                if (!empty($errors)) {
                    $msg = 'Model validation failed: ' . implode(', ', $errors);
                } elseif (!empty($dbError['message'])) {
                    $msg = 'Database error: ' . $dbError['message'];
                    error_log('DI Insert DB Error: ' . print_r($dbError, true));
                } else {
                    // Try to get more detailed error information
                    $lastQuery = $this->db->getLastQuery();
                    error_log('DI Insert - Last Query: ' . ($lastQuery ? $lastQuery : 'No query available'));
                    $msg = 'Insert failed with no specific error message';
                }
                throw new \Exception($msg);
            }
            
            // Get the inserted DI ID - use the returned value from insert() if it's the ID, otherwise use getInsertID()
            $diId = $insertResult;
            if (!is_numeric($diId) || $diId <= 0) {
                $diId = (int)$this->diModel->getInsertID();
            }
            
            // Double-check that we have a valid DI ID
            if (!$diId || $diId <= 0) {
                error_log('DI Create - Failed to get valid DI ID after insert');
                throw new \Exception('Failed to retrieve DI ID after insertion');
            }
            
            error_log("DI Insert successful with ID: $diId");
        } catch (\Exception $e) {
            $dbError = $this->db->error();
            $lastQuery = method_exists($this->db, 'getLastQuery') ? (string)$this->db->getLastQuery() : '';
            error_log('DI Insert Exception: ' . ($e->getMessage() ?: '[empty message]'));
            if (!empty($dbError)) error_log('DB Error: ' . print_r($dbError, true));
            if ($lastQuery) error_log('Last Query: ' . $lastQuery);
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'success'=>false,
                'message'=>'Gagal membuat DI: ' . ($e->getMessage() ?: (!empty($dbError['message']) ? $dbError['message'] : 'Unknown error')),
                'debug' => [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                    'payload' => $payload,
                ],
                'csrf_hash'=>csrf_hash()
            ]);
        }
        // Insert delivery items: prefer explicit unit_ids from form (multiple),
        // fallback to selected items from SPK if none provided.
        try {
            error_log('DI Create - About to insert delivery items for unit_ids: ' . json_encode($unitIds));
            
            if (!empty($unitIds)) {
                foreach ($unitIds as $uid) {
                    error_log("DI Create - Processing unit ID: $uid");
                    
                    // Verify if unit exists before insertion
                    $unitExists = $this->db->table('inventory_unit')
                        ->where('id_inventory_unit', (int)$uid)
                        ->countAllResults();
                    
                    if (!$unitExists) {
                        throw new \Exception("Unit dengan ID {$uid} tidak ditemukan di inventory");
                    }
                    
                    $unitPayload = [
                        'di_id' => $diId,
                        'item_type' => 'UNIT',
                        'unit_id' => (int)$uid,
                    ];
                    
                    // Only add optional fields if they have values
                    // attachment_id and keterangan are nullable, so we can skip them if null
                    
                    error_log('DI Create - About to insert unit payload: ' . json_encode($unitPayload));
                    
                    // Try direct DB insert first to see if model is the issue
                    try {
                        $directResult = $this->db->table('delivery_items')->insert($unitPayload);
                        error_log('DI Create - Direct DB insert result: ' . ($directResult ? 'SUCCESS' : 'FAILED'));
                        
                        if (!$directResult) {
                            $dbError = $this->db->error();
                            error_log('DI Create - Direct DB insert error: ' . print_r($dbError, true));
                            throw new \Exception('Direct DB insert failed: ' . ($dbError['message'] ?? 'Unknown DB error'));
                        }
                        
                    } catch (\Exception $directEx) {
                        error_log('DI Create - Direct insert exception: ' . $directEx->getMessage());
                        
                        // Fallback to model insert
                        error_log('DI Create - Trying model insert as fallback...');
                        $itemResult = $this->diItemModel->insert($unitPayload);
                        error_log('DI Create - Model insert result: ' . ($itemResult ? 'SUCCESS ID='.$itemResult : 'FAILED'));
                        
                        if (!$itemResult) {
                            $errors = $this->diItemModel->errors();
                            $dbError = $this->db->error();
                            $lastQuery = method_exists($this->db, 'getLastQuery') ? (string)$this->db->getLastQuery() : '';
                            error_log('DI Create - Model insert failed. Model errors: ' . print_r($errors, true));
                            error_log('DI Create - DB error: ' . print_r($dbError, true));
                            error_log('DI Create - Last query: ' . $lastQuery);
                            throw new \Exception('Failed to insert unit item: ' . implode(', ', $errors) . ' | DB: ' . ($dbError['message'] ?? 'No DB error'));
                        }
                    }
                }
                
                // Also check for attachments from SPK prepared_units and main spec
                if ($spkId > 0 && !empty($spk['spesifikasi'])) {
                    $spec = json_decode($spk['spesifikasi'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($spec)) {
                        $attachmentInventoryIds = [];
                        
                        // Only collect attachments from selected units in prepared_units
                        if (isset($spec['prepared_units']) && is_array($spec['prepared_units'])) {
                            foreach ($spec['prepared_units'] as $preparedUnit) {
                                // Only include attachments if this unit is selected for the DI
                                if (isset($preparedUnit['unit_id']) && in_array((int)$preparedUnit['unit_id'], $unitIds)) {
                                    if (isset($preparedUnit['attachment_inventory_id']) && is_numeric($preparedUnit['attachment_inventory_id'])) {
                                        $attachmentInventoryIds[] = (int)$preparedUnit['attachment_inventory_id'];
                                    }
                                    if (isset($preparedUnit['battery_inventory_id']) && is_numeric($preparedUnit['battery_inventory_id'])) {
                                        $attachmentInventoryIds[] = (int)$preparedUnit['battery_inventory_id'];
                                    }
                                    if (isset($preparedUnit['charger_inventory_id']) && is_numeric($preparedUnit['charger_inventory_id'])) {
                                        $attachmentInventoryIds[] = (int)$preparedUnit['charger_inventory_id'];
                                    }
                                }
                            }
                        }
                        
                        // Remove duplicates
                        $attachmentInventoryIds = array_unique($attachmentInventoryIds);
                        
                        // Insert each attachment for selected units only
                        foreach ($attachmentInventoryIds as $attachmentInvId) {
                            // Get attachment details including tipe_item
                            $inv = $this->db->table('inventory_attachment')
                                ->select('attachment_id, baterai_id, charger_id, tipe_item')
                                ->where('id_inventory_attachment', $attachmentInvId)
                                ->get()->getRowArray();
                            
                            if ($inv) {
                                $targetId = null;
                                $keterangan = '';
                                
                                // Determine which ID to use based on tipe_item
                                switch ($inv['tipe_item']) {
                                    case 'attachment':
                                        $targetId = $inv['attachment_id'];
                                        $keterangan = 'Attachment for selected unit';
                                        break;
                                    case 'battery':
                                        $targetId = $inv['baterai_id'];
                                        $keterangan = 'Battery for selected unit';
                                        break;
                                    case 'charger':
                                        $targetId = $inv['charger_id'];
                                        $keterangan = 'Charger for selected unit';
                                        break;
                                }
                                
                                if ($targetId) {
                                    $attachmentPayload = [
                                        'di_id' => $diId,
                                        'item_type' => 'ATTACHMENT',
                                        'attachment_id' => $targetId,
                                        'keterangan' => $keterangan
                                    ];
                                    
                                    $itemResult = $this->diItemModel->insert($attachmentPayload);
                                    if (!$itemResult) {
                                        $errors = $this->diItemModel->errors();
                                        throw new \Exception("Failed to insert {$inv['tipe_item']} for selected unit: " . implode(', ', $errors));
                                    }
                                    error_log("DI Create - Added {$inv['tipe_item']} for selected unit: attachment_id=$targetId");
                                }
                            }
                        }
                    }
                }
            } else {
                if (!empty($selected['unit_id'])) {
                    $itemResult = $this->diItemModel->insert([
                        'di_id' => $diId,
                        'item_type' => 'UNIT',
                        'unit_id' => (int)$selected['unit_id'],
                    ]);
                    if (!$itemResult) {
                        $errors = $this->diItemModel->errors();
                        throw new \Exception('Failed to insert selected unit: ' . implode(', ', $errors));
                    }
                }
                if (!empty($selected['inventory_attachment_id'])) {
                    // Map inventory_attachment to attachment_id if needed
                    $inv = $this->db->table('inventory_attachment')->select('attachment_id')->where('id_inventory_attachment', (int)$selected['inventory_attachment_id'])->get()->getRowArray();
                    $attId = $inv['attachment_id'] ?? null;
                    if ($attId) {
                        $itemResult = $this->diItemModel->insert([
                            'di_id' => $diId,
                            'item_type' => 'ATTACHMENT',
                            'attachment_id' => $attId,
                        ]);
                        if (!$itemResult) {
                            $errors = $this->diItemModel->errors();
                            throw new \Exception('Failed to insert attachment: ' . implode(', ', $errors));
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $dbError = $this->db->error();
            $lastQuery = method_exists($this->db, 'getLastQuery') ? (string)$this->db->getLastQuery() : '';
            error_log('DI Items Insert Exception: ' . ($e->getMessage() ?: '[empty message]'));
            if (!empty($dbError)) error_log('DB Error: ' . print_r($dbError, true));
            if ($lastQuery) error_log('Last Query: ' . $lastQuery);
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'success'=>false,
                'message'=>'Gagal membuat items DI: ' . ($e->getMessage() ?: (!empty($dbError['message']) ? $dbError['message'] : 'Unknown error')),
                'debug' => [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                ],
                'csrf_hash'=>csrf_hash()
            ]);
        }
        
        // Update SPK status to IN_PROGRESS when DI is created
        if ($spkId > 0) {
            try {
                $updateResult = $this->db->table('spk')->where('id', $spkId)->update([
                    'status' => 'IN_PROGRESS',
                    'diperbarui_pada' => date('Y-m-d H:i:s')
                ]);
                
                if (!$updateResult) {
                    throw new \Exception('Failed to update SPK status');
                }
                
                // Log status history
                try {
                    $this->db->query(
                        "INSERT INTO spk_status_history (spk_id, status_from, status_to, changed_by, note, changed_at) VALUES (?, ?, ?, ?, ?, ?)",
                        [$spkId, 'READY', 'IN_PROGRESS', session('user_id') ?: 1, 'DI created: ' . $payload['nomor_di'], date('Y-m-d H:i:s')]
                    );
                } catch (\Exception $e) {
                    // Continue if history logging fails (best effort)
                    error_log('SPK Status History Error: ' . $e->getMessage());
                }
            } catch (\Exception $e) {
                error_log('SPK Status Update Exception: ' . $e->getMessage());
                $this->db->transRollback();
                return $this->response->setStatusCode(500)->setJSON([
                    'success'=>false,
                    'message'=>'Gagal update status SPK: ' . $e->getMessage(),
                    'csrf_hash'=>csrf_hash()
                ]);
            }
        }
        
        // Debug what query is about to be executed in the transaction
        error_log('DI Creation - Before transComplete. Payload: ' . json_encode($payload));
        error_log('Unit IDs: ' . json_encode($unitIds));
        
        // Check what tables and columns are involved
        try {
            $diTable = $this->diModel->table;
            $diColumns = $this->db->getFieldNames($diTable);
            error_log('DI Table: ' . $diTable . ', Columns: ' . implode(', ', $diColumns));
            
            $diItemsTable = $this->diItemModel->table;
            $diItemColumns = $this->db->getFieldNames($diItemsTable);
            error_log('DI Items Table: ' . $diItemsTable . ', Columns: ' . implode(', ', $diItemColumns));
        } catch (\Exception $e) {
            error_log('Error getting table info: ' . $e->getMessage());
        }
        
        // Check transaction status and commit or rollback
        if ($this->db->transStatus() === false) {
            // Transaction failed, rollback and return error
            $this->db->transRollback();
            $dbError = $this->db->error();
            error_log('DI Creation DB Error: ' . print_r($dbError, true));
            error_log('DI Creation Payload: ' . print_r($payload, true));
            
            // Check if we can get the last query to debug
            $lastQuery = '';
            if (method_exists($this->db, 'getLastQuery')) {
                $lastQuery = (string)$this->db->getLastQuery();
                error_log('Last Query: ' . $lastQuery);
            }
            
            return $this->response->setStatusCode(500)->setJSON([
                'success'=>false,
                'message'=>'Gagal membuat DI: Transaction failed',
                'debug' => [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                    'payload' => $payload,
                ],
                'csrf_hash'=>csrf_hash()
            ]);
        } else {
            // Transaction successful, commit it
            $this->db->transCommit();
            
            return $this->response->setJSON([
                'success'=>true,
                'message'=>'DI dibuat',
                'nomor'=>$payload['nomor_di'],
                'csrf_hash'=>csrf_hash()
            ]);
        }
    }

    // ===== KONTRAK METHODS =====
    public function kontrak()
    {
        return view('marketing/kontrak');
    }

    // Get active contracts that have specifications for SPK creation
    public function getActiveContracts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        try {
            $kontraks = $this->kontrakModel
                ->select('kontrak.id, kontrak.no_kontrak, kontrak.pelanggan')
                ->join('kontrak_spesifikasi ks', 'ks.kontrak_id = kontrak.id', 'inner')
                ->whereIn('kontrak.status', ['Aktif', 'Pending'])
                ->groupBy('kontrak.id, kontrak.no_kontrak, kontrak.pelanggan')
                ->orderBy('kontrak.dibuat_pada', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $kontraks,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data kontrak: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request','csrf_hash'=>csrf_hash()]);
        }

        try {
            $draw   = (int)($this->request->getPost('draw') ?? 0);
            $start  = (int)($this->request->getPost('start') ?? 0);
            $length = (int)($this->request->getPost('length') ?? 10);
            $searchValue = trim($this->request->getPost('search')['value'] ?? '');

            // Base query
            $builder = $this->db->table('kontrak k');
            $countBuilder = $this->db->table('kontrak k');

            // Status filter functionality
            $statusFilter = trim($this->request->getPost('statusFilter') ?? 'all');
            if ($statusFilter !== 'all') {
                if ($statusFilter === 'expiring') {
                    // Expiring contracts (Aktif status and expiring within 30 days)
                    $expiringDate = date('Y-m-d', strtotime('+30 days'));
                    $builder->where('k.status', 'Aktif')
                           ->where('k.tanggal_berakhir <=', $expiringDate)
                           ->where('k.tanggal_berakhir >=', date('Y-m-d'));
                    $countBuilder->where('k.status', 'Aktif')
                                ->where('k.tanggal_berakhir <=', $expiringDate)
                                ->where('k.tanggal_berakhir >=', date('Y-m-d'));
                } else {
                    // Standard status filter
                    $builder->where('k.status', $statusFilter);
                    $countBuilder->where('k.status', $statusFilter);
                }
            }

            // Search functionality
            if ($searchValue !== '') {
                $builder->groupStart()
                    ->like('k.no_kontrak', $searchValue)
                    ->orLike('k.pelanggan', $searchValue)
                    ->orLike('k.lokasi', $searchValue)
                    ->orLike('k.no_po_marketing', $searchValue)
                ->groupEnd();

                $countBuilder->groupStart()
                    ->like('k.no_kontrak', $searchValue)
                    ->orLike('k.pelanggan', $searchValue)
                    ->orLike('k.lokasi', $searchValue)
                    ->orLike('k.no_po_marketing', $searchValue)
                ->groupEnd();
            }

            // Count records
            $recordsTotal = $this->db->table('kontrak')->countAllResults();
            $recordsFiltered = $countBuilder->countAllResults();

            // Safely determine total_units (kontrak_unit table may not yet exist)
            $hasKontrakUnit = $this->db->tableExists('kontrak_unit');
            if ($hasKontrakUnit) {
                $builder->select('k.*, (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.kontrak_id = k.id) as total_units');
            } else {
                $builder->select('k.*');
            }

            $kontrakData = $builder
                ->orderBy('k.id', 'DESC')
                ->limit($length, $start)
                ->get()
                ->getResultArray();

            // Fallback simple query if result unexpectedly empty but table has records
            if (empty($kontrakData)) {
                $totalCheck = $this->db->table('kontrak')->countAllResults();
                if ($totalCheck > 0) {
                    log_message('debug', 'Marketing::getDataTable primary query returned empty, running fallback simple select');
                    $kontrakData = $this->db->table('kontrak k')
                        ->orderBy('k.id','DESC')
                        ->limit($length, $start)
                        ->get()
                        ->getResultArray();
                }
            }

            // Format data
            $data = [];
            foreach ($kontrakData as $row) {
                $statusClass = $this->getStatusClass($row['status']);
                $startDate = date('d/m/Y', strtotime($row['tanggal_mulai']));
                $endDate = date('d/m/Y', strtotime($row['tanggal_berakhir']));
                $period = $startDate . ' - ' . $endDate;

                $totalUnits = $row['total_units'] ?? 0;
                if (!$hasKontrakUnit) {
                    // Optional: future-proof placeholder; keep 0 without failing
                    $totalUnits = 0;
                }

                $data[] = [
                    'id' => $row['id'],
                    'contract_number' => esc($row['no_kontrak']),
                    'po' => esc($row['no_po_marketing'] ?? ''),
                    'client_name' => esc($row['pelanggan']),
                    'period' => $period,
                    'value' => 'Rp ' . number_format(0, 0, ',', '.'), // Placeholder (no column yet)
                    'total_units' => $totalUnits,
                    'status' => '<span class="badge bg-' . $statusClass . '">' . esc($row['status']) . '</span>',
                    'actions' => $this->buildKontrakActions($row['id'])
                ];
            }

            // Calculate statistics
            $stats = $this->getKontrakStats();

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'stats' => $stats,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'draw' => $draw ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Generate unique contract number (private)
     */
    private function generateContractNumberPrivate()
    {
        $year = date('Y');
        $month = date('m');

        // Find the highest contract number for current year/month
        $prefix = "KTR/{$year}/{$month}/";
        $existing = $this->db->table('kontrak')
            ->select('no_kontrak')
            ->like('no_kontrak', $prefix, 'after')
            ->orderBy('no_kontrak', 'DESC')
            ->get()
            ->getRowArray();

        $nextNumber = 1;
        if ($existing) {
            // Extract number from existing contract (e.g., "KTR/2025/08/005" -> 5)
            $parts = explode('/', $existing['no_kontrak']);
            if (count($parts) >= 4) {
                $lastPart = end($parts);
                if (is_numeric($lastPart)) {
                    $nextNumber = (int)$lastPart + 1;
                }
            }
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if contract number already exists
     */
    public function checkContractNumberDuplicate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $contractNumber = trim((string)$this->request->getPost('contract_number'));
            
            if (!$contractNumber) {
                return $this->response->setJSON([
                    'success' => false,
                    'duplicate' => false,
                    'message' => 'Contract number is required',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            $existing = $this->kontrakModel->where('no_kontrak', $contractNumber)->first();
            
            return $this->response->setJSON([
                'success' => true,
                'duplicate' => $existing ? true : false,
                'existing_id' => $existing ? ($existing['id'] ?? $existing->id) : null,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error checking contract number: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Generate next available contract number
     */
    public function generateContractNumber()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $contractNumber = $this->generateContractNumberPrivate();

            return $this->response->setJSON([
                'success' => true,
                'contract_number' => $contractNumber,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal generate nomor kontrak: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function storeKontrak()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            // Use KontrakModel for proper validation
            $data = [
                'no_kontrak' => trim((string)$this->request->getPost('contract_number')),
                'no_po_marketing' => $this->request->getPost('po_number'),
                'pelanggan' => $this->request->getPost('client_name'),
                'lokasi' => $this->request->getPost('project_name'),
                'tanggal_mulai' => $this->request->getPost('start_date'),
                'tanggal_berakhir' => $this->request->getPost('end_date'),
                'status' => $this->request->getPost('status'),
                'dibuat_oleh' => 1, // TODO: Get from session
            ];

            // Validate using KontrakModel
            if (!$this->kontrakModel->save($data)) {
                $errors = $this->kontrakModel->errors();
                $existingId = null;

                // Check if it's a duplicate contract number
                if (!empty($errors['no_kontrak']) && strpos($errors['no_kontrak'], 'sudah digunakan') !== false) {
                    $contractNumber = trim((string)$this->request->getPost('contract_number'));
                    if ($contractNumber !== '') {
                        $existing = $this->kontrakModel->where('no_kontrak', $contractNumber)->first();
                        if ($existing) {
                            $existingId = is_array($existing) ? ($existing['id'] ?? null) : ($existing->id ?? null);
                        }
                    }
                }

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $errors,
                    'duplicate' => $existingId ? true : false,
                    'existing_id' => $existingId,
                    'csrf_hash' => csrf_hash()
                ]);
            }

            $newId = $this->kontrakModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kontrak berhasil ditambahkan',
                'data' => ['id' => $newId],
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan kontrak: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function detailKontrak($id)
    {
        try {
            $kontrak = $this->db->table('kontrak')
                ->where('id', $id)
                ->get()
                ->getRowArray();

            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $kontrak,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data kontrak: ' . $e->getMessage()
            ]);
        }
    }

    public function updateKontrak($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $data = [
                'no_kontrak' => $this->request->getPost('contract_number'),
                'no_po_marketing' => $this->request->getPost('po_number'),
                'pelanggan' => $this->request->getPost('client_name'),
                'lokasi' => $this->request->getPost('project_name'),
                'tanggal_mulai' => $this->request->getPost('start_date'),
                'tanggal_berakhir' => $this->request->getPost('end_date'),
                'status' => $this->request->getPost('status'),
                'diperbarui_pada' => date('Y-m-d H:i:s')
            ];

            $this->db->table('kontrak')->where('id', $id)->update($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kontrak berhasil diperbarui',
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui kontrak: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function deleteKontrak($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $this->db->table('kontrak')->where('id', $id)->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kontrak berhasil dihapus',
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus kontrak: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    private function getKontrakStats()
    {
        $stats = [
            'total' => $this->db->table('kontrak')->countAllResults(),
            'active' => $this->db->table('kontrak')->where('status', 'Aktif')->countAllResults(),
            'expiring' => 0, // Will be calculated based on date
            'expired' => $this->db->table('kontrak')->where('status', 'Berakhir')->countAllResults()
        ];

        // Calculate expiring contracts (within 30 days)
        $expiringDate = date('Y-m-d', strtotime('+30 days'));
        $stats['expiring'] = $this->db->table('kontrak')
            ->where('status', 'Aktif')
            ->where('tanggal_berakhir <=', $expiringDate)
            ->where('tanggal_berakhir >=', date('Y-m-d'))
            ->countAllResults();

        return $stats;
    }

    private function getStatusClass($status)
    {
        switch ($status) {
            case 'Aktif': return 'success';
            case 'Pending': return 'warning';
            case 'Berakhir': return 'secondary';
            case 'Dibatalkan': return 'danger';
            default: return 'secondary';
        }
    }

    private function buildKontrakActions($id)
    {
        return '<div class="dropdown">'
            .'<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">'
            .'<i class="fas fa-ellipsis-h"></i></button>'
            .'<ul class="dropdown-menu">'
            .'<li><a class="dropdown-item" href="javascript:viewContractUnits('.$id.')"><i class="fas fa-list me-2 text-info"></i>Lihat Unit</a></li>'
            .'<li><a class="dropdown-item" href="javascript:editContract('.$id.')"><i class="fas fa-edit me-2 text-primary"></i>Edit</a></li>'
            .'<li><a class="dropdown-item" href="javascript:deleteContract('.$id.')"><i class="fas fa-trash me-2 text-danger"></i>Hapus</a></li>'
            .'</ul></div>';
    }

    public function availableUnitsData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request','csrf_hash'=>csrf_hash()]);
        }

                $draw   = (int)($this->request->getPost('draw') ?? 0);
                $start  = (int)($this->request->getPost('start') ?? 0);
                $length = (int)($this->request->getPost('length') ?? 10);
                if ($length <= 0) { $length = 10; }
                if ($length === -1) { $length = null; }

            $statusTab   = $this->request->getPost('status_tab'); // 'all', '7', '8'
            $tipeFilter  = trim($this->request->getPost('tipe') ?? '');
                $lokasiFilter= trim($this->request->getPost('lokasi') ?? '');
                $searchValue = trim($this->request->getPost('search')['value'] ?? '');

    try {
                    // Base queries
                    $base = $this->baseQuery(); // already limited to status 7 & 8
                    $count = $this->baseQuery();

                    // Status tab filter (optional within 7 & 8)
                    if ($statusTab === '7' || $statusTab === '8') {
                        $base->where('iu.status_unit_id', (int)$statusTab);
                        $count->where('iu.status_unit_id', (int)$statusTab);
                    }

                    if ($tipeFilter !== '') {
                        // Cari pada tabel tipe_unit (kolom tipe atau jenis)
                        $base->groupStart()->like('tu.tipe', $tipeFilter)->orLike('tu.jenis', $tipeFilter)->groupEnd();
                        $count->groupStart()->like('tu.tipe', $tipeFilter)->orLike('tu.jenis', $tipeFilter)->groupEnd();
                    }
                    if ($lokasiFilter !== '') {
                        $base->like('iu.lokasi_unit', $lokasiFilter);
                        $count->like('iu.lokasi_unit', $lokasiFilter);
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

                        $count->groupStart()
                            ->like('iu.no_unit', $searchValue)
                            ->orLike('iu.serial_number', $searchValue)
                            ->orLike('mu.merk_unit', $searchValue)
                            ->orLike('mu.model_unit', $searchValue)
                            ->orLike('tu.tipe', $searchValue)
                            ->orLike('tu.jenis', $searchValue)
                            ->orLike('iu.lokasi_unit', $searchValue)
                        ->groupEnd();
                    }

                    // Counts
                    $recordsTotal    = $this->baseQuery()->countAllResults(); // total status 7 & 8
                    $recordsFiltered = $count->countAllResults();

                    // Ordering (simple & safe). If no_unit missing, fallback by id.
                    $base->orderBy('iu.no_unit','ASC')->orderBy('iu.id_inventory_unit','ASC');
                    if ($length !== null) { $base->limit($length, $start); }

                    $rows = $base->get()->getResultArray();
                    $data = [];
                    foreach ($rows as $r) {
                        $realId = isset($r['id_inventory_unit']) ? (int)$r['id_inventory_unit'] : 0;
                        $data[] = [
                            'id'              => $realId,
                            'no_unit'         => $r['no_unit'],
                            'serial_number'   => $r['serial_number'],
                            'brand'           => $r['merk_unit'],
                            'model'           => $r['model_unit'],
                            'type_full'       => $r['tipe_full'],
                            'capacity'        => $r['kapasitas_unit'],
                            'lokasi_unit'     => $r['lokasi_unit'],
                            'nama_departemen' => $r['nama_departemen'] ?? '-',
                            'status_id'       => (int)$r['status_unit_id'],
                            'status_name'     => strtoupper($r['status_unit_name']),
                            'actions'         => $this->buildActions($realId)
                        ];
                    }

            return $this->response->setJSON([
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
                'csrf_hash'       => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: '.$e->getMessage(),
                'csrf_hash' => csrf_hash(),
            ]);
        }
    }

    private function baseQuery(): BaseBuilder
    {
        $qb = $this->db->table('inventory_unit iu')
            ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.status_unit_id, iu.lokasi_unit, iu.created_at')
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
            ->whereIn('iu.status_unit_id',[7,8]);
        return $qb;
    }

    private function buildActions(int $id): string
    {
        return '<div class="dropdown">'
            .'<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>'
            .'<ul class="dropdown-menu">'
            .'<li><a class="dropdown-item" href="#" onclick="viewDetail('.$id.')"><i class="fas fa-eye me-2 text-info"></i>Lihat</a></li>'
            .'<li><a class="dropdown-item" href="'.base_url('marketing/penawaran').'?unit='.$id.'"><i class="fas fa-file-invoice me-2 text-primary"></i>Penawaran</a></li>'
            .'<li><a class="dropdown-item" href="'.base_url('marketing/booking').'?unit='.$id.'"><i class="fas fa-calendar-plus me-2 text-success"></i>Booking</a></li>'
            .'</ul></div>';
    }

    /**
     * Get contract details by ID
     */
    public function getKontrak($id)
    {
        try {
            $contract = $this->kontrakModel->find((int)$id);
            
            if (!$contract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $contract,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Marketing::getKontrak] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat detail kontrak',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Find contract by specification ID
     */
    public function findBySpesifikasi($spekId)
    {
        try {
            $spek = $this->kontrakSpesifikasiModel->find((int)$spekId);
            
            if (!$spek) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'kontrak_id' => $spek['kontrak_id'],
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Marketing::findBySpesifikasi] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mencari kontrak',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Cleanup SPK records with ID = 0
     */
    public function cleanupSpkZero()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            // Check for SPK records with ID = 0
            $spkZeroRecords = $this->db->table('spk')->where('id', 0)->get()->getResultArray();

            $result = [
                'success' => true,
                'message' => 'Cleanup completed',
                'found_records' => count($spkZeroRecords),
                'deleted_records' => 0,
                'deleted_history' => 0,
                'records' => []
            ];

            if (count($spkZeroRecords) > 0) {
                // Store record details for response
                foreach ($spkZeroRecords as $record) {
                    $result['records'][] = [
                        'id' => $record['id'],
                        'nomor_spk' => $record['nomor_spk'],
                        'status' => $record['status'],
                        'dibuat_pada' => $record['dibuat_pada']
                    ];
                }

                // Delete SPK records with ID = 0
                $deleted = $this->db->table('spk')->where('id', 0)->delete();
                $result['deleted_records'] = $deleted;

                // Delete related status history records
                $statusHistoryRecords = $this->db->table('spk_status_history')->where('spk_id', 0)->get()->getResultArray();
                if (count($statusHistoryRecords) > 0) {
                    $deletedHistory = $this->db->table('spk_status_history')->where('spk_id', 0)->delete();
                    $result['deleted_history'] = $deletedHistory;
                }

                log_message('info', 'Marketing::cleanupSpkZero - Deleted ' . $deleted . ' SPK records with ID = 0 and ' . $result['deleted_history'] . ' status history records');
            } else {
                $result['message'] = 'No SPK records with ID = 0 found';
            }

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::cleanupSpkZero - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error during cleanup: ' . $e->getMessage()
            ]);
        }
    }
}