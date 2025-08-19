<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitAssetModel extends Model
{
    // Dialihkan ke tabel inventory_unit yang sekarang menjadi sumber tunggal
    protected $table            = 'inventory_unit';
    protected $primaryKey       = 'no_unit'; // primary key sudah AUTO_INCREMENT di inventory_unit
    protected $useAutoIncrement = true; // aktifkan sesuai schema baru
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // Sesuaikan dengan kolom di inventory_unit (lihat InventoryUnitModel untuk referensi)
    protected $allowedFields    = [
        'serial_number',
        'id_po',
        'tahun_unit',
        'status_unit_id',
        'status_aset',
        'lokasi_unit',
        'departemen_id',
        'tanggal_kirim',
        'keterangan',
        'tipe_unit_id',
        'model_unit_id',
        'kapasitas_unit_id',
        'model_mast_id',
        'tinggi_mast',
        'sn_mast',
        'model_mesin_id',
        'sn_mesin',
        'model_attachment_id',
        'sn_attachment',
        'model_baterai_id',
        'sn_baterai',
        'model_charger_id',
        'sn_charger',
        'roda_id',
        'ban_id',
        'valve_id',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generates the next available unit number.
     * This is a basic implementation and might need to be adjusted.
     */
    public function getNextUnitNumber()
    {
    // Inventory_unit menggunakan auto increment numeric; jika tetap butuh kode FL- tampilkan saja format baru berbasis max(no_unit)
    $last = $this->selectMax('no_unit')->first();
    $nextNumeric = isset($last['no_unit']) ? ((int)$last['no_unit']) + 1 : 1;
    return $nextNumeric; // kembalikan angka agar kompatibel dengan PK auto increment
    }

    public function getUnitAssetWithDetails($no_unit = null)
    {
        $db = \Config\Database::connect();
        
        // Start with basic unit asset data
    $builder = $db->table('inventory_unit iu');
    $builder->select('iu.no_unit, iu.serial_number, iu.status_unit_id, iu.lokasi_unit, iu.status_aset, iu.departemen_id, iu.tanggal_kirim, iu.keterangan, iu.tipe_unit_id, iu.tahun_unit, iu.model_unit_id, iu.kapasitas_unit_id, iu.model_mast_id, iu.sn_mast, iu.model_mesin_id, iu.sn_mesin, iu.model_attachment_id, iu.sn_attachment, iu.model_baterai_id, iu.sn_baterai, iu.model_charger_id, iu.sn_charger, iu.roda_id, iu.ban_id, iu.valve_id, iu.created_at, iu.updated_at');
        
        // Join with basic reference tables
        if ($db->tableExists('departemen')) {
            $builder->select('d.nama_departemen as departemen_name', false);
            $builder->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left');
        }
        
        if ($db->tableExists('status_unit')) {
            $builder->select('su.status_unit as status_unit_name', false);
            $builder->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left');
        }
        
        if ($db->tableExists('tipe_unit')) {
            $builder->select('tu.nama_tipe_unit as tipe_unit_name', false);
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
        }
        
        if ($db->tableExists('model_unit')) {
            $builder->select('mu.merk_unit as merk_unit_name, mu.model_unit as model_unit_name', false);
            $builder->select('CONCAT(mu.merk_unit, " - ", mu.model_unit) as model_unit_display', false);
            $builder->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
        }
        
        if ($db->tableExists('kapasitas')) {
            $builder->select('k.kapasitas_unit as kapasitas_unit_name', false);
            $builder->select('k.kapasitas_unit as kapasitas_unit_display', false);
            $builder->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left');
        }
        
        // Join with specifications tables
        if ($db->tableExists('tipe_mast')) {
            $builder->select('tm.tipe_mast as model_mast_name', false);
            $builder->select('tm.tipe_mast as model_mast_display', false);
            $builder->join('tipe_mast tm', 'tm.id_mast = iu.model_mast_id', 'left');
        }
        
        // Try different possible mesin table names
        if ($db->tableExists('mesin')) {
            $builder->select('m.model_mesin as model_mesin_name, m.merk_mesin as merk_mesin_name', false);
            $builder->select('m.model_mesin as model_mesin, m.merk_mesin', false);
            $builder->join('mesin m', 'm.id = iu.model_mesin_id', 'left');
        } elseif ($db->tableExists('model_mesin')) {
            $builder->select('mm.model_mesin as model_mesin_name, mm.merk_mesin as merk_mesin_name', false);
            $builder->select('mm.model_mesin as model_mesin, mm.merk_mesin', false);
            $builder->join('model_mesin mm', 'mm.id = iu.model_mesin_id', 'left');
        }
        
        if ($db->tableExists('attachment')) {
            $builder->select('att.attachment as model_attachment_name', false);
            $builder->select('att.attachment as model_attachment_display', false);
            $builder->join('attachment att', 'att.id_attachment = iu.model_attachment_id', 'left');
        }
        
        // Try different possible baterai table names
        if ($db->tableExists('baterai')) {
            $builder->select('b.merk_baterai as model_baterai_name, b.tipe_baterai', false);
            $builder->select('b.merk_baterai as model_baterai, b.tipe_baterai', false);
            $builder->join('baterai b', 'b.id = iu.model_baterai_id', 'left');
        } elseif ($db->tableExists('model_baterai')) {
            $builder->select('mb.merk_baterai as model_baterai_name, mb.tipe_baterai', false);
            $builder->select('mb.merk_baterai as model_baterai, mb.tipe_baterai', false);
            $builder->join('model_baterai mb', 'mb.id = iu.model_baterai_id', 'left');
        }
        
        if ($db->tableExists('charger')) {
            $builder->select('ch.merk_charger as model_charger_name, ch.tipe_charger', false);
            $builder->select('ch.merk_charger as model_charger, ch.tipe_charger', false);
            $builder->join('charger ch', 'ch.id_charger = iu.model_charger_id', 'left');
        }
        
        // Join with wheels & tires tables (menggunakan fallback yang aman)
        if ($db->tableExists('jenis_roda')) {
            try {
                $rodaFields = $db->getFieldNames('jenis_roda');
                $rodaFieldName = null;
                
                if (in_array('jenis_roda', $rodaFields)) {
                    $rodaFieldName = 'jr.jenis_roda';
                } elseif (in_array('nama_jenis_roda', $rodaFields)) {
                    $rodaFieldName = 'jr.nama_jenis_roda';
                } elseif (in_array('jenis', $rodaFields)) {
                    $rodaFieldName = 'jr.jenis';
                }
                
                if ($rodaFieldName) {
                    $builder->select($rodaFieldName . ' as roda_name', false);
                    $builder->select($rodaFieldName . ' as roda_display', false);
                    // Try different possible ID field names
                    if (in_array('id_roda', $rodaFields)) {
                        $builder->join('jenis_roda jr', 'jr.id_roda = iu.roda_id', 'left');
                    } elseif (in_array('id', $rodaFields)) {
                        $builder->join('jenis_roda jr', 'jr.id = iu.roda_id', 'left');
                    }
                }
            } catch (\Exception $e) {
                log_message('warning', 'Could not join jenis_roda table: ' . $e->getMessage());
            }
        }
        
        if ($db->tableExists('tipe_ban')) {
            try {
                $banFields = $db->getFieldNames('tipe_ban');
                // Try different possible ID field names for tipe_ban
                if (in_array('id_ban', $banFields)) {
                    $builder->select('tb.tipe_ban as ban_name', false);
                    $builder->select('tb.tipe_ban as ban_display', false);
                    $builder->join('tipe_ban tb', 'tb.id_ban = iu.ban_id', 'left');
                } elseif (in_array('id', $banFields)) {
                    $builder->select('tb.tipe_ban as ban_name', false);
                    $builder->select('tb.tipe_ban as ban_display', false);
                    $builder->join('tipe_ban tb', 'tb.id = iu.ban_id', 'left');
                }
            } catch (\Exception $e) {
                log_message('warning', 'Could not join tipe_ban table: ' . $e->getMessage());
            }
        }
        
        if ($db->tableExists('valve')) {
            try {
                $valveFields = $db->getFieldNames('valve');
                $valveFieldName = null;
                
                if (in_array('valve', $valveFields)) {
                    $valveFieldName = 'v.valve';
                } elseif (in_array('nama_valve', $valveFields)) {
                    $valveFieldName = 'v.nama_valve';
                } elseif (in_array('tipe_valve', $valveFields)) {
                    $valveFieldName = 'v.tipe_valve';
                }
                
                if ($valveFieldName) {
                    $builder->select($valveFieldName . ' as valve_name', false);
                    $builder->select($valveFieldName . ' as valve_display', false);
                    // Try different possible ID field names for valve
                    if (in_array('id_valve', $valveFields)) {
                        $builder->join('valve v', 'v.id_valve = iu.valve_id', 'left');
                    } elseif (in_array('id', $valveFields)) {
                        $builder->join('valve v', 'v.id = iu.valve_id', 'left');
                    }
                }
            } catch (\Exception $e) {
                log_message('warning', 'Could not join valve table: ' . $e->getMessage());
            }
        }

        if ($no_unit) {
            return $builder->where('iu.no_unit', $no_unit)->get()->getRowArray();
        }
        return $builder->get()->getResultArray();
    }

    public function getUnitAssetStats()
    {
        return [
            'total'       => $this->db->table($this->table)->countAllResults(),
            'available'   => $this->db->table($this->table)->where('status_unit_id', 7)->countAllResults(), // 7 = STOCK ASET -> treat as available
            'rented'      => $this->db->table($this->table)->where('status_unit_id', 3)->countAllResults(),
            'maintenance' => $this->db->table($this->table)->where('status_unit_id', 2)->countAllResults(),
        ];
    }

    public function getDepartments()
    {
        return $this->db->table('departemen')->get()->getResultArray();
    }

    public function getLocations()
    {
    return $this->db->table($this->table)->distinct()->select('lokasi_unit')->where('lokasi_unit IS NOT NULL')->where('lokasi_unit !=', '')->get()->getResultArray();
    }

    public function getUnitTypes()
    {
        return $this->db->table('tipe_unit')->get()->getResultArray();
    }

    public function updateUnitStatus($id, $status)
    {
    return $this->update($id, ['status_unit_id' => $status]);
    }

    public function getUnitAssets($filters = [])
    {
        $builder = $this;
    if (!empty($filters['status_unit_id'])) $builder->where('status_unit_id', $filters['status_unit_id']);
    if (!empty($filters['departemen_id'])) $builder->where('departemen_id', $filters['departemen_id']);
        if (!empty($filters['lokasi_unit'])) $builder->like('lokasi_unit', $filters['lokasi_unit']);
        return $builder->findAll();
    }

    public function getUnitAssetsForDataTable($start, $length, $searchValue, $orderColumn, $orderDir, $filters)
    {
        // NOTE: This is a stub. Full implementation for server-side processing should be added here.
        $builder = $this->db->table($this->table);
        $data = $builder->limit($length, $start)->get()->getResultArray();
        return [
            'recordsTotal' => $this->countAllResults(),
            'recordsFiltered' => $builder->countAllResults(false),
            'data' => $data
        ];
    }
}