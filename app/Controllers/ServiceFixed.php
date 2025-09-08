<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MarketingModel;
use App\Models\ServiceModel;

class Service extends Controller
{
    protected $db;
    protected $session;
    
    public function __construct()
    {
        // Load database and session
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
    }
    
    /**
     * Get unit components from inventory_attachment (single source of truth)
     */
    private function getUnitComponents($unitId)
    {
        $components = [
            'battery' => null,
            'charger' => null,
            'attachment' => null
        ];

        // Get battery info - include both available (7) and in use (8) for the unit
        $battery = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.baterai_id, ia.sn_baterai, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'ia.baterai_id = b.id', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'battery')
            ->whereIn('ia.status_unit', [7, 8]) // Available or In use for this unit
            ->get()->getRowArray();

        if ($battery) {
            $components['battery'] = $battery;
        }

        // Get charger info - include both available (7) and in use (8) for the unit
        $charger = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.charger_id, ia.sn_charger, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'ia.charger_id = c.id_charger', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'charger')
            ->whereIn('ia.status_unit', [7, 8]) // Available or In use for this unit
            ->get()->getRowArray();

        if ($charger) {
            $components['charger'] = $charger;
        }

        // Get attachment info
        $attachment = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.attachment_id, ia.sn_attachment, a.tipe, a.merk, a.model')
            ->join('attachment a', 'ia.attachment_id = a.id_attachment', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'attachment')
            ->where('ia.status_unit', 8) // In use
            ->get()->getRowArray();

        if ($attachment) {
            $components['attachment'] = $attachment;
        }

        return $components;
    }

    /**
     * Improved SPK Print function that uses the same data retrieval logic as spkDetail
     */
    public function spkPrint($id)
    {
        // Get SPK data - bisa berdasarkan ID atau nomor_spk
        $spk = $this->db->table('spk')->where('id', $id)->get()->getRowArray();
        if (!$spk) {
            // Coba cari berdasarkan nomor_spk jika ID tidak ditemukan
            $spk = $this->db->table('spk')->where('nomor_spk', $id)->get()->getRowArray();
        }
        
        if (!$spk) {
            return $this->response->setStatusCode(404)->setBody('SPK tidak ditemukan');
        }

        // Dekode spesifikasi dari JSON jika tersedia
        $spec = [];
        $enriched = [];
        if (!empty($spk['spesifikasi'])) {
            $decoded = json_decode($spk['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $spec = $decoded;
                $enriched = $decoded;
            }
        }

        // Enrich names for ID-based fields (best-effort)
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

        // Get kontrak spesifikasi data
        $kontrak_spesifikasi = [];
        if (!empty($spk['kontrak_spesifikasi_id'])) {
            $kontrak_spesifikasi = $this->db->table('kontrak_spesifikasi')->where('id', $spk['kontrak_spesifikasi_id'])->get()->getRowArray() ?? [];
            
            // Decode aksesoris JSON if stored as string
            if ($kontrak_spesifikasi && isset($kontrak_spesifikasi['aksesoris']) && is_string($kontrak_spesifikasi['aksesoris'])) {
                try {
                    $decoded_aks = json_decode($kontrak_spesifikasi['aksesoris'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $kontrak_spesifikasi['aksesoris'] = $decoded_aks;
                    }
                } catch (\Exception $e) {
                    // keep original string on failure
                }
            }
        }
        
        // Enrich selected items (unit & attachment) with full details
        // First, check if data comes from approval workflow
        if (!empty($spk['persiapan_unit_id'])) {
            // Prioritaskan cari dengan id_inventory_unit
            $u = $this->db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id, iu.model_mast_id, iu.roda_id, iu.ban_id, iu.valve_id')
                ->select('iu.sn_mast, iu.sn_mesin')
                ->select('mu.merk_unit, mu.model_unit')
                ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model')
                ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left') 
                ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                ->join('mesin m','m.id = iu.model_mesin_id','left')
                ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                ->where('iu.id_inventory_unit', $spk['persiapan_unit_id'])
                ->get()->getRowArray();
            
            // Fallback ke no_unit jika tidak ditemukan
            if (!$u) {
                $u = $this->db->table('inventory_unit iu')
                    ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id, iu.model_mast_id, iu.roda_id, iu.ban_id, iu.valve_id')
                    ->select('iu.sn_mast, iu.sn_mesin')
                    ->select('mu.merk_unit, mu.model_unit')
                    ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                    ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model')
                    ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                    ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                    ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left') 
                    ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                    ->join('mesin m','m.id = iu.model_mesin_id','left')
                    ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                    ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                    ->where('iu.no_unit', $spk['persiapan_unit_id'])
                    ->get()->getRowArray();
            }
            
            if ($u) {
                // Get unit components from inventory_attachment (single source of truth for serial numbers)
                $unitComponents = $this->getUnitComponents($u['id_inventory_unit']);
                
                // Process battery data
                $batterySN = '';
                $batteryModel = '';
                $batteryDisplay = '';
                
                if (!empty($unitComponents['battery'])) {
                    if (!empty($unitComponents['battery']['sn_baterai'])) {
                        $batterySN = $unitComponents['battery']['sn_baterai'];
                    }
                    
                    if (!empty($unitComponents['battery']['tipe_baterai'])) {
                        $batteryModel = $unitComponents['battery']['tipe_baterai'];
                        $batteryDisplay = $batteryModel;
                        
                        if (!empty($batterySN)) {
                            $batteryDisplay = $batteryModel . ' (' . $batterySN . ')';
                        }
                    } else if (!empty($unitComponents['battery']['merk_baterai'])) {
                        $batteryModel = $unitComponents['battery']['merk_baterai'];
                        $batteryDisplay = $batteryModel;
                        
                        if (!empty($batterySN)) {
                            $batteryDisplay = $batteryModel . ' (' . $batterySN . ')';
                        }
                    } else {
                        if (!empty($batterySN)) {
                            $batteryDisplay = 'Baterai (' . $batterySN . ')';
                        }
                    }
                }
                
                // Process charger data
                $chargerSN = '';
                $chargerModel = '';
                $chargerDisplay = '';
                
                if (!empty($unitComponents['charger'])) {
                    if (!empty($unitComponents['charger']['sn_charger'])) {
                        $chargerSN = $unitComponents['charger']['sn_charger'];
                    }
                    
                    if (!empty($unitComponents['charger']['tipe_charger'])) {
                        $chargerModel = $unitComponents['charger']['tipe_charger'];
                        $chargerDisplay = $chargerModel;
                        
                        if (!empty($chargerSN)) {
                            $chargerDisplay = $chargerModel . ' (' . $chargerSN . ')';
                        }
                    } else if (!empty($unitComponents['charger']['merk_charger'])) {
                        $chargerModel = $unitComponents['charger']['merk_charger'];
                        $chargerDisplay = $chargerModel;
                        
                        if (!empty($chargerSN)) {
                            $chargerDisplay = $chargerModel . ' (' . $chargerSN . ')';
                        }
                    } else {
                        if (!empty($chargerSN)) {
                            $chargerDisplay = 'Charger (' . $chargerSN . ')';
                        }
                    }
                }
                
                // Process attachment data
                $attachmentSN = '';
                $attachmentModel = '';
                $attachmentDisplay = '';
                
                if (!empty($unitComponents['attachment'])) {
                    if (!empty($unitComponents['attachment']['sn_attachment'])) {
                        $attachmentSN = $unitComponents['attachment']['sn_attachment'];
                    }
                    
                    if (!empty($unitComponents['attachment']['model'])) {
                        $attachmentModel = $unitComponents['attachment']['model'];
                    }
                    
                    $attachmentDisplay = '';
                    if (!empty($unitComponents['attachment']['tipe'])) {
                        $attachmentDisplay = $unitComponents['attachment']['tipe'];
                        
                        if (!empty($unitComponents['attachment']['merk'])) {
                            $attachmentDisplay .= ' ' . $unitComponents['attachment']['merk'];
                        }
                        
                        if (!empty($attachmentModel)) {
                            $attachmentDisplay .= ' ' . $attachmentModel;
                        }
                        
                        if (!empty($attachmentSN)) {
                            $attachmentDisplay .= ' (' . $attachmentSN . ')';
                        }
                    }
                }
                
                // Create label
                $noUnit = isset($u['no_unit']) ? $u['no_unit'] : '-';
                $merkUnit = isset($u['merk_unit']) ? $u['merk_unit'] : '-';
                $modelUnit = isset($u['model_unit']) ? $u['model_unit'] : '';
                $lokasiUnit = isset($u['lokasi_unit']) ? $u['lokasi_unit'] : '-';
                
                $label = trim($noUnit . ' - ' . $merkUnit . ' ' . $modelUnit . ' @ ' . $lokasiUnit);
                
                // Get valve, mast, roda, ban data
                $valveId = isset($u['valve_id']) ? $u['valve_id'] : null;
                $mastId = isset($u['model_mast_id']) ? $u['model_mast_id'] : null;
                $rodaId = isset($u['roda_id']) ? $u['roda_id'] : null;
                $banId = isset($u['ban_id']) ? $u['ban_id'] : null;
                
                $valve = $this->db->table('valve')->select('jumlah_valve')->where('id_valve', $valveId)->get()->getRowArray();
                $mast = $this->db->table('tipe_mast')->select('tipe_mast')->where('id_mast', $mastId)->get()->getRowArray();
                $roda = $this->db->table('jenis_roda')->select('tipe_roda')->where('id_roda', $rodaId)->get()->getRowArray();
                $ban = $this->db->table('tipe_ban')->select('tipe_ban')->where('id_ban', $banId)->get()->getRowArray();
                
                $valveValue = '';
                if (!empty($valve) && isset($valve['jumlah_valve'])) {
                    $valveValue = $valve['jumlah_valve'];
                }
                
                $mastValue = '';
                if (!empty($mast) && isset($mast['tipe_mast'])) {
                    $mastValue = $mast['tipe_mast'];
                }
                
                $rodaValue = '';
                if (!empty($roda) && isset($roda['tipe_roda'])) {
                    $rodaValue = $roda['tipe_roda'];
                }
                
                $banValue = '';
                if (!empty($ban) && isset($ban['tipe_ban'])) {
                    $banValue = $ban['tipe_ban'];
                }
                
                // Format: Model (SN) atau hanya Model jika SN kosong
                $snMast = isset($u['sn_mast']) ? $u['sn_mast'] : null;
                $snMesin = isset($u['sn_mesin']) ? $u['sn_mesin'] : null;
                $mastModel = isset($u['mast_model']) ? $u['mast_model'] : 'Mast';
                $mesinModel = isset($u['mesin_model']) ? $u['mesin_model'] : 'Mesin';
                
                $snMastFormatted = $mastModel;
                if (!empty($snMast)) {
                    $snMastFormatted = $mastModel . ' (' . $snMast . ')';
                }
                
                $snMesinFormatted = $mesinModel;
                if (!empty($snMesin)) {
                    $snMesinFormatted = $mesinModel . ' (' . $snMesin . ')';
                }
                
                // Create the enriched unit data
                $enriched['selected']['unit'] = array(
                    'id' => (int)$u['id_inventory_unit'],
                    'label' => $label,
                    'no_unit' => isset($u['no_unit']) ? $u['no_unit'] : null,
                    'serial_number' => isset($u['serial_number']) ? $u['serial_number'] : null,
                    'tahun_unit' => isset($u['tahun_unit']) ? $u['tahun_unit'] : null,
                    'merk_unit' => isset($u['merk_unit']) ? $u['merk_unit'] : null,
                    'model_unit' => isset($u['model_unit']) ? $u['model_unit'] : null,
                    'tipe_jenis' => isset($u['tipe_jenis']) ? $u['tipe_jenis'] : null,
                    'jenis_unit' => isset($u['jenis_unit']) ? $u['jenis_unit'] : null,
                    'lokasi_unit' => isset($u['lokasi_unit']) ? $u['lokasi_unit'] : null,
                    'kapasitas_name' => isset($u['kapasitas_name']) ? $u['kapasitas_name'] : null,
                    'departemen_name' => isset($u['departemen_name']) ? $u['departemen_name'] : null,
                    'valve' => $valveValue,
                    'mast' => $mastValue,
                    'roda' => $rodaValue,
                    'ban' => $banValue,
                    'sn_mast' => $snMast,
                    'sn_mesin' => $snMesin,
                    'sn_baterai' => $batterySN,
                    'sn_charger' => $chargerSN,
                    'sn_mast_formatted' => $snMastFormatted,
                    'sn_mesin_formatted' => $snMesinFormatted,
                    'sn_baterai_formatted' => $batteryDisplay,
                    'sn_charger_formatted' => $chargerDisplay,
                    'attachment_display' => $attachmentDisplay
                );
            }
        }
        
        if (!empty($spk['fabrikasi_attachment_id'])) {
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $spk['fabrikasi_attachment_id'])
                ->get()->getRowArray();
                
            if ($a) {
                $label = trim((isset($a['tipe']) ? $a['tipe'] : '-') . ' ' . 
                             (isset($a['merk']) ? $a['merk'] : '') . ' ' . 
                             (isset($a['model']) ? $a['model'] : ''));
                $suffix = [];
                if (!empty($a['sn_attachment'])) $suffix[] = 'SN: '.$a['sn_attachment'];
                if (!empty($a['lokasi_penyimpanan'])) $suffix[] = '@ '.$a['lokasi_penyimpanan'];
                if ($suffix) $label .= ' ['.implode(', ', $suffix).']';
                
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'label' => $label,
                    'tipe' => isset($a['tipe']) ? $a['tipe'] : null,
                    'merk' => isset($a['merk']) ? $a['merk'] : null,
                    'model' => isset($a['model']) ? $a['model'] : null,
                    'sn_attachment' => isset($a['sn_attachment']) ? $a['sn_attachment'] : null,
                    'lokasi_penyimpanan' => isset($a['lokasi_penyimpanan']) ? $a['lokasi_penyimpanan'] : null,
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? 
                        (isset($a['model']) ? $a['model'] : 'Attachment') . ' (' . $a['sn_attachment'] . ')' : 
                        (isset($a['model']) ? $a['model'] : '')
                ];
            }
        }
        
        // Legacy: Load from spesifikasi selected if no approval workflow data
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            if (empty($enriched['selected']['unit']) && !empty($sel['unit_id'])) {
                $u = $this->db->table('inventory_unit iu')
                    ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.sn_mast, iu.sn_mesin, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                    ->where('iu.id_inventory_unit', (int)$sel['unit_id'])
                    ->get()->getRowArray();
                if ($u) {
                    $label = trim((isset($u['no_unit']) ? $u['no_unit'] : '-') . ' - ' . 
                                 (isset($u['merk_unit']) ? $u['merk_unit'] : '-') . ' ' . 
                                 (isset($u['model_unit']) ? $u['model_unit'] : '') . ' @ ' . 
                                 (isset($u['lokasi_unit']) ? $u['lokasi_unit'] : '-'));
                    
                    $enriched['selected']['unit'] = [
                        'id' => (int)$sel['unit_id'],
                        'label' => $label,
                        'no_unit' => isset($u['no_unit']) ? $u['no_unit'] : null,
                        'serial_number' => isset($u['serial_number']) ? $u['serial_number'] : null,
                        'tahun_unit' => isset($u['tahun_unit']) ? $u['tahun_unit'] : null,
                        'merk_unit' => isset($u['merk_unit']) ? $u['merk_unit'] : null,
                        'model_unit' => isset($u['model_unit']) ? $u['model_unit'] : null,
                        'lokasi_unit' => isset($u['lokasi_unit']) ? $u['lokasi_unit'] : null,
                        'sn_mast' => isset($u['sn_mast']) ? $u['sn_mast'] : null,
                        'sn_mesin' => isset($u['sn_mesin']) ? $u['sn_mesin'] : null
                    ];
                    
                    // Get unit components
                    $unitComponents = $this->getUnitComponents($u['id_inventory_unit']);
                    
                    // Battery component
                    if (!empty($unitComponents['battery'])) {
                        $batterySN = '';
                        if (!empty($unitComponents['battery']['sn_baterai'])) {
                            $batterySN = $unitComponents['battery']['sn_baterai'];
                        }
                        
                        $batteryModel = '';
                        if (!empty($unitComponents['battery']['tipe_baterai'])) {
                            $batteryModel = $unitComponents['battery']['tipe_baterai'];
                        }
                        
                        $batteryDisplay = $batteryModel;
                        if (!empty($batterySN)) {
                            if (!empty($batteryModel)) {
                                $batteryDisplay = $batteryModel . ' (' . $batterySN . ')';
                            } else {
                                $batteryDisplay = 'Baterai (' . $batterySN . ')';
                            }
                        }
                        
                        $enriched['selected']['unit']['sn_baterai'] = $batterySN;
                        $enriched['selected']['unit']['baterai_model'] = $batteryModel;
                        $enriched['selected']['unit']['sn_baterai_formatted'] = $batteryDisplay;
                    }
                    
                    // Charger component
                    if (!empty($unitComponents['charger'])) {
                        $chargerSN = '';
                        if (!empty($unitComponents['charger']['sn_charger'])) {
                            $chargerSN = $unitComponents['charger']['sn_charger'];
                        }
                        
                        $chargerModel = '';
                        if (!empty($unitComponents['charger']['tipe_charger'])) {
                            $chargerModel = $unitComponents['charger']['tipe_charger'];
                        }
                        
                        $chargerDisplay = $chargerModel;
                        if (!empty($chargerSN)) {
                            if (!empty($chargerModel)) {
                                $chargerDisplay = $chargerModel . ' (' . $chargerSN . ')';
                            } else {
                                $chargerDisplay = 'Charger (' . $chargerSN . ')';
                            }
                        }
                        
                        $enriched['selected']['unit']['sn_charger'] = $chargerSN;
                        $enriched['selected']['unit']['charger_model'] = $chargerModel;
                        $enriched['selected']['unit']['sn_charger_formatted'] = $chargerDisplay;
                    }
                    
                    // Attachment component
                    if (!empty($unitComponents['attachment'])) {
                        $attachmentSN = '';
                        if (!empty($unitComponents['attachment']['sn_attachment'])) {
                            $attachmentSN = $unitComponents['attachment']['sn_attachment'];
                        }
                        
                        $attachmentModel = '';
                        if (!empty($unitComponents['attachment']['model'])) {
                            $attachmentModel = $unitComponents['attachment']['model'];
                        }
                        
                        $attachmentTipe = '';
                        if (!empty($unitComponents['attachment']['tipe'])) {
                            $attachmentTipe = $unitComponents['attachment']['tipe'];
                        }
                        
                        $attachmentDisplay = '';
                        if (!empty($attachmentTipe)) {
                            $attachmentDisplay = $attachmentTipe;
                            
                            if (!empty($unitComponents['attachment']['merk'])) {
                                $attachmentDisplay .= ' ' . $unitComponents['attachment']['merk'];
                            }
                            
                            if (!empty($attachmentModel)) {
                                $attachmentDisplay .= ' ' . $attachmentModel;
                            }
                            
                            if (!empty($attachmentSN)) {
                                $attachmentDisplay .= ' (' . $attachmentSN . ')';
                            }
                        } else if (!empty($attachmentSN)) {
                            $attachmentDisplay = 'Attachment (' . $attachmentSN . ')';
                        }
                        
                        $enriched['selected']['unit']['attachment_display'] = $attachmentDisplay;
                    }
                }
            }
            
            if (empty($enriched['selected']['attachment']) && !empty($sel['inventory_attachment_id'])) {
                $a = $this->db->table('inventory_attachment ia')
                    ->select('a.tipe, a.merk, a.model, ia.sn_attachment, ia.lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = ia.attachment_id','left')
                    ->where('ia.id_inventory_attachment', (int)$sel['inventory_attachment_id'])
                    ->get()->getRowArray();
                if ($a) {
                    $label = trim((isset($a['tipe']) ? $a['tipe'] : '-') . ' ' . 
                                 (isset($a['merk']) ? $a['merk'] : '') . ' ' . 
                                 (isset($a['model']) ? $a['model'] : ''));
                    $suffix = [];
                    if (!empty($a['sn_attachment'])) $suffix[] = 'SN: '.$a['sn_attachment'];
                    if (!empty($a['lokasi_penyimpanan'])) $suffix[] = '@ '.$a['lokasi_penyimpanan'];
                    if ($suffix) $label .= ' ['.implode(', ', $suffix).']';
                    
                    $enriched['selected']['attachment'] = [
                        'id' => (int)$sel['inventory_attachment_id'],
                        'label' => $label,
                        'tipe' => isset($a['tipe']) ? $a['tipe'] : null,
                        'merk' => isset($a['merk']) ? $a['merk'] : null,
                        'model' => isset($a['model']) ? $a['model'] : null,
                        'sn_attachment' => isset($a['sn_attachment']) ? $a['sn_attachment'] : null,
                        'lokasi_penyimpanan' => isset($a['lokasi_penyimpanan']) ? $a['lokasi_penyimpanan'] : null,
                        'sn_attachment_formatted' => !empty($a['sn_attachment']) ? 
                            (isset($a['model']) ? $a['model'] : 'Attachment') . ' (' . $a['sn_attachment'] . ')' : 
                            (isset($a['model']) ? $a['model'] : '')
                    ];
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

        // Enrich prepared_units into prepared_units_detail for distinct display
        if (!empty($preparedUnits)) {
            $preparedDetails = [];
            foreach ($preparedUnits as $pu) {
                $uInfo = null; 
                $aInfo = null; 
                $unitLabel = ''; 
                $attLabel = '';
                
                if (!empty($pu['unit_id'])) {
                    $uInfo = $this->db->table('inventory_unit iu')
                        ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.lokasi_unit, mu.merk_unit, mu.model_unit, tu.tipe as tipe_jenis')
                        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                        ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                        ->where('iu.id_inventory_unit', $pu['unit_id'])
                        ->get()->getRowArray();
                    
                    if ($uInfo) {
                        $unitLabel = trim((isset($uInfo['no_unit']) ? $uInfo['no_unit'] : '-') . ' - ' . 
                                        (isset($uInfo['merk_unit']) ? $uInfo['merk_unit'] : '-') . ' ' . 
                                        (isset($uInfo['model_unit']) ? $uInfo['model_unit'] : '') . ' @ ' . 
                                        (isset($uInfo['lokasi_unit']) ? $uInfo['lokasi_unit'] : '-'));
                    }
                }
                
                if (!empty($pu['attachment_id'])) {
                    $aInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan, att.tipe, att.merk, att.model')
                        ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                        ->where('ia.id_inventory_attachment', $pu['attachment_id'])
                        ->get()->getRowArray();
                    
                    if ($aInfo) {
                        $attLabel = trim((isset($aInfo['tipe']) ? $aInfo['tipe'] : '-') . ' ' . 
                                       (isset($aInfo['merk']) ? $aInfo['merk'] : '') . ' ' . 
                                       (isset($aInfo['model']) ? $aInfo['model'] : ''));
                        $suf = [];
                        if (!empty($aInfo['sn_attachment'])) $suf[] = 'SN: '.$aInfo['sn_attachment'];
                        if (!empty($aInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$aInfo['lokasi_penyimpanan'];
                        if ($suf) $attLabel .= ' ['.implode(', ', $suf).']';
                    }
                }
                
                $preparedDetails[] = [
                    'unit_id' => isset($pu['unit_id']) ? $pu['unit_id'] : null,
                    'unit_label' => $unitLabel,
                    'no_unit' => isset($uInfo['no_unit']) ? $uInfo['no_unit'] : '',
                    'serial_number' => isset($uInfo['serial_number']) ? $uInfo['serial_number'] : '',
                    'merk_unit' => isset($uInfo['merk_unit']) ? $uInfo['merk_unit'] : '',
                    'model_unit' => isset($uInfo['model_unit']) ? $uInfo['model_unit'] : '',
                    'tipe_jenis' => isset($uInfo['tipe_jenis']) ? $uInfo['tipe_jenis'] : '',
                    'attachment_id' => isset($pu['attachment_id']) ? $pu['attachment_id'] : null,
                    'attachment_label' => $attLabel,
                    'mekanik' => isset($pu['mekanik']) ? $pu['mekanik'] : '',
                    'aksesoris_tersedia' => isset($pu['aksesoris_tersedia']) ? $pu['aksesoris_tersedia'] : '',
                    'catatan' => isset($pu['catatan']) ? $pu['catatan'] : '',
                    'timestamp' => isset($pu['timestamp']) ? $pu['timestamp'] : ''
                ];
            }
            $enriched['prepared_units_detail'] = $preparedDetails;
        }

        // Return clean view for printing
        $content = view('marketing/print_spk', [
            'spk' => $spk,
            'spesifikasi' => $enriched,
            'kontrak_spesifikasi' => $kontrak_spesifikasi
        ]);
        
        // Simple approach: just output the view content directly
        echo $content;
        exit();
    }
}
