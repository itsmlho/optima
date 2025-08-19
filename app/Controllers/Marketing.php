<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\BaseBuilder;
use App\Models\SpkModel;
use App\Models\KontrakModel;
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



    // DI page
    public function di()
    {
        return view('marketing/di');
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
        return $this->response->setJSON(['success'=>true,'data'=>$row,'spesifikasi'=>$enriched,'csrf_hash'=>csrf_hash()]);
    }

    // Provide kontrak options (Pending) for searchable dropdown
    public function kontrakOptions()
    {
        $q = trim($this->request->getGet('q') ?? '');
        $status = trim($this->request->getGet('status') ?? 'Pending');
    $builder = $this->kontrakModel->select('id, no_kontrak, no_po_marketing, pelanggan, lokasi');
        if ($status !== '' && strtolower($status) !== 'all') {
            $builder->where('status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('no_kontrak', $q)
                ->orLike('no_po_marketing', $q)
                ->orLike('pelanggan', $q)
            ->groupEnd();
        }
        $rows = $builder->orderBy('id','DESC')->limit(20)->get()->getResultArray();
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
        // Build spesifikasi array and normalize nested values (e.g., aksesoris JSON string -> array)
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
        // kontrak_id not used anymore; rely on selected kontrak/PO reference and pelanggan
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
    $this->spkModel->insert($payload);
        // Notify Service team bell: new SPK submitted
        try {
            // Ensure notifications table exists (Notifications controller does this too, but we'll be defensive)
            if (!$this->db->tableExists('notifications')) {
                $notifCtrl = new \App\Controllers\Notifications();
                // call a method that creates table without sending output
                $ref = new \ReflectionClass($notifCtrl);
                $m = $ref->getMethod('create'); // touch controller to ensure DB available; table creation occurs in create/stream/getCount, fallback below
            }
        } catch (\Throwable $e) { /* ignore */ }
        // Insert a broadcast-to-role notification if schema supports target_role
        try {
            $dataNotif = [
                'title' => 'SPK Baru',
                'message' => 'SPK ' . $payload['nomor_spk'] . ' diajukan oleh Marketing untuk diproses Service.',
                'type' => 'info',
                'user_id' => null,
                'url' => base_url('service/spk_service'),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            // target_role column may or may not exist; attempt insert with it first
            $hasTargetRole = false;
            try {
                $this->db->query('SELECT target_role FROM notifications LIMIT 1');
                $hasTargetRole = true;
            } catch (\Throwable $e) { $hasTargetRole = false; }
            if ($hasTargetRole) { $dataNotif['target_role'] = 'service'; }
            if ($this->notifModel) {
                $this->notifModel->insert($dataNotif);
            } else {
                $this->db->table('notifications')->insert($dataNotif);
            }
        } catch (\Throwable $e) {
            // silent fail; notifications are optional
        }
        return $this->response->setJSON(['success'=>true,'message'=>'SPK dibuat','nomor'=>$payload['nomor_spk'],'csrf_hash'=>csrf_hash()]);
    }

    // Generic options endpoint for SPK specifications
    public function specOptions()
    {
        $type = trim($this->request->getGet('type') ?? '');
        // Predefined simple maps
        $map = [
            'departemen'      => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen','order'=>'nama_departemen'],
            'kapasitas'       => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit','order'=>'kapasitas_unit'],
            'mast'            => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast','order'=>'tipe_mast'],
            'ban'             => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban','order'=>'tipe_ban'],
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

        // Handle special DISTINCT cases
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
        $spkId = (int)($this->request->getPost('spk_id') ?? 0);
        $poNo = trim((string)($this->request->getPost('po_kontrak_nomor') ?? ''));
        $tanggalKirim = $this->request->getPost('tanggal_kirim') ?: null;
        $catatan = $this->request->getPost('catatan') ?: null;

        $pelanggan = $this->request->getPost('pelanggan') ?: '';
        $lokasi = $this->request->getPost('lokasi') ?: null;

        $selected = ['unit_id'=>null,'inventory_attachment_id'=>null];
        if ($spkId > 0) {
            // Ensure SPK is READY (Service has assigned items)
            $spk = $this->db->table('spk')->where('id',$spkId)->get()->getRowArray();
            if (!$spk || $spk['status'] !== 'READY') {
                return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'SPK belum READY']);
            }
            $poNo = $spk['po_kontrak_nomor'];
            $pelanggan = $spk['pelanggan'];
            $lokasi = $spk['lokasi'];
            if (!empty($spk['spesifikasi'])) {
                $spec = json_decode($spk['spesifikasi'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($spec) && isset($spec['selected'])) {
                    $selected['unit_id'] = (int)($spec['selected']['unit_id'] ?? 0) ?: null;
                    $selected['inventory_attachment_id'] = (int)($spec['selected']['inventory_attachment_id'] ?? 0) ?: null;
                }
            }
        }

        if ($poNo === '') {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'PO/Kontrak wajib diisi']);
        }

        $payload = [
            'nomor_di' => method_exists($this->diModel,'generateNextNumber') ? $this->diModel->generateNextNumber() : $this->generateDiNumber(),
            'spk_id' => $spkId ?: null,
            'po_kontrak_nomor' => $poNo,
            'pelanggan' => $pelanggan,
            'lokasi' => $lokasi,
            'tanggal_kirim' => $tanggalKirim,
            'catatan' => $catatan,
            'status' => 'SUBMITTED',
            'dibuat_oleh' => session('user_id') ?: 1,
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ];
        $this->db->transStart();
        $this->diModel->insert($payload);
        $diId = (int)$this->diModel->getInsertID();
        // Insert delivery items from SPK selection
        if (!empty($selected['unit_id'])) {
            $this->diItemModel->insert([
                'di_id' => $diId,
                'item_type' => 'UNIT',
                'unit_id' => (int)$selected['unit_id'],
                'attachment_id' => null,
                'keterangan' => null,
            ]);
        }
        if (!empty($selected['inventory_attachment_id'])) {
            // Map inventory_attachment to attachment_id if needed: resolve attachment_id by joining inventory_attachment
            $inv = $this->db->table('inventory_attachment')->select('attachment_id')->where('id_inventory_attachment', (int)$selected['inventory_attachment_id'])->get()->getRowArray();
            $attId = $inv['attachment_id'] ?? null;
            $this->diItemModel->insert([
                'di_id' => $diId,
                'item_type' => 'ATTACHMENT',
                'unit_id' => null,
                'attachment_id' => $attId,
                'keterangan' => null,
            ]);
        }
        // Mark related SPK as COMPLETED (so Marketing can't create DI again) and log history
        if ($spkId > 0) {
            if (method_exists($this->spkModel,'setStatusWithHistory')) {
                $this->spkModel->setStatusWithHistory($spkId, 'COMPLETED', session('user_id') ?: 1, 'DI dibuat oleh Marketing');
            } else {
                $this->db->table('spk')->where('id',$spkId)->update([
                    'status' => 'COMPLETED',
                    'diperbarui_pada' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['success'=>false,'message'=>'Gagal membuat DI','csrf_hash'=>csrf_hash()]);
        }
        return $this->response->setJSON(['success'=>true,'message'=>'DI dibuat','nomor'=>$payload['nomor_di'],'csrf_hash'=>csrf_hash()]);
    }

    // ===== KONTRAK METHODS =====
    public function kontrak()
    {
        return view('marketing/kontrak');
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
                    'contract_number' => esc($row['no_kontrak']),
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

    public function storeKontrak()
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
                'dibuat_oleh' => 1, // TODO: Get from session
                'dibuat_pada' => date('Y-m-d H:i:s')
            ];

            $this->db->table('kontrak')->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kontrak berhasil ditambahkan',
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
}